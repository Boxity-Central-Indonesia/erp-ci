<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
    #ModalBahan { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php
                        $FiturID = 30; //FiturID di tabel serverfitur
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
                    <?php if ($canPrint == 1 && $dtinduk['ProdTglSelesai'] != null) { ?>
                    <div class="dropdown action-btn">
                        <a target="_blank" href="<?= base_url('transaksi/penyesuaian_produksi/cetakdetail/' . base64_encode($dtinduk['NoTrans'])) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
                            <i class="la la-download"></i> Cetak
                        </a>
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
                            <table class="table mb-0 table-borderless" style="font-size:14px;" width="100%">
                                <tr>
                                    <td style="width:15%;">Kode Penyesuaian Produksi</td>
                                    <td style="width:35%;">: <?= $dtinduk['NoTrans'] ?></td>
                                    <td style="width:15%;">Created By </td>
                                    <td style="width:35%;">: <?= $dtinduk['ActualName'] ?></td>
                                </tr>
                                <tr>
                                    <td>Gudang Asal</td>
                                    <td>: <?= $dtinduk['NamaGudangAsal'] ?></td>
                                    <td>Gudang Tujuan</td>
                                    <td>: <?= $dtinduk['NamaGudangTujuan'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Mulai </td>
                                    <td>: <?= $TanggalTransaksi ?></td>
                                    <td>Tanggal Selesai </td>
                                    <td>: <?= $ProdTglSelesai ?></td>
                                </tr>
                                <tr>
                                    <td>Keterangan </td>
                                    <td>: <?= $dtinduk['Deskripsi'] ?></td>
                                    <td colspan="2"></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Bahan Baku</div>
                                    <?php
                                    if ($canAdd == 1 && $dtinduk['ProdTglSelesai'] == null) {
                                    ?>
                                    <button type="button" id="btntambah2" class="btn btn-primary btn-sm btn-add ml-auto">
                                        <i class="la la-plus"></i> Tambah Data
                                    </button>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <table id="table-bahanbaku" class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Bahan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Bahan </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jumlah Berat </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Harga </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Total </span></th>
                                        <?php if ($canEdit == 1 && $dtinduk['ProdTglSelesai'] == null) { ?>
                                            <th style="display: table-cell; width:5%;">#</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                                <tfoot style="font-size:14px;">
                                    <tr>
                                        <td colspan="2"></td>
                                        <td class="text-right">Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <br><br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Produksi</div>
                                    <?php
                                    if ($canAdd == 1 && $dtinduk['ProdTglSelesai'] == null) { ?>
                                        <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add ml-auto">
                                            <i class="la la-plus"></i> Tambah Data
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>
                            <table id="table-spktambah" class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nama Barang </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Ukuran </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jumlah Daun </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Berat Kotor </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Berat Bersih </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Pemakaian Bahan Masak </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">HPP </span></th>
                                        <?php if ($canEdit == 1 && $dtinduk['ProdTglSelesai'] == null) { ?>
                                            <th style="display: table-cell; width:5%;">#</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                                <tfoot style="font-size:14px;">
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="text-right">Total</td>
                                        <td></td>
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
                    <div class="col-sm-12">
                        <div class="row clearfix">
                            <a href="<?= base_url('transaksi/penyesuaian_produksi') ?>" type="button" class="btn btn-sm btn-secondary">Kembali</a>
                            <?php
                            // if ($itemProd > 0 && $itemBahan > 0) {
                                if ($dtinduk['ProdTglSelesai'] == null) {
                            ?>
                                <a href="javascript:(0)" type="button" id="btn-verifikasi" class="btn btn-sm btn-primary ml-auto" data-kode="<?= $dtinduk['NoTrans'] ?>">Simpan</a>
                            <?php
                                } 
                            // }
                            ?>
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
                <h4 class="title" id="defaultModalLabel">Tambah Data Produksi</h4>
            </div>
            <form action="<?= base_url('transaksi/spk/simpantambah') ?>" method="post" id="form-simpan">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Barang</label>
                                <select class="form-control form-select" name="KodeBarang" id="KodeBarang" disabled required>
                                    <option></option>
                                </select>
                                <input type="text" class="form-control" name="" id="NamaBarang" hidden readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Ukuran</label>
                                <input type="text" class="form-control" id="ProdUkuran" name="ProdUkuran" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jumlah Daun</label>
                                <input type="text" class="form-control" id="ProdJmlDaun" name="ProdJmlDaun" readonly>
                                <input type="hidden" class="form-control" id="NoTrans" name="NoTrans" placeholder="notrans">
                                <input type="hidden" class="form-control" id="NoUrut" name="NoUrut" placeholder="nourut">
                                <input type="hidden" class="form-control" id="NoRefProduksi" name="NoRefProduksi" value="<?= $dtinduk['NoTrans'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="Qty">Berat Kotor</label>
                                <input type="text" class="form-control" name="Qty" id="Qty" required>
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

<div class="modal fade ui-dialog" id="ModalBahan" role="dialog">
    <div class="modal-dialog modal-lg ui-dialog-content modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Tambah Data Produksi</h4>
            </div>
            <form action="<?= base_url('transaksi/spk/simpantambah') ?>" method="post" id="form-simpan2">
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputFile">Nama Bahan</label>
                                <select class="form-control form-select select2" name="KodeBarang" id="KodeBarang2" disabled required>
                                    <option value="" selected>Pilih Bahan Produksi</option>
                                    <?php if($bahanbaku){
                                        foreach ($bahanbaku as $key) {
                                            // echo '<option value="'.$key['KodeBarang'].'">'.$key['KodeManual'].' - '.$key['NamaBarang'].'</option>';
                                            echo '<option value="'.$key['KodeBarang'].'">'.$key['NamaBarang'].'</option>';
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jenis Bahan</label>
                                <input type="text" class="form-control" id="JenisBarang2" name="JenisBarang" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Harga Satuan</label>
                                <input type="text" class="form-control" id="HargaSatuan" name="HargaSatuan" readonly>
                            </div>
                            <div class="form-group">
                                <label for="Stok">Stok</label>
                                <input type="text" class="form-control" name="" id="Stok" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Jumlah Berat</label>
                                <input type="text" class="form-control" id="Qty2" name="Qty" required>
                                <input type="hidden" class="form-control" id="NoTrans2" name="NoTrans" value="<?= $dtinduk['NoTrans'] ?>">
                                <input type="hidden" class="form-control" id="NoUrut2" name="NoUrut">
                                <input type="hidden" class="form-control" id="NoRefProduksi2" name="NoRefProduksi" value="<?= $dtinduk['NoTrans'] ?>">
                                <input type="hidden" class="form-control" id="Gudang2" name="Gudang" value="<?= @$dtinduk['GudangAsal'] ?>">
                                <input type="hidden" class="form-control" id="SatuanBarang2" name="SatuanBarang">
                                <input type="hidden" class="form-control" id="Kategory2" name="Kategory">
                            </div>
                            <div class="form-group">
                                <div class="checkbox-theme-default custom-checkbox ">
                                    <input type="hidden" class="form-control" name="IsBahanBaku" id="" value="off">
                                    <input class="checkbox" type="checkbox" name="IsBahanBaku" id="IsBahanBaku2" checked>
                                    <label for="IsBahanBaku2">
                                        <span class="checkbox-text">
                                            Bahan Baku
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
