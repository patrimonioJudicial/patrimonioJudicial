<?php
// app/Livewire/ReceptorPanel.php

namespace App\Livewire;

use App\Models\Bien;
use App\Models\Cuenta;
use App\Models\Proveedor;
use App\Models\Remito;
use App\Models\OrdenProvision;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReceptorPanel extends Component
{
    use WithFileUploads;

    // Campos del formulario
    public $cuenta_id = '';
    public $fecha_recepcion = '';
    public $numero_inventario = '';
    public $descripcion = '';
    public $tipo_bien = 'uso'; // 'uso' o 'consumo'
    public $compra_licitacion = false;
    public $cantidad = 1;
    public $proveedor_id = '';
    public $precio_unitario = '';
    public $monto_total = '';
    public $numero_remito = '';
    public $numero_expediente = '';
    public $orden_provision = '';
    public $foto_remito = null;

    public function mount()
    {
        // Establecer fecha actual por defecto
        $this->fecha_recepcion = date('Y-m-d');
    }

 public function render()
{
    return view('livewire.receptor-panel', [
        'cuentas' => Cuenta::where('activo', true)->orderBy('codigo')->get(),
        'proveedores' => Proveedor::where('estado', 1)->orderBy('razon_social')->get(),
    ])->layout('components.admin-layout', [
        'title' => 'Panel del Receptor',
    ]);
}

    public function updatedCantidad()
    {
        $this->calcularMontoTotal();
    }

    public function updatedPrecioUnitario()
    {
        $this->calcularMontoTotal();
    }

    private function calcularMontoTotal()
    {
        if ($this->cantidad && $this->precio_unitario) {
            $this->monto_total = $this->cantidad * $this->precio_unitario;
        }
    }

    public function registrarBien()
    {
        $this->validate([
            'cuenta_id' => 'required|exists:cuentas,id',
            'fecha_recepcion' => 'required|date',
            'numero_inventario' => 'required|string|unique:bienes,numero_inventario',
            'descripcion' => 'required|string|max:500',
            'cantidad' => 'required|integer|min:1',
            'proveedor_id' => 'required|exists:proveedores,id',
            'precio_unitario' => 'required|numeric|min:0',
            'monto_total' => 'required|numeric|min:0',
        ], [
            'cuenta_id.required' => 'Debe seleccionar una cuenta',
            'fecha_recepcion.required' => 'La fecha de recepción es obligatoria',
            'numero_inventario.required' => 'El número de inventario es obligatorio',
            'numero_inventario.unique' => 'Este número de inventario ya existe',
            'descripcion.required' => 'La descripción es obligatoria',
            'cantidad.required' => 'La cantidad es obligatoria',
            'proveedor_id.required' => 'Debe seleccionar un proveedor',
            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'monto_total.required' => 'El monto total es obligatorio',
        ]);

        // Crear el bien
        Bien::create([
            'cuenta_id' => $this->cuenta_id,
            'numero_inventario' => $this->numero_inventario,
            'descripcion' => $this->descripcion,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'monto_total' => $this->monto_total,
            'bien_uso' => $this->tipo_bien === 'uso',
            'bien_consumo' => $this->tipo_bien === 'consumo',
            'estado' => 'stock',
            'proveedor_id' => $this->proveedor_id,
            // Aquí puedes agregar la lógica para remito_id si lo necesitas
        ]);

        session()->flash('message', 'Bien registrado correctamente');
        $this->resetFormulario();
    }

    public function cancelar()
    {
        $this->resetFormulario();
    }

    private function resetFormulario()
    {
        $this->cuenta_id = '';
        $this->numero_inventario = '';
        $this->descripcion = '';
        $this->tipo_bien = 'uso';
        $this->compra_licitacion = false;
        $this->cantidad = 1;
        $this->proveedor_id = '';
        $this->precio_unitario = '';
        $this->monto_total = '';
        $this->numero_remito = '';
        $this->numero_expediente = '';
        $this->orden_provision = '';
        $this->foto_remito = null;
        $this->resetValidation();
    }
}
