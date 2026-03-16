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
        Schema::table('grd_egresos_fact', function (Blueprint $table) {
            $table->integer('edad')->nullable()->after('sexo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grd_egresos_fact', function (Blueprint $table) {
            $table->dropColumn('edad');
        });
    }
};
