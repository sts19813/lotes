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
    
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->string('lote_id')->nullable();
            $table->string('selectorSVG');
            $table->boolean('redirect')->default(false);
            $table->string('redirect_url')->nullable();
    
            // 🎨 Colores
            $table->string('color', 9)->nullable(); 
            $table->string('color_active', 9)->nullable(); 
    
            $table->timestamps();
    
            $table->foreign('desarrollo_id')
                  ->references('id')
                  ->on('desarrollos')
                  ->onDelete('cascade');
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
