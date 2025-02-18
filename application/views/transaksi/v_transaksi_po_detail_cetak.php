<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->line_header = 205;
$pdf->setPrintFooter(true);
$pdf->CustomFooterText = $src_url;
$pdf->SetTitle($title);
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
// $pdf->SetFont('helvetica', '', 10);
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 0, 10, true);


$html = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>' . $title . '</title>
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
<div class="container">
    <table class="table table-borderless" width="100%">
        <tr>
            <td style="width:40%;">
                <table width="100%"  border="0.2" style="font-size: 10pt; padding: 1.2px">
                    <tr>
                        <td>
                            <img src="' . base_url('assets/img/kemenkeu2.png') . '" alt="" width="auto" height="25px">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            NPWP :70.380.366.8-115.000 <br>
                            ' . strtoupper(dataPerusahaan('NamaPerusahaan')['ValueSetting']) . '
                            <span style="font-size:8pt;"> <br><br>
                                DUSUN XVII NO. RT. RW. <br>
                                KEL. SIMPANG EMPAT KEC. SIMPANG EMPAT <br>
                                ASAHAN, SUMATERA UTARA <br><br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KPP PRATAMA KISARAN
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:35%;">
                <span class="text-center" style="font-size:15pt; font-weight:bold;"><u>PURCHASE ORDER (P.O)</u></span><br>
                <span class="text-center" style="font-size:13pt; font-weight:bold;">SURAT PESANAN</span><br>
            </td>
            <td style="width:28%;">
                <table style="font-size: 10pt; padding: 1.2px;" class="table table-borderless text-left">
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td style="width:25%;">No. P.O</td>
                        <td style="width:75%;">: ' . $dtinduk['IDTransBeli'] . '</td>
                    </tr>
                    <tr>
                        <td style="width:25%;">Tanggal</td>
                        <td style="width:75%;">: ' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TglPO']))) . '</td>
                    </tr>
                    <tr>
                        <td style="width:25%;">Kepada</td>
                        <td style="width:75%;">: ' . $dtinduk['NamaUsaha'] . '</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br><br>
    
    <table width="100%"  border="1" style="font-size: 11pt; padding: 1.2px">
        <tr>
            <td class="text-center" style="line-height: 20px; width:5%"><b>NO</b></td>
            <td class="text-center" style="line-height: 20px; width:35%"><b>ITEM</b></td>
            <td class="text-center" style="line-height: 20px; width:15%"><b>QTY</b></td>
            <td class="text-center" style="line-height: 20px; width:20%"><b>PRICE</b></td>
            <td class="text-center" style="line-height: 20px; width:25%"><b>TOTAL</b></td>
        </tr>';


$no = 1;
$jumlah = 0;
foreach ($model as $row) {

    $html .= '<tr nobr="true">';
    $html .= '<td class="text-center">' . $no . '</td>';
    $html .= '<td class="text-left">' . $row['NamaBarang'] . '</td>';
    $html .= '<td class="text-center">' . $row['Qty'] . '</td>';
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['HargaSatuan'], 2)) . '</td>';
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Total'], 2)) . '</td>';
    $html .= '</tr>';

    $no++;
    $jumlah += $row['Total'];
}
if (!$model) {
    $html .= '<tr><td class="text-center" colspan="5"><strong>Tidak Ada Data</strong></td></tr>';
}



// <tr>
//     <td colspan="4" class="text-right">Jumlah</td>
//     <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah, 2)) . '</td>
// </tr>
$html .= '
    </table>
    <br><br>
    <table width="100%" class="table table-borderless" style="font-size: 11pt; padding: 1.2px">
        <tr>
            <td class="text-center" colspan="2">Issue By</td>
            <td colspan="3"></td>
            <td class="text-center" colspan="2">Supplier</td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td class="text-center" colspan="2">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
            <td colspan="3"></td>
            <td class="text-center" colspan="2">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
        </tr>
        <tr>
            <td colspan="7">' . strtoupper(dataPerusahaan('NamaPerusahaan')['ValueSetting']) . '</td>
        </tr>
    </table>
</div>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Transaksi_po_' . $dtinduk['IDTransBeli'] . '.pdf', 'I');
