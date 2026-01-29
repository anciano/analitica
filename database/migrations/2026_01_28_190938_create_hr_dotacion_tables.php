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
        Schema::create('stg_hr_dotacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_run_id')->constrained()->onDelete('cascade');
            $table->integer('row_number');
            $table->jsonb('payload_raw');
            $table->jsonb('payload_parsed')->nullable();
            $table->boolean('is_valid')->default(false);
            $table->jsonb('errors')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_dotacion_fact', function (Blueprint $table) {
            $table->id();
            $table->integer('anio');
            $table->integer('mes');
            $table->string('rut_hash')->index(); // Anonimizado
            $table->string('nombre_unidad')->nullable();
            $table->string('estamento')->index();
            $table->string('calidad_juridica'); // Planta, Contrata, etc.
            $table->decimal('horas', 8, 2);
            $table->bigInteger('total_haberes')->nullable();
            $table->foreignId('import_run_id')->constrained();
            $table->timestamps();

            $table->unique(['anio', 'mes', 'rut_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_dotacion_fact');
        Schema::dropIfExists('stg_hr_dotacion');
    }
};
