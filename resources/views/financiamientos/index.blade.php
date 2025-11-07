@extends('layouts.app')

@section('title', 'Modelos de Financiamiento')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Modelos de Financiamiento</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFinanciamiento">
                <i class="ki-duotone ki-plus fs-2"></i> Nuevo Financiamiento
            </button>
        </div>

        <div class="card-body">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="tablaFinanciamientos">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Meses</th>
                        <th>Enganche (%)</th>
                        <th>Interés (%)</th>
                        <th>Descuento (%)</th>
                        <th>Desarrollos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold"></tbody>
            </table>
        </div>
    </div>

    <!-- Modal Crear Financiamiento -->
    <div class="modal fade" id="modalFinanciamiento" tabindex="-1" aria-labelledby="modalFinanciamientoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-3 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalFinanciamientoLabel">Nuevo Modelo de Financiamiento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <form id="formFinanciamiento">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Campos básicos -->
                            <div class="col-md-6">
                                <label class="form-label">Nombre del plan</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Descripción</label>
                                <input type="text" name="descripcion" class="form-control">
                            </div>

                            <!-- Meses, enganche, interés -->
                            <div class="col-md-4">
                                <label class="form-label">Meses</label>
                                <input type="number" name="meses" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">% Enganche</label>
                                <input type="number" name="porcentaje_enganche" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">% Interés anual</label>
                                <input type="number" name="interes_anual" class="form-control" step="0.01" required>
                            </div>

                            <!-- Descuento, montos -->
                            <div class="col-md-4">
                                <label class="form-label">% Descuento</label>
                                <input type="number" name="descuento_porcentaje" class="form-control" step="0.01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Monto mínimo</label>
                                <input type="number" name="monto_minimo" class="form-control" step="0.01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Monto máximo</label>
                                <input type="number" name="monto_maximo" class="form-control" step="0.01">
                            </div>

                            <!-- Periodicidad de pago -->
                            <div class="col-md-6">
                                <label class="form-label">Periodicidad de pago</label>
                                <select name="periodicidad_pago" class="form-select">
                                    <option value="mensual">Mensual</option>
                                    <option value="bimestral">Bimestral</option>
                                    <option value="trimestral">Trimestral</option>
                                </select>
                            </div>

                            <!-- Cargo de apertura y penalización -->
                            <div class="col-md-6">
                                <label class="form-label">Cargo de apertura</label>
                                <input type="number" name="cargo_apertura" class="form-control" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Penalización por mora (%)</label>
                                <input type="number" name="penalizacion_mora" class="form-control" step="0.01">
                            </div>

                            <!-- Plazo de gracia y activo -->
                            <div class="col-md-6">
                                <label class="form-label">Plazo de gracia (meses)</label>
                                <input type="number" name="plazo_gracia_meses" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Activo</label>
                                <select name="activo" class="form-select">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>

                            <!-- Selección de desarrollos -->
                            <div class="col-md-6">
                                <label class="form-label">Desarrollos aplicables</label>
                                <button type="button" class="btn btn-sm btn-secondary mb-2"
                                    id="selectTodosDesarrollos">Seleccionar todos</button>

                                <select name="desarrollos[]" id="selectDesarrollos" class="form-select" multiple="multiple"
                                    style="width: 100%;">
                                    @foreach($desarrollos as $desarrollo)
                                        <option value="{{ $desarrollo->id }}">{{ $desarrollo->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            // Activar select2 en desarrollos
            $('#selectDesarrollos').select2({
                placeholder: 'Selecciona desarrollos',
                allowClear: true,
                width: 'resolve'
            });

            $('#selectTodosDesarrollos').click(function () {
                $('#selectDesarrollos > option').prop("selected", true);
                $('#selectDesarrollos').trigger("change");
            });
            const tabla = $('#tablaFinanciamientos').DataTable({
                ajax: '{{ route("financiamientos.data") }}',
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'meses' },
                    { data: 'porcentaje_enganche' },
                    { data: 'interes_anual' },
                    { data: 'descuento_porcentaje' },
                    {
                        data: 'desarrollos',
                        render: data => {
                            if (!data || data.length === 0)
                                return '<span class="badge bg-secondary">Ninguno</span>';
                            return data.map(d => `<span class="badge bg-light-primary me-1">${d.name}</span>`).join('');
                        },
                        width: '300px'
                    },
                    {
                        data: null,
                        render: row => `
                                    <button class="btn btn-sm btn-light-primary me-2 btnEditar" data-id="${row.id}">Editar</button>
                                    <button class="btn btn-sm btn-light-danger btnEliminar" data-id="${row.id}">Eliminar</button>
                                `
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });

            // Editar financiamiento
            $('#tablaFinanciamientos').on('click', '.btnEditar', function () {
                const id = $(this).data('id');

                $.get(`/financiamientos/${id}/edit`, function (res) {
                    const f = res.financiamiento;

                    $('#formFinanciamiento')[0].reset();
                    $('#formFinanciamiento').attr('data-id', f.id);

                    // Campos básicos
                    $('[name="nombre"]').val(f.nombre);
                    $('[name="descripcion"]').val(f.descripcion);
                    $('[name="meses"]').val(f.meses);
                    $('[name="porcentaje_enganche"]').val(f.porcentaje_enganche);
                    $('[name="interes_anual"]').val(f.interes_anual);
                    $('[name="descuento_porcentaje"]').val(f.descuento_porcentaje);
                    $('[name="monto_minimo"]').val(f.monto_minimo);
                    $('[name="monto_maximo"]').val(f.monto_maximo);
                    $('[name="periodicidad_pago"]').val(f.periodicidad_pago);
                    $('[name="cargo_apertura"]').val(f.cargo_apertura);
                    $('[name="penalizacion_mora"]').val(f.penalizacion_mora);
                    $('[name="plazo_gracia_meses"]').val(f.plazo_gracia_meses);
                    $('[name="activo"]').val(f.activo ? 1 : 0);

                    // Select2 desarrollos
                    let selected = f.desarrollos.map(d => d.id);
                    $('#selectDesarrollos').val(selected).trigger('change');

                    $('#modalFinanciamiento').modal('show');
                });
            });

            // Guardar (crear o actualizar)
            $('#formFinanciamiento').on('submit', function (e) {
                e.preventDefault();

                let id = $(this).attr('data-id');
                let url = id ? `/financiamientos/${id}` : '{{ route("financiamientos.store") }}';
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function () {
                        $('#modalFinanciamiento').modal('hide');
                        $('#formFinanciamiento')[0].reset();
                        $('#formFinanciamiento').removeAttr('data-id');
                        tabla.ajax.reload();
                        Swal.fire('Éxito', 'Financiamiento guardado correctamente', 'success');
                    },
                    error: function () {
                        Swal.fire('Error', 'No se pudo guardar el financiamiento', 'error');
                    }
                });
            });

            // Eliminar financiamiento
            $('#tablaFinanciamientos').on('click', '.btnEliminar', function () {
                const id = $(this).data('id');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/financiamientos/${id}`,
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function () {
                                tabla.ajax.reload();
                                Swal.fire('Eliminado', 'El financiamiento ha sido eliminado', 'success');
                            },
                            error: function () {
                                Swal.fire('Error', 'No se pudo eliminar', 'error');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush