<!-- Modal Formulario de Contacto -->
<div class="modal fade" id="downloadFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content contact-modal">

            <!-- Cerrar (cruz) -->
            <button type="button" class="btn-close close-x" data-bs-dismiss="modal" aria-label="Close"></button>

            <div class="p-4 text-center">

                <h3 class="fw-bold mb-2">Contáctanos</h3>
                <p class="modal-subtext mb-4">
                    Siempre atentos a tus necesidades, contáctanos y nuestros ejecutivos especializados 
                    te ayudarán a concretar tu evento en este grandioso escenario.
                </p>

                <form id="downloadForm" action="{{ route('leads.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <input type="text" class="modal-input" name="name" placeholder="Nombre" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <input type="text" class="modal-input" name="company" placeholder="Empresa">
                        </div>

                        <div class="col-12 col-md-6">
                            <input type="email" class="modal-input" name="email" placeholder="Email" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <input type="text" class="modal-input" name="phone" placeholder="Teléfono" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <select class="modal-input" name="event_type" required>
                                <option value="" selected disabled>Tipo de Evento</option>
                                <option>Boda</option>
                                <option>Conferencia</option>
                                <option>Congreso</option>
                                <option>Exposición</option>
                                <option>Reunión Empresarial</option>
                                <option>Otro</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <input type="date" class="modal-input" name="estimated_date" placeholder="Fecha Estimada">
                        </div>

                        <div class="col-12">
                            <textarea class="modal-input" name="message" placeholder="Mensaje" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- hidden fields -->
                    <input type="hidden" name="phase_id" value="{{ $lot->phase_id }}">
                    <input type="hidden" name="project_id" value="{{ $lot->project_id }}">
                    <input type="hidden" name="stage_id" value="{{ $lot->stage_id }}">
                    <input type="hidden" name="lot_number" id="lotNumberHidden">

                    <div class="text-center mt-4">
                        <button type="submit" class="submit-button">
                            Enviar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
