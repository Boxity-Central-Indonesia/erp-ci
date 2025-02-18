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
                        $FiturID = 29; //FiturID di tabel serverfitur
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
                    <div class="dropdown action-btn" hidden>
                        <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="la la-download"></i> Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <span class="dropdown-item">Export With</span>
                            <div class="dropdown-divider"></div>
                            <a href="" class="dropdown-item">
                                <i class="la la-file-pdf"></i> PDF</a>
                            <a href="" class="dropdown-item">
                                <i class="la la-file-excel"></i> Excel (XLSX)</a>
                        </div>
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
							<table id="table-prosesprod" class="table mb-0 table-borderless">
								<thead>
									<tr>
					                    <td colspan="2" hidden>
					                        <div class="form-group">
					                            <label class="form-control-label">Status</label>
					                            <select class="form-control" id="combo-status">
					                                <option value="">Semua Status</option>
					                                <option value="1">Aktif</option>
					                                <option value="0">Non-Aktif</option>
					                            </select>
					                        </div>
					                    </td>
					                    <td colspan="3">
					                        <div class="form-group">
					                            <label class="form-control-label">Pencarian Data</label>
					                            <input id="inp-search" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
					                        </div>
					                    </td>
										<td colspan="2">
											<div class="form-group">
												<label for="form-control-label">Total HPP Barang (WIP)</label>
												<input id="totalhpp" class="form-control" style="padding: 5px; box-sizing: border-box; text-align: right;" type="text" value="<?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalhpp, 2)) ?>" readonly>
											</div>
										</td>
					                </tr>
					                <tr class="userDatatable-header">
					                    <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Kode Produksi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">No SPK </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Tanggal SPK </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Estimasi Selesai </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Nama Customer </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Status Produksi </span></th>
					                    <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Created By </span></th>
					                    <th style="display: table-cell; width:4%;">#</th>
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
