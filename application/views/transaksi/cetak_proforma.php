<?php
$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->CustomFooterText = $src_url;
$pdf->SetTitle('Proforma Invoice');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
// $pdf->SetFont('helvetica', '', 10);
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 10, 10, true);

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
        $hasil = "minus " . trim(penyebut($nilai));
    } else {
        $hasil = trim(penyebut($nilai));
    }
    return $hasil;
}

setlocale(LC_ALL, 'IND');
$tanggal = $dtinduk['TglSlipOrder'] ? $dtinduk['TglSlipOrder'] : $dtinduk['TanggalPenjualan'];
$month = date('m', strtotime($tanggal));
$year = date('Y', strtotime($tanggal));
$romawi = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
$Romawimonth = $romawi[(int)$month];
$kode = str_split($dtinduk['IDTransJual']);
$number = "";
foreach ($kode as $key => $value) {
    if ($key == 15) {
        $tiga = $value;
    }
    if ($key == 16) {
        $dua = $value;
    }
    if ($key == 17) {
        $satu = $value;
    }
}
$number = $tiga . $dua . $satu;
$noSurat = $number . '/TJL/' . $Romawimonth . '/' . $year;

$html =
    '<h1>' . strtoupper($model['NamaPerusahaan']) . '</h1>
<br>
<br>
<br>
<br>
<table class="table table-borderless">
    <tr>
        <td style="width:6%;">Alamat</td>
        <td style="width:34%;">: ' . $model['AlamatPerusahaan'] . '</td>
        <td style="width:20%;"></td>
        <td style="width:16%; font-weight:bold;">FAKTUR #</td>
        <td style="width:19%;">&nbsp;&nbsp;: ' . $dtinduk['IDTransJual'] . '</td>
        <td style="width:10%;"></td>
    </tr>
    <tr>
        <td style="width:6%;">Telp</td>
        <td style="width:34%;">: ' . $model['NoTelpPerusahaan'] . '</td>
        <td style="width:20%;"></td>
        <td style="width:16%; font-weight:bold;">TANGGAL</td>
        <td style="width:19%;">&nbsp;&nbsp;: ' . shortdate_indo(date('Y-m-d', strtotime($tanggal))) . '</td>
        <td style="width:10%;"></td>
    </tr>
    <tr>
        <td style="width:6%;">Email</td>
        <td style="width:34%;">: ' . $model['EmailPerusahaan'] . '</td>
        <td style="width:50%;"></td>
        <td style="width:10%;"></td>
    </tr>
</table>
<br>
<br>
--------------------------------------------------------------------------------<span style="font-size:15px;">&nbsp;FAKTUR&nbsp;</span>-------------------------------------------------------------
<br>
<br>
PELANGGAN
<br>
<br>
<table class="table table-borderless" width="100%">
    <tr>
        <td style="width:50%;">
            <table style="font-size: 10pt; padding: 2px; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95; border: 1; float: left;" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="width:36%;">NAMA</td>
                    <td style="width:64%;">: ' . $dtinduk['NamaUsaha'] . '</td>
                </tr>
                <tr>
                    <td style="width:36%;">ALAMAT</td>
                    <td style="width:64%;">: ' . $dtinduk['AlamatPerson'] . '</td>
                </tr>
                <tr>
                    <td style="width:36%;">TELP</td>
                    <td style="width:64%;">: ' . $dtinduk['NoHP'] . '</td>
                </tr>
                <tr>
                    <td style="width:36%;">FAX</td>
                    <td style="width:64%;">: </td>
                </tr>
            </table>
        </td>
        <td style="width:5%;"></td>
        <td style="width:45%;">
            <table style="font-size: 10pt; padding: 2px; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95; border: 1; float: left;" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="width:36%;"></td>
                    <td style="width:64%;"></td>
                </tr>
                <tr>
                    <td style="width:36%;">JATUH TEMPO</td>
                    <td style="width:64%;">: ' . $jatuhtempo . '</td>
                </tr>
                <tr>
                    <td style="width:36%;"></td>
                    <td style="width:64%;"></td>
                </tr>
                <tr>
                    <td style="width:36%;"></td>
                    <td style="width:64%;"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<br>
<table width="100%"  border="1" style="font-size: 10pt; padding: 4px">
    <tr>
        <td style="line-height: 20px; text-align: center; width:5%; font-weight: bold;"><b>No</b></td>
        <td style="line-height: 20px; text-align: center; width:20%; font-weight: bold;"><b>Nama Barang</b></td>
        <td style="line-height: 20px; text-align: center; width:10%; font-weight: bold;"><b>Quantity</b></td>
        <td style="line-height: 20px; text-align: center; width:15%; font-weight: bold;"><b>Harga Satuan</b></td>
        <td style="line-height: 20px; text-align: center; width:15%; font-weight: bold;"><b>Diskon</b></td>
        <td style="line-height: 20px; text-align: center; width:15%; font-weight: bold;"><b>Pajak</b></td>
        <td style="line-height: 20px; text-align: center; width:20%; font-weight: bold;"><b>Jumlah</b></td>
    </tr>';


$no = 1;
$jumlah = 0;
foreach ($data as $row) {
    $barang = isset($row['AdditionalName']) ? $row['AdditionalName'] : $row['NamaBarang'];

    $html .= '<tr nobr="true">';
    $html .= '<td>' . $no . '</td>';
    $html .= '<td>' . $barang . '</td>';
    $html .= '<td style="text-align:center;">' . $row['Qty'] . '</td>';
    $html .= '<td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['HargaSatuan'], 2)) . '</td>';
    $html .= '<td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Diskon'], 2)) . '</td>';
    $html .= '<td style="text-align:center;"> X </td>';
    $html .= '<td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Total'], 2)) . '</td>';
    $html .= '</tr>';

    $no++;
    $jumlah += $row['Total'];
}
if (!$model) {
    $html .= '<tr><td style="text-align:center;" colspan="6"><strong>Tidak Ada Data</strong></td></tr>';
}



$html .= '
    <tr>
        <td colspan="6" style="text-align:right;">Subtotal</td>
        <td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah, 2)) . '</td>
    </tr>
    <tr>
        <td style="text-align:right;" colspan="6">Diskon Bawah</td>
        <td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($diskonbawah, 2)) . '</td>
    </tr>
    <tr>
        <td style="text-align:right;" colspan="6">PPN 11%</td>
        <td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($ppn, 2)) . '</td>
    </tr>
    <tr>
        <td style="text-align:right; font-weight:bold;" colspan="6">TOTAL</td>
        <td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totaltagihan, 2)) . '</td>
    </tr>
    <tr>
        <td style="text-align:right;" colspan="6">Bayaran Diterima</td>
        <td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totaltransaksi, 2)) . '</td>
    </tr>
    <tr>
        <td style="text-align:right;" colspan="6">Sisa Tagihan</td>
        <td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totaltagihan - $totaltransaksi, 2)) . '</td>
    </tr>
</table>
<br>
<br>
<table style="font-size: 10pt; padding: 2px; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95; border: 1; float: left;" border="0" cellpadding="0" cellspacing="0" width="60%">
    <tr>
        <td style="font-weight: bold;">PESAN</td>
    </tr>
    <tr>
        <td>' . $model['Pesan'] . '</td>
    </tr>
</table>
<br>
<br>
<span style="font-weight: bold;">DETAIL PEMBAYARAN</span>
<br>
<br>
<table style="font-size: 10pt; padding: 2px; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95; border: 1; float: left;" border="0" cellpadding="0" cellspacing="0" width="60%">
    <tr>
        <td style="width:36%;">NAMA BANK</td>
        <td style="width:64%;">: ' . $model['NamaBank'] . '</td>
    </tr>
    <tr>
        <td style="width:36%;">CABANG BANK</td>
        <td style="width:64%;">: ' . $model['CabangBank'] . '</td>
    </tr>
    <tr>
        <td style="width:36%;">NOMOR AKUN BANK</td>
        <td style="width:64%;">: ' . $model['NoAkunBank'] . '</td>
    </tr>
    <tr>
        <td style="width:36%;">ATAS NAMA</td>
        <td style="width:64%;">: ' . $model['AtasNamaBank'] . '</td>
    </tr>
</table>
<br>
<br>
<table style="font-size: 10pt; padding: 2px; background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95; border: 1; float: left;" border="0" cellpadding="0" cellspacing="0" width="60%">
    <tr>
        <td>TERBILANG</td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td>' . strtoupper(terbilang($totaltagihan)) . ' RUPIAH</td>
    </tr>
</table>
<br>
<div class="" style="text-align:right;">
    <br>
    <br>
    <br>
    <br>
    <span style="text-decoration:overline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $model['NamaPerusahaan'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <br>
    ' . $model['NamaPimpinan'] . '
</div>';

$pdf->AddPage('P', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Cetak_proforma_invoice_' . $dtinduk['IDTransJual'] . '.pdf', 'I');
