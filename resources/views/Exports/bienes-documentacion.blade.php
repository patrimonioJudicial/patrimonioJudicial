<table>
    <thead>
        <tr>
            <th>N° Inventario</th>
            <th>Descripción</th>
            <th>Dependencia</th>
            <th>N° Acta</th>
            <th>Fecha Acta</th>
            <th>Resolución</th>
            <th>N° Factura</th>
            <th>Fecha Factura</th>
            <th>Monto</th>
            <th>Partida Presupuestaria</th>
            <th>Orden de Pago</th>
            <th>Estado</th>
            <th>Observaciones</th>
            <th>N° de Provisión</th>
        </tr>
    </thead>
    <tbody>
        @foreach($documentaciones as $doc)
            <tr>
                <td>{{ $doc->bien->numero_inventario ?? '-' }}</td>
                <td>{{ $doc->bien->descripcion ?? '-' }}</td>
                <td>{{ $doc->bien->dependencia->nombre ?? '-' }}</td>
                <td>{{ $doc->numero_acta }}</td>
                <td>{{ $doc->fecha_acta }}</td>
                <td>{{ $doc->numero_resolucion }}</td>
                <td>{{ $doc->numero_factura }}</td>
                <td>{{ $doc->fecha_factura }}</td>
                <td>{{ $doc->monto }}</td>
                <td>{{ $doc->partida_presupuestaria }}</td>
                <td>{{ $doc->orden_pago }}</td>
                <td>{{ $doc->estado }}</td>
                <td>{{ $doc->observaciones }}</td>
                <td>{{ $doc->bien->remito->numero_provision ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
