@extends('layouts.cic')

@section('title', 'Naboo')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

    <!-- HEADER -->
    <nav class="navbar navbar-dark bg-dark px-4 py-3">
        <span class="navbar-brand mb-0 h1">Centro Internacional de Congresos de Yucatán</span>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">

            <!-- ============================= -->
            <!-- IZQUIERDA: PLANO -->
            <!-- ============================= -->
            <div class="col-lg-7">
                <div class="floor-plan">
                    <div style="position: relative; display: inline-block;">

                        {{-- PNG base --}}
                        @if (!empty($lot->png_image))
                            <img src="{{ asset('/' . $lot->png_image) }}" 
                                 alt="Plano PNG" 
                                 style="width:100%; height:auto;">
                        @endif

                        {{-- SVG encima --}}
                        @if (!empty($lot->svg_image))
                            @php
                                $svgPath = public_path($lot->svg_image);
                            @endphp

                            @if (file_exists($svgPath))
                                <div style="position: absolute; top:0; left:0; width:100%;">
                                    {!! file_get_contents($svgPath) !!}
                                </div>
                            @else
                                <div class="alert alert-warning mt-2">
                                    ⚠️ El archivo SVG no se encuentra. Por favor cárguelo nuevamente.
                                </div>
                            @endif
                        @endif

                    </div>
                </div>
            </div>

            <!-- ============================= -->
            <!-- DERECHA -->
            <!-- ============================= -->
            <div class="col-lg-5 px-4">

                <!-- Tabs -->
                <div class="tabs d-flex gap-3 mb-4">
                    <button class="btn btn-outline-dark rounded-pill px-4">Planta Alta</button>
                    <button class="btn btn-dark rounded-pill px-4 active">Planta Baja</button>
                    <button class="btn btn-outline-dark rounded-pill px-4">Mezzanine</button>
                </div>

                <hr>

                <!-- Información general -->
                <p class="mb-1">
                    <strong>Capacidad de carga en puntos de colgante fijos:</strong>
                    <span id="punto-colgado">{{ $lot->hanging_point ?? '---' }}</span>
                </p>

                <p>
                    <strong>Resistencia de piso:</strong>
                    <span id="resistencia-piso">{{ $lot->floor_resistance ?? '---' }}</span>
                </p>

                <h6 class="mt-4"><strong>Dimensiones</strong></h6>
                <p class="mb-1">Área: <span id="area">{{ $lot->area ?? '---' }}</span> m²</p>
                <p class="mb-1">Frente: <span id="frente">{{ $lot->front ?? '---' }}</span> m</p>
                <p class="mb-1">Fondo: <span id="fondo">{{ $lot->depth ?? '---' }}</span> m</p>
                <p>Altura: <span id="altura">{{ $lot->height ?? '---' }}</span> m</p>

                <h6><strong>Capacidades</strong></h6>
                <p class="mb-1">Auditorio: <span id="auditorio">{{ $lot->auditorium ?? '---' }}</span></p>
                <p class="mb-1">Banquete: <span id="banquete">{{ $lot->banquet ?? '---' }}</span></p>
                <p class="mb-1">Coctel: <span id="coctel">{{ $lot->cocktail ?? '---' }}</span></p>
                <p class="mb-1">Escuela: <span id="escuela">{{ $lot->school ?? '---' }}</span></p>
                <p class="mb-1">Herradura: <span id="herradura">{{ $lot->horseshoe ?? '---' }}</span></p>
                <p class="mb-1">Mesa Rusa: <span id="mesa-rusa">{{ $lot->russian_table ?? '---' }}</span></p>

                @if (!empty($lot->tour_link))
                    <div class="mt-3">
                        <a href="{{ $lot->tour_link }}" 
                           target="_blank" 
                           class="btn btn-outline-dark rounded-pill px-4">
                            Ver Recorrido Virtual
                        </a>
                    </div>
                @endif

                <p class="mt-3">
                    ¿Necesitas más espacio?<br>
                    Selecciona otro salón para ampliar tu espacio.
                </p>

                <!-- Botones -->
                <div class="d-flex gap-3 mt-2">
                    <button class="btn btn-light border rounded-pill px-4">Oficina 12</button>
                    <button class="btn btn-light border rounded-pill px-4">Oficina 13</button>
                </div>

            </div>

        </div>
    </div>


    <!-- ============================= -->
    <!-- BARRA DE MÉTRICAS -->
    <!-- ============================= -->
    <div class="metrics-bar mt-5">
        <div class="container text-center">
            <div class="row text-center">
                <div class="col-md-3">
                    <p>Nuestros ejecutivos especializados te ayudarán a concretar tu evento.</p>
                </div>

                <!-- Área -->
                <div class="col-md-2">
                    <p id="metric-area" class="metric-number">---</p>
                    <p>Área total para evento</p>
                </div>

                <!-- Auditorio -->
                <div class="col-md-1">
                    <p id="metric-auditorium" class="metric-number">---</p>
                    <p>Auditorio</p>
                </div>

                <!-- Banquete -->
                <div class="col-md-1">
                    <p id="metric-banquet" class="metric-number">---</p>
                    <p>Banquete</p>
                </div>

                <!-- Escuela -->
                <div class="col-md-1">
                    <p id="metric-school" class="metric-number">---</p>
                    <p>Escuela</p>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-dark rounded-pill mt-4 px-5 py-2">
                        Quiero organizar mi evento
                    </button>
                </div>
            </div>
        </div>
    </div>


    @include("iframe.modals.$templateModal")
    @include('iframe.modalLead')

@endsection

@push('scripts')
<script>
    let selector = @json($lot->modal_selector ?? 'svg g *');

    window.Laravel = {
        csrfToken: "{{ csrf_token() }}",
        routes: {
            lotsFetch: "{{ route('lots.fetch') }}",
            lotesStore: "{{ route('lotes.store') }}"
        }
    };

    window.currentLoteFinanciamientos = @json($financiamientos);
    window.preloadedLots = @json($lots);
    window.currentLot = @json($lot);
    window.projects = @json($projects);
    window.dbLotes = @json($dbLotes);

    window.idDesarrollo = {{ $lot->id }};
    let redireccion = true;
</script>

<script src="/assets/js/iframePublico/Mainiframe.js"></script>
<script src="/assets/js/iframePublico/ModalIframe.js"></script>
<script src="/assets/js/iframePublico/CotizacionIframe.js"></script>
@endpush
