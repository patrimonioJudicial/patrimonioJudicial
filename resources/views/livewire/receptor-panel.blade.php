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

@if($mostrarRegistros)
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M3 7h18M3 12h18m-9 5h9"/>
            </svg>
            Bienes Registrados
        </h2>
        <button wire:click="volverAlFormulario" 
            class="flex items-center gap-2 px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 19l-7-7 7-7" />
            </svg>
            <span>Volver</span>
        </button>
    </div>

    <table class="min-w-full border border-gray-200 text-sm">
    <thead class="bg-gray-100 text-gray-700">
        <tr>
            <th class="px-3 py-2 text-left">#</th>
            <th class="px-3 py-2 text-left">N° Remito</th>
            <th class="px-3 py-2 text-left">Expediente</th>
            <th class="px-3 py-2 text-left">Orden de Provisión</th>
            <th class="px-3 py-2 text-left">Descripción</th>
            <th class="px-3 py-2 text-left">Cuenta</th>
            <th class="px-3 py-2 text-left">Cantidad</th>
            <th class="px-3 py-2 text-left">Precio Unitario</th>
            <th class="px-3 py-2 text-left">Monto Total</th>
            <th class="px-3 py-2 text-left">Proveedor</th>
            <th class="px-3 py-2 text-left">Fecha Recepción</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
        @forelse($bienes as $index => $bien)
            <tr>
                <td class="px-3 py-2">{{ $index + 1 }}</td>
                <td class="px-3 py-2">{{ $bien->remito->numero_remito ?? '-' }}</td>
                <td class="px-3 py-2">{{ $bien->remito->numero_expediente ?? '-' }}</td>
                <td class="px-3 py-2">{{ $bien->remito->orden_provision ?? '-' }}</td>
                <td class="px-3 py-2">{{ $bien->descripcion }}</td>
                <td class="px-3 py-2">{{ $bien->cuenta->descripcion ?? '-' }}</td>
                <td class="px-3 py-2">{{ $bien->cantidad }}</td>
                <td class="px-3 py-2">${{ number_format($bien->precio_unitario, 2) }}</td>
                <td class="px-3 py-2">${{ number_format($bien->monto_total, 2) }}</td>
                <td class="px-3 py-2">{{ $bien->proveedor->razon_social ?? '-' }}</td>
                <td class="px-3 py-2">
                    {{ \Carbon\Carbon::parse($bien->remito->fecha_recepcion ?? $bien->fecha_recepcion)->format('d/m/Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center py-4 text-gray-500">
                    No hay bienes registrados.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>
@endif

        <form wire:submit.prevent="registrarBien">
          <!-- 🔹 Datos del Remito -->
           <!-- 🔹 Botón Ver Registros -->
<div class="flex justify-end mb-4">
    <button 
        type="button" 
        wire:click="verRegistros"
        class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9z"/>
        </svg>
        <span>Ver Registros</span>
    </button>
</div>

<div class="mb-6">
    <!-- Encabezado -->

    <!-- Recuadro con los tres campos -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M7 8h10M7 12h10m-5 4h5m2 4H5a2 2 0 01-2-2V6a2 2 0 
                     012-2h9l5 5v11a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-base font-semibold text-gray-800">Datos del Remito</h3>
    </div>
        <div class="grid grid-cols-3 gap-6">
            <div>
                <label class="text-sm font-medium text-gray-700">
                    N° de Remito<span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model="numero_remito"
                    placeholder="REM-2024-0000"
                    class="w-full mt-1 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">
                    N° de Expediente<span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model="numero_expediente"
                    placeholder="EXP-2024-0000"
                    class="w-full mt-1 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">
                    Orden de Provisión<span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model="orden_provision"
                    placeholder="OP-2024-0000"
                    class="w-full mt-1 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </div>
</div>

          <!-- 🔹 Información del Bien -->
<div class="mb-6">
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 shadow-sm space-y-6">

        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 7h18M3 12h18m-9 5h9"/>
                </svg>
                <h3 class="text-base font-semibold text-gray-800">Información del Bien</h3>
            </div>

            <!-- 🔘 Botón para agregar nuevo bien -->
            <button 
                type="button"
                wire:click="agregarFormulario"
                class="flex items-center gap-1 px-3 py-1 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                <span>Agregar</span>
            </button>
        </div>

        <!-- 🔁 Se repite un formulario por cada bien -->
        @foreach($formularios as $index => $form)
        <div class="border border-gray-200 rounded-lg p-5 bg-white space-y-6 relative">

            <!-- 🗑️ Botón para eliminar formulario -->
            @if(count($formularios) > 1)
    <button 
        type="button"
        wire:click="eliminarFormulario({{ $index }})"
        class="absolute top-2 right-2 flex items-center gap-1 px-2 py-1 text-xs bg-red-50 text-red-600 border border-red-200 rounded-md hover:bg-red-100 hover:text-red-700 transition"
        title="Eliminar este bien">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h10"/>
        </svg>
        <span>Eliminar</span>
    </button>
@endif


            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cuenta del Bien *</label>
                    <select 
                        wire:model="formularios.{{ $index }}.cuenta_id"
                        class="w-full px-3 py-2 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccione cuenta</option>
                        @foreach($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}">{{ $cuenta->codigo }} - {{ $cuenta->descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° Inventario Inicial *</label>
                    <input 
                        type="text" 
                        wire:model="formularios.{{ $index }}.numero_inventario"
                        placeholder="Ej: RG-001-9743"
                        class="w-full px-3 py-2 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                    <input
    type="number" 
    wire:model.live="formularios.{{ $index }}.cantidad"
    min="1"
    class="w-full px-3 py-2 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
    placeholder="Ej: 5">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                <textarea 
                    wire:model="formularios.{{ $index }}.descripcion"
                    rows="2"
                    placeholder="Ej: Silla ergonómica negra con apoyabrazos"
                    class="w-full px-3 py-2 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Unitario *</label>
                    <input 
    type="number" 
    wire:model.lazy="formularios.{{ $index }}.precio_unitario"
    step="0.01"
    min="0"
    placeholder="Ej: 1500.00"
    class="w-full px-3 py-2 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto Total *</label>
                    <input 
    type="number" 
    wire:model="formularios.{{ $index }}.monto_total"
    readonly
    class="w-full px-3 py-2 border-gray-200 bg-gray-100 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Recepción *</label>
                    <input 
                        type="date" 
                        wire:model="formularios.{{ $index }}.fecha_recepcion"
                        class="w-full px-3 py-2 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor *</label>
                    <select 
                        wire:model="formularios.{{ $index }}.proveedor_id"
                        class="w-full px-3 py-2 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccione proveedor</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}">{{ $proveedor->razon_social }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto del Remito</label>
                    <input 
                        type="file" 
                        wire:model="formularios.{{ $index }}.foto_remito"
                        accept="image/*"
                        class="w-full px-3 py-2 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Bien *</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                wire:model="formularios.{{ $index }}.tipo_bien"
                                value="uso"
                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                                checked>
                            <span class="ml-2 text-sm text-gray-700">Bien de Uso</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                wire:model="formularios.{{ $index }}.tipo_bien"
                                value="consumo"
                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Bien de Consumo</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Compra</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model="formularios.{{ $index }}.compra_licitacion"
                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 rounded">
                            <span class="ml-2 text-sm text-gray-700">Compra por Licitación</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Si no está marcado, se considera Compra Directa</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
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

    
</div>
