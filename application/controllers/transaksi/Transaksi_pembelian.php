<?php
defined('BASEPATH') or exit('No direct script access allowed');

class transaksi_pembelian extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[13]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[13]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'transbeli';
            $data['title'] = 'Transaksi Pembelian';
            $data['view'] = 'transaksi/v_transaksi_pembelian';
            $data['scripts'] = 'transaksi/s_transaksi_pembelian';

            $supplier = [
                'select' => '*',
                'from' => 'mstperson',
                'where' => [
                    [
                        'IsAktif' => 1,
                        'JenisPerson' => 'SUPPLIER'
                    ]
                ],
                'order_by' => 'KodePerson'
            ];
            $data['supplier'] = $this->crud->get_rows($supplier);

            $po = [
                'select' => 'b.IDTransBeli, b.NoPO, b.KodePerson, p.NamaPersonCP',
                'from' => 'transpembelian b',
                'join' => [
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = b.KodePerson",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['StatusProses' => 'APPROVED']],
            ];
            $data['po'] = $this->crud->get_rows($po);

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

            $cari     = $this->input->get('caribeli');
            if ($cari != '') {
                $configData['filters'][] = " (b.IDTransBeli LIKE '%$cari%' OR b.NoPO LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%' OR b.StatusBayar LIKE '%$cari%' OR br.NamaBarang LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tglbeli'));
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
                [
                    'table' => ' itempembelian i',
                    'on' => "i.IDTransBeli = b.IDTransBeli",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ]
            ];

            $configData['group_by'] = 'b.IDTransBeli';

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.IDTransBeli', 'b.UserPO', 'b.DiskonBawah', 'b.TotalTagihan', 'b.StatusProses', 'b.StatusKirim', 'b.StatusBayar', 'b.NoRef_Manual', 'b.TanggalPembelian', 'b.UraianPembelian', 'b.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'COALESCE(SUM(k.TotalTransaksi), 0) as TotalBayar', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName'
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
                'b.IDTransBeli', 'b.UserPO', 'b.DiskonBawah', 'b.TotalTagihan', 'b.StatusProses', 'b.StatusKirim', 'b.StatusBayar', 'b.NoRef_Manual', 'b.TanggalPembelian', 'b.UraianPembelian', 'b.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'COALESCE(SUM(k.TotalTransaksi), 0) as TotalBayar', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 13; //FiturID di tabel serverfitur
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
                $temp['TanggalPembelian'] = isset($temp['TanggalPembelian']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPembelian']))) . ' ' . date('H:i', strtotime($temp['TanggalPembelian'])) : '';
                if ($canDelete == 1 && $temp['StatusBayar'] == '') {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_pembelian/detail/' . base64_encode($temp['IDTransBeli'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransBeli'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_pembelian/detail/' . base64_encode($temp['IDTransBeli'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
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
            'from' => 'transpembelian',
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
        if (!($this->input->post('IDTransBeli') != null && $this->input->post('IDTransBeli') != '')) {
            $prefix = "TBL-" . date("Ym");
            $insertdata['IDTransBeli'] = $this->crud->get_kode([
                'select' => 'RIGHT(IDTransBeli, 7) AS KODE',
                'where' => [['LEFT(IDTransBeli, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'IDTransBeli DESC',
                'prefix' => $prefix
            ]);
            $insertdata['UserPO']           = $this->session->userdata('ActualName');
            $insertdata['TotalNilaiBarang'] = 0;
            $insertdata['TotalTagihan']     = 0;
            $insertdata['StatusKirim']      = 'BELUM';
            $insertdata['StatusBayar']      = 'BELUM';
            $insertdata['IsVoid']           = 0;
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'transpembelian');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('IDTransBeli') : $insertdata['IDTransBeli'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Transaksi Pembelian',
                'Description' => $ket . ' data transaksi pembelian ' . $id
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data"),
                'id' => $insertdata['IDTransBeli']
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
        $kode  = $this->input->get('IDTransBeli');
        $cekPO = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'transpembelian',
                'where' => [['IDTransBeli' => $kode]],
            ]
        );

        $cekbayarHutang = $this->crud->get_count(
            [
                'select' => '*',
                'from' => 'transaksikas',
                'where' => [['NoRef_Sistem' => $kode]],
            ]
        );

        $cekterimaBarang = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'transaksibarang',
                'where' => [['NoRefTrSistem' => $kode]],
            ]
        );

        $this->db->trans_begin();
        if ($cekterimaBarang) {
            $deletetrbarangitem  = $this->crud->delete(['NoTrans' => $cekterimaBarang['NoTrans']], 'itemtransaksibarang'); // hapus data di item tr barang
            $deletetrbaranginduk = $this->crud->delete(['NoTrans' => $cekterimaBarang['NoTrans']], 'transaksibarang'); // hapus data di tr barang induk
        }

        $cekKas = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transaksikas',
            'where' => [[
                'NoRef_Sistem' => $kode,
                'JenisTransaksiKas' => 'DP PEMBELIAN',
            ]],
        ]);

        $noreftrans = ($cekKas != null && $cekKas != '') ? $cekKas['NoTransKas'] : $kode;
        $getJurnal = $this->crud->get_one_row([
            'select' => 'IDTransJurnal',
            'from' => 'transjurnal',
            'where' => [['NoRefTrans' => $noreftrans]],
        ]);

        if ($getJurnal) {
            $deletejurnalitem   = $this->crud->delete(['IDTransJurnal' => $getJurnal['IDTransJurnal']], 'transjurnalitem'); // hapus data di item jurnal
            $deletejurnalinduk  = $this->crud->delete(['IDTransJurnal' => $getJurnal['IDTransJurnal']], 'transjurnal'); // hapus data di jurnal induk
        }

        if ($cekKas) {
            $deletekas = $this->crud->delete(['NoRef_Sistem' => $kode], 'transaksikas'); // hapus data di transaksi kas
        }

        // Hapus item pembelian dan transaksi pembelian
        $item = $this->crud->update(['IsVoid' => 1], ['IDTransBeli' => $kode], 'itempembelian');
        $res = $this->crud->update(['IsVoid' => 1], ['IDTransBeli' => $kode], 'transpembelian');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->db->trans_commit();
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Cetak Pembelian (PO)',
                'Description' => 'hapus data transaksi pembelian ' . $kode
            ]);
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
    }

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[13]);
        $idtransbeli   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_beli';
            $data['title'] = 'Detail Transaksi Pembelian';
            $data['view'] = 'transaksi/v_transaksi_pembelian_detail';
            $data['scripts'] = 'transaksi/s_transaksi_pembelian_detail';

            $dtbarang = [
                'select' => '*',
                'from' => 'mstbarang',
                'where' => [['IsAktif' => 1]]
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

            $tahunaktif = $this->akses->get_tahun_aktif();
            $noreftrans = ($dtinduk['NoTransKas'] != null && $dtinduk['NoTransKas'] != '') ? $dtinduk['NoTransKas'] : $dtinduk['IDTransBeli'];
            $jurnal = $this->crud->get_one_row([
                'select' => 'IDTransJurnal, KodeTahun',
                'from' => 'transjurnal',
                'where' => [['NoRefTrans' => $noreftrans]]
            ]);
            $tahuntransaksi = $jurnal ? $jurnal['KodeTahun'] : $tahunaktif;

            $data['exptahun'] = ($tahuntransaksi != $tahunaktif) ? 1 : 0;

            $data['counthutang'] = $this->crud->get_count([
                'select' => '*',
                'from' => 'transaksikas',
                'where' => [[
                    'NoRef_Sistem' => $idtransbeli,
                    'JenisTransaksiKas' => 'BAYAR HUTANG',
                ]]
            ]);

            $item = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'itempembelian',
                    'where' => [['IDTransBeli' => $idtransbeli]]
                ]
            );
            $tagihanawal = 0;
            $hd = [];
            $totalAsli = 0;
            $hp = [];
            $i = 0;
            foreach ($item as $items) {
                $hd[$i] = ($items['HargaSatuan'] - $items['Diskon']) * $items['Qty'];
                $tagihanawal += $hd[$i];

                $hp[$i] = $items['HargaSatuan'] * $items['Qty'];
                $totalAsli += $hp[$i];

                $i++;
            }
            $data['tagihanawal'] = $tagihanawal;
            $data['tanpadiskon'] = $totalAsli;

            $last_id = $this->crud->get_one_row(
                [
                    'select' => 'IDTransBeli',
                    'from' => 'transpembelian',
                    'order_by' => 'IDTransBeli DESC',
                ]
            );
            $data['last_id'] = $last_id['IDTransBeli'];
            $data['IDTransBeli'] = $idtransbeli;

            // memo jurnal
            $cekKas = $this->crud->get_one_row([
                'select' => 'NoTransKas',
                'from' => 'transaksikas',
                'where' => [[
                    'NoRef_Sistem' => $idtransbeli,
                    'JenisTransaksiKas' => 'DP PEMBELIAN',
                ]],
            ]);
            if ($cekKas) {
                $where = ['j.NoRefTrans' => $cekKas['NoTransKas']];
            } else {
                $where = [
                    'j.NoRefTrans' => $idtransbeli,
                    'LEFT(j.NarasiJurnal, 11) =' => 'Transaksi P',
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
                    ['IDTrans' => $idtransbeli],
                    ['IsVoid' => 0]
                ],
            ]);

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
            $idtransbeli   = $this->input->get('idtransbeli');
            $configData['table'] = 'itempembelian i';
            $configData['where'] = [['i.IDTransBeli'  => $idtransbeli]];

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
                    'table' => ' transpembelian t',
                    'on' => "t.IDTransBeli = i.IDTransBeli",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.NoRef_Sistem = t.IDTransBeli AND k.JenisTransaksiKas = 'BAYAR HUTANG'",
                    'param' => 'LEFT',
                ],
            ];

            $configData['group_by'] = 'i.IDTransBeli, i.NoUrut';

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.IDTransBeli', 'i.NoUrut', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Diskon', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.NoPO', 't.TglPO', 't.StatusProses', 't.StatusBayar', 't.StatusKirim', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'k.NoTransKas'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.IDTransBeli', 'i.NoUrut', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Diskon', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.NoPO', 't.TglPO', 't.StatusProses', 't.StatusBayar', 't.StatusKirim', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'k.NoTransKas',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 13; //FiturID di tabel serverfitur
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
                $temp['Diskon'] = isset($temp['Diskon']) ? $temp['Diskon'] : 0;
                if ($canEdit == 1 && $canDelete == 1 && $temp['StatusKirim'] == 'BELUM' && $temp['NoTransKas'] == null && $exptahun == 0) {
                    if ($temp['NoPO'] == null) {
                        $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransBeli'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                    } else {
                        $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                    }
                } elseif ($canEdit == 1 && $canDelete != 1 && $temp['StatusKirim'] == 'BELUM' && $temp['NoTransKas'] == null && $exptahun == 0) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1 && $temp['StatusKirim'] == 'BELUM' && $temp['NoTransKas'] == null && $exptahun == 0) {
                    if ($temp['NoPO'] == null) {
                        $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['IDTransBeli'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
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
        $idtransbeli   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('transaksi/transaksi_pembelian/detail/') . $this->uri->segment(4);

        $dtinduk = [
            'select' => 't.IDTransBeli, t.PPN, t.NoPO, t.DiskonBawah, t.NominalBelumPajak, t.TotalTagihan, t.StatusProses, t.StatusBayar, t.NoRef_Manual, t.TanggalPembelian, t.UraianPembelian, p.KodePerson, p.NamaPersonCP, p.NamaUsaha, k.NoTransKas, k.TotalTransaksi',
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
                ]
            ],
            'where' => [['t.IDTransBeli' => $idtransbeli]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);

        $data['kodePO'] = isset($data['dtinduk']['NoPO']) ? $data['dtinduk']['IDTransBeli'] : '-';
        $data['diskonbawah'] = isset($data['dtinduk']['DiskonBawah']) ? $data['dtinduk']['DiskonBawah'] : 0;
        $data['ppn'] = isset($data['dtinduk']['PPN']) ? $data['dtinduk']['PPN'] : 0;
        $data['totaltagihan'] = isset($data['dtinduk']['TotalTagihan']) ? $data['dtinduk']['TotalTagihan'] : 0;

        $sql = [
            'select' => 'i.IDTransBeli, i.NoUrut, i.Spesifikasi, i.HargaSatuan, i.Qty, i.Diskon, i.Total, i.SatuanBarang, i.Deskripsi, i.KodeBarang, br.NamaBarang',
            'from' => 'itempembelian i',
            'join' => [
                [
                    'table' => ' mstbarang br',
                    'on' => " br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['i.IDTransBeli' => $idtransbeli]],
        ];
        $data['model'] = $this->crud->get_rows($sql);

        $this->load->library('Pdf');
        $this->load->view('transaksi/v_transaksi_pembelian_detail_cetak', $data);
    }

    public function simpandetail()
    {
        $insertdata = $this->input->post();
        unset($insertdata['TotalLama']);
        unset($insertdata['JenisDiskon']);
        unset($insertdata['Diskon']);
        $jenisdiskon = $this->input->post('JenisDiskon');
        $diskon = str_replace(['.', ','], ['', '.'], $this->input->post('Diskon'));
        $isEdit = true;

        // Mengambil data induk di tabel transpembelian
        $induk = $this->crud->get_one_row(
            [
                'select'=> '*',
                'from'  => 'transpembelian',
                'where' => [['IDTransBeli' => $this->input->post('IDTransBeli')]]
            ]
        );

        ## POST DATA
        if (!($this->input->post('NoUrut') != null && $this->input->post('NoUrut') != '')) {
            $isEdit = false;
            $getNoUrut = $this->db->from('itempembelian')
            ->where('IDTransBeli', $this->input->post('IDTransBeli'))
            ->select('NoUrut')
            ->order_by('NoUrut', 'desc')
            ->get()->row();
            if ($getNoUrut) {
                $NoUrut = (int)$getNoUrut->NoUrut;
            } else {
                $NoUrut = 0;
            }
            $insertdata['NoUrut'] = $NoUrut + 1;
            $insertdata['HargaSatuan'] = str_replace(['.', ','], ['', '.'], $this->input->post('HargaSatuan'));
            $insertdata['Qty'] = str_replace(['.', ','], ['', '.'], $this->input->post('Qty')); // $this->input->post('Qty');
            $insertdata['IsVoid'] = 0;
            // $insertdata['HPPSaatBeli']  = 0;
            if ($jenisdiskon != null && $diskon != null) {
                if ($jenisdiskon == 'Nominal') {
                    $insertdata['Diskon'] = $diskon;
                } elseif ($jenisdiskon == 'Persen') {
                    $insertdata['Diskon'] = ($diskon / 100 * $insertdata['HargaSatuan']);
                } else {
                    $insertdata['Diskon'] = 0;
                }
                $insertdata['Total'] = ($insertdata['HargaSatuan'] - $insertdata['Diskon']) * $insertdata['Qty'];
            } else {
                $insertdata['Diskon'] = 0;
                $insertdata['Total'] = $insertdata['HargaSatuan'] * $insertdata['Qty'];
            }

            // Menambahkan total ke total nilai barang di tabel transpembelian
            if ($induk['TotalTagihan'] > 0) {
                $totaltagihan = $induk['TotalTagihan'] + $insertdata['Total'];
            } else {
                $totaltagihan = $insertdata['Total'];
            }
            // $updatetagihan = $this->crud->update(['TotalTagihan' => $totaltagihan], ['IDTransBeli' => $this->input->post('IDTransBeli')], 'transpembelian');

            $res = $this->crud->insert($insertdata, 'itempembelian');
        } else {
            $isEdit = true;
            $updatedata['HargaSatuan']  = str_replace(['.', ','], ['', '.'], $this->input->post('HargaSatuan'));
            $updatedata['Qty']          = $this->input->post('Qty');
            $updatedata['SatuanBarang'] = $this->input->post('SatuanBarang');
            $updatedata['Spesifikasi']  = $this->input->post('Spesifikasi');
            // $updatedata['HPPSaatBeli']  = 0;
            if ($jenisdiskon != null && $diskon != null) {
                if ($jenisdiskon == 'Nominal') {
                    $updatedata['Diskon'] = $diskon;
                } elseif ($jenisdiskon == 'Persen') {
                    $updatedata['Diskon'] = ($diskon / 100 * $updatedata['HargaSatuan']);
                } else {
                    $updatedata['Diskon'] = 0;
                }
                $updatedata['Total'] = ($updatedata['HargaSatuan'] - $updatedata['Diskon']) * $updatedata['Qty'];
            } elseif ($diskon != null && $jenisdiskon == null) {
                $updatedata['Diskon'] = $diskon;
                $updatedata['Total'] = ($updatedata['HargaSatuan'] - $updatedata['Diskon']) * $updatedata['Qty'];
            } else {
                $updatedata['Diskon'] = 0;
                $updatedata['Total'] = $updatedata['HargaSatuan'] * $updatedata['Qty'];
            }

            // Mengubah total ke total nilai barang di tabel transpembelian
            if ($induk['TotalTagihan'] > 0) {
                $totaltagihan = $induk['TotalTagihan'] - $this->input->post('TotalLama') + $updatedata['Total'];
            } else {
                $totaltagihan = $updatedata['Total'];
            }
            // $updatetagihan = $this->crud->update(['TotalTagihan' => $totaltagihan], ['IDTransBeli' => $this->input->post('IDTransBeli')], 'transpembelian');
            $res = $this->crud->update($updatedata, ['IDTransBeli' => $this->input->post('IDTransBeli'), 'NoUrut' => $this->input->post('NoUrut')], 'itempembelian');
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
        $kode  = $this->input->get('IDTransBeli');
        $kode2 = $this->input->get('NoUrut');

        $res = $this->crud->delete(['IDTransBeli' => $kode, 'NoUrut' => $kode2], 'itempembelian');
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

    public function simpanpembelian()
    {
        $this->db->trans_begin();
        $totaltransaksi = str_replace(['.', ','], ['', '.'], $this->input->post('TotalTransaksi'));

        $updatedata['NoRef_Manual']     = $this->input->post('NoRef_Manual');
        $updatedata['TanggalPembelian'] = $this->input->post('TanggalPembelian');
        $updatedata['UraianPembelian']  = $this->input->post('UraianPembelian');
        $updatedata['StatusProses']     = 'DONE';

        $item = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'itempembelian',
                'where' => [['IDTransBeli' => $this->input->post('IDTransBeli')]]
            ]
        );

        $totaldiskonatas = 0;
        $totalhargaasli = 0;
        $totalbarangbeli = 0;
        foreach ($item as $items) {
            $barang = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'mstbarang',
                'where' => [['KodeBarang' => $items['KodeBarang']]],
            ]);

            $hppbeli = $barang['NilaiHPP'];
            $updatehppbeli[] = $this->crud->update(
                [
                    'HPPSaatBeli' => $hppbeli
                ],
                [
                    'IDTransBeli' => $this->input->post('IDTransBeli'),
                    'NoUrut'      => $items['NoUrut'],
                ],
                'itempembelian'
            );

            $totaldiskonatas += $items['Diskon'] * $items['Qty'];
            $totalhargaasli += $items['HargaSatuan'] * $items['Qty'];
            $totalbarangbeli += $items['Qty'];
        }

        $updatedata['DiskonBawah'] = $this->input->post('NilaiDiskon');
        $updatedata['PPN'] = str_replace(['.', ','], ['', '.'], $this->input->post('NilaiPPN'));
        $updatedata['NominalBelumPajak'] = $this->input->post('NominalBelumPajak');
        $updatedata['TotalTagihan'] = str_replace(['.', ','], ['', '.'], $this->input->post('TagihanAkhir'));
        $updatedata['TotalNilaiBarangReal'] = $totalhargaasli;
        $diskontotal = $totaldiskonatas + $updatedata['DiskonBawah'];

        $tahun = $this->akses->get_tahun_aktif();
        $kodeakunkas = $this->input->post('KodeAkun');
        $namaakunkas = ($kodeakunkas != '') ? $this->lokasi->getnamaakun($kodeakunkas) : '';

        $akuntunai = $this->lokasi->get_akun_penjurnalan('Pembelian', 'Tunai');
        $akunkredit = $this->lokasi->get_akun_penjurnalan('Pembelian', 'Kredit');
        $akuntunaippn = $this->lokasi->get_akun_penjurnalan('Transaksi Pembelian PPN', 'Tunai');
        $akunkreditppn = $this->lokasi->get_akun_penjurnalan('Transaksi Pembelian PPN', 'Kredit');

        // hapus jurnal lama
        $noreftrans = ($this->input->post('NoTransKas') != null && $this->input->post('NoTransKas') != '') ? $this->input->post('NoTransKas') : $this->input->post('IDTransBeli');
        $getjurnalOld = $this->crud->get_one_row([
            'select' => 'IDTransJurnal',
            'from' => 'transjurnal',
            'where' => [['NoRefTrans' => $noreftrans]],
        ]);
        if ($getjurnalOld) {
            $deletejurnalitem   = $this->crud->delete(['IDTransJurnal' => $getjurnalOld['IDTransJurnal']], 'transjurnalitem');
            $deletejurnalinduk  = $this->crud->delete(['IDTransJurnal' => $getjurnalOld['IDTransJurnal']], 'transjurnal');
        }

        $notransbrg = $this->input->post('NoTrans');
        if ($notransbrg != null) {
            $deleteitembarang = $this->crud->delete(['NoTrans' => $notransbrg], 'itemtransaksibarang'); // hapus item transaksi barang lama
            $deletetrbarang   = $this->crud->delete(['NoTrans' => $notransbrg], 'transaksibarang'); // hapus transaksi barang lama
            $prefixbrg = "TBR-" . date("Ym");
            $insertbrg['NoTrans'] = $this->crud->get_kode([
                'select' => 'RIGHT(NoTrans, 7) AS KODE',
                'from' => 'transaksibarang',
                'where' => [['LEFT(NoTrans, 10) =' => $prefixbrg]],
                'limit' => 1,
                'order_by' => 'NoTrans DESC',
                'prefix' => $prefixbrg
            ]);
            $insertbrg['TanggalTransaksi']  = date('Y-m-d H:i');
            $insertbrg['UserName']          = $this->session->userdata('UserName');
            $insertbrg['KodePerson']        = $this->input->post('KodePerson');
            $insertbrg['JenisTransaksi']    = 'BARANG DATANG';
            $insertbrg['NoRefTrSistem']     = $this->input->post('IDTransBeli');
            $insertbrg['GudangTujuan']      = $this->input->post('KodeGudang');
            $insertbrg['IsHapus']           = 0;
            $simpantrbrg = $this->crud->insert($insertbrg, 'transaksibarang'); // simpan transaksi barang

            $insertitembarang = [];
            foreach ($item as $items) {
                $dtitemtrbrg = [
                    'NoUrut'        => $items['NoUrut'],
                    'NoTrans'       => $insertbrg['NoTrans'],
                    'KodeBarang'    => $items['KodeBarang'],
                    'Qty'           => $items['Qty'],
                    'HargaSatuan'   => $items['HargaSatuan'],
                    'Total'         => $items['HargaSatuan'] * $items['Qty'],
                    'SatuanBarang'  => $items['SatuanBarang'],
                    'JenisStok'     => 'MASUK',
                    'GudangTujuan'  => $insertbrg['GudangTujuan'],
                    'IsHapus'       => 0
                ];
                $insertitembarang[] = $this->crud->insert($dtitemtrbrg, 'itemtransaksibarang'); // simpan item transaksi barang
            }
        } else {
            $prefixbrg = "TBR-" . date("Ym");
            $insertbrg['NoTrans'] = $this->crud->get_kode([
                'select' => 'RIGHT(NoTrans, 7) AS KODE',
                'from' => 'transaksibarang',
                'where' => [['LEFT(NoTrans, 10) =' => $prefixbrg]],
                'limit' => 1,
                'order_by' => 'NoTrans DESC',
                'prefix' => $prefixbrg
            ]);
            $insertbrg['TanggalTransaksi']  = date('Y-m-d H:i');
            $insertbrg['UserName']          = $this->session->userdata('UserName');
            $insertbrg['KodePerson']        = $this->input->post('KodePerson');
            $insertbrg['JenisTransaksi']    = 'BARANG DATANG';
            $insertbrg['NoRefTrSistem']     = $this->input->post('IDTransBeli');
            $insertbrg['GudangTujuan']      = $this->input->post('KodeGudang');
            $insertbrg['IsHapus']           = 0;
            $simpantrbrg = $this->crud->insert($insertbrg, 'transaksibarang'); // simpan transaksi barang

            $updatedatabarang = [];
            $insertitembarang = [];
            foreach ($item as $items) {
                ## Perhitungan HPP
                $kodebarang = $items['KodeBarang'];
                $getdtbarang = $this->crud->get_one_row([
                    'select' => 'NilaiHPP',
                    'from' => 'mstbarang',
                    'where' => [['KodeBarang' => $kodebarang]],
                ]);

                $stok        = $this->lokasi->get_stok_asli($kodebarang);
                $stoksistem  = isset($stok['stok']) ? (int)$stok['stok'] : 0;
                $hpp         = $this->lokasi->get_hpp_sistem($kodebarang);
                $hppsistem   = isset($hpp) ? $hpp : 0;
                $stokdatang  = $items['Qty']; // pengganti jumlah beli ygy
                $totalbarang = $totalbarangbeli;
                $diskonbawah = $updatedata['DiskonBawah'];
                $ppn         = $updatedata['PPN'];
                $hargasatuan = $items['HargaSatuan'];

                // menentukan harga beli
                if ($diskonbawah > 0 && $ppn == 0) {
                    $hargabeli = $hargasatuan - ($diskonbawah / $totalbarang);
                } elseif ($ppn > 0 && isset($diskonbawah)) {
                    $hargabeli = $hargasatuan + ($ppn / $totalbarang) - ($diskonbawah / $totalbarang);
                } else {
                    $hargabeli = $hargasatuan;
                }

                $hasilhpp = (($stoksistem * $hppsistem) + ($stokdatang * $hargabeli)) / ($stoksistem + $stokdatang);

                if ($getdtbarang['NilaiHPP'] == 0) {
                    $dtbrg = [
                        'HargaBeliTerakhir' => $hargabeli,
                        'NilaiHPP'          => $hargabeli,
                    ];
                } else {
                    $dtbrg = [
                        'HargaBeliTerakhir' => $hargabeli,
                        'NilaiHPP'          => $hasilhpp,
                    ];
                }
                $updatedatabarang[] = $this->crud->update($dtbrg, ['KodeBarang' => $kodebarang], 'mstbarang'); // update hpp

                $dtitemtrbrg = [
                    'NoUrut'        => $items['NoUrut'],
                    'NoTrans'       => $insertbrg['NoTrans'],
                    'KodeBarang'    => $items['KodeBarang'],
                    'Qty'           => $items['Qty'],
                    'HargaSatuan'   => $items['HargaSatuan'],
                    'Total'         => $items['HargaSatuan'] * $items['Qty'],
                    'SatuanBarang'  => $items['SatuanBarang'],
                    'JenisStok'     => 'MASUK',
                    'GudangTujuan'  => $insertbrg['GudangTujuan'],
                    'IsHapus'       => 0
                ];
                $insertitembarang[] = $this->crud->insert($dtitemtrbrg, 'itemtransaksibarang'); // simpan item transaksi barang
            }
        }

        $status_jurnal = $this->lokasi->setting_jurnal_status();

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
                $insertkas['TanggalTransaksi']  = $this->input->post('TanggalPembelian');
                $insertkas['NoRef_Sistem']      = $this->input->post('IDTransBeli');
                $insertkas['NoRef_Manual']      = $this->input->post('NoRef_Manual');
                $insertkas['Uraian']            = $this->input->post('UraianPembelian');
                $insertkas['UserName']          = $this->session->userdata('UserName');
                $insertkas['KodePerson']        = $this->input->post('KodePerson');
                $insertkas['NominalBelumPajak'] = $updatedata['NominalBelumPajak'];
                $insertkas['PPN']               = $updatedata['PPN'];
                $insertkas['TotalTransaksi']    = $totaltransaksi;
                $insertkas['JenisTransaksiKas'] = 'DP PEMBELIAN';
                $insertkas['IsDijurnalkan']     = 0;
                $insertkas['Diskon']            = $updatedata['DiskonBawah'];

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
                $insertjurnal['NarasiJurnal']       = ($updatedata['StatusBayar'] == 'SEBAGIAN') ? "Transaksi Pembelian Kredit" : "Transaksi Pembelian Tunai";
                $insertjurnal['NominalTransaksi']   = $updatedata['TotalNilaiBarangReal'] + $updatedata['PPN'];
                $insertjurnal['NoRefTrans']         = $insertkas['NoTransKas'];
                $insertjurnal['UserName']           = $this->session->userdata['UserName'];

                $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');

                if ($status_jurnal == 'on') {
                    // jika status jurnal otomatis
                    if (count($akuntunai) > 0 && count($akunkredit) > 0 && count($akuntunaippn) > 0 && count($akunkreditppn)) {
                        if ($updatedata['StatusBayar'] == 'SEBAGIAN') { // jika dibayar sebagian
                            if ($updatedata['PPN'] > 0) {
                                foreach ($akunkreditppn as $key) {
                                    if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                        $debet = $updatedata['TotalNilaiBarangReal'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'PPn') {
                                        $debet = $updatedata['PPN'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = 0;
                                        $kredit = $diskontotal;
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = 0;
                                        $kredit = $totaltransaksi;
                                    } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                        $debet = 0;
                                        $kredit = $insertjurnal['NominalTransaksi'] - ($diskontotal + $totaltransaksi);
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
                                        $debet = $updatedata['TotalNilaiBarangReal'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = 0;
                                        $kredit = $diskontotal;
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = 0;
                                        $kredit = $totaltransaksi;
                                    } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                        $debet = 0;
                                        $kredit = $insertjurnal['NominalTransaksi'] - ($diskontotal + $totaltransaksi);
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
                            }
                        } else { // jika dibayar lunas
                            if ($updatedata['PPN'] > 0) {
                                foreach ($akuntunaippn as $key) {
                                    if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                        $debet = $updatedata['TotalNilaiBarangReal'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'PPn') {
                                        $debet = $updatedata['PPN'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = 0;
                                        $kredit = $totaltransaksi;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = 0;
                                        $kredit = $diskontotal;
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
                                        $debet = $updatedata['TotalNilaiBarangReal'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = 0;
                                        $kredit = $totaltransaksi;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = 0;
                                        $kredit = $diskontotal;
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
                            }
                        }
                    }
                } else {
                    // jika status jurnal manual
                }

                $kas = $this->crud->insert($insertkas, 'transaksikas');
            } else {
                $insertkas['KodeTahun']         = $tahun;
                $insertkas['TanggalTransaksi']  = $this->input->post('TanggalPembelian');
                $insertkas['NoRef_Sistem']      = $this->input->post('IDTransBeli');
                $insertkas['NoRef_Manual']      = $this->input->post('NoRef_Manual');
                $insertkas['Uraian']            = $this->input->post('UraianPembelian');
                $insertkas['UserName']          = $this->session->userdata('UserName');
                $insertkas['KodePerson']        = $this->input->post('KodePerson');
                $insertkas['NominalBelumPajak'] = $updatedata['NominalBelumPajak'];
                $insertkas['PPN']               = $updatedata['PPN'];
                $insertkas['TotalTransaksi']    = $totaltransaksi;
                $insertkas['JenisTransaksiKas'] = 'DP PEMBELIAN';
                $insertkas['IsDijurnalkan']     = 0;
                $insertkas['Diskon']            = $updatedata['DiskonBawah'];

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
                $insertjurnal['NarasiJurnal']       = ($updatedata['StatusBayar'] == 'SEBAGIAN') ? "Transaksi Pembelian Kredit" : "Transaksi Pembelian Tunai";
                $insertjurnal['NominalTransaksi']   = $updatedata['TotalNilaiBarangReal'] + $updatedata['PPN'];
                $insertjurnal['NoRefTrans']         = $this->input->post('NoTransKas');
                $insertjurnal['UserName']           = $this->session->userdata['UserName'];

                $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');

                if ($status_jurnal == 'on') {
                    // jika status jurnal otomatis
                    if (count($akuntunai) > 0 && count($akunkredit) > 0 && count($akuntunaippn) > 0 && count($akunkreditppn)) {
                        if ($updatedata['StatusBayar'] == 'SEBAGIAN') { // jika dibayar sebagian
                            if ($updatedata['PPN'] > 0) {
                                foreach ($akunkreditppn as $key) {
                                    if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                        $debet = $updatedata['TotalNilaiBarangReal'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'PPn') {
                                        $debet = $updatedata['PPN'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = 0;
                                        $kredit = $diskontotal;
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = 0;
                                        $kredit = $totaltransaksi;
                                    } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                        $debet = 0;
                                        $kredit = $insertjurnal['NominalTransaksi'] - ($diskontotal + $totaltransaksi);
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
                                        $debet = $updatedata['TotalNilaiBarangReal'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = 0;
                                        $kredit = $diskontotal;
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = 0;
                                        $kredit = $totaltransaksi;
                                    } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                        $debet = 0;
                                        $kredit = $insertjurnal['NominalTransaksi'] - ($diskontotal + $totaltransaksi);
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
                            }
                        } else { // jika dibayar lunas
                            if ($updatedata['PPN'] > 0) {
                                foreach ($akuntunaippn as $key) {
                                    if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                        $debet = $updatedata['TotalNilaiBarangReal'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'PPn') {
                                        $debet = $updatedata['PPN'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = 0;
                                        $kredit = $totaltransaksi;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = 0;
                                        $kredit = $diskontotal;
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
                                        $debet = $updatedata['TotalNilaiBarangReal'];
                                        $kredit = 0;
                                    } elseif ($key['StatusAkun'] == 'Kas') {
                                        $debet = 0;
                                        $kredit = $totaltransaksi;
                                    } elseif ($key['StatusAkun'] == 'Diskon') {
                                        $debet = 0;
                                        $kredit = $diskontotal;
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
                    'where' => [['NoRef_Sistem' => $this->input->post('IDTransBeli')]],
                ]
            );
            if ($cekKas > 0) {
                $kas = $this->crud->delete(['NoRef_Sistem' => $this->input->post('IDTransBeli')], 'transaksikas');
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
            $insertjurnal['TglTransJurnal']     = $this->input->post('TanggalPembelian');
            $insertjurnal['TipeJurnal']         = "UMUM";
            $insertjurnal['NarasiJurnal']       = "Transaksi Pembelian Kredit";
            $insertjurnal['NominalTransaksi']   = $updatedata['TotalNilaiBarangReal'] + $updatedata['PPN'];
            $insertjurnal['NoRefTrans']         = $this->input->post('IDTransBeli');
            $insertjurnal['UserName']           = $this->session->userdata['UserName'];

            $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');

            if ($status_jurnal == 'on') {
                // jika status jurnal otomatis
                if (count($akunkredit) > 0 && count($akunkreditppn) > 0) {
                    if ($updatedata['PPN'] > 0) {
                        foreach ($akunkreditppn as $key) {
                            if ($key['StatusAkun'] == 'Pembelian/Penjualan') {
                                $debet = $updatedata['TotalNilaiBarangReal'];
                                $kredit = 0;
                            } elseif ($key['StatusAkun'] == 'PPn') {
                                $debet = $updatedata['PPN'];
                                $kredit = 0;
                            } elseif ($key['StatusAkun'] == 'Diskon') {
                                $debet = 0;
                                $kredit = $diskontotal;
                            } elseif ($key['StatusAkun'] == 'Kas') {
                                $debet = 0;
                                $kredit = 0;
                            } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                $debet = 0;
                                $kredit = $insertjurnal['NominalTransaksi'] - $diskontotal;
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
                                $debet = $updatedata['TotalNilaiBarangReal'];
                                $kredit = 0;
                            } elseif ($key['StatusAkun'] == 'Diskon') {
                                $debet = 0;
                                $kredit = $diskontotal;
                            } elseif ($key['StatusAkun'] == 'Kas') {
                                $debet = 0;
                                $kredit = 0;
                            } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                $debet = 0;
                                $kredit = $insertjurnal['NominalTransaksi'] - $diskontotal;
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
                    }
                }
            } else {
                // jika status jurnal manual
            }
        }

        $res = $this->crud->update($updatedata, ['IDTransBeli' => $this->input->post('IDTransBeli')], 'transpembelian');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'edit',
                'JenisTransaksi' => 'Transaksi Pembelian',
                'Description' => 'update data transaksi pembelian ' . $this->input->post('IDTransBeli')
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

    public function kirimpembayaran()
    {
        checkAccess($this->session->userdata('fiturview')[13]);
        $idtransbeli   = escape(base64_decode($this->uri->segment(4)));
        $kodeperson   = escape(base64_decode($this->uri->segment(5)));

        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_beli';
            $data['title'] = 'Kirim Pembayaran';
            $data['view'] = 'transaksi/v_kirim_pembayaran';
            $data['scripts'] = 'transaksi/s_kirim_pembayaran';

            $dtinduk = [
                'select' => '*',
                'from' => 'transpembelian b',
                'join' => [
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = b.KodePerson",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['b.IDTransBeli' => $idtransbeli]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['IDTransBeli'] = $idtransbeli;
            $data['KodePerson'] = $kodeperson;

            $data['countdata'] = $this->crud->get_count([
                'select' => '*',
                'from' => 'transpembelian b',
                'where' => [
                    [
                        'b.StatusBayar !=' => 'LUNAS',
                        'b.KodePerson' => $kodeperson,
                        'b.StatusProses' => 'DONE',
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
            $configData['table'] = 'transpembelian b';
            $configData['where'] = [[
                'b.StatusBayar !=' => 'LUNAS',
                'b.KodePerson' => $kodeperson,
                'b.StatusProses' => 'DONE',
                'b.IsVoid' => 0
            ]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (b.IDTransBeli LIKE '%$cari%')";
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
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.IDTransBeli', 'b.TotalTagihan', 'b.StatusBayar', 'b.TanggalPembelian', 'b.UraianPembelian', 'b.KodePerson', 'p.NamaPersonCP', 'SUM(k.TotalTransaksi) as TotalBayar', 'b.CatatanKirimPembayaran AS BayarSekarang', 'b.NominalBelumPajak', 'b.PPN', 'b.PPh', 'b.DiskonBawah'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['group_by'] = 'b.IDTransBeli';

            $configData['display_column'] = [
                false,
                'b.IDTransBeli', 'b.TotalTagihan', 'b.StatusBayar', 'b.TanggalPembelian', 'b.UraianPembelian', 'b.KodePerson', 'p.NamaPersonCP', 'SUM(k.TotalTransaksi) as TotalBayar', 'b.CatatanKirimPembayaran AS BayarSekarang', 'b.NominalBelumPajak', 'b.PPN', 'b.PPh', 'b.DiskonBawah',
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
                $temp['totalhistory'] = count($this->lokasi->get_history_bayar($temp['IDTransBeli']));
                $temp['dthistory'] = $this->lokasi->get_history_bayar($temp['IDTransBeli']);
                $temp['TotalTagihan'] = isset($temp['TotalTagihan']) ? $temp['TotalTagihan'] : 0;
                $temp['TotalBayar'] = isset($temp['TotalBayar']) ? $temp['TotalBayar'] : 0;
                $temp['SisaTagihan'] = $temp['TotalTagihan'] - $temp['TotalBayar'];
                $temp['TanggalPembelian'] = isset($temp['TanggalPembelian']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPembelian']))) : '';
                $temp['DibayarSekarang'] = '<input type="text" style="width: 75%;" class="form-control" name="TotalTransaksi[]" id="Bayar'.$temp['no'].'" value="">
                                            <input type="hidden" class="form-control" name="IDTransBeli[]" value="'.$temp['IDTransBeli'].'">
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
                $temp['btn_aksi'] = '<a class="simpanperrow btn btn-sm btn-primary" type="button" data-kode="' . $temp['IDTransBeli'] . '" data-kode2="' . $temp['no'] . '" title=""><span aria-hidden="true">Simpan</span></a>';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function kirimpembayaranproses()
    {
        $this->db->trans_begin();
        $idtransbeli    = $this->input->post('IDTransBeli');
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

        $getakun = $this->lokasi->get_akun_penjurnalan('Hutang', 'Tunai');
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
        foreach ($idtransbeli as $key => $value) {
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
                    'JenisTransaksiKas' => 'BAYAR HUTANG',
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
                    'NarasiJurnal' => "Transaksi Hutang",
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
                                'Uraian' => "Penjurnalan otomatis untuk Transaksi Hutang"
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
                $res[] = $this->crud->update(['StatusBayar' => 'SEBAGIAN'], ['IDTransBeli' => $value], 'transpembelian');

                // ## INSERT TO SERVER LOG
                // $log[] = $this->logsrv->insert_log([
                //     'Action' => 'tambah',
                //     'JenisTransaksi' => 'Transaksi Hutang',
                //     'Description' => 'tambah data transaksi hurang ' . $value
                // ]);
            } else {
                $res[] = $this->crud->update(['StatusBayar' => 'LUNAS'], ['IDTransBeli' => $value], 'transpembelian');

                // ## INSERT TO SERVER LOG
                // $log[] = $this->logsrv->insert_log([
                //     'Action' => 'tambah',
                //     'JenisTransaksi' => 'Transaksi Hutang',
                //     'Description' => 'tambah data transaksi hurang ' . $value
                // ]);
            }
        }

        if ($res) {
            $this->db->trans_commit();
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil menyimpan data",
                'idjurnal' => $jurnal['IDTransJurnal'],
                'cn' => $check_nil,
                'stj' => $status_jurnal
            ]);
        } else {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menyimpan data"
            ]);
        }
    }

    public function kirimpertransaksi()
    {
        $idtransbeli    = $this->input->get('IDTransBeli');
        $totaltransaksi = (int)str_replace(['.', ','], ['', '.'], $this->input->get('nilaibayar'));
        $dtpembelian = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transpembelian',
            'where' => [['IDTransBeli' => $idtransbeli]],
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
                'NoRef_Sistem'      => $idtransbeli,
                'UserName'          => $this->session->userdata('UserName'),
                'KodePerson'        => $dtpembelian['KodePerson'],
                'NominalBelumPajak' => $dtpembelian['NominalBelumPajak'],
                'PPN'               => $dtpembelian['PPN'],
                'PPh'               => $dtpembelian['PPh'],
                'TotalTransaksi'    => $totaltransaksi,
                'IsDijurnalkan'     => 1,
                'JenisTransaksiKas' => 'BAYAR HUTANG',
                'Diskon'            => $dtpembelian['DiskonBawah'],
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
                'NarasiJurnal' => "Transaksi Hutang",
                'NominalTransaksi' => $kas['TotalTransaksi'],
                'NoRefTrans' => $kas['NoTransKas'],
                'UserName' => $this->session->userdata('UserName')
            ];
            $insertjurnal = $this->crud->insert($jurnal, 'transjurnal');

            // update tr pembelian
            $jumlah_bayar = $this->lokasi->get_total_dibayar($dtpembelian['KodePerson'], $idtransbeli);
            $jmlbayar = isset($jumlah_bayar['total_bayar']) ? $jumlah_bayar['total_bayar'] : 0;
            if ($jmlbayar < $dtpembelian['TotalTagihan']) {
                $updatepembelian = $this->crud->update(['StatusBayar' => 'SEBAGIAN'], ['IDTransBeli' => $idtransbeli], 'transpembelian');
            } else {
                $updatepembelian = $this->crud->update(['StatusBayar' => 'LUNAS'], ['IDTransBeli' => $idtransbeli], 'transpembelian');
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

    public function check_barang_datang()
    {
        $idtransbeli = $this->input->get('IDTransBeli');
        $data = $this->crud->get_rows([
            'select' => '*',
            'from' => 'transaksibarang',
            'where' => [[
                'NoRefTrSistem' => $idtransbeli,
                'JenisTransaksi' => 'BARANG DATANG',
                'IsHapus' => 0
            ]]
        ]);

        if (count($data) < 1) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Tidak bisa retur, barang belum datang.']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Barang sudah datang.']);
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
        $idtransbeli = $this->input->get('IDTransBeli');
        $jenisrealisasi = $this->input->get('JenisRealisasi');

        $dtpembelian = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transpembelian',
            'where' => [['IDTransBeli' => $idtransbeli]],
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
        $insertdata['JenisRetur']       = 'RETUR_BELI';
        $insertdata['IDTrans']          = $idtransbeli;
        $insertdata['KodePerson']       = $dtpembelian['KodePerson'];
        $insertdata['TanggalTransaksi'] = date('Y-m-d H-i');
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
                'JenisTransaksi' => 'Retur Pembelian',
                'Description' => 'tambah data retur pembelian ' . $insertdata['IDTransRetur']
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
