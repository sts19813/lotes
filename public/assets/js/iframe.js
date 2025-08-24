const tabs = document.querySelectorAll('.switch-tabs .btn');
const contents = document.querySelectorAll('.tab-content > div');
let info = null;

document.addEventListener("DOMContentLoaded", function () {
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

            info = JSON.parse(document.getElementById(elementId).getAttribute("data-lote-info"));


            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Alternar botón activo
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    const target = tab.getAttribute('data-tab');
                    contents.forEach(c => {
                        c.classList.remove('active');
                        if (c.id === target) c.classList.add('active');
                    });
                });
            });


            if (elementId) {


                if (info.status === "sold") {
                    // Opcional: mostrar alert o solo ignorar
                    console.log("Este lote ya está vendido");
                    return; // No abrir modal
                }

                llenarModal(info)


                polygonModal.show();
            }
        });
    });


    if (!window.dbLotes || !window.preloadedLots) return;

    window.dbLotes.forEach(dbLote => {
        const matchedLot = window.preloadedLots.find(l => l.id == dbLote.lote_id);
        if (!matchedLot) return;

        const selector = dbLote.selectorSVG;
        if (!selector) return;

        const svgElement = document.querySelector(`#${selector}`);
        if (!svgElement) return;

        // === Color por status ===
        let fillColor;
        switch (matchedLot.status) {
            case 'for_sale': fillColor = 'rgba(52, 199, 89, 0.7)'; break;
            case 'sold': fillColor = 'rgba(200, 0, 0, 0.6)'; break;
            case 'reserved': fillColor = 'rgba(255, 200, 0, 0.6)'; break;
            default: fillColor = 'rgba(100, 100, 100, 0.4)';
        }

        svgElement.querySelectorAll('*').forEach(el => {
            el.style.setProperty('fill', fillColor, 'important');
        });
        svgElement.style.setProperty('fill', fillColor, 'important');

        // Guardar info en dataset
        svgElement.dataset.loteInfo = JSON.stringify(matchedLot);

        if (matchedLot.status === "sold") {
            // Tooltip Bootstrap
            svgElement.setAttribute("data-bs-toggle", "tooltip");
            svgElement.setAttribute("data-bs-title", "Vendido");
            svgElement.style.cursor = "not-allowed";

            // Inicializar tooltip
            new bootstrap.Tooltip(svgElement);

            // Evitar click
            svgElement.onclick = (e) => e.preventDefault();
            return; // salir para no agregar el click handler
        }


    });



    document.getElementById('btnDescargarCotizacion').addEventListener('click', function () {
        let polygonModal = bootstrap.Modal.getInstance(document.getElementById('polygonModal'));
        polygonModal.hide();

        let downloadFormModal = new bootstrap.Modal(document.getElementById('downloadFormModal'));
        downloadFormModal.show();
    });

    const form = document.getElementById('downloadForm');
    if (form) {
        debugger
        form.addEventListener('submit', function () {
            debugger
            document.getElementById('lotNumberHidden').value = info.id;
        });
    }
});




function llenarModal(infovar) {
    // Cambiar imagen
    document.querySelector("#chepinaIMG").src = infovar.chepina;

    // Lote
    document.querySelector("#loteName").textContent = infovar.name;

    // Área
    document.querySelector("#lotearea").textContent = `${infovar.area.toFixed(2)} m²`;

    // Precio por m²
    document.querySelector("#lotePrecioMetro").textContent = `$${infovar.price_square_meter.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;

    // Precio total = área * precio por m²
    const total = infovar.area * infovar.price_square_meter;
    document.querySelector("#lotePrecioTotal").textContent = `$${total.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;
}