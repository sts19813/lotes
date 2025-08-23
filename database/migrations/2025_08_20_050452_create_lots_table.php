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
       Schema::create('desarrollos', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('total_lots')->default(1);
            $table->string('svg_image')->nullable(); // ruta del SVG
            $table->string('png_image')->nullable(); // ruta del PNG


             // 🔗 configuracion al api
             $table->unsignedBigInteger('project_id')->nullable();
             $table->unsignedBigInteger('phase_id')->nullable();
             $table->unsignedBigInteger('stage_id')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desarrollos');
    }
};
