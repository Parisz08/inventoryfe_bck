<?php 
    use Carbon\Carbon;
?>

<!DOCTYPE html>

<html lang="el">

  <body>
    <div class="container" style="font-family:Calibri; color: #000000; margin-top: -900; font-size:15px;">
        <h4 style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DELIVERY</h4>
      <!-- <table style="margin-bottom: -900px;">
          <tbody>
              <tr>
                  <td style=" border:1px solid black; font-weight: bold;" width="100"> PO NO</td>
                  <td colspan="3" width="135" style=" border:1px solid black;">   $data->po_no  </td>
                  <td style="display: none;"></td>
              </tr>
              <tr>
                  <td style=" border:1px solid black; font-weight: bold;" width="100"> JOB NO</td>
                  <td style=" border:1px solid black;" width="130">   $data->job_no  </td>
                  <td style=" border:1px solid black; font-weight: bold;" width="140"> SURAT JALAN NO</td>
                  <td style=" border:1px solid black;" width="205">  $data->packing_list_no  </td>
              </tr>
              <tr>
                  <td style=" border:1px solid black; font-weight: bold;" width="120"> LP No</td>
                  <td style=" border:1px solid black;" width="130">  $dataSJ->lpp_no  </td>
                  <td style=" border:1px solid black; font-weight: bold;" width="140"> TANGGAL</td>
                  <td style=" border:1px solid black;" width="205">  date('d F Y', strtotime($data->packing_date))  </td>
              </tr>
          </tbody>
      </table>

      CUSTOMER :
      <table style="margin-bottom: -900px;">
          <tbody>
              <tr>
                  <td style=" border:1px solid black;" width="500"> <span style="margin-left: 50px;">Kpd YTH : &#10;  $data->client_name  &#10;  $data->client_addres </span> </td>
                  <td style=" border:1px solid black;" width="500"> <span style="margin-left: 50px;"> $dataSJ->alamat_kirim  </span></td>
                  <td style="display: none;"></td>
              </tr>
          </tbody>
      </table> -->
        
      <u style="margin-bottom: -900px;">Sesuai dengan Pesanan tersebut di atas, maka harap diterima dengan baik barang-barang sbb.</u>
      <table style="margin-bottom: -900px;">
        <thead>
          <tr>
            <th style="border:1px solid black; font-weight: bold; text-align:center;" width="5">NO</th>
            <th style="border:1px solid black; font-weight: bold; text-align:center;" width="140">DESKRIPSI</th>
            <th style="border:1px solid black; font-weight: bold; text-align:center;" width="140">SPESIFIKASI</th>
            <th style="border:1px solid black; font-weight: bold; text-align:center;" width="100">JUMLAH</th>
            <th style="border:1px solid black; font-weight: bold; text-align:center;" width="100">TONASE (Kg)</th>
            <th style="border:1px solid black; font-weight: bold; text-align:center;" width="140">COIL NO</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="border:1px solid black; text-align:center;">  nl2br($dataSJ->no_seq)  </td>
            <td style="border:1px solid black;" height="140">  nl2br($dataSJ->deskripsi)  </td>
            <td style="border:1px solid black; text-align:center;" height="140"> nl2br($dataSJ->spesifikasi)    </td>
            <td style="border:1px solid black; text-align:center;" height="100">  nl2br($dataSJ->jumlah)  </td>
            <td style="border:1px solid black; text-align:center;" height="100">  nl2br($dataSJ->tonase)  </td>
            <td style="border:1px solid black;" height="140">  nl2br($dataSJ->coil_no_all)  </td>
          </tr>
          <tr>
            <td colspan="3" style="border:1px solid black; text-align:center;">Total</td>
            <td style="border:1px solid black; text-align:center;">  $data->qty  </td>
            <td style="border:1px solid black; text-align:center;">  $data->weight  </td>
            <td style="border:1px solid black; text-align:center;"></td>
          </tr>
        </tbody>
      </table>

      <table style="margin-bottom: -900px;">
        <tbody>
            <tr>
                <td style=" font-weight: bold;" width="100">TRANSPORTER</td>
                <td style="" width="130">:   $dataSJ->transporter  </td>
            </tr>
            <tr>
                <td style=" font-weight: bold;" width="120">NO KENDARAAN</td>
                <td style="" width="130">:  $dataSJ->no_kendaraan  </td>
            </tr>
            <tr>
                <td style=" font-weight: bold;" width="120">DRIVER</td>
                <td style="" width="130">:  $dataSJ->driver  </td>
            </tr>
        </tbody>
      </table>

      <table style="margin-bottom: -900px;">
        <tbody>
            <thead>
              <tr>
                <th style="text-align:center;" width="200">DI TERIMA OLEH &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <th style="text-align:center;" width="80">GUDANG &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <th style="text-align:center;" width="80">DRIVER &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <th style="text-align:center;" width="10">SECURITY &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <th style="text-align:center;" width="270">PT. BUANA CENTRA KARYA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
              </tr>
            </thead>span
            <tbody>
              <tr>
                <td style="text-align:center;" width="200"><u> $dataSJ->driver </u>  </td>
                <td style="text-align:center;" width="80"><u> $dataSJ->gudang </u></td>
                <td style="text-align:center;" width="80"><u> $dataSJ->driver </u></td>
                <td style="text-align:center;" width="10"><u> $dataSJ->security </u></td>
                <td style="text-align:center;" width="270"><u> $dataSJ->delegated_pt </u></td>
              </tr>
            </tbody>
        </tbody>
      </table>
      <span class="text-center">Barang-barang yang telah di terima dengan benar oleh pihak pembeli tidak dapat dikembalikan kecuali bila ada pembicaraan lebih dahulu.</span>

    </div>
  </body>
</html>
