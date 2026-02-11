<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fin_ejecucion_fact', function (Blueprint $table) {
            $table->foreignId('centro_costo_id')->nullable()->constrained('fin_centros_costo');
        });
    }

    public function down(): void
    {
        Schema::table('fin_ejecucion_fact', function (Blueprint $table) {
            $table->dropForeign(['centro_costo_id']);
            $table->dropColumn('centro_costo_id');
        });
    }
};
