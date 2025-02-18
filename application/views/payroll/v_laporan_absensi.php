<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php
                        $FiturID = 54; //FiturID di tabel serverfitur
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
                    <div class="dropdown action-btn">
                        <a target="_blank" href="<?= base_url('payroll/laporan_absensi/cetak/'.$bulan) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="btn-cetak">
                            <i class="la la-download"></i> Cetak
                        </a>

                        <!-- <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="la la-download"></i> Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <span class="dropdown-item">Export With</span>
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
                <div class="card-header color-dark fw-500">
                    Daftar <?= @$title ?>
                </div>
                <div class="card-body">

                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="table-responsive">
                            <div class="form-group col-lg-3">
                                <form  method="get" action="<?= base_url('payroll/laporan_absensi') ?>">
                                    <label class="form-control-label">Pencarian Bulan Tahun</label>
                                    <input id="inp-search" name="bulan" placeholder="Pencarian bulan tahun" class="form-control" style="padding: 5px; box-sizing: border-box;" type="month" autocomplete="off" value="<?= $bulan ?>">
                                </form>
                            </div>
                            <?php
                                $m = date('m', strtotime($bulan));
                                $y = date('Y', strtotime($bulan));
                                $totalhari = cal_days_in_month(CAL_GREGORIAN,$m,$y);
                                $tabel = '<table width="100%" class="table mb-0 table-borderless">';
                                    $tabel.='<thead><tr class="userDatatable-header">';
                                        $tabel.='<th style="display: table-cell; text-align:center;"><span class="userDatatable-title">NIP</span></th>';
                                        $tabel.='<th style="display: table-cell; text-align:center;"><span class="userDatatable-title">Nama</span></th>';
                                        for($i=0;$i<$totalhari; $i++){
                                            $tabel.='<th style="display: table-cell; width:3%; text-align:center;"><span class="userDatatable-title">'.($i+1).'</span></th>';
                                        }
                                    $tabel.='</tr></thead>';
                                    foreach ($model as $row) {
                                        $tabel.='<tbody style="font-size:14px;"><tr>';
                                            $tabel.='<td>'.$row['NIP'].'</td>';
                                            $tabel.='<td>'.$row['NamaPegawai'].'</td>';
                                            for($i=0;$i<$totalhari; $i++){
                                                if ($row['d'.($i+1)] == 'Hadir') {
                                                    $val = 'H';
                                                } elseif ($row['d'.($i+1)] == 'Izin') {
                                                    $val = 'I';
                                                } elseif ($row['d'.($i+1)] == 'Alpha') {
                                                    $val = 'A';
                                                } elseif ($row['d'.($i+1)] == 'Sakit') {
                                                    $val = 'S';
                                                } elseif ($row['d'.($i+1)] == 'Dinas Luar') {
                                                    $val = 'DL';
                                                } else {
                                                    $val = '<span style="color:red; font-weight:bold;">L</span>';
                                                }
                                                $tabel.='<td style="text-align:center;" width="10">'.$val.'</td>';
                                            }
                                        $tabel.='</tr></tbody>';
                                    }

                                $tabel.='</table>';

                                echo $tabel;
                            ?>
                            <br>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
