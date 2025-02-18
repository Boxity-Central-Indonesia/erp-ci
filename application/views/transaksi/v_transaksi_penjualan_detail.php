<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
    #ModalPenjualan { overflow-y:scroll !important; }
    #ModalMemo { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php if ($dtinduk['StatusProses'] == 'DONE') { ?>
                        <?php
                        if ($nominaltransaksi > 0) {
                            if ($nominaltransaksi > $totaljurnaldebet || $nominaltransaksi > $totaljurnalkredit) {
                        ?>
                        <div class="action-btn">
                            <a href="<?= base_url('transaksi/transaksi_kas/jurnalmanual/' . base64_encode('trans_jual') . '/' . base64_encode($idtransjurnal) . '/' . base64_encode($IDTransJual) . '/' . base64_encode('transaksi_penjualan/detail')) ?>" class="btn btn-info btn-sm btn-add" type="button">
                                <i class="las la-journal-whills"></i> Jurnalkan
                            </a>
                        </div>
                        <?php
                            }
                        }
                        ?>
                        <?php if ($check_retur == 0) { ?>
                        <div class="dropdown action-btn">
                            <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="las la-undo-alt"></i> Retur
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                <span class="dropdown-item">Pilih Jenis Retur</span>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item btn-retur" href="#" data-kode="<?= $IDTransJual ?>" data-kode2="KEMBALI BARANG" type="button">
                                    <i class="las la-toolbox"></i> Kembali Barang
                                </a>
                                <a class="dropdown-item btn-retur" href="#" data-kode="<?= $IDTransJual ?>" data-kode2="KEMBALI UANG" type="button">
                                    <i class="las la-dollar-sign"></i> Kembali Uang
                                </a>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="action-btn">
                            <a href="<?= base_url('transaksi/transaksi_penjualan/terimapembayaran/' . base64_encode($IDTransJual) . '/' . base64_encode($dtinduk['KodePerson'])) ?>" class="btn btn-primary btn-sm btn-add" type="button">
                                <i class="las la-arrows-alt"></i> Terima Pembayaran
                            </a>
                        </div>
                        <div class="action-btn">
                            <a href="#" class="btn btn-success btn-sm btn-add" type="button" id="btnmemo">
                                <i class="las la-search-dollar"></i> Memo Jurnal
                            </a>
                        </div>
                    <?php } ?>
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
                    <div class="dropdown action-btn">
                        <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="la la-download"></i> Cetak
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <span class="dropdown-item">Pilih Cetak</span>
                            <div class="dropdown-divider"></div>
                            <a target="_blank" href="<?= base_url('transaksi/quotation_invoice/cetakquotation/' . base64_encode($IDTransJual)) ?>" class="dropdown-item">
                                <i class="la la-file-pdf"></i> Quotation</a>
                            <a target="_blank" href="<?= base_url('transaksi/quotation_invoice/cetakproforma/' . base64_encode($IDTransJual)) ?>" class="dropdown-item">
                                <i class="la la-file-pdf"></i> Proforma Invoice</a>
                            <a target="_blank" href="<?= base_url('transaksi/quotation_invoice/cetakinvoice/' . base64_encode($IDTransJual)) ?>" class="dropdown-item">
                                <i class="la la-file-pdf"></i> Invoice</a>
                            <a target="_blank" href="<?= base_url('transaksi/transaksi_penjualan/cetakdetail/' . base64_encode($IDTransJual)) ?>" class="dropdown-item">
                                <i class="la la-download"></i> Cetak Penjualan</a>
                        </div>
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
                                    <td>Kode Transaksi </td>
                                    <td>: <?= $dtinduk['IDTransJual'] ?></td>
                                    <td>Kode SO </td>
                                    <td>: <?= isset($dtinduk['NoSlipOrder']) ? $dtinduk['IDTransJual'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Penjualan </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalPenjualan']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalPenjualan'])) ?></td>
                                    <td>Kode Customer </td>
                                    <td>: <?= $dtinduk['KodePerson'] ?></td>
                                </tr>
                                <tr>
                                    <td>Nama Gudang </td>
                                    <td>: <?= $dtinduk['NamaGudang'] ?></td>
                                    <td>Nama Customer </td>
                                    <td>: <?= $dtinduk['NamaPersonCP'] ?></td>
                                </tr>
                                <tr>
                                    <td>Created By </td>
                                    <td>: <?= ($dtinduk['SODibuatOleh'] != null) ? $dtinduk['SODibuatOleh'] : '-' ?></td>
                                    <td colspan="2"></td>
                                    <td hidden>No Referensi </td>
                                    <td hidden>: <?= $dtinduk['NoRef_Manual'] ?></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Detail</div>
                                    <?php
                                    if ($canAdd == 1) {
                                        if (!($dtinduk['TglSlipOrder'])) {
                                            if ($dtinduk['StatusProses'] != 'DONE' && $countpiutang == 0 && $exptahun == 0) {
                                    ?>
                                    <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add ml-auto">
                                        <i class="la la-plus"></i> Tambah Data
                                    </button>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <table id="table-jualdetail" class="table mb-0 table-borderless">
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
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kategori </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Qty </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Harga Satuan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Total </span></th>
                                        <?php
                                        if ($canEdit == 1 || $canDelete == 1) {
                                            if ($countpiutang == 0 && $exptahun == 0) {
                                                if (!($dtinduk['TglSlipOrder'])) {
                                        ?>
                                                <th style="display: table-cell; width:15%;">#</th>
                                        <?php
                                                }
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
                                        <td class="text-right">Jumlah</td>
                                        <td></td>
                                    </tr>
                                    <?php if ($dtinduk['StatusProses'] == 'DONE') { ?>
                                    <tr><td colspan="7"></td></tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-right">Diskon Bawah </td>
                                        <td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['DiskonBawah'], 2)) ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-right">PPN 11% </td>
                                        <td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['PPN'], 2)) ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-right">Total Tagihan </td>
                                        <td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['TotalTagihan'], 2)) ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-right">DP Dibayar </td>
                                        <td class="text-right"><?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['TotalTransaksi'], 2)) ?></td>
                                        <td></td>
                                    </tr>
                                    <?php } ?>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="row clearfix">
                        <?php if ($dtinduk['StatusProses'] == 'DONE') { ?>
                        <a href="<?=base_url('transaksi/trans_jual')?>" class="btn btn-sm btn-secondary">Kembali</a>
                            <?php if ($countpiutang == 0 && $exptahun == 0) { ?>
                            <button type="button" id="" class="btn btn-primary btn-sm simpanjual ml-auto">Edit</button>
                                <?php // if ($last_id == $IDTransJual) { ?>
                                &nbsp;<a href="#" type="button" id="" class="btn btn-sm btn-danger bataljual" data-kode="<?= $dtinduk['IDTransJual']?>">Batal</a>
                                <?php // } ?>
                            <?php } ?>
                        <?php } else { ?>
                        <a href="#" type="button" id="" class="btn btn-sm btn-secondary bataljual" data-kode="<?= $dtinduk['IDTransJual']?>">Batal</a>
                            <?php // if ($jml_item > 0 && $stok_kurang == 0) { ?>
                            <button type="button" id="" class="btn btn-primary btn-sm simpanjual ml-auto">Simpan</button>
                            <?php // } ?>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data Transaksi Penjualan Detail</h4>
            </div>
            <form action="<?= base_url('transaksi/transaksi_penjualan/simpandetail') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputFile">Barang</label>
                                <input type="hidden" class="form-control" id="IDTransJual" name="IDTransJual" value="<?= $IDTransJual ?>">
                                <input type="hidden" class="form-control" id="NoUrut" name="NoUrut">
                                <input type="hidden" class="form-control" id="TotalLama" name="TotalLama">
                                <input type="hidden" class="form-control" id="IDTransRetur" name="" value="<?= $dtinduk['IDTransRetur'] ?>">
                                <select class="form-control form-select select2" name="KodeBarang" id="KodeBarang" disabled required>
                                    <option value="" selected>Pilih Barang</option>
                                    <?php if($dtbarang){
                                        foreach ($dtbarang as $key) {
                                            echo '<option value="'.$key['KodeBarang'].'">'.$key['KodeManual'].' - '.$key['NamaBarang'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Barang</label>
                                <input type="text" class="form-control" id="JenisBarang" name="JenisBarang" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Kategori</label>
                                <input type="text" class="form-control" id="Kategory" name="Kategory" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Gudang | Stok (satuan)</label>
                                <input type="text" class="form-control" id="GS" name="" readonly>
                                <input type="hidden" class="form-control" id="SatuanBarang" name="SatuanBarang" readonly>
                                <input type="hidden" class="form-control" id="Spesifikasi" name="Spesifikasi" readonly>
                                <input type="hidden" class="form-control" id="Qty" name="Qty" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="SatuanPenjualan">Satuan Penjualan</label>
                                <select class="form-control" name="SatuanPenjualan" id="SatuanPenjualan" required>
                                    <option value="">Pilih Satuan</option>
                                    <option value="pcs">pcs</option>
                                    <option value="kilogram">kilogram</option>
                                    <option value="ecer">jual ecer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Quantity</label>
                                <input type="text" class="form-control" id="JmlStok" name="JmlStok" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Harga Barang</label>
                                <input type="text" class="form-control" id="Total" name="Total">
                            </div>
                            <div class="form-group">
                                <label for="Deskripsi">Keterangan</label>
                                <textarea class="form-control" name="Deskripsi" id="Deskripsi" rows="3"></textarea>
                            </div>
                            <div class="form-group" hidden>
                                <label for="exampleInputFile">Diskon</label><br>
                                <div class="icheck-success d-inline">
                                    <input type="radio" id="radioPrimary1" name="JenisDiskon" value="Nominal">
                                    <label for="radioPrimary1">
                                        Nominal
                                    </label>
                                </div>
                                &nbsp;&nbsp;&nbsp;
                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="radioPrimary2" name="JenisDiskon" value="Persen">
                                    <label for="radioPrimary2">
                                        Persen
                                    </label>
                                </div>
                                &nbsp;&nbsp;&nbsp;
                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="radioPrimary3" name="JenisDiskon" value="None">
                                    <label for="radioPrimary3">
                                        Tanpa Diskon
                                    </label>
                                </div>
                                <input type="text" class="form-control" id="Diskon" name="Diskon">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="simpan-detail" class="btn btn-primary waves-effect">Simpan</button>
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade ui-dialog" id="ModalPenjualan" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Transaksi</h4>
            </div>
            <form action="<?= base_url('transaksi/transaksi_penjualan/simpanpenjualan') ?>" method="post" id="form-simpan-penjualan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Penjualan</label>
                                <input type="datetime-local" class="form-control" id="TanggalPenjualan" name="TanggalPenjualan" required>
                                <input type="hidden" class="form-control" id="TglSlipOrder" name="TglSlipOrder">
                                <input type="hidden" class="form-control" id="NoRef_Manual" name="NoRef_Manual" readonly>
                                <input type="hidden" class="form-control" id="IDTransJual" name="IDTransJual" value="<?= $dtinduk['IDTransJual'] ?>">
                                <input type="hidden" class="form-control" id="NoTransKas" name="NoTransKas" value="<?= isset($dtinduk['NoTransKas']) ? $dtinduk['NoTransKas'] : '' ?>">
                                <input type="hidden" class="form-control" id="KodePerson" name="KodePerson" value="<?= $dtinduk['KodePerson'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Tanggal Jatuh Tempo</label>
                                <input type="datetime-local" class="form-control" id="TanggalJatuhTempo" name="TanggalJatuhTempo" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Gudang</label>
                                <input type="text" class="form-control" id="Gudang" name="" readonly value="<?= $dtinduk['KodeGudang'] . ' | ' . $dtinduk['NamaGudang'] ?>">
                                <input type="hidden" class="form-control" id="KodeGudang" name="KodeGudang" value="<?= $dtinduk['KodeGudang'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Total Tagihan Awal</label>
                                <input type="text" class="form-control" id="TotalTagihan" name="TotalTagihan" value="<?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($tagihanawal, 2)) ?>" readonly>
                                <input type="hidden" class="form-control" id="TanpaDiskon" name="TanpaDiskon" value="<?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($tanpadiskon, 2)) ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Diskon Bawah</label><br>
                                <div id="diskonbawahradio">
                                    <div class="icheck-success d-inline">
                                        <input type="radio" id="radioPrimary4" name="JenisDiskonBawah" value="Nominal" required>
                                        <label for="radioPrimary4">
                                            Nominal
                                        </label>
                                    </div>
                                    &nbsp;&nbsp;&nbsp;
                                    <div class="icheck-danger d-inline">
                                        <input type="radio" id="radioPrimary5" name="JenisDiskonBawah" value="Persen" required>
                                        <label for="radioPrimary5">
                                            Persen
                                        </label>
                                    </div>
                                    &nbsp;&nbsp;&nbsp;
                                    <div class="icheck-danger d-inline">
                                        <input type="radio" id="radioPrimary6" name="JenisDiskonBawah" value="None" required>
                                        <label for="radioPrimary6">
                                            Tanpa Diskon
                                        </label>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="DiskonBawah" name="DiskonBawah" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">PPN 11%</label><br>
                                <div id="ppnradio">
                                    <!-- <div class="icheck-success d-inline">
                                        <input type="radio" id="radioPrimary7" name="JenisPPN" value="Sebelum" required>
                                        <label for="radioPrimary7">
                                            Hitung PPN sebelum diskon
                                        </label>
                                    </div>
                                    &nbsp;&nbsp;&nbsp; -->
                                    <div class="icheck-danger d-inline">
                                        <input type="radio" id="radioPrimary8" name="JenisPPN" value="Sesudah" required>
                                        <label for="radioPrimary8">
                                            Hitung PPN setelah diskon
                                        </label>
                                    </div>
                                    &nbsp;&nbsp;&nbsp;
                                    <div class="icheck-danger d-inline">
                                        <input type="radio" id="radioPrimary9" name="JenisPPN" value="None" required>
                                        <label for="radioPrimary9">
                                            Tanpa PPN
                                        </label>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="NilaiPPN" name="NilaiPPN" readonly hidden>
                            </div>
                            <button type="button" id="hitung" class="btn btn-success waves-effect float-right" onclick="getHitung()">Hitung</button>
                            <button type="button" id="btnreset" class="btn btn-warning waves-effect float-right" onclick="getReset()">Reset</button>
                            <br>
                            <div class="form-group" id="t-akhir" style="display:none;">
                                <label for="exampleInputFile">Total Tagihan Akhir</label>
                                <input type="hidden" class="form-control" id="NilaiDiskon" name="NilaiDiskon" readonly>
                                <input type="hidden" class="form-control" id="NominalBelumPajak" name="NominalBelumPajak" readonly>
                                <input type="text" class="form-control" id="TagihanAkhir" name="TagihanAkhir" readonly>
                            </div>
                            <div class="form-group" id="dp" style="display:none;">
                                <label for="exampleInputFile">DP Dibayar</label>
                                <input type="text" class="form-control" id="TotalTransaksi" name="TotalTransaksi" readonly>
                            </div>
                            <div class="form-group" id="akunkas" style="display:none;">
                                <label for="exampleInputFile">Kode Akun Kas/Bank</label>
                                <select class="form-control form-select select2" name="KodeAkun" id="KodeAkun">
                                    <option value="" selected>Pilih Kode Akun</option>
                                    <?php if($dtakun){
                                        foreach ($dtakun as $key) {
                                            echo '<option value="'.$key['KodeAkun'].'">'.$key['KodeAkun'].' - '.$key['NamaAkun'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnsimpan" class="btn btn-primary waves-effect">Simpan</button>
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
                        <h4 class="title" id="defaultModalMemo">Purchase Invoice #<?= $IDTransJual ?></h4>
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
                    <span class="ml-auto" style="color: #272b4145; font-size: 16px;">Purchase Invoice #<?= $IDTransJual ?></span> -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" hidden id="btnsave" class="btn btn-primary waves-effect">Simpan</button>
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>