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
    document.querySelectorAll(".btn-lot-merge").forEach(btn => {
        btn.style.display = "none";
    });

    const select = document.getElementById("select-lot-merge");

    if (select) {
        select.addEventListener("change", function () {
            const option = this.options[this.selectedIndex];
            if (!option || !option.dataset.area) return;

            const lote = {
                id: option.value,
                area: option.dataset.area,
                front: option.dataset.front,
                depth: option.dataset.depth,
                auditorium: option.dataset.auditorio,
                banquet: option.dataset.banquete,
                school: option.dataset.escuela,
                horseshoe: option.dataset.herradura,
                russian_table: option.dataset.mesarusa
            };

            window.actualizarVista(lote);
        });
    }
});

function fmt(value) {
    if (value === null || value === undefined || value === "") return '---';
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
function setText(id, value) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = fmt(value);
}

/**
 * Actualiza TODOS los elementos de la vista cuando se selecciona un lote.
 * Actualiza: panel derecho (ids simples) y métricas (ids metric-*)
 *
 * Coloca esta función en window para que pueda llamarse desde otros scripts.
 */
function extraerNumero(name) {
    if (!name) return null;
    const match = name.match(/\d+/);
    return match ? match[0] : null;
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

    setText("metric-area", lot.area);
    setText("metric-area-mobile", lot.area);

    setText("metric-auditorium", lot.auditorium);
    setText("metric-auditorium-mobile", lot.auditorium);

    setText("metric-banquet", lot.banquet);
    setText("metric-banquet-mobile", lot.banquet);

    setText("metric-school", lot.school);
    setText("metric-school-mobile", lot.school);
    // === mostrar/ocultar botones segun numero ===
    const numero = extraerNumero(lot.name);

    if (numero) {
        document.querySelectorAll(".btn-lot-merge").forEach(btn => {
            const nombreBtn = btn.innerText.trim();
            const numeroBtn = extraerNumero(nombreBtn);

            if (numeroBtn == numero) {
                btn.style.display = "inline-block";
            } else {
                btn.style.display = "none";
            }
        });
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

/**
 * Función que se llamará cuando selecciones un lote (expuesta globalmente)
 * Llama a actualizarVista y puede abrir modal si quieres.
 */
window.llenarModal = function (lote) {
    if (!lote) return;
    window.currentLoteInfo = lote;
    window.actualizarVista(lote);

    // si quieres abrir un modal:
    // $('#modalLotInfo').modal('show'); // descomenta si tienes bootstrap modal
};

const buttons = document.querySelectorAll(".btn-lot-merge");
buttons.forEach(btn => {
    btn.addEventListener("click", () => {

        // Crear objeto lote desde los data-attr
        const lote = {
            id: btn.dataset.id,
            area: btn.dataset.area,
            front: btn.dataset.front,
            depth: btn.dataset.depth,
            auditorium: btn.dataset.auditorio,
            banquet: btn.dataset.banquete,
            school: btn.dataset.escuela,
            horseshoe: btn.dataset.herradura,
            russian_table: btn.dataset.mesarusa
        };

        // Actualizar toda la vista usando tu función
        window.actualizarVista(lote);
    });
});

