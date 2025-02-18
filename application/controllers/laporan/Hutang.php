<?php
defined('BASEPATH') or exit('No direct script access allowed');

class hutang extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[43]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[43]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'laphutang';
            $data['title'] = 'Laporan Hutang';
            $data['view'] = 'laporan/v_hutang';
            $data['scripts'] = 'laporan/s_hutang';

            $tanggalan = $this->input->get('tgl');
            $tgl = explode(" - ", $tanggalan);
            $d1 = date('Y-m-d', strtotime('-29 days'));
            $d2 = date('Y-m-d');
            $tglawal = $tgl[0] != '' ? date('Y-m-d', strtotime($tgl[0])) : $d1;
            $tglakhir = isset($tgl[1]) ? date('Y-m-d', strtotime($tgl[1])) : $d2;
            $data['tglawal'] = date('d-m-Y', strtotime($tglawal));
            $data['tglakhir'] = date('d-m-Y', strtotime($tglakhir));

            $supplier = [
                'select' => '*',
                'from' => 'mstperson p',
                'where' => [
                    [
                        'p.JenisPerson' => 'SUPPLIER',
                        // 'p.IsAktif' => 1,
                    ]
                ],
                'order_by' => 'p.NamaPersonCP'
            ];
            $data['supplier'] = $this->crud->get_rows($supplier);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transpembelian b';

            $configData['where'] = [
                [
                    'b.StatusProses' => 'DONE',
                    'b.IsVoid' => 0
                ]
            ];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (b.IDTransBeli LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%')";
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
            $FiturID = 43; //FiturID di tabel serverfitur
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
                $TotalBayar = $this->lokasi->count_total_bayar($record->IDTransBeli);
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['TotalTagihan'] = $TotalTagihan;
                $temp['TotalBayar'] = $TotalBayar;
                $temp['SisaTagihan'] = $TotalTagihan - $TotalBayar;
                $temp['TanggalPembelian'] = isset($temp['TanggalPembelian']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPembelian']))) . ' ' . date('H:i', strtotime($temp['TanggalPembelian'])) : '';
                $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('laporan/hutang/detail/' . base64_encode($temp['IDTransBeli'])) . '" type="button" title="Detail Pembayaran Hutang"><span class="fa fa-list" aria-hidden="true"></span></a>';
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
        $where2 = ($cari != '' && $cari != null) ? " AND (b.IDTransBeli LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%')" : " ";
        $where3 = " AND DATE(b.TanggalPembelian) BETWEEN '$tglawal' AND '$tglakhir'";
        $where = $where1 . $where2 . $where3;

        $sql = "SELECT b.IDTransBeli, b.TotalTagihan, b.KodePerson
            FROM transpembelian b
            LEFT JOIN mstperson p ON b.KodePerson = p.KodePerson
            $where
            GROUP BY b.IDTransBeli";
        $datas = $this->db->query($sql)->result_array();

        $totaltagihan = 0;
        $totaldibayar = 0;
        foreach ($datas as $val) {
            $totaltagihan += $val['TotalTagihan'];
            $totaldibayar += $this->lokasi->count_total_bayar($val['IDTransBeli']);
        }

        echo json_encode([
            'status' => true,
            'totaltagihan' => $totaltagihan,
            'totaldibayar' => $totaldibayar,
            'totalsisa' => $totaltagihan - $totaldibayar
        ]);
    }

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[43]);
        $idtransbeli   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'laphutang';
            $data['title'] = 'Detail Pembayaran Hutang';
            $data['view'] = 'laporan/v_hutang_detail';
            $data['scripts'] = 'laporan/s_hutang_detail';

            $dtinduk = $this->crud->get_one_row([
                'select' => 't.IDTransBeli, t.PPN, t.UserPO, t.NoPO, t.DiskonBawah, t.NominalBelumPajak, t.TotalTagihan, t.StatusProses, t.StatusBayar, t.StatusKirim, t.NoRef_Manual, t.TanggalPembelian, t.UraianPembelian, p.KodePerson, p.NamaPersonCP, p.NamaUsaha, k.NoTransKas, k.TotalTransaksi, r.IDTransRetur, tb.NoTrans, tb.GudangTujuan',
                'from' => 'transpembelian t',
                'join' => [
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = t.KodePerson",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' transaksikas k',
                        'on' => "k.NoRef_Sistem = t.IDTransBeli AND k.JenisTransaksiKas = 'DP PEMBELIAN'",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' transaksiretur r',
                        'on' => "r.IDTrans = t.IDTransBeli",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' transaksibarang tb',
                        'on' => "tb.NoRefTrSistem = t.IDTransBeli AND tb.JenisTransaksi = 'BARANG DATANG'",
                        'param' => 'LEFT',
                    ]
                ],
                'where' => [['t.IDTransBeli' => $idtransbeli]],
            ]);
            $data['dtinduk'] = $dtinduk;
            $data['idtransbeli'] = $idtransbeli;
            $data['totalbayar'] = $this->lokasi->count_total_bayar($idtransbeli);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $idtransbeli   = $this->input->get('idtransbeli');
            $configData['table'] = 'transaksikas k';
            $configData['where'] = [['k.NoRef_Sistem'  => $idtransbeli]];

            $configData['join'] = [
                [
                    'table' => ' transpembelian b',
                    'on' => "k.NoRef_Sistem = b.IDTransBeli",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = k.UserName",
                    'param' => 'LEFT',
                ]
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'k.NoTransKas', 'k.KodeTahun', 'k.TanggalTransaksi', 'k.NoRef_Sistem', 'k.JenisTransaksiKas', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'k.TotalTransaksi', 'k.Diskon', 'k.UserName', 'u.ActualName'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'k.NoTransKas', 'k.KodeTahun', 'k.TanggalTransaksi', 'k.NoRef_Sistem', 'k.JenisTransaksiKas', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'k.TotalTransaksi', 'k.Diskon', 'k.UserName', 'u.ActualName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            foreach ($records as $record) {
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['TanggalTransaksi'] = isset($temp['TanggalTransaksi']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalTransaksi']))) : '';
                $temp['Diskon'] = isset($temp['Diskon']) ? $temp['Diskon'] : 0;
                if ($temp['JenisTransaksiKas'] == 'BAYAR HUTANG') {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['NoTransKas'] . ' data-kode2=' . $temp['NoRef_Sistem'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function hapusdetail()
    {
        $notrkas = $this->input->get('NoTransKas');
        $idtransbeli = $this->input->get('IDTransBeli');
        $getJurnal = $this->crud->get_one_row([
            'from' => 'transjurnal',
            'where' => [['NoRefTrans' => $notrkas]]
        ]);
        if ($getJurnal) {
            $deletejurnalitem   = $this->crud->delete(['IDTransJurnal' => $getJurnal['IDTransJurnal']], 'transjurnalitem');
            $deletejurnalinduk  = $this->crud->delete(['IDTransJurnal' => $getJurnal['IDTransJurnal']], 'transjurnal');
        }

        $res = $this->crud->delete(['NoTransKas' => $notrkas], 'transaksikas');
        if ($res) {
            $statusbayar = ($this->lokasi->count_total_bayar($idtransbeli) > 0) ? 'SEBAGIAN' : 'BELUM';
            $updatepembelian = $this->crud->update(['StatusBayar' => $statusbayar], ['IDTransBeli' => $idtransbeli], 'transpembelian');
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

    public function cetak()
    {
        $tgltransaksi = escape(base64_decode($this->uri->segment(4)));
        $tgl = explode(" - ", $tgltransaksi);
        $d1 = date('Y-m-d', strtotime('-29 days'));
        $d2 = date('Y-m-d');
        $tglawal = $tgl[0] != '' ? date('Y-m-d', strtotime($tgl[0])) : $d1;
        $tglakhir = isset($tgl[1]) ? date('Y-m-d', strtotime($tgl[1])) : $d2;
        $data['src_url'] = base_url('laporan/hutang?tgl=') . $tglawal . '+-+' . $tglakhir;

        $sql = [
            'select' => 'b.IDTransBeli, b.DiskonBawah, b.TotalTagihan, b.StatusProses, b.StatusKirim, b.StatusBayar, b.NoRef_Manual, b.TanggalPembelian, b.UraianPembelian, b.KodePerson, p.NamaPersonCP, k.NoRef_Sistem, k.NoTransKas, k.TanggalTransaksi, k.NominalBelumPajak, k.PPN, k.PPh, SUM(k.TotalTransaksi) as TotalBayar, k.IsDijurnalkan, k.NoTransJurnal, k.TipeJurnal, k.NarasiJurnal, k.Diskon, k.KodeTahun, k.UserName, u.ActualName',
            'from' => 'transpembelian b',
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
            'where' => [
                [
                    'b.StatusProses' => 'DONE',
                ],
                " (DATE(b.TanggalPembelian) BETWEEN '$tglawal' AND '$tglakhir')",
            ],
            'group_by' => 'b.IDTransBeli',
        ];
        $data['model'] = $this->crud->get_rows($sql);
        $data['tglawal'] = $tglawal;
        $data['tglakhir'] = $tglakhir;

        $this->load->library('Pdf');
        $this->load->view('laporan/cetak_laporan_hutang', $data);
    }
}
