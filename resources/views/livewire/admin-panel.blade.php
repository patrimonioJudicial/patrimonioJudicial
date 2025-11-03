<div class="space-y-8">
    <!-- HEADER MEJORADO -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Panel de Administrador</h1>
                <p class="mt-1 text-sm text-gray-500">Gestión de usuarios, bienes y dependencias</p>
            </div>
    
        </div>
    </div>

    <!-- TABS MEJORADOS CON ICONOS -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex overflow-x-auto">
            <button wire:click="$set('tab', 'usuarios')"
                class="flex-1 flex items-center justify-center space-x-2 px-6 py-4 text-sm font-medium transition-all duration-200 
                    {{ $tab === 'usuarios' 
                        ? 'bg-blue-50 text-blue-700 border-b-2 border-blue-600' 
                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                <span>Usuarios</span>
            </button>

            <button wire:click="$set('tab', 'proveedores')"
                class="flex-1 flex items-center justify-center space-x-2 px-6 py-4 text-sm font-medium transition-all duration-200
                    {{ $tab === 'proveedores' 
                        ? 'bg-blue-50 text-blue-700 border-b-2 border-blue-600' 
                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                <span>Proveedores</span>
            </button>

            <button wire:click="$set('tab', 'bienes')"
                class="flex-1 flex items-center justify-center space-x-2 px-6 py-4 text-sm font-medium transition-all duration-200
                    {{ $tab === 'bienes' 
                        ? 'bg-blue-50 text-blue-700 border-b-2 border-blue-600' 
                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Bienes</span>
            </button>

            <button wire:click="$set('tab', 'dependencias')"
                class="flex-1 flex items-center justify-center space-x-2 px-6 py-4 text-sm font-medium transition-all duration-200
                    {{ $tab === 'dependencias' 
                        ? 'bg-blue-50 text-blue-700 border-b-2 border-blue-600' 
                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                </svg>
                <span>Dependencias</span>
            </button>
        </div>
    </div>

    <!-- CONTENIDO CON ANIMACIÓN MEJORADA -->
    <div class="transition-all duration-300 ease-in-out">
        @if ($tab === 'usuarios')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Usuarios del Sistema</h2>
                    <p class="text-sm text-gray-500 mt-1">Gestión de usuarios y roles</p>
                </div>
                @livewire('admin.usuarios')
            </div>
        @elseif ($tab === 'proveedores')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Proveedores</h2>
                    <p class="text-sm text-gray-500 mt-1">Gestión de proveedores del sistema</p>
                </div>
                @livewire('admin.proveedores')
            </div>
        @elseif ($tab === 'bienes')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Bienes Patrimoniales</h2>
                    <p class="text-sm text-gray-500 mt-1">Gestión de inventario y bienes</p>
                </div>
                @livewire('admin.bienes')
            </div>
        @elseif ($tab === 'dependencias')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Dependencias</h2>
                    <p class="text-sm text-gray-500 mt-1">Gestión de dependencias y ubicaciones</p>
                </div>
                @livewire('admin.dependencias')
            </div>
        @endif
    </div>
</div>