<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }

    .text-wrap{
        white-space: normal;
    }
    .width-250{
        width: 250px;
    }

    .vertical-align{
        vertical-align: top !important;
    }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php
                        $FiturID = 47; //FiturID di tabel serverfitur
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
                    <div class="dropdown action-btn">
                        <a target="_blank" href="<?= base_url('transaksi/transaksi_kas/cetakjurnal/' . base64_encode($IDTransJurnal)) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
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
                                    <td style="width:15%; vertical-align:top;">Kode Transaksi </td>
                                    <td style="width:35%; vertical-align:top;">: <?= $dtjurnal['NoTransKas'] ?></td>
                                    <td style="width:15%; vertical-align:top;">Tahun Anggaran </td>
                                    <td style="width:35%; vertical-align:top;">: <?= $dtjurnal['KodeTahun'] ?></td>
                                </tr>
                                <tr>
                                    <td style="width:15%; vertical-align:top;">Jenis Transaksi </td>
                                    <td style="width:35%; vertical-align:top;">: <?= $dtjurnal['JenisTransaksiKas'] ?></td>
                                    <td style="width:15%; vertical-align:top;">Tanggal Transaksi </td>
                                    <td style="width:35%; vertical-align:top;">: <?= shortdate_indo(date('Y-m-d', strtotime($dtjurnal['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($dtjurnal['TanggalTransaksi'])) ?></td>
                                </tr>
                                <tr>
                                    <td style="width:15%; vertical-align:top;">Uraian </td>
                                    <td style="width:35%; vertical-align:top;">: <?= ($dtjurnal['NoRef_Sistem'] == null) ? wordwrap($dtjurnal['Uraian'], 50, "<br>\n") : 'Transaksi Pinjaman Karyawan' ?></td>
                                    <td style="width:15%; vertical-align:top;">Nominal Transkasi </td>
                                    <td style="width:35%; vertical-align:top;">: <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtjurnal['NominalTransaksi'], 2)) ?></td>
                                </tr>
                                <tr>
                                    <td style="width:15%; vertical-align:top;">Created By </td>
                                    <td style="width:35%; vertical-align:top;">: <?= $dtjurnal['ActualName'] ?></td>
                                    <td style="width:50%; vertical-align:top;" colspan="2"></td>
                                </tr>
                            </table>
                        </div>
                        <br>
                        <div class="col-sm-12">
                            <div class="action-btn row clearfix">
                                <div>Daftar Item Jurnal</div>
                                <?php if ($canAdd == 1) {
                                    if ($dtjurnal['NoRef_Sistem'] == null) { ?>
                                <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add ml-auto">
                                    <i class="la la-plus"></i> Tambah Data
                                </button>
                                <?php }
                                } ?>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="table-jurnalkas" class="table mb-0 table-borderless">
                                <thead>
                                    <tr hidden>
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
                                        <td colspan="3">
                                            <div class="form-group">
                                                <label class="form-control-label">Pencarian Data</label>
                                                <input id="inp-search" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Akun </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Akun </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Keterangan </span></th>
                                        <th style="display: table-cell; text-align: center;"><span class="userDatatable-title">Debet </span></th>
                                        <th style="display: table-cell; text-align: center;"><span class="userDatatable-title">Kredit </span></th>
                                        <?php if ($canEdit == 1 || $canDelete == 1) { ?>
                                            <th style="display: table-cell; width:15%;">#</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                                <tfoot style="font-size:14px;">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">Jumlah</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="row clearfix">
                        <a href="<?=base_url('transaksi/transaksi_kas')?>" class="btn btn-sm btn-secondary">Kembali</a>
                        <?php if ((int)$dtjurnal['NominalTransaksi'] > $saldoDebet || (int)$dtjurnal['NominalTransaksi'] > $saldoKredit) {
                            if ($dtjurnal['NoRef_Sistem'] == null) { ?>
                        <a href="#" id="check_simpanjurnal" data-kode="<?= $IDTransJurnal ?>" class="btn btn-sm btn-primary ml-auto">Simpan</a>
                        <?php }
                        } ?>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Pembelian Detail</h4>
            </div>
            <form action="<?= base_url('transaksi/transaksi_kas/simpandetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Jurnal</label>
                                <select class="form-control form-select" name="JenisJurnal" id="JenisJurnal" required>
                                    <option value="" selected>Pilih Jenis Jurnal</option>
                                    <option value="Debet">Debet</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Kode Akun | Nama Akun</label>
                                <input type="hidden" class="form-control" id="NoUrut" name="NoUrut">
                                <input type="hidden" class="form-control" id="IDTransJurnal" name="IDTransJurnal" value="<?= $IDTransJurnal ?>">
                                <input type="hidden" class="form-control" id="KodeTahun" name="KodeTahun" value="<?= $dtjurnal['KodeTahun'] ?>">
                                <input type="hidden" class="form-control" id="NamaAkun" name="NamaAkun">
                                <select class="form-control form-select select2" name="KodeAkun" id="KodeAkun" disabled required>
                                    <option value="" selected>Pilih Akun</option>
                                    <?php if($dtakun){
                                        foreach ($dtakun as $key) {
                                            echo '<option value="'.$key['KodeAkun'].'">'.$key['KodeAkun'].' | '.$key['NamaAkun'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Nominal</label>
                                <input type="text" class="form-control" id="Nominal" name="Nominal" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Keterangan</label>
                                <textarea class="form-control" rows="3" id="Uraian" name="Uraian" required></textarea>
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