<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model {
    protected $table = 'cuentas';
    protected $fillable = ['codigo', 'descripcion', 'tipo', 'activo'];
    
    public function bienes() {
        return $this->hasMany(Bien::class);
    }
}