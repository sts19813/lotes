@component('mail::message')
# Cotización generada

Hola **{{ $lot->lead_name ?? 'Cliente' }}**,

Se ha generado tu cotización con los siguientes datos:

- **Nombre del lote:** {{ $lot->name }}
- **Área:** {{ $lot->area }} m²
- **Precio total:** ${{ number_format($lot->precioTotal, 2) }}

{{-- ✅ NUEVOS CAMPOS --}}
@if(isset($lot->desarrollo_name))
- **Desarrollo:** {{ $lot->desarrollo_name }} (ID: {{ $lot->desarrollo_id ?? '-' }})
@endif

@if(isset($lot->phase_id))
- **Phase ID:** {{ $lot->phase_id }}
@endif

@if(isset($lot->stage_id))
- **Stage ID:** {{ $lot->stage_id }}
@endif

Adjunto encontrarás el PDF con el detalle completo.

Gracias por confiar en nosotros.  

Saludos
@endcomponent
