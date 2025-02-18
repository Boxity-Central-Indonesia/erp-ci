<div class="social-dash-wrap">
	<div class="row">
		<div class="col-lg-12">

			<div class="breadcrumb-main">
				<h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
				<div class="breadcrumb-action justify-content-center flex-wrap">
					<?php
                        $FiturID = 34; //FiturID di tabel serverfitur
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
                    	<a target="_blank" href="<?= base_url('laporan/arus_kas/cetak/'.$tglawal.'/'.$tglakhir) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="btn-cetak">
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
							<table id="table-lappenjualan" class="table table-striped">
								<thead>
									<tr>
					                    <td colspan="2">
					                    	<form  method="get" action="<?= base_url('laporan/arus_kas') ?>" id="myform">
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
					                    <th style="display: table-cell; width:4%">#</th>
					                    <th style="display: table-cell;">Arus Kas </th>
					                    <th style="display: table-cell;">Nominal </th>
					                    <th style="display: table-cell;">Subtotal </th>
					                    <th style="display: table-cell;">Total </th>
					                </tr>
								</thead>
								<tbody style="font-size:14px;">
									<!-- arus kas operasional -->
									<tr style="font-weight: bold;">
										<td class="text-right">1</td>
										<td>Arus Kas Operational</td>
										<td colspan="3"></td>
									</tr>

									<?php
										$penerimaan_op = 0;
										if (count($masuk_op) > 0):
									?>
										<tr style="font-weight: bold;">
											<td class="text-right"></td>
											<td>Penerimaan kas dari:</td>
											<td colspan="3"></td>
										</tr>

										<?php foreach ($masuk_op as $row): ?>
											<tr>
												<td></td>
												<td>&nbsp;&nbsp;&nbsp;<?= $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' ?></td>
												<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) ?></td>
												<td colspan="2"></td>
											</tr>
										<?php
											$penerimaan_op += $row['Nominal'];
											endforeach
										?>
									<?php endif ?>

									<tr style="font-weight: bold;">
										<td></td>
										<td>&nbsp;&nbsp;&nbsp;Jumlah penerimaan kas:</td>
										<td></td>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_op, 2)) ?></td>
										<td></td>
									</tr>

									<?php
										$pengeluaran_op = 0;
										if (count($keluar_op) > 0):
									?>
										<tr style="font-weight: bold;">
											<td class="text-right"></td>
											<td>Pengeluaran kas untuk:</td>
											<td colspan="3"></td>
										</tr>

										<?php foreach ($keluar_op as $row): ?>
											<tr>
												<td></td>
												<td>&nbsp;&nbsp;&nbsp;<?= $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' ?></td>
												<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) ?></td>
												<td colspan="2"></td>
											</tr>
										<?php
											$pengeluaran_op += $row['Nominal'];
											endforeach
										?>
									<?php endif ?>

									<tr style="font-weight: bold;">
										<td></td>
										<td>&nbsp;&nbsp;&nbsp;Jumlah pengeluaran kas:</td>
										<td></td>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($pengeluaran_op, 2)) ?></td>
										<td></td>
									</tr>
									<tr style="font-weight: bold;">
										<td class="text-right"></td>
										<td>Arus Kas Operational</td>
										<td colspan="2"></td>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_op - $pengeluaran_op, 2)) ?></td>
									</tr>
									<tr>
										<td colspan="5"></td>
									</tr>
									<!-- arus kas operasional -->

									<!-- arus kas investasi -->
									<tr style="font-weight: bold;">
										<td class="text-right">2</td>
										<td>Arus Kas Investasi</td>
										<td colspan="3"></td>
									</tr>

									<?php
										$penerimaan_inv = 0;
										if (count($masuk_inv) > 0):
									?>
										<tr style="font-weight: bold;">
											<td class="text-right"></td>
											<td>Penerimaan kas dari:</td>
											<td colspan="3"></td>
										</tr>

										<?php foreach ($masuk_inv as $row): ?>
											<tr>
												<td></td>
												<td>&nbsp;&nbsp;&nbsp;<?= $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' ?></td>
												<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) ?></td>
												<td colspan="2"></td>
											</tr>
										<?php
											$penerimaan_inv += $row['Nominal'];
											endforeach
										?>
									<?php endif ?>

									<tr style="font-weight: bold;">
										<td></td>
										<td>&nbsp;&nbsp;&nbsp;Jumlah penerimaan kas:</td>
										<td></td>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_inv, 2)) ?></td>
										<td></td>
									</tr>

									<?php
										$pengeluaran_inv = 0;
										if (count($keluar_inv) > 0):
									?>
										<tr style="font-weight: bold;">
											<td class="text-right"></td>
											<td>Pengeluaran kas untuk:</td>
											<td colspan="3"></td>
										</tr>

										<?php foreach ($keluar_inv as $row): ?>
											<tr>
												<td></td>
												<td>&nbsp;&nbsp;&nbsp;<?= $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' ?></td>
												<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) ?></td>
												<td colspan="2"></td>
											</tr>
										<?php
											$pengeluaran_inv += $row['Nominal'];
											endforeach
										?>
									<?php endif ?>

									<tr style="font-weight: bold;">
										<td></td>
										<td>&nbsp;&nbsp;&nbsp;Jumlah pengeluaran kas:</td>
										<td></td>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($pengeluaran_inv, 2)) ?></td>
										<td></td>
									</tr>
									<tr style="font-weight: bold;">
										<td class="text-right"></td>
										<td>Arus Kas Investasi</td>
										<td colspan="2"></td>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_inv - $pengeluaran_inv, 2)) ?></td>
									</tr>
									<tr>
										<td colspan="5"></td>
									</tr>
									<!-- arus kas investasi -->

									<!-- arus kas pembiayaan -->
									<tr style="font-weight: bold;">
										<td class="text-right">3</td>
										<td>Arus Kas Pembiayaan</td>
										<td colspan="3"></td>
									</tr>

									<?php
										$penerimaan_bi = 0;
										if (count($masuk_bi) > 0):
									?>
										<tr style="font-weight: bold;">
											<td class="text-right"></td>
											<td>Penerimaan kas dari:</td>
											<td colspan="3"></td>
										</tr>

										<?php foreach ($masuk_bi as $row): ?>
											<tr>
												<td></td>
												<td>&nbsp;&nbsp;&nbsp;<?= $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' ?></td>
												<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) ?></td>
												<td colspan="2"></td>
											</tr>
										<?php
											$penerimaan_bi += $row['Nominal'];
											endforeach
										?>
									<?php endif ?>

									<tr style="font-weight: bold;">
										<td></td>
										<td>&nbsp;&nbsp;&nbsp;Jumlah penerimaan kas:</td>
										<td></td>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_bi, 2)) ?></td>
										<td></td>
									</tr>

									<?php
										$pengeluaran_bi = 0;
										if (count($keluar_bi) > 0):
									?>
										<tr style="font-weight: bold;">
											<td class="text-right"></td>
											<td>Pengeluaran kas untuk:</td>
											<td colspan="3"></td>
										</tr>

										<?php foreach ($keluar_bi as $row): ?>
											<tr>
												<td></td>
												<td>&nbsp;&nbsp;&nbsp;<?= $row['NoRefTrans'] . ': (' . $row['KodeAkun'] . '/' . $row['NamaAkun'] . ')' ?></td>
												<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Nominal'], 2)) ?></td>
												<td colspan="2"></td>
											</tr>
										<?php
											$pengeluaran_bi += $row['Nominal'];
											endforeach
										?>
									<?php endif ?>

									<tr style="font-weight: bold;">
										<td></td>
										<td>&nbsp;&nbsp;&nbsp;Jumlah pengeluaran kas:</td>
										<td></td>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($pengeluaran_bi, 2)) ?></td>
										<td></td>
									</tr>
									<tr style="font-weight: bold;">
										<td class="text-right"></td>
										<td>Arus Kas Pembiayaan</td>
										<td colspan="2"></td>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($penerimaan_bi - $pengeluaran_bi, 2)) ?></td>
									</tr>
									<tr>
										<td colspan="5"></td>
									</tr>
									<!-- arus kas pembiayaan -->

									<tr style="font-weight: bold;">
										<td></td>
										<td>Kenaikan Kas</td>
										<td colspan="2"></td>
										<?php
										$kenaikan = (($penerimaan_op - $pengeluaran_op) + ($penerimaan_inv - $pengeluaran_inv) + ($penerimaan_bi - $pengeluaran_bi));
										?>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($kenaikan, 2)) ?></td>
									</tr>
									<tr style="font-weight: bold;">
										<td></td>
										<td>Saldo Kas Awal</td>
										<td colspan="2"></td>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldoawal, 2)) ?></td>
									</tr>
									<tr style="font-weight: bold;">
										<td></td>
										<td>Saldo Kas Akhir</td>
										<td colspan="2"></td>
										<td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($saldoawal + $kenaikan, 2)) ?></td>
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