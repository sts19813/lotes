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
        columnDefs: [
            {
                targets: 0,          // Columna ID
                visible: false,      // Ocultar
                searchable: false    // No buscar por ID
            }
        ],
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
                        return `
                            <img 
                                data-src="/chepinas/${data}"
                                src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" 
                                alt="Imagen" 
                                class="img-thumbnail chepina-img lazy-chepina" 
                                style="width:60px; height:60px; object-fit:cover; cursor:pointer;">
                            <input type="file" class="d-none chepina-input" data-id="${row.id}">
                        `;
                    } else {
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
            { data: 'created_at', render: d => new Date(d).toLocaleDateString() },
            {
                data: null,
                orderable: false,
                render: function (data) {
                    return `
                        <button class="btn btn-sm btn-outline-primary btn-edit-lot" data-id="${data.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                    `;
                }
            },           
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


    // ============================================================
    // LAZY LOAD de imágenes de Chepina visibles
    // ============================================================

    function lazyLoadChepinas() {

        const images = document.querySelectorAll('img.lazy-chepina');

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const realSrc = img.getAttribute('data-src');

                    if (realSrc) {
                        img.src = realSrc;
                        img.removeAttribute('data-src');
                    }

                    obs.unobserve(img);
                }
            });
        }, { rootMargin: "100px" }); // carga anticipada suave

        images.forEach(img => observer.observe(img));
}

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

        $.get(`/api/lots?project_id=${projectId}&phase_id=${phaseId}&stage_id=${stageId}`, function (lots) {

 

            // Mapeo estatus EN ESPAÑOL
            const statusMap = {
                for_sale: "Disponible",
                sold: "Vendido",
                reserved: "Apartado",
                locked_sale: "Bloqueado"
            };

            // 🟢 INSTRUCCIONES COMPLETAS EN UNA SOLA CELDA
            const instrucciones = [
                "⚠️ INSTRUCCIONES IMPORTANTES:\n" +
                "- No modifiques los IDs de Proyecto, Fase y Etapa para registros existentes.\n" +
                "- Para NUEVOS registros: NO incluyas los campos de ID (déjalos vacíos o elimina la fila ejemplo).\n" +
                "- Solo edita columnas como: Nombre, Área, Precios, Estatus y Chepina.\n" +
                "- Estatus permitidos: Disponible, Vendido, Apartado, Bloqueado.\n"
            ];

            // 🟢 ENCABEZADOS EN ESPAÑOL
            const header = [
                "ID",
                "ID Proyecto",
                "ID Fase",
                "ID Etapa",
                "Nombre",
                "Profundidad",
                "Frente",
                "Área",
                "Precio m²",
                "Precio Total",
                "Estatus",
                "Chepina"
            ];

            // 🟢 Filas
            const dataRows = lots.map(l => [
                l.id,
                l.stage.phase.project_id,
                l.stage.phase_id,
                l.stage_id,
                l.name ?? "",
                l.depth ?? "",
                l.front ?? "",
                l.area ?? "",
                l.price_square_meter ?? "",
                l.total_price ?? "",
                statusMap[l.status] ?? "Disponible",
                l.chepina ?? ""
            ]);

            const aoa = [
                instrucciones,   // Fila 1 con instrucciones
                [],              // Fila 2 vacía
                header,          // Fila 3 encabezados
                ...dataRows      // Fila 4+ lotes
            ];

            const worksheet = XLSX.utils.aoa_to_sheet(aoa);

            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Lotes");

            XLSX.writeFile(workbook, "plantilla_lotes.xlsx");
        });
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
            error: function (error) {
                debugger
                Swal.fire('Error', 'No se pudo procesar el archivo', 'error');
            }
        });
    });

    $('#lotsTable').on('draw.dt', function () {
        lazyLoadChepinas();
    });

    //listener para la edicion de lote individual
    $('#lotsTable').on('click', '.btn-edit-lot', function () {
        let id = $(this).data('id');

        $.get(`/api/lots/${id}`, function (lot) {

            // Llenamos el modal de edición
            $('#editLotId').val(lot.id);
            $('#editLotName').val(lot.name);
            $('#editLotDepth').val(lot.depth);
            $('#editLotFront').val(lot.front);
            $('#editLotArea').val(lot.area);
            $('#editLotPriceM2').val(lot.price_square_meter);
            $('#editLotTotal').val(lot.total_price);
            $('#modalEditLot').modal('show');
        });
    });

    //guarddo de cambio   en la edicion de lote individual
    $('#formEditLot').on('submit', function (e) {
        e.preventDefault();

        const id = $('#editLotId').val();
        const data = $(this).serialize();

        $.ajax({
            url: `/api/lots/${id}`,
            type: 'PUT',
            data: data,
            success: () => {
                $('#modalEditLot').modal('hide');
                table.ajax.reload(null, false);

                Swal.fire({
                    icon: 'success',
                    title: 'Lote actualizado',
                    timer: 1500,
                    showConfirmButton: false
                });
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
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


        $.ajax({
            url: `/api/lots/${lotId}/status`,
            type: 'PUT',
            data: {
                status: newStatus,
                _token: csrf
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
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


        if (!file) return;

        let formData = new FormData();
        formData.append('chepina', file);
        formData.append('_token', csrf);

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
