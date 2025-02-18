<div class="social-dash-wrap">
	<div class="row">
		<div class="col-lg-12" style="
    margin-top: 1rem;
">

			<div class="breadcrumb-main">

				<body onload="startTime()">
					<ul>
						<li>
							<h4 class="breadcrumb-title">Selamat <span id="sapaan"></span>, <?= $this->session->userdata('ActualName'); ?>!</h4>
						</li>
						<li>
							<h6 class="text-capitalize" id="digitalClock" style="font-size:12px; font-weight:400; margin-top:0.25rem;"></h6>
						</li>
					</ul>
				</body>
				<div class="breadcrumb-action justify-content-center flex-wrap">
					<div class="action-btn" hidden>
					</div>
				</div>
			</div>

		</div>

		<div class="col-lg-12">
			<div class="breadcrumb-main">
				<h5 class="breadcrumb-title">Aktifitas apa yang ingin anda lakukan?</h5>
			</div>

			<div class="row">

				<!-- Tambah barang -->
				<div class="col-xxl-3 col-sm-3">
					<div class="media px-0 py-10  pl-10 pr-10 bg-white radius-xl users-list " onclick="getlinkbarang()" style="cursor: pointer;">
						<img class=" mr-0 wh-80 svg" src="https://res.cloudinary.com/boxity-id/image/upload/v1703865012/new_product_apsfi6.svg" alt="Boxity assets svg/png">
						<div class="media-body d-xl-flex users-list-body">
							<div class="flex-1 pr-xl-0 users-list-body__title">
								<h6 class="mt-0 fw-500" style="font-size:1rem; font-weight:700; margin-bottom:0; padding-left:9px; padding-top:10px;">Tambah barang</h6>
								<p class="mb-0" style="font-size:xx-small; padding-left:10px; padding-right:10px;padding-top:0;">Modul ini berguna untuk menambah barang baru, agar terautomasi dengan tepat.</p>
							</div>
						</div>
					</div>
				</div>

				<!-- Pesanan pembelian -->
				<div class="col-xxl-3 col-sm-3">
					<div class="media px-0 py-10  pl-10 pr-10 bg-white radius-xl users-list " onclick="getlinkpembelian()" style="cursor: pointer;">
						<img class=" mr-0 wh-80 svg" src="https://res.cloudinary.com/boxity-id/image/upload/v1703865011/buy_module_hybohf.svg" alt="Boxity assets svg/png">
						<div class="media-body d-xl-flex users-list-body">
							<div class="flex-1 pr-xl-0 users-list-body__title">
								<h6 class="mt-0 fw-500" style="font-size:1rem; font-weight:700; margin-bottom:0; padding-left:9px; padding-top:10px;">Buat pemesanan pembelian</h6>
								<p class="mb-0" style="font-size:xx-small; padding-left:10px; padding-right:10px;padding-top:0;">Buat pemesanan pembelian langsung dari modul ini.</p>
							</div>
						</div>
					</div>
				</div>

				<!-- Pesanan penjualan -->
				<div class="col-xxl-3 col-sm-3">
					<div class="media px-0 py-10  pl-10 pr-10 bg-white radius-xl users-list " onclick="getlinkpenjualan()" style="cursor: pointer;">
						<img class=" mr-0 wh-80 svg" src="https://res.cloudinary.com/boxity-id/image/upload/v1703865011/sell_module_altgow.svg" alt="Boxity assets svg/png">
						<div class="media-body d-xl-flex users-list-body">
							<div class="flex-1 pr-xl-0 users-list-body__title">
								<h6 class="mt-0 fw-500" style="font-size:1rem; font-weight:700; margin-bottom:0; padding-left:9px; padding-top:10px;">Buat pemesanan penjualan</h6>
								<p class="mb-0" style="font-size:xx-small; padding-left:10px; padding-right:10px;padding-top:0;">Buat pemesanan penjualan langsung dari modul ini.</p>
							</div>
						</div>
					</div>
				</div>

				<!-- SPK -->
				<div class="col-xxl-3 col-sm-3">
					<div class="media px-0 py-10  pl-10 pr-10 bg-white radius-xl users-list " onclick="getlinkspk()" style="cursor: pointer;">
						<img class=" mr-0 wh-80 svg" src="https://res.cloudinary.com/boxity-id/image/upload/v1703865011/spk_module_hh6pxm.svg" alt="Boxity assets svg/png">
						<div class="media-body d-xl-flex users-list-body">
							<div class="flex-1 pr-xl-0 users-list-body__title">
								<h6 class="mt-0 fw-500" style="font-size:1rem; font-weight:700; margin-bottom:0; padding-left:9px; padding-top:10px;">Buat Surat Perintah Kerja</h6>
								<p class="mb-0" style="font-size:xx-small; padding-left:10px; padding-right:10px;padding-top:0;">Buat surat perintah kerja untuk disimpan di stock gudang.</p>
							</div>
						</div>
					</div>
				</div>

			</div>

		</div>

		<div class="col-lg-12">
			<div class="breadcrumb-main">
				<h5 class="breadcrumb-title">Overview</h5>
			</div>

			<div class="row">
				<!-- Sales & Purchasing -->
				<div class="col-lg-12 mb-25">
					<div class="card broder-0">
						<div class="card-header">
							<h6>Sales & Purchasing</h6>
							<!-- <div class="action-btn"> -->
							<form method="get" action="<?= base_url('beranda') ?>" id="form-tgl" style="display: none;">
								<div class="form-group mb-0">
									<div class="input-container icon-left position-relative">
										<span class="input-icon icon-left">
											<span data-feather="calendar"></span>
										</span>
										<input id="tgl" name="tgl" class="form-control" style="" type="text" autocomplete="off" value="<?= date("d-m-Y", strtotime($today)) ?>">
										<span class="input-icon icon-right">
											<span data-feather="chevron-down"></span>
										</span>
									</div>
								</div>
							</form>
							<!-- </div> -->
							<div class="card-extra">
								<ul class="card-tab-links mr-3 nav-tabs nav" role="tablist">
									<li>
										<a href="#incExp-week" class="active" data-toggle="tab" id="incExp-week-tab" role="tab" aria-selected="true">Week</a>
									</li>
									<li>
										<a href="#incExp-month" data-toggle="tab" id="incExp-month-tab" role="tab" aria-selected="true">Month</a>
									</li>
									<li>
										<a href="#incExp-year" data-toggle="tab" id="incExp-year-tab" role="tab" aria-selected="true">Year</a>
									</li>
								</ul>
							</div>
						</div>
						<!-- ends: .card-header -->
						<div class="card-body">
							<div class="tab-content">
								<div class="tab-pane fade active show" id="incExp-week" role="tabpanel" aria-labelledby="incExp-week-tab">
									<div class="row">
										<div class="col-lg-3">
											<span style="font-size: x-small; font-weight: 600; padding: none;"><?= $first_week ?> - <?= $end_week ?></span><br>
											<span class="cashflow-display__title" style="font-size:x-small;">Total Sales In-Cash</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalmghutanglunas, 2)) ?></h6>
											<span class="cashflow-display__title" style="font-size:x-small;">Total Sales Still-Credit</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalmghutang, 2)) ?></h6>
											<span class="cashflow-display__title" style="font-size:x-small;">Total Purchases In-Cash</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalmgpiutanglunas, 2)) ?></h6>
											<span class="cashflow-display__title" style="font-size:x-small;">Total Purchases Still-Credit</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalmgpiutang, 2)) ?></h6>
										</div>
										<div class="col-lg-9 inEx-wrap d-flex" style="padding-top: 10px;">
											<div class="inEx-chart">
												<div class="parentContainer">


													<div>
														<canvas id="barChartInEx_Why"></canvas>
													</div>


												</div>
												<ul class="legend-static">
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(95, 99, 242);"></span>Sales In-Cash
													</li>
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(255, 105, 165);"></span>Sales Out-Cash
													</li>
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(250, 139, 12);"></span>Purchase In-Cash
													</li>
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(32, 201, 151);"></span>Purchase Out-Cash
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="incExp-month" role="tabpanel" aria-labelledby="incExp-month-tab">
									<div class="row">
										<div class="col-lg-3">
											<span class="cashflow-display__title" style="font-size:x-small;">Total Sales In-Cash</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalblnhutanglunas, 2)) ?></h6>
											<span class="cashflow-display__title" style="font-size:x-small;">Total Sales Still-Credit</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalblnhutang, 2)) ?></h6>
											<span class="cashflow-display__title" style="font-size:x-small;">Total Purchases In-Cash</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalblnpiutanglunas, 2)) ?></h6>
											<span class="cashflow-display__title" style="font-size:x-small;">Total Purchases Still-Credit</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalblnpiutang, 2)) ?></h6>
										</div>
										<div class="col-lg-9 inEx-wrap d-flex" style="padding-top: 10px;">
											<div class="inEx-chart">
												<div class="parentContainer">


													<div>
														<canvas id="barChartInEx_My"></canvas>
													</div>


												</div>
												<ul class="legend-static">
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(95, 99, 242);"></span>Sales In-Cash
													</li>
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(255, 105, 165);"></span>Sales Out-Cash
													</li>
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(250, 139, 12);"></span>Purchase In-Cash
													</li>
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(32, 201, 151);"></span>Purchase Out-Cash
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="incExp-year" role="tabpanel" aria-labelledby="incExp-year-tab">
									<div class="row">
										<div class="col-lg-3">
											<span class="cashflow-display__title" style="font-size:x-small;">Total Sales In-Cash</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalthnhutanglunas, 2)) ?></h6>
											<span class="cashflow-display__title" style="font-size:x-small;">Total Sales Still-Credit</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalthnhutang, 2)) ?></h6>
											<span class="cashflow-display__title" style="font-size:x-small;">Total Purchases In-Cash</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalthnpiutanglunas, 2)) ?></h6>
											<span class="cashflow-display__title" style="font-size:x-small;">Total Purchases Still-Credit</span>
											<h6 class="cashflow-display__amount" style="font-size:small;">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalthnpiutang, 2)) ?></h6>
										</div>
										<div class="col-lg-9 inEx-wrap d-flex" style="padding-top: 10px;">
											<div class="inEx-chart">
												<div class="parentContainer">


													<div>
														<canvas id="barChartInEx_Y"></canvas>
													</div>


												</div>
												<ul class="legend-static">
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(95, 99, 242);"></span>Sales In-Cash
													</li>
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(255, 105, 165);"></span>Sales Out-Cash
													</li>
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(250, 139, 12);"></span>Purchase In-Cash
													</li>
													<li style="display: inline-flex; align-items: center;">
														<span style="background-color: rgb(32, 201, 151);"></span>Purchase Out-Cash
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- ends: .card-body -->
					</div>
				</div>

				<!-- Stock_Available_Old -->
				<div class="col-lg-3 mb-25" hidden>
					<div class="card revenueChartTwo broder-0">
						<div class="card-header">
							<h6>Stock Available</h6>
						</div>
						<div class="card-body pt-0 px-20">
							<div class="social-overview-wrap">
								<div class="card border-0">
									<div class="card-body">
										<p style="font-size: x-small;">Total Weight/Unit</p>
										<div class="row stock">
											<span class="text">
												<h5 style="font-size: small;"><?= str_replace([','], ['.'], number_format($totalweight)) ?> Kg/Pcs</h5>
											</span>
											<span data-feather="anchor" class="icon ml-auto"></span>
										</div>
										<br>
										<p style="font-size: x-small;">Lots of Item</p>
										<div class="row stock">
											<span class="text">
												<h5 style="font-size: small;"><?= $totalitems ?> Items</h5>
											</span>
											<span data-feather="command" class="icon ml-auto"></span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Stock Available -->
			<div class="row">
				<div class="col-lg-12 mb-25">
					<div class="card revenueChartTwo broder-0">
						<div class="card-header">
							<h6>Stock Available</h6>
						</div>
						<div class="card-body">
							<div>
								<canvas id="mychart88"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-12 mb-30">
			<div class="card border-0">
				<div class="card-header">
					<h6>Production Progress</h6>
					<div class="card-extra text-non-capitalize">
						<div class="action-btn">
							<a href="<?= base_url('transaksi/proses_produksi/list_produksi') ?>" role="button" id="seeall" aria-expanded="false">
								<h4 style="font-size:12px; color:rgba(0,0,0,.5); text-transform: none;">See all production</h4>
							</a>
						</div>
					</div>
				</div>
				<div class="card-body p-0">
					<div class="" id="t_channel-today">
						<div class="table-responsive">
							<table class="table table--default traffic-table">
								<thead>
									<tr>
										<th style="text-align: left;">Kode Barang</th>
										<th style="text-align: left;">Nama Barang</th>
										<th style="text-align: left;">Tgl Masuk Produksi</th>
										<th style="text-align: center;">Berat Kotor</th>
										<th style="text-align: center;">Berat Bersih</th>
										<th style="max-width: 150px; text-align: left;">Percentage of Process (%)</th>
										<th style="text-align: center;">Status</th>
									</tr>
								</thead>
								<tbody>
									<?php if ($production) { ?>
										<?php foreach ($production as $row) {
											$jml = $row['PCetak'] + $row['PPotong'] + $row['PKasar'] + $row['PCR'] + $row['PBT'] + $row['PR'] + $row['PHalus'];
											$percentage = 0;
											if ($row['PR'] > 0) {
												$percentage = $jml / 7 * 100;
											} else {
												$percentage = $jml / 6 * 100;
											}

											if ($percentage >= 0 && $percentage < 20) {
												$bg = 'bg-danger';
											} elseif ($percentage >= 20 && $percentage < 75) {
												$bg = 'bg-warning';
											} elseif ($percentage >= 75 && $percentage < 100) {
												$bg = 'bg-success';
											} else {
												$bg = 'bg-info';
											}
											$statusproduksi = isset($row['ProdTglSelesai']) ? 'SELESAI' : 'WIP';
										?>
											<tr>
												<td><?= $row['KodeManual'] ?></td>
												<td style="text-align: left;"><?= $row['NamaBarang'] ?></td>
												<td style="text-align: left;"><?= shortdate_indo(date('Y-m-d', strtotime($row['TanggalTransaksi']))) ?></td>
												<td><?= ($row['BeratKotor'] != null) ? str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['BeratKotor'], 2)) : 0 ?> kg</td>
												<td><?= ($row['Qty'] != null) ? str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Qty'], 2)) : 0 ?> kg</td>
												<td>
													<div class="progress">
														<div class="progress-bar <?= $bg ?>" role="progressbar" style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
													</div>
												</td>
												<td style="text-align: center;"><?= $statusproduksi ?></td>
											</tr>
										<?php } ?>
									<?php } else { ?>
										<tr>
											<td colspan="7" class="text-center">Data tidak ditemukan.</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-12 mb-30" hidden>
			<div class="card">
				<div class="card-header color-dark fw-500">
					Pagination
				</div>
				<div class="card-body p-0">

					<div class="table4 table5 p-25 bg-white">
						<div class="table-responsive">
							<table class="table mb-0" id="userDatatable">
								<thead>
									<tr class="userDatatable-header">
										<th>
											<div class="userDatatable-title">
												Age
												<div class="d-flex justify-content-between align-items-center w-100">
													<span class="userDatatable-sort">
														<i class="fas fa-caret-down"></i>
													</span>
													<span class="userDatatable-filter">
														<i class="fas fa-filter"></i>
													</span>
												</div>
											</div>
										</th>
										<th>
											<div class="userDatatable-title">
												Age
												<div class="d-flex justify-content-between align-items-center w-100">
													<span class="userDatatable-sort">
														<i class="fas fa-sort-up up"></i>
														<i class="fas fa-caret-down down"></i>
													</span>
													<span class="userDatatable-filter">
														<i class="fas fa-filter"></i>
													</span>
												</div>
											</div>
										</th>
										<th>
											<div class="userDatatable-title">
												Address
												<div class="d-flex justify-content-between align-items-center w-100">
													<span class="userDatatable-sort">
														<i class="fas fa-sort-up up"></i>
														<i class="fas fa-caret-down down"></i>
													</span>
													<span class="userDatatable-filter">
														<i class="fas fa-filter"></i>
													</span>
												</div>
											</div>
										</th>
									</tr>
								</thead>
								<tbody>

									<tr>
										<td>
											<div class="userDatatable-content">
												Mike
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												32
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												10 Herry Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Jhon
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												2
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												34 Lolona Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Hulk
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												4
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												5 Rigliah Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Percy Jacksion
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												5
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												24 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Donald
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												7
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												5 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Mac Jons
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												8
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												18 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Hery
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												15
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												5 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Jhon Bush
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												18
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												85 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Rabin
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												23
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												5 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Herry
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												28
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												12 Downing Street
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="userDatatable-content">
												Mike
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												32
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												10 Herry Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Jhon
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												2
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												34 Lolona Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Hulk
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												4
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												5 Rigliah Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Percy Jacksion
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												5
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												24 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Donald
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												7
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												5 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Mac Jons
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												8
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												18 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Hery
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												15
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												5 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Jhon Bush
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												18
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												85 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Rabin
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												23
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												5 Downing Street
											</div>
										</td>
									</tr>


									<tr>
										<td>
											<div class="userDatatable-content">
												Herry
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												28
											</div>
										</td>
										<td>
											<div class="userDatatable-content">
												12 Downing Street
											</div>
										</td>
									</tr>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>