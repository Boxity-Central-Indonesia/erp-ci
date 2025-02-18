<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
</style>

<?php
function draw_table($judul, $space = '&nbsp;')
{
	$saldoanak = $judul['SaldoAnak'] + $judul['SaldoAnakPenyesuaian'];

    $data = '<tr>';
    $data .= '<td>' . $judul['KodeAkun'] . '</td>';
    $data .= '<td>' . ($space != '' ? $space . '- '   : $space . '* ') . $judul['NamaAkun'] . '</td>';
    $data .= '<td class="text-right">Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldoanak, 2)) . '</td>';
    $data .= '</tr>';
    if (isset($judul['anak'])) {
        foreach ($judul['anak'] as $key => $value) {
            $data .= draw_table($value, ($space . '&ensp;&emsp;'));
        }
    }
    return $data;
}
?>

<div class="social-dash-wrap">
	<div class="row">
		<div class="col-lg-12">

			<div class="breadcrumb-main">
				<h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
				<div class="breadcrumb-action justify-content-center flex-wrap">
					<?php
                        $FiturID = 33; //FiturID di tabel serverfitur
                        $canPrint = 0;
                        $print = [];
                        foreach ($this->session->userdata('fiturprint') as $key => $value) {
                            $print[$key] = $value;
                            if ($key == $FiturID && $value == 1) {
                                $canPrint = 1;
                            }
                        }
                        $canAdd = 0;
						$add = [];
						foreach ($this->session->userdata('fituradd') as $key => $value) {
							$add[$key] = $value;
							if ($key == $FiturID && $value == 1) {
								$canAdd = 1;
							}
						}
						$canEdit = 0;
						$edit = [];
						foreach ($this->session->userdata('fituredit') as $key => $value) {
							$edit[$key] = $value;
							if ($key == $FiturID && $value == 1) {
								$canEdit = 1;
							}
						}
						$canDelete = 0;
						$delete = [];
						foreach ($this->session->userdata('fiturdelete') as $key => $value) {
							$delete[$key] = $value;
							if ($key == $FiturID && $value == 1) {
								$canDelete = 1;
							}
						}
                    ?>
                    <?php if ($canPrint == 1) { ?>
                    <div class="dropdown action-btn">
                    	<a target="_blank" href="<?= base_url('laporan/laba_rugi?print=cetak&lvl=' . $level . '&bulan=' . $bulan) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="btn-cetak">
                            <i class="la la-download"></i> Cetak
                        </a>

                        <!-- <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="la la-download"></i> Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <span class="dropdown-item">Export With</span>
                            <div class="dropdown-divider"></div>
                            <a href="" class="dropdown-item">
                                <i class="la la-file-pdf"></i> PDF</a>
                            <a href="" class="dropdown-item">
                                <i class="la la-file-excel"></i> Excel (XLSX)</a>
                        </div> -->
                    </div>
                    <?php } ?>
				</div>
			</div>

		</div>
		<div class="col-lg-12 mb-30">
			<div class="card">
				<div class="card-header color-dark fw-500">
					Daftar <?= @$title ?>
					<div class="card-extra">
						Tahun Buku : <?= $kodetahun ?>
					</div>
				</div>
				<div class="card-body">

					<div  class="userDatatable global-shadow border-0 bg-white w-100">
						<div class="table-responsive">
							<table id="table-neraca" class="table table-striped">
								<thead>
									<tr>
										<form method="get" action="<?= base_url('laporan/laba_rugi') ?>" id="myForm">
					                    <td colspan="2">
                                            <div class="form-group mb-0">
					                            <label class="form-control-label">Per Bulan</label>
												<input id="bln" name="bulan" placeholder="Pilih bulan" class="form-control" style="padding: 5px; box-sizing: border-box;" type="month" autocomplete="off" value="<?= $bulan ?>">
					                        </div>
                                        </td>
					                    <td>
					                    	<div class="form-group">
                                                <label class="form-control-label">Level Akun</label><br>
                                                <select class="form-control" name="lvl" onchange="this.form.submit()">
		                                            <option <?= $level == 'semua' ? 'selected' : '' ?> value="semua">Semua</option>
		                                            <option <?= $level == '0' ? 'selected' : '' ?> value="0">Level 0</option>
		                                            <option <?= $level == '1' ? 'selected' : '' ?> value="1">Level 1</option>
		                                            <option <?= $level == '2' ? 'selected' : '' ?> value="2">Level 2</option>
		                                        </select>
                                            </div>
					                    </td>
					                    </form>
					                </tr>
					                <tr class="userDatatable-header">
					                    <th style="display: table-cell; width:4%"><span class="userDatatable-title">Kode Akun </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Nama Akun </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Nominal </span></th>
					                </tr>
								</thead>
								<tbody style="font-size:14px;">
									<?php
				                    $TotalPendapatan = 0;
				                    $TotalBebanProduksi = 0;
				                    $TotalBebanDilurProduksi = 0;
				                    $TotalPembelian = 0;

				                    foreach ($data as $key => $row) :
				                        if ($row['KodeAkun'] == 4) {
				                            $TotalPendapatan += ($row['SaldoAnak'] + $row['SaldoAnakPenyesuaian']);
				                        }
				                        if (substr($row['KodeAkun'], 0, 1) == 4) {
				                            echo draw_table($row, '');
				                        }
				                    endforeach;
				                    echo '<tr>
				                            <td colspan="2"><strong>TOTAL PENDAPATAN</strong></td>
				                            <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalPendapatan, 2)) . '</strong></td>
				                            </tr>';
				                    echo  '<tr><td></td><td colspan="2"></td></tr>';
									echo '<tr>
				                            <td colspan="2"><strong>TOTAL HPP</strong></td>
				                            <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalHPP, 2)) . '</strong></td>
				                            </tr>';
									echo  '<tr><td></td><td colspan="2"></td></tr>';

				                    foreach ($data as $key => $row) :
				                        if ($row['KodeAkun'] == 5 || $row['KodeAkun'] == 6) { // || $row['KodeAkun'] == 7
				                            if ($row['KodeAkun'] == 5) {
				                                $TotalBebanProduksi += ($row['SaldoAnak'] + $row['SaldoAnakPenyesuaian']);
				                            } elseif ($row['KodeAkun'] == 6) {
				                                echo '<tr>
				                                <td colspan="2"><strong>TOTAL BEBAN PRODUKSI</strong></td>
				                                <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalBebanProduksi, 2)) . '</strong></td>
				                                </tr>';
				                                echo  '<tr><td></td><td colspan="2"></td></tr>';
				                                $TotalBebanDilurProduksi += ($row['SaldoAnak'] + $row['SaldoAnakPenyesuaian']);
				                            }
											elseif ($row['KodeAkun'] == 7) {
							                    $TotalPembelian += ($row['SaldoAnak'] + $row['SaldoAnakPenyesuaian']);
				                            }
				                        }
				                        if (substr($row['KodeAkun'], 0, 1) == 5 || substr($row['KodeAkun'], 0, 1) == 6) { // || substr($row['KodeAkun'], 0, 1) == 7
				                            echo draw_table($row, '');
				                        }
				                    endforeach;
				                    // echo '<tr hidden>
				                    // <td colspan="2"><strong>TOTAL PEMBELIAN</strong></td>
				                    // <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalPembelian, 2)) . '</strong></td>
				                    // </tr>';
									echo '<tr>
									<td colspan="2"><strong>TOTAL BEBAN DILUAR PRODUKSI</strong></td>
									<td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalBebanDilurProduksi, 2)) . '</strong></td>
									</tr>';
				                    echo  '<tr><td></td><td colspan="2"></td></tr>';

				                    $TotalBeban = $TotalBebanProduksi + $TotalBebanDilurProduksi + $TotalPembelian;
				                    echo '<tr>
				                    <td colspan="2"><strong>TOTAL BEBAN</strong></td>
				                    <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalBeban, 2)) . '</strong></td>
				                    </tr>';

				                    echo '<tr>
				                            <td colspan="2"><strong>LABA / (RUGI) </strong></td>
				                            <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalPendapatan - $TotalBeban - $TotalHPP, 2)) . '</strong></td>
				                            </tr>';
				                    ?>
								</tbody>
							</table>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>