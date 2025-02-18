<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
    .card .uang {
	    display: flex;
	    flex-direction: column;
	    justify-content: space-between;
	    align-items: center;
	    border-radius: 10px 10px 0 0;
	    text-transform: capitalize;
	    padding-top: 0;
	    padding-bottom: 0;
	    min-height: 60px;
	}
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
                        $FiturID = 47; //FiturID di tabel serverfitur
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
                    	<a target="_blank" href="<?= base_url('transaksi/transaksi_kas/cetak') ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="btn-cetak">
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
					<?php if ($canAdd == 1) { ?>
					<div class="action-btn">
						<button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add">
				            <i class="la la-plus"></i> Tambah Data
				        </button>
					</div>
					<?php } ?>
				</div>
			</div>

		</div>
		<div class="col-lg-12 mb-30">
			<div class="card-body">
                <!-- <div class="columnCard-wrapper"> -->
                    <div class="row">
                        <div class="col-lg-4 mb-20">
                            <div class="card card-default card-md bg-white ">
                                <div class="card-header uang">
                                    <h5>Total Biaya Bulan Ini</h5>
                                </div>
                                <div class="card-body">
                                    <div class="card-content">
                                        <h4 style="font-weight: bold; text-align: right;"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($thismonth, 2)) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="card card-default card-md bg-white ">
                                <div class="card-header uang">
                                    <h5>Biaya 30 Hari Terakhir</h5>
                                </div>
                                <div class="card-body">
                                    <div class="card-content">
                                        <h4 style="font-weight: bold; text-align: right;"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($last30, 2)) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="card card-default card-md bg-white ">
                                <div class="card-header uang">
                                    <h5>Biaya Belum Dibayar</h5>
                                </div>
                                <div class="card-body">
                                    <div class="card-content">
                                        <h4 style="font-weight: bold; text-align: right;" ><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($belumbayar, 2)) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- </div> -->
            </div>
			<div class="card">
				<div class="card-header color-dark fw-500">
					Daftar <?= @$title ?>
				</div>
				<div class="card-body">

					<div  class="userDatatable global-shadow border-0 bg-white w-100">
						<div class="table-responsive">
							<table id="table-transkas" class="table mb-0 table-borderless">
								<thead>
									<tr>
										<td colspan="3">
					                        <div class="form-group">
					                            <label class="form-control-label">Pencarian Data</label>
					                            <input id="inp-search" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
					                        </div>
					                    </td>
					                    <td colspan="2">
					                        <div class="form-group">
					                            <label class="form-control-label">Jenis Kas</label>
					                            <select class="form-control" id="combo-jenis">
					                                <option value="">SEMUA JENIS KAS</option>
					                                <option value="KAS MASUK">KAS MASUK</option>
					                                <option value="KAS KELUAR">KAS KELUAR</option>
					                            </select>
					                        </div>
					                    </td>
					                    <td colspan="2">
					                        <div class="form-group">
					                            <label class="form-control-label">Status</label>
					                            <select class="form-control" id="combo-status">
					                                <option value="">Semua Status</option>
					                                <option value="PAID">PAID</option>
					                                <option value="PENDING">PENDING</option>
					                                <option value="OVERDUE">OVERDUE</option>
					                            </select>
					                        </div>
					                    </td>
					                </tr>
					                <tr class="userDatatable-header">
					                	<th style="display: table-cell; width:3%"></th>
					                    <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Kode Transaksi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">No Referensi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Tgl Transaksi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Tgl Jatuh Tempo </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Jenis Transaksi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Nominal </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Status </span></th>
					                    <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Created By </span></th>
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

<div class="modal fade ui-dialog" id="ModalTambah" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Kas</h4>
            </div>
            <form action="<?= base_url('transaksi/transaksi_kas/simpan') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group" hidden>
                                <label for="exampleInputFile">No Referensi</label>
                                <input type="text" class="form-control" id="NoRef_Manual" name="NoRef_Manual">
                                <input type="hidden" class="form-control" id="NoTransKas" name="NoTransKas">
                                <input type="hidden" class="form-control" id="Manual_Lama" name="">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal</label>
                                <input type="datetime-local" class="form-control" id="TanggalTransaksi" name="TanggalTransaksi" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Jatuh Tempo</label>
                                <input type="datetime-local" class="form-control" id="TanggalJatuhTempo" name="TanggalJatuhTempo" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Transaksi Kas</label>
                                <select class="form-control form-select" name="JenisTransaksiKas" id="JenisTransaksiKas" required>
                                    <option value="">Pilih Jenis Transaksi</option>
                                    <option value="KAS MASUK">KAS MASUK</option>
                                    <option value="KAS KELUAR">KAS KELUAR</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Uraian</label>
                                <textarea class="form-control" rows="3" id="Uraian" name="Uraian" required></textarea>
                            </div>
                            <div class="form-group">
                            	<label for="exampleInputFile">Nominal</label>
                            	<input type="text" class="form-control" name="TotalTransaksi" id="TotalTransaksi" required>
                            </div>
                            <div class="form-group">
                            	<label for="exampleInputFile">Status</label>
                            	<select class="form-control form-select" name="Status" id="Status" required disabled>
                            		<option value="">Pilih Status Pembayaran</option>
                            		<option value="PAID">PAID</option>
                            		<option value="PENDING">PENDING</option>
                            		<option value="OVERDUE">OVERDUE</option>
                            	</select>
                            	<input type="hidden" class="form-control" name="StatusEdit" id="StatusEdit">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnsave" class="btn btn-primary waves-effect">Simpan</button>
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>