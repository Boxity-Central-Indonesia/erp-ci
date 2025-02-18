<?php
defined('BASEPATH') or exit('No direct script access allowed');

class penyesuaian_produksi extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'aktivitasproduksi a';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[30]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[30]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'penyesuaianprod';
            $data['title'] = 'Penyesuaian Produksi';
            $data['view'] = 'transaksi/v_penyesuaian_produksi';
            $data['scripts'] = 'transaksi/s_penyesuaian_produksi';

            $data['gudang'] = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstgudang',
                    'where' => [['KodeGudang !=' => null]],
                ]
            );

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transaksibarang b';

            $configData['where'] = [
                [
                    'b.TanggalTransaksi !=' => null,
                    'b.JenisTransaksi' => 'PROSES PRODUKSI',
                    'b.IsHapus' => 0,
                ]
            ];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (b.NoTrans LIKE '%$cari%' OR b.Deskripsi LIKE '%$cari%')";
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
                    'table' => ' userlogin u',
                    'on' => "u.UserName = b.UserName",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang ga',
                    'on' => "ga.KodeGudang = b.GudangAsal",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang gt',
                    'on' => "gt.KodeGudang = b.GudangTujuan",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.UserName', 'u.ActualName', 'b.GudangAsal', 'ga.NamaGudang as NamaGudangAsal', 'gt.NamaGudang as NamaGudangTujuan'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            // $configData['group_by'] = 'b.NoTrans';
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'b.NoTrans';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.UserName', 'u.ActualName', 'b.GudangAsal', 'ga.NamaGudang as NamaGudangAsal', 'gt.NamaGudang as NamaGudangTujuan',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 30; //FiturID di tabel serverfitur
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
                $btn_hapus = $temp['ProdTglSelesai'] == null ? '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>' : '';
                $temp['no'] = ++$num_start_row;
                $temp['TanggalTransaksi'] = shortdate_indo(date('Y-m-d', strtotime($temp['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($temp['TanggalTransaksi']));
                $temp['ProdTglSelesai'] = isset($temp['ProdTglSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($temp['ProdTglSelesai']))) . ' ' . date('H:i', strtotime($temp['ProdTglSelesai'])) : '-';
                $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/penyesuaian_produksi/detail/' . base64_encode($temp['NoTrans'])) . '" type="button" title="Detail SPK"><span class="fa fa-list" aria-hidden="true"></span></a>' . $btn_hapus;
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpan()
    {
        $insertdata = $this->input->post();
        unset($insertdata['Gudang']);

        $prefix = "TBR-" . date("Ym");
        $insertdata['NoTrans'] = $this->crud->get_kode([
            'select' => 'RIGHT(NoTrans, 7) AS KODE',
            'from' => 'transaksibarang',
            'where' => [['LEFT(NoTrans, 10) =' => $prefix]],
            'limit' => 1,
            'order_by' => 'NoTrans DESC',
            'prefix' => $prefix
        ]);
        $insertdata['UserName'] = $this->session->userdata('UserName');
        $insertdata['GudangAsal'] = $this->input->post('Gudang');
        $insertdata['GudangTujuan'] = $this->input->post('Gudang');
        $insertdata['JenisTransaksi'] = 'PROSES PRODUKSI';
        $insertdata['IsHapus'] = 0;

        $res = $this->crud->insert($insertdata, 'transaksibarang');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'tambah',
                'JenisTransaksi' => 'Penyesuaian Produksi',
                'Description' => 'tambah data penyesuaian produksi ' . $insertdata['NoTrans']
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ("Berhasil menambah Data"),
                'id' => $insertdata['NoTrans']
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ("Gagal Menambah Data")
            ]);
        }
    }

    public function hapus()
    {
        $kode  = $this->input->get('NoTrans');
        $getItemBarang = $this->crud->get_rows([
            'select' => '*',
            'from' => 'itemtransaksibarang',
            'where' => [['NoRefProduksi' => $kode]]
        ]);

        // update barang produksi & hapus bahan baku
        foreach ($getItemBarang as $value) {
            if ($value['IsBarangJadi'] == 1) {
                $updatebarangjadi[] = $this->crud->update(['NoRefProduksi' => null], ['NoRefProduksi' => $kode], 'itemtransaksibarang');
            }
            if ($value['IsBahanBaku'] == 1) {
                $deletebahanbaku[] = $this->crud->delete(['NoTrans' => $kode], 'itemtransaksibarang');
            }
        }

        $res = $this->crud->delete(['NoTrans' => $kode], 'transaksibarang');
        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Penyesuaian Produksi',
                'Description' => 'hapus data penyesuaian produksi ' . $kode
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

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[30]);
        $notrans        = escape(base64_decode($this->uri->segment(4)));

        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu'] = 'penyesuaianprod';
        $data['title'] = 'Detail Penyesuaian Produksi';
        $data['view'] = 'transaksi/v_penyesuaian_produksi_detail';
        $data['scripts'] = 'transaksi/s_penyesuaian_produksi_detail';

        $dtinduk = [
            'select' => 'b.NoTrans, b.NoRefTrManual, b.TanggalTransaksi, b.Deskripsi, b.ProdTglSelesai, b.ProdUkuran, b.ProdJmlDaun, b.GudangAsal, ga.NamaGudang as NamaGudangAsal, b.GudangTujuan, gt.NamaGudang as NamaGudangTujuan, b.UserName, u.ActualName',
            'from' => 'transaksibarang b',
            'join' => [
                [
                    'table' => ' mstgudang ga',
                    'on' => "ga.KodeGudang = b.GudangAsal",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang gt',
                    'on' => "gt.KodeGudang = b.GudangTujuan",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "b.UserName = u.UserName",
                    'param' => 'LEFT',
                ]
            ],
            'where' => [['b.NoTrans' => $notrans]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
        $data['TanggalTransaksi'] = shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($data['dtinduk']['TanggalTransaksi']));
        $data['ProdTglSelesai'] = isset($data['dtinduk']['ProdTglSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['ProdTglSelesai']))) . ' ' . date('H:i', strtotime($data['dtinduk']['ProdTglSelesai'])) : '-';
        $data['NoTrans'] = $notrans;
        $data['KodeGudang'] = $data['dtinduk']['GudangAsal'];

        $data['barangjadi'] = $this->crud->get_rows([
            'select' => 'i.*, br.NamaBarang, br.KodeManual, j.SPKNomor',
            'from' => 'itemtransaksibarang i',
            'join' => [
                [
                    'table' => 'mstbarang br',
                    'on' => 'i.KodeBarang = br.KodeBarang',
                    'param' => 'INNER',
                ],
                [
                    'table' => 'transaksibarang b',
                    'on' => 'i.NoTrans = b.NoTrans',
                    'param' => 'INNER',
                ],
                [
                    'table' => 'transpenjualan j',
                    'on' => 'b.NoRefTrSistem = j.IDTransJual',
                    'param' => 'INNER',
                ]
            ],
            'where' => [[
                'i.NoRefProduksi' => null,
                'i.GudangTujuan' => $data['KodeGudang'],
                'i.IsBarangJadi' => 1,
                'i.IsBahanBaku' => 0,
                'i.IsHapus' => 0,
                'j.StatusProses' => 'DONE'
            ]],
            'order_by' => 'i.KodeBarang ASC'
        ]);

        $data['bahanbaku'] = $this->crud->get_rows([
            'select' => '*',
            'from' => 'mstbarang b',
            'join' => [[
                'table' => 'mstjenisbarang j',
                'on' => "b.KodeJenis = j.KodeJenis",
                'param' => 'INNER'
            ]],
            'where' => ["j.NamaJenisBarang NOT LIKE 'BARANG JADI'"],
            'order_by' => 'b.KodeBarang ASC'
        ]);
        // echo json_encode($data['barangjadi']); die;

        loadview($data);
    }

    public function get_all()
    {
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'penyesuaianprod';
            $data['title'] = 'Detail Penyesuaian Produksi';
            $data['view'] = 'transaksi/v_penyesuaian_produksi_detail';
            $data['scripts'] = 'transaksi/s_penyesuaian_produksi_detail';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $notrans = $this->input->get('notrans');
            $isbarangjadi = $this->input->get('isbarangjadi');
            $isbahanbaku = $this->input->get('isbahanbaku');
            $configData['table'] = 'itemtransaksibarang i';
            $configData['where'] = [
                [
                    'i.NoRefProduksi' => $notrans,
                    'i.IsBarangJadi' => $isbarangjadi,
                    'i.IsBahanBaku' => $isbahanbaku,
                    'i.IsHapus' => 0,
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
                    'param' => 'INNER',
                ],
                [
                    'table' => ' mstjenisbarang jb',
                    'on' => "jb.KodeJenis = br.KodeJenis",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksibarang b',
                    'on' => "b.NoTrans = i.NoRefProduksi",
                    'param' => 'INNER',
                ]
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.NoUrut', 'i.NoTrans', 'i.KodeBarang', 'i.Qty', 'i.HargaSatuan', 'i.Total', 'i.Deskripsi', 'i.JenisStok', 'i.GudangAsal', 'i.GudangTujuan', 'i.SatuanBarang', 'i.JenisBarang', 'i.Kategory', 'i.IsBahanBaku', 'i.IsBarangJadi', 'i.ProdUkuran', 'i.ProdJmlDaun', 'i.IsHapus', 'br.NamaBarang', 'br.KodeJenis', 'b.TanggalTransaksi', 'b.ProdTglSelesai', 'jb.NamaJenisBarang', 'i.NoRefProduksi', 'i.BeratKotor'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.NoUrut', 'i.NoTrans', 'i.KodeBarang', 'i.Qty', 'i.HargaSatuan', 'i.Total', 'i.Deskripsi', 'i.JenisStok', 'i.GudangAsal', 'i.GudangTujuan', 'i.SatuanBarang', 'i.JenisBarang', 'i.Kategory', 'i.IsBahanBaku', 'i.IsBarangJadi', 'i.ProdUkuran', 'i.ProdJmlDaun', 'i.IsHapus', 'br.NamaBarang', 'br.KodeJenis', 'b.TanggalTransaksi', 'b.ProdTglSelesai', 'jb.NamaJenisBarang', 'i.NoRefProduksi', 'i.BeratKotor',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 30; //FiturID di tabel serverfitur
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
                $temp['TanggalTransaksi'] = isset($temp['TanggalTransaksi']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($temp['TanggalTransaksi'])) : '-';
                $temp['ProdUkuran'] = isset($temp['ProdUkuran']) ? $temp['ProdUkuran'] : '-';
                $temp['ProdJmlDaun'] = isset($temp['ProdJmlDaun']) ? $temp['ProdJmlDaun'] : '-';
                $stok = $this->lokasi->get_stok_per_gudang($temp['GudangAsal'], $temp['KodeBarang'])['stok'];
                $temp['Stok'] = $stok;
                $temp['BeratJadi'] = $temp['Qty'] > 0 ? $temp['Qty'] : $temp['BeratKotor'];
                $status = $temp['Qty'] > 0 ? 'bersih' : 'kotor';
                $temp['PemakaianBahanMasak'] = ($temp['IsBarangJadi'] == 1) ? $this->pemakaian_bahan_masak($temp['NoRefProduksi'], $temp['BeratKotor'], 'kotor') : 0;
                if ($temp['ProdTglSelesai'] == null) {
                    if ($temp['IsBarangJadi'] == 1) {
                        $btn_edit = '<a class="btnedit" href="javascript:(0)" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                    } else {
                        $btn_edit = '<a class="btnedit" href="javascript:(0)" type="button" data-model=\'' . json_encode($temp) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                    }
                    $temp['btn_aksi'] = $btn_edit . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' data-kode2=' . $temp['NoUrut'] . ' data-kode3=' . $temp['NoRefProduksi'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function pemakaian_bahan_masak($notrans, $bkotor, $status)
    {
        $totalbbaku = $this->crud->get_one_row([
            'select' => 'SUM(Qty) AS TotalBaku',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoRefProduksi' => $notrans,
                'IsBahanBaku' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $totalbjadi = $this->crud->get_one_row([
            'select' => 'SUM(Qty) AS TotalJadi, SUM(BeratKotor) AS TotalKotor',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoRefProduksi' => $notrans,
                'IsBarangJadi' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $pembilang = $status == 'bersih' ? $totalbjadi['TotalJadi'] : $totalbjadi['TotalKotor'];
        $presentasebahan = (int)$totalbbaku['TotalBaku'] > 0 ? $pembilang / $totalbbaku['TotalBaku'] * 100 : 1;
        $pemakaianbahan = (int)$bkotor > 0 ? $bkotor / $presentasebahan * 100 : 0;

        return $pemakaianbahan;
    }

    public function cetakdetail()
    {
        $notrans = escape(base64_decode($this->uri->segment(4)));

        $data['dtinduk'] = $this->crud->get_one_row([
            'select' => 'b.*, j.IDTransJual, j.TglSlipOrder, j.EstimasiSelesai, j.SPKTanggal, j.NoSlipOrder, j.SPKNomor, j.KodePerson, ga.NamaGudang as NamaGudangAsal, gt.NamaGudang as NamaGudangTujuan, j.StatusProduksi, j.SPKDibuatOleh, u.ActualName',
            'from' => 'transaksibarang b',
            'join' => [
                [
                    'table' => ' transpenjualan j',
                    'on' => "b.NoRefTrSistem = j.IDTransJual",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang ga',
                    'on' => "ga.KodeGudang = b.GudangAsal",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang gt',
                    'on' => "gt.KodeGudang = b.GudangTujuan",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = b.UserName",
                    'param' => 'LEFT',
                ]
            ],
            'where' => [['b.NoTrans' => $notrans]],
        ]);
        $data['SPKTanggal'] = isset($data['dtinduk']['SPKTanggal']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['SPKTanggal']))) : '';
        $data['TglMulai'] = isset($data['dtinduk']['TanggalTransaksi']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['TanggalTransaksi']))) : '';
        $data['TglSelesai'] = isset($data['dtinduk']['ProdTglSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['ProdTglSelesai']))) : '';

        $data['dtbahan'] = $this->crud->get_rows([
            'select' => 'i.*, br.NamaBarang',
            'from' => 'itemtransaksibarang i',
            'join' => [[
                'table' => 'mstbarang br',
                'on' => 'i.KodeBarang = br.KodeBarang',
                'param' => 'INNER'
            ]],
            'where' => [[
                'NoRefProduksi' => $notrans,
                'IsBahanBaku' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $produksibarang = $this->crud->get_rows([
            'select' => 'i.*, br.NamaBarang',
            'from' => 'itemtransaksibarang i',
            'join' => [[
                'table' => 'mstbarang br',
                'on' => 'i.KodeBarang = br.KodeBarang',
                'param' => 'INNER'
            ]],
            'where' => [[
                'NoRefProduksi' => $notrans,
                'IsBarangJadi' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $dtproduksi = [];
        $beratjadi = 0;
        foreach ($produksibarang as $key => $value) {
            $dtproduksi[$key] = $value;
            $dtproduksi[$key]['PemakaianBahanMasak'] = $this->pemakaian_bahan_masak($value['NoRefProduksi'], $value['BeratKotor'], 'kotor');
        }
        $data['dtproduksi'] = $dtproduksi;
        // echo json_encode($data['dtproduksi']); die;

        $data['src_url'] = base_url('transaksi/proses_produksi/detail/') . base64_encode($data['dtinduk']['IDTransJual']);

        $this->load->library('Pdf');
        $this->load->view('transaksi/v_cetak_penyesuaianprod_detail', $data);
    }

    public function simpanbahan()
    {
        $insertdata = $this->input->post();
        unset($insertdata['Gudang']);
        $bahanbaku = $this->input->post('IsBahanBaku');
        $isEdit = true;
        
        $insertdata['IsBahanBaku'] = $bahanbaku == 'on' ? 1 : 0;
        $insertdata['IsBarangJadi'] = $insertdata['IsBahanBaku'] == 1 ? 0 : 1;
        $insertdata['JenisStok'] = $insertdata['IsBahanBaku'] == 1 ? 'KELUAR' : 'MASUK';
        $insertdata['HargaSatuan'] = ($this->input->post('HargaSatuan') != null && $insertdata['IsBahanBaku'] == 1) ? str_replace(['.', ','], ['', '.'], $this->input->post('HargaSatuan')) : 0;
        $insertdata['Qty'] = ($this->input->post('Qty') != null && $insertdata['IsBahanBaku'] == 1) ? str_replace(['.', ','], ['', '.'], $this->input->post('Qty')) : 0;
        $insertdata['Total'] = $insertdata['IsBahanBaku'] == 1 ? $insertdata['HargaSatuan'] * $insertdata['Qty'] : 0;
        $insertdata['BeratKotor'] = $insertdata['IsBahanBaku'] == 1 ? null : str_replace(['.', ','], ['', '.'], $this->input->post('Qty'));
        if (!($this->input->post('NoUrut') != null && $this->input->post('NoUrut') != '')) {
            $getNoUrut = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'itemtransaksibarang',
                'where' => [['NoTrans' => $this->input->post('NoTrans')]],
                'order_by' => 'NoUrut DESC'
            ]);
            $insertdata['NoUrut'] = isset($getNoUrut) ? (int)$getNoUrut['NoUrut'] + 1 : 1;
            $insertdata['GudangAsal'] = $this->input->post('Gudang');
            $insertdata['GudangTujuan'] = $this->input->post('Gudang');
            $insertdata['IsHapus'] = 0;
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'itemtransaksibarang');
        if ($res) {
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

    public function list_barang_jadi(){
        $kodegudang = $this->input->get('kodegudang', TRUE);
        if ($this->input->get('searchTerm', TRUE)) {
            $data = $this->lokasi->get_item_produksi_like($this->input->get('searchTerm', TRUE), $kodegudang);
        } else {
            $data = $this->lokasi->get_item_produksi($kodegudang);
        }

        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function get_one_jadi(){
        $kodebarang = $this->input->get('KodeBarang');
        $data = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'itemtransaksibarang',
            'where' => [['KodeBarang' => $kodebarang]]
        ]);
        echo json_encode($data);
    }

    public function simpanbarangjadi()
    {
        $updatedata = [
            'BeratKotor' => str_replace(['.', ','], ['', '.'], $this->input->post('Qty')),
            'NoRefProduksi' => $this->input->post('NoRefProduksi'),
        ];

        $where = [
            'NoTrans' => $this->input->post('NoTrans'),
            'NoUrut' => $this->input->post('NoUrut'),
        ];

        $res = $this->crud->update($updatedata, $where, 'itemtransaksibarang');

        if ($res) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Menyimpan Data"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Menyimpan Data"
            ]);
        }
    }

    public function hapusdetail()
    {
        $notrans  = $this->input->get('NoTrans');
        $nourut  = $this->input->get('NoUrut');
        $norefprod  = $this->input->get('NoRefProduksi');

        if ($notrans != $norefprod) {
            $res = $this->crud->update([
                'Qty' => 0,
                'HargaSatuan' => 0,
                'Total' => 0,
                'BeratKotor' => 0,
                'NoRefProduksi' => null
            ], [
                'NoUrut' => $nourut,
                'NoTrans' => $notrans
            ], 'itemtransaksibarang');
        } else {
            $res = $this->crud->delete(['NoUrut' => $nourut, 'NoTrans' => $notrans], 'itemtransaksibarang');
        }

        if ($res) {
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

    public function checkItemProd()
    {
        $notrans = $this->input->get('NoTrans');
        $getItemProd = $this->crud->get_count([
            'select' => '*',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoRefProduksi' => $notrans,
                'IsBarangJadi' => 1,
                'IsBahanBaku' => 0,
                'IsHapus' => 0
            ]]
        ]);

        $getItemBahan = $this->crud->get_count([
            'select' => '*',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoRefProduksi' => $notrans,
                'IsBarangJadi' => 0,
                'IsBahanBaku' => 1,
                'IsHapus' => 0
            ]]
        ]);

        if ($getItemProd < 1 || $getItemBahan < 1) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Item Bahan Baku dan Item Produksi harus diisi terlebih dahulu!']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Item Lengkap']);
        }
    }

    public function selesai_produksi()
    {
        $notrans = $this->input->get('NoTrans');
        
        $bahanbaku = $this->crud->get_one_row([
            'select' => 'SUM(Qty) AS TotalBahanBaku, SUM(Total) AS ModalBahan',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoRefProduksi' => $notrans,
                'IsBahanBaku' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $barangjadi = $this->crud->get_one_row([
            'select' => 'SUM(Qty) AS TotalJadi',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoRefProduksi' => $notrans,
                'IsBarangJadi' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $barangproduksi = $this->crud->get_rows([
            'select' => '*',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoRefProduksi' => $notrans,
                'IsBarangJadi' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $totalpemakaian = 0;
        foreach ($barangproduksi as $value) {
            $totalpemakaian += $this->pemakaian_bahan_masak($value['NoRefProduksi'], $value['BeratKotor'], 'kotor');
        }

        $this->db->trans_begin();
        $is_item_produksi = false;
        $modalbahan_perkg = ($totalpemakaian > 0) ? $bahanbaku['ModalBahan'] / $totalpemakaian : 0;
        $updateproduksi = [];
        foreach ($barangproduksi as $value) {
            $pemakaianbahan = $this->pemakaian_bahan_masak($value['NoRefProduksi'], $value['BeratKotor'], 'kotor');
            if ($value['BeratKotor'] > 0) {
                $updateproduksi = [
                    'Total' => $pemakaianbahan * $modalbahan_perkg,
                    'HargaSatuan' => $value['BeratKotor'] > 0 ? ($pemakaianbahan * $modalbahan_perkg) / $value['BeratKotor'] : 0
                ];
                $where = [
                    'NoTrans' => $value['NoTrans'],
                    'NoUrut' => $value['NoUrut']
                ];
    
                $updateitemprod[] = $this->crud->update($updateproduksi, $where, 'itemtransaksibarang');
                $updatemstbrg[] = $this->crud->update([
                    'NilaiHPP' => $updateproduksi['HargaSatuan'],
                    'HargaJual' => $updateproduksi['Total']
                ], [
                    'KodeBarang' => $value['KodeBarang']
                ], 'mstbarang');
            }
            $is_item_produksi = true;
        }

        if ($is_item_produksi) {
            $updatetrbarang = $this->crud->update(
                [
                    'ProdTglSelesai' => date('Y-m-d H:i'),
                    'BiayaProduksi' => $bahanbaku['ModalBahan']
                ], ['NoTrans' => $notrans], 'transaksibarang');
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'tambah',
                'JenisTransaksi' => 'Penyesuaian Produksi',
                'Description' => 'penyesuaian produksi selesai ' . $notrans
            ]);
            $this->db->trans_commit();
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil menyelesaikan penyesuaian produksi"
            ]);
        } else {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menyelesaikan penyesuaian produksi"
            ]);
        }
    }
}

