<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Detail Transaksi Pembelian');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
// $pdf->SetFont('helvetica', '', 10);
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 25, 10, true);


$html = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Detail Transaksi Pembelian</title>
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
    <span class="text-center" style="font-size:13pt; font-weight:bold;">Transaksi Pembelian</span>
</div>
<br>

<table width="100%" class="table table-borderless" style="font-size: 10pt; padding: 1px">
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Kode Transaksi</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['IDTransBeli'] . '</td>
        <td class="text-left" style="line-height: 20px; width:15%">Kode PO</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $kodePO . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Tgl Pembelian</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalPembelian']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalPembelian'])) . '</td>
        <td class="text-left" style="line-height: 20px; width:15%">Kode Supplier</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['KodePerson'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">No Referensi</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['NoRef_Manual'] . '</td>
        <td class="text-left" style="line-height: 20px; width:15%">Nama Supplier</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['NamaUsaha'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Uraian</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['UraianPembelian'] . '</td>
        <td class="text-left" style="line-height: 20px;" colspan="2"></td>
    </tr>
</table>
<br><br>

<table width="100%"  border="1" style="font-size: 10pt; padding: 1px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Nama Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Satuan Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Harga Satuan</b></td>
        <td class="text-center" style="line-height: 20px; width:10%"><b>Quantity</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Diskon</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Total</b></td>
    </tr>';


$no = 1;
$jumlah = 0;
foreach ($model as $row) {

    $html .= '<tr nobr="true">';
    $html .= '<td class="text-center">' . $no . '</td>';
    $html .= '<td class="text-left">' . $row['NamaBarang'] . '</td>';
    $html .= '<td class="text-left">' . $row['SatuanBarang'] . '</td>';
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['HargaSatuan'], 2)) . '</td>';
    $html .= '<td class="text-center">' . $row['Qty'] . '</td>';
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Diskon'], 2)) . '</td>';
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Total'], 2)) . '</td>';
    $html .= '</tr>';

    $no++;
    $jumlah += $row['Total'];
}
if (!$model) {
    $html .= '<tr><td class="text-center" colspan="6"><strong>Tidak Ada Data</strong></td></tr>';
}



$html .= '
    <tr>
        <td class="text-right" colspan="6">Jumlah</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah, 2)) . '</td>
    </tr>
    <tr>
        <td class="text-right" colspan="6">Diskon Bawah</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($diskonbawah, 2)) . '</td>
    </tr>
    <tr>
        <td class="text-right" colspan="6">PPN 11%</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($ppn, 2)) . '</td>
    </tr>
    <tr>
        <td class="text-right" colspan="6">Total Tagihan</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['TotalTagihan'], 2)) . '</td>
    </tr>
    <tr>
        <td class="text-right" colspan="6">DP Dibayar</td>
        <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['TotalTransaksi'], 2)) . '</td>
    </tr>
</table>
<br>
<table width="100%" border-spacing: 0px; style="font-size: 10pt; padding: 1px">
    <tr>
        <th class="text-center">Dibuat Oleh</th>
        <th class="text-center">Disetujui Oleh</th>
        <th class="text-center">Diketahui Oleh</th>
    </tr>
</table>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Transaksi_pembelian_' . $dtinduk['IDTransBeli'] . '.pdf', 'I');
