@extends('layouts.app')

@section('title', 'Modelos de Financiamiento')

@section('content')

    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Modelos de Financiamiento</h3>
        <a href="{{ route('financiamientos.create') }}" class="btn btn-primary">
            <i class="ki-duotone ki-plus fs-2"></i> Nuevo Financiamiento
        </a>
    </div>

    <div class="card-body">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="tablaFinanciamientos">
            <thead>
                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Meses</th>
                    <th>Enganche (%)</th>
                    <th>Interés (%)</th>
                    <th>Descuento (%)</th>
                    <th>Desarrollos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold"></tbody>
        </table>
    </div>


@endsection
@push('scripts')
    <script>
        $(document).ready(function () {

            /**********************************************************
             * DATATABLE
             **********************************************************/
            const tabla = $('#tablaFinanciamientos').DataTable({
                ajax: '{{ route("financiamientos.data") }}',
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'financiamiento_meses' },
                    { data: 'porcentaje_enganche' },
                    { data: 'financiamiento_interes' },
                    { data: 'descuento_porcentaje' },
                    {
                        data: 'desarrollos',
                        render: data => {
                            if (!data || data.length === 0)
                                return '<span class="badge bg-secondary">Ninguno</span>';
                            return data.map(d => `<span class="badge bg-light-primary me-1">${d.name}</span>`).join('');
                        },
                        width: '300px'
                    },
                    {
                        data: null,
                        render: row => `
                        <a href="/financiamientos/${row.id}/edit" class="btn btn-sm btn-light-danger me-2" title="Editar">
                            <span class="svg-icon svg-icon-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 21h4l11-11-4-4L4 17v4z" fill="currentColor"/>
                                    <path opacity="0.3" d="M14.828 6.172l3.586 3.586-1.414 1.414-3.586-3.586 1.414-1.414z" fill="currentColor"/>
                                </svg>
                            </span>
                        </a>
                        <button class="btn btn-sm btn-light-danger btnEliminar" data-id="${row.id}" title="Eliminar">
                            <span class="svg-icon svg-icon-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 6h14l-1 14H6L5 6z" fill="currentColor"/>
                                    <path opacity="0.3" d="M9 10h2v6H9v-6zm4 0h2v6h-2v-6z" fill="currentColor"/>
                                    <path d="M15 4V3H9v1H4v2h16V4h-5z" fill="currentColor"/>
                                </svg>
                            </span>
                        </button>
                    `,
                        width: '150px'
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });



            /**********************************************************
             * ELIMINAR FINANCIAMIENTO
             **********************************************************/
            $('#tablaFinanciamientos').on('click', '.btnEliminar', function () {
                const id = $(this).data('id');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/financiamientos/${id}`,
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function () {
                                tabla.ajax.reload();
                                Swal.fire('Eliminado', 'El financiamiento ha sido eliminado.', 'success');
                            },
                            error: function () {
                                Swal.fire('Error', 'No se pudo eliminar el financiamiento.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush