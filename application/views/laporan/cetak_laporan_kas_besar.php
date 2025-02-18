<?php
$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Laporan Kas Besar');
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
  <title>Laporan Kas Besar</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Laporan Kas Besar</span><br>
    <span class="text-center" style="font-size:10pt; font-weight:none !important;">Kode/Nama Akun: ' . $dataakun['KodeAkun'] . ' / ' . $dataakun['NamaAkun'] . '</span><br>
    <span class="text-center" style="font-size:10pt; font-weight:none !important;">Periode Tanggal: ' . $tglawal . ' s.d. ' . $tglakhir . '</span><br>
</div>
<br>
<table width="100%"  border="1" style="font-size: 10pt; padding: 1px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:4%;"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:15%;"><b>Tgl Transaksi</b></td>
        <td class="text-center" style="line-height: 20px; width:15%;"><b>No Transaksi</b></td>
        <td class="text-center" style="line-height: 20px; width:15%;"><b>No Referensi</b></td>
        <td class="text-center" style="line-height: 20px; width:15%;"><b>Uraian</b></td>
        <td class="text-center" style="line-height: 20px; width:12%;"><b>Debet</b></td>
        <td class="text-center" style="line-height: 20px; width:12%;"><b>Kredit</b></td>
        <td class="text-center" style="line-height: 20px; width:12%;"><b>Saldo</b></td>
    </tr>
    <tr style="background-color:#b0aeac; font-weight:bold;">
        <td colspan="7">Saldo sebelum tanggal: ' . $tglawal . '</td>
        <td style="text-align:right; width:12%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldoawal, 2)) . '</td>
    </tr>
    ';


    if (!$model) {
        $html .= '<tr><td class="text-center" colspan="8"><strong>Tidak Ada Data</strong></td></tr>';
    } else {
        $no = 1;
        $saldo = $saldoawal;
        foreach ($model as $row) {
            $saldo += $row['Debet'] - $row['Kredit'];

            $html .= '<tr nobr="true">';
            $html .= '<td class="text-center" style="width:4%;">' . $no . '</td>';
            $html .= '<td class="text-left" style="width:15%;">' . shortdate_indo(date('Y-m-d', strtotime($row['TglTransJurnal']))) . '</td>';
            $html .= '<td class="text-left" style="width:15%;">' . $row['IDTransJurnal'] . '</td>';
            $html .= '<td class="text-left" style="width:15%;">' . $row['NoRefTrans'] . '</td>';
            $html .= '<td class="text-left" style="width:15%;">' . $row['NarasiJurnal'] . '</td>';
            $html .= '<td class="text-right" style="width:12%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Debet'], 2)) . '</td>';
            $html .= '<td class="text-right" style="width:12%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Kredit'], 2)) . '</td>';
            $html .= '<td class="text-right" style="width:12%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldo, 2)) . '</td>';
            $html .= '</tr>';

            $no++;
        }

        $html .=
        '<tr style="background-color:#b0aeac; font-weight:bold;">
            <td colspan="7">Saldo akhir sampai tanggal: ' . $tglakhir . '</td>
            <td style="text-align:right; width:12%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldoakhir, 2)) . '</td>
        </tr>';
    }
    


$html.= '</table>';

$pdf->AddPage('P', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Laporan_Kas_Besar_' . $dataakun['KodeAkun'] . '_' . str_replace('-', '', $tglawal) . '_' . str_replace('-', '', $tglakhir) . '.pdf', 'I');
?>