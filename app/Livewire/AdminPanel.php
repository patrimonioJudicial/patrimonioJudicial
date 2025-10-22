<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Proveedor;
use App\Models\Bien;
use App\Models\Dependencia;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AdminPanel extends Component
{
    public $activeTab = 'usuarios';
    
    // Propiedades para el modal de proveedor
    public $showProveedorModal = false;
    public $proveedorId = null; // 👈 Nueva propiedad para editar
    public $modoEdicion = false; // 👈 Nueva propiedad
    public $razon_social = '';
    public $cuil = '';
    public $nombre_contacto = '';
    public $telefono = '';
    public $email = '';
    public $direccion = '';
    
    public function render()
    {
        $data = [];
        
        switch ($this->activeTab) {
            case 'usuarios':
                $data['usuarios'] = User::where('activo', true)
                    ->with('rol')
                    ->orderBy('nombre')
                    ->get();
                break;
            case 'proveedores':
                $data['proveedores'] = Proveedor::where('estado', 1)
                    ->orderBy('razon_social')
                    ->get();
                break;
            case 'bienes':
                $data['bienes'] = Bien::with(['dependencia', 'proveedor'])
                    ->orderBy('numero_inventario')
                    ->get();
                break;
            case 'dependencias':
                $data['dependencias'] = Dependencia::where('activo', true)
                    ->orderBy('nombre')
                    ->get();
                break;
        }
        
        return view('livewire.admin-panel', $data);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function darDeBaja($type, $id)
    {
        switch ($type) {
            case 'usuario':
                $user = User::find($id);
                if ($user && $user->id !== Auth::id()) {
                    $user->activo = false;
                    $user->save();
                    session()->flash('message', 'Usuario dado de baja correctamente');
                }
                break;
            case 'proveedor':
                $proveedor = Proveedor::find($id);
                if ($proveedor) {
                    $proveedor->estado = 0;
                    $proveedor->save();
                    session()->flash('message', 'Proveedor dado de baja correctamente');
                }
                break;
        }
    }
    
    // Métodos para el modal de proveedor
    public function abrirModalProveedor()
    {
        $this->modoEdicion = false;
        $this->showProveedorModal = true;
        $this->resetearFormulario();
    }
    
    // 👇 Nuevo método para editar
    public function editarProveedor($id)
    {
        $this->modoEdicion = true;
        $this->proveedorId = $id;
        
        $proveedor = Proveedor::find($id);
        
        if ($proveedor) {
            $this->razon_social = $proveedor->razon_social;
            $this->cuil = $proveedor->cuil;
            $this->nombre_contacto = $proveedor->nombre_contacto;
            $this->telefono = $proveedor->telefono;
            $this->email = $proveedor->email;
            $this->direccion = $proveedor->direccion;
            
            $this->showProveedorModal = true;
        }
    }
    
    public function cerrarModalProveedor()
    {
        $this->showProveedorModal = false;
        $this->resetearFormulario();
    }
    
    public function resetearFormulario()
    {
        $this->proveedorId = null;
        $this->modoEdicion = false;
        $this->razon_social = '';
        $this->cuil = '';
        $this->nombre_contacto = '';
        $this->telefono = '';
        $this->email = '';
        $this->direccion = '';
        $this->resetValidation();
    }
    
    public function guardarProveedor()
    {
        $rules = [
            'razon_social' => 'required|string|max:255',
            'nombre_contacto' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'direccion' => 'required|string|max:500',
        ];
        
        // Solo validar CUIL único si estamos creando o si cambió el CUIL
        if ($this->modoEdicion) {
            $rules['cuil'] = 'required|string|max:20|unique:proveedores,cuil,' . $this->proveedorId;
        } else {
            $rules['cuil'] = 'required|string|max:20|unique:proveedores,cuil';
        }
        
        $this->validate($rules, [
            'razon_social.required' => 'La razón social es obligatoria',
            'cuil.required' => 'El CUIL es obligatorio',
            'cuil.unique' => 'Este CUIL ya está registrado',
            'nombre_contacto.required' => 'El nombre de contacto es obligatorio',
            'telefono.required' => 'El teléfono es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser una dirección válida',
            'direccion.required' => 'La dirección es obligatoria',
        ]);
        
        $datos = [
            'razon_social' => $this->razon_social,
            'cuil' => $this->cuil,
            'nombre_contacto' => $this->nombre_contacto,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'estado' => 1,
        ];
        
        if ($this->modoEdicion) {
            // Actualizar proveedor existente
            $proveedor = Proveedor::find($this->proveedorId);
            $proveedor->update($datos);
            session()->flash('message', 'Proveedor actualizado correctamente');
        } else {
            // Crear nuevo proveedor
            Proveedor::create($datos);
            session()->flash('message', 'Proveedor agregado correctamente');
        }
        
        $this->cerrarModalProveedor();
    }
}