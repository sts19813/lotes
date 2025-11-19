@extends('layouts.iframe')

@section('title', 'Naboo')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

	<link rel="stylesheet" href="/assets/css/configurador.css">
	<link rel="stylesheet" href="/assets/css/iframe.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

	<div class="text-center">
		<div style="position: relative; display: inline-block;">

			{{-- Imagen base PNG --}}
			@if ($lot->png_image)
				<img src="{{ asset('/' . $lot->png_image) }}" alt="PNG" style="width:100%; height:auto;">
			@endif

			{{-- SVG encima --}}
			@if ($lot->svg_image)
				<div style="position: absolute; top:0; left:0; width:100%;">
					{!! file_get_contents(public_path($lot->svg_image)) !!}
				</div>
			@endif

			{{-- 🔗 Iconos flotantes --}}
			<div style="position: absolute; top: 10px; left: 10px; display: flex; gap: 8px;">
				@if ($lot->redirect_return)
					<a href="{{ route('iframe.index', $lot->redirect_return) }}" class="" title="Regresar">
						<img src="{{ asset('assets/controes/Regresar.svg') }}" alt="Regresar" style="height:24px;">
					</a>
				@endif
				@if ($lot->redirect_previous)
					<a href="{{ route('iframe.index', $lot->redirect_previous) }}" class="" title="Anterior">
						<img src="{{ asset('assets/controes/Anterior.svg') }}" alt="Anterior" style="height:24px;">
					</a>
				@endif
				@if ($lot->redirect_next)
					<a href="{{ route('iframe.index', $lot->redirect_next) }}" class="" title="Siguiente">
						<img src="{{ asset('assets/controes/Siguiente.svg') }}" alt="Siguiente" style="height:24px;">
					</a>
				@endif
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