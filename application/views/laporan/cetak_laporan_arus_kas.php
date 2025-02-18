<?php
$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Laporan Arus Kas');
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
  <title>Laporan Arus Kas</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold">Laporan Arus Kas</span><br>
    <span class="text-center" style="font-size:11pt; font-weight:none !important;">Periode Tanggal: ' . $tglawal . ' s.d. ' . $tglakhir . '</span>
</div>
<br>
<table width="100%" class="table table-borderless" style="font-size: 10pt; padding: 2px">
    <thead>
        <tr style="font-weight: bold; background-color: #acacac;">
            <th class="text-center" style="width: 5%;"><b>#</b></th>
            <th class="text-center" style="width: 50%;"><b>Arus Kas</b></th>
            <th class="text-center" style="width: 15%;"><b>Nominal</b></th>
            <th class="text-center" style="width: 15%;"><b>Subtotal</b></th>
            <th class="text-center" style="width: 15%;"><b>Total</b></th>
        </tr>
    </thead>
    <tbody>';

    // arus kas operasional
    $html .= '
        <tr style="font-weight: bold;">
            <td class="text-right" style="width: 5%;">1</td>
            <td class="text-left" style="width: 50%;">Arus Kas Operational</td>
            <td colspan="3"></td>
        </tr>';
        
        $penerimaan_op = 0;
        if (count($masuk_op) > 0) {
            $html .= '
                <tr style="font-weight: bold;">
                    <td class="text-right" style="width: 5%;"></td>
                    <td style="width: 50%;">Penerimaan kas dari:</td>
                    <td colspan="3"></td>
                </tr>';

            foreach ($masuk_op as $row){
                $html .= '<tr nobr="true">';
                $html .= '<td class="text-center" style="width: 5%;"></td>';
                $html .= '<td class="text-left" style="width: 50%;">&nbsp;&nbsp;&nbsp;' . $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' . '</td>';
                $html .= '<td class="text-right" style="width: 15%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) . '</td>';
                $html .= '<td class="text-right" colspan="2"></td>';
                $html .= '</tr>';

                $penerimaan_op += $row['Nominal'];
            }
        }

    $html .= '
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">&nbsp;&nbsp;&nbsp;Jumlah penerimaan kas:</td>
            <td style="width: 15%;"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_op, 2)) . '</td>
            <td style="width: 15%;"></td>
        </tr>';

        $pengeluaran_op = 0;
        if (count($keluar_op) > 0) {
            $html .= '
                <tr style="font-weight: bold;">
                    <td class="text-right" style="width: 5%;"></td>
                    <td style="width: 50%;">Pengeluaran kas untuk:</td>
                    <td colspan="3"></td>
                </tr>';

            foreach ($keluar_op as $row) {
                $html .= '<tr nobr="true">';
                $html .= '<td class="text-center" style="width: 5%;"></td>';
                $html .= '<td class="text-left" style="width: 50%;">&nbsp;&nbsp;&nbsp;' . $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' . '</td>';
                $html .= '<td class="text-right" style="width: 15%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) . '</td>';
                $html .= '<td class="text-right" colspan="2"></td>';
                $html .= '</tr>';

                $pengeluaran_op += $row['Nominal'];
            }
        }

    $html .= '
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">&nbsp;&nbsp;&nbsp;Jumlah pengeluaran kas:</td>
            <td style="width: 15%;"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($pengeluaran_op, 2)) . '</td>
            <td style="width: 15%;"></td>
        </tr>
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">Arus Kas Operational</td>
            <td colspan="2"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_op - $pengeluaran_op, 2)) . '</td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
    ';
    // arus kas operasional

    // arus kas investasi
    $html .= '
        <tr style="font-weight: bold;">
            <td class="text-right" style="width: 5%;">2</td>
            <td class="text-left" style="width: 50%;">Arus Kas Investasi</td>
            <td colspan="3"></td>
        </tr>';
        
        $penerimaan_inv = 0;
        if (count($masuk_inv) > 0) {
            $html .= '
                <tr style="font-weight: bold;">
                    <td class="text-right" style="width: 5%;"></td>
                    <td style="width: 50%;">Penerimaan kas dari:</td>
                    <td colspan="3"></td>
                </tr>';

            foreach ($masuk_inv as $row){
                $html .= '<tr nobr="true">';
                $html .= '<td class="text-center" style="width: 5%;"></td>';
                $html .= '<td class="text-left" style="width: 50%;">&nbsp;&nbsp;&nbsp;' . $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' . '</td>';
                $html .= '<td class="text-right" style="width: 15%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) . '</td>';
                $html .= '<td class="text-right" colspan="2"></td>';
                $html .= '</tr>';

                $penerimaan_inv += $row['Nominal'];
            }
        }

    $html .= '
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">&nbsp;&nbsp;&nbsp;Jumlah penerimaan kas:</td>
            <td style="width: 15%;"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_inv, 2)) . '</td>
            <td style="width: 15%;"></td>
        </tr>';

        $pengeluaran_inv = 0;
        if (count($keluar_inv) > 0) {
            $html .= '
                <tr style="font-weight: bold;">
                    <td class="text-right" style="width: 5%;"></td>
                    <td style="width: 50%;">Pengeluaran kas untuk:</td>
                    <td colspan="3"></td>
                </tr>';

            foreach ($keluar_inv as $row) {
                $html .= '<tr nobr="true">';
                $html .= '<td class="text-center" style="width: 5%;"></td>';
                $html .= '<td class="text-left" style="width: 50%;">&nbsp;&nbsp;&nbsp;' . $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' . '</td>';
                $html .= '<td class="text-right" style="width: 15%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) . '</td>';
                $html .= '<td class="text-right" colspan="2"></td>';
                $html .= '</tr>';

                $pengeluaran_inv += $row['Nominal'];
            }
        }

    $html .= '
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">&nbsp;&nbsp;&nbsp;Jumlah pengeluaran kas:</td>
            <td style="width: 15%;"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($pengeluaran_inv, 2)) . '</td>
            <td style="width: 15%;"></td>
        </tr>
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">Arus Kas Investasi</td>
            <td colspan="2"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_inv - $pengeluaran_inv, 2)) . '</td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
    ';
    // arus kas investasi

    // arus kas pembiayaan
    $html .= '
        <tr style="font-weight: bold;">
            <td class="text-right" style="width: 5%;">3</td>
            <td class="text-left" style="width: 50%;">Arus Kas Pembiayaan</td>
            <td colspan="3"></td>
        </tr>';
        
        $penerimaan_bi = 0;
        if (count($masuk_bi) > 0) {
            $html .= '
                <tr style="font-weight: bold;">
                    <td class="text-right" style="width: 5%;"></td>
                    <td style="width: 50%;">Penerimaan kas dari:</td>
                    <td colspan="3"></td>
                </tr>';

            foreach ($masuk_bi as $row){
                $html .= '<tr nobr="true">';
                $html .= '<td class="text-center" style="width: 5%;"></td>';
                $html .= '<td class="text-left" style="width: 50%;">&nbsp;&nbsp;&nbsp;' . $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' . '</td>';
                $html .= '<td class="text-right" style="width: 15%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) . '</td>';
                $html .= '<td class="text-right" colspan="2"></td>';
                $html .= '</tr>';

                $penerimaan_bi += $row['Nominal'];
            }
        }

    $html .= '
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">&nbsp;&nbsp;&nbsp;Jumlah penerimaan kas:</td>
            <td style="width: 15%;"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_bi, 2)) . '</td>
            <td style="width: 15%;"></td>
        </tr>';

        $pengeluaran_bi = 0;
        if (count($keluar_bi) > 0) {
            $html .= '
                <tr style="font-weight: bold;">
                    <td class="text-right" style="width: 5%;"></td>
                    <td style="width: 50%;">Pengeluaran kas untuk:</td>
                    <td colspan="3"></td>
                </tr>';

            foreach ($keluar_bi as $row) {
                $html .= '<tr nobr="true">';
                $html .= '<td class="text-center" style="width: 5%;"></td>';
                $html .= '<td class="text-left" style="width: 50%;">&nbsp;&nbsp;&nbsp;' . $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' . '</td>';
                $html .= '<td class="text-right" style="width: 15%;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) . '</td>';
                $html .= '<td class="text-right" colspan="2"></td>';
                $html .= '</tr>';

                $pengeluaran_bi += $row['Nominal'];
            }
        }

    $html .= '
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">&nbsp;&nbsp;&nbsp;Jumlah pengeluaran kas:</td>
            <td style="width: 15%;"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($pengeluaran_bi, 2)) . '</td>
            <td style="width: 15%;"></td>
        </tr>
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">Arus Kas Pembiayaan</td>
            <td colspan="2"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_bi - $pengeluaran_bi, 2)) . '</td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
    ';
    // arus kas pembiayaan

    $kenaikan = (($penerimaan_op - $pengeluaran_op) + ($penerimaan_inv - $pengeluaran_inv) + ($penerimaan_bi - $pengeluaran_bi));
    $html .= '
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">Kenaikan Kas</td>
            <td colspan="2"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($kenaikan, 2)) . '</td>
        </tr>
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">Saldo Kas Awal</td>
            <td colspan="2"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldoawal, 2)) . '</td>
        </tr>
        <tr style="font-weight: bold;">
            <td style="width: 5%;"></td>
            <td style="width: 50%;">Saldo Kas Akhir</td>
            <td colspan="2"></td>
            <td style="width: 15%;" class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldoawal + $kenaikan, 2)) . '</td>
        </tr>';



$html.= '</tbody></table>';

$pdf->AddPage('P', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Laporan_Arus_Kas_' . str_replace('-', '', $tglawal) . '_' . str_replace('-', '', $tglakhir) . '.pdf', 'I');
?>