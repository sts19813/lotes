@extends('layouts.app')

@section('title', 'Desarrollos')

@section('content')
<div class="d-flex flex-column flex-column-fluid">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold text-gray-800">
                <i class="ki-outline ki-home-2 fs-2 me-2 text-primary"></i>
                {{ __('messages.title') }}
            </h1>
            <span class="text-muted fs-7">{{ __('messages.subtitle') }}</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('desarrollos.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="ki-duotone ki-plus fs-2"></i>
                <span>{{ __('messages.new') }}</span>
            </a>
            <button class="btn btn-light-success" id="btnActualizar">
                <i class="ki-outline ki-arrows-circle fs-2 me-1"></i> {{ __('messages.refresh') }}
            </button>
        </div>
    </div>

    <!--begin::Card-->
    <div class="card shadow-sm">
        <div class="card-body pt-0">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table id="lots_table" class="table table-striped table-row-dashed align-middle fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th>{{ __('messages.table.id') }}</th>
                        <th>{{ __('messages.table.name') }}</th>
                        <th>{{ __('messages.table.description') }}</th>
                        <th>{{ __('messages.table.total_lots') }}</th>
                        <th>{{ __('messages.table.map') }}</th>
                        <th>{{ __('messages.table.created') }}</th>
                        <th>{{ __('messages.table.actions') }}</th>
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
                            <div class="image-container" style="position: relative; height: 100px;">
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
                                {{ __('messages.buttons.configure') }}
                            </a>
                            <a href="{{ url('iframe/' . $lot->id) }}" 
                               class="btn btn-sm btn-secondary" style="background-color: #3FB549 !important" target="_blank">
                                {{ __('messages.buttons.iframe') }}

                            </a>
                            <a href="{{ route('desarrollos.edit', $lot->id) }}" 
                               class="btn btn-sm btn-warning">
                                {{ __('messages.buttons.edit') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!--end::Card-->

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Lazy load de imágenes
    let observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                observer.unobserve(img);
            }
        });
    }, { root: null, threshold: 0.1 });

    // DataTable
    const table = $("#lots_table").DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50],
        order: [],
        language: { url: '//cdn.datatables.net/plug-ins/2.3.2/i18n/es-MX.json' },
        columnDefs: [
            {
                targets: 0,
                visible: false,
                searchable: false
            }
        ],
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

    // Botón actualizar
    $("#btnActualizar").on("click", function() {
        window.location.reload();
    });
});
</script>
@endpush
