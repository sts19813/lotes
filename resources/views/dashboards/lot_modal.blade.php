<!-- Lot detail modal -->
<div class="modal fade" id="lotDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle del Lote</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="lot_detail_container">
            <div class="row">
                <div class="col-md-6">
                    <img id="lot_chepina" src="" alt="Chepina" class="img-fluid" />
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><strong>ID:</strong> <span id="lot_id"></span></li>
                        <li><strong>Nombre:</strong> <span id="lot_name"></span></li>
                        <li><strong>Área:</strong> <span id="lot_area"></span> m²</li>
                        <li><strong>Precio total:</strong> $<span id="lot_price"></span></li>
                        <li><strong>Status:</strong> <span id="lot_status"></span></li>
                    </ul>
                    <div class="mt-3">
                        <button id="btnOpenIframe" class="btn btn-outline-primary">Abrir vista SVG (iframe)</button>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
