@extends('layouts.app')

@section('title', 'Configurador de Lote')

@section('content')

<style>
    .cls-1 {
        fill: transparent !important;
        stroke: #00aeef;
        stroke-miterlimit: 10;
        cursor: pointer;
        transition: fill 0.3s ease;
    }

    .cls-1:hover {
        fill: rgba(0, 200, 0, 0.6) !important;
    }
</style>


<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title">Configurador: {{ $lot->name }}</h3>
        <div class="card-toolbar">
            <a href="{{ route('lots.form') }}" class="btn btn-secondary">Regresar</a>
        </div>
    </div>
    <div class="card-body text-center">
        <div style="position: relative; display: inline-block;">
            @if ($lot->png_image)
            <img src="{{ asset('/' . $lot->png_image) }}" alt="PNG" style="width:900px; height:auto;">
            @endif

            @if ($lot->svg_image)
            <div style="position: absolute; top:0; left:0; width:100%;">
                {!! file_get_contents(public_path($lot->svg_image)) !!}
            </div>
            @endif
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="polygonModal" tabindex="-1" aria-labelledby="polygonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="polygonModalLabel">Elemento seleccionado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>Elemento seleccionado: <strong id="selectedElementId"></strong></p>
                <form id="polygonForm">
                    @csrf
                    <input type="hidden" id="polygonId" name="polygonId">

                    {{-- Formulario dinámico --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Proyecto</label>
                            <select name="project_id" id="modal_project_id" class="form-select form-select-solid" required>
                                <option value="">Seleccione un proyecto...</option>
                                @foreach($projects as $project)
                                <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Fase</label>
                            <select name="phase_id" id="modal_phase_id" class="form-select form-select-solid" required>
                                <option value="">Seleccione una fase...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="stage_id" class="form-label">Etapa (Stage)</label>
                            <select id="modal_stage_id" name="stage_id" class="form-select" required>
                                <option value="">Seleccione una etapa...</option>
                            </select>
                        </div>
                    </div>

                    {{-- Información adicional del polígono --}}
                    <div class="mb-3">
                        <label for="info" class="form-label">Información adicional:</label>
                        <input type="text" class="form-control" id="info" name="info">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const projectSelect = document.getElementById("modal_project_id");
        const phaseSelect = document.getElementById("modal_phase_id");
        const stageSelect = document.getElementById("modal_stage_id");

        // 1️⃣ Detectar click sobre polygons/path con clase .cls-1
        const svgElements = document.querySelectorAll('svg .cls-1');
        svgElements.forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                const elementId = this.id;
                document.getElementById('selectedElementId').innerText = elementId;
                document.getElementById('polygonId').value = elementId;

                // Abrir modal
                const modal = new bootstrap.Modal(document.getElementById('polygonModal'));
                modal.show();
            });
        });

        // 2️⃣ Cargar fases según proyecto
        projectSelect.addEventListener("change", function() {
            const projectId = this.value;
            phaseSelect.innerHTML = `<option value="">Cargando fases...</option>`;
            stageSelect.innerHTML = `<option value="">Seleccione una fase primero</option>`;
            if (!projectId) return;

            fetch(`/api/projects/${projectId}/phases`)
                .then(res => res.json())
                .then(data => {
                    phaseSelect.innerHTML = `<option value="">Seleccione una fase...</option>`;
                    data.forEach(phase => {
                        const opt = document.createElement("option");
                        opt.value = phase.id;
                        opt.textContent = phase.name;
                        phaseSelect.appendChild(opt);
                    });
                });
        });

        // 3️⃣ Cargar stages según fase
        phaseSelect.addEventListener("change", function() {
            const projectId = projectSelect.value;
            const phaseId = this.value;
            stageSelect.innerHTML = `<option value="">Cargando etapas...</option>`;
            if (!projectId || !phaseId) return;

            fetch(`/api/projects/${projectId}/phases/${phaseId}/stages`)
                .then(res => res.json())
                .then(data => {
                    stageSelect.innerHTML = `<option value="">Seleccione una etapa...</option>`;
                    data.forEach(stage => {
                        const opt = document.createElement("option");
                        opt.value = stage.id;
                        opt.textContent = stage.name;
                        stageSelect.appendChild(opt);
                    });
                });
        });
    });
</script>
@endpush