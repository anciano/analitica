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
        // 1. Models (Types)
        Schema::create('ml_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g., grd_classifier
            $table->text('description')->nullable();
            $table->unsignedBigInteger('active_version_id')->nullable();
            $table->timestamps();
        });

        // 2. Model Versions
        Schema::create('ml_model_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ml_model_id')->constrained('ml_models')->onDelete('cascade');
            $table->string('version_tag'); // v1.0, v2.0-catboost
            $table->string('algorithm')->nullable();
            $table->jsonb('dataset_info')->nullable();
            $table->jsonb('features')->nullable();
            $table->jsonb('metrics')->nullable();
            $table->string('artifact_path')->nullable();
            $table->string('status')->default('draft'); // draft, active, deprecated
            $table->timestamps();
        });

        // Add foreign key to ml_models for active version
        Schema::table('ml_models', function (Blueprint $table) {
            $table->foreign('active_version_id')->references('id')->on('ml_model_versions')->onDelete('set null');
        });

        // 3. Prediction Logs
        Schema::create('ml_prediction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ml_model_id')->constrained('ml_models');
            $table->foreignId('ml_model_version_id')->constrained('ml_model_versions');
            $table->jsonb('input_data');
            $table->jsonb('output_data');
            $table->integer('response_time_ms')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_models', function (Blueprint $table) {
            $table->dropForeign(['active_version_id']);
        });
        Schema::dropIfExists('ml_prediction_logs');
        Schema::dropIfExists('ml_model_versions');
        Schema::dropIfExists('ml_models');
    }
};
