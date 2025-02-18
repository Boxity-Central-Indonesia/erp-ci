<style type="text/css">
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: 0 !important;
        margin-left: 0 !important;
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
                            <form id="formTambah" action="<?= base_url('master/barang/simpan') ?>" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Kode Manual</label>
                                            <input type="text" class="form-control" name="KodeManual" id="KodeManual">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Nama Barang</label>
                                            <input type="hidden" class="form-control" id="KodeBarang" name="KodeBarang" />
                                            <input type="text" class="form-control" name="NamaBarang" id="NamaBarang" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Deskripsi</label>
                                            <textarea class="form-control" name="DeskripsiBarang" id="DeskripsiBarang"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Harga Beli Terakhir</label>
                                            <input type="text" class="form-control" name="HargaBeliTerakhir" id="HargaBeliTerakhir" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Harga Jual</label>
                                            <input type="text" class="form-control" name="HargaJual" id="HargaJual" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Berat</label>
                                            <div class="position-relative input-group">
                                                <input type="text" class="form-control" name="BeratBarang" id="BeratBarang">
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
                                            <input type="text" class="form-control" name="SatuanBarang" id="SatuanBarang" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Production Code</label>
                                            <input type="text" class="form-control" name="ProductionCode" id="ProductionCode">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Spesifikasi</label>
                                            <textarea class="form-control" rows="3" id="Spesifikasi" name="Spesifikasi" value=""></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Jenis Barang</label>
                                            <select class="form-control form-select select2" name="KodeJenis" id="comboJns" required>
                                                <option value="" selected>Pilih Jenis Barang</option>
                                                <?php if($jbrg){
                                                    foreach ($jbrg as $key) {
                                                        echo '<option value="'.$key['KodeJenis'].'">'.$key['NamaJenisBarang'].'</option>';
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
                                                        echo '<option value="'.$key['KodeKategori'].'">'.$key['NamaKategori'].'</option>';
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nilai HPP / Unit</label>
                                            <input type="text" class="form-control" name="HPPOpeningBalance" id="HPPOpeningBalance">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Stok Opening Balance</label>
                                            <input type="text" class="form-control" name="StokOpeningBalance" id="StokOpeningBalance">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Gudang</label>
                                            <select class="form-control form-select select2" name="KodeGudang" id="KodeGudang">
                                                <option value="" selected>Pilih Gudang</option>
                                                <?php if ($gudang) {
                                                    foreach ($gudang as $key) {
                                                        echo '<option value="'.$key['KodeGudang'].'">'.$key['NamaGudang'].'</option>';
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <div class="form-group">
                                            <div class="checkbox-theme-default custom-checkbox ">
                                                <input class="checkbox" type="checkbox" name="SimpanOB" id="simpanob" checked>
                                                <label for="simpanob">
                                                    <span class="checkbox-text">
                                                        Simpan Opening Balance
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Foto 1</label>
                                            <input type="file" class="form-control" name="Foto1" id="Foto1">
                                            <label class="small">Hanya ekstensi .png .jpg .jpeg</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Foto 2</label>
                                            <input type="file" class="form-control" name="Foto2" id="Foto2">
                                            <label class="small">Hanya ekstensi .png .jpg .jpeg</label>
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

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah2 = document.getElementById('HargaBeliTerakhir');
    tanpa_rupiah2.addEventListener('keyup', function(e)
    {
        tanpa_rupiah2.value = formatRupiah(this.value);
    });

    var tanpa_rupiah3 = document.getElementById('HargaJual');
    tanpa_rupiah3.addEventListener('keyup', function(e)
    {
        tanpa_rupiah3.value = formatRupiah(this.value);
    });

    var tanpa_rupiah4 = document.getElementById('HPPOpeningBalance');
    tanpa_rupiah4.addEventListener('keyup', function(e)
    {
        tanpa_rupiah4.value = formatRupiah(this.value);
    });

    var tanpa_rupiah5 = document.getElementById('StokOpeningBalance');
    tanpa_rupiah5.addEventListener('keyup', function(e)
    {
        tanpa_rupiah5.value = formatRupiah(this.value);
    });

    var tanpa_rupiah6 = document.getElementById('BeratBarang');
    tanpa_rupiah6.addEventListener('keyup', function(e)
    {
        tanpa_rupiah6.value = formatRupiah(this.value);
    });
</script>

<script type="text/javascript">
    
</script>