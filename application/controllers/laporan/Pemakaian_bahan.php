<?php
defined('BASEPATH') or exit('No direct script access allowed');

class pemakaian_bahan extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'mstbarang b';
        checkAccess($this->session->userdata('fiturview')[42]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[42]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'lappemakaian';
            $data['title'] = 'Laporan Pemakaian Bahan Baku, Penolong & Pembantu';
            $data['view'] = 'laporan/v_pemakaian_bahan';
            $data['scripts'] = 'laporan/s_pemakaian_bahan';

            $dtjenis = [
                'select' => '*',
                'from' => 'mstjenisbarang',
                'where' => [
                    [
                        'IsAktif !=' => null,
                    ],
                    "NamaJenisBarang NOT LIKE '%BARANG JADI%'"
                ],
                'order_by' => 'KodeJenis'
            ];
            $data['dtjenis'] = $this->crud->get_rows($dtjenis);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstbarang b';

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " b.IsAktif = $status ";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (b.NamaBarang LIKE '%$cari%')";
            }

            $jenis   = $this->input->get('jenis');
            if ($jenis != '') {
                $configData['where'] = [['b.KodeJenis' => $jenis]];
            } else {
                $configData['where'] = ["j.NamaJenisBarang NOT LIKE '%BARANG JADI%'"];
            }

            $configData['join'] = [
                [
                    'table' => ' mstjenisbarang j',
                    'on' => "j.KodeJenis = b.KodeJenis",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstkategori k',
                    'on' => "k.KodeKategori = b.KodeKategori",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.KodeBarang', 'b.NamaBarang', 'b.DeskripsiBarang', 'b.HargaBeliTerakhir', 'b.HargaJual', 'b.NilaiHPP', 'b.IsAktif', 'b.Foto1', 'b.Foto2', 'b.TglInput', 'b.SatuanBarang', 'b.Spesifikasi', 'b.KodeJenis', 'j.NamaJenisBarang', 'b.KodeKategori', 'k.NamaKategori'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'b.KodeBarang';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'b.KodeBarang', 'b.NamaBarang', 'b.DeskripsiBarang', 'b.HargaBeliTerakhir', 'b.HargaJual', 'b.NilaiHPP', 'b.IsAktif', 'b.Foto1', 'b.Foto2', 'b.TglInput', 'b.SatuanBarang', 'b.Spesifikasi', 'b.KodeJenis', 'j.NamaJenisBarang', 'b.KodeKategori', 'k.NamaKategori',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 42; //FiturID di tabel serverfitur
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
                $hargabeli = $record->HargaBeliTerakhir > 0 ? $record->HargaBeliTerakhir : 0;
                $hargajual = $record->HargaJual > 0 ? $record->HargaJual : 0;
                $hpp = $record->NilaiHPP > 0 ? $record->NilaiHPP : 0;
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['IsAktif'] = $temp['IsAktif'] == "1" ? 'Aktif' : 'NonAktif';
                $temp['hargabeli'] = $hargabeli;
                $temp['hargajual'] = $hargajual;
                $temp['hpp'] = $hpp;
                $temp['btn_aksi'] = '<a class="btnfitur" type="button" href="' . base_url('laporan/pemakaian_bahan/detail/' . base64_encode($temp['KodeBarang'])) . '" title="Detail"><i class="fa fa-list"></i></a>';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[42]);
        $kodeBarang   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA BARANG
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'lappemakaian';
            $data['title'] = 'Detail Laporan Pemakaian Bahan Baku, Penolong & Pembantu';
            $data['view']   = 'laporan/v_pemakaian_bahan_detail';
            $data['scripts'] = 'laporan/s_pemakaian_bahan_detail';

            $dtinduk = [
                'select' => 'b.KodeBarang, b.NamaBarang, b.DeskripsiBarang, b.HargaBeliTerakhir, b.HargaJual, b.NilaiHPP, b.IsAktif, b.Foto1, b.Foto2, b.TglInput, b.SatuanBarang, b.Spesifikasi, b.KodeJenis, j.NamaJenisBarang, b.KodeKategori, k.NamaKategori',
                'from' => $this->crud->table,
                'join' => [
                    [
                        'table' => 'mstjenisbarang j',
                        'on' => "j.KodeJenis = b.KodeJenis",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' mstkategori k',
                        'on' => "k.KodeKategori = b.KodeKategori",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['b.KodeBarang' => $kodeBarang]]
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['KodeBarang'] = $kodeBarang;

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
            $kodebarang   = $this->input->get('kodebarang');
            $configData['table'] = 'itemtransaksibarang i';

            $configData['where'] = [
                [
                    'b.JenisTransaksi' => 'PRODUKSI',
                    'b.IsHapus' => 0,
                    'i.KodeBarang' => $kodebarang,
                    'i.JenisStok' => 'KELUAR',
                    'i.IsHapus' => 0,
                ]
            ];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (b.NoRefTrSistem LIKE '%$cari%' OR b.KodeProduksi LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(b.TanggalTransaksi) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksibarang b',
                    'on' => "b.NoTrans = i.NoTrans",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.NoRefTrSistem', 'b.TanggalTransaksi', 'b.NoTrans', 'i.NoUrut', 'b.KodeProduksi', 'i.KodeBarang', 'br.NamaBarang', 'i.Qty'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'b.NoTrans, i.NoUrut';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'b.NoRefTrSistem', 'b.TanggalTransaksi', 'b.NoTrans', 'i.NoUrut', 'b.KodeProduksi', 'i.KodeBarang', 'br.NamaBarang', 'i.Qty',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 42; //FiturID di tabel serverfitur
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

            $saldo = 0;
            foreach ($records as $record) {
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['TanggalTransaksi'] = shortdate_indo(date('Y-m-d', strtotime($temp['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($temp['TanggalTransaksi']));
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetak()
    {
        $kodeBarang   = escape(base64_decode($this->uri->segment(4)));
        $tgltransaksi = escape(base64_decode($this->uri->segment(5)));
        $tgl = explode(" - ", $tgltransaksi);
        $d1 = date('Y-m-d', strtotime('-29 days'));
        $d2 = date('Y-m-d');
        $tglawal = $tgl[0] != '' ? date('Y-m-d', strtotime($tgl[0])) : $d1;
        $tglakhir = isset($tgl[1]) ? date('Y-m-d', strtotime($tgl[1])) : $d2;

        $data['src_url'] = base_url('laporan/pemakaian_bahan/detail/') . $this->uri->segment(4) . '?tgl=' . $tglawal . '+-+' . $tglakhir;

        $data['dtinduk'] = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'mstbarang br',
                'join' => [
                    [
                        'table' => ' mstjenisbarang j',
                        'on' => "j.KodeJenis = br.KodeJenis",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['br.KodeBarang' => $kodeBarang]],
            ]
        );

        $sql = [
            'select' => 'b.NoRefTrSistem, b.TanggalTransaksi, b.NoTrans, i.NoUrut, b.KodeProduksi, i.KodeBarang, br.NamaBarang, i.Qty',
            'from' => 'itemtransaksibarang i',
            'join' => [
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksibarang b',
                    'on' => "b.NoTrans = i.NoTrans",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [
                [
                    'b.JenisTransaksi' => 'PRODUKSI',
                    'b.IsHapus' => 0,
                    'i.KodeBarang' => $kodeBarang,
                    'i.JenisStok' => 'KELUAR',
                    'i.IsHapus' => 0,
                ],
                " (DATE(b.TanggalTransaksi) BETWEEN '$tglawal' AND '$tglakhir')",
            ],
            'order_by' => 'b.NoTrans, i.NoUrut',
        ];
        $data['model'] = $this->crud->get_rows($sql);
        $data['tglawal'] = $tglawal;
        $data['tglakhir'] = $tglakhir;

        $this->load->library('Pdf');
        $this->load->view('laporan/cetak_laporan_pemakaian_bahan', $data);
    }
}
