<?php
defined('BASEPATH') or exit('No direct script access allowed');

class laporan_aktivitas extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'aktivitasproduksi a';
        checkAccess($this->session->userdata('fiturview')[56]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[56]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'lapaktivitas';
            $data['title'] = 'Laporan Aktivitas Pegawai';
            $data['view'] = 'payroll/v_laporan_aktivitas';
            $data['scripts'] = 'payroll/s_laporan_aktivitas';
            $tanggalan = $this->input->get('tgl');
            $tgl = explode(" - ", $tanggalan);
            $d1 = date('Y-m-d', strtotime('-29 days'));
            $d2 = date('Y-m-d');
            $tglawal = $tgl[0] != '' ? date('Y-m-d', strtotime($tgl[0])) : $d1;
            $tglakhir = isset($tgl[1]) ? date('Y-m-d', strtotime($tgl[1])) : $d2;
            $data['tglawal'] = date('d-m-Y', strtotime($tglawal));
            $data['tglakhir'] = date('d-m-Y', strtotime($tglakhir));
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'aktivitasproduksi a';

            $status   = $this->input->get('isaktif');
            $cari     = $this->input->get('cari');

            if ($cari != '') {
                $configData['filters'][] = " (a.NoTrAktivitas LIKE '%$cari%' OR p.NamaPegawai LIKE '%$cari%' OR a.JenisAktivitas LIKE '%$cari%')";
            }
            if ($status != '') {
                $configData['filters'][] = " k.IsAktif = $status ";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(a.TglAktivitas) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = a.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstaktivitas ma',
                    'on' => "ma.KodeAktivitas = a.KodeAktivitas",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'a.NoTrAktivitas', 'p.NamaPegawai', 'a.TglAktivitas', 'a.JenisAktivitas', 'a.KodeAktivitas', 'ma.BatasBawah', 'ma.BatasAtas', 'ma.JmlDaun', 'a.Biaya', 'a.JmlAmpasDapur', 'SUM(if(a.JenisAktivitas = "Ampas Dapur", a.Biaya * a.JmlAmpasDapur, 0)) AS TotalBiaya'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'a.NoTrAktivitas';
            $configData['custom_column_sort_order'] = 'ASC';
            $configData['group_by'] = 'a.NoTrAktivitas';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'a.NoTrAktivitas', 'p.NamaPegawai', 'a.TglAktivitas', 'a.JenisAktivitas', 'a.KodeAktivitas', 'ma.BatasBawah', 'ma.BatasAtas', 'ma.JmlDaun', 'a.Biaya', 'a.JmlAmpasDapur', 'SUM(if(a.JenisAktivitas = "Ampas Dapur", a.Biaya * a.JmlAmpasDapur, 0)) AS TotalBiaya',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 56; //FiturID di tabel serverfitur
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
                $temp['TglAktivitas'] = isset($temp['TglAktivitas']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TglAktivitas']))) : '';
                $temp['Nominal'] = isset($temp['JmlAmpasDapur']) ? $temp['TotalBiaya'] : $temp['Biaya'];
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetak()
    {
        $tgltransaksi = escape(base64_decode($this->uri->segment(4)));
        $tgl = explode(" - ", $tgltransaksi);
        $d1 = date('Y-m-d', strtotime('-29 days'));
        $d2 = date('Y-m-d');
        $tglawal = $tgl[0] != '' ? date('Y-m-d', strtotime($tgl[0])) : $d1;
        $tglakhir = isset($tgl[1]) ? date('Y-m-d', strtotime($tgl[1])) : $d2;
        $data['src_url'] = base_url('payroll/laporan_aktivitas?tgl=') . $tglawal . '+-+' . $tglakhir;
        $sql = [
            'select' => 'a.NoTrAktivitas, p.NamaPegawai, a.TglAktivitas, a.JenisAktivitas, a.KodeAktivitas, ma.BatasBawah, ma.BatasAtas, ma.JmlDaun, a.Biaya, a.JmlAmpasDapur',
            'from' => 'aktivitasproduksi a',
            'where' => [
                " (DATE(a.TglAktivitas) BETWEEN '$tglawal' AND '$tglakhir')",
            ],
            'join' => [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = a.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstaktivitas ma',
                    'on' => "ma.KodeAktivitas = a.KodeAktivitas",
                    'param' => 'LEFT',
                ],
            ],
            'group_by' => 'a.NoTrAktivitas',
        ];
        $data['model'] = $this->crud->get_rows($sql);
        $data['tglawal'] = $tglawal;
        $data['tglakhir'] = $tglakhir;

        $this->load->library('Pdf');
        $this->load->view('payroll/cetak_laporan_aktivitas_pegawai', $data);
    }
}
