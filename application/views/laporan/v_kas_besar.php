<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
	<div class="row">
		<div class="col-lg-12">

			<div class="breadcrumb-main">
				<h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
				<div class="breadcrumb-action justify-content-center flex-wrap">
					<?php
                        $FiturID = 39; //FiturID di tabel serverfitur
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
                    	<a target="_blank" href="<?= base_url('laporan/kas_besar/cetak/'.$kodeakun.'/'.date('Y-m-d', strtotime($tglawal)).'/'.date('Y-m-d', strtotime($tglakhir))) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="btn-cetak">
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
				</div>
				<div class="card-body">

					<div  class="userDatatable global-shadow border-0 bg-white w-100">
						<div class="table-responsive">
							<input type="hidden" id="tgl_awal">
							<input type="hidden" id="tgl_akhir">
							<table id="table-kasbesar" class="table mb-0 table-borderless">
								<thead>
									<tr>
					                    <td colspan="3">
                                            <div class="form-group mb-0">
					                            <label class="form-control-label">Tanggal Transaksi</label>
					                            <div class="input-container icon-left position-relative">
													<span class="input-icon icon-left">
														<span data-feather="calendar"></span>
													</span>
													<input type="text" class="form-control" id="tgl-transaksi" name="tgl">
													<span class="input-icon icon-right">
														<span data-feather="chevron-down"></span>
													</span>
												</div>
					                        </div>
                                        </td>
					                    <td colspan="2">
					                    	<div class="form-group">
                                                <label class="form-control-label">Kode Akun</label><br>
                                                <select class="form-control form-select select2" id="combo-akun">
					                                <?php
					                                echo '<option value="">Pilih Kode Akun</option>';
					                                foreach ($dtakun as $key) {
					                                	if ($kodeakun != null && $kodeakun == $key['KodeAkun']) {
					                                		echo '<option value="' . $key['KodeAkun'] . '" selected>' . $key['KodeAkun'] . ' | ' . $key['NamaAkun'] . '</option>';
					                                	} else {
					                                    	echo '<option value="' . $key['KodeAkun'] . '">' . $key['KodeAkun'] . ' | ' . $key['NamaAkun'] . '</option>';
					                                	}
					                                }
					                                ?>
					                            </select>
                                            </div>
					                    </td>
					                </tr>
					                <tr class="userDatatable-header">
					                    <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Tanggal Transaksi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">No Transaksi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">No Referensi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Uraian </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Debet </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Kredit </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Saldo </span></th>
					                    <th style="display: table-cell; width:1%;">#</th>
					                </tr>
					                <tr style="font-size:14px; font-weight:bold;">
					                	<td colspan="7">Saldo sebelum tanggal: <span id="tglawal"><?= $tglawal ?></span></td>
					                	<td style="text-align: right;">
					                		<span id="saldo_awal"></span>
					                	</td>
					                	<td></td>
					                </tr>
								</thead>
								<tbody style="font-size:14px;">
								</tbody>
								<tr style="font-size:14px; font-weight:bold;">
									<td colspan="7">Saldo akhir sampai tanggal: <span id="tglakhir"><?= $tglakhir ?></span></td>
									<td style="text-align: right;">
										<span id="saldo_akhir"></span>
									</td>
									<td></td>
								</tr>
							</table>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>