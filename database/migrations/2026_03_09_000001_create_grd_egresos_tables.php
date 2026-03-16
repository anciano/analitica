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
        // 1. Staging Table
        Schema::create('stg_grd_egresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_run_id')->constrained('import_runs')->onDelete('cascade');
            $table->integer('row_number');
            $table->jsonb('payload_raw')->nullable();
            $table->jsonb('payload_parsed')->nullable();
            $table->boolean('is_valid')->default(false);
            $table->jsonb('validation_errors')->nullable();
            $table->timestamps();

            $table->index('import_run_id');
        });

        // 2. Fact Table
        Schema::create('grd_egresos_fact', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_run_id')->constrained('import_runs')->onDelete('cascade');
            $table->integer('anio');
            $table->integer('mes');
            $table->string('mes_nombre')->nullable();
            $table->string('num_historia')->nullable();
            $table->string('episodio_cmbd')->nullable();
            $table->string('prevision')->nullable();
            $table->string('sexo')->nullable();
            $table->string('grd_id_original')->nullable();
            $table->text('grd_nombre')->nullable();
            $table->text('dx_principal')->nullable();
            $table->jsonb('dx_secundarios')->nullable();
            $table->text('proc_principal')->nullable();
            $table->jsonb('proc_secundarios')->nullable();
            $table->float('estancia_media')->nullable();
            $table->float('corte_superior')->nullable();
            $table->boolean('tiene_vm')->default(false);
            $table->float('peso_grd')->nullable();
            $table->integer('egresos')->default(1);
            $table->timestamps();

            $table->index(['anio', 'mes']);
            $table->index('episodio_cmbd');
            $table->index('num_historia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grd_egresos_fact');
        Schema::dropIfExists('stg_grd_egresos');
    }
};
