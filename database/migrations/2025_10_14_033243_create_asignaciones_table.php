<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bien_id');
            $table->unsignedBigInteger('dependencia_id');
            $table->date('fecha_asignacion');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('recibido_por')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
            
            $table->index(['bien_id', 'fecha_asignacion']);
        });

        Schema::table('asignaciones', function (Blueprint $table) {
            $table->foreign('bien_id')
                ->references('id')
                ->on('bienes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('dependencia_id')
                ->references('id')
                ->on('dependencias')
                ->onUpdate('cascade');
                
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
                
            $table->foreign('recibido_por')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};