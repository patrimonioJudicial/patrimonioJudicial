<div class="space-y-8">

    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Panel de Cargador</h1>
            <p class="text-gray-600">Completar documentación de bienes</p>
        </div>

        <div class="flex gap-2">
            <button wire:click="mostrarModal('sin-asignar')" class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                <x-heroicon-o-cube class="w-5 h-5" /> Bienes Sin Asignar
            </button>

            <button wire:click="mostrarModal('exportar')" class="flex items-center gap-2 px-4 py-2 bg-indigo-900 text-white rounded-lg hover:bg-indigo-800">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5" /> Exportar a Excel
            </button>
        </div>
    </div>

    <!-- Buscar bien -->
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h3 class="font-semibold flex items-center gap-2 mb-4 text-gray-800">
            <x-heroicon-o-magnifying-glass class="w-5 h-5" /> Buscar Bien
        </h3>

        <label class="text-sm font-medium text-gray-700">Número de Inventario</label>
        <input type="text" wire:model.defer="busqueda" placeholder="Ej: 400-001"
            class="w-full mt-1 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">

        <button wire:click="buscar" class="w-full mt-4 bg-indigo-900 text-white rounded-lg py-2 hover:bg-indigo-800">Buscar</button>

        @if (session()->has('message'))
            <div class="mt-3 text-sm text-green-700 bg-green-50 border border-green-200 p-2 rounded-lg">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <!-- Pendientes -->
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h3 class="font-semibold text-gray-800 mb-2">Pendientes de Documentación</h3>
        <p class="text-sm text-gray-500 mb-4">Bienes que requieren completar documentación</p>

        <div class="space-y-2">
            @forelse ($pendientes as $b)
                <div wire:click="$set('bienSeleccionado', {{ $b->id }})"
                    class="p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <p class="font-semibold">{{ $b->numero_inventario }}</p>
                    <p class="text-sm text-gray-600">{{ $b->descripcion }}</p>
                </div>
            @empty
                <p class="text-gray-500">No hay bienes pendientes.</p>
            @endforelse
        </div>
    </div>

    <!-- Documentación asociada -->
    @if ($bienSeleccionado)
        @php $bien = \App\Models\Bien::find($bienSeleccionado); @endphp
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2 mb-4">
                <x-heroicon-o-clipboard-document class="w-5 h-5" /> Documentación Asociada
            </h3>

            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <p class="font-semibold">{{ $bien->numero_inventario }}</p>
                <p class="text-sm text-gray-600">{{ $bien->descripcion }}</p>
                <p class="text-sm text-gray-500 mt-1">Remito: {{ $bien->remito ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">Expediente: {{ $bien->expediente ?? 'N/A' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <x-input label="Número de Acta" model="numero_acta" placeholder="ACTA-2024-0000" />
                <x-input label="Número de Resolución" model="numero_resolucion" placeholder="RES-2024-0000" />
                <x-input label="Número de Factura" model="numero_factura" placeholder="FAC-A-0000000" />
                <x-input label="Monto" model="monto" type="number" step="0.01" />
                <x-input label="Partida Presupuestaria" model="partida_presupuestaria" placeholder="Ej: 2.9.3" />
                <x-input label="Orden de Pago" model="orden_pago" placeholder="PO-2024-0000" />
            </div>

            <div class="flex justify-end mt-6 gap-2">
                <button wire:click="$set('bienSeleccionado', null)" class="px-4 py-2 border rounded-lg">Cancelar</button>
                <button wire:click="guardarDocumentacion" class="px-4 py-2 bg-indigo-900 text-white rounded-lg hover:bg-indigo-800">
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5 inline-block" /> Guardar Documentación
                </button>
            </div>
        </div>
    @endif

    <!-- Modales -->
    @include('livewire.partials.modal-sin-asignar')
    @include('livewire.partials.modal-exportar')
</div>
