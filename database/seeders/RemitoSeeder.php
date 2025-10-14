<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Remito;
class RemitoSeeder extends Seeder {
    public function run(): void {
        Remito::create([
            'numero_remito' => 'REM-2024-0312',
            'fecha_recepcion' => '2024-01-20',
            'tipo_compra' => 'directa',
            'proveedor_id' => 1,
            'user_id' => 2,
            'orden_provision_id' => 1
        ]);
        Remito::create([
            'numero_remito' => 'REM-2024-0350',
            'fecha_recepcion' => '2024-02-05',
            'tipo_compra' => 'directa',
            'proveedor_id' => 2,
            'user_id' => 2,
            'orden_provision_id' => 2
        ]);
    }
}
