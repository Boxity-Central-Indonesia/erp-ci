<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Detail Transaksi Slip Order');
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
  <title>Detail Transaksi Slip Order</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Slip Order</span>
</div>
<br>

<table width="100%" class="table table-borderless" style="font-size: 12pt; padding: 1px">
    <tr>
        <td class="text-left" style="line-height: 20px; width:25%">Kode Slip Order</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['IDTransJual'] . '</td>
        <td class="text-left" style="line-height: 20px; width:25%">Kode Customer</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['KodePerson'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:25%">Nomor Slip Order</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['NoSlipOrder'] . '</td>
        <td class="text-left" style="line-height: 20px; width:25%">Nama Customer</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['NamaUsaha'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:25%">Tanggal Slip Order</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TglSlipOrder']))) . ' ' . date('H:i', strtotime($dtinduk['TglSlipOrder'])) . '</td>
        <td colspan="2"></td>
    </tr>
</table>
<br><br>

<table width="100%"  border="1" style="font-size: 12pt; padding: 1px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:25%"><b>Nama Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Jenis Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Satuan Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:10%"><b>Qty</b></td>
        <td class="text-center" style="line-height: 20px; width:25%"><b>Total</b></td>
    </tr>';


$no = 1;
$jumlah = 0;
foreach ($model as $row) {
    $barang = isset($row['AdditionalName']) ? $row['AdditionalName'] : $row['NamaBarang'];

    $html .= '<tr nobr="true">';
    $html .= '<td class="text-center">' . $no . '</td>';
    $html .= '<td class="text-left">' . $barang . '</td>';
    $html .= '<td class="text-left">' . $row['JenisBarang'] . '</td>';
    $html .= '<td class="text-left">' . $row['SatuanBarang'] . '</td>';
    $html .= '<td class="text-center">' . $row['Qty'] . '</td>';
    $html .= '<td class="text-right">' . number_format($row['Total']) . '</td>';
    $html .= '</tr>';

    $no++;
    $jumlah += $row['Total'];
}
if (!$model) {
    $html .= '<tr><td class="text-center" colspan="6"><strong>Tidak Ada Data</strong></td></tr>';
}



$html .= '
    <tr>
        <td colspan="5" class="text-right">Jumlah</td>
        <td class="text-right">' . number_format($jumlah) . '</td>
    </tr>
</table>
<br><br>
<table width="100%" class="table table-borderless" style="font-size: 12pt; padding: 2px">
    <tr>
        <td class="text-center" colspan="2">Dibuat Oleh</td>
        <td colspan="3"></td>
        <td class="text-center" colspan="2">Penerima Pesanan</td>
    </tr>
    <tr>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td class="text-center" colspan="2"><strong>' . $dtinduk['SODibuatOleh'] . '</strong></td>
        <td colspan="3"></td>
        <td class="text-center" colspan="2"><strong>' . $dtinduk['NamaUsaha'] . '</strong></td>
    </tr>
</table>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Transaksi_slip_order_' . $dtinduk['IDTransJual'] . '.pdf', 'I');
