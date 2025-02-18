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
                        $FiturID = 49; //FiturID di tabel serverfitur
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
                            <i class="las la-file-import"></i> Import Data
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
                            <table id="table-absensipegawai" class="table mb-0 table-borderless">
                                <thead>
                                    <tr>
                                        <td colspan="2">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">Menit Pelanggaran </span></th>
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
                <h4 class="title" id="defaultModalLabel">Import Data Absensi Pegawai</h4>
            </div>
            <form action="<?= base_url('payroll/import_absensi/simpan') ?>" method="post" id="form-simpan" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Pilih File Excel</label>
                                <input type="file" class="form-control" id="file" name="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                <div class="row clearfix">
                                    <label class="small">Hanya ekstensi .xlsx</label>
                                    <a target="_blank" href="<?= base_url('assets/contoh_template.xlsx') ?>" class="ml-auto"><i class="la la-download"></i><span style="font-size:12px;">&nbsp;Download template excel .xlsx</span></a>
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