<div class="social-dash-wrap">
	<div class="row">
		<div class="col-lg-12">

			<div class="breadcrumb-main">
				<h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
				<div class="breadcrumb-action justify-content-center flex-wrap">
					<?php
                        $FiturID = 67; //FiturID di tabel serverfitur
                        $canPrint = 0;
                        $print = [];
                        foreach ($this->session->userdata('fiturprint') as $key => $value) {
                            $print[$key] = $value;
                            if ($key == $FiturID && $value == 1) {
                                $canPrint = 1;
                            }
                        }
                    ?>
                    <?php if ($canPrint == 1) { ?>
                    <div class="dropdown action-btn">
                    	<a target="_blank" href="<?= base_url('laporan/buku_besar?tgl='.$tglawal.'+-+'.$tglakhir.'&jenis=cetak') ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="btn-cetak">
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
							<table id="table-bukubesar" class="table mb-0 table-borderless">
								<thead>
									<tr>
					                    <td colspan="3">
                                            <form method="get" action="<?= base_url('laporan/buku_besar') ?>" id="formfilter">
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
                                            </form>
                                        </td>
					                </tr>
					                <tr class="userDatatable-header">
					                    <th class="text-center" style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
					                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Tanggal Transaksi </span></th>
					                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">No Transaksi </span></th>
					                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">No Referensi </span></th>
					                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Uraian </span></th>
					                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Debet </span></th>
					                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Kredit </span></th>
					                    <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Saldo </span></th>
					                </tr>
								</thead>
								<tbody style="font-size:14px;">
                                <?php
                                if ($data) {
                                    $totaldebet = 0;
                                    $totalkredit = 0;
                                    foreach ($data as $row) {
                                        if ($row['Item']) { ?>
                                            <tr>
                                                <th></th>
                                                <th><?= $row['KodeAkun'] ?></th>
                                                <th colspan="2"><?= $row['NamaAkun'] ?></th>
                                                <th colspan="3">Saldo sebelum tanggal <?= date_indo($t_awal) ?> :</th>
                                                <th class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['SaldoAwal'], 2)) ?></th>
                                            </tr>
                                            <?php
                                                $no = 1;
                                                foreach ($row['Item'] as $row2) {
                                                    echo '<tr>';
                                                    echo '<td>'. $no .'</td>';
                                                    echo '<td>'. shortdate_indo(date('Y-m-d', strtotime($row2['TglTransJurnal']))) .'</td>';
                                                    echo '<td>'. $row2['IDTransJurnal'] .'</td>';
                                                    echo '<td>'. $row2['NoRefTrans'] .'</td>';
                                                    echo '<td>'. $row2['NarasiJurnal'] .'</td>';
                                                    echo '<td class="text-right">'. str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row2['Debet'], 2)) .'</td>';
                                                    echo '<td class="text-right">'. str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row2['Kredit'], 2)) .'</td>';
                                                    echo '<td class="text-right">'. str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row2['Saldo'], 2)) .'</td>';
                                                    echo '</tr>';

                                                    $no++;
                                                    $totaldebet += $row2['Debet'];
                                                    $totalkredit += $row2['Kredit'];
                                                }
                                            ?>
                                            <tr>
                                                <th colspan="4"></th>
                                                <th colspan="3">Saldo akhir sampai tanggal <?= date_indo($t_akhir) ?> :</th>
                                                <th class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['SaldoAkhir'], 2)) ?></th>
                                            </tr>
                                            <tr><td colspan="8"></td></tr>
                                <?php
                                        } else { ?>
                                            <tr>
                                                <th></th>
                                                <th><?= $row['KodeAkun'] ?></th>
                                                <th colspan="2"><?= $row['NamaAkun'] ?></th>
                                                <th colspan="3">Saldo akhir sampai tanggal <?= date_indo($t_akhir) ?> :</th>
                                                <th class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['SaldoAkhir'], 2)) ?></th>
                                            </tr>
                                            <!-- <tr><td colspan="8"></td></tr> -->
                                <?php
                                        }
                                    }
                                ?>
                                    <tr>
                                        <th class="text-right" colspan="5">Total</th>
                                        <th class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totaldebet, 2)) ?></th>
                                        <th class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalkredit, 2)) ?></th>
                                        <th></th>
                                    </tr>
                                <?php
                                } else {
                                ?>
                                    <tr>
                                        <td class="text-center" colspan="8">Data Tidak Ada.</td>
                                    </tr>
                                <?php
                                }
                                ?>
								</tbody>
							</table>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>