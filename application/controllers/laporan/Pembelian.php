<?php
defined('BASEPATH') or exit('No direct script access allowed');

class pembelian extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transpembelian b';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[36]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[36]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'lappembelian';
            $data['title'] = 'Laporan Pembelian';
            $data['view'] = 'laporan/v_pembelian';
            $data['scripts'] = 'laporan/s_pembelian';

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
            $configData['table'] = 'transpembelian b';

            $configData['where'] = [
                [
                    'b.StatusProses' => 'DONE',
                    'b.IsVoid' => 0,
                    // 'k.JenisTransaksiKas' => 'DP PEMBELIAN',
                ]
            ];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (b.IDTransBeli LIKE '%$cari%' OR b.NoPO LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%' OR b.StatusBayar LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(b.TanggalPembelian) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = b.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.NoRef_Sistem = b.IDTransBeli",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = k.UserName",
                    'param' => 'LEFT',
                ],
            ];

            $configData['group_by'] = 'b.IDTransBeli';

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.IDTransBeli', 'b.DiskonBawah', 'b.TotalTagihan', 'b.StatusProses', 'b.StatusKirim', 'b.StatusBayar', 'b.NoRef_Manual', 'b.TanggalPembelian', 'b.UraianPembelian', 'b.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'SUM(k.TotalTransaksi) as TotalBayar', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'b.TanggalPembelian';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'b.IDTransBeli', 'b.DiskonBawah', 'b.TotalTagihan', 'b.StatusProses', 'b.StatusKirim', 'b.StatusBayar', 'b.NoRef_Manual', 'b.TanggalPembelian', 'b.UraianPembelian', 'b.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'SUM(k.TotalTransaksi) as TotalBayar', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 36; //FiturID di tabel serverfitur
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
                $TotalTagihan = $record->TotalTagihan > 0 ? $record->TotalTagihan : 0;
                $TotalBayar = $record->TotalBayar > 0 ? $record->TotalBayar : 0;
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['TotalTagihan'] = $TotalTagihan;
                $temp['TotalBayar'] = $TotalBayar;
                $temp['TanggalPembelian'] = isset($temp['TanggalPembelian']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPembelian']))) . ' ' . date('H:i', strtotime($temp['TanggalPembelian'])) : '';
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function get_total()
    {
        $cari = $this->input->get('cari');
        $tanggal = explode(" - ", $this->input->get('tgl'));
        $tglawal = date('Y-m-d', strtotime($tanggal[0]));
        $tglakhir = date('Y-m-d', strtotime($tanggal[1]));

        $where1 = "WHERE b.StatusProses = 'DONE' AND b.IsVoid = 0";
        $where2 = ($cari != '' && $cari != null) ? " AND (b.IDTransBeli LIKE '%$cari%' OR b.NoPO LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%' OR b.StatusBayar LIKE '%$cari%')" : " ";
        $where3 = " AND DATE(b.TanggalPembelian) BETWEEN '$tglawal' AND '$tglakhir'";
        $where = $where1 . $where2 . $where3;

        $sql = "SELECT b.IDTransBeli, b.DiskonBawah, b.TotalTagihan, b.StatusProses, b.StatusKirim, b.StatusBayar, b.NoRef_Manual, b.TanggalPembelian, b.UraianPembelian, b.KodePerson, p.NamaPersonCP, k.NoRef_Sistem, k.NoTransKas, k.TanggalTransaksi, k.NominalBelumPajak, k.PPN, k.PPh, SUM(k.TotalTransaksi) as TotalBayar
            FROM transpembelian b
            LEFT JOIN mstperson p ON b.KodePerson = p.KodePerson
            LEFT JOIN transaksikas k ON b.IDTransBeli = k.NoRef_Sistem
            $where
            GROUP BY b.IDTransBeli";
        $datas = $this->db->query($sql)->result_array();

        $totaltagihan = 0;
        $totaldibayar = 0;
        foreach ($datas as $val) {
            $totaltagihan += $val['TotalTagihan'];
            $totaldibayar += $val['TotalBayar'];
        }

        echo json_encode([
            'status' => true,
            'totaltagihan' => $totaltagihan,
            'totaldibayar' => $totaldibayar
        ]);
    }

    public function cetak()
    {
        $tgltransaksi = escape(base64_decode($this->uri->segment(4)));
        $tgl = explode(" - ", $tgltransaksi);
        $d1 = date('Y-m-d', strtotime('-29 days'));
        $d2 = date('Y-m-d');
        $tglawal = $tgl[0] != '' ? date('Y-m-d', strtotime($tgl[0])) : $d1;
        $tglakhir = isset($tgl[1]) ? date('Y-m-d', strtotime($tgl[1])) : $d2;
        $data['src_url'] = base_url('laporan/pembelian?tgl=') . $tglawal . '+-+' . $tglakhir;
        $sql = [
            'select' => 'b.IDTransBeli, b.DiskonBawah, b.TotalTagihan, b.StatusProses, b.StatusKirim, b.StatusBayar, b.NoRef_Manual, b.TanggalPembelian, b.UraianPembelian, b.KodePerson, p.NamaPersonCP, k.NoRef_Sistem, k.NoTransKas, k.TanggalTransaksi, k.NominalBelumPajak, k.PPN, k.PPh, SUM(k.TotalTransaksi) as TotalBayar, k.IsDijurnalkan, k.NoTransJurnal, k.TipeJurnal, k.NarasiJurnal, k.Diskon, k.KodeTahun, k.UserName, u.ActualName',
            'from' => 'transpembelian b',
            'where' => [
                ['b.StatusProses' => 'DONE'],
                " (DATE(b.TanggalPembelian) BETWEEN '$tglawal' AND '$tglakhir')",
            ],
            'join' => [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = b.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.NoRef_Sistem = b.IDTransBeli",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = k.UserName",
                    'param' => 'LEFT',
                ],
            ],
            'group_by' => 'b.IDTransBeli',
        ];
        $data['model'] = $this->crud->get_rows($sql);
        $data['tglawal'] = $tglawal;
        $data['tglakhir'] = $tglakhir;

        $this->load->library('Pdf');
        $this->load->view('laporan/cetak_laporan_pembelian', $data);
    }
}
