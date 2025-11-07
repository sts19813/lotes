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
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->integer('meses');
            $table->decimal('porcentaje_enganche', 5, 2);
            $table->decimal('interes_anual', 5, 2)->default(0);
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('monto_minimo', 12, 2)->nullable();
            $table->decimal('monto_maximo', 12, 2)->nullable();
            $table->enum('periodicidad_pago', ['mensual', 'bimestral', 'trimestral'])->default('mensual');
            $table->decimal('cargo_apertura', 12, 2)->nullable();
            $table->decimal('penalizacion_mora', 5, 2)->nullable();
            $table->integer('plazo_gracia_meses')->nullable();
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
