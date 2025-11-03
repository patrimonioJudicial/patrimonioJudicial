<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Proveedor;
use App\Models\Cuenta;
use App\Models\Remito;

class Bienes extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // ─── Propiedades principales ───────────────────────────────
    public $search = '';
    public $mostrarInactivos = false;
    public $detalleBien = null;

    // ─── Campos del formulario ─────────────────────────────────
    public $numero_inventario, $descripcion, $cantidad = 1, $precio_unitario = 0;
    public $dependencia_id, $proveedor_id, $cuenta_id, $remito_id;
    public $bien_id = null;
    public $modoEdicion = false;
    public $showModal = false;

    protected $queryString = ['search', 'mostrarInactivos'];

    protected $rules = [
        'numero_inventario' => 'required|string|max:50|unique:bienes,numero_inventario',
        'descripcion' => 'required|string|max:255',
        'cantidad' => 'required|integer|min:1',
        'precio_unitario' => 'required|numeric|min:0',
        'dependencia_id' => 'nullable|exists:dependencias,id',
        'proveedor_id' => 'required|exists:proveedores,id',
        'cuenta_id' => 'required|exists:cuentas,id',
        'remito_id' => 'required|exists:remitos,id',
    ];

    // ─── ABRIR MODAL PARA CREAR ────────────────────────────────
    public function abrirModalCrear()
    {
        $this->resetForm();
        $this->modoEdicion = false;
        $this->showModal = true;

        /** @var \App\Models\Bien|null $ultimoBien */
        $ultimoBien = Bien::orderByDesc('id')->first();
        $ultimoNumero = $ultimoBien?->numero_inventario;

        $this->numero_inventario = $this->generarSiguienteNumero($ultimoNumero);
    }

    private function generarSiguienteNumero($ultimoNumero)
    {
        if (!$ultimoNumero) {
            return 'INV-001';
        }

        if (preg_match('/^([A-Za-z\-]*)(\d+)$/', $ultimoNumero, $m)) {
            $prefijo = $m[1];
            $numero = (int)$m[2] + 1;
            $longitud = strlen($m[2]);
            return $prefijo . str_pad($numero, $longitud, '0', STR_PAD_LEFT);
        }

        return is_numeric($ultimoNumero)
            ? str_pad($ultimoNumero + 1, 3, '0', STR_PAD_LEFT)
            : 'INV-001';
    }

    // ─── EDITAR BIEN ───────────────────────────────────────────
    public function editar($id)
    {
        $bien = Bien::findOrFail($id);

        $this->bien_id = $bien->id;
        $this->numero_inventario = $bien->numero_inventario;
        $this->descripcion = $bien->descripcion;
        $this->cantidad = $bien->cantidad;
        $this->precio_unitario = $bien->precio_unitario;
        $this->dependencia_id = $bien->dependencia_id;
        $this->proveedor_id = $bien->proveedor_id;
        $this->cuenta_id = $bien->cuenta_id;
        $this->remito_id = $bien->remito_id;
        $this->modoEdicion = true;
        $this->showModal = true;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    // ─── GUARDAR NUEVO BIEN ───────────────────────────────────
    public function guardar()
    {
        $this->validate();

        Bien::create([
            'numero_inventario' => $this->numero_inventario,
            'descripcion' => $this->descripcion,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'monto_total' => $this->cantidad * $this->precio_unitario,
            'dependencia_id' => $this->dependencia_id,
            'proveedor_id' => $this->proveedor_id,
            'cuenta_id' => $this->cuenta_id,
            'remito_id' => $this->remito_id,
            'estado' => 'stock',
            'bien_uso' => 1,
            'bien_consumo' => 0,
            'user_id' => auth()->id(),
        ]);

        $this->cerrarModal();
        session()->flash('message', 'Bien agregado correctamente ✅');
    }

    // ─── ACTUALIZAR BIEN ──────────────────────────────────────
    public function actualizar()
    {
        $this->validate([
            'numero_inventario' => 'required|string|max:50|unique:bienes,numero_inventario,' . $this->bien_id,
            'descripcion' => 'required|string|max:255',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'dependencia_id' => 'nullable|exists:dependencias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'cuenta_id' => 'required|exists:cuentas,id',
            'remito_id' => 'required|exists:remitos,id',
        ]);

        $bien = Bien::findOrFail($this->bien_id);

        $bien->update([
            'numero_inventario' => $this->numero_inventario,
            'descripcion' => $this->descripcion,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'monto_total' => $this->cantidad * $this->precio_unitario,
            'dependencia_id' => $this->dependencia_id,
            'proveedor_id' => $this->proveedor_id,
            'cuenta_id' => $this->cuenta_id,
            'remito_id' => $this->remito_id,
        ]);

        $this->cerrarModal();
        session()->flash('message', 'Bien actualizado correctamente ✏️');
    }

    private function resetForm()
    {
        $this->reset([
            'numero_inventario', 'descripcion', 'cantidad', 'precio_unitario',
            'dependencia_id', 'proveedor_id', 'cuenta_id', 'remito_id',
            'bien_id', 'modoEdicion'
        ]);
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingMostrarInactivos() { $this->resetPage(); }

    public function render()
    {
        $query = Bien::with(['dependencia', 'proveedor', 'cuenta', 'remito']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('numero_inventario', 'like', "%{$this->search}%")
                  ->orWhere('descripcion', 'like', "%{$this->search}%");
            });
        }

        if (!$this->mostrarInactivos) {
            $query->where('estado', '!=', 'baja');
        }

        $bienes = $query->orderByDesc('id')->paginate(8);

        return view('livewire.admin.bienes', [
            'bienes' => $bienes,
            'dependencias' => Dependencia::orderBy('nombre')->get(),
            'proveedores' => Proveedor::orderBy('razon_social')->get(),
            'cuentas' => Cuenta::orderBy('codigo')->get(),
            'remitos' => Remito::orderBy('numero_remito')->get(),
        ]);
    }

    // ─── Acciones ─────────────────────────────────────────────
    public function darDeBajaBien($id)
    {
        if ($bien = Bien::find($id)) {
            $bien->update(['estado' => 'baja']);
            session()->flash('message', 'Bien dado de baja 🚫');
        }
    }

    public function activarBien($id)
    {
        if ($bien = Bien::find($id)) {
            $bien->update(['estado' => 'stock']);
            session()->flash('message', 'Bien reactivado ✅');
        }
    }

    public function verDetalle($id)
    {
        $this->detalleBien = Bien::with(['dependencia', 'proveedor', 'cuenta', 'remito'])->find($id);
    }

    public function cerrarDetalle()
    {
        $this->detalleBien = null;
    }
}
