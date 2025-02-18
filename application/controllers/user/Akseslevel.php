<?php
defined('BASEPATH') or exit('No direct script access allowed');

class akseslevel extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'accesslevel';
        checkAccess($this->session->userdata('fiturview')[1]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[1]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'akseslevel';
            $data['title'] = 'Akses Level';
            $data['view'] = 'user/v_akseslevel';
            $data['scripts'] = 'user/s_akseslevel';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'accesslevel';
            $configData['where'] = [
                ['IsAktif !=' => null]
            ];
            $cari     = $this->input->get('cari');
            $status   = $this->input->get('isaktif');
            if ($cari != '') {
                $configData['filters'][] = " (LevelName LIKE '%$cari%' OR DivisiName LIKE '%$cari%')";
            }

            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }
            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'LevelID', 'LevelName', 'IsAktif', 'DivisiName'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'LevelID';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'LevelID', 'LevelName', 'IsAktif', 'DivisiName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 1; //FiturID di tabel serverfitur
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
                $status    = $record->IsAktif > 0 ? '<span class="text-success">Aktif</span>' : '<span class="text-danger">Tidak Aktif</span>';
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['status'] = $status;
                $temp['IsAktif'] = ((int)$temp['IsAktif'] == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>');
                if ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('user/akseslevel/fitur/' . base64_encode($temp['LevelID'])) . '" type="button" title="Fitur Level"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><span class="fa fa-edit" aria-hidden="true"></span></a>' .
                    ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;&nbsp;<a data-kode=' . $temp['LevelID'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['LevelID'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['LevelID'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>');
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['LevelID'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('user/akseslevel/fitur/' . base64_encode($temp['LevelID'])) . '" type="button" title="Fitur Level"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><span class="fa fa-edit" aria-hidden="true"></span></a>' .
                    ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;&nbsp;<a data-kode=' . $temp['LevelID'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['LevelID'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['LevelID'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>') .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['LevelID'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
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

        ## POST DATA
        if (!($this->input->post('LevelID') != null && $this->input->post('LevelID') != '')) {
            $getlevelid = $this->db->from('accesslevel')
            ->select('LevelID')
            ->order_by('LevelID', 'desc')
            ->get()->row();
            if ($getlevelid) {
                $idlevel = (int)$getlevelid->LevelID;
            } else {
                $idlevel = 0;
            }
            $insertdata['LevelID'] = $idlevel + 1;
            $isEdit = false;
            $insertdata['IsAktif'] = 1;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'accesslevel');

        ## Menambah data di tabel fitur level
        if ($isEdit == false) {
            $countfitur = $this->crud->get_count(
                [
                    'select'=> '*',
                    'from'  => 'serverfitur',
                ]
            );
            for($i=1; $i<=$countfitur; $i++){
                $this->db->insert('fiturlevel', array('LevelID' => $insertdata['LevelID'], 'FiturID' =>  $i, 'ViewData' => 0, 'AddData' => 0, 'EditData' => 0, 'DeleteData' => 0, 'PrintData' => 0));
            }
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('LevelID') : $insertdata['LevelID'];
            $this->logsrv->insert_log([
                'Description' => $ket . ' data akses level ' . $id,
                'JenisTransaksi' => 'Akses Level',
                'Action' => $aksi
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

    public function hapus()
    {
        $kode = $this->input->get('LevelID');
        $countUser = $this->crud->get_count(
            [
                'select'=> '*',
                'from'  => 'userlogin',
                'where' => [['LevelID' => $kode]]
            ]
        );

        if ($countUser > 0) {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menghapus data karena Level digunakan di User Login."
            ]);
        } else {
            ## Menghapus data di tabel fitur level
            $fitur = $this->crud->delete(['LevelID' => $kode], 'fiturlevel');

            $res = $this->crud->delete(['LevelID' => $kode], 'accesslevel');
            if ($res) {
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Description' => 'hapus data akses level ' . $kode,
                    'JenisTransaksi' => 'Akses Level',
                    'Action' => 'hapus'
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
        $kode = $this->input->get('LevelID');
        $data = ['IsAktif' => (int)$this->input->get('IsAktif')];
        $result = $this->crud->update($data, ['LevelID' => $kode], "accesslevel");
        if ($result) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Description' => 'update data akses level ' . $kode,
                'JenisTransaksi' => 'Akses Level',
                'Action' => 'edit'
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

    public function fitur()
    {
        $LevelID = escape(base64_decode($this->uri->segment(4)));
        $data['LevelID'] = $LevelID;

        ## AMBIL DATA FITUR LEVEL
        $sql = "SELECT T1.FiturID, T1.FiturName, T2.LevelName, T2.ViewData, T2.AddData, T2.EditData, T2.DeleteData, T2.PrintData
            FROM (
                SELECT s.FiturID, s.FiturName
                FROM serverfitur AS s
                ) AS T1
            LEFT JOIN (
                SELECT f.LevelID, f.FiturID, f.ViewData, f.AddData, f.EditData, f.DeleteData, f.PrintData, a.LevelName
                FROM fiturlevel AS f
                LEFT JOIN accesslevel AS a ON f.LevelID = a.LevelID
                WHERE f.LevelID = '$LevelID'
                ) AS T2
            ON T1.FiturID = T2.FiturID
            ORDER BY T1.FiturID";
        $data['data'] = $this->db->query($sql)->result_array();

        $level = [
            'select' => '*',
            'from' => 'accesslevel',
            'where' => [['LevelID' => $LevelID]]
        ];
        $data['level'] = $this->crud->get_one_row($level);

        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu']   = 'akseslevel';
        $data['title']  = 'Fitur Level';
        $data['view']   = 'user/v_aksesfitur';
        loadview($data);
    }

    public function simpanfitur()
    {
        $level  = $this->input->post('LevelID');
        $lihat  = $this->input->post('ViewData');
        $tambah = $this->input->post('AddData');
        $ubah   = $this->input->post('EditData');
        $hapus  = $this->input->post('DeleteData');
        $cetak  = $this->input->post('PrintData');

        $dltfitur = $this->crud->delete(['LevelID' => $level], 'fiturlevel');

        $countlevelfitur = $this->crud->get_count(
            [
                'select' => '*',
                'from' => 'fiturlevel',
                'where' => [['LevelID' => $level]],
            ]
        );
        $countfitur = $this->crud->get_count(
            [
                'select'=> '*',
                'from'  => 'serverfitur',
            ]
        );
        // cek apakah level sudah memiliki semua fitur, jika belum tambahkan fitur baru untuk level
        if ($countfitur > $countlevelfitur) {
            for($i=$countlevelfitur+1; $i<=$countfitur; $i++){
                $add_new = $this->db->insert('fiturlevel', array('LevelID' => $level, 'FiturID' =>  $i, 'ViewData' => 0, 'AddData' => 0, 'EditData' => 0, 'DeleteData' => 0, 'PrintData' => 0));
            }
        }

        $view = [];
        $fiturview = [];
        foreach ($lihat as $key => $value) {
            $view[$key] = $value;
            if ($value == 'true') {
                $res = $this->crud->update(['ViewData' => 1], ['LevelID' => $level, 'FiturID' => $key], "fiturlevel");
                $value = 1;
            } else {
                $res = $this->crud->update(['ViewData' => 0], ['LevelID' => $level, 'FiturID' => $key], "fiturlevel");
                $value = 0;
            }
            $fiturview[$key] = $value;
        }
        $add = [];
        $fituradd = [];
        foreach ($tambah as $key => $value) {
            $add[$key] = $value;
            if ($value == 'true') {
                $res = $this->crud->update(['AddData' => 1], ['LevelID' => $level, 'FiturID' => $key], "fiturlevel");
                $value = 1;
            } else {
                $res = $this->crud->update(['AddData' => 0], ['LevelID' => $level, 'FiturID' => $key], "fiturlevel");
                $value = 0;
            }
            $fituradd[$key] = $value;
        }
        $edit = [];
        $fituredit = [];
        foreach ($ubah as $key => $value) {
            $edit[$key] = $value;
            if ($value == 'true') {
                $res = $this->crud->update(['EditData' => 1], ['LevelID' => $level, 'FiturID' => $key], "fiturlevel");
                $value = 1;
            } else {
                $res = $this->crud->update(['EditData' => 0], ['LevelID' => $level, 'FiturID' => $key], "fiturlevel");
                $value = 0;
            }
            $fituredit[$key] = $value;
        }
        $delete = [];
        $fiturdelete = [];
        foreach ($hapus as $key => $value) {
            $delete[$key] = $value;
            if ($value == 'true') {
                $res = $this->crud->update(['DeleteData' => 1], ['LevelID' => $level, 'FiturID' => $key], "fiturlevel");
                $value = 1;
            } else {
                $res = $this->crud->update(['DeleteData' => 0], ['LevelID' => $level, 'FiturID' => $key], "fiturlevel");
                $value = 0;
            }
            $fiturdelete[$key] = $value;
        }
        $print = [];
        $fiturprint = [];
        foreach ($cetak as $key => $value) {
            $print[$key] = $value;
            if ($value == 'true') {
                $res = $this->crud->update(['PrintData' => 1], ['LevelID' => $level, 'FiturID' => $key], "fiturlevel");
                $value = 1;
            } else {
                $res = $this->crud->update(['PrintData' => 0], ['LevelID' => $level, 'FiturID' => $key], "fiturlevel");
                $value = 0;
            }
            $fiturprint[$key] = $value;
        }
        if ($this->session->userdata('LevelID') == $level) {
            $this->session->set_userdata('fiturview', $fiturview);
            $this->session->set_userdata('fituradd', $fituradd);
            $this->session->set_userdata('fituredit', $fituredit);
            $this->session->set_userdata('fiturdelete', $fiturdelete);
            $this->session->set_userdata('fiturprint', $fiturprint);
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Description' => 'update data akses level ' . $level,
                'JenisTransaksi' => 'Akses Level',
                'Action' => 'edit'
            ]);
            $this->session->set_flashdata('berhasil', 'Berhasil mengubah fitur level!');
        } else {
            $this->session->set_flashdata('gagal', 'Gagal mengubah fitur level!');
        }

        redirect(base_url('user/akseslevel'));
    }
}
