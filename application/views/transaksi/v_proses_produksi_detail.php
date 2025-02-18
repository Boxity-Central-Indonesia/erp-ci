<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
    .hiddenRow {
        padding: 0 !important;
    }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php
                    if ($dtinduk['StatusProduksi'] == 'SELESAI' && $status_jurnal == 'off' && $dtinduk['IDTransJurnal'] != null) {
                        if ($dtinduk['NominalTransaksi'] > $totaljurnaldebet || $dtinduk['NominalTransaksi'] > $totaljurnalkredit) {
                    ?>
                    <div class="action-btn">
                        <a href="<?= base_url('transaksi/transaksi_kas/jurnalmanual/' . base64_encode('prosesprod') . '/' . base64_encode($dtinduk['IDTransJurnal']) . '/' . base64_encode($NoRefTrSistem) . '/' . base64_encode('proses_produksi/detail')) ?>" class="btn btn-info btn-sm btn-add" type="button">
                            <i class="las la-journal-whills"></i> Jurnalkan
                        </a>
                    </div>
                    <?php
                        }
                    }
                    ?>
                    <div class="action-btn" hidden>

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
                    <?php if ($canPrint == 1 && $dtinduk['StatusProduksi'] == 'SELESAI') { ?>
                    <div class="dropdown action-btn" hidden>
                        <a target="_blank" href="<?= base_url('transaksi/proses_produksi/cetakdetail/' . base64_encode($dtinduk['NoTrans'])) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
                            <i class="la la-download"></i> Cetak
                        </a>
                    </div>
                    <?php } ?>
                    <?php if ($canAdd == 1) { ?>
                    <div class="action-btn" hidden>
                        <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add">
                            <i class="la la-plus"></i> Tambah Data
                        </button>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless" style="font-size:14px;">
                                <tr>
                                    <td>No SPK </td>
                                    <td>: <?= $dtinduk['SPKNomor'] ?></td>
                                    <td>Gudang Asal </td>
                                    <td>: <?= $dtinduk['NamaGudangAsal'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal SPK </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['SPKTanggal']))) . ' ' . date('H:i', strtotime($dtinduk['SPKTanggal'])) ?></td>
                                    <td>Gudang Tujuan </td>
                                    <td>: <?= $dtinduk['NamaGudangTujuan'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Mulai Produksi</td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalTransaksi']))) ?></td>
                                    <td>Tanggal Selesai Produksi</td>
                                    <td>: <?= isset($dtinduk['ProdTglSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($dtinduk['ProdTglSelesai']))) : '-' ?></td>
                                </tr>
                                <tr hidden>
                                    <td>Total Bahan Baku</td>
                                    <td>: <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($bahanbaku['TotalBahanBaku'], 2)) ?> kilogram</td>
                                    <td>Presentase Bahan</td>
                                    <td>: <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($presentase, 2)) ?>%</td>
                                </tr>
                                <tr hidden>
                                    <td>Susut</td>
                                    <td>: <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($susut, 2)) ?> kilogram</td>
                                    <td>Modal Bahan</td>
                                    <td>: <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($bahanbaku['ModalBahan'], 2)) ?></td>
                                </tr>
                                <tr>
                                    <td>Created By</td>
                                    <td>: <?= $dtinduk['SPKDibuatOleh'] ?></td>
                                    <td colspan="2"></td>
                                </tr>
                            </table>
                            <br>
                            <div>Daftar Item Pesanan</div>
                            <table id="table-prosesproddetails" class="table mb-0 table-borderless">
                                <thead>
                                    <tr hidden>
                                        <td colspan="2">
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
                                    </tr>
                                    <tr class="userDatatable-header">
                                        <th class="text-center" style="display: table-cell; width:3%"></th>
                                        <th class="text-center" style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Kode Barang </span></th>
                                        <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Nama Barang </span></th>
                                        <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Berat Kotor </span></th>
                                        <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Berat Bersih </span></th>
                                        <th class="text-center" style="display: table-cell;"><span class="userDatatable-title">Status </span></th>
                                        <th class="text-center" style="display: table-cell; width:4%;">#</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                    <?php if ($model) {
                                        $totalbkotor = 0;
                                        $totalbbersih = 0;
                                        foreach ($model as $no => $row) { ?>
                                            <tr>
                                                <td data-toggle="collapse" data-target="#demo<?= $no+1 ?>" class="accordion-toggle"><button onclick="changeIcon(this)" class="btn btn-default btn-xs"><i class="fa fa-caret-right"></i></button></td>
                                                <td><?= $no+1 ?></td>
                                                <td><?= $row['KodeManual'] ?></td>
                                                <td><?= $row['NamaBarang'] ?></td>
                                                <td class="text-center"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['BeratKotor'], 2)) ?> kilogram</td>
                                                <td class="text-center"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Qty'], 2)) ?> kilogram</td>
                                                <td class="text-center"><?= isset($row['Qty']) && $row['Qty'] > 0 ? 'SELESAI' : 'WIP' ?></td>
                                                <td>
                                                    <a class="btnfitur" href="<?= base_url('transaksi/proses_produksi/subdetail/' . base64_encode($row['NoTrans']) . '/' . base64_encode($row['NoUrut'])) ?>" type="button" title="Sub Detail Proses Produksi"><span class="fa fa-list" aria-hidden="true"></span></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="11" class="hiddenRow">
                                                    <div class="accordian-body collapse" id="demo<?= $no+1 ?>">
                                                        <table width="100%;">
                                                            <tr style="background-color:#e3e4e6;">
                                                                <!-- <th class="text-center">Pc/Np</th> -->
                                                                <?php foreach ($jenisaktivitas as $col) { ?>
                                                                    <th class="text-center"><?= $col['JenisAktivitas'] ?></th>
                                                                <?php } ?>
                                                                <!-- <th class="text-center">Berat Kotor(kg)</th> -->
                                                                <!-- <th class="text-center">Berat Bersih(kg)</th> -->
                                                            </tr>
                                                            <tr style="background-color:#f0f1f5;">
                                                                <!-- <td><?= $row['KodeManual'] ?></td> -->
                                                                <?php foreach ($jenisaktivitas as $col) {
                                                                    $namapeg = $row[$col['JenisAktivitas']];
                                                                    $namapegawai = ($namapeg != null) ? implode(', ', $namapeg) : '-';
                                                                    $class = ($namapeg != null) ? 'class="text-left"' : 'class="text-center"';
                                                                    echo '<td ' . $class . '>' . $namapegawai . '</td>';
                                                                } ?>
                                                                <!-- <td class="text-center"><?= ($row['Qty'] != null) ? $row['Qty'] : 0 ?></td> -->
                                                                <!-- <td class="text-center"><?= ($row['Qty'] != null) ? $row['Qty'] : 0 ?></td> -->
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php
                                    $totalbkotor += $row['BeratKotor'];
                                    $totalbbersih += $row['Qty'];
                                    }
                                    } else {
                                        echo '<tr><td class="text-center" style="font-weight:bold;" colspan="11">Data tidak ditemukan.</td></tr>';
                                    } ?>
                                </tbody>
                                <tfoot style="font-size:14px;">
                                    <td colspan="4" class="text-right">Total </td>
                                    <td class="text-center"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalbkotor, 2)) ?> kilogram</td>
                                    <td class="text-center"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalbbersih, 2)) ?> kilogram</td>
                                    <td colspan="2"></td>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="row clearfix">
                        <a href="<?=base_url('transaksi/proses_produksi')?>" class="btn btn-sm btn-secondary">Kembali</a>
                        <?php if ($dtinduk['StatusProduksi'] != 'SELESAI') { ?>
                            <a href="javascript:(0)" type="button" id="btn-selesai" data-kode="<?= $NoRefTrSistem ?>" data-kode2="<?= $dtinduk['NoTrans'] ?>" class="btn btn-sm btn-success ml-auto">Selesai</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
