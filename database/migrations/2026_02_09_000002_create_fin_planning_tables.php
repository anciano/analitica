<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Planes de Presupuesto
        Schema::create('fin_planes', function (Blueprint $table) {
            $table->id();
            $table->integer('anio')->index();
            $table->integer('version')->default(1);
            $table->string('nombre');
            $table->enum('estado', ['borrador', 'aprobado', 'historico'])->default('borrador');
            $table->timestamp('aprobado_at')->nullable();
            $table->foreignId('aprobado_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['anio', 'version'], 'idx_plan_unique_anio_version');
        });

        // 2. Detalle de Ítems Presupuestados por CC
        Schema::create('fin_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('fin_planes')->onDelete('cascade');
            $table->foreignId('clasificador_item_id')->constrained('fin_clasificador_items');
            $table->foreignId('centro_costo_id')->constrained('fin_centros_costo');
            $table->decimal('monto_anual', 20, 2);
            $table->timestamps();

            $table->unique(['plan_id', 'clasificador_item_id', 'centro_costo_id'], 'idx_plan_item_cc_unique');
        });

        // 3. Distribución Mensual del Presupuesto
        Schema::create('fin_plan_mensual', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_item_id')->constrained('fin_plan_items')->onDelete('cascade');
            $table->integer('mes')->comment('1-12');
            $table->decimal('monto_planificado', 20, 2);
            $table->timestamps();

            $table->unique(['plan_item_id', 'mes'], 'idx_plan_mensual_item_mes_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_plan_mensual');
        Schema::dropIfExists('fin_plan_items');
        Schema::dropIfExists('fin_planes');
    }
};
