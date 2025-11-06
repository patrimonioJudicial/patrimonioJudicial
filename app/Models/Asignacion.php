<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model {
    protected $table = 'asignaciones';
    protected $fillable = ['bien_id', 'dependencia_id', 'fecha_asignacion', 'user_id', 'recibido_por', 'observacion'];
    protected $dates = ['fecha_asignacion'];
    
    public function bien() {
        return $this->belongsTo(Bien::class);
    }
    public function dependencia() {
        return $this->belongsTo(Dependencia::class);
    }
    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

    public function recibioPor() {
        return $this->belongsTo(User::class, 'recibido_por');
    }
}