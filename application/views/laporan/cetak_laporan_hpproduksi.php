<?php
$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Laporan Harga Pokok Produksi');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 25, 10, true);

$date = date("d-m-Y");
setlocale(LC_ALL, 'IND');

$html='
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Laporan Harga Pokok Produksi</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Laporan Harga Pokok Produksi</span><br>
    <span class="text-center" style="font-size:11pt; font-weight:none !important;">' . strftime('%d %B %Y', strtotime($tglawal)) . '&nbsp;s.d.&nbsp;' . strftime('%d %B %Y', strtotime($tglakhir)) . '</span>
</div>
<br>
<table width="100%"  border="1" style="font-size: 10pt; padding: 2px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:4%;"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:18%;"><b>Kode Produksi</b></td>
        <td class="text-center" style="line-height: 20px; width:18%;"><b>No SPK</b></td>
        <td class="text-center" style="line-height: 20px; width:14%;"><b>Tanggal SPK</b></td>
        <td class="text-center" style="line-height: 20px; width:9%;"><b>Kode Prod Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:13%;"><b>Barang Produksi</b></td>
        <td class="text-center" style="line-height: 20px; width:6%;"><b>Qty</b></td>
        <td class="text-center" style="line-height: 20px; width:11%;"><b>HPP Per Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:11%;"><b>Total HPP Produksi</b></td>
    </tr>';


    $no = 1;
    foreach ($model as $row) {

        $html .= '<tr nobr="true">';
        $html .= '<td class="text-center">' . $no . '</td>';
        $html .= '<td class="text-left">' . $row['NoRefTrSistem'] . '</td>';
        $html .= '<td class="text-center">' . $row['SPKNomor'] . '</td>';
        $html .= '<td class="text-left">' . shortdate_indo(date('Y-m-d', strtotime($row['SPKTanggal']))) . ' ' . date('H:i', strtotime($row['SPKTanggal'])) . '</td>';
        $html .= '<td class="text-left">' . $row['KodeProduksi'] . '</td>';
        $html .= '<td class="text-left">' . $row['NamaBarang'] . '</td>';
        $html .= '<td class="text-center">' . $row['JmlProduksi'] . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['HPPProduksi'], 2)) . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['JmlProduksi'] * $row['HPPProduksi'], 2)) . '</td>';
        $html .= '</tr>';

        $no++;
    }
    if (!$model) {
        $html .= '<tr><td class="text-center" colspan="8"><strong>Tidak Ada Data</strong></td></tr>';
    }
    


$html.= 
    // <tr>
    //     <td colspan="6" class="text-right">Jumlah</td>
    //     <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah, 2)) . '</td>
    // </tr>
'</table>';

$pdf->AddPage('P', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Laporan_HPProduksi_' . shortdate_indo(date('Y-m-d', strtotime($tglawal))) . '_' . shortdate_indo(date('Y-m-d', strtotime($tglakhir))) . '.pdf', 'I');
?>