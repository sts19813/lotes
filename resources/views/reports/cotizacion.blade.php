<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cotización #{{ $lot->name }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#222; margin: 25px; }
        h1 { font-size: 24px; margin-bottom: 10px; }
        .row { display: flex; width: 100%; gap: 20px; margin-bottom: 12px; }
        .col { flex: 1; }
        table { width:100%; border-collapse: collapse; margin-bottom: 10px; }
        table th, table td { border: 1px solid #d0cfcf; padding: 8px; vertical-align: middle; }
        .table-light th { background:#f8f8f8; font-weight:600; }
        .purple-header { background:#9a7f91; color:#fff; font-weight:700; text-align:center; padding:10px; }
        .summary-box { border:1px solid #d0cfcf; padding:8px; background:#faf7fb; }
        .small { font-size: 11px; }
        .big-right { text-align:right; font-weight:700; }
        .table-plusvalia th { background:#9a7f91; color:#fff; padding:10px; }
        .text-success { color: #2a8f48; }
        .text-primary { color:#3b6fb8; }
        .footnotes { font-size:10px; margin-top:8px; color:#666; }
        .logo { width:140px; }
    </style>
</head>
<body>
    <h1>Cotización</h1>

    <div class="row">
        <div class="col">
            <table>
                <thead class="table-light"><tr><th colspan="2">Datos del Cliente</th></tr></thead>
                <tbody>
                    <tr><td>Nombre</td><td>{{ $lot->lead_name ?? '—' }}</td></tr>
                    <tr><td>Teléfono</td><td>{{ $lot->lead_phone ?? '—' }}</td></tr>
                    <tr><td>Correo</td><td>{{ $lot->lead_email ?? '—' }}</td></tr>
                    <tr><td>Ciudad</td><td>{{ $lot->city ?? '—' }}</td></tr>
                </tbody>
            </table>
        </div>

        <div class="col">
            <table>
                <thead class="table-light"><tr><th colspan="2">Información del Lote</th></tr></thead>
                <tbody>
                    <tr><td>Lote</td><td>{{ $lot->name }}</td></tr>
                    <tr><td>Área</td><td>{{ number_format($lot->area,2) }} m²</td></tr>
                    <tr><td>Precio m²</td><td>${{ number_format($lot->price_square_meter,2) }}</td></tr>
                    <tr><td>Precio Total</td><td>${{ number_format($lot->precioTotal,2) }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <h3>RESUMEN</h3>
    <table>
        <thead><tr><th class="purple-header" colspan="3">Enganche / Financiamiento / Monto Financiado</th></tr></thead>
        <tbody>
            <tr>
                <td>{{ $lot->enganchePorc }}% - ${{ number_format($lot->engancheMonto,2) }}</td>
                <td>{{ $lot->meses }} Meses</td>
                <td>${{ number_format($lot->precioTotal - $lot->engancheMonto,2) }}</td>
            </tr>
        </tbody>
    </table>

    <table>
        <thead><tr><th class="purple-header" colspan="2">Mensualidad / Costo Total</th></tr></thead>
        <tbody>
            <tr><td>${{ number_format($lot->mensualidad,2) }}</td><td>${{ number_format($lot->precioTotal,2) }}</td></tr>
        </tbody>
    </table>

    <h3>Simulador de Plusvalía (5 años)</h3>
    <div class="row">
        <div class="col summary-box">
            <div class="purple-header">Plusvalía Total</div>
            <div style="padding:8px; text-align:center; font-weight:700;">${{ number_format($lot->plusvaliaTotal - $lot->precioTotal,2) }}</div>
        </div>
        <div class="col summary-box">
            <div class="purple-header">ROI Proyectado</div>
            <div style="padding:8px; text-align:center; font-weight:700;">{{ number_format($lot->roi,2) }}%</div>
        </div>
    </div>

    <h3>Tabla de Plusvalía</h3>
    <table class="table-plusvalia">
        <thead>
            <tr>
                <th>Año</th>
                <th>Valor Propiedad</th>
                <th>Monto Pagado</th>
                <th>Plusvalía Acumulada</th>
                <th>ROI (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lot->years as $r)
                <tr>
                    <td>{{ $r['year'] }}</td>
                    <td>${{ number_format($r['valorProp'],2) }}</td>
                    <td>${{ number_format($r['montoPagado'],2) }}</td>
                    <td class="{{ $r['plusvaliaAcum'] > 0 ? 'text-success' : '' }}">+${{ number_format($r['plusvaliaAcum'],2) }}</td>
                    <td class="{{ $r['roiAnual'] > 0 ? 'text-primary' : '' }}">{{ number_format($r['roiAnual'],2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footnotes">
        <p>• Los cálculos consideran el plan de financiamiento y la ubicación.</p>
        <p>• Esta es una proyección basada en tendencias históricas del mercado.</p>
    </div>

    <div style="margin-top:20px; text-align:center;">
        <img src="{{ $lot->chepinaUrl }}" class="logo">
    </div>
</body>
</html>
