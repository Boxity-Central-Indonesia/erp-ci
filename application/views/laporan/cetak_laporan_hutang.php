<?php
$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Laporan Hutang');
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
  <title>Laporan Hutang</title>
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
    <span class="text-center" style="font-size:15pt; font-weight: bold;">Laporan Hutang</span><br>
    <span class="text-center" style="font-size:11pt; font-weight:none !important;">' . strftime('%d %B %Y', strtotime($tglawal)) . '&nbsp;s.d.&nbsp;' . strftime('%d %B %Y', strtotime($tglakhir)) . '</span>
</div>
<br>
<table width="100%"  border="1" style="font-size: 10pt; padding: 2px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Kode Transaksi</b></td>
        <td class="text-center" style="line-height: 20px; width:10%"><b>No Ref</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Tanggal Pembelian</b></td>
        <td class="text-center" style="line-height: 20px; width:12%"><b>Nama Supplier</b></td>
        <td class="text-center" style="line-height: 20px; width:13%"><b>Total Tagihan</b></td>
        <td class="text-center" style="line-height: 20px; width:12%"><b>Total Dibayar</b></td>
        <td class="text-center" style="line-height: 20px; width:13%"><b>Sisa Tagihan</b></td>
    </tr>';


    $no = 1;
    foreach ($model as $row) {
        $referensi = isset($row['NoRef_Manual']) ? $row['NoRef_Manual'] : '-';

        $html .= '<tr nobr="true">';
        $html .= '<td class="text-center">' . $no . '</td>';
        $html .= '<td class="text-left">' . $row['IDTransBeli'] . '</td>';
        $html .= '<td class="text-center">' . $referensi . '</td>';
        $html .= '<td class="text-left">' . shortdate_indo(date('Y-m-d', strtotime($row['TanggalPembelian']))) . ' ' . date('H:i', strtotime($row['TanggalPembelian'])) . '</td>';
        $html .= '<td class="text-left">' . $row['NamaPersonCP'] . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['TotalTagihan'], 2)) . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['TotalBayar'], 2)) . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['TotalTagihan'] - $row['TotalBayar'], 2)) . '</td>';
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
$pdf->Output('Laporan_Hutang_' . shortdate_indo(date('Y-m-d', strtotime($tglawal))) . '_' . shortdate_indo(date('Y-m-d', strtotime($tglakhir))) . '.pdf', 'I');
?>