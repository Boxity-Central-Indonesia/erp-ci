<?php
defined('BASEPATH') or exit('No direct script access allowed');

class userlogin extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'userlogin';
        checkAccess($this->session->userdata('fiturview')[2]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[2]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'userlogin';
            $data['title'] = 'User Login';
            $data['view'] = 'user/v_userlogin';
            $data['scripts'] = 'user/s_userlogin';

            $akses = [
                'select' => 'LevelID, LevelName, DivisiName',
                'from' => 'accesslevel',
                'order_by' => 'LevelID'
            ];
            $data['akses'] = $this->crud->get_rows($akses);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'userlogin u';
            $configData['where'] = [
                ['u.IsAktif !=' => null]
            ];
            $cari     = $this->input->get('cari');
            $status   = $this->input->get('isaktif');
            if ($cari != '') {
                $configData['filters'][] = " (u.UserName LIKE '%$cari%' OR u.ActualName LIKE '%$cari%' OR u.Email LIKE '%$cari%')";
            }

            if ($status != '') {
                $configData['filters'][] = " u.IsAktif = $status ";
            }

            $configData['join'] = [
                [
                    'table' => ' accesslevel a',
                    'on' => "a.LevelID = u.LevelID",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'u.UserName', 'FROM_BASE64(u.UserPsw) as UserPsw', 'u.ActualName', 'u.Address', 'u.Phone', 'u.Email', 'u.LevelID', 'u.IsAktif', 'a.LevelName'
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
                'u.UserName', 'FROM_BASE64(u.UserPsw) as UserPsw', 'u.ActualName', 'u.Address', 'u.Phone', 'u.Email', 'u.LevelID', 'u.IsAktif', 'a.LevelName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 2; //FiturID di tabel serverfitur
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
                if ($temp['UserName'] != 'admin') {
                    if ($canEdit == 1 && $canDelete == 1) {
                        $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>
                        ' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['UserName'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['UserName'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['UserName'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>') . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['UserName'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                    } elseif ($canEdit == 1 && $canDelete != 1) {
                        $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>
                        ' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['UserName'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['UserName'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['UserName'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>');
                    } elseif ($canDelete == 1 && $canEdit != 1) {
                        $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['UserName'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                    } else {
                        $temp['btn_aksi'] = '';
                    }
                } else {
                    if ($canEdit == 1) {
                        $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                    } else {
                        $temp['btn_aksi'] = '';
                    }
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
        unset($insertdata['Isedit']);
        unset($insertdata['bumdesLama']);
        $isEdit = true;

        ## POST DATA
        if ($this->input->post('Isedit') == 'tambah') {
            $insertdata['IsAktif'] = 1;
            $insertdata['UserPsw'] = base64_encode($this->input->post('UserPsw'));
            $insertdata['IsOnline'] = 0;
            $isEdit = false;

            $res = $this->crud->insert($insertdata, 'userlogin');
        } else {
            $username = $this->input->post('UserName');
            unset($insertdata['UserName']);
            $insertdata['UserPsw'] = base64_encode($this->input->post('UserPsw'));
            $isEdit = true;

            $res = $this->crud->update($insertdata, ['UserName' => $username], 'userlogin');
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'User Login',
                'Description' => $ket . ' data user login ' . $this->input->post('UserName'),
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

    public function checkUsername()
    {
        $UserName = $this->input->get('UserName');
        $emailLama = $this->input->get('emailLama');
        $Email = $this->input->get('Email');
        if ($emailLama != null && $emailLama != '') {
            $countUsername = 0;
            $countEmail =  $this->crud->get_count([
                'select' => 'Email',
                'from' => 'userlogin',
                'where' => [[
                    'Email' => $Email,
                    'Email !=' => $emailLama
                ]]
            ]);
        } else {
            $countUsername =  $this->crud->get_count([
                'select' => 'UserName',
                'from' => 'userlogin',
                'where' => [['UserName' => $UserName]]
            ]);
            $countEmail =  $this->crud->get_count([
                'select' => 'Email',
                'from' => 'userlogin',
                'where' => [['Email' => $Email]]
            ]);
        }
        if ($countUsername > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Username telah terdaftar']);
        } elseif ($countEmail > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Email telah terdaftar']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Username dan Email tersedia']);
        }
    }

    public function hapus()
    {
        $kode = $this->input->get('UserName');
        $res = $this->crud->delete(['UserName' => $kode], 'userlogin');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'User Login',
                'Description' => 'hapus data user login ' . $kode,
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

    public function aktif()
    {
        $kode = $this->input->get('UserName');
        $data = ['IsAktif' => (int)$this->input->get('IsAktif')];
        $result = $this->crud->update($data, ['UserName' => $kode], 'userlogin');
        if ($result) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'edit',
                'JenisTransaksi' => 'User Login',
                'Description' => 'update data user login ' . $kode,
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
}
