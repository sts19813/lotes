document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById("project_id");
    const phaseSelect = document.getElementById("phase_id");
    const stageSelect = document.getElementById("stage_id");

    // Al cambiar el proyecto
    projectSelect.addEventListener("change", function() {
        const projectId = this.value;
        phaseSelect.innerHTML = `<option value="">Cargando fases...</option>`;
        stageSelect.innerHTML = `<option value="">Seleccione una fase primero</option>`;
        phaseSelect.disabled = true;
        stageSelect.disabled = true;

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
                phaseSelect.disabled = false;
            });
    });

    // Al cambiar la fase
    phaseSelect.addEventListener("change", function() {
        const projectId = projectSelect.value;
        const phaseId = this.value;
        stageSelect.innerHTML = `<option value="">Cargando etapas...</option>`;
        stageSelect.disabled = true;

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
                stageSelect.disabled = false;
            });
    });
});