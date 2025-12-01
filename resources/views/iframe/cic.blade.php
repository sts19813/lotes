@extends('layouts.cic')

@section('title', 'Naboo')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://db.onlinewebfonts.com/c/88f10bf18a36407ef36bf30bc25a3618?family=SuisseIntl-Regular"
    rel="stylesheet">
<link rel="stylesheet" href="/assets/css/styleCic.css">
@section('content')

    <!-- HEADER -->
    <nav class="navbar navbar-dark bg-dark px-4 py-3 d-flex justify-content-between align-items-center">

        {{-- LOGO IZQUIERDA --}}
        <img src="/Imagotipo Horizontal.svg" alt="" class="logo-navbar">

        {{-- BOTÓN A LA DERECHA --}}
        <a href="https://cicyucatan.com/" class="btn text-white d-flex align-items-center gap-2">
            <!-- Texto solo en pantallas grandes -->
            <span class="d-none d-md-inline">Regresar</span>

            <!-- Ícono solo en pantallas pequeñas -->
            <span class="d-inline d-md-none" style="font-size: 24px;">‹</span>
        </a>

    </nav>


    <div class="container-fluid">
        <div class="row">

            <!-- ============================= -->
            <!-- IZQUIERDA: PLANO -->
            <!-- ============================= -->
            <div class="col-lg-7 panel-left">
                <div class="floor-plan">
                    <div style="position: relative; display: inline-block;">

                        {{-- PNG base --}}
                        @if (!empty($lot->png_image))
                            <img src="{{ asset('/' . $lot->png_image) }}" alt="Plano PNG" style="width:100%; height:auto;">
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
            <div class="col-lg-5 right-panel">

                <!-- Tabs -->
                <div class="tabs d-flex gap-5 mb-4">
                    <img src="/Modo_de_aislamiento.svg" alt="">

                    <a href="/cic/1"
                        class="btn btn-outline-dark rounded-pill px-4 {{ request()->is('cic/1') ? 'active' : '' }}">
                        Planta Alta
                    </a>

                    <a href="/cic/2"
                        class="btn btn-outline-dark rounded-pill px-4 {{ request()->is('cic/2') ? 'active' : '' }}">
                        Planta Baja
                    </a>

                    <a href="/cic/3"
                        class="btn btn-outline-dark rounded-pill px-4 {{ request()->is('cic/3') ? 'active' : '' }}">
                        Mezzanine
                    </a>
                </div>

                <hr>

                <!-- SOLO EN MÓVIL -->
                <div class="d-md-none mt-3">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-custom-mobile">Personaliza tu espacio.</span>

                        <div class="custom-select-wrapper">
                            <select id="select-lot-merge" class="custom-select-mobile">
                                @foreach ($lots as $item)
                                    <option value="{{ $item['id'] ?? $item->id }}"
                                        data-area="{{ $item['area'] ?? $item->area }}"
                                        data-front="{{ $item['front'] ?? $item->front }}"
                                        data-depth="{{ $item['depth'] ?? $item->depth }}"
                                        data-auditorio="{{ $item['auditorium'] ?? $item->auditorium }}"
                                        data-banquete="{{ $item['banquet'] ?? $item->banquet }}"
                                        data-coctel="{{ $item['cocktail'] ?? $item->cocktail }}"
                                        data-escuela="{{ $item['school'] ?? $item->school }}"
                                        data-herradura="{{ $item['horseshoe'] ?? $item->horseshoe }}"
                                        data-mesarusa="{{ $item['russian_table'] ?? $item->russian_table }}">
                                        {{ $item['name'] ?? $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>


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
                        <a href="{{ $lot->tour_link }}" target="_blank" class="btn btn-outline-dark rounded-pill px-4">
                            Ver Recorrido Virtual
                        </a>
                    </div>
                @endif

                <p class="mt-3">
                    ¿Necesitas más espacio?<br>
                    Selecciona otro salón para ampliar tu espacio.
                </p>

                <!-- Botones -->
                <div class="d-flex flex-wrap gap-3 mt-2">
                    @foreach ($lots as $item)
                        <button class="btn btn-light border rounded-pill px-4 btn-lot-merge"
                            data-id="{{ $item['id'] ?? $item->id }}" data-area="{{ $item['area'] ?? $item->area }}"
                            data-front="{{ $item['front'] ?? $item->front }}" data-depth="{{ $item['depth'] ?? $item->depth }}"
                            data-auditorio="{{ $item['auditorium'] ?? $item->auditorium }}"
                            data-banquete="{{ $item['banquet'] ?? $item->banquet }}"
                            data-coctel="{{ $item['cocktail'] ?? $item->cocktail }}"
                            data-escuela="{{ $item['school'] ?? $item->school }}"
                            data-herradura="{{ $item['horseshoe'] ?? $item->horseshoe }}"
                            data-mesarusa="{{ $item['russian_table'] ?? $item->russian_table }}">
                            {{ $item['name'] ?? $item->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    <!-- ============================= -->
    <!-- BARRA DE MÉTRICAS -->
    <!-- ============================= -->
    <div class="metrics-bar mt-5 fixed-metrics d-flex align-items-center">
        <div class="container text-center">
            <div class="row text-center align-items-center">

                <!-- ======================== -->
                <!-- TÍTULO (siempre igual)   -->
                <!-- ======================== -->
                <div class="col-12 col-md-3 mb-3 mb-md-0">
                    <span>Nuestros ejecutivos especializados te ayudarán a concretar tu evento.</span>
                </div>

                <!-- ===================================================== -->
                <!-- MÓVIL: 4 valores en una fila (visible solo en móvil) -->
                <!-- ===================================================== -->
                <div class="col-12 d-flex d-md-none justify-content-between mb-3">

                    <div class="text-center flex-fill">
                        <p id="metric-area-mobile" class="metric-number">---</p>
                        <p class="small">Área</p>
                    </div>

                    <div class="text-center flex-fill">
                        <p id="metric-auditorium-mobile" class="metric-number">---</p>
                        <p class="small">Auditorio</p>
                    </div>

                    <div class="text-center flex-fill">
                        <p id="metric-banquet-mobile" class="metric-number">---</p>
                        <p class="small">Banquete</p>
                    </div>

                    <div class="text-center flex-fill">
                        <p id="metric-school-mobile" class="metric-number">---</p>
                        <p class="small">Escuela</p>
                    </div>

                </div>

                <!-- ==================================================================== -->
                <!-- ESCRITORIO: 4 valores en columnas como antes (visible solo en ≥md) -->
                <!-- ==================================================================== -->
                <div class="col-md-2 d-none d-md-block">
                    <p id="metric-area" class="metric-number">---</p>
                    <p>Área total para evento</p>
                </div>

                <div class="col-md-1 d-none d-md-block">
                    <p id="metric-auditorium" class="metric-number">---</p>
                    <p>Auditorio</p>
                </div>

                <div class="col-md-1 d-none d-md-block">
                    <p id="metric-banquet" class="metric-number">---</p>
                    <p>Banquete</p>
                </div>

                <div class="col-md-1 d-none d-md-block">
                    <p id="metric-school" class="metric-number">---</p>
                    <p>Escuela</p>
                </div>

                <!-- BOTÓN -->
                <div class="col-12 col-md-3 text-center mt-3 mt-md-0">
                    <button id="btnSolicitarEvento" class="btn btn-dark rounded-pill px-5 py-2">
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