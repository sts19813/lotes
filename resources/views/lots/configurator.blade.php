@extends('layouts.app')

@section('title', 'Configurador de Lote')

@section('content')




				<link rel="stylesheet" href="/assets/css/configurador.css">
				<div class="card shadow-sm">
								<div class="card-header">
												<h3 class="card-title">Configurador: {{ $lot->name }}</h3>
												<div class="card-toolbar">
																<a href="{{ route('lots.form') }}" class="btn btn-secondary">Regresar</a>
												</div>
								</div>
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


				<!-- Modal -->
				<div class="modal fade" id="polygonModal" tabindex="-1" aria-labelledby="polygonModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-xl">
												<div class="modal-content">
																<div class="modal-header">
																				<h5 class="modal-title" id="polygonModalLabel">Elemento seleccionado</h5>
																				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
																</div>
																<div class="modal-body">
																				<p>Elemento seleccionado: <strong id="selectedElementId"></strong></p>
																				<form id="polygonForm">
																								@csrf
																								<input type="hidden" id="polygonId" name="polygonId">

																								{{-- Formulario dinámico --}}
																								<div class="row g-3 mb-4">
																												<div class="col-md-4">
																																<label class="form-label fw-bold">Proyecto</label>
																																<select name="project_id" id="modal_project_id" class="form-select form-select-solid"
																																				required>
																																				<option value="">Seleccione un proyecto...</option>
																																				@foreach ($projects as $project)
																																								<option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
																																				@endforeach
																																</select>
																												</div>
																												<div class="col-md-4">
																																<label class="form-label fw-bold">Fase</label>
																																<select name="phase_id" id="modal_phase_id" class="form-select form-select-solid" required>
																																				<option value="">Seleccione una fase...</option>
																																</select>
																												</div>
																												<div class="col-md-4">
																																<label for="stage_id" class="form-label">Etapa (Stage)</label>
																																<select id="modal_stage_id" name="stage_id" class="form-select" required>
																																				<option value="">Seleccione una etapa...</option>
																																</select>
																												</div>
																												<div class="col-md-12 mb-3">
																																<label for="lot_id" class="form-label fw-bold">Lote</label>
																																<select id="modal_lot_id" name="lot_id" class="form-select form-select-solid" required>
																																				<option value="">Seleccione un lote...</option>
																																</select>
																												</div>
																								</div>

																								{{-- Información adicional del polígono --}}
																								<div class="form-check mb-3">
																												<input class="form-check-input" type="checkbox" value="1" id="redirect" name="redirect">
																												<label class="form-check-label" for="redirect">
																																¿Aplicar redirección?
																												</label>
																								</div>

																								<div class="mb-3">
																												<label for="redirect_url" class="form-label">URL de redirección:</label>
																												<input type="url" class="form-control" id="redirect_url" name="redirect_url"
																																placeholder="https://example.com" disabled>
																								</div>
																								<div class="d-flex justify-content-end">
																												<button type="submit" class="btn btn-primary">Guardar</button>
																								</div>
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
 </script>
	<script src="/assets/js/lotes.js"></script>
 <script src="/assets/js/iframe.js"></script>

@endpush
