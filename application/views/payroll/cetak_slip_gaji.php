<?php
$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Laporan Slip Gaji');
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
  <title>Laporan Slip Gaji</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Laporan Slip Gaji</span>
</div>
<br>
<table width="100%"  class="table table-borderless" style="font-size: 12pt; padding: 2px">
    <tr>
        <td style="width:15%">NIP </td>
        <td style="width:35%">:&nbsp;' . $dtinduk['NIP'] . '</td>
        <td style="width:15%">Jabatan </td>
        <td style="width:35%">:&nbsp;' . $dtinduk['NamaJabatan'] . '</td>
    </tr>
    <tr>
        <td style="width:15%">Nama </td>
        <td style="width:35%">:&nbsp;' . $dtinduk['NamaPegawai'] . '</td>
        <td style="width:15%">Periode </td>
        <td style="width:35%">:&nbsp;' . $bulan . '</td>
    </tr>
</table>
<br><br>
<table width="100%"  border="1" style="font-size: 12pt; padding: 2px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:30%"><b>Nama Komponen/Aktivitas</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Perolehan</b></td>
        <td class="text-center" style="line-height: 20px; width:45%"><b>Keterangan</b></td>
    </tr>';

    $jumlah1 = 0;
    $jumlah2 = 0;
    $no = 1;
    foreach ($model as $row) {
        $jmlperolehan = ($row['CaraHitung'] == 'Tambah') ? str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['JmlPerolehan'], 2)) : '(' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['JmlPerolehan'], 2)) . ')';
        $text_ket = isset($row['Keterangan']) ? "text-left" : "text-center";
        $ket = isset($row['Keterangan']) ? $row['Keterangan'] : "-";

        $html .= '<tr nobr="true">';
        $html .= '<td class="text-center">' . $no . '</td>';
        $html .= '<td class="text-left">' . $row['NamaPekerjaan'] . '</td>';
        $html .= '<td class="text-right">' . $jmlperolehan . '</td>';
        $html .= '<td class="' . $text_ket . '">' . $ket . '</td>';
        $html .= '</tr>';

        $jumlah1 += ($row['CaraHitung'] == 'Tambah') ? $row['JmlPerolehan'] : 0;
        $jumlah2 += ($row['CaraHitung'] == 'Kurang') ? $row['JmlPerolehan'] : 0;
        $no++;
    }
    if (!$model) {
        $html .= '<tr><td class="text-center" colspan="5"><strong>Tidak Ada Data</strong></td></tr>';
    }
    


$html.= 
    '<tr>
        <td colspan="2" class="text-right"><b>Total Perolehan</b></td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah1 - $jumlah2, 2)) . '</td>
        <td></td>
    </tr>
</table>';

$pdf->AddPage('P', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Laporan_Slip_Gaji_' . str_replace(' ', '_', $dtinduk['NamaPegawai']) . '_' . str_replace(' ', '_', $bulan) . '.pdf', 'I');
?>