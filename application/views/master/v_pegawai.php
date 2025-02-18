<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
    #ModalImport { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php
                        $FiturID = 10; //FiturID di tabel serverfitur
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
                        <button type="button" id="btnimport" class="btn btn-success btn-sm btn-add">
                            <i class="las la-file-import"></i> Import Data Pegawai
                        </button>
                    </div>
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
                    Daftar Pegawai
                </div>
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <table id="table-pegawai" class="table mb-0 table-borderless">
                                <thead>
                                    <tr>
                                        <td colspan="3">
                                            <div class="form-group">
                                                <label class="form-control-label">Jabatan</label>
                                                <select class="form-control" id="combo-jab">
                                                    <option value="">Semua Jabatan</option>
                                                    <?php foreach ($dtjab as $key) { ?>
                                                        <option value="<?= $key['KodeJabatan'] ?>"><?= $key['NamaJabatan'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td colspan="2">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">NIP </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Pegawai </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jabatan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jabatan Atasan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Pegawai </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Status </span></th>
                                        <?php if ($canEdit == 1 || $canDelete == 1) { ?>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data Pegawai</h4>
            </div>
            <form action="<?= base_url('master/pegawai/simpan') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Pegawai</label>
                                <input type="hidden" class="form-control" id="KodePegawai" name="KodePegawai" value="">
                                <input type="hidden" class="form-control" id="emailLama" name="">
                                <input type="hidden" class="form-control" id="nipLama" name="">
                                <input type="hidden" class="form-control" id="fingerLama" name="">
                                <input type="hidden" name="Isedit" id="Isedit" value="tambah">
                                <input type="text" class="form-control" id="NamaPegawai" name="NamaPegawai" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">NIP</label>
                                <input type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==20) return false;" class="form-control" id="NIP" name="NIP" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tempat Tanggal Lahir</label>
                                <input type="text" class="form-control" id="TTL" name="TTL" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Alamat</label>
                                <input type="text" class="form-control" id="Alamat" name="Alamat" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">No HP</label>
                                <input type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==15) return false;" class="form-control" id="TelpHP" name="TelpHP" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Email</label>
                                <input type="email" class="form-control" id="Email" name="Email" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Kode Finger</label>
                                <input type="text" class="form-control" id="IDFinger" name="IDFinger" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Pegawai</label>
                                <input type="hidden" class="form-control" name="JenisPegawai" id="JenisPegawai2">
                                <select class="form-control form-select" name="JenisPegawai" id="JenisPegawai" required>
                                    <option value="" selected>Pilih Jenis Pegawai</option>
                                    <option value="Probation">Probation</option>
                                    <option value="Kontrak">Kontrak</option>
                                    <option value="Tetap">Tetap</option>
                                    <option value="Gaji Harian">Gaji Harian</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Mulai Kerja</label>
                                <input type="date" class="form-control" id="TglMulaiKerja" name="TglMulaiKerja" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Resign</label>
                                <input type="date" class="form-control" id="TglResign" name="TglResign" value="">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Bank</label>
                                <select class="form-control form-select select2" name="KodeBank" id="KodeBank" required>
                                    <option value="" selected>Pilih Bank</option>
                                    <?php if ($dtbank) {
                                        foreach ($dtbank as $key) {
                                            echo '<option value="'.$key['bank_code'].'">'.$key['name'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">No Rekening</label>
                                <input type="number" class="form-control" id="NoRek" name="NoRek" value="" required>
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
                                <label for="exampleInputFile">Atasan</label>
                                <select class="form-control form-select select2" name="KodeJabAtasanLangsung" id="KodeJabAtasanLangsung">
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

<div class="modal fade ui-dialog" id="ModalImport" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel2">Import Data Absensi Pegawai</h4>
            </div>
            <form action="<?= base_url('master/pegawai/importdata') ?>" method="post" id="form-import" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Pilih File Excel</label>
                                <input type="file" class="form-control" id="file" name="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                <div class="row clearfix">
                                    <label class="small">Hanya ekstensi .xlsx</label>
                                    <a target="_blank" href="<?= base_url('assets/contoh_template_data_pegawai.xlsx') ?>" class="ml-auto"><i class="la la-download"></i><span style="font-size:12px;">&nbsp;Download template excel .xlsx</span></a>
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