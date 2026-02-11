<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_auditoria', function (Blueprint $table) {
            $table->id();
            $table->string('tabla');
            $table->unsignedBigInteger('registro_id');
            $table->string('evento'); // created, updated, deleted, approved
            $table->jsonb('valores_anteriores')->nullable();
            $table->jsonb('valores_nuevos')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->text('comentarios')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_auditoria');
    }
};
