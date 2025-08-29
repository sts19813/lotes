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
            $table->string('redirect_return')->nullable()->after('stage_id');
            $table->string('redirect_next')->nullable()->after('redirect_return');
            $table->string('redirect_previous')->nullable()->after('redirect_next');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desarrollos', function (Blueprint $table) {
            $table->dropColumn(['redirect_return', 'redirect_next', 'redirect_previous']);
        });
    }
};
