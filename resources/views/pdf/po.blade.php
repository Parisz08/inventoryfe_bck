<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $po->po_number }}</title>
  <style>
    @page { size: A4; margin: 12mm; }
    * { box-sizing: border-box; }
    body { font-family: 'Calibri', Arial, sans-serif; font-size: 11.5px; color: #000; margin: 0; }
    .sheet { width: 100%; max-width: 820px; margin: 0 auto; padding: 10px; }

    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    .header-table td { vertical-align: middle; padding: 0; }
    .logo-cell { width: 90px; }
    .logo-cell img { width: 75px; }
    .company-cell h1 { font-size: 13px; margin: 0; font-weight: bold; }
    .company-cell p { font-size: 9px; margin: 0; }

    .doc-title { text-align: center; font-size: 20px; font-weight: bold; letter-spacing: 2px; margin: 8px 0 14px 0; }

    table.info { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    table.info td { vertical-align: top; padding: 1px 0; font-size: 11px; }
    table.info td.label { width: 110px; font-weight: bold; }
    table.info td.sep { width: 10px; }
    .info-left { width: 52%; }
    .info-right { width: 48%; padding-left: 14px; }
    .info-block-title { font-weight: bold; margin-bottom: 2px; }

    table.items { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    table.items th, table.items td { border: 1px solid #000; padding: 4px 6px; font-size: 11px; }
    table.items th { text-align: center; font-weight: bold; background-color: #f2f2f2; }
    table.items td.no { text-align: center; width: 28px; }
    table.items td.qty { text-align: center; width: 50px; }
    table.items td.unit { text-align: center; width: 55px; }
    table.items td.price, table.items td.total { text-align: right; width: 95px; }

    table.totals { width: 260px; margin-left: auto; border-collapse: collapse; margin-bottom: 14px; }
    table.totals td { padding: 3px 6px; font-size: 11px; }
    table.totals td.tlabel { text-align: left; }
    table.totals td.tval { text-align: right; width: 120px; }
    table.totals tr.grand td { border-top: 2px solid #000; font-weight: bold; font-size: 12.5px; }

    .term-row { margin-bottom: 10px; font-size: 11px; }
    .term-row .label { font-weight: bold; display: inline-block; width: 110px; }

    .catatan { margin-bottom: 20px; }
    .catatan h6 { font-size: 11px; font-weight: bold; margin: 0 0 4px 0; }
    .catatan p { font-size: 10.5px; margin: 1px 0; }

    table.signature { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.signature td { text-align: center; vertical-align: top; padding: 0 8px; font-size: 11px; }
    table.signature .sign-title { font-weight: bold; margin-bottom: 55px; }
    table.signature .sign-name { border-top: 1px solid #000; display: inline-block; min-width: 130px; padding-top: 4px; }
    table.signature .sign-role { font-size: 10.5px; margin-top: 2px; }

    .footer-note { font-size: 9px; color: #444; margin-top: 22px; }

    @media print { .no-print { display: none !important; } body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
    .no-print { text-align: center; margin-bottom: 14px; margin-top: 24px; display: flex; justify-content: center; gap: 24px; }
    .no-print button { padding: 8px 18px; font-size: 13px; cursor: pointer; border-radius: 4px; border: 1px solid #ccc; background: #fff; }
    .no-print button.primary { background: #2dce89; color: #fff; border-color: #2dce89; }
  </style>
</head>
<body>

  <div class="no-print">
    <button class="primary" onclick="window.print()">🖨️ Print / Simpan sebagai PDF</button>
    <button onclick="window.close()">Tutup</button>
  </div>

  <div class="sheet">
    <!-- ============ HEADER ============ -->
    <table class="header-table">
      <tr>
        <td class="logo-cell"><img src="{{ URL('bck.png') }}" alt="logo"></td>
        <td class="company-cell">
          <h1>PT. BUANA CENTRA KARYA</h1>
          <p>PIPE MANUFACTURING &amp; STEEL FABRICATION</p>
        </td>
      </tr>
    </table>

    <div class="doc-title">PURCHASE ORDER</div>

    <!-- ============ INFO (Kepada / PO No / Ship to / Divisi) ============ -->
    <table class="info">
      <tr>
        <td class="info-left">
          <div class="info-block-title">Kepada :</div>
          {{ $po->supplier }}<br>
          {{ optional($po->vendor)->address }}<br>
          @if(optional($po->vendor)->phone)Telp. {{ $po->vendor->phone }}@endif

          <div class="info-block-title" style="margin-top: 10px;">Kirim ke / Ship to :</div>
          PT. Buana Centra Karya<br>
          Jln. Raya Merak KM. 115 Rawa Arum,<br>
          Cilegon Banten 42436<br>
          Phone : 0254-572111/574222
        </td>
        <td class="info-right">
          <table>
            <tr><td class="label">PO No.</td><td class="sep">:</td><td>{{ $po->po_number }}</td></tr>
            <tr><td class="label">Tanggal / Date</td><td class="sep">:</td><td>{{ \Carbon\Carbon::parse($po->po_date)->format('d-m-Y') }}</td></tr>
            <tr><td class="label">Up</td><td class="sep">:</td><td>{{ optional($po->vendor)->pic }}</td></tr>
            <tr><td class="label">Divisi</td><td class="sep">:</td><td>{{ optional($po->spb)->divisi }}</td></tr>
            <tr><td class="label">Referensi SPPB</td><td class="sep">:</td><td>{{ optional($po->spb)->no_spb }}</td></tr>
            <tr><td class="label">Currency</td><td class="sep">:</td><td>RUPIAH</td></tr>
          </table>
        </td>
      </tr>
    </table>

    <!-- ============ ITEMS ============ -->
    <table class="items">
      <thead>
        <tr>
          <th style="width: 28px;">No.</th>
          <th>Nama Barang</th>
          <th style="width: 50px;">Jumlah</th>
          <th style="width: 55px;">Satuan</th>
          <th style="width: 95px;">Harga Satuan</th>
          <th style="width: 95px;">Jumlah Harga</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($po->items as $i => $item)
        <tr>
          <td class="no">{{ $i + 1 }}</td>
          <td>{{ $item->material_name }}{{ $item->merek ? ' (Merk ' . $item->merek . ')' : '' }}</td>
          <td class="qty">{{ $item->qty }}</td>
          <td class="unit">{{ $item->unit }}</td>
          <td class="price">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
          <td class="total">{{ number_format($item->line_total, 0, ',', '.') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <!-- ============ TOTALS ============ -->
    <table class="totals">
      <tr><td class="tlabel">Jumlah</td><td class="tval">{{ number_format($subtotal, 0, ',', '.') }}</td></tr>
      <tr><td class="tlabel">Discount {{ $discountPercent }}%</td><td class="tval">{{ number_format($discount, 0, ',', '.') }}</td></tr>
      <tr><td class="tlabel">Total</td><td class="tval">{{ number_format($total, 0, ',', '.') }}</td></tr>
      <tr><td class="tlabel">PPN 12%</td><td class="tval">{{ number_format($ppn, 0, ',', '.') }}</td></tr>
      <tr class="grand"><td class="tlabel">Grand Total</td><td class="tval">{{ number_format($grandTotal, 0, ',', '.') }}</td></tr>
    </table>

    <!-- ============ TERM & CATATAN ============ -->
    <div class="term-row"><span class="label">Term / Condition</span>: {{ optional($po->vendor)->payment_term ?: '-' }}</div>

    <div class="catatan">
      <h6>Catatan :</h6>
      <p>- Pengiriman Barang Paling Lambat 3 Hari Setelah PO Diterbitkan Kepada Pihak Supplier / Vendor</p>
      <p>- Barang Akan Kami Kembalikan Apabila Tidak Sesuai Dengan Pesanan (PO)</p>
      <p>- Semua Pengiriman Barang Harus Disertakan Dengan Nota / Faktur Dan Kwitansi</p>
      <p>- Nomor PO Harus Dicantumkan Dalam Invoice</p>
    </div>

    <!-- ============ SIGNATURE ============ -->
    <table class="signature">
      <tr>
        <td style="width: 33%;">
          <div class="sign-title">Dibuat Oleh,</div>
          <div class="sign-name">{{ $po->updated_by ?: '' }}</div>
          <div class="sign-role">Purchasing</div>
        </td>
        <td style="width: 34%;">
          <div class="sign-title">Diajukan Oleh,</div>
          <div class="sign-name">&nbsp;</div>
          <div class="sign-role">Manager Dept.</div>
        </td>
        <td style="width: 33%;">
          <div class="sign-title">Disetujui Oleh,</div>
          <div class="sign-name">{{ optional($po->spb)->approved_by }}</div>
          <div class="sign-role">Direktur</div>
        </td>
      </tr>
    </table>

    <div class="footer-note">
      Distribusi: 1. Pemasok (Supplier), 2. Keuangan &amp; Akuntansi, 3. Arsip<br>
      Dokumen ini milik PT. BCK, isi dari dokumen ini tidak diperkenankan untuk digandakan atau disalin baik seluruh atau sebagian tanpa izin tertulis.
    </div>
  </div>

</body>
</html>