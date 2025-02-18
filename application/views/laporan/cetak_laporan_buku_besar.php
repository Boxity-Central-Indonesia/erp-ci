<?php
$pdf = new Pdf('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 292;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Laporan Buku Besar');
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
  <title>Laporan Buku Besar</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Laporan Buku Besar</span><br>
    <span class="text-center" style="font-size:10pt; font-weight:none !important;">Periode Tanggal: ' . $tglawal . ' s.d. ' . $tglakhir . '</span><br>
</div>
<br>
<table width="100%"  border="1" style="font-size: 12pt; padding: 2.5px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:4%;"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:10%;"><b>Tgl Transaksi</b></td>
        <td class="text-center" style="line-height: 20px; width:15%;"><b>No Transaksi</b></td>
        <td class="text-center" style="line-height: 20px; width:15%;"><b>No Referensi</b></td>
        <td class="text-center" style="line-height: 20px; width:20%;"><b>Uraian</b></td>
        <td class="text-center" style="line-height: 20px; width:12%;"><b>Debet</b></td>
        <td class="text-center" style="line-height: 20px; width:12%;"><b>Kredit</b></td>
        <td class="text-center" style="line-height: 20px; width:12%;"><b>Saldo</b></td>
    </tr>';


    if (!$data) {
        $html .= '<tr><td class="text-center" colspan="8"><strong>Tidak Ada Data</strong></td></tr>';
    } else {
        $totaldebet = 0;
        $totalkredit = 0;
        foreach ($data as $row) {

            if ($row['Item']) {
                $html .= '<tr nobr="true" style="font-weight:bold;">';
                $html .= '<td class="text-center" style="width:14%;" colspan="2">' . $row['KodeAkun'] . '</td>';
                $html .= '<td class="text-left" style="width:30%;" colspan="2">' . $row['NamaAkun'] . '</td>';
                $html .= '<td class="text-left" style="width:44%;">Saldo sebelum tanggal '. date_indo($t_awal) .' :</td>';
                $html .= '<td class="text-right" style="width:12%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['SaldoAwal'], 2)) . '</td>';
                $html .= '</tr>';

                $no = 1;
                foreach ($row['Item'] as $row2) {
                    $html .= '<tr nobr="true">';
                    $html .= '<td style="width:4%;" class="text-center">'. $no .'</td>';
                    $html .= '<td style="width:10%;">'. shortdate_indo(date('Y-m-d', strtotime($row2['TglTransJurnal']))) .'</td>';
                    $html .= '<td style="width:15%;">'. $row2['IDTransJurnal'] .'</td>';
                    $html .= '<td style="width:15%;">'. $row2['NoRefTrans'] .'</td>';
                    $html .= '<td style="width:20%;">'. $row2['NarasiJurnal'] .'</td>';
                    $html .= '<td style="width:12%;" class="text-right">'. str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row2['Debet'], 2)) .'</td>';
                    $html .= '<td style="width:12%;" class="text-right">'. str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row2['Kredit'], 2)) .'</td>';
                    $html .= '<td style="width:12%;" class="text-right">'. str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row2['Saldo'], 2)) .'</td>';
                    $html .= '</tr>';

                    $no++;
                    $totaldebet += $row2['Debet'];
                    $totalkredit += $row2['Kredit'];
                }

                $html .= '<tr nobr="true" style="font-weight:bold;">';
                $html .= '<td class="text-left" style="width:44%;" colspan="4"></td>';
                $html .= '<td class="text-left" style="width:44%;">Saldo akhir sampai tanggal '. date_indo($t_akhir) .' :</td>';
                $html .= '<td class="text-right" style="width:12%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['SaldoAkhir'], 2)) . '</td>';
                $html .= '</tr>';
                $html .= '<tr><td colspan="8" style="width:100%;"></td></tr>';
            } else {
                $html .= '<tr nobr="true" style="font-weight:bold;">';
                $html .= '<td class="text-center" style="width:14%;" colspan="2">'. $row['KodeAkun'] .'</td>';
                $html .= '<td class="text-left" style="width:30%;" colspan="2">'. $row['NamaAkun'] .'</td>';
                $html .= '<td class="text-left" style="width:44%;" colspan="3">Saldo akhir sampai tanggal '. date_indo($t_akhir) .' :</td>';
                $html .= '<td class="text-right" style="width:12%;">'. str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['SaldoAkhir'], 2)) .'</td>';
                $html .= '</tr>';
                // $html .= '<tr><td colspan="8"></td></tr>';
            }
        }

        $html .= '<tr nobr="true" style="font-weight:bold;">';
        $html .= '<td class="text-right" colspan="5">Total</td>';
        $html .= '<td class="text-right">'. str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totaldebet, 2)) .'</td>';
        $html .= '<td class="text-right">'. str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalkredit, 2)) .'</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
    }
    


$html.= '</table>';

$pdf->AddPage('L', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Laporan Buku Besar mulai ' . date_indo($t_awal) . ' s.d. ' . date_indo($t_akhir) . '.pdf', 'I');
?>