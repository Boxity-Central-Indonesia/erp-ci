<?php
defined('BASEPATH') or exit('No direct script access allowed');

class gudang extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'mstgudang';
        checkAccess($this->session->userdata('fiturview')[3]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[3]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'gudang';
            $data['title'] = 'Master Gudang';
            $data['view'] = 'master/v_gudang';
            $data['scripts'] = 'master/s_gudang';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstgudang g';

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (g.NamaGudang LIKE '%$cari%')";
            }

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'g.KodeGudang', 'g.NamaGudang', 'g.Alamat', 'g.Deskripsi', 'g.TglInput'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'g.KodeGudang';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'g.KodeGudang', 'g.NamaGudang', 'g.Alamat', 'g.Deskripsi', 'g.TglInput',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 3; //FiturID di tabel serverfitur
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
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeGudang'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodeGudang'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
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
        if (!($this->input->post('KodeGudang') != null && $this->input->post('KodeGudang') != '')) {
            $insertdata['KodeGudang'] = $this->crud->get_kode([
                'select' => 'RIGHT(KodeGudang, 7) AS KODE',
                'limit' => 1,
                'order_by' => 'KodeGudang DESC',
                'prefix' => 'GDG'
            ]);
            $insertdata['TglInput'] = date("Y-m-d H:i:s");
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'mstgudang');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('KodeGudang') : $insertdata['KodeGudang'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Master Gudang',
                'Description' => $ket . ' data master gudang ' . $id
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
        $kode = $this->input->get('KodeGudang');

        $cek_gudangasal = $this->crud->get_count([
            'select' => 'NoTrans',
            'from' => 'transaksibarang',
            'where' => [['GudangAsal' => $kode]],
        ]);

        $cek_gudangtujuan = $this->crud->get_count([
            'select' => 'NoTrans',
            'from' => 'transaksibarang',
            'where' => [['GudangTujuan' => $kode]],
        ]);

        if ($cek_gudangasal > 0 || $cek_gudangtujuan > 0) {
            $res = null;
        } else {
            $res = $this->crud->delete(['KodeGudang' => $kode], 'mstgudang');
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Master Gudang',
                'Description' => 'hapus data master gudang ' . $kode
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
