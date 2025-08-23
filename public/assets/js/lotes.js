const tabs = document.querySelectorAll('.switch-tabs .btn');
const contents = document.querySelectorAll('.tab-content > div');
const redirectCheckbox = document.getElementById('redirect');
const redirectUrlInput = document.getElementById('redirect_url');
const polygonForm = document.getElementById('polygonForm');


redirectCheckbox.addEventListener('change', function() {
    redirectUrlInput.disabled = !this.checked;
    if (!this.checked) redirectUrlInput.value = '';
});



tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        // Alternar botón activo
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        // Alternar contenido
        const target = tab.getAttribute('data-tab');
        contents.forEach(c => {
            c.classList.remove('active');
            if (c.id === target) c.classList.add('active');
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const projectSelect = document.getElementById("modal_project_id");
    const phaseSelect = document.getElementById("modal_phase_id");
    const stageSelect = document.getElementById("modal_stage_id");
    const lotSelect = document.getElementById('modal_lot_id');


    const modalEl = document.getElementById('polygonModal');
    polygonModal = new bootstrap.Modal(modalEl); // se crea una sola instancia

    // 1️⃣ Detectar click sobre polygons/path con clase .cls-1
    const svgElements = document.querySelectorAll(selector);
    svgElements.forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();

            // Priorizar el id del elemento actual
            let elementId = (this.id && this.id.trim() !== "") ? this.id : null;

            // Si no tiene id, buscar en el padre <g>
            if (!elementId) {
                const parentG = this.closest("g");
                if (parentG && parentG.id && parentG.id.trim() !== "") {
                    elementId = parentG.id;
                }
            }

            // Mostrar solo si hay un id válido
            if (elementId) {
                document.getElementById('selectedElementId').innerText = elementId;
                document.getElementById('polygonId').value = elementId;
                polygonModal.show();
            }
        });
    });

    // 2️⃣ Cargar fases según proyecto
    projectSelect.addEventListener("change", function () {
        const projectId = this.value;
        phaseSelect.innerHTML = `<option value="">Cargando fases...</option>`;
        stageSelect.innerHTML = `<option value="">Seleccione una fase primero</option>`;
        if (!projectId) return;

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

    // 3️⃣ Cargar stages según fase
    phaseSelect.addEventListener("change", function () {
        const projectId = projectSelect.value;
        const phaseId = this.value;
        stageSelect.innerHTML = `<option value="">Cargando etapas...</option>`;
        if (!projectId || !phaseId) return;

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



    stageSelect.addEventListener("change", function () {
        const projectId = projectSelect.value;
        const phaseId = phaseSelect.value;
        const stageId = stageSelect.value;
    
        lotSelect.innerHTML = `<option value="">Cargando lotes...</option>`;
    
        if (!projectId || !phaseId || !stageId) {
            lotSelect.innerHTML = `<option value="">Seleccione primero proyecto, fase y etapa</option>`;
            return;
        }
    
        // Llamada al endpoint que ya tienes
        const formData = new FormData();
        formData.append('project_id', projectId);
        formData.append('phase_id', phaseId);
        formData.append('stage_id', stageId);
    
        fetch(window.Laravel.routes.lotsFetch, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            lotSelect.innerHTML = `<option value="">Seleccione un lote...</option>`;
            data.forEach(lot => {
                const opt = document.createElement("option");
                opt.value = lot.id; // o lot.lote_id si quieres
                opt.textContent = lot.name; // o el campo que quieras mostrar
                lotSelect.appendChild(opt);
            });
        })
        .catch(err => {
            console.error(err);
            lotSelect.innerHTML = `<option value="">Error al cargar lotes</option>`;
        });
    });


    polygonForm.addEventListener('submit', function(e) {
        e.preventDefault();
    debugger
        const formData = new FormData(this);
    
        if(window.idDesarrollo) {
            formData.append('desarrollo_id', window.idDesarrollo);
        }
        
        fetch(window.Laravel.routes.lotesStore, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken
            }
        })
        .then(async res => {
            const text = await res.text(); // primero leemos como texto
            try {
                return JSON.parse(text); // intentamos parsear JSON
            } catch {
                throw new Error('Respuesta no es JSON: ' + text); // lanzamos error si es HTML
            }
        })
        .then(data => {
            if(data.success) {
                alert('Lote guardado correctamente');
                polygonModal.hide();
                polygonForm.reset();
                lotSelect.innerHTML = `<option value="">Seleccione un lote...</option>`;
            } else {
                alert('Error: ' + (data.message || 'No se pudo guardar'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Ocurrió un error al guardar el lote. Revisa la consola.');
        });
    });
});