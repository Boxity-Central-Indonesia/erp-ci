<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Jurnal Transaksi Kas');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
// $pdf->SetFont('helvetica', '', 10);
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 25, 10, true);


$html='
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Jurnal Transaksi Kas</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Jurnal Transaksi Kas</span>
</div>
<br>

<table width="100%" class="table table-borderless" style="font-size: 12pt; padding: 1.5px">
    <tr>
        <td class="text-left" style="line-height: 20px; width:18%">Kode Transaksi</td>
        <td style="width:2%;">:</td>
        <td class="text-left" style="line-height: 20px; width:30%">' . $dtinduk['NoTransKas'] . '</td>
        <td class="text-left" style="line-height: 20px; width:18%">No Referensi</td>
        <td style="width:2%;">:</td>
        <td class="text-left" style="line-height: 20px; width:30%">' . $dtinduk['NoRef_Manual'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:18%">Tahun Anggaran</td>
        <td style="width:2%;">:</td>
        <td class="text-left" style="line-height: 20px; width:30%">' . $dtinduk['KodeTahun'] . '</td>
        <td class="text-left" style="line-height: 20px; width:18%">Tanggal Transaksi</td>
        <td style="width:2%;">:</td>
        <td class="text-left" style="line-height: 20px; width:30%">' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalTransaksi'])) . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:18%">Jenis Transaksi</td>
        <td style="width:2%;">:</td>
        <td class="text-left" style="line-height: 20px; width:30%">' . $dtinduk['JenisTransaksiKas'] . '</td>
        <td class="text-left" style="line-height: 20px; width:18%">Nominal Transaksi</td>
        <td style="width:2%;">:</td>
        <td class="text-left" style="line-height: 20px; width:30%">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['NominalTransaksi'], 2)) . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:18%">Uraian</td>
        <td style="width:2%;">:</td>
        <td colspan="4" class="text-left" style="line-height: 20px; width:80%">' . $dtinduk['Uraian'] . '</td>
    </tr>
</table>
<br><br>

<table width="100%"  border="1" style="font-size: 12pt; padding: 1.5px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Kode Akun</b></td>
        <td class="text-center" style="line-height: 20px; width:30%"><b>Nama Akun</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Keterangan</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Debet</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Kredit</b></td>
    </tr>';


    $no = 1;
    $jmldebet = 0;
    $jmlkredit = 0;
    foreach ($model as $row) {
        
        $html .= '<tr nobr="true">';
        $html .= '<td class="text-center">' . $no . '</td>';
        $html .= '<td class="text-left">' . $row['KodeAkun'] . '</td>';
        $html .= '<td class="text-left">' . $row['NamaAkun'] . '</td>';
        $html .= '<td class="text-left">' . $row['Uraian'] . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Debet'], 2)) . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Kredit'], 2)) . '</td>';
        $html .= '</tr>';

        $no++;
        $jmldebet += $row['Debet'];
        $jmlkredit += $row['Kredit'];
    }
    if (!$model) {
        $html .= '<tr><td class="text-center" colspan="6"><strong>Tidak Ada Data</strong></td></tr>';
    }

    if ($model) {
        $html.= '
            <tr>
                <td class="text-right" colspan="4">Jumlah</td>
                <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jmldebet, 2)) . '</td>
                <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jmlkredit, 2)) . '</td>
            </tr>';
    }

$html.= '</table>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Transaksi_kas_jurnal_' . $dtinduk['NoTransKas'] . '.pdf', 'I');
?>