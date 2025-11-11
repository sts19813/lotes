@extends('layouts.app')

@section('title', 'Nuevo Financiamiento')

@section('content')
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Nuevo Plan de Financiamiento</h3>
        <a href="{{ route('financiamientos.index') }}" class="btn btn-light">
            <i class="fa fa-arrow-left"></i> Volver
        </a>
    </div>
    <br><br>
    <div class="card-body">
        <form id="formFinanciamiento" action="{{ route('financiamientos.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <!-- Porcentajes principales -->
                <div class="card mb-5" id="porcentajesCard">
                    <div class="card-body row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">% Enganche</label>
                            <input type="number" step="0.01" name="porcentaje_enganche" class="form-control porcentaje"
                                value="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">% Financiamiento</label>
                            <input type="number" step="0.01" name="porcentaje_financiamiento"
                                class="form-control porcentaje" value="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">% Saldo / Contado</label>
                            <input type="number" step="0.01" name="porcentaje_saldo" class="form-control porcentaje"
                                value="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Por asignar</label>
                            <input type="text" class="form-control" id="porAsignar" value="100%" readonly>
                        </div>
                    </div>
                </div>


                <div class="card mb-5">
                    <div class="card-body row">
                        <!-- Nombre y visibilidad -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre del plan</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Visibilidad</label>
                            <select name="visible" class="form-select">
                                <option value="1">Público</option>
                                <option value="0">Privado</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">% Descuento</label>
                            <input type="number" step="0.01" name="descuento_porcentaje" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">% Interés</label>
                            <input type="number" step="0.01" name="financiamiento_interes" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cuota de apertura</label>
                            <input type="number" step="0.01" name="financiamiento_cuota_apertura" class="form-control">
                        </div>

                        <div class="col-12 mt-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"
                                placeholder="Detalles legales, condiciones, etc."></textarea>
                        </div>

                        <!-- Selección de desarrollos -->
                        <div class="col-md-12">
                            <br>
                            <label class="form-label">Desarrollos aplicables</label>
                            <button type="button" class="btn btn-sm btn-secondary mb-2"
                                id="selectTodosDesarrollos">Seleccionar
                                todos</button>
                            <select name="desarrollos[]" id="selectDesarrollos" class="form-select" multiple="multiple"
                                style="width: 100%;">
                                @foreach($desarrollos as $desarrollo)
                                    <option value="{{ $desarrollo->id }}">{{ $desarrollo->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Enganche Card -->
                <div class="card mb-5" id="cardEnganche" style="display:none;">
                    <div class="card-body row">
                        <h6 class="fw-bold text-primary mt-2">Enganche</h6>
                        <div class="col-md-3">
                            <label class="form-label">¿Diferir?</label>
                            <select name="enganche_diferido" id="engancheDiferido" class="form-select">
                                <option value="0" selected>No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="engancheNumPagosContainer" style="display: none;">
                            <label class="form-label">Número de pagos</label>
                            <input type="number" name="enganche_num_pagos" class="form-control" min="1">
                        </div>
                    </div>
                </div>

                <!-- Financiamiento Card -->
                <div class="card mb-5" id="cardFinanciamiento" style="display:none;">
                    <div class="card-body row">
                        <h6 class="fw-bold text-primary mt-4">Financiamiento</h6>
                        <div class="col-md-3">
                            <label class="form-label">Meses</label>
                            <input type="number" name="financiamiento_meses" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">¿Anualidad?</label>
                            <select name="tiene_anualidad" id="tieneAnualidad" class="form-select">
                                <option value="0" selected>No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="porcentajeAnualidadContainer" style="display: none;">
                            <label class="form-label">% Anualidad</label>
                            <input type="number" step="0.01" name="porcentaje_anualidad" class="form-control">
                        </div>
                        <div class="col-md-3" id="numeroAnualidadesContainer" style="display: none;">
                            <label class="form-label">Número de anualidades</label>
                            <input type="number" name="numero_anualidades" class="form-control">
                        </div>
                        <div class="col-md-3" id="pagosPorAnualidadContainer" style="display: none;">
                            <label class="form-label">Pagos por anualidad</label>
                            <input type="number" name="pagos_por_anualidad" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Saldo / Contado Card -->
                <div class="card mb-5" id="cardSaldo" style="display:none;">
                    <div class="card-body row">
                        <h6 class="fw-bold text-primary mt-4">Saldo / Contado</h6>
                        <div class="col-md-3">
                            <label class="form-label">¿Diferir?</label>
                            <select name="saldo_diferido" id="saldoDiferido" class="form-select">
                                <option value="0" selected>No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="saldoNumPagosContainer" style="display: none;">
                            <label class="form-label">Número de pagos</label>
                            <input type="number" name="saldo_num_pagos" class="form-control">
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-5">
                <button type="submit" class="btn btn-primary">Guardar Financiamiento</button>
                <a href="{{ route('financiamientos.index') }}" class="btn btn-light">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            /**********************************************************
             * SELECT2 DESARROLLOS
             **********************************************************/
            $('#selectDesarrollos').select2({
                placeholder: 'Selecciona desarrollos',
                allowClear: true,
                width: 'resolve'
            });

            $('#selectTodosDesarrollos').click(function () {
                $('#selectDesarrollos > option').prop("selected", true);
                $('#selectDesarrollos').trigger("change");
            });

            /**********************************************************
             * GUARDAR FINANCIAMIENTO POR AJAX
             **********************************************************/
            $('#formFinanciamiento').on('submit', function (e) {
                e.preventDefault();

                let url = $(this).attr('action'); // route('financiamientos.store')
                let method = 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'El plan de financiamiento se guardó correctamente.',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            // Redirigir al inicio de financiamientos
                            window.location.href = "{{ route('financiamientos.index') }}";
                        });
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo guardar el financiamiento. Revisa los campos requeridos.'
                        });
                    }
                });
            });


            function actualizarPorAsignar() {
                let suma = 0;
                $('.porcentaje').each(function () {
                    let val = parseFloat($(this).val()) || 0;
                    suma += val;
                });

                let restante = 100 - suma;
                let campo = $('#porAsignar');

                campo.val(restante.toFixed(2) + '%');

                // Cambiar color si llega a 100%
                if (Math.abs(restante) < 0.01) {
                    campo.removeClass('text-danger').addClass('text-success fw-bold');
                } else {
                    campo.removeClass('text-success fw-bold').addClass('text-danger');
                }
            }

            // Cada vez que cambie alguno de los porcentajes
            $('.porcentaje').on('input', actualizarPorAsignar);

            // Inicializar al cargar
            actualizarPorAsignar();


            // Enganche
            $('#engancheDiferido').on('change', function () {
                if ($(this).val() == '1') {
                    $('#engancheNumPagosContainer').slideDown();
                } else {
                    $('#engancheNumPagosContainer').slideUp();
                    $('#engancheNumPagosContainer input').val('');
                }
            });

            // Financiamiento
            $('#tieneAnualidad').on('change', function () {
                if ($(this).val() == '1') {
                    $('#porcentajeAnualidadContainer, #numeroAnualidadesContainer, #pagosPorAnualidadContainer').slideDown();
                } else {
                    $('#porcentajeAnualidadContainer, #numeroAnualidadesContainer, #pagosPorAnualidadContainer').slideUp();
                    $('#porcentajeAnualidadContainer input, #numeroAnualidadesContainer input, #pagosPorAnualidadContainer input').val('');
                }
            });

            // Saldo / Contado
            $('#saldoDiferido').on('change', function () {
                if ($(this).val() == '1') {
                    $('#saldoNumPagosContainer').slideDown();
                } else {
                    $('#saldoNumPagosContainer').slideUp();
                    $('#saldoNumPagosContainer input').val('');
                }
            });


            // Mostrar campos según "Sí/No"
            function toggleFields() {
                $('#engancheNumPagosContainer').toggle($('#engancheDiferido').val() == '1');
                $('#porcentajeAnualidadContainer, #numeroAnualidadesContainer, #pagosPorAnualidadContainer')
                    .toggle($('#tieneAnualidad').val() == '1');
                $('#saldoNumPagosContainer').toggle($('#saldoDiferido').val() == '1');
            }

            $('#engancheDiferido, #tieneAnualidad, #saldoDiferido').on('change', toggleFields);
            toggleFields(); // inicial

            // Mostrar/ocultar cards según porcentaje
            function updatePorcentajes() {
                let enganche = parseFloat($('input[name="porcentaje_enganche"]').val()) || 0;
                let financiamiento = parseFloat($('input[name="porcentaje_financiamiento"]').val()) || 0;
                let saldo = parseFloat($('input[name="porcentaje_saldo"]').val()) || 0;

                // Cards
                $('#cardEnganche').toggle(enganche > 0);
                $('#cardFinanciamiento').toggle(financiamiento > 0);
                $('#cardSaldo').toggle(saldo > 0);

                // Por asignar
                let porAsignar = 100 - (enganche + financiamiento + saldo);
                porAsignar = porAsignar < 0 ? 0 : porAsignar;
                $('#porAsignar').val(porAsignar + '%');

               
            }

            $('.porcentaje').on('input', updatePorcentajes);
            updatePorcentajes(); // inicial
        });
    </script>
@endpush