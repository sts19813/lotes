@extends('layouts.cic')

@section('title', 'Centro Internacional de Congresos de Yucatán')
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
            <div class="col-lg-8 panel-left">
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
                <div class="salon-info-fixed d-none d-lg-block">
                    <strong>Clasificación técnica de los salones</strong>
                    <ul class="mt-2">
                        <li>Medio Salón Superior (B)</li>
                        <li>Medio Salón Inferior (A)</li>
                        <li>Salón Completo (A + B)</li>
                        <li>Salón Completo + Pasillo (área adicional de circulación)</li>
                    </ul>
                </div>
            </div>

            <!-- ============================= -->
            <!-- DERECHA -->
            <!-- ============================= -->
            <div class="col-lg-4 right-panel" style="padding-bottom: 180px;">

                <!-- Tabs -->
                <div class="tabs d-flex gap-5 mb-4">
                    <img src="/Modo_de_aislamiento.svg" alt="">

                    <a href="/cic/1"
                        class="text-normal-standart btn btn-outline-dark rounded-pill px-4 {{ request()->is('cic/1') ? 'actives' : '' }}">
                        Planta Alta
                    </a>

                    <a href="/cic/2"
                        class="text-normal-standart btn btn-outline-dark rounded-pill px-4 {{ request()->is('cic/2') ? 'actives' : '' }}">
                        Planta Baja
                    </a>

                    <a href="/cic/3"
                        class="text-normal-standart btn btn-outline-dark rounded-pill px-4 {{ request()->is('cic/3') ? 'actives' : '' }}">
                        Mezzanine
                    </a>
                </div>

                <hr>

                <!-- SOLO EN MÓVIL -->
                <div class="d-md-none mt-3">
                    <div class="mb-2">
                        <!-- Título 100% ancho -->
                        <p class="text-normal-standart d-md-none mb-3" id="instrucciones-mobile">
                            <strong class="text-custom-desktop">Selecciona un salón y consulta todas las configuraciones
                                disponibles.</strong><br>
                            Visualiza de inmediato el área total, capacidades y configuraciones disponibles para cada
                            salón.<br><br>
                            Elige un salón desde el mapa o la lista para ver sus características y combinaciones posibles.
                        </p>

                        <!-- Select 100% ancho con borde -->
                        <div class="custom-select-wrapper-mobile w-100">
                            <select id="select-lot-merge" class="custom-select-mobile w-100">
                                @foreach ($lots as $item)
                                    <option value="{{ $item['id'] ?? $item->id }}" data-id="{{ $item['id'] ?? $item->id }}"
                                        data-area="{{ $item['area'] ?? $item->area }}"
                                        data-front="{{ $item['front'] ?? $item->front }}"
                                        data-depth="{{ $item['depth'] ?? $item->depth }}"
                                        data-auditorio="{{ $item['auditorium'] ?? $item->auditorium }}"
                                        data-banquete="{{ $item['banquet'] ?? $item->banquet }}"
                                        data-coctel="{{ $item['cocktail'] ?? $item->cocktail }}"
                                        data-escuela="{{ $item['school'] ?? $item->school }}"
                                        data-herradura="{{ $item['horseshoe'] ?? $item->horseshoe }}"
                                        data-chepina="{{ $item['chepina'] ?? $item->chepina }}"
                                        data-mesarusa="{{ $item['russian_table'] ?? $item->russian_table }}">
                                        {{ $item['name'] ?? $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="text-custom-desktop d-none container-info"> <strong>Personaliza tu espacio.</strong>
                    <br>
                    <p class="text-normal-standart mb-4 ">
                        <strong>Los salones están diseñados con un sistema modular que permite:</strong>
                    </p>
                    <ul class="text-normal-standart mb-4">
                        <li>Integrar pasillos de acceso para mejorar la circulación y el servicio.</li>
                        <li>Unir medios salones para formar un salón completo.</li>
                        <li>Sumar salones completos para incrementar la capacidad total del espacio.</li>
                    </ul>
                    <strong> <span class="salon-seleccionado"></span></strong>
                </div>
                <br>

                <p class="text-normal-standart d-none d-lg-block mb-4" id="instrucciones-desktop">
                    <strong class="text-custom-desktop"> Selecciona un salón y consulta todas las configuraciones disponibles.</strong><br>
                    Nuestra herramienta te permite visualizar de forma inmediata el área total, la capacidad máxima
                    y las posibles configuraciones de cada salón, considerando sus dimensiones, divisiones y
                    combinaciones.<br><br>
                    Para comenzar, selecciona un salón desde el mapa o la lista. La herramienta mostrará automáticamente
                    las configuraciones permitidas según la combinación y disposición del espacio.
                </p>

                <div class="container-info d-none">

                    <!-- Información general -->
                    <p class="mb-1 text-normal-standart ">
                        <strong>Capacidad de carga en puntos de colgante fijos:</strong>
                        <span id="punto-colgado">{{ $lot->hanging_point ?? '0' }}</span>
                    </p>

                    <p class="text-normal-standart ">
                        <strong>Resistencia de piso:</strong>
                        <span id="resistencia-piso">{{ $lot->floor_resistance ?? '0' }}</span>
                    </p>

                    <h6 class="mt-4 text-normal-standart "><strong>Dimensiones</strong></h6>
                    <p class="mb-1 text-normal-standart ">Área: <span id="area">{{ $lot->area ?? '0' }}</span> m²</p>
                    <p class="mb-1 text-normal-standart ">Frente: <span id="frente">{{ $lot->front ?? '0' }}</span> m</p>
                    <p class="mb-1 text-normal-standart ">Fondo: <span id="fondo">{{ $lot->depth ?? '0' }}</span> m</p>
                    <p class="text-normal-standart ">Altura: <span id="altura">{{ $lot->height ?? '0' }}</span> m</p>
                    <br>
                    <h6 class="text-normal-standart "><strong>Capacidades</strong></h6>
                    <p class="mb-1 text-normal-standart ">Auditorio: <span
                            id="auditorio">{{ $lot->auditorium ?? '0' }}</span>
                    </p>
                    <p class="mb-1 text-normal-standart ">Banquete: <span id="banquete">{{ $lot->banquet ?? '0' }}</span>
                    </p>
                    <p class="mb-1 text-normal-standart ">Coctel: <span id="coctel">{{ $lot->cocktail ?? '0' }}</span></p>
                    <p class="mb-1 text-normal-standart ">Escuela: <span id="escuela">{{ $lot->school ?? '0' }}</span></p>
                    <p class="mb-1 text-normal-standart ">Herradura: <span
                            id="herradura">{{ $lot->horseshoe ?? '0' }}</span>
                    </p>
                    <p class="mb-1 text-normal-standart ">Mesa Rusa: <span
                            id="mesa-rusa">{{ $lot->russian_table ?? '0' }}</span></p>

                    @if (!empty($lot->tour_link))
                        <div class="mt-3">
                            <a href="{{ $lot->tour_link }}" target="_blank" class="btn btn-outline-dark rounded-pill px-4">
                                Ver Recorrido Virtual
                            </a>
                        </div>
                    @endif

                    <p class="mt-3 d-none d-lg-block text-normal-standart">
                        <strong>¿Necesitas más espacio?</strong><br>
                        Selecciona otro salón para ampliar tu espacio.
                    </p>

                </div>



                <div class="btn-grid-2 mt-2 text-normal-standart">
                    @foreach ($lots as $item)
                        <button class="btn btn-light border rounded-pill px-4 btn-lot-merge"
                            data-id="{{ $item['id'] ?? $item->id }}" data-area="{{ $item['area'] ?? $item->area }}"
                            data-front="{{ $item['front'] ?? $item->front }}" data-depth="{{ $item['depth'] ?? $item->depth }}"
                            data-auditorio="{{ $item['auditorium'] ?? $item->auditorium }}"
                            data-banquete="{{ $item['banquet'] ?? $item->banquet }}"
                            data-coctel="{{ $item['cocktail'] ?? $item->cocktail }}"
                            data-escuela="{{ $item['school'] ?? $item->school }}"
                            data-herradura="{{ $item['horseshoe'] ?? $item->horseshoe }}"
                            data-chepina="{{ $item['chepina'] ?? $item->chepina }}"
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
                <div class="col-12 col-md-3 mb-3 mb-md-0 text-footer text-normal-standart">
                    <span>Nuestros ejecutivos especializados te ayudarán a concretar tu evento.</span>
                </div>

                <!-- ===================================================== -->
                <!-- MÓVIL: 4 valores en una fila (visible solo en móvil) -->
                <!-- ===================================================== -->
                <div class="col-12 d-flex d-md-none justify-content-between mb-3">

                    <div class="text-center flex-fill">
                        <p id="metric-area-mobile" class="metric-number">0</p>
                        <p class="small">Área</p>
                    </div>

                    <div class="text-center flex-fill">
                        <p id="metric-auditorium-mobile" class="metric-number">0</p>
                        <p class="small">Auditorio</p>
                    </div>

                    <div class="text-center flex-fill">
                        <p id="metric-banquet-mobile" class="metric-number">0</p>
                        <p class="small">Banquete</p>
                    </div>

                    <div class="text-center flex-fill">
                        <p id="metric-school-mobile" class="metric-number">0</p>
                        <p class="small">Escuela</p>
                    </div>
                </div>

                <!-- ==================================================================== -->
                <!-- ESCRITORIO: 4 valores en columnas como antes (visible solo en ≥md) -->
                <!-- ==================================================================== -->
                <div class="col-md-2 d-none d-md-block">
                    <p id="metric-area" class="metric-number">0 m²</p>
                    <p>Área total para evento</p>
                </div>

                <div class="col-md-1 d-none d-md-block">
                    <p id="metric-auditorium" class="metric-number">0</p>
                    <p>Auditorio</p>
                </div>

                <div class="col-md-1 d-none d-md-block">
                    <p id="metric-banquet" class="metric-number">0</p>
                    <p>Banquete</p>
                </div>

                <div class="col-md-1 d-none d-md-block">
                    <p id="metric-school" class="metric-number">0</p>
                    <p>Escuela</p>
                </div>

                <!-- BOTÓN -->
                <div class="col-12 col-md-3 text-center mt-3 mt-md-0">
                    <button id="btnSolicitarEvento" class="btn btn-dark rounded-pill px-5 text-normal-standart">
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