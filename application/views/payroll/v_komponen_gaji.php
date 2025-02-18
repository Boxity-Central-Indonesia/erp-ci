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
                        $FiturID = 51; //FiturID di tabel serverfitur
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
                            <table id="table-kompgaji" class="table mb-0 table-borderless">
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
                                        <td colspan="2">
                                            <div class="form-group">
                                                <label class="form-control-label">Pencarian Data</label>
                                                <input id="inp-search" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Komponen Gaji </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Komponen </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Cara Hitung </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kriteria </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jabatan </span></th>
                                        <th style="display: table-cell; text-align: left;"><span class="userDatatable-title">Nominal</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Status </span></th>
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
                                <label for="exampleInputFile">Nama Komponen Gaji</label>
                                <input type="hidden" class="form-control" id="KodeKompGaji" name="KodeKompGaji" value="">
                                <input type="text" class="form-control" id="NamaKomponenGaji" name="NamaKomponenGaji" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Komponen</label>
                                <select class="form-control form-select select2" name="JenisKomponen" id="JenisKomponen" required>
                                    <option value="" selected>Pilih Jenis Komponen Gaji</option>
                                    <option value="UANG MAKAN">Uang Makan</option>
                                    <option value="LEMBUR">Lembur</option>
                                    <option value="THR">Tunjangan Hari Raya</option>
                                    <option value="TUNJANGAN JABATAN">Tunjangan Jabatan</option>
                                    <option value="TUNJANGAN DINAS LUAR">Tunjangan Dinas Luar</option>
                                    <option value="POT TELAT">Potongan Telat</option>
                                    <option value="POT ALPHA">Potongan Alpha</option>
                                    <option value="POT CUTI">Potongan Cuti</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Cara Hitung</label>
                                <select class="form-control form-select" name="CaraHitung" id="CaraHitung" required>
                                    <option value="" selected>Pilih Cara Hitung</option>
                                    <option value="Tambah">Tambah</option>
                                    <option value="Kurang">Kurang</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Kriteria</label>
                                <select class="form-control form-select" name="Kriteria" id="Kriteria" required>
                                    <option value="" selected>Pilih Kriteria</option>
                                    <option value="Menit">Menit</option>
                                    <option value="Jam">Jam</option>
                                    <option value="Harian">Harian</option>
                                    <option value="Bulanan">Bulanan</option>
                                    <option value="Tahunan">Tahunan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jabatan</label>
                                <select class="form-control form-select select2" name="KodeJabatan" id="KodeJabatan" required>
                                    <option value="" selected>Pilih Jabatan</option>
                                    <?php if($dtjab){
                                        foreach ($dtjab as $key) {
                                            echo '<option value="'.$key['KodeJabatan'].'">'.$key['NamaJabatan'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Nominal</label>
                                <input type="text" class="form-control" id="NominalRp" name="NominalRp" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Deskripsi</label>
                                <textarea class="form-control" rows="3" id="Deskripsi" name="Deskripsi"></textarea>
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