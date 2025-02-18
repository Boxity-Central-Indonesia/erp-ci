<?php
defined('BASEPATH') or exit('No direct script access allowed');

class setting_akun extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'setakunjurnal s';
        checkAccess($this->session->userdata('fiturview')[58]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[58]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'settingakun';
            $data['title'] = 'Setting Akun Penjurnalan';
            $data['view'] = 'akuntansi/v_setting_akun';
            $data['scripts'] = 'akuntansi/s_setting_akun';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'setakunjurnal s';

            $configData['where'] = [['s.IsAktif' => 1]];

            $status   = $this->input->get('isaktif');
            $cari     = $this->input->get('cari');

            if ($cari != '') {
                $configData['filters'][] = " (s.NamaTransaksi LIKE '%$cari%' OR s.JenisTransaksi LIKE '%$cari%')";
            }
            if ($status != '') {
                $configData['filters'][] = " s.IsAktif = $status ";
            }

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                's.KodeSetAkun', 's.NoUrut', 's.NamaTransaksi', 's.JenisTransaksi', 's.IsAktif'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 's.NoUrut';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                's.KodeSetAkun', 's.NoUrut', 's.NamaTransaksi', 's.JenisTransaksi', 's.IsAktif',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 58; //FiturID di tabel serverfitur
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

            foreach ($records as $record) {
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['IsAktif'] = $temp['IsAktif'] == "1" ? 'Aktif' : 'NonAktif';
                if ($temp['NamaTransaksi'] == 'PO') {
                    $nama = "Cetak Pembelian (PO)";
                } elseif ($temp['NamaTransaksi'] == 'Pembelian') {
                    $nama = "Transaksi Pembelian";
                } elseif ($temp['NamaTransaksi'] == 'Hutang') {
                    $nama = "Transaksi Hutang";
                } elseif ($temp['NamaTransaksi'] == 'SO') {
                    $nama = "Transaksi Slip Order";
                } elseif ($temp['NamaTransaksi'] == 'Penjualan') {
                    $nama = "Transaksi Penjualan";
                } elseif ($temp['NamaTransaksi'] == 'Piutang') {
                    $nama = "Transaksi Terima Piutang";
                } elseif ($temp['NamaTransaksi'] == 'Produksi') {
                    $nama = "Proses Produksi";
                } elseif ($temp['NamaTransaksi'] == 'BahanPro') {
                    $nama = "Bahan Baku Produksi";
                } elseif ($temp['NamaTransaksi'] == 'AktivitasPro') {
                    $nama = "Biaya Aktivitas Produksi";
                } elseif ($temp['NamaTransaksi'] == 'Penggajian') {
                    $nama = "Penggajian";
                } elseif ($temp['NamaTransaksi'] == 'Penutup') {
                    $nama = "Jurnal Penutup";
                } else {
                    $nama = $temp['NamaTransaksi'];
                }
                $temp['NamaTransaksi'] = $nama;

                // if ($canEdit == 1 && $canDelete == 1) {
                //     $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('akuntansi/setting_akun/detail/' . base64_encode($temp['KodeSetAkun'])) . '" type="button" title="Detail Setting"><span class="fa fa-list" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodeSetAkun'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodeSetAkun'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodeSetAkun'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>') . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeSetAkun'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                // } elseif ($canEdit == 1 && $canDelete != 1) {
                //     $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('akuntansi/setting_akun/detail/' . base64_encode($temp['KodeSetAkun'])) . '" type="button" title="Detail Setting"><span class="fa fa-list" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodeSetAkun'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodeSetAkun'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodeSetAkun'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>');
                // } elseif ($canDelete == 1 && $canEdit != 1) {
                //     $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('akuntansi/setting_akun/detail/' . base64_encode($temp['KodeSetAkun'])) . '" type="button" title="Detail Setting"><span class="fa fa-list" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeSetAkun'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                // } else {
                //     $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('akuntansi/setting_akun/detail/' . base64_encode($temp['KodeSetAkun'])) . '" type="button" title="Detail Setting"><span class="fa fa-list" aria-hidden="true"></span></a>';
                // }
                $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('akuntansi/setting_akun/detail/' . base64_encode($temp['KodeSetAkun'])) . '" type="button" title="Detail Setting"><span class="fa fa-list" aria-hidden="true"></span></a>';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpan()
    {
        $insertdata = $this->input->post();
        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('KodeSetAkun') != null && $this->input->post('KodeSetAkun') != '')) {
            $insertdata['KodeSetAkun'] = $this->crud->get_kode([
                'select' => 'RIGHT(KodeSetAkun, 7) AS KODE',
                'limit' => 1,
                'order_by' => 'KodeSetAkun DESC',
                'prefix' => 'AKJ'
            ]);
            $insertdata['IsAktif'] = 1;
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'setakunjurnal');

        if ($res) {
            if ($isEdit) {
                $keterangan = 'update data setting akun penjurnalan ' . $insertdata['KodeSetAkun'];
                $aksi = 'edit';
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => $aksi,
                    'JenisTransaksi' => 'Setting Akun Penjurnalan',
                    'Description' => $keterangan,
                ]);
            } else {
                $keterangan = 'tambah data setting akun penjurnalan ' . $insertdata['KodeSetAkun'];
                $aksi = 'tambah';
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => $aksi,
                    'JenisTransaksi' => 'Setting Akun Penjurnalan',
                    'Description' => $keterangan,
                ]);
            }
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data"),
                'action' => ($isEdit ? "edit" : "tambah"),
                'id' => $insertdata['KodeSetAkun']
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ($isEdit ? "Gagal Edit Data" : "Gagal Menambah Data")
            ]);
        }
    }

    public function hapus()
    {
        $kode = $this->input->get('KodeSetAkun');
        $countdetail = $this->crud->get_count([
            'select' => 'KodeSetAkun',
            'from' => 'detailsetakun',
            'where' => [['KodeSetAkun' => $kode]],
        ]);

        if ($countdetail > 0) {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menghapus data, silahkan hapus item detail terlebih dahulu."
            ]);
        } else {
            $res = $this->crud->delete(['KodeSetAkun' => $kode], 'setakunjurnal');

            if ($res) {
                $keterangan = 'hapus data setting akun penjurnalan ' . $kode;
                $aksi = 'hapus';
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => $aksi,
                    'JenisTransaksi' => 'Setting Akun Penjurnalan',
                    'Description' => $keterangan,
                ]);
                echo json_encode([
                    'status' => true,
                    'msg'  => "Berhasil Menghapus Data"
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'msg'  => "Gagal Menghapus Data"
                ]);
            }
        }
    }

    public function aktif()
    {
        $kode = $this->input->get('KodeSetAkun');
        $value = (int) $this->input->get('IsAktif');

        $data = ['IsAktif' => $value];
        $result = $this->crud->update($data, ['KodeSetAkun' => $kode], "setakunjurnal");

        if ($result) {
            $keterangan = 'update data setting akun penjurnalan ' . $kode;
            $aksi = 'edit';
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Setting Akun Penjurnalan',
                'Description' => $keterangan,
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Mengubah Data"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Mengubah Data"
            ]);
        }
    }

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[58]);
        $kodesetakun   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA AKUN PENJURNALAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'settingakun';
            $data['title'] = 'Detail Setting Akun Penjurnalan';
            $data['view'] = 'akuntansi/v_setting_akun_detail';
            $data['scripts'] = 'akuntansi/s_setting_akun_detail';

            $dtakun = [
                'select' => '*',
                'from' => 'mstakun',
                'where' => [[
                    'KodeAkun !=' => '.01',
                    'IsParent' => 0,
                    'IsAktif' => 1,
                ]]
            ];
            $data['dtakun'] = $this->crud->get_rows($dtakun);

            $dtinduk = [
                'select' => '*',
                'from' => 'setakunjurnal s',
                'where' => [['s.KodeSetAkun' => $kodesetakun]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['KodeSetAkun'] = $kodesetakun;
            $data['statusakun'] = ($data['dtinduk']['NamaTransaksi'] == 'Transaksi Pembelian PPN' || $data['dtinduk']['NamaTransaksi'] == 'Transaksi Penjualan PPN' || $data['dtinduk']['NamaTransaksi'] == 'Retur Penjualan PPN' || $data['dtinduk']['NamaTransaksi'] == 'Pembelian' || $data['dtinduk']['NamaTransaksi'] == 'Penjualan' || $data['dtinduk']['NamaTransaksi'] == 'Retur Pembelian PPN') ? "" : "hidden";

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $kodesetakun   = $this->input->get('kodesetakun');
            $configData['table'] = 'detailsetakun d';
            $configData['where'] = [['d.KodeSetAkun'  => $kodesetakun]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (d.JenisJurnal LIKE '%$cari%' OR a.KodeAkun LIKE '%$cari%' OR a.NamaAkun LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' setakunjurnal s',
                    'on' => "s.KodeSetAkun = d.KodeSetAkun",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstakun a',
                    'on' => "a.KodeAkun = d.KodeAkun",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'd.NoUrut', 'd.JenisJurnal', 'd.KodeSetAkun', 'd.StatusAkun', 's.NamaTransaksi', 's.JenisTransaksi', 'd.KodeAkun', 'a.NamaAkun', 'd.IsBank'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'd.JenisJurnal, d.NoUrut';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'd.NoUrut', 'd.JenisJurnal', 'd.KodeSetAkun', 'd.StatusAkun', 's.NamaTransaksi', 's.JenisTransaksi', 'd.KodeAkun', 'a.NamaAkun', 'd.IsBank',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 58; //FiturID di tabel serverfitur
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

            foreach ($records as $record) {
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeSetAkun'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodeSetAkun'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cekJenisJurnal()
    {
        $KodeSetAkun = $this->input->get('KodeSetAkun');
        $JenisJurnal = $this->input->get('JenisJurnal');

        $countjenisdetail = $this->crud->get_count([
            'select' => 'NoUrut, KodeSetAkun',
            'from' => 'detailsetakun',
            'where' => [[
                'KodeSetAkun' => $KodeSetAkun,
                'JenisJurnal' => $JenisJurnal,
            ]],
        ]);

        if ($countjenisdetail > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Anda telah menambahkan jenis jurnal ' . $JenisJurnal . ' pada transaksi ini.']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Jenis jurnal tersedia']);
        }
    }

    public function simpandetail()
    {
        $insertdata = $this->input->post();
        $isEdit = true;

        $isbank = $this->input->post('IsBank');
        $insertdata['IsBank'] = $isbank != 'on'  ? (int) 0 : (int) 1;

        ## POST DATA
        if (!($this->input->post('NoUrut') != null && $this->input->post('NoUrut') != '')) {
            $isEdit = false;
            $getNoUrut = $this->db->from('detailsetakun')
            ->where('KodeSetAkun', $this->input->post('KodeSetAkun'))
            ->select('NoUrut')
            ->order_by('NoUrut', 'desc')
            ->get()->row();
            if ($getNoUrut) {
                $NoUrut = (int)$getNoUrut->NoUrut;
            } else {
                $NoUrut = 0;
            }
            $insertdata['NoUrut'] = $NoUrut + 1;
            $isEdit = false;

            $res = $this->crud->insert($insertdata, 'detailsetakun');
        } else {
            $isEdit = true;
            unset($insertdata['JenisJurnal']);

            $res = $this->crud->update($insertdata, ['KodeSetAkun' => $this->input->post('KodeSetAkun'), 'NoUrut' => $this->input->post('NoUrut')], 'detailsetakun');
        }

        if ($res) {
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data")
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ($isEdit ? "Gagal Edit Data" : "Gagal Menambah Data")
            ]);
        }
    }

    public function hapusdetail()
    {
        $kode  = $this->input->get('KodeSetAkun');
        $kode2 = $this->input->get('NoUrut');

        $res = $this->crud->delete(['KodeSetAkun' => $kode, 'NoUrut' => $kode2], 'detailsetakun');
        if ($res) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Menghapus Data"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Menghapus Data"
            ]);
        }
    }
}
