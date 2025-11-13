@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fw-bold text-gray-800">Editar conexión</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('connections.update', $connection) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" value="{{ $connection->name }}" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="form-label">URL del API</label>
                <input type="url" name="api_url" value="{{ $connection->api_url }}" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="form-label">API Key</label>
                <input type="text" name="api_key" value="{{ $connection->api_key }}" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="ki-outline ki-check fs-2 me-2"></i> Actualizar
            </button>
            <a href="{{ route('connections.index') }}" class="btn btn-light ms-2">Cancelar</a>
        </form>
    </div>
</div>
@endsection
