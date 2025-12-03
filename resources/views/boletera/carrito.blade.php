{{-- Carrito flotante / Offcanvas sencillo (Metronic-friendly) --}}
<div id="cartSidebar" class="card shadow-sm" style="position: fixed; top: 80px; right: 20px; width: 360px; z-index: 2000;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Carrito</h5>
        <button id="cartCloseBtn" class="btn btn-sm btn-icon btn-light" title="Cerrar">&times;</button>
    </div>

    <div class="card-body p-0">
        <div id="cartItems" style="max-height: 360px; overflow:auto; padding: 12px;"></div>

        <div class="p-3 border-top">
            <div class="d-flex justify-content-between mb-2">
                <strong>Total</strong>
                <strong id="cartTotal">$0.00</strong>
            </div>

            <div class="d-flex gap-2">
                <button id="btnClearCart" class="btn btn-outline-secondary btn-sm flex-grow-1">Vaciar</button>
                <button id="btnCheckout" class="btn btn-primary btn-sm flex-grow-1">Pagar</button>
            </div>
        </div>
    </div>
</div>
