<table>
    <thead>
    <tr>
        <th colspan="13" style="background-color: #7FFF00; font-weight:bold; text-align:center; font-size: 12px;">REPORT STOCK BARANG</th>
        <th></th>
    </tr>
    <tr>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">NO</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">MATERIAL CODE</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">MATERIAL NAME</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">SPECIFICATION</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">TYPE</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">UNIT</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">ACTUAL STOCK</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">STOCK IN</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">STOCK OUT</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">MIN STOCK</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">STORAGE LOCATION</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">DATE</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">CREATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $index => $value)
        <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td style="text-align:center;">{{ $value->material_code }}</td>
            <td style="text-align:center;">{{ $value->material_name }}</td>
            <td style="text-align:center;">{{ $value->specification }}</td>
            <td style="text-align:center;">{{ $value->type }}</td>
            <td style="text-align:center;">{{ $value->unit }}</td>
            @if( $value->stock_barang >= $value->min_stock )
                <td style="text-align:center; background-color: green;">{{ $value->stock_barang }}</td>
            @else
                <td style="text-align:center; background-color: red;">{{ $value->stock_barang }}</td>
            @endif
            <td style="text-align:center;">{{ $value->total_barang_masuk_count }}</td>
            <td style="text-align:center;">{{ $value->total_barang_keluar_count }}</td>
            <td style="text-align:center;">{{ $value->min_stock }}</td>
            <td style="text-align:center;">{{ $value->storage_location }}</td>
            <td style="text-align:center;">{{ $value->created_at }}</td>
            <td style="text-align:center;">{{ $value->created_by }}</td>
        </tr>
    @endforeach
    </tbody>
</table>