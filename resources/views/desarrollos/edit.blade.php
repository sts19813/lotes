@extends('layouts.app')

@section('title', 'Editar Desarrollo')

@section('content')

    <div class="card-header">
        <h3 class="card-title">Editar Desarrollo</h3>
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

        <!-- Formulario editar -->
        <form action="{{ route('desarrollos.update', $lot->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div class="mb-4">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $lot->name) }}" required>
            </div>

            <!-- Descripción -->
            <div class="mb-4">
                <label class="form-label">Descripción</label>
                <textarea name="description" class="form-control"
                    rows="2">{{ old('description', $lot->description) }}</textarea>
            </div>

            <!-- Fuente -->
            <div class="mb-4">
                <label class="form-label fw-bold">Fuente</label>
                <select name="source_type" id="source_type" class="form-select form-select-solid" required>
                    <option value="">Seleccione una fuente...</option>
                    <option value="adara" {{ $lot->source_type === 'adara' ? 'selected' : '' }}>Adara</option>
                    <option value="naboo" {{ $lot->source_type === 'naboo' ? 'selected' : '' }}>Naboo</option>
                </select>
            </div>

            <!-- Proyecto -->
            <div class="mb-4">
                <label class="form-label fw-bold">Proyecto</label>
                <select name="project_id" id="project_id" class="form-select form-select-solid"
                    data-adara-projects='@json($projects)'>
                    <option value="">Seleccione un proyecto...</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project['id'] }}" {{ $lot->project_id == $project['id'] ? 'selected' : '' }}>
                            {{ $project['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fase -->
            <div class="mb-4">
                <label class="form-label fw-bold">Fase</label>
                <select name="phase_id" id="phase_id" class="form-select form-select-solid">
                    <option value="">Seleccione una fase...</option>
                </select>
            </div>

            <!-- Etapa -->
            <div class="mb-4">
                <label class="form-label fw-bold">Etapa (Stage)</label>
                <select name="stage_id" id="stage_id" class="form-select form-select-solid">
                    <option value="">Seleccione una etapa...</option>
                </select>
            </div>

            <!-- Total de lotes -->
            <div class="mb-4">
                <label class="form-label">Total de Lotes</label>
                <input type="number" name="total_lots" class="form-control"
                    value="{{ old('total_lots', $lot->total_lots) }}" min="1" required>
            </div>

            <!-- Imágenes -->
            <div class="row mb-4">
                <div class="col">
                    <label class="form-label">Imagen SVG</label>
                    @if ($lot->svg_image)
                        <div class="mb-2">{!! file_get_contents(public_path($lot->svg_image)) !!}</div>
                    @endif
                    <input type="file" name="svg_image" accept=".svg" class="form-control">
                </div>
                <div class="col">
                    <label class="form-label">Imagen PNG</label>
                    @if ($lot->png_image)
                        <img src="{{ asset($lot->png_image) }}" width="150" class="mb-2">
                    @endif
                    <input type="file" name="png_image" accept="image/png" class="form-control">
                </div>
            </div>

            <!-- Colores -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Color Modal</label>
                    <input type="text" name="modal_color" class="form-control"
                        value="{{ old('modal_color', $lot->modal_color) }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Selector Modal</label>
                    <input type="text" name="modal_selector" class="form-control"
                        value="{{ old('modal_selector', $lot->modal_selector) }}">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col">
                    <label class="form-label">Color Primario</label>
                    <input type="text" name="color_primario" class="form-control"
                        value="{{ old('color_primario', $lot->color_primario) }}">
                </div>
                <div class="col">
                    <label class="form-label">Color Acento</label>
                    <input type="text" name="color_acento" class="form-control"
                        value="{{ old('color_acento', $lot->color_acento) }}">
                </div>
            </div>

            <!-- Plusvalía -->
            <div class="mb-4">
                <label class="form-label">% Plusvalía</label>
                <input type="number" step="0.01" name="plusvalia" class="form-control"
                    value="{{ old('plusvalia', $lot->plusvalia) }}">
            </div>

            <!-- Financiamiento -->
            <div class="mb-4">
                <label class="form-label">Meses de financiamiento</label>
                <input type="number" name="financing_months" class="form-control"
                    value="{{ old('financing_months', $lot->financing_months) }}">
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    Actualizar Desarrollo
                </button>
            </div>

        </form>

        <!-- Botón eliminar -->
        <form action="{{ route('desarrollos.destroy', $lot->id) }}" method="POST"
            onsubmit="return confirm('¿Estás seguro de eliminar este desarrollo?');" class="mt-4">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger w-100">Eliminar Desarrollo</button>
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        window.selectedPhase = "{{ $lot->phase_id }}";
        window.selectedStage = "{{ $lot->stage_id }}";
        window.selectedSource = "{{ $lot->source_type }}";
        window.selectedProject = "{{ $lot->project_id }}";
    </script>
    <script src="/assets/js/desarrollo.js"></script>
@endpush