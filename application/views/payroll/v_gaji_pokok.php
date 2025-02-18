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
                        $FiturID = 52; //FiturID di tabel serverfitur
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
                    <div class="action-btn" hidden>
                        <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add">
                            <i class="la la-plus"></i> Tambah Data
                        </button>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-header color-dark fw-500">
                    Daftar <?= @$title ?>
                </div>
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <table id="table-gajipokok" class="table mb-0 table-borderless">
                                <thead>
                                    <tr>
                                        <td colspan="2" hidden>
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">NIP </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Pegawai </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jabatan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jabatan Atasan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Pegawai </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Gaji Pokok </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Satuan Gaji </span></th>
                                        <?php if ($canEdit == 1) { ?>
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

<div class="modal fade ui-dialog" id="ModalTambah" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Tambah Data Komponen Gaji</h4>
            </div>
            <form action="<?= base_url('payroll/komponen_gaji/simpan') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Pegawai</label>
                                <input type="hidden" class="form-control" id="KodePegawai" name="KodePegawai" value="">
                                <input type="text" class="form-control" id="NamaPegawai" name="NamaPegawai" value="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">NIP</label>
                                <input type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==20) return false;" class="form-control" id="NIP" name="NIP" value="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Pegawai</label>
                                <select class="form-control form-select" name="JenisPegawai" id="JenisPegawai" disabled>
                                    <option value="" selected>Pilih Jenis Pegawai</option>
                                    <option value="Probation">Probation</option>
                                    <option value="Kontrak">Kontrak</option>
                                    <option value="Tetap">Tetap</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jabatan</label>
                                <select class="form-control form-select select2" name="KodeJabatan" id="KodeJabatan" disabled>
                                    <option value="" selected>Pilih Jabatan</option>
                                    <?php if($dtjab){
                                        foreach ($dtjab as $key) {
                                            echo '<option value="'.$key['KodeJabatan'].'">'.$key['NamaJabatan'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Atasan</label>
                                <select class="form-control form-select select2" name="KodeJabAtasanLangsung" id="KodeJabAtasanLangsung" disabled>
                                    <option value="" selected>Nama Atasan | Jabatan Atasan</option>
                                    <?php if($atasan){
                                        foreach ($atasan as $key) {
                                            echo '<option value="'.$key['KodePegawai'].'">' . $key['NamaPegawai'] . ' | ' . $key['NamaJabatan'] . '</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Gaji Pokok</label>
                                <input type="text" class="form-control" id="GajiPokok" name="GajiPokok" value="" required>
                            </div>
                            <div class="form-group">
                                <div class="checkbox-theme-default custom-checkbox ">
                                    <input class="checkbox" type="checkbox" id="IsGajiHarian" name="IsGajiHarian">
                                    <label for="IsGajiHarian">
                                        <span class="checkbox-text" style="color: #666d92;">
                                            Gaji Harian
                                        </span>
                                    </label>
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