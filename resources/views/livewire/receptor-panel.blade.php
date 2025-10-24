{{-- resources/views/livewire/receptor-panel.blade.php --}}
<div>
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Panel de Receptor</h1>
        <p class="text-gray-600 mt-1">Registro y alta de bienes patrimoniales</p>
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

    <!-- Formulario de Registro -->
    <div class="bg-gray-50 rounded-lg p-6">
        <div class="mb-6">
            <div class="flex items-center space-x-2 mb-2">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900">Registro de Nuevo Bien</h2>
            </div>
            <p class="text-sm text-gray-600">Complete los datos obligatorios para dar de alta un nuevo bien</p>
        </div>

        <form wire:submit.prevent="registrarBien">
            <!-- Primera fila: N° de Remito, N° de Expediente, Orden de Provisión -->
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° de Remito</label>
                    <input 
                        type="text" 
                        wire:model="numero_remito"
                        placeholder="REM-2024-0000"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° de Expediente</label>
                    <input 
                        type="text" 
                        wire:model="numero_expediente"
                        placeholder="EXP-2024-0000"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Orden de Provisión</label>
                    <input 
                        type="text" 
                        wire:model="orden_provision"
                        placeholder="OP-2024-0000"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Segunda fila: Cuenta del Bien, N° de Inventario, Cantidad -->
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Cuenta del Bien <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="cuenta_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">Seleccione cuenta</option>
                        @foreach($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}">{{ $cuenta->codigo }} - {{ $cuenta->descripcion }}</option>
                        @endforeach
                    </select>
                    @error('cuenta_id') 
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Número de Inventario Inicial <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="numero_inventario"
                        placeholder="Ej: Rg 001 451-9743"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <p class="text-xs text-gray-500 mt-1">El número correlativo se incrementará automáticamente según la cantidad ingresada</p>
                    @error('numero_inventario') 
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Cantidad <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        wire:model.live="cantidad"
                        placeholder="Ej: 5"
                        min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    @error('cantidad') 
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>
            </div>

            <!-- Tercera fila: Descripción del Bien -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Descripción del Bien <span class="text-red-500">*</span>
                </label>
                <textarea 
                    wire:model="descripcion"
                    rows="3"
                    placeholder="Ej: Silla ergonómica tapizada negra con apoyabrazos"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required></textarea>
                @error('descripcion') 
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Cuarta fila: Precio Unitario, Monto Total, Fecha de Recepción -->
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Precio Unitario <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        wire:model.live="precio_unitario"
                        placeholder="Ej: 1500.00"
                        step="0.01"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    @error('precio_unitario') 
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Monto Total <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        wire:model="monto_total"
                        placeholder="Ej: 7500.00"
                        step="0.01"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100"
                        readonly>
                    @error('monto_total') 
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha de Recepción <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        wire:model="fecha_recepcion"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    @error('fecha_recepcion') 
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>
            </div>

            <!-- Quinta fila: Proveedor, Foto del Remito -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Proveedor <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="proveedor_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">Seleccione proveedor</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}">{{ $proveedor->razon_social }}</option>
                        @endforeach
                    </select>
                    @error('proveedor_id') 
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto del Remito</label>
                    <input 
                        type="file" 
                        wire:model="foto_remito"
                        accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Sexta fila: Tipo de Bien y Tipo de Compra -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Bien <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                wire:model="tipo_bien"
                                value="uso"
                                class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                checked>
                            <span class="ml-2 text-sm text-gray-700">Bien de Uso (perdurable en el tiempo)</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                wire:model="tipo_bien"
                                value="consumo"
                                class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Bien de Consumo (vida útil corta)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Compra</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model="compra_licitacion"
                                class="w-4 h-4 text-blue-600 focus:ring-blue-500 rounded">
                            <span class="ml-2 text-sm text-gray-700">Compra por Licitación</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Si no está marcado, se considera Compra Directa</p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <button 
                    type="button"
                    wire:click="cancelar"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors">
                    Cancelar
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Registrar Bien</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Botón Ver Registros (opcional) -->
    <div class="mt-6 flex justify-end">
        <a href="#" class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>Ver Registros</span>
        </a>
    </div>
</div>
