<?php
$pdf = new Pdf('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->CustomFooterText = $src_url;
$pdf->SetTitle('INVOICE');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
$pdf->SetFont('helvetica', '', 10);
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 0, 10, true);


$html = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>INVOICE</title>
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
<div class="" style="text-align:center;">
    <span style="font-size:18px; font-weight:bold;">' . strtoupper(dataPerusahaan('NamaPerusahaan')['ValueSetting']) . '</span><br>
    <span style="font-size:11px;">' . dataPerusahaan('AlamatPerusahaan')['ValueSetting'] . ' | Telepon ' . dataPerusahaan('NoTelpPerusahaan')['ValueSetting'] . '</span>
</div>
<hr>
<div class="container">
    <h1 class="text-center">INVOICE</h1>
</div>
<br><br><br><br><br><br>

<table width="100%" class="table table-borderless" style="font-size: 14pt; padding: 4px">
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Kode Slip Order</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['IDTransJual'] . '</td>
        <td class="text-left" style="line-height: 20px; width:15%">Kode Customer</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['KodePerson'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Nomor Slip Order</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['NoSlipOrder'] . '</td>
        <td class="text-left" style="line-height: 20px; width:15%">Nama Customer</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['NamaUsaha'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Tanggal Slip Order</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TglSlipOrder']))) . ' ' . date('H:i', strtotime($dtinduk['TglSlipOrder'])) . '</td>
        <td colspan="2"></td>
    </tr>
</table>
<br><br><br><br>

<table width="100%"  border="1" style="font-size: 14pt; padding: 4px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Nama Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Jenis Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Satuan Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:10%"><b>Quantity</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Harga Satuan</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Total</b></td>
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
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['HargaSatuan'], 2)) . '</td>';
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Total'], 2)) . '</td>';
    $html .= '</tr>';

    $no++;
    $jumlah += $row['Total'];
}
if (!$model) {
    $html .= '<tr><td class="text-center" colspan="6"><strong>Tidak Ada Data</strong></td></tr>';
}



$html .= '
    <tr>
        <td colspan="6" class="text-right">Jumlah</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah, 2)) . '</td>
    </tr>' .
    // <tr>
    //     <td class="text-right" colspan="6">Diskon Bawah</td>
    //     <td class="text-right">' . number_format($diskonbawah) . '</td>
    // </tr>
    // <tr>
    //     <td class="text-right" colspan="6">PPN 11%</td>
    //     <td class="text-right">' . number_format($ppn) . '</td>
    // </tr>
    // <tr>
    //     <td class="text-right" colspan="6">Total Tagihan</td>
    //     <td class="text-right">' . number_format($dtinduk['TotalTagihan']) . '</td>
    // </tr>
    '</table>';

$pdf->AddPage('L', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Cetak_invoice_' . $dtinduk['IDTransJual'] . '.pdf', 'I');
