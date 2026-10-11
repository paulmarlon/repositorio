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
        Schema::create('model_has_role_gestions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id'); // ID del usuario
            $table->unsignedBigInteger('gestion_id');
            // SoftDeletes para auditoría y borrado seguro administrado
            $table->softDeletes();
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('gestion_id')->references('id')->on('gestion_academicas')->onDelete('cascade');
            $table->foreign('model_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_role_gestions');
    }
};
