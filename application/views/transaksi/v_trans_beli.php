<style type="text/css">
	#ModalTambahPO {
		overflow-y: scroll !important;
	}

	#ModalTambahTBL {
		overflow-y: scroll !important;
	}

	#ModalTambahHT {
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
				<div class="card-body">
					<div class="userDatatable global-shadow border-0 bg-white w-100">
						<div class="tab-wrapper">
							<div class="atbd-tab tab-horizontal">
								<ul class="nav nav-tabs vertical-tabs" role="tablist">
									<?php if ($poview == 1) { ?>
										<li class="nav-item">
											<a class="nav-link <?= ($poview == 1) ? 'active' : '' ?>" id="custom-tabs-po-tab" data-toggle="tab" href="#custom-tabs-for-po" role="tab" aria-controls="custom-tabs-for-po" aria-selected="true">Cetak Pembelian (PO)</a>
										</li>
									<?php } ?>
									<?php if ($approvalview == 1) { ?>
										<li class="nav-item">
											<a class="nav-link <?= ($poview == 0 && $approvalview == 1) ? 'active' : '' ?>" id="custom-tabs-approval-tab" data-toggle="tab" href="#custom-tabs-for-approval" role="tab" aria-controls="custom-tabs-for-approval" aria-selected="false">Approval Transaksi Pembelian (PO)</a>
										</li>
									<?php } ?>
									<?php if ($transbeliview == 1) { ?>
										<li class="nav-item">
											<a class="nav-link <?= ($poview == 0 && $approvalview == 0 && $transbeliview == 1) ? 'active' : '' ?>" id="custom-tabs-tbl-tab" data-toggle="tab" href="#custom-tabs-for-tbl" role="tab" aria-controls="custom-tabs-for-tbl" aria-selected="false">Transaksi Pembelian</a>
										</li>
									<?php } ?>
									<?php if ($returview == 1) { ?>
										<li class="nav-item">
											<a class="nav-link <?= ($poview == 0 && $approvalview == 0 && $transbeliview == 0 && $returview == 1) ? 'active' : '' ?>" id="custom-tabs-rtr-tab" data-toggle="tab" href="#custom-tabs-for-rtr" role="tab" aria-controls="custom-tabs-for-rtr" aria-selected="false">Retur</a>
										</li>
									<?php } ?>
									<?php if ($hutangview == 1) { ?>
										<li class="nav-item" hidden>
											<a class="nav-link" id="custom-tabs-hutang-tab" data-toggle="tab" href="#custom-tabs-for-hutang" role="tab" aria-controls="custom-tabs-for-hutang" aria-selected="false">Transaksi Hutang</a>
										</li>
									<?php } ?>
								</ul>
							</div>

							<div class="tab-content">
								<div class="tab-pane fade <?= ($poview == 1) ? 'show active' : '' ?>" id="custom-tabs-for-po" role="tabpanel" aria-labelledby="custom-tabs-po-tab">
									<div class="userDatatable global-shadow border-0 bg-white w-100">
										<div class="table-responsive">
											<table id="table-po" class="table mb-0 table-borderless">
												<thead>
													<tr>
														<td colspan="3">
															<div class="form-group">
																<label class="form-control-label">Pencarian Data</label>
																<input id="inp-search-po" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
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
																			<input type="text" class="form-control" id="tgl-transaksi-po">
																			<span class="input-icon icon-right">
																				<span data-feather="chevron-down"></span>
																			</span>
																		</div>
																	</div>
																</div>
																<?php if ($poadd == 1) { ?>
																	&nbsp;&nbsp;
																	<button type="button" id="btntambahpo" class="btn btn-primary btn-sm btn-add">
																		<i class="la la-plus"></i> Tambah Data
																	</button>
																<?php } ?>
															</div>
														</td>
													</tr>
													<tr class="userDatatable-header">
														<th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Kode PO </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">No PO </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Tanggal PO </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Nama Supplier </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Total Tagihan </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Status PO </span></th>
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
								<div class="tab-pane fade <?= ($poview == 0 && $approvalview == 1) ? 'show active' : '' ?>" id="custom-tabs-for-approval" role="tabpanel" aria-labelledby="custom-tabs-approval-tab">
									<div class="userDatatable global-shadow border-0 bg-white w-100">
										<div class="table-responsive">
											<table id="table-approval" class="table mb-0 table-borderless">
												<thead>
													<tr>
														<td colspan="2">
															<div class="form-group">
																<label class="form-control-label">Status PO</label>
																<select class="form-control" id="combo-status-approval">
																	<option value="">Semua Status</option>
																	<option value="PO" selected>PO</option>
																	<option value="APPROVED">APPROVED</option>
																	<option value="FAILED">FAILED</option>
																	<option value="DONE">DONE</option>
																</select>
															</div>
														</td>
														<td colspan="3">
															<div class="form-group">
																<label class="form-control-label">Pencarian Data</label>
																<input id="inp-search-approval" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
															</div>
														</td>
														<td colspan="3"></td>
														<td colspan="4">
															<div class="row">
																<div class="action-btn ml-auto">
																	<div class="form-group mb-0">
																		<div class="input-container icon-left position-relative">
																			<span class="input-icon icon-left">
																				<span data-feather="calendar"></span>
																			</span>
																			<input type="text" class="form-control" id="tgl-transaksi-approval">
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
														<th style="display: table-cell;"><span class="userDatatable-title">Kode PO </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">No PO </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Tanggal PO </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Nama Supplier </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Total Tagihan </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">No Approved </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Approved Oleh </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Tanggal Approved </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Keterangan </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Status PO </span></th>
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
								<div class="tab-pane fade <?= ($poview == 0 && $approvalview == 0 && $transbeliview == 1) ? 'show active' : '' ?>" id="custom-tabs-for-tbl" role="tabpanel" aria-labelledby="custom-tabs-tbl-tab">
									<div class="userDatatable global-shadow border-0 bg-white w-100">
										<div class="table-responsive">
											<table id="table-pembelian" class="table mb-0 table-borderless">
												<thead>
													<tr>
														<td colspan="3">
															<div class="form-group">
																<label class="form-control-label">Pencarian Data</label>
																<input id="inp-search-beli" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
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
																			<input type="text" class="form-control" id="tgl-transaksi-beli">
																			<span class="input-icon icon-right">
																				<span data-feather="chevron-down"></span>
																			</span>
																		</div>
																	</div>
																</div>
																<?php if ($transbeliadd == 1) { ?>
																	&nbsp;&nbsp;
																	<button type="button" id="btntambahbeli" class="btn btn-primary btn-sm btn-add">
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
														<th style="display: table-cell;"><span class="userDatatable-title">Tanggal Pembelian </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Nama Supplier </span></th>
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
								<div class="tab-pane fade <?= ($poview == 0 && $approvalview == 0 && $transbeliview == 0 && $returview == 1) ? 'show active' : '' ?>" id="custom-tabs-for-rtr" role="tabpanel" aria-labelledby="custom-tabs-rtr-tab">
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
																<?php if ($transbeliadd == 1) { ?>
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
														<th style="display: table-cell;"><span class="userDatatable-title">No Referensi </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Tanggal Retur </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Nama Supplier </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Total Retur </span></th>
														<th style="display: table-cell;"><span class="userDatatable-title">Nama Gudang </span></th>
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
								<div class="tab-pane fade" id="custom-tabs-for-hutang" role="tabpanel" aria-labelledby="custom-tabs-hutang-tab">
									<div class="userDatatable global-shadow border-0 bg-white w-100">
										<div class="table-responsive">
											<table id="table-bayarhutang" class="table mb-0 table-borderless">
												<thead>
													<tr>
														<td colspan="3">
															<div class="form-group">
																<label class="form-control-label">Pencarian Data</label>
																<input id="inp-search-hutang" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
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
																			<input type="text" class="form-control" id="tgl-transaksi-hutang">
																			<span class="input-icon icon-right">
																				<span data-feather="chevron-down"></span>
																			</span>
																		</div>
																	</div>
																</div>
																<?php if ($hutangadd == 1) { ?>
																	&nbsp;&nbsp;
																	<button type="button" id="btntambahhutang" class="btn btn-primary btn-sm btn-add">
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
			</div>
		</div>
	</div>
</div>

<div class="modal fade ui-dialog" id="ModalTambahPO" role="dialog">
	<div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Pembelian</h4>
			</div>
			<form action="<?= base_url('transaksi/transaksi_po/simpan') ?>" method="post" id="form-simpan-po">
				<div class="modal-body">
					<div class="row clearfix">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="exampleInputFile">No PO</label>
								<input type="hidden" class="form-control" id="IDTransBeli" name="IDTransBeli" />
								<input type="text" class="form-control" id="NoPO" name="NoPO" value="" required>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Tanggal PO</label>
								<input type="datetime-local" class="form-control" id="TglPO" name="TglPO" required>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Nama Supplier</label>
								<select class="form-control form-select select2" name="KodePerson" id="KodePerson" required>
									<option value="" selected>Pilih Supplier</option>
									<?php if ($supplier) {
										foreach ($supplier as $key) {
											echo '<option value="' . $key['KodePerson'] . '">' . $key['KodePerson'] . ' | ' . $key['NamaPersonCP'] . '</option>';
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

<div class="modal fade ui-dialog" id="ModalTambahTBL" role="dialog">
	<div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Pembelian</h4>
			</div>
			<form action="<?= base_url('transaksi/transaksi_pembelian/simpan') ?>" method="post" id="form-simpan-beli">
				<div class="modal-body">
					<div class="row clearfix">
						<div class="col-sm-12">
							<div class="form-group" hidden>
								<label for="exampleInputFile">No Referensi</label>
								<input type="text" class="form-control" id="NoRef_Manual" name="NoRef_Manual" value="">
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Tanggal Pembelian</label>
								<input type="datetime-local" class="form-control" id="TanggalPembelian" name="TanggalPembelian" required>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Kode PO (opsional)</label>
								<select class="form-control form-select" name="IDTransBeli" id="IDTransBeli2" disabled>
									<!-- <option value="" selected>Pilih Kode PO</option> -->
									<option></option>
								</select>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Supplier</label>
								<select class="form-control form-select select2" name="KodePerson" id="KodePerson2" required disabled>
									<option value="" selected>Pilih Supplier</option>
									<?php if ($supplier) {
										foreach ($supplier as $key) {
											echo '<option value="' . $key['KodePerson'] . '">' . $key['KodePerson'] . ' | ' . $key['NamaPersonCP'] . '</option>';
										}
									} ?>
								</select>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Uraian</label>
								<textarea class="form-control" rows="3" id="UraianPembelian" name="UraianPembelian"></textarea>
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

<div class="modal fade ui-dialog" id="ModalTambahHT" role="dialog">
	<div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Bayar Hutang</h4>
			</div>
			<form action="<?= base_url('transaksi/bayar_hutang/simpan') ?>" method="post" id="form-simpan-hutang">
				<div class="modal-body">
					<div class="row clearfix">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="exampleInputFile">No Referensi Bayar Hutang</label>
								<input type="text" class="form-control" id="NoRef_Manual2" name="NoRef_Manual" value="" required>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Tanggal Bayar Hutang</label>
								<input type="datetime-local" class="form-control" id="TanggalTransaksi" name="TanggalTransaksi" required>
							</div>
							<div class="form-group">
								<label for="exampleInputFile">Supplier</label>
								<select class="form-control form-select select2" name="KodePerson" id="KodePerson3" required disabled>
									<option value="" selected>Pilih Supplier</option>
									<?php if ($supplier_hutang) {
										foreach ($supplier_hutang as $key) {
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
				<h4 class="title" id="defaultModalLabel">Tambah Data Retur Pembelian</h4>
			</div>
			<form action="<?= base_url('transaksi/retur_pembelian/simpan') ?>" method="post" id="form-simpan-retur">
				<div class="modal-body">
					<div class="row clearfix">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="exampleInputFile">Kode Referensi Pembelian</label>
								<input type="hidden" class="form-control" id="IDTransRetur" name="IDTransRetur">
								<select class="form-control form-select select2" name="IDTrans" id="IDTrans" disabled required>
									<option value="" selected>Pilih Kode Referensi Pembelian</option>
									<?php if ($idtrans) {
										foreach ($idtrans as $key) {
											echo '<option value="' . $key['IDTransBeli'] . '">' . $key['IDTransBeli'] . '</option>';
										}
									} ?>
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="exampleInputFile">Supplier</label>
								<input type="hidden" class="form-control" id="KodePerson4" name="KodePerson" value="" readonly>
								<input type="text" class="form-control" id="KodePersonView" name="" value="" readonly>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="exampleInputFile">Tanggal Pembelian</label>
								<input type="datetime-local" class="form-control" id="TglPembelian" name="" readonly>
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
							<div class="form-group" hidden>
								<label for="exampleInputFile">Gudang</label>
								<select class="form-control form-select select2" name="KodeGudang" id="KodeGudangRet" disabled>
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