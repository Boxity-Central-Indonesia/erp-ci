<?php
defined('BASEPATH') or exit('No direct script access allowed');

class person extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'mstperson';
    }

    public function customer()
    {
        checkAccess($this->session->userdata('fiturview')[4]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'customer';
            $data['title'] = 'Master Customer';
            $data['view'] = 'master/v_customer';
            $data['scripts'] = 'master/s_customer';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstperson p';
            $configData['where'] = [
                [
                    'p.IsAktif !=' => null,
                    'p.JenisPerson' => "CUSTOMER"
                ]
            ];
            $cari     = $this->input->get('cari');
            $status   = $this->input->get('isaktif');
            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPersonCP LIKE '%$cari%')";
            }

            if ($status != '') {
                $configData['filters'][] = " p.IsAktif = $status ";
            }

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'p.KodePerson', 'p.NamaPersonCP', 'p.NoHP', 'p.AlamatPerson', 'p.NamaUsaha', 'p.Keterangan', 'p.IsAktif', 'p.JenisPerson', 'p.KodeManual', 'p.TglInput'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'p.KodePerson';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'p.KodePerson', 'p.NamaPersonCP', 'p.NoHP', 'p.AlamatPerson', 'p.NamaUsaha', 'p.Keterangan', 'p.IsAktif', 'p.JenisPerson', 'p.KodeManual', 'p.TglInput',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 4; //FiturID di tabel serverfitur
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
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodePerson'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodePerson'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodePerson'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>') . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodePerson'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodePerson'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodePerson'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodePerson'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>');
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodePerson'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpancustomer()
    {
        $insertdata = $this->input->post();
        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('KodePerson') != null && $this->input->post('KodePerson') != '')) {
            $insertdata['KodePerson'] = $this->crud->get_kode([
                'select' => 'RIGHT(KodePerson, 7) AS KODE',
                'where' => [['JenisPerson' => "CUSTOMER"]],
                'limit' => 1,
                'order_by' => 'KodePerson DESC',
                'prefix' => 'CST'
            ]);
            $insertdata['IsAktif'] = 1;
            $insertdata['JenisPerson'] = 'CUSTOMER';
            $insertdata['TglInput'] = date('Y-m-d H:i:s');
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'mstperson');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('KodePerson') : $insertdata['KodePerson'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Master Customer',
                'Description' => $ket . ' data master customer ' . $id
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

    public function supplier()
    {
        checkAccess($this->session->userdata('fiturview')[5]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'supplier';
            $data['title'] = 'Master Supplier';
            $data['view'] = 'master/v_supplier';
            $data['scripts'] = 'master/s_supplier';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstperson p';
            $configData['where'] = [
                [
                    'p.IsAktif !=' => null,
                    'p.JenisPerson' => "SUPPLIER"
                ]
            ];
            $cari     = $this->input->get('cari');
            $status   = $this->input->get('isaktif');
            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPersonCP LIKE '%$cari%')";
            }

            if ($status != '') {
                $configData['filters'][] = " p.IsAktif = $status ";
            }

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'p.KodePerson', 'p.NamaPersonCP', 'p.NoHP', 'p.AlamatPerson', 'p.NamaUsaha', 'p.Keterangan', 'p.IsAktif', 'p.JenisPerson', 'p.KodeManual', 'p.TglInput'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'p.KodePerson';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'p.KodePerson', 'p.NamaPersonCP', 'p.NoHP', 'p.AlamatPerson', 'p.NamaUsaha', 'p.Keterangan', 'p.IsAktif', 'p.JenisPerson', 'p.KodeManual', 'p.TglInput',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 5; //FiturID di tabel serverfitur
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
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodePerson'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodePerson'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodePerson'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>') . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodePerson'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodePerson'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodePerson'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodePerson'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>');
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodePerson'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpansupplier()
    {
        $insertdata = $this->input->post();
        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('KodePerson') != null && $this->input->post('KodePerson') != '')) {
            $insertdata['KodePerson'] = $this->crud->get_kode([
                'select' => 'RIGHT(KodePerson, 7) AS KODE',
                'where' => [['JenisPerson' => "SUPPLIER"]],
                'limit' => 1,
                'order_by' => 'KodePerson DESC',
                'prefix' => 'SPL'
            ]);
            $insertdata['IsAktif'] = 1;
            $insertdata['JenisPerson'] = 'SUPPLIER';
            $insertdata['TglInput'] = date('Y-m-d H:i:s');
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'mstperson');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('KodePerson') : $insertdata['KodePerson'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Master Supplier',
                'Description' => $ket . ' data master supplier ' . $id
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
        $kode = $this->input->get('KodePerson');

        $cekpembelian = $this->crud->get_count([
            'select' => 'KodePerson',
            'from' => 'transpembelian',
            'where' => [['KodePerson' => $kode]],
        ]);

        $cekpenjualan = $this->crud->get_count([
            'select' => 'KodePerson',
            'from' => 'transpenjualan',
            'where' => [['KodePerson' => $kode]],
        ]);

        if ($cekpembelian > 0 || $cekpenjualan > 0) {
            $res = null;
        } else {
            $res = $this->crud->delete(['KodePerson' => $kode], 'mstperson');
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $menu = (substr($kode, 0, 3) == 'CST') ? 'Customer' : 'Supplier';
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Master ' . $menu,
                'Description' => 'hapus data master ' . strtolower($menu) . ' ' . $kode
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
        $kode = $this->input->get('KodePerson');
        $value = (int) $this->input->get('IsAktif');

        $data = ['IsAktif' => $value];
        $result = $this->crud->update($data, ['KodePerson' => $kode], "mstperson");

        if ($result) {
            ## INSERT TO SERVER LOG
            $menu = (substr($kode, 0, 3) == 'CST') ? 'Customer' : 'Supplier';
            $this->logsrv->insert_log([
                'Action' => 'edit',
                'JenisTransaksi' => 'Master ' . $menu,
                'Description' => 'update data master ' . strtolower($menu) . ' ' . $kode
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
