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
        Schema::create('reports', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->double('area', 20, 2);
                    $table->double('price_square_meter', 20, 2);
                    $table->double('down_payment_percent', 5, 2)->nullable();
                    $table->integer('financing_months')->nullable();
                    $table->double('annual_appreciation', 5, 2)->nullable();
                    $table->string('lead_name')->nullable();
                    $table->string('lead_phone')->nullable();
                    $table->string('lead_email')->nullable();
                    $table->string('city')->nullable();
                    $table->double('precio_total', 20, 2);
                    $table->double('enganche_porcentaje', 5, 2);
                    $table->double('enganche_monto', 20, 2);
                    $table->double('mensualidad', 20, 2);
                    $table->double('plusvalia_total', 20, 2);
                    $table->double('roi', 10, 2);
                    $table->json('years_data')->nullable();
                    $table->text('chepina')->nullable();
                    $table->text('chepina_url')->nullable();
                    $table->timestamps();
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
