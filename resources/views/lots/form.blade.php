@extends('layouts.app')

@section('title', 'Consulta de Lotes')

@section('content')
<div class="app-container container-xxl">
    <div class="card mb-5">
        <div class="card-header">
            <h3 class="card-title">Consultar Lotes</h3>
        </div>
        <div class="card-body">
            {{-- Mensajes --}}
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Formulario --}}
            <form action="{{ route('lots.fetch') }}" method="POST" class="row g-3 mb-4">
                @csrf
                <div class="col-md-4">
                    <label class="form-label fw-bold">Project ID</label>
                    <input type="number" name="project_id" class="form-control form-control-solid"
                           value="{{ old('project_id', $project_id ?? '') }}" required />
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Phase ID</label>
                    <input type="number" name="phase_id" class="form-control form-control-solid"
                           value="{{ old('phase_id', $phase_id ?? '') }}" required />
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Stage ID</label>
                    <input type="number" name="stage_id" class="form-control form-control-solid"
                           value="{{ old('stage_id', $stage_id ?? '') }}" required />
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="ki-duotone ki-magnifier fs-2"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de resultados --}}
    @isset($lots)
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold">Resultados de Lotes</h3>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="lots_table" class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                    <thead class="fs-7 text-gray-400 text-uppercase">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Área</th>
                            <th>Precio m²</th>
                            <th>Total</th>
                            <th>Estatus</th>
                            <th>Imagen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lots as $lot)
                            <tr>
                                <td>{{ $lot['id'] }}</td>
                                <td>{{ $lot['name'] }}</td>
                                <td>{{ $lot['area'] }}</td>
                                <td>${{ number_format($lot['price_square_meter'], 2) }}</td>
                                <td>${{ number_format($lot['total_price'], 2) }}</td>
                                <td>
                                    <span class="badge 
                                        @if($lot['status'] === 'available') badge-light-success 
                                        @elseif($lot['status'] === 'sold') badge-light-danger 
                                        @else badge-light-warning @endif">
                                        {{ ucfirst($lot['status']) }}
                                    </span>
                                </td>
                                <td>
                                    @if(isset($lot['chepina']))
                                        <img src="{{ $lot['chepina'] }}" alt="Imagen Lote {{ $lot['name'] }}" class="img-thumbnail" style="width: 80px;">
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-gray-500">No se encontraron lotes</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endisset
</div>
@endsection

@section('scripts')
    {{-- Metronic Datatables (DataTables o KTDatatable según tu versión) --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            $("#lots_table").DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es_es.json"
                }
            });
        });
    </script>
@endsection
