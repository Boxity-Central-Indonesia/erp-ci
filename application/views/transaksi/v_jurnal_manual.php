<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    
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
                                    <td style="width:15%;">Kode Jurnal </td>
                                    <td style="width:35%;">: <?= $dtjurnal['IDTransJurnal'] ?></td>
                                    <td style="width:15%;">No Referensi </td>
                                    <td style="width:35%;">: <?= $dtjurnal['NoRefTrans'] ?></td>
                                </tr>
                                <tr>
                                    <td style="width:15%;">Tahun Anggaran </td>
                                    <td style="width:35%;">: <?= $dtjurnal['KodeTahun'] ?></td>
                                    <td style="width:15%;">Tanggal Transaksi </td>
                                    <td style="width:35%;">: <?= shortdate_indo(date('Y-m-d', strtotime($dtjurnal['TglTransJurnal']))) . ' ' . date('H:i', strtotime($dtjurnal['TglTransJurnal'])) ?></td>
                                </tr>
                                <tr>
                                    <td style="width:15%;">Narasi Jurnal </td>
                                    <td style="width:35%;">: <?= $dtjurnal['NarasiJurnal'] ?></td>
                                    <td style="width:15%;">Nominal Transkasi </td>
                                    <td style="width:35%;">: <span id="nominaltransaksi"></span></td>
                                </tr>
                                <tr>
                                    <td style="width:15%;">Created By </td>
                                    <td style="width:35%;">: <?= $dtjurnal['ActualName'] ?></td>
                                    <td style="width:50%;" colspan="2"></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Jurnal</div>
                                    <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add ml-auto">
                                        <i class="la la-plus"></i> Tambah Data
                                    </button>
                                </div>
                            </div>
                            <table id="table-jurnalmanual" class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Akun </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Akun </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Keterangan </span></th>
                                        <th style="display: table-cell; text-align: center;"><span class="userDatatable-title">Debet </span></th>
                                        <th style="display: table-cell; text-align: center;"><span class="userDatatable-title">Kredit </span></th>
                                        <th style="display: table-cell; width:15%;">#</th>
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
                        <?php if ($dtjurnal['NarasiJurnal'] != 'Transaksi Hutang' && $dtjurnal['NarasiJurnal'] != 'Transaksi Terima Piutang') { ?>
                        <a href="javascript:history.back()" id="" class="btn btn-sm btn-secondary">Kembali</a>
                        <?php } ?>
                        <a href="#" id="check_simpanjurnal" data-kode="<?= $IDTransJurnal ?>" class="btn btn-sm btn-primary ml-auto">Simpan</a>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data Penjurnalan Manual Transaksi</h4>
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
                                <input type="text" class="form-control" id="Nominal" name="Nominal">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Keterangan</label>
                                <textarea class="form-control" rows="3" id="Uraian" name="Uraian"></textarea>
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