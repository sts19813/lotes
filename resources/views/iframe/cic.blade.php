@extends('layouts.cic')

@section('title', 'Naboo')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')


    <!-- HEADER (simulado) -->
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
                        {{-- Imagen base PNG --}}
                        @if ($lot->png_image)
                            <img src="{{ asset('/' . $lot->png_image) }}" alt="PNG" style="width:100%; height:auto;">
                        @endif

                        {{-- SVG encima --}}
                        @if ($lot->svg_image)
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

                <!-- Información -->
                <p class="mb-1"><strong>Capacidad de carga en puntos de colgante fijos</strong> 350 kg</p>
                <p><strong>Foyer para registros, exposiciones o recepciones:</strong> 1,714 m²</p>

                <h6 class="mt-4"><strong>Dimensiones</strong></h6>
                <p class="mb-1">Área: 456.34 m²</p>
                <p class="mb-1">Largo: 45.80 m</p>
                <p class="mb-1">Ancho: 75.90 m</p>
                <p>Alto: 9.40 m</p>

                <h6><strong>Capacidades</strong></h6>
                <p class="mb-1">Auditorio: 450</p>
                <p class="mb-1">Banquete: 225</p>
                <p>Escuela: 225</p>

                <!-- Imagen -->
                <div class="mt-4">
                    <img src="https://via.placeholder.com/600x350" class="img-fluid rounded-3">
                </div>

                <p class="mt-3">¿Necesitas más espacio?<br>Selecciona otro salón para ampliar tu espacio.</p>

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
                <div class="col-md-2">
                    <p class="metric-number">915.99 m²</p>
                    <p>Área total para evento</p>
                </div>
                <div class="col-md-1">
                    <p class="metric-number">450</p>
                    <p>Auditorio</p>
                </div>
                <div class="col-md-1">
                    <p class="metric-number">225</p>
                    <p>Banquete</p>
                </div>
                <div class="col-md-1">
                    <p class="metric-number">225</p>
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
@endsection

@push('scripts')
    <script>


    </script>
@endpush