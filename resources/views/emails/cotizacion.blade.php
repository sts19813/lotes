@component('mail::message')
# Cotización generada

Hola **{{ $lot->lead_name ?? 'Cliente' }}**,

Se ha generado tu cotización con los siguientes datos:

- **Nombre del lote:** {{ $lot->name }}
- **Área:** {{ $lot->area }} m²
- **Precio total:** ${{ number_format($lot->precioTotal, 2) }}
- **Ciudad:** {{ $lot->city }}

Adjunto encontrarás el PDF con el detalle completo.

Gracias por confiar en nosotros.  

Saludos 
@endcomponent
