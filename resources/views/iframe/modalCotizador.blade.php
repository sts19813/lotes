<!-- Modal Cotizador -->
<div class="modal fade" id="polygonModal" tabindex="-1" aria-labelledby="polygonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 10px; overflow: hidden;">
            <div class="row g-0">
                <!-- LADO IZQUIERDO -->
                <div class="col-md-6 p-4 text-white" style="background: {{ $lot->modal_color ?? '#927A94' }};">

                    <div class="d-flex justify-content-between align-items-start">
                        <img src="/assets/img/title.svg" alt="logo">

                        <!-- Botón cerrar solo en tablets y celulares -->
                        <button type="button" class="btn-close d-md-none" data-bs-dismiss="modal" aria-label="Cerrar"
                            style="position: absolute; top: 15px; right: 15px; filter: invert(1);">
                        </button>
                    </div>
                    <div class="info-container row g-3">
                        <div class="info-item col-12 col-md-6 col-lg-3">
                            <span class="label">Lote</span>
                            <strong class="value" id="loteName">0</strong>
                        </div>
                        <div class="info-item col-12 col-md-6 col-lg-3">
                            <span class="label">Área</span>
                            <strong class="value" id="lotearea">0.0 m²</strong>
                        </div>
                        <div class="info-item col-12 col-md-6 col-lg-3">
                            <span class="label">Precio m²</span>
                            <strong class="value" id="lotePrecioMetro">$0.0</strong>
                        </div>
                        <div class="info-item col-12 col-md-6 col-lg-3">
                            <span class="label">Precio</span>
                            <strong class="value" id="lotePrecioTotal">$0.00</strong>
                        </div>
                    </div>
                    <div class="linea-discontinua "></div>
                    <img src="/assets/img/Plan de pagos.svg" alt="logo">
                    <div class="mb-4 mt-4">
                        <label class="label">Porcentaje de enganche</label>
                        <select class="form-select form-select-sm">
                            <option>30% de enganche</option>

                        </select>
                    </div>
                    <p class="label"> Monto de Enganche: <strong>$0MXN</strong></p>
                    <div class="linea-discontinua "></div>
                    <!-- Planes dinámicos -->
                    <div class="row row-cols-2 g-3 mt-3">
                        @if($financiamientos->count() > 0)
                            @foreach($financiamientos as $plan)
                                <div class="col">
                                    <div class="plan-box {{ $loop->first ? 'active' : '' }}" data-financing='@json($plan)'
                                        data-meses="{{ $plan->financiamiento_meses ?? $plan->months ?? $plan->financing_months ?? 60 }}"
                                        role="button">
                                        <img src="/assets/img/Plan.svg" width="80">
                                        <p class="mb-1 fw-bold">
                                            <span style="font-size: 44px;">{{ $plan->financiamiento_meses }}</span>
                                            <span style="font-size: 12px;">Meses</span>
                                        </p>
                                        <small class="monthlyPayment">$0</small><br>
                                        <small style="color: #D9D9D6; font-family: poppins; font-size: 13px;">Mensuales</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Default: un solo plan con enganche 30%, 0% descuento, 0% interés -->
                            <div class="plan-box active" style="padding: 40px;"
                                data-financing='{{ json_encode(["financing_months" => $lot->financing_months ?? 60, "porcentaje_enganche" => 30, "descuento_porcentaje" => 0, "financiamiento_interes" => 0]) }}'
                                data-meses="{{ $lot->financing_months ?? 60 }}" role="button">

                                <img src="/assets/img/Plan.svg" width="80">
                                <p class="mb-1 fw-bold">
                                    <span style="font-size: 44px;">{{ $lot->financing_months ?? 60 }}</span>
                                    <span style="font-size: 12px;">Meses</span>
                                </p>
                                <small class="monthlyPayment"
                                    style="font-size: 13px;font-family: 'Poppins';color: #F2F2F2 !important;">$0</small><br>
                                <small style="color: #D9D9D6; font-family: poppins; font-size: 13px;">Mensuales</small>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- LADO DERECHO -->
                <div class="col-md-6 p-4">
                    <div class="switch-tabs btn-group" role="group">
                        <button type="button" class="btn active" data-tab="tab1">
                            <img src="/assets/img/resumen-icon.svg" alt="" srcset="">
                            Resumen Financiero
                        </button>
                        <button type="button" class="btn" data-tab="tab2">
                            <img src="/assets/img/chepina-icon.svg" alt="" srcset="">
                            Chepina
                        </button>
                    </div>
                    <!-- Contenido de cada tab -->
                    <div class="tab-content">
                        <div id="tab1" class="active mt-4">
                            <img src="/assets/img/resumen.svg" alt="logo" class="mt-4 img-fluid">
                            <!-- Datos principales -->
                            <div class="row g-3 mt-4">
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="label text-modal">Enganche</div>
                                    <div class="d-flex gap-2">
                                        <span class="value text-primary fw-bold" id="loteEnganchePorcentaje">30%</span>
                                        <span class="value text-primary fw-bold" style="white-space: nowrap;">
                                            ( <span id="loteContraEntrega">$0</span> )
                                        </span>
                                    </div>
                                </div>
                                <div class="col-3" id="divloteIntereses">
                                    <div class="label text-modal">Intereses</div>
                                    <div class="value fw-bold" id="loteIntereses">0%</div>
                                </div>
                                <div class="col-3" id="divloteDescuento">
                                    <div class="label text-modal">Descuento</div>
                                    <div class="value fw-bold" id="loteDescuento">0%</div>
                                </div>
                                <div class="col-4">
                                    <div class="label text-modal">Financiamiento</div>
                                    <div class="value fw-bold" id="loteFinanciamiento">60 meses</div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="label text-modal">Mensualidad</div>
                                    <div class="value text-primary fw-bold" id="loteMensualidad">$8,295.00</div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="label text-modal">Monto Financiado</div>
                                    <div class="value fw-bold" id="loteMontoFinanciado">$497,700</div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="label text-modal">Costo total</div>
                                    <div class="value text-primary fw-bold" id="loteCostoTotal">$711,000.00</div>
                                </div>
                            </div>
                            <div class="linea-discontinua-black mt-4"></div>

                            <img src="/assets/img/simulador de plusvalía.svg" alt="logo" class="mt-4 img-fluid">

                            <p class="text-modal label mt-4">
                                Proyección de plusvalía a <strong>5 años</strong> de acuerdo al plan <br> de pagos
                                seleccionado
                            </p>
                            <!-- Tarjetas -->
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <div class="card p-3 text-center background-verde h-100">
                                        <img src="/assets/img/dinero.svg" alt="logo" class="mt-4 logos-modal img-fluid">
                                        <small class="text-modal-card">Plusvalía Total</small>
                                        <h6 class="fw-bold text-success">$719,074.97</h6>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="card p-3 text-center background-azul h-100">
                                        <img src="/assets/img/mira.svg" alt="logo" class="mt-4 logos-modal img-fluid">
                                        <small class="text-modal-card">ROI Proyectado</small>
                                        <h6 class="fw-bold text-primary">101.14%</h6>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="card p-3 text-center background-morado h-100">
                                        <img src="/assets/img/calendario.svg" alt="logo"
                                            class="mt-4 logos-modal img-fluid">
                                        <small class="text-modal-card">Plusvalía Anual</small>
                                        <h6 class="fw-bold">15%</h6>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="card p-3 text-center background-amarillo h-100">
                                        <img src="/assets/img/arriba.svg" alt="logo" class="mt-4 logos-modal img-fluid">
                                        <small class="text-modal-card">Valor Final</small>
                                        <h6 class="fw-bold text-danger">$1,430,074.97</h6>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla -->
                            <div class="table-responsive small mb-3">
                                <table class="table table-sm table-borderless">
                                    <thead class="table-light">
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
                                            <td>$711,000</td>
                                            <td>$711,000</td>
                                            <td>$0</td>
                                            <td>0%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p>
                                Los cálculos consideran el plan de financiamiento y la ubicación <br>
                                Esta es una proyección basada en tendencias históricas del mercado
                            </p>
                        </div>
                        <div id="tab2">
                            <img src="/assets/img/CHEPINA.svg" alt="logo" class="mt-4 img-fluid">
                            <img id="chepinaIMG" src="" alt="Lote" class="chepina img-fluid">
                        </div>
                    </div>
                    <button class="btn btn-dark w-100" id="btnDescargarCotizacion">DESCARGAR COTIZACIÓN</button>
                </div>
            </div>
        </div>
    </div>
</div>