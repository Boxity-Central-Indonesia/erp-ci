<style type="text/css">
    #ModalTambah {
        overflow-y: scroll !important;
    }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php
                    $FiturID = 27; //FiturID di tabel serverfitur
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
                            <a target="_blank" href="<?= base_url('transaksi/terima_piutang/cetakdetail/' . base64_encode($IDTransJual)) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
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

                    <div class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless" style="font-size:14px;">
                                <tr>
                                    <td>Kode Transaksi Penjualan </td>
                                    <td>: <?= $dtinduk['IDTransJual'] ?></td>
                                    <td>Kode Customer </td>
                                    <td>: <?= $dtinduk['KodePerson'] ?></td>
                                </tr>
                                <tr>
                                    <td>No Ref Penjualan </td>
                                    <td>: <?= $dtinduk['NoRef_Manual'] ?></td>
                                    <td>Nama Customer </td>
                                    <td>: <?= $dtinduk['NamaUsaha'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Penjualan </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalPenjualan']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalPenjualan'])) ?></td>
                                    <td colspan="2"></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div>Daftar Detail</div>
                            </div>
                            <table id="table-terimapiutangdetail" class="table mb-0 table-borderless">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Transaksi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">No Ref Terima Piutang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Tanggal Terima Piutang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Keterangan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nominal Dibayar </span></th>
                                        <?php
                                        if ($canEdit == 1 || $canDelete == 1) {
                                            if ($dtinduk['StatusBayar'] != 'LUNAS') {
                                        ?>
                                                <th style="display: table-cell; width:15%;">#</th>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                                <tfoot style="font-size: 14px;">
                                    <tr>
                                        <td colspan="7"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td class="text-center">Total Tagihan</td>
                                        <td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['TotalTagihan'], 2)) ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td class="text-center">Total Dibayar</td>
                                        <td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($TotalBayar, 2)) ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td class="text-center">Sisa Tagihan</td>
                                        <td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($SisaTagihan, 2)) ?></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <a href="<?= base_url('transaksi/trans_jual') ?>" type="button" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ui-dialog" id="ModalTambah" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Terima Piutang</h4>
            </div>
            <form action="<?= base_url('transaksi/terima_piutang/simpandetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Kode Transaksi</label>
                                <input type="text" class="form-control" id="NoTransKas" name="NoTransKas" readonly>
                                <input type="hidden" class="form-control" id="NoRef_Sistem" name="NoRef_Sistem">
                                <input type="hidden" class="form-control" id="KodePerson" name="KodePerson" value="<?= $dtinduk['KodePerson'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">No Ref Terima Piutang</label>
                                <input type="text" class="form-control" id="NoRef_Manual" name="NoRef_Manual" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Terima Piutang</label>
                                <input type="datetime-local" class="form-control" id="TanggalTransaksi" name="TanggalTransaksi" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Keterangan</label>
                                <textarea class="form-control" id="Uraian" name="Uraian" rows="3" readonly></textarea>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Total Dibayar</label>
                                <input type="text" class="form-control" id="TotalTransaksi" name="TotalTransaksi">
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