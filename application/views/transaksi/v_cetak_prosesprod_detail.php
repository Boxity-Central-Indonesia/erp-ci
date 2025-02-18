<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->line_header = 205;
$pdf->setPrintFooter(true);
$pdf->CustomFooterText = $src_url;
$pdf->SetTitle('Cetak Detail Proses Produksi');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
// $pdf->SetFont('helvetica', '', 10);
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, -5, 10, true);


$html='
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cetak Detail Proses Produksi</title>
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
    <div class="text-center" style="font-size:14pt; font-weight:bold;"><u>DETAIL PROSES PRODUKSI</u></div>
    <br>
    <table width="100%" class="table table-borderless" style="font-size: 11pt; padding: 1.2px">
        <tr>
            <td class="text-left" style="width:20%">No. SPK</td>
            <td class="text-left" style="width:30%">&nbsp;:&nbsp;' . $dtinduk['SPKNomor'] . '</td>
            <td class="text-left" style="width:20%">Gudang Asal</td>
            <td class="text-left" style="width:30%">&nbsp;:&nbsp;' . $dtinduk['NamaGudangAsal'] . '</td>
        </tr>
        <tr>
            <td class="text-left" style="width:20%">Tgl. SPK</td>
            <td class="text-left" style="width:30%">&nbsp;:&nbsp;' . $SPKTanggal . '</td>
            <td class="text-left" style="width:20%">Gudang Tujuan</td>
            <td class="text-left" style="width:30%">&nbsp;:&nbsp;' . $dtinduk['NamaGudangTujuan'] . '</td>
        </tr>
        <tr>
            <td class="text-left" style="width:20%">Tgl. Mulai Produksi</td>
            <td class="text-left" style="width:30%">&nbsp;:&nbsp;' . $TglMulai . '</td>
            <td class="text-left" style="width:20%">Tgl. Selesai Produksi</td>
            <td class="text-left" style="width:30%">&nbsp;:&nbsp;' . $TglSelesai . '</td>
        </tr>
    </table>
    <br>
    <div style="font-size:11px;">Daftar Item Bahan Baku</div>
    <table width="100%"  border="1" style="font-size: 11pt; padding: 1.2px">
        <tr>
            <td class="text-center" style="width:5%"><b>No</b></td>
            <td class="text-center" style="width:45%"><b>Nama Bahan</b></td>
            <td class="text-center" style="width:20%"><b>Jumlah Berat</b></td>
            <td class="text-center" style="width:15%"><b>Harga</b></td>
            <td class="text-center" style="width:15%"><b>Total</b></td>
        </tr>';
    
    
        $no = 1;
        $beratbahan = 0;
        $modalbahan = 0;
        foreach ($dtbahan as $row) {

            $html .= '<tr nobr="true">';
            $html .= '<td class="text-center">' . $no . '</td>';
            $html .= '<td class="text-left">' . $row['NamaBarang'] . '</td>';
            $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Qty'], 2)) . ' kilogram</td>';
            $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['HargaSatuan'], 2)) . '</td>';
            $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($row['Total']), 2)) . '</td>';
            $html .= '</tr>';
    
            $no++;
            $beratbahan += $row['Qty'];
            $modalbahan += $row['Total'];
        }

    $html.= '
        <tr>
            <td colspan="2" class="text-right">Total</td>
            <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($beratbahan), 2)) . ' kilogram</td>
            <td></td>
            <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($modalbahan), 2)) . '</td>
        </tr>
    </table>
    <br>
    <div style="font-size:11px;">Daftar Item Produksi</div>
    <table width="100%"  border="1" style="font-size: 11pt; padding: 1.2px">
        <tr>
            <td class="text-center" style="width:5%"><b>No</b></td>
            <td class="text-center" style="width:35%"><b>Nama Barang</b></td>
            <td class="text-center" style="width:17%"><b>Berat Kotor</b></td>
            <td class="text-center" style="width:25%"><b>Pemakaian Bahan Masak</b></td>
            <td class="text-center" style="width:18%"><b>HPP</b></td>
        </tr>';
    
    
        $no = 1;
        $beratprod = 0;
        $beratpemakaian = 0;
        $nominalprod = 0;
        foreach ($dtproduksi as $row) {

            $html .= '<tr nobr="true">';
            $html .= '<td class="text-center">' . $no . '</td>';
            $html .= '<td class="text-left">' . $row['NamaBarang'] . '</td>';
            $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Qty'], 2)) . ' kilogram</td>';
            $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['PemakaianBahanMasak'], 2)) . ' kilogram</td>';
            $html .= '<td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Total'], 2)) . '</td>';
            $html .= '</tr>';
    
            $no++;
            $beratprod += $row['Qty'];
            $beratpemakaian += $row['PemakaianBahanMasak'];
            $nominalprod += $row['Total'];
        }

    $html.= '
        <tr>
            <td colspan="2" class="text-right">Total</td>
            <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($beratprod), 2)) . ' kilogram</td>
            <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($beratpemakaian), 2)) . ' kilogram</td>
            <td class="text-right">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($nominalprod), 2)) . '</td>
        </tr>
    </table>
    <br><br>
    <table width="100%" class="table table-borderless" style="font-size: 11pt; padding: 1.2px">
        <tr>
            <td width="33%">Susut : ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($beratbahan - $beratprod), 2)) . ' kilogram</td>
            <td width="34%">Presentase Bahan : ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($beratprod / $beratbahan * 100), 2)) . '%</td>
            <td width="33%">Modal Bahan : ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($modalbahan / $beratpemakaian), 2)) . '</td>
        </tr>
    </table>
</div>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Cetak_spk_' . $dtinduk['IDTransJual'] . '.pdf', 'I');
?>