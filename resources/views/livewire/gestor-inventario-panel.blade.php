{{-- resources/views/livewire/repartidor-panel.blade.php --}}
<div>
    
    <!-- Page Header -->
  <h1 class="text-3xl font-bold text-gray-900">Panel del Gestor de Inventario</h1>
<p class="text-gray-600 mt-1">Asignación y control de bienes patrimoniales</p>


    <!-- Mensajes de éxito/error -->
    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
            <span>✅ {{ session('message') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-800 hover:text-green-900">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
            <span>❌ {{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-800 hover:text-red-900">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Vista de Asignaciones Recientes -->
    @if($mostrarAsignaciones)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Asignaciones Recientes
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

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Inventario</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Descripción</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Cuenta</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Dependencia</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Fecha</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">QR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($asignaciones as $asignacion)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-gray-800">
                                    {{ $asignacion->bien->numero_inventario }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-800">
                                    {{ $asignacion->bien->descripcion }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-800">
                                    {{ $asignacion->bien->cuenta->codigo }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-800">
                                    {{ $asignacion->dependencia->nombre }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-800">
                                    {{ \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-2">
    <button 
        wire:click="generarQR({{ $asignacion->bien->id }})"
        class="flex items-center gap-1 px-2 py-1 text-xs bg-indigo-50 text-indigo-600 border border-indigo-200 rounded hover:bg-indigo-100"
        title="Generar Código QR">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
    </button>
</td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    No hay asignaciones registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Panel Principal - Grid de dos columnas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            
            <!-- Panel Izquierdo: Bienes en Stock -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-900">Bienes en Stock</h2>
                    </div>
                    <span class="text-sm text-gray-600">
                        Seleccione los bienes a asignar
                    </span>
                </div>

                <div class="space-y-2 max-h-[600px] overflow-y-auto">
                    @forelse($bienesStock as $bien)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input 
                                    type="checkbox" 
    value="{{ $bien->id }}"
    wire:click="toggleBien({{ $bien->id }})"
    @checked(in_array($bien->id, $bienesSeleccionados))
    class="mt-1 w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
    <div class="flex items-center gap-3">
        <span class="font-semibold text-gray-900">{{ $bien->numero_inventario }}</span>

        @if($bien->remito && $bien->remito->foto_remito)
            <img 
                src="{{ asset('storage/' . $bien->remito->foto_remito) }}" 
                alt="Foto del remito"
                wire:click="verFotoBien({{ $bien->id }})"
                class="w-12 h-12 object-cover rounded border border-gray-200 shadow-sm hover:scale-105 transition-transform cursor-pointer"
                title="Clic para ampliar">
        @else
            <div class="w-12 h-12 flex items-center justify-center border border-dashed border-gray-300 text-gray-400 text-[10px] rounded">
                Sin foto
            </div>
        @endif
    </div>
</div>

                                    <p class="text-sm text-gray-700 mb-2">{{ $bien->descripcion }}</p>
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                            O. Provisión: {{ $bien->remito->orden_provision ?? 'N/A' }}
                                        </span>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                            Expediente: {{ $bien->remito->numero_expediente ?? 'N/A' }}
                                        </span>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                            Fecha Recepción: {{ $bien->remito->fecha_recepcion ? \Carbon\Carbon::parse($bien->remito->fecha_recepcion)->format('d/m/Y') : 'N/A' }}
                                        </span>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-medium">
                                            {{ $bien->bien_uso ? 'Bien de Uso' : 'Bien de Consumo' }}
                                        </span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="text-lg font-medium">No hay bienes en stock</p>
                            <p class="text-sm mt-1">Los bienes aparecerán aquí cuando sean registrados por el receptor</p>
                        </div>
                    @endforelse
                </div>

                @if(count($bienesStock) > 0)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold">{{ count($bienesSeleccionados) }}</span> 
                            bien(es) seleccionado(s)
                        </p>
                    </div>
                @endif
            </div>

            <!-- Panel Derecho: Asignar a Dependencia -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900">Asignar a Dependencia</h2>
                </div>

                <p class="text-sm text-gray-600 mb-6">Complete los datos de asignación</p>

                <form wire:submit.prevent="asignarBienes" class="space-y-6">
                    
                    <!-- Bienes seleccionados -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-2">Bienes seleccionados:</h3>

    @if(count($bienesSeleccionados) === 0)
        <p class="text-sm text-gray-500 italic">Ningún bien seleccionado</p>
    @else
        <ul class="space-y-1 text-sm text-gray-700">
            @foreach($bienesStock->whereIn('id', $bienesSeleccionados) as $bien)
                <li class="flex items-center justify-between bg-white border border-gray-100 rounded-md px-3 py-1">
                    <span class="font-medium">{{ $bien->numero_inventario }} - {{ $bien->descripcion }}</span>
                    <button 
                        type="button"
                        wire:click="toggleBien({{ $bien->id }})"
                        class="text-red-500 hover:text-red-700 text-xs"
                        title="Quitar bien">
                        ✕
                    </button>
                </li>
            @endforeach
        </ul>

        <p class="text-xs text-gray-500 mt-2">
            Total: <span class="font-semibold text-indigo-600">{{ count($bienesSeleccionados) }}</span> bien(es)
        </p>
    @endif
</div>


                    <!-- Dependencia Destino -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dependencia Destino <span class="text-red-500">*</span>
                        </label>
                        <select 
                            wire:model="dependencia_id"
                            class="w-full px-3 py-2 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Seleccione una dependencia</option>
                            @foreach($dependencias as $dependencia)
                                <option value="{{ $dependencia->id }}">
                                    {{ $dependencia->codigo }} - {{ $dependencia->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Fecha de Asignación -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Asignación <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            wire:model="fecha_asignacion"
                            class="w-full px-3 py-2 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Observación -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Observación (opcional)
                        </label>
                        <textarea 
                            wire:model="observacion"
                            rows="3"
                            placeholder="Ej: Asignado para uso en sala de audiencias..."
                            class="w-full px-3 py-2 border-gray-200 bg-white rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button 
                            type="button"
                            wire:click="cancelar"
                            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors">
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium transition-colors flex items-center space-x-2"
                            @if(count($bienesSeleccionados) === 0) disabled @endif>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span>Asignar Bienes</span>
                        </button>
                    </div>
                </form>
                

            </div>
        </div>

        <!-- Botón Ver Asignaciones -->
        <div class="flex justify-end">
            <button 
                wire:click="verAsignaciones"
                class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9z"/>
                </svg>
                <span>Ver Asignaciones Recientes</span>
            </button>
        </div>
    @endif

    <!-- Modal para ver foto -->
    @if($mostrarModalFoto)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" wire:click="cerrarModalFoto">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 p-6" wire:click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Foto del Remito - {{ $bienSeleccionado->numero_inventario ?? 'N/A' }}
                    </h3>
                    <button 
                        wire:click="cerrarModalFoto"
                        class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="flex justify-center">
                    <img src="{{ $fotoUrl }}" alt="Foto del remito" class="max-w-full max-h-[70vh] rounded-lg shadow">
                </div>
                
                <div class="mt-4 flex justify-end">
                    <button 
                        wire:click="cerrarModalFoto"
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>