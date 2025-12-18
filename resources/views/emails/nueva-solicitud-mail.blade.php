<h2>Nueva Solicitud de Evento</h2>

<hr>

<h4>Datos del contacto</h4>
<p><strong>Nombre:</strong> {{ $lead->name }}</p>
<p><strong>Empresa:</strong> {{ $lead->company ?? '—' }}</p>
<p><strong>Email:</strong> {{ $lead->email }}</p>
<p><strong>Teléfono:</strong> {{ $lead->phone }}</p>

<hr>

<h4>Información del evento</h4>
<p><strong>Tipo de evento:</strong> {{ $lead->event_type ?? '—' }}</p>
<p><strong>Fecha estimada:</strong> 
    {{ $lead->estimated_date ? \Carbon\Carbon::parse($lead->estimated_date)->format('d/m/Y') : '—' }}
</p>

<p><strong>Mensaje:</strong></p>
<p>{{ $lead->message ?? '—' }}</p>

<hr>



<p><strong>Salón seleccionado:</strong> {{ $lead->lots }}</p>
