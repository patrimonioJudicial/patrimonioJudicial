<?php

namespace App\Livewire;
use App\Models\Remito;
use Illuminate\Support\Facades\Auth;
use App\Models\Bien;
use App\Models\Cuenta;
use App\Models\Proveedor;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ReceptorPanel extends Component
{
    use WithFileUploads, WithPagination;

    public $formularios = [];
    public $bienes = [];
    public $numero_remito = '';
    public $numero_expediente = '';
    public $orden_provision = '';
    public $fecha_recepcion = '';
    public $foto_remito = null;
    public $proveedor_id = '';
    public $tipo_compra = 'directa'; // o licitacion
    public $mostrarRegistros = false;
    public $editandoBien = null;


    public function mount()
    {
        $this->formularios[] = $this->formularioVacio();
    }

    // 🔹 Método para volver al formulario
    public function volverAlFormulario()
{
    $this->mostrarRegistros = false;
}


    private function formularioVacio()
    {
        return [
            'cuenta_id' => '',
            'numero_inventario' => '',
            'descripcion' => '',
            'cantidad' => 1,
            'precio_unitario' => '',
            'monto_total' => '',
            'fecha_recepcion' => date('Y-m-d'),
            'proveedor_id' => '',
            'tipo_bien' => 'uso',
            'compra_licitacion' => false,
        ];
    }

    public function agregarFormulario()
    {
        $this->formularios[] = $this->formularioVacio();
    }

    public function eliminarFormulario($index)
    {
        unset($this->formularios[$index]);
        $this->formularios = array_values($this->formularios);
    }

    public function updatedFormularios($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];

        if (isset($this->formularios[$index]['precio_unitario']) && isset($this->formularios[$index]['cantidad'])) {
            $precio = floatval($this->formularios[$index]['precio_unitario']);
            $cantidad = intval($this->formularios[$index]['cantidad']);
            $this->formularios[$index]['monto_total'] = $precio * $cantidad;
        }
    }

    public function registrarBien()
{
    // Buscar si el remito ya existe
    $remito = Remito::where('numero_remito', $this->numero_remito)->first();

    if ($remito) {
        // 🔹 Si existe, actualiza los datos
        $remito->update([
            'numero_expediente' => $this->numero_expediente,
            'orden_provision'   => $this->orden_provision,
            'fecha_recepcion'   => $this->formularios[0]['fecha_recepcion'],
            'tipo_compra'       => $this->formularios[0]['compra_licitacion'] ? 'licitacion' : 'directa',
            'proveedor_id'      => $this->formularios[0]['proveedor_id'],
            'user_id'           => Auth::id(),
        ]);
    } else {
        // 🔹 Si no existe, lo crea
        $remito = Remito::create([
            'numero_remito'     => $this->numero_remito,
            'numero_expediente' => $this->numero_expediente,
            'orden_provision'   => $this->orden_provision,
            'fecha_recepcion'   => $this->formularios[0]['fecha_recepcion'],
            'tipo_compra'       => $this->formularios[0]['compra_licitacion'] ? 'licitacion' : 'directa',
            'proveedor_id'      => $this->formularios[0]['proveedor_id'],
            'user_id'           => Auth::id(),
        ]);
    }

    // 🔹 Crear los bienes asociados
    foreach ($this->formularios as $form) {
        [$prefijo, $numeroBase] = explode('-', $form['numero_inventario']);
        $numeroBase = intval($numeroBase);

        for ($i = 0; $i < $form['cantidad']; $i++) {
            $numeroInventario = $prefijo . '-' . str_pad($numeroBase + $i, 3, '0', STR_PAD_LEFT);

            Bien::create([
                'cuenta_id'        => $form['cuenta_id'],
                'numero_inventario'=> $numeroInventario,
                'descripcion'      => $form['descripcion'],
                'cantidad'         => 1,
                'precio_unitario'  => $form['precio_unitario'],
                'monto_total'      => $form['precio_unitario'],
                'bien_uso'         => $form['tipo_bien'] === 'uso',
                'bien_consumo'     => $form['tipo_bien'] === 'consumo',
                'estado'           => 'stock',
                'proveedor_id'     => $form['proveedor_id'],
                'remito_id'        => $remito->id,
            ]);
        }
    }

    session()->flash('message', 'Bienes registrados correctamente');
    $this->formularios = [$this->formularioVacio()];
}



    public function cancelar()
    {
        $this->formularios = [$this->formularioVacio()];
    }

    public function render()
{
    return view('livewire.receptor-panel', [
        'cuentas' => Cuenta::where('activo', true)->orderBy('codigo')->get(),
        'proveedores' => Proveedor::where('estado', 1)->orderBy('razon_social')->get(),
        'bienes' => $this->mostrarRegistros
            ? $this->bienes
            : [],
    ])->layout('components.admin-layout', [
        'title' => 'Panel del Receptor',
    ]);
}


public function verRegistros()
{
    $this->bienes = Bien::with(['proveedor', 'cuenta', 'remito'])
        ->orderBy('created_at', 'desc')
        ->get();

    $this->mostrarRegistros = true;
}

// 🔹 Eliminar un bien
public function eliminarBien($id)
{
    $bien = Bien::find($id);
    if ($bien) {
        $bien->delete();
        session()->flash('message', '🗑️ Bien eliminado correctamente.');
    }
    $this->verRegistros(); // refresca la tabla
}

// 🔹 Cargar datos para edición
public function editarBien($id)
{
    $this->editandoBien = Bien::find($id);
}

// 🔹 Guardar cambios
public function actualizarBien()
{
    if ($this->editandoBien) {
        $this->validate([
            'editandoBien.descripcion' => 'required|string|max:255',
            'editandoBien.precio_unitario' => 'required|numeric|min:0',
            'editandoBien.monto_total' => 'required|numeric|min:0',
        ]);

        $this->editandoBien->save();

        session()->flash('message', '✏️ Bien actualizado correctamente.');
        $this->editandoBien = null;
        $this->verRegistros(); // recargar lista
    }
}

}




