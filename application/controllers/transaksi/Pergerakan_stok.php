<?php
defined('BASEPATH') or exit('No direct script access allowed');

class pergerakan_stok extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[22]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[22]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'pergerakanstok';
            $data['title'] = 'Pergerakan Stok';
            $data['view'] = 'transaksi/v_pergerakan_stok';
            $data['scripts'] = 'transaksi/s_pergerakan_stok';

            $data['dtgudang'] = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstgudang',
                    'where' => [['KodeGudang !=' => null]],
                ]
            );

            $dtjenis = [
                'select' => '*',
                'from' => 'mstjenisbarang',
                'where' => [['IsAktif !=' => null]],
                'order_by' => 'KodeJenis'
            ];
            $data['dtjenis'] = $this->crud->get_rows($dtjenis);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstbarang br';

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.KodeBarang LIKE '%$cari%' OR br.NamaBarang LIKE '%$cari%')";
            }

            $jenis   = $this->input->get('jenis');
            if ($jenis != '') {
                $configData['where'] = [['br.KodeJenis' => $jenis]];
            }

            $configData['join'] = [
                [
                    'table' => ' itemtransaksibarang i',
                    'on' => "i.KodeBarang = br.KodeBarang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang ga',
                    'on' => "ga.KodeGudang = i.GudangAsal",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang gt',
                    'on' => "gt.KodeGudang = i.GudangTujuan",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstjenisbarang j',
                    'on' => "j.KodeJenis = br.KodeJenis",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'br.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang', 'br.KodeJenis', 'j.NamaJenisBarang', 'i.GudangAsal', 'i.GudangTujuan', 'SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok'
            ];

            $configData['display_column'] = [
                false,
                'br.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang', 'br.KodeJenis', 'j.NamaJenisBarang', 'i.GudangAsal', 'i.GudangTujuan', 'SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok',
                false
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['group_by'] = 'br.KodeBarang';
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'br.KodeBarang';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];

            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 22; //FiturID di tabel serverfitur
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
                $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/pergerakan_stok/detail/' . base64_encode($temp['KodeBarang'])) . '" type="button" title="Detail"><span class="fa fa-list" aria-hidden="true"></span></a>';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[22]);
        $kodebarang = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA BARANG
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'pergerakanstok';
            $data['title'] = 'Detail Pergerakan Stok';
            $data['view'] = 'transaksi/v_pergerakan_stok_detail';
            $data['scripts'] = 'transaksi/s_pergerakan_stok_detail';

            $dtinduk = [
                'select' => '*',
                'from' => 'mstbarang br',
                'join' => [
                    [
                        'table' => ' mstjenisbarang j',
                        'on' => "j.KodeJenis = br.KodeJenis",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['br.KodeBarang' => $kodebarang]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['KodeBarang'] = $kodebarang;

            $dtgudang = [
                'select' => '*',
                'from' => 'mstgudang',
            ];
            $data['dtgudang'] = $this->crud->get_rows($dtgudang);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $kodebarang   = $this->input->get('kodebarang');
            $configData['table'] = 'itemtransaksibarang i';

            $configData['where'] = [
                [
                    'i.KodeBarang' => $kodebarang,
                    'i.IsHapus' => 0,
                    'b.IsHapus' => 0,
                ]
            ];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.NoTrans LIKE '%$cari%')";
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
                [
                    'table' => ' mstgudang ga',
                    'on' => "ga.KodeGudang = i.GudangAsal",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang gt',
                    'on' => "gt.KodeGudang = i.GudangTujuan",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $gudang     = $this->input->get('gudang');
            if ($gudang != '') {
                $configData['filters'][] = " (i.GudangAsal = '$gudang' OR i.GudangTujuan = '$gudang')";

                $configData['selected_column'] = [
                    'b.NoTrans', 'b.NoRefTrSistem', 'IFNULL(b.NoRefTrSistem, b.NoTrans) AS KodeTransaksi', 'b.TanggalTransaksi', 'i.KodeBarang', 'br.NamaBarang', 'b.JenisTransaksi', 'i.JenisStok', 'i.GudangAsal', 'ga.NamaGudang AS NamaGudangAsal', 'i.GudangTujuan', 'gt.NamaGudang AS NamaGudangTujuan', 'i.SatuanBarang', 'i.Qty', 'i.IsHapus', 'SUM(if(i.GudangTujuan = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) AS Masuk', 'SUM(if(i.GudangAsal = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) AS Keluar'
                ];
                $configData['display_column'] = [
                    false,
                    'b.NoTrans', 'b.NoRefTrSistem', 'IFNULL(b.NoRefTrSistem, b.NoTrans) AS KodeTransaksi', 'b.TanggalTransaksi', 'i.KodeBarang', 'br.NamaBarang', 'b.JenisTransaksi', 'i.JenisStok', 'i.GudangAsal', 'ga.NamaGudang AS NamaGudangAsal', 'i.GudangTujuan', 'gt.NamaGudang AS NamaGudangTujuan', 'i.SatuanBarang', 'i.Qty', 'i.IsHapus', 'SUM(if(i.GudangTujuan = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) AS Masuk', 'SUM(if(i.GudangAsal = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) AS Keluar',
                    false
                ];
            } else {
                $configData['selected_column'] = [
                    'b.NoTrans', 'b.NoRefTrSistem', 'IFNULL(b.NoRefTrSistem, b.NoTrans) AS KodeTransaksi', 'b.TanggalTransaksi', 'i.KodeBarang', 'br.NamaBarang', 'b.JenisTransaksi', 'i.JenisStok', 'i.GudangAsal', 'ga.NamaGudang AS NamaGudangAsal', 'i.GudangTujuan', 'gt.NamaGudang AS NamaGudangTujuan', 'i.SatuanBarang', 'i.Qty', 'i.IsHapus', 'SUM(if(i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) AS Masuk', 'SUM(if(i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) AS Keluar'
                ];
                $configData['display_column'] = [
                    false,
                    'b.NoTrans', 'b.NoRefTrSistem', 'IFNULL(b.NoRefTrSistem, b.NoTrans) AS KodeTransaksi', 'b.TanggalTransaksi', 'i.KodeBarang', 'br.NamaBarang', 'b.JenisTransaksi', 'i.JenisStok', 'i.GudangAsal', 'ga.NamaGudang AS NamaGudangAsal', 'i.GudangTujuan', 'gt.NamaGudang AS NamaGudangTujuan', 'i.SatuanBarang', 'i.Qty', 'i.IsHapus', 'SUM(if(i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) AS Masuk', 'SUM(if(i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) AS Keluar',
                    false
                ];
            }
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['group_by'] = 'IF(b.NoRefTrSistem IS NOT NULL, CONCAT (b.NoRefTrSistem, b.JenisTransaksi), b.NoTrans)';
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'b.NoTrans';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 21; //FiturID di tabel serverfitur
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
                if ($temp['JenisTransaksi'] == 'BARANG DATANG') {
                    $temp['Transaksi'] = 'Transaksi Pembelian ' . $temp['NoRefTrSistem'];
                } elseif ($temp['JenisTransaksi'] == 'BARANG KELUAR') {
                    $temp['Transaksi'] = 'Transaksi Penjualan ' . $temp['NoRefTrSistem'];
                } elseif ($temp['JenisTransaksi'] == 'MUTASI') {
                    $temp['Transaksi'] = 'Mutasi ' . $temp['NamaGudangAsal'] . ' ke ' .$temp['NamaGudangTujuan'];
                } elseif ($temp['JenisTransaksi'] == 'PRODUKSI') {
                    $temp['Transaksi'] = 'Produksi Barang ' . $temp['NoTrans'];
                } else {
                    $temp['Transaksi'] = 'Penyesuaian Stok ' . $temp['NoTrans'];
                }
                $saldo += $temp['Masuk'] - $temp['Keluar'];
                $temp['Saldo'] = $saldo;
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetak()
    {
        $kodebarang   = escape(base64_decode($this->uri->segment(4)));
        $kodegudang   = escape(base64_decode($this->uri->segment(5)));
        $data['src_url'] = base_url('transaksi/pergerakan_stok/detail/') . $this->uri->segment(4) . '/' . $this->uri->segment(5);

        $data['barang'] = $this->crud->get_one_row(
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
                'where' => [['br.KodeBarang' => $kodebarang]],
            ]
        );

        $data['gudang'] = $this->crud->get_one_row(
            [
                'select' => 'g.KodeGudang, g.NamaGudang',
                'from' => 'mstgudang g',
                'where' => [['g.KodeGudang' => $kodegudang]],
            ]
        );

        if ($kodegudang) {
            $sql = "SELECT b.NoTrans, b.NoRefTrSistem, IFNULL(b.NoRefTrSistem, b.NoTrans) AS KodeTransaksi, b.TanggalTransaksi, i.KodeBarang, br.NamaBarang, b.JenisTransaksi, i.JenisStok, i.GudangAsal, ga.NamaGudang AS NamaGudangAsal, i.GudangTujuan, gt.NamaGudang AS NamaGudangTujuan, i.SatuanBarang, i.Qty, SUM(if(i.GudangTujuan = '$kodegudang' AND (i.JenisStok = 'MASUK' OR i.JenisStok = 'MUTASI'), i.Qty, 0)) AS Masuk, SUM(if(i.GudangAsal = '$kodegudang' AND (i.JenisStok = 'KELUAR' OR i.JenisStok = 'MUTASI'), i.Qty, 0)) AS Keluar
                FROM itemtransaksibarang AS i
                LEFT JOIN transaksibarang AS b ON i.NoTrans = b.NoTrans
                LEFT JOIN mstbarang AS br ON i.KodeBarang = br.KodeBarang
                LEFT JOIN mstgudang AS ga ON i.GudangAsal = ga.KodeGudang
                LEFT JOIN mstgudang AS gt ON i.GudangTujuan = gt.KodeGudang
                WHERE i.KodeBarang = '$kodebarang'
                AND i.IsHapus = 0
                AND b.IsHapus = 0
                AND (i.GudangAsal = '$kodegudang' OR i.GudangTujuan = '$kodegudang')
                GROUP BY KodeTransaksi
                ORDER BY b.NoTrans";
        } else {
            $sql = "SELECT b.NoTrans, b.NoRefTrSistem, IFNULL(b.NoRefTrSistem, b.NoTrans) AS KodeTransaksi, b.TanggalTransaksi, i.KodeBarang, br.NamaBarang, b.JenisTransaksi, i.JenisStok, i.GudangAsal, ga.NamaGudang AS NamaGudangAsal, i.GudangTujuan, gt.NamaGudang AS NamaGudangTujuan, i.SatuanBarang, i.Qty, SUM(if(i.JenisStok = 'MASUK' OR i.JenisStok = 'MUTASI', i.Qty, 0)) AS Masuk, SUM(if(i.JenisStok = 'KELUAR' OR i.JenisStok = 'MUTASI', i.Qty, 0)) AS Keluar
                FROM itemtransaksibarang AS i
                LEFT JOIN transaksibarang AS b ON i.NoTrans = b.NoTrans
                LEFT JOIN mstbarang AS br ON i.KodeBarang = br.KodeBarang
                LEFT JOIN mstgudang AS ga ON i.GudangAsal = ga.KodeGudang
                LEFT JOIN mstgudang AS gt ON i.GudangTujuan = gt.KodeGudang
                WHERE i.KodeBarang = '$kodebarang'
                AND i.IsHapus = 0
                AND b.IsHapus = 0
                GROUP BY KodeTransaksi
                ORDER BY b.NoTrans";
        }
        $data['model'] = $this->db->query($sql)->result_array();;
        $data['kodegudang'] = $kodegudang;

        $this->load->library('Pdf');
        $this->load->view('transaksi/v_pergerakan_stok_detail_cetak', $data);
    }
}
