$(document).ready(function () {

    // ============================================================
    //  ABRIR MODAL AL DAR CLIC EN "QUIERO ORGANIZAR MI EVENTO"
    // ============================================================

    const btnSolicitar = document.getElementById('btnSolicitarEvento');
    if (btnSolicitar) {
        btnSolicitar.addEventListener('click', function () {

            // Mostrar modal de formulario
            let modal = new bootstrap.Modal(document.getElementById('downloadFormModal'));
            modal.show();

            // Guardamos el ID del salón actual en el campo hidden
            if (window.currentLot) {
                document.getElementById('lotNumberHidden').value = window.currentLot.id;
            }
        });
    }

    // ============================================================
    //  FORMULARIO: ENVÍO DEL LEAD
    // ============================================================

    const form = document.getElementById('downloadForm');
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // =====================================
        // OBTENER TEXTO DEL SALÓN SELECCIONADO
        // =====================================
        const salonSpan = document.querySelector('.salon-seleccionado');
        const salonHiddenInput = document.getElementById('selectedLotHidden');

        if (salonSpan && salonHiddenInput) {
            salonHiddenInput.value = salonSpan.textContent.trim();
        }

        // Serializar datos
        let formData = new FormData(form);

        // Mostrar loader profesional
        Swal.fire({
            title: 'Enviando información',
            text: 'Por favor espera...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Enviar al backend vía fetch
        fetch(form.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
            .then(res => {
                if (!res.ok) throw new Error("Error al enviar el formulario");
                return res.json();
            })
            .then(data => {

                // Cerrar modal Bootstrap
                let modal = bootstrap.Modal.getInstance(
                    document.getElementById('downloadFormModal')
                );
                if (modal) modal.hide();

                form.reset();

                // Alerta de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Solicitud enviada',
                    text: 'Un ejecutivo se pondrá en contacto contigo a la brevedad.',
                    confirmButtonText: 'Aceptar'
                });
            })
            .catch(err => {
                console.error(err);

                Swal.fire({
                    icon: 'error',
                    title: 'Error al enviar',
                    text: 'No fue posible enviar la información. Intenta nuevamente.',
                    confirmButtonText: 'Cerrar'
                });
            });
    });

});
