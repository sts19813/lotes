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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('desarrollo_id'); 

            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('phase_id');
            $table->unsignedBigInteger('stage_id');
            $table->string('lote_id'); // ID del lote en el sistema (puede ser código interno)
            $table->string('selectorSVG'); // ID del polígono en el SVG
            $table->boolean('redirect')->default(false); // Nuevo campo booleano
            $table->string('redirect_url')->nullable(); // Nuevo campo URL

            $table->timestamps();
            $table->foreign('desarrollo_id')->references('id')->on('desarrollos')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
