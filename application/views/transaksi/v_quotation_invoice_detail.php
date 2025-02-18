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
                    $FiturID = 24; //FiturID di tabel serverfitur
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
                            <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="la la-download"></i> Cetak
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                <span class="dropdown-item">Pilih Cetak</span>
                                <div class="dropdown-divider"></div>
                                <a target="_blank" href="<?= base_url('transaksi/quotation_invoice/cetakquotation/' . base64_encode($IDTransJual)) ?>" class="dropdown-item">
                                    <i class="la la-file-pdf"></i> Quotation</a>
                                <a target="_blank" href="<?= base_url('transaksi/quotation_invoice/cetakproforma/' . base64_encode($IDTransJual)) ?>" class="dropdown-item">
                                    <i class="la la-file-pdf"></i> Proforma Invoice</a>
                                <a target="_blank" href="<?= base_url('transaksi/quotation_invoice/cetakinvoice/' . base64_encode($IDTransJual)) ?>" class="dropdown-item">
                                    <i class="la la-file-pdf"></i> Invoice</a>
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
                                    <!-- <td>Estimasi Selesai </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['EstimasiSelesai']))) . ' ' . date('H:i', strtotime($dtinduk['EstimasiSelesai'])) ?></td> -->
                                </tr>
                            </table>
                            <br>
                            <?php
                            if ($canAdd == 1) {
                                if ($dtinduk['StatusProses'] == 'SO') {
                            ?>
                                    <div class="col-sm-12">
                                        <div class="action-btn row clearfix">
                                            <div>Daftar Item Detail</div>
                                            <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add ml-auto" hidden>
                                                <i class="la la-plus"></i> Tambah Data
                                            </button>
                                        </div>
                                    </div>
                            <?php
                                }
                            }
                            ?>
                            <table id="table-qidetail" class="table mb-0 table-borderless">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">Satuan Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Harga Satuan </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Quantity </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Total </span></th>
                                        <?php if ($canEdit == 1 || $canDelete == 1) { ?>
                                            <th style="display: table-cell; width:15%;">#</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                                <tfoot style="font-size:14px;">
                                    <tr>
                                        <td colspan="5"></td>
                                        <td>Jumlah</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <a href="<?= base_url('transaksi/trans_jual') ?>" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ui-dialog" id="ModalTambah" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Tambah Data</h4>
            </div>
            <form action="<?= base_url('transaksi/quotation_invoice/simpandetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Barang</label>
                                <input type="hidden" class="form-control" id="IDTransJual" name="IDTransJual" value="<?= $IDTransJual ?>">
                                <input type="hidden" class="form-control" id="NoUrut" name="NoUrut">
                                <input type="hidden" class="form-control" id="TotalLama" name="TotalLama">
                                <input type="hidden" class="form-control" id="SatuanBarang" name="SatuanBarang" readonly>
                                <select class="form-control form-select select2" name="KodeBarang" id="KodeBarang" disabled required>
                                    <option value="" selected>Pilih Barang</option>
                                    <?php if ($dtbarang) {
                                        foreach ($dtbarang as $key) {
                                            echo '<option value="' . $key['KodeBarang'] . '">' . $key['NamaBarang'] . '</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Barang</label>
                                <input type="text" class="form-control" id="JenisBarang" name="JenisBarang" readonly>
                                <input type="hidden" class="form-control" id="Kategory" name="Kategory">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Harga Satuan</label>
                                <input type="text" class="form-control" id="HargaSatuan" name="HargaSatuan" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Spesifikasi</label>
                                <input type="text" class="form-control" id="Spesifikasi" name="Spesifikasi" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Quantity</label>
                                <div class="position-relative input-group">
                                    <input type="number" class="form-control" id="Qty" name="Qty" readonly>
                                    <div class="input-group-append" style="cursor: pointer">
                                        <div class="input-group-text">
                                            <span id="SatuanEdit1"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Additional Name</label>
                                <input type="text" class="form-control" id="AdditionalName" name="AdditionalName">
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