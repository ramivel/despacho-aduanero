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
        Schema::create('importaciones', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->default(DB::raw('gen_random_uuid()'));
            $table->string('archivo_original');
            $table->string('nombre_archivo');
            $table->timestamp('fecha_importacion');
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users');
            $table->integer('total_registros')->default(0);
            $table->integer('total_validos')->default(0);
            $table->integer('total_existentes')->default(0);
            $table->integer('total_errores')->default(0);
            $table->integer('total_certificados')->default(0);
            $table->integer('total_items')->default(0);
            $table->string('estado',20)
                ->default('COMPLETADA');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
        DB::statement("
        ALTER TABLE importaciones
        ADD CONSTRAINT chk_estado_importacion
        CHECK (
            estado IN (
                'PROCESANDO',
                'COMPLETADA',
                'ERROR',
                'ANULADA'
            )
        )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('importaciones');
    }
};
