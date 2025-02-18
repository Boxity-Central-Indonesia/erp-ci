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
                        $FiturID = 62; //FiturID di tabel serverfitur
                        $canEdit = 0;
                        $edit = [];
                        foreach ($this->session->userdata('fituredit') as $key => $value) {
                            $edit[$key] = $value;
                            if ($key == $FiturID && $value == 1) {
                                $canEdit = 1;
                            }
                        }
                    ?>
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
                            <form action="<?= base_url('user/ganti_password/ganti_password_aksi') ?>" method="post" id="form-simpan">
                                <div class="modal-body">
                                    <div class="row clearfix">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="pass_baru">Password Baru</label>
                                                <input id="pass_baru" type="Password" class="form-control" name="pass_baru" <?= $canEdit == 1 ? '' : 'readonly' ?> required>
                                                <?= form_error('pass_baru', '<small class="text-danger pl-3">', ' </small>'); ?>
                                                <div class="group1">
                                                    <input type="checkbox" id="pas1" <?= $canEdit == 1 ? '' : 'disabled' ?>>
                                                    <label id="shw" class="label">Show Password</label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="d-block">
                                                    <label for="ulang_pass" class="control-label">Konfirmasi Password</label>
                                                </div>
                                                <input id="ulang_pass" type="Password" class="form-control" name="ulang_pass" <?= $canEdit == 1 ? '' : 'readonly' ?> required>
                                                <?= form_error('ulang_pass', '<small class="text-danger pl-3">', ' </small>'); ?>
                                                <div class="group2">
                                                    <input type="checkbox" id="ulang" <?= $canEdit == 1 ? '' : 'disabled' ?>>
                                                    <label id="hide" class="label">Show Password</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($canEdit == 1) { ?>
                                    <button type="submit" class="btn btn-primary waves-effect">Ganti Password</button>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>