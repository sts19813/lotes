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

        // Serializar datos
        let formData = new FormData(form);

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

                // Cerrar modal
                let modal = bootstrap.Modal.getInstance(document.getElementById('downloadFormModal'));
                if (modal) modal.hide();

                // Limpiar formulario
                form.reset();

                // Mostrar mensaje
                alert("¡Gracias! Un ejecutivo se pondrá en contacto contigo.");
            })
            .catch(err => {
                console.error(err);
                alert("Ocurrió un error al enviar la información.");
            });

    });
});
