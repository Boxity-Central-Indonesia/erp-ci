<?php

$FiturID = 57; //FiturID di tabel serverfitur
$addData = 0;
foreach ($this->session->userdata('fituradd') as $key => $value) {
    if ($key == $FiturID && $value == 1) {
        $addData = 1;
    }
}
$editData = 0;
foreach ($this->session->userdata('fituredit') as $key => $value) {
    if ($key == $FiturID && $value == 1) {
        $editData = 1;
    }
}
$deleteData = 0;
foreach ($this->session->userdata('fiturdelete') as $key => $value) {
    if ($key == $FiturID && $value == 1) {
        $deleteData = 1;
    }
}

function parent($judul, $anak, $isparent = false, $ishasInduk = false, $edit, $delete)
{
    $data = '<tr aria-expanded="false"><td><div class="row">';
    if ($isparent) {
        $data .= '<a href=".myCollapse' . str_replace('.', '', $judul['KodeAkun']) . '" onclick="clps(' . str_replace('.', '', $judul['KodeAkun']) . ')" data-toggle="collapse" type="button" class="btn btn-sm btn-primary p-0"><i id="no' . str_replace('.', '', $judul['KodeAkun']) . '" class="fa fa-caret-right fa-fw"></i></a>&ensp;&emsp;&nbsp;';
    }
    $data .= '(' . $judul['KodeAkun'] . ')&nbsp;&nbsp;' . $judul['NamaAkun'];
    if ($ishasInduk) {
        if ($edit == '1' && $delete == '0') {
            $data .= "&ensp;&emsp;<a class='btn-edit' data-obj='" . json_encode($judul) . "'href='#'><span><i class='fa fa-edit ml-4'></i></span></a>
            &nbsp;";            
        } elseif ($edit == '0' && $delete == '1') {
            $data .= "&ensp;&emsp;<a class='btn-hapus' data-kode='" . $judul['KodeAkun'] . "'href='#'><span><i class='fa fa-trash ml-4'></i></span></a>
            &nbsp;";
        } elseif ($edit == '1' && $delete == '1') {
            $data .= "&ensp;&emsp;<a class='btn-edit' data-obj='" . json_encode($judul) . "'href='#'><span><i class='fa fa-edit ml-4'></i></span></a>
            &nbsp;<a class='btn-hapus' data-kode='" . $judul['KodeAkun'] . "'href='#'><span><i class='fa fa-trash ml-4'></i></span></a>";    
        } else {
            $data .= "";
        }
        
    } else {
        $data .= '</div></td></tr>';
    }
    $data .= anak($anak, $edit, $delete, str_replace('.', '', $judul['KodeAkun']));
    return $data;
}

function anak($anak, $editData, $deleteData, $judul)
{
    $data = '';
    $data .= '<tr class="collapse myCollapse' . $judul . '">
    <td>
        <div class="p-0">
            <table class="table table-hover">
                <tbody>';
    foreach ($anak as $key => $value) {
        $data .= parent($value, $value['anak'], $value['IsParent'], true, $editData, $deleteData);
    }
    $data .= ' </tbody>
            </table>
        </div>
    </td>
 </tr>';
    return $data;
}

function draw_table($judul, $space = '&nbsp;')
{
    $data = '<tr>';
    $data .= '<td>' . $judul['KodeAkun'] . '</td>';
    $data .= '<td>' . ($space != '' ? $space . '- '   : $space . '* ') . $judul['NamaAkun'] . '</td>';
    $data .= '</tr>';
    foreach ($judul['anak'] as $key => $value) {
        $data .= draw_table($value, ($space . '&ensp;&emsp;'));
    }
    return $data;
}
?>
<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <?php if ($addData == 1) { ?>
                    <div class="action-btn">
                        <button type="button" id="btntambah" class="btn btn-primary btn-sm btn-add">
                            <i class="la la-plus"></i> Tambah Data
                        </button>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                        <div class="tab-wrapper">
                            <?php if ($this->session->flashdata('berhasil')) { ?>
                                <div class=" alert alert-success alert-dismissible fade show " role="alert">
                                    <div class="alert-content">
                                        <p><?= $this->session->flashdata('berhasil') ?></p>
                                        <button type="button" class="close text-capitalize" data-dismiss="alert" aria-label="Close">
                                            <span data-feather="x" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($this->session->flashdata('gagal')) { ?>
                                <div class=" alert alert-danger alert-dismissible fade show " role="alert">
                                    <div class="alert-content">
                                        <p><?= $this->session->flashdata('gagal') ?></p>
                                        <button type="button" class="close text-capitalize" data-dismiss="alert" aria-label="Close">
                                            <span data-feather="x" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="atbd-tab tab-horizontal">
                                <ul class="nav nav-tabs vertical-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-tree-tab" data-toggle="tab" href="#custom-tabs-for-tree" role="tab" aria-controls="custom-tabs-for-tree" aria-selected="true">Tree View</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-tbl-tab" data-toggle="tab" href="#custom-tabs-for-tbl" role="tab" aria-controls="custom-tabs-for-tbl" aria-selected="false">Table View</a>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="custom-tabs-for-tree" role="tabpanel" aria-labelledby="custom-tabs-tree-tab">
                                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                                        <div class="table-responsive">
                                            <table id="table-tree" class="table">
                                                <tbody>
                                                    <?php foreach ($data as $key => $row) : ?>
                                                        <?= parent($row, $row['anak'], true, false, $editData, $deleteData) ?>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-for-tbl" role="tabpanel" aria-labelledby="custom-tabs-tbl-tab">
                                    <div  class="userDatatable global-shadow border-0 bg-white w-100">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Kode Akun</th>
                                                        <th>Nama Akun</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($data as $key => $row) : ?>
                                                        <?= draw_table($row, '') ?>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-default" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modaltitle"> Tambah Data</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="myform" action="<?= base_url('akuntansi/daftar_akun/simpan') ?>" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kelompok Akun</label>
                                <select id="cbkelompok" name="KelompokAkun" class="form-control" required>
                                    <option value="">Pilih Kelompok</option>
                                    <?php foreach ($kelompok as $key) { ?>
                                        <option data-kode="<?= @$key['KodeAkun'] ?>" <?= (@$data['KelompokAkun'] == $key['NamaAkun'] ? 'selected' : '') ?> value="<?= $key['NamaAkun'] ?>"><?= $key['NamaAkun'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Akun Induk</label>
                                <select id='indukakun' name="AkunInduk" class="form-control select2" required>
                                    <option value="" selected>Pilih Akun Induk</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="NamaAkun">Nama Akun</label>
                                <input type="hidden" name="KodeAkun">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span id="txt-kode-akun-add" class="input-group-text">-</span>
                                    </div>
                                    <input required name="NamaAKun" class="form-control" id="namaakun" placeholder="Masukkan Nama Akun">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Keterangan">Keterangan</label>
                                <input name="Keterangan" class="form-control" id="Keterangan" placeholder="Masukkan Keterangan">
                            </div>
                            <div class="form-group">
                                <label>Kategori Arus Kas</label>
                                <select id="KategoriArusKas" required name="KategoriArusKas" class="form-control">
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($kat as $key) { ?>
                                        <option <?= (@$data['KategoriArusKas'] == $key ? 'selected' : '') ?> value="<?= $key ?>"><?= $key ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="JenisAkun">Jenis Akun</label>
                                <select name="JenisAkun" id="JenisAkun" class="form-control" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="Debit">Debit</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="checkbox-theme-default custom-checkbox ">
                                    <input class="checkbox" type="checkbox" name="IsParent" id="cbparent" checked>
                                    <label for="cbparent">
                                        <span class="checkbox-text">
                                            Akun Induk
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <div class="checkbox-theme-default custom-checkbox ">
                                    <input class="checkbox" type="checkbox" name="IsPersediaan" id="persediaan">
                                    <label for="persediaan">
                                        <span class="checkbox-text">
                                            Akun Persediaan
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox-theme-default custom-checkbox ">
                            <input class="checkbox" type="checkbox" name="IsAktif" id="cbaktif" checked>
                            <label for="cbaktif">
                                <span class="checkbox-text">
                                    Aktif
                                </span>
                            </label>
                        </div>
                        <!-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" name="IsParent" type="checkbox" id="cbparent" checked>
                            <label for="cbparent" class="custom-control-label"><span class="checkbox-text">Akun Induk</span></label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" name="IsAktif" type="checkbox" id="cbaktif" checked>
                            <label for="cbaktif" class="custom-control-label"><span class="checkbox-text">Aktif</span></label>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="modal-default2" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modaltitle2"> Tambah Data</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="myform2" action="<?= base_url('akuntansi/daftar_akun/simpan') ?>" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kelompok Akun</label>
                                <input name="KelompokAkun" class="form-control" id="KelompokAkun2" placeholder="Masukkan Kelompok Akun">
                            </div>
                            <div class="form-group">
                                <label>Akun Induk</label>
                                <input required name="AkunInduk" class="form-control" id="AkunInduk2" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="NamaAkun">Nama Akun</label>
                                <input id="KodeAkun2" type="hidden" name="KodeAkun">
                                <input required name="NamaAKun" class="form-control" id="NamaAkun2" placeholder="Masukkan Nama Akun">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Keterangan">Keterangan</label>
                                <input name="Keterangan" class="form-control" id="Keterangan2" placeholder="Masukkan Keterangan">
                            </div>
                            <div class="form-group">
                                <label>Kategori Arus Kas</label>
                                <select id="KatArusKas" required name="KategoriArusKas" class="form-control">
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($kat as $key) { ?>
                                        <option <?= (@$data['KategoriArusKas'] == $key ? 'selected' : '') ?> value="<?= $key ?>"><?= $key ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="JenisAkun2">Jenis Akun</label>
                                <select name="JenisAkun" id="JenisAkun2" class="form-control" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="Debit">Debit</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="checkbox-theme-default custom-checkbox ">
                                    <input class="checkbox" type="checkbox" name="IsParent" id="cbparent2" checked>
                                    <label for="cbparent2">
                                        <span class="checkbox-text">
                                            Akun Induk
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <div class="checkbox-theme-default custom-checkbox ">
                                    <input class="checkbox" type="checkbox" name="IsPersediaan" id="persediaan2">
                                    <label for="persediaan2">
                                        <span class="checkbox-text">
                                            Akun Persediaan
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox-theme-default custom-checkbox ">
                            <input class="checkbox" type="checkbox" name="IsAktif" id="cbaktif2" checked>
                            <label for="cbaktif2">
                                <span class="checkbox-text">
                                    Aktif
                                </span>
                            </label>
                        </div>
                        <!-- <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" name="IsParent" type="checkbox" id="cbparent2">
                            <label for="cbparent2" class="custom-control-label">Akun Induk</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" name="IsAktif" type="checkbox" id="cbaktif2">
                            <label for="cbaktif2" class="custom-control-label">Aktif</label>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>