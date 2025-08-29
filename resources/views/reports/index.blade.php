@extends('layouts.app')

@section('title', 'Reportes / Cotizaciones')

@section('content')
<div class="app-container container-xxl">
    <div class="card mb-5">
        <div class="card-header">
            <h3 class="card-title">Cotizaciones Guardadas</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                    <thead class="fs-7 text-gray-400 text-uppercase">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Área</th>
                            <th>Precio Total</th>
                            <th>Lead</th>
                            <th>Ciudad</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->name }}</td>
                            <td>{{ $report->area }} m²</td>
                            <td>${{ number_format($report->precio_total, 2) }}</td>
                            <td>{{ $report->lead_name }} <br><small>{{ $report->lead_email }}</small></td>
                            <td>{{ $report->city }}</td>
                            <td>{{ $report->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('reports.download', $report->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="ki-duotone ki-cloud-download"></i> Descargar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
