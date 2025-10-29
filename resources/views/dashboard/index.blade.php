@extends('layouts.app')

@section('title', 'Dashboard de Lotes')

@section('content')
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">

    <!-- Filtros -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title fw-bold">Filtros</h3>
        </div>
        <div class="card-body">
            <form id="dashboardFilter" class="row g-3">
                @csrf

                <div class="col-md-3">
                    <label class="form-label fw-bold">Proyecto</label>
                    <select id="project_id" name="project_id" class="form-select form-select-solid" required>
                        <option value="">Todos...</option>
                        @foreach($projects as $project)
                            <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Fase</label>
                    <select id="phase_id" name="phase_id" class="form-select form-select-solid">
                        <option value="">Todas...</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Etapa</label>
                    <select id="stage_id" name="stage_id" class="form-select form-select-solid">
                        <option value="">Todas...</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Estatus Lote</label>
                    <select id="status" name="status" class="form-select form-select-solid">
                        <option value="">Todos...</option>
                        <option value="available">Disponible</option>
                        <option value="sold">Vendido</option>
                        <option value="reserved">Apartado</option>
                        <option value="locked_sale">Bloqueado</option>
                    </select>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary mt-5">
                        <i class="ki-duotone ki-magnifier fs-2"></i> Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Cards información general -->
<div class="row g-5 mb-5">
    <div class="col-xl-3">
        <div class="card card-stats">
            <div class="card-body">
                <span class="text-gray-500 fs-7">Total Lotes</span>
                <div id="card_total" class="fs-2 fw-bold">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="card card-stats">
            <div class="card-body">
                <span class="text-gray-500 fs-7">Disponibles</span>
                <div id="card_available" class="fs-2 fw-bold text-success">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="card card-stats">
            <div class="card-body">
                <span class="text-gray-500 fs-7">Indisponibles</span>
                <div id="card_unavailable" class="fs-2 fw-bold text-danger">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="card card-stats">
            <div class="card-body">
                <span class="text-gray-500 fs-7">Bloqueados</span>
                <div id="card_blocked" class="fs-2 fw-bold text-info">0</div>
            </div>
        </div>
    </div>

</div>
<!-- Tabla resumen -->
<div class="card mt-5">
    <div class="card-header">
        <h3 class="card-title fw-bold">Resumen por Fase / Etapa</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="resume_table" class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                <thead class="text-uppercase text-gray-400 fs-7">
                    <tr>
                        <th>Proyecto</th>
                        <th>Fase</th>
                        <th>Etapa</th>
                        <th>Total</th>
                        <th>Disponibles</th>
                        <th>Vendidos</th>
                        <th>Apartados</th>
                        <th>Bloqueados</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Highchart -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title fw-bold">Distribución de Lotes por Estatus</h3>
    </div>
    <div class="card-body">
        <div id="lotsChart" style="height: 400px;"></div>
    </div>
</div>


@endsection

@push('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush
