<?php
defined('BASEPATH') or exit('No direct script access allowed');

class komponen_gaji extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'mstkomponengaji';
        checkAccess($this->session->userdata('fiturview')[51]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[51]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'komponengaji';
            $data['title'] = 'Setting Komponen Gaji';
            $data['view'] = 'payroll/v_komponen_gaji';
            $data['scripts'] = 'payroll/s_komponen_gaji';

            $dtjab = [
                'select' => 'KodeJabatan, NamaJabatan',
                'from' => 'mstjabatan',
                'where' => [['IsAktif !=' => null]],
                'order_by' => 'KodeJabatan'
            ];
            $data['dtjab'] = $this->crud->get_rows($dtjab);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstkomponengaji k';

            $configData['where'] = [
                [
                    'k.JenisKomponen !=' => 'GAJI POKOK',
                    'k.IsAktif !=' => null,
                ]
            ];

            $status   = $this->input->get('isaktif');
            $cari     = $this->input->get('cari');

            if ($cari != '') {
                $configData['filters'][] = " (k.KodeKompGaji LIKE '%$cari%' OR k.NamaKomponenGaji LIKE '%$cari%' OR k.JenisKomponen LIKE '%$cari%')";
            }
            if ($status != '') {
                $configData['filters'][] = " k.IsAktif = $status ";
            }

            $configData['join'] = [
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = k.KodeJabatan",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'k.KodeKompGaji', 'k.NamaKomponenGaji', 'k.IsAktif', 'k.JenisKomponen', 'k.NominalRp', 'k.NominalProses', 'k.Deskripsi', 'k.CaraHitung', 'k.Kriteria', 'k.KodeJabatan', 'j.NamaJabatan'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'k.KodeKompGaji';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'k.KodeKompGaji', 'k.NamaKomponenGaji', 'k.IsAktif', 'k.JenisKomponen', 'k.NominalRp', 'k.NominalProses', 'k.Deskripsi', 'k.CaraHitung', 'k.Kriteria', 'k.KodeJabatan', 'j.NamaJabatan',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 51; //FiturID di tabel serverfitur
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
                if ($temp['JenisKomponen'] == 'UANG MAKAN') {
                    $jeniskomp = 'Uang Makan';
                } elseif ($temp['JenisKomponen'] == 'LEMBUR') {
                    $jeniskomp = 'Lembur';
                } elseif ($temp['JenisKomponen'] == 'THR') {
                    $jeniskomp = 'Tunjangan Hari Raya';
                } elseif ($temp['JenisKomponen'] == 'TUNJANGAN JABATAN') {
                    $jeniskomp = 'Tunjangan Jabatan';
                } elseif ($temp['JenisKomponen'] == 'TUNJANGAN DINAS LUAR') {
                    $jeniskomp = 'Tunjangan Dinas Luar';
                } elseif ($temp['JenisKomponen'] == 'POT TELAT') {
                    $jeniskomp = 'Potongan Telat';
                } elseif ($temp['JenisKomponen'] == 'POT ALPHA') {
                    $jeniskomp = 'Potongan Alpha';
                } elseif ($temp['JenisKomponen'] == 'POT CUTI') {
                    $jeniskomp = 'Potongan Cuti';
                }
                $temp['JenisKomp'] = $jeniskomp;
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodeKompGaji'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodeKompGaji'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodeKompGaji'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>') . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeKompGaji'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodeKompGaji'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodeKompGaji'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodeKompGaji'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>');
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodeKompGaji'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
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
        unset($insertdata['NominalRp']);
        $rp = str_replace(['.', ','], ['', '.'], $this->input->post('NominalRp'));
        $insertdata['NominalRp'] = $rp;

        ## POST DATA
        if (!($this->input->post('KodeKompGaji') != null && $this->input->post('KodeKompGaji') != '')) {
            $insertdata['KodeKompGaji'] = $this->crud->get_kode([
                'select' => 'RIGHT(KodeKompGaji, 7) AS KODE',
                'limit' => 1,
                'order_by' => 'KodeKompGaji DESC',
                'prefix' => 'KOM'
            ]);
            $insertdata['IsAktif'] = 1;
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'mstkomponengaji');

        if ($res) {
            if ($isEdit) {
                $keterangan = 'update data komponen gaji ' . $insertdata['KodeKompGaji'];
                $aksi = 'edit';
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Description' => $keterangan,
                    'JenisTransaksi' => 'Setting Komponen Gaji',
                    'Action' => $aksi
                ]);
            } else {
                $keterangan = 'tambah data komponen gaji ' . $insertdata['KodeKompGaji'];
                $aksi = 'tambah';
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Description' => $keterangan,
                    'JenisTransaksi' => 'Setting Komponen Gaji',
                    'Action' => $aksi
                ]);
            }
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
        $kode = $this->input->get('KodeKompGaji');
        $res = $this->crud->delete(['KodeKompGaji' => $kode], 'mstkomponengaji');

        if ($res) {
            $keterangan = 'hapus data komponen gaji ' . $kode;
            $aksi = 'hapus';
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Description' => $keterangan,
                'JenisTransaksi' => 'Setting Komponen Gaji',
                'Action' => $aksi
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
        $kode = $this->input->get('KodeKompGaji');
        $value = (int) $this->input->get('IsAktif');

        $data = ['IsAktif' => $value];
        $result = $this->crud->update($data, ['KodeKompGaji' => $kode], "mstkomponengaji");

        if ($result) {
            $keterangan = 'update data komponen gaji ' . $kode;
            $aksi = 'edit';
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Description' => $keterangan,
                'JenisTransaksi' => 'Setting Komponen Gaji',
                'Action' => $aksi
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
