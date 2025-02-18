<style type="text/css">
    #ModalTambah {
        overflow-y: scroll !important;
    }

    #ModalBahan {
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
                <!-- <div class="card-header color-dark fw-500">
                </div> -->
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
                                    <td>: <?= isset($dtinduk['NamaGudangAsal']) ? $dtinduk['NamaGudangAsal'] : $dtinduk['Gudang'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal SPK </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['SPKTanggal']))) . ' ' . date('H:i', strtotime($dtinduk['SPKTanggal'])) ?></td>
                                    <td>Gudang Tujuan </td>
                                    <td>: <?= isset($dtinduk['NamaGudangTujuan']) ? $dtinduk['NamaGudangTujuan'] : $dtinduk['Gudang'] ?></td>
                                </tr>
                                <?php if ($dtinduk['TglSlipOrder']) { ?>
                                    <tr>
                                        <td>Estimasi Selesai </td>
                                        <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['EstimasiSelesai']))) . ' ' . date('H:i', strtotime($dtinduk['EstimasiSelesai'])) ?></td>
                                        <td>Keterangan </td>
                                        <td>: <?= $dtinduk['Deskripsi'] ?></td>
                                    </tr>
                                <?php } else {  ?>
                                    <tr class="userDatatable-header">
                                        <td>SPK Dibuat Oleh</td>
                                        <td>: <?= $dtinduk['SPKDibuatOleh'] ?></td>
                                        <td>Estimasi Selesai </td>
                                        <td>: <?= isset($dtinduk['EstimasiSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($dtinduk['EstimasiSelesai']))) . ' ' . date('H:i', strtotime($dtinduk['EstimasiSelesai'])) : '-' ?></td>
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
                                <?php } ?>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <?php
                                if ($canEdit == 1) {
                                    if (!($dtinduk['TglSlipOrder'])) {
                                ?>
                                        <div class="action-btn row clearfix">
                                            <button type="button" id="verifikasispk" class="btn <?= isset($dtinduk['SPKDisetujuiTgl']) ? 'btn-info' : 'btn-danger' ?> btn-sm ml-auto">Verifikasi SPK</button>
                                        </div>
                                        <br>
                                <?php
                                    }
                                }
                                ?>
                                <div class="action-btn row clearfix" hidden>
                                    <div>Daftar Item Bahan Baku</div>
                                    <?php
                                    if ($canAdd == 1 && $dtinduk['StatusProses'] != 'DONE') {
                                        if (!($dtinduk['TglSlipOrder'])) {
                                    ?>
                                            <button type="button" id="btntambah2" class="btn btn-primary btn-sm btn-add ml-auto">
                                                <i class="la la-plus"></i> Tambah Data
                                            </button>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <table id="table-bahanbaku" class="table mb-0 table-borderless" hidden>
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Bahan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Bahan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jumlah Berat </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Harga </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Total </span></th>
                                        <?php if ($canEdit == 1 && $dtinduk['StatusProses'] != 'DONE') { ?>
                                            <th style="display: table-cell; width:5%;">#</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                                <tfoot style="font-size:14px;">
                                    <tr>
                                        <td colspan="2"></td>
                                        <td class="text-right">Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <!-- <br><br> -->
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Produksi</div>
                                    <?php
                                    if ($canAdd == 1 && $dtinduk['StatusProses'] != 'DONE') {
                                        if (!($dtinduk['TglSlipOrder'])) {

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
                            <table id="table-spktambah" class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Ukuran </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jumlah Daun </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Berat Kotor </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Pemakaian Bahan Masak </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">HPP </span></th>
                                        <?php if ($canEdit == 1 && $dtinduk['StatusProses'] != 'DONE') { ?>
                                            <th style="display: table-cell; width:5%;">#</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                                <tfoot style="font-size:14px;">
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="text-right">Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="col-sm-12">
                        <div class="row clearfix">
                            <a href="javascript:(0)" type="button" hidden id="btn-batal" class="btn btn-sm btn-secondary" data-kode="<?= $dtinduk['IDTransJual'] ?>">Batal</a>
                            <a href="<?= base_url('transaksi/spk') ?>" type="button" class="btn btn-sm btn-secondary">Kembali</a>
                            <?php
                            // if ($itemProd > 0 && $itemBahan > 0) {
                            if ($dtinduk['SPKDisetujuiTgl'] != null && $dtinduk['StatusProses'] != 'DONE') {
                            ?>
                                <a href="javascript:(0)" type="button" id="btn-verifikasi" class="btn btn-sm btn-primary ml-auto" data-kode="<?= $dtinduk['NoTrans'] ?>">Simpan</a>
                            <?php
                            }
                            // }
                            ?>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data Produksi</h4>
            </div>
            <form action="<?= base_url('transaksi/spk/simpantambah') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Barang</label>
                                <select class="form-control form-select select2" name="KodeBarang" id="KodeBarang" disabled required>
                                    <option value="" selected>Pilih Barang Produksi</option>
                                    <?php if ($barangjadi) {
                                        foreach ($barangjadi as $key) {
                                            echo '<option value="' . $key['KodeBarang'] . '">' . $key['KodeManual'] . ' - ' . $key['NamaBarang'] . '</option>';
                                            // echo '<option value="'.$key['KodeBarang'].'">'.$key['NamaBarang'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Ukuran</label>
                                <input type="text" class="form-control" id="ProdUkuran" name="ProdUkuran">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jumlah Daun</label>
                                <input type="number" class="form-control" id="ProdJmlDaun" name="ProdJmlDaun">
                                <input type="hidden" class="form-control" id="Qty" name="Qty">
                                <input type="hidden" class="form-control" id="NoTrans" name="NoTrans" value="<?= $dtinduk['NoTrans'] ?>">
                                <input type="hidden" class="form-control" id="NoUrut" name="NoUrut">
                                <input type="hidden" class="form-control" id="Gudang" name="Gudang" value="<?= @$dtinduk['KodeGudang'] ?>">
                                <input type="hidden" class="form-control" id="JenisStok" name="JenisStok" value="MASUK">
                                <input type="hidden" class="form-control" id="IsBarangJadi" name="IsBarangJadi" value="1">
                                <input type="hidden" class="form-control" id="IsBahanBaku" name="IsBahanBaku" value="0">
                                <input type="hidden" class="form-control" id="SatuanBarang" name="SatuanBarang">
                                <input type="hidden" class="form-control" id="JenisBarang" name="JenisBarang">
                                <input type="hidden" class="form-control" id="Kategory" name="Kategory">
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

<div class="modal fade ui-dialog" id="ModalBahan" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Tambah Data Produksi</h4>
            </div>
            <form action="<?= base_url('transaksi/spk/simpantambah') ?>" method="post" id="form-simpan2">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Bahan</label>
                                <select class="form-control form-select select2" name="KodeBarang" id="KodeBarang2" disabled required>
                                    <option value="" selected>Pilih Bahan Produksi</option>
                                    <?php if ($bahanbaku) {
                                        foreach ($bahanbaku as $key) {
                                            // echo '<option value="'.$key['KodeBarang'].'">'.$key['KodeManual'].' - '.$key['NamaBarang'].'</option>';
                                            echo '<option value="' . $key['KodeBarang'] . '">' . $key['NamaBarang'] . '</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Bahan</label>
                                <input type="text" class="form-control" id="JenisBarang2" name="JenisBarang" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Harga Satuan</label>
                                <input type="text" class="form-control" id="HargaSatuan" name="HargaSatuan" readonly>
                            </div>
                            <div class="form-group">
                                <label for="Stok">Stok</label>
                                <input type="text" class="form-control" name="" id="Stok" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jumlah Berat</label>
                                <input type="text" class="form-control" id="Qty2" name="Qty" required>
                                <input type="hidden" class="form-control" id="NoTrans2" name="NoTrans" value="<?= $dtinduk['NoTrans'] ?>">
                                <input type="hidden" class="form-control" id="NoUrut2" name="NoUrut">
                                <input type="hidden" class="form-control" id="Gudang2" name="Gudang" value="<?= @$dtinduk['KodeGudang'] ?>">
                                <input type="hidden" class="form-control" id="JenisStok2" name="JenisStok" value="KELUAR">
                                <input type="hidden" class="form-control" id="IsBarangJadi2" name="IsBarangJadi" value="0">
                                <input type="hidden" class="form-control" id="IsBahanBaku2" name="IsBahanBaku" value="1">
                                <input type="hidden" class="form-control" id="SatuanBarang2" name="SatuanBarang">
                                <input type="hidden" class="form-control" id="Kategory2" name="Kategory">
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