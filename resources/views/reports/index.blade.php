@extends('layouts.app')

@section('title', 'Reportes / Cotizaciones')

@section('content')

    <div class="card-header d-flex flex-wrap justify-content-between align-items-center py-5">
        <div class="card-title mb-0">
            <h3 class="fw-bold text-gray-800 mb-1">Reportes</h3>
            <span class="text-muted fs-7">Descarga las cotizaciones generadas por los posibles prospectos.</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="margin-top: -60px">
            <table id="reports_table" class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                <thead class="fs-7 text-gray-400 text-uppercase">
                    <tr>
                        <th>ID</th>
                        <th>Desarrollo</th>
                        <th>Phase ID</th>
                        <th>Stage ID</th>
                        <th>Nombre Del lote</th>
                        <th>Área</th>
                        <th>Precio Total</th>
                        <th>Lead</th>
                        <th>Fecha</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>
                                @if($report->desarrollo_name)
                                    {{ $report->desarrollo_name }} ({{ $report->desarrollo_id ?? '-' }})
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $report->phase_id ?? '—' }}</td>
                            <td>{{ $report->stage_id ?? '—' }}</td>
                            <td>{{ $report->name }}</td>
                            <td>{{ $report->area }} m²</td>
                            <td>${{ number_format($report->precio_total, 2) }}</td>
                            <td>{{ $report->lead_name }} <br><small>{{ $report->lead_email }}</small></td>

                            <td>{{ $report->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('reports.download', $report->id) }}" class="btn btn-sm btn-primary">
                                    <i class="ki-duotone ki-cloud-download"></i> Descargar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#reports_table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "language": { url: '//cdn.datatables.net/plug-ins/2.3.2/i18n/es-MX.json' },
                "dom": "<'row mb-3'<'col-12 d-flex justify-content-end'f>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'p>>",
                "order": [[0, "asce"]]
            });
        });
    </script>
@endpush