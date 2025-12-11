@extends('layouts.cic')

@section('title', 'Centro Internacional de Congresos de Yucatán')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://db.onlinewebfonts.com/c/88f10bf18a36407ef36bf30bc25a3618?family=SuisseIntl-Regular"
    rel="stylesheet">
<link rel="stylesheet" href="/assets/css/styleCic.css">
<style>
    /* Asegura que el <li> es el contenedor del submenu */
    /* El li es el contenedor del submenu */
    .nav-item.has-submenu {
        position: relative !important;
        display: inline-block;
    }

    /* El enlace no debe crear espacio debajo */
    .nav-item.has-submenu>a {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    /* Submenú alineado automáticamente debajo del enlace */
    .nav-item.has-submenu .submenu {
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 220px;
        display: none;
        z-index: 2050;
        padding: 26px 0;
        margin-top: 0;
        /* IMPORTANTE: no separar del enlace */
    }

    /* Mostrar al hacer hover en el <li> o en el submenu */
    .nav-item.has-submenu:hover .submenu,
    .nav-item.has-submenu .submenu:hover {
        display: block;
    }

    .navbar-nav.flex-row>.nav-item {
        margin-right: 22px;
    }

    .navbar-nav.flex-row>.nav-item:last-child {
        margin-right: 0;
    }

    .nav-item,
    .btn-evento {
        font-family: 'Acumin Pro', sans-serif !important;
        font-size: 14px !important;
        letter-spacing: -0.5px;
    }

    .btn-evento {
        background-color: white !important;
        color: #000 !important;
        padding: 6px 20px !important;
        font-weight: 400 !important;
    }

    .language-selector {
        margin-right: 20px !important;
    }
</style>

@section('content')

    <!-- HEADER -->
    <nav class="navbar  d-flex justify-content-between align-items-center">

        {{-- LOGO IZQUIERDA --}}
        <a href="/" class="navbar-brand">
            <img src="/Imagotipo Horizontal.svg" alt="CIC Logo" class="logo-navbar">
        </a>

        {{-- MENÚ DERECHA --}}
        <div class="d-flex align-items-center">

            {{-- LINKS PRINCIPALES --}}
            <ul class="navbar-nav flex-row align-items-center gap-5 d-none d-md-flex">

                <li class="nav-item">
                    <a class="nav-link text-white" href="https://cicyucatan.com/nosotros">Sobre el CIC</a>
                </li>

                <!-- Dropdown Recinto -->
                <li class="nav-item position-static recinto-parent has-submenu">
                    <a class="nav-link text-white" href="#">Recinto</a>

                    <div class="submenu bg-dark rounded shadow">
                        <a class="d-block px-3 py-2 text-white text-decoration-none"
                            href="https://cicyucatan.com/espacios-y-auditorios">
                            Espacios y salones
                        </a>
                        <a class="d-block px-3 py-2 text-white text-decoration-none" href="/venue/planos-especificaciones">
                            Planos y especificaciones
                        </a>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="https://cicyucatan.com/yucatan">Yucatán</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="https://cicyucatan.com/contacto">Contacto</a>
                </li>

                <li class="nav-item">
                    <a href="/contacto" class="btn btn-evento rounded-pill fw-semibold">
                        Organizar Evento
                    </a>
                </li>

                <li class="nav-item language-selector">
                    <select class="form-select form-select-sm bg-transparent text-white border-light" style="width: auto; border: black; font-size: 14px;">
                        <option value="es" selected>Español</option>
                        <option value="en">English</option>
                    </select>
                </li>

            </ul>

            {{-- MENÚ MÓVIL --}}
            <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

        </div>

        <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="mobileMenu">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Menú</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body">
                <ul class="navbar-nav gap-3">

                    <li class="nav-item">
                        <a class="nav-link text-white" href="/nosotros">About the CIC</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white" href="/venue">Venue</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white" href="/yucatan">Yucatán</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white" href="/contacto">Contact</a>
                    </li>

                </ul>

                <a href="/contacto" class="btn btn-light rounded-pill w-100 mt-4">Organize Event</a>

                <hr class="border-gray-500">

                <select class="form-select bg-transparent text-white border-light">
                    <option value="es" selected>Español</option>
                    <option value="en">English</option>
                </select>
            </div>
        </div>

    </nav>

    <div class="container-fluid">
        <div class="row">

            <!-- IZQUIERDA: PLANO -->
            <div class="col-lg-8 panel-left">
                <div class="floor-plan">
                    <div style="position: relative; display: inline-block;">

                        {{-- PNG base --}}
                        @if (!empty($lot->png_image))
                            <img src="{{ asset('/' . $lot->png_image) }}" alt="Plano PNG" style="width:100%; height:auto;">
                        @endif

                        {{-- SVG encima --}}
                        @if (!empty($lot->svg_image))
                            @php $svgPath = public_path($lot->svg_image); @endphp

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

                    <button class="btn btn-white-bottom" data-bs-toggle="modal" data-bs-target="#modalClasificacionTecnica">
                        Ver clasificación técnica
                    </button>

                    <button class="btn btn-white-bottom" data-bs-toggle="modal" data-bs-target="#modalFAQ">
                        Preguntas frecuentes
                    </button>

                    <button class="btn btn-white-bottom" data-bs-toggle="modal" data-bs-target="#modalInstruccionesbtn">
                        Ver instrucciones
                    </button>
                </div>
            </div>

            <!-- DERECHA -->
            <div class="col-lg-4 right-panel" style="padding-bottom: 180px !important;">

                <!-- Tabs -->
                <div class="tabs d-flex gap-5 mb-4">
                    <a href="/" style="margin-top: 10px;">
                        <img src="/Modo_de_aislamiento.svg" alt="">
                    </a>

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
                        Oficinas
                    </a>
                </div>

                <hr>

                <div class="text-custom-desktop d-none container-info">
                    <strong style="font-size: 18px">Personaliza tu espacio.</strong><br>

                    <p class="text-normal-standart mb-4">
                        <strong>Los salones están diseñados con un sistema modular que permite:</strong>
                    </p>

                    <ul class="text-normal-standart mb-4">
                        <li>Integrar pasillos de acceso para mejorar la circulación y el servicio.</li>
                        <li>Unir medios salones para formar un salón completo.</li>
                        <li>Sumar salones completos para incrementar la capacidad total del espacio.</li>
                    </ul>

                    <strong><span class="salon-seleccionado d-none d-lg-block"></span></strong>
                </div>

                <!-- Móvil -->
                <div class="d-md-none mt-3">
                    <div class="mb-2">
                        <div class="custom-select-wrapper-mobile w-100 d-none">
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

                <br>

                <p class="text-normal-standart mb-4" id="instrucciones-desktop">
                    <strong class="text-custom-desktop" style="font-size: 18px !important;">
                        Selecciona un salón y consulta todas las configuraciones disponibles.
                    </strong><br>
                </p>

                <!-- Información general -->
                <div class="container-info d-none">
                    <p class="mb-1 text-normal-standart">
                        <strong>Capacidad de carga en puntos de colgante fijos:</strong>
                        <span id="punto-colgado">{{ $lot->hanging_point ?? '0' }}</span>
                    </p>

                    <p class="text-normal-standart">
                        <strong>Resistencia de piso:</strong>
                        <span id="resistencia-piso">{{ $lot->floor_resistance ?? '0' }}</span>
                    </p>

                    <h6 class="mt-4 text-normal-standart"><strong>Dimensiones</strong></h6>

                    <p class="mb-1 text-normal-standart">Área: <span id="area">{{ $lot->area ?? '0' }}</span> m²</p>
                    <p class="mb-1 text-normal-standart">Frente: <span id="frente">{{ $lot->front ?? '0' }}</span> m</p>
                    <p class="mb-1 text-normal-standart">Fondo: <span id="fondo">{{ $lot->depth ?? '0' }}</span> m</p>
                    <p class="text-normal-standart">Altura: <span id="altura">{{ $lot->height ?? '0' }}</span> m</p>

                    <br>

                    <h6 class="text-normal-standart capacidades-text"><strong>Capacidades</strong></h6>

                    <p class="mb-1 text-normal-standart">Auditorio: <span
                            id="auditorio">{{ $lot->auditorium ?? '0' }}</span></p>
                    <p class="mb-1 text-normal-standart">Banquete: <span id="banquete">{{ $lot->banquet ?? '0' }}</span></p>
                    <p class="mb-1 text-normal-standart">Coctel: <span id="coctel">{{ $lot->cocktail ?? '0' }}</span></p>
                    <p class="mb-1 text-normal-standart">Escuela: <span id="escuela">{{ $lot->school ?? '0' }}</span></p>
                    <p class="mb-1 text-normal-standart">Herradura: <span id="herradura">{{ $lot->horseshoe ?? '0' }}</span>
                    </p>
                    <p class="mb-1 text-normal-standart">Mesa Rusa: <span
                            id="mesa-rusa">{{ $lot->russian_table ?? '0' }}</span></p>

                    @if (!empty($lot->tour_link))
                        <div class="mt-3">
                            <a href="{{ $lot->tour_link }}" target="_blank" class="btn btn-outline-dark rounded-pill px-4">
                                Ver Recorrido Virtual
                            </a>
                        </div>
                    @endif

                    <p class="mt-3 text-normal-standart">
                        <strong>¿Necesitas más espacio?</strong><br>
                        Selecciona otro salón para ampliar tu espacio.
                    </p>
                </div>

                <!-- Botones -->
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

    <!-- MÉTRICAS -->
    <div class="metrics-bar mt-5 fixed-metrics d-flex align-items-center">
        <div class="container text-center">
            <div class="row text-center align-items-center">

                <div class="col-12 col-md-3 mb-3 mb-md-0 text-footer text-normal-standart d-none d-lg-block">
                    <span>Nuestros ejecutivos especializados te ayudarán a concretar tu evento.</span>
                </div>

                <!-- Móvil -->
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

                <!-- Escritorio -->
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

    <!-- Modal instrucciones -->
    <!-- Modal instrucciones -->
    <div class="modal fade" id="modalInstrucciones" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-instrucciones">

                <!-- Botón de cierre (X blanca) -->
                <button type="button" class="btn-close btn-close-white custom-close" data-bs-dismiss="modal"></button>

                <h4 class="text-center fw-bold mb-3 text-white">Instrucciones:</h4>

                <p class="text-center text-white text-normal-standart">
                    Esta herramienta te permitirá visualizar las áreas, salones<br>
                    y posibles configuraciones.
                </p>

                <p class="text-center text-white text-normal-standart mt-3">
                    Paso 1. Elige la planta o área de oficina en el configurador.<br>
                    Paso 2. Elige el salón u oficina de preferencia en el mapa.<br>
                    Paso 3. Selecciona cualquiera de las combinaciones<br>
                    del configurador.
                </p>

                <div class="text-center mt-4">
                    <button id="btnEntendido" class="btn btn-light rounded-pill px-4 fw-semibold text-dark">
                        Entendido
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Clasificación Técnica -->
    <div class="modal fade" id="modalClasificacionTecnica" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-instrucciones">

                <!-- Botón X blanca grande -->
                <button type="button" class="btn-close btn-close-white custom-close" data-bs-dismiss="modal"></button>

                <h4 class="text-center fw-bold mb-3 text-white">Clasificación técnica<br>de los salones:</h4>

                <p class="text-center text-white text-normal-standart">

                    Medio Salón Superior (B)<br>
                    Medio Salón Inferior (A)<br>
                    Salón Completo (A + B)<br>
                    Salón Completo + Pasillo (Salón Completo<br>
                    + área adicional de circulación)

                </p>

                <div class="text-center mt-4">
                    <button class="btn btn-light rounded-pill px-4 fw-semibold text-dark" data-bs-dismiss="modal">
                        Entendido
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalInstruccionesbtn" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-instrucciones">

                <!-- Botón X blanca grande -->
                <button type="button" class="btn-close btn-close-white custom-close" data-bs-dismiss="modal"></button>


                <h4 class="text-center fw-bold mb-3 text-white">Pasos para personalizar tu espacio.</h4>

                <p class="text-center text-white text-normal-standart mt-3">
                    Paso 1. Elige la planta o área de oficina en el configurador.<br>
                    Paso 2. Elige el salón u oficina de preferencia en el mapa.<br>
                    Paso 3. Selecciona cualquiera de las combinaciones<br>
                    del configurador.
                </p>

                <div class="text-center mt-4">
                    <button class="btn btn-light rounded-pill px-4 fw-semibold text-dark" data-bs-dismiss="modal">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Preguntas Frecuentes -->
    <div class="modal fade" id="modalFAQ" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-instrucciones">

                <!-- X blanca grande -->
                <button type="button" class="btn-close btn-close-white custom-close" data-bs-dismiss="modal"></button>

                <h4 class="text-center fw-bold mb-4 text-white">Preguntas Frecuentes</h4>

                <!-- Acordeón FAQ -->
                <div class="accordion" id="faqAccordion">

                    <!-- FAQ 1 -->
                    <div class="accordion-item faq-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed faq-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq1">
                                ¿Qué es el configurador de salones?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body faq-body">
                                Es una herramienta digital que te permite previsualizar las diferentes
                                combinaciones de espacios para tu evento dentro del Centro Internacional
                                de Congresos de Yucatán, de forma sencilla e interactiva.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="accordion-item faq-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed faq-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq2">
                                ¿Qué es un salón para un evento?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body faq-body">
                                Es un espacio diseñado para reuniones, congresos, exposiciones, conferencias
                                y eventos sociales, adaptable a distintos montajes y capacidades según las
                                necesidades de cada evento.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="accordion-item faq-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed faq-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq3">
                                ¿Cuál es la función de las oficinas dentro del Centro Internacional de Congresos?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body faq-body">
                                Son espacios privados de apoyo al evento que se pueden rentar adicionalmente.
                                Se utilizan como área de trabajo del comité organizador, sala de juntas,
                                oficina de producción, sala de prensa o espacio de coordinación para staff.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="accordion-item faq-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed faq-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq4">
                                ¿Qué es una combinación de salones?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body faq-body">
                                Es la integración de uno o más salones en un solo espacio funcional,
                                ajustando capacidad y distribución según asistentes, montaje
                                y necesidades del evento.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="accordion-item faq-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed faq-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq5">
                                ¿Cómo puedo cotizar el salón?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body faq-body">
                                Tras elegir la combinación deseada y completar el formulario,
                                el equipo de atención al cliente te contactará para enviarte
                                una cotización personalizada.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 6 -->
                    <div class="accordion-item faq-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed faq-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq6">
                                ¿Cuentan con estacionamiento?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body faq-body">
                                Sí, el Centro Internacional de Congresos de Yucatán cuenta
                                con estacionamiento subterráneo para la comodidad de los asistentes.
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>





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