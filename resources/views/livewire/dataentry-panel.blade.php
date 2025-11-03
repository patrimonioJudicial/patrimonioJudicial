{{-- resources/views/livewire/dataentry-panel.blade.php --}}
<div class="space-y-8">

    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Panel de Cargador</h1>
            <p class="text-gray-600">Completar documentación de bienes</p>
        </div>

        <div class="flex gap-2">
            <button wire:click="mostrarModal('sin-asignar')" 
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Bienes Sin Asignar
            </button>

            <button wire:click="mostrarModal('exportar')" 
                class="flex items-center gap-2 px-4 py-2 bg-indigo-900 text-white rounded-lg hover:bg-indigo-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar a Excel
            </button>
        </div>
    </div>

    <!-- Mensajes -->
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-800 hover:text-green-900">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-800 hover:text-red-900">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Buscar bien -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <h3 class="font-semibold flex items-center gap-2 mb-4 text-gray-800">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Buscar Bien
        </h3>

        <div>
            <label class="text-sm font-medium text-gray-700">Número de Inventario</label>
            <input type="text" 
                wire:model.defer="busqueda" 
                placeholder="Ej: 400-001"
                class="w-full mt-1 px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <button wire:click="buscar" 
            class="w-full mt-4 bg-indigo-900 text-white rounded-lg py-2 hover:bg-indigo-800 transition-colors font-medium">
            Buscar
        </button>
    </div>

    <!-- Pendientes -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Pendientes de Documentación
        </h3>
        <p class="text-sm text-gray-500 mb-4">Bienes que requieren completar documentación</p>

        <div class="space-y-2 max-h-96 overflow-y-auto">
            @forelse ($this->pendientes as $b)
                <div wire:click="seleccionarBien({{ $b->id }})"
                    class="p-3 border rounded-lg hover:bg-gray-50 cursor-pointer transition-colors @if($bienSeleccionado == $b->id) bg-indigo-50 border-indigo-300 @endif">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $b->numero_inventario }}</p>
                            <p class="text-sm text-gray-600">{{ $b->descripcion }}</p>
                            <div class="flex gap-2 mt-1">
                                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                    {{ $b->cuenta->codigo ?? 'N/A' }}
                                </span>
                                @if($b->remito)
                                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                    Remito: {{ $b->remito->numero_remito }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p>No hay bienes pendientes de documentación.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Documentación asociada -->
    @if ($bienSeleccionado)
        @php $bien = \App\Models\Bien::with('remito')->find($bienSeleccionado); @endphp
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Documentación Asociada
            </h3>

            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-4 rounded-lg mb-6 border border-indigo-100">
                <p class="font-semibold text-gray-900 text-lg">{{ $bien->numero_inventario }}</p>
                <p class="text-sm text-gray-700 mt-1">{{ $bien->descripcion }}</p>
                <div class="flex gap-3 mt-2 text-xs text-gray-600">
                    <span>📋 Remito: {{ $bien->remito->numero_remito ?? 'N/A' }}</span>
                    <span>📁 Expediente: {{ $bien->remito->numero_expediente ?? 'N/A' }}</span>
                </div>
            </div>

            <form wire:submit.prevent="guardarDocumentacion">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Número de Acta -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Número de Acta
                        </label>
                        <input type="text" 
                            wire:model="numero_acta" 
                            placeholder="ACTA-2024-0000"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Fecha de Acta -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Acta
                        </label>
                        <input type="date" 
                            wire:model="fecha_acta"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Número de Resolución -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Número de Resolución
                        </label>
                        <input type="text" 
                            wire:model="numero_resolucion" 
                            placeholder="RES-2024-0000"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Número de Factura -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Número de Factura
                        </label>
                        <input type="text" 
                            wire:model="numero_factura" 
                            placeholder="FAC-A-0000000"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Fecha de Factura -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Factura
                        </label>
                        <input type="date" 
                            wire:model="fecha_factura"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Monto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Monto
                        </label>
                        <input type="number" 
                            wire:model="monto" 
                            step="0.01"
                            placeholder="0.00"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Proveedor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Proveedor
                        </label>
                        <select wire:model="proveedor_id"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Seleccione proveedor</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}">{{ $proveedor->razon_social }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Partida Presupuestaria -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Partida Presupuestaria
                        </label>
                        <input type="text" 
                            wire:model="partida_presupuestaria" 
                            placeholder="Ej: 2.9.3"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Orden de Pago -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Orden de Pago
                        </label>
                        <input type="text" 
                            wire:model="orden_pago" 
                            placeholder="PO-2024-0000"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Ejercicio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ejercicio
                        </label>
                        <input type="text" 
                            wire:model="ejercicio" 
                            placeholder="2025"
                            maxlength="4"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Orden de Provisión -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Orden de Provisión
                        </label>
                        <select wire:model="orden_provision_id"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Seleccione orden</option>
                            @foreach($ordenesProvision as $orden)
                                <option value="{{ $orden->id }}">{{ $orden->numero }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Estado
                        </label>
                        <select wire:model="estado"
                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="pendiente">Pendiente</option>
                            <option value="completo">Completo</option>
                            <option value="revisado">Revisado</option>
                        </select>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Observaciones
                    </label>
                    <textarea 
                        wire:model="observaciones"
                        rows="3"
                        placeholder="Observaciones adicionales..."
                        class="w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div class="flex justify-end mt-6 gap-2">
                    <button type="button" wire:click="$set('bienSeleccionado', null)" 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-900 text-white rounded-lg hover:bg-indigo-800 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Guardar Documentación
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Modal Bienes Sin Asignar -->
    @if($modalSinAsignar)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" wire:click="cerrarModal('sin-asignar')">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 p-6 max-h-[80vh] overflow-y-auto" wire:click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Bienes Sin Asignar</h3>
                    <button wire:click="cerrarModal('sin-asignar')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-2">
                    @forelse($this->bienesSinAsignar as $bien)
                        <div class="p-3 border rounded-lg hover:bg-gray-50">
                            <p class="font-semibold">{{ $bien->numero_inventario }}</p>
                            <p class="text-sm text-gray-600">{{ $bien->descripcion }}</p>
                            <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Sin Asignar</span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">No hay bienes sin asignar</p>
                    @endforelse
                </div>

                <div class="mt-4 flex justify-end">
                    <button wire:click="cerrarModal('sin-asignar')" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Exportar -->
    @if($modalExportar)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" wire:click="cerrarModal('exportar')">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6" wire:click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Exportar a Excel</h3>
                    <button wire:click="cerrarModal('exportar')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <p class="text-gray-600 mb-6">¿Desea exportar todos los bienes con su documentación a un archivo Excel?</p>

                <div class="flex justify-end gap-2">
                    <button wire:click="cerrarModal('exportar')" 
                        class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button wire:click="exportarExcel" 
                        class="px-4 py-2 bg-indigo-900 text-white rounded-md hover:bg-indigo-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Exportar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>