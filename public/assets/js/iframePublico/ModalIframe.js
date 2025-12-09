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
    if (chepinaImg) {
        let rutaChepina = lote.chepina || "/assets/img/CHEPINA.svg";
        debugger

        // Si el lote es de Naboo, agregar prefijo a la ruta
        if (window.currentLot.source_type === "naboo") {
            rutaChepina = "/chepinas/" + lote.chepina;
        }

        chepinaImg.src = rutaChepina;
    }
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
    // NUEVA REGLA: si hay INTERÉS -> se aplica INTERÉS al precio total (y se ignora descuento).
    // Si NO hay interés pero hay descuento -> aplicar DESCUENTO.
    // Si ninguno -> precio base.
    // ------------------------------------------------------------
    // precioTotal: original calculado en llenarModal = area * price_square_meter
    let precioFinal = precioTotal;             // precio que usaremos para todo (enganches, saldo, mensualidades)
    let tipoAplicado = 'none';                // 'interes' | 'descuento' | 'none'
    const loteInfo = window.currentLoteInfo || {}; // por si necesitas acceder a area / precio m2 original

    if (!isFinite(precioFinal)) precioFinal = 0;

    if (interesPorc > 0) {
        // Aplicar interés SOBRE EL PRECIO TOTAL (sube el precio por m2 y total)
        precioFinal = precioTotal + (precioTotal * (interesPorc / 100));
        tipoAplicado = 'interes';
    } else if (descuentoPorc > 0) {
        // Aplicar descuento SOBRE EL PRECIO TOTAL
        precioFinal = precioTotal - (precioTotal * (descuentoPorc / 100));
        tipoAplicado = 'descuento';
    } else {
        tipoAplicado = 'none';
    }

    // ------------------------------------------------------------
    // Calcular enganche y saldo sobre precioFinal
    // ------------------------------------------------------------
    const engancheMonto = precioFinal * (enganchePorc / 100);

    // ------------------------------------------------------------
    // DIFERIR enganche (tu lógica original NO SE TOCA)
    // ------------------------------------------------------------
    const divDiferido = document.getElementById("divloteDiferido");
    const diferidoElem = document.getElementById("loteDiferido");

    if (divDiferido && diferidoElem) {
        const engancheDiferido = plan.enganche_diferido == 1 || plan.enganche_diferido === true;
        const numPagos = parseInt(plan.enganche_num_pagos || 0);

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
    // calcular saldo y DIFERIR SALDO (TAMPOCO SE TOCA)
    // ------------------------------------------------------------
    const saldoPorc = parseFloat(plan.porcentaje_saldo || 0);
    const saldoMonto = precioFinal * (saldoPorc / 100);

    const divSaldoDiferido = document.getElementById("divSaldoDiferido");
    const saldoDiferidoElem = document.getElementById("loteSaldoDiferido");

    if (divSaldoDiferido && saldoDiferidoElem) {
        const saldoDiferido = plan.saldo_diferido == 1 || plan.saldo_diferido === true;
        const saldoNumPagos = parseInt(plan.saldo_num_pagos || 0);

        if (saldoDiferido && saldoNumPagos > 0 && saldoMonto > 0) {
            const pagoSaldoDiferido = saldoMonto / saldoNumPagos;

            divSaldoDiferido.style.display = "block";
            saldoDiferidoElem.innerHTML =
                `<span class="saldo-num-pagos">${saldoNumPagos} pagos de </span>
                <span class="saldo-monto">${formatMoney(pagoSaldoDiferido)}</span>`;
        } else {
            divSaldoDiferido.style.display = "none";
        }
    }

    // ------------------------------------------------------------
    // Monto financiado = precioFinal - enganche - saldo
    // ------------------------------------------------------------
    const montoFinanciado = precioFinal - engancheMonto - saldoMonto;

    // ------------------------------------------------------------
    // Mensualidad final (si meses = 0 evitar división)
    // ------------------------------------------------------------
    const mensualidad = (meses > 0) ? (montoFinanciado / meses) : montoFinanciado;

    // ------------------------------------------------------------
    // Mostrar u ocultar secciones (NO SE TOCA)
    // ------------------------------------------------------------
    const divDescuento = document.getElementById("divloteDescuento");
    const divIntereses = document.getElementById("divloteIntereses");
    if (divDescuento) divDescuento.style.display = (descuentoPorc > 0 && tipoAplicado === 'descuento') ? "block" : "none";
    if (divIntereses) divIntereses.style.display = (interesPorc > 0 && tipoAplicado === 'interes') ? "block" : "none";

    // ------------------------------------------------------------
    // CALCULAR SALDO CONTRA ENTREGA (NO SE TOCA)
    // ------------------------------------------------------------
    const divSaldo = document.getElementById("divSaldo");
    const saldoPorcentajeElem = document.getElementById("SaldoPorcentaje");
    const saldoMontoElem = document.getElementById("SaldoMonto");
    const saldoSimpleElem = document.getElementById("Saldo");

    if (divSaldo) {
        if (saldoMonto > 0) {
            divSaldo.style.display = "block";

            if (saldoPorcentajeElem && saldoMontoElem) {
                saldoPorcentajeElem.textContent = `${saldoPorc}%`;
                saldoMontoElem.textContent = formatMoney(saldoMonto);
            }
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
    const selectEnganche = document.querySelector("#planSelectEnganche");
    if (selectEnganche) selectEnganche.innerHTML = `<option>${enganchePorc}% de enganche</option>`;

    const labelStrong = document.querySelector("p.label strong");
    if (labelStrong) labelStrong.textContent = formatMoney(engancheMonto);

    const tabEnganche = document.getElementById("loteEnganchePorcentaje");
    if (tabEnganche) tabEnganche.textContent = `${enganchePorc}%`;

    const loteContraEntrega = document.getElementById("loteContraEntrega");
    if (loteContraEntrega) loteContraEntrega.textContent = formatMoney(engancheMonto);

    const loteDescuento = document.getElementById("loteDescuento");
    if (loteDescuento) loteDescuento.textContent = `${descuentoPorc}%`;

    const loteIntereses = document.getElementById("loteIntereses");
    if (loteIntereses) loteIntereses.textContent = `${interesPorc}%`;

    if (mensualidad <= 0) {
        if (loteFinanciamiento) loteFinanciamiento.textContent = `${meses} meses`;
        if (loteMensualidad) loteMensualidad.textContent = "";
        document.querySelector(".MensualesText").style.display = "none";
    } else {
        if (loteFinanciamiento) loteFinanciamiento.textContent = `${meses} meses`;
        if (loteMensualidad) loteMensualidad.textContent = formatMoney(mensualidad);

        const mensualesText = document.querySelector(".MensualesText");

        if (mensualesText) {
            mensualesText.style.display = "flex";
        }
    }
    const loteMontoFinanciado = document.getElementById("loteMontoFinanciado");
    if (loteMontoFinanciado) loteMontoFinanciado.textContent = formatMoney(montoFinanciado);

    // Mostrar costo total final (precioFinal) en la UI
    const loteCostoTotal = document.getElementById("loteCostoTotal");
    if (loteCostoTotal) loteCostoTotal.textContent = formatMoney(precioFinal);

    // ------------------------------------------------------------
    // ACTUALIZAR VISUAL DE PRECIO M2 Y PRECIO TOTAL (según lo pedido)
    // - Si tipoAplicado === 'interes' -> mostrar original tachado + precio con interés
    // - Si tipoAplicado === 'descuento' -> mostrar original tachado + precio con descuento
    // - Si 'none' -> mostrar normal
    // ------------------------------------------------------------
    const precioMetroEl = document.getElementById("lotePrecioMetro");
    const precioTotalEl = document.getElementById("lotePrecioTotal");

    // precio m2 original desde window.currentLoteInfo (si existe), sino calcular desde precioTotal/area
    let precioM2Original = null;
    if (loteInfo && loteInfo.price_square_meter) {
        precioM2Original = parseFloat(loteInfo.price_square_meter);
    } else if (loteInfo && loteInfo.area && loteInfo.area > 0) {
        precioM2Original = precioTotal / loteInfo.area;
    } else {
        precioM2Original = precioTotal; // fallback
    }

    // precio m2 ajustado según lo aplicado
    let precioM2Ajustado = (loteInfo && loteInfo.area && loteInfo.area > 0) ? (precioFinal / loteInfo.area) : precioM2Original;

    if (precioMetroEl) {
        precioMetroEl.textContent = formatMoney(precioM2Ajustado);
    }

    if (precioTotalEl) {
        precioTotalEl.textContent = formatMoney(precioFinal);
    }

    // ------------------------------------------------------------
    // Actualizar texto dentro del plan seleccionado (monthly text)
    // ------------------------------------------------------------
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
    window.currentPrecioFinal = precioFinal;

    // Llamada original: pasar precioTotal original (para proyeccion) o pasar precioFinal?
    // Dejamos como original para que proyecciones históricas sigan igual,
    // pero si quieres que proyecciones usen el precioFinal, cambialo aquí:
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
/**
 * PROYECCIÓN REAL
 * precioReal = valor sin interés ni descuento
 * precioFinal = valor con interés o descuento (para pagos)
 */
function actualizarProyeccion(precioReal, plusvaliaRate, plan) {

    // ---------------------------------------
    // BASES
    // ---------------------------------------
    plusvaliaRate = parseFloat(plusvaliaRate);
    if (isNaN(plusvaliaRate)) plusvaliaRate = 0.15;

    const valorFinal5Anios = precioReal * Math.pow(1 + plusvaliaRate, 5);
    const plusvaliaTotal = valorFinal5Anios - precioReal;
    const roi = ((valorFinal5Anios - precioReal) / precioReal) * 100;

    // ---------------------------------------
    // CARDS DE RESUMEN
    // ---------------------------------------
    document.querySelector(".background-verde h6").textContent = formatMoney(plusvaliaTotal);
    document.querySelector(".background-azul h6").textContent = formatPercent(roi);
    document.querySelector(".background-morado h6").textContent = formatPercent(plusvaliaRate * 100);
    document.querySelector(".background-amarillo h6").textContent = formatMoney(valorFinal5Anios);

    // ---------------------------------------
    // TABLA: valor de propiedad SIEMPRE sobre precioReal
    //         pagos SIEMPRE sobre precioFinal
    // ---------------------------------------
    const tbody = document.querySelector(".table-responsive tbody");
    tbody.innerHTML = "";

    const meses = window.currentMeses || 60;

    // precioFinal = precio con interés o descuento
    const precioFinal = window.currentPrecioFinal || precioReal;

    const enganchePorc = parseFloat(plan?.porcentaje_enganche || 30);
    const saldoPorc = parseFloat(plan?.porcentaje_saldo || 0);

    const engancheMonto = precioFinal * (enganchePorc / 100);
    const saldoMonto = precioFinal * (saldoPorc / 100);
    const montoFinanciado = precioFinal - engancheMonto - saldoMonto;

    const mensualidad = window.currentMensualidad || (montoFinanciado / meses);

    for (let year = 0; year <= 5; year++) {

        // --- Valor de propiedad siempre con precio REAL ---
        const valorProp = precioReal * Math.pow(1 + plusvaliaRate, year);

        // --- Meses pagados ---
        let mesesPagados = 0;
        if (year === 1) mesesPagados = Math.min(meses, 11);
        if (year > 1) mesesPagados = Math.min(meses, (year - 1) * 12 + 11);

        // --- Pagos siempre sobre PRECIO FINAL ---
        const montoPagado = engancheMonto + saldoMonto + (mensualidad * mesesPagados);

        // --- Plusvalía real ---
        const plusvaliaAcum = valorProp - precioReal;
        const roiAnual = ((valorProp - precioReal) / precioReal) * 100;

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${year}</td>
            <td>${formatMoney(valorProp)}</td>
            <td>${formatMoney(montoPagado)}</td>
            <td class="text-success fw-semibold">+${formatMoney(plusvaliaAcum)}</td>
            <td class="text-primary fw-semibold">${formatPercent(roiAnual)}</td>
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