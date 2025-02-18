<?php
defined('BASEPATH') or exit('No direct script access allowed');

class quotation_invoice extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transpenjualan j';
        checkAccess($this->session->userdata('fiturview')[24]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[24]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'quotation';
            $data['title'] = 'Quotation, Purchase Invoice, Invoice';
            $data['view'] = 'transaksi/v_quotation_invoice';
            $data['scripts'] = 'transaksi/s_quotation_invoice';

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
                    'LEFT(j.IDTransJual, 3) =' => 'TJL',
                ]
            ];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cariqi');
            if ($cari != '') {
                $configData['filters'][] = " (j.IDTransJual LIKE '%$cari%' OR j.NoSlipOrder LIKE '%$cari%' OR j.StatusProses LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tglqi'));
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
            $FiturID = 24; //FiturID di tabel serverfitur
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
                $temp['TglSlipOrder'] = shortdate_indo(date('Y-m-d', strtotime($temp['TglSlipOrder']))) . ' ' . date('H:i', strtotime($temp['TglSlipOrder']));
                $temp['EstimasiSelesai'] = shortdate_indo(date('Y-m-d', strtotime($temp['EstimasiSelesai']))) . ' ' . date('H:i', strtotime($temp['EstimasiSelesai']));
                $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/quotation_invoice/detail/' . base64_encode($temp['IDTransJual'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
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

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[24]);
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_jual';
            $data['title'] = 'Detail Quotation, Purchase Invoice, Invoice';
            $data['view'] = 'transaksi/v_quotation_invoice_detail';
            $data['scripts'] = 'transaksi/s_quotation_invoice_detail';

            $dtbarang = [
                'select' => '*',
                'from' => 'mstbarang',
                'where' => [['IsAktif' => 1]]
            ];
            $data['dtbarang'] = $this->crud->get_rows($dtbarang);

            $dtinduk = [
                'select' => 't.IDTransJual, t.TglSlipOrder, t.SODibuatOleh, t.EstimasiSelesai, t.NoSlipOrder, t.StatusProses, p.KodePerson, p.NamaPersonCP, p.NamaUsaha',
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
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.IDTransJual', 'i.NoUrut', 'i.JenisBarang', 'i.Kategory', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.StatusProses', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'br.HargaJual', 'i.AdditionalName'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.IDTransJual', 'i.NoUrut', 'i.JenisBarang', 'i.Kategory', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.StatusProses', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'br.HargaJual', 'i.AdditionalName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 24; //FiturID di tabel serverfitur
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
                $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetakquotation()
    {
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('transaksi/quotation_invoice/detail/') . $this->uri->segment(4);

        $dtinduk = [
            'select' => 't.IDTransJual, t.TotalNilaiBarang, t.TotalTagihan, t.TglSlipOrder, t.EstimasiSelesai, t.NoSlipOrder, t.StatusProses, t.TanggalPenjualan, p.KodePerson, p.NamaPersonCP, p.NoHP, p.AlamatPerson, p.NamaUsaha, p.AlamatPerson',
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

        $comp = $this->crud->get_rows([
            'select' => '*',
            'from' => 'sistemsetting',
        ]);
        $models = [];
        foreach ($comp as $key) {
            if ($key['KodeSetting'] == 1) {
                $models['NamaPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 2) {
                $models['EmailPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 3) {
                $models['NoTelpPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 4) {
                $models['AlamatPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 5) {
                $models['WebsitePerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 6) {
                $models['NamaPimpinan'] = $key['ValueSetting'];
            }
        }
        $data['model'] = $models;

        $sql = [
            'select' => 'i.IDTransJual, i.NoUrut, i.JenisBarang, i.Kategory, i.Spesifikasi, i.HargaSatuan, i.Qty, i.Total, i.SatuanBarang, i.Deskripsi,  i.KodeBarang, br.NamaBarang, br.SatuanBarang as satuanAsal, br.Spesifikasi as spesifikasiAsal, br.HargaBeliTerakhir, br.HargaJual, i.AdditionalName',
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
        $data['data'] = $this->crud->get_rows($sql);

        $this->load->library('Pdf');
        $this->load->view('transaksi/cetak_quotation', $data);
    }

    public function cetakinvoice()
    {
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('transaksi/quotation_invoice/detail/') . $this->uri->segment(4);

        $dtinduk = [
            'select' => 't.IDTransJual, t.TglSlipOrder, t.EstimasiSelesai, t.NoSlipOrder, t.StatusProses, t.TanggalPenjualan, p.KodePerson, p.NamaPersonCP, p.NamaUsaha, p.NoHP, t.DiskonBawah, t.PPN, t.TotalTagihan, k.TotalTransaksi',
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
            ],
            'where' => [['t.IDTransJual' => $idtransjual]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);

        $sql = [
            'select' => 'i.IDTransJual, i.NoUrut, i.JenisBarang, i.Kategory, i.Spesifikasi, i.HargaSatuan, i.Qty, i.Total, i.SatuanBarang, i.Deskripsi,  i.KodeBarang, br.NamaBarang, br.SatuanBarang as satuanAsal, br.Spesifikasi as spesifikasiAsal, br.DeskripsiBarang, br.HargaBeliTerakhir, br.HargaJual, i.AdditionalName',
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
        $data['diskonbawah'] = isset($data['dtinduk']['DiskonBawah']) ? $data['dtinduk']['DiskonBawah'] : 0;
        $data['ppn'] = isset($data['dtinduk']['PPN']) ? $data['dtinduk']['PPN'] : 0;
        $data['totaltagihan'] = isset($data['dtinduk']['TotalTagihan']) ? $data['dtinduk']['TotalTagihan'] : 0;
        $data['totaltransaksi'] = isset($data['dtinduk']['TotalTransaksi']) ? $data['dtinduk']['TotalTransaksi'] : 0;

        $this->load->library('Pdf');
        $this->load->view('transaksi/cetak_invoice', $data);
    }

    public function cetakproforma()
    {
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('transaksi/quotation_invoice/detail/') . $this->uri->segment(4);

        $dtinduk = [
            'select' => 't.IDTransJual, t.TotalNilaiBarang, t.DiskonBawah, t.PPN, t.TotalTagihan, t.TglSlipOrder, t.EstimasiSelesai, t.NoSlipOrder, t.TanggalPenjualan, t.StatusProses, t.TanggalJatuhTempo, p.KodePerson, p.NamaPersonCP, p.NoHP, p.AlamatPerson, p.NamaUsaha, p.AlamatPerson, k.NoRef_Sistem, k.TotalTransaksi',
            'from' => 'transpenjualan t',
            'join' => [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = t.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.NoRef_Sistem = t.IDTransJual",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['t.IDTransJual' => $idtransjual]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
        $data['diskonbawah'] = isset($data['dtinduk']['DiskonBawah']) ? $data['dtinduk']['DiskonBawah'] : 0;
        $data['ppn'] = isset($data['dtinduk']['PPN']) ? $data['dtinduk']['PPN'] : 0;
        $data['totaltagihan'] = isset($data['dtinduk']['TotalTagihan']) ? $data['dtinduk']['TotalTagihan'] : 0;
        $data['totaltransaksi'] = isset($data['dtinduk']['TotalTransaksi']) ? $data['dtinduk']['TotalTransaksi'] : 0;
        $data['jatuhtempo'] = isset($data['dtinduk']['TanggalJatuhTempo']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['TanggalJatuhTempo']))) : '';

        $comp = $this->crud->get_rows([
            'select' => '*',
            'from' => 'sistemsetting',
        ]);
        $models = [];
        foreach ($comp as $key) {
            if ($key['KodeSetting'] == 1) {
                $models['NamaPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 2) {
                $models['EmailPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 3) {
                $models['NoTelpPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 4) {
                $models['AlamatPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 5) {
                $models['WebsitePerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 6) {
                $models['NamaPimpinan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 7) {
                $models['NamaBank'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 8) {
                $models['CabangBank'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 9) {
                $models['NoAkunBank'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 10) {
                $models['AtasNamaBank'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 11) {
                $models['Pesan'] = $key['ValueSetting'];
            }
        }
        $data['model'] = $models;

        $sql = [
            'select' => 'i.IDTransJual, i.NoUrut, i.JenisBarang, i.Kategory, i.Spesifikasi, i.HargaSatuan, i.Qty, i.Diskon, i.Total, i.SatuanBarang, i.Deskripsi,  i.KodeBarang, br.NamaBarang, br.SatuanBarang as satuanAsal, br.Spesifikasi as spesifikasiAsal, br.HargaBeliTerakhir, br.HargaJual, i.AdditionalName',
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
        $data['data'] = $this->crud->get_rows($sql);

        $this->load->library('Pdf');
        $this->load->view('transaksi/cetak_proforma', $data);
    }

    public function simpandetail()
    {
        $insertdata = $this->input->post();
        unset($insertdata['TotalLama']);
        $isEdit = true;

        // Mengambil data induk di tabel transaksi penjualan
        $induk = $this->crud->get_one_row(
            [
                'select'=> '*',
                'from'  => 'transpenjualan',
                'where' => [['IDTransJual' => $this->input->post('IDTransJual')]]
            ]
        );

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
            $insertdata['HargaSatuan'] = str_replace(['.', ','], ['', '.'], $this->input->post('HargaSatuan'));
            $insertdata['Total'] = $insertdata['HargaSatuan'] * $this->input->post('Qty');

            // Menambahkan total ke total nilai barang di tabel transaksi penjualan
            if ($induk['TotalNilaiBarang'] > 0) {
                $totaltagihan = $induk['TotalNilaiBarang'] + $insertdata['Total'];
            } else {
                $totaltagihan = $insertdata['Total'];
            }
            $updatetagihan = $this->crud->update(['TotalNilaiBarang' => $totaltagihan, 'TotalTagihan' => $totaltagihan], ['IDTransJual' => $this->input->post('IDTransJual')], 'transpenjualan');

            $res = $this->crud->insert($insertdata, 'itempenjualan');
        } else {
            $isEdit = true;
            $updatedata['HargaSatuan']      = str_replace(['.', ','], ['', '.'], $this->input->post('HargaSatuan'));
            $updatedata['Qty']              = $this->input->post('Qty');
            $updatedata['AdditionalName']   = $this->input->post('AdditionalName');
            $updatedata['SatuanBarang']     = $this->input->post('SatuanBarang');
            $updatedata['Spesifikasi']      = $this->input->post('Spesifikasi');
            $updatedata['Total']            = $updatedata['HargaSatuan'] * $updatedata['Qty'];

            // Mengubah total ke total nilai barang di tabel transaksi penjualan
            if ($induk['TotalNilaiBarang'] > 0) {
                $totaltagihan = $induk['TotalNilaiBarang'] - $this->input->post('TotalLama') + $updatedata['Total'];
            } else {
                $totaltagihan = $updatedata['Total'];
            }
            $updatetagihan = $this->crud->update(['TotalNilaiBarang' => $totaltagihan, 'TotalTagihan' => $totaltagihan], ['IDTransJual' => $this->input->post('IDTransJual')], 'transpenjualan');

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
}
