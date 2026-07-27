<table>
    <thead>
    <tr>
        <th colspan="9" style="background-color: #7FFF00; font-weight:bold; text-align:center; font-size: 12px;">REPORT BARANG MASUK</th>
        <th></th>
    </tr>
    <tr>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">NO</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">MATERIAL CODE</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">MATERIAL NAME</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">SPECIFICATION</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">TYPE</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">QTY</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">NOTE</th>
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
            <td style="text-align:center;">{{ $value->qty }}</td>
            <td style="text-align:center;">{{ $value->note }}</td>
            <td style="text-align:center;">{{ $value->date }}</td>
            <td style="text-align:center;">{{ $value->created_by }}</td>
        </tr>
    @endforeach
    </tbody>
</table>