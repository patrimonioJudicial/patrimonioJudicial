<?php

namespace App\Livewire;

use App\Models\Bien;
use App\Models\Asignacion;
use App\Models\Dependencia;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

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
            $this->bienesSeleccionados = array_diff($this->bienesSeleccionados, [$bienId]);
        } else {
            $this->bienesSeleccionados[] = $bienId;
        }
    }

    // Ver foto del bien
    public function verFotoBien($bienId)
    {
        $bien = Bien::find($bienId);

        if ($bien && $bien->foto) {
            $this->bienSeleccionado = $bien;
            $this->fotoUrl = asset('storage/' . $bien->foto);
            $this->mostrarModalFoto = true;
        } else {
            session()->flash('error', 'No hay foto disponible para este bien.');
        }
    }

    public function cerrarModalFoto()
    {
        $this->mostrarModalFoto = false;
        $this->fotoUrl = '';
        $this->bienSeleccionado = null;
    }

    // ✅ Asignar bienes y generar QR automáticamente
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
                // Crear la asignación
                Asignacion::create([
                    'bien_id' => $bien->id,
                    'dependencia_id' => $this->dependencia_id,
                    'fecha_asignacion' => $this->fecha_asignacion,
                    'user_id' => Auth::id(),
                    'observacion' => $this->observacion,
                ]);

                // Actualizar estado del bien
                $bien->update([
                    'estado' => 'asignado',
                    'dependencia_id' => $this->dependencia_id,
                ]);

                // ✅ Generar URL interna para el consultor
                $url = route('consultor.panel') . '?id=' . $bien->id;

                // Ruta del QR
                $rutaQR = "qrcodes/bien_{$bien->id}.svg";

                // Generar y guardar el QR
                Storage::disk('public')->put(
                    $rutaQR,
                    QrCode::format('svg')
                        ->size(250)
                        ->encoding('UTF-8')
                        ->errorCorrection('H')
                        ->generate($url)
                );

                // Guardar la ruta del QR en el bien
                $bien->update(['codigo_qr' => $rutaQR]);
            }
        }

        // ✅ Mensaje de éxito
        session()->flash(
            'message',
            count($this->bienesSeleccionados) . ' bien(es) asignado(s) correctamente y se generó su código QR.'
        );

        // Resetear formulario
        $this->reset(['bienesSeleccionados', 'dependencia_id', 'observacion']);
        $this->fecha_asignacion = date('Y-m-d');
    }

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
