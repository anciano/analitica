<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('import_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_version_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('file_name');
            $table->integer('total_rows')->default(0);
            $table->integer('valid_rows')->default(0);
            $table->integer('error_rows')->default(0);
            $table->string('status')->default('pending'); // pending, staging, validating, processing, completed, failed
            $table->integer('target_anio');
            $table->integer('target_mes');
            $table->timestamps();
        });

        Schema::create('import_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_run_id')->constrained();
            $table->integer('row_number');
            $table->string('column_name')->nullable();
            $table->string('error_code');
            $table->text('error_message');
            $table->text('original_value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_errors');
        Schema::dropIfExists('import_runs');
    }
};
