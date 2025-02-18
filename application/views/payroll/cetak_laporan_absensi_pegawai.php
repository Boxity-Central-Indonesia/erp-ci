<?php
$pdf = new Pdf('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 292;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Laporan Absensi Pegawai');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 25, 10, true);

$html='
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Laporan Absensi Pegawai</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Laporan Absensi Pegawai</span><br>
    <span class="text-center" style="font-size:11pt; font-weight:none !important;">Bulan&nbsp;' . $bulan . '</span>
</div>
<br>
<table width="100%"  border="1" style="font-size: 10pt; padding: 2px">';
    $m = date('m', strtotime($bln));
    $y = date('Y', strtotime($bln));
    $totalhari = cal_days_in_month(CAL_GREGORIAN,$m,$y);
    $no = 1;
    $html.='<tr>';
        $html.='<td class="text-center" style="line-height: 20px; width:4%;">No</td>';
        $html.='<td class="text-center" style="line-height: 20px; width:8%;">NIP</td>';
        $html.='<td class="text-center" style="line-height: 20px; width:10.5%;">Nama</td>';
        for($i=0;$i<$totalhari; $i++){
            $html.='<td class="text-center" style="line-height: 20px; width:2.5%;">'.($i+1).'</td>';
        }
    $html.='</tr>';
    foreach ($model as $row) {
        $html.='<tr nobr="true">';
            $html.='<td class="text-center" style="line-height: 20px; width:4%;">'.$no.'</td>';
            $html.='<td style="line-height: 20px; width:8%;">'.$row['NIP'].'</td>';
            $html.='<td style="line-height: 20px; width:10.5%;">'.$row['NamaPegawai'].'</td>';
            for($i=0;$i<$totalhari; $i++){
                if ($row['d'.($i+1)] == 'Hadir') {
                    $val = 'H';
                } elseif ($row['d'.($i+1)] == 'Izin') {
                    $val = 'I';
                } elseif ($row['d'.($i+1)] == 'Alpha') {
                    $val = 'A';
                } elseif ($row['d'.($i+1)] == 'Sakit') {
                    $val = 'S';
                } elseif ($row['d'.($i+1)] == 'Dinas Luar') {
                    $val = 'DL';
                } else {
                    $val = '<span style="color:red; font-weight:bold;">L</span>';
                }
                $html.='<td style="text-align:center; width:2.5%;">'.$val.'</td>';
            }
        $html.='</tr>';

        $no++;
    }
    if (!$model) {
        $html .= '<tr><td class="text-center" colspan="'.($totalhari+3).'"><strong>Tidak Ada Data</strong></td></tr>';
    }

$html.= '</table>';

// echo $html;

$pdf->AddPage('L', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Laporan_Absensi_Pegawai_' . str_replace(' ', '_', $bulan) . '.pdf', 'I');
?>