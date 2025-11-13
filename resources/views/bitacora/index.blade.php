@extends('layouts.app')

@section('title', 'Bitácora')

@section('content')
<div class="d-flex flex-column flex-column-fluid">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-bold text-gray-800">
            <i class="ki-outline ki-notepad fs-2 me-2 text-primary"></i>
            Bitácora del Sistema
        </h1>

        <button class="btn btn-primary d-flex align-items-center gap-2" id="btnFiltrar">
            <i class="ki-outline ki-filter fs-2 me-2"></i> Filtrar
        </button>
    </div>

    <!--begin::Card-->
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-3"></i>
                    <input type="text" id="searchBitacora" class="form-control form-control-solid w-250px ps-12"
                        placeholder="Buscar en bitácora..." />
                </div>
            </div>

            <div class="card-toolbar">
                <button class="btn btn-sm btn-light-success">
                    <i class="ki-outline ki-arrows-circle fs-2 me-1"></i>Actualizar
                </button>
            </div>
        </div>

        <div class="card-body pt-0">
            <table id="tablaBitacora" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Módulo</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 fw-semibold">
                    <tr>
                        <td>1</td>
                        <td>Juan Pérez</td>
                        <td>Creación</td>
                        <td>Proyectos</td>
                        <td>Registró un nuevo proyecto: “Residencial Aurora”.</td>
                        <td>2025-11-12 09:35:12</td>
                        <td>192.168.0.12</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>María López</td>
                        <td>Actualización</td>
                        <td>Clientes</td>
                        <td>Modificó el correo del cliente “Inmobiliaria del Sol”.</td>
                        <td>2025-11-12 10:02:18</td>
                        <td>192.168.0.15</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Admin</td>
                        <td>Eliminación</td>
                        <td>Usuarios</td>
                        <td>Eliminó el usuario inactivo “test_user”.</td>
                        <td>2025-11-11 22:15:45</td>
                        <td>10.0.0.8</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Juan Pérez</td>
                        <td>Inicio de sesión</td>
                        <td>Autenticación</td>
                        <td>El usuario inició sesión correctamente.</td>
                        <td>2025-11-10 08:41:22</td>
                        <td>192.168.0.12</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!--end::Card-->

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // Inicializar DataTable
        const tabla = $("#tablaBitacora").DataTable({
            responsive: true,
            pageLength: 10,
            lengthChange: false,
            ordering: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json"
            }
        });

        // Buscador personalizado
        $("#searchBitacora").on("keyup", function () {
            tabla.search(this.value).draw();
        });

        // Botón de ejemplo
        $("#btnFiltrar").on("click", function () {
            Swal.fire({
                title: 'Filtros de Bitácora',
                text: 'Aquí podrás aplicar filtros por usuario, módulo o fecha.',
                icon: 'info',
                confirmButtonText: 'Entendido',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        });
    });
</script>
@endpush
