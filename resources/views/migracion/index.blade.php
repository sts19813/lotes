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

            <div class="mb-4">
                <button id="btnDescargarChepinas" class="btn btn-primary">
                    <i class="ki-outline ki-image fs-2 me-2"></i> migrar imágenes (Chepinas)
                </button>
            </div>

            <div id="progresoChepinas" class="alert d-none"></div>
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
                body: "{}"
            });

            // Monitorear progreso cada 2 segundos
            const intervalo = setInterval(() => {
                fetch("{{ route('migracion.progreso') }}")
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('resultadoMigracion').classList.remove('d-none');
                        document.getElementById('resultadoMigracion').innerHTML = `
                                    <strong>Progreso actual:</strong><br>
                                    Proyectos: ${data.projects}<br>
                                    Fases: ${data.phases}<br>
                                    Etapas: ${data.stages}<br>
                                    Lotes: ${data.lots}
                                `;

                        if (data.done) {
                            clearInterval(intervalo);

                            btn.innerHTML = '✔ Migración completada';
                            btn.classList.remove('btn-primary');
                            btn.classList.add('btn-success');

                            document.getElementById('resultadoMigracion').classList.add('alert-success');
                            document.getElementById('resultadoMigracion').innerHTML += "<br><strong>✔ Migración finalizada correctamente.</strong>";
                        }

                    });
            }, 2000);

        });


        document.getElementById('btnDescargarChepinas').addEventListener('click', function () {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Descargando...';

            fetch("{{ route('migracion.descargarChepinas') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: "{}"
            });

            // Monitorear progreso
            const intervalo = setInterval(() => {
                fetch("{{ route('migracion.progresoChepinas') }}")
                    .then(res => res.json())
                    .then(data => {

                        const alertBox = document.getElementById('progresoChepinas');
                        alertBox.classList.remove('d-none');

                        alertBox.innerHTML = `
                        <strong>Descargando chepinas:</strong><br>
                        ${data.actual} / ${data.total}<br>
                        <div class="progress mt-2">
                            <div class="progress-bar" role="progressbar" style="width:${data.porcentaje}%;">
                                ${data.porcentaje}%
                            </div>
                        </div>
                    `;

                        if (data.finalizado) {
                            clearInterval(intervalo);
                            btn.innerHTML = '✔ Chepinas descargadas';
                            btn.classList.remove('btn-warning');
                            btn.classList.add('btn-success');
                        }
                    });
            }, 1500);
        });

    </script>
@endpush