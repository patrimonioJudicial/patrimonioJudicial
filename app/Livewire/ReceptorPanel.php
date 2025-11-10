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
    public $fotos = [];
    public $proveedor_id = '';
    public $tipo_compra = 'directa'; // o licitacion
    public $mostrarRegistros = false;
    public $editandoBien = null;
    
    // 🔑 ¡NUEVO! Contador único para keys
    public $nextId = 1;


    public function mount()
{
    $this->formularios[] = $this->formularioVacio();
    $this->fotos = []; // ✅ Inicializar array de fotos
}

    // 🔹 Método para volver al formulario
    public function volverAlFormulario()
{
    $this->mostrarRegistros = false;
}


    private function formularioVacio()
    {
        return [
            'id' => $this->nextId++, // 🔑 ¡NUEVO! ID único para cada formulario
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

    // 🔧 ¡MODIFICADO! Ahora elimina por el ID único, no por índice
    public function eliminarFormulario($formId)
    {
        $this->formularios = array_filter($this->formularios, function($form) use ($formId) {
            return $form['id'] !== $formId;
        });
        
        // También eliminamos la foto correspondiente si existe
        if (isset($this->fotos[$formId])) {
            unset($this->fotos[$formId]);
        }
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
    // Validación de datos principales
    $this->validate([
        'numero_remito' => 'required|string|max:255',
        'numero_expediente' => 'nullable|string|max:255',
        'orden_provision' => 'nullable|string|max:255',
        'fotos.*' => 'nullable|image|max:2048', // ✅ Validación para array de fotos
        'formularios.*.proveedor_id' => 'required|exists:proveedores,id',
        'formularios.*.cuenta_id' => 'required|exists:cuentas,id',
        'formularios.*.numero_inventario' => 'required|numeric|min:1',
        'formularios.*.descripcion' => 'required|string|max:255',
        'formularios.*.cantidad' => 'required|integer|min:1',
        'formularios.*.precio_unitario' => 'required|numeric|min:0',
        'formularios.*.fecha_recepcion' => 'required|date',
    ]);

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
        $numeroBase = intval($form['numero_inventario']);
        
        // 📸 Guardar la foto individual si existe (usando el ID único del formulario)
      if (isset($this->fotos[$form['id']]) && $this->fotos[$form['id']]) {
    $rutaFoto = $this->fotos[$form['id']]->store('bienes', 'public');
}

        for ($i = 0; $i < $form['cantidad']; $i++) {
            $numeroInventario = $numeroBase + $i;

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
                'foto'      => $rutaFoto, // ✅ Foto individual por bien
            ]);
        }
    }

    session()->flash('message', 'Bienes registrados correctamente');
    
    // 🔄 Reiniciar correctamente
    $this->nextId = 1; // Resetear contador
    $this->formularios = [$this->formularioVacio()];
    $this->fotos = []; // ✅ Limpiar array de fotos
    $this->numero_remito = '';
    $this->numero_expediente = '';
    $this->orden_provision = '';
}


    public function cancelar()
{
    $this->nextId = 1; // Resetear contador
    $this->formularios = [$this->formularioVacio()];
    $this->fotos = []; // ✅ Limpiar fotos
    $this->numero_remito = '';
    $this->numero_expediente = '';
    $this->orden_provision = '';
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




