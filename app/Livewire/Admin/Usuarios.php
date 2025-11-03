<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Validation\Rule;

class Usuarios extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $mostrarInactivos = false;
    public $showModal = false;
    public $showConfirm = false;
    public $usuarioSeleccionado = null;

    public $nombre, $email, $password, $role_id, $modoEdicion = false;

    protected function rules()
    {
        $emailRule = $this->modoEdicion
            ? ['required', 'email', Rule::unique('users', 'email')->ignore($this->usuarioSeleccionado)]
            : ['required', 'email', 'unique:users,email'];

        $passwordRule = $this->modoEdicion
            ? ['nullable', 'string', 'min:6']
            : ['required', 'string', 'min:6'];

        return [
            'nombre' => 'required|string|max:255',
            'email' => $emailRule,
            'password' => $passwordRule,
            'role_id' => 'required|exists:roles,id',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMostrarInactivos()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::with('rol');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if (!$this->mostrarInactivos) {
            $query->where('activo', 1);
        }

        $usuarios = $query->orderBy('nombre')->paginate(10);
        $roles = Rol::orderBy('nombre')->get();

        return view('livewire.admin.usuarios', [
            'usuarios' => $usuarios,
            'roles' => $roles,
        ]);
    }

    // Crear nuevo usuario
    public function guardarUsuario()
    {
        $this->validate();

        User::create([
            'nombre' => $this->nombre,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role_id' => $this->role_id,
            'activo' => 1,
        ]);

        $this->resetFormulario();
        session()->flash('message', 'Usuario creado correctamente ✅');
    }

    // Mostrar modal para editar
    public function editar($id)
    {
        $usuario = User::findOrFail($id);
        $this->usuarioSeleccionado = $usuario->id;
        $this->nombre = $usuario->nombre;
        $this->email = $usuario->email;
        $this->role_id = $usuario->role_id;
        $this->password = '';
        $this->modoEdicion = true;
        $this->showModal = true;
    }

    // Actualizar usuario
    public function actualizarUsuario()
    {
        $this->validate();

        $usuario = User::findOrFail($this->usuarioSeleccionado);
        $usuario->update([
            'nombre' => $this->nombre,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'password' => $this->password ? bcrypt($this->password) : $usuario->password,
        ]);

        $this->resetFormulario();
        session()->flash('message', 'Usuario actualizado correctamente ✏️');
    }

    // Mostrar modal de confirmación de baja
    public function confirmarBaja($id)
    {
        $this->usuarioSeleccionado = $id;
        $this->showConfirm = true;
    }

    // Dar de baja (confirmado)
    public function darDeBajaConfirmado()
    {
        if ($user = User::find($this->usuarioSeleccionado)) {
            $user->update(['activo' => 0]);
            session()->flash('message', 'Usuario dado de baja 🚫');
        }
        $this->showConfirm = false;
    }

    // Activar usuario
    public function activar($id)
    {
        if ($user = User::find($id)) {
            $user->update(['activo' => 1]);
            session()->flash('message', 'Usuario activado ✅');
        }
    }

    // Cerrar modal y limpiar
    public function resetFormulario()
    {
        $this->reset([
            'nombre', 'email', 'password', 'role_id', 'showModal',
            'usuarioSeleccionado', 'modoEdicion'
        ]);
    }
}
