@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fw-bold text-gray-800">Lotes</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLot">
        <i class="ki-duotone ki-plus fs-2"></i> Nuevo Lote
    </button>
</div>

<!-- 🔹 Filtros arriba -->
<div class="row mb-5">
    <div class="col-md-4">
        <select id="filterProject" class="form-select">
            <option value="">Todos los proyectos</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <select id="filterPhase" class="form-select" disabled>
            <option value="">Selecciona un proyecto...</option>
        </select>
    </div>
    <div class="col-md-4">
        <select id="filterStage" class="form-select" disabled>
            <option value="">Selecciona una fase...</option>
        </select>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table id="lotsTable" class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Proyecto</th>
                    <th>Fase</th>
                    <th>Etapa</th> 
                    <th>Depth</th>
                    <th>Front</th>
                    <th>Area</th>
                    <th>Precio/m²</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Chepina</th>
                    <th>Creado</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>


<!-- Modal Crear Lote -->
<div class="modal fade" id="modalLot" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Lote</h5>
                <button class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="formLot">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Proyecto</label>
                        <select id="lotProject" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fase</label>
                        <select id="lotPhase" name="phase_id" class="form-select" required disabled>
                            <option value="">Selecciona primero un proyecto...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Etapa</label>
                        <select id="lotStage" name="stage_id" class="form-select" required disabled>
                            <option value="">Selecciona primero una fase...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Depth</label>
                        <input type="number" step="0.01" name="depth" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Front</label>
                        <input type="number" step="0.01" name="front" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Area</label>
                        <input type="number" step="0.01" name="area" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio / m²</label>
                        <input type="number" step="0.01" name="price_square_meter" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <input type="number" step="0.01" name="total_price" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <input type="text" name="status" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chepina</label>
                        <input type="text" name="chepina" class="form-control">
                    </div>


                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Guardar Lote</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // 🔹 Inicializar DataTable
    const table = $('#lotsTable').DataTable({
        ajax: {
            url: '/api/lots',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'name', defaultContent: '-' },
            { data: 'stage.phase.project.name', defaultContent: 'Sin proyecto' },
            { data: 'stage.phase.name', defaultContent: 'Sin fase' },
            { data: 'stage.name', defaultContent: 'Sin etapa' },
            { data: 'depth', defaultContent: '-' },
            { data: 'front', defaultContent: '-' },
            { data: 'area', defaultContent: '-' },
            { data: 'price_square_meter', defaultContent: '-' },
            { data: 'total_price', defaultContent: '-' },
            { data: 'status', defaultContent: '-' },
            { data: 'chepina', defaultContent: '-' },
            { data: 'created_at', render: d => new Date(d).toLocaleDateString() }
        ],
        autoWidth: false,
        responsive: true
    });

    // 🔹 Filtros dependientes arriba
    $('#filterProject').on('change', function () {
        const projectId = $(this).val();
        const phaseSelect = $('#filterPhase');
        const stageSelect = $('#filterStage');

        stageSelect.prop('disabled', true).html('<option>Selecciona una fase...</option>');

        if (!projectId) {
            phaseSelect.prop('disabled', true).html('<option>Selecciona un proyecto...</option>');
            table.ajax.url('/api/lots').load();
            return;
        }

        $.get(`/api/phases?project_id=${projectId}`, function(phases) {
            phaseSelect.prop('disabled', false).html('<option value="">Seleccionar...</option>');
            phases.forEach(p => phaseSelect.append(`<option value="${p.id}">${p.name}</option>`));
        });

        table.ajax.url(`/api/lots?project_id=${projectId}`).load();
    });

    $('#filterPhase').on('change', function () {
        const phaseId = $(this).val();
        const stageSelect = $('#filterStage');

        stageSelect.prop('disabled', true).html('<option>Selecciona una fase...</option>');

        if (!phaseId) {
            table.ajax.url('/api/lots').load();
            return;
        }

        // 🔹 Solo etapas de la fase seleccionada
        $.get(`/api/stages?phase_id=${phaseId}`, function(stages) {
            stageSelect.prop('disabled', false).html('<option value="">Seleccionar...</option>');
            stages.forEach(s => stageSelect.append(`<option value="${s.id}">${s.name}</option>`));
        });

        table.ajax.url(`/api/lots?phase_id=${phaseId}`).load();
    });


    $('#filterStage').on('change', function () {
        const stageId = $(this).val();
        let projectId = $('#filterProject').val();
        let phaseId = $('#filterPhase').val();

        const params = {};
        if (projectId) params.project_id = projectId;
        if (phaseId) params.phase_id = phaseId;
        if (stageId) params.stage_id = stageId;

        const query = new URLSearchParams(params).toString();
        table.ajax.url(`/api/lots?${query}`).load();
    });

    // 🔹 Formulario modal
    $('#lotProject').on('change', function () {
        const projectId = $(this).val();
        const phaseSelect = $('#lotPhase');
        const stageSelect = $('#lotStage');

        stageSelect.prop('disabled', true).html('<option>Selecciona una fase...</option>');

        if (!projectId) {
            phaseSelect.prop('disabled', true).html('<option>Selecciona un proyecto...</option>');
            return;
        }

        $.get(`/api/phases?project_id=${projectId}`, function(phases) {
            phaseSelect.prop('disabled', false).html('<option value="">Seleccionar...</option>');
            phases.forEach(p => phaseSelect.append(`<option value="${p.id}">${p.name}</option>`));
        });
    });

    $('#lotPhase').on('change', function () {
        const phaseId = $(this).val();
        const stageSelect = $('#lotStage');

        stageSelect.prop('disabled', true).html('<option>Selecciona una fase...</option>');

        if (!phaseId) return;

        $.get(`/api/stages?phase_id=${phaseId}`, function(stages) {
            stageSelect.prop('disabled', false).html('<option value="">Seleccionar...</option>');
            stages.forEach(s => stageSelect.append(`<option value="${s.id}">${s.name}</option>`));
        });
    });


    $('#formLot').on('submit', function(e) {
        e.preventDefault();
        $.post('/api/lots', $(this).serialize())
            .done(() => {
                $('#modalLot').modal('hide');
                table.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Lote creado',
                    timer: 1700,
                    showConfirmButton: false
                });
                $(this)[0].reset();
                $('#lotPhase, #lotStage').prop('disabled', true);
            })
            .fail(xhr => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON.message
                });
            });
    });
});
</script>
@endpush
