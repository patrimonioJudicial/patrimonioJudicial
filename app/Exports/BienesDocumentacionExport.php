<?php

namespace App\Exports;

use App\Models\Documentacion;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class BienesDocumentacionExport implements FromView
{
    use Exportable;

    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function view(): View
    {
        $documentaciones = Documentacion::with(['bien.remito', 'bien.dependencia'])
            ->whereBetween('fecha_acta', [$this->fechaInicio, $this->fechaFin])
            ->orderBy('fecha_acta', 'asc')
            ->get();

        return view('exports.bienes-documentacion', [
            'documentaciones' => $documentaciones
        ]);
    }
}

