<style type="text/css"></style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
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
                                    <td>: <?= $dtinduk['IDTransBeli'] ?></td>
                                    <td>No Referensi </td>
                                    <td>: <?= $dtinduk['NoRef_Manual'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Pembelian </td>
                                    <td>: <?= shortdate_indo(date('Y-m-d', strtotime($dtinduk['TanggalPembelian']))) . ' ' . date('H:i', strtotime($dtinduk['TanggalPembelian'])) ?></td>
                                    <td>Supplier </td>
                                    <td>: <?= $dtinduk['KodePerson'] . ' | ' . $dtinduk['NamaPersonCP'] ?></td>
                                </tr>
                                <tr>
                                    <td>Total Tagihan </td>
                                    <td>: <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($dtinduk['TotalTagihan'], 2)) ?></td>
                                    <td>Total Dibayar </td>
                                    <td>: <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format($totalbayar, 2)) ?></td>
                                </tr>
                                <tr>
                                    <td>Sisa Tagihan </td>
                                    <td>: <?= str_replace(['.', ',', '+'], ['+', '.', ','], number_format(($dtinduk['TotalTagihan'] - $totalbayar), 2)) ?></td>
                                </tr>
                            </table>
                            <br>
                            <div class="col-sm-12">
                                <div class="action-btn row clearfix">
                                    <div>Daftar Pembayaran Hutang</div>
                                </div>
                            </div>
                            <table id="table-hutangdetail" class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="display: table-cell; width:4%"><span class="userDatatable-title">No</span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Kode Pembayaran </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Tanggal Pembayaran </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Jenis Bayar </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Dibayar Oleh </span></th>
                                        <th style="display: table-cell;"><span class="userDatatable-title">Nominal </span></th>
                                        <th style="display: table-cell; width:15%;">#</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;">
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="row clearfix">
                        <a href="<?=base_url('laporan/hutang')?>" class="btn btn-sm btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
