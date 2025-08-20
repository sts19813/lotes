@extends('layouts.app')

@section('title', 'Crear Lote')

@section('content')
<div class="app-container container-xxl">

    <div class="card card-flush shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Crear Desarrollo</h3>
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

            <form action="{{ route('desarrollo.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="total_lots" class="form-label">Total de Lotes</label>
                    <input type="number" name="total_lots" class="form-control" value="1" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="svg_image" class="form-label">Imagen SVG</label>
                    <input type="file" name="svg_image" accept=".svg" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="png_image" class="form-label">Imagen PNG</label>
                    <input type="file" name="png_image" accept="image/png" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Lote</button>
            </form>
        </div>
    </div>

</div>
@endsection
