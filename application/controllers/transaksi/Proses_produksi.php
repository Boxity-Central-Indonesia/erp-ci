<?php
defined('BASEPATH') or exit('No direct script access allowed');

class proses_produksi extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[29]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[29]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);

            $sql = "SELECT COALESCE(SUM(i.Total), 0) AS Total
                FROM transpenjualan j
                JOIN transaksibarang tb ON j.IDTransJual = tb.NoRefTrSistem
                JOIN itemtransaksibarang i ON tb.NoTrans = i.NoTrans
                WHERE LEFT(j.IDTransJual, 3) = 'PRD'
                AND j.StatusProduksi = 'WIP'";
            $data['totalhpp'] = $this->db->query($sql)->row_array()['Total'];

            $data['menu'] = 'prosesprod';
            $data['title'] = 'Proses Produksi';
            $data['view'] = 'transaksi/v_proses_produksi';
            $data['scripts'] = 'transaksi/s_proses_produksi';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transpenjualan j';

            $configData['where'] = [
                [
                    'j.SPKNomor !=' => null,
                    'j.StatusProses' => 'DONE',
                    // 'b.ProdUkuran !=' => null,
                    // 'b.ProdJmlDaun !=' => null,
                ],
                "LEFT(j.IDTransJual, 3) = 'PRD'",
            ];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (j.IDTransJual LIKE '%$cari%' OR j.SPKNomor LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(j.SPKTanggal) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' transaksibarang b',
                    'on' => "b.NoRefTrSistem = j.IDTransJual",
                    'param' => 'INNER',
                ],
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = j.KodePerson",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.IDTransJual', 'j.NoSlipOrder', 'j.SPKDibuatOleh', 'j.TglSlipOrder', 'j.EstimasiSelesai', 'j.SODibuatOleh', 'j.SPKNomor', 'j.SPKTanggal', 'j.TotalNilaiBarang', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.KodePerson', 'p.NamaPersonCP', 'b.NoRefTrSistem', 'j.StatusProduksi'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['group_by'] = 'b.NoRefTrSistem';
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'j.SPKTanggal';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'j.IDTransJual', 'j.NoSlipOrder', 'j.SPKDibuatOleh', 'j.TglSlipOrder', 'j.EstimasiSelesai', 'j.SODibuatOleh', 'j.SPKNomor', 'j.SPKTanggal', 'j.TotalNilaiBarang', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.KodePerson', 'p.NamaPersonCP', 'b.NoRefTrSistem', 'j.StatusProduksi',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 29; //FiturID di tabel serverfitur
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
                $temp['TglSlipOrder'] = shortdate_indo(date('Y-m-d', strtotime($temp['TglSlipOrder']))) . ' ' . date('H:i', strtotime($temp['TglSlipOrder']));
                $temp['SPKTanggal'] = shortdate_indo(date('Y-m-d', strtotime($temp['SPKTanggal']))) . ' ' . date('H:i', strtotime($temp['SPKTanggal']));
                $temp['EstimasiSelesai'] = isset($temp['EstimasiSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($temp['EstimasiSelesai']))) . ' ' . date('H:i', strtotime($temp['EstimasiSelesai'])) : '-';
                $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/proses_produksi/detail/' . base64_encode($temp['NoRefTrSistem'])) . '" type="button" title="Detail Proses Produksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetakdetail()
    {
        $notrans = escape(base64_decode($this->uri->segment(4)));

        $data['dtinduk'] = $this->crud->get_one_row([
            'select' => 'b.*, j.IDTransJual, j.TglSlipOrder, j.EstimasiSelesai, j.SPKTanggal, j.NoSlipOrder, j.SPKNomor, j.KodePerson, ga.NamaGudang as NamaGudangAsal, gt.NamaGudang as NamaGudangTujuan, j.StatusProduksi, j.SPKDibuatOleh',
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
                'NoTrans' => $notrans,
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
                'NoTrans' => $notrans,
                'IsBarangJadi' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $dtproduksi = [];
        foreach ($produksibarang as $key => $value) {
            $dtproduksi[$key] = $value;
            $dtproduksi[$key]['PemakaianBahanMasak'] = $this->pemakaian_bahan_masak($value['NoTrans'], $value['Qty']);
        }
        $data['dtproduksi'] = $dtproduksi;

        $data['src_url'] = base_url('transaksi/proses_produksi/detail/') . base64_encode($data['dtinduk']['IDTransJual']);

        $this->load->library('Pdf');
        $this->load->view('transaksi/v_cetak_prosesprod_detail', $data);
    }

    public function subdetail()
    {
        checkAccess($this->session->userdata('fiturview')[29]);
        $notrans = escape(base64_decode($this->uri->segment(4)));
        $nourut  = escape(base64_decode($this->uri->segment(5)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'prosesprod';
            $data['title'] = 'Sub Detail Proses Produksi';
            $data['view'] = 'transaksi/v_proses_produksi_subdetail';
            $data['scripts'] = 'transaksi/s_proses_produksi_subdetail';

            $dtinduk = [
                'select' => 'i.*, b.TanggalTransaksi, b.ProdTglSelesai, ga.NamaGudang as NamaGudangAsal, b.GudangTujuan, gt.NamaGudang as NamaGudangTujuan, b.NoRefTrSistem, j.IDTransJual, j.SPKNomor, j.SPKTanggal, br.NamaBarang, j.StatusProses, j.StatusProduksi',
                'from' => 'itemtransaksibarang i',
                'join' => [
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
                        'table' => ' mstbarang br',
                        'on' => "br.KodeBarang = i.KodeBarang",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' transaksibarang b',
                        'on' => "b.NoTrans = i.NoTrans",
                        'param' => 'INNER',
                    ],
                    [
                        'table' => ' transpenjualan j',
                        'on' => "j.IDTransJual = b.NoRefTrSistem",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [[
                    'i.NoTrans' => $notrans,
                    'i.NoUrut' => $nourut
                ]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['ProdTglSelesai'] = isset($data['dtinduk']['ProdTglSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['ProdTglSelesai']))) . ' ' . date('H:i', strtotime($data['dtinduk']['ProdTglSelesai'])) : '-';
            $data['NoTrans'] = $notrans;
            $data['NoUrut'] = $nourut;
            $data['NoRefTrSistem'] = $data['dtinduk']['NoRefTrSistem'];

            $data['countAktivitas'] = $this->crud->get_count(
                [
                    'select' => '*',
                    'from' => 'aktivitasproduksi',
                    'where' => [[
                        'NoTrans' => $notrans,
                        'NoUrut' => $nourut
                    ]],
                ]
            );

            $dtaktivitas = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstaktivitas',
                ]
            );
            $data['dtaktivitas'] = $dtaktivitas;

            $dtpegawai = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstpegawai',
                    // 'where' => [['KodeJabatan' => 'JBT-0000003']],
                ]
            );
            $data['dtpegawai'] = $dtpegawai;

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $notrans   = $this->input->get('notrans');
            $nourut   = $this->input->get('nourut');
            $configData['table'] = 'aktivitasproduksi a';
            $configData['where'] = [[
                'a.NoTrans' => $notrans,
                'a.NoUrut' => $nourut
            ]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (a.JenisAktivitas LIKE '%$cari%' OR p.NamaPegawai LIKE '%$cari%')";
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
                [
                    'table' => ' transaksibarang b',
                    'on' => "b.NoTrans = a.NoTrans",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' itemtransaksibarang i',
                    'on' => "i.NoTrans = a.NoTrans AND i.NoUrut = a.NoUrut",
                    'param' => 'LEFT'
                ]
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'a.NoTrAktivitas', 'a.Biaya', 'a.JenisAktivitas', 'a.TglAktivitas', 'a.Keterangan', 'a.JmlAmpasDapur', 'a.GoniAmpasDapur', 'a.Satuan', 'a.KodePegawai', 'p.NamaPegawai', 'a.KodeAktivitas', 'ma.BatasBawah', 'ma.JmlDaun', 'ma.BatasAtas', 'a.NoTrans', 'b.ProdTglSelesai', 'i.Qty'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'a.TglAktivitas';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'a.NoTrAktivitas', 'a.Biaya', 'a.JenisAktivitas', 'a.TglAktivitas', 'a.Keterangan', 'a.JmlAmpasDapur', 'a.GoniAmpasDapur', 'a.Satuan', 'a.KodePegawai', 'p.NamaPegawai', 'a.KodeAktivitas', 'ma.BatasBawah', 'ma.JmlDaun', 'ma.BatasAtas', 'a.NoTrans', 'b.ProdTglSelesai', 'i.Qty',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 29; //FiturID di tabel serverfitur
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
                $temp['TglAktivitas'] = shortdate_indo(date('Y-m-d', strtotime($temp['TglAktivitas'])));
                if ($canEdit == 1 && $canDelete == 1 && $temp['ProdTglSelesai'] == null && $temp['Qty'] == 0) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrAktivitas'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1 && $temp['ProdTglSelesai'] == null && $temp['Qty'] == 0) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1 && $temp['ProdTglSelesai'] == null && $temp['Qty'] == 0) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['NoTrAktivitas'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpansubdetail()
    {
        $insertdata = $this->input->post();
        unset($insertdata['Biaya']);
        $biaya = str_replace(['.', ','], ['', '.'], $this->input->post('Biaya'));
        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('NoTrAktivitas') != null && $this->input->post('NoTrAktivitas') != '')) {
            $prefix = "AKP-" . date("Ym");
            $insertdata['NoTrAktivitas'] = $this->crud->get_kode([
                'select' => 'RIGHT(NoTrAktivitas, 7) AS KODE',
                'where' => [['LEFT(NoTrAktivitas, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'NoTrAktivitas DESC',
                'prefix' => $prefix
            ]);
            $insertdata['Biaya'] = $biaya;
            $insertdata['UserName'] = $this->session->userdata('UserName');
            $isEdit = false;
        } else {
            $insertdata['Biaya'] = $biaya;
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'aktivitasproduksi');

        if ($res) {
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

    public function hapussubdetail()
    {
        $kode  = $this->input->get('NoTrAktivitas');

        $res = $this->crud->delete(['NoTrAktivitas' => $kode], 'aktivitasproduksi');
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

    public function checkJmlAktivitas()
    {
        $notrans = $this->input->get('NoTrans');
        $nourut = $this->input->get('NoUrut');

        $count = $this->crud->get_count([
            'select' => 'NoTrAktivitas',
            'from' => 'aktivitasproduksi',
            'where' => [[
                'NoTrans' => $notrans,
                'NoUrut' => $nourut
            ]]
        ]);

        if ($count < 1) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Aktivitas proses produksi belum diinputkan!']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Aktivitas ok']);
        }
    }

    public function selesai_per_item()
    {
        $qty = str_replace(['.', ','], ['', '.'], $this->input->post('Qty'));
        $total = $this->input->post('Total');
        $kodebarang = $this->input->post('KodeBarang');

        $where = [
            'NoTrans' => $this->input->post('NoTrans'),
            'NoUrut' => $this->input->post('NoUrut')
        ];
        $updatedata = [
            'Qty' => $qty,
            'HargaSatuan' => $total / $qty
        ];

        // update hpp di master barang
        if ($kodebarang) {
            $updatebarang = $this->crud->update(['NilaiHPP' => $updatedata['HargaSatuan']], ['KodeBarang' => $kodebarang], 'mstbarang');
        }

        $res = $this->crud->update($updatedata, $where, 'itemtransaksibarang');
        if ($res) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil menyelesaikan proses produksi"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menyelesaikan proses produksi"
            ]);
        }
    }

    public function updatebiayaprod()
    {
        $biaya = str_replace(['.', ','], ['', '.'], $this->input->post('BiayaProduksi'));
        $getKas = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transaksikas',
            'where' => [['NoRef_Sistem' => $this->input->post('NoTrans')]],
        ]);

        // insert ke tabel tr kas
        if ($getKas == null) {
            $prefix = "TRK-" . date("Ym");
            $insertkas['NoTransKas'] = $this->crud->get_kode([
                'select' => 'RIGHT(NoTransKas, 7) AS KODE',
                'from' => 'transaksikas',
                'where' => [['LEFT(NoTransKas, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'NoTransKas DESC',
                'prefix' => $prefix
            ]);
            $insertkas['KodeTahun']         = $this->akses->get_tahun_aktif();
            $insertkas['TanggalTransaksi']  = date("Y-m-d H:i");
            $insertkas['NoRef_Sistem']      = $this->input->post('NoTrans');
            $insertkas['Uraian']            = 'Biaya proses produksi barang';
            $insertkas['UserName']          = $this->session->userdata('UserName');
            $insertkas['TotalTransaksi']    = $biaya;
            $insertkas['JenisTransaksiKas'] = 'BIAYA PRODUKSI';
            $insertkas['IsDijurnalkan']     = 0;

            $kas = $this->crud->insert($insertkas, 'transaksikas');
        } else {
            $kas = $this->crud->update(['TotalTransaksi' => $biaya], ['NoRef_Sistem' => $this->input->post('NoTrans')], 'transaksikas');
        }

        $res = $this->crud->update(['BiayaProduksi' => $biaya], ['NoTrans' => $this->input->post('NoTrans')], 'transaksibarang');
        if ($res) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Mengubah Biaya Produksi"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Mengubah Biaya Produksi"
            ]);
        }
    }

    public function checkQty()
    {
        $notrans = $this->input->get('NoTrans');
        $getItemProd = $this->crud->get_rows([
            'select' => '*',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoTrans' => $notrans,
                'IsBarangJadi' => 1,
                'IsBahanBaku' => 0,
                'IsHapus' => 0
            ]]
        ]);

        $cekqty = 1;
        foreach ($getItemProd as $value) {
            if ((int)$value['Qty'] == 0) {
                $cekqty = 0;
            }
        }

        if ($cekqty < 1) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Berat bersih masih belum diinputkan seluruhnya!']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Berat bersih lengkap']);
        }
    }

    public function selesai()
    {
        $idtransjual = $this->input->get('IDTransJual');
        $notrans = $this->input->get('NoTrans');

        $updatetrbarang = $this->crud->update(['ProdTglSelesai' => date('Y-m-d H:i')], ['NoTrans' => $notrans], 'transaksibarang');
        $updatetrjual = $this->crud->update(['StatusProduksi' => 'SELESAI'], ['IDTransJual' => $idtransjual], 'transpenjualan');
        if ($updatetrjual) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'tambah',
                'JenisTransaksi' => 'Proses Produksi',
                'Description' => 'proses produksi selesai ' . $notrans
            ]);
            $this->db->trans_commit();
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil menyelesaikan proses produksi"
            ]);
        } else {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menyelesaikan proses produksi"
            ]);
        }

    }

    public function pemakaian_bahan_masak($notrans, $bkotor)
    {
        $totalbbaku = $this->crud->get_one_row([
            'select' => 'SUM(Qty) AS TotalBaku',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoTrans' => $notrans,
                'IsBahanBaku' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $totalbjadi = $this->crud->get_one_row([
            'select' => 'SUM(Qty) AS TotalJadi',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoTrans' => $notrans,
                'IsBarangJadi' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $presentasebahan = (int)$totalbbaku['TotalBaku'] > 0 ? $totalbjadi['TotalJadi'] / $totalbbaku['TotalBaku'] * 100 : 1;
        $pemakaianbahan = (int)$bkotor > 0 ? $bkotor / $presentasebahan * 100 : 0;

        return $pemakaianbahan;
    }

    public function selesai_old()
    {
        $kode = $this->input->post('NoTrans');
        $kode2 = $this->input->post('NoRefTrSistem');
        $biayaprod = str_replace(['.', ','], ['', '.'], $this->input->post('BiayaProduksi'));

        // insert ke tabel transaksi kas
        $prefixkas = "TRK-" . date("Ym");
        $insertkasprod['NoTransKas'] = $this->crud->get_kode([
            'select' => 'RIGHT(NoTransKas, 7) AS KODE',
            'from' => 'transaksikas',
            'where' => [['LEFT(NoTransKas, 10) =' => $prefixkas]],
            'limit' => 1,
            'order_by' => 'NoTransKas DESC',
            'prefix' => $prefixkas
        ]);
        $insertkasprod['KodeTahun']         = $this->akses->get_tahun_aktif();
        $insertkasprod['TanggalTransaksi']  = date("Y-m-d H:i");
        $insertkasprod['NoRef_Sistem']      = $kode;
        $insertkasprod['Uraian']            = 'Biaya proses produksi barang';
        $insertkasprod['UserName']          = $this->session->userdata('UserName');
        $insertkasprod['TotalTransaksi']    = $biayaprod;
        $insertkasprod['JenisTransaksiKas'] = 'BIAYA PRODUKSI';
        $insertkasprod['IsDijurnalkan']     = 0;
        $kas = $this->crud->insert($insertkasprod, 'transaksikas');

        $getakunpro = $this->crud->get_rows([
            'select' => 's.KodeSetAkun, d.NoUrut, d.JenisJurnal, d.KodeAkun, a.NamaAkun, s.NamaTransaksi, s.JenisTransaksi',
            'from' => 'setakunjurnal s',
            'join' => [
                [
                    'table' => 'detailsetakun d',
                    'on' => "d.KodeSetAkun = s.KodeSetAkun",
                    'param' => 'INNER',
                ],
                [
                    'table' => 'mstakun a',
                    'on' => "a.KodeAkun = d.KodeAkun",
                    'param' => 'INNER',
                ],
            ],
            'where' => [[
                's.NamaTransaksi' => 'Produksi',
                's.JenisTransaksi' => 'Tunai',
            ]],
        ]);

        $getakunbahan = $this->crud->get_rows([
            'select' => 's.KodeSetAkun, d.NoUrut, d.JenisJurnal, d.KodeAkun, a.NamaAkun, s.NamaTransaksi, s.JenisTransaksi',
            'from' => 'setakunjurnal s',
            'join' => [
                [
                    'table' => 'detailsetakun d',
                    'on' => "d.KodeSetAkun = s.KodeSetAkun",
                    'param' => 'INNER',
                ],
                [
                    'table' => 'mstakun a',
                    'on' => "a.KodeAkun = d.KodeAkun",
                    'param' => 'INNER',
                ],
            ],
            'where' => [[
                's.NamaTransaksi' => 'BahanPro',
                's.JenisTransaksi' => 'Tunai',
            ]],
        ]);

        $getakunakt = $this->crud->get_rows([
            'select' => 's.KodeSetAkun, d.NoUrut, d.JenisJurnal, d.KodeAkun, a.NamaAkun, s.NamaTransaksi, s.JenisTransaksi',
            'from' => 'setakunjurnal s',
            'join' => [
                [
                    'table' => 'detailsetakun d',
                    'on' => "d.KodeSetAkun = s.KodeSetAkun",
                    'param' => 'INNER',
                ],
                [
                    'table' => 'mstakun a',
                    'on' => "a.KodeAkun = d.KodeAkun",
                    'param' => 'INNER',
                ],
            ],
            'where' => [[
                's.NamaTransaksi' => 'AktivitasPro',
                's.JenisTransaksi' => 'Tunai',
            ]],
        ]);

        $status_jurnal = $this->lokasi->setting_jurnal_status();

        // update di tabel transaksi barang
        $updatetrbarang = [
            'ProdTglSelesai' => date('Y-m-d H:i'),
            'BiayaProduksi' => $biayaprod,
            'BeratKotor' => $this->input->post('BeratKotor'),
            'BeratBersih' => $this->input->post('BeratBersih')
        ];
        $res = $this->crud->update($updatetrbarang, ['NoTrans' => $kode], 'transaksibarang');

        // check & update status produksi di transkasi penjualan
        $trbarang = $this->crud->get_rows(
            [
                'select' => 'b.NoTrans, b.NoRefTrSistem, b.ProdTglSelesai, b.KodeBarang, ij.Qty, ij.HargaSatuan, ij.Total, ij.Deskripsi, br.SatuanBarang, ij.JenisBarang, ij.Kategory, b.GudangAsal, b.GudangTujuan, b.JmlProduksi, b.BiayaProduksi, j.TglSlipOrder, br.HargaJual, k.NoTransKas, k.KodeTahun, k.TanggalTransaksi, k.TotalTransaksi',
                'from' => 'transaksibarang b',
                'join' => [
                    [
                        'table' => ' itempenjualan ij',
                        'on' => "ij.KodeBarang = b.KodeBarang AND ij.IDTransJual = b.NoRefTrSistem",
                        'param' => 'LEFT',
                    ],
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
                    [
                        'table' => ' transaksikas k',
                        'on' => "k.NoRef_Sistem = b.NoTrans AND k.JenisTransaksiKas = 'BIAYA PRODUKSI'",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['NoRefTrSistem' => $kode2]],
            ]
        );
        $statusProduksi = 1;
        $i = 0;
        foreach ($trbarang as $key) {
            if (!($key['ProdTglSelesai'])) {
                $statusProduksi = 0;
            }

            $i++;
        }
        if ($statusProduksi == 1) {
            $transjual = $this->crud->get_one_row(
                [
                    'select' => '*',
                    'from' => 'transpenjualan',
                    'where' => [['IDTransJual' => $kode2]],
                ]
            );
            if ($transjual['TglSlipOrder']) {
                $dtpenjualan = $this->crud->update(['StatusProduksi' => 'SELESAI'], ['IDTransJual' => $kode2], 'transpenjualan');
            } else {
                $dtpenjualan = $this->crud->update(['StatusProses' => 'DONE', 'StatusProduksi' => 'SELESAI'], ['IDTransJual' => $kode2], 'transpenjualan');
            }

            // insert barang hasil produksi ketika semua barang selesai diproduksi
            $j = 0;
            $totalbiaya_prod    = 0;
            $totalbiaya_bb      = 0;
            $totalbiaya_akt     = 0;
            foreach ($trbarang as $key) {
                $getNoUrut = $this->db->from('itemtransaksibarang')
                ->where('NoTrans', $key['NoTrans'])
                ->select('NoUrut')
                ->order_by('NoUrut', 'desc')
                ->get()->row();
                $NoUrut = (int)$getNoUrut->NoUrut;
                $inserthasil = $this->db->insert('itemtransaksibarang', array(
                    'NoUrut'        => $NoUrut + 1,
                    'NoTrans'       => $key['NoTrans'],
                    'KodeBarang'    => $key['KodeBarang'],
                    'Qty'           => $key['JmlProduksi'],
                    'HargaSatuan'   => $key['HargaJual'],
                    'Total'         => $key['JmlProduksi'] * $key['HargaJual'],
                    'Deskripsi'     => $key['Deskripsi'],
                    'JenisStok'     => 'MASUK',
                    'GudangAsal'    => $key['GudangAsal'],
                    'GudangTujuan'  => $key['GudangTujuan'],
                    'SatuanBarang'  => $key['SatuanBarang'],
                    'JenisBarang'   => $key['JenisBarang'],
                    'Kategory'      => $key['Kategory'],
                    'IsHapus'       => 0,
                ));

                $stok[$j] = $this->lokasi->get_stok_asli($key['KodeBarang']);
                foreach ($stok as $stock) {
                    $hpp_prod[$j]['StokSistem'] = $stock['stok'];
                }

                $hppsistem[$j] = $this->lokasi->get_hpp_sistem($key['KodeBarang']);
                foreach ($hppsistem as $hpp) {
                    $hpp_prod[$j]['HPPSistem'] = $hpp;
                }

                $biayabahan[$j] = $this->lokasi->get_biaya_bahan_prod($key['NoTrans']);
                foreach ($biayabahan as $bb) {
                    $biaya[$j]['BiayaBahanProd'] = $bb['BiayaBahanProd'];
                }
                $biayaaktivitas[$j] = $this->lokasi->get_biaya_aktivitas($key['NoTrans']);
                foreach ($biayaaktivitas as $ba) {
                    $biaya[$j]['BiayaAktivitas'] = $ba['BiayaAktivitas'];
                }
                $biayaprodperbarang[$j]     = $bb['BiayaBahanProd'] + $ba['BiayaAktivitas'] + $key['BiayaProduksi'];
                foreach ($biayaprodperbarang as $biayaperbarang) {
                    $biaya[$j]['BiayaPerBarang'] = $biayaperbarang;
                }
                $biayaprodperitembarang[$j] = ($bb['BiayaBahanProd'] + $ba['BiayaAktivitas'] + $key['BiayaProduksi']) / $key['JmlProduksi'];
                foreach ($biayaprodperitembarang as $peritem) {
                    $biaya[$j]['BiayaPerItemBarang'] = $peritem;
                }

                // Update HPP Produksi pada tabel transaksi barang
                $this->crud->update(
                    [
                        'HPPProduksi' => (($stock['stok'] * $hpp) + ($key['JmlProduksi'] * $peritem)) / ($stock['stok'] + $key['JmlProduksi'])
                    ],
                    [
                        'NoTrans' => $key['NoTrans']
                    ],
                    'transaksibarang');

                // Update HPP hasil produksi (average) pada tabel master barang
                $this->db->where('KodeBarang', $key['KodeBarang']);
                $this->db->update('mstbarang', array(
                    'NilaiHPP' => (($stock['stok'] * $hpp) + ($key['JmlProduksi'] * $peritem)) / ($stock['stok'] + $key['JmlProduksi'])
                ));

                if ($status_jurnal == 'on') {
                    // jurnal proses produksi
                    if ($getakunpro) {
                        if ($key['NoTransKas']) {
                            $updatekas = $this->crud->update(['IsDijurnalkan' => 1], ['NoTransKas' => $key['NoTransKas']], 'transaksikas'); // update kas
                        }
                        $prefix2 = "JRN-" . date("Ym");
                        $insertjurnalpro = [
                            'IDTransJurnal' => $this->crud->get_kode([
                                'select'    => 'RIGHT(IDTransJurnal, 7) AS KODE',
                                'from'      => 'transjurnal',
                                'where'     => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                                'limit'     => 1,
                                'order_by'  => 'IDTransJurnal DESC',
                                'prefix'    => $prefix2
                            ]),
                            'KodeTahun' => $key['KodeTahun'],
                            'TglTransJurnal' => date("Y-m-d H:i"),
                            'TipeJurnal' => "UMUM",
                            'NarasiJurnal' => "Proses Produksi",
                            'NominalTransaksi' => isset($key['BiayaProduksi']) ? $key['BiayaProduksi'] : 0,
                            'NoRefTrans' => isset($key['NoTransKas']) ? $key['NoTransKas'] : '-',
                            'UserName' => $this->session->userdata('UserName')
                        ];
                        $insertjurnalproinduk = $this->crud->insert($insertjurnalpro, 'transjurnal'); // simpan data di tabel transjurnal

                        foreach ($getakunpro as $item) {
                            $insertitem = [
                                'NoUrut' => $item['NoUrut'],
                                'IDTransJurnal' => $insertjurnalpro['IDTransJurnal'],
                                'KodeTahun' => $insertjurnalpro['KodeTahun'],
                                'KodeAkun' => $item['KodeAkun'],
                                'NamaAkun' => $item['NamaAkun'],
                                'Debet' => ($item['JenisJurnal'] == 'Debet') ? $insertjurnalpro['NominalTransaksi'] : 0,
                                'Kredit' => ($item['JenisJurnal'] == 'Kredit') ? $insertjurnalpro['NominalTransaksi'] : 0,
                                'Uraian' => "Penjurnalan otomatis untuk Proses Produksi Barang dengan no ref: ".$key['NoTrans']
                            ];
                            $insertjurnalproitem[] = $this->crud->insert($insertitem, 'transjurnalitem'); // simpan data di tabel transjurnalitem
                        }
                    }

                    // jurnal bahan baku produksi
                    if ($getakunbahan) {
                        $prefix = "TRK-" . date("Ym");
                        $insertkasbahan['NoTransKas'] = $this->crud->get_kode([
                            'select' => 'RIGHT(NoTransKas, 7) AS KODE',
                            'from' => 'transaksikas',
                            'where' => [['LEFT(NoTransKas, 10) =' => $prefix]],
                            'limit' => 1,
                            'order_by' => 'NoTransKas DESC',
                            'prefix' => $prefix
                        ]);
                        $insertkasbahan['KodeTahun']         = $key['KodeTahun'];
                        $insertkasbahan['TanggalTransaksi']  = date("Y-m-d H:i");
                        $insertkasbahan['NoRef_Sistem']      = $key['NoTrans'];
                        $insertkasbahan['Uraian']            = 'Biaya bahan baku produksi';
                        $insertkasbahan['UserName']          = $this->session->userdata('UserName');
                        $insertkasbahan['TotalTransaksi']    = $bb['BiayaBahanProd'];
                        $insertkasbahan['JenisTransaksiKas'] = 'BIAYA PRODUKSI';
                        $insertkasbahan['IsDijurnalkan']     = 1;
                        $kas = $this->crud->insert($insertkasbahan, 'transaksikas'); // insert kas

                        $prefix2 = "JRN-" . date("Ym");
                        $insertjurnalbahan = [
                            'IDTransJurnal' => $this->crud->get_kode([
                                'select'    => 'RIGHT(IDTransJurnal, 7) AS KODE',
                                'from'      => 'transjurnal',
                                'where'     => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                                'limit'     => 1,
                                'order_by'  => 'IDTransJurnal DESC',
                                'prefix'    => $prefix2
                            ]),
                            'KodeTahun' => $insertkasbahan['KodeTahun'],
                            'TglTransJurnal' => date("Y-m-d H:i"),
                            'TipeJurnal' => "UMUM",
                            'NarasiJurnal' => "Bahan Baku Produksi",
                            'NominalTransaksi' => $insertkasbahan['TotalTransaksi'],
                            'NoRefTrans' => $insertkasbahan['NoTransKas'],
                            'UserName' => $this->session->userdata('UserName')
                        ];
                        $insertjurnalbahaninduk = $this->crud->insert($insertjurnalbahan, 'transjurnal'); // simpan data di tabel transjurnal

                        foreach ($getakunbahan as $item) {
                            $insertitembahan = [
                                'NoUrut' => $item['NoUrut'],
                                'IDTransJurnal' => $insertjurnalbahan['IDTransJurnal'],
                                'KodeTahun' => $insertjurnalbahan['KodeTahun'],
                                'KodeAkun' => $item['KodeAkun'],
                                'NamaAkun' => $item['NamaAkun'],
                                'Debet' => ($item['JenisJurnal'] == 'Debet') ? $insertjurnalbahan['NominalTransaksi'] : 0,
                                'Kredit' => ($item['JenisJurnal'] == 'Kredit') ? $insertjurnalbahan['NominalTransaksi'] : 0,
                                'Uraian' => "Penjurnalan otomatis untuk Bahan Baku Produksi dengan no ref: ".$key['NoTrans']
                            ];
                            $insertjurnalbahanitem[] = $this->crud->insert($insertitembahan, 'transjurnalitem'); // simpan data di tabel transjurnalitem
                        }
                    }

                    // jurnal biaya aktivitas produksi
                    if ($getakunakt) {
                        $prefix = "TRK-" . date("Ym");
                        $insertkasakt['NoTransKas'] = $this->crud->get_kode([
                            'select' => 'RIGHT(NoTransKas, 7) AS KODE',
                            'from' => 'transaksikas',
                            'where' => [['LEFT(NoTransKas, 10) =' => $prefix]],
                            'limit' => 1,
                            'order_by' => 'NoTransKas DESC',
                            'prefix' => $prefix
                        ]);
                        $insertkasakt['KodeTahun']         = $key['KodeTahun'];
                        $insertkasakt['TanggalTransaksi']  = date("Y-m-d H:i");
                        $insertkasakt['NoRef_Sistem']      = $key['NoTrans'];
                        $insertkasakt['Uraian']            = 'Biaya aktivitas produksi';
                        $insertkasakt['UserName']          = $this->session->userdata('UserName');
                        $insertkasakt['TotalTransaksi']    = $ba['BiayaAktivitas'];
                        $insertkasakt['JenisTransaksiKas'] = 'BIAYA PRODUKSI';
                        $insertkasakt['IsDijurnalkan']     = 1;
                        $kas = $this->crud->insert($insertkasakt, 'transaksikas'); // insert kas

                        $prefix2 = "JRN-" . date("Ym");
                        $insertjurnalakt = [
                            'IDTransJurnal' => $this->crud->get_kode([
                                'select'    => 'RIGHT(IDTransJurnal, 7) AS KODE',
                                'from'      => 'transjurnal',
                                'where'     => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                                'limit'     => 1,
                                'order_by'  => 'IDTransJurnal DESC',
                                'prefix'    => $prefix2
                            ]),
                            'KodeTahun' => $insertkasakt['KodeTahun'],
                            'TglTransJurnal' => date("Y-m-d H:i"),
                            'TipeJurnal' => "UMUM",
                            'NarasiJurnal' => "Biaya Aktivitas Produksi",
                            'NominalTransaksi' => $insertkasakt['TotalTransaksi'],
                            'NoRefTrans' => $insertkasakt['NoTransKas'],
                            'UserName' => $this->session->userdata('UserName')
                        ];
                        $insertjurnalaktinduk = $this->crud->insert($insertjurnalakt, 'transjurnal'); // simpan data di tabel transjurnal

                        foreach ($getakunakt as $item) {
                            $insertitemakt = [
                                'NoUrut' => $item['NoUrut'],
                                'IDTransJurnal' => $insertjurnalakt['IDTransJurnal'],
                                'KodeTahun' => $insertjurnalakt['KodeTahun'],
                                'KodeAkun' => $item['KodeAkun'],
                                'NamaAkun' => $item['NamaAkun'],
                                'Debet' => ($item['JenisJurnal'] == 'Debet') ? $insertjurnalakt['NominalTransaksi'] : 0,
                                'Kredit' => ($item['JenisJurnal'] == 'Kredit') ? $insertjurnalakt['NominalTransaksi'] : 0,
                                'Uraian' => "Penjurnalan otomatis untuk Biaya Aktivitas Produksi dengan no ref: ".$key['NoTrans']
                            ];
                            $insertjurnalaktitem[] = $this->crud->insert($insertitemakt, 'transjurnalitem'); // simpan data di tabel transjurnalitem
                        }
                    }
                }

                $totalbiaya_prod += $key['BiayaProduksi'];
                $totalbiaya_bb += $bb['BiayaBahanProd'];
                $totalbiaya_akt += $ba['BiayaAktivitas'];

                $j++;
            }

            if ($status_jurnal != 'on') {
                $prefix2 = "JRN-" . date("Ym");
                $insert1jurnal = [
                    'IDTransJurnal' => $this->crud->get_kode([
                        'select'    => 'RIGHT(IDTransJurnal, 7) AS KODE',
                        'from'      => 'transjurnal',
                        'where'     => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                        'limit'     => 1,
                        'order_by'  => 'IDTransJurnal DESC',
                        'prefix'    => $prefix2
                    ]),
                    'KodeTahun' => $this->akses->get_tahun_aktif(),
                    'TglTransJurnal' => date("Y-m-d H:i"),
                    'TipeJurnal' => "UMUM",
                    'NarasiJurnal' => "Proses Produksi",
                    'NominalTransaksi' => ((int)$totalbiaya_prod + (int)$totalbiaya_bb + (int)$totalbiaya_akt),
                    'NoRefTrans' => $kode2,
                    'UserName' => $this->session->userdata('UserName')
                ];
                $insert1jurnalinduk = $this->crud->insert($insert1jurnal, 'transjurnal'); // simpan data di tabel transjurnal
            }
        }


        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'tambah',
                'JenisTransaksi' => 'Proses Produksi',
                'Description' => 'proses produksi selesai ' . $kode
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil menyelesaikan proses produksi"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menyelesaikan proses produksi"
            ]);
        }
    }

    public function detail_old()
    {
        checkAccess($this->session->userdata('fiturview')[29]);
        $noreftrsistem        = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM SPK DETAIL
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'prosesprod';
            $data['title'] = 'Detail Proses Produksi';
            $data['view'] = 'transaksi/v_proses_produksi_detail';
            $data['scripts'] = 'transaksi/s_proses_produksi_detail';

            $dtinduk = [
                'select' => 'j.IDTransJual, j.TglSlipOrder, j.EstimasiSelesai, j.SPKTanggal, j.NoSlipOrder, j.SPKNomor, j.KodePerson, p.NamaPersonCP, b.NoRefTrSistem, b.Deskripsi, b.GudangAsal, ga.NamaGudang as NamaGudangAsal, b.GudangTujuan, gt.NamaGudang as NamaGudangTujuan, j.StatusProduksi, jr.IDTransJurnal, jr.NominalTransaksi, j.SPKDibuatOleh',
                'from' => 'transpenjualan j',
                'join' => [
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = j.KodePerson",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' transaksibarang b',
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
                        'table' => ' transjurnal jr',
                        'on' => "jr.NoRefTrans = j.IDTransJual AND jr.NarasiJurnal = 'Proses Produksi'",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['j.IDTransJual' => $noreftrsistem]],
                'group_by' => 'b.NoRefTrSistem',
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['NoRefTrSistem'] = $noreftrsistem;

            $idjurnal = isset($data['dtinduk']['IDTransJurnal']) ? $data['dtinduk']['IDTransJurnal'] : '';
            $itemjurnal = $this->lokasi->get_total_item_jurnal($idjurnal);
            $data['totaljurnaldebet'] = (int)$itemjurnal['Debet'];
            $data['totaljurnalkredit'] = (int)$itemjurnal['Kredit'];

            $data['status_jurnal'] = $this->lokasi->setting_jurnal_status();

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            $noreftrsistem   = $this->input->get('noreftrsistem');
            ## table
            $configData['table'] = 'transaksibarang b';

            $configData['where'] = [
                [
                    'b.TanggalTransaksi !=' => null,
                    'b.NoRefTrSistem' => $noreftrsistem,
                    'b.JenisTransaksi' => 'PRODUKSI',
                    'b.KodeBarang !=' => null,
                    'b.IsHapus' => 0,
                ]
            ];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (b.NoTrans LIKE '%$cari%' OR br.NamaBarang LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(j.SPKTanggal) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = b.KodeBarang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transpenjualan j',
                    'on' => "j.IDTransJual = b.NoRefTrSistem",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' itempenjualan ij',
                    'on' => "ij.KodeBarang = b.KodeBarang AND ij.IDTransJual = b.NoRefTrSistem",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ac',
                    'on' => "ac.NoTrans = b.NoTrans AND ac.JenisAktivitas = 'T. Cetak'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pc',
                    'on' => "pc.KodePegawai = ac.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ap',
                    'on' => "ap.NoTrans = b.NoTrans AND ap.JenisAktivitas = 'Potong'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pp',
                    'on' => "pp.KodePegawai = ap.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ak',
                    'on' => "ak.NoTrans = b.NoTrans AND ak.JenisAktivitas = 'Kasar'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pk',
                    'on' => "pk.KodePegawai = ak.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi cr',
                    'on' => "cr.NoTrans = b.NoTrans AND cr.JenisAktivitas = 'Bubut CR'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pcr',
                    'on' => "pcr.KodePegawai = cr.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi bt',
                    'on' => "bt.NoTrans = b.NoTrans AND bt.JenisAktivitas = 'Bubut T'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pt',
                    'on' => "pt.KodePegawai = bt.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ar',
                    'on' => "ar.NoTrans = b.NoTrans AND ar.JenisAktivitas = 'Bubut R'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pr',
                    'on' => "pr.KodePegawai = ar.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ah',
                    'on' => "ah.NoTrans = b.NoTrans AND ah.JenisAktivitas = 'Halus'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai ph',
                    'on' => "ph.KodePegawai = ah.KodePegawai",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.GudangAsal', 'b.BeratKotor', 'b.BeratBersih', 'b.KodeBarang', 'br.NamaBarang', 'b.NoRefTrSistem', 'j.IDTransJual', 'j.SPKNomor', 'j.SPKTanggal', 'ij.NoUrut', 'ij.Qty', 'b.KodeProduksi', 'b.JmlProduksi', 'pc.NamaPegawai as Cetak', 'pp.NamaPegawai as Potong', 'pk.NamaPegawai as Kasar', 'pcr.NamaPegawai as CR', 'pt.NamaPegawai as T', 'pr.NamaPegawai as R', 'ph.NamaPegawai as Halus'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['group_by'] = 'b.NoTrans';
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'b.NoTrans';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.GudangAsal', 'b.BeratKotor', 'b.BeratBersih', 'b.KodeBarang', 'br.NamaBarang', 'b.NoRefTrSistem', 'j.IDTransJual', 'j.SPKNomor', 'j.SPKTanggal', 'ij.NoUrut', 'ij.Qty', 'b.KodeProduksi', 'b.JmlProduksi', 'pc.NamaPegawai as Cetak', 'pp.NamaPegawai as Potong', 'pk.NamaPegawai as Kasar', 'pcr.NamaPegawai as CR', 'pt.NamaPegawai as T', 'pr.NamaPegawai as R', 'ph.NamaPegawai as Halus',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 29; //FiturID di tabel serverfitur
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
                $temp['Status'] = isset($temp['ProdTglSelesai']) ? 'SELESAI' : 'WIP';
                $temp['SPKTanggal'] = shortdate_indo(date('Y-m-d', strtotime($temp['SPKTanggal'])));
                $temp['TanggalTransaksi'] = shortdate_indo(date('Y-m-d', strtotime($temp['TanggalTransaksi'])));
                $temp['ProdTglSelesai'] = isset($temp['ProdTglSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($temp['ProdTglSelesai']))) : '-';
                $temp['Cetak'] = isset($temp['Cetak']) ? $temp['Cetak'] : '-';
                $temp['Potong'] = isset($temp['Potong']) ? $temp['Potong'] : '-';
                $temp['Kasar'] = isset($temp['Kasar']) ? $temp['Kasar'] : '-';
                $temp['CR'] = isset($temp['CR']) ? $temp['CR'] : '-';
                $temp['T'] = isset($temp['T']) ? $temp['T'] : '-';
                $temp['R'] = isset($temp['R']) ? $temp['R'] : '-';
                $temp['Halus'] = isset($temp['Halus']) ? $temp['Halus'] : '-';
                $temp['BeratKotor'] = isset($temp['BeratKotor']) ? $temp['BeratKotor'] : 0;
                $temp['BeratBersih'] = isset($temp['BeratBersih']) ? $temp['BeratBersih'] : 0;
                $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/proses_produksi/subdetail/' . base64_encode($temp['NoTrans'])) . '" type="button" title="Sub Detail Proses Produksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[29]);
        $noreftrsistem        = escape(base64_decode($this->uri->segment(4)));

        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu'] = 'prosesprod';
        $data['title'] = 'Detail Proses Produksi';
        $data['view'] = 'transaksi/v_proses_produksi_detail';
        $data['scripts'] = 'transaksi/s_proses_produksi_detail';

        $dtinduk = [
            'select' => 'j.IDTransJual, j.TglSlipOrder, j.EstimasiSelesai, j.SPKTanggal, j.NoSlipOrder, j.SPKNomor, j.KodePerson, p.NamaPersonCP, b.NoTrans, b.NoRefTrSistem, b.Deskripsi, b.GudangAsal, ga.NamaGudang as NamaGudangAsal, b.GudangTujuan, gt.NamaGudang as NamaGudangTujuan, j.StatusProduksi, jr.IDTransJurnal, jr.NominalTransaksi, j.SPKDibuatOleh, b.TanggalTransaksi, b.ProdTglSelesai',
            'from' => 'transpenjualan j',
            'join' => [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = j.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksibarang b',
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
                    'table' => ' transjurnal jr',
                    'on' => "jr.NoRefTrans = j.IDTransJual AND jr.NarasiJurnal = 'Proses Produksi'",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['j.IDTransJual' => $noreftrsistem]],
            'group_by' => 'b.NoRefTrSistem',
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
        $data['NoRefTrSistem'] = $noreftrsistem;

        $idjurnal = isset($data['dtinduk']['IDTransJurnal']) ? $data['dtinduk']['IDTransJurnal'] : '';
        $itemjurnal = $this->lokasi->get_total_item_jurnal($idjurnal);
        $data['totaljurnaldebet'] = (int)$itemjurnal['Debet'];
        $data['totaljurnalkredit'] = (int)$itemjurnal['Kredit'];

        $data['status_jurnal'] = $this->lokasi->setting_jurnal_status();

        $tgl = escape($this->input->get('tgl')) != '' ? escape($this->input->get('tgl')) : date('d-m-Y', strtotime('-30 days')).' - '.date("d-m-Y");
        $tgl = explode(" - ", $tgl);
        $tglawal = date('Y-m-d', strtotime($tgl[0]));
        $tglakhir = date('Y-m-d', strtotime($tgl[1]));

        $data['tglawal'] = date('d-m-Y', strtotime($tglawal));
        $data['tglakhir'] = date('d-m-Y', strtotime($tglakhir));

        $data['bahanbaku'] = $this->crud->get_one_row([
            'select' => 'SUM(Qty) AS TotalBahanBaku, SUM(Total) AS ModalBahan',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoTrans' => $data['dtinduk']['NoTrans'],
                'IsBahanBaku' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $data['barangjadi'] = $this->crud->get_one_row([
            'select' => 'SUM(Qty) AS TotalJadi',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoTrans' => $data['dtinduk']['NoTrans'],
                'IsBarangJadi' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $data['susut'] = (int)$data['bahanbaku']['TotalBahanBaku'] - (int)$data['barangjadi']['TotalJadi'];
        $data['presentase'] = (int)$data['bahanbaku']['TotalBahanBaku'] > 0 ? ((int)$data['barangjadi']['TotalJadi'] / (int)$data['bahanbaku']['TotalBahanBaku'] * 100) : 0;

        $barangproduksi = $this->crud->get_rows([
            'select' => 'i.NoUrut, i.NoTrans, i.KodeBarang, i.Qty, i.HargaSatuan, i.Total, i.Deskripsi, i.JenisStok, i.GudangAsal, i.GudangTujuan, i.SatuanBarang, i.JenisBarang, i.Kategory, i.IsBarangJadi, i.IsBahanBaku, i.ProdUkuran, i.ProdJmlDaun, i.IsHapus, b.TanggalTransaksi, b.NoRefTrSistem, br.NamaBarang, br.KodeManual, a.NoTrAktivitas, a.JenisAktivitas, a.KodePegawai, p.NamaPegawai, i.BeratKotor',
            'from' => 'itemtransaksibarang i',
            'join' => [
                [
                    'table' => 'transaksibarang b',
                    'on' => 'i.NoTrans = b.NoTrans',
                    'param' => 'INNER',
                ],
                [
                    'table' => 'mstbarang br',
                    'on' => 'i.KodeBarang = br.KodeBarang',
                    'param' => 'INNER',
                ],
                [
                    'table' => ' aktivitasproduksi a',
                    'on' => "i.NoTrans = a.NoTrans AND i.NoUrut = a.NoUrut",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai p',
                    'on' => "a.KodePegawai = p.KodePegawai",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [[
                'i.NoTrans' => $data['dtinduk']['NoTrans'],
                'i.IsBarangJadi' => 1,
                'i.IsHapus' => 0
            ]]
        ]);

        $ja = $this->crud->get_rows([
            'select' => '*',
            'from' => 'mstjenisaktivitas',
            'where' => [['IsAktif' => 1]],
            'order_by' => 'NoUrut',
        ]);
        $data['jenisaktivitas'] = $ja;

        $namajenis = [];
        foreach ($ja as $key) {
            $namajenis[] = $key['JenisAktivitas'];
        }

        $notrans = '';
        $nourut = '';
        $per_transaksi = [];
        foreach ($barangproduksi as $key) {
            if ($nourut != $key['NoUrut']) {
                $item = $key;
                unset($item['KodePegawai']);
                unset($item['NamaPegawai']);
                $notrans = $key['NoTrans'];
                $nourut = $key['NoUrut'];

                foreach ($namajenis as $key2) {
                    $namapegawai = $this->getjenispertrans2($barangproduksi, $key['NoTrans'], $key['NoUrut'], $key2);
                    $item = array_merge($item, [$key2 => $namapegawai]);
                }
                $per_transaksi[] = $item;
            }
        }
        $data['model'] = $per_transaksi;
        $data['total'] = count($per_transaksi);

        loadview($data);
    }

    public function list_produksi()
    {
        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu'] = 'listproduksi';
        $data['title'] = 'List Produksi';
        $data['view'] = 'transaksi/v_list_produksi';
        $data['scripts'] = 'transaksi/s_list_produksi';

        $tgl = escape($this->input->get('tgl')) != '' ? escape($this->input->get('tgl')) : date('d-m-Y', strtotime('-30 days')).' - '.date("d-m-Y");
        $tgl = explode(" - ", $tgl);
        $tglawal = date('Y-m-d', strtotime($tgl[0]));
        $tglakhir = date('Y-m-d', strtotime($tgl[1]));

        $data['tglawal'] = date('d-m-Y', strtotime($tglawal));
        $data['tglakhir'] = date('d-m-Y', strtotime($tglakhir));

        $ja = $this->crud->get_rows([
            'select' => '*',
            'from' => 'mstjenisaktivitas',
            'where' => [['IsAktif' => 1]],
            'order_by' => 'NoUrut',
        ]);
        $data['jenisaktivitas'] = $ja;

        $barangproduksi = $this->crud->get_rows([
            'select' => 'i.NoUrut, i.NoTrans, i.KodeBarang, i.Qty, i.HargaSatuan, i.Total, i.Deskripsi, i.JenisStok, i.GudangAsal, i.GudangTujuan, i.SatuanBarang, i.JenisBarang, i.Kategory, i.IsBarangJadi, i.IsBahanBaku, i.ProdUkuran, i.ProdJmlDaun, i.IsHapus, b.TanggalTransaksi, b.NoRefTrSistem, br.NamaBarang, br.KodeManual, a.NoTrAktivitas, a.JenisAktivitas, a.KodePegawai, p.NamaPegawai, b.ProdTglSelesai, j.SPKDibuatOleh, i.BeratKotor',
            'from' => 'itemtransaksibarang i',
            'join' => [
                [
                    'table' => 'transaksibarang b',
                    'on' => 'i.NoTrans = b.NoTrans',
                    'param' => 'INNER',
                ],
                [
                    'table' => 'transpenjualan j',
                    'on' => 'j.IDTransJual = b.NoRefTrSistem',
                    'param' => 'LEFT',
                ],
                [
                    'table' => 'mstbarang br',
                    'on' => 'i.KodeBarang = br.KodeBarang',
                    'param' => 'INNER',
                ],
                [
                    'table' => ' aktivitasproduksi a',
                    'on' => "i.NoTrans = a.NoTrans AND i.NoUrut = a.NoUrut",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai p',
                    'on' => "a.KodePegawai = p.KodePegawai",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [
                [
                    'i.IsBarangJadi' => 1,
                    'i.IsHapus' => 0
                ],
                "DATE(b.TanggalTransaksi) BETWEEN '$tglawal' AND '$tglakhir'"
            ]
        ]);

        $namajenis = [];
        foreach ($ja as $key) {
            $namajenis[] = $key['JenisAktivitas'];
        }

        $notrans = '';
        $nourut = '';
        $per_transaksi = [];
        foreach ($barangproduksi as $key) {
            if ($nourut != $key['NoUrut']) {
                $item = $key;
                unset($item['KodePegawai']);
                unset($item['NamaPegawai']);
                $notrans = $key['NoTrans'];
                $nourut = $key['NoUrut'];

                foreach ($namajenis as $key2) {
                    $namapegawai = $this->getjenispertrans2($barangproduksi, $key['NoTrans'], $key['NoUrut'], $key2);
                    $item = array_merge($item, [$key2 => $namapegawai]);
                }
                $per_transaksi[] = $item;
            }
        }
        $data['model'] = $per_transaksi;
        $data['total'] = count($per_transaksi);

        loadview($data);
    }

    public function getjenispertrans($array, $notrans, $ja)
    {
        $namapeg = [];
        foreach ($array as $key) {
            if ($notrans == $key['NoTrans'] && $ja == $key['JenisAktivitas']) {
                $namapeg[] = $key['NamaPegawai'];
            }
        }

        return $namapeg;
    }

    public function getjenispertrans2($array, $notrans, $nourut, $ja)
    {
        $namapeg = [];
        foreach ($array as $key) {
            if ($notrans == $key['NoTrans'] && $nourut == $key['NoUrut'] && $ja == $key['JenisAktivitas']) {
                $namapeg[] = $key['NamaPegawai'];
            }
        }

        return $namapeg;
    }

    public function list_produksi_old()
    {
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'listproduksi';
            $data['title'] = 'List Produksi';
            $data['view'] = 'transaksi/v_list_produksi';
            $data['scripts'] = 'transaksi/s_list_produksi';

            $data['jenisaktivitas'] = $this->crud->get_rows([
                'select' => '*',
                'from' => 'mstjenisaktivitas',
                'where' => [['IsAktif' => 1]],
                'order_by' => 'NoUrut',
            ]);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transaksibarang b';

            $configData['where'] = [
                [
                    'b.TanggalTransaksi !=' => null,
                    'b.JenisTransaksi' => 'PRODUKSI',
                    'b.KodeBarang !=' => null,
                    'b.IsHapus' => 0,
                ]
            ];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (b.NoTrans LIKE '%$cari%' OR br.NamaBarang LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(j.SPKTanggal) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = b.KodeBarang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transpenjualan j',
                    'on' => "j.IDTransJual = b.NoRefTrSistem",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' itempenjualan ij',
                    'on' => "ij.KodeBarang = b.KodeBarang AND ij.IDTransJual = b.NoRefTrSistem",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ac',
                    'on' => "ac.NoTrans = b.NoTrans AND ac.JenisAktivitas = 'T. Cetak'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pc',
                    'on' => "pc.KodePegawai = ac.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ap',
                    'on' => "ap.NoTrans = b.NoTrans AND ap.JenisAktivitas = 'Potong'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pp',
                    'on' => "pp.KodePegawai = ap.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ak',
                    'on' => "ak.NoTrans = b.NoTrans AND ak.JenisAktivitas = 'Kasar'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pk',
                    'on' => "pk.KodePegawai = ak.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi cr',
                    'on' => "cr.NoTrans = b.NoTrans AND cr.JenisAktivitas = 'Bubut CR'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pcr',
                    'on' => "pcr.KodePegawai = cr.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi bt',
                    'on' => "bt.NoTrans = b.NoTrans AND bt.JenisAktivitas = 'Bubut T'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pt',
                    'on' => "pt.KodePegawai = bt.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ar',
                    'on' => "ar.NoTrans = b.NoTrans AND ar.JenisAktivitas = 'Bubut R'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pr',
                    'on' => "pr.KodePegawai = ar.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ah',
                    'on' => "ah.NoTrans = b.NoTrans AND ah.JenisAktivitas = 'Halus'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai ph',
                    'on' => "ph.KodePegawai = ah.KodePegawai",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.GudangAsal', 'b.BeratKotor', 'b.BeratBersih', 'b.KodeBarang', 'br.NamaBarang', 'b.NoRefTrSistem', 'j.IDTransJual', 'j.SPKDibuatOleh', 'j.SPKNomor', 'j.SPKTanggal', 'ij.NoUrut', 'ij.Qty', 'b.KodeProduksi', 'b.JmlProduksi', 'pc.NamaPegawai as Cetak', 'pp.NamaPegawai as Potong', 'pk.NamaPegawai as Kasar', 'pcr.NamaPegawai as CR', 'pt.NamaPegawai as T', 'pr.NamaPegawai as R', 'ph.NamaPegawai as Halus'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            // $configData['group_by'] = 'b.NoTrans';
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'b.NoTrans';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.GudangAsal', 'b.BeratKotor', 'b.BeratBersih', 'b.KodeBarang', 'br.NamaBarang', 'b.NoRefTrSistem', 'j.IDTransJual', 'j.SPKDibuatOleh', 'j.SPKNomor', 'j.SPKTanggal', 'ij.NoUrut', 'ij.Qty', 'b.KodeProduksi', 'b.JmlProduksi', 'pc.NamaPegawai as Cetak', 'pp.NamaPegawai as Potong', 'pk.NamaPegawai as Kasar', 'pcr.NamaPegawai as CR', 'pt.NamaPegawai as T', 'pr.NamaPegawai as R', 'ph.NamaPegawai as Halus',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 29; //FiturID di tabel serverfitur
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
                $temp['Status'] = isset($temp['ProdTglSelesai']) ? 'SELESAI' : 'WIP';
                $temp['SPKTanggal'] = shortdate_indo(date('Y-m-d', strtotime($temp['SPKTanggal'])));
                $temp['TanggalTransaksi'] = shortdate_indo(date('Y-m-d', strtotime($temp['TanggalTransaksi'])));
                $temp['ProdTglSelesai'] = isset($temp['ProdTglSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($temp['ProdTglSelesai']))) : '-';
                $temp['Cetak'] = isset($temp['Cetak']) ? $temp['Cetak'] : '-';
                $temp['Potong'] = isset($temp['Potong']) ? $temp['Potong'] : '-';
                $temp['Kasar'] = isset($temp['Kasar']) ? $temp['Kasar'] : '-';
                $temp['CR'] = isset($temp['CR']) ? $temp['CR'] : '-';
                $temp['T'] = isset($temp['T']) ? $temp['T'] : '-';
                $temp['R'] = isset($temp['R']) ? $temp['R'] : '-';
                $temp['Halus'] = isset($temp['Halus']) ? $temp['Halus'] : '-';
                $temp['BeratKotor'] = isset($temp['BeratKotor']) ? $temp['BeratKotor'] : 0;
                $temp['BeratBersih'] = isset($temp['BeratBersih']) ? $temp['BeratBersih'] : 0;
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }
}

