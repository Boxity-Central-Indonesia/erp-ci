<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->line_header = 205;
$pdf->setPrintFooter(true);
$pdf->CustomFooterText = $src_url;
$pdf->SetTitle('Detail Transaksi Penjualan');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 0, 10, true);

function penyebut($nilai)
{
    $nilai = abs($nilai);
    $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10) . " Belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai / 10) . " Puluh" . penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " Seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai / 100) . " Ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " Seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai / 1000) . " Ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai / 1000000) . " Juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai / 1000000000) . " Miliar" . penyebut(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai / 1000000000000) . " Triliun" . penyebut(fmod($nilai, 1000000000000));
    }
    return $temp;
}

function terbilang($nilai)
{
    if ($nilai < 0) {
        $hasil = "minus " . trim(penyebut($nilai)) . " Rupiah";
    } else {
        $hasil = trim(penyebut($nilai)) . " Rupiah";
    }
    return $hasil;
}

$tanggal = $dtinduk['TglSlipOrder'] ? $dtinduk['TglSlipOrder'] : $dtinduk['TanggalPenjualan'];

$html = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Detail Transaksi Penjualan</title>
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
            <td style="width:50%;">
                <table width="100%"  border="1" style="font-size: 10pt; padding: 1.2px">
                    <tr>
                        <td width="18%;">Kepada:</td>
                        <td width="82%;">' . $dtinduk['NamaUsaha'] . '</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            &nbsp;<br>Telp: ' . $dtinduk['NoHP'] . '<br><br>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:10%;"></td>
            <td style="width:40%;">
                <table width="100%"  border="1" style="font-size: 10pt; padding: 1.2px">
                    <tr>
                        <td class="text-center" style="font-size:13pt; font-weight:bold;">INVOICE</td>
                    </tr>
                    <tr>
                        <td class="text-center" style="font-weight:bold;">' . strtoupper(dataPerusahaan('NamaPerusahaan')['ValueSetting']) . '</td>
                    </tr>
                    <tr>
                        <td width="40%;">No. Invoice</td>
                        <td width="60%;">' . $dtinduk['IDTransJual'] . '</td>
                    </tr>
                    <tr>
                        <td width="40%;">Tgl Invoice</td>
                        <td width="60%;">' . shortdate_indo(date('Y-m-d', strtotime($tanggal))) . '</td>
                    </tr>
                    <tr>
                        <td width="40%;">No. Surat Jalan</td>
                        <td width="60%;"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br><br>
    
    <table width="100%"  border="1" style="font-size: 10pt; padding: 1.2px">
        <tr>
            <td class="text-center" style="line-height: 20px; width:20%"><b>Nama Barang</b></td>
            <td class="text-center" style="line-height: 20px; width:10%"><b>Banyak</b></td>
            <td class="text-center" style="line-height: 20px; width:10%"><b>Satuan</b></td>
            <td class="text-center" style="line-height: 20px; width:30%"><b>Keterangan</b></td>
            <td class="text-center" style="line-height: 20px; width:15%"><b>Harga Satuan</b></td>
            <td class="text-center" style="line-height: 20px; width:15%"><b>Total Harga</b></td>
        </tr>';


$no = 1;
$jumlah = 0;
foreach ($model as $row) {

    $html .= '<tr nobr="true">';
    $html .= '<td class="text-left">' . $row['NamaBarang'] . '</td>';
    $html .= '<td class="text-center">' . $row['Qty'] . '</td>';
    $html .= '<td class="text-center">' . $row['SatuanBarang'] . '</td>';
    $html .= '<td class="text-left">' . $row['Deskripsi'] . '</td>';
    $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['HargaSatuan'], 2)) . '</td>';
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
            <td colspan="4" rowspan="5">&nbsp;<br>Terbilang:<br>' . terbilang($dtinduk['TotalTagihan'] - $totaltransaksi) . '</td>
            <td>TOTAL</td>
            <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah, 2)) . '</td>
        </tr>
        <tr>
            <td>DISKON</td>
            <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($diskonbawah, 2)) . '</td>
        </tr>
        <tr>
            <td>PPN 11%</td>
            <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($ppn, 2)) . '</td>
        </tr>
        <tr>
            <td>PEMOTONGAN</td>
            <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(0, 2)) . '</td>
        </tr>
        <tr>
            <td>PEMBAYARAN</td>
            <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totaltransaksi, 2)) . '</td>
        </tr>
        <tr>
            <td>Catatan</td>
            <td colspan="3"></td>
            <td>NETTO</td>
            <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['TotalTagihan'] - $totaltransaksi, 2)) . '</td>
        </tr>
    </table>
    <br><br>
    <table width="100%" class="table table-borderless" style="font-size: 10pt; padding: 1.2px">
        <tr>
            <td class="text-center" colspan="2">Diterima Oleh</td>
            <td colspan="3"></td>
            <td class="text-center" colspan="2">Hormat Kami,</td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td class="text-center" colspan="2">(...............................)</td>
            <td colspan="3"></td>
            <td class="text-center" colspan="2">(...............................)</td>
        </tr>
    </table>
</div>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Transaksi_penjualan_' . $dtinduk['IDTransJual'] . '.pdf', 'I');
