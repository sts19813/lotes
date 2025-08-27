const tabs = document.querySelectorAll('.switch-tabs .btn');
const contents = document.querySelectorAll('.tab-content > div');
let info = null;
const statusMap = {
    for_sale: "Disponible",
    sold: "Vendido",
    reserved: "Apartado",
    locked_sale: "Bloqueado"
};

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


                if (info.status === "sold" || info.status === "locked_sale") {
                    console.log(`Este lote está ${statusMap[info.status]}`);
                    return; // No abrir modal
                }

                llenarModal(info)


                polygonModal.show();
            }
        });
    });


    if (window.dbLotes && Array.isArray(window.dbLotes)) {
        if (window.preloadedLots && window.preloadedLots.length > 0) {
            // Caso normal: hay lots precargados y dbLotes
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
                    default: fillColor = 'rgba(100, 100, 100, .9)';
                }

                svgElement.querySelectorAll('*').forEach(el => {
                    el.style.setProperty('fill', fillColor, 'important');
                });
                svgElement.style.setProperty('fill', fillColor, 'important');

                // Guardar info en dataset
                svgElement.dataset.loteInfo = JSON.stringify(matchedLot);

                // ✅ Tooltip siempre visible: Estatus + Número de lote
                const statusText = statusMap[matchedLot.status] || matchedLot.status;
                svgElement.setAttribute("data-bs-toggle", "tooltip");
                svgElement.setAttribute("data-bs-title", `Lote ${matchedLot.name} - ${statusText}`);
                new bootstrap.Tooltip(svgElement);

                // Si está vendido o bloqueado -> no permitir click ni abrir modal
                if (matchedLot.status === "sold" || matchedLot.status === "locked_sale") {
                    svgElement.style.cursor = "not-allowed";
                    svgElement.onclick = (e) => e.preventDefault();
                    return;
                }
            });

        } else {

            if (redireccion) {
                window.dbLotes.forEach(dbLote => {
                    if (!dbLote.selectorSVG || !dbLote.redirect_url) return;

                    const svgElement = document.querySelector(`#${dbLote.selectorSVG}`);
                    if (!svgElement) return;

                    // Helper para pintar el elemento y todos sus hijos
                    const paintAll = (color) => {
                        if (!color) return;
                        svgElement.querySelectorAll('*').forEach(el => {
                            el.style.setProperty('fill', color, 'important');
                        });
                        svgElement.style.setProperty('fill', color, 'important');
                    };

                    // 1) Pintar color base si existe
                    if (dbLote.color) paintAll(dbLote.color);

                    // Guardar colores en dataset
                    svgElement.dataset.baseColor = dbLote.color || "";
                    svgElement.dataset.activeColor = dbLote.color_active || "";

                    // 2) Hover IN -> aplicar color_active
                    svgElement.addEventListener('mouseover', () => {
                        if (svgElement.dataset.activeColor) {
                            paintAll(svgElement.dataset.activeColor);
                        }
                    });

                    // 3) Hover OUT -> restaurar color base
                    svgElement.addEventListener('mouseleave', () => {
                        if (svgElement.dataset.baseColor) {
                            paintAll(svgElement.dataset.baseColor);
                        }
                    });

                    // 4) Cursor y click (redirección)
                    svgElement.style.cursor = "pointer";
                    svgElement.addEventListener("click", () => {
                        window.location.href = dbLote.redirect_url;
                    });
                });
            }

        }
    }


    //modal de leeds, se dispara cuando se descarga
    const btn = document.getElementById('btnDescargarCotizacion');
    if (btn) {
        btn.addEventListener('click', function () {
            let polygonModal = bootstrap.Modal.getInstance(document.getElementById('polygonModal'));
            if (polygonModal) polygonModal.hide();

            let downloadFormModal = new bootstrap.Modal(document.getElementById('downloadFormModal'));
            downloadFormModal.show();
        });
    }


    const form = document.getElementById('downloadForm');
    if (form) {
        form.addEventListener('submit', function () {

            document.getElementById('lotNumberHidden').value = info.id;
        });
    }
});




function llenarModal(lote) {
    // Cambiar imagen
    document.querySelector("#chepinaIMG").src = lote.chepina;

    // Lote
    document.querySelector("#loteName").textContent = lote.name;

    // Área
    document.querySelector("#lotearea").textContent = `${lote.area.toFixed(2)} m²`;

    // Precio por m²
    document.querySelector("#lotePrecioMetro").textContent = `$${lote.price_square_meter.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;

    // Precio total = área * precio por m²
    const precioTotal = lote.area * lote.price_square_meter;
    document.querySelector("#lotePrecioTotal").textContent = `$${precioTotal.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;

    // --- FINANCIAMIENTO ---
    // Enganche y monto
    const enganchePorc = lote.down_payment_percent || 30; // default 30%
    const engancheMonto = precioTotal * (enganchePorc / 100);
    document.querySelector(".form-select").value = `${enganchePorc}% de enganche`;
    document.querySelector("p.label strong").textContent = `$${engancheMonto.toLocaleString('es-MX', { minimumFractionDigits: 2 })} MXN`;

    // Intereses y descuento
    const intereses = lote.interest_rate || 0; // ejemplo
    const descuento = lote.discount_percent || 0;
    document.querySelector("#tab1 .value.text-primary.fw-bold").textContent = `${enganchePorc}%`; // Enganche
    document.querySelector("#tab1 .col-3 .value.fw-bold").textContent = `${intereses}%`; // Intereses
    document.querySelector("#tab1 .col-3:nth-child(3) .value.fw-bold").textContent = `${descuento}%`; // Descuento

    // Financiamiento
    const meses = lote.financing_months || 60;
    const mensualidad = (precioTotal - engancheMonto) / meses;
    document.querySelector("#tab1 .col-4 .value.fw-bold").textContent = `${meses} meses`; // Financiamiento
    document.getElementById("loteMensualidad").textContent = `$${mensualidad.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;

    // Monto financiado y contra entrega
    document.getElementById("loteMontoFinanciado").textContent = `$${(precioTotal - engancheMonto).toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;
    document.getElementById("loteContraEntrega").textContent = `$${engancheMonto.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;

    // Costo total
    document.getElementById("loteCostoTotal").textContent = `$${precioTotal.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;

    // --- PROYECCIÓN PLUSVALÍA & ROI 5 AÑOS ---
    const plusvaliaRate = lote.annual_appreciation || 0.15; //5% anual
    const planMeses = meses; // Usamos mismo financiamiento para cálculo
    const plusvaliaTotal = precioTotal * Math.pow(1 + plusvaliaRate, 5);
    const roi = ((plusvaliaTotal - precioTotal) / precioTotal) * 100;

    document.querySelector(".background-verde h6").textContent = `$${plusvaliaTotal.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;
    document.querySelector(".background-azul h6").textContent = `${roi.toFixed(2)}%`;
    document.querySelector(".background-morado h6").textContent = `${(plusvaliaRate * 100).toFixed(0)}%`; // Plusvalía anual
    document.querySelector(".background-amarillo h6").textContent = `$${(precioTotal + (plusvaliaTotal - precioTotal)).toLocaleString('es-MX', { minimumFractionDigits: 2 })}`; // Valor final

    // --- TAB 2 CHEPINA ---
    const chepinaImg = document.getElementById("chepinaIMG");
    if (chepinaImg) chepinaImg.src = lote.chepina || "/assets/img/CHEPINA.svg";

    // Opcional: actualizar tabla de proyección
    const tbody = document.querySelector(".table-responsive tbody");
    if (tbody) {
        tbody.innerHTML = "";
        let acumulado = 0;
        for (let year = 0; year <= 5; year++) {
            const valorProp = precioTotal * Math.pow(1 + plusvaliaRate, year);
            const montoPagado = (year >= 1) ? mensualidad * 12 * year + engancheMonto : engancheMonto;
            const plusvaliaAcum = valorProp - precioTotal;
            const roiAnual = ((valorProp - precioTotal) / precioTotal) * 100;
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${year}</td>
                <td>$${valorProp.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                <td>$${montoPagado.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                <td>$${plusvaliaAcum.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                <td>${roiAnual.toFixed(2)}%</td>
            `;
            tbody.appendChild(tr);
        }
    }
}