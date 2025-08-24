@extends('layouts.app')

@section('title', 'Editar Lote')

@section('content')
<div class="app-container container-xxl">

    <div class="card card-flush shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Editar Desarrollo</h3>
        </div>
        <div class="card-body">
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
            
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $lot->name) }}" required>
                </div>
            
                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $lot->description) }}</textarea>
                </div>
            
                <div class="mb-3">
                    <label class="form-label fw-bold">Proyecto</label>
                    <select name="project_id" id="project_id" class="form-select form-select-solid">
                        <option value="">Seleccione un proyecto...</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project['id'] }}" 
                                {{ $lot->project_id == $project['id'] ? 'selected' : '' }}>
                                {{ $project['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            
                <div class="mb-3">
                    <label class="form-label fw-bold">Fase</label>
                    <select name="phase_id" id="phase_id" class="form-select form-select-solid">
                        <option value="">Seleccione una fase...</option>
                        @foreach ($phases as $phase)
                            <option value="{{ $phase['id'] }}" 
                                {{ $lot->phase_id == $phase['id'] ? 'selected' : '' }}>
                                {{ $phase['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            
                <div class="mb-3">
                    <label class="form-label fw-bold">Etapa (Stage)</label>
                    <select name="stage_id" id="stage_id" class="form-select form-select-solid">
                        <option value="">Seleccione una etapa...</option>
                        @foreach ($stages as $stage)
                            <option value="{{ $stage['id'] }}" 
                                {{ $lot->stage_id == $stage['id'] ? 'selected' : '' }}>
                                {{ $stage['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            
                <div class="mb-3">
                    <label for="total_lots" class="form-label">Total de Lotes</label>
                    <input type="number" name="total_lots" class="form-control" value="{{ old('total_lots', $lot->total_lots) }}" min="1" required>
                </div>
            
                <div class="mb-3">
                    <label for="svg_image" class="form-label">Imagen SVG</label>
                    @if ($lot->svg_image)
                        <div class="mb-2">
                            <small class="text-muted">Actual:</small>
                            <div>{!! file_get_contents(public_path($lot->svg_image)) !!}</div>
                        </div>
                    @endif
                    <input type="file" name="svg_image" accept=".svg" class="form-control">
                </div>
            
                <div class="mb-3">
                    <label for="png_image" class="form-label">Imagen PNG</label>
                    @if ($lot->png_image)
                        <div class="mb-2">
                            <small class="text-muted">Actual:</small>
                            <img src="{{ asset('/' . $lot->png_image) }}" alt="PNG" style="width: 200px;">
                        </div>
                    @endif
                    <input type="file" name="png_image" accept="image/png" class="form-control">
                </div>
            
                <div class="d-flex justify-content-between">
                   
                    
                    <form action="{{ route('desarrollos.destroy', $lot->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este desarrollo?');" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>

                    <form action="{{ route('desarrollos.update', $lot->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                    
                        <button type="submit" class="btn btn-primary">Actualizar Lote</button>
                    </form>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
