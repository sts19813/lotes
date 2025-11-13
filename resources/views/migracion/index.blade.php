@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-bold text-gray-800">Migrar información (Adara → Naboo)</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="text-gray-600 mb-4">
                Esta herramienta permite conectar con el sistema <strong>Adara</strong> y migrar toda la información hacia
                <strong>Naboo</strong>.
            </p>

            <div class="mb-4">
                <button id="btnIniciarMigracion" class="btn btn-primary">
                    <i class="ki-outline ki-update fs-2 me-2"></i> Iniciar migración
                </button>
            </div>

            <div id="resultadoMigracion" class="alert d-none"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('btnIniciarMigracion').addEventListener('click', function () {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Migrando...';

            fetch("{{ route('migracion.importar') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
                .then(res => res.json())
                .then(data => {
                    const result = document.getElementById('resultadoMigracion');
                    result.classList.remove('d-none', 'alert-danger');
                    result.classList.add('alert-success');
                    result.innerHTML = `
                <strong>✅ Migración completada con éxito.</strong><br>
                Clientes: ${data.clientes_importados}<br>
                Proyectos: ${data.proyectos_importados}<br>
                Unidades: ${data.unidades_importadas}
            `;
                })
                .catch(err => {
                    const result = document.getElementById('resultadoMigracion');
                    result.classList.remove('d-none', 'alert-success');
                    result.classList.add('alert-danger');
                    result.innerHTML = `<strong>❌ Error en la migración:</strong> ${err.message}`;
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ki-outline ki-update fs-2 me-2"></i> Iniciar migración';
                });
        });
    </script>
@endpush