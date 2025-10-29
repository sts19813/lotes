@extends('layouts.app')

@section('title', 'Dashboard de Lotes')

@section('content')
<link rel="stylesheet" href="/assets/css/dashboards.css">

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
                    <select id="project_id" name="project_id" class="form-select form-select-solid">
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
            </form>
        </div>
    </div>

</div>

@include('dashboards.cards')
@include('dashboards.summary')
@include('dashboards.chart')

<!-- Loader -->
<div id="global_loader">
    <div class="spinner"></div>
    <span>Cargando información...</span>
</div>

@endsection

@push('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush
