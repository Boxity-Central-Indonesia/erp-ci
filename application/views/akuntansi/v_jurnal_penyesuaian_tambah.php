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
                        $FiturID = 59; //FiturID di tabel serverfitur
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
                        <!-- <div class="action-btn">
                            <a hidden target="_blank" href="<?= base_url('akuntansi/jurnal_penyesuaian/cetakjurnal/' . base64_encode($IDTransJurnal)) ?>" class="btn btn-sm btn-default btn-white dropdown-toggle" type="button">
                                <i class="la la-download"></i> Cetak
                            </a>
                        </div> -->
                    <?php } ?>
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <!-- <div class="card-header color-dark fw-500">
                </div> -->
                <form action="<?= base_url('akuntansi/jurnal_penyesuaian/simpanlangsung') ?>" method="post" id="form-simpan">
                    <div class="card-body">
                        <div  class="userDatatable global-shadow border-0 bg-white w-100">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="exampleInputFile">No Referensi</label>
                                        <input type="text" class="form-control" id="NoRefTrans" name="NoRefTrans" value="<?= @$data['NoRefTrans'] ?>" required>
                                        <input type="hidden" class="form-control" id="IDTransJurnal" name="IDTransJurnal" value="<?= @$data['IDTransJurnal'] ?>" readonly>
                                        <input type="hidden" class="form-control" name="KodeTahun" id="KodeTahun" value="<?= $tahunaktif ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputFile">Uraian</label>
                                        <textarea class="form-control" rows="3" id="NarasiJurnal" name="NarasiJurnal" required><?= @$data['NarasiJurnal'] ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="exampleInputFile">Tanggal</label>
                                        <input type="datetime-local" class="form-control" id="TglTransJurnal" name="TglTransJurnal" value="<?= @$data['TglTransJurnal'] ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputFile">Nominal</label>
                                        <input type="text" class="form-control" name="NominalTransaksi" id="NominalTransaksi" value="<?= str_replace(',', '.', number_format(@$data['NominalTransaksi'])) ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="card-body">
                                    <button type="button" style="float:right;" id="" onclick="tambahAkun()" class="btn btn-primary btn-sm btn-add">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button><br><br>
                                    <table width="100%" class="table" id="tbl-container">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th style="width: 50%;">Data Akun</th>
                                                <th style="width: 20%;">Debit</th>
                                                <th style="width: 20%;">Kredit</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($item)) { foreach ($item as $key => $value) { ?>
                                                <tr>
                                                    <td style="width: 5%;"><?= $key+1 ?></td>
                                                    <td style="width: 50%;">
                                                        <select class="form-control form-select select2" name="KodeAkun[]" id="KodeAkun<?= $key+1 ?>">
                                                            <option value=""> - Pilih Akun - </option>
                                                            <?php
                                                                foreach ($dtakun as $akun) :
                                                                    $selected =  $akun['KodeAkun'] == $value['KodeAkun'] ? 'selected' : '';
                                                                    echo '<option value="' . $akun['KodeAkun'] . '" ' . $selected . '>' . $akun['KodeAkun'] . ' - ' . $akun['NamaAkun'] . '</option>';
                                                                endforeach;
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td style="width: 20%;">
                                                        <input type="text" class="form-control text-right input-debet" onkeyup="rpdb(<?= $key+1 ?>)" name="Debet[]" id="Debet<?= $key+1 ?>" value="<?= str_replace(',', '.', number_format($value['Debet'])) ?>">
                                                    </td>
                                                    <td style="width: 20%;">
                                                        <input type="text" class="form-control text-right input-kredit" onkeyup="rpkr(<?= $key+1 ?>)" name="Kredit[]" id="Kredit<?= $key+1 ?>" value="<?= str_replace(',', '.', number_format($value['Kredit'])) ?>">
                                                    </td>
                                                    <td style="width: 5%;">
                                                        <button type="button" title="Hapus data" class="btn btn-sm btn-danger hapusakun" id="btn-<?= $key+1 ?>">x</button>
                                                    </td>
                                                </tr>
                                            <?php } } else { ?>
                                                <tr id="first_row">
                                                    <td style="width: 5%;">1</td>
                                                    <td style="width: 50%;">
                                                        <select class="form-control form-select select2" name="KodeAkun[]" id="KodeAkun1">
                                                            <option value=""> - Pilih Akun - </option>
                                                            <?php
                                                                foreach ($dtakun as $akun) :
                                                                    echo '<option value="' . $akun['KodeAkun'] . '">' . $akun['KodeAkun'] . ' - ' . $akun['NamaAkun'] . '</option>';
                                                                endforeach;
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td style="width: 20%;">
                                                        <input type="text" class="form-control text-right input-debet" onkeyup="rpdb(1)" name="Debet[]" id="Debet1" value="0">
                                                    </td>
                                                    <td style="width: 20%;">
                                                        <input type="text" class="form-control text-right input-kredit" onkeyup="rpkr(1)" name="Kredit[]" id="Kredit1" value="0">
                                                    </td>
                                                    <td style="width: 5%;">
                                                        <button type="button" title="Hapus data" class="btn btn-sm btn-danger hapusakun" id="btn-1">x</button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="text-right"><strong>Total</strong></td>
                                                <td><input type="text" class="form-control text-right" name="" id="TotalDebet" readonly></td>
                                                <td><input type="text" class="form-control text-right" name="" id="TotalKredit" readonly></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row clearfix">
                            <a href="<?=base_url('akuntansi/jurnal_penyesuaian')?>" class="btn btn-sm btn-secondary">Kembali</a>
                            <button type="submit" id="simpanjurnal" class="btn btn-primary btn-sm ml-auto">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>