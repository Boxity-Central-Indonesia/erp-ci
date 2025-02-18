<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
    #ModalEdit { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php
                        $FiturID = 63; //FiturID di tabel serverfitur
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
                    <?php if ($canAdd == 1) { ?>
                    <div class="action-btn">
                        <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add">
                            <i class="la la-plus"></i> Tambah Database
                        </button>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <!-- <div class="card-header color-dark fw-500">
                    Data Perusahaan
                </div> -->
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <?php if ($this->session->flashdata('message')) { ?>
                                <div class="text">
                                    <div class=" alert alert-dismissible fade show " role="alert">
                                        <div class="alert-content">
                                            <p><?= $this->session->flashdata('message'); ?></p>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="close text-capitalize" data-dismiss="alert" aria-label="Close">
                                                <span data-feather="x" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
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
                            <form action="<?= base_url('user/ganti_db/ganti_db_aksi') ?>" method="post" id="form-ganti">
                                <div class="modal-body">
                                    <div class="row clearfix">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="exampleInputFile">Pilih Database</label><br>
                                                <?php foreach ($listdb as $key) { ?>
                                                <div class="icheck-success d-inline">
                                                    <input type="radio" id="<?= $key['db'] ?>" name="database" value="<?= $key['db'] ?>" <?= $key['isaktif'] == 1 ? 'checked' : 0 ?> <?= $canEdit == 1 ? '' : 'disabled' ?> required>
                                                    <label for="<?= $key['db'] ?>">
                                                        <?= $key['db_alias'] ?>
                                                    </label>
                                                </div>
                                                <?php if ($key['isaktif'] != 1 && $key['ismain'] != 1) { 
                                                    if ($canEdit == 1) {
                                                ?>
                                                &ensp;&emsp;<a class="btn-edit" data-obj='<?= json_encode($key) ?>' href="#"><span><i class="fa fa-edit ml-4"></i></span></a>
                                                <?php } if ($canDelete == 1) { ?>
                                                <a class="btn-hapus" data-kode="<?= $key['db'] ?>" href="#"><span><i class="fa fa-trash ml-4"></i></span></a>&nbsp;
                                                <?php 
                                                    }
                                                } ?>
                                                <br>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($canEdit == 1) { ?>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary waves-effect">Simpan</button>
                                </div>
                                <?php } ?>
                            </form>
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
                <h4 class="title" id="defaultModalLabel">Tambah Database</h4>
            </div>
            <form action="<?= base_url('user/ganti_db/simpan') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Host</label>
                                <input type="text" class="form-control" id="hostname" name="hostname" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" value="" required>
                                    <div class="input-group-append" style="cursor: pointer">
                                        <div class="input-group-text">
                                            <i class="fa fa-eye" id="togglePassword"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Database</label>
                                <input type="text" class="form-control" id="database" name="database" value="" required>
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

<div class="modal fade ui-dialog" id="ModalEdit" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel2">Edit Database</h4>
            </div>
            <form action="<?= base_url('user/ganti_db/update') ?>" method="post" id="form-update">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Host</label>
                                <input type="text" class="form-control" id="hostname2" name="hostname" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Username</label>
                                <input type="text" class="form-control" id="username2" name="username" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password2" name="password" value="" required>
                                    <div class="input-group-append" style="cursor: pointer">
                                        <div class="input-group-text">
                                            <i class="fa fa-eye" id="togglePassword2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Database</label>
                                <input type="text" class="form-control" id="database2" name="database" value="" required>
                                <input type="hidden" class="form-control" id="db_alias2" name="db_alias" value="" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect">Simpan</button>
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>