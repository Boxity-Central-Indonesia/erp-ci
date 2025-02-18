<style type="text/css">
    #ModalTambah { overflow-y:scroll !important; }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <div class="action-btn">
                        <form method="get" action="<?= base_url('transaksi/proses_produksi/list_produksi') ?>" id="myForm">
                        <div class="form-group mb-0">
                            <div class="input-container icon-left position-relative">
                                <span class="input-icon icon-left">
                                    <span data-feather="calendar"></span>
                                </span>
                                <input type="text" class="form-control" id="tgl-transaksi" name="tgl">
                                <span class="input-icon icon-right">
                                    <span data-feather="chevron-down"></span>
                                </span>
                            </div>
                        </div>
                        </form>
                    </div>
                    <?php
                        $FiturID = 29; //FiturID di tabel serverfitur
                        $canPrint = 0;
                        $print = [];
                        foreach ($this->session->userdata('fiturprint') as $key => $value) {
                            $print[$key] = $value;
                            if ($key == $FiturID && $value == 1) {
                                $canPrint = 1;
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
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <div>Daftar Item Produksi</div>
                            <table id="table-listproduksi" class="table mb-0 table-striped">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%" class="text-center"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Kode Barang </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Nama Barang </span></th>
                                        <?php
                                        if ($jenisaktivitas) {
                                            foreach ($jenisaktivitas as $col) {
                                        ?>
                                            <th style="display: table-cell;" class="text-center"><span class="userDatatable-title"><?= $col['JenisAktivitas'] ?> </span></th>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Berat Kotor(kg) </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Berat Bersih(kg) </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Status </span></th>
                                        <th style="display: table-cell;" class="text-center"><span class="userDatatable-title">Created By </span></th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                    <?php if ($model) {
                                        foreach ($model as $no => $row) { ?>
                                        <tr>
                                            <td><?= $no+1; ?></td>
                                            <td><?= $row['KodeManual'] ?></td>
                                            <td><?= $row['NamaBarang'] ?></td>
                                            <?php foreach ($jenisaktivitas as $col) {
                                                $namapeg = $row[$col['JenisAktivitas']];
                                                $namapegawai = ($namapeg != null) ? implode(', ', $namapeg) : '-';
                                                $class = ($namapeg != null) ? 'class="text-left"' : 'class="text-center"';
                                                echo '<td ' . $class . '>' . $namapegawai . '</td>';
                                            } ?>
                                            <td class="text-center"><?= ($row['BeratKotor'] != null) ? str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['BeratKotor'], 2)) : 0 ?></td>
                                            <td class="text-center"><?= ($row['Qty'] != null) ? str_replace(['.', ',', '+'], ['+', '.', ','], number_format($row['Qty'], 2)) : 0 ?></td>
                                            <td class="text-center"><?= ($row['ProdTglSelesai'] != null) ? 'SELESAI' : 'WIP' ?></td>
                                            <td><?= $row['SPKDibuatOleh'] ?></td>
                                        </tr>
                                        <?php } 
                                    } else {
                                        $totalja = count($jenisaktivitas);
                                        $clspn = 7 + $totalja;
                                        echo '<tr><td class="text-center" style="font-weight:bold;" colspan="'.$clspn.'">Data tidak ditemukan.</td></tr>';
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
