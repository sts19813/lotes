@extends('layouts.app')

@section('title', 'Consulta de Lotes')

@section('content')
    <div class="card mb-5">
        <div class="card-header">
            <h3 class="card-title">Consultar Lotes</h3>
        </div>
        <div class="card-body">
            {{-- Mensajes --}}
            <div id="alert-container"></div>

            {{-- Formulario --}}
            <form id="filterForm" class="row g-3 mb-4">
                @csrf
                <div class="col-md-4">
                    <label class="form-label fw-bold">Proyecto</label>
                    <select name="project_id" class="form-select form-select-solid" required>
                        <option value="">Seleccione un proyecto...</option>
                        @foreach($projects as $project)
                            <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Fase</label>
                    <select name="phase_id" id="phase_id" class="form-select form-select-solid" required>
                        <option value="">Seleccione una fase...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="stage_id" class="form-label">Etapa (Stage)</label>
                    <select id="stage_id" name="stage_id" class="form-select" required>
                        <option value="">Seleccione una etapa...</option>
                    </select>
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
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.Laravel = {
            csrfToken: "{{ csrf_token() }}",
            routes: {
                lotsFetch: "{{ route('lots.fetch') }}"
            }
        };
    </script>

    <script src="{{ asset('assets/js/ApiConsulta.js') }}"></script>
@endpush