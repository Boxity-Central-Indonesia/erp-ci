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
                    if ($nominaltransaksi > 0) {
                        if ($nominaltransaksi > $totaljurnaldebet || $nominaltransaksi > $totaljurnalkredit) {
                    ?>
                            <div class="action-btn">
                                <a href="<?= base_url('transaksi/transaksi_kas/jurnalmanual/' . base64_encode('trans_beli') . '/' . base64_encode($idtransjurnal) . '/' . base64_encode($IDTransBeli) . '/' . base64_encode('approval_pembelian/detail')) ?>" class="btn btn-info btn-sm btn-add" type="button">
                                    <i class="las la-journal-whills"></i> Jurnalkan
                                </a>
                            </div>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    $FiturID = 12; //FiturID di tabel serverfitur
                    $canPrint = 0;
                    $print = [];
                    foreach ($this->session->userdata('fiturprint') as $key => $value) {
                        $print[$key] = $value;
                        if ($key == $FiturID && $value == 1) {
                            $canPrint = 1;
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
                    ?>
                    <?php if ($canPrint == 1) { ?>
                        <div class="dropdown action-btn">
                            <a target="_blank" href="<?= base_url('transaksi/approval_pembelian/cetakdetail/' . base64_encode($IDTransBeli)) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
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
                                <tr class="userDatatable-header">
                                    <td>Nomor PO </td>
                                    <td>: <?= $dtinduk['IDTransBeli'] ?></td>
                                    <td>Kode Supplier </td>
                                    <td>: <?= $dtinduk['KodePerson'] ?></td>
                                </tr>
                                <tr class="userDatatable-header">
                                    <td>Tanggal PO </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TglPO']))) . ' ' . date('H:i', strtotime($dtinduk['TglPO'])) ?></td>
                                    <td>Nama Supplier </td>
                                    <td>: <?= $dtinduk['NamaUsaha'] ?></td>
                                </tr>
                                <tr>
                                    <td>Created By</td>
                                    <td>: <?= $dtinduk['UserPO'] ?></td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr class="userDatatable-header">
                                    <td>Nomor Approved </td>
                                    <td>: <?= $dtinduk['ApprovedNo'] ?></td>
                                    <td>Approved Oleh </td>
                                    <td>: <?= $dtinduk['ApprovedBy'] ?></td>
                                </tr>
                                <tr class="userDatatable-header">
                                    <td>Tanggal Approved </td>
                                    <td>: <?= isset($dtinduk['ApprovedDate']) ? shortdate_indo(date('Y-m-d', strtotime($dtinduk['ApprovedDate']))) . ' ' . date('H:i', strtotime($dtinduk['ApprovedDate'])) : '' ?></td>
                                    <td>Keterangan </td>
                                    <td>: <?= $dtinduk['ApprovedDesc'] ?></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div>Daftar Item Detail</div>
                            </div>
                            <table id="table-approvaldetail" class="table mb-0 table-borderless">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Satuan Barang </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Harga Satuan </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Quantity </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Total </span></th>
                                        <!-- <th style="display: table-cell; width:15%;">#</th> -->
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                                <tfoot style="font-size:14px;">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Jumlah</td>
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
                        <?php
                        if ($canEdit == 1) {
                            if ($dtinduk['StatusProses'] == "PO" && $dtinduk['TotalTagihan'] > 0) {
                        ?>
                                <a href="<?= base_url('transaksi/trans_beli') ?>" class="btn btn-sm btn-secondary">Kembali</a>
                                <button class="btn btn-sm btn-primary ml-auto" id="btn-approve">Approve</button>
                            <?php } else { ?>
                                <a href="<?= base_url('transaksi/trans_beli') ?>" class="btn btn-sm btn-primary ml-auto">Selesai</a>
                        <?php
                            }
                        }
                        ?>
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
                <h4 class="title" id="defaultModalLabel">Approval Transaksi Pembelian</h4>
            </div>
            <form action="<?= base_url('transaksi/approval_pembelian/approve') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Kode PO</label>
                                <input type="text" class="form-control" id="IDTransBeli" name="IDTransBeli" value="<?= $dtinduk['IDTransBeli'] ?>" readonly>
                                <input type="hidden" class="form-control" id="TotalTagihan" name="TotalTagihan" value="<?= $dtinduk['TotalTagihan'] ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Approval</label>
                                <input type="datetime-local" class="form-control" id="ApprovedDate" name="ApprovedDate" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Keterangan</label>
                                <textarea class="form-control" rows="3" id="ApprovedDesc" name="ApprovedDesc"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group" hidden>
                                <label for="exampleInputFile">No Referensi Pembelian</label>
                                <input type="text" class="form-control" id="NoRef_Manual" name="NoRef_Manual" value="">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Pembelian</label>
                                <input type="datetime-local" class="form-control" id="TanggalPembelian" name="TanggalPembelian" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Uraian Pembelian</label>
                                <textarea class="form-control" rows="3" id="UraianPembelian" name="UraianPembelian"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Approval</label><br>
                                <div class="icheck-success d-inline">
                                    <input type="radio" id="radioPrimary1" name="StatusProses" value="APPROVED" required>
                                    <label for="radioPrimary1">
                                        Ya
                                    </label>
                                </div>
                                &nbsp;&nbsp;&nbsp;
                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="radioPrimary2" name="StatusProses" value="FAILED" required>
                                    <label for="radioPrimary2">
                                        Tidak
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