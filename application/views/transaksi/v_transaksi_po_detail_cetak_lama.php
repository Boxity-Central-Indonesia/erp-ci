<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Detail Transaksi Pembelian (PO)');
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
  <title>Detail Transaksi Pembelian (PO)</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Transaksi Pembelian (PO)</span>
</div>
<br>

<table width="100%" class="table table-borderless" style="font-size: 12pt; padding: 1.5px">
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Nomor PO</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['IDTransBeli'] . '</td>
        <td class="text-left" style="line-height: 20px; width:15%">Kode Supplier</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['KodePerson'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Tanggal PO</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TglPO']))) . ' ' . date('H:i', strtotime($dtinduk['TglPO'])) . '</td>
        <td class="text-left" style="line-height: 20px; width:15%">Nama Supplier</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['NamaUsaha'] . '</td>
    </tr>
</table>
<br><br><br><br>

<table width="100%"  border="1" style="font-size: 12pt; padding: 1.5px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:25%"><b>Nama Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Satuan Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Harga Satuan</b></td>
        <td class="text-center" style="line-height: 20px; width:10%"><b>Quantity</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Total</b></td>
    </tr>';


$no = 1;
$jumlah = 0;
foreach ($model as $row) {

    $html .= '<tr nobr="true">';
    $html .= '<td class="text-center">' . $no . '</td>';
    $html .= '<td class="text-left">' . $row['NamaBarang'] . '</td>';
    $html .= '<td class="text-left">' . $row['SatuanBarang'] . '</td>';
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['HargaSatuan'], 2)) . '</td>';
    $html .= '<td class="text-center">' . $row['Qty'] . '</td>';
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
        <td colspan="5" class="text-right">Jumlah</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah, 2)) . '</td>
    </tr>
</table>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Transaksi_po_' . $dtinduk['IDTransBeli'] . '.pdf', 'I');
