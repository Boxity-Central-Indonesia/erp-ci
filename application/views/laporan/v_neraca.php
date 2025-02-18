<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
</style>

<?php
function draw_table($judul, $space = '&nbsp;')
{
    if (substr($judul['KodeAkun'], 0, 1) == '1' || substr($judul['KodeAkun'], 0, 1) == '5' || substr($judul['KodeAkun'], 0, 1) == '6') {
        $saldoanak = $judul['SaldoAnak'];
		$saldoanaklalu = $judul['SaldoAnakLalu'];
    } elseif (substr($judul['KodeAkun'], 0, 1) == '2' || substr($judul['KodeAkun'], 0, 1) == '3' || substr($judul['KodeAkun'], 0, 1) == '4') {
        $saldoanak = $judul['SaldoAnak'];
		$saldoanaklalu = $judul['SaldoAnakLalu'];
    }
    $data = '<tr>';
    $data .= '<td>' . $judul['KodeAkun'] . '</td>';
    $data .= '<td>' . ($space != '' ? $space . '- '   : $space . '* ') . $judul['NamaAkun'] . '</td>';
    $data .= '<td class="text-right">Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldoanaklalu, 2)) . '</td>';
    $data .= '<td class="text-right">Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldoanak, 2)) . '</td>';
    $data .= '<td class="text-right">Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($saldoanak - $saldoanaklalu), 2)) . '</td>'; // tahun laporan - tahun lalu
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
                    	<a target="_blank" href="<?= base_url('laporan/neraca?print=cetak&lvl=' . $level . '&bulan=' . $bulan) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="btn-cetak">
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
										<form method="get" action="<?= base_url('laporan/neraca') ?>" id="myForm">
					                    <td colspan="2">
                                            <div class="form-group mb-0">
					                            <label class="form-control-label">Per Bulan</label>
												<input id="bln" name="bulan" placeholder="Pilih bulan" class="form-control" style="padding: 5px; box-sizing: border-box;" type="month" autocomplete="off" value="<?= $bulan ?>">
					                        </div>
                                        </td>
					                    <td colspan="2">
					                    	<div class="form-group">
                                                <label class="form-control-label">Level Akun</label><br>
                                                <select class="form-control" id="lvl" name="lvl" onchange="this.form.submit()">
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
					                    <th style="display: table-cell;"><span class="userDatatable-title">Tahun Lalu </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Tahun Laporan </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Selisih </span></th>
					                </tr>
								</thead>
								<tbody style="font-size:14px;">
									<?php
				                    $TotalPasiva = 0;
				                    foreach ($data as $key => $row) :
				                        if ($row['KodeAkun'] == 1) {
				                        } else if ($row['KodeAkun'] == 2) {
				                            echo '<tr>
				                            <td colspan="4"><strong>TOTAL AKTIVA</strong></td>
				                            <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($aktiva - $aktivalalu), 2)) . '</strong></td>
				                            </tr>';
				                            $TotalPasiva += ($row['SaldoAnak'] - $row['SaldoAnakLalu']);
				                        } else if ($row['KodeAkun'] == 3) {
				                            $TotalPasiva += ($row['SaldoAnak'] - $row['SaldoAnakLalu']);
				                        }
				                        echo draw_table($row, '');
				                    endforeach;
				                    echo '<tr>
				                            <td colspan="4"><strong>TOTAL KEWAJIBAN + EKUITAS</strong></td>
				                            <td class="text-right"><strong>Rp. ' . str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalPasiva, 2)) . '</strong></td>
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