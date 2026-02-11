<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fin_ejecucion_fact', function (Blueprint $table) {
            $table->integer('nivel')->nullable();
        });

        // Populate existing data by parsing it from stg_fin_ejecucion payload_raw
        // Note: This logic assumes that for every fact row there is a corresponding staging row
        // with the same row_number and import_run_id (which is how the system works).

        DB::statement("
            UPDATE fin_ejecucion_fact f
            SET nivel = (s.payload_raw->>'nivel')::integer
            FROM stg_fin_ejecucion s
            WHERE f.import_run_id = s.import_run_id
              AND f.subtitulo = (s.payload_parsed->>'subtitulo')
              AND f.item = (s.payload_parsed->>'item')
              AND COALESCE(f.asignacion, '') = COALESCE(s.payload_parsed->>'asignacion', '')
        ");
    }

    public function down(): void
    {
        Schema::table('fin_ejecucion_fact', function (Blueprint $table) {
            $table->dropColumn('nivel');
        });
    }
};
