<style type="text/css">
	/*.nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
	    color: #291ae3a1 !important;
	    background-color: #ffffff;
	    border-color: #dee2e6 #dee2e6 #fff;
	}*/
</style>
<div class="social-dash-wrap">
	<div class="row">
		<div class="col-lg-12">

			<div class="breadcrumb-main">
				<h4 class="text-capitalize breadcrumb-title">Akses Level</h4>
				<div class="breadcrumb-action justify-content-center flex-wrap">
					<div class="action-btn" hidden>
						<button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add">
				            <i class="la la-plus"></i> Tambah Data
				        </button>
					</div>
				</div>
			</div>

		</div>
		<div class="col-lg-12 mb-30">
			<div class="card">
				<div class="card-header color-dark fw-500">
					Fitur Level <span style="color:#FA7C41;"><?= $level['LevelName']; ?></span>
				</div>
				<div class="card-body">
					<form action="<?= base_url('user/akseslevel/simpanfitur') ?>" method="post">
						<input type="hidden" class="form-control" name="LevelID" value="<?= $level['LevelID'] ?>">
						<div  class="userDatatable global-shadow border-0 bg-white w-100">
							Akses:
							<!-- <div class="table-responsive">
							</div> -->
							<div class="tab-wrapper">
						        <div class="atbd-tab tab-horizontal">
						            <ul class="nav nav-tabs vertical-tabs" role="tablist">
						                <li class="nav-item">
						                    <a class="nav-link active" id="custom-tabs-admin-tab" data-toggle="tab" href="#custom-tabs-for-admin" role="tab" aria-controls="custom-tabs-for-admin" aria-selected="true">Administrator</a>
						                </li>
						                <li class="nav-item">
						                    <a class="nav-link" id="custom-tabs-master-tab" data-toggle="tab" href="#custom-tabs-for-master" role="tab" aria-controls="custom-tabs-for-master" aria-selected="false">Master</a>
						                </li>
						                <li class="nav-item">
						                    <a class="nav-link" id="custom-tabs-transbeli-tab" data-toggle="tab" href="#custom-tabs-for-transbeli" role="tab" aria-controls="custom-tabs-for-transbeli" aria-selected="false">Transaksi Pembelian</a>
						                </li>
						                <li class="nav-item">
						                    <a class="nav-link" id="custom-tabs-transjual-tab" data-toggle="tab" href="#custom-tabs-for-transjual" role="tab" aria-controls="custom-tabs-for-transjual" aria-selected="false">Transaksi Penjualan</a>
						                </li>
						                <li class="nav-item">
						                    <a class="nav-link" id="custom-tabs-transkas-tab" data-toggle="tab" href="#custom-tabs-for-transkas" role="tab" aria-controls="custom-tabs-for-transkas" aria-selected="false">Transaksi Biaya</a>
						                </li>
						                <li class="nav-item">
						                    <a class="nav-link" id="custom-tabs-manufaktur-tab" data-toggle="tab" href="#custom-tabs-for-manufaktur" role="tab" aria-controls="custom-tabs-for-manufaktur" aria-selected="false">Manufaktur</a>
						                </li>
						                <li class="nav-item">
						                    <a class="nav-link" id="custom-tabs-warehouse-tab" data-toggle="tab" href="#custom-tabs-for-warehouse" role="tab" aria-controls="custom-tabs-for-warehouse" aria-selected="false">Warehouse</a>
						                </li>
						                <li class="nav-item" hidden>
						                    <a class="nav-link" id="custom-tabs-ampas-tab" data-toggle="tab" href="#custom-tabs-for-ampas" role="tab" aria-controls="custom-tabs-for-ampas" aria-selected="false">Ampas Dapur</a>
						                </li>
						                <li class="nav-item">
						                    <a class="nav-link" id="custom-tabs-akuntansi-tab" data-toggle="tab" href="#custom-tabs-for-akuntansi" role="tab" aria-controls="custom-tabs-for-akuntansi" aria-selected="false">Akuntansi</a>
						                </li>
						                <li class="nav-item">
						                    <a class="nav-link" id="custom-tabs-laporan-tab" data-toggle="tab" href="#custom-tabs-for-laporan" role="tab" aria-controls="custom-tabs-for-laporan" aria-selected="false">Laporan</a>
						                </li>
						                <li class="nav-item">
						                    <a class="nav-link" id="custom-tabs-payroll-tab" data-toggle="tab" href="#custom-tabs-for-payroll" role="tab" aria-controls="custom-tabs-for-payroll" aria-selected="false">Payroll</a>
						                </li>
						                <li class="nav-item">
						                    <a class="nav-link" id="custom-tabs-flip-tab" data-toggle="tab" href="#custom-tabs-for-flip" role="tab" aria-controls="custom-tabs-for-flip" aria-selected="false">Flip</a>
						                </li>
						            </ul>
						        </div>
						        
					            <div class="tab-content">
					                <div class="tab-pane fade show active" id="custom-tabs-for-admin" role="tabpanel" aria-labelledby="custom-tabs-admin-tab">
					                    <table id="table-admin" class="table table-striped">
											<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$admin = array('1','2','31','61','62','63','64');
						                    	if(in_array($row['FiturID'], $admin)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 31 || $row['FiturID'] == 61 || $row['FiturID'] == 62 || $row['FiturID'] == 64) ? 'hidden' : '' ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 64) ? 'hidden' : '' ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 31 || $row['FiturID'] == 61 || $row['FiturID'] == 62 || $row['FiturID'] == 64) ? 'hidden' : '' ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 31 || $row['FiturID'] == 61 || $row['FiturID'] == 62 || $row['FiturID'] == 64) ? 'hidden' : '' ?>/>
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
										</table>
					                </div>
					                <div class="tab-pane fade" id="custom-tabs-for-master" role="tabpanel" aria-labelledby="custom-tabs-master-tab">
					                    <table id="table-master" class="table table-striped">
					                    	<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$master = array('3','4','5','6','7','46','8','9','10','45');
						                    	if(in_array($row['FiturID'], $master)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
					                    </table>
					                </div>
					                <div class="tab-pane fade" id="custom-tabs-for-transbeli" role="tabpanel" aria-labelledby="custom-tabs-transbeli-tab">
					                    <table id="table-transbeli" class="table table-striped">
					                    	<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$transbeli = array('11','12','13','14','68');
						                    	if(in_array($row['FiturID'], $transbeli)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 12) ? 'hidden' : ''; ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 12) ? 'hidden' : ''; ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
					                    </table>
					                </div>
					                <div class="tab-pane fade" id="custom-tabs-for-transjual" role="tabpanel" aria-labelledby="custom-tabs-transjual-tab">
					                    <table id="table-transjual" class="table table-striped">
					                    	<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$transjual = array('23','24','25','26','27');
						                    	if(in_array($row['FiturID'], $transjual)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 24) ? 'hidden' : ''; ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 24) ? 'hidden' : ''; ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 24) ? 'hidden' : ''; ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
					                    </table>
					                </div>
					                <div class="tab-pane fade" id="custom-tabs-for-transkas" role="tabpanel" aria-labelledby="custom-tabs-transkas-tab">
					                    <table id="table-transkas" class="table table-striped">
					                    	<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$transkas = array('47');
						                    	if(in_array($row['FiturID'], $transkas)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
					                    </table>
					                </div>
					                <div class="tab-pane fade" id="custom-tabs-for-manufaktur" role="tabpanel" aria-labelledby="custom-tabs-manufaktur-tab">
					                    <table id="table-manufaktur" class="table table-striped">
					                    	<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$manufaktur = array('28','29','30');
						                    	if(in_array($row['FiturID'], $manufaktur)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
					                    </table>
					                </div>
					                <div class="tab-pane fade" id="custom-tabs-for-warehouse" role="tabpanel" aria-labelledby="custom-tabs-warehouse-tab">
					                    <table id="table-warehouse" class="table table-striped">
					                    	<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$warehouse = array('15','16','17','18','19','20','21','22');
						                    	if(in_array($row['FiturID'], $warehouse)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 17 || $row['FiturID'] == 18 || $row['FiturID'] == 19 || $row['FiturID'] == 20 || $row['FiturID'] == 22) ? 'hidden' : '' ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 17 || $row['FiturID'] == 18 || $row['FiturID'] == 19 || $row['FiturID'] == 20 || $row['FiturID'] == 22) ? 'hidden' : '' ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 17 || $row['FiturID'] == 18 || $row['FiturID'] == 19 || $row['FiturID'] == 20 || $row['FiturID'] == 22) ? 'hidden' : '' ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
					                    </table>
					                </div>
					                <div class="tab-pane fade" id="custom-tabs-for-ampas" role="tabpanel" aria-labelledby="custom-tabs-ampas-tab">
					                    <table id="table-ampas" class="table table-striped">
					                    	<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$ampas = array('48');
						                    	if(in_array($row['FiturID'], $ampas)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
					                    </table>
					                </div>
					                <div class="tab-pane fade" id="custom-tabs-for-akuntansi" role="tabpanel" aria-labelledby="custom-tabs-akuntansi-tab">
					                    <table id="table-akuntansi" class="table table-striped">
					                    	<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$akuntansi = array('57','58','59','60');
						                    	if(in_array($row['FiturID'], $akuntansi)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 60) ? 'hidden' : '' ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 60) ? 'hidden' : '' ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
					                    </table>
					                </div>
					                <div class="tab-pane fade" id="custom-tabs-for-laporan" role="tabpanel" aria-labelledby="custom-tabs-laporan-tab">
					                    <table id="table-laporan" class="table table-striped">
					                    	<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$laporan = array('32','33','34','35','36','38','39','40','41','42','43','44','67');
						                    	if(in_array($row['FiturID'], $laporan)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> hidden/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> hidden/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> hidden/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
					                    </table>
					                </div>
					                <div class="tab-pane fade" id="custom-tabs-for-payroll" role="tabpanel" aria-labelledby="custom-tabs-payroll-tab">
					                    <table id="table-payroll" class="table table-striped">
					                    	<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$payroll = array('48','49','50','51','52','53','54','55','56','65');
						                    	if(in_array($row['FiturID'], $payroll)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 54 || $row['FiturID'] == 55 || $row['FiturID'] == 56) ? 'hidden' : '' ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 54 || $row['FiturID'] == 55 || $row['FiturID'] == 56) ? 'hidden' : '' ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> <?= ($row['FiturID'] == 54 || $row['FiturID'] == 55 || $row['FiturID'] == 56) ? 'hidden' : '' ?>/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
					                    </table>
					                </div>
					                <div class="tab-pane fade" id="custom-tabs-for-flip" role="tabpanel" aria-labelledby="custom-tabs-flip-tab">
					                    <table id="table-flip" class="table table-striped">
					                    	<thead>
								                <tr class="userDatatable-header">
								                    <th style="display: table-cell; width:25%"><span class="userDatatable-title">Nama Fitur</span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Lihat </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tambah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Ubah </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Hapus </span></th>
								                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Cetak </span></th>
								                </tr>
											</thead>
											<tbody style="font-size:14px;">
											<?php 
						                    foreach($data as $row){
						                    	$flip = array('66');
						                    	if(in_array($row['FiturID'], $flip)) {
							                ?>
							                	<tr>
							                		<td><?= $row['FiturName'] ?></td>
							                		<td class="text-center">
							                			<input type="hidden" name="ViewData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="ViewData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['ViewData']) && $row['ViewData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="AddData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="AddData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['AddData']) && $row['AddData']==1) ?'checked="checked"':''; ?> />
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="EditData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="EditData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['EditData']) && $row['EditData']==1) ?'checked="checked"':''; ?> hidden/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="DeleteData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="DeleteData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['DeleteData']) && $row['DeleteData']==1) ?'checked="checked"':''; ?> hidden/>
							                		</td>
							                		<td class="text-center">
							                			<input type="hidden" name="PrintData[<?= $row['FiturID'] ?>]" value="false">
							                			<input type="checkbox" name="PrintData[<?= $row['FiturID'] ?>]" value="true" <?php echo (isset($row['PrintData']) && $row['PrintData']==1) ?'checked="checked"':''; ?> hidden/>
							                		</td>
							                	</tr>
							                <?php
						                		}
						                    }
							                ?>
											</tbody>
					                    </table>
					                </div>
					            </div>
						        
						    </div>

							<br><br><br>
							<div class="row mb-4">
								<div class="col-12">
									<div class="row clearfix">
										<a href="<?=base_url('user/akseslevel')?>" type="button" class="btn btn-sm btn-secondary">Kembali</a>
										<button id="save" type="submit" class="btn btn-sm btn-primary ml-auto">Simpan</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>