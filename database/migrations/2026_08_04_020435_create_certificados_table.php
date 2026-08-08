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
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('importacion_id')
                ->constrained('importaciones')
                ->restrictOnDelete();
            $table->string('codigo_certificado',30)->unique();
            $table->string('tipo_solicitud',80)->nullable();
            $table->string('nit',30)->nullable();
            $table->text('nombre_empresa');
            $table->date('fecha_solicitud')->nullable();
            $table->timestamp('fecha_emision_certificado')->nullable();
            $table->string('uso',120)->nullable();
            $table->string('tipo_producto',120)->nullable();
            $table->text('proveedor')->nullable();
            $table->boolean('producto_refrigerado')->default(false);
            $table->string('nro_factura',80)->nullable();
            $table->decimal('monto_factura',12,2)->nullable();
            $table->string('procedencia',100)->nullable();
            $table->string('usuario_origen',100);
            $table->integer('cantidad_items')->default(0);
            $table->boolean('activo')->default(true);
            $table->boolean('editado')->default(false);
            $table->text('observacion')->nullable();
            $table->timestamps();

            // Índices para reportes
            $table->index('fecha_emision_certificado');
            $table->index('fecha_solicitud');
            $table->index('tipo_producto');
            $table->index('tipo_solicitud');
            $table->index('procedencia');
            $table->index('usuario_origen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
