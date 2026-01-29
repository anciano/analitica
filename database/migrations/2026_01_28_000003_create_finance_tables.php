<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stg_fin_ejecucion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_run_id')->constrained();
            $table->integer('row_number');
            $table->jsonb('payload_raw');
            $table->jsonb('payload_parsed')->nullable();
            $table->boolean('is_valid')->default(false);
            $table->jsonb('validation_errors')->nullable();
            $table->timestamps();

            $table->index('import_run_id');
        });

        Schema::create('fin_ejecucion_fact', function (Blueprint $table) {
            $table->id();
            $table->integer('anio');
            $table->integer('mes');
            $table->string('subtitulo');
            $table->string('item');
            $table->string('asignacion')->nullable();
            $table->text('concepto')->nullable();
            $table->decimal('presupuesto_vigente', 20, 2)->nullable();
            $table->decimal('compromiso', 20, 2)->nullable();
            $table->decimal('devengado', 20, 2);
            $table->decimal('pagado', 20, 2)->nullable();
            $table->decimal('saldo', 20, 2)->nullable();
            $table->string('fuente')->default('ejecucion_presupuestaria');
            $table->foreignId('import_run_id')->constrained();
            $table->timestamps();

            $table->unique(['anio', 'mes', 'subtitulo', 'item', 'asignacion', 'fuente'], 'fin_ejecucion_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_ejecucion_fact');
        Schema::dropIfExists('stg_fin_ejecucion');
    }
};
