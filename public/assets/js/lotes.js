document.addEventListener('DOMContentLoaded', function () {
    // Referencias a elementos
    const redirectCheckbox = document.getElementById('redirect');
    const redirectUrlInput = document.getElementById('redirect_url');
    const polygonForm = document.getElementById('polygonForm');
    const lotSelect = document.getElementById('modal_lot_id');
    const colorInput = document.getElementById('color');
    const colorActiveInput = document.getElementById('color_active');


    // Habilitar/deshabilitar inputs extra
    redirectCheckbox.addEventListener('change', function() {
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
    // Instancia del modal
    const modalEl = document.getElementById('polygonModal');
    const polygonModal = new bootstrap.Modal(modalEl);

    // Detectar click sobre cualquier elemento dentro de SVG
    const svgElements = document.querySelectorAll(selector);
    svgElements.forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();

            // Obtener ID del elemento clickeado o del <g> padre
            let elementId = this.id?.trim() || this.closest('g')?.id?.trim() || null;
            if (!elementId) return;

            document.getElementById('selectedElementId').innerText = elementId;
            document.getElementById('polygonId').value = elementId;

            // Limpiar select
            lotSelect.innerHTML = `<option value="">Cargando lotes...</option>`;

            // Usar currentLot para enviar IDs al endpoint
            const formData = new FormData();
            formData.append('project_id', window.currentLot.project_id);
            formData.append('phase_id', window.currentLot.phase_id);
            formData.append('stage_id', window.currentLot.stage_id);

            fetch(window.Laravel.routes.lotsFetch, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': window.Laravel.csrfToken }
            })
            .then(res => res.json())
            .then(data => {
                lotSelect.innerHTML = `<option value="">Seleccione un lote...</option>`;
                data.forEach(lot => {
                    const opt = document.createElement('option');
                    opt.value = lot.id;
                    opt.textContent = lot.name;
                    lotSelect.appendChild(opt);
                });
            })
            .catch(err => {
                console.error(err);
                lotSelect.innerHTML = `<option value="">Error al cargar lotes</option>`;
            });

            polygonModal.show();
        });
    });

    // Enviar formulario del modal
    polygonForm.addEventListener('submit', function(e) {
        e.preventDefault();
    
        const formData = new FormData(this);
    
        // ⚡ Siempre enviamos el desarrollo actual
        formData.set('desarrollo_id', window.idDesarrollo);
    
        // Agregar los demás IDs desde currentLot
        formData.append('project_id', window.currentLot.project_id ?? '');
        formData.append('phase_id', window.currentLot.phase_id ?? '');
        formData.append('stage_id', window.currentLot.stage_id ?? '');
        formData.append('lot_id', document.getElementById('modal_lot_id').value ?? '');
    
        // Enviar checkbox de redirección
        const redirectCheckbox = document.getElementById('redirect');
        formData.set('redirect', redirectCheckbox.checked ? 1 : 0);

        
        // Redirección + colores
        if (redirectCheckbox.checked) {
            formData.set('redirect', 1);
            formData.set('redirect_url', redirectUrlInput.value ?? '');
            formData.set('color', colorInput.value);
            formData.set('color_active', colorActiveInput.value);
        } else {
            formData.set('redirect', 0);
            formData.set('redirect_url', '');
            formData.set('color', '');
            formData.set('color_active', '');
        }
    
    
        fetch(window.Laravel.routes.lotesStore, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': window.Laravel.csrfToken }
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch {
                throw new Error('Respuesta no es JSON: ' + text);
            }
        })
        .then(data => {
            if (data.success) {
                polygonModal.hide();
                polygonForm.reset();
                document.getElementById('modal_lot_id').innerHTML = `<option value="">Seleccione un lote...</option>`;
                const redirectSelect = document.getElementById('redirect_url');
                redirectSelect.disabled = true;
                redirectSelect.value = '';
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo guardar'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Ocurrió un error al guardar el lote. Revisa la consola.');
        });
    });
});
