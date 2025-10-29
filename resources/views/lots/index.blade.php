@extends('layouts.app')

@section('title', 'Listado de Lotes')

@section('content')

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card-header">
    <h3 class="card-title">Desarrollos</h3>
</div>

<div class="card-body">
    <table id="lots_table" class="table table-striped table-bordered table-hover table-checkable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Total Lotes</th>
                <th>Imagen</th>
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
                <td>{{ $lot->total_lots ?? 0 }}</td>
                <td>
                    @if ($lot->png_image || $lot->svg_image)
                    <div class="image-container position-relative" style="width:200px;">
                        @if ($lot->png_image)
                        <img src="{{ asset($lot->png_image) }}" alt="PNG"
                            style="width:100%;height:100%;object-fit:cover;">
                        @endif

                        @if ($lot->svg_image)
                        <img src="{{ asset($lot->svg_image) }}"
                            class="position-absolute top-0 start-0 w-100 h-100" alt="SVG">
                        @endif
                    </div>
                    @endif
                </td>
                <td>{{ $lot->created_at->format('d/m/Y H:i') }}</td>
              <td class="text-end">
                    <a href="{{ url('iframe/' . $lot->id) }}"
                    class="btn btn-sm btn-light btn-active-light-primary"
                    data-bs-toggle="tooltip"
                    title="Ver iframe"
                    target="_blank">
                        <i class="ki-outline ki-arrow-up-right fs-2"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $("#lots_table").DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es_es.json"
            }
        });
    });
</script>
@endsection
