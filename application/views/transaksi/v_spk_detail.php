<style type="text/css">
    #ModalTambah {
        overflow-y: scroll !important;
    }

    #ModalSPK {
        overflow-y: scroll !important;
    }
</style>

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
                                <input type="text" class="form-control" id="tgl-transaksi">
                                <span class="input-icon icon-right">
                                    <span data-feather="chevron-down"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php
                    $FiturID = 28; //FiturID di tabel serverfitur
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
                            <a target="_blank" href="<?= base_url('transaksi/spk/cetakdetail/' . base64_encode($dtinduk['IDTransJual'])) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
                                <i class="la la-download"></i> Cetak
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">

                    <div class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless" style="font-size:14px;">
                                <?php if ($dtinduk['TglSlipOrder']) { ?>
                                    <tr>
                                        <td>No Slip Order</td>
                                        <td>: <?= $dtinduk['NoSlipOrder'] ?></td>
                                        <td>Kode Customer </td>
                                        <td>: <?= $dtinduk['KodePerson'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Slip Order </td>
                                        <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TglSlipOrder']))) . ' ' . date('H:i', strtotime($dtinduk['TglSlipOrder'])) ?></td>
                                        <td>Nama Customer </td>
                                        <td>: <?= $dtinduk['NamaUsaha'] ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td>No SPK </td>
                                    <td>: <?= $dtinduk['SPKNomor'] ?></td>
                                    <td>Gudang Asal </td>
                                    <td>: <?= $dtinduk['NamaGudangAsal'] ?></td>
                                </tr>
                                <tr>
                                    <td>Estimasi Selesai </td>
                                    <td>: <?= isset($dtinduk['EstimasiSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($dtinduk['EstimasiSelesai']))) . ' ' . date('H:i', strtotime($dtinduk['EstimasiSelesai'])) : '-' ?></td>
                                    <td>Gudang Tujuan </td>
                                    <td>: <?= $dtinduk['NamaGudangTujuan'] ?></td>
                                </tr>
                                <tr class="userDatatable-header">
                                    <td>SPK Dibuat Oleh</td>
                                    <td>: <?= $dtinduk['SPKDibuatOleh'] ?></td>
                                    <td>Tanggal SPK Dibuat</td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['SPKTanggal']))) . ' ' . date('H:i', strtotime($dtinduk['SPKTanggal'])) ?></td>
                                </tr>
                                <tr class="userDatatable-header">
                                    <td>SPK Disetujui Oleh</td>
                                    <td>: <?= $dtinduk['SPKDisetujuiOleh'] ?></td>
                                    <td>Tanggal SPK Disetujui</td>
                                    <td>: <?= isset($dtinduk['SPKDisetujuiTgl']) ? shortdate_indo(date('Y-m-d', strtotime($dtinduk['SPKDisetujuiTgl']))) . ' ' . date('H:i', strtotime($dtinduk['SPKDisetujuiTgl'])) : '-' ?></td>
                                </tr>
                                <tr class="userDatatable-header">
                                    <td>SPK Diketahui Oleh</td>
                                    <td>: <?= $dtinduk['SPKDiketahuiOleh'] ?></td>
                                    <td>Tanggal SPK Diketahui</td>
                                    <td>: <?= isset($dtinduk['SPKDiketahuiTgl']) ? shortdate_indo(date('Y-m-d', strtotime($dtinduk['SPKDiketahuiTgl']))) . ' ' . date('H:i', strtotime($dtinduk['SPKDiketahuiTgl'])) : '-' ?></td>
                                </tr>
                                <?php //if ($dtinduk['TglSlipOrder']) { 
                                ?>
                                <tr>
                                    <td>Keterangan </td>
                                    <td>: <?= $dtinduk['Deskripsi'] ?></td>
                                    <td>Created By</td>
                                    <td>: <?= $dtinduk['SPKDibuatOleh'] ?></td>
                                </tr>
                                <?php //} 
                                ?>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <?php
                                if ($canEdit == 1) {
                                    if (!($dtinduk['SPKDisetujuiOleh'])) {
                                ?>
                                        <button type="button" id="verifikasispk" class="btn btn-info btn-sm ml-auto">Verifikasi SPK</button>
                                        <br>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Pesanan</div>
                                    <?php if ($canAdd == 1) {
                                        if ($dtinduk['TglSlipOrder'] == null && $dtinduk['StatusProduksi'] == 'WIP') {
                                    ?>
                                            <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add ml-auto">
                                                <i class="la la-plus"></i> Tambah Data
                                            </button>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <table id="table-spkdetail" class="table mb-0 table-borderless">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Transaksi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">No SPK </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Tgl SPK </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Barang Produksi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Qty </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Mulai Produksi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Pc/Np </span></th>
                                        <th style="display: table-cell; width:4%;">#</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <a href="<?= base_url('transaksi/spk') ?>" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ui-dialog" id="ModalTambah" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Tambah Data Produksi</h4>
            </div>
            <form action="<?= base_url('transaksi/spk/simpandetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Barang</label>
                                <select class="form-control form-select" name="KodeBarang" id="KodeBarang" disabled required>
                                    <option></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Ukuran</label>
                                <input type="text" class="form-control" id="ProdUkuran" name="ProdUkuran" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jumlah Daun</label>
                                <input type="number" class="form-control" id="ProdJmlDaun" name="ProdJmlDaun" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Quantity</label>
                                <input type="number" class="form-control" id="Qty" name="Qty" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Mulai Produksi</label>
                                <input type="datetime-local" class="form-control" id="TanggalTransaksi" name="TanggalTransaksi" required>
                                <input type="hidden" class="form-control" id="NoTrans" name="NoTrans">
                                <!-- <input type="hidden" class="form-control" id="NoRefTrManual" name="NoRefTrManual"> -->
                                <input type="hidden" class="form-control" id="NoRefTrSistem" name="NoRefTrSistem" value="<?= $dtinduk['IDTransJual'] ?>">
                                <input type="hidden" class="form-control" id="Gudang" name="Gudang" value="<?= $dtinduk['KodeGudang'] ?>">
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

<div class="modal fade ui-dialog" id="ModalSPK" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Verifikasi SPK</h4>
            </div>
            <form action="<?= base_url('transaksi/slip_order/simpanspk') ?>" method="post" id="form-simpan-spk">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nomor SPK</label>
                                <input type="text" class="form-control" id="SPKNomor" name="SPKNomor">
                                <input type="hidden" class="form-control" id="SPKLama" name="SPKLama">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Estimasi Selesai</label>
                                <input type="hidden" class="form-control" id="IDTransJual" name="IDTransJual" value="<?= $dtinduk['IDTransJual'] ?>">
                                <input type="datetime-local" class="form-control" id="EstimasiSelesai" name="EstimasiSelesai">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal SPK Dibuat</label>
                                <input type="datetime-local" class="form-control" id="SPKTanggal" name="SPKTanggal">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">SPK Disetujui Oleh</label>
                                <input type="text" class="form-control" id="SPKDisetujuiOleh" name="SPKDisetujuiOleh">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal SPK Disetujui</label>
                                <input type="datetime-local" class="form-control" id="SPKDisetujuiTgl" name="SPKDisetujuiTgl">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">SPK Diketahui Oleh</label>
                                <input type="text" class="form-control" id="SPKDiketahuiOleh" name="SPKDiketahuiOleh">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal SPK Diketahui</label>
                                <input type="datetime-local" class="form-control" id="SPKDiketahuiTgl" name="SPKDiketahuiTgl">
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