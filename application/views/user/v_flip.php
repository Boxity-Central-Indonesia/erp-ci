<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <div class="action-btn" hidden>

                        <div class="form-group mb-0">
                            <div class="input-container icon-left position-relative">
                                <span class="input-icon icon-left">
                                    <span data-feather="calendar"></span>
                                </span>
                                <input type="text" class="form-control form-control-default date-ranger" name="date-ranger" placeholder="Oct 30, 2019 - Nov 30, 2019">
                                <span class="input-icon icon-right">
                                    <span data-feather="chevron-down"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php
                        $FiturID = 66; //FiturID di tabel serverfitur
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
                    ?>
                    <?php if ($canPrint == 1) { ?>
                    <div class="dropdown action-btn" hidden>
                        <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="la la-download"></i> Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <span class="dropdown-item">Export With</span>
                            <div class="dropdown-divider"></div>
                            <a href="" class="dropdown-item">
                                <i class="la la-file-pdf"></i> PDF</a>
                            <a href="" class="dropdown-item">
                                <i class="la la-file-excel"></i> Excel (XLSX)</a>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if ($canAdd == 1) { ?>
                    <div class="action-btn">
                        <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add">
                            <i class="las la-wallet"></i> Topup
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
                            <table id="table-flip" class="table mb-0 table-borderless">
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
                                        <td colspan="3">
                                            <div class="form-group">
                                                <label class="form-control-label">Pencarian Data</label>
                                                <input id="inp-search" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">ID Transaksi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Tipe Pembayaran </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Bank </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">No Rekening </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Mutasi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Batas Waktu </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Status </span></th>
                                        <th style="display: table-cell; width:15%;">#</th>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data User Login</h4>
            </div>
            <form action="<?= base_url('user/flip/simpan') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">ID Transaksi (Auto Generated)</label>
                                <input type="text" class="form-control" id="Title" name="Title" value="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Bank</label>
                                <select class="form-control form-select select2" name="SenderBank" id="SenderBank" required>
                                    <option value="" selected>Pilih Bank</option>
                                    <?php if ($dtbank) {
                                        foreach ($dtbank as $key) {
                                            echo '<option value="'.$key['bank_code'].'">'.$key['name'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jumlah</label>
                                <input type="text" class="form-control" id="Amount" name="Amount" value="" required>
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