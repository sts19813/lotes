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
                const tooltipContent = `Lote ${matchedLot.name} - ${statusText}<br>Área: ${matchedLot.area} m²`;

                svgElement.setAttribute("data-bs-toggle", "tooltip");
                svgElement.setAttribute("data-bs-html", "true"); // permite HTML
                svgElement.setAttribute("data-bs-title", tooltipContent);

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

                        // Función para convertir #RRGGBBAA a rgba()
                        const hex8ToRgba = (hex8) => {
                            hex8 = hex8.replace('#', '');
                            if (hex8.length === 8) {
                                const r = parseInt(hex8.substring(0, 2), 16);
                                const g = parseInt(hex8.substring(2, 4), 16);
                                const b = parseInt(hex8.substring(4, 6), 16);
                                const a = parseInt(hex8.substring(6, 8), 16) / 255;
                                return `rgba(${r},${g},${b},${a.toFixed(2)})`;
                            }
                            return hex8; // si no tiene alpha, devolver tal cual
                        };

                        const finalColor = hex8ToRgba(color);

                        svgElement.querySelectorAll('*').forEach(el => {
                            el.removeAttribute('fill'); // elimina cualquier fill inline
                            el.style.setProperty('fill', finalColor, 'important');
                        });

                        svgElement.removeAttribute('fill');
                        svgElement.style.setProperty('fill', finalColor, 'important');
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
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const lote = window.currentLoteInfo;
            if (!lote) return alert("Error: no se seleccionó un lote");

            const submitBtn = document.getElementById("submitBtn");
            const btnText = submitBtn.querySelector(".btn-text");
            const spinner = submitBtn.querySelector(".spinner-border");

            // 🔄 Mostrar loader y deshabilitar botón
            submitBtn.disabled = true;
            spinner.classList.remove("d-none");
            btnText.textContent = "Enviando...";

            const fd = new FormData();
            fd.append("_token", document.querySelector('meta[name="csrf-token"]').content);
            fd.append("name", lote.name);
            fd.append("area", lote.area);
            fd.append("price_square_meter", lote.price_square_meter);
            fd.append("down_payment_percent", lote.down_payment_percent || 30);
            fd.append("financing_months", lote.financing_months || 60);
            fd.append("annual_appreciation", lote.annual_appreciation || 0.15);
            fd.append("chepina", lote.chepina);

            fd.append("lead_name", document.querySelector("#leadName").value);
            fd.append("lead_phone", document.querySelector("#leadPhone").value);
            fd.append("lead_email", document.querySelector("#leadEmail").value);
            fd.append("city", document.querySelector("#leadCity").value);
            

            fd.append("desarrollo_id", window.currentLot.desarrollo_id);
            fd.append("desarrollo_name", window.currentLot.desarrollo_name);
            fd.append("phase_id", window.currentLot.phase_id);
            fd.append("stage_id", window.currentLot.stage_id);

            fetch("/reports/generate", { method: "POST", body: fd })
                .then(async res => {
                    if (!res.ok) {
                        const errorText = await res.text();
                        console.error("Error del servidor:", errorText);
                        alert("Ocurrió un error. Revisa la consola.");
                        throw new Error(errorText);
                    }
                    return res.blob();
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = "cotizacion.pdf";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);

                    // ✅ Cerrar modal automáticamente
                    const downloadFormModalEl = document.getElementById('downloadFormModal');
                    const downloadFormModal = bootstrap.Modal.getInstance(downloadFormModalEl);
                    if (downloadFormModal) downloadFormModal.hide();

                    // Opcional: limpiar formulario
                    form.reset();
                })
                .catch(err => {
                    console.error("Fetch fallo:", err);
                    alert("Ocurrió un error al generar la cotización.");
                })
                .finally(() => {
                    // 🔄 Restaurar botón
                    submitBtn.disabled = false;
                    spinner.classList.add("d-none");
                    btnText.textContent = "ENVIAR Y DESCARGAR";
                });
        });

    }
});




function llenarModal(lote) {
    // Guardamos el lote globalmente para usarlo en el submit
    window.currentLoteInfo = lote;

    // Cambiar imagen
    document.querySelector("#chepinaIMG").src = lote.chepina;

    // Lote
    document.querySelector("#loteName").textContent = lote.name;
    document.querySelector("#lotearea").textContent = `${lote.area.toFixed(2)} m²`;
    document.querySelector("#lotePrecioMetro").textContent = `$${lote.price_square_meter.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;

    const precioTotal = lote.area * lote.price_square_meter;
    document.querySelector("#lotePrecioTotal").textContent = `$${precioTotal.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;

    // --- FINANCIAMIENTO ---
    const enganchePorc = lote.down_payment_percent || 30;
    const engancheMonto = precioTotal * (enganchePorc / 100);
    document.querySelector(".form-select").value = `${enganchePorc}% de enganche`;
    document.querySelector("p.label strong").textContent = `$${engancheMonto.toLocaleString('es-MX', { minimumFractionDigits: 2 })} MXN`;

    const intereses = lote.interest_rate || 0;
    const descuento = lote.discount_percent || 0;
    document.querySelector("#tab1 .value.text-primary.fw-bold").textContent = `${enganchePorc}%`;
    document.querySelector("#tab1 .col-3 .value.fw-bold").textContent = `${intereses}%`;
    document.querySelector("#tab1 .col-3:nth-child(3) .value.fw-bold").textContent = `${descuento}%`;

    const meses = lote.financing_months || 60;
    const mensualidad = (precioTotal - engancheMonto) / meses;
    document.querySelector("#tab1 .col-4 .value.fw-bold").textContent = `${meses} meses`;
    document.getElementById("loteMensualidad").textContent = `$${mensualidad.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;
    document.getElementById("monthlyPayment").textContent = `$${mensualidad.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;
    document.getElementById("loteMontoFinanciado").textContent = `$${(precioTotal - engancheMonto).toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;
    document.getElementById("loteContraEntrega").textContent = `$${engancheMonto.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;
    document.getElementById("loteCostoTotal").textContent = `$${precioTotal.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;

    // --- PROYECCIÓN PLUSVALÍA & ROI 5 AÑOS ---
    const plusvaliaRate = lote.annual_appreciation || 0.15;
    const plusvaliaTotal = precioTotal * Math.pow(1 + plusvaliaRate, 5);
    const roi = ((plusvaliaTotal - precioTotal) / precioTotal) * 100;
    document.querySelector(".background-verde h6").textContent = `$${plusvaliaTotal.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;
    document.querySelector(".background-azul h6").textContent = `${roi.toFixed(2)}%`;
    document.querySelector(".background-morado h6").textContent = `${(plusvaliaRate * 100).toFixed(0)}%`;
    document.querySelector(".background-amarillo h6").textContent = `$${plusvaliaTotal.toLocaleString('es-MX', { minimumFractionDigits: 2 })}`;

    // --- TAB 2 CHEPINA ---
    const chepinaImg = document.getElementById("chepinaIMG");
    if (chepinaImg) chepinaImg.src = lote.chepina || "/assets/img/CHEPINA.svg";

    const tbody = document.querySelector(".table-responsive tbody");
    if (tbody) {
        tbody.innerHTML = "";
        for (let year = 0; year <= 5; year++) {
            const valorProp = precioTotal * Math.pow(1 + plusvaliaRate, year);
            const montoPagado = (year >= 1) ? mensualidad * 12 * year + engancheMonto : engancheMonto;
            const plusvaliaAcum = valorProp - precioTotal;
            const roiAnual = ((valorProp - precioTotal) / precioTotal) * 100;
            const plusColor = plusvaliaAcum > 0 ? "text-success fw-semibold" : "";
            const roiColor = roiAnual > 0 ? "text-primary fw-semibold" : "";

            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${year}</td>
                <td>$${valorProp.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                <td>$${montoPagado.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                <td class="${plusColor}">+${plusvaliaAcum.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                <td class="${roiColor}">${roiAnual.toFixed(2)}%</td>
            `;
            tbody.appendChild(tr);
        }
    }
}
