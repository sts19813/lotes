@extends('layouts.iframe')

@section('title', 'Configurador de Lote')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

	<link rel="stylesheet" href="/assets/css/configurador.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

	<style>
		.table {
			font-family: 'Inter', sans-serif;
			/* Fuente limpia como en la imagen */
			font-size: 0.95rem;
		}

		.table th {
			font-weight: 600;
			color: #333;
		}

		.table td {
			vertical-align: middle;
		}

		.table td,
		.table th {
			border: none !important;
		}

		.table-light {
			font-weight: bold !important;
			--bs-table-bg: #FFFFFF !important;
		}

		.text-primary {
			color: #1a73e8 !important;
			/* Azul tipo Google */
		}

		.fw-semibold {
			font-weight: 600;
		}

		#divloteDescuento,
		#divloteIntereses {
			display: none
		}

		svg:focus,
		svg g:focus,
		svg path:focus,
		svg rect:focus,
		svg polygon:focus {
			outline: none !important;
			box-shadow: none !important;
		}

		.btn-guardar-flotante {
			position: fixed;
			bottom: 30px;
			right: 30px;
			z-index: 1050;
			/* por encima de tooltips o SVG */
			padding: 12px 20px;
			font-weight: 600;
			border-radius: 8px;
			box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
			transition: all 0.2s ease-in-out;
		}

		.btn-guardar-flotante:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 14px rgba(0, 0, 0, 0.3);
		}

		@media (max-width: 992px) {
			#cartSidebar { right: 10px; width: 300px; top: 70px; }
			}

		/* Cursor siempre como flecha */
		body, html, div, span, p, a, button, input, textarea, svg, g, path, polygon, rect {
			cursor: default !important;
			user-select: none; /* opcional: evita selección de texto si no quieres que se marque */
		}
	</style>

	@if(auth()->check() && auth()->user()->isAdmin())
		<a href="{{ route('checkout') }}" class="btn btn-primary">
            Pagar ahora con Stripe
        </a>
	@endif

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

		
		</div>
	</div>
  @include('boletera.carrito')
@endsection

@push('scripts')
<script>
    window.isAdmin = @json(auth()->check() && auth()->user()->isAdmin());
</script>
	<script>
		let selector = @json($lot->modal_selector ?? 'svg g *');

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

	<script>
/**
 * Carrito: sincroniza con sesión en servidor, pero mantiene fallback en sessionStorage.
 * Estructura de item: { id, name, price, qty, selectorSVG (opcional) }
 */

const CART_URLS = {
    get: "{{ route('cart.get') }}",         // GET
    add: "{{ route('cart.add') }}",         // POST
    remove: "{{ route('cart.remove') }}",   // POST
    clear: "{{ route('cart.clear') }}",     // POST
    checkout: "{{ route('cart.checkout') }}"// POST -> crea session stripe
};

let cart = []; // local copy

// Formateo moneda (adaptar locale)
const money = (n) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n || 0);

// Render del carrito
function renderCart() {
    const $items = document.getElementById('cartItems');
    $items.innerHTML = '';

    if (!cart.length) {
        $items.innerHTML = '<p class="text-muted p-2">No hay items en el carrito</p>';
        document.getElementById('cartTotal').innerText = money(0);
        return;
    }

    cart.forEach((it) => {
        const row = document.createElement('div');
        row.className = 'd-flex align-items-center justify-content-between mb-2';
        row.innerHTML = `
            <div>
                <div class="fw-semibold">${it.name || 'Asiento ' + it.id}</div>
                <div class="text-muted small">${money(it.price)} x ${it.qty}</div>
            </div>
            <div class="d-flex flex-column align-items-end">
                <div>
                    <button class="btn btn-sm btn-light btn-decrease" data-id="${it.id}">-</button>
                    <button class="btn btn-sm btn-light btn-increase" data-id="${it.id}">+</button>
                </div>
                <button class="btn btn-sm btn-link text-danger mt-1 btn-remove" data-id="${it.id}">Quitar</button>
            </div>
        `;
        $items.appendChild(row);
    });

    const total = cart.reduce((s, i) => s + (Number(i.price || 0) * (i.qty || 1)), 0);
    document.getElementById('cartTotal').innerText = money(total);

    // Listeners
    document.querySelectorAll('.btn-remove').forEach(b => b.addEventListener('click', (e) => {
        const id = e.currentTarget.dataset.id;
        removeFromCartClient(id, true);
    }));
    document.querySelectorAll('.btn-increase').forEach(b => b.addEventListener('click', (e) => {
        const id = e.currentTarget.dataset.id;
        changeQtyClient(id, 1, true);
    }));
    document.querySelectorAll('.btn-decrease').forEach(b => b.addEventListener('click', (e) => {
        const id = e.currentTarget.dataset.id;
        changeQtyClient(id, -1, true);
    }));
}

// Sincronización con servidor (POST/GET) con CSRF
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

async function syncCartToServer() {
    try {
        const res = await fetch(CART_URLS.add, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ cart })
        });
        const data = await res.json();
        if (data.success) {
            // ok
            return true;
        }
    } catch (err) {
        console.warn('No se pudo sincronizar carrito al servidor, usando sessionStorage.', err);
    }
    // fallback: guardar localmente
    sessionStorage.setItem('svg_cart', JSON.stringify(cart));
    return false;
}

async function loadCartFromServerOrLocal() {
    try {
        const res = await fetch(CART_URLS.get, { headers: { 'X-CSRF-TOKEN': csrfToken } });
        if (res.ok) {
            const data = await res.json();
            if (Array.isArray(data.cart)) {
                cart = data.cart;
                renderCart();
                return;
            }
        }
    } catch (err) {
        console.warn('No se pudo obtener carrito del servidor', err);
    }

    // fallback sessionStorage
    const local = sessionStorage.getItem('svg_cart');
    if (local) {
        cart = JSON.parse(local);
        renderCart();
    }
}

function findCartIndex(id) {
    return cart.findIndex(i => String(i.id) === String(id));
}

// CLIENT-ONLY helpers that además llaman al servidor
async function addToCartClient(item, sync=true) {
    const idx = findCartIndex(item.id);
    if (idx >= 0) {
        cart[idx].qty = (cart[idx].qty || 1) + 1;
    } else {
        cart.push(Object.assign({ qty: 1 }, item));
    }
    renderCart();
    if (sync) await syncCartToServer();
}

async function removeFromCartClient(id, sync=true) {
    const idx = findCartIndex(id);
    if (idx >= 0) cart.splice(idx, 1);
    renderCart();
    if (sync) {
        await fetch(CART_URLS.remove, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ id })
        }).catch(()=> syncCartToServer());
    }
}

async function changeQtyClient(id, delta, sync=true) {
    const idx = findCartIndex(id);
    if (idx === -1) return;
    cart[idx].qty = Math.max(1, (cart[idx].qty || 1) + delta);
    renderCart();
    if (sync) await syncCartToServer();
}

// Manejo de selección SVG: al seleccionar un lote agregar al carrito; al deseleccionar quitar
function attachCartIntegrationToSelectionLogic() {
    // Supongo que cuando seleccionas tu código ya empuja info al array selectedLots.
    // Aquí vigilamos selectedLots y actualizamos carrito en consecuencia.
    // Si tu lógica ya llama explícitamente (p.ej. selectedLots.push(info)), puedes llamar addToCartClient(info) ahí.
    // Para ser más directo, interceptamos click handler existente: en tu código, cuando seleccionas se pushea `selectedLots.push(info)`
    // Vamos a escuchar cambios simples (no hay observer nativo para arrays): reemplazo simple: cada vez que pulses en un lote llamaremos a add/remove.
    // Modifica en tu handler de selección/deselección para llamar estas funciones. Ejemplo abajo.

    // --- NO HACER doble-binding si ya lo añadiste manualmente ---
}

// UI controls
document.getElementById('cartCloseBtn').addEventListener('click', () => {
    document.getElementById('cartSidebar').style.display = 'none';
});
document.getElementById('btnClearCart').addEventListener('click', async () => {
    cart = [];
    renderCart();
    try {
        await fetch(CART_URLS.clear, { method:'POST', headers: {'X-CSRF-TOKEN': csrfToken} });
        sessionStorage.removeItem('svg_cart');
    } catch (err) {
        sessionStorage.removeItem('svg_cart');
    }
});

document.getElementById('btnCheckout').addEventListener('click', async () => {
    if (!cart.length) return Swal.fire('Carrito vacío', 'Agrega al menos un asientos.', 'info');

    // Llamada a backend para crear Session de Stripe (o PaymentIntent) y redirigir
    try {
        const res = await fetch(CART_URLS.checkout, {
            method:'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
            body: JSON.stringify({ cart })
        });
        const data = await res.json();
        if (data.success && data.checkoutUrl) {
            window.location.href = data.checkoutUrl; // redirige a Stripe Checkout
        } else if (data.success && data.sessionId) {
            // ejemplo si quieres usar stripe.redirectToCheckout
            const stripe = Stripe(data.publishableKey);
            stripe.redirectToCheckout({ sessionId: data.sessionId });
        } else {
            Swal.fire('Error', data.message || 'No se pudo iniciar el pago', 'error');
        }
    } catch (err) {
        console.error(err);
        Swal.fire('Error', 'Ocurrió un error al iniciar el pago.', 'error');
    }
});

// ---------- Integración con tu selección actual ----------
// Modifica tu listener de click sobre el SVG para llamar a addToCartClient/removeFromCartClient.
// En tu código tienes esto (resumen): cuando se selecciona -> selectedLots.push(info) y pinta amarillos; cuando se deselecciona -> selectedLots.splice(...)
// Sustituye o añade llamadas a las funciones del carrito.

(function patchSelectionHandlers() {
    // Asumo que tus svgElements fueron declarados antes. Re-iteramos y sobrescribimos el handler para el admin.
    const svgElements = document.querySelectorAll(selector);
    svgElements.forEach(el => {
        // Evitar duplicar listeners: quitamos el existente si lo hay (si lo controlas en otro sitio, adapta esto)
        el.addEventListener('click', function (e) {
            if (!window.isAdmin) return;
            const clickedSVG = e.target.closest('g[id]');
            if (!clickedSVG) return;
            const info = clickedSVG.dataset.loteInfo ? JSON.parse(clickedSVG.dataset.loteInfo) : null;
            if (!info) return;

            const alreadyIdx = selectedLots.findIndex(l => l.id === info.id);
            if (alreadyIdx >= 0) {
                // deselección: quitar color y del carrito
                selectedLots.splice(alreadyIdx, 1);
                removeFromCartClient(info.id);
                // restaurar color basado en status (tu lógica original)
                const originalStatus = info.status;
                let fillColor;
                switch (originalStatus) {
                    case 'for_sale': fillColor = 'rgba(52, 199, 89, 0.4)'; break;
                    case 'sold': fillColor = 'rgba(200, 0, 0, 0.4)'; break;
                    case 'reserved': fillColor = 'rgba(255, 200, 0, 0.6)'; break;
                    default: fillColor = 'rgba(100, 100, 100, .9)';
                }
                clickedSVG.querySelectorAll('*').forEach(el => el.style.setProperty('fill', fillColor, 'important'));
                clickedSVG.style.setProperty('fill', fillColor, 'important');
                if (selectedLots.length === 0) selectionMode = null;
                return;
            }

            // selección: validar y agregar
            // ... mantén tu validación de selectionMode
            if (selectionMode === null) selectionMode = (info.status === 'for_sale') ? 'available' : 'sold';
            if (selectionMode === 'available' && (info.status === 'sold' || info.status === 'locked_sale')) {
                Swal.fire('No permitido', 'No puedes seleccionar lotes ocupados', 'warning');
                return;
            }

            clickedSVG.querySelectorAll('*').forEach(el => el.style.setProperty('fill', 'yellow', 'important'));
            selectedLots.push(info);

            // Agregar al carrito: usa campos que tengas en tu DB (name, price). Ajusta price si lo llamas distinto.
            const item = {
                id: info.id,
                name: info.name || info.nombre || ('Asiento ' + info.id),
                price: Number(info.price || info.precio || 0),
                selectorSVG: clickedSVG.id || null
            };
            addToCartClient(item);
        });
    });
})();

// carga inicial
document.addEventListener('DOMContentLoaded', loadCartFromServerOrLocal);
</script>
@endpush