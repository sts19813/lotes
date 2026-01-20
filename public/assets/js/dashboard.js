document.addEventListener("DOMContentLoaded", function () {

    const filterForm = document.getElementById("dashboardFilter");
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const phaseSelect = document.getElementById("phase_id");
    const stageSelect = document.getElementById("stage_id");
    const projectSelect = document.getElementById("project_id");

    const sourceSelect = filterForm.querySelector('[name="source"]');

    sourceSelect.addEventListener('change', () => {
        if (!sourceSelect.value) {
            projectSelect.innerHTML = `<option value="">Todos...</option>`;
            phaseSelect.innerHTML = `<option value="">Todas...</option>`;
            stageSelect.innerHTML = `<option value="">Todas...</option>`;
            return;
        }

        loadProjects().finally(fetchDashboardData);
    });


    let chart = Highcharts.chart('lotsChart', {
        chart: { type: 'column' },
        title: { text: 'Distribución de Estatus' },
        xAxis: { categories: ['Disponible', 'Vendido', 'Apartado', 'Bloqueado', 'Total'] },
        series: [{ data: [0, 0, 0, 0, 0] }]
    });

    function loadProjects() {
        const source = filterForm.querySelector('[name="source"]').value;

        projectSelect.innerHTML = `<option value="">Todos...</option>`;
        phaseSelect.innerHTML = `<option value="">Todas...</option>`;
        stageSelect.innerHTML = `<option value="">Todas...</option>`;

        return fetch(`/api/dashboard/projects?source=${source}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(p => {
                    projectSelect.innerHTML +=
                        `<option value="${p.id}">${p.name}</option>`;
                });
            });
    }


    function loadPhases(projectId) {
        const source = filterForm.querySelector('[name="source"]').value;

        return fetch(`/api/dashboard/phases?project_id=${projectId}&source=${source}`)
            .then(r => r.json())
            .then(data => {
                phaseSelect.innerHTML = `<option value="">Todas...</option>`;
                data.forEach(f =>
                    phaseSelect.innerHTML += `<option value="${f.id}">${f.name}</option>`
                );
            });
    }

    function loadStages(projectId, phaseId) {
        const source = filterForm.querySelector('[name="source"]').value;

        return fetch(`/api/dashboard/stages?project_id=${projectId}&phase_id=${phaseId}&source=${source}`)
            .then(r => r.json())
            .then(data => {
                stageSelect.innerHTML = `<option value="">Todas...</option>`;
                data.forEach(s =>
                    stageSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`
                );
            });
    }

    projectSelect.addEventListener("change", () => {
        let id = projectSelect.value;
        phaseSelect.innerHTML = stageSelect.innerHTML = `<option value="">Todas...</option>`;
        if (id) loadPhases(id).finally(fetchDashboardData);
        else fetchDashboardData();
    });

    phaseSelect.addEventListener("change", () => {
        let phaseId = phaseSelect.value;
        if (!phaseId) return fetchDashboardData();
        loadStages(projectSelect.value, phaseId).finally(fetchDashboardData);
    });

    stageSelect.addEventListener("change", fetchDashboardData);

    filterForm.addEventListener("submit", e => {
        e.preventDefault();
        fetchDashboardData();
    });

    function fetchDashboardData() {
        const source = filterForm.querySelector('[name="source"]').value;

        if (!source) {
            return;
        }
        document.body.classList.add("loading");

        fetch("/dashboards/data", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            body: new FormData(filterForm)
        })
            .then(r => r.json())
            .then(updateUI)
            .catch(err => console.error(err))
            .finally(() => document.body.classList.remove("loading"));
    }

    function updateUI(data) {
        document.getElementById('card_total').textContent = data.total;
        document.getElementById('card_available').textContent = data.available;
        document.getElementById('card_sold').textContent = data.sold;
        document.getElementById('card_reserved').textContent = data.reserved;
        document.getElementById('card_blocked').textContent = data.blocked;

        chart.series[0].setData([
            data.available, data.sold, data.reserved, data.blocked, data.total
        ]);

        const tbody = document.querySelector("#resume_table tbody");
        tbody.innerHTML = "";

        if (!data.resume?.length) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center text-gray-500">Sin resultados</td></tr>`;
            return;
        }

        data.resume.forEach(r => {
            tbody.innerHTML += `
            <tr>
                <td>${r.project}</td>
                <td>${r.phase ?? '-'}</td>
                <td>${r.stage ?? '-'}</td>
                <td>${r.total}</td>
                <td class="text-success">${r.available}</td>
                <td class="text-danger">${r.sold}</td>
                <td class="text-warning">${r.reserved}</td>
                <td class="text-info">${r.blocked}</td>
            </tr>`;
        });
    }

});
