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
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inicializar DataTable
        const table = $("#lots_table").DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es_es.json"
            },
            columns: [{}, {}, {}, {}, {}, {}, {}]
        });

        const projectSelect = document.querySelector("select[name='project_id']");
        const phaseSelect = document.getElementById("phase_id");
        const stageSelect = document.getElementById("stage_id");

        // Cargar fases según proyecto
        projectSelect.addEventListener("change", function() {
            const projectId = this.value;
            phaseSelect.innerHTML = `<option value="">Cargando fases...</option>`;
            stageSelect.innerHTML = `<option value="">Seleccione una fase primero</option>`;

            if (!projectId) {
                phaseSelect.innerHTML = `<option value="">Seleccione un proyecto primero</option>`;
                return;
            }

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

        // Cargar etapas según fase
        phaseSelect.addEventListener("change", function() {
            const projectId = projectSelect.value;
            const phaseId = this.value;
            stageSelect.innerHTML = `<option value="">Cargando etapas...</option>`;

            if (!projectId || !phaseId) {
                stageSelect.innerHTML = `<option value="">Seleccione una fase primero</option>`;
                return;
            }

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

        // AJAX submit del formulario
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch("{{ route('lots.fetch') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    table.clear();

                    data.forEach(lot => {
                        table.row.add([
                            lot.id,
                            lot.name,
                            lot.area,
                            `$${Number(lot.price_square_meter).toFixed(2)}`,
                            `$${Number(lot.total_price).toFixed(2)}`,
                            `<span class="badge ${lot.status==='available'?'badge-light-success':lot.status==='sold'?'badge-light-danger':'badge-light-warning'}">${lot.status.charAt(0).toUpperCase()+lot.status.slice(1)}</span>`,
                            lot.chepina ? `<img src="${lot.chepina}" style="width:80px;" class="img-thumbnail">` : ''
                        ]);
                    });

                    table.draw();
                })
                .catch(err => {
                    console.error(err);
                    alert("Error al cargar los lotes.");
                });
        });
    });
</script>
@endpush