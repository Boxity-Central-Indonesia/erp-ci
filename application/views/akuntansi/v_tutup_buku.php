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
                        $FiturID = 60; //FiturID di tabel serverfitur
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
                    <?php if ($canAdd == 1) { ?>
                    <div class="action-btn" hidden>
                        <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add">
                            <i class="la la-plus"></i> Tambah Data
                        </button>
                    </div>
                    <?php } ?>
                    <h6>Tahun Buku : <?= $tahunaktif ?></h6>
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-header color-dark fw-500" hidden>
                    Daftar <?= @$title ?>
                </div>
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <table id="table-tutupbuku" class="table table-striped" style="font-size:12px;">
                                <thead>
                                    <tr>
                                        <form  method="get" action="<?= base_url('akuntansi/tutup_buku') ?>" id="myform">
                                        <td>
                                            <div class="form-group">
                                                <label class="form-control-label">Tahun Anggaran</label><br>
                                                <select class="form-control form-select select2" name="kodetahun" id="kode-tahun" onchange="this.form.submit()">
                                                    <?php if($tahunanggaran) {
                                                        foreach ($tahunanggaran as $key) {
                                                            if(isset($kodetahun) && $key['KodeTahun'] == $kodetahun) {
                                                                echo '<option value="'.$key['KodeTahun'].'" selected>'.$key['KodeTahun'].'</option>';
                                                            } else {
                                                                echo '<option value="'.$key['KodeTahun'].'">'.$key['KodeTahun'].'</option>';
                                                            }
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td colspan="4">
                                            <div class="form-group mb-0">
                                                <label class="form-control-label">Per Tanggal</label>
                                                <div class="input-container icon-left position-relative">
                                                    <span class="input-icon icon-left">
                                                        <span data-feather="calendar"></span>
                                                    </span>
                                                    <input type="text" class="form-control" id="tgl-transaksi" name="tgl" value="<?= date('d-m-Y', strtotime($tgl)) ?>" onchange="this.form.submit()">
                                                    <span class="input-icon icon-right">
                                                        <span data-feather="chevron-down"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        </form>
                                    </tr>
                                    <tr class="userDatatable-header">
                                        <th></th>
                                        <th style="display: table-cell;" class="text-center" colspan="2">Neraca Saldo </th>
                                        <th style="display: table-cell;" class="text-center" colspan="2">Jurnal Penyesuaian </th>
                                        <th style="display: table-cell;" class="text-center" colspan="2">Neraca Saldo Setelah Disesuaikan </th>
                                        <th style="display: table-cell;" class="text-center" colspan="2">Laba Rugi </th>
                                        <th style="display: table-cell;" class="text-center" colspan="2">Neraca </th>
                                    </tr>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell;" class="text-center">Daftar Akun </th>
                                        <th style="display: table-cell;">Debet </th>
                                        <th style="display: table-cell;">Kredit </th>
                                        <th style="display: table-cell;">Debet </th>
                                        <th style="display: table-cell;">Kredit </th>
                                        <th style="display: table-cell;">Debet </th>
                                        <th style="display: table-cell;">Kredit </th>
                                        <th style="display: table-cell;">Debet </th>
                                        <th style="display: table-cell;">Kredit </th>
                                        <th style="display: table-cell;">Debet </th>
                                        <th style="display: table-cell;">Kredit </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        function format_number_minus($value)
                                        {
                                            $result = '';
                                            if ((int) $value < 0) {
                                                $result = '(' . number_format(-1 * $value, 2) . ')';
                                            } else {
                                                $result = number_format($value, 2);
                                            }
                                            return str_replace(['.', ',', '+'], ['+', '.', ','], $result);
                                        }
                                    ?>
                                    <tr>
                                        <td style="font-weight: bold;">AKTIVA</td>
                                        <td colspan="10"></td>
                                    </tr>
                                    <?php
                                        $nu1 = 0;
                                        $np1 = 0;
                                        $ns1 = 0;
                                        foreach ($model as $row) {
                                    ?>
                                        <?php if (substr($row['KodeAkun'], 0, 1) == 1){
                                            $neracasesuai = $row['NominalUmum'] + $row['NominalPenyesuaian'];
                                        ?>
                                        <tr>
                                            <td><?= $row['KodeAkun'] ?> - <?= $row['NamaAkun'] ?></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalUmum']) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalPenyesuaian']) ?></td>
                                            <td class="text-right"><?= format_number_minus(0) ?></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <?php
                                            $nu1 += $row['NominalUmum'];
                                            $np1 += $row['NominalPenyesuaian'];
                                            $ns1 = $nu1 + $np1;
                                            }
                                        ?>
                                    <?php } ?>
                                    <tr>
                                        <td style="font-weight: bold;">KEWAJIBAN</td>
                                        <td colspan="10"></td>
                                    </tr>
                                    <?php
                                        $nu2 = 0;
                                        $np2 = 0;
                                        $ns2 = 0;
                                        foreach ($model as $row) {
                                    ?>
                                        <?php if (substr($row['KodeAkun'], 0, 1) == 2){
                                            $neracasesuai = $row['NominalUmum'] + $row['NominalPenyesuaian'];
                                        ?>
                                        <tr>
                                            <td><?= $row['KodeAkun'] ?> - <?= $row['NamaAkun'] ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalUmum']) ?></td>
                                            <td class="text-right"><?= format_number_minus(0) ?></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalPenyesuaian']) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                        </tr>
                                        <?php
                                            $nu2 += $row['NominalUmum'];
                                            $np2 += $row['NominalPenyesuaian'];
                                            $ns2 = $nu2 + $np2;
                                            }
                                        ?>
                                    <?php } ?>
                                    <tr>
                                        <td style="font-weight: bold;">EKUITAS</td>
                                        <td colspan="10"></td>
                                    </tr>
                                    <?php
                                        $nu3 = 0;
                                        $np3 = 0;
                                        $ns3 = 0;
                                        foreach ($model as $row) {
                                    ?>
                                        <?php if (substr($row['KodeAkun'], 0, 1) == 3){
                                            $neracasesuai = $row['NominalUmum'] + $row['NominalPenyesuaian'];
                                        ?>
                                        <tr>
                                            <td><?= $row['KodeAkun'] ?> - <?= $row['NamaAkun'] ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalUmum']) ?></td>
                                            <td class="text-right"><?= format_number_minus(0) ?></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalPenyesuaian']) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                        </tr>
                                        <?php
                                            $nu3 += $row['NominalUmum'];
                                            $np3 += $row['NominalPenyesuaian'];
                                            $ns3 = $nu3 + $np3;
                                            }
                                        ?>
                                    <?php } ?>
                                    <tr>
                                        <td style="font-weight: bold;">PENDAPATAN</td>
                                        <td colspan="10"></td>
                                    </tr>
                                    <?php
                                        $nu4 = 0;
                                        $np4 = 0;
                                        $ns4 = 0;
                                        foreach ($model as $row) {
                                    ?>
                                        <?php if (substr($row['KodeAkun'], 0, 1) == 4){
                                            $neracasesuai = $row['NominalUmum'] + $row['NominalPenyesuaian'];
                                        ?>
                                        <tr>
                                            <td><?= $row['KodeAkun'] ?> - <?= $row['NamaAkun'] ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalUmum']) ?></td>
                                            <td class="text-right"><?= format_number_minus(0) ?></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalPenyesuaian']) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <?php
                                            $nu4 += $row['NominalUmum'];
                                            $np4 += $row['NominalPenyesuaian'];
                                            $ns4 = $nu4 + $np4;
                                            }
                                        ?>
                                    <?php } ?>
                                    <tr>
                                        <td style="font-weight: bold;">BEBAN PRODUKSI</td>
                                        <td colspan="10"></td>
                                    </tr>
                                    <?php
                                        $nu5 = 0;
                                        $np5 = 0;
                                        $ns5 = 0;
                                        foreach ($model as $row) {
                                    ?>
                                        <?php if (substr($row['KodeAkun'], 0, 1) == 5){
                                            $neracasesuai = $row['NominalUmum'] + $row['NominalPenyesuaian'];
                                        ?>
                                        <tr>
                                            <td><?= $row['KodeAkun'] ?> - <?= $row['NamaAkun'] ?></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalUmum']) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalPenyesuaian']) ?></td>
                                            <td class="text-right"><?= format_number_minus(0) ?></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <?php
                                            $nu5 += $row['NominalUmum'];
                                            $np5 += $row['NominalPenyesuaian'];
                                            $ns5 = $nu5 + $np5;
                                            }
                                        ?>
                                    <?php } ?>
                                    <tr>
                                        <td style="font-weight: bold;">BEBAN DILUAR PRODUKSI</td>
                                        <td colspan="10"></td>
                                    </tr>
                                    <?php
                                        $nu6 = 0;
                                        $np6 = 0;
                                        $ns6 = 0;
                                        foreach ($model as $row) {
                                    ?>
                                        <?php if (substr($row['KodeAkun'], 0, 1) == 6){
                                            $neracasesuai = $row['NominalUmum'] + $row['NominalPenyesuaian'];
                                        ?>
                                        <tr>
                                            <td><?= $row['KodeAkun'] ?> - <?= $row['NamaAkun'] ?></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalUmum']) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalPenyesuaian']) ?></td>
                                            <td class="text-right"><?= format_number_minus(0) ?></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <?php
                                            $nu6 += $row['NominalUmum'];
                                            $np6 += $row['NominalPenyesuaian'];
                                            $ns6 = $nu6 + $np6;
                                            }
                                        ?>
                                    <?php } ?>
                                    <tr>
                                        <td style="font-weight: bold;">PEMBELIAN</td>
                                        <td colspan="10"></td>
                                    </tr>
                                    <?php
                                        $nu7 = 0;
                                        $np7 = 0;
                                        $ns7 = 0;
                                        foreach ($model as $row) {
                                    ?>
                                        <?php if (substr($row['KodeAkun'], 0, 1) == 7){
                                            $neracasesuai = $row['NominalUmum'] + $row['NominalPenyesuaian'];
                                        ?>
                                        <tr>
                                            <td><?= $row['KodeAkun'] ?> - <?= $row['NamaAkun'] ?></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalUmum']) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($row['NominalPenyesuaian']) ?></td>
                                            <td class="text-right"><?= format_number_minus(0) ?></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><?= format_number_minus($neracasesuai) ?></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <?php
                                            $nu7 += $row['NominalUmum'];
                                            $np7 += $row['NominalPenyesuaian'];
                                            $ns7 = $nu7 + $np7;
                                            }
                                        ?>
                                    <?php } ?>
                                    <?php
                                        $totalnominalumumdebet  = $nu1 + $nu5 + $nu6 + $nu7;
                                        $totalnominalumumkredit = $nu2 + $nu3 + $nu4;
                                        $totalnominalpsydebet   = $np1 + $np5 + $np6 + $np7;
                                        $totalnominalpsykredit  = $np2 + $np3 + $np4;
                                        $totalneracasesuaidebet = $ns1 + $ns5 + $ns6 + $ns7;
                                        $totalneracasesuaikredit= $ns2 + $ns3 + $ns4;
                                        $totallabarugidebet     = $ns5 + $ns6;
                                        $totallabarugikredit    = $ns4;
                                        $totalneracadebet       = $ns1 + $ns7;
                                        $totalneracakredit      = $ns2 + $ns3;
                                    ?>
                                    <tr style="font-weight: bold;">
                                        <td>TOTAL</td>
                                        <td class="text-right"><?= format_number_minus($totalnominalumumdebet) ?></td>
                                        <td class="text-right"><?= format_number_minus($totalnominalumumkredit) ?></td>
                                        <td class="text-right"><?= format_number_minus($totalnominalpsydebet) ?></td>
                                        <td class="text-right"><?= format_number_minus($totalnominalpsykredit) ?></td>
                                        <td class="text-right"><?= format_number_minus($totalneracasesuaidebet) ?></td>
                                        <td class="text-right"><?= format_number_minus($totalneracasesuaikredit) ?></td>
                                        <td class="text-right"><?= format_number_minus($totallabarugidebet) ?></td>
                                        <td class="text-right"><?= format_number_minus($totallabarugikredit) ?></td>
                                        <td class="text-right"><?= format_number_minus($totalneracadebet) ?></td>
                                        <td class="text-right"><?= format_number_minus($totalneracakredit) ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">Net Profit Loss</td>
                                        <td class="text-right" colspan="7"></td>
                                        <td class="text-right" style="font-size:14px;"><?= format_number_minus($totallabarugikredit - $totallabarugidebet) ?></td>
                                        <td class="text-center" colspan="2">
                                            <a href="#" type="button" id="btn-tutupbuku" <?= ($kodetahun == $tahunaktif ? '' : 'hidden') ?> class="btn btn-sm btn-primary" data-kode="<?= $tahunaktif ?>" data-kode2="<?= date('Y-m-d') ?>">Tutup Buku</a>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

