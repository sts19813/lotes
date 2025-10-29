const tabs = document.querySelectorAll('.switch-tabs .btn');
const contents = document.querySelectorAll('.tab-content > div');
let selectedLots = []; // array global para almacenar los lotes seleccionados
let selectionMode = null; // 'available' o 'sold', para controlar qué se puede seleccionar

let info = null;
const statusMap = {
    for_sale: "Disponible",
    sold: "Vendido",
    reserved: "Apartado",
    locked_sale: "Bloqueado"
};

document.addEventListener("DOMContentLoaded", function () {
    const modalEl = document.getElementById('polygonModal');

    // 1️⃣ Detectar click sobre polygons/path con clase .cls-1
    const svgElements = document.querySelectorAll(selector);

    svgElements.forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();

            let elementId = (this.id && this.id.trim() !== "") ? this.id : null;
            if (!elementId) {
                const parentG = this.closest("g");
                if (parentG && parentG.id && parentG.id.trim() !== "") {
                    elementId = parentG.id;
                }
            }

            const clickedSVG = document.getElementById(elementId);
            if (!clickedSVG) return;

            const info = JSON.parse(clickedSVG.dataset.loteInfo);
            const originalStatus = info.status;

            // --- Deselección ---
            const alreadySelectedIndex = selectedLots.findIndex(l => l.id === info.id);
            if (alreadySelectedIndex >= 0) {
                // Quitar del array de seleccionados
                selectedLots.splice(alreadySelectedIndex, 1);

                // Restaurar color original según su status
                let fillColor;
                switch (originalStatus) {
                    case 'for_sale': fillColor = 'rgba(52, 199, 89, 0.7)'; break;
                    case 'sold': fillColor = 'rgba(200, 0, 0, 0.6)'; break;
                    case 'reserved': fillColor = 'rgba(255, 200, 0, 0.6)'; break;
                    default: fillColor = 'rgba(100, 100, 100, .9)';
                }
                clickedSVG.querySelectorAll('*').forEach(el => el.style.setProperty('fill', fillColor, 'important'));
                clickedSVG.style.setProperty('fill', fillColor, 'important');

                // Ajustar modo de selección si no hay seleccionados
                if (selectedLots.length === 0) selectionMode = null;
                return;
            }

            // --- Selección ---
            if (selectionMode === null) {
                // Primera selección define el modo
                selectionMode = (info.status === 'for_sale') ? 'available' : 'sold';
            }

            // Validar si puede seleccionarse según modo
            if (selectionMode === 'available' && (info.status === 'sold' || info.status === 'locked_sale')) {
                alert('No puedes seleccionar lotes ocupados si hay disponibles seleccionados.');
                return;
            }

            // Pintar de amarillo
            clickedSVG.querySelectorAll('*').forEach(el => el.style.setProperty('fill', 'yellow', 'important'));
            clickedSVG.style.setProperty('fill', 'yellow', 'important');

            // Guardar en array
            selectedLots.push(info);
        });
    });

    // === Inicialización de colores y tooltips ===
    if (window.dbLotes && Array.isArray(window.dbLotes)) {
        if (window.preloadedLots && window.preloadedLots.length > 0) {
            // Caso normal: hay lots precargados y dbLotes
            window.dbLotes.forEach(dbLote => {
                let matchedLot;
                if (window.currentLot.source_type === 'adara') {
                    matchedLot = window.preloadedLots.find(l => l.id == dbLote.lote_id);
                } else if (window.currentLot.source_type === 'naboo') {
                    matchedLot = window.preloadedLots.find(l => l.id == dbLote.lote_id); // usar id directamente
                }
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

                svgElement.querySelectorAll('*').forEach(el => el.style.setProperty('fill', fillColor, 'important'));
                svgElement.style.setProperty('fill', fillColor, 'important');

                // Guardar info en dataset
                svgElement.dataset.loteInfo = JSON.stringify(matchedLot);

                // Tooltip
                const statusText = statusMap[matchedLot.status] || matchedLot.status;
                const tooltipContent = `Asiento ${matchedLot.name} - ${statusText}`;
                svgElement.setAttribute("data-bs-toggle", "tooltip");
                svgElement.setAttribute("data-bs-html", "true"); 
                svgElement.setAttribute("data-bs-title", tooltipContent);
                new bootstrap.Tooltip(svgElement);

                // Si está bloqueado -> no permitir click
                if (matchedLot.status === "locked_sale") {
                    svgElement.style.cursor = "not-allowed";
                    svgElement.onclick = (e) => e.preventDefault();
                    return;
                }
            });
        } else {
            // Manejo de redirección u otros casos si aplica
            if (redireccion) {
                window.dbLotes.forEach(dbLote => {
                    if (!dbLote.selectorSVG || !dbLote.redirect_url) return;
                    const svgElement = document.querySelector(`#${dbLote.selectorSVG}`);
                    if (!svgElement) return;

                    const paintAll = (color) => {
                        if (!color) return;
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

                    if (dbLote.color) paintAll(dbLote.color);

                    svgElement.dataset.baseColor = dbLote.color || "";
                    svgElement.dataset.activeColor = dbLote.color_active || "";

                    svgElement.addEventListener('mouseover', () => {
                        if (svgElement.dataset.activeColor) paintAll(svgElement.dataset.activeColor);
                    });
                    svgElement.addEventListener('mouseleave', () => {
                        if (svgElement.dataset.baseColor) paintAll(svgElement.dataset.baseColor);
                    });

                    svgElement.style.cursor = "pointer";
                    svgElement.addEventListener("click", () => {
                        window.location.href = dbLote.redirect_url;
                    });
                });
            }
        }
    }

});

// === Guardar cambios ===
document.getElementById('btnGuardarAsientos').addEventListener('click', function() {
    if (selectedLots.length === 0) return;

    // Determinar nuevo estatus según modo
    const newStatus = (selectionMode === 'available') ? 'sold' : 'for_sale';

    fetch('/lotes/guardar-asientos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            lots: selectedLots.map(l => l.id),
            status: newStatus
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Ocurrió un error al guardar los asientos.');
        }
    })
    .catch(err => console.error(err));
});
