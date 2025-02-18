<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
	<div class="row">
		<div class="col-lg-12">

			<div class="breadcrumb-main">
				<h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
				<div class="breadcrumb-action justify-content-center flex-wrap">
					<div class="action-btn">

						<div class="form-group mb-0">
							<div class="input-container icon-left position-relative">
								<span class="input-icon icon-left">
									<span data-feather="calendar"></span>
								</span>
								<input type="text" class="form-control" id="tgl-transaksi">
								<span class="input-icon icon-right">
									<span data-feather="chevron-down"></span>
								</span>
							</div>
						</div>
					</div>
					<?php
                        $FiturID = 43; //FiturID di tabel serverfitur
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
                    	<a target="_blank" href="<?= base_url('laporan/hutang/cetak/') . base64_encode(date('Y-m-d', strtotime($tglawal)) . ' - ' . date('Y-m-d', strtotime($tglakhir))) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="btn-cetak">
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
							<table id="table-hutang" class="table mb-0 table-borderless">
								<thead>
									<tr>
					                    <td colspan="3">
					                        <div class="form-group">
					                            <label class="form-control-label">Pencarian Data</label>
					                            <input id="inp-search" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text" onkeyup="filtrasi()">
					                        </div>
					                    </td>
										<td colspan="4"></td>
										<td colspan="2">
											<div class="form-group">
												<label class="form-control-label">Total Sisa Tagihan</label>
												<input type="text" name="" id="total-sisa" class="form-control" style="padding: 5px; box-sizing: border-box; text-align: right;" readonly>
											</div>
										</td>
					                </tr>
					                <tr class="userDatatable-header">
					                    <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Kode Transaksi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">No Referensi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Tanggal Pembelian </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Nama Supplier </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Total Tagihan </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Total Dibayar </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Sisa Tagihan </span></th>
					                    <th style="display: table-cell; width:5%;">#</th>
					                </tr>
								</thead>
								<tbody style="font-size:14px;">
								</tbody>
							</table>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
