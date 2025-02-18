<div class="social-dash-wrap">
	<div class="row">
		<div class="col-lg-12">

			<div class="breadcrumb-main">
				<h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
				<div class="breadcrumb-action justify-content-center flex-wrap">
					<div class="action-btn" hidden>

						<div class="form-group mb-0">
							<div class="input-container icon-left position-relative">
								<span class="input-icon icon-left">
									<span data-feather="calendar"></span>
								</span>
								<input type="text" class="form-control form-control-default date-ranger" name="date-ranger" placeholder="Oct 30, 2019 - Nov 30, 2019">
								<span class="input-icon icon-right">
									<span data-feather="chevron-down"></span>
								</span>
							</div>
						</div>
					</div>
					<?php
                        $FiturID = 38; //FiturID di tabel serverfitur
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
                    	<a target="_blank" href="<?= base_url('laporan/nilai_persediaan/cetak/' . base64_encode($gudang)) ?>" id="btn-cetak" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
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
				<div class="row">
					<div class="col-md-8">
						<div class="card-header color-dark fw-500">
							Daftar <?= @$title ?>
						</div>
					</div>
					<div class="col-md-4">
						<a href="javascript:(0)" type="button" onclick="simpanhpp()" class="btn btn-primary btn-sm btn-add mt-2 mr-4" style="float:right;">
							<i class="la la-save"></i> Simpan Nilai Persediaan Barang
						</a>
					</div>
				</div>
				<div class="card-body">

					<div  class="userDatatable global-shadow border-0 bg-white w-100">
						<div class="table-responsive">
							<table id="table-nilaipersediaan" class="table mb-0 table-borderless">
								<thead>
									<tr>
					                    <td colspan="3">
					                        <div class="form-group">
					                            <label class="form-control-label">Gudang</label>
					                            <select class="form-control form-select" onchange="filtrasi()" id="combo-gudang">
													<option value="">Semua Gudang</option>
					                                <?php foreach ($dtgudang as $key) {
					                                	if ($gudang != '' && $gudang == $key['KodeGudang']) {
					                                		echo '<option value="' . $key['KodeGudang'] . '" selected>' . $key['NamaGudang'] . '</option>';
					                                	} else {
					                                    	echo '<option value="' . $key['KodeGudang'] . '">' . $key['NamaGudang'] . '</option>';
					                                	}
					                                } ?>
					                            </select>
					                        </div>
					                    </td>
					                    <td colspan="2">
					                        <div class="form-group">
					                            <label class="form-control-label">Jenis Barang</label>
					                            <select class="form-control form-selcet" onchange="filtrasi()" id="combo-jenis">
					                                <option value="">Semua Jenis Barang</option>
					                                <?php foreach ($dtjenis as $key) { ?>
					                                    <option value="<?= $key['KodeJenis'] ?>"><?= $key['NamaJenisBarang'] ?></option>
					                                <?php } ?>
					                            </select>
					                        </div>
					                    </td>
					                    <td colspan="2">
					                        <div class="form-group">
					                            <label class="form-control-label">Pencarian Data</label>
					                            <input id="inp-search" placeholder="Cari kode/nama barang" class="form-control" onkeyup="filtrasi()" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
					                        </div>
					                    </td>
					                    <td>
					                    	<div class="form-group">
					                    		<label class="form-control-label">Filter Stok</label>
					                    		<select class="form-control form-select" onchange="filtrasi()" id="combo-stock">
					                    			<option value="" selected>Stok Tersedia</option>
					                    			<option value="0">Tampilkan Stok 0</option>
					                    		</select>
					                    	</div>
					                    </td>
					                    <td>
					                    	<div class="form-group">
					                    		<label class="form-control-label">Jumlah Total HPP</label>
					                    		<input type="text" name="" class="form-control" id="TotalNilaiHPP" style="padding: 5px; box-sizing: border-box; text-align: right;" readonly>
					                    	</div>
					                    </td>
					                </tr>
					                <tr class="userDatatable-header">
					                	<th style="display: table-cell; width:3%"></th>
					                    <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Kode Barang </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Nama Barang </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Jenis Barang </span></th>
					                    <th style="display: table-cell;"><span class="userDatatable-title">Satuan Barang </span></th>
					                    <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Stok </span></th>
					                    <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Nilai HPP </span></th>
					                    <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Total </span></th>
					                    <th style="display: table-cell; width:10%;">#</th>
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
