<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-header color-dark fw-500">
                    <div class="userDatatable global-shadow border-0 bg-white w-100 text-center">
                        <?= (strtolower($data['Status']) == 'pending' || strtolower($data['Status']) == 'processed') ? 'Bayar sebelum: <br> <span id="bataswaktu" style="color:red; font-size:32px; font-weight:bold;"></span> <br>' : '' ?>
                        Batas waktu pembayaran: <?= $tanggal_kadaluarsa ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="text-center" style="font-weight:bold;">Transfer ke rekening:</div>
                        <br>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <?php $url_logo = isset($dtbank['logo']) ? base_url($dtbank['logo']) : null ?>
                                <img id="image-bank" class="img-thumbnail" alt="no image" style="width:60%; height:auto;" src="<?= $url_logo ?>"/>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <?= ($data['SenderBankType'] == 'virtual_account') ? 'Bank ' : '' ?> <?= $data['BankName'] ?> <br>
                                <?= $namaperusahaan ?>
                            </div>
                        </div>
                        <br><br>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <?php if (strtolower($data['Status']) == 'pending' || strtolower($data['Status']) == 'processed') { ?>
                                    <span>No. Virtual Account</span><br>
                                    <input type="hidden" class="form-control" id="norek" value="<?= $data['NoRekening'] ?>">
                                    <span style="font-size:26px; font-weight:bold;"><?= $data['NoRekening'] ?></span>&nbsp;
                                    <a class="" href="#" type="button" title="Salin" onclick="copyFunction()"><i class="fa fa-copy" style="font-size:20px;"></i></a>
                                <?php } elseif (strtolower($data['Status']) == 'done' || strtolower($data['Status']) == 'successful') { ?>
                                    <div class="userDatatable global-shadow border-0 bg- w-100">
                                        Pembayaran berhasil dilakukan.
                                    </div>
                                <?php } elseif (strtolower($data['Status']) == 'cancelled' || strtolower($data['Status']) == 'failed') { ?>
                                    <div class="userDatatable global-shadow border-0 bg-white w-100">
                                        Pembayaran gagal.
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <span>Total Nominal Transfer:</span><br>
                                <span style="font-size:26px; font-weight:bold;">Rp. <?= str_replace(',', '.', number_format($data['Amount'])) ?></span>
                            </div>
                        </div>
                        <br><br>
                        <div class="row" <?= (strtolower($data['Status']) == 'pending' || strtolower($data['Status']) == 'processed') ? '' : 'hidden' ?>>
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <div class="atbd-collapse">
                                    <?php if (isset($dtbank['transfer_step']['internet_banking'])) { ?>
                                        <div class="atbd-collapse-item">
                                            <div class="atbd-collapse-item__header active">
                                                <a href="#" class="item-link" data-toggle="collapse" data-target="#internetbanking" aria-expanded="true" aria-controls="internetbanking">

                                                    <i class="la la-angle-right"></i>

                                                    <h6>Petunjuk Transfer Internet Banking</h6>
                                                </a>
                                            </div>
                                            <div id="internetbanking" class="collapse atbd-collapse-item__body show">
                                                <div class="collapse-body-text">
                                                    <?php if (isset($dtbank['transfer_step']['internet_banking'])) {
                                                        foreach ($dtbank['transfer_step']['internet_banking'] as $key => $value) { ?>
                                                            <?= $key . '. ' . str_replace('#va', $data['NoRekening'], $value) . '<br>' ?>
                                                    <?php }
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if (isset($dtbank['transfer_step']['mobile_banking'])) { ?>
                                        <div class="atbd-collapse-item">
                                            <div class="atbd-collapse-item__header active">
                                                <a href="#" class="item-link" data-toggle="collapse" data-target="#mobilebanking" aria-expanded="true" aria-controls="mobilebanking">
    
                                                    <i class="la la-angle-right"></i>
    
                                                    <h6>Petunjuk Transfer Mobile Banking</h6>
                                                </a>
                                            </div>
                                            <div id="mobilebanking" class="collapse atbd-collapse-item__body show">
                                                <div class="collapse-body-text">
                                                    <?php if (isset($dtbank['transfer_step']['mobile_banking'])) {
                                                        foreach ($dtbank['transfer_step']['mobile_banking'] as $key => $value) { ?>
                                                            <?= $key . '. ' . str_replace('#va', $data['NoRekening'], $value) . '<br>' ?>
                                                    <?php }
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if (isset($dtbank['transfer_step']['atm'])) { ?>
                                        <div class="atbd-collapse-item">
                                            <div class="atbd-collapse-item__header active">
                                                <a href="#" class="item-link" data-toggle="collapse" data-target="#atm" aria-expanded="true" aria-controls="atm">
    
                                                    <i class="la la-angle-right"></i>
    
                                                    <h6>Petunjuk Transfer ATM <?= $data['BankName'] ?></h6>
                                                </a>
                                            </div>
                                            <div id="atm" class="collapse atbd-collapse-item__body show">
                                                <div class="collapse-body-text">
                                                    <?php if (isset($dtbank['transfer_step']['atm'])) {
                                                        foreach ($dtbank['transfer_step']['atm'] as $key => $value) { ?>
                                                            <?= $key . '. ' . str_replace('#va', $data['NoRekening'], $value) . '<br>' ?>
                                                    <?php }
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?=base_url('user/flip')?>" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
