<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
    #ModalJenis { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <?php
                    $FiturID = 45; //FiturID di tabel serverfitur
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
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <!-- <div class="card-header color-dark fw-500">
                    Daftar Jenis Aktivitas
                </div> -->
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="tab-wrapper">
                            <div class="atbd-tab tab-horizontal">
                                <ul class="nav nav-tabs vertical-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-aktivitas-tab" data-toggle="tab" href="#custom-tabs-for-aktivitas" role="tab" aria-controls="custom-tabs-for-aktivitas" aria-selected="true">Master Aktivitas</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-jenis-tab" data-toggle="tab" href="#custom-tabs-for-jenis" role="tab" aria-controls="custom-tabs-for-jenis" aria-selected="false">Master Jenis Aktivitas</a>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="custom-tabs-for-aktivitas" role="tabpanel" aria-labelledby="custom-tabs-aktivitas-tab">
                                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                                        <div class="table-responsive">
                                            <table id="table-aktivitas" class="table mb-0 table-borderless">
                                                <thead>
                                                    <tr>
                                                        <td colspan="3">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Pencarian Data</label>
                                                                <input id="inp-search" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
                                                            </div>
                                                        </td>
                                                        <td colspan="3"></td>
                                                        <td colspan="3">
                                                            <?php if ($canAdd == 1) { ?>
                                                            <button type="button" style="float: right;" id="btntambah" class="btn btn-primary btn-sm btn-add">
                                                                <i class="la la-plus"></i> Tambah Data
                                                            </button>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="userDatatable-header">
                                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Aktivitas </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Aktivitas </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Batas Bawah </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Batas Atas </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Jumlah Daun </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Biaya </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Satuan </span></th>
                                                        <?php if ($canEdit == 1 || $canDelete == 1) { ?>
                                                        <th style="display: table-cell; width:10%;">#</th>
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
                                                                <label class="form-control-label">Pencarian Data</label>
                                                                <input id="inp-search-jenis" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
                                                            </div>
                                                        </td>
                                                        <td colspan="3">
                                                            <?php if ($canAdd == 1) { ?>
                                                            <button type="button" style="float: right;" id="btnjenis" class="btn btn-primary btn-sm btn-add">
                                                                <i class="la la-plus"></i> Tambah Data
                                                            </button>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="userDatatable-header">
                                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Jenis Aktivitas </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Nomor Urut </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Aktivitas </span></th>
                                                        <?php if ($canEdit == 1 || $canDelete == 1) { ?>
                                                        <th style="display: table-cell; width:10%;">#</th>
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

<div class="modal fade ui-dialog" id="ModalTambah" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Tambah Data Aktivitas</h4>
            </div>
            <form action="<?= base_url('master/aktivitas/simpan') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Aktivitas</label>
                                <input type="hidden" class="form-control" id="KodeAktivitas" name="KodeAktivitas" value="">
                                <input type="hidden" class="form-control" id="JenisAktivitas" name="JenisAktivitas" value="">
                                <select class="form-control form-select select2" name="KodeJenisAktivitas" id="KodeJenisAktivitas" disabled required>
                                    <option value="" selected>Pilih Jenis Aktivitas</option>
                                    <?php if($dtjenis){
                                        foreach ($dtjenis as $key) {
                                            echo '<option value="'.$key['KodeJenisAktivitas'].'">'.$key['JenisAktivitas'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Batas Bawah</label>
                                <input type="number" class="form-control" id="BatasBawah" name="BatasBawah">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Batas Atas</label>
                                <input type="number" class="form-control" id="BatasAtas" name="BatasAtas">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jumlah Daun</label>
                                <input type="number" class="form-control" id="JmlDaun" name="JmlDaun">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Biaya</label>
                                <input type="text" class="form-control" id="Biaya" name="Biaya">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Satuan</label>
                                <input type="text" class="form-control" id="Satuan" name="Satuan">
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

<div class="modal fade ui-dialog" id="ModalJenis" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel2">Tambah Data Jenis Aktivitas</h4>
            </div>
            <form action="<?= base_url('master/aktivitas/simpanjenis') ?>" method="post" id="form-jenis">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nomor Urut</label>
                                <input type="number" class="form-control" id="NoUrut" name="NoUrut" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Aktivitas</label>
                                <input type="hidden" class="form-control" id="KodeJenisAktivitas2" name="KodeJenisAktivitas" value="">
                                <input type="text" class="form-control" id="JenisAktivitas2" name="JenisAktivitas" value="" required>
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