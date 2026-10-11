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
        Schema::create('trabajos_grado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('tipo_id')->constrained('tipo_trabajos')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('gestion_id')->constrained('gestion_academicas')->onDelete('cascade');

            $table->string('titulo');
            $table->text('resumen');
            $table->string('archivo_url'); // Ruta del PDF en storage
            $table->string('estado')->default('pendiente'); // pendiente, aceptado, rechazado
            $table->decimal('puntaje', 5, 2)->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->string('token')->nullable()->unique()->after('id');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('trabajos_grado', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
