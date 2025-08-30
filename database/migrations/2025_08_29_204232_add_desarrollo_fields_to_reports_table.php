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
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('desarrollo_id')->nullable()->after('chepina_url');
            $table->string('desarrollo_name')->nullable()->after('desarrollo_id');
            $table->unsignedBigInteger('phase_id')->nullable()->after('desarrollo_name');
            $table->unsignedBigInteger('stage_id')->nullable()->after('phase_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['desarrollo_id', 'desarrollo_name', 'phase_id', 'stage_id']);
        });
    }
};
