<?php
defined('BASEPATH') or exit('No direct script access allowed');

class tahunanggaran extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'msttahunanggaran';
        checkAccess($this->session->userdata('fiturview')[6]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[6]);
        if (!$this->input->is_ajax_request()) {
            $sql = [
                'select' => 'KodeTahun, Keterangan, IsAktif'
            ];
            $data['data'] = $this->crud->get_rows($sql);
            $data['breadcrumb'][] = array('Name' => 'Tahun Anggaran', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'tahunanggaran';
            $data['title'] = 'Master Tahun Anggaran';
            $data['view'] = 'master/v_tahunanggaran';
            $data['scripts'] = 'master/s_tahunanggaran';
            loadview($data);
        } else {

            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'msttahunanggaran';
            $cari     = $this->input->get('cari');
            $status   = $this->input->get('isaktif');
            if ($cari != '') {
                $configData['filters'][] = " (Keterangan LIKE '%$cari%' OR KodeTahun LIKE '%$cari%')";
            }

            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }
            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'KodeTahun', 'Keterangan', 'IsAktif'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'KodeTahun',
                'Keterangan',
                'IsAktif',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 6; //FiturID di tabel serverfitur
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
                // $temp['TanggalAwal'] = date_indo($temp['TanggalAwal']);
                // $temp['TanggalAkhir'] = date_indo($temp['TanggalAkhir']);
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodeTahun'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodeTahun'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodeTahun'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>') . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeTahun'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodeTahun'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodeTahun'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodeTahun'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>');
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodeTahun'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria="true"></span></a>';
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
        $insertdata =  $this->input->post();
        unset($insertdata['isedit']);
        unset($insertdata['kodeLama']);
        $isEdit = $this->input->post('isedit');

        // if ($isEdit == 'false') {
        //     $this->crud->update(
        //         [
        //             'IsAktif' => 0
        //         ],
        //         [],
        //         "msttahunanggaran"
        //     );
        // } else {
        //     unset($insertdata['IsAktif']);
        // }
        $insertdata['IsAktif'] = 1;
        $res = $this->crud->insert_or_update($insertdata, 'msttahunanggaran');
        if ($res) {
            $keterangan = 'input data tahun ' . $insertdata['KodeTahun'];
            $aksi = 'ubah';
            // $this->logsrv->insert_log([
            //     'Description' => $keterangan,
            //     'Action' => $aksi
            // ]);
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil Edit Data" : "Berhasil Menambah Data")
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ($isEdit ? "Gagal Edit Data" : "Gagal Menambah Data")
            ]);
        }
    }
    public function cekkode()
    {
        $kodeLama = $this->input->get('kodeLama');
        $KodeTahun = $this->input->get('KodeTahun');
        if ($kodeLama) {
            $count = $this->crud->get_count([
                'select' => 'KodeTahun',
                'from' => 'msttahunanggaran',
                'where' => [[
                    'KodeTahun' => $KodeTahun,
                    'KodeTahun !=' => $kodeLama
                ]]
            ]);
        } else {
            $count = $this->crud->get_count([
                'select' => 'KodeTahun',
                'from' => 'msttahunanggaran',
                'where' => [['KodeTahun' => $KodeTahun]]
            ]);
        }
        if ($count > 0) {
            echo json_encode([
                'status' => false,
                'msg'  => 'kdoe tahun sudah digunakan'
            ]);
        } else {
            echo json_encode([
                'status' => true,
                'msg'  => 'kdoe tahun tersedia'
            ]);
        }
    }
    public function hapus()
    {
        $kodetahun = $this->input->get('KodeTahun');

        $countrekap = $this->crud->get_count([
            'select' => 'IDRekap, KodeTahun',
            'from' => 'rekapinsentifbulanan',
            'where' => [['KodeTahun' => $kodetahun]],
        ]);

        $countkas = $this->crud->get_count([
            'select' => 'NoTransKas, KodeTahun',
            'from' => 'transaksikas',
            'where' => [['KodeTahun' => $kodetahun]],
        ]);

        $countjurnal = $this->crud->get_count([
            'select' => 'IDTransJurnal, KodeTahun',
            'from' => 'transjurnal',
            'where' => [['KodeTahun' => $kodetahun]],
        ]);

        $tahunaktif = $this->akses->get_tahun_aktif();

        if ($countrekap > 0 || $countkas > 0 || $countjurnal > 0) {
            echo json_encode([
                'status' => false,
                'msg'  => "Kode Tahun gagal dihapus karena sudah digunakan."
            ]);
        } elseif ($tahunaktif == $kodetahun) {
            echo json_encode([
                'status' => false,
                'msg'  => "Kode Tahun gagal dihapus karena menjadi tahun aktif."
            ]);
        } else {
            $res = $this->crud->delete(['KodeTahun' => $kodetahun], 'msttahunanggaran');

            if ($res) {
                $keterangan = 'delete data tahun ' . $kodetahun;
                $aksi = 'hapus';
                // $this->logsrv->insert_log([
                //     'Description' => $keterangan,
                //     'Action' => $aksi
                // ]);
                echo json_encode([
                    'status' => true,
                    'msg'  => "Berhasil Menghapus Data"
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'msg'  => "Kode Tahun gagal dihapus."
                ]);
            }
        }

    }

    public function aktif()
    {
        $kode = $this->input->get('KodeTahun');
        $value = (int) $this->input->get('IsAktif');

        $this->crud->update(
            [
                'IsAktif' => 0
            ],
            [],
            "msttahunanggaran"
        );

        $data = ['IsAktif' => $value];
        $result = $this->crud->update($data, ['KodeTahun' => $kode], "msttahunanggaran");

        if ($result) {
            $keterangan = 'update data tahun ' . $kode;
            $aksi = 'edit';
            ## INSERT TO SERVER LOG
            // $this->logsrv->insert_log([
            //     'Description' => $keterangan,
            //     'Action' => $aksi
            // ]);
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
