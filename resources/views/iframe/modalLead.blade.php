	<!-- Modal Formulario de Descarga -->
	<div class="modal fade" id="downloadFormModal" tabindex="-1" aria-labelledby="downloadFormModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-md modal-dialog-centered">
			<div class="modal-content"
				style="border-radius: 15px; overflow: hidden; color:white; background: {{ $lot->modal_color ?? '#927A94' }}; ">
				<div class="p-4 text-center">
					<h5 class="fw-bold mt-4 mb-4">TUS DATOS</h5>
					<p>Favor de dejar tus datos para descargar la cotización y nosotros te contactaremos lo más pronto,
						gracias.</p>
					<div class="linea-discontinua mb-3"></div>
					<h6 class="sub-title">DESCARGA TU COTIZACIÓN</h6>

					<form id="downloadForm" action="{{ route('leads.store') }}" method="POST" class="mt-3">
						@csrf
						<div class="mb-3">
							<input type="text" class="form-control" name="name" id="leadName" placeholder="Nombre Completo"
								required>
						</div>
						<div class="mb-3">
							<input type="text" class="form-control" name="phone" id="leadPhone" placeholder="Celular"
								required>
						</div>
						<div class="mb-3">
							<input type="email" class="form-control" name="email" id="leadEmail" placeholder="Correo"
								required>
						</div>
						<div class="mb-3">
							<input type="text" class="form-control" name="city" id="leadCity" placeholder="Ciudad" required>
						</div>

						<!-- HIDDEN FIELDS -->
						<input type="hidden" name="phase_id" value="{{ $lot->phase_id }}">
						<input type="hidden" name="project_id" value="{{ $lot->project_id }}">
						<input type="hidden" name="stage_id" value="{{ $lot->stage_id }}">
						<input type="hidden" name="lot_number" id="lotNumberHidden" value="">

						<button type="submit" id="submitBtn" class="btn btn-light w-100"
							style="border-radius: 25px; color:black;">
							<span class="btn-text">ENVIAR Y DESCARGAR</span>
							<span class="spinner-border spinner-border-sm ms-2 d-none" role="status"
								aria-hidden="true"></span>
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>