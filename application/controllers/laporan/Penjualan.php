<?php
defined('BASEPATH') or exit('No direct script access allowed');

class penjualan extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transpenjualan j';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[35]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[35]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'lappenjualan';
            $data['title'] = 'Laporan Penjualan';
            $data['view'] = 'laporan/v_penjualan';
            $data['scripts'] = 'laporan/s_penjualan';

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
            $configData['table'] = 'transpenjualan j';

            $configData['where'] = [
                [
                    'j.StatusProses' => 'DONE',
                    'LEFT(j.IDTransJual, 3) =' => 'TJL',
                ]
            ];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (j.IDTransJual LIKE '%$cari%' OR j.NoRef_Manual LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%' OR j.StatusBayar LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(j.TanggalPenjualan) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = j.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = j.KodeGudang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.NoRef_Sistem = j.IDTransJual",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = k.UserName",
                    'param' => 'LEFT',
                ],
            ];

            $configData['group_by'] = 'j.IDTransJual';

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.IDTransJual', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.NoRef_Manual', 'j.TanggalPenjualan', 'j.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'SUM(k.TotalTransaksi) as TotalBayar', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName', 'j.KodeGudang', 'g.NamaGudang'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'j.TanggalPenjualan';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'j.IDTransJual', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.NoRef_Manual', 'j.TanggalPenjualan', 'j.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'SUM(k.TotalTransaksi) as TotalBayar', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName', 'j.KodeGudang', 'g.NamaGudang',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 35; //FiturID di tabel serverfitur
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
                $temp['TanggalPenjualan'] = isset($temp['TanggalPenjualan']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPenjualan']))) . ' ' . date('H:i', strtotime($temp['TanggalPenjualan'])) : '';
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

        $where1 = "WHERE j.StatusProses = 'DONE' AND LEFT(j.IDTransJual, 3) = 'TJL'";
        $where2 = ($cari != '' && $cari != null) ? " AND (j.IDTransJual LIKE '%$cari%' OR j.NoRef_Manual LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%' OR j.StatusBayar LIKE '%$cari%')" : " ";
        $where3 = " AND DATE(j.TanggalPenjualan) BETWEEN '$tglawal' AND '$tglakhir'";
        $where = $where1 . $where2 . $where3;

        $sql = "SELECT j.IDTransJual, j.TotalTagihan, j.StatusProses, j.StatusKirim, j.StatusBayar, j.NoRef_Manual, j.TanggalPenjualan, j.KodePerson, p.NamaPersonCP, SUM(k.TotalTransaksi) as TotalBayar
            FROM transpenjualan j
            LEFT JOIN mstperson p ON j.KodePerson = p.KodePerson
            LEFT JOIN transaksikas k ON j.IDTransJual = k.NoRef_Sistem
            $where
            GROUP BY j.IDTransJual";
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
        $data['src_url'] = base_url('laporan/penjualan?tgl=') . $tglawal . '+-+' . $tglakhir;
        $sql = [
            'select' => 'j.IDTransJual, j.TotalTagihan, j.StatusProses, j.StatusKirim, j.StatusBayar, j.NoRef_Manual, j.TanggalPenjualan, j.KodePerson, p.NamaPersonCP, k.NoRef_Sistem, k.NoTransKas, k.TanggalTransaksi, k.NominalBelumPajak, k.PPN, k.PPh, SUM(k.TotalTransaksi) as TotalBayar, k.IsDijurnalkan, k.NoTransJurnal, k.TipeJurnal, k.NarasiJurnal, k.Diskon, k.KodeTahun, k.UserName, u.ActualName, j.KodeGudang, g.NamaGudang',
            'from' => 'transpenjualan j',
            'where' => [
                [
                    'j.StatusProses' => 'DONE',
                    'LEFT(j.IDTransJual, 3) =' => 'TJL',
                ],
                " (DATE(j.TanggalPenjualan) BETWEEN '$tglawal' AND '$tglakhir')",
            ],
            'join' => [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = j.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = j.KodeGudang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.NoRef_Sistem = j.IDTransJual",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = k.UserName",
                    'param' => 'LEFT',
                ],
            ],
            'group_by' => 'j.IDTransJual',
        ];
        $data['model'] = $this->crud->get_rows($sql);
        $data['tglawal'] = $tglawal;
        $data['tglakhir'] = $tglakhir;

        $this->load->library('Pdf');
        $this->load->view('laporan/cetak_laporan_penjualan', $data);
    }
}
