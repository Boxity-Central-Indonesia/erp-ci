<style type="text/css">
    #ModalTambahJns { overflow-y:scroll !important; }
    #ModalTambahKtg { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <?php
                    $barangview = 0; $barangadd = 0; $barangedit = 0; $barangdelete = 0;
                    $jenisview = 0; $jenisadd = 0; $jenisedit = 0; $jenisdelete = 0;
                    $kategoriview = 0; $kategoriadd = 0; $kategoriedit = 0; $kategoridelete = 0;

                    $view = [];
                    foreach ($this->session->userdata('fiturview') as $key => $value) {
                        $view[$key] = $value;
                        if ($key == 8 && $value == 1) {
                            $barangview = 1;
                        }
                        if ($key == 7 && $value == 1) {
                            $jenisview = 1;
                        }
                        if ($key == 46 && $value == 1) {
                            $kategoriview = 1;
                        }
                    }

                    $add = [];
                    foreach ($this->session->userdata('fituradd') as $key => $value) {
                        $view[$key] = $value;
                        if ($key == 8 && $value == 1) {
                            $barangadd = 1;
                        }
                        if ($key == 7 && $value == 1) {
                            $jenisadd = 1;
                        }
                        if ($key == 46 && $value == 1) {
                            $kategoriadd = 1;
                        }
                    }

                    $edit = [];
                    foreach ($this->session->userdata('fituredit') as $key => $value) {
                        $view[$key] = $value;
                        if ($key == 8 && $value == 1) {
                            $barangedit = 1;
                        }
                        if ($key == 7 && $value == 1) {
                            $jenisedit = 1;
                        }
                        if ($key == 46 && $value == 1) {
                            $kategoriedit = 1;
                        }
                    }

                    $delete = [];
                    foreach ($this->session->userdata('fiturdelete') as $key => $value) {
                        $view[$key] = $value;
                        if ($key == 8 && $value == 1) {
                            $barangdelete = 1;
                        }
                        if ($key == 7 && $value == 1) {
                            $jenisdelete = 1;
                        }
                        if ($key == 46 && $value == 1) {
                            $kategoridelete = 1;
                        }
                    }
                ?>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="tab-wrapper">
                            <div class="atbd-tab tab-horizontal">
                                <ul class="nav nav-tabs vertical-tabs" role="tablist">
                                    <?php if ($barangview == 1) { ?>
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-barang-tab" data-toggle="tab" href="#custom-tabs-for-barang" role="tab" aria-controls="custom-tabs-for-barang" aria-selected="true">Master Barang</a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($jenisview == 1) { ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-jenis-tab" data-toggle="tab" href="#custom-tabs-for-jenis" role="tab" aria-controls="custom-tabs-for-jenis" aria-selected="false">Master Jenis Barang</a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($kategoriview == 1) { ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-kategori-tab" data-toggle="tab" href="#custom-tabs-for-kategori" role="tab" aria-controls="custom-tabs-for-kategori" aria-selected="false">Master Kategori Barang</a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="custom-tabs-for-barang" role="tabpanel" aria-labelledby="custom-tabs-barang-tab">
                                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                                        <div class="table-responsive">
                                            <?php if ($this->session->flashdata('berhasil')) { ?>
                                                <div class=" alert alert-success alert-dismissible fade show " role="alert">
                                                    <div class="alert-content">
                                                        <p><?= $this->session->flashdata('berhasil') ?></p>
                                                        <button type="button" class="close text-capitalize" data-dismiss="alert" aria-label="Close">
                                                            <span data-feather="x" aria-hidden="true"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($this->session->flashdata('gagal')) { ?>
                                                <div class=" alert alert-danger alert-dismissible fade show " role="alert">
                                                    <div class="alert-content">
                                                        <p><?= $this->session->flashdata('gagal') ?></p>
                                                        <button type="button" class="close text-capitalize" data-dismiss="alert" aria-label="Close">
                                                            <span data-feather="x" aria-hidden="true"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($barangadd == 1) { ?>
                                                <a href="<?= base_url('master/barang/tambah') ?>" type="button" id="btntambah" class="btn btn-primary btn-sm btn-add" style="float:right;">
                                                    <i class="la la-plus"></i> Tambah Data
                                                </a>
                                            <?php } ?>
                                            <table id="table-barang" class="table mb-0 table-borderless">
                                                <thead>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Jenis Barang</label>
                                                                <select class="form-control" id="combo-jenis-brg">
                                                                    <option value="">Semua Jenis</option>
                                                                    <?php foreach ($dtjenis as $key) { ?>
                                                                        <option value="<?= $key['KodeJenis'] ?>"><?= $key['NamaJenisBarang'] ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Kategori</label>
                                                                <select class="form-control" id="combo-kategori-brg">
                                                                    <option value="">Semua Kategori</option>
                                                                    <?php foreach ($dtkategori as $key) { ?>
                                                                        <option value="<?= $key['KodeKategori'] ?>"><?= $key['NamaKategori'] ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Pencarian Data</label>
                                                                <input id="inp-search-brg" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
                                                            </div>
                                                        </td>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Status</label>
                                                                <select class="form-control" id="combo-status-brg">
                                                                    <option value="">Semua Status</option>
                                                                    <option value="1">Aktif</option>
                                                                    <option value="0">Non-Aktif</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr class="userDatatable-header">
                                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Barang </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Manual </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Barang </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Barang </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Kategori </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Harga Beli Terakhir </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Harga Jual </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Nilai HPP / Unit </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Stok </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">HPP Balance </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Status </span></th>
                                                        <?php if ($barangedit == 1 || $barangdelete == 1) { ?>
                                                        <th style="display: table-cell; width:15%;">#</th>
                                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody style="font-size:14px;">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-for-jenis" role="tabpanel" aria-labelledby="custom-tabs-jenis-tab">
                                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                                        <div class="table-responsive">
                                            <table id="table-jenis" class="table mb-0 table-borderless">
                                                <thead>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Status</label>
                                                                <select class="form-control" id="combo-status-jenis">
                                                                    <option value="">Semua Status</option>
                                                                    <option value="1">Aktif</option>
                                                                    <option value="0">Non-Aktif</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Pencarian Data</label>
                                                                <input id="inp-search-jenis" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php if ($jenisadd == 1) { ?>
                                                            <button type="button" id="btntambahjenis" style="float: right;" class="btn btn-primary btn-sm btn-add">
                                                                <i class="la la-plus"></i> Tambah Data
                                                            </button>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="userDatatable-header">
                                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Barang </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Deskripsi </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Status </span></th>
                                                        <?php if ($jenisedit == 1 || $jenisdelete == 1) { ?>
                                                        <th style="display: table-cell; width:15%;">#</th>
                                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody style="font-size:14px;">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-for-kategori" role="tabpanel" aria-labelledby="custom-tabs-kategori-tab">
                                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                                        <div class="table-responsive">
                                            <table id="table-kategori" class="table mb-0 table-borderless">
                                                <thead>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Status</label>
                                                                <select class="form-control" id="combo-status-ktg">
                                                                    <option value="">Semua Status</option>
                                                                    <option value="1">Aktif</option>
                                                                    <option value="0">Non-Aktif</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <label class="form-control-label">Pencarian Data</label>
                                                                <input id="inp-search-ktg" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php if ($kategoriadd == 1) { ?>
                                                            <button type="button" id="btntambahktg" style="float: right;" class="btn btn-primary btn-sm btn-add">
                                                                <i class="la la-plus"></i> Tambah Data
                                                            </button>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="userDatatable-header">
                                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Kategori </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Status </span></th>
                                                        <?php if ($kategoriedit == 1 || $kategoridelete == 1) { ?>
                                                        <th style="display: table-cell; width:15%;">#</th>
                                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody style="font-size:14px;">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ui-dialog" id="ModalTambahJns" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Tambah Data Jenis Barang</h4>
            </div>
            <form action="<?= base_url('master/jenisbarang/simpan') ?>" method="post" id="form-jenis">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Barang</label>
                                <input type="hidden" class="form-control" id="KodeJenis" name="KodeJenis" value="">
                                <input type="text" class="form-control" id="NamaJenisBarang" name="NamaJenisBarang" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Deskripsi</label>
                                <textarea class="form-control" rows="3" id="Deskripsi" name="Deskripsi" value=""></textarea>
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

<div class="modal fade ui-dialog" id="ModalTambahKtg" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel2">Tambah Data Kategori Barang</h4>
            </div>
            <form action="<?= base_url('master/kategori/simpan') ?>" method="post" id="form-kategori">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Kategori</label>
                                <input type="hidden" class="form-control" id="KodeKategori" name="KodeKategori" value="">
                                <input type="text" class="form-control" id="NamaKategori" name="NamaKategori" value="" required>
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