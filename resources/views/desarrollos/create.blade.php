@extends('layouts.app')

@section('title', 'Crear Desarrollo')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="fw-bold text-gray-800">
                    <i class="ki-outline ki-plus fs-2 me-2 text-primary"></i>
                    Crear Nuevo Desarrollo
                </h1>
                <span class="text-muted fs-7">Crea tu Desarrollo para posterior configurar el cotizador mapeando tus
                    unidades.</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.index') }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="ki-outline ki-arrow-left fs-2 me-2"></i> Volver a Desarrollos
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
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

                <div class="card shadow-sm mb-5">
                    <div class="card-header">
                        <h4 class="card-title fw-bold">Formulario de Desarrollo</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre</label>
                                <input type="text" name="name" class="form-control" required />
                            </div>

                            <!-- Descripción -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>

                            <!-- Fuente de datos -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fuente de datos</label>
                                <select name="source_type" id="source_type" class="form-select form-select-solid" required>
                                    <option value="adara" selected>API Adara</option>
                                    <option value="naboo">Catálogo Naboo</option>
                                </select>
                            </div>

                            <!-- Proyecto -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Proyecto</label>
                                <select name="project_id" id="project_id" data-adara-projects='@json($projects)'
                                    class="form-select form-select-solid">
                                    <option value="">Seleccione un proyecto...</option>
                                </select>
                            </div>

                            <!-- Fase -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fase</label>
                                <select name="phase_id" id="phase_id" class="form-select form-select-solid" disabled>
                                    <option value="">Seleccione una fase...</option>
                                </select>
                            </div>

                            <!-- Etapa -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Etapa (Stage)</label>
                                <select name="stage_id" id="stage_id" class="form-select form-select-solid" disabled>
                                    <option value="">Seleccione una etapa...</option>
                                </select>
                            </div>

                            <!-- Total lotes -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Total de Lotes</label>
                                <input type="number" name="total_lots" class="form-control" min="1" value="1" required />
                            </div>

                            <!-- Plusvalía -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">% Plusvalía</label>
                                <input type="number" step="0.01" name="plusvalia" class="form-control"
                                    placeholder="Ej. 5.00">
                            </div>

                            <!-- Financiamiento -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Meses de financiamiento</label>
                                <i class="ki-outline ki-information-5 fs-5 ms-2" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Si no se configura un modelo de financiamiento específico, se utilizará esta configuración por defecto en el cotizador.">
                                </i>
                                <input type="number" name="financing_months" class="form-control" placeholder="Ej. 36">
                            </div>

                            <!-- Imágenes -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Imagen SVG</label>
                                <input type="file" id="svg_input" name="svg_image" accept=".svg" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Imagen PNG</label>
                                <input type="file" name="png_image" accept="image" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Colores en card separado -->
                <div class="card mt-5 shadow-sm">
                    <div class="card-header">
                        <h4 class="card-title fw-bold">Colores del Desarrollo</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-2">
                                <label class="form-label">Color Modal</label>
                                <input type="text" name="modal_color" id="modal_color"
                                    class="form-control form-control-solid color-picker" placeholder="rgba(0,0,0,0.5)">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Color Primario</label>
                                <input type="text" name="color_primario" id="color_primario"
                                    class="form-control form-control-solid color-picker" placeholder="#0044CC">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Color Acento</label>
                                <input type="text" name="color_acento" id="color_acento"
                                    class="form-control form-control-solid color-picker" placeholder="#FFAA00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Selector Modal</label>
                                <i class="ki-outline ki-information-5 fs-5 ms-2" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Este campo se rellena automáticamente al cargar el archivo SVG.">
                                </i>
                                <input type="text" id="modal_selector" name="modal_selector" class="form-control"
                                    placeholder="svg *">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Redirecciones -->
                <div class="card mt-5 shadow-sm">
                    <div class="card-header">
                        <h4 class="card-title fw-bold">Redirecciones</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label">URL Regresar</label>
                                <select name="redirect_return" class="form-select">
                                    <option value="">Seleccione una opción</option>
                                    @foreach ($desarrollos as $desarrollo)
                                        <option value="{{ $desarrollo['id'] }}">{{ $desarrollo['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">URL Siguiente</label>
                                <select name="redirect_next" class="form-select">
                                    <option value="">Seleccione una opción</option>
                                    @foreach ($desarrollos as $desarrollo)
                                        <option value="{{ $desarrollo['id'] }}">{{ $desarrollo['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">URL Anterior</label>
                                <select name="redirect_previous" class="form-select">
                                    <option value="">Seleccione una opción</option>
                                    @foreach ($desarrollos as $desarrollo)
                                        <option value="{{ $desarrollo['id'] }}">{{ $desarrollo['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-5">
                    <button type="submit" class="btn btn-primary">
                        Guardar Desarrollo
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/assets/js/desarrollo.js"></script>
    <script src="/assets/js/desarrollo/procesarSVG.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pickers = document.querySelectorAll('.color-picker');

            pickers.forEach(input => {
                const pickrContainer = document.createElement('div');
                input.parentNode.insertBefore(pickrContainer, input.nextSibling);

                const pickr = Pickr.create({
                    el: pickrContainer,
                    theme: 'classic',
                    default: input.value || '#30362D',
                    components: {
                        preview: true,
                        opacity: false,
                        hue: true,
                        interaction: {
                            hex: true,
                            input: true,
                            save: true
                        }
                    }
                });

                pickr.on('change', (color) => {
                    const hex = color.toHEXA().slice(0, 3).map(c => c.toString(16).padStart(2, '0')).join('');
                    input.value = `#${hex}`;
                    input.style.backgroundColor = input.value;
                });

                pickr.on('save', (color) => {
                    if (!color) return;
                    const hex = color.toHEXA().slice(0, 3).map(c => c.toString(16).padStart(2, '0')).join('');
                    input.value = `#${hex}`;
                    input.style.backgroundColor = input.value;
                    pickr.hide();
                });

                if (input.value) {
                    input.style.backgroundColor = input.value;
                }
            });
        });
    </script>
@endpush