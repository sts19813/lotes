$(document).ready(function () {

    /**********************************************************
     * SELECT2 DESARROLLOS
     **********************************************************/
    if ($('#selectDesarrollos').length) {
        $('#selectDesarrollos').select2({
            placeholder: 'Selecciona desarrollos',
            allowClear: true,
            width: 'resolve'
        });

        $('#selectTodosDesarrollos').on('click', function () {
            $('#selectDesarrollos > option').prop('selected', true);
            $('#selectDesarrollos').trigger('change');
        });
    }


    /**********************************************************
     * SUBMIT CREATE / EDIT (POST o PUT)
     **********************************************************/
    $('#formFinanciamiento').on('submit', function (e) {
        e.preventDefault();

        let $form = $(this);
        let url = $form.attr('action');
        let method = $form.find('input[name="_method"]').length ? 'POST' : 'POST';
        let data = $form.serialize();

        if ($form.find('input[name="_method"]').length) {
            data += '&_method=PUT';
        }

        let enganche = parseFloat($('input[name="porcentaje_enganche"]').val()) || 0;
        let financiamiento = parseFloat($('input[name="porcentaje_financiamiento"]').val()) || 0;
        let saldo = parseFloat($('input[name="porcentaje_saldo"]').val()) || 0;

        /* =====================================================
         * VALIDACIÓN 1: SUMA TOTAL
         * ===================================================== */
        let total = enganche + financiamiento + saldo;

        if (Math.abs(total - 100) > 0.01) {
            Swal.fire({
                icon: 'error',
                title: 'Porcentajes inválidos',
                text: 'La suma de Enganche, Financiamiento y Saldo debe ser exactamente 100%.'
            });
            return;
        }

        /* =====================================================
         * VALIDACIÓN 2: ENGANCHE DIFERIDO
         * ===================================================== */
        if (enganche > 0 && $('#engancheDiferido').val() === '1') {
            let pagosEnganche = parseInt($('input[name="enganche_num_pagos"]').val(), 10);

            if (!isEnteroValido(pagosEnganche)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Enganche inválido',
                    text: 'El número de pagos del enganche debe ser un entero mayor a 0.'
                });
                return;
            }
        }

        /* =====================================================
         * VALIDACIÓN 3: FINANCIAMIENTO
         * ===================================================== */
        if (financiamiento > 0) {
            let meses = parseInt($('input[name="financiamiento_meses"]').val(), 10);

            if (!isEnteroValido(meses)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Financiamiento inválido',
                    text: 'Los meses de financiamiento deben ser un entero mayor a 0.'
                });
                return;
            }
        }

        /* =====================================================
         * VALIDACIÓN 4: SALDO DIFERIDO
         * ===================================================== */
        if (saldo > 0 && $('#saldoDiferido').val() === '1') {
            let pagosSaldo = parseInt($('input[name="saldo_num_pagos"]').val(), 10);

            if (!isEnteroValido(pagosSaldo)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Saldo inválido',
                    text: 'El número de pagos del saldo debe ser un entero mayor a 0.'
                });
                return;
            }
        }

        /* =====================================================
         * AJAX
         * ===================================================== */
        $.ajax({
            url: url,
            type: method,
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'El plan de financiamiento se guardó correctamente.',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.href = window.financiamientosIndexUrl;
                });
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al guardar el financiamiento.'
                });
            }
        });
    });


    /**********************************************************
     * POR ASIGNAR
     **********************************************************/
    function actualizarPorAsignar() {
        let suma = 0;

        $('.porcentaje').each(function () {
            suma += parseFloat($(this).val()) || 0;
        });

        let restante = 100 - suma;
        let campo = $('#porAsignar');

        campo.val(restante.toFixed(2) + '%');

        if (Math.abs(restante) < 0.01) {
            campo.removeClass('text-danger').addClass('text-success fw-bold');
        } else {
            campo.removeClass('text-success fw-bold').addClass('text-danger');
        }
    }

    $('.porcentaje').on('input', actualizarPorAsignar);
    actualizarPorAsignar();


    /**********************************************************
     * TOGGLES ENGANCHE
     **********************************************************/
    $('#engancheDiferido').on('change', function () {
        $('#engancheNumPagosContainer').toggle($(this).val() === '1');

        if ($(this).val() !== '1') {
            $('#engancheNumPagosContainer input').val('');
        }
    });


    /**********************************************************
     * TOGGLES ANUALIDAD
     **********************************************************/
    $('#tieneAnualidad').on('change', function () {
        let visible = $(this).val() === '1';

        $('#porcentajeAnualidadContainer, #numeroAnualidadesContainer, #pagosPorAnualidadContainer')
            .toggle(visible);

        if (!visible) {
            $('#porcentajeAnualidadContainer input, #numeroAnualidadesContainer input, #pagosPorAnualidadContainer input')
                .val('');
        }
    });


    /**********************************************************
     * TOGGLES SALDO
     **********************************************************/
    $('#saldoDiferido').on('change', function () {
        $('#saldoNumPagosContainer').toggle($(this).val() === '1');

        if ($(this).val() !== '1') {
            $('#saldoNumPagosContainer input').val('');
        }
    });


    /**********************************************************
     * MOSTRAR SEGÚN VALORES PRECARGADOS
     **********************************************************/
    function toggleFields() {
        $('#engancheNumPagosContainer').toggle($('#engancheDiferido').val() === '1');
        $('#porcentajeAnualidadContainer, #numeroAnualidadesContainer, #pagosPorAnualidadContainer')
            .toggle($('#tieneAnualidad').val() === '1');
        $('#saldoNumPagosContainer').toggle($('#saldoDiferido').val() === '1');
    }

    toggleFields();


    /**********************************************************
     * MOSTRAR / OCULTAR CARDS
     **********************************************************/
    function updatePorcentajes() {
        let enganche = parseFloat($('input[name="porcentaje_enganche"]').val()) || 0;
        let financiamiento = parseFloat($('input[name="porcentaje_financiamiento"]').val()) || 0;
        let saldo = parseFloat($('input[name="porcentaje_saldo"]').val()) || 0;

        $('#cardEnganche').toggle(enganche > 0);
        $('#cardFinanciamiento').toggle(financiamiento > 0);
        $('#cardSaldo').toggle(saldo > 0);

        let porAsignar = 100 - (enganche + financiamiento + saldo);
        $('#porAsignar').val((porAsignar < 0 ? 0 : porAsignar) + '%');
    }

    $('.porcentaje').on('input', updatePorcentajes);
    updatePorcentajes();
});


/**********************************************************
 * UTILIDADES
 **********************************************************/
function isEnteroValido(valor) {
    return Number.isInteger(valor) && valor > 0;
}
