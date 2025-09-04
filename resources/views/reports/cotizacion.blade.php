<!DOCTYPE html>
<html lang="es">

<head>
				<meta charset="utf-8">
				<title>Cotización - {{ $lot->lead_name ?? $lot->name }}</title>
				<style>
								body,
								html {
												margin: 0 !important;
												padding: 15px !important;
												font-family: DejaVu Sans, Arial, sans-serif;
								}

								.header {
												background: #000;
												color: #fff;
												text-align: left;
												padding: 10px 10px;
								}

								.header h1 {
												margin: 0;
												font-size: 22px;
												letter-spacing: 2px;
								}

								.subheader {
												font-size: 16px;
												font-weight: bold;
												margin: 0;
								}

								.section {
												padding: 15px 25px;
								}

								h3 {
												font-size: 14px;
												margin-bottom: 5px;
												text-transform: uppercase;
								}

								table {
												width: 100%;
												border-collapse: collapse;
												margin-bottom: 15px;
								}

								th,
								td {
												border: 1px solid #000;
												padding: 5px 0px;
												font-size: 15px;
								}

								th {
												background: #eee;
												text-align: left;
								}

								.black-header th {
												background: #000;
												color: #fff;
												text-align: left;
												font-weight: bold;
								}

								.white-header th {
												background: #fff;
												color: #000;
												text-align: left;
												font-weight: bold;
								}

								.no-border td,
								.no-border th {
												border: none !important;
								}


								.highlight {
												font-weight: bold;
								}

								.footnote {
												font-size: 10px;
												margin-top: 5px;
								}

								.lot-images {
												display: flex;
												gap: 10px;
												margin-top: 20px;
								}

								.lot-images .img-box {
												flex: 1;
												border: 1px solid #000;
												text-align: center;
												padding: 8px 0px;
												font-size: 10px;
								}

								.lot-images img {
												width: 100%;
												height: auto;
								}

								.two-col {
												width: 100%;
												border-collapse: collapse;
								}

								.two-col .col {
												width: 50%;
												vertical-align: top;
												padding: 0px;
								}


								.separator {
												border-top: 1px dashed #b4b4b4;
												/* línea negra arriba */
												margin: 0px 0;
												/* espacio arriba y abajo */
								}

								h2 {
												font-size: 26px;
												margin-bottom: 15px;
												text-transform: uppercase;
								}
				</style>
</head>

<body>

				{{-- ENCABEZADO --}}
				<div class="header" style="text-align: center; background-color: #000; color: #fff; padding: 40px 0;">
								@if (!empty($lot->desarrollo_logo))
												<img src="{{ public_path($lot->desarrollo_logo) }}" alt="{{ $lot->desarrollo_name }}"
																style="height: 80px; display: block; margin: 0 auto 20px auto;">
								@endif

								<div class="subheader" style="font-size: 16px; letter-spacing: 2px; margin-bottom: 10px;">
												{{ $lot->desarrollo_name ?? 'HACIENDA' }}
								</div>

								<h1 style="font-size: 48px; margin: 0 0 10px 0; font-weight: bold; letter-spacing: 5px;">
												{{ $lot->name ?? 'PIARÓ' }}
								</h1>

								<div class="subheader" style="font-size: 18px; letter-spacing: 2px;">
												COTIZACIÓN
								</div>
				</div>

				{{-- DATOS DE CLIENTE Y LOTE --}}
				{{-- DATOS DE CLIENTE Y LOTE --}}
				<div class="section">
								<h2>Datos de Cliente y Lote</h2>
								<table class="two-col no-border">
												<tr>
																<!-- Columna izquierda -->
																<td class="col">
																				<table class="no-border">
																								<tbody>
																												<tr>
																																<td>Nombre</td>
																																<td><b>{{ $lot->lead_name ?? '—' }}</b></td>
																												</tr>
																												<tr>
																																<td>Celular</td>
																																<td><b>{{ $lot->lead_phone ?? '—' }}</b></td>
																												</tr>
																												<tr>
																																<td>Correo</td>
																																<td><b>{{ $lot->lead_email ?? '—' }}</b></td>
																												</tr>
																												<tr>
																																<td>Ciudad</td>
																																<td><b>{{ $lot->city ?? '—' }}</b></td>
																												</tr>
																								</tbody>
																				</table>
																</td>

																<!-- Columna derecha -->
																<td class="col">
																				<table class="no-border">
																								<tbody>
																												<tr>
																																<td>Desarrollo</td>
																																<td><b>{{ $lot->desarrollo_name ?? '—' }}</b></td>
																												</tr>
																												<tr>
																																<td>Etapa</td>
																																<td><b>{{ $lot->stage_name ?? '—' }}</b></td>
																												</tr>
																												<tr>
																																<td>Lote</td>
																																<td><b>{{ $lot->name ?? '—' }}</b></td>
																												</tr>
																												<tr>
																																<td>Área</td>
																																<td><b>{{ number_format($lot->area, 2) }} m2</b></td>
																												</tr>
																												<tr>
																																<td>Precio m2</td>
																																<td><b>${{ number_format($lot->price_square_meter, 2) }}</b></td>
																												</tr>
																												<tr>
																																<td>Precio Total</td>
																																<td><b>${{ number_format($lot->precioTotal, 2) }}</b></td>
																												</tr>
																								</tbody>
																				</table>
																</td>
												</tr>
								</table>
				</div>


				<div class="separator"></div>



				{{-- PLAN DE PAGOS --}}
				<div class="section">
								<h2>Plan de Pagos</h2>
								<table class="no-border">
												<thead class="white-header">
																<tr>
																				<th>Enganche</th>
																				<th>Financiamiento</th>
																				<th>Mensualidad</th>
																				<th>Monto financiado</th>
																</tr>
												</thead>
												<tbody>
																<tr>
																				<td>{{ $lot->enganchePorc }}% <br> ${{ number_format($lot->engancheMonto, 2) }}</td>
																				<td>{{ $lot->meses }} Meses</td>
																				<td>${{ number_format($lot->mensualidad, 2) }}</td>
																				<td>${{ number_format($lot->precioTotal - $lot->engancheMonto, 2) }}</td>
																</tr>
												</tbody>
								</table>
				</div>

				<div class="separator"></div>

				{{-- SIMULADOR DE PLUSVALÍA --}}
				<div class="section">
								<h2>Simulador de Plusvalía a 5 años</h2>
								<table class="no-border">
												<thead class="white-header">
																<tr>
																				<th>Plusvalía Total</th>
																				<th>Plusvalía Anual</th>
																				<th>ROI Proyectado</th>
																				<th>Valor Final</th>
																</tr>
												</thead>
												<tbody>
																@php
																				$valorFinal = $lot->precioTotal * pow(1 + ($lot->plusvaliaRate ?? 0.15), 5);
																@endphp
																<tr>
																				<td>${{ number_format($valorFinal - $lot->precioTotal, 2) }}</td>
																				<td>{{ ($lot->plusvaliaRate ?? 0.15) * 100 }}%</td>
																				<td>{{ number_format($lot->roi, 2) }}%</td>
																				<td>${{ number_format($valorFinal, 2) }}</td>
																</tr>
												</tbody>
								</table>
				</div>

				<div class="separator"></div>

				{{-- TABLA DE PAGOS --}}
				<div class="section">
								<h2>Tabla de Pagos</h2>
								<table>
												<thead class="black-header">
																<tr>
																				<th>Concepto</th>
																				<th>Monto</th>
																</tr>
												</thead>
												<tbody>
																<tr>
																				<td>Enganche</td>
																				<td>{{ $lot->enganchePorc }}% ${{ number_format($lot->engancheMonto, 2) }}</td>
																</tr>
																@for ($i = 1; $i <= min($lot->meses, 16); $i++)
																				<tr>
																								<td>Mes {{ $i }}</td>
																								<td>${{ number_format($lot->mensualidad, 2) }}</td>
																				</tr>
																@endfor
												</tbody>
								</table>
								<div class="footnote">
												*Vigencia de 7 días y/o sujeto a: <br>
												• Cambio de precio o promoción sin previo aviso <br>
												• Disponibilidad del lote
								</div>
				</div>

				{{-- PLANO Y SIMBOLOGÍA --}}
				<div class="section">
								<div class="lot-images">
												<div class="img-box">
																		<img src="{{ $lot->chepina_base64 }}" alt="Chepina" style="width:100%;">
												</div>
								</div>
				</div>

				<div class="section" style="text-align:center; font-size:10px; margin-top:20px;">
								Powered by
				</div>
</body>

</html>
