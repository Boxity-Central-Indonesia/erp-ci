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
                        $FiturID = 50; //FiturID di tabel serverfitur
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
                            <table id="table-adjustment" class="table mb-0 table-borderless">
                                <thead>
                                    <tr>
                                        <td colspan="3">
                                            <div class="form-group">
                                                <label class="form-control-label">Pencarian Bulan Tahun</label>
                                                <input id="bulan" placeholder="Bulan Tahun" class="form-control" style="padding: 5px; box-sizing: border-box;" id="bulan" type="month" value="<?= date('Y-m') ?>">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">NIP </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Pegawai </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jabatan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Tanggal </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jam Kerja Masuk </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jam Kerja Pulang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jam Masuk </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jam Pulang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Ket </span></th>
                                        <?php if ($canEdit == 1 || $canDelete == 1) { ?>
                                        <th style="display: table-cell; width:5%;">#</th>
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
            <form action="<?= base_url('payroll/adjustment_absensi/simpan') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Pegawai</label>
                                <input type="hidden" class="form-control" id="kodepegLama" name="kodepegLama" value="" readonly>
                                <select class="form-control form-select select2" name="KodePegawai" id="KodePegawai" disabled required>
                                    <option value="" selected>Pilih Pegawai</option>
                                    <?php if($dtpeg){
                                        foreach ($dtpeg as $key) {
                                            echo '<option value="'.$key['KodePegawai'].'">'.$key['NIP'].' | '.$key['NamaPegawai'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jabatan</label>
                                <input type="text" class="form-control" id="NamaJabatan" name="" value="" readonly>
                                <input type="hidden" class="form-control" id="IDFinger" name="IDFinger" value="" readonly>
                                <input type="hidden" class="form-control" id="Isedit" name="Isedit" value="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal</label>
                                <input type="date" class="form-control" id="Tanggal" name="Tanggal" value="" readonly required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Jam Kerja Masuk</label>
                                <input type="time" class="form-control" id="JamKerjaMasuk" name="JamKerjaMasuk" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jam Masuk</label>
                                <input type="time" class="form-control" id="JamMasuk" name="JamMasuk" value="" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Jam Kerja Pulang</label>
                                <input type="time" class="form-control" id="JamKerjaPulang" name="JamKerjaPulang" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jam Pulang</label>
                                <input type="time" class="form-control" id="JamPulang" name="JamPulang" value="" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Keterangan</label>
                                <select class="form-control form-select" name="Keterangan" id="Keterangan" required>
                                    <option value="" selected>Pilih Keterangan</option>
                                    <option value="Hadir">Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Alpha">Alpha</option>
                                    <option value="Dinas Luar">Dinas Luar</option>
                                </select>
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