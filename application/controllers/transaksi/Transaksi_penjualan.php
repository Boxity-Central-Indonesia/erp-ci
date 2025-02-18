<?php
defined('BASEPATH') or exit('No direct script access allowed');

class transaksi_penjualan extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[25]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[25]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'transjual';
            $data['title'] = 'Transaksi Penjualan';
            $data['view'] = 'transaksi/v_transaksi_penjualan';
            $data['scripts'] = 'transaksi/s_transaksi_penjualan';

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

            $data['gudang'] = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstgudang',
                ]
            );

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
                    // 'k.JenisTransaksiKas' => 'DP PEMBELIAN',
                ]
            ];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (j.IDTransJual LIKE '%$cari%' OR j.NoRef_Manual LIKE '%$cari%' OR j.StatusBayar LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%' OR br.NamaBarang LIKE '%$cari%')";
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
                [
                    'table' => ' itempenjualan i',
                    'on' => "j.IDTransJual = i.IDTransJual",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstbarang br',
                    'on' => "i.KodeBarang = br.KodeBarang",
                    'param' => 'LEFT',
                ]
            ];

            $configData['group_by'] = 'j.IDTransJual';

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.IDTransJual', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.NoRef_Manual', 'j.TanggalPenjualan', 'j.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'SUM(k.TotalTransaksi) as TotalBayar', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName', 'j.KodeGudang', 'g.NamaGudang', 'j.SODibuatOleh'
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
                'j.IDTransJual', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.NoRef_Manual', 'j.TanggalPenjualan', 'j.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'SUM(k.TotalTransaksi) as TotalBayar', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName', 'j.KodeGudang', 'g.NamaGudang', 'j.SODibuatOleh',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 25; //FiturID di tabel serverfitur
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
                $TotalBayar = $this->lokasi->count_total_bayar($record->IDTransJual);
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['TotalTagihan'] = $TotalTagihan;
                $temp['TotalBayar'] = $TotalBayar;
                $temp['TanggalPenjualan'] = isset($temp['TanggalPenjualan']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPenjualan']))) . ' ' . date('H:i', strtotime($temp['TanggalPenjualan'])) : '';
                if ($canDelete == 1 && $temp['StatusBayar'] == '') {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_penjualan/detail/' . base64_encode($temp['IDTransJual'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransJual'] . ' class="btnhapustjl" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_penjualan/detail/' . base64_encode($temp['IDTransJual'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function checkManualCode()
    {
        $NoRef_Manual = $this->input->get('NoRef_Manual');
        $count =  $this->crud->get_count([
            'select' => '*',
            'from' => 'transpenjualan',
            'where' => [['NoRef_Manual' => $NoRef_Manual]]
        ]);
        if ($count > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'No Referensi telah digunakan']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'No Referensi tersedia']);
        }
    }

    public function simpan()
    {
        $insertdata = $this->input->post();
        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('IDTransJual') != null && $this->input->post('IDTransJual') != '')) {
            $prefix = "TJL-" . date("Ym");
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
            $insertdata['StatusKirim']      = 'BELUM';
            $insertdata['StatusBayar']      = 'BELUM';
            $isEdit = false;
        } else {
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
                'JenisTransaksi' => 'Transaksi Penjualan',
                'Description' => $ket . ' data transaksi penjualan ' . $id
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data"),
                'id' => $insertdata['IDTransJual']
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
        $this->db->trans_begin();
        $kode = $this->input->get('IDTransJual');
        $cekSO = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'transpenjualan',
                'where' => [['IDTransJual' => $kode]],
            ]
        );

        $cekterimaPiutang = $this->crud->get_count(
            [
                'select' => '*',
                'from' => 'transaksikas',
                'where' => [['NoRef_Sistem' => $kode]],
            ]
        );

        $barangKeluar = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'transaksibarang',
                'where' => [['NoRefTrSistem' => $kode]],
            ]
        );

        $getkas = $this->crud->get_one_row(
            [
                'select' => 'NoTransKas',
                'from' => 'transaksikas',
                'where' => [['NoRef_Sistem' => $kode]],
            ]
        );

        // if ($cekterimaPiutang > 0) {
            // echo json_encode([
            //     'status' => false,
            //     'msg'  => "Gagal menghapus data transaksi karena sudah menginputkan pembayaran, silahkan hapus data pembayaran terlebih dahulu di menu Transaksi Terima Piutang."
            // ]);
        // } else {

            // Menghapus data di tabel item & transaksi penjualan
            $item = $this->crud->delete(['IDTransJual' => $kode], 'itempenjualan');
            $res = $this->crud->delete(['IDTransJual' => $kode], 'transpenjualan');

            // Menghapus data penjurnalan langsung
            $getjurnalpenjualan = $this->crud->get_one_row([
                'select' => 'IDTransJurnal',
                'from' => 'transjurnal',
                'where' => [['NoRefTrans' => $kode]],
            ]);
            if ($getjurnalpenjualan) {
                $deleteitemjurnal = $this->crud->delete(['IDTransJurnal' => $getjurnalpenjualan['IDTransJurnal']], 'transjurnalitem');
                $deletejurnalinduk = $this->crud->delete(['IDTransJurnal' => $getjurnalpenjualan['IDTransJurnal']], 'transjurnal');
            }

            // Menghapus data penjurnalan kas
            if ($getkas) {
                $getjurnalkas = $this->crud->get_one_row([
                    'select' => 'IDTransJurnal',
                    'from' => 'transjurnal',
                    'where' => [['NoRefTrans' => $getkas['NoTransKas']]],
                ]);
                if ($getjurnalkas) {
                    $deleteitemjurnal = $this->crud->delete(['IDTransJurnal' => $getjurnalkas['IDTransJurnal']], 'transjurnalitem');
                    $deletejurnalinduk = $this->crud->delete(['IDTransJurnal' => $getjurnalkas['IDTransJurnal']], 'transjurnal');
                }
                $deletekas = $this->crud->delete(['NoTransKas' => $getkas['NoTransKas']], 'transaksikas');
            }

            if ($barangKeluar) {
                // Menghapus data di item & transaksi barang
                $itembarang = $this->crud->delete(['NoTrans' => $barangKeluar['NoTrans']], 'itemtransaksibarang');
                $trbarang = $this->crud->delete(['NoTrans' => $barangKeluar['NoTrans']], 'transaksibarang');
            }

            if ($res) {
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => 'hapus',
                    'JenisTransaksi' => 'Transaksi Penjualan',
                    'Description' => 'hapus data transaksi penjualan ' . $kode
                ]);
                $this->db->trans_commit();
                echo json_encode([
                    'status' => true,
                    'msg'  => "Berhasil Menghapus Data"
                ]);
            } else {
                $this->db->trans_rollback();
                echo json_encode([
                    'status' => false,
                    'msg'  => "Gagal Menghapus Data"
                ]);
            }
        // }
    }

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[25]);
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_jual';
            $data['title'] = 'Detail Transaksi Penjualan';
            $data['view'] = 'transaksi/v_transaksi_penjualan_detail';
            $data['scripts'] = 'transaksi/s_transaksi_penjualan_detail';

            $dtbarang = [
                'select' => '*',
                'from' => 'mstbarang b',
                'join' => [[
                    'table' => 'mstjenisbarang j',
                    'on' => "j.KodeJenis = b.KodeJenis",
                    'param' => 'LEFT'
                ]],
                'where' => [
                    ['b.IsAktif' => 1],
                    // "j.NamaJenisBarang LIKE '%BARANG JADI%'"
                ]
            ];
            $data['dtbarang'] = $this->crud->get_rows($dtbarang);

            $data['dtakun'] = $this->crud->get_rows([
                'select' => '*',
                'from' => 'mstakun',
                'where' => [
                    [
                        'IsParent' => 0,
                        'IsAktif' => 1,
                        'JenisAkun' => 'Debit'
                    ], " LEFT(KodeAkun, 1) = 1"
                ]
            ]);

            $dtinduk = $this->crud->get_one_row([
                'select' => 't.IDTransJual, t.NoSlipOrder, t.SODibuatOleh, t.TglSlipOrder, t.PPN, t.DiskonBawah, t.NominalBelumPajak, t.TotalTagihan, t.StatusProses, t.StatusBayar, t.NoRef_Manual, t.TanggalPenjualan, t.TanggalJatuhTempo, p.KodePerson, p.NamaPersonCP, p.NamaUsaha, k.NoTransKas, k.TotalTransaksi, t.KodeGudang, g.NamaGudang, r.IDTransRetur',
                'from' => 'transpenjualan t',
                'join' => [
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = t.KodePerson",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' mstgudang g',
                        'on' => "g.KodeGudang = t.KodeGudang",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' transaksikas k',
                        'on' => "k.NoRef_Sistem = t.IDTransJual AND k.JenisTransaksiKas = 'DP PENJUALAN'",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' transaksiretur r',
                        'on' => "r.IDTrans = t.IDTransJual",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['t.IDTransJual' => $idtransjual]],
            ]);
            $data['dtinduk'] = $dtinduk;

            $tahunaktif = $this->akses->get_tahun_aktif();
            $noreftrans = ($dtinduk['NoTransKas'] != null && $dtinduk['NoTransKas'] != '') ? $dtinduk['NoTransKas'] : $dtinduk['IDTransJual'];
            $jurnal = $this->crud->get_one_row([
                'select' => 'IDTransJurnal, KodeTahun',
                'from' => 'transjurnal',
                'where' => [['NoRefTrans' => $noreftrans]]
            ]);
            $tahuntransaksi = $jurnal ? $jurnal['KodeTahun'] : $tahunaktif;

            $data['exptahun'] = ($tahuntransaksi != $tahunaktif) ? 1 : 0;

            $data['countpiutang'] = $this->crud->get_count([
                'select' => '*',
                'from' => 'transaksikas',
                'where' => [[
                    'NoRef_Sistem' => $idtransjual,
                    'JenisTransaksiKas' => 'TERIMA PIUTANG',
                ]]
            ]);

            $item = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'itempenjualan',
                    'where' => [['IDTransJual' => $idtransjual]]
                ]
            );
            $tanpadiskon = 0;
            $tagihanawal = 0;
            $hd = [];
            $stokgudang = [];
            $sisa = [];
            $stok_kurang = 0;
            $i = 0;
            foreach ($item as $items) {
                $tanpadiskon += $items['HargaSatuan'] * $items['Qty'];
                $hd[$i] = ($items['HargaSatuan'] - $items['Diskon']) * $items['Qty'];
                $tagihanawal += $hd[$i];
                $stok = $this->lokasi->get_stok_per_gudang($data['dtinduk']['KodeGudang'], $items['KodeBarang']);
                $stokgudang[$i] = isset($stok['stok']) ? $stok['stok'] : 0;
                $sisa[$i] = $stokgudang[$i] - $items['Qty'];
                if ($sisa[$i] < 0) {
                    $stok_kurang = 1;
                }

                $i++;
            }
            $data['tanpadiskon'] = $tanpadiskon;
            $data['tagihanawal'] = $tagihanawal;
            $data['stok_kurang'] = $stok_kurang;
            $data['jml_item'] = count($item);

            $last_id = $this->crud->get_one_row(
                [
                    'select' => 'IDTransJual',
                    'from' => 'transpenjualan',
                    'order_by' => 'IDTransJual DESC',
                ]
            );
            $data['last_id'] = $last_id['IDTransJual'];
            $data['IDTransJual'] = $idtransjual;

            // memo jurnal
            $cekKas = $this->crud->get_one_row([
                'select' => 'NoTransKas',
                'from' => 'transaksikas',
                'where' => [[
                    'NoRef_Sistem' => $idtransjual,
                    'JenisTransaksiKas' => 'DP PENJUALAN',
                ]],
            ]);
            if ($cekKas) {
                $where = ['j.NoRefTrans' => $cekKas['NoTransKas']];
            } else {
                $where = [
                    'j.NoRefTrans' => $idtransjual,
                    'LEFT(j.NarasiJurnal, 11) =' => 'Transaksi P'
                ];
            }

            $data['memojurnal'] = $this->crud->get_rows([
                'select' => '*',
                'from' => 'transjurnalitem i',
                'join' => [
                    [
                        'table' => ' transjurnal j',
                        'on' => "j.IDTransJurnal = i.IDTransJurnal",
                        'param' => 'INNER',
                    ],
                ],
                'where' => [$where],
            ]);
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

            $data['check_retur'] = $this->crud->get_count([
                'select' => 'IDTransRetur',
                'from' => 'transaksiretur',
                'where' => [
                    ['IDTrans' => $idtransjual],
                    ['IsVoid' => 0]
                ],
            ]);

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
                [
                    'table' => ' transaksikas k',
                    'on' => "k.NoRef_Sistem = t.IDTransJual AND k.JenisTransaksiKas = 'TERIMA PIUTANG'",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.IDTransJual', 'i.NoUrut', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.NoSlipOrder', 't.TglSlipOrder', 't.StatusProses', 't.StatusBayar', 't.KodeGudang', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'br.HargaJual', 'i.JenisBarang', 'i.Kategory', 'i.Diskon', 'i.SatuanPenjualan', 'g.NamaGudang', 'k.NoTransKas'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.IDTransJual', 'i.NoUrut', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.NoSlipOrder', 't.TglSlipOrder', 't.StatusProses', 't.StatusBayar', 't.KodeGudang', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'br.HargaJual', 'i.JenisBarang', 'i.Kategory', 'i.Diskon', 'i.SatuanPenjualan', 'g.NamaGudang', 'k.NoTransKas',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 25; //FiturID di tabel serverfitur
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

            $exptahun = $this->input->get('exptahun');

            foreach ($records as $record) {
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $stok = $this->lokasi->get_stok_per_gudang($temp['KodeGudang'], $temp['KodeBarang']);
                $stokgudang = isset($stok['stok']) ? $stok['stok'] : 0;
                $temp['Stok'] = $stokgudang;
                $temp['Sisa'] = $stokgudang - $temp['Qty'];
                $temp['StokGudang'] = $stokgudang . ' ' . $temp['SatuanBarang'];
                $temp['QtyFormat'] = str_replace(['.', ',', '+'], ['+', '.', ','], number_format($temp['Qty'], 2));
                $temp['QtyShow'] = $temp['QtyFormat'] . ' ' . $temp['SatuanBarang'];
                $temp['Quantity'] = ($temp['SatuanPenjualan'] == 'pcs') ? '1 pcs' : $temp['QtyShow'];
                if ($canEdit == 1 && $canDelete == 1 && $temp['NoTransKas'] == null && $exptahun == 0) {
                    if ($temp['TglSlipOrder'] == null) {
                        $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($temp) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransJual'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                    } else {
                        $temp['btn_aksi'] = '';
                    }
                } elseif ($canEdit == 1 && $canDelete != 1 && $temp['NoTransKas'] == null && $exptahun == 0) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($temp) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1 && $temp['NoTransKas'] == null && $exptahun == 0) {
                    if ($temp['TglSlipOrder'] == null) {
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

    public function cetakdetail()
    {
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('transaksi/transaksi_penjualan/detail/') . $this->uri->segment(4);

        $dtinduk = [
            'select' => 't.IDTransJual, t.NoSlipOrder, t.TglSlipOrder, t.PPN, t.DiskonBawah, t.NominalBelumPajak, t.TotalTagihan, t.StatusProses, t.StatusBayar, t.NoRef_Manual, t.TanggalPenjualan, t.TanggalJatuhTempo, p.KodePerson, p.NamaPersonCP, p.NamaUsaha, p.NoHP, k.NoTransKas, k.TotalTransaksi, t.KodeGudang, g.NamaGudang',
            'from' => 'transpenjualan t',
            'join' => [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = t.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = t.KodeGudang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.NoRef_Sistem = t.IDTransJual AND k.JenisTransaksiKas = 'DP PENJUALAN'",
                    'param' => 'LEFT',
                ]
            ],
            'where' => [['t.IDTransJual' => $idtransjual]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);

        $data['kodeSO'] = isset($data['dtinduk']['TglSlipOrder']) ? $data['dtinduk']['IDTransJual'] : '-';
        $data['diskonbawah'] = isset($data['dtinduk']['DiskonBawah']) ? $data['dtinduk']['DiskonBawah'] : 0;
        $data['ppn'] = isset($data['dtinduk']['PPN']) ? $data['dtinduk']['PPN'] : 0;
        $data['totaltagihan'] = isset($data['dtinduk']['TotalTagihan']) ? $data['dtinduk']['TotalTagihan'] : 0;
        $data['totaltransaksi'] = isset($data['dtinduk']['TotalTransaksi']) ? $data['dtinduk']['TotalTransaksi'] : 0;

        $sql = [
            'select' => 'i.IDTransJual, i.NoUrut, i.Spesifikasi, i.HargaSatuan, i.Qty, i.Total, i.SatuanBarang, i.Deskripsi, i.KodeBarang, br.NamaBarang, br.DeskripsiBarang',
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
        $this->load->view('transaksi/v_transaksi_penjualan_detail_cetak', $data);
    }

    public function simpandetail()
    {
        $insertdata = $this->input->post();
        unset($insertdata['TotalLama']);
        unset($insertdata['JmlStok']);
        $isEdit = true;

        // Mengambil data induk di tabel transpenjualan
        $induk = $this->crud->get_one_row(
            [
                'select'=> '*',
                'from'  => 'transpenjualan',
                'where' => [['IDTransJual' => $this->input->post('IDTransJual')]]
            ]
        );
        $satuanpenjualan = $this->input->post('SatuanPenjualan');
        $qty = ($satuanpenjualan == 'pcs') ? str_replace(['.', ','], ['', '.'], $this->input->post('Qty')) : str_replace(['.', ','], ['', '.'], $this->input->post('JmlStok'));

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
            $insertdata['NoUrut'] = $NoUrut + 1;
            $insertdata['Qty'] = $qty;
            if ($satuanpenjualan == 'ecer') { // jika item bukan barang jadi
                $insertdata['HargaSatuan'] = str_replace(['.', ','], ['', '.'], $this->input->post('Total'));
                $insertdata['Total'] = $insertdata['HargaSatuan'] * $insertdata['Qty'];
            } else {
                $insertdata['Total'] = str_replace(['.', ','], ['', '.'], $this->input->post('Total'));
                $insertdata['HargaSatuan'] = $insertdata['Total'] / $insertdata['Qty'];
            }
            $insertdata['Diskon'] = 0;

            // Menambahkan total ke total nilai barang di tabel transpenjualan
            // if ($induk['TotalTagihan'] > 0) {
            //     $totaltagihan = $induk['TotalTagihan'] + $insertdata['Total'];
            // } else {
            //     $totaltagihan = $insertdata['Total'];
            // }
            // $updatetagihan = $this->crud->update(['TotalTagihan' => $totaltagihan], ['IDTransJual' => $this->input->post('IDTransJual')], 'transpenjualan');
            $res = $this->crud->insert($insertdata, 'itempenjualan');
        } else {
            $isEdit = true;
            $updatedata['SatuanBarang']     = $this->input->post('SatuanBarang');
            $updatedata['Spesifikasi']      = $this->input->post('Spesifikasi');
            $updatedata['SatuanPenjualan']  = $satuanpenjualan;
            $updatedata['Qty']              = $qty;
            if ($satuanpenjualan == 'ecer') { // jika item bukan barang jadi
                $updatedata['HargaSatuan'] = str_replace(['.', ','], ['', '.'], $this->input->post('Total'));
                $updatedata['Total'] = $updatedata['HargaSatuan'] * $updatedata['Qty'];
            } else {
                $updatedata['Total']            = str_replace(['.', ','], ['', '.'], $this->input->post('Total'));
                $updatedata['HargaSatuan']      = $updatedata['Total'] / $updatedata['Qty'];
            }

            // Mengubah total ke total nilai barang di tabel transpenjualan
            // if ($induk['TotalTagihan'] > 0) {
            //     $totaltagihan = $induk['TotalTagihan'] - $this->input->post('TotalLama') + $updatedata['Total'];
            // } else {
            //     $totaltagihan = $updatedata['Total'];
            // }
            // $updatetagihan = $this->crud->update(['TotalTagihan' => $totaltagihan], ['IDTransJual' => $this->input->post('IDTransJual')], 'transpenjualan');

            $res = $this->crud->update($updatedata, ['IDTransJual' => $this->input->post('IDTransJual'), 'NoUrut' => $this->input->post('NoUrut')], 'itempenjualan');
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
        $this->db->trans_begin();
        $totaltransaksi = str_replace(['.', ','], ['', '.'], $this->input->post('TotalTransaksi'));
        $item = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'itempenjualan',
                'where' => [['IDTransJual' => $this->input->post('IDTransJual')]]
            ]
        );

        $tahun = $this->akses->get_tahun_aktif();
        $kodeakunkas = $this->input->post('KodeAkun');
        $namaakunkas = ($kodeakunkas != '') ? $this->lokasi->getnamaakun($kodeakunkas) : '';

        $akuntunai = $this->lokasi->get_akun_penjurnalan('Penjualan', 'Tunai');
        $akunkredit = $this->lokasi->get_akun_penjurnalan('Penjualan', 'Kredit');
        $akuntunaippn = $this->lokasi->get_akun_penjurnalan('Transaksi Penjualan PPN', 'Tunai');
        $akunkreditppn = $this->lokasi->get_akun_penjurnalan('Transaksi Penjualan PPN', 'Kredit');

        $status_jurnal = $this->lokasi->setting_jurnal_status();
        $insertjurnal['IDTransJurnal'] = null;

        // hapus jurnal lama
        $noreftrans = ($this->input->post('NoTransKas') != null && $this->input->post('NoTransKas') != '') ? $this->input->post('NoTransKas') : $this->input->post('IDTransJual');
        $getjurnalOld = $this->crud->get_one_row([
            'select' => 'IDTransJurnal',
            'from' => 'transjurnal',
            'where' => [['NoRefTrans' => $noreftrans]],
        ]);
        if ($getjurnalOld) {
            $deletejurnalitem   = $this->crud->delete(['IDTransJurnal' => $getjurnalOld['IDTransJurnal']], 'transjurnalitem');
            $deletejurnalinduk  = $this->crud->delete(['IDTransJurnal' => $getjurnalOld['IDTransJurnal']], 'transjurnal');
        }

        $updatedata['NoRef_Manual']      = $this->input->post('NoRef_Manual');
        $updatedata['TanggalPenjualan']  = $this->input->post('TanggalPenjualan');
        $updatedata['TanggalJatuhTempo'] = $this->input->post('TanggalJatuhTempo');
        $updatedata['StatusProses']      = 'DONE';
        $updatedata['StatusKirim']       = 'TERKIRIM';

        $hp = [];
        $hd = [];
        $totalhargaasli = 0;
        $totaldiskonatas = 0;
        $totalPenjualanBarang = 0;
        $hargaAsli = 0;
        $diskonAsli = 0;
        $i = 0;
        foreach ($item as $items) {
            $hp[$i] = $items['HargaSatuan'] * $items['Qty'];
            $hargaAsli += $hp[$i];
            $hd[$i] = ($items['HargaSatuan'] - $items['Diskon']) * $items['Qty'];
            $diskonAsli += $hd[$i];
            $totalPenjualanBarang += $items['Qty'];

            $totalhargaasli += $items['HargaSatuan'] * $items['Qty'];
            $totaldiskonatas += $items['Diskon'] * $items['Qty'];
            $i++;
        }

        $updatedata['DiskonBawah'] = $this->input->post('NilaiDiskon');
        $updatedata['PPN'] = str_replace(['.', ','], ['', '.'], $this->input->post('NilaiPPN'));
        $updatedata['NominalBelumPajak'] = $this->input->post('NominalBelumPajak');
        $updatedata['TotalTagihan'] = str_replace(['.', ','], ['', '.'], $this->input->post('TagihanAkhir'));
        $updatedata['TotalNilaiBarangReal'] = $totalhargaasli;
        $diskontotal = $totaldiskonatas + $updatedata['DiskonBawah'];

        if ($this->input->post('TglSlipOrder') == null && $this->input->post('TglSlipOrder') == '') {
            if ($totaltransaksi > 0) {
                if ($updatedata['TotalTagihan'] > $totaltransaksi) {
                    $updatedata['StatusBayar'] = 'SEBAGIAN';
                } elseif ($updatedata['TotalTagihan'] < $totaltransaksi) {
                    echo json_encode([
                        'status' => false,
                        'msg'  => ("Gagal menyimpan data, DP Dibayar tidak boleh melebihi Total Tagihan Akhir")
                    ]); die;
                } else {
                    $updatedata['StatusBayar'] = 'LUNAS';
                }

                if (!($this->input->post('NoTransKas') != null && $this->input->post('NoTransKas') != '')) {
                    $prefix = "TRK-" . date("Ym");
                    $insertkas['NoTransKas'] = $this->crud->get_kode([
                        'select' => 'RIGHT(NoTransKas, 7) AS KODE',
                        'from' => 'transaksikas',
                        'where' => [['LEFT(NoTransKas, 10) =' => $prefix]],
                        'limit' => 1,
                        'order_by' => 'NoTransKas DESC',
                        'prefix' => $prefix
                    ]);
                    $insertkas['KodeTahun']         = $tahun;
                    $insertkas['TanggalTransaksi']  = $this->input->post('TanggalPenjualan');
                    $insertkas['NoRef_Sistem']      = $this->input->post('IDTransJual');
                    $insertkas['NoRef_Manual']      = $this->input->post('NoRef_Manual');
                    $insertkas['UserName']          = $this->session->userdata('UserName');
                    $insertkas['KodePerson']        = $this->input->post('KodePerson');
                    $insertkas['NominalBelumPajak'] = $updatedata['NominalBelumPajak'];
                    $insertkas['PPN']               = $updatedata['PPN'];
                    $insertkas['TotalTransaksi']    = $totaltransaksi;
                    $insertkas['JenisTransaksiKas'] = 'DP PENJUALAN';
                    $insertkas['IsDijurnalkan']     = 0;
                    $insertkas['Diskon']            = 0;
                    $insertkas['IsDijurnalkan']         = 1;

                    $prefix2 = "JRN-" . date("Ym");
                    $insertjurnal['IDTransJurnal']      = $this->crud->get_kode([
                        'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                        'from' => 'transjurnal',
                        'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                        'limit' => 1,
                        'order_by' => 'IDTransJurnal DESC',
                        'prefix' => $prefix2
                    ]);
                    $insertjurnal['KodeTahun']          = $insertkas['KodeTahun'];
                    $insertjurnal['TglTransJurnal']     = $insertkas['TanggalTransaksi'];
                    $insertjurnal['TipeJurnal']         = "UMUM";
                    $insertjurnal['NarasiJurnal']       = ($updatedata['StatusBayar'] == 'SEBAGIAN') ? "Transaksi Penjualan Kredit" : "Transaksi Penjualan Tunai";
                    $insertjurnal['NominalTransaksi']   = $updatedata['TotalNilaiBarangReal'] + $updatedata['PPN'];
                    $insertjurnal['NoRefTrans']         = $insertkas['NoTransKas'];
                    $insertjurnal['UserName']           = $this->session->userdata['UserName'];

                    $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');

                    if ($status_jurnal == 'on') {
                        // jika status jurnal otomatis
                        if (count($akuntunai) > 0 && count($akunkredit) > 0 && count($akuntunaippn) > 0 && count($akunkreditppn) > 0) {
                            if ($updatedata['StatusBayar'] == 'SEBAGIAN') { // jika dp dibayar sebagian
                                if ($updatedata['PPN'] > 0) {
                                    foreach ($akunkreditppn as $key) {
                                        if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                            $debet = 0;
                                            $kredit = $updatedata['TotalNilaiBarangReal'];
                                        } elseif ($key['StatusAkun'] == 'PPn') {
                                            $debet = 0;
                                            $kredit = $updatedata['PPN'];
                                        } elseif ($key['StatusAkun'] == 'Kas') {
                                            $debet = $totaltransaksi;
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                            $debet = $insertjurnal['NominalTransaksi'] - ($totaltransaksi + $diskontotal);
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Diskon') {
                                            $debet = $diskontotal;
                                            $kredit = 0;
                                        }
                                        $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                        $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                        $list_item = [
                                            'NoUrut' => $key['NoUrut'],
                                            'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                            'KodeTahun' => $insertjurnal['KodeTahun'],
                                            'KodeAkun' => $kodeakun,
                                            'NamaAkun' => $namaakun,
                                            'Debet' => $debet,
                                            'Kredit' => $kredit,
                                            'Uraian' => "Penjurnalan otomatis untuk Transaksi Pembelian Kredit"
                                        ];
                                        $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                    }
                                } else {
                                    foreach ($akunkredit as $key) {
                                        if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                            $debet = 0;
                                            $kredit = $updatedata['TotalNilaiBarangReal'];
                                        } elseif ($key['StatusAkun'] == 'Kas') {
                                            $debet = $totaltransaksi;
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                            $debet = $insertjurnal['NominalTransaksi'] - ($totaltransaksi + $diskontotal);
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Diskon') {
                                            $debet = $diskontotal;
                                            $kredit = 0;
                                        }
                                        $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                        $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                        $list_item = [
                                            'NoUrut' => $key['NoUrut'],
                                            'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                            'KodeTahun' => $insertjurnal['KodeTahun'],
                                            'KodeAkun' => $kodeakun,
                                            'NamaAkun' => $namaakun,
                                            'Debet' => $debet,
                                            'Kredit' => $kredit,
                                            'Uraian' => "Penjurnalan otomatis untuk Transaksi Penjualan Kredit"
                                        ];
                                        $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                    }
                                }
                            } else { // jika dp dibayar lunas
                                if ($updatedata['PPN'] > 0) {
                                    foreach ($akuntunaippn as $key) {
                                        if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                            $debet = 0;
                                            $kredit = $updatedata['TotalNilaiBarangReal'];
                                        } elseif ($key['StatusAkun'] == 'PPn') {
                                            $debet = 0;
                                            $kredit = $updatedata['PPN'];
                                        } elseif ($key['StatusAkun'] == 'Kas') {
                                            $debet = $totaltransaksi;
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Diskon') {
                                            $debet = $diskontotal;
                                            $kredit = 0;
                                        }
                                        $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                        $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                        $list_item = [
                                            'NoUrut' => $key['NoUrut'],
                                            'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                            'KodeTahun' => $insertjurnal['KodeTahun'],
                                            'KodeAkun' => $kodeakun,
                                            'NamaAkun' => $namaakun,
                                            'Debet' => $debet,
                                            'Kredit' => $kredit,
                                            'Uraian' => "Penjurnalan otomatis untuk Transaksi Pembelian Tunai"
                                        ];
                                        $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                    }
                                } else {
                                    foreach ($akuntunai as $key) {
                                        if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                            $debet = 0;
                                            $kredit = $updatedata['TotalNilaiBarangReal'];
                                        } elseif ($key['StatusAkun'] == 'Kas') {
                                            $debet = $totaltransaksi;
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Diskon') {
                                            $debet = $diskontotal;
                                            $kredit = 0;
                                        }
                                        $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                        $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                        $list_item = [
                                            'NoUrut' => $key['NoUrut'],
                                            'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                            'KodeTahun' => $insertjurnal['KodeTahun'],
                                            'KodeAkun' => $kodeakun,
                                            'NamaAkun' => $namaakun,
                                            'Debet' => $debet,
                                            'Kredit' => $kredit,
                                            'Uraian' => "Penjurnalan otomatis untuk Transaksi Penjualan Tunai"
                                        ];
    
                                        $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                    }
                                }
                            }
                        }
                    } else {
                        // jika status jurnal manual
                    }

                    $kas = $this->crud->insert($insertkas, 'transaksikas');
                } else {
                    $insertkas['KodeTahun']         = $tahun;
                    $insertkas['TanggalTransaksi']  = $this->input->post('TanggalPenjualan');
                    $insertkas['NoRef_Sistem']      = $this->input->post('IDTransJual');
                    $insertkas['NoRef_Manual']      = $this->input->post('NoRef_Manual');
                    $insertkas['UserName']          = $this->session->userdata('UserName');
                    $insertkas['KodePerson']        = $this->input->post('KodePerson');
                    $insertkas['NominalBelumPajak'] = $updatedata['NominalBelumPajak'];
                    $insertkas['PPN']               = $updatedata['PPN'];
                    $insertkas['TotalTransaksi']    = $totaltransaksi;
                    $insertkas['JenisTransaksiKas'] = 'DP PENJUALAN';
                    $insertkas['IsDijurnalkan']     = 0;
                    $insertkas['Diskon']            = 0;
                    $insertkas['IsDijurnalkan']         = 1;

                    $prefix2 = "JRN-" . date("Ym");
                    $insertjurnal['IDTransJurnal']      = $this->crud->get_kode([
                        'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                        'from' => 'transjurnal',
                        'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                        'limit' => 1,
                        'order_by' => 'IDTransJurnal DESC',
                        'prefix' => $prefix2
                    ]);
                    $insertjurnal['KodeTahun']          = $insertkas['KodeTahun'];
                    $insertjurnal['TglTransJurnal']     = $insertkas['TanggalTransaksi'];
                    $insertjurnal['TipeJurnal']         = "UMUM";
                    $insertjurnal['NarasiJurnal']       = ($updatedata['StatusBayar'] == 'SEBAGIAN') ? "Transaksi Penjualan Kredit" : "Transaksi Penjualan Tunai";
                    $insertjurnal['NominalTransaksi']   = $updatedata['TotalNilaiBarangReal'] + $updatedata['PPN'];
                    $insertjurnal['NoRefTrans']         = $this->input->post('NoTransKas');
                    $insertjurnal['UserName']           = $this->session->userdata['UserName'];

                    $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');

                    if ($status_jurnal == 'on') {
                        // jika status jurnal otomatis
                        if (count($akuntunai) > 0 && count($akunkredit) > 0 && count($akuntunaippn) > 0 && count($akunkreditppn) > 0) {
                            if ($updatedata['StatusBayar'] == 'SEBAGIAN') { // jika dp dibayar sebagian
                                if ($updatedata['PPN'] > 0) {
                                    foreach ($akunkreditppn as $key) {
                                        if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                            $debet = 0;
                                            $kredit = $updatedata['TotalNilaiBarangReal'];
                                        } elseif ($key['StatusAkun'] == 'PPn') {
                                            $debet = 0;
                                            $kredit = $updatedata['PPN'];
                                        } elseif ($key['StatusAkun'] == 'Kas') {
                                            $debet = $totaltransaksi;
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                            $debet = $insertjurnal['NominalTransaksi'] - ($totaltransaksi + $diskontotal);
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Diskon') {
                                            $debet = $diskontotal;
                                            $kredit = 0;
                                        }
                                        $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                        $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                        $list_item = [
                                            'NoUrut' => $key['NoUrut'],
                                            'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                            'KodeTahun' => $insertjurnal['KodeTahun'],
                                            'KodeAkun' => $kodeakun,
                                            'NamaAkun' => $namaakun,
                                            'Debet' => $debet,
                                            'Kredit' => $kredit,
                                            'Uraian' => "Penjurnalan otomatis untuk Transaksi Pembelian Kredit"
                                        ];
                                        $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                    }
                                } else {
                                    foreach ($akunkredit as $key) {
                                        if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                            $debet = 0;
                                            $kredit = $updatedata['TotalNilaiBarangReal'];
                                        } elseif ($key['StatusAkun'] == 'Kas') {
                                            $debet = $totaltransaksi;
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                            $debet = $insertjurnal['NominalTransaksi'] - ($totaltransaksi + $diskontotal);
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Diskon') {
                                            $debet = $diskontotal;
                                            $kredit = 0;
                                        }
                                        $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                        $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                        $list_item = [
                                            'NoUrut' => $key['NoUrut'],
                                            'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                            'KodeTahun' => $insertjurnal['KodeTahun'],
                                            'KodeAkun' => $kodeakun,
                                            'NamaAkun' => $namaakun,
                                            'Debet' => $debet,
                                            'Kredit' => $kredit,
                                            'Uraian' => "Penjurnalan otomatis untuk Transaksi Penjualan Kredit"
                                        ];
                                        $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                    }
                                }
                            } else { // jika dp dibayar lunas
                                if ($updatedata['PPN'] > 0) {
                                    foreach ($akuntunaippn as $key) {
                                        if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                            $debet = 0;
                                            $kredit = $updatedata['TotalNilaiBarangReal'];
                                        } elseif ($key['StatusAkun'] == 'PPn') {
                                            $debet = 0;
                                            $kredit = $updatedata['PPN'];
                                        } elseif ($key['StatusAkun'] == 'Kas') {
                                            $debet = $totaltransaksi;
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Diskon') {
                                            $debet = $diskontotal;
                                            $kredit = 0;
                                        }
                                        $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                        $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                        $list_item = [
                                            'NoUrut' => $key['NoUrut'],
                                            'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                            'KodeTahun' => $insertjurnal['KodeTahun'],
                                            'KodeAkun' => $kodeakun,
                                            'NamaAkun' => $namaakun,
                                            'Debet' => $debet,
                                            'Kredit' => $kredit,
                                            'Uraian' => "Penjurnalan otomatis untuk Transaksi Pembelian Tunai"
                                        ];
                                        $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                    }
                                } else {
                                    foreach ($akuntunai as $key) {
                                        if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                            $debet = 0;
                                            $kredit = $updatedata['TotalNilaiBarangReal'];
                                        } elseif ($key['StatusAkun'] == 'Kas') {
                                            $debet = $totaltransaksi;
                                            $kredit = 0;
                                        } elseif ($key['StatusAkun'] == 'Diskon') {
                                            $debet = $diskontotal;
                                            $kredit = 0;
                                        }
                                        $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                        $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                        $list_item = [
                                            'NoUrut' => $key['NoUrut'],
                                            'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                            'KodeTahun' => $insertjurnal['KodeTahun'],
                                            'KodeAkun' => $kodeakun,
                                            'NamaAkun' => $namaakun,
                                            'Debet' => $debet,
                                            'Kredit' => $kredit,
                                            'Uraian' => "Penjurnalan otomatis untuk Transaksi Penjualan Tunai"
                                        ];
    
                                        $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                    }
                                }
                            }
                        }
                    } else {
                        // jika status jurnal manual
                    }

                    $kas = $this->crud->update($insertkas, ['NoTransKas' => $this->input->post('NoTransKas')], 'transaksikas');
                }
            } else {
                $updatedata['StatusBayar'] = 'BELUM';

                $cekKas = $this->crud->get_count(
                    [
                        'select' => '*',
                        'from' => 'transaksikas',
                        'where' => [['NoRef_Sistem' => $this->input->post('IDTransJual')]],
                    ]
                );
                if ($cekKas > 0) {
                    $kas = $this->crud->delete(['NoRef_Sistem' => $this->input->post('IDTransJual')], 'transaksikas');
                }

                // jika dp tidak dibayar
                $updatedata['IsDijurnalkan']        = 1;
                $prefix2 = "JRN-" . date("Ym");
                $insertjurnal['IDTransJurnal']      = $this->crud->get_kode([
                    'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                    'from' => 'transjurnal',
                    'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                    'limit' => 1,
                    'order_by' => 'IDTransJurnal DESC',
                    'prefix' => $prefix2
                ]);
                $insertjurnal['KodeTahun']          = $tahun;
                $insertjurnal['TglTransJurnal']     = $this->input->post('TanggalPenjualan');
                $insertjurnal['TipeJurnal']         = "UMUM";
                $insertjurnal['NarasiJurnal']       = "Transaksi Penjualan Kredit";
                $insertjurnal['NominalTransaksi']   = $updatedata['TotalNilaiBarangReal'] + $updatedata['PPN'];
                $insertjurnal['NoRefTrans']         = $this->input->post('IDTransJual');
                $insertjurnal['UserName']           = $this->session->userdata['UserName'];

                $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');

                if ($status_jurnal == 'on') {
                    // jika status jurnal otomatis
                    if (count($akunkredit) > 0 && count($akunkreditppn) > 0) {
                        if ($updatedata['PPN'] > 0) {
                            foreach ($akunkreditppn as $key) {
                                if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                    $debet = 0;
                                    $kredit = $updatedata['TotalNilaiBarangReal'];
                                } elseif ($key['StatusAkun'] == 'PPn') {
                                    $debet = 0;
                                    $kredit = $updatedata['PPN'];
                                } elseif ($key['StatusAkun'] == 'Kas') {
                                    $debet = 0;
                                    $kredit = 0;
                                } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                    $debet = $insertjurnal['NominalTransaksi'] - $diskontotal;
                                    $kredit = 0;
                                } elseif ($key['StatusAkun'] == 'Diskon') {
                                    $debet = $diskontotal;
                                    $kredit = 0;
                                }
                                $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                $list_item = [
                                    'NoUrut' => $key['NoUrut'],
                                    'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                    'KodeTahun' => $insertjurnal['KodeTahun'],
                                    'KodeAkun' => $kodeakun,
                                    'NamaAkun' => $namaakun,
                                    'Debet' => $debet,
                                    'Kredit' => $kredit,
                                    'Uraian' => "Penjurnalan otomatis untuk Transaksi Pembelian Kredit"
                                ];
                                $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                            }
                        } else {
                            foreach ($akunkredit as $key) {
                                if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                    $debet = 0;
                                    $kredit = $updatedata['TotalNilaiBarangReal'];
                                } elseif ($key['StatusAkun'] == 'Kas') {
                                    $debet = 0;
                                    $kredit = 0;
                                } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                    $debet = $insertjurnal['NominalTransaksi'] - $diskontotal;
                                    $kredit = 0;
                                } elseif ($key['StatusAkun'] == 'Diskon') {
                                    $debet = $diskontotal;
                                    $kredit = 0;
                                }
                                $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                $list_item = [
                                    'NoUrut' => $key['NoUrut'],
                                    'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                    'KodeTahun' => $insertjurnal['KodeTahun'],
                                    'KodeAkun' => $kodeakun,
                                    'NamaAkun' => $namaakun,
                                    'Debet' => $debet,
                                    'Kredit' => $kredit,
                                    'Uraian' => "Penjurnalan otomatis untuk Transaksi Penjualan Kredit"
                                ];
                                $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                            }
                        }
                    }
                } else {
                    // jika status jurnal manual
                }
            }
        } else {
            $getPenjualan = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'transpenjualan',
                'where' => [['IDTransJual' => $this->input->post('IDTransJual')]],
            ]);

            $getJurnal = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'transjurnal',
                'where' => [['NoRefTrans' => $this->input->post('IDTransJual')]],
            ]);

            if ($getJurnal != null && $totaltransaksi > 0) {
                if ($getPenjualan['TotalTagihan'] > $totaltransaksi) {
                    $updatedata['StatusBayar'] = 'SEBAGIAN';
                } elseif ($getPenjualan['TotalTagihan'] < $totaltransaksi) {
                    echo json_encode([
                        'status' => false,
                        'msg'  => ("Gagal menyimpan data, DP Dibayar tidak boleh melebihi Total Tagihan Akhir")
                    ]); die;
                } else {
                    $updatedata['StatusBayar'] = 'LUNAS';
                }

                $deleteitemjurnal = $this->crud->delete(['IDTransJurnal' => $getJurnal['IDTransJurnal']], 'transjurnalitem');
                $deletejurnalinduk = $this->crud->delete(['IDTransJurnal' => $getJurnal['IDTransJurnal']], 'transjurnal');

                $prefix = "TRK-" . date("Ym");
                $insertkas['NoTransKas'] = $this->crud->get_kode([
                    'select' => 'RIGHT(NoTransKas, 7) AS KODE',
                    'from' => 'transaksikas',
                    'where' => [['LEFT(NoTransKas, 10) =' => $prefix]],
                    'limit' => 1,
                    'order_by' => 'NoTransKas DESC',
                    'prefix' => $prefix
                ]);
                $insertkas['KodeTahun']         = $tahun;
                $insertkas['TanggalTransaksi']  = $this->input->post('TanggalPenjualan');
                $insertkas['NoRef_Sistem']      = $this->input->post('IDTransJual');
                $insertkas['NoRef_Manual']      = $this->input->post('NoRef_Manual');
                $insertkas['UserName']          = $this->session->userdata('UserName');
                $insertkas['KodePerson']        = $this->input->post('KodePerson');
                $insertkas['NominalBelumPajak'] = $getPenjualan['NominalBelumPajak'];
                $insertkas['PPN']               = $getPenjualan['PPN'];
                $insertkas['TotalTransaksi']    = $totaltransaksi;
                $insertkas['JenisTransaksiKas'] = 'DP PENJUALAN';
                $insertkas['IsDijurnalkan']     = 0;
                $insertkas['Diskon']            = $getPenjualan['DiskonBawah'];

                $insertkas['IsDijurnalkan']         = 1;
                $prefix2 = "JRN-" . date("Ym");
                $insertjurnal['IDTransJurnal']      = $this->crud->get_kode([
                    'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                    'from' => 'transjurnal',
                    'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                    'limit' => 1,
                    'order_by' => 'IDTransJurnal DESC',
                    'prefix' => $prefix2
                ]);
                $insertjurnal['KodeTahun']          = $insertkas['KodeTahun'];
                $insertjurnal['TglTransJurnal']     = $insertkas['TanggalTransaksi'];
                $insertjurnal['TipeJurnal']         = "UMUM";
                $insertjurnal['NarasiJurnal']       = ($updatedata['StatusBayar'] == 'SEBAGIAN') ? "Transaksi Penjualan Kredit" : "Transaksi Penjualan Tunai";
                $insertjurnal['NominalTransaksi']   = $updatedata['TotalNilaiBarangReal'] + $updatedata['PPN'];
                $insertjurnal['NoRefTrans']         = $insertkas['NoTransKas'];
                $insertjurnal['UserName']           = $this->session->userdata['UserName'];

                $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');

                if ($status_jurnal == 'on') {
                    // jika status jurnal otomatis
                    if (count($akuntunai) > 0 && count($akunkredit) > 0 && count($akuntunaippn) > 0 && count($akunkreditppn) > 0) {
                        if ($updatedata['StatusBayar'] == 'SEBAGIAN') { // jika dibayar sebagian
                            if ($updatedata['PPN'] > 0) {
                                foreach ($akunkreditppn as $key) {
                                    if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                        $debet = 0;
                                        $kredit = $updatedata['TotalNilaiBarangReal'];
                                    } elseif ($key['StatusAkun'] == 'PPn') {
                                        $debet = 0;
                                        $kredit = $updatedata['PPN'];
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = $totaltransaksi;
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                        $debet = $insertjurnal['NominalTransaksi'] - ($totaltransaksi + $diskontotal);
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = $diskontotal;
                                        $kredit = 0;
                                    }
                                    $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                    $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                    $list_item = [
                                        'NoUrut' => $key['NoUrut'],
                                        'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                        'KodeTahun' => $insertjurnal['KodeTahun'],
                                        'KodeAkun' => $kodeakun,
                                        'NamaAkun' => $namaakun,
                                        'Debet' => $debet,
                                        'Kredit' => $kredit,
                                        'Uraian' => "Penjurnalan otomatis untuk Transaksi Pembelian Kredit"
                                    ];
                                    $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                }
                            } else {
                                foreach ($akunkredit as $key) {
                                    if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                        $debet = 0;
                                        $kredit = $updatedata['TotalNilaiBarangReal'];
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = $totaltransaksi;
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                        $debet = $insertjurnal['NominalTransaksi'] - ($totaltransaksi + $diskontotal);
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = $diskontotal;
                                        $kredit = 0;
                                    }
                                    $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                    $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                    $list_item = [
                                        'NoUrut' => $key['NoUrut'],
                                        'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                        'KodeTahun' => $insertjurnal['KodeTahun'],
                                        'KodeAkun' => $kodeakun,
                                        'NamaAkun' => $namaakun,
                                        'Debet' => $debet,
                                        'Kredit' => $kredit,
                                        'Uraian' => "Penjurnalan otomatis untuk Transaksi Penjualan Kredit"
                                    ];
                                    $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                }
                            }
                        } else { // jika dibayar lunas
                            if ($updatedata['PPN'] > 0) {
                                foreach ($akuntunaippn as $key) {
                                    if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                        $debet = 0;
                                        $kredit = $updatedata['TotalNilaiBarangReal'];
                                    } elseif ($key['StatusAkun'] == 'PPn') {
                                        $debet = 0;
                                        $kredit = $updatedata['PPN'];
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = $insertjurnal['NominalTransaksi'] - $diskontotal;
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = $diskontotal;
                                        $kredit = 0;
                                    }
                                    $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                    $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                    $list_item = [
                                        'NoUrut' => $key['NoUrut'],
                                        'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                        'KodeTahun' => $insertjurnal['KodeTahun'],
                                        'KodeAkun' => $kodeakun,
                                        'NamaAkun' => $namaakun,
                                        'Debet' => $debet,
                                        'Kredit' => $kredit,
                                        'Uraian' => "Penjurnalan otomatis untuk Transaksi Pembelian Kredit"
                                    ];
                                    $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                }
                            } else {
                                foreach ($akuntunai as $key) {
                                    if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                        $debet = 0;
                                        $kredit = $updatedata['TotalNilaiBarangReal'];
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = $insertjurnal['NominalTransaksi'] - $diskontotal;
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = $diskontotal;
                                        $kredit = 0;
                                    }
                                    $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                    $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                    $list_item = [
                                        'NoUrut' => $key['NoUrut'],
                                        'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                        'KodeTahun' => $insertjurnal['KodeTahun'],
                                        'KodeAkun' => $kodeakun,
                                        'NamaAkun' => $namaakun,
                                        'Debet' => $debet,
                                        'Kredit' => $kredit,
                                        'Uraian' => "Penjurnalan otomatis untuk Transaksi Penjualan Tunai"
                                    ];
                                    $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                                }
                            }
                        }
                    }
                } else {
                    // jika status jurnal manual
                }

                $kas = $this->crud->insert($insertkas, 'transaksikas');
            }

            $updatedata['TanggalPenjualan']  = $this->input->post('TanggalPenjualan');
            $updatedata['TanggalJatuhTempo'] = $this->input->post('TanggalJatuhTempo');
            $updatedata['StatusProses']      = 'DONE';
            $updatedata['StatusKirim']       = 'TERKIRIM';
        }

        // hapus transaksi barang lama
        $idtransjual = $this->input->post('IDTransJual');
        $gettrbarang = $this->crud->get_rows([
            'select' => 'NoTrans',
            'from' => 'transaksibarang',
            'where' => [['NoRefTrSistem' => $idtransjual]]
        ]);
        if ($gettrbarang) {
            foreach ($gettrbarang as $value) {
                $deleteitembarang[]   = $this->crud->delete(['NoTrans' => $value['NoTrans']], 'itemtransaksibarang');
                $deletetrbarang[]     = $this->crud->delete(['NoTrans' => $value['NoTrans']], 'transaksibarang');
            }
        }

        // Insert tabel transaksi barang
        $prefixbrg = "TBR-" . date("Ym");
        $insertbarang['NoTrans'] = $this->crud->get_kode([
            'select' => 'RIGHT(NoTrans, 7) AS KODE',
            'from' => 'transaksibarang',
            'where' => [['LEFT(NoTrans, 10) =' => $prefixbrg]],
            'limit' => 1,
            'order_by' => 'NoTrans DESC',
            'prefix' => $prefixbrg
        ]);
        $insertbarang['TanggalTransaksi']   = $this->input->post('TanggalPenjualan');
        $insertbarang['Username']           = $this->session->userdata('UserName');
        $insertbarang['KodePerson']         = $this->input->post('KodePerson');
        $insertbarang['JenisTransaksi']     = 'BARANG KELUAR';
        $insertbarang['NoRefTrSistem']      = $this->input->post('IDTransJual');
        $insertbarang['NoRefTrManual']      = $this->input->post('NoRef_Manual');
        $insertbarang['GudangAsal']         = $this->input->post('KodeGudang');
        $insertbarang['IsHapus']            = 0;
        $brg = $this->crud->insert($insertbarang, 'transaksibarang');

        $j = 0;
        foreach ($item as $key) {
            // Insert tabel item barang
            $res = $this->db->insert('itemtransaksibarang', array(
                'NoTrans'       => $insertbarang['NoTrans'],
                'NoUrut'        => $key['NoUrut'],
                'KodeBarang'    => $key['KodeBarang'],
                'Qty'           => $key['Qty'],
                'HargaSatuan'   => $key['HargaSatuan'],
                'Total'         => $key['Qty'] * $key['HargaSatuan'],
                'Deskripsi'     => $key['Deskripsi'],
                'JenisStok'     => 'KELUAR',
                'GudangAsal'    => $this->input->post('KodeGudang'),
                'SatuanBarang'  => $key['SatuanBarang'],
                'JenisBarang'   => $key['JenisBarang'],
                'Kategory'      => $key['Kategory'],
                'IsHapus'       => 0
            ));

            $dtbarang = $this->crud->get_one_row(
                [
                    'select' => '*',
                    'from' => 'mstbarang',
                    'where' => [['KodeBarang' => $key['KodeBarang']]],
                ]
            );
            // Update hpp saat jual di item penjualan
            $hppsaatjual = $this->crud->update(['HPPSaatJual' => $dtbarang['NilaiHPP']], ['IDTransJual' => $this->input->post('IDTransJual'), 'NoUrut' => $key['NoUrut'], 'KodeBarang' => $key['KodeBarang']], 'itempenjualan');

            $j++;
        }

        $res = $this->crud->update($updatedata, ['IDTransJual' => $this->input->post('IDTransJual')], 'transpenjualan');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'edit',
                'JenisTransaksi' => 'Transaksi Penjualan',
                'Description' => 'update data transaksi penjualan ' . $this->input->post('IDTransJual')
            ]);
            $this->db->trans_commit();
            echo json_encode([
                'status' => true,
                'msg'  => ("Berhasil Mengubah Data"),
                'idjurnal' => $insertjurnal['IDTransJurnal'],
                'stj' => $status_jurnal
            ]);
        } else {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => false,
                'msg'  => ("Gagal Mengubah Data")
            ]);
        }
    }

    public function terimapembayaran()
    {
        checkAccess($this->session->userdata('fiturview')[25]);
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));
        $kodeperson   = escape(base64_decode($this->uri->segment(5)));

        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_jual';
            $data['title'] = 'Terima Pembayaran';
            $data['view'] = 'transaksi/v_terima_pembayaran';
            $data['scripts'] = 'transaksi/s_terima_pembayaran';

            $dtinduk = [
                'select' => '*',
                'from' => 'transpenjualan j',
                'join' => [
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = j.KodePerson",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['j.IDTransJual' => $idtransjual]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['KodePerson'] = $kodeperson;
            $data['IDTransJual'] = $idtransjual;

            $data['countdata'] = $this->crud->get_rows([
                'select' => '*',
                'from' => 'transpenjualan j',
                'where' => [
                    [
                        'j.StatusBayar !=' => 'LUNAS',
                        'j.KodePerson' => $kodeperson,
                        'j.StatusProses' => 'DONE',
                    ]
                ],
            ]);

            $data['dtakun'] = $this->crud->get_rows([
                'select' => '*',
                'from' => 'mstakun',
                'where' => [
                    [
                        'IsParent' => 0,
                        'IsAktif' => 1,
                        'JenisAkun' => 'Debit'
                    ], " LEFT(KodeAkun, 1) = 1"
                ]
            ]);

            $data['status_jurnal'] = $this->lokasi->setting_jurnal_status();
            $data['tahunaktif'] = $this->akses->get_tahun_aktif();

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $kodeperson   = $this->input->get('kodeperson');
            $status_jurnal = $this->input->get('status_jurnal');
            $configData['table'] = 'transpenjualan j';
            $configData['where'] = [[
                'j.StatusBayar !=' => 'LUNAS',
                'j.KodePerson' => $kodeperson,
                'j.StatusProses' => 'DONE',
            ]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (j.IDTransJual LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = j.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.NoRef_Sistem = j.IDTransJual",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.IDTransJual', 'j.TotalTagihan', 'j.StatusBayar', 'j.TanggalPenjualan', 'j.KodePerson', 'p.NamaPersonCP', 'SUM(k.TotalTransaksi) as TotalBayar', 'j.CatatanTerimaBayar AS BayarSekarang', 'j.NominalBelumPajak', 'j.PPN', 'j.PPh', 'j.DiskonBawah'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['group_by'] = 'j.IDTransJual';

            $configData['display_column'] = [
                false,
                'j.IDTransJual', 'j.TotalTagihan', 'j.StatusBayar', 'j.TanggalPenjualan', 'j.KodePerson', 'p.NamaPersonCP', 'SUM(k.TotalTransaksi) as TotalBayar', 'j.CatatanTerimaBayar AS BayarSekarang', 'j.NominalBelumPajak', 'j.PPN', 'j.PPh', 'j.DiskonBawah',
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
                $temp['totalhistory'] = count($this->lokasi->get_history_bayar($temp['IDTransJual']));
                $temp['dthistory'] = $this->lokasi->get_history_bayar($temp['IDTransJual']);
                $temp['TotalTagihan'] = isset($temp['TotalTagihan']) ? $temp['TotalTagihan'] : 0;
                $temp['TotalBayar'] = isset($temp['TotalBayar']) ? $temp['TotalBayar'] : 0;
                $temp['SisaTagihan'] = $temp['TotalTagihan'] - $temp['TotalBayar'];
                $temp['TanggalPenjualan'] = isset($temp['TanggalPenjualan']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPenjualan']))) : '';
                $temp['DibayarSekarang'] = '<input type="text" style="width: 75%;" class="form-control" name="TotalTransaksi[]" id="Bayar'.$temp['no'].'" value="">
                                            <input type="hidden" class="form-control" name="IDTransJual[]" value="'.$temp['IDTransJual'].'">
                                            <input type="hidden" class="form-control" name="KodePerson[]" value="'.$temp['KodePerson'].'">
                                            <input type="hidden" class="form-control" name="NominalBelumPajak[]" value="'.$temp['NominalBelumPajak'].'">
                                            <input type="hidden" class="form-control" name="PPN[]" value="'.$temp['PPN'].'">
                                            <input type="hidden" class="form-control" name="PPh[]" value="'.$temp['PPh'].'">
                                            <input type="hidden" class="form-control" name="DiskonBawah[]" value="'.$temp['DiskonBawah'].'">
                                            <input type="hidden" class="form-control" name="TotalTagihan[]" value="'.$temp['TotalTagihan'].'">
                                            <script type="text/javascript">
                                                var tanpa_rupiah'.$temp['no'].' = document.getElementById("Bayar'.$temp['no'].'");
                                                tanpa_rupiah'.$temp['no'].'.addEventListener("keyup", function(e)
                                                {
                                                    tanpa_rupiah'.$temp['no'].'.value = formatRupiah(this.value);
                                                });
                                            </script>';
                $temp['btn_aksi'] = '<a class="simpanperrow btn btn-sm btn-primary" type="button" data-kode="' . $temp['IDTransJual'] . '" data-kode2="' . $temp['no'] . '" title=""><span aria-hidden="true">Simpan</span></a>';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function terimapembayaranproses()
    {
        $idtransjual    = $this->input->post('IDTransJual');
        $totaltransaksi = $this->input->post('TotalTransaksi');
        $kodeperson     = $this->input->post('KodePerson');
        $nbp            = $this->input->post('NominalBelumPajak');
        $ppn            = $this->input->post('PPN');
        $pph            = $this->input->post('PPh');
        $diskonbawah    = $this->input->post('DiskonBawah');
        $totaltagihan   = $this->input->post('TotalTagihan');

        $totalnominal = 0;
        $check_nil = 0;
        foreach (str_replace(['.', ','], ['', '.'], $totaltransaksi) as $key => $value) {
            $totalnominal += (int)$value;
            if ((int)$value > 0) {
                $check_nil = 1;
            }
        }

        $getakun = $this->lokasi->get_akun_penjurnalan('Piutang', 'Tunai');
        $kodeakunkas = $this->input->post('KodeAkun');
        $namaakunkas = $kodeakunkas != '' ? $this->lokasi->getnamaakun($kodeakunkas) : '';

        $status_jurnal = $this->lokasi->setting_jurnal_status();

        $prefix = "TRK-" . date("Ym");
        $prefix2 = "JRN-" . date("Ym");
        $first_notranskas = $this->crud->get_kode([
            'select' => 'RIGHT(NoTransKas, 7) AS KODE',
            'from' => 'transaksikas',
            'where' => [['LEFT(NoTransKas, 10) =' => $prefix]],
            'limit' => 1,
            'order_by' => 'NoTransKas DESC',
            'prefix' => $prefix
        ]);
        $jurnal['IDTransJurnal'] = null;
        foreach ($idtransjual as $key => $value) {
            if ($totaltransaksi[$key]) {
                $nominal = str_replace(['.', ','], ['', '.'], $totaltransaksi[$key]);
            } else {
                $nominal = 0;
            }

            $notranskas = $this->crud->get_kode([
                'select' => 'RIGHT(NoTransKas, 7) AS KODE',
                'from' => 'transaksikas',
                'where' => [['LEFT(NoTransKas, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'NoTransKas DESC',
                'prefix' => $prefix
            ]);
            if ($nominal > 0) {
                $kas = [
                    'NoTransKas'        => $notranskas,
                    'KodeTahun'         => $this->akses->get_tahun_aktif(),
                    'TanggalTransaksi'  => $this->input->post('TanggalTransaksi'),
                    'NoRef_Sistem'      => $value,
                    'UserName'          => $this->session->userdata('UserName'),
                    'KodePerson'        => $kodeperson[$key],
                    'NominalBelumPajak' => $nbp[$key],
                    'PPN'               => $ppn[$key],
                    'PPh'               => $pph[$key],
                    'TotalTransaksi'    => $nominal,
                    'IsDijurnalkan'     => 1,
                    'JenisTransaksiKas' => 'TERIMA PIUTANG',
                    'Diskon'            => $diskonbawah[$key],
                ];
                $insertkas[] = $this->crud->insert($kas, 'transaksikas');

                $jurnal = [
                    'IDTransJurnal' => $this->crud->get_kode([
                        'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                        'from' => 'transjurnal',
                        'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                        'limit' => 1,
                        'order_by' => 'IDTransJurnal DESC',
                        'prefix' => $prefix2
                    ]),
                    'KodeTahun' => $kas['KodeTahun'],
                    'TglTransJurnal' => $kas['TanggalTransaksi'],
                    'TipeJurnal' => "UMUM",
                    'NarasiJurnal' => "Transaksi Terima Piutang",
                    'NominalTransaksi' => $kas['TotalTransaksi'],
                    'NoRefTrans' => $kas['NoTransKas'],
                    'UserName' => $this->session->userdata('UserName')
                ];
                $insertjurnal[] = $this->crud->insert($jurnal, 'transjurnal');
                // if ($status_jurnal == 'on') {
                    if ($getakun) {
                        foreach ($getakun as $keys) {
                            $itemjurnal = [
                                'NoUrut' => $keys['NoUrut'],
                                'IDTransJurnal' => $jurnal['IDTransJurnal'],
                                'KodeTahun' => $jurnal['KodeTahun'],
                                'KodeAkun' => ($keys['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $keys['KodeAkun'],
                                'NamaAkun' => ($keys['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $keys['NamaAkun'],
                                'Debet' => ($keys['JenisJurnal'] == 'Debet') ? $jurnal['NominalTransaksi'] : 0,
                                'Kredit' => ($keys['JenisJurnal'] == 'Kredit') ? $jurnal['NominalTransaksi'] : 0,
                                'Uraian' => "Penjurnalan otomatis untuk Transaksi Terima Piutang"
                            ];

                            $insertjurnalitem[] = $this->crud->insert_or_update($itemjurnal, 'transjurnalitem');
                        }
                    }
                // } else {
                    
                // }
            }

            $jumlah_bayar = $this->lokasi->get_total_dibayar($kodeperson[$key], $value);
            $jmlbayar = isset($jumlah_bayar['total_bayar']) ? $jumlah_bayar['total_bayar'] : 0;
            if ($jmlbayar < $totaltagihan[$key]) {
                $res[] = $this->crud->update(['StatusBayar' => 'SEBAGIAN'], ['IDTransJual' => $value], 'transpenjualan');

                // ## INSERT TO SERVER LOG
                // $log[] = $this->logsrv->insert_log([
                //     'Action' => 'tambah',
                //     'JenisTransaksi' => 'Transaksi Terima Piutang',
                //     'Description' => 'tambah data transaksi terima piutang ' . $value
                // ]);
            } else {
                $res[] = $this->crud->update(['StatusBayar' => 'LUNAS'], ['IDTransJual' => $value], 'transpenjualan');

                // ## INSERT TO SERVER LOG
                // $log[] = $this->logsrv->insert_log([
                //     'Action' => 'tambah',
                //     'JenisTransaksi' => 'Transaksi Terima Piutang',
                //     'Description' => 'tambah data transaksi terima piutang ' . $value
                // ]);
            }
        }

        if ($res) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil menyimpan data",
                'idjurnal' => $jurnal['IDTransJurnal'],
                'cn' => $check_nil,
                'stj' => $status_jurnal
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menyimpan data"
            ]);
        }
    }

    public function kirimpertransaksi()
    {
        $idtransjual    = $this->input->get('IDTransJual');
        $totaltransaksi = (int)str_replace(['.', ','], ['', '.'], $this->input->get('nilaibayar'));
        $dtpenjualan = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transpenjualan',
            'where' => [['IDTransJual' => $idtransjual]],
        ]);

        $jurnal['IDTransJurnal'] = null;
        if ($totaltransaksi > 0) {
            // insert tr kas
            $prefix = "TRK-" . date("Ym");
            $kas = [
                'NoTransKas'        => $this->crud->get_kode([
                    'select' => 'RIGHT(NoTransKas, 7) AS KODE',
                    'from' => 'transaksikas',
                    'where' => [['LEFT(NoTransKas, 10) =' => $prefix]],
                    'limit' => 1,
                    'order_by' => 'NoTransKas DESC',
                    'prefix' => $prefix
                ]),
                'KodeTahun'         => $this->akses->get_tahun_aktif(),
                'TanggalTransaksi'  => date('Y-m-d H:i'),
                'NoRef_Sistem'      => $idtransjual,
                'UserName'          => $this->session->userdata('UserName'),
                'KodePerson'        => $dtpenjualan['KodePerson'],
                'NominalBelumPajak' => $dtpenjualan['NominalBelumPajak'],
                'PPN'               => $dtpenjualan['PPN'],
                'PPh'               => $dtpenjualan['PPh'],
                'TotalTransaksi'    => $totaltransaksi,
                'IsDijurnalkan'     => 1,
                'JenisTransaksiKas' => 'TERIMA PIUTANG',
                'Diskon'            => $dtpenjualan['DiskonBawah'],
            ];
            $insertkas = $this->crud->insert($kas, 'transaksikas');

            // insert tr jurnal
            $prefix2 = "JRN-" . date("Ym");
            $jurnal = [
                'IDTransJurnal' => $this->crud->get_kode([
                    'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                    'from' => 'transjurnal',
                    'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                    'limit' => 1,
                    'order_by' => 'IDTransJurnal DESC',
                    'prefix' => $prefix2
                ]),
                'KodeTahun' => $kas['KodeTahun'],
                'TglTransJurnal' => $kas['TanggalTransaksi'],
                'TipeJurnal' => "UMUM",
                'NarasiJurnal' => "Transaksi Terima Piutang",
                'NominalTransaksi' => $kas['TotalTransaksi'],
                'NoRefTrans' => $kas['NoTransKas'],
                'UserName' => $this->session->userdata('UserName')
            ];
            $insertjurnal = $this->crud->insert($jurnal, 'transjurnal');

            // update tr penjualan
            $jumlah_bayar = $this->lokasi->get_total_dibayar($dtpenjualan['KodePerson'], $idtransjual);
            $jmlbayar = isset($jumlah_bayar['total_bayar']) ? $jumlah_bayar['total_bayar'] : 0;
            if ($jmlbayar < $dtpenjualan['TotalTagihan']) {
                $updatepembelian = $this->crud->update(['StatusBayar' => 'SEBAGIAN'], ['IDTransJual' => $idtransjual], 'transpenjualan');
            } else {
                $updatepembelian = $this->crud->update(['StatusBayar' => 'LUNAS'], ['IDTransJual' => $idtransjual], 'transpenjualan');
            }
        }

        if ($jurnal['IDTransJurnal']) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil menyimpan data",
                'idjurnal' => $jurnal['IDTransJurnal']
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menyimpan data, mohon isi terlebih dahulu kolom Dibayar Sekarang"
            ]);
        }
    }

    public function editpertransaksi()
    {
        $this->db->trans_begin();
        $insertitemjurnal = false;
        $notranskas = $this->input->post('NoTransKas');
        $totaltransaksi = str_replace(['.', ','], ['', '.'], $this->input->post('TotalTransaksi'));

        $getakun = $this->lokasi->get_akun_penjurnalan('Hutang', 'Tunai');
        $kodeakunkas = $this->input->post('KodeAkun');
        $namaakunkas = $kodeakunkas != '' ? $this->lokasi->getnamaakun($kodeakunkas) : '';

        $getJurnal = $this->crud->get_one_row([
            'select' => 'IDTransJurnal',
            'from' => 'transjurnal',
            'where' => [['NoRefTrans' => $notranskas]]
        ]);
        // hapus jurnal lama
        if ($getJurnal) {
            $deletejurnalitem   = $this->crud->delete(['IDTransJurnal' => $getJurnal['IDTransJurnal']], 'transjurnalitem');
            $deletejurnalinduk  = $this->crud->delete(['IDTransJurnal' => $getJurnal['IDTransJurnal']], 'transjurnal');
        }

        $updatekas = $this->crud->update(['TotalTransaksi' => $totaltransaksi], ['NoTransKas' => $notranskas], 'transaksikas');
        $prefix = "JRN-" . date("Ym");
        $jurnal = [
            'IDTransJurnal' => $this->crud->get_kode([
                'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                'from' => 'transjurnal',
                'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'IDTransJurnal DESC',
                'prefix' => $prefix
            ]),
            'KodeTahun' => $this->input->post('KodeTahun'),
            'TglTransJurnal' => $this->input->post('TanggalTransaksi'),
            'TipeJurnal' => "UMUM",
            'NarasiJurnal' => "Transaksi Hutang",
            'NominalTransaksi' => $totaltransaksi,
            'NoRefTrans' => $notranskas,
            'UserName' => $this->session->userdata('UserName')
        ];
        $insertjurnal = $this->crud->insert($jurnal, 'transjurnal');

        if ($getakun) {
            foreach ($getakun as $keys) {
                $itemjurnal = [
                    'NoUrut' => $keys['NoUrut'],
                    'IDTransJurnal' => $jurnal['IDTransJurnal'],
                    'KodeTahun' => $jurnal['KodeTahun'],
                    'KodeAkun' => ($keys['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $keys['KodeAkun'],
                    'NamaAkun' => ($keys['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $keys['NamaAkun'],
                    'Debet' => ($keys['JenisJurnal'] == 'Debet') ? $jurnal['NominalTransaksi'] : 0,
                    'Kredit' => ($keys['JenisJurnal'] == 'Kredit') ? $jurnal['NominalTransaksi'] : 0,
                    'Uraian' => "Penjurnalan otomatis untuk Transaksi Hutang"
                ];

                $insertjurnalitem[] = $this->crud->insert_or_update($itemjurnal, 'transjurnalitem');
                $insertitemjurnal = true;
            }
        }

        if ($insertitemjurnal) {
            $this->db->trans_commit();
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil mengubah data",
                'idjurnal' => $jurnal['IDTransJurnal'],
                // 'cn' => $check_nil,
                // 'stj' => $status_jurnal
            ]);
        } else {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal mengubah data"
            ]);
        }
    }

    public function check_retur()
    {
        $idretur = $this->input->get('IDTransRetur');

        if ($idretur != null && $idretur != '') {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Transaksi sudah diretur']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Belum pernah retur']);
        }
    }

    public function retur()
    {
        $idtransjual = $this->input->get('IDTransJual');
        $jenisrealisasi = $this->input->get('JenisRealisasi');

        $dtpenjualan = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transpenjualan',
            'where' => [['IDTransJual' => $idtransjual]],
        ]);

        $prefix = "RTJ-" . date("Ym");
        $insertdata['IDTransRetur'] = $this->crud->get_kode([
            'select' => 'RIGHT(IDTransRetur, 7) AS KODE',
            'from'  => 'transaksiretur',
            'where' => [['LEFT(IDTransRetur, 10) =' => $prefix]],
            'limit' => 1,
            'order_by' => 'IDTransRetur DESC',
            'prefix' => $prefix
        ]);
        $insertdata['JenisRetur']       = 'RETUR_JUAL';
        $insertdata['IDTrans']          = $idtransjual;
        $insertdata['KodePerson']       = $dtpenjualan['KodePerson'];
        $insertdata['TanggalTransaksi'] = date('Y-m-d H-i');
        $insertdata['KodeGudang']       = $dtpenjualan['KodeGudang'];
        $insertdata['TotalRetur']       = 0;
        $insertdata['IsRealisasi']      = 0;
        $insertdata['JenisRealisasi']   = $jenisrealisasi;
        $insertdata['IsDijurnalkan']    = 0;
        $insertdata['IsVoid']           = 0;

        $res = $this->crud->insert($insertdata, 'transaksiretur');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'tambah',
                'JenisTransaksi' => 'Retur Penjualan',
                'Description' => 'tambah data retur penjualan ' . $insertdata['IDTransRetur']
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Menambah Data",
                'id' => $insertdata['IDTransRetur']
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Menambah Data"
            ]);
        }
    }
}
