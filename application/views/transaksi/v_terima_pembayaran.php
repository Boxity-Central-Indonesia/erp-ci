<style type="text/css">
    #ModalEdit { overflow-y:scroll !important; }

    .btn span, .btn i {
        font-size: 13px;
        display: inline-block;
        color: white;
        margin-right: none !important;
    }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php
                        $FiturID = 25; //FiturID di tabel serverfitur
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
                        <!-- <a target="_blank" href="<?= base_url('transaksi/transaksi_penjualan/cetakterimapembayaran/' . base64_encode($dtiinduk['KodePerson'])) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
                            <i class="la la-download"></i> Cetak
                        </a> -->
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

                    <form action="<?= base_url('transaksi/transaksi_penjualan/terimapembayaranproses') ?>" method="post" id="form-terimabayar">
                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless" style="font-size:14px;">
                                <tr>
                                    <td style="width: 15%;">Kode Customer </td>
                                    <td style="width: 35%;">: <?= $dtinduk['KodePerson'] ?></td>
                                    <td style="width: 15%;">Tanggal Transaksi</td>
                                    <td style="width: 2%;">:</td>
                                    <td style="width: 33%;"><input class="form-control" type="date" name="TanggalTransaksi" id="TglTransaksiMain" required></td>
                                </tr>
                                <tr>
                                    <td style="width: 15%;">Nama Customer </td>
                                    <td style="width: 35%;">: <?= $dtinduk['NamaPersonCP'] ?></td>
                                    <td style="width: 15%;">Kode Akun Kas/Bank</td>
                                    <td style="width: 2%;">:</td>
                                    <td style="width: 33%;">
                                        <select class="form-control form-select select2" name="" id="KodeAkun">
                                            <option value="">Pilih Kode Akun</option>
                                            <?php
                                                if ($dtakun) {
                                                    foreach ($dtakun as $value) {
                                                        echo '<option value="'.$value['KodeAkun'].'">'.$value['KodeAkun'].' - '.$value['NamaAkun'].'</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <table id="table-terimabayar" class="table mb-0 table-borderless">
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
                                    <div class="col-sm-12">
                                        <div>Daftar Transaksi Pembelian</div>
                                    </div>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:3%;"></th>
                                        <th style="display: table-cell; width:4%;"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Transaksi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Tanggal Pembelian </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Total Tagihan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Total Dibayar </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Sisa Tagihan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Dibayar Sekarang </span></th>
                                        <th style="display: table-cell; width:3%">#</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>

                    <div class="row clearfix">
                        <input type="hidden" name="KodeAkun" id="KodeAkunInp" readonly>
                        <a href="<?= base_url('transaksi/transaksi_penjualan/detail/' . base64_encode($IDTransJual)) ?>" type="button" class="btn btn-sm btn-secondary">Kembali</a>
                        <?php 
                        if ($countdata > 0 && $status_jurnal == 'on') {
                        ?>
                        <button type="submit" id="simpanbayar" class="btn btn-primary btn-sm ml-auto">Simpan</button>
                        <?php } ?>
                    </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ui-dialog" id="ModalEdit" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Edit Terima Piutang</h4>
            </div>
            <form action="<?= base_url('transaksi/transaksi_penjualan/editpertransaksi') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Kode Transaksi</label>
                                <input type="text" class="form-control" id="NoTransKas" name="NoTransKas" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Terima Piutang</label>
                                <input type="text" class="form-control" id="Tgl" name="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Bayar</label>
                                <input type="text" class="form-control" id="JenisTransaksiKas" name="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Nominal Bayar</label>
                                <input type="text" class="form-control" id="TotalEdit" name="TotalTransaksi" required>
                                <input type="hidden" class="form-control" id="KodeTahun" name="KodeTahun" readonly>
                                <input type="hidden" class="form-control" id="TanggalTransaksi" name="TanggalTransaksi" readonly>
                            </div>
                            <div class="form-group">
                                <label for="">Kode Akun Kas/Bank</label>
                                <select class="form-control form-select select2" name="KodeAkun" id="KodeAkuns">
                                    <option value="">Pilih Kode Akun</option>
                                    <?php
                                        if ($dtakun) {
                                            foreach ($dtakun as $value) {
                                                echo '<option value="'.$value['KodeAkun'].'">'.$value['KodeAkun'].' - '.$value['NamaAkun'].'</option>';
                                            }
                                        }
                                    ?>
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