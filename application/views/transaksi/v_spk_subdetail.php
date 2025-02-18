<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
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
                    <div class="dropdown action-btn" hidden>
                        <a target="_blank" href="<?= base_url('transaksi/spk/cetakdetail/' . base64_encode($NoTrans)) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
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
                                    <td>No Referensi </td>
                                    <td>: <?= $dtinduk['NoRefTrManual'] ?></td>
                                    <td>Tanggal SPK </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['SPKTanggal']))) . ' ' . date('H:i', strtotime($dtinduk['SPKTanggal'])) ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Mulai Produksi </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalTransaksi'])) ?></td>
                                    <td>Gudang Asal </td>
                                    <td>: <?= $dtinduk['NamaGudangAsal'] ?></td>
                                </tr>
                                <tr>
                                    <td>Barang Produksi </td>
                                    <td>: <?= $dtinduk['NamaBarang'] ?></td>
                                    <td>Gudang Tujuan </td>
                                    <td>: <?= $dtinduk['NamaGudangTujuan'] ?></td>
                                </tr>
                                <tr>
                                    <td>Jumlah Permintaan </td>
                                    <td>: <?= $dtinduk['JmlProduksi'] ?></td>
                                    <td>Keterangan </td>
                                    <td>: <?= $dtinduk['Deskripsi'] ?></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Kebutuhan Produksi</div>
                                    <?php
                                    if ($canAdd == 1) {
                                        if (!($dtinduk['ProdTglSelesai'])) {
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
                            <table id="table-spksubdetail" class="table mb-0 table-borderless">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Satuan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Stok Gudang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Quantity </span></th>
                                        <?php
                                        if ($canEdit == 1 || $canDelete == 1) {
                                            if (!($dtinduk['ProdTglSelesai'])) {
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
                    <!-- <a href="<?=base_url('transaksi/spk')?>" class="btn btn-sm btn-primary">Simpan</a> -->
                    <a href="<?=base_url('transaksi/spk/detail/') . base64_encode($NoRefTrSistem) ?>" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ui-dialog" id="ModalTambah" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Tambah Data SPK</h4>
            </div>
            <form action="<?= base_url('transaksi/spk/simpansubdetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Barang</label>
                                <input type="hidden" class="form-control" id="NoTrans" name="NoTrans" value="<?= $NoTrans ?>">
                                <input type="hidden" class="form-control" id="NoUrut" name="NoUrut">
                                <input type="hidden" class="form-control" id="QtyLama" name="">
                                <input type="hidden" class="form-control" id="KodeBarang" name="KodeBarang">
                                <select class="form-control form-select select2" name="" id="kodebrg" disabled required>
                                    <option value="" selected>Pilih Barang</option>
                                    <?php if($dtbarang){
                                        foreach ($dtbarang as $key) {
                                            echo '<option value="' . $key['KodeBarang'] . '">' . $key['KodeBarang'] . ' | ' . $key['NamaBarang'] . '</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Barang</label>
                                <input type="text" class="form-control" id="NamaJenisBarang" name="" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Stok Gudang</label>
                                <div class="position-relative input-group">
                                    <input type="number" class="form-control" id="StokAsal" name="" readonly>
                                    <div class="input-group-append" style="cursor: pointer">
                                        <div class="input-group-text">
                                            <span id="SatuanEdit1"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Quantity</label>
                                <div class="position-relative input-group">
                                    <input type="number" name="StokTujuan" id="StokTujuan" class="form-control" required>
                                    <div class="input-group-append" style="cursor: pointer">
                                        <div class="input-group-text">
                                            <span id="SatuanEdit2"></span>
                                        </div>
                                    </div>
                                </div>
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
