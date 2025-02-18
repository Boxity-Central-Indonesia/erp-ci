<?php
$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Pergerakan Stok');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 25, 10, true);

$date = date("d-m-Y");
setlocale(LC_ALL, 'IND');
$namagudang = isset($gudang) ? $gudang['NamaGudang'] : 'Semua Gudang';
$judul = isset($gudang) ? str_replace(' ', '_', $gudang['NamaGudang']) : 'Semua_Gudang';

$html='
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Pergerakan Stok</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Pergerakan Stok</span><br>
    <span class="text-center" style="font-size:11pt; font-weight:none !important;">' . strftime('%d %B %Y', strtotime($date)) . '</span>
</div>
<br>

<table width="100%" class="table table-borderless" style="font-size: 12pt; padding: 1px">
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Kode Barang</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $barang['KodeBarang'] . '</td>
        <td class="text-left" style="line-height: 20px; width:15%">Jenis Barang</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $barang['NamaJenisBarang'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Nama Barang</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $barang['NamaBarang'] . '</td>
        <td class="text-left" style="line-height: 20px; width:15%">Satuan Barang</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $barang['SatuanBarang'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Gudang</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $namagudang . '</td>
        <td colspan="2"></td>
    </tr>
</table>

<table width="100%"  border="1" style="font-size: 12pt; padding: 4px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:50%"><b>Transaksi</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Masuk</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Keluar</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Saldo</b></td>
    </tr>';


    $no = 1;
    $saldo = 0;
    foreach ($model as $row) {
        if ($row['JenisTransaksi'] == 'BARANG DATANG') {
            $transaksi = 'Transaksi Pembelian ' . $row['NoRefTrSistem'];
        } elseif ($row['JenisTransaksi'] == 'BARANG KELUAR') {
            $transaksi = 'Transaksi Penjualan ' . $row['NoRefTrSistem'];
        } elseif ($row['JenisTransaksi'] == 'MUTASI') {
            $transaksi = 'Mutasi ' . $row['NamaGudangAsal'] . ' ke ' .$row['NamaGudangTujuan'];
        } elseif ($row['JenisTransaksi'] == 'PRODUKSI') {
            $transaksi = 'Produksi Barang ' . $row['NoTrans'];
        } else {
            $transaksi = 'Penyesuaian Stok ' . $row['NoTrans'];
        }
        $saldo += $row['Masuk'] - $row['Keluar'];
        
        $html .= '<tr nobr="true">';
        $html .= '<td class="text-center">' . $no . '</td>';
        $html .= '<td class="text-left">' . $transaksi . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Masuk'], 2)) . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Keluar'], 2)) . '</td>';
        $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldo, 2)) . '</td>';
        $html .= '</tr>';

        $no++;
    }
    if (!$model) {
        $html .= '<tr><td class="text-center" colspan="5"><strong>Tidak Ada Data</strong></td></tr>';
    }
    


$html.= '</table>';

$pdf->AddPage('P', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Riwayat_Pergerakan_Stok_' . str_replace(' ', '_', $barang['NamaBarang']) . '_' . $judul . '_' . $date . '.pdf', 'I');
?>