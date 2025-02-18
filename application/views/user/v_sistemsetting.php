<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
    #ModalReset { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php
                        $FiturID = 31; //FiturID di tabel serverfitur
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
                        <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="la la-download"></i> Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <span class="dropdown-item">Export With</span>
                            <div class="dropdown-divider"></div>
                            <a href="" class="dropdown-item">
                                <i class="la la-file-pdf"></i> PDF</a>
                            <a href="" class="dropdown-item">
                                <i class="la la-file-excel"></i> Excel (XLSX)</a>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-header color-dark fw-500">
                    Data Perusahaan
                </div>
                <form action="<?= base_url('user/sistemsetting/simpan') ?>" method="post" id="form-simpan">
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <div class="modal-body">
                                <div class="row clearfix">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleInputFile">Nama Perusahaan</label>
                                            <input type="text" class="form-control" id="NamaPerusahaan" name="NamaPerusahaan" value="<?= $model['NamaPerusahaan'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?>>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Email Perusahaan</label>
                                            <input type="email" class="form-control" id="EmailPerusahaan" name="EmailPerusahaan" value="<?= $model['EmailPerusahaan'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?>>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Nomor Telpon Perusahaan</label>
                                            <input type="text" class="form-control" id="NoTelpPerusahaan" name="NoTelpPerusahaan" value="<?= $model['NoTelpPerusahaan'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?>>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Alamat Perusahaan</label>
                                            <textarea type="text" rows="4" class="form-control" id="AlamatPerusahaan" name="AlamatPerusahaan" <?= $canEdit == 1 ? '' : 'readonly' ?>><?= $model['AlamatPerusahaan'] ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Website</label>
                                            <input type="text" class="form-control" id="WebsitePerusahaan" name="WebsitePerusahaan" value="<?= $model['WebsitePerusahaan'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?>>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Nama Pimpinan</label>
                                            <input type="text" class="form-control" id="NamaPimpinan" name="NamaPimpinan" value="<?= $model['NamaPimpinan'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?>>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleInputFile">Nama Bank</label>
                                            <input type="text" class="form-control" id="NamaBank" name="NamaBank" value="<?= $model['NamaBank'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?>>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Cabang Bank</label>
                                            <input type="text" class="form-control" id="CabangBank" name="CabangBank" value="<?= $model['CabangBank'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?>>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">No Akun Bank</label>
                                            <input type="number" class="form-control" id="NoAkunBank" name="NoAkunBank" value="<?= $model['NoAkunBank'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?>>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Atas Nama Bank</label>
                                            <input type="text" class="form-control" id="AtasNamaBank" name="AtasNamaBank" value="<?= $model['AtasNamaBank'] ?>" <?= $canEdit == 1 ? '' : 'readonly' ?>>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Pesan</label>
                                            <textarea type="text" rows="4" class="form-control" id="Pesan" name="Pesan" <?= $canEdit == 1 ? '' : 'readonly' ?>><?= $model['Pesan'] ?></textarea>
                                        </div>
                                        <div class="form-group" hidden>
                                            <label for="exampleInputFile">Limit Pinjaman Karyawan</label>
                                            <input type="text" class="form-control" id="LimitPinjamanKaryawan" name="LimitPinjamanKaryawan" value="<?= ($model['LimitPinjamanKaryawan'] != null) ? str_replace(['.', ',', '+'], ['+', '.', ','], number_format($model['LimitPinjamanKaryawan'], 2)) : 0 ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div>
                                            <label for="exampleInputFile">Tampilkan Balance</label>
                                            <input type="hidden" class="form-control" name="Balance" value="off">
                                            <div class="custom-control custom-switch switch-primary switch-md ">
                                                <input type="checkbox" class="custom-control-input" id="Balance" name="Balance" value="on" <?= $model['Balance'] == 'on' ? 'checked' : '' ?> <?= $canEdit == 1 ? '' : 'disabled' ?>>
                                                <label class="custom-control-label" for="Balance"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div>
                                            <label for="exampleInputFile">Live Mode Flip</label>
                                            <input type="hidden" class="form-control" name="FlipApi" value="off">
                                            <div class="custom-control custom-switch switch-primary switch-md ">
                                                <input type="checkbox" class="custom-control-input" id="FlipApi" name="FlipApi" value="on" <?= $model['FlipApi'] == 'on' ? 'checked' : '' ?> <?= $canEdit == 1 ? '' : 'disabled' ?>>
                                                <label class="custom-control-label" for="FlipApi"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div>
                                            <label for="exampleInputFile">Setting Jurnal Otomatis</label>
                                            <input type="hidden" class="form-control" name="SettingJurnal" value="off">
                                            <div class="custom-control custom-switch switch-primary switch-md ">
                                                <input type="checkbox" class="custom-control-input" id="SettingJurnal" name="SettingJurnal" value="on" <?= $model['SettingJurnal'] == 'on' ? 'checked' : '' ?> <?= $canEdit == 1 ? '' : 'disabled' ?>>
                                                <label class="custom-control-label" for="SettingJurnal"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="row clearfix">
                        <?php if ($this->session->userdata('LevelID') == 1) { ?>
                        <a href="#" type="button" id="btn-forcereset" class="btn btn-danger btn-sm">Force Reset Data</a>
                        <?php } if ($canEdit == 1) { ?>
                        <button type="submit" id="btnsave" class="btn btn-primary btn-sm ml-auto">Simpan</button>
                        <?php } ?>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ui-dialog" id="ModalReset" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Form Force Reset Data</h4>
            </div>
            <form action="<?= base_url('user/sistemsetting/forcereset') ?>" method="post" id="form-simpan-reset">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group mb-20">
                                <label for="password-field">Password</label>
                                <input type="hidden" class="form-control" name="confirm" id="confirm" value="1">
                                <div class="position-relative input-group">
                                    <input name="password" id="password" type="password" class="form-control" placeholder="Password" onkeyup="matching()" required>
                                    <div class="input-group-append" style="cursor: pointer">
                                        <div class="input-group-text">
                                            <i class="fa fa-eye" id="togglePassword"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-20">
                                <label for="password-field">Konfirmasi Password</label>
                                <div class="position-relative input-group">
                                    <input name="confirmation" id="confirmation" type="password" class="form-control" placeholder="Konfirmasi Password" onkeyup="matching()" required>
                                    <div class="input-group-append" style="cursor: pointer">
                                        <div class="input-group-text">
                                            <i class="fa fa-eye" id="togglePassword2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <label class="small"><span style="color: red; font-weight: bold;">*</span>Jika melakukan <b>Force Reset Data</b> maka semua Data Transaksi akan terhapus termasuk Data Master Barang, Data Master Jenis dan Kategori Barang, Data Master Gudang, Data Master Customer/Supplier dan Data Master Tahun Anggaran.</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnsavereset" disabled class="btn btn-primary waves-effect">Simpan</button>
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>