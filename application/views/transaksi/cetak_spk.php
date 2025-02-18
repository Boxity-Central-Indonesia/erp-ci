<?php
$pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->line_header = 205;
$pdf->setPrintFooter(true);
$pdf->CustomFooterText = $src_url;
$pdf->SetTitle('Cetak SPK');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
// $pdf->SetFont('helvetica', '', 10);
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 0, 10, true);


$html='
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cetak SPK</title>
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
            <td class="text-right" width="45%">No. Dokumen</td>
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
    <span class="text-center" style="font-size:14pt; font-weight:bold;"><u>SURAT PERINTAH KERJA PRODUKSI</u><br>
    <span class="text-center" style="font-size:10pt; font-weight:none !important;">No SPK:&nbsp;&nbsp;&nbsp;' . $dtinduk['SPKNomor'] . '</span></span>
    <br><br>
    <table width="100%" class="table table-borderless" style="font-size: 10pt; padding: 1px">
        <tr>
            <td class="text-left" style="width:25%">No. Orderan</td>
            <td class="text-left" style="width:75%">&nbsp;:&nbsp;' . $dtinduk['IDTransJual'] . '</td>
        </tr>
        <tr>
            <td class="text-left" style="width:25%">Tgl. Orderan</td>
            <td class="text-left" style="width:75%">&nbsp;:&nbsp;' . shortdate_indo(date('Y-m-d', strtotime($dtinduk['TglSlipOrder']))) . '</td>
        </tr>
        <tr>
            <td class="text-left" style="width:25%">Estimasi Tanggal Selesai</td>
            <td class="text-left" style="width:75%">&nbsp;:&nbsp;' . $EstimasiSelesai . '</td>
        </tr>
        <tr>
            <td class="text-left" style="width:25%">Pemesan</td>
            <td class="text-left" style="width:75%">&nbsp;:&nbsp;' . $dtinduk['NamaUsaha'] . '</td>
        </tr>
    </table>

    <table width="100%"  border="1" style="font-size: 10pt; padding: 1px">
        <tr>
            <td class="text-center" style="width:5%"><b>NO</b></td>
            <td class="text-center" style="width:25%"><b>NAMA BARANG</b></td>
            <td class="text-center" style="width:45%"><b>SPESIFIKASI</b></td>
            <td class="text-center" style="width:25%"><b>JUMLAH BARANG</b></td>
        </tr>';
    
    
        $no = 1;
        $jumlah = 0;
        foreach ($model as $row) {
            $barang = isset($row['AdditionalName']) ? $row['AdditionalName'] : $row['NamaBarang'];
            
            $html .= '<tr nobr="true">';
            $html .= '<td class="text-center">' . $no . '</td>';
            $html .= '<td class="text-left">' . $barang . '</td>';
            $html .= '<td class="text-left">' . $row['Spesifikasi'] . '</td>';
            $html .= '<td class="text-center">' . $row['JmlProduksi'] . '</td>';
            $html .= '</tr>';
    
            $no++;
            $jumlah += $row['Total'];
        }
        if (!$model) {
            $html .= '<tr><td class="text-center" colspan="4"><strong>Tidak Ada Data</strong></td></tr>';
        }
        
    
    
    $html.= '
    </table>
    <br><br>
    <table width="100%" style="font-size: 10pt; padding: 1px" border="1">
        <tr>
            <td class="text-center">Dibuat Oleh</td>
            <td class="text-center">Disetujui Oleh</td>
            <td class="text-center">Diketahui Oleh</td>
        </tr>
        <tr>
            <td rowspan="3"></td>
            <td rowspan="3"></td>
            <td rowspan="3"></td>
        </tr>
        <tr>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td class="text-left">Nama:&nbsp;' . $dtinduk['SPKDibuatOleh'] . '</td>
            <td class="text-left">Nama:&nbsp;' . $dtinduk['SPKDisetujuiOleh'] . '</td>
            <td class="text-left">Nama:&nbsp;' . $dtinduk['SPKDiketahuiOleh'] . '</td>
        </tr>
        <tr>
            <td class="text-left">Tanggal/Jam:&nbsp;' . $SPKTanggal . '</td>
            <td class="text-left">Tanggal/Jam:&nbsp;' . $SPKDisetujuiTgl . '</td>
            <td class="text-left">Tanggal/Jam:&nbsp;' . $SPKDiketahuiTgl . '</td>
        </tr>
    </table>
</div>';

$pdf->AddPage('L', 'A5');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Cetak_spk_' . $dtinduk['IDTransJual'] . '.pdf', 'I');
?>