<?php

namespace App\Livewire;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Bien;
use App\Models\Asignacion;
use App\Models\Dependencia;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;

class GestorInventario extends Component
{
    // Bienes seleccionados
    public $bienesSeleccionados = [];
    
    // Datos para asignación
    public $dependencia_id = '';
    public $fecha_asignacion = '';
    public $observacion = '';
    
    // Vista de registros
    public $mostrarAsignaciones = false;
    public $asignaciones = [];
    
    // Modal para ver foto
    public $mostrarModalFoto = false;
    public $fotoUrl = '';
    public $bienSeleccionado = null;

    public function mount()
    {
        $this->fecha_asignacion = date('Y-m-d');
    }

    // Seleccionar/deseleccionar bien
    public function toggleBien($bienId)
    {
        if (in_array($bienId, $this->bienesSeleccionados)) {
            $this->bienesSeleccionados = array_values(array_diff($this->bienesSeleccionados, [$bienId]));
        } else {
            $this->bienesSeleccionados[] = $bienId;
        }
    }

    // Método para ver la foto de un bien
    public function verFotoBien($bienId)
    {
        $bien = Bien::with('remito')->find($bienId);

        if (!$bien) {
            session()->flash('error', 'No se encontró el bien seleccionado.');
            return;
        }

        $remito = $bien->remito;

        if ($remito && !empty($remito->foto_remito)) {
            $this->fotoUrl = asset('storage/' . $remito->foto_remito);
            $this->bienSeleccionado = $bien;
            $this->mostrarModalFoto = true;
        } else {
            session()->flash('error', 'No hay foto disponible para este bien.');
        }
    }

    public function generarQR($bienId)
{
    $bien = Bien::with(['remito', 'documentacion', 'dependencia'])->findOrFail($bienId);

    // 📋 Datos del bien
    $data = "🧾 BIEN PATRIMONIAL\n";
    $data .= "Inventario: {$bien->numero_inventario}\n";
    $data .= "Descripción: {$bien->descripcion}\n";
    $data .= "Tipo: " . ($bien->bien_uso ? 'Bien de Uso' : 'Bien de Consumo') . "\n";
    $data .= "Monto Total: $" . number_format((float)($bien->monto_total ?? 0), 2) . "\n";

    if ($bien->remito) {
        $data .= "\n📦 REMITO\n";
        $data .= "Orden de Provisión: {$bien->remito->orden_provision}\n";
        $data .= "Expediente: {$bien->remito->numero_expediente}\n";
        $data .= "Fecha Recepción: {$bien->remito->fecha_recepcion}\n";
    }

    if ($bien->documentacion) {
        $data .= "\n📄 DOCUMENTACIÓN\n";
        $data .= "Acta: {$bien->documentacion->numero_acta}\n";
        $data .= "Resolución: {$bien->documentacion->numero_resolucion}\n";
        $data .= "Factura: {$bien->documentacion->numero_factura}\n";
        $data .= "Monto: $" . number_format((float)($bien->documentacion->monto ?? 0), 2) . "\n";
    }

    if ($bien->dependencia) {
        $data .= "\n🏛️ DEPENDENCIA ACTUAL\n";
        $data .= "{$bien->dependencia->codigo} - {$bien->dependencia->nombre}\n";
    }

    // 📁 Crear carpeta si no existe
    if (!Storage::disk('public')->exists('qrs')) {
        Storage::disk('public')->makeDirectory('qrs');
    }

    // 📄 Nombre del archivo
    $fileName = 'qrs/bien_' . $bien->id . '_' . Str::random(6) . '.png';
    $path = storage_path('app/public/' . $fileName);

    // 🧠 Generar QR usando Endroid
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($data)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(300)
        ->margin(5)
        ->backgroundColor(new Color(255, 255, 255))
        ->foregroundColor(new Color(0, 0, 0))
        ->build();

    // 🖼️ Guardar QR como archivo
    $result->saveToFile($path);

    // 💾 Guardar ruta en la BD
    $bien->update(['codigo_qr' => $fileName]);

    // 🔄 Refrescar vista y mostrar mensaje
    $this->dispatch('$refresh');
    session()->flash('message', '✅ Código QR generado correctamente.');
}

    public function cerrarModalFoto()
    {
        $this->mostrarModalFoto = false;
        $this->fotoUrl = '';
        $this->bienSeleccionado = null;
    }

    // Asignar bienes a dependencia
    public function asignarBienes()
    {
        $this->validate([
            'dependencia_id' => 'required|exists:dependencias,id',
            'fecha_asignacion' => 'required|date',
            'bienesSeleccionados' => 'required|array|min:1',
        ], [
            'dependencia_id.required' => 'Debe seleccionar una dependencia destino',
            'bienesSeleccionados.required' => 'Debe seleccionar al menos un bien',
            'bienesSeleccionados.min' => 'Debe seleccionar al menos un bien',
        ]);

        $dependencia = Dependencia::find($this->dependencia_id);

        foreach ($this->bienesSeleccionados as $bienId) {
            $bien = Bien::find($bienId);
            
            if ($bien && $bien->estado === 'stock') {
                Asignacion::create([
                    'bien_id' => $bien->id,
                    'dependencia_id' => $this->dependencia_id,
                    'fecha_asignacion' => $this->fecha_asignacion,
                    'user_id' => Auth::id(),
                    'observacion' => $this->observacion,
                ]);

                $bien->update([
                    'estado' => 'asignado',
                    'dependencia_id' => $this->dependencia_id,
                ]);
            }
        }

        session()->flash('message', count($this->bienesSeleccionados) . ' bien(es) asignado(s) correctamente a ' . $dependencia->nombre);
        
        $this->reset(['bienesSeleccionados', 'dependencia_id', 'observacion']);
        $this->fecha_asignacion = date('Y-m-d');
    }

    // Ver asignaciones recientes
    public function verAsignaciones()
    {
        $this->asignaciones = Asignacion::with(['bien.cuenta', 'bien.proveedor', 'dependencia', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
        
        $this->mostrarAsignaciones = true;
    }

    public function volverAlFormulario()
    {
        $this->mostrarAsignaciones = false;
    }

    public function cancelar()
    {
        $this->reset(['bienesSeleccionados', 'dependencia_id', 'observacion']);
        $this->fecha_asignacion = date('Y-m-d');
    }

    public function render()
    {
        return view('livewire.gestor-inventario-panel', [
            'bienesStock' => Bien::with(['cuenta', 'proveedor', 'remito'])
                ->where('estado', 'stock')
                ->orderBy('created_at', 'desc')
                ->get(),
            'dependencias' => Dependencia::where('activo', true)
                ->orderBy('codigo')
                ->get(),
        ])->layout('components.admin-layout', [
            'title' => 'Panel del Gestor de Inventario',
        ]);
    }
}

