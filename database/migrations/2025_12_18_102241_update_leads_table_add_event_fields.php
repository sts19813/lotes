<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {

            $table->id();

            // =========================
            // DATOS DE CONTACTO
            // =========================
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email');
            $table->string('phone', 20);

            // =========================
            // INFORMACIÓN DEL EVENTO
            // =========================
            $table->string('event_type')->nullable();
            $table->date('estimated_date')->nullable();
            $table->text('message')->nullable();

            // =========================
            // CONTEXTO DEL LOTE
            // =========================
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->string('lot_number')->nullable();

            // =========================
            // TEXTO DEL SALÓN / LOTES
            // =========================
            $table->string('lots')->nullable();

            $table->timestamps();

            // =========================
            // ÍNDICES
            // =========================
            $table->index('project_id');
            $table->index('phase_id');
            $table->index('stage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
