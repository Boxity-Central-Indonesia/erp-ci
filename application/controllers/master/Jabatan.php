<?php
defined('BASEPATH') or exit('No direct script access allowed');

class jabatan extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'mstjabatan';
        checkAccess($this->session->userdata('fiturview')[9]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[9]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'jabatan';
            $data['title'] = 'Master Jabatan';
            $data['view'] = 'master/v_jabatan';
            $data['scripts'] = 'master/s_jabatan';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstjabatan j';

            $status   = $this->input->get('isaktif');
            $cari     = $this->input->get('cari');

            if ($cari != '') {
                $configData['filters'][] = " (j.NamaJabatan LIKE '%$cari%')";
            }
            if ($status != '') {
                $configData['filters'][] = " j.IsAktif = $status ";
            }

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.KodeJabatan', 'j.NamaJabatan', 'j.Deskripsi', 'j.IsAktif'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'j.KodeJabatan';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'j.KodeJabatan', 'j.NamaJabatan', 'j.Deskripsi', 'j.IsAktif',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 9; //FiturID di tabel serverfitur
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
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodeJabatan'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodeJabatan'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodeJabatan'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>') . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeJabatan'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodeJabatan'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodeJabatan'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodeJabatan'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>');
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodeJabatan'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
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
        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('KodeJabatan') != null && $this->input->post('KodeJabatan') != '')) {
            $insertdata['KodeJabatan'] = $this->crud->get_kode([
                'select' => 'RIGHT(KodeJabatan, 7) AS KODE',
                'limit' => 1,
                'order_by' => 'KodeJabatan DESC',
                'prefix' => 'JBT'
            ]);
            $insertdata['IsAktif'] = 1;
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'mstjabatan');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('KodeJabatan') : $insertdata['KodeJabatan'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Master Jabatan',
                'Description' => $ket . ' data master jabatan ' . $id
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
        $kode = $this->input->get('KodeJabatan');
        $countPegawai = $this->crud->get_count(
            [
                'select' => '*',
                'from' => 'mstpegawai',
                'where' => [['KodeJabatan' => $kode]],
            ]
        );

        if ($countPegawai > 0) {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menghapus data karena Kode Jabatan digunakan di Master Pegawai."
            ]);
        } else {
            $res = $this->crud->delete(['KodeJabatan' => $kode], 'mstjabatan');

            if ($res) {
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => 'hapus',
                    'JenisTransaksi' => 'Master Jabatan',
                    'Description' => 'hapus data master jabatan ' . $kode
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
        $kode = $this->input->get('KodeJabatan');
        $value = (int) $this->input->get('IsAktif');

        $data = ['IsAktif' => $value];
        $result = $this->crud->update($data, ['KodeJabatan' => $kode], "mstjabatan");

        if ($result) {
            $keterangan = 'update data tahun ' . $kode;
            $aksi = 'edit';
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'edit',
                'JenisTransaksi' => 'Master Jabatan',
                'Description' => 'hapus data master jabatan ' . $kode
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
