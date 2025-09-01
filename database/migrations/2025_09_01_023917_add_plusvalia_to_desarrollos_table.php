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
        Schema::table('desarrollos', function (Blueprint $table) {
            // plusvalía como decimal, ej. 0.15 = 15%
            $table->decimal('plusvalia', 5, 2)->default(0.00)->after('stage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('desarrollos', function (Blueprint $table) {
            $table->dropColumn('plusvalia');
        });
    }
};
