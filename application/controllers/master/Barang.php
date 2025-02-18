<?php
defined('BASEPATH') or exit('No direct script access allowed');

class barang extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'mstbarang b';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[8]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[8]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'barang';
            $data['title'] = 'Master Barang';
            $data['view'] = 'master/v_barang';
            $data['scripts'] = 'master/s_barang';

            $dtjenis = [
                'select' => '*',
                'from' => 'mstjenisbarang',
                'where' => [['IsAktif !=' => null]],
                'order_by' => 'KodeJenis'
            ];
            $data['dtjenis'] = $this->crud->get_rows($dtjenis);

            $data['dtkategori'] = $this->crud->get_rows([
                'select' => '*',
                'from' => 'mstkategori',
                'where' => [['IsAktif !=' => null]],
                'order_by' => 'KodeKategori'
            ]);

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
                $configData['filters'][] = " (b.NamaBarang LIKE '%$cari%' OR b.KodeBarang LIKE '%$cari%' OR b.KodeManual LIKE '%$cari%')";
            }

            $jenis   = $this->input->get('jenis');
            if ($jenis != '') {
                $configData['where'] = [['b.KodeJenis' => $jenis]];
            }

            $kategori   = $this->input->get('kategori');
            if ($kategori != '') {
                $configData['where'] = [['b.KodeKategori' => $kategori]];
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
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = b.KodeGudang",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.KodeBarang', 'b.NamaBarang', 'b.DeskripsiBarang', 'b.HargaBeliTerakhir', 'b.HargaJual', 'b.NilaiHPP', 'b.IsAktif', 'b.Foto1', 'b.Foto2', 'b.TglInput', 'b.SatuanBarang', 'b.Spesifikasi', 'b.KodeJenis', 'j.NamaJenisBarang', 'b.KodeKategori', 'k.NamaKategori', 'BeratBarang', 'ProductionCode', 'b.KodeManual', 'b.HPPOpeningBalance', 'b.StokOpeningBalance', 'b.KodeGudang', 'g.NamaGudang'
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
                'b.KodeBarang', 'b.NamaBarang', 'b.DeskripsiBarang', 'b.HargaBeliTerakhir', 'b.HargaJual', 'b.NilaiHPP', 'b.IsAktif', 'b.Foto1', 'b.Foto2', 'b.TglInput', 'b.SatuanBarang', 'b.Spesifikasi', 'b.KodeJenis', 'j.NamaJenisBarang', 'b.KodeKategori', 'k.NamaKategori', 'BeratBarang', 'ProductionCode', 'b.KodeManual', 'b.HPPOpeningBalance', 'b.StokOpeningBalance', 'b.KodeGudang', 'g.NamaGudang',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 8; //FiturID di tabel serverfitur
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
                $temp['stokasli'] = $this->lokasi->get_stok_asli($temp['KodeBarang'])['stok'];
                $temp['stok'] = ($this->lokasi->get_stok_asli($temp['KodeBarang'])['stok'] > 0) ? $this->lokasi->get_stok_asli($temp['KodeBarang'])['stok'] : 1;
                $temp['hppbalance'] = $temp['hpp'] * $temp['stok'];
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" type="button" href="javascript:void(0);" data-kode="' . $temp['KodeBarang'] . '" title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodeBarang'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodeBarang'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodeBarang'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>') . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeBarang'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" type="button" href="javascript:void(0);" data-kode="' . $temp['KodeBarang'] . '" title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodeBarang'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodeBarang'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodeBarang'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>');
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodeBarang'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function tambah()
    {
        $data['menu']   = 'barang';
        $data['title']  = 'Tambah Data Barang';

        $jbrg = [
                'select' => 'KodeJenis, NamaJenisBarang',
                'from' => 'mstjenisbarang',
                'where' => [['IsAktif !=' => 0]],
                'order_by' => 'KodeJenis',
            ];
        $data['jbrg'] = $this->crud->get_rows($jbrg);

        $data['ktg'] = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'mstkategori',
                'where' => [['IsAktif !=' => 0]],
                'order_by' => 'KodeKategori',
            ]
        );

        $data['gudang'] = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'mstgudang',
                'order_by' => 'NamaGudang',
            ]
        );

        $data['view'] = 'master/v_barangtambah';
        loadview($data);
    }

    public function create_qr_bar()
    {
        $this->load->library('ciqrcode'); //pemanggilan library QR CODE
        $this->load->library('zend'); //pemanggilan library Barcode
        $this->zend->load('Zend/Barcode');
        $kodebarang = $this->input->get('KodeBarang');

        // QR Code
        $path_qr = realpath(APPPATH . '../assets/barang/qr_'.$kodebarang.'.png');
        if (!@file_exists($path_qr))
        {
            $config['cacheable']    = true; //boolean, the default is true
            $config['cachedir']     = './assets/'; //string, the default is application/cache/
            $config['errorlog']     = './assets/'; //string, the default is application/logs/
            $config['imagedir']     = './assets/barang/'; //direktori penyimpanan qr code
            $config['quality']      = true; //boolean, the default is true
            $config['size']         = '1024'; //interger, the default is 1024
            $config['black']        = array(224,255,255); // array, default is array(255,255,255)
            $config['white']        = array(70,130,180); // array, default is array(0,0,0)
            $this->ciqrcode->initialize($config);

            $image_name = 'qr_' . $kodebarang . '.png'; //buat name dari qr code dengan random string

            $params['data'] = $kodebarang; //data yang akan di jadikan QR CODE
            $params['level'] = 'H'; //H=High
            $params['size'] = 10;
            $params['savename'] = FCPATH.$config['imagedir'].$image_name; //simpan image QR CODE ke folder assets/barang/
            $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE
        }

        // Barcode
        $path_bar = realpath(APPPATH . '../assets/barang/bar_'.$kodebarang.'.png');
        if (!@file_exists($path_bar))
        {
            //generate barcode
            $imageResource = Zend_Barcode::factory('code128', 'image', array('text'=>$kodebarang), array())->draw();
            imagepng($imageResource, 'assets/barang/bar_'.$kodebarang.'.png');

            $barcode = 'assets/barang/bar_'.$kodebarang.'.png';
        }

        if ($kodebarang) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Menambah Data",
                'id' => $kodebarang
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Menambah Data"
            ]);
        }
    }

    public function edit()
    {
        $kodeBarang   = escape(base64_decode($this->uri->segment(4)));
        $data['kodeBarang'] = $kodeBarang;

        ## AMBIL DATA BARANG
        $where = [
            'b.KodeBarang' => $kodeBarang
        ];
        $sql = [
            'select' => 'b.KodeBarang, b.NamaBarang, b.DeskripsiBarang, b.HargaBeliTerakhir, b.HargaJual, b.NilaiHPP, b.IsAktif, b.Foto1, b.Foto2, b.TglInput, b.SatuanBarang, b.Spesifikasi, b.KodeJenis, j.NamaJenisBarang, b.KodeKategori, k.NamaKategori, b.BeratBarang, b.ProductionCode, b.KodeManual, b.KodeGudang, g.NamaGudang, b.HPPOpeningBalance, b.StokOpeningBalance, t.NoTrans, i.Qty, i.HargaSatuan',
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
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = b.KodeGudang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => 'transaksibarang t',
                    'on' => "b.KodeBarang = t.NoRefTrSistem AND b.KodeGudang = t.GudangTujuan AND t.JenisTransaksi = 'OPENING BALANCE'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => 'itemtransaksibarang i',
                    'on' => 't.NoTrans = i.NoTrans',
                    'param' => 'LEFT'
                ]
            ],
            'where' => [$where]
        ];
        $data['model'] = $this->crud->get_one_row($sql);

        $data['stok'] = ($this->lokasi->get_stok_asli($kodeBarang)['stok'] > 0) ? $this->lokasi->get_stok_asli($kodeBarang)['stok'] : 1;
        $data['hppbalance'] = $data['model']['NilaiHPP'] * $data['stok'];

        $jbrg = [
            'select' => 'KodeJenis, NamaJenisBarang',
            'from' => 'mstjenisbarang',
            'order_by' => 'KodeJenis'
        ];
        $data['jbrg'] = $this->crud->get_rows($jbrg);

        $data['ktg'] = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'mstkategori',
                'order_by' => 'KodeKategori',
            ]
        );

        $data['gudang'] = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'mstgudang',
                'order_by' => 'NamaGudang',
            ]
        );

        $countitembarang = $this->crud->get_count([
            'select' => 'i.NoTrans, i.NoUrut',
            'from' => 'itemtransaksibarang i',
            'join' => [[
                'table' => ' transaksibarang tb',
                'on' => "tb.NoTrans = i.NoTrans",
                'param' => 'INNER',
            ]],
            'where' => [[
                'i.KodeBarang' => $kodeBarang,
                'tb.JenisTransaksi !=' => 'OPENING BALANCE',
            ]],
        ]);
        $data['canedit_ob'] = ($countitembarang > 0) ? 0 : 1;

        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu']   = 'barang';
        $data['title']  = 'Edit Data Barang';
        $data['view']   = 'master/v_barangedit';
        $data['scripts'] = 'master/s_barangedit';
        loadview($data);
    }

    public function simpan()
    {
        $this->load->library('upload');
        $isEdit = true;
        $simpanob = $this->input->post('SimpanOB') != 'on'  ? (int) 0 : (int) 1;

        ## POST DATA
        if (!($this->input->post('KodeBarang') != null && $this->input->post('KodeBarang') != '')) {
            $insertdata = $this->input->post();
            unset($insertdata['SimpanOB']);
            ## UPLOAD FOTO BARANG
            if(!empty($_FILES['Foto1']['name'])){
                $Foto1 = $this->uploadFoto('Foto1');
                $insertdata['Foto1'] = $Foto1;
            } else {
                unset($insertdata['Foto1']);
            }
            if(!empty($_FILES['Foto2']['name'])){
                $Foto2 = $this->uploadFoto('Foto2');
                $insertdata['Foto2'] = $Foto2;
            } else {
                unset($insertdata['Foto2']);
            }

            $insertdata['KodeBarang'] = $this->crud->get_kode_barang([
                'select' => 'RIGHT(KodeBarang, 6) AS KODE',
                'limit' => 1,
                'order_by' => 'KodeBarang DESC',
                'prefix' => 'BRG'
            ]);
            $insertdata['HargaBeliTerakhir'] = str_replace(['.', ','], ['', '.'], $this->input->post('HargaBeliTerakhir'));
            $insertdata['HargaJual']         = str_replace(['.', ','], ['', '.'], $this->input->post('HargaJual'));
            $insertdata['BeratBarang']       = str_replace(['.', ','], ['', '.'], $this->input->post('BeratBarang'));
            $insertdata['NilaiHPP']          = ($this->input->post('HPPOpeningBalance') != null) ? str_replace(['.', ','], ['', '.'], $this->input->post('HPPOpeningBalance')) : 0;
            $insertdata['StokOpeningBalance'] = 0;
            $insertdata['HPPOpeningBalance'] = 0;
            $insertdata['IsAktif']           = 1;
            $insertdata['TglInput']          = date('Y-m-d H:i:s');

            $isEdit = false;
            $res = $this->crud->insert($insertdata, 'mstbarang');

            $prefix = "TBR-" . date("Ym");
            if ($simpanob != 0) {
                $stokob = ($this->input->post('StokOpeningBalance') != null) ? str_replace(['.', ','], ['', '.'], $this->input->post('StokOpeningBalance')) : 0;
                $trbarang = [
                    'NoTrans' => $this->crud->get_kode([
                        'select' => 'RIGHT(NoTrans, 7) AS KODE',
                        'from' => 'transaksibarang',
                        'where' => [['LEFT(NoTrans, 10) =' => $prefix]],
                        'limit' => 1,
                        'order_by' => 'NoTrans DESC',
                        'prefix' => $prefix
                    ]),
                    'TanggalTransaksi'  => date('Y-m-d H:i'),
                    'UserName'          => $this->session->userdata('UserName'),
                    'JenisTransaksi'    => "OPENING BALANCE",
                    'NoRefTrSistem'     => $insertdata['KodeBarang'],
                    'GudangTujuan'      => $insertdata['KodeGudang'],
                    'IsHapus'           => 0
                ];

                $itembarang = [
                    'NoUrut'        => 1,
                    'NoTrans'       => $trbarang['NoTrans'],
                    'KodeBarang'    => $trbarang['NoRefTrSistem'],
                    'Qty'           => $stokob,
                    'HargaSatuan'   => $insertdata['NilaiHPP'],
                    'Total'         => $insertdata['NilaiHPP'] * $stokob,
                    'JenisStok'     => "MASUK",
                    'GudangTujuan'  => $trbarang['GudangTujuan'],
                    'SatuanBarang'  => $insertdata['SatuanBarang'],
                    'IsHapus'       => 0
                ];

                if ((int)$stokob > 0) {
                    $insertbarang = $this->crud->insert($trbarang, 'transaksibarang');
                    $insertitembr = $this->crud->insert($itembarang, 'itemtransaksibarang');
                }
            }
        } else {
            $updatedata = $this->input->post();
            unset($updatedata['fotoLama1']);
            unset($updatedata['fotoLama2']);
            unset($updatedata['fotoLama3']);
            unset($updatedata['KodeBarang']);
            unset($updatedata['NilaiHPP']);
            unset($updatedata['KodeGudang']);
            unset($updatedata['CanEditOB']);
            unset($updatedata['SimpanOB']);

            ## UPDATE FOTO BARANG
            if(!empty($_FILES['Foto1']['name'])){
                $Foto1 = $this->uploadFoto('Foto1');
                $updatedata['Foto1'] = $Foto1;
                $path = realpath(APPPATH . '../assets/barang/'.$this->input->post('fotoLama1'));
                if (@file_exists($path)) 
                {
                    @unlink($path);
                }
            } else {
                unset($updatedata['Foto1']);
            }
            if(!empty($_FILES['Foto2']['name'])){
                $Foto2 = $this->uploadFoto('Foto2');
                $updatedata['Foto2'] = $Foto2;
                $path = realpath(APPPATH . '../assets/barang/'.$this->input->post('fotoLama2'));
                if (@file_exists($path)) 
                {
                    @unlink($path);
                }
            } else {
                unset($updatedata['Foto2']);
            }

            $updatedata['HargaBeliTerakhir']    = str_replace(['.', ','], ['', '.'], $this->input->post('HargaBeliTerakhir'));
            $updatedata['HargaJual']            = str_replace(['.', ','], ['', '.'], $this->input->post('HargaJual'));
            $updatedata['BeratBarang']          = str_replace(['.', ','], ['', '.'], $this->input->post('BeratBarang'));
            $updatedata['StokOpeningBalance']   = 0;
            $updatedata['HPPOpeningBalance']    = 0;
            $kodeBarang  = $this->input->post('KodeBarang');
            $hppob = ($this->input->post('HPPOpeningBalance') != null) ? str_replace(['.', ','], ['', '.'], $this->input->post('HPPOpeningBalance')) : 0;
            $stokob = ($this->input->post('StokOpeningBalance') != null) ? str_replace(['.', ','], ['', '.'], $this->input->post('StokOpeningBalance')) : 0;
            $hppsistem = $this->input->post('NilaiHPP');
            $stoksistem = $this->lokasi->get_stok_asli($kodeBarang)['stok'];

            if ($simpanob != 0) {
                $updatedata['KodeGudang'] = $this->input->post('KodeGudang');
                if ((int)$stokob > 0) {
                    $cektrbarang = $this->crud->get_one_row([
                        'select' => '*',
                        'from' => 'transaksibarang t',
                        'join' => [[
                            'table' => 'itemtransaksibarang i',
                            'on' => 't.NoTrans = i.NoTrans',
                            'param' => 'INNER'
                        ]],
                        'where' => [[
                            't.NoRefTrSistem' => $kodeBarang,
                            't.GudangTujuan' => $updatedata['KodeGudang']
                        ]]
                    ]);
                    if ($cektrbarang) {
                        $stokgudang = $cektrbarang['Qty'];
                        $itembarang = [
                            'Qty'           => $stokob,
                            'HargaSatuan'   => $hppob,
                            'Total'         => $hppob * $stokob
                        ];
                        $updateitembarang = $this->crud->update($itembarang, ['NoTrans' => $cektrbarang['NoTrans']], 'itemtransaksibarang');
                    } else {
                        $stokgudang = 0;
                        $prefix = "TBR-" . date("Ym");
                        $trbarang = [
                            'NoTrans' => $this->crud->get_kode([
                                'select' => 'RIGHT(NoTrans, 7) AS KODE',
                                'from' => 'transaksibarang',
                                'where' => [['LEFT(NoTrans, 10) =' => $prefix]],
                                'limit' => 1,
                                'order_by' => 'NoTrans DESC',
                                'prefix' => $prefix
                            ]),
                            'TanggalTransaksi'  => date('Y-m-d H:i'),
                            'UserName'          => $this->session->userdata('UserName'),
                            'JenisTransaksi'    => "OPENING BALANCE",
                            'NoRefTrSistem'     => $kodeBarang,
                            'GudangTujuan'      => $updatedata['KodeGudang'],
                            'IsHapus'           => 0
                        ];

                        $itembarang = [
                            'NoUrut'        => 1,
                            'NoTrans'       => $trbarang['NoTrans'],
                            'KodeBarang'    => $trbarang['NoRefTrSistem'],
                            'Qty'           => $stokob,
                            'HargaSatuan'   => $hppob,
                            'Total'         => $hppob * $stokob,
                            'JenisStok'     => "MASUK",
                            'GudangTujuan'  => $trbarang['GudangTujuan'],
                            'SatuanBarang'  => $updatedata['SatuanBarang'],
                            'IsHapus'       => 0
                        ];

                        $insertbarang = $this->crud->insert($trbarang, 'transaksibarang');
                        $insertitembr = $this->crud->insert($itembarang, 'itemtransaksibarang');
                    }
                    $stokkondisi = (int)$stoksistem - (int)$stokgudang;
                    $updatedata['NilaiHPP'] = (($stokkondisi * $hppsistem) + ($itembarang['Qty'] * $hppob)) / ($stokkondisi + $itembarang['Qty']);
                }
            }

            $isEdit = true;
            $res = $this->crud->update($updatedata, ['KodeBarang' => $kodeBarang], "mstbarang");
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('KodeBarang') : $insertdata['KodeBarang'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Master Barang',
                'Description' => $ket . ' data master barang ' . $id
            ]);
            if ($isEdit == true) {
                $this->session->set_flashdata('berhasil', 'Berhasil mengubah data!');
            } else {
                $this->session->set_flashdata('berhasil', 'Berhasil menambahkan data!');
            }
        } else {
            if ($isEdit == true) {
                $this->session->set_flashdata('gagal', 'Gagal mengubah data!');
            } else {
                $this->session->set_flashdata('gagal', 'Gagal menambahkan data!');
            }
        }

        redirect(base_url('master/barang'));
    }

    public function get_ob()
    {
        $kodebarang = $this->input->get('KodeBarang');
        $kodegudang = $this->input->get('KodeGudang');

        $data = $this->crud->get_one_row([
            'select' => 't.NoTrans, i.KodeBarang, i.Qty, i.HargaSatuan, i.Total',
            'from' => 'itemtransaksibarang i',
            'join' => [[
                'table' => 'transaksibarang t',
                'on' => "i.NoTrans = t.NoTrans",
                'param' => 'INNER'
            ]],
            'where' => [[
                'i.KodeBarang' => $kodebarang,
                't.GudangTujuan' => $kodegudang,
                't.JenisTransaksi' => 'OPENING BALANCE'
            ]]
        ]);

        echo json_encode($data);
    }

    private function uploadFoto($param, $targetdir = "assets/barang") {
        $this->load->library('upload');
        $config['upload_path'] = $targetdir; //path folder
        $config['allowed_types'] = 'png|jpeg|jpg'; //type yang dapat diakses bisa anda sesuaikan
        $config['encrypt_name'] = TRUE; //Enkripsi nama yang terupload
        $this->upload->initialize($config);

        $gambar1 = "";
        if ($this->upload->do_upload($param)) {
            $gbr = $this->upload->data();
            if ($gbr) {
                $gambar1 = $gbr['file_name'];
                return $gambar1;
            } else {
                $this->upload->display_errors();
            }
        } else {
            $this->upload->display_errors();
        }
    }

    public function hapus()
    {
        $kode  = $this->input->get('KodeBarang');

        $cekitem = $this->crud->get_count([
            'select' => 'i.NoTrans, i.NoUrut, i.KodeBarang',
            'from' => 'itemtransaksibarang i',
            'join' => [[
                'table' => ' transaksibarang tb',
                'on' => "tb.NoTrans = i.NoTrans",
                'param' => 'INNER',
            ]],
            'where' => [[
                'i.KodeBarang' => $kode,
                'tb.JenisTransaksi !=' => "OPENING BALANCE"
            ]],
        ]);

        if ($cekitem > 0) {
            $res = null;
        } else {
            $cek_ob = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'itemtransaksibarang i',
                'join' => [[
                    'table' => ' transaksibarang tb',
                    'on' => "tb.NoTrans = i.NoTrans",
                    'param' => 'INNER',
                ]],
                'where' => [[
                    'i.KodeBarang' => $kode,
                    'tb.JenisTransaksi' => "OPENING BALANCE"
                ]],
            ]);
            if ($cek_ob) {
                $deleteitembarang = $this->crud->delete(['NoTrans' => $cek_ob['NoTrans']], 'itemtransaksibarang');
                $deletetrbarang   = $this->crud->delete(['NoTrans' => $cek_ob['NoTrans']], 'transaksibarang');
            }

            $cekdb = $this->db->from('mstbarang')->where(['KodeBarang' => $kode])->get();
            if ((int)$cekdb->num_rows() > 0) {
                $gb = [];
                $foto = $cekdb->row();
                if ($foto->Foto1 != null) {
                    $gb[] = $foto->Foto1;
                }
                if ($foto->Foto2 != null) {
                    $gb[] = $foto->Foto2;
                }

                if (count($gb) > 0) {
                    $gbr = [];
                    foreach ($gb as $val => $value) {
                        $gbr[] = $value;

                        $path = realpath(APPPATH . '../assets/barang/'.$value);
                        if (@file_exists($path)) 
                        {
                            @unlink($path);
                        }
                    }
                }
            }

            $res = $this->crud->delete(['KodeBarang' => $kode], 'mstbarang');
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Master Barang',
                'Description' => 'hapus data master barang ' . $kode
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

    public function aktif()
    {
        $kode = $this->input->get('KodeBarang');
        $value = (int) $this->input->get('IsAktif');

        $data = ['IsAktif' => $value];
        $result = $this->crud->update($data, ['KodeBarang' => $kode], "mstbarang");

        if ($result) {
            $keterangan = 'update data tahun ' . $kode;
            $aksi = 'edit';
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'edit',
                'JenisTransaksi' => 'Master Barang',
                'Description' => 'update data master barang ' . $kode
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Mengubah Data"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Mengubah Data"
            ]);
        }
    }
}
