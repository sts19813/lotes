document.addEventListener("DOMContentLoaded", function () {
    // Verificamos que existan las variables
    if (!window.dbLotes || !window.preloadedLots) return;

    debugger

    window.dbLotes.forEach(dbLote => {
        // Buscar el lote en los preloadedLots usando lote_id
        const matchedLot = window.preloadedLots.find(l => l.id == dbLote.lote_id);
        if (!matchedLot) return;

        // Obtener el selector del SVG
        const selector = dbLote.selectorSVG;
        if (!selector) return;

        const svgElement = document.querySelector(`#${selector}`);
        if (!svgElement) return;

        // Determinar el color según status
        let fillColor;
        switch (matchedLot.status) {
            case 'for_sale':
                fillColor = 'rgba(52, 199, 89, 0.7)'; // verde
                break;
            case 'sold':
                fillColor = 'rgba(200, 0, 0, 0.6)'; // rojo
                break;
            case 'reserved':
                fillColor = 'rgba(255, 200, 0, 0.6)'; // amarillo
                break;
            default:
                fillColor = 'rgba(100, 100, 100, 0.4)'; // gris neutro
        }

        // Aplicar el color al SVG
        const allChildren = svgElement.querySelectorAll('*');

        // Recorrerlos y aplicar el fill con !important
        allChildren.forEach(el => {
            el.style.setProperty('fill', fillColor, 'important');
        });

        // Si quieres aplicar también al elemento padre
        svgElement.style.setProperty('fill', fillColor, 'important');

        // Si quieres guardar la info completa en el dataset
        svgElement.dataset.loteInfo = JSON.stringify(matchedLot);
    });
});