<?php
$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Laporan Laba Rugi');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 25, 10, true);

$date = date("d-m-Y");
setlocale(LC_ALL, 'IND');

function draw_table($judul, $space = '&nbsp;')
{
    $saldoanak = $judul['SaldoAnak'] + $judul['SaldoAnakPenyesuaian'];

    $data = '<tr>';
    $data .= '<td>' . $judul['KodeAkun'] . '</td>';
    $data .= '<td>' . ($space != '' ? $space . '- '   : $space . '* ') . $judul['NamaAkun'] . '</td>';
    $data .= '<td class="text-right">Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldoanak, 2)) . '</td>';
    $data .= '</tr>';
    if (isset($judul['anak'])) {
        foreach ($judul['anak'] as $key => $value) {
            $data .= draw_table($value, ($space . '&ensp;&emsp;'));
        }
    }
    return $data;
}

$html='
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Laporan Laba Rugi</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <style>
    .text-center {
        vertical-align: middle;
        text-align: center;
    }
    .text-left {
        vertical-align: middle;
        text-align: left;
        padding-left: 3px !important;
    }
    .text-right{
        text-align:right;
        padding-right: 2px !important;
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
  </style>
</head>
<body>
<div class="container text-center">
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Laporan Laba Rugi</span><br>
    <span class="text-center" style="font-size:11pt; font-weight:none !important;">Level Akun: ' . ucfirst($level) . '</span><br>
    <span class="text-center" style="font-size:11pt; font-weight:none !important;">Bulan: ' . bln_indo($bulan) . '</span><br>
</div>
<br>
<table width="100%"  border="1" style="font-size: 10pt; padding: 1px">
    <tr>
        <td class="text-center" style="line-height: 20px; width: 15%;"><b>KodeAkun</b></td>
        <td class="text-center" style="line-height: 20px; width: 35%;"><b>Nama Akun</b></td>
        <td class="text-center" style="line-height: 20px; width: 50%;"><b>Nominal</b></td>
    </tr>
    ';


    if (!isset($data)) {
        $html .= '<tr><td class="text-center" colspan="3"><strong>Tidak Ada Data</strong></td></tr>';
    } else {
        $TotalPendapatan = 0;
        $TotalBebanProduksi = 0;
        $TotalBebanDilurProduksi = 0;
        $TotalPembelian = 0;
        foreach ($data as $key => $row) {
            if ($row['KodeAkun'] == 4) {
                $TotalPendapatan += ($row['SaldoAnak'] + $row['SaldoAnakPenyesuaian']);
            }
            if (substr($row['KodeAkun'], 0, 1) == 4) {
                $html .= draw_table($row, '');
            }
        }
        $html .= '<tr>
                <td colspan="2"><strong>TOTAL PENDAPATAN</strong></td>
                <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalPendapatan, 2)) . '</strong></td>
                </tr>';
        $html .=  '<tr><td></td><td colspan="2"></td></tr>';
        $html .= '<tr>
                <td colspan="2"><strong>TOTAL HPP</strong></td>
                <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalHPP, 2)) . '</strong></td>
                </tr>';
        $html .=  '<tr><td></td><td colspan="2"></td></tr>';
        foreach ($data as $key => $row) {
            if ($row['KodeAkun'] == 5 || $row['KodeAkun'] == 6) { // || $row['KodeAkun'] == 7
                if ($row['KodeAkun'] == 5) {
                    $TotalBebanProduksi += ($row['SaldoAnak'] + $row['SaldoAnakPenyesuaian']);
                } elseif ($row['KodeAkun'] == 6) {
                    $html .= '<tr>
                    <td colspan="2"><strong>TOTAL BEBAN PRODUKSI</strong></td>
                    <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalBebanProduksi, 2)) . '</strong></td>
                    </tr>';
                    $html .=  '<tr><td></td><td colspan="2"></td></tr>';
                    $TotalBebanDilurProduksi += ($row['SaldoAnak'] + $row['SaldoAnakPenyesuaian']);
                }
                elseif ($row['KodeAkun'] == 7) {
                    $TotalPembelian += ($row['SaldoAnak'] + $row['SaldoAnakPenyesuaian']);
                }
            }
            if (substr($row['KodeAkun'], 0, 1) == 5 || substr($row['KodeAkun'], 0, 1) == 6) { // || substr($row['KodeAkun'], 0, 1) == 7
                $html .= draw_table($row, '');
            }
        }
        $html .= '<tr>
        <td colspan="2"><strong>TOTAL BEBAN DILUAR PRODUKSI</strong></td>
        <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalBebanDilurProduksi, 2)) . '</strong></td>
        </tr>';
        $html .=  '<tr><td></td><td colspan="2"></td></tr>';

        $TotalBeban = $TotalBebanProduksi + $TotalBebanDilurProduksi + $TotalPembelian;
        $html .= '<tr>
        <td colspan="2"><strong>TOTAL BEBAN</strong></td>
        <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalBeban, 2)) . '</strong></td>
        </tr>';

        $html .= '<tr>
        <td colspan="2"><strong>LABA / (RUGI) </strong></td>
        <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalPendapatan - $TotalBeban - $TotalHPP, 2)) . '</strong></td>
        </tr>';
    }
    


$html.= '</table>';

$pdf->AddPage('P', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Laporan_Laba_Rugi_' . $nama_level . '_Bulan_' . str_replace(' ', '_', bln_indo($bulan)) . '.pdf', 'I');
?>