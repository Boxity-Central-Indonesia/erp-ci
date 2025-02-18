<style type="text/css">
    #ModalTambah {
        overflow-y: scroll !important;
    }

    #ModalRetur {
        overflow-y: scroll !important;
    }

    #ModalMemo {
        overflow-y: scroll !important;
    }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php if ($memojurnal) { ?>
                        <div class="action-btn">
                            <a href="#" class="btn btn-success btn-sm btn-add" type="button" id="btnmemo">
                                <i class="las la-search-dollar"></i> Memo Jurnal
                            </a>
                        </div>
                    <?php } ?>
                    <?php
                    $FiturID = 68; //FiturID di tabel serverfitur
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
                            <a target="_blank" href="<?= base_url('transaksi/retur_pembelian/cetakdetail/' . base64_encode($IDTransRetur)) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
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
                                    <td>Kode Transaksi </td>
                                    <td>: <?= $dtinduk['IDTransRetur'] ?></td>
                                    <td>Kode Ref Pembelian </td>
                                    <td>: <?= $dtinduk['IDTransBeli'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Transaksi </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalTransaksi'])) ?></td>
                                    <td>Tanggal Pembelian </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalPembelian']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalPembelian'])) ?></td>
                                </tr>
                                <tr>
                                    <td>Nama Customer </td>
                                    <td>: <?= $dtinduk['NamaUsaha'] ?></td>
                                    <td>Jenis Realisasi </td>
                                    <td>: <?= $dtinduk['JenisRealisasi'] ?></td>
                                </tr>
                                <tr>
                                    <td>Nama Gudang </td>
                                    <td>: <?= $dtinduk['NamaGudang'] ?></td>
                                    <td>Keterangan </td>
                                    <td>: <?= $dtinduk['Keterangan'] ?></td>
                                </tr>
                                <tr>
                                    <td>Created By </td>
                                    <td>: <?= ($dtinduk['ActualName'] != null) ? $dtinduk['ActualName'] : '-' ?></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Detail</div>
                                    <?php
                                    if ($canAdd == 1) {
                                        if ($dtinduk['IsRealisasi'] != 1) {
                                    ?>
                                            <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add ml-auto">
                                                <i class="la la-plus"></i> Tambah Data
                                            </button>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <table id="table-returdetail" class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Qty Jual </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Qty Datang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Qty Retur </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Harga Satuan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Total Retur </span></th>
                                        <th style="display: table-cell;" class="text-left"><span class="userDatatable-title">Alasan Retur </span></th>
                                        <?php
                                        if ($canEdit == 1 || $canDelete == 1) {
                                            if ($dtinduk['IsRealisasi'] != 1) {
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
                                <tfoot style="font-size:14px;">
                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-center">Jumlah</td>
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
                        <?php if ($dtinduk['IsRealisasi'] == 1) { ?>
                            <a href="<?= base_url('transaksi/trans_beli') ?>" class="btn btn-sm btn-secondary">Kembali</a>
                        <?php } else { ?>
                            <a href="#" type="button" id="" class="btn btn-sm btn-secondary batalretur" data-kode="<?= $dtinduk['IDTransRetur'] ?> ">Batal</a>
                            <button type="button" id="" class="btn btn-primary btn-sm btnsimpanrt ml-auto">Simpan</button>
                        <?php } ?>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data Retur Pembelian Detail</h4>
            </div>
            <form action="<?= base_url('transaksi/retur_pembelian/simpandetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Barang</label>
                                <input type="hidden" class="form-control" id="IDTransRetur" name="IDTransRetur" value="<?= $IDTransRetur ?>">
                                <input type="hidden" class="form-control" id="JenisRetur" name="JenisRetur" value="<?= $dtinduk['JenisRetur'] ?>">
                                <input type="hidden" class="form-control" id="NoUrut" name="NoUrut">
                                <select class="form-control form-select select2" name="KodeBarang" id="KodeBarang" disabled required>
                                    <option value="" selected>Pilih Barang</option>
                                    <?php if ($itembeli) {
                                        foreach ($itembeli as $key) {
                                            echo '<option value="' . $key['KodeBarang'] . '">' . $key['NamaBarang'] . '</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Satuan Barang</label>
                                <input type="text" class="form-control" id="SatuanBarang" name="SatuanBarang" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Qty Jual</label>
                                <input type="number" class="form-control" id="JmlJual" name="JmlJual" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Qty Retur</label>
                                <input type="number" class="form-control" id="JmlRetur" name="JmlRetur" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Additional Name</label>
                                <input type="text" class="form-control" id="AdditionalName" name="AdditionalName">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Harga Jual</label>
                                <input type="text" class="form-control" id="HargaJual" name="" readonly>
                                <input type="hidden" class="form-control" id="HargaJualReal" name="HargaJual" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Qty Datang</label>
                                <input type="number" class="form-control" id="JmlDatang" name="JmlDatang" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Alasan Retur</label>
                                <textarea class="form-control" rows="3" id="AlasanRetur" name="AlasanRetur" required></textarea>
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

<div class="modal fade ui-dialog" id="ModalRetur" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Simpan Retur</h4>
            </div>
            <form action="<?= base_url('transaksi/retur_pembelian/simpanretur') ?>" method="post" id="form-simpan-retur">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Kode Referensi Pembelian</label>
                                <input type="hidden" class="form-control" id="IDTransRetur" name="IDTransRetur" value="<?= $IDTransRetur ?>">
                                <input type="text" class="form-control" id="IDTransBeli" name="IDTrans" value="<?= $dtinduk['IDTransBeli'] ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Customer</label>
                                <input type="hidden" class="form-control" id="KodePerson" name="KodePerson" value="<?= $dtinduk['KodePerson'] ?>" readonly>
                                <input type="text" class="form-control" id="KodePersonView" name="" value="<?= $dtinduk['KodePerson'] . ' | ' . $dtinduk['NamaUsaha'] ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Pembelian</label>
                                <input type="datetime-local" class="form-control" id="TglPembelian" name="" value="<?= $dtinduk['TanggalPembelian'] ?>" readonly>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Total Retur</label>
                                <input type="text" class="form-control" id="TotalReturs" name="TotalRetur" value="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Gudang</label>
                                <select class="form-control form-select select2" name="KodeGudang" id="KodeGudang" required>
                                    <option value="" selected>Pilih Gudang</option>
                                    <?php if ($gudang) {
                                        foreach ($gudang as $key) {
                                            if (isset($dtinduk['KodeGudang']) && $key['KodeGudang'] == $dtinduk['KodeGudang']) {
                                                echo '<option value="' . $key['KodeGudang'] . '" selected>' . $key['NamaGudang'] . '</option>';
                                            } else {
                                                echo '<option value="' . $key['KodeGudang'] . '">' . $key['NamaGudang'] . '</option>';
                                            }
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Realisasi</label>
                                <select class="form-control form-select" id="JenisRealisasi" name="JenisRealisasi" required>
                                    <option value="">Pilih Jenis Realisasi</option>
                                    <option value="KEMBALI BARANG">Kembali Barang</option>
                                    <option value="KEMBALI UANG">Kembali Uang</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Keterangan Realisasi</label>
                                <textarea class="form-control" rows="3" id="KetRealisasi" name="KetRealisasi"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Kode Akun Kas/Bank</label>
                                <select class="form-control form-select select2" name="KodeAkun" id="KodeAkun">
                                    <option value="" selected>Pilih Kode Akun</option>
                                    <?php if ($dtakun) {
                                        foreach ($dtakun as $key) {
                                            echo '<option value="' . $key['KodeAkun'] . '">' . $key['KodeAkun'] . ' - ' . $key['NamaAkun'] . '</option>';
                                        }
                                    } ?>
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

<div class="modal fade ui-dialog" id="ModalMemo" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <ul>
                    <li>
                        <h6 class="title" style="color: #272b4187;">Laporan Jurnal</h6>
                    </li>
                    <li>
                        <!-- <h4 class="title" id="defaultModalMemo">Purchase Invoice #<?= $IDTransBeli ?></h4> -->
                    </li>
                </ul>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <table class="table mb-0" border-collapse="collapse">
                        <thead>
                            <tr class="userDatatable-header">
                                <th><span class="userDatatable-title">Kode Akun</span></th>
                                <th><span class="userDatatable-title">Nama Akun</span></th>
                                <th><span class="userDatatable-title">Debet</span></th>
                                <th><span class="userDatatable-title">Kredit</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totaldebet = 0;
                            $totalkredit = 0;
                            if ($memojurnal) {
                                foreach ($memojurnal as $row) {
                                    $totaldebet += $row['Debet'];
                                    $totalkredit += $row['Kredit'];
                            ?>
                                    <tr>
                                        <td><?= $row['KodeAkun'] ?></td>
                                        <td><?= $row['NamaAkun'] ?></td>
                                        <td class="text-right">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Debet'], 2)) ?></td>
                                        <td class="text-right">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Kredit'], 2)) ?></td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                            <tr style="font-weight: bold;">
                                <td colspan="2">Total</td>
                                <td class="text-right">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totaldebet, 2)) ?></td>
                                <td class="text-right">Rp. <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalkredit, 2)) ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- <br>
                    <span class="ml-auto" style="color: #272b4145; font-size: 16px;">Purchase Invoice #<?= $IDTransBeli ?></span> -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" hidden id="btnsave" class="btn btn-primary waves-effect">Simpan</button>
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>