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
        Schema::table('lots', function (Blueprint $table) {
            // Nuevos campos
            $table->string('area2')->nullable();               // Área
            $table->string('front2')->nullable();              // Frente
            $table->string('depth2')->nullable();              // Fondo
            $table->string('height')->nullable();          // Altura
            $table->string('floor_resistance')->nullable();           // Resistencia de piso
            $table->string('hanging_point')->nullable();              // Punto de colgado

            // Capacidades
            $table->string('auditorium')->nullable();
            $table->string('school')->nullable();
            $table->string('horseshoe')->nullable();                 // Herradura
            $table->string('russian_table')->nullable();             // Mesa Rusa
            $table->string('banquet')->nullable();
            $table->string  ('cocktail')->nullable();

            // Link recorrido
            $table->string('tour_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn([
                'area2',
                'front2',
                'depth2',
                'height',
                'floor_resistance',
                'hanging_point',
                'auditorium',
                'school',
                'horseshoe',
                'russian_table',
                'banquet',
                'cocktail',
                'tour_link',
            ]);
        });
    }
};
