@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fw-bold text-gray-800">Nueva conexión</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('connections.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-control" placeholder="Ej. Adara API" required>
            </div>

            <div class="mb-4">
                <label class="form-label">URL del API</label>
                <input type="url" name="api_url" class="form-control" placeholder="https://adara.com/api" required>
            </div>

            <div class="mb-4">
                <label class="form-label">API Key</label>
                <input type="text" name="api_key" class="form-control" placeholder="Ingresa tu API key" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="ki-outline ki-check fs-2 me-2"></i> Guardar conexión
            </button>
            <a href="{{ route('connections.index') }}" class="btn btn-light ms-2">Cancelar</a>
        </form>
    </div>
</div>
@endsection
