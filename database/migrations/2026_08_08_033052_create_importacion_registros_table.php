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
        Schema::create('importacion_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('importacion_id')
                ->constrained('importaciones')
                ->cascadeOnDelete();
            $table->unsignedInteger('numero_fila');
            $table->string('codigo_certificado', 100);
            $table->jsonb('datos_certificado')
                ->nullable();
            $table->jsonb('datos_item')
                ->nullable();
            $table->string('estado', 20)
                ->default('VALIDO');
            $table->text('mensaje')
                ->nullable();
            $table->boolean('seleccionado')
                ->default(true);
            $table->timestamps();

            $table->index([
                'importacion_id',
                'estado',
                'seleccionado',
            ], 'idx_importacion_estado_seleccionado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('importacion_registros');
    }
};
