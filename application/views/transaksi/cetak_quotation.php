<?php
$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->CustomFooterText = $src_url;
$pdf->SetTitle('Quotation');
$pdf->setFooterMargin(10);
$pdf->SetAuthor('Author');
$pdf->SetDisplayMode('real', 'default');
// $pdf->SetFont('helvetica', '', 10);
$pdf->SetFont('Times', '', 10);
$pdf->SetMargins(10, 10, 10, true);

function penyebut($nilai) {
	$nilai = abs($nilai);
	$huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
	$temp = "";
	if ($nilai < 12) {
		$temp = " ". $huruf[$nilai];
	} else if ($nilai <20) {
		$temp = penyebut($nilai - 10). " Belas";
	} else if ($nilai < 100) {
		$temp = penyebut($nilai/10)." Puluh". penyebut($nilai % 10);
	} else if ($nilai < 200) {
		$temp = " Seratus" . penyebut($nilai - 100);
	} else if ($nilai < 1000) {
		$temp = penyebut($nilai/100) . " Ratus" . penyebut($nilai % 100);
	} else if ($nilai < 2000) {
		$temp = " Seribu" . penyebut($nilai - 1000);
	} else if ($nilai < 1000000) {
		$temp = penyebut($nilai/1000) . " Ribu" . penyebut($nilai % 1000);
	} else if ($nilai < 1000000000) {
		$temp = penyebut($nilai/1000000) . " Juta" . penyebut($nilai % 1000000);
	} else if ($nilai < 1000000000000) {
		$temp = penyebut($nilai/1000000000) . " Miliar" . penyebut(fmod($nilai,1000000000));
	} else if ($nilai < 1000000000000000) {
		$temp = penyebut($nilai/1000000000000) . " Triliun" . penyebut(fmod($nilai,1000000000000));
	}
	return $temp;
}

function terbilang($nilai) {
	if($nilai<0) {
		$hasil = "minus ". trim(penyebut($nilai));
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
$number = $tiga.$dua.$satu;
$noSurat = $number.'/TJL/'.$Romawimonth.'/'.$year;

$html = 
'<div class="" style="text-align:center;">
	'. strtoupper($model['NamaPerusahaan']) .'
	<br>
	'. $model['AlamatPerusahaan'] .'
	<br>
	Telepon '. $model['NoTelpPerusahaan'] .' | Email : '. $model['EmailPerusahaan'] .' | Website : '. $model['WebsitePerusahaan'] .'
</div>
<br>
<hr>
<br>
<br>
<div class="" style="text-align:right;">Jakarta, '. strftime('%d %B %Y', strtotime($tanggal)) .'</div>
<br>
<br>
<br>
<table class="table table-borderless">
	<tr>
		<td style="width:15%;">Nomor Surat</td>
		<td style="width:20%;">: '. $noSurat .'</td>
		<td rowspan="10"></td>
	</tr>
	<tr>
		<td style="width:15%;">Lampiran</td>
		<td style="width:20%;">: –</td>
		<td rowspan="10"></td>
	</tr>
	<tr>
		<td style="width:15%;">Perihal</td>
		<td style="width:20%;">: Penawaran Harga</td>
		<td rowspan="10"></td>
	</tr>
</table>
<br>
<br>
Kepada Yth.
<br>
Direktur
<br>
'. $dtinduk['NamaUsaha'] .'
<br>
di
<br>
Tempat
<br>
<br>
<br>
Dengan hormat,
<br>
Sehubungan dengan rencana proyek instalansi lanskap kantor '. $dtinduk['NamaUsaha'] .'. Dengan ini kami mengajukan penawaran harga untuk pekerjaan tersebut sebagai berikut :
<br>
Adapun penawaran yang kami ajukan adalah sebesar Rp. '. str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['TotalTagihan'], 2)) .',-
<br>
Terbilang : '. terbilang($dtinduk['TotalTagihan']) .' Rupiah.
<br>
Dengan rincian sebagai berikut :
<br>
<table width="100%"  border="1" style="font-size: 10pt; padding: 4px">
    <tr>
        <td style="line-height: 20px; text-align: center; width:5%"><b>No</b></td>
        <td style="line-height: 20px; text-align: center; width:20%"><b>Nama Barang</b></td>
        <td style="line-height: 20px; text-align: center; width:15%"><b>Jenis Barang</b></td>
        <td style="line-height: 20px; text-align: center; width:15%"><b>Satuan Barang</b></td>
        <td style="line-height: 20px; text-align: center; width:10%"><b>Quantity</b></td>
        <td style="line-height: 20px; text-align: center; width:15%"><b>Harga Satuan</b></td>
        <td style="line-height: 20px; text-align: center; width:20%"><b>Total</b></td>
    </tr>';


    $no = 1;
    $jumlah = 0;
    foreach ($data as $row) {
    	$barang = isset($row['AdditionalName']) ? $row['AdditionalName'] : $row['NamaBarang'];
        
        $html .= '<tr nobr="true">';
        $html .= '<td>' . $no . '</td>';
        $html .= '<td>' . $barang . '</td>';
        $html .= '<td>' . $row['JenisBarang'] . '</td>';
        $html .= '<td>' . $row['SatuanBarang'] . '</td>';
        $html .= '<td style="text-align:center;">' . $row['Qty'] . '</td>';
        $html .= '<td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['HargaSatuan'], 2)) . '</td>';
        $html .= '<td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Total'], 2)) . '</td>';
        $html .= '</tr>';

        $no++;
        $jumlah += $row['Total'];
    }
    if (!$model) {
        $html .= '<tr><td style="text-align:center;" colspan="6"><strong>Tidak Ada Data</strong></td></tr>';
    }
    


$html.= '
    <tr>
        <td colspan="6" style="text-align:right;">Jumlah</td>
        <td style="text-align:right;">' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($jumlah, 2)) . '</td>
    </tr>'.
    // <tr>
    //     <td class="text-right" colspan="6">Diskon Bawah</td>
    //     <td class="text-right">' . number_format($diskonbawah) . '</td>
    // </tr>
    // <tr>
    //     <td class="text-right" colspan="6">PPN 11%</td>
    //     <td class="text-right">' . number_format($ppn) . '</td>
    // </tr>
    // <tr>
    //     <td class="text-right" colspan="6">Total Tagihan</td>
    //     <td class="text-right">' . number_format($dtinduk['TotalTagihan']) . '</td>
    // </tr>
'</table>
<br>
<br>
Demikian surat penawaran ini kami sampaikan. Atas perhatian dan kerja sama Bapak kami sampaikan terima kasih.
<br>
<br>
<br>
<br>
<br>
<div class="" style="text-align:right;">
	Direktur '. $model['NamaPerusahaan'] .'
	<br>
	<br>
	<br>
	<br>
	<br>
	('. $model['NamaPimpinan'] .')
</div>';

$pdf->AddPage('P', 'A4');
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Cetak_quotation_' . $dtinduk['IDTransJual'] . '.pdf', 'I');
?>