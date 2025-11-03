<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentacion extends Model
{
    use HasFactory;

    protected $table = 'documentaciones';

    protected $fillable = [
        'bien_id',
        'numero_acta',
        'fecha_acta',
        'numero_resolucion',
        'numero_factura',
        'fecha_factura',
        'monto',
        'proveedor_id',
        'partida_presupuestaria',
        'orden_pago',
        'ejercicio',
        'orden_provision_id',
        'estado',
        'observaciones',
    ];

    // Relaciones
    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function ordenProvision()
    {
        return $this->belongsTo(OrdenProvision::class);
    }
}
