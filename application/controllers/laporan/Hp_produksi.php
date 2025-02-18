<?php
defined('BASEPATH') or exit('No direct script access allowed');

class hp_produksi extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'itemtransaksibarang i';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[40]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[40]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'laphpp';
            $data['title'] = 'Laporan Harga Pokok Produksi';
            $data['view'] = 'laporan/v_hpproduksi';
            $data['scripts'] = 'laporan/s_hpproduksi';

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
            $configData['table'] = 'transaksibarang b';

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (b.NoRefTrSistem LIKE '%$cari%' OR j.SPKNomor LIKE '%$cari%' OR b.KodeProduksi LIKE '%$cari%' OR br.NamaBarang LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(j.SPKTanggal) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['where'] = [
                [
                    'b.JenisTransaksi' => 'PRODUKSI',
                    'j.StatusProduksi' => 'SELESAI',
                ]
            ];

            $configData['join'] = [
                [
                    'table' => ' transpenjualan j',
                    'on' => "j.IDTransJual = b.NoRefTrSistem",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = b.KodeBarang",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.IDTransJual', 'j.SPKNomor', 'j.SPKTanggal', 'b.NoTrans', 'b.NoRefTrSistem', 'b.JmlProduksi', 'b.KodeProduksi', 'br.NamaBarang', 'br.HargaJual', 'b.HPPProduksi'
            ];

            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'j.SPKTanggal, b.NoTrans';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'j.IDTransJual', 'j.SPKNomor', 'j.SPKTanggal', 'b.NoTrans', 'b.NoRefTrSistem', 'b.JmlProduksi', 'b.KodeProduksi', 'br.NamaBarang', 'br.HargaJual', 'b.HPPProduksi',
                false
            ];

            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            $dtkotor = $this->crud->get_one_row([
                'select' => 'SUM(i.Qty) as BeratKotor, SUM(i.Total) as BiayaKotor',
                'from' => 'itemtransaksibarang i',
                'join' => [[
                    'table' => ' transaksibarang b',
                    'on' => 'b.NoTrans = i.NoTrans',
                    'param' => 'INNER',
                ]],
                'where' => [
                    [
                        'b.JenisTransaksi' => 'PRODUKSI',
                        'i.JenisStok' => 'KELUAR',
                        'i.IsHapus' => 0,
                    ],
                    "(DATE(b.TanggalTransaksi) BETWEEN '$tglawal' AND '$tglakhir')",
                ],
            ]);
            $data['beratkotor'] = isset($dtkotor['BeratKotor']) ? $dtkotor['BeratKotor'] : 0;
            $data['biayakotor'] = isset($dtkotor['BiayaKotor']) ? (int)$dtkotor['BiayaKotor'] : 0;

            $dtbersih = $this->crud->get_one_row([
                'select' => 'SUM(b.BeratBersih) as BeratBersih, SUM(b.HPPProduksi) as BiayaBersih',
                'from' => 'transaksibarang b',
                'where' => [
                    [
                        'b.JenisTransaksi' => 'PRODUKSI',
                        'b.IsHapus' => 0,
                    ],
                    "(DATE(b.TanggalTransaksi) BETWEEN '$tglawal' AND '$tglakhir')",
                ],
            ]);
            $data['beratbersih'] = isset($dtbersih['BeratBersih']) ? $dtbersih['BeratBersih'] : 0;
            $data['biayabersih'] = isset($dtbersih['BiayaBersih']) ? (int)$dtbersih['BiayaBersih'] : 0;

            ## Set fitur level untuk edit dan hapus
            $FiturID = 40; //FiturID di tabel serverfitur
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
                $temp['SPKTanggal'] = shortdate_indo(date('Y-m-d', strtotime($temp['SPKTanggal']))) . ' ' . date('H:i', strtotime($temp['SPKTanggal']));
                $temp['HPPTotal'] = $temp['HPPProduksi'] * $temp['JmlProduksi'];
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
        $data['src_url'] = base_url('laporan/hp_produksi?tgl=') . $tglawal . '+-+' . $tglakhir;

        $sql = [
            'select' => 'j.IDTransJual, j.SPKNomor, j.SPKTanggal, b.NoTrans, b.NoRefTrSistem, b.JmlProduksi, b.KodeProduksi, br.NamaBarang, br.HargaJual, b.HPPProduksi',
            'from' => 'transaksibarang b',
            'join' => [
                [
                    'table' => ' transpenjualan j',
                    'on' => "j.IDTransJual = b.NoRefTrSistem",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = b.KodeBarang",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [
                [
                    'b.JenisTransaksi' => 'PRODUKSI',
                    'j.StatusProduksi' => 'SELESAI',
                ],
                " (DATE(j.SPKTanggal) BETWEEN '$tglawal' AND '$tglakhir')",
            ],
            'order_by' => 'j.SPKTanggal, b.NoTrans',
        ];
        $data['model'] = $this->crud->get_rows($sql);
        $data['tglawal'] = $tglawal;
        $data['tglakhir'] = $tglakhir;

        $this->load->library('Pdf');
        $this->load->view('laporan/cetak_laporan_hpproduksi', $data);
    }
}
