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
                        $FiturID = 61; //FiturID di tabel serverfitur
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
                <div class="card-header color-dark fw-500">
                    Data User
                </div>
                <div class="card-body">

                    <form action="<?= base_url('user/profile/simpan') ?>" method="post" id="form-simpan" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exampleInputFile">Nama</label>
                                        <input type="text" class="form-control" id="ActualName" name="ActualName" value="<?= @$model['ActualName'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?> required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputFile">Alamat</label>
                                        <input type="text" class="form-control" id="Address" name="Address" value="<?= @$model['Address'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?> required>
                                    </div>
                                    <?php
                                    ## cek ekstensi file
                                    if(isset($model['Photo'])){
                                        $ekstensi = explode(".", $model['Photo']);
                                        if($ekstensi[1] == "png" || $ekstensi[1] == "PNG" || $ekstensi[1] == "jpg" || $ekstensi[1] == "jpeg"){
                                            $url_gambar_1 = base_url().'assets/img/users/'.$model['Photo'];
                                        }
                                    }else{
                                        $url_gambar_1 = base_url().'assets/no_image.jpg';
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label>Foto</label>
                                        <input type="file" class="form-control" name="Photo" id="Photo" accept=".png,.jpg,.jpeg">
                                        <label class="small">Hanya ekstensi .png .jpg .jpeg</label>
                                        <div class="col-md-8">
                                            <img id="image-preview-1"  class="img-thumbnail" alt="image preview" style="max-width:100%; height:250px; margin-bottom: 1rem;" src="<?php echo $url_gambar_1 ?>"/><br>
                                            <?php echo isset($model['Photo']) ? '<a class="btn btn-primary btn-sm" title="Download Foto" target="_blank" download href="'.base_url().'assets/img/users/'.$model['Photo'].'"><span><i class="fa fa-download"></i> Download</span></a>' : '' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exampleInputFile">Nomor HP</label>
                                        <input type="number" class="form-control" id="Phone" name="Phone" value="<?= @$model['Phone'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?> required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputFile">Email</label>
                                        <input type="email" class="form-control" id="Email" name="Email" value="<?= @$model['Email'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?> required>
                                        <input type="hidden" class="form-control" id="emailLama" name="" value="<?= @$model['Email'] ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputFile">Username</label>
                                        <input type="text" class="form-control" id="UserName" name="" value="<?= @$model['UserName'] ?>" readonly required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputFile">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="UserPsw" name="" value="<?= @$model['UserPsw'] ?>" readonly>
                                            <div class="input-group-append" style="cursor: pointer">
                                                <div class="input-group-text">
                                                    <i class="fa fa-eye" id="togglePassword"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($canEdit == 1) { ?>
                        <div class="modal-footer">
                            <button type="submit" id="btnsave" class="btn btn-primary waves-effect">Simpan</button>
                        </div>
                        <?php } ?>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>