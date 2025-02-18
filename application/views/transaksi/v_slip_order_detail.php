<style type="text/css">
    #ModalTambah {
        overflow-y: scroll !important;
    }

    #ModalPenjualan {
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
                    if ($nominaltransaksi > 0) {
                        if ($nominaltransaksi > $totaljurnaldebet || $nominaltransaksi > $totaljurnalkredit) {
                    ?>
                            <div class="action-btn" hidden>
                                <a href="<?= base_url('transaksi/transaksi_kas/jurnalmanual/' . base64_encode('trans_jual') . '/' . base64_encode($idtransjurnal) . '/' . base64_encode($IDTransJual) . '/' . base64_encode('slip_order/detail')) ?>" class="btn btn-info btn-sm btn-add" type="button">
                                    <i class="las la-journal-whills"></i> Jurnalkan
                                </a>
                            </div>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    $FiturID = 23; //FiturID di tabel serverfitur
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
                            <a target="_blank" href="<?= base_url('transaksi/slip_order/cetakdetail/' . base64_encode($IDTransJual)) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
                                <i class="la la-download"></i> Cetak
                            </a>
                        </div>
                        <div class="dropdown action-btn" hidden>
                            <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="la la-download"></i> Cetak
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                <span class="dropdown-item">Pilih Cetak</span>
                                <div class="dropdown-divider"></div>
                                <a target="_blank" href="<?= base_url('transaksi/slip_order/cetakdetail/' . base64_encode($IDTransJual)) ?>" class="dropdown-item" type="button">
                                    <i class="la la-download"></i> Cetak SO
                                </a>
                                <?php if ($dtinduk['DiskonBawah'] != null) { ?>
                                    <a class="dropdown-item" href="#" id="btnspk" data-kode="<?= $IDTransJual ?>" title="">
                                        <i class="la la-check-circle"></i> Cetak SPK
                                    </a>
                                <?php } ?>
                            </div>
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
                                <tr class="userDatatable-header">
                                    <td>Kode Slip Order </td>
                                    <td>: <?= $dtinduk['IDTransJual'] ?></td>
                                    <td>Kode Customer </td>
                                    <td>: <?= $dtinduk['KodePerson'] ?></td>
                                </tr>
                                <tr class="userDatatable-header">
                                    <td>Nomor Slip Order </td>
                                    <td>: <?= $dtinduk['NoSlipOrder'] ?></td>
                                    <td>Nama Customer </td>
                                    <td>: <?= $dtinduk['NamaUsaha'] ?></td>
                                </tr>
                                <tr class="userDatatable-header">
                                    <td>Tanggal Slip Order </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TglSlipOrder']))) . ' ' . date('H:i', strtotime($dtinduk['TglSlipOrder'])) ?></td>
                                    <td>Created By</td>
                                    <td>: <?= $dtinduk['SODibuatOleh'] ?></td>
                                </tr>
                                <tr class="userDatatable-header">
                                    <td>Estimasi Selesai </td>
                                    <td>: <?= isset($dtinduk['EstimasiSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($dtinduk['EstimasiSelesai']))) . ' ' . date('H:i', strtotime($dtinduk['EstimasiSelesai'])) : '' ?></td>
                                    <td>Tanggal Jatuh Tempo </td>
                                    <td>: <?= $jatuhtempo ?></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Detail</div>
                                    <?php
                                    if ($canAdd == 1) {
                                        if ($dtinduk['EstimasiSelesai'] == null) {
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
                            <table id="table-sodetail" class="table mb-0 table-borderless">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kategori </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Qty </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Ukuran </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jumlah Daun </span></th>
                                        <?php
                                        if ($canEdit == 1 || $canDelete == 1) {
                                            if ($dtinduk['EstimasiSelesai'] == null) {
                                        ?>
                                                <th style="display: table-cell; width:15%;">#</th>
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
                    <?php if (isset($dtinduk['DiskonBawah'])) { ?>
                        <a href="<?= base_url('transaksi/trans_jual') ?>" class="btn btn-sm btn-secondary">Kembali</a>
                    <?php } else { ?>
                        <div class="col-sm-12">
                            <div class="row clearfix">
                                <a href="#" type="button" hidden id="" class="btn btn-sm btn-secondary bataljual" data-kode="<?= $dtinduk['IDTransJual'] ?> ">Batal</a>
                                <a href="<?= base_url('transaksi/trans_jual') ?>" class="btn btn-sm btn-secondary">Kembali</a>
                                <?php if ($countItem == 1 && $dtinduk['EstimasiSelesai'] == null) { ?>
                                    <a href="#" type="button" class="btn btn-sm btn-primary ml-auto simpanjual">Simpan</a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ui-dialog" id="ModalTambah" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Pembelian Detail</h4>
            </div>
            <form action="<?= base_url('transaksi/slip_order/simpandetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Barang</label>
                                <input type="hidden" class="form-control" id="IDTransJual" name="IDTransJual" value="<?= $IDTransJual ?>">
                                <input type="hidden" class="form-control" id="NoUrut" name="NoUrut">
                                <select class="form-control form-select select2" name="KodeBarang" id="KodeBarang" disabled required>
                                    <option value="" selected>Pilih Barang</option>
                                    <?php if ($dtbarang) {
                                        foreach ($dtbarang as $key) {
                                            echo '<option value="' . $key['KodeBarang'] . '">' . $key['ProductionCode'] . ' | ' . $key['NamaBarang'] . ' | ' . $key['BeratBarang'] . ' kilogram</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Barang</label>
                                <input type="text" class="form-control" id="JenisBarang" name="JenisBarang" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Kategori</label>
                                <input type="text" class="form-control" id="Kategory" name="Kategory" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Gudang | Stok (satuan)</label>
                                <input type="text" class="form-control" id="GS" name="" readonly>
                                <input type="hidden" class="form-control" id="SatuanBarang" name="SatuanBarang" readonly>
                                <input type="hidden" class="form-control" id="Spesifikasi" name="Spesifikasi" readonly>
                            </div>
                            <div class="form-group">
                                <label for="Deskripsi">Keterangan</label>
                                <textarea class="form-control" name="Deskripsi" id="Deskripsi" cols="" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Quantity</label>
                                <input type="text" class="form-control" id="Qty" name="Qty" required>
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
                                <label for="exampleInputFile">Additional Name</label>
                                <input type="text" class="form-control" id="AdditionalName" name="AdditionalName">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-header">
                    <h4 class="title">Bahan Baku Produksi</h4>
                    <?php if ($dtinduk['StatusProses'] == 'SO') { ?>
                        <button type="button" id="" onclick="tambahBahan()" class="btn btn-success btn-sm btn-add ml-auto">
                            <i class="la la-plus"></i> Tambah Bahan
                        </button>
                    <?php } ?>
                </div>
                <div class="modal-body">
                    <table width="100%" class="table" id="tbl-container">
                        <thead>
                            <tr>
                                <th style="width: 45%;">Nama Bahan</th>
                                <th style="width: 25%;">Quantity</th>
                                <th style="width: 25%;">Satuan</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="first_row">
                                <td>
                                    <select class="form-control form-select select2" name="KodeBahan[]" id="KodeBahan1">
                                        <option value="" selected>Pilih Barang</option>
                                        <?php if ($dtbahan) {
                                            foreach ($dtbahan as $key) {
                                                echo '<option value="' . $key['KodeBarang'] . '">' . $key['NamaBarang'] . '</option>';
                                            }
                                        } ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="QtyBahan[]" id="QtyBahan1">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="SatuanBahan[]" id="SatuanBahan1" readonly>
                                </td>
                                <td>
                                    <button type="button" hidden title="Hapus data" class="btn btn-sm btn-danger hapusbahan">x</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnsave" class="btn btn-primary waves-effect">Simpan</button>
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade ui-dialog" id="ModalPenjualan" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Transaksi</h4>
            </div>
            <form action="<?= base_url('transaksi/slip_order/simpanpenjualan') ?>" method="post" id="form-simpan-penjualan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Slip Order</label>
                                <input type="datetime-local" class="form-control" id="TglSlipOrder" name="TglSlipOrder" required>
                                <input type="hidden" class="form-control" id="NoRef_Manual" name="NoRef_Manual">
                                <input type="hidden" class="form-control" id="IDTransJual" name="IDTransJual" value="<?= $dtinduk['IDTransJual'] ?>">
                                <input type="hidden" class="form-control" id="KodePerson" name="KodePerson" value="<?= $dtinduk['KodePerson'] ?>">
                                <input type="hidden" class="form-control" id="NoTransKas" name="NoTransKas" value="<?= isset($dtinduk['NoTransKas']) ? $dtinduk['NoTransKas'] : '' ?>">
                            </div>
                            <div class="form-group">
                                <label for="EstimasiSelesai">Estimasi Selesai</label>
                                <input type="datetime-local" class="form-control" id="EstimasiSelesai" name="EstimasiSelesai" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Jatuh Tempo</label>
                                <input type="datetime-local" class="form-control" id="TanggalJatuhTempo" name="TanggalJatuhTempo" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnsimpan" class="btn btn-primary waves-effect">Simpan</button>
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>