@extends('layouts.app')

@section('title', 'Listado de Lotes')

@section('content')

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card-header d-flex flex-wrap justify-content-between align-items-center py-5">
    <div class="card-title mb-0">
        <h3 class="fw-bold text-gray-800 mb-1">Desarrollos</h3>
        <span class="text-muted fs-7">Gestiona la configuración de todos tus desarrollos inmobiliarios</span>
    </div>

    <div class="card-toolbar">
        <a href="{{ route('desarrollos.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="ki-duotone ki-plus fs-2"></i>
            <span>Nuevo Desarrollo</span>
        </a>
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
                        <img data-src="{{ asset('/' . $lot->png_image) }}"
                            alt="PNG"
                            class="img-thumbnail lazy-img"
                            style="width:100%; height:100%; object-fit:cover;"
                            loading="lazy">
                        @endif

                        @if ($lot->svg_image)
                        <img data-src="{{ asset('/' . $lot->svg_image) }}"
                            alt="SVG"
                            class="svg-lazy lazy-img"
                            style="position:absolute; top:0; left:0; width:100%; height:100%;"
                            loading="lazy">
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

    let observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                observer.unobserve(img);
            }
        });
    }, { root: null, threshold: 0.1 });

    const table = $("#lots_table").DataTable({
        responsive: true,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        language: { url: '//cdn.datatables.net/plug-ins/2.3.2/i18n/es-MX.json' },
        dom: "<'row mb-3'<'col-12 d-flex justify-content-end'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'p>>",
        drawCallback: function () {
            document.querySelectorAll("img.lazy-img:not([data-observed])")
                .forEach(img => {
                    img.dataset.observed = "true";
                    observer.observe(img);
                });
        }
    });
});
</script>
@endpush