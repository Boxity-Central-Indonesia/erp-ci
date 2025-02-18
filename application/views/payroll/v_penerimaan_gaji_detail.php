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
                    if ($nominaltransaksi > 0) {
                        if ($nominaltransaksi > $totaljurnaldebet || $nominaltransaksi > $totaljurnalkredit) {
                    ?>
                    <div class="action-btn">
                        <a href="<?= base_url('transaksi/transaksi_kas/jurnalmanual/' . base64_encode('penerimaangaji') . '/' . base64_encode($idtransjurnal) . '/' . base64_encode($IDRekap) . '/' . base64_encode('penerimaan_gaji/detail')) ?>" class="btn btn-info btn-sm btn-add" type="button">
                            <i class="las la-journal-whills"></i> Jurnalkan
                        </a>
                    </div>
                    <?php
                        }
                    }
                    ?>
                    <?php
                        setlocale(LC_ALL, 'IND');
                        $FiturID = 53; //FiturID di tabel serverfitur
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
                        <a target="_blank" href="<?= base_url('payroll/penerimaan_gaji/cetakdetail/' . base64_encode($IDRekap)) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
                            <i class="la la-download"></i> Cetak
                        </a>
                        <!-- <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="la la-download"></i> Cetak
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <span class="dropdown-item">Cetak Dengan</span>
                            <div class="dropdown-divider"></div>
                            <a href="" class="dropdown-item">
                                <i class="la la-file-pdf"></i> PDF</a>
                            <a href="" class="dropdown-item">
                                <i class="la la-file-excel"></i> Excel (XLSX)</a>
                        </div> -->
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <!-- <div class="card-header color-dark fw-500">
                </div> -->
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless" style="font-size:14px;">
                                <tr>
                                    <td>NIP </td>
                                    <td>: <?= $dtinduk['NIP'] ?></td>
                                    <td>Nama Pegawai </td>
                                    <td>: <?= $dtinduk['NamaPegawai'] ?></td>
                                </tr>
                                <tr>
                                    <td>Jabatan </td>
                                    <td>: <?= $dtinduk['NamaJabatan'] ?></td>
                                    <td>Periode </td>
                                    <td>: <?= strftime('%B %Y', strtotime($dtinduk['Bulan'])) ?></td>
                                </tr>
                                <tr>
                                    <td>Created By</td>
                                    <td>: <?= $dtinduk['ActualName'] ?></td>
                                    <td colspan="2"></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix" style="font-size:14px;">
                                    <div>Komponen Gaji</div>
                                    <div class="ml-auto">Total Perolehan: <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['InsentifPegawai'], 2)) ?></div>
                                    <?php if ($canAdd == 1) { ?>
                                    <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add ml-auto" hidden>
                                        <i class="la la-plus"></i> Tambah Data
                                    </button>
                                    <?php } ?>
                                </div>
                            </div>
                            <table id="table-pgajidetail" class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Nama Komponen / Aktivitas </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Perolehan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Keterangan </span></th>
                                        <?php
                                        if ($canEdit == 1 || $canDelete == 1) { ?>
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
                <div class="card-footer">
                    <div class="row clearfix">
                        <a href="<?=base_url('payroll/penerimaan_gaji')?>" class="btn btn-sm btn-secondary">Kembali</a>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Penerimaan Gaji Detail</h4>
            </div>
            <form action="<?= base_url('payroll/penerimaan_gaji/simpandetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Harga Satuan</label>
                                <input type="text" class="form-control" id="HargaSatuan" name="HargaSatuan" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Quantity</label>
                                <input type="number" class="form-control" id="Qty" name="Qty" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Diskon</label><br>
                                <div class="icheck-success d-inline">
                                    <input type="radio" id="radioPrimary1" name="JenisDiskon" value="Nominal" required>
                                    <label for="radioPrimary1">
                                        Nominal
                                    </label>
                                </div>
                                &nbsp;&nbsp;&nbsp;
                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="radioPrimary2" name="JenisDiskon" value="Persen" required>
                                    <label for="radioPrimary2">
                                        Persen
                                    </label>
                                </div>
                                &nbsp;&nbsp;&nbsp;
                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="radioPrimary3" name="JenisDiskon" value="None" required>
                                    <label for="radioPrimary3">
                                        Tanpa Diskon
                                    </label>
                                </div>
                                <input type="text" class="form-control" id="Diskon" name="Diskon">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Satuan Barang</label>
                                <input type="text" class="form-control" id="SatuanBarang" name="SatuanBarang" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Spesifikasi</label>
                                <input type="text" class="form-control" id="Spesifikasi" name="Spesifikasi" readonly>
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