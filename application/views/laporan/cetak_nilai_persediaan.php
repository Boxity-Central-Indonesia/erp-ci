<?php
$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Laporan Nilai Persediaan Barang');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 25, 10, true);

$date = date("d-m-Y");
setlocale(LC_ALL, 'IND');
$namagudang = isset($gudang) ? $gudang['NamaGudang'] : 'Semua Gudang';
$judul = isset($gudang) ? str_replace(' ', '_', $gudang['NamaGudang']) : 'Semua_Gudang';

$html='
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Laporan Nilai Persediaan Barang</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Laporan Nilai Persediaan Barang</span><br>
    <span class="text-center" style="font-size:11pt; font-weight:none !important;">' . strftime('%d %B %Y', strtotime($date)) . '</span>
</div>
<br>
<div style="font-size:12pt;">' . $namagudang . '</div>
<table width="100%"  border="1" style="font-size: 10pt; padding: 1.2px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Kode Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Nama Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Jenis Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:16%"><b>Stok</b></td>
        <td class="text-center" style="line-height: 20px; width:16%"><b>Nilai HPP</b></td>
        <td class="text-center" style="line-height: 20px; width:13%"><b>Total</b></td>
    </tr>';


    $no = 1;
    $jumlah = 0;
    foreach ($model as $row) {
        $html .= '<tr nobr="true">';
        $html .= '<td class="text-center">' . $no . '</td>';
        $html .= '<td class="text-left">' . $row['KodeManual'] . '</td>';
        $html .= '<td class="text-left">' . $row['NamaBarang'] . '</td>';
        $html .= '<td class="text-left">' . $row['NamaJenisBarang'] . '</td>';
        $html .= '<td class="text-center">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Stok'], 2)) . ' ' . $row['SatuanBarang'] . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['NilaiHPP'], 2)) . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['NilaiHPP'] * $row['Stok'], 2)) . '</td>';
        $html .= '</tr>';

        $no++;
        $jumlah += $row['NilaiHPP'] * $row['Stok'];
    }
    if (!$model) {
        $html .= '<tr><td class="text-center" colspan="7"><strong>Tidak Ada Data</strong></td></tr>';
    }

$html.= 
    '<tr>
        <td colspan="6" class="text-right">Jumlah</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah, 2)) . '</td>
    </tr>
</table>';

$pdf->AddPage('P', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Laporan_Nilai_Persediaan_Barang_' . $judul . '_' . $date . '.pdf', 'I');
?>