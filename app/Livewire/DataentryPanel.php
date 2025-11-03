<?php

namespace App\Livewire;

use App\Models\Bien;
use App\Models\Documentacion;
use App\Models\Proveedor;
use App\Models\OrdenProvision;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DataentryPanel extends Component
{
    // Búsqueda
    public $busqueda = '';
    public $bienSeleccionado = null;

    // Campos de documentación
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

    // Modales
    public $modalSinAsignar = false;
    public $modalExportar = false;

    // Listeners para refrescar datos
    protected $listeners = ['refrescarPendientes' => '$refresh'];

    public function mount()
    {
        // Establecer valores por defecto
        $this->fecha_acta = date('Y-m-d');
        $this->fecha_factura = date('Y-m-d');
        $this->ejercicio = date('Y');
    }

    /**
     * Buscar bien por número de inventario
     */
    public function buscar()
    {
        $this->validate([
            'busqueda' => 'required|string',
        ], [
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

    /**
     * Cargar documentación existente del bien
     */
    public function cargarDocumentacion($bienId)
    {
        $bien = Bien::with('documentacion')->find($bienId);
        
        if ($bien && $bien->documentacion) {
            $doc = $bien->documentacion;
            $this->numero_acta = $doc->numero_acta ?? '';
            $this->fecha_acta = $doc->fecha_acta ? \Carbon\Carbon::parse($doc->fecha_acta)->format('Y-m-d') : date('Y-m-d');
            $this->numero_resolucion = $doc->numero_resolucion ?? '';
            $this->numero_factura = $doc->numero_factura ?? '';
            $this->fecha_acta = optional($doc->fecha_acta)->format('Y-m-d') ?? date('Y-m-d');

            $this->monto = $doc->monto ?? '';
            $this->proveedor_id = $doc->proveedor_id ?? '';
            $this->partida_presupuestaria = $doc->partida_presupuestaria ?? '';
            $this->orden_pago = $doc->orden_pago ?? '';
            $this->ejercicio = $doc->ejercicio ?? date('Y');
            $this->orden_provision_id = $doc->orden_provision_id ?? '';
            $this->estado = $doc->estado ?? 'pendiente';
            $this->observaciones = $doc->observaciones ?? '';
        } else {
            // Limpiar campos si no hay documentación
            $this->limpiarCampos();
        }
    }

    /**
     * Guardar documentación del bien
     */
    public function guardarDocumentacion()
    {
        $this->validate([
            'numero_acta' => 'nullable|string|max:255',
            'fecha_acta' => 'nullable|date',
            'numero_resolucion' => 'nullable|string|max:255',
            'numero_factura' => 'nullable|string|max:255',
            'fecha_factura' => 'nullable|date',
            'monto' => 'nullable|numeric|min:0',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'partida_presupuestaria' => 'nullable|string|max:255',
            'orden_pago' => 'nullable|string|max:255',
            'ejercicio' => 'nullable|string|max:4',
            'orden_provision_id' => 'nullable|exists:ordenes_provision,id',
            'estado' => 'required|in:pendiente,completo,revisado',
            'observaciones' => 'nullable|string',
        ]);

        $bien = Bien::find($this->bienSeleccionado);

        if (!$bien) {
            session()->flash('error', '❌ Bien no encontrado');
            return;
        }

        // Crear o actualizar documentación
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
        
        // Limpiar selección
        $this->bienSeleccionado = null;
        $this->limpiarCampos();
        $this->busqueda = '';
        
        // Refrescar lista de pendientes
        $this->dispatch('refrescarPendientes');
    }

    /**
     * Limpiar campos del formulario
     */
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

    /**
     * Mostrar/ocultar modales
     */
    public function mostrarModal($tipo)
    {
        if ($tipo === 'sin-asignar') {
            $this->modalSinAsignar = true;
        } elseif ($tipo === 'exportar') {
            $this->modalExportar = true;
        }
    }

    public function cerrarModal($tipo)
    {
        if ($tipo === 'sin-asignar') {
            $this->modalSinAsignar = false;
        } elseif ($tipo === 'exportar') {
            $this->modalExportar = false;
        }
    }

    /**
     * Obtener bienes pendientes de documentación
     */
    public function getPendientesProperty()
    {
        return Bien::whereDoesntHave('documentacion')
            ->orWhereHas('documentacion', function($query) {
                $query->where('estado', '!=', 'completo');
            })
            ->with(['cuenta', 'remito'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Obtener bienes sin asignar (para el modal)
     */
    public function getBienesSinAsignarProperty()
    {
        return Bien::where('estado', 'stock')
            ->with(['cuenta', 'remito'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Seleccionar bien desde la lista de pendientes
     */
    public function seleccionarBien($bienId)
    {
        $this->bienSeleccionado = $bienId;
        $this->cargarDocumentacion($bienId);
    }

    /**
     * Exportar a Excel
     */
    public function exportarExcel()
    {
        // Aquí implementarías la lógica de exportación
        // Por ahora, mostramos un mensaje
        session()->flash('message', '📊 Exportación en proceso...');
        $this->modalExportar = false;
        
        // Podrías usar algo como:
        // return Excel::download(new BienesExport, 'bienes.xlsx');
    }

    public function render()
    {
        return view('livewire.dataentry-panel', [
            'proveedores' => Proveedor::where('estado', 1)->orderBy('razon_social')->get(),
           'ordenesProvision' => OrdenProvision::orderBy('numero_orden')->get(),

        ])->layout('components.admin-layout', [
            'title' => 'Panel del Cargador',
        ]);
    }
}