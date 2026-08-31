<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->default(DB::raw('gen_random_uuid()'));
            $table->string('tabla', 100);
            $table->unsignedBigInteger('registro_id');
            $table->string('accion', 20);
            $table->jsonb('valor_anterior')
                ->nullable();
            $table->jsonb('valor_nuevo')
                ->nullable();
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('ip', 45)
                ->nullable();
            $table->timestamps();

            // Índices
            $table->index('tabla');
            $table->index('registro_id');
            $table->index('created_at');
        });
        DB::statement("
            ALTER TABLE auditoria
            ADD CONSTRAINT chk_accion
            CHECK (
                accion IN (
                    'INSERT',
                    'UPDATE',
                    'DELETE'
                )
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
