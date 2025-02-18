<?php
defined('BASEPATH') or exit('No direct script access allowed');

class aktivitas extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'mstaktivitas';
        checkAccess($this->session->userdata('fiturview')[45]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[45]);
        if (!$this->input->is_ajax_request()) {
            $data['dtjenis'] = $this->crud->get_rows([
                'select' => '*',
                'from' => 'mstjenisaktivitas',
                'where' => [['IsAktif' => 1]],
            ]);

            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'aktivitas';
            $data['title'] = 'Master Aktivitas';
            $data['view'] = 'master/v_aktivitas';
            $data['scripts'] = 'master/s_aktivitas';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstaktivitas a';

            $status   = $this->input->get('isaktif');
            $cari     = $this->input->get('cari');

            if ($cari != '') {
                $configData['filters'][] = " (a.KodeAktivitas LIKE '%$cari%' OR a.JenisAktivitas LIKE '%$cari%')";
            }
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'a.KodeAktivitas', 'a.BatasBawah', 'a.JmlDaun', 'a.BatasAtas', 'a.JenisAktivitas', 'a.Biaya', 'a.Satuan', 'a.KodeJenisAktivitas'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'a.KodeAktivitas';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'a.KodeAktivitas', 'a.BatasBawah', 'a.JmlDaun', 'a.BatasAtas', 'a.JenisAktivitas', 'a.Biaya', 'a.Satuan', 'a.KodeJenisAktivitas',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 45; //FiturID di tabel serverfitur
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
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeAktivitas'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeAktivitas'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpan()
    {
        $insertdata = $this->input->post();
        unset($insertdata['Biaya']);
        $insertdata['Biaya'] = str_replace(['.', ','], ['', '.'], $this->input->post('Biaya'));
        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('KodeAktivitas') != null && $this->input->post('KodeAktivitas') != '')) {
            $insertdata['KodeAktivitas'] = $this->crud->get_kode([
                'select' => 'RIGHT(KodeAktivitas, 7) AS KODE',
                'limit' => 1,
                'order_by' => 'KodeAktivitas DESC',
                'prefix' => 'AKT'
            ]);
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'mstaktivitas');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('KodeAktivitas') : $insertdata['KodeAktivitas'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Master Aktivitas',
                'Description' => $ket . ' data master aktivitas ' . $id
            ]);
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

    public function datajenis(){
        $KodeJenisAktivitas = $this->input->get('KodeJenisAktivitas');
        $data = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'mstjenisaktivitas',
            'where' => [['KodeJenisAktivitas' => $KodeJenisAktivitas]]
        ]);

        echo json_encode($data);
    }

    public function hapus()
    {
        $kode = $this->input->get('KodeAktivitas');
        $countProduksi = $this->crud->get_count(
            [
                'select' => '*',
                'from' => 'aktivitasproduksi',
                'where' => [['KodeAktivitas' => $kode]]
            ]
        );

        if ($countProduksi > 0) {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menghapus data karena Aktivitas digunakan di SPK."
            ]);
        } else {
            $res = $this->crud->delete(['KodeAktivitas' => $kode], 'mstaktivitas');

            if ($res) {
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => 'hapus',
                    'JenisTransaksi' => 'Master Aktivitas',
                    'Description' => 'hapus data master aktivitas ' . $kode
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

    public function jenis()
    {
        checkAccess($this->session->userdata('fiturview')[45]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'aktivitas';
            $data['title'] = 'Master Aktivitas';
            $data['view'] = 'master/v_aktivitas';
            $data['scripts'] = 'master/s_aktivitas';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstjenisaktivitas j';

            $status   = $this->input->get('isaktif');
            $cari     = $this->input->get('cari');

            if ($cari != '') {
                $configData['filters'][] = " (j.KodeJenisAktivitas LIKE '%$cari%' OR j.JenisAktivitas LIKE '%$cari%')";
            }
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.KodeJenisAktivitas', 'j.NoUrut', 'j.JenisAktivitas', 'j.IsAktif'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'j.KodeJenisAktivitas';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'j.KodeJenisAktivitas', 'j.NoUrut', 'j.JenisAktivitas', 'j.IsAktif',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 45; //FiturID di tabel serverfitur
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
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeJenisAktivitas'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeJenisAktivitas'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpanjenis()
    {
        $insertdata = $this->input->post();
        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('KodeJenisAktivitas') != null && $this->input->post('KodeJenisAktivitas') != '')) {
            $insertdata['KodeJenisAktivitas'] = $this->crud->get_kode([
                'select' => 'RIGHT(KodeJenisAktivitas, 7) AS KODE',
                'from' => 'mstjenisaktivitas',
                'limit' => 1,
                'order_by' => 'KodeJenisAktivitas DESC',
                'prefix' => 'KJA'
            ]);
            $insertdata['IsAktif'] = 1;
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'mstjenisaktivitas');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('KodeJenisAktivitas') : $insertdata['KodeJenisAktivitas'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Master Jenis Aktivitas',
                'Description' => $ket . ' data master jenis aktivitas ' . $id
            ]);
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

    public function hapusjenis()
    {
        $kode = $this->input->get('KodeJenisAktivitas');
        $countjenis = $this->crud->get_count(
            [
                'select' => '*',
                'from' => 'mstaktivitas',
                'where' => [['KodeJenisAktivitas' => $kode]]
            ]
        );

        if ($countjenis > 0) {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menghapus data karena Jenis Aktivitas digunakan di Master Aktivitas."
            ]);
        } else {
            $res = $this->crud->delete(['KodeJenisAktivitas' => $kode], 'mstjenisaktivitas');

            if ($res) {
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => 'hapus',
                    'JenisTransaksi' => 'Master Jenis Aktivitas',
                    'Description' => 'hapus data master jenis aktivitas ' . $kode
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
}
