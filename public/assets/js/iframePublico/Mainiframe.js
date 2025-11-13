const statusMap = {
    for_sale: "Disponible",
    sold: "Vendido",
    reserved: "Apartado",
    locked_sale: "Bloqueado"
};

$(document).ready(function () {

    // ==========================================================
    //  CONFIGURACIONES Y VARIABLES GLOBALES
    // ==========================================================
    const tabs = document.querySelectorAll('.switch-tabs .btn');
    const contents = document.querySelectorAll('.tab-content > div');
    let info = null;
    // ==========================================================
    //  CONFIGURACIÓN DEL MODAL (instancia única)
    // ==========================================================
    const modalEl = document.getElementById('polygonModal');
    polygonModal = new bootstrap.Modal(modalEl);

    // ==========================================================
    //  SELECCIÓN Y CONFIGURACIÓN DE ELEMENTOS SVG
    // ==========================================================
    const svgElements = document.querySelectorAll(selector);

    svgElements.forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();

            // --- Obtener ID válido del elemento o su padre ---
            let elementId = (this.id && this.id.trim() !== "") ? this.id : null;
            if (!elementId) {
                const parentG = this.closest("g");
                if (parentG && parentG.id && parentG.id.trim() !== "") {
                    elementId = parentG.id;
                }
            }

            // --- Obtener información del lote ---
            info = JSON.parse(document.getElementById(elementId).getAttribute("data-lote-info"));

            // --- Configuración de pestañas internas del modal ---
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    const target = tab.getAttribute('data-tab');
                    contents.forEach(c => {
                        c.classList.remove('active');
                        if (c.id === target) c.classList.add('active');
                    });
                });
            });

            // --- Validaciones de estatus ---
            if (elementId) {
                if (info.status === "sold" || info.status === "locked_sale") {
                    console.log(`Esta Unidad está ${statusMap[info.status]}`);
                    return; // No abrir modal
                }

                // --- Mostrar información del lote en modal ---
                llenarModal(info);
                polygonModal.show();
            }
        });
    });

    // ==========================================================
    //  PINTADO Y TOOLTIP DE LOTES (CASO: dbLotes y preloadedLots)
    // ==========================================================
    if (window.dbLotes && Array.isArray(window.dbLotes) && window.preloadedLots && window.preloadedLots.length > 0) {

        window.dbLotes.forEach(dbLote => {
            let matchedLot;

            // --- Búsqueda según origen del lote ---
            if (window.currentLot.source_type === 'adara') {
                matchedLot = window.preloadedLots.find(l => l.id == dbLote.lote_id);
            } else if (window.currentLot.source_type === 'naboo') {
                matchedLot = window.preloadedLots.find(l => l.id == dbLote.lote_id);
            }
            if (!matchedLot) return;

            const selector = dbLote.selectorSVG;
            if (!selector) return;

            const svgElement = document.querySelector(`#${selector}`);
            if (!svgElement) return;

            // --- Color según estatus ---
            let fillColor;
            switch (matchedLot.status) {
                case 'for_sale': fillColor = 'rgba(52, 199, 89, 0.7)'; break;
                case 'sold': fillColor = 'rgba(200, 0, 0, 0.6)'; break;
                case 'reserved': fillColor = 'rgba(255, 200, 0, 0.6)'; break;
                default: fillColor = 'rgba(100, 100, 100, .9)';
            }

            // --- Aplicar color ---
            svgElement.querySelectorAll('*').forEach(el => {
                el.style.setProperty('fill', fillColor, 'important');
            });
            svgElement.style.setProperty('fill', fillColor, 'important');

            // --- Guardar información en dataset ---
            svgElement.dataset.loteInfo = JSON.stringify(matchedLot);

            // --- Crear tooltip (estatus + número de lote + área) ---
            const statusText = statusMap[matchedLot.status] || matchedLot.status;
            const tooltipContent = `Unidad: ${matchedLot.name} - ${statusText}<br>Área: ${matchedLot.area} m²`;

            svgElement.setAttribute("data-bs-toggle", "tooltip");
            svgElement.setAttribute("data-bs-html", "true");
            svgElement.setAttribute("data-bs-title", tooltipContent);

            new bootstrap.Tooltip(svgElement);

            // --- Desactivar click si está vendido o bloqueado ---
            if (matchedLot.status === "sold" || matchedLot.status === "locked_sale") {
                svgElement.style.cursor = "not-allowed";
                svgElement.onclick = (e) => e.preventDefault();
            }
        });
    }

    // ==========================================================
    //  CASO ALTERNATIVO: REDIRECCIÓN (solo si existe `redireccion`)
    // ==========================================================
    else if (window.dbLotes && Array.isArray(window.dbLotes) && typeof redireccion !== 'undefined' && redireccion) {

        window.dbLotes.forEach(dbLote => {
            if (!dbLote.selectorSVG || !dbLote.redirect_url) return;

            const svgElement = document.querySelector(`#${dbLote.selectorSVG}`);
            if (!svgElement) return;

            // --- Función para pintar el SVG completo ---
            const paintAll = (color) => {
                if (!color) return;

                // Convertir color #RRGGBBAA → rgba()
                const hex8ToRgba = (hex8) => {
                    hex8 = hex8.replace('#', '');
                    if (hex8.length === 8) {
                        const r = parseInt(hex8.substring(0, 2), 16);
                        const g = parseInt(hex8.substring(2, 4), 16);
                        const b = parseInt(hex8.substring(4, 6), 16);
                        const a = parseInt(hex8.substring(6, 8), 16) / 255;
                        return `rgba(${r},${g},${b},${a.toFixed(2)})`;
                    }
                    return hex8;
                };

                const finalColor = hex8ToRgba(color);

                svgElement.querySelectorAll('*').forEach(el => {
                    el.removeAttribute('fill');
                    el.style.setProperty('fill', finalColor, 'important');
                });

                svgElement.removeAttribute('fill');
                svgElement.style.setProperty('fill', finalColor, 'important');
            };

            // --- Pintar color base ---
            if (dbLote.color) paintAll(dbLote.color);

            // --- Guardar colores en dataset ---
            svgElement.dataset.baseColor = dbLote.color || "";
            svgElement.dataset.activeColor = dbLote.color_active || "";

            // --- Efectos hover ---
            svgElement.addEventListener('mouseover', () => {
                if (svgElement.dataset.activeColor) paintAll(svgElement.dataset.activeColor);
            });
            svgElement.addEventListener('mouseleave', () => {
                if (svgElement.dataset.baseColor) paintAll(svgElement.dataset.baseColor);
            });

            // --- Redirección ---
            svgElement.style.cursor = "pointer";
            svgElement.addEventListener("click", () => {
                window.location.href = dbLote.redirect_url;
            });
        });
    }
});