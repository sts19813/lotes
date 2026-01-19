let lotSelect;      // select nativo
let lotTomSelect;  // instancia Tom Select

document.addEventListener('DOMContentLoaded', function () {

    // =============================
    // REFERENCIAS
    // =============================
    const redirectCheckbox = document.getElementById('redirect');
    const redirectUrlInput = document.getElementById('redirect_url');
    const polygonForm = document.getElementById('polygonForm');
    lotSelect = document.getElementById('modal_lot_id');
    const colorInput = document.getElementById('color');
    const colorActiveInput = document.getElementById('color_active');

    // =============================
    // TOGGLE REDIRECT
    // =============================
    redirectCheckbox.addEventListener('change', function () {
        const enabled = this.checked;
        redirectUrlInput.disabled = !enabled;
        colorInput.disabled = !enabled;
        colorActiveInput.disabled = !enabled;

        if (!enabled) {
            redirectUrlInput.value = '';
            colorInput.value = '#34c759ff';
            colorActiveInput.value = '#2c7be5ff';
        }
    });

    // =============================
    // MODAL
    // =============================
    const modalEl = document.getElementById('polygonModal');
    const polygonModal = new bootstrap.Modal(modalEl);

    // =============================
    // SVG CLICK
    // =============================
    document.querySelectorAll(selector).forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();

            const elementId =
                this.id?.trim() ||
                this.closest('g')?.id?.trim() ||
                null;

            if (!elementId) return;

            document.getElementById('selectedElementId').innerText = elementId;
            document.getElementById('polygonId').value = elementId;

            loadLotsForCurrentSource();
            polygonModal.show();
        });
    });

    // =============================
    // FORM SUBMIT
    // =============================
    polygonForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        formData.set('desarrollo_id', window.idDesarrollo);
        formData.set('project_id', window.currentLot.project_id ?? '');
        formData.set('phase_id', window.currentLot.phase_id ?? '');
        formData.set('stage_id', window.currentLot.stage_id ?? '');
        formData.set('lot_id', lotTomSelect?.getValue() ?? '');

        formData.set('redirect', redirectCheckbox.checked ? 1 : 0);

        if (redirectCheckbox.checked) {
            formData.set('redirect_url', redirectUrlInput.value ?? '');
            formData.set('color', colorInput.value);
            formData.set('color_active', colorActiveInput.value);
        } else {
            formData.set('redirect_url', '');
            formData.set('color', '');
            formData.set('color_active', '');
        }

        fetch(window.Laravel.routes.lotesStore, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': window.Laravel.csrfToken }
        })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message || 'Error al guardar');
                    return;
                }

                polygonModal.hide();
                polygonForm.reset();
                lotTomSelect?.clear();
                location.reload();
            })
            .catch(err => {
                console.error(err);
                alert('Error al guardar');
            });
    });

    // =============================
    // LOAD LOTS
    // =============================
    function loadLotsForCurrentSource() {

        if (window.currentLot.source_type === 'adara') {

            const fd = new FormData();
            fd.append('project_id', window.currentLot.project_id);
            fd.append('phase_id', window.currentLot.phase_id);
            fd.append('stage_id', window.currentLot.stage_id);

            fetch(window.Laravel.routes.lotsFetch, {
                method: 'POST',
                body: fd,
                headers: { 'X-CSRF-TOKEN': window.Laravel.csrfToken }
            })
                .then(r => r.json())
                .then(lots => initLotSelect(lots))
                .catch(() => initLotSelect([]));

        } else {
            initLotSelect(window.preloadedLots || []);
        }
    }
});

// =============================
// FILTRO LOTES NO MAPEADOS
// =============================
function getUnmappedLots(allLots, dbLotes) {
    const mappedIds = new Set(
        (dbLotes || []).map(l => String(l.lote_id))
    );

    return allLots.filter(l => !mappedIds.has(String(l.id)));
}

// =============================
// INIT TOM SELECT
// =============================
function initLotSelect(lots) {

    const unmappedLots = getUnmappedLots(lots, window.dbLotes);

    if (lotTomSelect) {
        lotTomSelect.destroy();
    }

    lotSelect.innerHTML = '';

    lotTomSelect = new TomSelect(lotSelect, {
        options: unmappedLots.map(l => ({
            value: l.id,
            text: l.name
        })),
        placeholder: 'Buscar o seleccionar lote...',
        allowEmptyOption: true,
        maxOptions: 500,
        searchField: ['text']
    });
}
