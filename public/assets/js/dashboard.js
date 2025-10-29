document.addEventListener("DOMContentLoaded", function () {

 // === VARIABLES GLOBALES ===
 const filterForm = document.getElementById("dashboardFilter");
 const phaseSelect = document.getElementById("phase_id");
 const stageSelect = document.getElementById("stage_id");
 const projectSelect = document.getElementById("project_id");

 const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

 // === HIGHCHART === ✅ — Se agrega "Bloqueado"
 let chart = Highcharts.chart('lotsChart', {
  chart: { type: 'column' },
  title: { text: 'Distribución de Estatus' },
  xAxis: { categories: ['Disponible', 'Vendido', 'Apartado', 'Bloqueado'] },
  series: [{ name: 'Lotes', data: [0, 0, 0, 0] }]
 });

 // === FILTROS DINÁMICOS ===
 projectSelect.addEventListener("change", function () {
  let projectId = this.value;

  phaseSelect.innerHTML = `<option value="">Todas...</option>`;
  stageSelect.innerHTML = `<option value="">Todas...</option>`;

  if (!projectId) return fetchDashboardData();

  fetch(`/api/phases?project_id=${projectId}`)
   .then(res => res.json())
   .then(data => {
    data.forEach(f => {
     phaseSelect.innerHTML += `<option value="${f.id}">${f.name}</option>`;
    });
   }).catch(console.error);

  fetchDashboardData();
 });

 phaseSelect.addEventListener("change", function () {
  let phaseId = this.value;
  let projectId = projectSelect.value;

  stageSelect.innerHTML = `<option value="">Todas...</option>`;

  if (!phaseId) return fetchDashboardData();

  fetch(`/api/stages?project_id=${projectId}&phase_id=${phaseId}`)
   .then(res => res.json())
   .then(data => {
    data.forEach(s => {
     stageSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
    });
   }).catch(console.error);

  fetchDashboardData();
 });

 stageSelect.addEventListener("change", fetchDashboardData);

 // ✅ Evitar recarga
 filterForm.addEventListener('submit', function (e) {
  e.preventDefault();
  fetchDashboardData();
 });

 // === LLAMADO AL BACKEND ===
 function fetchDashboardData() {
  const formData = new FormData(filterForm);

  document.body.classList.add('loading');

  fetch("/dashboards/data", {
   method: "POST",
   headers: { "X-CSRF-TOKEN": csrfToken },
   body: formData
  })
   .then(res => res.json())
   .then(data => {
    updateCards(data);
    updateChart(data);
    updateTable(data);
   })
   .catch(err => console.error("❌ Error en Dashboard:", err))
   .finally(() => document.body.classList.remove('loading'));
 }

 // === ACTUALIZAR CARDS === ✅ — Se agrega bloqueados
 function updateCards(data) {
  document.getElementById('card_total').textContent = data.total || 0;
  document.getElementById('card_available').textContent = data.available || 0;
  document.getElementById('card_unavailable').textContent = (data.sold || 0) + (data.reserved || 0);
  document.getElementById('card_blocked').textContent = data.blocked || 0;
 }

 // === ACTUALIZAR GRAFICA === ✅ — ahora 4 valores
 function updateChart(data) {
  chart.series[0].setData([
   data.available || 0,
   data.sold || 0,
   data.reserved || 0,
   data.blocked || 0 // ✅
  ]);
 }

 // === TABLA === ✅ — Se agrega columna Bloqueado
 function updateTable(data) {
  const tbody = document.querySelector("#resume_table tbody");
  tbody.innerHTML = "";

  if (!data.resume || data.resume.length === 0) {
   tbody.innerHTML = `<tr>
                <td class="text-center text-gray-500" colspan="8">Sin resultados</td>
            </tr>`;
   return;
  }

  data.resume.forEach(row => {
   tbody.innerHTML += `
                <tr>
                    <td>${row.project}</td>
                    <td>${row.phase ?? '-'}</td>
                    <td>${row.stage ?? '-'}</td>
                    <td>${row.total}</td>
                    <td class="text-success">${row.available}</td>
                    <td class="text-danger">${row.sold}</td>
                    <td class="text-warning">${row.reserved}</td>
                    <td class="text-info">${row.blocked}</td> <!-- ✅ NUEVO -->
                </tr>`;
  });
 }

 // ✅ CARGA INICIAL
 fetchDashboardData();

});
