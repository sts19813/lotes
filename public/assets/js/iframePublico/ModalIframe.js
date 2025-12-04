/**
 * ================================================================
 * FUNCIÓN PRINCIPAL: llenarModal(lote)
 * ---------------------------------------------------------------
 * Llena el modal con la información del lote seleccionado, calcula
 * los precios base, enganche, mensualidades y configura los planes 
 * de financiamiento disponibles.
 * ================================================================
 */

document.addEventListener("DOMContentLoaded", function () {

    const lotButtons = document.querySelectorAll(".btn-lot-merge");
    lotButtons.forEach(btn => {
        const name = btn.innerText.trim();
        if (name.includes("Medio")) {
            btn.style.display = "none";
        } else {
            btn.style.display = "inline-block"; // visible por default
        }
    });

    lotButtons.forEach(btn => {
        btn.addEventListener("click", function () {

            // Quitar active de todos
            lotButtons.forEach(b => b.classList.remove("active-lot"));

            // Activar el seleccionado
            this.classList.add("active-lot");
        });
    });

    const select = document.getElementById("select-lot-merge");

    if (select) {
        select.addEventListener("change", function () {

            ocultarInstrucciones();

            const option = this.options[this.selectedIndex];
            if (!option || !option.dataset.area) return;

            const lote = {
                id: option.value,
                name: option.innerText.trim(),  
                area: option.dataset.area,
                front: option.dataset.front,
                depth: option.dataset.depth,
                auditorium: option.dataset.auditorio,
                banquet: option.dataset.banquete,
                school: option.dataset.escuela,
                horseshoe: option.dataset.herradura,
                russian_table: option.dataset.mesarusa,
                chepina: option.dataset.chepina  
            };

            // ================================
            // MISMA LÓGICA DE COLOR QUE LOS BOTONES
            // ================================
            limpiarColoresSVG();
            colorearSVGPorChepina(lote.chepina);

            // Actualizar info del lado derecho
            window.actualizarVista(lote);

            const all = document.querySelectorAll(".lote-svg");
            let svgElement = null;

            all.forEach(el => {
                try {
                    const data = JSON.parse(el.dataset.loteInfo);
                    if (data.id == lote.id) {
                        svgElement = el;
                    }
                } catch (e) {}
            });

            if (svgElement) {
                svgElement.dispatchEvent(new Event("click", { bubbles: true }));
            }
        });
    }
});

function fmt(value) {
    if (value === null || value === undefined || value === "") return 'N/A';
    // si es numérico en string, formatea
    const n = Number(String(value).replace(/,/g, ''));
    if (!isNaN(n)) {
        // quitar decimales .00 si no quieres
        return Number.isInteger(n) ? n.toString() : n.toFixed(2).replace(/\.00$/, '');
    }
    return String(value);
}

/**
 * SetText seguro: no truena si el elemento no existe.
 */
function setText(id, value, unit = "") {
    const el = document.getElementById(id);
    if (!el) return;

    const formatted = fmt(value);

    // Si es N/A → NO PONE unidad
    if (formatted === "N/A") {
        el.textContent = "N/A";
    } else {
        el.textContent = unit ? `${formatted} ${unit}` : formatted;
    }
}
/**
 * Actualiza TODOS los elementos de la vista cuando se selecciona un lote.
 * Actualiza: panel derecho (ids simples) y métricas (ids metric-*)
 *
 * Coloca esta función en window para que pueda llamarse desde otros scripts.
 */
function extraerNumeros(name) {
    if (!name) return [];
    const matches = name.match(/\d+/g);
    return matches ? matches : [];
}


window.actualizarVista = function (lot) {
    if (!lot) return;

    // === panel derecho ===
    setText("punto-colgado", lot.hanging_point);
    setText("resistencia-piso", lot.floor_resistance);

    setText("area", lot.area);
    setText("frente", lot.front);
    setText("fondo", lot.depth);
    setText("altura", lot.height);

    setText("auditorio", lot.auditorium);
    setText("banquete", lot.banquet);
    setText("coctel", lot.cocktail);
    setText("escuela", lot.school);
    setText("herradura", lot.horseshoe);
    setText("mesa-rusa", lot.russian_table);

    setText("metric-area", lot.area, "m²");
    setText("metric-area-mobile", lot.area, "m²");

    setText("metric-auditorium", lot.auditorium);
    setText("metric-auditorium-mobile", lot.auditorium);

    setText("metric-banquet", lot.banquet);
    setText("metric-banquet-mobile", lot.banquet);

    setText("metric-school", lot.school);
    setText("metric-school-mobile", lot.school);

    const salonSpan = document.querySelector(".salon-seleccionado");
    if (salonSpan) {
        salonSpan.textContent = lot.name ?  `${lot.name}` : "";
    }
    // === mostrar/ocultar botones segun numero ===
    const numerosSeleccionados = extraerNumeros(lot.name);
    const numero = numerosSeleccionados.length ? numerosSeleccionados[0] : null;

    if (numero) {
        document.querySelectorAll(".btn-lot-merge").forEach(btn => {
            const nombreBtn = btn.innerText.trim();
             // ej. ["21","22"]

            if (nombreBtn.includes("Medio")) {
                btn.style.display = "none";
                return;
            }

            const numerosBtn = extraerNumeros(nombreBtn);
            // si el array contiene el numero seleccionado lo mostramos
            if (numerosBtn.includes(numero)) {
                btn.style.display = "inline-block";
            } else {
                btn.style.display = "none";
            }
        });
    } else {
        // si no hay número en lote, oculta todos o decide comportamiento
        document.querySelectorAll(".btn-lot-merge").forEach(btn => btn.style.display = "none");
    }

    // === tour link ===
    const tourContainerId = 'btn-tour-virtual';
    const existing = document.getElementById(tourContainerId);
    if (lot.tour_link) {
        const html = `<div id="${tourContainerId}" class="mt-3">
                        <a href="${lot.tour_link}" target="_blank" class="btn btn-outline-dark rounded-pill px-4">
                            Ver Recorrido Virtual
                        </a>
                      </div>`;

        const parent = document.querySelector('.col-lg-5');
        if (parent) {
            if (existing) {
                existing.outerHTML = html;
            } else {
                parent.insertAdjacentHTML('beforeend', html);
            }
        }
    } else {
        if (existing) existing.remove();
    }
};


function ocultarInstrucciones() {
    const d = document.getElementById("instrucciones-desktop");
    const m = document.getElementById("instrucciones-mobile");
    const c = document.querySelector(".container-info");

    if (d) d.style.setProperty("display", "none", "important");
    if (m) m.style.setProperty("display", "none", "important");
    if (c) c.classList.remove("d-none");
}


/**
 * Función que se llamará cuando selecciones un lote (expuesta globalmente)
 * Llama a actualizarVista y puede abrir modal si quieres.
 */
window.llenarModal = function (lote) {
    if (!lote) return;

     ocultarInstrucciones();
    window.currentLoteInfo = lote;
    window.actualizarVista(lote);

    // si quieres abrir un modal:
    // $('#modalLotInfo').modal('show'); // descomenta si tienes bootstrap modal
};

const buttons = document.querySelectorAll(".btn-lot-merge");
buttons.forEach(btn => {
    btn.addEventListener("click", () => {

        ocultarInstrucciones();

        const lote = {
            id: btn.dataset.id,
            name: btn.innerText.trim(),
            area: btn.dataset.area,
            front: btn.dataset.front,
            depth: btn.dataset.depth,
            auditorium: btn.dataset.auditorio,
            banquet: btn.dataset.banquete,
            school: btn.dataset.escuela,
            horseshoe: btn.dataset.herradura,
            russian_table: btn.dataset.mesarusa
        };
 // ================================
        //  NUEVO: RESALTAR EN EL SVG
        // ================================
        const chepina = btn.dataset.chepina;

        limpiarColoresSVG();           // quitar colores previos
        colorearSVGPorChepina(chepina); // aplicar nuevos
        
        window.actualizarVista(lote);

        const svgElement = document.querySelector(
            `.lote-svg[data-lote-info*='"id":"${lote.id}"']`
        );

        if (svgElement) {
            svgElement.dispatchEvent(new Event("click", { bubbles: true }));
        }
    });
});

// Limpia todos los elementos pintados anteriormente
function limpiarColoresSVG() {

    // 1. Limpiar clases .active en el DOM normal
    document.querySelectorAll(".active").forEach(el => {
        el.classList.remove("active");
    });

    // 2. Buscar SVGs inline
    document.querySelectorAll("svg .active").forEach(el => {
        el.classList.remove("active");
    });

    // 3. Buscar dentro de <object> (si tu SVG está cargado externamente)
    document.querySelectorAll("object").forEach(obj => {
        try {
            const svgDoc = obj.contentDocument;
            if (svgDoc) {
                svgDoc.querySelectorAll(".active").forEach(el => el.classList.remove("active"));
            }
        } catch(e){}
    });

    // 4. Buscar dentro de <iframe> (si aplica)
    document.querySelectorAll("iframe").forEach(frame => {
        try {
            const svgDoc = frame.contentDocument;
            if (svgDoc) {
                svgDoc.querySelectorAll(".active").forEach(el => el.classList.remove("active"));
            }
        } catch(e){}
    });
}


// Colorea elementos del SVG según ids de data-chepina
function colorearSVGPorChepina(cadena) {
    
    if (!cadena) return;

    const ids = cadena.split(",").map(e => e.trim());

    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add("active");
        }
    });
}
