@extends('layouts.app')

@section('title', 'Administrar Usuarios')

@section('content')
<div class="d-flex flex-column flex-column-fluid">

    <!-- Header -->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <h1 class="page-heading d-flex text-dark fw-bold fs-2">Usuarios</h1>
        </div>
    </div>

    <!-- Content -->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-fluid">

            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title">Lista de Usuarios</h3>
                </div>

                <div class="card-body">

                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="users_table">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Acceso al sistema</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="fw-semibold text-gray-600">
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>

                                    <td>
                                        <span class="badge badge-light-primary">{{ $user->role }}</span>
                                    </td>

                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge badge-light-success">Sí</span>
                                        @else
                                            <span class="badge badge-light-danger">No</span>
                                        @endif
                                    </td>

                                    <td class="text-end">

                                        <button class="btn btn-light btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-role="{{ $user->role }}"
                                            data-admin="{{ $user->is_admin }}">
                                            Editar
                                        </button>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered mw-450px">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="modal-title">Editar Usuario</h3>
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </button>
            </div>

            <div class="modal-body">

                <form id="editUserForm">
                    @csrf

                    <input type="hidden" id="edit_user_id">

                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Rol</label>
                        <select class="form-select" id="edit_role">
                            <option value="client">Admin</option>
                            <option value="admin">Super Admin</option>
                        </select>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fw-semibold fs-6 mb-2">Acceso al sistema</label>
                        <select class="form-select" id="edit_is_admin">
                            <option value="0">No</option>
                            <option value="1">Sí</option>
                        </select>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    // Llenar Modal
    $('#editUserModal').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget);

        $('#edit_user_id').val(button.data('id'));
        $('#edit_role').val(button.data('role'));
        $('#edit_is_admin').val(button.data('admin'));
    });

    // Guardar Cambios
    $('#editUserForm').submit(function(e) {
        e.preventDefault();

        let id = $('#edit_user_id').val();

        $.ajax({
            url: "/users/update-role/" + id,
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                role: $('#edit_role').val(),
                is_admin: $('#edit_is_admin').val()
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Actualizado',
                    text: response.message,
                }).then(() => location.reload());
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar'
                });
            }
        });
    });
</script>
@endpush
