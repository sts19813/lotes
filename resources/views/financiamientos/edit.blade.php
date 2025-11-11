@extends('layouts.app')

@section('title', 'Editar Financiamiento')

@section('content')
<div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title">Editar Plan de Financiamiento</h3>
    <a href="{{ route('financiamientos.index') }}" class="btn btn-light">
        <i class="fa fa-arrow-left"></i> Volver
    </a>
</div>
<div class="card-body">
    <form id="formFinanciamiento" action="{{ route('financiamientos.update', $financiamiento->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">

            <!-- Porcentajes principales -->
            <div class="col-md-3">
                <label class="form-label">% Enganche</label>
                <input type="number" step="0.01" name="porcentaje_enganche" class="form-control"
                    value="{{ $financiamiento->porcentaje_enganche }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">% Financiamiento</label>
                <input type="number" step="0.01" name="porcentaje_financiamiento" class="form-control"
                    value="{{ $financiamiento->porcentaje_financiamiento }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">% Saldo / Contado</label>
                <input type="number" step="0.01" name="porcentaje_saldo" class="form-control"
                    value="{{ $financiamiento->porcentaje_saldo }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">%  0</label>
                
            </div>

            <!-- Nombre y visibilidad -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Nombre del plan</label>
                <input type="text" name="nombre" class="form-control" value="{{ $financiamiento->nombre }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Visibilidad</label>
                <select name="visible" class="form-select">
                    <option value="1" {{ $financiamiento->visible ? 'selected' : '' }}>Público</option>
                    <option value="0" {{ !$financiamiento->visible ? 'selected' : '' }}>Privado</option>
                </select>
            </div>

            <!-- Descuento, Interés, Cuota de apertura -->
            <div class="col-md-4">
                <label class="form-label">% Descuento</label>
                <input type="number" step="0.01" name="descuento_porcentaje" class="form-control"
                    value="{{ $financiamiento->descuento_porcentaje }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">% Interés</label>
                <input type="number" step="0.01" name="financiamiento_interes" class="form-control"
                    value="{{ $financiamiento->financiamiento_interes }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Cuota de apertura</label>
                <input type="number" step="0.01" name="financiamiento_cuota_apertura" class="form-control"
                    value="{{ $financiamiento->financiamiento_cuota_apertura }}">
            </div>

            <!-- Descripción -->
            <div class="col-12 mt-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"
                    placeholder="Detalles legales, condiciones, etc.">{{ $financiamiento->descripcion }}</textarea>
            </div>

            <!-- Selección de desarrollos -->
            <div class="col-md-12">
                <label class="form-label">Desarrollos aplicables</label>
                <button type="button" class="btn btn-sm btn-secondary mb-2" id="selectTodosDesarrollos">Seleccionar
                    todos</button>
                <select name="desarrollos[]" id="selectDesarrollos" class="form-select" multiple="multiple"
                    style="width: 100%;">
                    @foreach($desarrollos as $desarrollo)
                        <option value="{{ $desarrollo->id }}" {{ in_array($desarrollo->id, $financiamiento->desarrollos->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{ $desarrollo->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Enganche -->
            <h6 class="fw-bold text-primary mt-4">Enganche</h6>
            <div class="col-md-3">
                <label class="form-label">¿Diferir?</label>
                <select name="enganche_diferido" class="form-select">
                    <option value="0" {{ $financiamiento->enganche_diferido == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ $financiamiento->enganche_diferido == 1 ? 'selected' : '' }}>Sí</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Número de pagos</label>
                <input type="number" name="enganche_num_pagos" class="form-control" min="1"
                    value="{{ $financiamiento->enganche_num_pagos }}">
            </div>

            <!-- Financiamiento -->
            <h6 class="fw-bold text-primary mt-4">Financiamiento</h6>
            <div class="col-md-3">
                <label class="form-label">Meses</label>
                <input type="number" name="financiamiento_meses" class="form-control"
                    value="{{ $financiamiento->financiamiento_meses }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">¿Anualidad?</label>
                <select name="tiene_anualidad" class="form-select">
                    <option value="0" {{ $financiamiento->tiene_anualidad == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ $financiamiento->tiene_anualidad == 1 ? 'selected' : '' }}>Sí</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">% Anualidad</label>
                <input type="number" step="0.01" name="porcentaje_anualidad" class="form-control"
                    value="{{ $financiamiento->porcentaje_anualidad }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Número de anualidades</label>
                <input type="number" name="numero_anualidades" class="form-control"
                    value="{{ $financiamiento->numero_anualidades }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Pagos por anualidad</label>
                <input type="number" name="pagos_por_anualidad" class="form-control"
                    value="{{ $financiamiento->pagos_por_anualidad }}">
            </div>

            <!-- Saldo / Contado -->
            <h6 class="fw-bold text-primary mt-4">Saldo / Contado</h6>
            <div class="col-md-3">
                <label class="form-label">¿Diferir?</label>
                <select name="saldo_diferido" class="form-select">
                    <option value="0" {{ $financiamiento->saldo_diferido == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ $financiamiento->saldo_diferido == 1 ? 'selected' : '' }}>Sí</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Número de pagos</label>
                <input type="number" name="saldo_num_pagos" class="form-control"
                    value="{{ $financiamiento->saldo_num_pagos }}">
            </div>

        </div>

        <div class="mt-5">
            <button type="submit" class="btn btn-primary">Actualizar Financiamiento</button>
            <a href="{{ route('financiamientos.index') }}" class="btn btn-light">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            // SELECT2 Desarrollos
            $('#selectDesarrollos').select2({
                placeholder: 'Selecciona desarrollos',
                allowClear: true,
                width: 'resolve'
            });

            $('#selectTodosDesarrollos').click(function () {
                $('#selectDesarrollos > option').prop("selected", true);
                $('#selectDesarrollos').trigger("change");
            });

            // Guardar cambios por AJAX
            $('#formFinanciamiento').on('submit', function (e) {
                e.preventDefault();

                let url = $(this).attr('action'); // route('financiamientos.update', $financiamiento->id)
                let method = 'PUT';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'El plan de financiamiento se actualizó correctamente.',
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
                            text: 'No se pudo actualizar el financiamiento. Revisa los campos requeridos.'
                        });
                    }
                });
            });

        });
    </script>
@endpush