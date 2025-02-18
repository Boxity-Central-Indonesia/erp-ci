<?php
defined('BASEPATH') or exit('No direct script access allowed');

class adjustment_absensi extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'absensipegawai a';
        checkAccess($this->session->userdata('fiturview')[50]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[50]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'adjustmentabs';
            $data['title'] = 'Adjustment Absensi';
            $data['view'] = 'payroll/v_adjustment_absensi';
            $data['scripts'] = 'payroll/s_adjustment_absensi';

            $data['dtpeg'] = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstpegawai',
                    'where' => [['IsAktif !=' => null]]
                ]
            );

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'absensipegawai a';

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPegawai LIKE '%$cari%')";
            }

            $bulan     = $this->input->get('bulan');
            if ($bulan) {
                $configData['where'] = [['DATE_FORMAT(a.Tanggal,  "%Y-%m") =' => $bulan]];
            }

            $configData['join'] = [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = a.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = p.KodeJabatan",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'a.Tanggal', 'a.IDFinger', 'TIME(a.JamKerjaMasuk) as JamKerjaMasuk', 'TIME(a.JamKerjaPulang) as JamKerjaPulang', 'TIME(a.JamMasuk) as JamMasuk', 'TIME(a.JamPulang) as JamPulang', 'a.Telat', 'a.Keterangan', 'a.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'a.Tanggal, a.KodePegawai';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'a.Tanggal', 'a.IDFinger', 'TIME(a.JamKerjaMasuk) as JamKerjaMasuk', 'TIME(a.JamKerjaPulang) as JamKerjaPulang', 'TIME(a.JamMasuk) as JamMasuk', 'TIME(a.JamPulang) as JamPulang', 'a.Telat', 'a.Keterangan', 'a.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 50; //FiturID di tabel serverfitur
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
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodePegawai'] . ' data-kode2=' . $temp['Tanggal'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodePegawai'] . ' data-kode2=' . $temp['Tanggal'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $temp['Tgl'] = isset($temp['Tanggal']) ? shortdate_indo(date('Y-m-d', strtotime($temp['Tanggal']))) : '';
                $temp['JamKerjaMasuk'] = isset($temp['JamKerjaMasuk']) ? date('H:i', strtotime($temp['JamKerjaMasuk'])) : '';
                $temp['JamKerjaPulang'] = isset($temp['JamKerjaPulang']) ? date('H:i', strtotime($temp['JamKerjaPulang'])) : '';
                $temp['JamMasuk'] = isset($temp['JamMasuk']) ? date('H:i', strtotime($temp['JamMasuk'])) : '';
                $temp['JamPulang'] = isset($temp['JamPulang']) ? date('H:i', strtotime($temp['JamPulang'])) : '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function checkDB()
    {
        $KodePegawai = $this->input->get('KodePegawai');
        $Tanggal = $this->input->get('Tanggal');
        $count =  $this->crud->get_count([
            'select' => '*',
            'from' => 'absensipegawai',
            'where' => [
                [
                    'KodePegawai' => $KodePegawai,
                    'Tanggal' => $Tanggal,
                ]
            ]
        ]);
        if ($count > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Absensi pegawai sudah diinputkan di tanggal yang sama']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Tanggal untuk pegawai tersedia']);
        }
    }

    public function simpan()
    {
        ## POST DATA
        if ($this->input->post('Isedit') == 'tambah') {
            $insertdata = $this->input->post();
            unset($insertdata['Isedit']);
            unset($insertdata['kodepegLama']);
            $insertdata['JamKerjaMasuk'] = $this->input->post('Tanggal').' '.$this->input->post('JamKerjaMasuk');
            $insertdata['JamKerjaPulang'] = $this->input->post('Tanggal').' '.$this->input->post('JamKerjaPulang');
            $insertdata['JamMasuk'] = $this->input->post('Tanggal').' '.$this->input->post('JamMasuk');
            $insertdata['JamPulang'] = $this->input->post('Tanggal').' '.$this->input->post('JamPulang');
            $selisihmasuk = ((strtotime($insertdata['JamMasuk']) - strtotime($insertdata['JamKerjaMasuk']))/60 > 0) ? (strtotime($insertdata['JamMasuk']) - strtotime($insertdata['JamKerjaMasuk']))/60 : 0;
            $selisihpulang = ((strtotime($insertdata['JamKerjaPulang']) - strtotime($insertdata['JamPulang']))/60 > 0) ? (strtotime($insertdata['JamKerjaPulang']) - strtotime($insertdata['JamPulang']))/60 : 0;
            $insertdata['Telat'] = $selisihmasuk + $selisihpulang;

            $isEdit = false;
            $res = $this->crud->insert($insertdata, 'absensipegawai');
        } else {
            $updatedata = $this->input->post();
            unset($updatedata['Isedit']);
            unset($updatedata['kodepegLama']);
            $updatedata['JamKerjaMasuk'] = $this->input->post('Tanggal').' '.$this->input->post('JamKerjaMasuk');
            $updatedata['JamKerjaPulang'] = $this->input->post('Tanggal').' '.$this->input->post('JamKerjaPulang');
            $updatedata['JamMasuk'] = $this->input->post('Tanggal').' '.$this->input->post('JamMasuk');
            $updatedata['JamPulang'] = $this->input->post('Tanggal').' '.$this->input->post('JamPulang');
            $selisihmasuk = ((strtotime($updatedata['JamMasuk']) - strtotime($updatedata['JamKerjaMasuk']))/60 > 0) ? (strtotime($updatedata['JamMasuk']) - strtotime($updatedata['JamKerjaMasuk']))/60 : 0;
            $selisihpulang = ((strtotime($updatedata['JamKerjaPulang']) - strtotime($updatedata['JamPulang']))/60 > 0) ? (strtotime($updatedata['JamKerjaPulang']) - strtotime($updatedata['JamPulang']))/60 : 0;
            $updatedata['Telat'] = ($this->input->post('Keterangan') == 'Hadir' || $this->input->post('Keterangan') == 'Dinas Luar') ? $selisihmasuk + $selisihpulang : 0;

            $isEdit = true;
            $res = $this->crud->update($updatedata, ['Tanggal' => $this->input->post('Tanggal'), 'KodePegawai' => $this->input->post('kodepegLama')], 'absensipegawai');
        }

        if ($res) {
            if ($isEdit) {
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => 'edit',
                    'JenisTransaksi' => 'Adjustment Absensi',
                    'Description' => 'update data absensi pegawai ' . $this->input->post('kodepegLama') . ' ' . date('d-m-Y', strtotime($updatedata['Tanggal'])),
                ]);
            } else {
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => 'tambah',
                    'JenisTransaksi' => 'Adjustment Absensi',
                    'Description' => 'tambah data absensi pegawai ' . $insertdata['KodePegawai'] . ' ' . date('d-m-Y', strtotime($insertdata['Tanggal'])),
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
        $kode = $this->input->get('KodePegawai');
        $kode2 = $this->input->get('Tanggal');
        $res = $this->crud->delete(['KodePegawai' => $kode, 'Tanggal' => $kode2], 'absensipegawai');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Adjustment Absensi',
                'Description' => 'hapus data absensi pegawai ' . $kode . ' ' . date('d-m-Y', strtotime($kode2)),
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
