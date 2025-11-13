<!-- resources/views/emails/cotizacion.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Cotización</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			background-color: #f4f4f4;
			color: #333;
			margin: 0;
			padding: 0;
		}

		.container {
			max-width: 600px;
			margin: 20px auto;
			background-color: #fff;
			border-radius: 8px;
			overflow: hidden;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		}

		.header {
			background-color: #000;
			color: #fff;
			text-align: center;
			padding: 15px;
		}

		.header img {
			height: 50px;
			margin-bottom: 10px;
		}

		.content {
			padding: 20px;
		}

		h1 {
			font-size: 20px;
			margin-bottom: 10px;
		}

		p {
			font-size: 14px;
			line-height: 1.5;
		}

		.footer {
			text-align: center;
			font-size: 12px;
			color: #999;
			padding: 15px;
		}

		.btn {
			display: inline-block;
			background-color: #000;
			color: #fff;
			text-decoration: none;
			padding: 10px 15px;
			border-radius: 5px;
			margin-top: 15px;
		}
	</style>
</head>

<body>
	<div class="container"
		style="max-width:600px; margin:20px auto; background:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); font-family:Arial,sans-serif; color:#333;">
		{{-- Header --}}
		<div class="header" style="background:#000; color:#fff; text-align:center; padding:15px;">
			@if(!empty($desarrollo_logo))
				<img src="{{ url($desarrollo_logo) }}" alt="Logo" style="height:50px; margin-bottom:10px;">
			@else
			@endif
			<div style="font-size:18px; font-weight:bold;">COTIZACIÓN</div>
		</div>

		{{-- Content --}}
		<div class="content" style="padding:20px;">
			<h1 style="font-size:20px; margin-bottom:15px;">Hola {{ $user->name ?? 'Cliente' }},</h1>
			<p style="font-size:14px; line-height:1.5; margin-bottom:15px;">
				Se ha generado tu cotización para el lote <strong>{{ $lot->name ?? '' }}</strong> en el desarrollo
				<strong>{{ $lot->desarrollo_name ?? '' }}</strong>.
			</p>

			<h2 style="font-size:16px; margin-bottom:10px; border-bottom:1px solid #ddd; padding-bottom:5px;">Detalles
				del Lote</h2>
			<ul style="list-style:none; padding:0; font-size:14px; line-height:1.6;">
				<li><strong>Nombre de la unidad:</strong> {{ $lot->name }}</li>
				<li><strong>Área:</strong> {{ $lot->area }} m²</li>
				<li><strong>Precio total:</strong> ${{ number_format($lot->precioTotal, 2) }}</li>

				@if (isset($lot->desarrollo_name))
					<li><strong>Desarrollo:</strong> {{ $lot->desarrollo_name }}</li>
				@endif
			</ul>

			<p style="font-size:14px; margin-top:15px;">
				Para ver tu cotización completa, descarga el archivo adjunto.
			</p>
		</div>

		{{-- Footer --}}
		<div class="footer" style="text-align:center; font-size:12px; color:#999; padding:15px;">
			&copy; {{ date('Y') }}. Todos los derechos reservados.
		</div>
	</div>
</body>

</html>