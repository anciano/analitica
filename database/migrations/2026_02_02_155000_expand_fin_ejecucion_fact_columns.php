<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fin_ejecucion_fact', function (Blueprint $table) {
            $table->string('codigo_completo')->nullable();
            $table->decimal('requerimiento', 20, 2)->nullable();
            $table->decimal('deuda_flotante', 20, 2)->nullable();
            $table->decimal('saldo_por_aplicar', 20, 2)->nullable();
            $table->decimal('saldo_por_devengar', 20, 2)->nullable();
            $table->integer('row_number')->nullable(); // Adding row_number to fact for better traceability

            // Core unique key for Chilean finance data
            $table->unique(['anio', 'mes', 'codigo_completo', 'fuente'], 'idx_fin_ejec_unique_code');

            // Drop the old index that is now too restrictive/redundant
            $table->dropUnique('fin_ejecucion_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::table('fin_ejecucion_fact', function (Blueprint $table) {
            $table->dropUnique('idx_fin_ejec_unique_code');
            $table->dropColumn([
                'codigo_completo',
                'requerimiento',
                'deuda_flotante',
                'saldo_por_aplicar',
                'saldo_por_devengar',
                'row_number'
            ]);

            // Restore the old unique index
            $table->unique(['anio', 'mes', 'subtitulo', 'item', 'asignacion', 'fuente'], 'fin_ejecucion_unique_idx');
        });
    }
};
