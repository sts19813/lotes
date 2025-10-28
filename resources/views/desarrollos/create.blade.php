@extends('layouts.app')

@section('title', 'Crear Desarrollo')

@section('content')


<div class="card-header">
    <h3 class="card-title">Crear Desarrollo</h3>
</div>
<div class="card-body">

    {{-- Mostrar errores --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('desarrollo.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Nombre -->
        <div class="mb-4">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control" required />
        </div>

        <!-- Descripción -->
        <div class="mb-4">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control" rows="2"></textarea>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Fuente de datos</label>
            <select name="source_type" id="source_type" class="form-select form-select-solid" required>
                <option value="adara" selected>API Adara</option>
                <option value="naboo">Catálogo Naboo</option>
            </select>
        </div>


        <!-- Proyecto -->
        <div class="mb-4">
            <label class="form-label fw-bold">Proyecto</label>
            <select name="project_id" id="project_id" data-adara-projects='@json($projects)' class="form-select form-select-solid">
                <option value="">Seleccione un proyecto...</option>
                
            </select>
        </div>

        <!-- Fase -->
        <div class="mb-4">
            <label class="form-label fw-bold">Fase</label>
            <select name="phase_id" id="phase_id" class="form-select form-select-solid" disabled>
                <option value="">Seleccione una fase...</option>
            </select>
        </div>

        <!-- Etapa -->
        <div class="mb-4">
            <label class="form-label fw-bold">Etapa (Stage)</label>
            <select name="stage_id" id="stage_id" class="form-select form-select-solid" disabled>
                <option value="">Seleccione una etapa...</option>
            </select>
        </div>

        <!-- Total lotes -->
        <div class="mb-4">
            <label class="form-label">Total de lotes</label>
            <input type="number" name="total_lots" class="form-control" min="1" value="1" required />
        </div>

        <!-- Selección de imágenes -->
        <div class="row mb-4">
            <div class="col">
                <label class="form-label">Imagen SVG</label>
                <input type="file" name="svg_image" accept=".svg" class="form-control" required>
            </div>

            <div class="col">
                <label class="form-label">Imagen PNG</label>
                <input type="file" name="png_image" accept="image/png" class="form-control" required>
            </div>
        </div>

        <!-- Colores del modal -->
        <div class="row mb-4">
            <div class="col-md-2">
                <label class="form-label">Color Modal</label>
                <input type="text" name="modal_color" id="modal_color" class="form-control form-control-solid color-picker" placeholder="rgba(0,0,0,0.5)">
            </div>

            <div class="col-md-2">
                <label class="form-label">Color Primario</label>
                <input type="text" name="color_primario" id="color_primario" class="form-control form-control-solid color-picker" placeholder="#0044CC">
            </div>

            <div class="col-md-2">
                <label class="form-label">Color Acento</label>
                <input type="text" name="color_acento" id="color_acento" class="form-control form-control-solid color-picker" placeholder="#FFAA00">
            </div>

            <div class="col-md-6">
                <label class="form-label">Selector Modal</label>
                <input type="text" name="modal_selector" class="form-control" placeholder=".modal-class">
            </div>
        </div>


        <!-- Plusvalia -->
        <div class="mb-4">
            <label class="form-label">% Plusvalía</label>
            <input type="number" step="0.01" name="plusvalia" class="form-control" placeholder="Ej. 5.00">
        </div>

        <!-- Financiamiento -->
        <div class="mb-4">
            <label class="form-label">Meses de financiamiento</label>
            <input type="number" name="financing_months" class="form-control" placeholder="Ej. 36">
        </div>

        <!-- Redirect -->
        <div class="row mb-4">
            <div class="col">
                <label class="form-label">URL Regresar</label>
                <input type="text" name="redirect_return" class="form-control">
            </div>
            <div class="col">
                <label class="form-label">URL Siguiente</label>
                <input type="text" name="redirect_next" class="form-control">
            </div>
            <div class="col">
                <label class="form-label">URL Anterior</label>
                <input type="text" name="redirect_previous" class="form-control">
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                Guardar Desarrollo
            </button>
        </div>

    </form>
</div>


@endsection

@push('scripts')
     <script src="/assets/js/desarrollo.js"></script>
     <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pickers = document.querySelectorAll('.color-picker');

            pickers.forEach(input => {
                const pickr = Pickr.create({
                    el: input,
                    theme: 'classic',
                    default: input.value || null,
                    components: {
                        preview: true,
                        opacity: true, // Soporta transparencia ✅
                        hue: true,
                        interaction: {
                            input: true,
                            save: true,
                            clear: true
                        }
                    }
                });

                pickr.on('save', (color) => {
                    input.value = color.toRGBA().toString();
                    pickr.hide();
                });
            });
        });
        </script>
@endpush