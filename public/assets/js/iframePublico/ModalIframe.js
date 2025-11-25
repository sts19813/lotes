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
function llenarModal(lote) {
    window.currentLoteInfo = lote;

    // ------------------------------------------------------------
    //  IMAGEN DEL LOTE
    // ------------------------------------------------------------
    const chepinaImg = document.getElementById("chepinaIMG");
    if (chepinaImg) chepinaImg.src = lote.chepina || "/assets/img/CHEPINA.svg";

    // ------------------------------------------------------------
    //  DATOS BÁSICOS DEL LOTE
    // ------------------------------------------------------------
    document.querySelector("#loteName").textContent = lote.name;
    document.querySelector("#lotearea").textContent = `${parseFloat(lote.area).toFixed(2)} m²`;
    document.querySelector("#lotePrecioMetro").textContent = formatMoney(lote.price_square_meter);

    const precioTotal = lote.area * lote.price_square_meter;
    document.querySelector("#lotePrecioTotal").textContent = formatMoney(precioTotal);

    // ------------------------------------------------------------
    //  CALCULAR MENSUALIDAD PARA CADA PLAN
    // ------------------------------------------------------------
    document.querySelectorAll(".plan-box").forEach(box => {
        const plan = JSON.parse(box.dataset.financing || "{}");
        const mesesPlan = parseInt(
            box.dataset.meses || 
            plan.financiamiento_meses || 
            plan.months || 
            plan.financing_months || 
            60
        );

        const enganchePorc = parseFloat(plan.down_payment_percent || 30);
        const engancheMonto = precioTotal * (enganchePorc / 100);
        const mensualidad = (precioTotal - engancheMonto) / mesesPlan;

        const monthlyElem = box.querySelector(".monthlyPayment");
        if (monthlyElem) monthlyElem.textContent = formatMoney(mensualidad);
    });

    // ------------------------------------------------------------
    //  ACTIVAR PLAN POR DEFECTO Y GENERAR PROYECCIÓN
    // ------------------------------------------------------------
    const firstPlanBox = document.querySelector(".plan-box.active") || document.querySelector(".plan-box");
    if (firstPlanBox) {
        actualizarFinanciamiento(firstPlanBox, precioTotal);
    }

    // ------------------------------------------------------------
    //  EVENTOS DE SELECCIÓN DE PLANES
    // ------------------------------------------------------------
    document.querySelectorAll(".plan-box").forEach(box => {
        box.addEventListener("click", function() {
            // Remover clase 'active' de todos los planes
            document.querySelectorAll(".plan-box").forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            // Actualizar financiamiento y tabla de proyección
            actualizarFinanciamiento(this, precioTotal);
        });
    });
}

/**
 * Actualiza la información del financiamiento dentro del modal de cotización.
 * Aplica descuento, calcula enganche sobre el precio con descuento,
 * y aplica intereses únicamente sobre el monto financiado.
 */
function actualizarFinanciamiento(box, precioTotal) {
    const plan = JSON.parse(box.dataset.financing || "{}");

    // ------------------------------------------------------------
    // Extraer valores del plan (según tus campos reales)
    // ------------------------------------------------------------
    const meses = parseInt(
        plan.financiamiento_meses ||
        plan.months ||
        plan.financing_months ||
        60
    );

    const enganchePorc = parseFloat(plan.porcentaje_enganche || 30);
    const descuentoPorc = parseFloat(plan.descuento_porcentaje || 0);
    const interesPorc = parseFloat(plan.financiamiento_interes || 0);

    // ------------------------------------------------------------
    // Aplicar DESCUENTO al precio total
    // ------------------------------------------------------------
    const precioConDescuento = precioTotal - (precioTotal * (descuentoPorc / 100));

    // ------------------------------------------------------------
    // Calcular ENGANCHE sobre el precio con descuento
    // ------------------------------------------------------------
    const engancheMonto = precioConDescuento * (enganchePorc / 100);

    // ------------------------------------------------------------
    // DIFERIR enganche (si aplica)
    //
    const divDiferido = document.getElementById("divloteDiferido");
    const diferidoElem = document.getElementById("loteDiferido");
    if (divDiferido && diferidoElem) {

        const engancheDiferido = plan.enganche_diferido == 1 || plan.enganche_diferido === true;
        const numPagos = parseInt(plan.enganche_num_pagos || 0);

        // Si el plan tiene "enganche diferido"
        if (engancheDiferido && numPagos > 0) {

            const pagoDiferido = engancheMonto / numPagos;

            divDiferido.style.display = "block";
            diferidoElem.innerHTML =
                `<span class="pagos-text">${numPagos} pagos de</span>
                <span class="pago-monto">${formatMoney(pagoDiferido)}</span>`;

        } else {
            divDiferido.style.display = "none";
        }
    }

    // ------------------------------------------------------------
    // calcular saldo y DIFERIR SALDO (si aplica)
    // ------------------------------------------------------------
    const saldoPorc = parseFloat(plan.porcentaje_saldo || 0);
    const saldoMonto = precioConDescuento * (saldoPorc / 100);
    const divSaldoDiferido = document.getElementById("divSaldoDiferido");
    const saldoDiferidoElem = document.getElementById("loteSaldoDiferido");
    if (divSaldoDiferido && saldoDiferidoElem) {

        const saldoDiferido = plan.saldo_diferido == 1 || plan.saldo_diferido === true;
        const saldoNumPagos = parseInt(plan.saldo_num_pagos || 0);

        if (saldoDiferido && saldoNumPagos > 0 && saldoMonto > 0) {

            const pagoSaldoDiferido = saldoMonto / saldoNumPagos;

            divSaldoDiferido.style.display = "block";
            saldoDiferidoElem.innerHTML =
            `${`<span class="saldo-num-pagos">${saldoNumPagos} pagos de </span>`}<span class="saldo-monto">${formatMoney(pagoSaldoDiferido)}</span>`;
        } else {
            divSaldoDiferido.style.display = "none";
        }
    }
    // ------------------------------------------------------------
    // Calcular monto FINANCIADO (precio con descuento - enganche)
    // ------------------------------------------------------------
    const montoFinanciadoBase = precioConDescuento - engancheMonto - saldoMonto;

    // ------------------------------------------------------------
    // Aplicar INTERESES sobre el monto financiado
    // ------------------------------------------------------------
    const interesMonto = montoFinanciadoBase * (interesPorc / 100);
    const montoFinanciado = montoFinanciadoBase + interesMonto;

    // ------------------------------------------------------------
    // Calcular mensualidad final
    // ------------------------------------------------------------
    const mensualidad = montoFinanciado / meses;

    // ------------------------------------------------------------
    // Mostrar u ocultar secciones de descuento/intereses
    // ------------------------------------------------------------
    const divDescuento = document.getElementById("divloteDescuento");
    const divIntereses = document.getElementById("divloteIntereses");
    if (divDescuento) divDescuento.style.display = descuentoPorc > 0 ? "block" : "none";
    if (divIntereses) divIntereses.style.display = interesPorc > 0 ? "block" : "none";

    // ------------------------------------------------------------
    // CALCULAR SALDO (Contra Entrega)
    // ------------------------------------------------------------
    const divSaldo = document.getElementById("divSaldo");

    // Para plantilla COMPLETA default
    const saldoPorcentajeElem = document.getElementById("SaldoPorcentaje");
    const saldoMontoElem = document.getElementById("SaldoMonto");
    const saldoSimpleElem = document.getElementById("Saldo");
    if (divSaldo) {
        if (saldoMonto > 0) {
            divSaldo.style.display = "block";

            //  default 
            if (saldoPorcentajeElem && saldoMontoElem) {
                saldoPorcentajeElem.textContent = `${saldoPorc}%`;
                saldoMontoElem.textContent = formatMoney(saldoMonto);
            }

            // emedos
            if (saldoSimpleElem) {
                saldoSimpleElem.textContent = `${saldoPorc}% (${formatMoney(saldoMonto)})`;
            }

        } else {
            divSaldo.style.display = "none";
        }
    }

    // ------------------------------------------------------------
    // Actualizar elementos del DOM con los valores calculados
    // ------------------------------------------------------------
    // Enganche
    const selectEnganche = document.querySelector("#planSelectEnganche");
    if (selectEnganche) selectEnganche.innerHTML = `<option>${enganchePorc}% de enganche</option>`;

    const labelStrong = document.querySelector("p.label strong");
    if (labelStrong) labelStrong.textContent = formatMoney(engancheMonto);

    const tabEnganche = document.getElementById("loteEnganchePorcentaje");
    if (tabEnganche) tabEnganche.textContent = `${enganchePorc}%`;

    const loteContraEntrega = document.getElementById("loteContraEntrega");
    if (loteContraEntrega) loteContraEntrega.textContent = formatMoney(engancheMonto);

    // Descuento e intereses
    const loteDescuento = document.getElementById("loteDescuento");
    if (loteDescuento) loteDescuento.textContent = `${descuentoPorc}%`;

    const loteIntereses = document.getElementById("loteIntereses");
    if (loteIntereses) loteIntereses.textContent = `${interesPorc}%`;

    // Meses y mensualidad
    const loteFinanciamiento = document.getElementById("loteFinanciamiento");
    if (loteFinanciamiento) loteFinanciamiento.textContent = `${meses} meses`;

    const loteMensualidad = document.getElementById("loteMensualidad");
    if (loteMensualidad) loteMensualidad.textContent = formatMoney(mensualidad);

    // Montos totales
    const loteMontoFinanciado = document.getElementById("loteMontoFinanciado");
    if (loteMontoFinanciado) loteMontoFinanciado.textContent = formatMoney(montoFinanciado);

    const loteCostoTotal = document.getElementById("loteCostoTotal");
    if (loteCostoTotal) loteCostoTotal.textContent = formatMoney(precioConDescuento + interesMonto);

    // Actualizar texto dentro del plan seleccionado
    if (box.querySelector) {
        const monthlyElem = box.querySelector(".monthlyPayment");
        if (monthlyElem) monthlyElem.textContent = formatMoney(mensualidad);
    }
    // ------------------------------------------------------------
    // Guardar datos globales y actualizar proyección
    // ------------------------------------------------------------
    window.currentPlan = plan;
    window.currentMeses = meses;
    window.currentMensualidad = mensualidad;

    actualizarProyeccion(precioTotal, window.currentLot?.plusvalia || 0.15, plan);
}



/**
 * ================================================================
 * FUNCIÓN: actualizarProyeccion(precioTotal, plusvaliaRate, plan)
 * ---------------------------------------------------------------
 * Calcula y muestra los valores de plusvalía, ROI y proyección anual
 * del lote a 5 años con base en el plan seleccionado.
 * ================================================================
 */
function actualizarProyeccion(precioTotal, plusvaliaRate, plan) {

    // ------------------------------------------------------------
    // CONVERSIÓN Y CÁLCULOS BASE
    // ------------------------------------------------------------
    plusvaliaRate = parseFloat(plusvaliaRate);
    if (isNaN(plusvaliaRate)) plusvaliaRate = 0.15; // valor por defecto

    const valorFinal = precioTotal * Math.pow(1 + plusvaliaRate, 5);
    const plusvaliaTotal = valorFinal - precioTotal;
    const roi = ((valorFinal - precioTotal) / precioTotal) * 100;

    // ------------------------------------------------------------
    // ACTUALIZAR VALORES RESUMIDOS
    // ------------------------------------------------------------
    const bgVerde = document.querySelector(".background-verde h6");
    if (bgVerde) bgVerde.textContent = formatMoney(plusvaliaTotal);

    const bgAzul = document.querySelector(".background-azul h6");
    if (bgAzul) bgAzul.textContent = formatPercent(roi);

    const bgMorado = document.querySelector(".background-morado h6");
    if (bgMorado) bgMorado.textContent = formatPercent(plusvaliaRate * 100);

    const bgAmarillo = document.querySelector(".background-amarillo h6");
    if (bgAmarillo) bgAmarillo.textContent = formatMoney(valorFinal);

    // ------------------------------------------------------------
    // TABLA DE PROYECCIÓN (5 años)
    // ------------------------------------------------------------
    const tbody = document.querySelector(".table-responsive tbody");
    if (!tbody) return;

    tbody.innerHTML = "";

    const totalAnios = 5;
    const meses = window.currentMeses || 60;
    const engancheMonto = precioTotal * ((plan?.down_payment_percent || 30) / 100);
    const mensualidad = window.currentMensualidad || (precioTotal - engancheMonto) / meses;

    for (let year = 0; year <= totalAnios; year++) {
        const valorProp = precioTotal * Math.pow(1 + plusvaliaRate, year);

        // --- Calcular meses pagados hasta el año actual ---
        let mesesPagados = 0;
        if (year === 0) mesesPagados = 0;
        else if (year === 1) mesesPagados = Math.min(meses, 11);
        else mesesPagados = Math.min(meses, (year - 1) * 12 + 11);

        // --- Calcular montos y ROI ---
        const montoPagado = engancheMonto + (mensualidad * mesesPagados);
        const plusvaliaAcum = valorProp - precioTotal;
        const roiAnual = ((valorProp - precioTotal) / precioTotal) * 100;

        const plusColor = plusvaliaAcum > 0 ? "text-success fw-semibold" : "";
        const roiColor = roiAnual > 0 ? "text-primary fw-semibold" : "";

        // --- Generar fila ---
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${year}</td>
            <td>${formatMoney(valorProp)}</td>
            <td>${formatMoney(montoPagado)}</td>
            <td class="${plusColor}">+${formatMoney(plusvaliaAcum)}</td>
            <td class="${roiColor}">${formatPercent(roiAnual)}</td>
        `;
        tbody.appendChild(tr);
    }
}

/**
 * ================================================================
 * FUNCIONES AUXILIARES DE FORMATO
 * ================================================================
 */

// Formatea valores numéricos a formato monetario MXN
function formatMoney(value) {
    return `$${value.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

// Formatea valores numéricos a porcentaje con 2 decimales
function formatPercent(value) {
    return `${value.toFixed(2)}%`;
}

//funcion para inicializar el select de los planes de financiamiento esto no se ejecuta si la plantilla no tiene el select
function inicializarSelect() {
    const planSelect = document.getElementById("planSelect");
    if (!planSelect || !window.currentLoteInfo) return;

    // Tomamos el primer plan por defecto
    const precioTotal = window.currentLoteInfo.area * window.currentLoteInfo.price_square_meter;

    // Función para actualizar plan usando tu JS original
    const actualizarPlanDesdeSelect = () => {
        const selectedOption = planSelect.selectedOptions[0];
        if (!selectedOption) return;

        const financing = JSON.parse(selectedOption.dataset.financing || "{}");

        // Creamos un "box temporal" para no tocar tu JS
        const tempBox = { dataset: { financing: JSON.stringify(financing) } };
        actualizarFinanciamiento(tempBox, precioTotal);
    };

    // Evento change
    planSelect.addEventListener("change", actualizarPlanDesdeSelect);

    // Inicializamos al primer plan
    planSelect.dispatchEvent(new Event('change'));
}