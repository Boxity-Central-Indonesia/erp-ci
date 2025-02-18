<style type="text/css">
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
                    <?php if ($canPrint == 1) {
                        if ($dtinduk['SPKNomor']) {
                    ?>
                            <div class="dropdown action-btn">
                                <a target="_blank" href="<?= base_url('transaksi/slip_order/cetakspk/' . base64_encode($IDTransJual)) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
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
                    <?php
                        }
                    }
                    ?>
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
                                    <td>SO Dibuat Oleh</td>
                                    <td>: <?= $dtinduk['SODibuatOleh'] ?></td>
                                    <td>Tanggal Slip Order </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TglSlipOrder']))) . ' ' . date('H:i', strtotime($dtinduk['TglSlipOrder'])) ?></td>
                                </tr>
                                <tr class="userDatatable-header">
                                    <td>Nomor SPK</td>
                                    <td>: <?= $dtinduk['SPKNomor'] ?></td>
                                    <td>Estimasi Selesai </td>
                                    <td>: <?= isset($dtinduk['EstimasiSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($dtinduk['EstimasiSelesai']))) . ' ' . date('H:i', strtotime($dtinduk['EstimasiSelesai'])) : '-' ?></td>
                                </tr>
                                <tr class="userDatatable-header">
                                    <td>SPK Dibuat Oleh</td>
                                    <td>: <?= $dtinduk['SPKDibuatOleh'] ?></td>
                                    <td>Tanggal SPK Dibuat</td>
                                    <td>: <?= isset($dtinduk['SPKTanggal']) ? shortdate_indo(date('Y-m-d', strtotime($dtinduk['SPKTanggal']))) . ' ' . date('H:i', strtotime($dtinduk['SPKTanggal'])) : '-' ?></td>
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
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Detail</div>
                                    <?php
                                    if ($canEdit == 1) {
                                        if ($dtinduk['StatusProses'] == 'SO') { // || $dtinduk['StatusProses'] == 'SPK') {
                                    ?>
                                            <button type="button" id="simpanspk" class="btn btn-primary btn-sm ml-auto">Verifikasi SPK</button>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <table id="table-sodetailspk" class="table mb-0 table-borderless">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Produksi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kategori </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Satuan Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jumlah Produksi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Quantity </span></th>
                                        <!-- <th style="display: table-cell;"><span class="userDatatable-title">Total </span></th> -->
                                        <!-- <th style="display: table-cell; width:15%;">#</th> -->
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                                <!-- <tfoot style="font-size:14px;">
                                    <tr>
                                        <td colspan="5"></td>
                                        <td>Jumlah</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot> -->
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <a href="<?= base_url('transaksi/slip_order/detail/' . base64_encode($IDTransJual)) ?>" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ui-dialog" id="ModalSPK" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Simpan SPK</h4>
            </div>
            <form action="<?= base_url('transaksi/slip_order/simpanspk') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-6">
                            <?php if ($dtinduk['StatusProses'] == 'SPK') { ?>
                                <div class="form-group">
                                    <label for="exampleInputFile">Nomor SPK</label>
                                    <input type="text" class="form-control" id="SPKNomor" name="SPKNomor">
                                    <input type="hidden" class="form-control" id="SPKLama" name="SPKLama">
                                </div>
                            <?php } ?>
                            <div class="form-group">
                                <label for="exampleInputFile">Estimasi Selesai</label>
                                <input type="hidden" class="form-control" id="IDTransJual" name="IDTransJual" value="<?= $IDTransJual ?>">
                                <input type="datetime-local" class="form-control" id="EstimasiSelesai" name="EstimasiSelesai">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">SPK Disetujui Oleh</label>
                                <input type="text" class="form-control" id="SPKDisetujuiOleh" name="SPKDisetujuiOleh" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">SPK Diketahui Oleh</label>
                                <input type="text" class="form-control" id="SPKDiketahuiOleh" name="SPKDiketahuiOleh" required>
                            </div>
                            <div class="form-group" hidden>
                                <label for="exampleInputFile">No Referensi</label>
                                <input type="text" class="form-control" id="NoRefTrManual" name="NoRefTrManual" value="">
                                <input type="hidden" class="form-control" id="NoTrans" name="NoTrans" value="">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Keterangan</label>
                                <textarea class="form-control" rows="3" id="Deskripsi" name="Deskripsi"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal SPK Dibuat</label>
                                <input type="datetime-local" class="form-control" id="SPKTanggal" name="SPKTanggal" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal SPK Disetujui</label>
                                <input type="datetime-local" class="form-control" id="SPKDisetujuiTgl" name="SPKDisetujuiTgl" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal SPK Diketahui</label>
                                <input type="datetime-local" class="form-control" id="SPKDiketahuiTgl" name="SPKDiketahuiTgl" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Gudang</label>
                                <select class="form-control form-select" name="" id="KodeGudang" disabled>
                                    <?php if ($gudang) {
                                        foreach ($gudang as $key) {
                                            echo '<option ' . ((@$dtinduk['KodeGudang'] == $key['KodeGudang']) ? 'selected' : '') . ' value="' . $key['KodeGudang'] . '">' . $key['NamaGudang'] . '</option>';
                                        }
                                    } ?>
                                </select>
                                <input type="hidden" class="form-control" id="Gudang" name="Gudang" readonly>
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