/**
 * ================================================================
 * MÓDULO DE MODAL DE LOTES
 * ---------------------------------------------------------------
 * Contiene toda la lógica para construir y actualizar el modal 
 * dentro del iframe con los precios del lote y los diferentes 
 * modelos de financiamiento disponibles.
 * ================================================================
 */

/**
 * ================================================================
 * FUNCIÓN PRINCIPAL: llenarModal(lote)
 * ---------------------------------------------------------------
 * Llena el modal con la información del lote seleccionado, calcula
 * los precios base, enganche, mensualidades y configura los planes 
 * de financiamiento disponibles.
 * ================================================================
 */
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
window.actualizarVista = function(lot) {
    if (!lot) return;

    // PANEL DERECHO
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

    // METRICS BAR (ids únicos)
    setText("metric-area", lot.area);
    setText("metric-auditorium", lot.auditorium);
    setText("metric-banquet", lot.banquet);
    setText("metric-school", lot.school);

    // Tour link: agrega/elimina botón según exista
    const tourContainerId = 'btn-tour-virtual';
    const existing = document.getElementById(tourContainerId);
    if (lot.tour_link) {
        const html = `<div id="${tourContainerId}" class="mt-3">
                        <a href="${lot.tour_link}" target="_blank" class="btn btn-outline-dark rounded-pill px-4">
                            Ver Recorrido Virtual
                        </a>
                      </div>`;
        // preferimos colocarlo debajo de la sección principal (col-lg-5)
        const parent = document.querySelector('.col-lg-5');
        if (parent) {
            if (existing) {
                existing.outerHTML = html;
            } else {
                // insertar antes del texto de "¿Necesitas más espacio?" (o al final del padre)
                // acá lo añadimos al final del parent
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
window.llenarModal = function(lote) {
    if (!lote) return;
    window.currentLoteInfo = lote;
    window.actualizarVista(lote);

    // si quieres abrir un modal:
    // $('#modalLotInfo').modal('show'); // descomenta si tienes bootstrap modal
};

/**
 * Ejemplo: si quieres probar manualmente en consola:
 * window.llenarModal(window.preloadedLots[0])
 *
 * Asegúrate de que tu evento de selección de SVG llame a:
 * window.llenarModal(loteObject)
 *
 * Si tu sistema identifica lotes por id, puedes buscarlo en preloadedLots:
 * const lot = window.preloadedLots.find(l => l.id == SOME_ID);
 * window.llenarModal(lot);
 */