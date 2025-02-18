<?php
defined('BASEPATH') or exit('No direct script access allowed');

class gaji_pokok extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'mstkomponengaji';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[52]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[52]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'gajipokok';
            $data['title'] = 'Setting Gaji Pokok';
            $data['view'] = 'payroll/v_gaji_pokok';
            $data['scripts'] = 'payroll/s_gaji_pokok';

            $dtjab = [
                'select' => 'KodeJabatan, NamaJabatan',
                'from' => 'mstjabatan',
                'where' => [['IsAktif !=' => null]],
                'order_by' => 'KodeJabatan'
            ];
            $data['dtjab'] = $this->crud->get_rows($dtjab);

            $atasan = [
                'select' => 'pgat.KodePegawai, pgat.NamaPegawai, jbat.NamaJabatan',
                'from' => 'mstpegawai pgat',
                'where' => [
                    [
                        'pgat.IsAktif !=' => null,
                        // 'KodeJabAtasanLangsung' => null
                    ]
                ],
                'join' => [
                    [
                        'table' => ' mstjabatan jbat',
                        'on' => "jbat.KodeJabatan = pgat.KodeJabatan",
                        'param' => 'LEFT'
                    ]
                ],
                'order_by' => 'pgat.KodePegawai'
            ];
            $data['atasan'] = $this->crud->get_rows($atasan);

            // get data bank from API
            $data['dtbank'] = $this->lokasi->get_all_bank();

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstpegawai p';

            $status   = $this->input->get('isaktif');
            $cari     = $this->input->get('cari');
            $jabatan  = $this->input->get('jabatan');

            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPegawai LIKE '%$cari%')";
            }
            if ($status != '') {
                $configData['filters'][] = " p.IsAktif = $status ";
            }
            if ($jabatan != '') {
                $configData['where'] = [['p.KodeJabatan' => $jabatan]];
            }

            $configData['join'] = [
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = p.KodeJabatan",
                    'param' => 'LEFT'

                ],
                [
                    'table' => ' mstpegawai pa',
                    'on' => "pa.KodePegawai = p.KodeJabAtasanLangsung",
                    'param' => 'LEFT'
                ],
                [
                    'table' => ' mstjabatan ja',
                    'on' => "ja.KodeJabatan = pa.KodeJabatan",
                    'param' => 'LEFT'
                ]
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'p.KodePegawai', 'p.NIP', 'p.NamaPegawai', 'p.TTL', 'p.Alamat', 'p.TelpHP', 'p.Email', 'p.TglMulaiKerja', 'p.TglResign', 'p.IsAktif', 'p.IDFinger', 'p.KodeJabatan', 'j.NamaJabatan', 'p.KodeJabAtasanLangsung', 'pa.NamaPegawai as NamaAtasan', 'ja.NamaJabatan as JabatanAtasan', 'p.KodeBank', 'p.NoRek', 'p.GajiPokok', 'p.IsGajiHarian', 'p.JenisPegawai'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'p.KodePegawai';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'p.KodePegawai', 'p.NIP', 'p.NamaPegawai', 'p.TTL', 'p.Alamat', 'p.TelpHP', 'p.Email', 'p.TglMulaiKerja', 'p.TglResign', 'p.IsAktif', 'p.IDFinger', 'p.KodeJabatan', 'j.NamaJabatan', 'p.KodeJabAtasanLangsung', 'pa.NamaPegawai as NamaAtasan', 'ja.NamaJabatan as JabatanAtasan', 'p.KodeBank', 'p.NoRek', 'p.GajiPokok', 'p.IsGajiHarian', 'p.JenisPegawai',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 52; //FiturID di tabel serverfitur
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
                $temp['IsGajiHarian'] = $temp['IsGajiHarian'] == "1" ? 'Harian' : 'Bulanan';
                if ($canEdit == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
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
        $updatedata['GajiPokok']    = str_replace(['.', ','], ['', '.'], $this->input->post('GajiPokok'));
        $updatedata['IsGajiHarian'] = ($this->input->post('IsGajiHarian') == 'on') ? 1 : 0;
        $res = $this->crud->update($updatedata, ['KodePegawai' => $this->input->post('KodePegawai')], 'mstpegawai');

        if ($res) {
            $keterangan = 'update data gaji pokok ' . $this->input->post('NamaPegawai');
            $aksi = 'edit';
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Description' => $keterangan,
                'JenisTransaksi' => 'Setting Gaji Pokok',
                'Action' => $aksi
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ("Berhasil Mengubah Data")
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ("Gagal Mengubah Data")
            ]);
        }
    }

    public function hapus()
    {
        $kode = $this->input->get('KodeKompGaji');
        $res = $this->crud->delete(['KodeKompGaji' => $kode], 'mstkomponengaji');

        if ($res) {
            $keterangan = 'hapus data gaji pokok ' . $kode;
            $aksi = 'hapus';
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Description' => $keterangan,
                'JenisTransaksi' => 'Setting Gaji Pokok',
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
            $keterangan = 'update data gaji pokok ' . $kode;
            $aksi = 'edit';
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Description' => $keterangan,
                'JenisTransaksi' => 'Setting Gaji Pokok',
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
