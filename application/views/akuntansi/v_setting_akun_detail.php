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
                        $FiturID = 58; //FiturID di tabel serverfitur
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
                        <a target="_blank" href="<?= base_url('akuntansi/setting_akun/cetakdetail/' . base64_encode($KodeSetAkun)) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
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
                                <?php
                                    if ($dtinduk['NamaTransaksi'] == 'PO') {
                                        $nama = "Cetak Pembelian (PO)";
                                    } elseif ($dtinduk['NamaTransaksi'] == 'Pembelian') {
                                        $nama = "Transaksi Pembelian";
                                    } elseif ($dtinduk['NamaTransaksi'] == 'Hutang') {
                                        $nama = "Transaksi Hutang";
                                    } elseif ($dtinduk['NamaTransaksi'] == 'SO') {
                                        $nama = "Transaksi Slip Order";
                                    } elseif ($dtinduk['NamaTransaksi'] == 'Penjualan') {
                                        $nama = "Transaksi Penjualan";
                                    } elseif ($dtinduk['NamaTransaksi'] == 'Piutang') {
                                        $nama = "Transaksi Terima Piutang";
                                    } elseif ($dtinduk['NamaTransaksi'] == 'Produksi') {
                                        $nama = "Proses Produksi";
                                    } elseif ($dtinduk['NamaTransaksi'] == 'BahanPro') {
                                        $nama = "Bahan Baku Produksi";
                                    } elseif ($dtinduk['NamaTransaksi'] == 'AktivitasPro') {
                                        $nama = "Biaya Aktivitas Produksi";
                                    } elseif ($dtinduk['NamaTransaksi'] == 'Penggajian') {
                                        $nama = "Penggajian";
                                    } elseif ($dtinduk['NamaTransaksi'] == 'Penutup') {
                                        $nama = "Jurnal Penutup";
                                    } else {
                                        $nama = $dtinduk['NamaTransaksi'];
                                    }
                                ?>
                                <tr>
                                    <td style="width:10%;">Nama Transaksi </td>
                                    <td style="width:90%;">: <?= $nama ?></td>
                                </tr>
                                <tr>
                                    <td style="width:10%;">Jenis Transaksi </td>
                                    <td style="width:90%;">: <?= $dtinduk['JenisTransaksi'] ?></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Setting</div>
                                    <?php if ($canAdd == 1) { ?>
                                    <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add ml-auto">
                                        <i class="la la-plus"></i> Tambah Data
                                    </button>
                                    <?php } ?>
                                </div>
                            </div>
                            <table id="table-setakundetail" class="table mb-0 table-borderless">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Jurnal </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Status Akun </span></th>
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
                <div class="card-footer">
                    <a href="<?=base_url('akuntansi/setting_akun')?>" class="btn btn-sm btn-secondary">Kembali</a>
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
            <form action="<?= base_url('akuntansi/setting_akun/simpandetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Jurnal</label>
                                <input type="hidden" class="form-control" id="action" name="">
                                <input type="hidden" class="form-control" id="statusppn" name="" value="<?= $statusakun ?>">
                                <input type="hidden" class="form-control" id="KodeSetAkun" name="KodeSetAkun" value="<?= $KodeSetAkun ?>">
                                <input type="hidden" class="form-control" id="NoUrut" name="NoUrut">
                                <select class="form-control form-select" name="JenisJurnal" id="JenisJurnal" disabled required>
                                    <option value="" selected>Pilih Jenis Jurnal</option>
                                    <option value="Debet">Debet</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Kode Akun | Nama Akun</label>
                                <select class="form-control form-select select2" name="KodeAkun" id="KodeAkun" required>
                                    <option value="" selected>Pilih Akun</option>
                                    <?php if($dtakun){
                                        foreach ($dtakun as $key) {
                                            echo '<option value="'.$key['KodeAkun'].'">'.$key['KodeAkun'].' | '.$key['NamaAkun'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group" <?= $statusakun ?>>
                                <label for="StatusAkun">Status Akun</label>
                                <select class="form-control form-select select2" name="StatusAkun" id="StatusAkun">
                                    <option value="">Pilih Status</option>
                                    <option value="Pembelian/Penjualan">Pembelian/Penjualan</option>
                                    <option value="Retur">Retur</option>
                                    <option value="Diskon">Diskon</option>
                                    <option value="PPn">PPn</option>
                                    <option value="Kas">Kas</option>
                                    <option value="Hutang/Piutang">Hutang/Piutang</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="checkbox-theme-default custom-checkbox ">
                                    <input class="checkbox" type="checkbox" name="IsBank" id="IsBank">
                                    <label for="IsBank">
                                        <span class="checkbox-text">
                                            Is Bank
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