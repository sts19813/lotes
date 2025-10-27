@extends('layouts.app')

@section('title', 'Listado de Lotes')

@section('content')

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card-header ">
    <h3 class="card-title">Desarrollos</h3>
    <div class="card-toolbar">
        <a href="{{ route('desarrollos.create') }}" class="btn btn-primary">Nuevo Desarrollo</a>
    </div>
</div>

<div class="card-body">
    <table id="lots_table" class="table table-striped table-bordered table-hover table-checkable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Total Lotes</th>
                <th></th>
                <th>Creado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lots as $lot)
            <tr>
                <td>{{ $lot->id }}</td>
                <td>{{ $lot->name }}</td>
                <td>{{ $lot->description }}</td>
                <td>{{ $lot->total_lots }}</td>
                <td>
                    @if ($lot->png_image || $lot->svg_image)
                    <div class="image-container" style="position: relative; width: 200px;">
                        @if ($lot->png_image)
                        <img src="{{ asset('/' . $lot->png_image) }}" alt="PNG"
                            style="width: 100%; height: 100%; object-fit: cover;">
                        @endif

                        @if ($lot->svg_image)
                        <div class="svg-wrapper"
                            style="position: absolute; top: 0; left: 0; width: 100%; ">
                            {!! file_get_contents(public_path($lot->svg_image)) !!}
                        </div>
                        @endif
                    </div>
                    @endif
                </td>
                <td>{{ $lot->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('desarrollos.configurator', $lot->id) }}" 
                        class="btn btn-sm btn-primary">
                        Configurar
                    </a>
                
                    <a href="{{ url('iframe/' . $lot->id) }}" 
                        class="btn btn-sm btn-secondary" target="_blank">
                        Iframe
                    </a>
                
                    <a href="{{ route('desarrollos.edit', $lot->id) }}" 
                        class="btn btn-sm btn-warning">
                        Editar
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>



@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $("#lots_table").DataTable({
        responsive: true,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es_es.json"
        }
    });
});
</script>
@endpush