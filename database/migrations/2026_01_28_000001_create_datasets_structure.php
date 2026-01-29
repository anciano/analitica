<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // e.g. 'fin_ejecucion'
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('dataset_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained();
            $table->string('version')->default('v1');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('dataset_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_version_id')->constrained();
            $table->string('canonical_name');
            $table->string('data_type')->default('string'); // string, numeric, integer
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });

        Schema::create('dataset_column_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_column_id')->constrained();
            $table->string('alias');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dataset_column_aliases');
        Schema::dropIfExists('dataset_columns');
        Schema::dropIfExists('dataset_versions');
        Schema::dropIfExists('datasets');
    }
};
