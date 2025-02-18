<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetTitle('Detail Transaksi Hutang');
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
  <title>Detail Transaksi Hutang</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Transaksi Hutang</span>
</div>
<br>

<table width="100%" class="table table-borderless" style="font-size: 10pt; padding: 1px">
    <tr>
        <td class="text-left" style="line-height: 20px; width:25%">Kode Transaksi Pembelian</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['IDTransBeli'] . '</td>
        <td class="text-left" style="line-height: 20px; width:25%">Kode Supplier</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['KodePerson'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:25%">No Referensi Pembelian</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['NoRef_Manual'] . '</td>
        <td class="text-left" style="line-height: 20px; width:25%">Nama Supplier</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . $dtinduk['NamaUsaha'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:25%">Tanggal Pembelian</td>
        <td class="text-left" style="line-height: 20px; width:25%">&nbsp;:&nbsp;' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalPembelian']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalPembelian'])) . '</td>
        <td class="text-left" style="line-height: 20px;" colspan="2"></td>
    </tr>
</table>
<br><br>

<table width="100%"  border="1" style="font-size: 10pt; padding: 1px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Kode Transaksi</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>No Ref Bayar Hutang</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Tanggal Bayar Hutang</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Keterangan</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Nominal Dibayar</b></td>
    </tr>';


$no = 1;
$dibayar = 0;
foreach ($model as $row) {

    $html .= '<tr nobr="true">';
    $html .= '<td class="text-center">' . $no . '</td>';
    $html .= '<td class="text-left">' . $row['NoTransKas'] . '</td>';
    $html .= '<td class="text-left">' . $row['NoRef_Manual'] . '</td>';
    $html .= '<td class="text-center">' . shortdate_indo(date('Y-m-d', strtotime($row['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($row['TanggalTransaksi'])) . '</td>';
    $html .= '<td class="text-left">' . $row['Uraian'] . '</td>';
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['TotalTransaksi'], 2)) . '</td>';
    $html .= '</tr>';

    $no++;
    $dibayar += $row['TotalTransaksi'];
}
if (!$model) {
    $html .= '<tr><td class="text-center" colspan="6"><strong>Tidak Ada Data</strong></td></tr>';
}

$sisa = $dtinduk['TotalTagihan'] - $dibayar;


$html .= '
    <tr>
        <td class="text-right" colspan="5">Total Tagihan</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['TotalTagihan'], 2)) . '</td>
    </tr>
    <tr>
        <td class="text-right" colspan="5">Total Dibayar</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dibayar, 2)) . '</td>
    </tr>
    <tr>
        <td class="text-right" colspan="5">Sisa Tagihan</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($sisa, 2)) . '</td>
    </tr>
</table>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Transaksi_hutang_' . $dtinduk['IDTransBeli'] . '.pdf', 'I');
