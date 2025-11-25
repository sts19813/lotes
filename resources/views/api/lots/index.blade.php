@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-bold text-gray-800">Lotes</h1>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLot">
                <i class="ki-duotone ki-plus fs-2"></i> Nuevo Lote
            </button>

            <button id="btnDownloadTemplate" class="btn btn-success">
                <i class="ki-duotone ki-download fs-2"></i> Descargar Plantilla
            </button>

            <input type="file" id="inputImport" accept=".xlsx" hidden>
            <button id="btnImport" class="btn btn-info">
                <i class="ki-duotone ki-upload fs-2"></i> Importar Lotes
            </button>
        </div>
    </div>
    <!--  Filtros arriba -->
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
                        @php
                            $statusMap = [
                                'for_sale' => 'Disponible',
                                'sold' => 'Vendido',
                                'reserved' => 'Apartado',
                                'locked_sale' => 'Bloqueado',
                            ];
                        @endphp
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Selecciona un status</option>

                                @foreach($statusMap as $value => $label)
                                    <option value="{{ $value }}" @if(isset($financiamiento) && $financiamiento->status === $value)
                                    selected @endif>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function () {

            /**
             * ===============================================================
             *  DataTable: Listado de Lotes
             * ===============================================================
             * Inicializa la tabla con AJAX, mapeo de columnas, renderizado
             * personalizado del estatus, fechas y relaciones anidadas.
             */
            const table = $('#lotsTable').DataTable({
                ajax: {
                    url: '/api/lots', // Endpoint que devuelve todos los lotes
                    dataSrc: ''       // Respuesta es un array directo
                },
                columns: [
                    { data: 'id' },
                    { data: 'name', defaultContent: '-' },

                    // Proyecto del lote (relación anidada)
                    { data: 'stage.phase.project.name', defaultContent: 'Sin proyecto' },

                    // Fase del lote
                    { data: 'stage.phase.name', defaultContent: 'Sin fase' },

                    // Etapa del lote
                    { data: 'stage.name', defaultContent: 'Sin etapa' },

                    { data: 'depth', defaultContent: '-' },
                    { data: 'front', defaultContent: '-' },
                    { data: 'area', defaultContent: '-' },
                    { data: 'price_square_meter', defaultContent: '-' },
                    { data: 'total_price', defaultContent: '-' },

                    /**
                     * Select dinámico del estatus
                     * Se muestran las opciones en español, pero se guardan en inglés.
                     */
                    {
                        data: 'status',
                        render: function (status, type, row) {
                            const statusMap = {
                                for_sale: "Disponible",
                                sold: "Vendido",
                                reserved: "Apartado",
                                locked_sale: "Bloqueado"
                            };

                            let options = "";
                            for (const key in statusMap) {
                                options += `<option value="${key}" ${key === status ? "selected" : ""}>${statusMap[key]}</option>`;
                            }

                            return `
                                                <select class="form-select form-select-sm lot-status" data-id="${row.id}">
                                                    ${options}
                                                </select>
                                            `;
                        }
                    },
                    {
                        data: 'chepina',
                        render: function (data, type, row) {
                            if (data) {
                                // Si tiene imagen
                                return `
                        <img src="/chepinas/${data}" 
                             alt="Imagen" 
                             class="img-thumbnail chepina-img" 
                             style="width:60px; height:60px; object-fit:cover; cursor:pointer;">
                        <input type="file" class="d-none chepina-input" data-id="${row.id}">
                    `;
                            } else {
                                // Si NO tiene imagen → icono upload
                                return `
                        <button class="btn btn-sm btn-outline-primary upload-chepina" data-id="${row.id}">
                            <i class="fas fa-upload"></i> Subir
                        </button>
                        <input type="file" class="d-none chepina-input" data-id="${row.id}">
                    `;
                            }
                        },
                        defaultContent: '-'
                    },

                    // Fecha formateada al estilo local del navegador
                    { data: 'created_at', render: d => new Date(d).toLocaleDateString() }
                ],
                autoWidth: false,
                language: { url: '//cdn.datatables.net/plug-ins/2.3.2/i18n/es-MX.json' },
                responsive: true,
                dom:
                    "<'row mb-3'<'col-12 d-flex justify-content-end'f>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'p>>"
            });

            // ========================================================================
            //  FILTROS DEPENDIENTES SUPERIORES
            // ========================================================================

            /**
             * Filtra por Proyecto 
             * Actualiza fases y recarga la tabla según el proyecto seleccionado.
             */
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

                // Obtener fases del proyecto
                $.get(`/api/phases?project_id=${projectId}`, function (phases) {
                    phaseSelect.prop('disabled', false).html('<option value="">Seleccionar...</option>');
                    phases.forEach(p => phaseSelect.append(`<option value="${p.id}">${p.name}</option>`));
                });

                table.ajax.url(`/api/lots?project_id=${projectId}`).load();
            });

            /**
             * Filtra por Fase
             * Actualiza etapas y recarga la tabla según la fase seleccionada.
             */
            $('#filterPhase').on('change', function () {
                const phaseId = $(this).val();
                const stageSelect = $('#filterStage');

                stageSelect.prop('disabled', true).html('<option>Selecciona una fase...</option>');

                if (!phaseId) {
                    table.ajax.url('/api/lots').load();
                    return;
                }

                // Cargar etapas de la fase
                $.get(`/api/stages?phase_id=${phaseId}`, function (stages) {
                    stageSelect.prop('disabled', false).html('<option value="">Seleccionar...</option>');
                    stages.forEach(s => stageSelect.append(`<option value="${s.id}">${s.name}</option>`));
                });

                table.ajax.url(`/api/lots?phase_id=${phaseId}`).load();
            });

            /**
             * Filtra por Etapa
             * Refresca la tabla según Proyecto + Fase + Etapa seleccionados.
             */
            $('#filterStage').on('change', function () {
                const stageId = $(this).val();

                const params = {
                    project_id: $('#filterProject').val(),
                    phase_id: $('#filterPhase').val(),
                    stage_id: stageId
                };

                const query = new URLSearchParams(params).toString();
                table.ajax.url(`/api/lots?${query}`).load();
            });

            // ========================================================================
            //  FORMULARIO MODAL (crear nuevo lote)
            // ========================================================================

            /**
             * Cuando se selecciona un proyecto en el formulario modal,
             * se cargan las fases correspondientes.
             */
            $('#lotProject').on('change', function () {
                const projectId = $(this).val();
                const phaseSelect = $('#lotPhase');
                const stageSelect = $('#lotStage');

                stageSelect.prop('disabled', true).html('<option>Selecciona una fase...</option>');

                if (!projectId) {
                    phaseSelect.prop('disabled', true).html('<option>Selecciona un proyecto...</option>');
                    return;
                }

                $.get(`/api/phases?project_id=${projectId}`, function (phases) {
                    phaseSelect.prop('disabled', false).html('<option value="">Seleccionar...</option>');
                    phases.forEach(p => phaseSelect.append(`<option value="${p.id}">${p.name}</option>`));
                });
            });

            /**
             * Carga etapas cuando se selecciona una fase en el formulario modal.
             */
            $('#lotPhase').on('change', function () {
                const phaseId = $(this).val();
                const stageSelect = $('#lotStage');

                stageSelect.prop('disabled', true).html('<option>Selecciona una fase...</option>');

                if (!phaseId) return;

                $.get(`/api/stages?phase_id=${phaseId}`, function (stages) {
                    stageSelect.prop('disabled', false).html('<option value="">Seleccionar...</option>');
                    stages.forEach(s => stageSelect.append(`<option value="${s.id}">${s.name}</option>`));
                });
            });

            /**
             * Envía el formulario del modal para crear un nuevo lote.
             * Al guardar, recarga la tabla y resetea el formulario.
             */
            $('#formLot').on('submit', function (e) {
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

                        this.reset();
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

            // ========================================================================
            //  EXPORTAR PLANTILLA DE EXCEL
            // ========================================================================

            /**
             * Genera y descarga una plantilla Excel para importar lotes.
             * Los IDs se rellenan automáticamente para evitar errores.
             */
            $('#btnDownloadTemplate').on('click', function () {
                const projectId = $('#filterProject').val();
                const phaseId = $('#filterPhase').val();
                const stageId = $('#filterStage').val();

                if (!projectId || !phaseId || !stageId) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Selecciona Proyecto, Fase y Etapa',
                        text: 'La plantilla necesita IDs correctos.'
                    });
                }

                const instructions = [
                    "⚠️ IMPORTANTE: No modificar los IDs de Proyecto, Fase y Etapa."
                ];

                const header = [
                    "project_id", "phase_id", "stage_id", "name",
                    "depth", "front", "area", "price_square_meter",
                    "total_price", "status", "chepina",

                    // NUEVOS CAMPOS
                    "area_new",
                    "front_new",
                    "fondo",
                    "altura",
                    "resistencia_piso",
                    "punto_colgado",
                    "auditorio",
                    "escuela",
                    "herradura",
                    "mesa_rusa",
                    "banquete",
                    "coctel",
                    "link_recorrido"
                ];


                const dataRows = Array.from({ length: 10 }, () => [
                    projectId, phaseId, stageId, "", "", "", "", "", "",
                    "Disponible", "",

                    // Nuevos campos vacíos
                    "", "", "", "", "", "",
                    "", "", "", "", "", "",
                    ""
                ]);


                const worksheet = XLSX.utils.aoa_to_sheet([
                    instructions,
                    header,
                    ...dataRows
                ]);

                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Lotes");

                XLSX.writeFile(workbook, "plantilla_lotes.xlsx");
            });

            // ========================================================================
            //  IMPORTAR LOTES DESDE EXCEL
            // ========================================================================

            /** Abre el input file */
            $('#btnImport').on('click', () => $('#inputImport').click());

            /**
             * Procesa el archivo Excel seleccionado.
             * Envía el archivo por AJAX y muestra un informe de resultados.
             */
            $('#inputImport').on('change', function () {
                let file = this.files[0];
                if (!file) return;

                let formData = new FormData();
                formData.append('file', file);

                $.ajax({
                    url: '/api/lots/import',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {

                        let message = `✅ Lotes importados: ${response.success}\n\n`;

                        if (response.errors.length > 0) {
                            message += `⚠️ Errores encontrados:\n\n`;
                            response.errors.forEach(err => message += `• ${err}\n`);
                        }

                        Swal.fire({
                            icon: response.errors.length > 0 ? 'warning' : 'success',
                            title: 'Resultado de importación',
                            html: `<pre style="white-space:pre-wrap;text-align:left;">${message}</pre>`,
                            width: '800px'
                        });

                        $('#modalImport').modal('hide');
                        table.ajax.reload();
                    },
                    error: function () {
                        Swal.fire('Error', 'No se pudo procesar el archivo', 'error');
                    }
                });
            });

            // ========================================================================
            //  ACTUALIZAR STATUS EN TIEMPO REAL DESDE EL DATATABLE
            // ========================================================================

            /**
             * Actualiza el estatus del lote cuando el usuario cambia el select.
             * Guarda en base sin recargar la página.
             */
            $('#lotsTable').on('change', '.lot-status', function () {
                const lotId = $(this).data('id');
                const newStatus = $(this).val();

                $.ajax({
                    url: `/api/lots/${lotId}/status`,
                    type: 'PUT',
                    data: {
                        status: newStatus,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function () {
                        Swal.fire({
                            icon: "success",
                            title: "Estatus actualizado",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "No se pudo cambiar el estatus"
                        });
                    }
                });
            });

            // ========================================================================
            //  SUBIR IMAGEN "CHEPINA" DESDE EL DATATABLE
            // ========================================================================

            // Abrir input file cuando se da clic en el botón "Subir"
            // Listener para abrir input file al hacer clic en botón o imagen
            $('#lotsTable').on('click', '.upload-chepina, .chepina-img', function () {
                $(this).closest('td').find('.chepina-input').click();
            });


            // Cuando el usuario selecciona un archivo
            $('#lotsTable').on('change', '.chepina-input', function () {
                const lotId = $(this).data('id');
                const file = this.files[0];

                if (!file) return;

                let formData = new FormData();
                formData.append('chepina', file);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: `/api/lots/${lotId}/chepina`,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Imagen subida',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Recargar SOLO la fila actual
                        $('#lotsTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo subir la imagen.'
                        });
                    }
                });
            });

        });

    </script>
@endpush