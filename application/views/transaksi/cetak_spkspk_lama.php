<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Cetak SPK');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
// $pdf->SetFont('helvetica', '', 10);
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 25, 10, true);


$html = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cetak SPK</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <style>
    .text-center {
        vertical-align: middle;
        text-align: center;
    }
    .text-left {
        vertical-align: middle;
        text-align: left;
    }

    .dashed {
        border: 2px dashed gray;
        padding-left: 1em;
        padding-right: 1em;
        font-family: monospace;
    }
    .solid {
        border: 2px solid gray;
        padding-left: .75em;
        padding-right: .75em;
        margin-left: 1em;
        margin-right: .5em;
        font-family: monospace;
    }
    .solid:first-child {
        margin-left: 0;
    }
    
    .text-center{
        text-align:center;
    }

    .text-right{
        text-align:right;
    }
  </style>
</head>
<body>
<div class="container text-center">
    <span class="text-center" style="font-size:15pt; font-weight:bold;">SURAT PERINTAH KERJA PRODUKSI</span><br>
    <span class="text-center" style="font-size:11pt; font-weight:none !important;">No SPK:&nbsp;&nbsp;&nbsp;' . $dtinduk['SPKNomor'] . '</span>
</div>
<br><br>

<table width="100%" class="table table-borderless" style="font-size: 10pt; padding: 1px">
    <tr>
        <td class="text-left" style="width:25%">Nomor Slip Order</td>
        <td class="text-left" style="width:25%">&nbsp;:&nbsp;' . $NoSO . '</td>
        <td class="text-left" style="width:25%">Kode Customer</td>
        <td class="text-left" style="width:25%">&nbsp;:&nbsp;' . $KodePerson . '</td>
    </tr>
    <tr>
        <td class="text-left" style="width:25%">Tanggal Slip Order</td>
        <td class="text-left" style="width:25%">&nbsp;:&nbsp;' . $TglSlipOrder . '</td>
        <td class="text-left" style="width:25%">Nama Customer</td>
        <td class="text-left" style="width:25%">&nbsp;:&nbsp;' . $NamaUsaha . '</td>
    </tr>
    <tr>
        <td class="text-left" style="width:25%">Estimasi Selesai</td>
        <td class="text-left" style="width:25%">&nbsp;:&nbsp;' . $EstimasiSelesai . '</td>
        <td colspan="2"></td>
    </tr>
</table>
<br><br>

<table width="100%"  border="1" style="font-size: 10pt; padding: 1px">
    <tr>
        <td class="text-center" style="width:5%"><b>No</b></td>
        <td class="text-center" style="width:15%"><b>Kode Produksi</b></td>
        <td class="text-center" style="width:25%"><b>Nama Barang</b></td>
        <td class="text-center" style="width:15%"><b>Jenis</b></td>
        <td class="text-center" style="width:15%"><b>Kategori</b></td>
        <td class="text-center" style="width:15%"><b>Spesifikasi</b></td>
        <td class="text-center" style="width:10%"><b>Quantity</b></td>
    </tr>';


$no = 1;
$jumlah = 0;
foreach ($model as $row) {
    $barang         = isset($row['AdditionalName']) ? $row['AdditionalName'] : $row['NamaBarang'];
    $jenisbarang    = isset($row['NoUrut']) ? $row['JenisBarang'] : $row['NamaJenisBarang'];
    $kategory       = isset($row['NoUrut']) ? $row['Kategory'] : $row['NamaKategori'];
    $spesifikasi    = isset($row['NoUrut']) ? $row['Spesifikasi'] : $row['spesifikasiAsal'];

    $html .= '<tr nobr="true">';
    $html .= '<td class="text-center">' . $no . '</td>';
    $html .= '<td class="text-left">' . $row['KodeProduksi'] . '</td>';
    $html .= '<td class="text-left">' . $barang . '</td>';
    $html .= '<td class="text-left">' . $jenisbarang . '</td>';
    $html .= '<td class="text-center">' . $kategory . '</td>';
    $html .= '<td class="text-left">' . $spesifikasi . '</td>';
    $html .= '<td class="text-center">' . $row['JmlProduksi'] . '</td>';
    $html .= '</tr>';

    $no++;
    $jumlah += $row['Total'];
}
if (!$model) {
    $html .= '<tr><td class="text-center" colspan="6"><strong>Tidak Ada Data</strong></td></tr>';
}



$html .= '
</table>
<br><br>
<table width="100%" border-spacing: 0px; style="font-size: 10pt; padding: 1px">
    <tr>
        <th class="text-center">Dibuat Oleh</th>
        <th class="text-center">Disetujui Oleh</th>
        <th class="text-center">Diketahui Oleh</th>
    </tr>
    <tr>
        <td colspan="3"></td>
    </tr>
    <tr>
        <td colspan="3"></td>
    </tr>
    <tr>
        <td class="text-left">Nama:&nbsp;' . $dtinduk['SPKDibuatOleh'] . '</td>
        <td class="text-left">Nama:&nbsp;' . $dtinduk['SPKDisetujuiOleh'] . '</td>
        <td class="text-left">Nama:&nbsp;' . $dtinduk['SPKDiketahuiOleh'] . '</td>
    </tr>
    <tr>
        <td class="text-left">Tanggal/Jam:&nbsp;' . $SPKTanggal . '</td>
        <td class="text-left">Tanggal/Jam:&nbsp;' . $SPKDisetujuiTgl . '</td>
        <td class="text-left">Tanggal/Jam:&nbsp;' . $SPKDiketahuiTgl . '</td>
    </tr>
</table>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Cetak_spk_' . $dtinduk['IDTransJual'] . '.pdf', 'I');
