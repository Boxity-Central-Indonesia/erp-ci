<?php
defined('BASEPATH') or exit('No direct script access allowed');

class transaksi_po extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transpembelian b';
        checkAccess($this->session->userdata('fiturview')[11]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[11]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'transpo';
            $data['title'] = 'Cetak Pembelian (PO)';
            $data['view'] = 'transaksi/v_transaksi_po';
            $data['scripts'] = 'transaksi/s_transaksi_po';

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

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transpembelian b';

            $configData['where'] = [[
                'b.NoPO !=' => null,
                'b.IsVoid' => 0,
            ]];

            $cari     = $this->input->get('caripo');
            if ($cari != '') {
                $configData['filters'][] = " (b.IDTransBeli LIKE '%$cari%' OR b.NoPO LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%' OR b.StatusProses LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tglpo'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(b.TglPO) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = b.KodePerson",
                    'param' => 'LEFT',

                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.IDTransBeli', 'b.NoPO', 'b.TglPO', 'b.UserPO', 'b.TotalNilaiBarang', 'b.TotalTagihan', 'b.StatusProses', 'b.StatusKirim', 'b.StatusBayar', 'b.KodePerson', 'p.NamaPersonCP'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'b.IDTransBeli';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'b.IDTransBeli', 'b.NoPO', 'b.TglPO', 'b.UserPO', 'b.TotalNilaiBarang', 'b.TotalTagihan', 'b.StatusProses', 'b.StatusKirim', 'b.StatusBayar', 'b.KodePerson', 'p.NamaPersonCP',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 11; //FiturID di tabel serverfitur
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
                $temp['TglPO'] = shortdate_indo(date('Y-m-d', strtotime($temp['TglPO']))) . ' ' . date('H:i', strtotime($temp['TglPO']));
                if ($canEdit == 1 && $canDelete == 1 && $temp['StatusProses'] == 'PO') {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_po/detail/' . base64_encode($temp['IDTransBeli'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a class="btneditpo" type="button" href="#" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransBeli'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1 && $temp['StatusProses'] == 'PO') {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_po/detail/' . base64_encode($temp['IDTransBeli'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a class="btneditpo" type="button" href="#" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit == 1 && $temp['StatusProses'] == 'PO') {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_po/detail/' . base64_encode($temp['IDTransBeli'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransBeli'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_po/detail/' . base64_encode($temp['IDTransBeli'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
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
            $insertdata['StatusProses']     = 'PO';
            $insertdata['IsVoid']           = 0;
            $aksi = 'tambah';
            $isEdit = false;
        } else {
            $aksi = 'edit';
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
                'JenisTransaksi' => 'Cetak Pembelian (PO)',
                'Description' => $ket . ' data cetak pembelian (po) ' . $id
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data"),
                'id' => $insertdata['IDTransBeli'],
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
        $kode  = $this->input->get('IDTransBeli');

        ## Menghapus data di tabel item pembelian
        $item = $this->crud->delete(['IDTransBeli' => $kode], 'itempembelian');

        $res = $this->crud->delete(['IDTransBeli' => $kode], 'transpembelian');
        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Cetak Pembelian (PO)',
                'Description' => 'hapus data cetak pembelian (po) ' . $kode
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
        checkAccess($this->session->userdata('fiturview')[11]);
        $idtransbeli   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_beli';
            $data['title'] = 'Detail Transaksi Pembelian (PO)';
            $data['view'] = 'transaksi/v_transaksi_po_detail';
            $data['scripts'] = 'transaksi/s_transaksi_po_detail';

            $dtbarang = [
                'select' => '*',
                'from' => 'mstbarang',
                'where' => [['IsAktif' => 1]]
            ];
            $data['dtbarang'] = $this->crud->get_rows($dtbarang);

            $dtinduk = [
                'select' => 't.IDTransBeli, t.UserPO, t.TglPO, t.StatusProses, p.KodePerson, p.NamaPersonCP, p.NamaUsaha',
                'from' => 'transpembelian t',
                'join' => [
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = t.KodePerson",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['t.IDTransBeli' => $idtransbeli]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['IDTransBeli'] = $idtransbeli;

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
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.IDTransBeli', 'i.NoUrut', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.StatusProses', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.IDTransBeli', 'i.NoUrut', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 't.StatusProses', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 11; //FiturID di tabel serverfitur
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
                if ($canEdit == 1 && $canDelete == 1 && $temp['StatusProses'] == 'PO') {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransBeli'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1 && $temp['StatusProses'] == 'PO') {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1 && $temp['StatusProses'] == 'PO') {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['IDTransBeli'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
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
        $data['src_url'] = base_url('transaksi/transaksi_po/detail/') . $this->uri->segment(4);
        $data['title'] = 'Detail Transaksi Pembelian (PO)';

        $dtinduk = [
            'select' => 't.IDTransBeli, t.TglPO, p.KodePerson, p.NamaPersonCP, p.NamaUsaha',
            'from' => 'transpembelian t',
            'join' => [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = t.KodePerson",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['t.IDTransBeli' => $idtransbeli]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);

        $sql = [
            'select' => 'i.IDTransBeli, i.NoUrut, i.Spesifikasi, i.HargaSatuan, i.Qty, i.Total, i.SatuanBarang, i.Deskripsi, i.KodeBarang, br.NamaBarang',
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
        $this->load->view('transaksi/v_transaksi_po_detail_cetak', $data);
    }

    public function simpandetail()
    {
        $insertdata = $this->input->post();
        unset($insertdata['TotalLama']);
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
            $insertdata['Total'] = $insertdata['HargaSatuan'] * $this->input->post('Qty');
            $insertdata['IsVoid'] = 0;

            // Menambahkan total ke total nilai barang di tabel transpembelian
            if ($induk['TotalNilaiBarang'] > 0) {
                $totaltagihan = $induk['TotalNilaiBarang'] + $insertdata['Total'];
            } else {
                $totaltagihan = $insertdata['Total'];
            }
            $updatetagihan = $this->crud->update(['TotalNilaiBarang' => $totaltagihan, 'TotalTagihan' => $totaltagihan], ['IDTransBeli' => $this->input->post('IDTransBeli')], 'transpembelian');

            $res = $this->crud->insert($insertdata, 'itempembelian');
        } else {
            $isEdit = true;
            $updatedata['HargaSatuan']  = str_replace(['.', ','], ['', '.'], $this->input->post('HargaSatuan'));
            $updatedata['Qty']          = $this->input->post('Qty');
            $updatedata['SatuanBarang'] = $this->input->post('SatuanBarang');
            $updatedata['Spesifikasi']  = $this->input->post('Spesifikasi');
            $updatedata['Total']        = $updatedata['HargaSatuan'] * $updatedata['Qty'];

            // Mengubah total ke total nilai barang di tabel transpembelian
            if ($induk['TotalNilaiBarang'] > 0) {
                $totaltagihan = $induk['TotalNilaiBarang'] - $this->input->post('TotalLama') + $updatedata['Total'];
            } else {
                $totaltagihan = $updatedata['Total'];
            }
            $updatetagihan = $this->crud->update(['TotalNilaiBarang' => $totaltagihan, 'TotalTagihan' => $totaltagihan], ['IDTransBeli' => $this->input->post('IDTransBeli')], 'transpembelian');

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
        $induk = $this->crud->get_one_row(
            [
                'select'=> '*',
                'from'  => 'transpembelian',
                'where' => [['IDTransBeli' => $kode]]
            ]
        );
        $child = $this->crud->get_one_row(
            [
                'select'=> '*',
                'from'  => 'itempembelian',
                'where' => [['IDTransBeli' => $kode, 'NoUrut' => $kode2]]
            ]
        );

        // Mengurangi total ke total nilai barang di tabel transpembelian
        $totaltagihan = $induk['TotalNilaiBarang'] - $child['Total'];
        $updatetagihan = $this->crud->update(['TotalNilaiBarang' => $totaltagihan, 'TotalTagihan' => $totaltagihan], ['IDTransBeli' => $kode], 'transpembelian');

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
}
