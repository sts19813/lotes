<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
          Schema::create('financiamientos', function (Blueprint $table) {
            $table->id();

            // Información general
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('visible')->default(true); // Público o Privado

            // Porcentajes principales
            $table->decimal('porcentaje_enganche', 8, 2)->nullable();
            $table->decimal('porcentaje_financiamiento', 8, 2)->nullable();
            $table->decimal('porcentaje_saldo', 8, 2)->nullable();

            // Descuentos e intereses
            $table->decimal('descuento_porcentaje', 8, 2)->nullable();
            $table->decimal('financiamiento_interes', 8, 2)->nullable();
            $table->decimal('financiamiento_cuota_apertura', 10, 2)->nullable();

            // Enganche
            $table->boolean('enganche_diferido')->default(false);
            $table->integer('enganche_num_pagos')->nullable();

            // Financiamiento
            $table->integer('financiamiento_meses')->nullable();

            // Anualidad
            $table->boolean('tiene_anualidad')->default(false);
            $table->decimal('porcentaje_anualidad', 8, 2)->nullable();
            $table->integer('numero_anualidades')->nullable();
            $table->integer('pagos_por_anualidad')->nullable();

            // Saldo / Contado
            $table->boolean('saldo_diferido')->default(false);
            $table->integer('saldo_num_pagos')->nullable();

            // Estado del registro
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financiamientos');
    }
};
