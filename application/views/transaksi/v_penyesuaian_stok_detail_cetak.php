<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(true);
$pdf->CustomHeaderText = $src_url;
$pdf->line_header = 205;
$pdf->setPrintFooter(false);
$pdf->SetTitle('Detail Penyesuaian Stok');
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
  <title>Detail Penyesuaian Stok</title>
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
    <span class="text-center" style="font-size:15pt; font-weight:bold;">Penyesuaian Stok</span>
</div>
<br>

<table width="100%" class="table table-borderless" style="font-size: 12pt; padding: 1px">
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">No Transaksi</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['NoTrans'] . '</td>
        <td class="text-left" style="line-height: 20px; width:15%">Tanggal</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalTransaksi'])) . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">No Referensi</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['NoRefTrManual'] . '</td>
        <td class="text-left" style="line-height: 20px; width:15%">Gudang</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['NamaGudangAsal'] . '</td>
    </tr>
    <tr>
        <td class="text-left" style="line-height: 20px; width:15%">Keterangan</td>
        <td class="text-left" style="line-height: 20px; width:35%">&nbsp;:&nbsp;' . $dtinduk['Deskripsi'] . '</td>
    </tr>
</table>
<br><br>

<table width="100%"  border="1" style="font-size: 12pt; padding: 1px">
    <tr>
        <td class="text-center" style="line-height: 20px; width:5%"><b>No</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Kode Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:25%"><b>Nama Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:20%"><b>Satuan Barang</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Stok Sistem</b></td>
        <td class="text-center" style="line-height: 20px; width:15%"><b>Stok Fisik</b></td>
    </tr>';


    $no = 1;
    $this->load->model('M_Lokasi', 'lokasi');
    foreach ($model as $row) {
        $GudangAsal = $row['GudangAsal'];
        $KodeBarang = $row['KodeBarang'];
        $stok = $this->lokasi->get_stok_per_gudang($GudangAsal, $KodeBarang);
        $row['Stok'] = $stok['stok'];
        $html .= '<tr nobr="true">';
        $html .= '<td class="text-center">' . $no . '</td>';
        $html .= '<td class="text-left">' . $row['KodeBarang'] . '</td>';
        $html .= '<td class="text-left">' . $row['NamaBarang'] . '</td>';
        $html .= '<td class="text-left">' . $row['SatuanBarang'] . '</td>';
        $html .= '<td class="text-center">' . $row['StokSistemPenyesuaian'] . '</td>';
        $html .= '<td class="text-center">' . $row['StokFisikPenyesuaian'] . '</td>';
        $html .= '</tr>';

        $no++;
    }
    if (!$model) {
        $html .= '<tr><td class="text-center" colspan="5"><strong>Tidak Ada Data</strong></td></tr>';
    }
    


$html.= '
</table>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Penyesuaian_stok_' . $NoTrans . '.pdf', 'I');
?>