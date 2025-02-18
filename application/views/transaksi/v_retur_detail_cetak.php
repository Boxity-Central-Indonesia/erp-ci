<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetTitle('Retur Penjualan');
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
  <title>Detail Retur Penjualan</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Retur Penjualan</span>
</div>
<br>

<table width="100%" class="table table-borderless" style="font-size: 10pt; padding: 1px">
    <tr>
        <td class="text-left" style="line-height: 20px; width:25%">Kode Transaksi</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['IDTransRetur'] . '</td>
        <td class="text-left" style="line-height: 20px; width:25%">Kode Referensi Penjualan</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['IDTransJual'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:25%">Tanggal Transaksi</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalTransaksi'])) . '</td>
        <td class="text-left" style="line-height: 20px; width:25%">Tanggal Penjualan</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalPenjualan']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalPenjualan'])) . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:25%">Nama Customer</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['NamaUsaha'] . '</td>
        <td class="text-left" style="line-height: 20px; width:25%">Jenis Realisasi</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['JenisRealisasi'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:25%">Nama Gudang</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['NamaGudang'] . '</td>
        <td class="text-left" style="line-height: 20px; width:25%">Keterangan</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['Keterangan'] . '</td>
    </tr>
</table>
<br><br>

<table width="100%"  border="1" style="font-size: 10pt; padding: 1px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:25%"><b>Nama Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:10%"><b>Qty Jual</b></td>
        <td class="text-center" style="line-height: 20px; width:10%"><b>Qty Retur</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Harga Satuan</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Total Retur</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Alasan Retur</b></td>
    </tr>';


$no = 1;
$jumlah = 0;
foreach ($model as $row) {

    $html .= '<tr nobr="true">';
    $html .= '<td class="text-center">' . $no . '</td>';
    $html .= '<td class="text-left">' . $row['NamaBarang'] . '</td>';
    $html .= '<td class="text-center">' . $row['JmlJual'] . ' ' . $row['SatuanBarang'] . '</td>';
    $html .= '<td class="text-center">' . $row['JmlRetur'] . ' ' . $row['SatuanBarang'] . '</td>';
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['HargaJual'], 2)) . '</td>';
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['TotalRetur'], 2)) . '</td>';
    $html .= '<td class="text-left">' . $row['AlasanRetur'] . '</td>';
    $html .= '</tr>';

    $no++;
    $jumlah += $row['TotalRetur'];
}
if (!$model) {
    $html .= '<tr><td class="text-center" colspan="7"><strong>Tidak Ada Data</strong></td></tr>';
}



$html .= '
    <tr>
        <td class="text-right" colspan="5">Jumlah</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah, 2)) . '</td>
    </tr>
</table>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Retur_penjualan_' . $dtinduk['IDTransRetur'] . '.pdf', 'I');
