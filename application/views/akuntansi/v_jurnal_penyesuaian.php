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
                        $FiturID = 59; //FiturID di tabel serverfitur
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
						<button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add" hidden>
				            <i class="la la-plus"></i> Tambah Data
				        </button>
						<a href="<?= base_url('akuntansi/jurnal_penyesuaian/tambah') ?>" type="button" class="btn btn-primary btn-sm btn-add">
				            <i class="la la-plus"></i> Tambah Data
				        </a>
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
							<table id="table-jrnpenyesuaian" class="table mb-0 table-borderless">
								<thead>
									<tr>
					                    <td colspan="2">
					                        <div class="form-group">
                                                <label class="form-control-label">Tahun Anggaran</label><br>
                                                <select class="form-control form-select select2" name="" id="kode-tahun">
                                                    <option value="" selected>Pilih Tahun Anggaran</option>
                                                    <?php if($tahunanggaran){
                                                        foreach ($tahunanggaran as $key) {
															$selected = $key['KodeTahun'] == $tahunaktif ? 'selected' : '';
                                                            echo '<option value="'.$key['KodeTahun'].'" '.$selected.'>'.$key['KodeTahun'].'</option>';
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
					                    </td>
					                    <td colspan="3">
					                        <div class="form-group">
					                            <label class="form-control-label">Pencarian Data</label>
					                            <input id="inp-search" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
					                        </div>
					                    </td>
					                </tr>
					                <tr class="userDatatable-header">
					                    <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Kode Transaksi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">No Referensi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Tahun Anggaran </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Tanggal Transaksi </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Uraian </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Nominal Transaksi </span></th>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data Jurnal Penyesuaian</h4>
            </div>
            <form action="<?= base_url('akuntansi/jurnal_penyesuaian/simpan') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">No Referensi</label>
                                <input type="text" class="form-control" id="NoRefTrans" name="NoRefTrans" required>
                                <input type="hidden" class="form-control" id="IDTransJurnal" name="IDTransJurnal">
                                <input type="hidden" class="form-control" id="Manual_Lama" name="">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal</label>
                                <input type="datetime-local" class="form-control" id="TglTransJurnal" name="TglTransJurnal" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Uraian</label>
                                <textarea class="form-control" rows="3" id="NarasiJurnal" name="NarasiJurnal" required></textarea>
                            </div>
                            <div class="form-group">
                            	<label for="exampleInputFile">Nominal</label>
                            	<input type="text" class="form-control" name="NominalTransaksi" id="NominalTransaksi" required>
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