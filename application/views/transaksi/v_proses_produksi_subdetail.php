<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
    #ModalSelesai { overflow-y:scroll !important; }

    .btn-kcl, .btn-group-sm > .btn {
        padding-top: 0;
        padding-right: 0.5rem;
        padding-bottom: 0;
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

    .btn {
        width: fit-content;
        display: inline;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
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
                    <?php if ($canPrint == 1) { ?>
                    <div class="dropdown action-btn" hidden>
                        <a target="_blank" href="<?= base_url('transaksi/proses_produksi/cetakdetail/' . base64_encode($NoTrans)) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
                            <i class="la la-download"></i> Cetak
                        </a>
                        <!-- <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="la la-download"></i> Cetak
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <span class="dropdown-item">Cetak Dengan</span>
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
                <!-- <div class="card-header color-dark fw-500">
                </div> -->
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless" style="font-size:14px;">
                                <tr>
                                    <td>No Transaksi </td>
                                    <td>: <?= $dtinduk['NoTrans'] ?></td>
                                    <td>No SPK </td>
                                    <td>: <?= $dtinduk['SPKNomor'] ?></td>
                                </tr>
                                <tr>
                                    <td>Barang Produksi </td>
                                    <td>: <?= $dtinduk['NamaBarang'] ?></td>
                                    <td>Tanggal SPK </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['SPKTanggal']))) . ' ' . date('H:i', strtotime($dtinduk['SPKTanggal'])) ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Mulai Produksi </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalTransaksi'])) ?></td>
                                    <td>Tanggal Selesai Produksi </td>
                                    <td>: <?= $ProdTglSelesai ?></td>
                                </tr>
                                <tr>
                                    <td>Gudang Asal </td>
                                    <td>: <?= $dtinduk['NamaGudangAsal'] ?></td>
                                    <td>Gudang Tujuan </td>
                                    <td>: <?= $dtinduk['NamaGudangTujuan'] ?></td>
                                </tr>
                                <tr>
                                    <td>Ukuran </td>
                                    <td>: <?= $dtinduk['ProdUkuran'] ?></td>
                                    <td>Jumlah Daun </td>
                                    <td>: <?= $dtinduk['ProdJmlDaun'] ?></td>
                                </tr>
                                <tr>
                                    <td>Berat Kotor</td>
                                    <td>: <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['BeratKotor'], 2)) ?> kilogram</td>
                                    <td>Berat Bersih</td>
                                    <td>: <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['Qty'], 2)) ?> kilogram</td>
                                </tr>
                                <tr>
                                    <td>Keterangan </td>
                                    <td>: <?= $dtinduk['Deskripsi'] ?></td>
                                    <td colspan="2"></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Aktivitas Proses Produksi</div>
                                    <?php if ($canAdd == 1 && $dtinduk['ProdTglSelesai'] == null && $dtinduk['Qty'] == 0) { ?>
                                        <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add ml-auto">
                                            <i class="la la-plus"></i> Tambah Data
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>
                            <table id="table-prosesprodsubdetail" class="table mb-0 table-borderless">
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
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Tgl Aktivitas </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Aktivitas </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Batas Bawah </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Batas Atas </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jumlah Daun </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Pegawai </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Biaya </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Keterangan </span></th>
                                        <?php
                                        if ($canEdit == 1 || $canDelete == 1) {
                                            if ($dtinduk['ProdTglSelesai'] == null && $dtinduk['Qty'] == 0) {
                                        ?>
                                            <th style="display: table-cell; width:5%;">#</th>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="col-sm-12">
                        <div class="row clearfix">
                            <a href="<?=base_url('transaksi/proses_produksi/detail/') . base64_encode($NoRefTrSistem) ?>" class="btn btn-sm btn-secondary">Kembali</a>
                            <?php if ($dtinduk['Qty'] == 0) { ?>
                                <a href="#" type="button" id="gagal" class="btn btn-sm btn-danger ml-auto" hidden data-kode="<?= $NoTrans ?>" data-kode2="<?= $NoRefTrSistem ?>">Gagal</a>
                                &nbsp;&nbsp;
                                <a href="javascript:(0)" type="button" id="selesai" class="btn btn-sm btn-success ml-auto">Selesai</a>
                            <?php } ?>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data Proses Produksi</h4>
            </div>
            <form action="<?= base_url('transaksi/proses_produksi/simpandetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Aktivitas</label>
                                <input type="date" class="form-control" id="TglAktivitas" name="TglAktivitas">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Aktivitas</label>
                                <input type="hidden" class="form-control" id="NoTrans" name="NoTrans" value="<?= $NoTrans ?>">
                                <input type="hidden" class="form-control" id="NoUrut" name="NoUrut" value="<?= $NoUrut ?>">
                                <input type="hidden" class="form-control" id="NoTrAktivitas" name="NoTrAktivitas">
                                <select class="form-control form-select select2" name="KodeAktivitas" id="KodeAktivitas" disabled required>
                                    <option value="" selected>Pilih Jenis Aktivitas</option>
                                    <?php if($dtaktivitas){
                                        foreach ($dtaktivitas as $key) {
                                            if ($key['JmlDaun']) {
                                                echo '<option value="' . $key['KodeAktivitas'] . '">' . $key['JenisAktivitas'] . ' | ' . $key['BatasBawah'] . '-' . $key['BatasAtas'] . ' | ' . $key['JmlDaun'] . '</option>';
                                            } else {
                                                echo '<option value="' . $key['KodeAktivitas'] . '">' . $key['JenisAktivitas'] . '</option>';
                                            }
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Biaya</label>
                                <input type="hidden" class="form-control" id="JenisAktivitas" name="JenisAktivitas">
                                <input type="text" class="form-control" id="Biaya" name="Biaya" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Pegawai</label>
                                <select class="form-control form-select select2" id="KodePegawai" name="KodePegawai" disabled required>
                                    <option value="" selected>Pilih Pegawai</option>
                                    <?php if ($dtpegawai){
                                        foreach ($dtpegawai as $key) {
                                            echo'<option value="' . $key['KodePegawai'] . '">' . $key['NamaPegawai'] . '</option>';
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

<div class="modal fade ui-dialog" id="ModalSelesai" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Selesai Proses Produksi</h4>
            </div>
            <form action="<?= base_url('transaksi/proses_produksi/selesai_per_item') ?>" method="post" id="form-selesai">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Berat Kotor</label>
                                <input type="text" class="form-control" id="BeratKotor" name="BeratKotor" value="<?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['BeratKotor'], 2)) ?>" readonly>
                                <input type="hidden" class="form-control" id="Total" name="Total" value="<?= $dtinduk['Total'] ?>" readonly>
                                <input type="hidden" class="form-control" id="NoTrans" name="NoTrans" value="<?= $NoTrans ?>">
                                <input type="hidden" class="form-control" id="NoUrut" name="NoUrut" value="<?= $NoUrut ?>">
                                <input type="hidden" class="form-control" id="KodeBarang" name="KodeBarang" value="<?= $dtinduk['KodeBarang'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="Qty">Berat Bersih</label>
                                <input type="text" class="form-control" name="Qty" id="Qty" required>
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