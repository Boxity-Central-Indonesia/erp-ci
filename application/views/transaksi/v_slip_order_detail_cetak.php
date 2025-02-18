<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->CustomFooterText = $src_url;
$pdf->SetTitle('Detail Transaksi Slip Order');
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
  <title>Detail Transaksi Slip Order</title>
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
    <table class="table" width="25%" style="font-size: 8pt; padding: 1px; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.5; border: 0.5; float: left;" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="45%" class="text-right">No. Dokumen</td>
            <td width="55%">: ....................</td>
        </tr>
        <tr>
            <td class="text-right">No. Revisi</td>
            <td>: ....................</td>
        </tr>
        <tr>
            <td class="text-right">Tgl. Revisi</td>
            <td>: ....................</td>
        </tr>
    </table>
    <div class="text-center" style="font-size: 16pt;"><strong><u>SLIP ORDER</u></strong></div>
    <table width="100%" class="table table-borderless" style="font-size: 10pt; padding: 1px">
        <tr>
            <td class="text-left" style="width:25%">Nomor Orderan</td>
            <td class="text-left" style="width:25%">&nbsp;:&nbsp;' . $dtinduk['NoSlipOrder'] . '</td>
        </tr>
        <tr>
            <td class="text-left" style="width:25%">Tanggal</td>
            <td class="text-left" style="width:25%">&nbsp;:&nbsp;' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TglSlipOrder']))) . '</td>
        </tr>
        <tr>
            <td class="text-left" style="width:25%">Pemesan</td>
            <td class="text-left" style="width:25%">&nbsp;:&nbsp;' . $dtinduk['NamaUsaha'] . '</td>
        </tr>
    </table>
    
    <table class="table" width="100%"  border="1" style="font-size: 11pt; padding: 1px">
        <tr>
            <td class="text-center" style="width:5%"><b>NO.</b></td>
            <td class="text-center" style="width:25%"><b>NAMA BARANG</b></td>
            <td class="text-center" style="width:50%"><b>SPESIFIKASI</b></td>
            <td class="text-center" style="width:20%"><b>JUMLAH BARANG</b></td>
        </tr>';


$no = 1;
$jumlah = 0;
foreach ($model as $row) {
    $barang = isset($row['AdditionalName']) ? $row['AdditionalName'] : $row['NamaBarang'];

    $html .= '<tr nobr="true">';
    $html .= '<td class="text-center">' . $no . '</td>';
    $html .= '<td class="text-left">' . $barang . '</td>';
    $html .= '<td class="text-left">' . $row['ProdUkuran'] . ' ' . $row['ProdJmlDaun'] . ' daun ' . $row['Deskripsi'] . '</td>';
    $html .= '<td class="text-center">' . $row['Qty'] . '</td>';
    $html .= '</tr>';

    $no++;
    $jumlah += $row['Total'];
}
if (!$model) {
    $html .= '<tr><td class="text-center" colspan="4"><strong>Tidak Ada Data</strong></td></tr>';
}



$html .= '
    </table>
    <br><br>
    <table width="100%" class="table table-borderless" style="font-size: 11pt; padding: 2px">
        <tr>
            <td class="text-center" colspan="2">Dibuat Oleh</td>
            <td colspan="3"></td>
            <td class="text-center" colspan="2">Penerima Pesanan</td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td class="text-center" colspan="2"><strong>' . $dtinduk['SODibuatOleh'] . '</strong></td>
            <td colspan="3"></td>
            <td class="text-center" colspan="2"><strong>' . $dtinduk['NamaUsaha'] . '</strong></td>
        </tr>
    </table>
</div>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Transaksi_slip_order_' . $dtinduk['IDTransJual'] . '.pdf', 'I');
