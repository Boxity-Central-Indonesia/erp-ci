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
                        $FiturID = 22; //FiturID di tabel serverfitur
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
                        <a target="_blank" href="<?= base_url('transaksi/pergerakan_stok/cetak/' . base64_encode($dtinduk['KodeBarang'])) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="btn-cetak">
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
                                    <td>Kode Barang </td>
                                    <td>: <?= $dtinduk['KodeBarang'] ?></td>
                                    <td>Jenis Barang </td>
                                    <td>: <?= $dtinduk['NamaJenisBarang'] ?></td>
                                </tr>
                                <tr>
                                    <td>Nama Barang </td>
                                    <td>: <?= $dtinduk['NamaBarang'] ?></td>
                                    <td>Satuan Barang </td>
                                    <td>: <?= $dtinduk['SatuanBarang'] ?></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12" hidden>
                                <div class="action-btn row clearfix">
                                    <div>Daftar Item Detail</div>
                                </div>
                            </div>
                            <table id="table-pergerakanstokdetail" class="table mb-0 table-borderless">
                                <thead>
                                    <tr>
                                        <td colspan="2">
					                        <div class="form-group">
					                            <label class="form-control-label">Gudang</label>
					                            <select class="form-control" id="combo-gudang">
					                                <option value="">Semua Gudang</option>
					                                <?php foreach ($dtgudang as $key) { ?>
					                                    <option value="<?= $key['KodeGudang'] ?>"><?= $key['NamaGudang'] ?></option>
					                                <?php } ?>
					                            </select>
					                        </div>
					                    </td>
                                        <td colspan="3" hidden>
                                            <div class="form-group">
                                                <label class="form-control-label">Pencarian Data</label>
                                                <input id="inp-search" placeholder="Pencarian Data" class="form-control" style="padding: 5px; box-sizing: border-box;" id="search" type="text">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Transaksi </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Tanggal </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Masuk </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Keluar </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Saldo </span></th>
                                        <th style="display: table-cell; width:5%;">#</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <a href="<?=base_url('transaksi/pergerakan_stok')?>" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
