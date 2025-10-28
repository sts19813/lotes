document.addEventListener("DOMContentLoaded", function() {
    // Inicializar DataTable
    let observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                observer.unobserve(img);
            }
        });
    }, { root: null, threshold: 0.1 });

    const table = $("#lots_table").DataTable({
        responsive: true,
        pageLength: 10,
        language: { url: '//cdn.datatables.net/plug-ins/2.3.2/i18n/es-MX.json' },
        dom: "<'row mb-3'<'col-12 d-flex justify-content-end'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'p>>",
        columns: [{}, {}, {}, {}, {}, {}, {}],

        drawCallback: function () {
            document.querySelectorAll("img.lazy-img:not([data-observed])")
                .forEach(img => {
                    img.dataset.observed = "true";
                    observer.observe(img);
                });
        }
    });

    const projectSelect = document.querySelector("select[name='project_id']");
    const phaseSelect = document.getElementById("phase_id");
    const stageSelect = document.getElementById("stage_id");

    projectSelect.addEventListener("change", function() {
        const projectId = this.value;
        phaseSelect.innerHTML = `<option value="">Cargando fases...</option>`;
        stageSelect.innerHTML = `<option value="">Seleccione una fase primero</option>`;

        if (!projectId) {
            phaseSelect.innerHTML = `<option value="">Seleccione un proyecto primero</option>`;
            return;
        }

        fetch(`/api/projects/${projectId}/phases`)
            .then(res => res.json())
            .then(data => {
                phaseSelect.innerHTML = `<option value="">Seleccione una fase...</option>`;
                data.forEach(phase => {
                    const opt = document.createElement("option");
                    opt.value = phase.id;
                    opt.textContent = phase.name;
                    phaseSelect.appendChild(opt);
                });
            });
    });

    phaseSelect.addEventListener("change", function() {
        const projectId = projectSelect.value;
        const phaseId = this.value;
        stageSelect.innerHTML = `<option value="">Cargando etapas...</option>`;

        if (!projectId || !phaseId) {
            stageSelect.innerHTML = `<option value="">Seleccione una fase primero</option>`;
            return;
        }

        fetch(`/api/projects/${projectId}/phases/${phaseId}/stages`)
            .then(res => res.json())
            .then(data => {
                stageSelect.innerHTML = `<option value="">Seleccione una etapa...</option>`;
                data.forEach(stage => {
                    const opt = document.createElement("option");
                    opt.value = stage.id;
                    opt.textContent = stage.name;
                    stageSelect.appendChild(opt);
                });
            });
    });

    // AJAX submit del formulario
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(window.Laravel.routes.lotsFetch, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                table.clear();
                data.forEach(lot => {
                    table.row.add([
                        lot.id,
                        lot.name,
                        lot.area,
                        `$${Number(lot.price_square_meter).toFixed(2)}`,
                        `$${Number(lot.total_price).toFixed(2)}`,
                        `<span class="badge ${lot.status==='available'?'badge-light-success':lot.status==='sold'?'badge-light-danger':'badge-light-warning'}">${lot.status.charAt(0).toUpperCase()+lot.status.slice(1)}</span>`,
                        lot.chepina ? `<img data-src="${lot.chepina}" loading="lazy" class="img-thumbnail lazy-img" style="width:80px;">`: ''
                    ]);
                });
                table.draw();
            })
            .catch(err => {
                console.error(err);
                alert("Error al cargar los lotes.");
            });
    });
});
