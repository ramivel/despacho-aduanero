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
        Schema::create('certificado_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('certificado_id')
                ->constrained('certificados')
                ->cascadeOnDelete();
            $table->integer('numero_item');
            $table->decimal('cantidad', 12, 2)
                ->nullable();
            $table->text('producto');
            $table->string('registro_sanitario', 80)
                ->nullable();
            $table->date('fecha_vencimiento')
                ->nullable();
            $table->string('numero_lote', 100)
                ->nullable();
            $table->boolean('evaluacion')
                ->default(false);
            $table->boolean('activo')
                ->default(true);
            $table->boolean('editado')
                ->default(false);
            $table->timestamps();

            // Restricción de unicidad
            $table->unique(
                ['certificado_id', 'numero_item'],
                'uq_item_certificado'
            );

            // Índices para reportes
            $table->index('producto');
            $table->index('registro_sanitario');
            $table->index('fecha_vencimiento');
            $table->index('numero_lote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificado_items');
    }
};
