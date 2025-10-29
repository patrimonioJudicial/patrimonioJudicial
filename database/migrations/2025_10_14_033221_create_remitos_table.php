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
        Schema::table('remitos', function (Blueprint $table) {
            $table->string('numero_expediente', 50)->nullable()->after('numero_remito');
            $table->string('orden_provision', 50)->nullable()->after('numero_expediente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remitos', function (Blueprint $table) {
            $table->foreignId('orden_provision_id')->constrained('ordenes_provision')->onUpdate('cascade');
            $table->dropColumn(['numero_expediente', 'orden_provision']);
        });
    }
};

