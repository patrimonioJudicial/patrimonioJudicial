<?php

namespace App\Livewire;

use App\Models\Bien;
use App\Models\Documentacion;
use App\Models\Proveedor;
use App\Models\OrdenProvision;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BienesDocumentacionExport;

class DataentryPanel extends Component
{
    public $busqueda = '';
    public $bienSeleccionado = null;

    public $numero_acta = '';
    public $fecha_acta = '';
    public $numero_resolucion = '';
    public $numero_factura = '';
    public $fecha_factura = '';
    public $monto = '';
    public $proveedor_id = '';
    public $partida_presupuestaria = '';
    public $orden_pago = '';
    public $ejercicio = '';
    public $orden_provision_id = '';
    public $estado = 'pendiente';
    public $observaciones = '';
    public $bienesCompletos = [];
    public $modalDetalles = false;
    public $bienDetalle = null;

    // 🔹 Unificamos el nombre correcto
    public $showExportModal = false;
    public $fechaInicio;
    public $fechaFin;

    public $modalSinAsignar = false;

    protected $listeners = ['refrescarPendientes' => '$refresh'];

    public function mount()
    {
        $this->fecha_acta = date('Y-m-d');
        $this->fecha_factura = date('Y-m-d');
        $this->ejercicio = date('Y');
    }

    /** Buscar bien */
    public function buscar()
    {
        $this->validate(['busqueda' => 'required|string'], [
            'busqueda.required' => 'Debe ingresar un número de inventario',
        ]);

        $bien = Bien::where('numero_inventario', $this->busqueda)->first();

        if ($bien) {
            $this->bienSeleccionado = $bien->id;
            $this->cargarDocumentacion($bien->id);
            session()->flash('message', '✅ Bien encontrado correctamente');
        } else {
            session()->flash('error', '❌ No se encontró ningún bien con ese número de inventario');
            $this->bienSeleccionado = null;
        }
    }

    public function verDetalles($bienId)
    {
        $this->bienDetalle = Bien::with(['remito', 'proveedor', 'documentacion'])->find($bienId);
        $this->modalDetalles = true;
    }

    /** Cargar documentación */
    public function cargarDocumentacion($bienId)
    {
        $bien = Bien::with('documentacion')->find($bienId);

        if ($bien && $bien->documentacion) {
            $doc = $bien->documentacion;
            $this->numero_acta = $doc->numero_acta ?? '';
            $this->fecha_acta = $doc->fecha_acta ? Carbon::parse($doc->fecha_acta)->format('Y-m-d') : date('Y-m-d');
            $this->numero_resolucion = $doc->numero_resolucion ?? '';
            $this->numero_factura = $doc->numero_factura ?? '';
            $this->fecha_factura = $doc->fecha_factura ? Carbon::parse($doc->fecha_factura)->format('Y-m-d') : date('Y-m-d');
            $this->monto = $doc->monto ?? '';
            $this->proveedor_id = $doc->proveedor_id ?? '';
            $this->partida_presupuestaria = $doc->partida_presupuestaria ?? '';
            $this->orden_pago = $doc->orden_pago ?? '';
            $this->ejercicio = $doc->ejercicio ?? date('Y');
            $this->orden_provision_id = $doc->orden_provision_id ?? '';
            $this->estado = $doc->estado ?? 'pendiente';
            $this->observaciones = $doc->observaciones ?? '';
        } else {
            $this->limpiarCampos();
        }
    }

    /** Guardar documentación */
    public function guardarDocumentacion()
    {
        $bien = Bien::find($this->bienSeleccionado);

        if (!$bien) {
            session()->flash('error', '❌ Bien no encontrado.');
            return;
        }

        Documentacion::updateOrCreate(
            ['bien_id' => $bien->id],
            [
                'numero_acta' => $this->numero_acta,
                'fecha_acta' => $this->fecha_acta,
                'numero_resolucion' => $this->numero_resolucion,
                'numero_factura' => $this->numero_factura,
                'fecha_factura' => $this->fecha_factura,
                'monto' => $this->monto,
                'proveedor_id' => $this->proveedor_id,
                'partida_presupuestaria' => $this->partida_presupuestaria,
                'orden_pago' => $this->orden_pago,
                'ejercicio' => $this->ejercicio,
                'orden_provision_id' => $this->orden_provision_id,
                'estado' => $this->estado,
                'observaciones' => $this->observaciones,
            ]
        );

        session()->flash('message', '✅ Documentación guardada correctamente');
    }

    public function limpiarCampos()
    {
        $this->numero_acta = '';
        $this->fecha_acta = date('Y-m-d');
        $this->numero_resolucion = '';
        $this->numero_factura = '';
        $this->fecha_factura = date('Y-m-d');
        $this->monto = '';
        $this->proveedor_id = '';
        $this->partida_presupuestaria = '';
        $this->orden_pago = '';
        $this->ejercicio = date('Y');
        $this->orden_provision_id = '';
        $this->estado = 'pendiente';
        $this->observaciones = '';
    }

    /** Mostrar / cerrar modales */
    public function mostrarModal($tipo)
    {
        if ($tipo === 'sin-asignar') {
            $this->obtenerBienesCompletos();
            $this->modalSinAsignar = true;
        }

        if ($tipo === 'exportar') {
            $this->showExportModal = true; // ✅ corregido
        }
    }

    public function cerrarModal($tipo)
    {
        if ($tipo === 'sin-asignar') $this->modalSinAsignar = false;
        if ($tipo === 'exportar') $this->showExportModal = false; // ✅ corregido
        if ($tipo === 'detalles') $this->modalDetalles = false;
    }

    /** Bienes pendientes */
    public function getPendientesProperty()
    {
        return Bien::whereDoesntHave('documentacion')
            ->orWhereHas('documentacion', function ($q) {
                $q->where('estado', '!=', 'completo');
            })
            ->with(['cuenta', 'remito'])
            ->latest()
            ->take(20)
            ->get();
    }

    public function getBienesSinAsignarProperty()
    {
        return Bien::whereHas('documentacion', function ($q) {
            $q->where('estado', 'completo');
        })
        ->with(['cuenta', 'remito'])
        ->latest()
        ->get();
    }

    public function obtenerBienesCompletos()
    {
        $this->bienesCompletos = Bien::whereHas('documentacion', function ($query) {
            $query->where('estado', 'completo');
        })
        ->with(['documentacion'])
        ->get();
    }

    /** Exportar */
    public function exportarExcelPorFechas()
    {
        $this->validate([
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date|after_or_equal:fechaInicio',
        ]);

        $this->showExportModal = false;

        $this->dispatchBrowserEvent('descargar-excel', [
            'inicio' => $this->fechaInicio,
            'fin' => $this->fechaFin,
        ]);

        session()->flash('message', "📦 Se generará el Excel desde {$this->fechaInicio} hasta {$this->fechaFin}");
    }

    public function render()
    {
        $this->obtenerBienesCompletos();

        return view('livewire.dataentry-panel', [
            'bienesCompletos' => $this->bienesCompletos,
            'proveedores' => Proveedor::where('estado', 1)->orderBy('razon_social')->get(),
            'ordenesProvision' => OrdenProvision::orderBy('id')->get(),
        ])->layout('components.admin-layout', [
            'title' => 'Panel de Cargador',
        ]);
    }
}

