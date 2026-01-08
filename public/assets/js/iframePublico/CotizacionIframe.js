$(document).ready(function () {

    // ============================================================
    //  MODAL DE LEADS (se abre al descargar la cotización)
    // ============================================================

    const btn = document.getElementById('btnDescargarCotizacion');
    if (btn) {
        btn.addEventListener('click', function () {
            // Cerrar modal de lote (si está abierto)
            let polygonModal = bootstrap.Modal.getInstance(document.getElementById('polygonModal'));
            if (polygonModal) polygonModal.hide();

            // Mostrar modal de formulario de descarga
            let downloadFormModal = new bootstrap.Modal(document.getElementById('downloadFormModal'));
            downloadFormModal.show();
        });
    }

    // ============================================================
    //  FORMULARIO DE DESCARGA DE COTIZACIÓN
    // ============================================================

    const form = document.getElementById('downloadForm');
    if (!form) return; // Si no existe el formulario, no ejecutar nada más

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Obtener información del lote seleccionado
        const lote = window.currentLoteInfo;
        if (!lote) {
            alert("Error: no se seleccionó un lote");
            return;
        }

        // Referencias al botón y elementos del loader
        const submitBtn = document.getElementById("submitBtn");
        const btnText = submitBtn.querySelector(".btn-text");
        const spinner = submitBtn.querySelector(".spinner-border");

        // Mostrar loader y deshabilitar botón
        submitBtn.disabled = true;
        spinner.classList.remove("d-none");
        btnText.textContent = "Enviando...";

        // Obtener el plan actualmente seleccionado
        const planSeleccionado = window.currentPlan || {
            financing_months: window.currentLot.financing_months || 0,
            porcentaje_enganche: 30,
            descuento_porcentaje: 0,
            financiamiento_interes: 0
        };

        const precioReal = lote.area * lote.price_square_meter;

        // Valores ya calculados por el JS
        const precioFinal = window.currentPrecioFinal ?? precioReal;
        const plan = planSeleccionado;

        // Detectar tipo aplicado (MISMA REGLA DEL JS)
        let tipoAplicado = 'none';
        if ((plan.financiamiento_interes || 0) > 0) {
            tipoAplicado = 'interes';
        } else if ((plan.descuento_porcentaje || 0) > 0) {
            tipoAplicado = 'descuento';
        }

        // Enganche
        const enganchePorc = plan.porcentaje_enganche || 30;
        const engancheMonto = precioFinal * (enganchePorc / 100);

        // Saldo contra entrega
        const saldoPorc = plan.porcentaje_saldo || 0;
        const saldoMonto = precioFinal * (saldoPorc / 100);

        // Financiamiento
        const meses =  plan.financiamiento_meses || plan.financing_months || window.currentMeses|| 0;
        debugger
        const montoFinanciado = precioFinal - engancheMonto - saldoMonto;
        const mensualidad = window.currentMensualidad || (meses > 0 ? montoFinanciado / meses : montoFinanciado);

        const params = new URLSearchParams({
            
            /* =============================
             * DATOS DEL LOTE
             * ============================= */
            name: lote.name,
            area: lote.area,
            price_square_meter: window.currentPriceSquareMeter ?? 0,


            precio_real: precioReal,
            precio_final: precioFinal,
            tipo_aplicado: tipoAplicado,

            /* =============================
             * PLAN FINANCIERO
             * ============================= */
            porcentaje_enganche: enganchePorc,
            enganche_monto: engancheMonto,

            porcentaje_saldo: saldoPorc,
            saldo_monto: saldoMonto,

            monto_financiado: montoFinanciado,
            financing_months: meses,
            mensualidad: mensualidad,

            descuento_porcentaje: plan.descuento_porcentaje || 0,
            financiamiento_interes: plan.financiamiento_interes || 0,

            /* =============================
             * PLUSVALÍA (desde JS)
             * ============================= */
            annual_appreciation: lote.annual_appreciation || 0.15,
            plusvalia_total: window.plusvaliaTotal ?? 0,
            roi: window.roi ?? 0,
            valor_final: window.valorFinal5Anios ?? 0,

            /* =============================
             * IMÁGENES
             * ============================= */
            chepina: lote.chepina,

            /* =============================
             * LEAD
             * ============================= */
            lead_name: document.querySelector("#leadName").value,
            lead_phone: document.querySelector("#leadPhone").value,
            lead_email: document.querySelector("#leadEmail").value,
            city: document.querySelector("#leadCity").value,

            /* =============================
             * CONTEXTO DESARROLLO
             * ============================= */
            desarrollo_id: window.currentLot.id,
            desarrollo_name: window.currentLot.name,
            phase_id: window.currentLot.phase_id,
            stage_id: window.currentLot.stage_id,
            project_id: window.currentLot.project_id,
            source_type: window.currentLot.source_type
        });

        debugger

        const url = `/reports/generate?${params.toString()}`;

        // ============================================================
        //  Generar y descargar el PDF
        // ============================================================

        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error("Error al generar PDF");
                return res.blob();
            })
            .then(blob => {
                // Crear enlace temporal para descargar el PDF
                const blobUrl = URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = blobUrl;
                a.download = `cotizacion_${lote.name}.pdf`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(blobUrl);

                // Cerrar modal después de iniciar la descarga
                const downloadFormModalEl = document.getElementById("downloadFormModal");
                const downloadFormModal = bootstrap.Modal.getInstance(downloadFormModalEl);
                if (downloadFormModal) downloadFormModal.hide();

                // Limpiar formulario
                form.reset();
            })
            .catch(err => {
                console.error(err);
                alert("Ocurrió un error al generar la cotización.");
            })
            .finally(() => {
                // Restaurar estado del botón
                submitBtn.disabled = false;
                spinner.classList.add("d-none");
                btnText.textContent = "ENVIAR Y DESCARGAR";
            });
    });
});
