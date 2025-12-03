<html>
<head>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .contenedor { width: 400px; margin: 40px auto; }
        .item { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .total { font-weight: bold; font-size: 18px; margin-top: 15px; }
    </style>
</head>
<body>
<div class="contenedor">

    <h2>Resumen de compra</h2>

    @foreach ($carrito as $item)
        <div class="item">
            <span>{{ $item['nombre'] }} (x{{ $item['cantidad'] }})</span>
            <span>${{ number_format($item['precio'] / 100, 2) }}</span>
        </div>
    @endforeach

    <hr>

    <div class="item">
        <span>Subtotal:</span>
        <span>${{ number_format($subtotal / 100, 2) }}</span>
    </div>

    <div class="item">
        <span>Comisión:</span>
        <span>${{ number_format($comision / 100, 2) }}</span>
    </div>

    <div class="total">
        Total a pagar: ${{ number_format($total / 100, 2) }} MXN
    </div>

    <hr><br>

    <h2>Pagar con Tarjeta</h2>

    <form id="payment-form">
        <div id="payment-element"></div>

        <button id="submit" style="margin-top:20px;">
            Pagar Ahora
        </button>

        <div id="message"></div>
    </form>

</div>

<script>
    const stripe = Stripe("{{ env('STRIPE_KEY') }}");

    const elements = stripe.elements({
        clientSecret: "{{ $clientSecret }}"
    });

    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    const form = document.getElementById('payment-form');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const result = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: "{{ url('/pago/success') }}",
            },
        });

        if (result.error) {
            document.getElementById('message').textContent = result.error.message;
        }
    });
</script>

</body>
</html>
