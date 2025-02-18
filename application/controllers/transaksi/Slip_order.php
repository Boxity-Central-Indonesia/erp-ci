<?php
defined('BASEPATH') or exit('No direct script access allowed');

class slip_order extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[23]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[23]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'sliporder';
            $data['title'] = 'Transaksi Slip Order';
            $data['view'] = 'transaksi/v_slip_order';
            $data['scripts'] = 'transaksi/s_slip_order';

            $customer = [
                'select' => '*',
                'from' => 'mstperson',
                'where' => [
                    [
                        'IsAktif' => 1,
                        'JenisPerson' => 'CUSTOMER'
                    ]
                ],
                'order_by' => 'KodePerson'
            ];
            $data['customer'] = $this->crud->get_rows($customer);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transpenjualan j';

            $configData['where'] = [
                [
                    'j.TglSlipOrder !=' => null,
                    'LEFT(j.IDTransJual, 3) =' => 'SPO',
                ]
            ];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cariso');
            if ($cari != '') {
                $configData['filters'][] = " (j.IDTransJual LIKE '%$cari%' OR j.NoSlipOrder LIKE '%$cari%' OR j.StatusProses LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tglso'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(j.TglSlipOrder) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = j.KodePerson",
                    'param' => 'LEFT',

                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.IDTransJual', 'j.NoSlipOrder', 'j.TglSlipOrder', 'j.EstimasiSelesai', 'j.SODibuatOleh', 'j.TotalNilaiBarang', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.KodePerson', 'p.NamaPersonCP'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'j.IDTransJual';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'j.IDTransJual', 'j.NoSlipOrder', 'j.TglSlipOrder', 'j.EstimasiSelesai', 'j.SODibuatOleh', 'j.TotalNilaiBarang', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.KodePerson', 'p.NamaPersonCP',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 23; //FiturID di tabel serverfitur
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
                $TotalNilaiBarang = $record->TotalNilaiBarang > 0 ? $record->TotalNilaiBarang : 0;
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['TotalTagihan'] = $TotalTagihan;
                $temp['TotalNilaiBarang'] = $TotalNilaiBarang;
                $temp['Total'] = $TotalTagihan > 0 ? $TotalTagihan : $TotalNilaiBarang;
                $temp['TglSlipOrder'] = shortdate_indo(date('Y-m-d', strtotime($temp['TglSlipOrder']))) . ' ' . date('H:i', strtotime($temp['TglSlipOrder']));
                $statusproses = ($temp['StatusProses'] == 'SO' && $temp['EstimasiSelesai'] == null) ? 1 : 0;
                if ($canEdit == 1 && $canDelete == 1 && $statusproses == 1) {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/slip_order/detail/' . base64_encode($temp['IDTransJual'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a class="btneditso" type="button" href="#" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransJual'] . ' class="btnhapusso" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a hidden class="btn-sm btn-warning btnspk" href="#" data-kode="' . $temp['IDTransJual'] . '" title=""><span aria-hidden="true">Cetak SPK</span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1 && $statusproses == 1) {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/slip_order/detail/' . base64_encode($temp['IDTransJual'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a class="btneditso" type="button" href="#" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' .
                    '&nbsp;&nbsp;&nbsp;<a hidden class="btn-sm btn-warning btnspk" href="#" data-kode="' . $temp['IDTransJual'] . '" title=""><span aria-hidden="true">Cetak SPK</span></a>';
                } elseif ($canDelete == 1 && $canEdit == 1 && $statusproses == 1) {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/slip_order/detail/' . base64_encode($temp['IDTransJual'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransJual'] . ' class="btnhapusso" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a hidden class="btn-sm btn-warning btnspk" href="#" data-kode="' . $temp['IDTransJual'] . '" title=""><span aria-hidden="true">Cetak SPK</span></a>';
                } else {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/slip_order/detail/' . base64_encode($temp['IDTransJual'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a hidden class="btn-sm btn-warning btnspk" href="#" data-kode="' . $temp['IDTransJual'] . '" title=""><span aria-hidden="true">Cetak SPK</span></a>';
                }
                $temp['EstimasiSelesai'] = shortdate_indo(date('Y-m-d', strtotime($temp['EstimasiSelesai']))) . ' ' . date('H:i', strtotime($temp['EstimasiSelesai']));
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpan()
    {
        $insertdata = $this->input->post();
        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('IDTransJual') != null && $this->input->post('IDTransJual') != '')) {
            $prefix = "SPO-" . date("Ym");
            $insertdata['IDTransJual'] = $this->crud->get_kode([
                'select' => 'RIGHT(IDTransJual, 7) AS KODE',
                'where' => [['LEFT(IDTransJual, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'IDTransJual DESC',
                'prefix' => $prefix
            ]);
            $insertdata['SODibuatOleh']     = $this->session->userdata('ActualName');
            $insertdata['TotalNilaiBarang'] = 0;
            $insertdata['TotalTagihan']     = 0;
            $insertdata['StatusProses']     = 'SO';
            $insertdata['StatusKirim']      = 'BELUM';
            $insertdata['StatusBayar']      = 'BELUM';
            $aksi = 'tambah';
            $isEdit = false;
        } else {
            $aksi = 'edit';
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'transpenjualan');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('IDTransJual') : $insertdata['IDTransJual'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Transaksi Slip Order',
                'Description' => $ket . ' data transaksi slip order ' . $id
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data"),
                'id' => $insertdata['IDTransJual'],
                'action' => $aksi
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
        $kode  = $this->input->get('IDTransJual');

        $statusproses = $this->crud->get_one_row([
            'select' => 'StatusProses',
            'from' => 'transpenjualan',
            'where' => [['IDTransJual' => $kode]],
        ]);

        $countBarang = $this->crud->get_count([
            'select' => '*',
            'from' => 'transaksibarang',
            'where' => [['NoRefTrSistem' => $kode]],
        ]);

        $trKas = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transaksikas',
            'where' => [['NoRef_Sistem' => $kode]],
        ]);


        if ($statusproses['StatusProses'] == 'SPK') {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menghapus data! Item penjualan sedang dalam proses produksi."
            ]);
        } else {
            ## Menghapus data di tabel tranksaksi kas
            if ($trKas) {
                $kas = $this->crud->delete(['NoRef_Sistem' => $kode], 'transaksikas');

                $trJurnal = $this->crud->get_one_row([
                    'select' => '*',
                    'from' => 'transjurnal',
                    'where' => [['NoRefTrans' => $trKas['NoTransKas']]],
                ]);

                ## Menghapus data di tabel jurnal dan item jurnal
                if ($trJurnal) {
                    $itemJurnal = $this->crud->get_rows([
                        'select' => '*',
                        'from' => 'transjurnalitem',
                        'where' => [['IDTransJurnal' => $trJurnal['IDTransJurnal']]],
                    ]);

                    if (count($itemJurnal) > 0) {
                        $item = $this->crud->delete(['IDTransJurnal' => $trJurnal['IDTransJurnal']], 'transjurnalitem');
                    }

                    $jurnal = $this->crud->delete(['IDTransJurnal' => $trJurnal['IDTransJurnal']], 'transjurnal');
                }
            }

            ## Menghapus data di tabel tr barang
            if ($countBarang > 0) {
                $brg = $this->crud->delete(['NoRefTrSistem' => $kode], 'transaksibarang');
            }

            ## Menghapus data di tabel item penjualan
            $item = $this->crud->delete(['IDTransJual' => $kode], 'itempenjualan');

            $res = $this->crud->delete(['IDTransJual' => $kode], 'transpenjualan');
            if ($res) {
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => 'hapus',
                    'JenisTransaksi' => 'Transaksi Slip Order',
                    'Description' => 'hapus data transaksi slip order ' . $kode
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

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[23]);
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_jual';
            $data['title'] = 'Detail Transaksi Slip Order';
            $data['view'] = 'transaksi/v_slip_order_detail';
            $data['scripts'] = 'transaksi/s_slip_order_detail';

            $dtbarang = [
                'select' => '*',
                'from' => 'mstbarang b',
                'join' => [[
                    'table' => 'mstjenisbarang j',
                    'on' => "b.KodeJenis = j.KodeJenis",
                    'param' => 'LEFT'
                ]],
                'where' => [
                    [
                        'b.IsAktif' => 1
                    ],
                    "j.NamaJenisBarang LIKE '%BARANG JADI%'"
                ]
            ];
            $data['dtbarang'] = $this->crud->get_rows($dtbarang);

            $dtbahan = [
                'select' => '*',
                'from' => 'mstbarang b',
                'join' => [[
                    'table' => 'mstjenisbarang j',
                    'on' => "b.KodeJenis = j.KodeJenis",
                    'param' => 'LEFT'
                ]],
                'where' => [
                    [
                        'b.IsAktif' => 1
                    ],
                    "j.NamaJenisBarang NOT LIKE '%BARANG JADI%'"
                ]
            ];
            $data['dtbahan'] = $this->crud->get_rows($dtbahan);

            $dtinduk = [
                'select' => 't.IDTransJual, t.TglSlipOrder, t.EstimasiSelesai, t.NoSlipOrder, t.SODibuatOleh, t.StatusProses, t.PPN, t.DiskonBawah, t.NominalBelumPajak, t.TotalTagihan, t.TanggalJatuhTempo, p.KodePerson, p.NamaPersonCP, p.NamaUsaha, k.NoTransKas, k.TotalTransaksi, t.KodeGudang, g.NamaGudang',
                'from' => 'transpenjualan t',
                'join' => [
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = t.KodePerson",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' transaksikas k',
                        'on' => "k.NoRef_Sistem = t.IDTransJual AND k.JenisTransaksiKas = 'DP PENJUALAN'",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' mstgudang g',
                        'on' => "g.KodeGudang = t.KodeGudang",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['t.IDTransJual' => $idtransjual]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['jatuhtempo'] = isset($data['dtinduk']['TanggalJatuhTempo']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['TanggalJatuhTempo']))) : '';
            $data['IDTransJual'] = $idtransjual;

            $item = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'itempenjualan',
                    'where' => [['IDTransJual' => $idtransjual]],
                ]
            );

            $data['countItem'] = 0;
            $tanpadiskon = 0;
            $tagihanawal = 0;
            if (count($item) > 0) {
                $data['countItem'] = 1;
                foreach ($item as $items) {
                    $tanpadiskon += $items['HargaSatuan'] * $items['Qty'];
                    $tagihanawal += ($items['HargaSatuan'] - $items['Diskon']) * $items['Qty'];
                }
            }
            $data['tanpadiskon'] = $tanpadiskon;
            $data['tagihanawal'] = $tagihanawal;

            if ($data['dtinduk']['NoTransKas']) {
                $where = ['j.NoRefTrans' => $data['dtinduk']['NoTransKas']];
            } else {
                $where = [
                    'j.NoRefTrans' => $idtransjual,
                    'LEFT(j.NarasiJurnal, 11) =' => 'Transaksi P'
                ];
            }
            $dtjurnal = $this->crud->get_one_row([
                'select' => 'IDTransJurnal, NominalTransaksi, NoRefTrans',
                'from' => 'transjurnal j',
                'where' => [$where],
            ]);
            $data['idtransjurnal'] = isset($dtjurnal['IDTransJurnal']) ? $dtjurnal['IDTransJurnal'] : '';
            $itemjurnal = $this->lokasi->get_total_item_jurnal($data['idtransjurnal']);
            $data['nominaltransaksi']   = isset($dtjurnal['NominalTransaksi']) ? (int)$dtjurnal['NominalTransaksi'] : 0;
            $data['totaljurnaldebet']   = (int)$itemjurnal['Debet'];
            $data['totaljurnalkredit']  = (int)$itemjurnal['Kredit'];

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $idtransjual   = $this->input->get('idtransjual');
            $configData['table'] = 'itempenjualan i';
            $configData['where'] = [['i.IDTransJual'  => $idtransjual]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.NoUrut LIKE '%$cari%' OR i.Spesifikasi LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transpenjualan t',
                    'on' => "t.IDTransJual = i.IDTransJual",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = t.KodeGudang",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.IDTransJual', 'i.NoUrut', 'i.JenisBarang', 'i.Kategory', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.StatusProses', 't.DiskonBawah', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'i.AdditionalName', 'i.Diskon', 'i.ProdUkuran', 'i.ProdJmlDaun', 'i.SatuanPenjualan', 't.KodeGudang', 'g.NamaGudang', 't.EstimasiSelesai'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.IDTransJual', 'i.NoUrut', 'i.JenisBarang', 'i.Kategory', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.StatusProses', 't.DiskonBawah', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'i.AdditionalName', 'i.Diskon', 'i.ProdUkuran', 'i.ProdJmlDaun', 'i.SatuanPenjualan', 't.KodeGudang', 'g.NamaGudang', 't.EstimasiSelesai',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 23; //FiturID di tabel serverfitur
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
                $temp['Barang'] = isset($temp['AdditionalName']) ? $temp['AdditionalName'] : $temp['NamaBarang'];
                $dtdraft = $this->getDraft($temp['IDTransJual'], $temp['NoUrut']);
                $temp['dtdraft'] = isset($dtdraft) ? $dtdraft : 0;
                $temp['countdraft'] = count($temp['dtdraft']);
                $stok = $this->lokasi->get_stok_per_gudang($temp['KodeGudang'], $temp['KodeBarang']);
                $temp['Stok'] = isset($stok['stok']) ? $stok['stok'] : 0;
                if ($canEdit == 1 && $canDelete == 1 && $temp['StatusProses'] != null) {
                    if ($temp['StatusProses'] == 'SO' && $temp['EstimasiSelesai'] == null) {
                        $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($temp) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransJual'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                    } else {
                        // $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($temp) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                        $temp['btn_aksi'] = '';
                    }
                } elseif ($canEdit == 1 && $canDelete != 1 && $temp['StatusProses'] != null) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($temp) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1 && $temp['StatusProses'] == 'SO') {
                    if ($temp['EstimasiSelesai'] == null) {
                        $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['IDTransJual'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                    } else {
                        $temp['btn_aksi'] = '';
                    }
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    function getDraft($IDTransJual, $NoUrut)
    {
        $sql = "SELECT d.*, br.NamaBarang, j.StatusProses
            FROM draftbahanproduksi d
            LEFT JOIN mstbarang br ON d.KodeBarang = br.KodeBarang
            LEFT JOIN transpenjualan j ON d.IDTransJual = j.IDTransJual
            WHERE d.IDTransJual = '$IDTransJual'
            AND d.NoUrut = '$NoUrut'";
        return $this->db->query($sql)->result_array();
    }

    public function cetakdetail()
    {
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('transaksi/slip_order/detail/') . $this->uri->segment(4);

        $dtinduk = [
            'select' => 't.IDTransJual, t.TglSlipOrder, t.EstimasiSelesai, t.NoSlipOrder, t.SODibuatOleh, t.StatusProses, p.KodePerson, p.NamaPersonCP, p.NamaUsaha',
            'from' => 'transpenjualan t',
            'join' => [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = t.KodePerson",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['t.IDTransJual' => $idtransjual]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);

        $sql = [
            'select' => 'i.IDTransJual, i.NoUrut, i.JenisBarang, i.Kategory, i.Spesifikasi, i.HargaSatuan, i.Qty, i.Total, i.SatuanBarang, i.Deskripsi,  i.KodeBarang, br.NamaBarang, br.SatuanBarang as satuanAsal, br.Spesifikasi as spesifikasiAsal, br.HargaBeliTerakhir, i.AdditionalName, i.ProdUkuran, i.ProdJmlDaun',
            'from' => 'itempenjualan i',
            'join' => [
                [
                    'table' => ' mstbarang br',
                    'on' => " br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['i.IDTransJual' => $idtransjual]],
        ];
        $data['model'] = $this->crud->get_rows($sql);

        $this->load->library('Pdf');
        $this->load->view('transaksi/v_slip_order_detail_cetak', $data);
    }

    public function simpandetail()
    {
        $insertdata = $this->input->post();
        unset($insertdata['Diskon']);
        unset($insertdata['KodeBahan']);
        unset($insertdata['QtyBahan']);
        unset($insertdata['SatuanBahan']);
        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('NoUrut') != null && $this->input->post('NoUrut') != '')) {
            $isEdit = false;
            $getNoUrut = $this->db->from('itempenjualan')
            ->where('IDTransJual', $this->input->post('IDTransJual'))
            ->select('NoUrut')
            ->order_by('NoUrut', 'desc')
            ->get()->row();
            if ($getNoUrut) {
                $NoUrut = (int)$getNoUrut->NoUrut;
            } else {
                $NoUrut = 0;
            }
            $insertdata['NoUrut']       = $NoUrut + 1;
            $insertdata['Qty']          = str_replace(['.', ','], ['', '.'], $this->input->post('Qty'));
            $insertdata['HargaSatuan']  = 0;
            $insertdata['Diskon']       = 0;
            $insertdata['Total']        = 0;

            $res = $this->crud->insert($insertdata, 'itempenjualan');

            $kodebahan = $this->input->post('KodeBahan');
            $qtybahan = $this->input->post('QtyBahan');
            $satuanbahan = $this->input->post('SatuanBahan');
            $no = 1;
            foreach ($kodebahan as $key => $value) {
                if ($value != '' && $qtybahan[$key] != '') {
                    $draft = [
                        'DraftID'       => $no,
                        'IDTransJual'   => $this->input->post('IDTransJual'),
                        'NoUrut'        => $insertdata['NoUrut'],
                        'KodeBarang'    => $value,
                        'Qty'           => $qtybahan[$key],
                        'SatuanBarang'  => $satuanbahan[$key]
                    ];
                    $insertdraft[] = $this->crud->insert($draft, 'draftbahanproduksi');
                }
                $no++;
            }
        } else {
            $isEdit = true;
            $updatedata['Qty']              = str_replace(['.', ','], ['', '.'], $this->input->post('Qty'));
            $updatedata['AdditionalName']   = $this->input->post('AdditionalName');
            $updatedata['ProdUkuran']       = $this->input->post('ProdUkuran');
            $updatedata['ProdJmlDaun']      = $this->input->post('ProdJmlDaun');
            $updatedata['Deskripsi']        = $this->input->post('Deskripsi');

            $where = [
                'IDTransJual' => $this->input->post('IDTransJual'),
                'NoUrut' => $this->input->post('NoUrut')
            ];

            $res = $this->crud->update($updatedata, $where, 'itempenjualan');

            // delete data di tabel draft bahan produksi
            $dltdraft = $this->crud->delete($where, 'draftbahanproduksi');

            // insert data di tabel draft bahan produksi
            $kodebahan = $this->input->post('KodeBahan');
            $qtybahan = $this->input->post('QtyBahan');
            $satuanbahan = $this->input->post('SatuanBahan');
            $no = 0;
            foreach ($kodebahan as $key => $value) {
                if ($no > 0) {
                    if ($value != '' && $qtybahan[$key] != '') {
                        $draft = [
                            'DraftID'       => $no,
                            'IDTransJual'   => $this->input->post('IDTransJual'),
                            'NoUrut'        => $this->input->post('NoUrut'),
                            'KodeBarang'    => $value,
                            'Qty'           => $qtybahan[$key],
                            'SatuanBarang'  => $satuanbahan[$key]
                        ];
                        $insertdraft[] = $this->crud->insert($draft, 'draftbahanproduksi');
                    }
                }

                $no++;
            }
        }

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

    public function hapusdetail()
    {
        $kode  = $this->input->get('IDTransJual');
        $kode2 = $this->input->get('NoUrut');
        $induk = $this->crud->get_one_row(
            [
                'select'=> '*',
                'from'  => 'transpenjualan',
                'where' => [['IDTransJual' => $kode]]
            ]
        );
        $child = $this->crud->get_one_row(
            [
                'select'=> '*',
                'from'  => 'itempenjualan',
                'where' => [['IDTransJual' => $kode, 'NoUrut' => $kode2]]
            ]
        );

        // Mengurangi total ke total nilai barang di tabel transaksi penjualan
        $totaltagihan = $induk['TotalNilaiBarang'] - $child['Total'];
        $updatetagihan = $this->crud->update(['TotalNilaiBarang' => $totaltagihan], ['IDTransJual' => $kode], 'transpenjualan');

        // hapus di tabel draft
        $deletedraft = $this->crud->delete(['IDTransJual' => $kode, 'NoUrut' => $kode2], 'draftbahanproduksi');
        $res = $this->crud->delete(['IDTransJual' => $kode, 'NoUrut' => $kode2], 'itempenjualan');
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

    public function simpanpenjualan()
    {
        $updatedata = [
            'TglSlipOrder'      => $this->input->post('TglSlipOrder'),
            'EstimasiSelesai'   => $this->input->post('EstimasiSelesai'),
            'TanggalJatuhTempo' => $this->input->post('TanggalJatuhTempo')
        ];

        $res = $this->crud->update($updatedata, ['IDTransJual' => $this->input->post('IDTransJual')], 'transpenjualan');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'edit',
                'JenisTransaksi' => 'Transaksi Slip Order',
                'Description' => 'update data transaksi slip order ' . $this->input->post('IDTransJual')
            ]);
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

    public function jmlproduksi()
    {
        $kode = $this->input->get('IDTransJual');

        $trbarang = $this->crud->get_count(
            [
                'select' => '*',
                'from' => 'transaksibarang',
                'where' => [['NoRefTrSistem' => $kode]],
            ]
        );
        $item = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'itempenjualan i',
                'join' => [[
                    'table' => 'transpenjualan j',
                    'on' => "j.IDTransJual = i.IDTransJual",
                    'param' => 'INNER',
                ]],
                'where' => [['i.IDTransJual' => $kode]],
            ]
        );

        if ($trbarang == 0) {
            $prefix = "TBR-" . date("Ym");
            $i = 0;
            foreach ($item as $key) {
                $notrans = $this->crud->get_kode([
                    'select' => 'RIGHT(NoTrans, 7) AS KODE',
                    'from' => 'transaksibarang',
                    'where' => [['LEFT(NoTrans, 10) =' => $prefix]],
                    'limit' => 1,
                    'order_by' => 'NoTrans DESC',
                    'prefix' => $prefix
                ]);

                $barang = $this->crud->get_one_row([
                    'select' => 'LEFT(NamaBarang, 1) AS initial',
                    'from' => 'mstbarang',
                    'where' => [['KodeBarang' => $key['KodeBarang']]],
                ]);
                $inisial = $barang['initial'];

                $kodeprod = $this->crud->get_kode_produksi([
                    'select' => 'RIGHT(KodeProduksi, 7) AS KODE',
                    'from' => 'transaksibarang',
                    'limit' => 1,
                    'prefix' => $inisial
                ]);

                // Insert transaksi barang
                $stok = $this->lokasi->get_stok_per_gudang($key['KodeGudang'], $key['KodeBarang']);
                $stock = isset($stok['stok']) ? $stok['stok'] : 0;
                $qty = $key['Qty'];
                $jumlahproduksi = $qty - $stock;
                if ($jumlahproduksi > 0) {
                    $insertbarang = [
                        'NoTrans'           => $notrans,
                        'UserName'          => $this->session->userdata('UserName'),
                        'Deskripsi'         => $key['Deskripsi'],
                        'JenisTransaksi'    => 'PRODUKSI',
                        'NoRefTrSistem'     => $kode,
                        'KodeBarang'        => $key['KodeBarang'],
                        'KodeProduksi'      => $kodeprod,
                        'JmlProduksi'       => $jumlahproduksi,
                        'IsHapus'           => 0,
                    ];

                    $res[] = $this->crud->insert($insertbarang, 'transaksibarang');
                }

                $i++;
            }
        }

        if ($kode) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil",
                'id' => $kode
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal"
            ]);
        }
    }

    public function detailspk()
    {
        checkAccess($this->session->userdata('fiturview')[23]);
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_jual';
            $data['title'] = 'Cetak SPK';
            $data['view'] = 'transaksi/v_slip_order_spk';
            $data['scripts'] = 'transaksi/s_slip_order_spk';

            $dtbarang = [
                'select' => '*',
                'from' => 'mstbarang',
                'where' => [['IsAktif' => 1]]
            ];
            $data['dtbarang'] = $this->crud->get_rows($dtbarang);

            $data['gudang'] = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstgudang',
                    'where' => [['KodeGudang !=' => null]],
                ]
            );

            $dtinduk = [
                'select' => 't.IDTransJual, t.TglSlipOrder, t.EstimasiSelesai, t.SODibuatOleh, t.SPKDisetujuiOleh, t.SPKDisetujuiTgl, t.SPKDiketahuiOleh, t.SPKDiketahuiTgl, t.SPKDibuatOleh, t.SPKTanggal, t.NoSlipOrder, t.SPKNomor, t.StatusProses, t.KodeGudang, p.KodePerson, p.NamaPersonCP, p.NamaUsaha',
                'from' => 'transpenjualan t',
                'join' => [
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = t.KodePerson",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['t.IDTransJual' => $idtransjual]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['IDTransJual'] = $idtransjual;

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $idtransjual   = $this->input->get('idtransjual');
            $configData['table'] = 'itempenjualan i';
            $configData['where'] = [['i.IDTransJual'  => $idtransjual]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.NoUrut LIKE '%$cari%' OR i.Spesifikasi LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transpenjualan t',
                    'on' => "t.IDTransJual = i.IDTransJual",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksibarang b',
                    'on' => "b.KodeBarang = i.KodeBarang AND b.NoRefTrSistem = i.IDTransJual",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.IDTransJual', 'i.NoUrut', 'i.JenisBarang', 'i.Kategory', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.StatusProses', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'i.AdditionalName', 'b.JmlProduksi', 'b.KodeProduksi'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.IDTransJual', 'i.NoUrut', 'i.JenisBarang', 'i.Kategory', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.StatusProses', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'i.AdditionalName', 'b.JmlProduksi', 'b.KodeProduksi',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 23; //FiturID di tabel serverfitur
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
                $stok = $this->lokasi->get_stok_asli($temp['KodeBarang']);
                $temp['Stok'] = isset($stok['stok']) ? $stok['stok'] : 0;
                $temp['Sisa'] = $temp['Stok'] - $temp['Qty'];
                $temp['Barang'] = isset($temp['AdditionalName']) ? $temp['AdditionalName'] : $temp['NamaBarang'];
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function checkNoSPK()
    {
        $SPKNomor = $this->input->get('SPKNomor');
        $SPKLama = $this->input->get('SPKLama');
        $count =  $this->crud->get_count([
            'select' => 'SPKNomor',
            'from' => 'transpenjualan',
            'where' => [
                [
                    'SPKNomor' => $SPKNomor,
                    'SPKNomor !=' => $SPKLama
                ]
            ]
        ]);
        if ($count > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Nomor SPK telah terpakai']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Nomor SPK tersedia']);
        }
    }

    public function simpanspk()
    {
        $data = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'transpenjualan',
                'where' => [['IDTransJual' => $this->input->post('IDTransJual')]],
            ]
        );

        ## POST DATA
        if (!($this->input->post('SPKNomor') != null && $this->input->post('SPKNomor') != '')) {
            $prefix = "SPK-" . date("Ym");
            $insertdata['SPKNomor'] = $this->crud->get_kode([
                'select' => 'RIGHT(SPKNomor, 7) AS KODE',
                'from' => 'transpenjualan',
                'where' => [['LEFT(SPKNomor, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'SPKNomor DESC',
                'prefix' => $prefix
            ]);
        } else {
            $insertdata['SPKNomor'] = $this->input->post('SPKNomor');
        }
        $insertdata['SPKDibuatOleh']    = $this->session->userdata('ActualName');
        $insertdata['EstimasiSelesai']  = $this->input->post('EstimasiSelesai');
        $insertdata['SPKTanggal']       = $this->input->post('SPKTanggal');
        $insertdata['SPKDisetujuiOleh'] = $this->input->post('SPKDisetujuiOleh');
        $insertdata['SPKDisetujuiTgl']  = $this->input->post('SPKDisetujuiTgl');
        $insertdata['SPKDiketahuiOleh'] = $this->input->post('SPKDiketahuiOleh');
        $insertdata['SPKDiketahuiTgl']  = $this->input->post('SPKDiketahuiTgl');
        if ($data['StatusProses'] == 'SO') {
            $insertdata['StatusProses']     = 'SPK';
            $insertdata['StatusProduksi']   = 'WIP';
        }

        $itemjual = $this->crud->get_rows([
            'select' => 'i.NoUrut, tb.NoTrans, i.ProdUkuran, i.ProdJmlDaun, i.KodeBarang, br.NamaBarang, tb.JmlProduksi',
            'from' => 'itempenjualan i',
            'join' => [
                [
                    'table' => ' transaksibarang tb',
                    'on' => "tb.NoRefTrSistem = i.IDTransJual AND tb.KodeBarang = i.KodeBarang",
                    'param' => 'INNER',
                ],
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'INNER',
                ],
            ],
            'where' => [['i.IDTransJual' => $this->input->post('IDTransJual')]],
        ]);

        // update data di transaksi barang
        if (count($itemjual) > 0) {
            foreach ($itemjual as $val) {
                $updatebrg = [
                    'NoRefTrManual'     => $this->input->post('NoRefTrManual'),
                    'Deskripsi'         => $this->input->post('Deskripsi'),
                    'GudangAsal'        => $this->input->post('Gudang'),
                    'GudangTujuan'      => $this->input->post('Gudang'),
                    'TanggalTransaksi'  => date('Y-m-d H:i'),
                    'ProdUkuran'        => $val['ProdUkuran'],
                    'ProdJmlDaun'       => $val['ProdJmlDaun']
                ];

                $countitembahan = $this->crud->get_count([
                    'select' => 'NoTrans, NoUrut',
                    'from' => 'itemtransaksibarang',
                    'where' => [['NoTrans' => $val['NoTrans']]],
                ]);
                if ($countitembahan == 0) {
                    $itembahan = $this->crud->get_rows([
                        'select' => 'tb.NoTrans, d.DraftID, d.KodeBarang, br.NamaBarang, d.Qty, d.SatuanBarang, br.HargaBeliTerakhir, SUM(d.Qty * br.HargaBeliTerakhir) AS Total, jb.NamaJenisBarang',
                        'from' => 'draftbahanproduksi d',
                        'join' => [
                            [
                                'table' => ' itempenjualan i',
                                'on' => "i.IDTransJual = d.IDTransJual AND i.NoUrut = d.NoUrut",
                                'param' => 'INNER',
                            ],
                            [
                                'table' => ' transaksibarang tb',
                                'on' => "tb.NoRefTrSistem = i.IDTransJual AND tb.KodeBarang = i.KodeBarang",
                                'param' => 'INNER',
                            ],
                            [
                                'table' => ' mstbarang br',
                                'on' => "br.KodeBarang = d.KodeBarang",
                                'param' => 'INNER',
                            ],
                            [
                                'table' => ' mstjenisbarang jb',
                                'on' => 'jb.KodeJenis = br.KodeJenis',
                                'param' => 'INNER',
                            ],
                        ],
                        'where' => [[
                            'd.IDTransJual' => $this->input->post('IDTransJual'),
                            'tb.NoTrans' => $val['NoTrans'],
                        ]],
                        'group_by' => 'd.DraftID',
                    ]);
                    foreach ($itembahan as $items) {
                        $itemvalue = [
                            'NoUrut'       => $items['DraftID'],
                            'NoTrans'      => $items['NoTrans'],
                            'KodeBarang'   => $items['KodeBarang'],
                            'Qty'          => $items['Qty'],
                            'HargaSatuan'  => $items['HargaBeliTerakhir'],
                            'Total'        => $items['Total'],
                            'JenisStok'    => 'KELUAR',
                            'GudangAsal'   => $this->input->post('Gudang'),
                            'SatuanBarang' => $items['SatuanBarang'],
                            'JenisBarang'  => $items['NamaJenisBarang'],
                            'IsHapus'      => 0
                        ];
                        $saveitembahan[] = $this->crud->insert($itemvalue, 'itemtransaksibarang');
                    }
                }
                $updatetrbarang[] = $this->crud->update($updatebrg, ['NoTrans' => $val['NoTrans']], 'transaksibarang');
            }
        }

        $res = $this->crud->update($insertdata, ['IDTransJual' => $this->input->post('IDTransJual')], 'transpenjualan');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'cetak',
                'JenisTransaksi' => 'Transaksi Slip Order',
                'Description' => 'cetak spk transaksi slip order ' . $this->input->post('IDTransJual')
            ]);
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

    public function cetakspk()
    {
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('transaksi/slip_order/detailspk/') . $this->uri->segment(4);

        $dtinduk = [
            'select' => 't.IDTransJual, t.TglSlipOrder, t.EstimasiSelesai, t.SODibuatOleh, t.SPKDisetujuiOleh, t.SPKDisetujuiTgl, t.SPKDiketahuiOleh, t.SPKDiketahuiTgl, t.SPKDibuatOleh, t.SPKTanggal, t.NoSlipOrder, t.SPKNomor, t.StatusProses, p.KodePerson, p.NamaPersonCP, p.NamaUsaha',
            'from' => 'transpenjualan t',
            'join' => [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = t.KodePerson",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['t.IDTransJual' => $idtransjual]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
        $data['EstimasiSelesai'] = isset($data['dtinduk']['EstimasiSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['EstimasiSelesai']))) : ''; //. ' ' . date('H:i', strtotime($data['dtinduk']['EstimasiSelesai']))
        $data['SPKTanggal'] = isset($data['dtinduk']['SPKTanggal']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['SPKTanggal']))) . ' ' . date('H:i', strtotime($data['dtinduk']['SPKTanggal'])) : '';
        $data['SPKDisetujuiTgl'] = isset($data['dtinduk']['SPKDisetujuiTgl']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['SPKDisetujuiTgl']))) . ' ' . date('H:i', strtotime($data['dtinduk']['SPKDisetujuiTgl'])) : '';
        $data['SPKDiketahuiTgl'] = isset($data['dtinduk']['SPKDiketahuiTgl']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['SPKDiketahuiTgl']))) . ' ' . date('H:i', strtotime($data['dtinduk']['SPKDiketahuiTgl'])) : '';

        $sql = [
            'select' => 'i.IDTransJual, i.NoUrut, i.JenisBarang, i.Kategory, i.Spesifikasi, i.HargaSatuan, i.Qty, i.Total, i.SatuanBarang, i.Deskripsi,  i.KodeBarang, br.NamaBarang, br.SatuanBarang as satuanAsal, br.Spesifikasi as spesifikasiAsal, br.HargaBeliTerakhir, i.AdditionalName, b.JmlProduksi, b.KodeProduksi',
            'from' => 'itempenjualan i',
            'join' => [
                [
                    'table' => ' mstbarang br',
                    'on' => " br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksibarang b',
                    'on' => "b.KodeBarang = i.KodeBarang AND b.NoRefTrSistem = i.IDTransJual",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['i.IDTransJual' => $idtransjual]],
        ];
        $data['model'] = $this->crud->get_rows($sql);

        $this->load->library('Pdf');
        $this->load->view('transaksi/cetak_spk', $data);
    }
}
