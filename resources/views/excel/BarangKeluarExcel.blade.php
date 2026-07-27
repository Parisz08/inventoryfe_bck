<table>
    <thead>
    <tr>
        <th colspan="14" style="background-color: #7FFF00; font-weight:bold; text-align:center; font-size: 12px;">REPORT BARANG KELUAR</th>
        <th></th>
    </tr>
    <tr>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">NO</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">NO BARANG KELUAR</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">MATERIAL CODE</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">MATERIAL NAME</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">SPECIFICATION</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">TYPE</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">QTY</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">DESCRIPTION</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">DIVISI</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">DI SERAHKAN</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">DI SETUJUI</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">DI TERIMA</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">DATE</th>
        <th style="background-color: #6495ED; font-weight:bold; text-align:center;">CREATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $index => $value)
        <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td style="text-align:center;">{{ $value->no_sj }}</td>
            <td style="text-align:center;">{{ $value->material_code }}</td>
            <td style="text-align:center;">{{ $value->material_name }}</td>
            <td style="text-align:center;">{{ $value->specification }}</td>
            <td style="text-align:center;">{{ $value->type }}</td>
            <td style="text-align:center;">{{ $value->qty }}</td>
            <td style="text-align:center;">{{ $value->description }}</td>
            <td style="text-align:center;">{{ $value->divisi }}</td>
            <td style="text-align:center;">{{ $value->diserahkan }}</td>
            <td style="text-align:center;">{{ $value->disetujui }}</td>
            <td style="text-align:center;">{{ $value->diterima }}</td>
            <td style="text-align:center;">{{ $value->date }}</td>
            <td style="text-align:center;">{{ $value->created_by }}</td>
        </tr>
    @endforeach
    </tbody>
</table>