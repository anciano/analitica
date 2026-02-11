<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Clasificador Presupuestario
        Schema::create('fin_clasificador_items', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->index(); // Ej: 21, 21.01, 21.01.001
            $table->string('denominacion');
            $table->integer('nivel'); // 1, 2, 3
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('anio_vigencia')->index();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('fin_clasificador_items')->onDelete('cascade');
            $table->unique(['codigo', 'anio_vigencia'], 'idx_clasificador_unique_cod_anio');
        });

        // 2. Centros de Costo
        Schema::create('fin_centros_costo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('fin_centros_costo')->onDelete('cascade');
        });

        // 3. Reglas de Imputación CC <-> Ítem (Opcional)
        Schema::create('fin_cc_item_imputacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centro_costo_id')->constrained('fin_centros_costo');
            $table->foreignId('clasificador_item_id')->constrained('fin_clasificador_items');
            $table->timestamps();

            $table->unique(['centro_costo_id', 'clasificador_item_id'], 'idx_cc_item_imput_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_cc_item_imputacion');
        Schema::dropIfExists('fin_centros_costo');
        Schema::dropIfExists('fin_clasificador_items');
    }
};
