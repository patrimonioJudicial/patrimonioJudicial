@extends('layouts.app')

@section('title', 'Gestión de Dependencias')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4 class="m-0"><i class="bi bi-building-gear"></i> Gestión de Dependencias</h4>
            <a href="{{ route('dependencias.importar.form') }}" class="btn btn-success fw-semibold">
                <i class="bi bi-file-earmark-excel"></i> Importar Excel
            </a>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($dependencias->count())
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                                <th>Responsable</th>
                                <th>Teléfono</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dependencias as $dep)
                                <tr>
                                    <td>{{ $dep->codigo }}</td>
                                    <td>{{ $dep->nombre }}</td>
                                    <td>{{ $dep->ubicacion ?? '-' }}</td>
                                    <td>{{ $dep->responsable ?? '-' }}</td>
                                    <td>{{ $dep->telefono ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $dependencias->links() }}
                </div>
            @else
                <p class="text-center text-muted">No hay dependencias registradas.</p>
            @endif
        </div>
    </div>
</div>
@endsection
