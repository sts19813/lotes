document.addEventListener('DOMContentLoaded', function () {
    const projectSelect = document.getElementById("project_id");
    const phaseSelect = document.getElementById("phase_id");
    const stageSelect = document.getElementById("stage_id");

    // Limpiar y bloquear selects
    function resetSelect(select, message) {
        select.innerHTML = `<option value="">${message}</option>`;
        select.disabled = true;
    }

    // Al cambiar proyecto -> Cargar fases
    projectSelect.addEventListener("change", function () {
        const projectId = this.value;
        resetSelect(phaseSelect, "Seleccione un proyecto primero");
        resetSelect(stageSelect, "Seleccione una fase primero");

        if (!projectId) return;

        phaseSelect.innerHTML = `<option value="">Cargando fases...</option>`;

        fetch(`/api/projects/${projectId}/phases`)
            .then(response => response.json())
            .then(data => {
                phaseSelect.innerHTML = `<option value="">Seleccione una fase...</option>`;
                data.forEach(phase => {
                    phaseSelect.innerHTML += `<option value="${phase.id}">${phase.name}</option>`;
                });
                phaseSelect.disabled = false;
            })
            .catch(() => {
                resetSelect(phaseSelect, "Error al cargar fases");
            });
    });

    // Al cambiar fase -> Cargar etapas
    phaseSelect.addEventListener("change", function () {
        const projectId = projectSelect.value;
        const phaseId = this.value;

        resetSelect(stageSelect, "Seleccione una fase primero");

        if (!projectId || !phaseId) return;

        stageSelect.innerHTML = `<option value="">Cargando etapas...</option>`;

        fetch(`/api/projects/${projectId}/phases/${phaseId}/stages`)
            .then(response => response.json())
            .then(data => {
                stageSelect.innerHTML = `<option value="">Seleccione una etapa...</option>`;
                data.forEach(stage => {
                    stageSelect.innerHTML += `<option value="${stage.id}">${stage.name}</option>`;
                });
                stageSelect.disabled = false;
            })
            .catch(() => {
                resetSelect(stageSelect, "Error al cargar etapas");
            });
    });

});
