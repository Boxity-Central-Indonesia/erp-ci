<style type="text/css">
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: 0 !important;
        margin-left: 0 !important;
    }

    .btn span, .btn i {
        font-size: 12px !important;
        display: inline-block;
        margin-right: 0 !important;
    }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-header color-dark fw-500">
                    Tambah Data
                </div>
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="">
                            <form id="formEdit" action="<?= base_url('master/barang/simpan') ?>" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Kode Manual</label>
                                            <input type="text" class="form-control" name="KodeManual" id="KodeManual" value="<?= @$model['KodeManual'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Nama Barang</label>
                                            <input type="hidden" class="form-control" id="KodeBarang" name="KodeBarang" value="<?= @$model['KodeBarang'] ?>"/>
                                            <input type="hidden" class="form-control" id="NilaiHPP" name="NilaiHPP" value="<?= @$model['NilaiHPP'] ?>"/>
                                            <input type="hidden" class="form-control" id="fotoLama1" name="fotoLama1" value="<?= @$model['Foto1'] ?>" />
                                            <input type="hidden" class="form-control" id="fotoLama2" name="fotoLama2" value="<?= @$model['Foto2'] ?>" />
                                            <input type="text" class="form-control" name="NamaBarang" id="NamaBarang" required value="<?= @$model['NamaBarang'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Deskripsi</label>
                                            <textarea class="form-control" name="DeskripsiBarang" id="DeskripsiBarang"><?= @$model['DeskripsiBarang'] ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Harga Beli Terakhir</label>
                                            <input type="text" class="form-control" name="HargaBeliTerakhir" id="HargaBeliTerakhir" required value="<?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format(@$model['HargaBeliTerakhir'], 2)) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Harga Jual</label>
                                            <input type="text" class="form-control" name="HargaJual" id="HargaJual" required value="<?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format(@$model['HargaJual'], 2)) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Berat</label>
                                            <div class="position-relative input-group">
                                                <input type="text" class="form-control" name="BeratBarang" id="BeratBarang" value="<?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format(@$model['BeratBarang'], 2)) ?>">
                                                <div class="input-group-append" style="cursor: pointer">
                                                    <div class="input-group-text">
                                                        <span>kilogram</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Satuan</label>
                                            <input type="text" class="form-control" name="SatuanBarang" id="SatuanBarang" required value="<?= @$model['SatuanBarang'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Production Code</label>
                                            <input type="text" class="form-control" name="ProductionCode" id="ProductionCode" value="<?= @$model['ProductionCode'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Spesifikasi</label>
                                            <textarea class="form-control" rows="3" id="Spesifikasi" name="Spesifikasi" value=""><?= @$model['Spesifikasi'] ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Jenis Barang</label>
                                            <select class="form-control form-select select2" name="KodeJenis" id="comboJns" required>
                                                <option value="" selected>Pilih Jenis Barang</option>
                                                <?php if($jbrg){
                                                    foreach ($jbrg as $key) {
                                                        if(isset($model['KodeJenis']) && $key['KodeJenis'] == $model['KodeJenis']){
                                                            echo '<option value="'.$key['KodeJenis'].'" selected>'.$key['NamaJenisBarang'].'</option>';
                                                        }else{
                                                            echo '<option value="'.$key['KodeJenis'].'">'.$key['NamaJenisBarang'].'</option>';
                                                        }
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Kategori</label>
                                            <select class="form-control form-select select2" name="KodeKategori" id="comboKtg">
                                                <option value="" selected>Pilih Kategori</option>
                                                <?php if($ktg){
                                                    foreach ($ktg as $key) {
                                                        if(isset($model['KodeKategori']) && $key['KodeKategori'] == $model['KodeKategori']){
                                                            echo '<option value="'.$key['KodeKategori'].'" selected>'.$key['NamaKategori'].'</option>';
                                                        }else{
                                                            echo '<option value="'.$key['KodeKategori'].'">'.$key['NamaKategori'].'</option>';
                                                        }
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nilai HPP / Unit</label>
                                            <input type="text" class="form-control" name="HPPOpeningBalance" id="HPPOpeningBalance" value="<?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format(@$model['HargaSatuan'], 2)) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Stok Opening Balance</label>
                                            <input type="text" class="form-control" name="StokOpeningBalance" id="StokOpeningBalance" value="<?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format(@$model['Qty'], 2)) ?>">
                                            <input type="hidden" class="form-control" name="CanEditOB" id="CanEditOB" value="<?= $canedit_ob ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Gudang</label>
                                            <select class="form-control form-select select2" name="KodeGudang" id="KodeGudang">
                                                <option value="" selected>Pilih Gudang</option>
                                                <?php if ($gudang) {
                                                    foreach ($gudang as $key) {
                                                        if(isset($model['KodeGudang']) && $key['KodeGudang'] == $model['KodeGudang']){
                                                            echo '<option value="'.$key['KodeGudang'].'" selected>'.$key['NamaGudang'].'</option>';
                                                        }else{
                                                            echo '<option value="'.$key['KodeGudang'].'">'.$key['NamaGudang'].'</option>';
                                                        }
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>HPP Opening Balance</label>
                                            <input type="text" class="form-control" name="" id="HPPBalance" value="<?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format(@$hppbalance, 2)) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" hidden>
                                            <label>HPP Balance</label>
                                            <input type="text" class="form-control" name="" id="HPPSistem" value="<?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format(@$model['NilaiHPP'], 2)) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <div class="form-group">
                                            <div class="checkbox-theme-default custom-checkbox ">
                                                <input class="checkbox" type="checkbox" name="SimpanOB" id="simpanob">
                                                <label for="simpanob">
                                                    <span class="checkbox-text">
                                                        Simpan Opening Balance
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    ## cek ekstensi file
                                    if(isset($model['Foto1'])){
                                        $ekstensi = explode(".", $model['Foto1']);
                                        if($ekstensi[1] == "png" || $ekstensi[1] == "PNG" || $ekstensi[1] == "jpg" || $ekstensi[1] == "jpeg"){
                                            $url_gambar_1 = base_url().'assets/barang/'.$model['Foto1'];
                                        }
                                    }else{
                                        $url_gambar_1 = base_url().'assets/no_image.jpg';
                                    }

                                    if(isset($model['Foto2'])){
                                        $ekstensi = explode(".", $model['Foto2']);
                                        if($ekstensi[1] == "png" || $ekstensi[1] == "PNG" || $ekstensi[1] == "jpg" || $ekstensi[1] == "jpeg"){
                                            $url_gambar_2 = base_url().'assets/barang/'.$model['Foto2'];
                                        }
                                    }else{
                                        $url_gambar_2 = base_url().'assets/no_image.jpg';
                                    }
                                    ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Foto 1</label>
                                            <input type="file" class="form-control" name="Foto1" id="Foto1">
                                            <label class="small">Hanya ekstensi .png .jpg .jpeg</label>
                                            <div class="col-md-8">
                                                <img id="image-preview-1"  class="img-thumbnail" alt="image preview" style="max-width:100%; height:250px; margin-bottom: 1rem;" src="<?php echo $url_gambar_1 ?>"/>
                                                <?php echo isset($model['Foto1']) ? '<a class="btn btn-primary btn-sm" title="Download Foto 1 Lama" target="_blank" download href="'.base_url().'assets/barang/'.$model['Foto1'].'"><span><i class="fa fa-download"></i> Download</span></a>' : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Foto 2</label>
                                            <input type="file" class="form-control" name="Foto2" id="Foto2">
                                            <label class="small">Hanya ekstensi .png .jpg .jpeg</label>
                                            <div class="col-md-8">
                                                <img id="image-preview-2"  class="img-thumbnail" alt="image preview" style="max-width:100%; height:250px; margin-bottom: 1rem;" src="<?php echo $url_gambar_2 ?>"/>
                                                <?php echo isset($model['Foto2']) ? '<a class="btn btn-primary btn-sm" title="Download Foto 2 Lama" target="_blank" download href="'.base_url().'assets/barang/'.$model['Foto2'].'"><span><i class="fa fa-download"></i> Download</span></a>' : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>QR Code</label>
                                            <div class="col-md-8">
                                                <img id="qr_image"  class="img-thumbnail" alt="image preview" style="max-width:100%; height:125px;" src="<?php echo base_url().'assets/barang/qr_' . $model['KodeBarang'] . '.png' ?>"/>
                                                <?php echo '<a class="btn btn-primary btn-sm" title="Download QR Code" target="_blank" download href="'.base_url().'assets/barang/qr_'.$model['KodeBarang'].'.png"><span><i class="fa fa-download"></i> Download</span></a>' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Barcode</label>
                                            <div class="col-md-8">
                                                <img id="barcode_img"  class="img-thumbnail" alt="image preview" style="max-width:100%; height:125px;" src="<?php echo base_url().'assets/barang/bar_' . $model['KodeBarang'] . '.png' ?>"/>
                                                <?php echo '<a class="btn btn-primary btn-sm" title="Download Barcode" target="_blank" download href="'.base_url().'assets/barang/bar_'.$model['KodeBarang'].'.png"><span><i class="fa fa-download"></i> Download</span></a>' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <div class="row clearfix">
                                        <a href="<?=base_url('master/barang')?>" class="btn btn-sm btn-secondary">Kembali</a>
                                        <button id="save" type="submit" class="btn btn-sm btn-primary ml-auto">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
