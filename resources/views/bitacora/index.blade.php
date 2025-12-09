@extends('layouts.app')

@section('title', 'Bitácora')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">

        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center mb-7">
            <h1 class="fw-bold text-gray-800 mb-0">
                <i class="ki-outline ki-notepad fs-2 me-2 text-primary"></i>
                Bitácora del Sistema
            </h1>


            <button class="btn btn-sm btn-light-success actualziar-bitacora" id="btnFiltrar">
                <i class="ki-outline ki-arrows-circle fs-2 me-1"></i>Actualizar
            </button>

        </div>


        <!-- LARAVEL LOGS -->
        <div class="card shadow-sm mt-10 mb-10">
            <div class="card-header border-0 pt-7 pb-4">
                <h3 class="fw-bold text-gray-800 fs-3">
                    <i class="ki-outline ki-alert fs-2 me-2 text-danger"></i>
                    Log del sistema
                </h3>
            </div>

            <div class="card-body pt-0">
                <table id="tablaLaravel" class="table table-sm table-hover table-row-dashed align-middle fs-7 gy-4">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold text-uppercase">
                            <th>Fecha</th>
                            <th>Nivel</th>
                            <th>Mensaje</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach($laravelLogs as $log)
                            <tr>
                                <td>{{ $log['datetime'] }}</td>
                                <td>
                                    <span class="badge 
                                                                @if($log['level'] === 'ERROR') bg-danger 
                                                                @elseif($log['level'] === 'WARNING') bg-warning 
                                                                @else bg-info @endif
                                                            ">
                                        {{ $log['level'] }}
                                    </span>
                                </td>
                                <td style="white-space: pre-wrap">{{ $log['message'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MIGRATION LOGS -->
        <div class="card shadow-sm mb-10">
            <div class="card-header border-0 pt-7 pb-4">
                <h3 class="fw-bold text-gray-800 fs-3">
                    <i class="ki-outline ki-shield fs-2 me-2 text-warning"></i>
                    Informe de Migración
                </h3>
            </div>

            <div class="card-body pt-0">
                <table id="tablaMigracion" class="table table-sm table-hover table-row-dashed align-middle fs-7 gy-4">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold text-uppercase">
                            <th>Fecha</th>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Estatus</th>
                            <th>Mensaje</th>
                        </tr>
                    </thead>

                    <tbody class="text-gray-700 fw-semibold">
                        @foreach($migrationLogs as $log)
                            <tr>
                                <td>{{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '' }}</td>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->type }}</td>
                                <td>{{ $log->origin_id }}</td>
                                <td>{{ $log->target_id }}</td>
                                <td>{{ $log->status }}</td>
                                <td>{{ $log->message }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>



    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            // Tabla de migración
            $("#tablaMigracion").DataTable({
                responsive: true,
                pageLength: 10,
                lengthChange: false,
                language: { url: "//cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json" },
                columnDefs: [
                    { width: "180px", targets: 0 } // Aumenta el ancho de la columna fecha
                ],
                order: [[0, "desc"]], 
            });

            // Tabla del log de Laravel
            $("#tablaLaravel").DataTable({
                responsive: true,
                pageLength: 5,
                lengthChange: false,
                language: { url: "//cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json" },

                columnDefs: [
                    { width: "180px", targets: 0 } // Aumenta el ancho de la columna fecha
                ],
                order: [[0, "desc"]]
            });

            // Ventana de filtros
            $("#btnFiltrar").on("click", function () {
                window.location.reload();
            });
        });
    </script>
@endpush