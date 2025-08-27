@extends('layouts.iframe')

@section('title', 'Configurador de Lote')

@section('content')

				<link rel="stylesheet" href="/assets/css/configurador.css">
				<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

				<div class="card shadow-sm">
								<div class="card-body text-center">
												<div style="position: relative; display: inline-block;">
																@if ($lot->png_image)
																				<img src="{{ asset('/' . $lot->png_image) }}" alt="PNG" style="width:900px; height:auto;">
																@endif

																@if ($lot->svg_image)
																				<div style="position: absolute; top:0; left:0; width:100%;">
																								{!! file_get_contents(public_path($lot->svg_image)) !!}
																				</div>
																@endif
												</div>
								</div>
				</div>


				<!-- Modal Cotizador -->
				<div class="modal fade" id="polygonModal" tabindex="-1" aria-labelledby="polygonModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-xl modal-dialog-centered">
												<div class="modal-content" style="border-radius: 10px; overflow: hidden;">
																<div class="row g-0">
																				<!-- LADO IZQUIERDO -->
																				<div class="col-md-6 p-4 text-white" style="background:#927A94;">

																								<img src="/assets/img/title.svg" alt="logo">

																								<div class="linea-discontinua "></div>


																								<div class="info-container">
																												<div class="info-item">
																																<span class="label">Lote</span>
																																<strong class="value" id="loteName">629</strong>
																												</div>
																												<div class="info-item">
																																<span class="label">Área</span>
																																<strong class="value" id="lotearea">200.0 m²</strong>
																												</div>
																												<div class="info-item">
																																<span class="label">Precio m²</span>
																																<strong class="value" id="lotePrecioMetro">$2,912.50</strong>
																												</div>
																												<div class="info-item">
																																<span class="label">Precio</span>
																																<strong class="value" id="lotePrecioTotal">$711,000.00</strong>
																												</div>
																								</div>

																								<div class="linea-discontinua "></div>


																								<img src="/assets/img/Plan de pagos.svg" alt="logo">
																								<div class="mb-4 mt-4">
																												<label class="label">Porcentaje de enganche</label>
																												<select class="form-select form-select-sm">
																																<option>30% de enganche</option>
																																<option>40% de enganche</option>
																																<option>50% de enganche</option>
																												</select>
																								</div>
																								<p class="label"> Monto de Enganche: <strong>$213,300.00 MXN</strong></p>


																								<div class="linea-discontinua "></div>
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

																																<img src="/assets/img/resumen.svg" alt="logo" class="mt-4">

																																<div class="row g-3 mt-4">
																																				<div class="col-6">
																																								<div class="label text-modal">Enganche</div>
																																								<div class="value text-primary fw-bold">30%</div>
																																				</div>
																																				<div class="col-3">
																																								<div class="label text-modal">Intereses</div>
																																								<div class="value fw-bold">8%</div>
																																				</div>
																																				<div class="col-3">
																																								<div class="label text-modal">Descuento</div>
																																								<div class="value fw-bold">8%</div>
																																				</div>

																																				<div class="col-4">
																																								<div class="label text-modal">Financiamiento</div>
																																								<div class="value fw-bold">60 meses</div>
																																				</div>
																																				<div class="col-4">
																																								<div class="label text-modal">Mensualidad</div>
																																								<div class="value text-primary fw-bold" id="loteMensualidad">$8,295.00</div>
																																				</div>
																																				<div class="col-4">
																																								<div class="label text-modal">Monto Financiado</div>
																																								<div class="value fw-bold" id="loteMontoFinanciado">$497,700</div>
																																				</div>

																																				<div class="col-4">
																																								<div class="label text-modal">Contra Entrega</div>
																																								<div class="value text-primary fw-bold" id="loteContraEntrega">$120,700</div>
																																				</div>
																																				<div class="col-4">
																																								<div class="label text-modal">Costo total</div>
																																								<div class="value text-primary fw-bold" id="loteCostoTotal">$711,000.00</div>
																																				</div>
																																</div>


																																<div class="linea-discontinua-black "></div>


																																<img src="/assets/img/simulador de plusvalía.svg" alt="logo" class="mt-4">

																																<p class="text-modal label mt-4">Proyección de plusvalía a <strong>5 años</strong> de
																																				acuerdo al plan <br> de pagos
																																				seleccionado</p>

																																<!-- Tarjetas -->
																																<div class="row g-3 mb-3">
																																				<div class="col-6">
																																								<div class="card p-3 shadow-sm text-center background-verde">
																																												<img src="/assets/img/dinero.svg" alt="logo" class="mt-4 logos-modal">
																																												<small class="text-modal-card">Plusvalía Total</small>
																																												<h6 class="fw-bold text-success">$719,074.97</h6>
																																								</div>
																																				</div>
																																				<div class="col-6">
																																								<div class="card p-3 shadow-sm text-center background-azul">
																																												<img src="/assets/img/mira.svg" alt="logo" class="mt-4 logos-modal">
																																												<small class="text-modal-card">ROI Proyectado</small>
																																												<h6 class="fw-bold text-primary">101.14%</h6>
																																								</div>
																																				</div>
																																				<div class="col-6">
																																								<div class="card p-3 shadow-sm text-center background-morado">
																																												<img src="/assets/img/calendario.svg" alt="logo"
																																																class="mt-4 logos-modal">
																																												<small class="text-modal-card">Plusvalía Anual</small>
																																												<h6 class="fw-bold">15%</h6>
																																								</div>
																																				</div>
																																				<div class="col-6">
																																								<div class="card p-3 shadow-sm text-center background-amarillo">
																																												<img src="/assets/img/arriba.svg" alt="logo" class="mt-4 logos-modal">
																																												<small class="text-modal-card">Valor Final</small>
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
																																								</tbody>
																																				</table>
																																</div>

																																<p>
																																				Los cálculos consideran el plan de financiamiento y la ubicación <br>
																																				Esta es una proyección basada en tendencias históricas del mercado
																																</p>

																												</div>
																												<div id="tab2">
																																<img src="/assets/img/CHEPINA.svg" alt="logo" class="mt-4">
																																<img id="chepinaIMG" src="" alt="Lote" class="chepina">
																												</div>
																								</div>



																								<button class="btn btn-dark w-100" id="btnDescargarCotizacion">DESCARGAR COTIZACIÓN</button>
																				</div>
																</div>
												</div>
								</div>
				</div>


				<!-- Modal Formulario de Descarga -->
				<div class="modal fade" id="downloadFormModal" tabindex="-1" aria-labelledby="downloadFormModalLabel"
								aria-hidden="true">
								<div class="modal-dialog modal-md modal-dialog-centered">
												<div class="modal-content" style="border-radius: 15px; overflow: hidden; background:#927A94; color:white;">

																<div class="p-4 text-center">
																				<h5 class="fw-bold mt-4 mb-4">TUS DATOS</h5>
																				<p>Favor de dejar tus datos para descargar la cotización y nosotros te contactaremos lo más pronto,
																								gracias.</p>
																				<div class="linea-discontinua mb-3"></div>
																				<h6 class="sub-title">DESCARGA TU COTIZACIÓN</h6>

																				<form id="downloadForm" action="{{ route('leads.store') }}" method="POST" class="mt-3">
																								@csrf
																								<div class="mb-3">
																												<input type="text" class="form-control" name="name" placeholder="Nombre Completo"
																																required>
																								</div>
																								<div class="mb-3">
																												<input type="text" class="form-control" name="phone" placeholder="Celular" required>
																								</div>
																								<div class="mb-3">
																												<input type="email" class="form-control" name="email" placeholder="Correo" required>
																								</div>
																								<div class="mb-3">
																												<input type="text" class="form-control" name="city" placeholder="Ciudad" required>
																								</div>

																								<!-- HIDDEN FIELDS -->
																								<input type="hidden" name="phase_id" value="{{ $lot->phase_id }}">
																								<input type="hidden" name="project_id" value="{{ $lot->project_id }}">
																								<input type="hidden" name="stage_id" value="{{ $lot->stage_id }}">
																								<input type="hidden" name="lot_number" id="lotNumberHidden" value="">

																								<button type="submit" class="btn btn-light w-100" style="border-radius: 25px; color:black;">
																												ENVIAR Y DESCARGAR
																								</button>
																				</form>

																</div>
												</div>
								</div>
				</div>

@endsection

@push('scripts')
				<script>
								let selector = "svg g *";
				</script>

				<script>
								window.Laravel = {
												csrfToken: "{{ csrf_token() }}",
												routes: {
																lotsFetch: "{{ route('lots.fetch') }}",
																lotesStore: "{{ route('lotes.store') }}"
												}
								};

								window.preloadedLots = @json($lots);
								window.currentLot = @json($lot);
								window.projects = @json($projects);
								window.dbLotes = @json($dbLotes);

								window.idDesarrollo = {{ $lot->id }};
								let redireccion = true;


				</script>
				<script src="/assets/js/iframe.js"></script>
@endpush
