<style type="text/css">
    #ModalTambah {
        overflow-y: scroll !important;
    }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php
                    $FiturID = 27; //FiturID di tabel serverfitur
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
                            <!-- <a target="_blank" href="<?= base_url('transaksi/terima_piutang/cetakdetail/' . base64_encode($dtiinduk['KodePerson'])) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
                            <i class="la la-download"></i> Cetak
                        </a> -->
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

                    <div class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless" style="font-size:14px;">
                                <tr>
                                    <td>No Ref Terima Piutang </td>
                                    <td>: <?= $dtinduk['NoRef_Manual'] ?></td>
                                    <td>Kode Customer </td>
                                    <td>: <?= $dtinduk['KodePerson'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Terima Piutang </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalTransaksi'])) ?></td>
                                    <td>Nama Customer </td>
                                    <td>: <?= $dtinduk['NamaUsaha'] ?></td>
                                </tr>
                                <tr>
                                    <td>Keterangan </td>
                                    <td>: <?= $dtinduk['Uraian'] ?></td>
                                    <td colspan="2"></td>
                                </tr>
                            </table>
                            <br>
                            <table id="table-terimapiutangtambah" class="table mb-0 table-borderless">
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
                                    <div class="col-sm-12">
                                        <div>Daftar Detail</div>
                                    </div>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Transaksi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">No Referensi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Tanggal Penjualan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Total Tagihan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Total Dibayar </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Sisa Tagihan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Dibayar Sekarang </span></th>
                                        <?php
                                        if ($canEdit == 1 || $canDelete == 1) {
                                            // if ($dtinduk['StatusProses'] == 'PO') {
                                        ?>
                                            <th style="display: table-cell; width:15%;">#</th>
                                        <?php
                                            // }
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
                    <div class="row clearfix">
                        <a href="#" type="button" id="batalbayar" class="btn btn-sm btn-danger" data-kode="<?= $dtinduk['NoRef_Manual'] ?>">Batal</a>
                        <a href="#" type="button" id="simpan-bayar" class="btn btn-primary btn-sm ml-auto" data-kode="<?= $dtinduk['NoRef_Manual'] ?>">Simpan</a>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Terima Piutang</h4>
            </div>
            <form action="<?= base_url('transaksi/terima_piutang/simpandetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Kode Transaksi</label>
                                <input type="text" class="form-control" id="NoRef_Sistem" name="NoRef_Sistem" readonly>
                                <input type="hidden" class="form-control" id="NoTransKas" name="NoTransKas">
                                <input type="hidden" class="form-control" id="KodePerson" name="KodePerson" value="<?= $dtinduk['KodePerson'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">No Referensi</label>
                                <input type="text" class="form-control" id="NoRef_Manual" name="NoRef_Manual" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Penjualan</label>
                                <input type="text" class="form-control" id="TanggalPenjualan" name="TanggalPenjualan" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Total Tagihan</label>
                                <input type="text" class="form-control" id="TotalTagihan" name="TotalTagihan" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Total Dibayar</label>
                                <input type="text" class="form-control" id="TotalBayar" name="TotalBayar" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Sisa Tagihan</label>
                                <input type="text" class="form-control" id="SisaTagihan" name="SisaTagihan" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Dibayar Sekarang</label>
                                <input type="text" class="form-control" id="DibayarSekarang" name="DibayarSekarang" required>
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