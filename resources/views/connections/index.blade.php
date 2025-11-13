@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-bold text-gray-800">Conexiones con sistemas externos</h1>
        <a href="{{ route('connections.create') }}" class="btn btn-primary">
            <i class="ki-outline ki-plus fs-2 me-2"></i> Nueva conexión
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table align-middle table-row-dashed gy-5">
                <thead>
                    <tr class="fw-semibold fs-6 text-gray-600">
                        <th>Nombre</th>
                        <th>URL del API</th>
                        <th>API Key</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($connections as $conn)
                        <tr>
                            <td>{{ $conn->name }}</td>
                            <td>{{ $conn->api_url }}</td>
                            <td><span class="text-muted">{{ Str::limit($conn->api_key, 20) }}</span></td>
                            <td>
                                <a href="{{ route('connections.edit', $conn) }}" class="btn btn-sm btn-light-primary">
                                    <i class="ki-outline ki-pencil fs-2"></i>
                                </a>
                                <form action="{{ route('connections.destroy', $conn) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-light-danger"
                                        onclick="return confirm('¿Eliminar esta conexión?')">
                                        <i class="ki-outline ki-trash fs-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500">No hay conexiones registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection