<!-- Modal Cotizador -->
<div class="modal fade" id="polygonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content cotizador-modal">
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- COTIZADOR IZQUIERDO -->
                    <div class="col-lg-6 p-4 cotizador-left d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold text-white titleCotizador m-0 d-flex align-items-center gap-2 flex-wrap">
                                Cotizador
                            </h4>

          
                                
                            <!-- Selects idioma y moneda -->
                            <div class="d-none gap-2 col-6 col-md-6">
                                <select class="form-select selector-select form-select-combo">
                                    <option>Es</option>
                                    <option>En</option>
                                </select>
                                <select class="form-select selector-select form-select-combo">
                                    <option>MXN</option>
                                    <option>USD</option>
                                </select>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-3">
                                <label class="form-label text-white">Unidad</label>
                                 <span class="value color-Primario" id="loteName"></span>
                            </div>
                            <div class="col-3">
                                <label class="form-label text-white">Área</label>
                                 <span class="value color-Primario" id="lotearea"></span>
                            </div>
                            <div class="col-3">
                                <label class="form-label text-white">Precio m2</label>
                                 <span class="value color-Primario" id="lotePrecioMetro"></span>
                            </div>
                            
                        </div>


                        <div class="row mb-3 align-items-end">
                            <div class="col-12 col-md-8">
                                <label class="form-label text-white">Selecciona un plan</label>
                                <select class="form-select form-select-combo" id="planSelect">
                                    @if($financiamientos->count() > 0)
                                        @foreach($financiamientos as $plan)
                                            <option 
                                                value="{{ $plan->financiamiento_meses }}" 
                                                data-financing='@json($plan)'
                                                {{ $loop->first ? 'selected' : '' }}>
                                                {{ $plan->financiamiento_meses }} meses
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ $lot->financing_months ?? 60 }}"
                                            data-financing='{{ json_encode(["financing_months" => $lot->financing_months ?? 60, "porcentaje_enganche" => 30, "descuento_porcentaje" => 0, "financiamiento_interes" => 0]) }}'
                                            selected>
                                            {{ $lot->financing_months ?? 60 }} meses
                                        </option>
                                    @endif
                                </select>
                            </div>

                            <!-- Plan, Intereses y Descuento -->
                            <div class="col-6 col-md-2" id="divloteIntereses">
                                <label class="form-label text-white">Intereses</label>
                                <div class="form-control form-control-plaintext text-white fw-bold p-2 color-Primario"
                                    id="loteIntereses">0%</div>
                            </div>

                            <div class="col-6 col-md-2" id="divloteDescuento">
                                <label class="form-label text-white">Descuento</label>
                                <div class="form-control form-control-plaintext text-white fw-bold p-2 color-Primario"
                                    id="loteDescuento">0%</div>
                            </div>
                        </div>

                        <!-- Enganche -->
                        <label class="form-label text-white">Enganche</label>
                        <div class="row mb-3">
                            <div class="col-4">
                                <div class="form-control form-control-plaintext text-white fw-bold p-2 color-Primario"
                                    id="loteEnganchePorcentaje">30%</div>
                            </div>
                            <div class="col-8">
                                <div class="form-control form-control-plaintext text-white fw-bold p-2 color-Primario"
                                    id="loteContraEntrega">$0</div>
                            </div>
                        </div>

                        <!-- Diferido -->
                        <div class="col-12" id="divloteDiferido" style="display:none;">
                            <label class="form-label text-white">Diferido</label>
                            <div class="form-control form-control-plaintext text-white fw-bold mb-3 p-2 color-Primario" id="loteDiferido"></div>
                        </div>

                       <!-- Financiamiento -->
                       <div class="row mb-3">
                            <div class="col-8">
                                <label class="form-label text-white">Financiamiento</label>
                                <div class="form-control form-control-plaintext text-white fw-bold p-2 justify-content-between align-items-center text-white">
                                    <div id="loteFinanciamiento" class="color-Primario" style="display: inline-block;"></div> 
                                    <div id="loteMensualidad" style="display: inline-block;"></div> <span style="font-weight: 300"> Mensuales</span> 
                                </div>
                            </div>
                            <div class="col-4">
                                <label class="form-label text-white">Monto financiado</label>
                                <div class="form-control form-control-plaintext text-white fw-bold p-2 color-Primario" id="loteMontoFinanciado">$0.00</div>
                            </div>
                        </div>
                        <!-- Saldo -->
                        <div id="divSaldo" class="col-12">
                            <label class="form-label text-white">Saldo</label>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="form-control form-control-plaintext text-white fw-bold p-2 color-Primario" 
                                        id="SaldoPorcentaje">0%</div>
                                </div>

                                <div class="col-6">
                                    <div class="form-control form-control-plaintext text-white fw-bold p-2 color-Primario"
                                        id="SaldoMonto">$0.00</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12" id="divSaldoDiferido" style="display:none;">
                            <label class="form-label text-white">Diferido</label>
                            <div class="form-control form-control-plaintext text-white fw-bold mb-3 p-2 color-Primario" id="loteSaldoDiferido"></div>
                        </div>

                        <!-- Precio total -->
                        <label class="form-label text-white">Precio total</label>
                        <div class="form-control form-control-plaintext mb-3 text-white fw-bold p-2 color-Primario"
                            id="lotePrecioTotal">$0</div>

                        <div class="powered-by mt-auto text-center">
                            Powered by: <img src="/assets/logos/naboo-logo1.svg" height="22">
                        </div>
                    </div>

                    <!-- SIMULADOR DERECHO -->
                    <div class="col-lg-6 p-4 bg-white">
                        <div class="switch-tabs btn-group mb-4" role="group">
                            <button type="button" class="btn active" data-tab="tab1">
                                Resumen Financiero
                            </button>
                            <button type="button" class="btn" data-tab="tab2">
                                Chepina
                            </button>
                        </div>

                        <!-- Contenido Tabs -->
                        <div class="tab-content">
                            <!-- TAB 1: Resumen Financiero -->
                            <div id="tab1" class="active">
                                <!-- Tarjetas horizontales -->

                                <div style="color: #7A84A7; font-size:15px !important; " class="mb-4">
                                    Proyección de plusvalía a 5 años de acuerdo al plan de pagos seleccionado
                                </div>
                                <div class="row g-3 mb-3 mt-4">
                                    <div class="col-12">
                                        <div class="card p-4 d-flex flex-row align-items-center mb-2 background-verde"
                                            style="border-radius: 10px; background-color: #f8f9fa;">
                                            <img src="/assets/img/dinero.svg" alt="logo" class="me-3"
                                                style="width:20px; height:20px;">
                                            <small class="text-muted me-auto">Plusvalía Total</small>
                                            <h6 class="fw-bold text-success mb-0" id="PlusvaliaTotal">$0</h6>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card p-4 d-flex flex-row align-items-center mb-2 background-azul"
                                            style="border-radius: 10px; background-color: #f8f9fa;">
                                            <img src="/assets/img/mira.svg" alt="logo" class="me-3"
                                                style="width200px; height:20px;">
                                            <small class="text-muted me-auto">ROI Proyectado</small>
                                            <h6 class="fw-bold text-primary mb-0" id="ROIProyectado">0%</h6>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card p-4 d-flex flex-row align-items-center mb-2 background-morado"
                                            style="border-radius: 10px; background-color: #f8f9fa;">
                                            <img src="/assets/img/calendario.svg" alt="logo" class="me-3"
                                                style="width:20px; height:20px;">
                                            <small class="text-muted me-auto">Plusvalía Anual</small>
                                            <h6 class="fw-bold text-warning mb-0" id="PlusvaliaAnual">0%</h6>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card p-4 d-flex flex-row align-items-center mb-2 background-amarillo"
                                            style="border-radius: 10px; background-color: #f8f9fa;">
                                            <img src="/assets/img/arriba.svg" alt="logo" class="me-3"
                                                style="width:20px; height:20px;">
                                            <small class="text-muted me-auto">Valor Final</small>
                                            <h6 class="fw-bold text-success mb-0" id="ValorFinal">$0</h6>
                                        </div>
                                    </div>
                                </div>
                                <br><br>

                                <!-- Tabla resumen -->
                                <!-- Tabla -->
                                <div class="table-responsive small mb-4 mt-4">
                                    <table class="table table-sm table-borderless">
                                        <thead class="table-light mb-4 mt-4">
                                            <tr>
                                                <th>Año</th>
                                                <th>Valor Propiedad</th>
                                                <th>Monto Pagado</th>
                                                <th>Plusvalía Acumulada</th>
                                                <th>ROI (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <p class="small">
                                    Los cálculos consideran el plan de financiamiento y la ubicación <br>
                                    Esta es una proyección basada en tendencias históricas del mercado
                                </p>

                            </div>

                            <!-- TAB 2: Chepina -->
                            <div id="tab2">
                                <img id="chepinaIMG" src="" alt="Lote" class="chepina img-fluid">
                            </div>

                        </div>

                        <button class="btn btn-dark w-100 mt-3" id="btnDescargarCotizacion">
                            DESCARGAR COTIZACIÓN
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


                
<style>

@font-face {
    font-family: "RFlex";
    src: url("/assets/fonts/R-FLEX-REGULAR.woff2") format("woff2");
    font-weight: normal;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: "RFlex";
    src: url("/assets/fonts/R-FLEX-LIGHT.woff2") format("woff2");
    font-weight: 300; /* Light */
    font-style: normal;
    font-display: swap;
}
@font-face {
    font-family: "RFlex";
    src: url("/assets/fonts/R-FLEX-MEDIUM.woff2") format("woff2");
    font-weight: 500; /* Medium */
    font-style: normal;
    font-display: swap;
}


    .pago-monto,.saldo-monto{
        color: white !important;
        margin-left: 15px !important;
        font-weight: 300 !important
    }
    #loteMensualidad{
        font-size: 16px !important;
        margin-left: 10px !important;
        font-weight: 300 !important

    }
    .titleCotizador {
        font-family: "RFlex", sans-serif;
        font-size: 26px;
        font-weight: 700;
    }

    .table-light th{
        color: #7A84A7 !important;
    }

    .table td{
        color: #7A84A7 !important;
    }

    .table .text-success{
        color: #00B845 !important;
    }

    .table .text-primary{
        color: #5820FF !important;
    }
    /* Fondo principal izquierda */
    .cotizador-left {
        color: #fff;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
        padding: 35px 20px !important;
        background: {{ $lot->modal_color ?? '#c47332' }};
    }

    /* Quitar bordes del modal */
    .cotizador-modal {
        border-radius: 10px;
        overflow: hidden;
    }

    .color-Primario{
        font-size: 16px !important;
        font-family: "RFlex", sans-serif !important;
        color: {{ $lot->color_primario ?? '#8C470F' }} !important;
    }


    /* Inputs */
    .cotizador-left .form-control,
    .cotizador-left .form-select {
        background: color-mix(in srgb, {{ $lot->modal_color ?? '#c47332' }} 87%, white);
        border: none;
        color: #fff;
        border-radius: 3px;
        padding: 12px !important;
    }

    .cotizador-left .form-select-combo {
        background: color-mix(in srgb, {{ $lot->modal_color ?? '#c47332' }} 80%, black);
        border: none;
        color: #fff;
        border-radius: 3px;
        padding: 12px !important;
    }

    /* Placeholders white */
    .cotizador-left .form-control:read-only {
        color: #fff;
    }

    .form-label{
        font-family: "RFlex", sans-serif !important;
        font-weight: 300 !important;
        font-size: 16px !important;
    } 
    /* Tarjetas simulador */
    .sim-card {
        display: flex;
        align-items: center;
        background: #f6f6f6;
        padding: 16px;
        border-radius: 12px;
    }

    .sim-icon {
        font-size: 22px;
        margin-right: 12px;
    }

    /* Tab buttons */
    .tab-btn-active {
        background: #ff559d;
        color: white;
        border-radius: 8px;
        padding: 8px 18px;
    }

    .tab-btn {
        background: #ececec;
        color: #333;
        border-radius: 8px;
        padding: 8px 18px;
    }

    .label {
        color: #777;
        margin: 0;
    }

    .value {
        font-size: 20px;
        font-weight: bold;
        margin: 0;
    }

    /* Botón descargar */
    .descargar-btn {
        background: #ff2f76;
        color: white;
        padding: 16px;
        font-size: 18px;
        border-radius: 10px;
    }

    .card{
        border:none !important;
    }

    .text-muted{
        font-family: "RFlex", sans-serif !important;
        font-size: 14px !important;
        color: #323232 !important;
    }

    .small{
        color: #494949 !important;
    }

    /* Mobile */
    @media (max-width: 768px) {
        .cotizador-left {
            border-radius: 0;
        }
    }
</style>