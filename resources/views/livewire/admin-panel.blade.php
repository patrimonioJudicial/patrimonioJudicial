{{-- resources/views/livewire/admin-panel.blade.php --}}
<div>
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Panel de Administrador</h1>
        <p class="text-gray-600 mt-1">Gestión de usuarios, bienes y dependencias</p>
    </div>

    <!-- Mensajes de éxito -->
    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-800 hover:text-green-900">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button 
                wire:click="setActiveTab('usuarios')"
                class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'usuarios' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Usuarios
            </button>
            <button 
                wire:click="setActiveTab('proveedores')"
                class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'proveedores' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Proveedores
            </button>
            <button 
                wire:click="setActiveTab('bienes')"
                class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'bienes' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Bienes
            </button>
            <button 
                wire:click="setActiveTab('dependencias')"
                class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'dependencias' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Dependencias
            </button>
        </nav>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-lg shadow">
        @if($activeTab === 'usuarios')
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Usuarios del Sistema</h2>
                    <p class="text-sm text-gray-500 mt-1">Gestión de usuarios y roles - {{ $usuarios->count() }} activos</p>
                </div>

                <div class="space-y-4">
                    @forelse($usuarios as $usuario)
                    <div class="flex items-center justify-between p-4 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $usuario->nombre }}</h3>
                            <p class="text-sm text-gray-600">{{ $usuario->email }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            @if($usuario->rol)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-800 text-white">
                                {{ $usuario->rol->nombre }}
                            </span>
                            @endif
                            @if($usuario->id !== Auth::id())
                            <button 
                                wire:click="darDeBaja('usuario', {{ $usuario->id }})"
                                wire:confirm="¿Está seguro de dar de baja a este usuario?"
                                class="flex items-center space-x-1 px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 13H5v-2h14v2z"/>
                                </svg>
                                <span>Dar de baja</span>
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        No hay usuarios registrados
                    </div>
                    @endforelse
                </div>
            </div>
        @endif

        @if($activeTab === 'proveedores')
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 00-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                            </svg>
                            <h2 class="text-xl font-semibold text-gray-900">Proveedores</h2>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Gestión de proveedores - {{ $proveedores->count() }} activos</p>
                    </div>
                    <button 
                        wire:click="abrirModalProveedor"
                        class="flex items-center space-x-2 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Agregar Proveedor</span>
                    </button>
                </div>

                <div class="space-y-4">
                    @forelse($proveedores as $proveedor)
                    <div class="flex items-start justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
    <div class="flex-1">
        <h3 class="font-semibold text-gray-900">{{ $proveedor->razon_social }}</h3>
        <p class="text-sm text-gray-600">CUIL: {{ $proveedor->cuil }}</p>
        <p class="text-sm text-gray-600">{{ $proveedor->email }}</p>
    </div>
    <div class="ml-4 text-right">
        <p class="text-sm text-gray-600 mb-1">{{ $proveedor->telefono }}</p>
        <p class="text-sm text-gray-600">{{ $proveedor->direccion }}</p>
    </div>
    <div class="ml-4 flex items-center space-x-2">
        <button 
            wire:click="editarProveedor({{ $proveedor->id }})"
            class="flex items-center space-x-1 px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Editar</span>
        </button>
        <button 
            wire:click="darDeBaja('proveedor', {{ $proveedor->id }})"
            wire:confirm="¿Está seguro de dar de baja a este proveedor?"
            class="flex items-center space-x-1 px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 13H5v-2h14v2z"/>
            </svg>
            <span>Dar de baja</span>
        </button>
    </div>
</div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        No hay proveedores registrados
                    </div>
                    @endforelse
                </div>
            </div>
        @endif

        @if($activeTab === 'bienes')
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Listado de Bienes</h2>
                    <p class="text-sm text-gray-500 mt-1">Todos los bienes registrados en el sistema - {{ $bienes->count() }} total</p>
                </div>

                <div class="space-y-4">
                    @forelse($bienes as $bien)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $bien->descripcion }}</h3>
                            <p class="text-sm text-gray-600">N° Inventario: {{ $bien->numero_inventario }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            @if($bien->bien_uso)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-800 text-white">
                                Uso
                            </span>
                            @endif
                            @if($bien->bien_consumo)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-700 text-white">
                                Consumo
                            </span>
                            @endif
                            @if($bien->estado === 'asignado')
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-800 text-white">
                                Asignado
                            </span>
                            @elseif($bien->estado === 'stock')
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-600 text-white">
                                Stock
                            </span>
                            @elseif($bien->estado === 'consumo')
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-700 text-white">
                                Consumo
                            </span>
                            @elseif($bien->estado === 'baja')
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-600 text-white">
                                Baja
                            </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        No hay bienes registrados
                    </div>
                    @endforelse
                </div>
            </div>
        @endif

        @if($activeTab === 'dependencias')
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Dependencias Judiciales</h2>
                    <p class="text-sm text-gray-500 mt-1">Listado de todas las dependencias registradas - {{ $dependencias->count() }} total</p>
                </div>

                <div class="space-y-4">
                    @forelse($dependencias as $dependencia)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $dependencia->nombre }}</h3>
                            <p class="text-sm text-gray-600">Código: {{ $dependencia->codigo }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-700">{{ $dependencia->ubicacion }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        No hay dependencias registradas
                    </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    <!-- Modal para Agregar Proveedor -->
    @if($showProveedorModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cerrarModalProveedor"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                {{ $modoEdicion ? 'Editar Proveedor' : 'Agregar Nuevo Proveedor' }}
                            </h3>
                            
                            <form wire:submit.prevent="guardarProveedor" class="space-y-4">
                                <!-- Razón Social -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Razón Social <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="razon_social"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                    @error('razon_social') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- CUIL -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        CUIL <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="cuil"
                                        placeholder="20-12345678-9"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                    @error('cuil') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Nombre Contacto -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre de Contacto <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="nombre_contacto"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                    @error('nombre_contacto') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Teléfono -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Teléfono <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="telefono"
                                        placeholder="011-1234-5678"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                    @error('telefono') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="email" 
                                        wire:model="email"
                                        placeholder="ejemplo@correo.com"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                    @error('email') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Dirección -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Dirección <span class="text-red-500">*</span>
                                    </label>
                                    <textarea 
                                        wire:model="direccion"
                                        rows="2"
                                        placeholder="Calle, número, ciudad"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required></textarea>
                                    @error('direccion') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Botones del modal -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        type="button"
                        wire:click="guardarProveedor"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        {{ $modoEdicion ? 'Actualizar' : 'Guardar' }}
                </button>
                    <button 
                        type="button"
                        wire:click="cerrarModalProveedor"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>