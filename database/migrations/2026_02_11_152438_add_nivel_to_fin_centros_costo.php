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
        Schema::table('fin_centros_costo', function (Blueprint $table) {
            $table->integer('nivel')->default(1)->after('nombre')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fin_centros_costo', function (Blueprint $table) {
            $table->dropColumn('nivel');
        });
    }
};
