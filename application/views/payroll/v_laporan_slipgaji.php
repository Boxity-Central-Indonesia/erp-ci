<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
    #ModalCair { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php
                        $FiturID = 55; //FiturID di tabel serverfitur
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
                        $canPrint = 0;
                        $print = [];
                        foreach ($this->session->userdata('fiturprint') as $key => $value) {
                            $print[$key] = $value;
                            if ($key == $FiturID && $value == 1) {
                                $canPrint = 1;
                            }
                        }
                    ?>
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <!-- <div class="card-header color-dark fw-500">
                    Daftar <?= @$title ?>
                </div> -->
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="tab-wrapper">
                            <div class="atbd-tab tab-horizontal">
                                <ul class="nav nav-tabs vertical-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-slip-tab" data-toggle="tab" href="#custom-tabs-for-slip" role="tab" aria-controls="custom-tabs-for-slip" aria-selected="true">Slip Gaji</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-insentif-tab" data-toggle="tab" href="#custom-tabs-for-insentif" role="tab" aria-controls="custom-tabs-for-insentif" aria-selected="false">Insentif Pegawai</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-pinjaman-tab" data-toggle="tab" href="#custom-tabs-for-pinjaman" role="tab" aria-controls="custom-tabs-for-pinjaman" aria-selected="false">Pinjaman Pegawi/Karyawan</a>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="custom-tabs-for-slip" role="tabpanel" aria-labelledby="custom-tabs-slip-tab">
                                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                                        <div class="table-responsive">
                                            <table id="table-lapslipgaji" class="table mb-0 table-borderless">
                                                <thead>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Pencarian Periode</label>
                                                                <input id="bulan" placeholder="Periode" class="form-control" style="padding: 5px; box-sizing: border-box;" type="month" value="<?= $month ?>">
                                                            </div>
                                                        </td>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Pencarian Data</label>
                                                                <input id="inp-search" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" type="text">
                                                            </div>
                                                        </td>
                                                        <td colspan="2"></td>
                                                        <td colspan="2">
                                                            <?php if ($canPrint == 1) { ?>
                                                            <a target="_blank" href="<?= base_url('payroll/laporan_slipgaji/cetaklist_slip/' . $month) ?>" id="cetak-slip" class="btn btn-sm btn-default btn-primary dropdown-toggle" type="button" style="float:right;">
                                                                <i class="la la-download"></i> Cetak
                                                            </a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="userDatatable-header">
                                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">NIP </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Pegawai </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Jabatan </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Periode </span></th>
                                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Total Perolehan </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Status Dibayarkan </span></th>
                                                        <?php if ($canPrint == 1) { ?>
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
                                <div class="tab-pane fade" id="custom-tabs-for-insentif" role="tabpanel" aria-labelledby="custom-tabs-insentif-tab">
                                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                                        <div class="table-responsive">
                                            <table id="table-insentif" class="table mb-0 table-borderless">
                                                <thead>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Pencarian Periode</label>
                                                                <input id="bulan-ins" placeholder="Periode" class="form-control" style="padding: 5px; box-sizing: border-box;" type="month" value="<?= $month_ins ?>">
                                                            </div>
                                                        </td>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Pencarian Data</label>
                                                                <input id="inp-search-ins" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" type="text">
                                                            </div>
                                                        </td>
                                                        <td colspan="2">
                                                            <?php if ($canPrint == 1) { ?>
                                                            <a target="_blank" href="<?= base_url('payroll/laporan_slipgaji/cetaklist_insentif/' . $month_ins) ?>" id="cetak-insentif" class="btn btn-sm btn-default btn-primary dropdown-toggle" type="button" style="float:right;">
                                                                <i class="la la-download"></i> Cetak
                                                            </a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="userDatatable-header">
                                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">NIP </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Pegawai </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Jabatan </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Periode </span></th>
                                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Total Perolehan </span></th>
                                                        <th style="display: table-cell; width:5%;">#</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="font-size:14px;">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-for-pinjaman" role="tabpanel" aria-labelledby="custom-tabs-pinjaman-tab">
                                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                                        <div class="table-responsive">
                                            <table id="table-pinjaman" class="table mb-0 table-borderless">
                                                <thead>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Pencarian Periode</label>
                                                                <input id="bulan-pjm" placeholder="Periode" class="form-control" style="padding: 5px; box-sizing: border-box;" type="month" value="<?= $month_pjm ?>">
                                                            </div>
                                                        </td>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Pencarian Data</label>
                                                                <input id="inp-search-pjm" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" type="text">
                                                            </div>
                                                        </td>
                                                        <td></td>
                                                        <td colspan="2">
                                                            <?php if ($canPrint == 1) { ?>
                                                            <a target="_blank" href="<?= base_url('payroll/laporan_slipgaji/cetaklist_pinjaman/' . $month_pjm) ?>" id="cetak-pinjaman" class="btn btn-sm btn-default btn-primary dropdown-toggle" type="button" style="float:right;">
                                                                <i class="la la-download"></i> Cetak
                                                            </a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="userDatatable-header">
                                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">NIP </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Pegawai </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Jabatan </span></th>
                                                        <th style="display: table-cell;"><span class="userDatatable-title">Periode </span></th>
                                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Nominal Pinjam </span></th>
                                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Sisa Belum Dibayar </span></th>
                                                        <th style="display: table-cell; width:5%;">#</th>
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
            </div>
        </div>
    </div>
</div>