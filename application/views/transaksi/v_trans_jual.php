<style type="text/css">
	#ModalTambahSO {
		overflow-y: scroll !important;
	}

	#ModalTambahTJL {
		overflow-y: scroll !important;
	}

	#ModalTambahTP {
		overflow-y: scroll !important;
	}

	#ModalTambahRet {
		overflow-y: scroll !important;
	}

	.btn-sm,
	.btn-group-sm>.btn {
		padding-top: 0.1rem;
		padding-right: 0.5rem;
		padding-bottom: 0.1rem;
		padding-left: 0.5rem;
		font-size: 13px;
		line-height: 2.3rem;
		border-radius: 0.2rem;
	}

	.btn-warning {
		color: #ffffff;
		background-color: #fa8b0c;
		border-color: #fa8b0c;
		box-shadow: none;
	}

	.dataTables_wrapper input[type="search"],
	.dataTables_wrapper input[type="text"],
	.dataTables_wrapper select {
		border: 1px solid #adb5bd;
		/* padding: 0.3rem 1rem; */
		color: #715d5d;
	}

	.input-container.icon-left input {
		padding-left: 40px;
	}
</style>
<div class="social-dash-wrap">
	<div class="row">
		<div class="col-lg-12">

			<div class="breadcrumb-main">
				<h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
				<div class="breadcrumb-action justify-content-center flex-wrap">
				</div>
			</div>

		</div>
		<div class="col-lg-12 mb-30">
			<div class="card">
				<!-- <div class="card-header color-dark fw-500">
					Fitur Level <span style="color:#FA7C41;"><?= $level['LevelName']; ?></span>
				</div> -->
				<div class="card-body">
					<div class="userDatatable global-shadow border-0 bg-white w-100">
						<div class="tab-wrapper">
							<div class="atbd-tab tab-horizontal">
								<ul class="nav nav-tabs vertical-tabs" role="tablist">
									<?php if ($sliporderview == 1) { ?>
										<li class="nav-item">
											<a class="nav-link <?= ($sliporderview == 1) ? 'active' : '' ?>" id="custom-tabs-so-tab" data-toggle="tab" href="#custom-tabs-for-so" role="tab" aria-controls="custom-tabs-for-so" aria-selected="true">Transaksi Slip Order</a>
										</li>
									<?php } ?>
									<?php if ($quotationview == 1) { ?>
										<li class="nav-item" hidden>
											<a class="nav-link <?= ($sliporderview == 0 && $quotationview == 1) ? 'active' : '' ?>" id="custom-tabs-quotation-tab" data-toggle="tab" href="#custom-tabs-for-quotation" role="tab" aria-controls="custom-tabs-for-quotation" aria-selected="false">Quotation, Purchase Invoice, dan Invoice</a>
										</li>
									<?php } ?>
									<?php if ($transjualview == 1) { ?>
										<li class="nav-item">
											<a class="nav-link <?= ($sliporderview == 0 && $quotationview == 0 && $transjualview == 1) ? 'active' : '' ?>" id="custom-tabs-tjl-tab" data-toggle="tab" href="#custom-tabs-for-tjl" role="tab" aria-controls="custom-tabs-for-tjl" aria-selected="false">Transaksi Penjualan</a>
										</li>
									<?php } ?>
									<?php if ($returview == 1) { ?>
										<li class="nav-item">
											<a class="nav-link <?= ($sliporderview == 0 && $quotationview == 0 && $transjualview == 0 && $returview == 1) ? 'active' : '' ?>" id="custom-tabs-retur-tab" data-toggle="tab" href="#custom-tabs-for-retur" role="tab" aria-controls="custom-tabs-for-retur" aria-selected="false">Retur</a>
										</li>
									<?php } ?>
									<?php if ($piutangview == 1) { ?>
										<li class="nav-item" hidden>
											<a class="nav-link" id="custom-tabs-piutang-tab" data-toggle="tab" href="#custom-tabs-for-piutang" role="tab" aria-controls="custom-tabs-for-piutang" aria-selected="false">Transaksi Terima Piutang</a>
										</li>
									<?php } ?>
								</ul>
							</div>

							<div class="tab-content">
								<div class="tab-pane fade <?= ($sliporderview == 1) ? 'show active' : '' ?>" id="custom-tabs-for-so" role="tabpanel" aria-labelledby="custom-tabs-so-tab">
									<div class="userDatatable global-shadow border-0 bg-white w-100">
										<div class="table-responsive">
											<table id="table-so" class="table mb-0 table-borderless">
												<thead>
													<tr>
														<td colspan="3">
															<div class="form-group">
																<label class="form-control-label">Pencarian Data</label>
																<input id="inp-search-so" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
															</div>
														</td>
														<td></td>
														<td colspan="4">
															<div class="row">
																<div class="action-btn ml-auto">
																	<div class="form-group mb-0">
																		<div class="input-container icon-left position-relative">
																			<span class="input-icon icon-left">
																				<span data-feather="calendar"></span>
																			</span>
																			<input type="text" class="form-control" id="tgl-transaksi-so">
																			<span class="input-icon icon-right">
																				<span data-feather="chevron-down"></span>
																			</span>
																		</div>
																	</div>
																</div>
																<?php if ($sliporderadd == 1) { ?>
																	&nbsp;&nbsp;
																	<button type="button" id="btntambahso" class="btn btn-primary btn-sm btn-add">
																		<i class="la la-plus"></i> Tambah Data
																	</button>
																<?php } ?>
															</div>
														</td>
													</tr>
													<tr class="userDatatable-header">
														<th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Kode SO </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">No SO </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Tanggal SO </span></th>
														<!-- <th style="display: table-cell;"><span class="userDatatable-title">Estimasi Selesai </span></th> -->
														<th style="display: table-cell;"><span class="userDatatable-title">Nama Customer </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Total Tagihan </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Status SO </span></th>
														<th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Created By</span></th>
														<th style="display: table-cell; width:15%;">#</th>
													</tr>
												</thead>
												<tbody style="font-size:14px;">
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="tab-pane fade <?= ($sliporderview == 0 && $quotationview == 1) ? 'show active' : '' ?>" id="custom-tabs-for-quotation" role="tabpanel" aria-labelledby="custom-tabs-quotation-tab">
									<div class="userDatatable global-shadow border-0 bg-white w-100">
										<div class="table-responsive">
											<table id="table-qi" class="table mb-0 table-borderless">
												<thead>
													<tr>
														<td colspan="3">
															<div class="form-group">
																<label class="form-control-label">Pencarian Data</label>
																<input id="inp-search-qi" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
															</div>
														</td>
														<td colspan="2"></td>
														<td colspan="3">
															<div class="row">
																<div class="action-btn ml-auto">
																	<div class="form-group mb-0">
																		<div class="input-container icon-left position-relative">
																			<span class="input-icon icon-left">
																				<span data-feather="calendar"></span>
																			</span>
																			<input type="text" class="form-control" id="tgl-transaksi-qi">
																			<span class="input-icon icon-right">
																				<span data-feather="chevron-down"></span>
																			</span>
																		</div>
																	</div>
																</div>
															</div>
														</td>
													</tr>
													<tr class="userDatatable-header">
														<th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Kode SO </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">No SO </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Tanggal SO </span></th>
														<!-- <th style="display: table-cell;"><span class="userDatatable-title">Estimasi Selesai </span></th> -->
														<th style="display: table-cell;"><span class="userDatatable-title">Nama Customer </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Total Tagihan </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Status SO </span></th>
														<th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Created By</span></th>
														<th style="display: table-cell; width:4%;">#</th>
													</tr>
												</thead>
												<tbody style="font-size:14px;">
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="tab-pane fade <?= ($sliporderview == 0 && $quotationview == 0 && $transjualview == 1) ? 'show active' : '' ?>" id="custom-tabs-for-tjl" role="tabpanel" aria-labelledby="custom-tabs-tjl-tab">
									<div class="userDatatable global-shadow border-0 bg-white w-100">
										<div class="table-responsive">
											<table id="table-penjualan" class="table mb-0 table-borderless">
												<thead>
													<tr>
														<td colspan="3">
															<div class="form-group">
																<label class="form-control-label">Pencarian Data</label>
																<input id="inp-search-tjl" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
															</div>
														</td>
														<td colspan="2"></td>
														<td colspan="4">
															<div class="row">
																<div class="action-btn ml-auto">
																	<div class="form-group mb-0">
																		<div class="input-container icon-left position-relative">
																			<span class="input-icon icon-left">
																				<span data-feather="calendar"></span>
																			</span>
																			<input type="text" class="form-control" id="tgl-transaksi-tjl">
																			<span class="input-icon icon-right">
																				<span data-feather="chevron-down"></span>
																			</span>
																		</div>
																	</div>
																</div>
																<?php if ($transjualadd == 1) { ?>
																	&nbsp;&nbsp;
																	<button type="button" id="btntambahtjl" class="btn btn-primary btn-sm btn-add">
																		<i class="la la-plus"></i> Tambah Data
																	</button>
																<?php } ?>
															</div>
														</td>
													</tr>
													<tr class="userDatatable-header">
														<th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Kode Transaksi </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">No Referensi </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Tanggal Penjualan </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Nama Customer </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Total Tagihan </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Total Dibayar </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Status Bayar </span></th>
														<th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Created By</span></th>
														<th style="display: table-cell; width:5%;">#</th>
													</tr>
												</thead>
												<tbody style="font-size:14px;">
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="tab-pane fade <?= ($sliporderview == 0 && $quotationview == 0 && $transjualview == 0 && $returview == 1) ? 'show active' : '' ?>" id="custom-tabs-for-retur" role="tabpanel" aria-labelledby="custom-tabs-retur-tab">
									<div class="userDatatable global-shadow border-0 bg-white w-100">
										<div class="table-responsive">
											<table id="table-retur" class="table mb-0 table-borderless">
												<thead>
													<tr>
														<td colspan="3">
															<div class="form-group">
																<label class="form-control-label">Pencarian Data</label>
																<input id="inp-search-retur" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
															</div>
														</td>
														<td colspan="2"></td>
														<td colspan="4">
															<div class="row">
																<div class="action-btn ml-auto">
																	<div class="form-group mb-0">
																		<div class="input-container icon-left position-relative">
																			<span class="input-icon icon-left">
																				<span data-feather="calendar"></span>
																			</span>
																			<input type="text" class="form-control" id="tgl-transaksi-retur">
																			<span class="input-icon icon-right">
																				<span data-feather="chevron-down"></span>
																			</span>
																		</div>
																	</div>
																</div>
																<?php if ($transjualadd == 1) { ?>
																	&nbsp;&nbsp;
																	<button type="button" id="btntambahretur" class="btn btn-primary btn-sm btn-add">
																		<i class="la la-plus"></i> Tambah Data
																	</button>
																<?php } ?>
															</div>
														</td>
													</tr>
													<tr class="userDatatable-header">
														<th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Kode Transaksi </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Kode Referensi </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Tanggal Retur </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Nama Customer </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Total Retur </span></th>
														<th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Nama Gudang </span></th>
														<th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Created By</span></th>
														<th style="display: table-cell; width:5%;">#</th>
													</tr>
												</thead>
												<tbody style="font-size:14px;">
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="custom-tabs-for-piutang" role="tabpanel" aria-labelledby="custom-tabs-piutang-tab">
									<div class="userDatatable global-shadow border-0 bg-white w-100">
										<div class="table-responsive">
											<table id="table-terimapiutang" class="table mb-0 table-borderless">
												<thead>
													<tr>
														<td colspan="3">
															<div class="form-group">
																<label class="form-control-label">Pencarian Data</label>
																<input id="inp-search-tp" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
															</div>
														</td>
														<td colspan="2"></td>
														<td colspan="4">
															<div class="row">
																<div class="action-btn ml-auto">
																	<div class="form-group mb-0">
																		<div class="input-container icon-left position-relative">
																			<span class="input-icon icon-left">
																				<span data-feather="calendar"></span>
																			</span>
																			<input type="text" class="form-control" id="tgl-transaksi-tp">
																			<span class="input-icon icon-right">
																				<span data-feather="chevron-down"></span>
																			</span>
																		</div>
																	</div>
																</div>
																<?php if ($piutangadd == 1) { ?>
																	&nbsp;&nbsp;
																	<button type="button" id="btntambahtp" class="btn btn-primary btn-sm btn-add">
																		<i class="la la-plus"></i> Tambah Data
																	</button>
																<?php } ?>
															</div>
														</td>
													</tr>
													<tr class="userDatatable-header">
														<th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Kode Transaksi </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">No Referensi </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Tanggal Penjualan </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Nama Customer </span></th>
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
			</div>
		</div>
	</div>
</div>

<div class="modal fade ui-dialog" id="ModalTambahSO" role="dialog">
	<div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Slip Order</h4>
			</div>
			<form action="<?= base_url('transaksi/slip_order/simpan') ?>" method="post" id="form-simpan-so">
				<div class="modal-body">
					<div class="row clearfix">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="exampleInputFile">No Slip Order</label>
								<input type="hidden" class="form-control" id="IDTransJual" name="IDTransJual" />
								<input type="text" class="form-control" id="NoSlipOrder" name="NoSlipOrder" value="">
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Tanggal Slip Order</label>
								<input type="datetime-local" class="form-control" id="TglSlipOrder" name="TglSlipOrder" required>
							</div>
							<!-- <div class="form-group">
                                <label for="exampleInputFile">Estimasi Selesai</label>
                                <input type="datetime-local" class="form-control" id="EstimasiSelesai" name="EstimasiSelesai" required>
                            </div> -->
							<div class="form-group">
								<label for="exampleInputFile">Nama Customer</label>
								<select class="form-control form-select select2" name="KodePerson" id="KodePerson" required>
									<option value="" selected>Pilih Customer</option>
									<?php if ($customer) {
										foreach ($customer as $key) {
											echo '<option value="' . $key['KodePerson'] . '">' . $key['KodePerson'] . ' | ' . $key['NamaPersonCP'] . '</option>';
										}
									} ?>
								</select>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Gudang</label>
								<select class="form-control form-select select2" name="KodeGudang" id="KodeGudangSO" required>
									<option value="" selected>Pilih Gudang</option>
									<?php if ($gudang) {
										foreach ($gudang as $key) {
											echo '<option value="' . $key['KodeGudang'] . '">' . $key['KodeGudang'] . ' | ' . $key['NamaGudang'] . '</option>';
										}
									} ?>
								</select>
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

<div class="modal fade ui-dialog" id="ModalTambahTJL" role="dialog">
	<div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Penjualan</h4>
			</div>
			<form action="<?= base_url('transaksi/transaksi_penjualan/simpan') ?>" method="post" id="form-simpan-tjl">
				<div class="modal-body">
					<div class="row clearfix">
						<div class="col-sm-12">
							<div class="form-group" hidden>
								<label for="exampleInputFile">No Referensi</label>
								<input type="text" class="form-control" id="NoRef_Manual" name="NoRef_Manual" value="">
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Tanggal Penjualan</label>
								<input type="datetime-local" class="form-control" id="TanggalPenjualan" name="TanggalPenjualan" required>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Kode SO (opsional)</label>
								<select class="form-control form-select" name="IDTransJual" id="IDTransJual2" disabled>
									<!-- <option value="" selected>Pilih Kode SO</option> -->
									<option></option>
								</select>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Customer</label>
								<select class="form-control form-select select2" name="KodePerson" id="KodePerson2" required disabled>
									<option value="" selected>Pilih Customer</option>
									<?php if ($customer) {
										foreach ($customer as $key) {
											echo '<option value="' . $key['KodePerson'] . '">' . $key['KodePerson'] . ' | ' . $key['NamaPersonCP'] . '</option>';
										}
									} ?>
								</select>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Gudang</label>
								<select class="form-control form-select select2" name="KodeGudang" id="KodeGudang" required disabled>
									<option value="" selected>Pilih Gudang</option>
									<?php if ($gudang) {
										foreach ($gudang as $key) {
											echo '<option value="' . $key['KodeGudang'] . '">' . $key['KodeGudang'] . ' | ' . $key['NamaGudang'] . '</option>';
										}
									} ?>
								</select>
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

<div class="modal fade ui-dialog" id="ModalTambahTP" role="dialog">
	<div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Terima Piutang</h4>
			</div>
			<form action="<?= base_url('transaksi/terima_piutang/simpan') ?>" method="post" id="form-simpan-tp">
				<div class="modal-body">
					<div class="row clearfix">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="exampleInputFile">No Referensi Terima Piutang</label>
								<input type="text" class="form-control" id="NoRef_Manual2" name="NoRef_Manual" value="" required>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Tanggal Terima Piutang</label>
								<input type="datetime-local" class="form-control" id="TanggalTransaksi" name="TanggalTransaksi" required>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Customer</label>
								<select class="form-control form-select select2" name="KodePerson" id="KodePerson3" required disabled>
									<option value="" selected>Pilih Customer</option>
									<?php if ($customer_piutang) {
										foreach ($customer_piutang as $key) {
											echo '<option value="' . $key['KodePerson'] . '">' . $key['KodePerson'] . ' | ' . $key['NamaPersonCP'] . '</option>';
										}
									} ?>
								</select>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Keterangan</label>
								<textarea class="form-control" rows="3" id="Uraian" name="Uraian" required></textarea>
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

<div class="modal fade ui-dialog" id="ModalTambahRet" role="dialog">
	<div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="title" id="defaultModalLabel">Tambah Data Retur Penjualan</h4>
			</div>
			<form action="<?= base_url('transaksi/retur/simpan') ?>" method="post" id="form-simpan-retur">
				<div class="modal-body">
					<div class="row clearfix">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="exampleInputFile">Kode Referensi Penjualan</label>
								<input type="hidden" class="form-control" id="IDTransRetur" name="IDTransRetur">
								<select class="form-control form-select select2" name="IDTrans" id="IDTrans" disabled required>
									<option value="" selected>Pilih Kode Referensi Penjualan</option>
									<?php if ($idtrans) {
										foreach ($idtrans as $key) {
											echo '<option value="' . $key['IDTransJual'] . '">' . $key['IDTransJual'] . '</option>';
										}
									} ?>
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="exampleInputFile">Customer</label>
								<input type="hidden" class="form-control" id="KodePerson4" name="KodePerson" value="" readonly>
								<input type="text" class="form-control" id="KodePersonView" name="" value="" readonly>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="exampleInputFile">Tanggal Penjualan</label>
								<input type="datetime-local" class="form-control" id="TglPenjualan" name="" readonly>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="exampleInputFile">Tanggal Transaksi</label>
								<input type="datetime-local" class="form-control" id="TanggalTransaksiRet" name="TanggalTransaksi" required>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Jenis Realisasi</label>
								<select class="form-control form-select" id="JenisRealisasi" name="JenisRealisasi" required>
									<option value="" selected>Pilih Jenis Realisasi</option>
									<option value="KEMBALI BARANG">Kembali Barang</option>
									<option value="KEMBALI UANG">Kembali Uang</option>
								</select>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Gudang</label>
								<select class="form-control form-select select2" name="KodeGudang" id="KodeGudangRet" required disabled>
									<option value="" selected>Pilih Gudang</option>
									<?php if ($gudang) {
										foreach ($gudang as $key) {
											echo '<option value="' . $key['KodeGudang'] . '">' . $key['KodeGudang'] . ' | ' . $key['NamaGudang'] . '</option>';
										}
									} ?>
								</select>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Keterangan</label>
								<textarea class="form-control" rows="3" id="Keterangan" name="Keterangan"></textarea>
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