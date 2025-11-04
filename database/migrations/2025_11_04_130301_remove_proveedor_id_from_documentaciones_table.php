<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('documentaciones', function (Blueprint $table) {
        // Eliminar la clave foránea
        $table->dropForeign(['proveedor_id']);
        // Eliminar la columna
        $table->dropColumn('proveedor_id');
    });
}

public function down()
{
    Schema::table('documentaciones', function (Blueprint $table) {
        // Revertir la eliminación de la columna
        $table->unsignedBigInteger('proveedor_id')->nullable();
        // Revertir la clave foránea
        $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('set null');
    });
}

};
