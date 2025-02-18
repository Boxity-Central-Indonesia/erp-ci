<?php
defined('BASEPATH') or exit('No direct script access allowed');

class retur_pembelian extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transaksiretur r';
        $this->load->model('M_Lokasi', 'lokasi');
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[68]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'retur';
            $data['title'] = 'Retur Pembelian';
            $data['view'] = 'transaksi/v_retur';
            $data['scripts'] = 'transaksi/s_retur';

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
            $configData['table'] = 'transaksiretur r';

            $configData['where'] = [
                [
                    'r.JenisRetur' => 'RETUR_BELI',
                    'LEFT(r.IDTrans, 3) =' => 'TBL',
                    'r.IsRealisasi' => 1,
                    'r.IsVoid' => 0,
                ]
            ];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (r.IDTransRetur LIKE '%$cari%' OR r.IDTrans LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(r.TanggalTransaksi) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' transpembelian b',
                    'on' => "b.IDTransBeli = r.IDTrans",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = r.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = r.KodeGudang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = r.UserName",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'r.IDTransRetur', 'r.JenisRetur', 'r.TanggalTransaksi', 'r.TotalRetur', 'r.Keterangan', 'r.IsRealisasi', 'r.JenisRealisasi', 'r.KetRealisasi', 'r.IsDijurnalkan', 'r.NoTransJurnal', 'r.IsVoid', 'r.IDTrans', 'b.IDTransBeli', 'r.KodePerson', 'p.NamaPersonCP', 'r.KodeGudang', 'g.NamaGudang', 'r.UserName', 'u.ActualName'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'r.TanggalTransaksi';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'r.IDTransRetur', 'r.JenisRetur', 'r.TanggalTransaksi', 'r.TotalRetur', 'r.Keterangan', 'r.IsRealisasi', 'r.JenisRealisasi', 'r.KetRealisasi', 'r.IsDijurnalkan', 'r.NoTransJurnal', 'r.IsVoid', 'r.IDTrans', 'b.IDTransBeli', 'r.KodePerson', 'p.NamaPersonCP', 'r.KodeGudang', 'g.NamaGudang', 'r.UserName', 'u.ActualName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 68; //FiturID di tabel serverfitur
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
                $temp['TotalRetur'] = isset($temp['TotalRetur']) ? $temp['TotalRetur'] : 0;
                $temp['TanggalTransaksi'] = isset($temp['TanggalTransaksi']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($temp['TanggalTransaksi'])) : '';
                if ($canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/retur_pembelian/detail/' . base64_encode($temp['IDTransRetur'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransRetur'] . ' class="btnhapusretur" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/retur_pembelian/detail/' . base64_encode($temp['IDTransRetur'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
                }
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
        if (!($this->input->post('IDTransRetur') != null && $this->input->post('IDTransRetur') != '')) {
            $prefix = "RTJ-" . date("Ym");
            $insertdata['IDTransRetur'] = $this->crud->get_kode([
                'select' => 'RIGHT(IDTransRetur, 7) AS KODE',
                'where' => [['LEFT(IDTransRetur, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'IDTransRetur DESC',
                'prefix' => $prefix
            ]);
            $insertdata['JenisRetur']       = 'RETUR_BELI';
            $insertdata['TotalRetur']       = 0;
            $insertdata['IsRealisasi']      = 0;
            $insertdata['IsDijurnalkan']    = 0;
            $insertdata['UserName']         = $this->session->userdata('UserName');
            $insertdata['IsVoid']           = 0;
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'transaksiretur');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('IDTransRetur') : $insertdata['IDTransRetur'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Retur Pembelian',
                'Description' => $ket . ' data retur pembelian ' . $id
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data"),
                'id' => $insertdata['IDTransRetur']
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ($isEdit ? "Gagal Edit Data" : "Gagal Menambah Data")
            ]);
        }
    }

    public function batal()
    {
        $kode  = $this->input->get('IDTransRetur');
        
        $item = $this->crud->delete(['IDTransRetur' => $kode], 'itemretur');
        $res = $this->crud->delete(['IDTransRetur' => $kode], 'transaksiretur');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Retur Pembelian',
                'Description' => 'hapus data retur pembelian ' . $kode
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
        checkAccess($this->session->userdata('fiturview')[68]);
        $idretur   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_beli';
            $data['title'] = 'Detail Transaksi Retur Pembelian';
            $data['view'] = 'transaksi/v_retur_pembelian_detail';
            $data['scripts'] = 'transaksi/s_retur_pembelian_detail';

            $dtinduk = [
                'select' => 'r.IDTransRetur, r.JenisRetur, r.TanggalTransaksi, r.TotalRetur, r.Keterangan, r.IsRealisasi, r.JenisRealisasi, r.KetRealisasi, r.IsDijurnalkan, r.NoTransJurnal, r.IDTrans, b.IDTransBeli, b.TanggalPembelian, r.KodePerson, p.NamaPersonCP, r.KodeGudang, g.NamaGudang, r.Username, u.ActualName',
                'from' => 'transaksiretur r',
                'join' => [
                    [
                        'table' => ' transpembelian b',
                        'on' => "b.IDTransBeli = r.IDTrans",
                        'param' => 'INNER',
                    ],
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = r.KodePerson",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' mstgudang g',
                        'on' => "g.KodeGudang = r.KodeGudang",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' userlogin u',
                        'on' => "u.UserName = r.UserName",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['r.IDTransRetur' => $idretur]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);

            $data['itembeli'] = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'itempembelian i',
                    'join' => [[
                        'table' => ' mstbarang br',
                        'on' => "br.KodeBarang = i.KodeBarang",
                        'param' => 'INNER',
                    ]],
                    'where' => [['IDTransBeli' => $data['dtinduk']['IDTransBeli']]],
                ]
            );
            $data['IDTransRetur'] = $idretur;

            $data['gudang'] = $this->crud->get_rows([
                'select' => '*',
                'from' => 'mstgudang',
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

            // memo jurnal
            $cekKas = $this->crud->get_one_row([
                'select' => 'NoTransKas',
                'from' => 'transaksikas',
                'where' => [['NoRef_Sistem' => $idretur]],
            ]);
            if ($cekKas) {
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
                    'where' => [['j.NoRefTrans' => $cekKas['NoTransKas']]],
                ]);
            } else {
                $data['memojurnal'] = null;
            }

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $idretur   = $this->input->get('idretur');
            $configData['table'] = 'itemretur i';
            $configData['where'] = [[
                'i.IDTransRetur'  => $idretur,
                'i.IsVoid' => 0,
            ]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.NoUrut LIKE '%$cari%' OR i.AlasanRetur LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' transaksiretur r',
                    'on' => "r.IDTransRetur = i.IDTransRetur",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.NoUrut', 'i.IDTransRetur', 'i.JenisRetur', 'i.KodeBarang', 'i.SatuanBarang', 'i.AdditionalName', 'br.NamaBarang', 'i.JmlJual', 'i.HargaJual', 'i.JmlRetur', 'i.TotalRetur', 'i.AlasanRetur', 'i.IsVoid', 'r.IDTrans', 'r.IsRealisasi'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.NoUrut', 'i.IDTransRetur', 'i.JenisRetur', 'i.KodeBarang', 'i.SatuanBarang', 'i.AdditionalName', 'br.NamaBarang', 'i.JmlJual', 'i.HargaJual', 'i.JmlRetur', 'i.TotalRetur', 'i.AlasanRetur', 'i.IsVoid', 'r.IDTrans', 'r.IsRealisasi',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 68; //FiturID di tabel serverfitur
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
                $temp['JmlJual'] = $temp['JmlJual'] . ' ' . $temp['SatuanBarang'];
                $temp['JmlRetur'] = $temp['JmlRetur'] . ' ' . $temp['SatuanBarang'];
                $temp['JmlDatang'] = $this->get_barang_datang($temp['IDTrans'], $temp['KodeBarang']);
                $temp['BarangDatang'] = $temp['JmlDatang'] . ' ' . $temp['SatuanBarang'];
                if ($canEdit == 1 && $canDelete == 1 && $temp['IsRealisasi'] != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransRetur'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1 && $temp['IsRealisasi'] != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1 && $temp['IsRealisasi'] != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['IDTransRetur'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function getitembeli()
    {
        $kodebarang = $this->input->get('KodeBarang');
        $idtransbeli = $this->input->get('IDTransBeli');

        $data = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'itempembelian',
            'where' => [[
                'IDTransBeli' => $idtransbeli,
                'KodeBarang' => $kodebarang,
            ]],
        ]);
        $data['BarangDatang'] = $this->get_barang_datang($idtransbeli, $kodebarang);

        echo json_encode($data);
    }

    public function get_barang_datang($idtransbeli, $kodebarang)
    {
        $sql = "SELECT COALESCE(SUM(i.Qty), 0) AS Total
            FROM itemtransaksibarang i
            JOIN transaksibarang tb ON i.NoTrans = tb.NoTrans
            JOIN mstbarang b ON i.KodeBarang = b.KodeBarang
            WHERE tb.NoRefTrSistem = '$idtransbeli'
            AND i.KodeBarang = '$kodebarang'
            AND tb.JenisTransaksi = 'BARANG DATANG'
            AND tb.IsHapus = 0";
        return $this->db->query($sql)->row_array()['Total'];
    }

    public function cetakdetail()
    {
        $idretur   = escape(base64_decode($this->uri->segment(4)));

        $dtinduk = [
            'select' => 'r.IDTransRetur, r.JenisRetur, r.TanggalTransaksi, r.TotalRetur, r.Keterangan, r.IsRealisasi, r.JenisRealisasi, r.KetRealisasi, r.IsDijurnalkan, r.NoTransJurnal, r.IDTrans, b.IDTransBeli, b.TanggalPembelian, r.KodePerson, p.NamaPersonCP, r.KodeGudang, g.NamaGudang',
            'from' => 'transaksiretur r',
            'join' => [
                [
                    'table' => ' transpembelian b',
                    'on' => "b.IDTransBeli = r.IDTrans",
                    'param' => 'INNER',
                ],
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = r.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = r.KodeGudang",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['r.IDTransRetur' => $idretur]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);

        $sql = [
            'select' => 'i.NoUrut, i.IDTransRetur, i.JenisRetur, i.KodeBarang, i.SatuanBarang, i.AdditionalName, br.NamaBarang, i.JmlJual, i.HargaJual, i.JmlRetur, i.TotalRetur, i.AlasanRetur, i.IsVoid, r.IsRealisasi',
            'from' => 'itemretur i',
            'join' => [
                [
                    'table' => ' transaksiretur r',
                    'on' => "r.IDTransRetur = i.IDTransRetur",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['i.IDTransRetur' => $idretur]],
        ];
        $data['model'] = $this->crud->get_rows($sql);

        $this->load->library('Pdf');
        $this->load->view('transaksi/v_retur_detail_cetak', $data);
    }

    public function checkitembeli()
    {
        $jmldatang  = ($this->input->get('JmlDatang') != null) ? $this->input->get('JmlDatang') : 0;
        $jmlretur   = ($this->input->get('JmlRetur') != null) ? $this->input->get('JmlRetur') : 0;

        if ($jmlretur > $jmldatang) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Jumlah retur barang tidak boleh melebihi jumlah barang datang']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Jumlah retur ok']);
        }
    }

    public function simpandetail()
    {
        $insertdata = $this->input->post();
        $isEdit = true;
        unset($insertdata['JmlDatang']);
        unset($insertdata['HargaJual']);
        $insertdata['HargaJual'] = $this->input->post('HargaJual');

        ## POST DATA
        if (!($this->input->post('NoUrut') != null && $this->input->post('NoUrut') != '')) {
            $isEdit = false;
            $getNoUrut = $this->db->from('itemretur')
            ->where('IDTransRetur', $this->input->post('IDTransRetur'))
            ->select('NoUrut')
            ->order_by('NoUrut', 'desc')
            ->get()->row();
            if ($getNoUrut) {
                $NoUrut = (int)$getNoUrut->NoUrut;
            } else {
                $NoUrut = 0;
            }
            $insertdata['NoUrut']     = $NoUrut + 1;
            $insertdata['TotalRetur'] = $insertdata['HargaJual'] * $insertdata['JmlRetur'];
            $insertdata['IsVoid']     = 0;
            $isEdit = false;
        } else {
            $insertdata['TotalRetur']     = $insertdata['HargaJual'] * $insertdata['JmlRetur'];
            $isEdit = true;
        }

        $res = $this->crud->insert_or_update($insertdata, 'itemretur');

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
        $kode  = $this->input->get('IDTransRetur');
        $kode2 = $this->input->get('NoUrut');

        $res = $this->crud->delete(['IDTransRetur' => $kode, 'NoUrut' => $kode2], 'itemretur');
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

    public function simpanretur()
    {
        $this->db->trans_begin();
        $updatedata = $this->input->post();
        unset($updatedata['IDTransRetur']);
        unset($updatedata['TotalRetur']);
        unset($updatedata['KodeAkun']);
        // $totalretur = str_replace(['.', ','], ['', '.'], $this->input->post('TotalRetur'));

        $tahun = $this->akses->get_tahun_aktif();

        $dtpembelian = $this->crud->get_one_row([
            'select' => 'b.IDTransBeli, b.KodePerson, b.PPN, b.DiskonBawah, b.NominalBelumPajak, b.TotalTagihan, b.TanggalPembelian, b.StatusBayar, SUM(COALESCE(k.TotalTransaksi, 0)) AS TotalTransaksi',
            'from' => 'transpembelian b',
            'join' => [[
                'table' => 'transaksikas k',
                'on' => "b.IDTransBeli = k.NoRef_Sistem",
                'param' => 'LEFT'
            ]],
            'where' => [['IDTransBeli' => $this->input->post('IDTrans')]]
        ]);

        $totalpembelian = $this->crud->get_one_row([
            'select' => 'SUM(Qty) AS Total',
            'from' => 'itempembelian',
            'where' => [['IDTransBeli' => $dtpembelian['IDTransBeli']]]
        ]);

        $dbperitem = $dtpembelian['DiskonBawah'] / $totalpembelian['Total'];

        $akuntunai      = $this->lokasi->get_akun_penjurnalan('Retur Pembelian', 'Tunai');
        $akunkredit     = $this->lokasi->get_akun_penjurnalan('Retur Pembelian', 'Kredit');
        $akuntunaippn   = $this->lokasi->get_akun_penjurnalan('Retur Pembelian PPN', 'Tunai');
        $akunkreditppn  = $this->lokasi->get_akun_penjurnalan('Retur Pembelian PPN', 'Kredit');

        $itemretur = $this->crud->get_rows([
            'select' => '*',
            'from' => 'itemretur',
            'where' => [[
                'IDTransRetur' => $this->input->post('IDTransRetur'),
                'IsVoid' => 0,
            ]],
        ]);

        $itemrt = [];
        foreach ($itemretur as $key => $value) {
            $itemrt[$key] = $value;
            $itemrt[$key]['HargaJualTotal'] = $value['HargaJual'] * $value['JmlRetur'];
            $itemrt[$key]['DiskonRetur']    = $dbperitem * $value['JmlRetur'];
            if ($dtpembelian['PPN'] > 0) {
                $itemrt[$key]['PPNRetur']   = ($itemrt[$key]['HargaJualTotal'] - $itemrt[$key]['DiskonRetur']) * 11 / 100;
            } else {
                $itemrt[$key]['PPNRetur']   = 0;
            }
            $itemrt[$key]['TotalReturNew']  = $itemrt[$key]['HargaJualTotal'] - $itemrt[$key]['DiskonRetur'] + $itemrt[$key]['PPNRetur'];
        }
        
        $totalretur = 0;
        $totalppn = 0;
        foreach ($itemrt as $value) {
            $totalretur += $value['TotalReturNew'];
            $totalppn += $value['PPNRetur'];
        }

        $dp = 0;
        $trkas = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transaksikas',
            'where' => [[
                'NoRef_Sistem' => $dtpembelian['IDTransBeli'],
                'JenisTransaksiKas' => 'DP PEMBELIAN'
            ]]
        ]);
        if ($trkas) {
            $dp = (double)$trkas['TotalTransaksi'];
        }

        $status_jurnal = $this->lokasi->setting_jurnal_status();
        $insertjurnal['IDTransJurnal'] = null;
        $kodeakunkas = $this->input->post('KodeAkun');
        $namaakunkas = ($kodeakunkas != '') ? $this->lokasi->getnamaakun($kodeakunkas) : '';

        if ($this->input->post('JenisRealisasi') == 'KEMBALI UANG') {
            // insert tabel trkas
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
            $insertkas['TanggalTransaksi']  = date('Y-m-d H:i');
            $insertkas['NoRef_Sistem']      = $this->input->post('IDTransRetur');
            $insertkas['Uraian']            = "Retur Pembelian";
            $insertkas['UserName']          = $this->session->userdata('UserName');
            $insertkas['KodePerson']        = $this->input->post('KodePerson');
            $insertkas['NominalBelumPajak'] = 0;
            $insertkas['PPN']               = 0;
            $insertkas['TotalTransaksi']    = $totalretur;
            $insertkas['JenisTransaksiKas'] = "KAS MASUK";
            $insertkas['IsDijurnalkan']     = 1;
            $insertkas['Diskon']            = 0;
            $savekas = $this->crud->insert($insertkas, 'transaksikas');

            // insert tabel jurnal dan item jurnal
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
            $insertjurnal['TglTransJurnal']     = $insertkas['TanggalTransaksi'];
            $insertjurnal['TipeJurnal']         = "UMUM";
            $insertjurnal['NarasiJurnal']       = "Retur Pembelian";
            $insertjurnal['NominalTransaksi']   = $totalretur;
            $insertjurnal['NoRefTrans']         = $insertkas['NoTransKas'];
            $insertjurnal['UserName']           = $this->session->userdata('UserName');
            $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');

            // jika status jurnal otomatis
            if ($status_jurnal == 'on') {
                if ($totalretur < $dp) { // retur pembelian tunai
                    if (count($akuntunai) > 0 && count($akuntunaippn) > 0) {
                        if ($dtpembelian['PPN'] > 0) {
                            foreach ($akuntunaippn as $key) {
                                if ($key['StatusAkun'] == 'Kas') {
                                    $debet = $totalretur;
                                    $kredit = 0;
                                } elseif ($key['StatusAkun'] == 'Retur') {
                                    $debet = 0;
                                    $kredit = $totalretur - $totalppn;
                                } elseif ($key['StatusAkun'] == 'PPn') {
                                    $debet = 0;
                                    $kredit = $totalppn;
                                }
                                $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                $itemjurnal = [
                                    'NoUrut'        => $key['NoUrut'],
                                    'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                    'KodeTahun'     => $insertjurnal['KodeTahun'],
                                    'KodeAkun'      => $kodeakun,
                                    'NamaAkun'      => $namaakun,
                                    'Debet'         => $debet,
                                    'Kredit'        => $kredit,
                                    'Uraian'        => "Penjurnalan otomatis untuk Retur Pembelian Tunai"
                                ];
                                $insertjurnalitem[] = $this->crud->insert($itemjurnal, 'transjurnalitem');
                            }
                        } else {
                            foreach ($akuntunai as $key) {
                                $itemjurnal = [
                                    'NoUrut'        => $key['NoUrut'],
                                    'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                    'KodeTahun'     => $insertjurnal['KodeTahun'],
                                    'KodeAkun'      => ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'],
                                    'NamaAkun'      => ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'],
                                    'Debet'         => ($key['JenisJurnal'] == 'Debet') ? $totalretur : 0,
                                    'Kredit'        => ($key['JenisJurnal'] == 'Kredit') ? $totalretur : 0,
                                    'Uraian'        => "Penjurnalan otomatis untuk Retur Pembelian Tunai"
                                ];
                                $insertjurnalitem[] = $this->crud->insert($itemjurnal, 'transjurnalitem');
                            }
                        }
                    }
                } else { // retur pembelian kredit
                    if (count($akunkredit) > 0 && count($akunkreditppn) > 0) {
                        if ($dtpembelian['PPN'] > 0) {
                            foreach ($akunkreditppn as $key) {
                                if ($key['StatusAkun'] == 'Kas') {
                                    $debet = $dp;
                                    $kredit = 0;
                                } elseif ($key['StatusAkun'] == 'Hutang/Piutang') {
                                    $debet = $totalretur - $dp;
                                    $kredit = 0;
                                } elseif ($key['StatusAkun'] == 'Retur') {
                                    $debet = 0;
                                    $kredit = $totalretur - $totalppn;
                                } elseif ($key['StatusAkun'] == 'PPn') {
                                    $debet = 0;
                                    $kredit = $totalppn;
                                }
                                $kodeakun = ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'];
                                $namaakun = ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'];
                                $itemjurnal = [
                                    'NoUrut'        => $key['NoUrut'],
                                    'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                    'KodeTahun'     => $insertjurnal['KodeTahun'],
                                    'KodeAkun'      => $kodeakun,
                                    'NamaAkun'      => $namaakun,
                                    'Debet'         => $debet,
                                    'Kredit'        => $kredit,
                                    'Uraian'        => "Penjurnalan otomatis untuk Retur Pembelian Kredit"
                                ];
                                $insertjurnalitem[] = $this->crud->insert($itemjurnal, 'transjurnalitem');
                            }
                        } else {
                            foreach ($akunkredit as $key) {
                                $itemjurnal = [
                                    'NoUrut'        => $key['NoUrut'],
                                    'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                                    'KodeTahun'     => $insertjurnal['KodeTahun'],
                                    'KodeAkun'      => ($key['IsBank'] == 1 && $kodeakunkas != '') ? $kodeakunkas : $key['KodeAkun'],
                                    'NamaAkun'      => ($key['IsBank'] == 1 && $namaakunkas != '') ? $namaakunkas : $key['NamaAkun'],
                                    'Debet'         => ($key['JenisJurnal'] == 'Debet') ? $totalretur : 0,
                                    'Kredit'        => ($key['JenisJurnal'] == 'Kredit') ? $totalretur : 0,
                                    'Uraian'        => "Penjurnalan otomatis untuk Retur Pembelian Kredit"
                                ];
                                $insertjurnalitem[] = $this->crud->insert($itemjurnal, 'transjurnalitem');
                            }
                        }
                    }
                }
            } else {
                // jika status jurnal manual
            }

            // insert tabel trbarang dan item trbarang
            if ($itemrt) {
                $prefixbrg = "TBR-" . date("Ym");
                $insertbarang['NoTrans'] = $this->crud->get_kode([
                    'select' => 'RIGHT(NoTrans, 7) AS KODE',
                    'from' => 'transaksibarang',
                    'where' => [['LEFT(NoTrans, 10) =' => $prefixbrg]],
                    'limit' => 1,
                    'order_by' => 'NoTrans DESC',
                    'prefix' => $prefixbrg
                ]);
                $insertbarang['TanggalTransaksi'] = date('Y-m-d H:i');
                $insertbarang['Username']         = $this->session->userdata('UserName');
                $insertbarang['KodePerson']       = $this->input->post('KodePerson');
                $insertbarang['Deskripsi']        = 'Retur Pembelian';
                $insertbarang['JenisTransaksi']   = 'BARANG DATANG';
                $insertbarang['NoRefTrSistem']    = $this->input->post('IDTransRetur');
                $insertbarang['GudangTujuan']     = $this->input->post('KodeGudang');
                $insertbarang['IsHapus']          = 0;
                $savebarang = $this->crud->insert($insertbarang, 'transaksibarang');

                foreach ($itemrt as $key) {
                    $itembarang = [
                        'NoUrut'        => $key['NoUrut'],
                        'NoTrans'       => $insertbarang['NoTrans'],
                        'KodeBarang'    => $key['KodeBarang'],
                        'Qty'           => $key['JmlRetur'],
                        'HargaSatuan'   => $key['HargaJual'],
                        'Total'         => $key['TotalReturNew'],
                        'JenisStok'     => 'MASUK',
                        'GudangTujuan'  => $this->input->post('KodeGudang'),
                        'SatuanBarang'  => $key['SatuanBarang'],
                        'IsHapus'       => 0
                    ];
                    $saveitembarang[] = $this->crud->insert($itembarang, 'itemtransaksibarang');
                }
            }
        }

        $updatedata['TotalRetur']       = $totalretur;
        $updatedata['IsRealisasi']      = 1;
        $updatedata['IsDijurnalkan']    = 1;
        $updatedata['UserName']         = $this->session->userdata('UserName');

        $res = $this->crud->update($updatedata, ['IDTransRetur' => $this->input->post('IDTransRetur')], 'transaksiretur');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'edit',
                'JenisTransaksi' => 'Retur Pembelian',
                'Description' => 'update data retur pembelian ' . $this->input->post('IDTransRetur')
            ]);
            $this->db->trans_commit();
            echo json_encode([
                'status' => true,
                'msg'  => ("Berhasil Menyimpan Data"),
                'jenisrealisasi' => $this->input->post('JenisRealisasi'),
                'idjurnal' => $insertjurnal['IDTransJurnal'],
                'stj' => $status_jurnal
            ]);
        } else {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => false,
                'msg'  => ("Gagal Menyimpan Data")
            ]);
        }
    }

    public function hapus()
    {
        $kode = $this->input->get('IDTransRetur');

        // hapus data di tabel trkas, trjurnal & item trjurnal
        $cekKas = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transaksikas',
            'where' => [['NoRef_Sistem' => $kode]]
        ]);
        if ($cekKas) {
            $cekjurnal = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'transjurnal',
                'where' => [['NoRefTrans' => $cekKas['NoTransKas']]],
            ]);
            if ($cekjurnal) {
                $deleteitemjurnal   = $this->crud->delete(['IDTransJurnal' => $cekjurnal['IDTransJurnal']], 'transjurnalitem');
                $deletejurnalinduk  = $this->crud->delete(['IDTransJurnal' => $cekjurnal['IDTransJurnal']], 'transjurnal');
            }
            $deletetranskas = $this->crud->delete(['NoTransKas' => $cekKas['NoTransKas']], 'transaksikas');
        }

        // hapus data di tabel trbarang dan item trbarang
        $cekBarang = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transaksibarang',
            'where' => [['NoRefTrSistem' => $kode]],
        ]);
        if ($cekBarang) {
            $deleteitembarang   = $this->crud->delete(['NoTrans' => $cekBarang['NoTrans']], 'itemtransaksibarang');
            $deletetrbarang     = $this->crud->delete(['NoTrans' => $cekBarang['NoTrans']], 'transaksibarang');
        }

        // hapus di tabel retur dan item retur
        $item   = $this->crud->delete(['IDTransRetur' => $kode], 'itemretur');
        $res    = $this->crud->delete(['IDTransRetur' => $kode], 'transaksiretur');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Retur Pembelian',
                'Description' => 'hapus data retur pembelian ' . $kode
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
