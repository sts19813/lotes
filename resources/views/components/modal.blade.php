<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cotizadorModal">
				Abrir Cotizador
</button>

<div class="modal fade" id="cotizadorModal" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-xl modal-dialog-centered">
								<div class="modal-content" style="border-radius: 16px; overflow: hidden;">
												<div class="row g-0">
																<!-- LADO IZQUIERDO -->
																<div class="col-md-6 p-4 text-white" style="background:#6e5d7e;">


																				<h5 class="fw-bold mb-3">COTIZADOR</h5>
																				<p class="mb-1">Lote: <strong>629</strong></p>
																				<p class="mb-1">Área: <strong>200.0 m2</strong></p>
																				<p class="mb-1">Precio m2: <strong>$2,912.50</strong></p>
																				<p class="mb-3">Precio: <strong>$711,000.00</strong></p>

																				<h6 class="fw-bold mt-4">PLAN DE PAGOS</h6>
																				<div class="mb-2">
																								<label class="form-label">Porcentaje de enganche</label>
																								<select class="form-select form-select-sm">
																												<option>30% de enganche</option>
																												<option>40% de enganche</option>
																												<option>50% de enganche</option>
																								</select>
																				</div>
																				<p>Monto de Enganche: <strong>$213,300.00 MXN</strong></p>

																				<!-- Planes -->
																				<div class="row row-cols-2 g-3 mt-3">
																								<div class="col">
																												<div class="plan-box">
																																<p class="mb-1 fw-bold">Contado</p>
																																<small>$680,000.00</small><br>
																																<small>10% descuento</small>
																												</div>
																								</div>
																								<div class="col">
																												<div class="plan-box">
																																<p class="mb-1 fw-bold">24 Meses</p>
																																<small>$16,925.00</small><br>
																																<small>Mensuales</small>
																												</div>
																								</div>
																								<div class="col">
																												<div class="plan-box">
																																<p class="mb-1 fw-bold">32 Meses</p>
																																<small>$13,925.00</small><br>
																																<small>Mensuales</small>
																												</div>
																								</div>
																								<div class="col">
																												<div class="plan-box active">
																																<p class="mb-1 fw-bold">60 Meses</p>
																																<small>$8,925.00</small><br>
																																<small>Mensuales</small>
																												</div>
																								</div>
																				</div>
																</div>

																<!-- LADO DERECHO -->
																<div class="col-md-6 p-4">
																				<div class="d-flex justify-content-between align-items-center mb-3">
																								<button class="btn btn-sm btn-light">Resumen Financiero</button>
																								<span class="fw-bold">Chepina</span>
																				</div>

																				<div class="switch-tabs btn-group" role="group">
																								<button type="button" class="btn active" data-tab="tab1">
																												<i class="bi bi-file-earmark-text"></i> Resumen Financiero
																								</button>
																								<button type="button" class="btn" data-tab="tab2">
																												<i class="bi bi-person-circle"></i> Chepina
																								</button>
																				</div>

																				<!-- Contenido de cada tab -->
																				<div class="tab-content">
																								<div id="tab1" class="active">
																												<h3>Resumen Financiero</h3>
																												<p>Aquí va el contenido del Resumen Financiero...</p>
																								</div>
																								<div id="tab2">
																												<h3>Chepina</h3>
																												<p>Aquí va el contenido de Chepina...</p>
																								</div>
																				</div>


																				<h5 class="fw-bold">RESUMEN</h5>
																				<div class="mb-3">
																								<p class="mb-1">Enganche: <strong class="text-primary">30% $213,300.00</strong></p>
																								<p class="mb-1">Financiamiento: <strong>60 meses</strong></p>
																								<p class="mb-1">Mensualidad: <strong class="text-primary">$8,295.00</strong></p>
																								<p class="mb-1">Monto Financiado: <strong>$497,700</strong></p>
																								<p class="mb-1">Contra Entrega: <strong class="text-primary">$120,700</strong></p>
																								<p class="mb-1">Costo total: <strong>$711,000.00</strong></p>
																				</div>

																				<h6 class="fw-bold mt-4">SIMULADOR DE PLUSVALÍA</h6>
																				<p>Proyección de plusvalía a <strong>5 años</strong> de acuerdo al plan seleccionado</p>

																				<!-- Tarjetas -->
																				<div class="row g-3 mb-3">
																								<div class="col-6">
																												<div class="card p-3 shadow-sm text-center">
																																<small>Plusvalía Total</small>
																																<h6 class="fw-bold text-success">$719,074.97</h6>
																												</div>
																								</div>
																								<div class="col-6">
																												<div class="card p-3 shadow-sm text-center">
																																<small>ROI Proyectado</small>
																																<h6 class="fw-bold text-primary">101.14%</h6>
																												</div>
																								</div>
																								<div class="col-6">
																												<div class="card p-3 shadow-sm text-center">
																																<small>Plusvalía Anual</small>
																																<h6 class="fw-bold">15%</h6>
																												</div>
																								</div>
																								<div class="col-6">
																												<div class="card p-3 shadow-sm text-center">
																																<small>Valor Final</small>
																																<h6 class="fw-bold text-danger">$1,430,074.97</h6>
																												</div>
																								</div>
																				</div>

																				<!-- Tabla -->
																				<div class="table-responsive small mb-3">
																								<table class="table table-sm table-bordered">
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
																																<tr>
																																				<td>1</td>
																																				<td>$817,650</td>
																																				<td>$817,650</td>
																																				<td>+$106,650</td>
																																				<td>15%</td>
																																</tr>
																																<tr>
																																				<td>2</td>
																																				<td>$940,297</td>
																																				<td>$940,297</td>
																																				<td>+$229,297</td>
																																				<td>32.26%</td>
																																</tr>
																																<tr>
																																				<td>3</td>
																																				<td>$1,081,342</td>
																																				<td>$1,081,342</td>
																																				<td>+$370,342</td>
																																				<td>52.08%</td>
																																</tr>
																																<tr>
																																				<td>4</td>
																																				<td>$1,243,543</td>
																																				<td>$1,243,543</td>
																																				<td>+$532,543</td>
																																				<td>74.89%</td>
																																</tr>
																																<tr>
																																				<td>5</td>
																																				<td>$1,430,074</td>
																																				<td>$1,430,074</td>
																																				<td>+$719,074</td>
																																				<td>101.14%</td>
																																</tr>
																												</tbody>
																								</table>
																				</div>

																				<button class="btn btn-dark w-100">DESCARGAR COTIZACIÓN</button>
																</div>
												</div>
								</div>
				</div>
</div>
