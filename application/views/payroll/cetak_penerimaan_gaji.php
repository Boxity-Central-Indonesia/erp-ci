<?php
$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Transaksi Penerimaan Gaji');
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
  <title>Transaksi Penerimaan Gaji</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Transaksi Penerimaan Gaji</span><br>
    <span class="text-center" style="font-size:11pt; font-weight:none !important;">Periode:&nbsp;' . $bulan . '</span>
</div>
<br>
<table width="100%"  border="1" style="font-size: 12pt; padding: 2px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>NIP</b></td>
        <td class="text-center" style="line-height: 20px; width:25%"><b>Nama Pegawai</b></td>
        <td class="text-center" style="line-height: 20px; width:25%"><b>Jabatan</b></td>
        <td class="text-center" style="line-height: 20px; width:25%"><b>Total Insentif</b></td>
    </tr>';

    $jumlah = 0;
    $no = 1;
    foreach ($model as $row) {

        $html .= '<tr nobr="true">';
        $html .= '<td class="text-center">' . $no . '</td>';
        $html .= '<td class="text-left">' . $row['NIP'] . '</td>';
        $html .= '<td class="text-left">' . $row['NamaPegawai'] . '</td>';
        $html .= '<td class="text-left">' . $row['NamaJabatan'] . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['InsentifPegawai'], 2)) . '</td>';
        $html .= '</tr>';

        $jumlah += $row['InsentifPegawai'];
        $no++;
    }
    if (!$model) {
        $html .= '<tr><td class="text-center" colspan="5"><strong>Tidak Ada Data</strong></td></tr>';
    }
    


$html.= 
    '<tr>
        <td colspan="4" class="text-right"><b>Jumlah Total</b></td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah, 2)) . '</td>
    </tr>
</table>';

$pdf->AddPage('P', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Transaksi_Penerimaan_Gaji_Periode_' . str_replace(' ', '_', $bulan) . '.pdf', 'I');
?>