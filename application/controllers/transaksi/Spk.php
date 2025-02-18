<?php
defined('BASEPATH') or exit('No direct script access allowed');

class spk extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transaksibarang b';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[28]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[28]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'spk';
            $data['title'] = 'Surat Perintah Kerja (SPK)';
            $data['view'] = 'transaksi/v_spk';
            $data['scripts'] = 'transaksi/s_spk';

            $data['gudang'] = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstgudang',
                    'where' => [['KodeGudang !=' => null]],
                ]
            );

            $data['dtbarang'] = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstbarang b',
                    'join' => [[
                        'table' => 'mstjenisbarang j',
                        'on' => "b.KodeJenis = j.KodeJenis",
                        'param' => 'LEFT' 
                    ]],
                    'where' => [
                        "j.NamaJenisBarang LIKE '%BARANG JADI%'"
                    ],
                ]
            );

            $trbarang = $this->db->select('NoRefTrSistem')
            ->from('transaksibarang')
            ->where('IsHapus', 0)
            ->where('GudangAsal !=', null)
            ->like('NoRefTrSistem', 'TJL')
            ->get()
            ->result_array();
            $noreftrsistem = ['0'];
            $i = 0;
            foreach ($trbarang as $key) {
                $noreftrsistem[$i] = $key['NoRefTrSistem'];
                $i++;
            }

            $data['dtspk'] = $this->db->select('*')
            ->from('transpenjualan')
            ->where(
                [
                    'TglSlipOrder !=' => null,
                    'SPKNomor !=' => null,
                    'StatusProses' => 'SPK'
                ]
            )
            ->where_not_in('IDTransJual', $noreftrsistem)
            ->get()
            ->result_array();

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transpenjualan j';

            $configData['where'] = [
                [
                    'j.SPKNomor !=' => null,
                    'j.StatusProses !=' => 'SO',
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
                'j.IDTransJual', 'j.NoSlipOrder', 'j.SPKDibuatOleh', 'j.TglSlipOrder', 'j.EstimasiSelesai', 'j.SODibuatOleh', 'j.SPKNomor', 'j.SPKTanggal', 'j.TotalNilaiBarang', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.KodePerson', 'p.NamaPersonCP', 'b.NoRefTrSistem', 'j.StatusProduksi', 'b.NoTrans'
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
                'j.IDTransJual', 'j.NoSlipOrder', 'j.SPKDibuatOleh', 'j.TglSlipOrder', 'j.EstimasiSelesai', 'j.SODibuatOleh', 'j.SPKNomor', 'j.SPKTanggal', 'j.TotalNilaiBarang', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.KodePerson', 'p.NamaPersonCP', 'b.NoRefTrSistem', 'j.StatusProduksi', 'b.NoTrans',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 28; //FiturID di tabel serverfitur
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
                $btn_hapus = ($temp['StatusProses'] != 'DONE') ? '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoRefTrSistem'] . ' data-kode2=' . $temp['NoTrans'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>' : '';
                $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/spk/tambah/' . base64_encode($temp['NoRefTrSistem'])) . '" type="button" title="Detail SPK"><span class="fa fa-list" aria-hidden="true"></span></a>' . $btn_hapus;
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpan()
    {
        $isEdit = true;
        if (!($this->input->post('NoRefTrSistem') != null && $this->input->post('NoRefTrSistem') != '')) {
            $prefix = "PRD-" . date("Ym");
            $prefixspk = "SPK-" . date("Ym");
            $insertdata['IDTransJual'] = $this->crud->get_kode([
                'select' => 'RIGHT(IDTransJual, 7) AS KODE',
                'from' => 'transpenjualan',
                'where' => [['LEFT(IDTransJual, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'IDTransJual DESC',
                'prefix' => $prefix
            ]);
            $insertdata['SPKDibuatOleh']    = $this->session->userdata('ActualName');
            $insertdata['SPKTanggal']       = date('Y-m-d H:i');
            $insertdata['NoRef_Manual']     = $this->input->post('NoRefTrManual');
            $insertdata['StatusProses']     = 'SPK';
            $insertdata['KodeGudang']       = $this->input->post('Gudang');
            $insertdata['StatusProduksi']   = 'WIP';
            $insertdata['SPKNomor'] = $this->crud->get_kode([
                'select' => 'RIGHT(SPKNomor, 7) AS KODE',
                'from' => 'transpenjualan',
                'where' => [['LEFT(SPKNomor, 10) =' => $prefixspk]],
                'limit' => 1,
                'order_by' => 'SPKNomor DESC',
                'prefix' => $prefixspk
            ]);
            $inserttrjual = $this->crud->insert($insertdata, 'transpenjualan');

            $prefix2 = "TBR-" . date("Ym");
            $insertbrg['NoTrans'] = $this->crud->get_kode([
                'select' => 'RIGHT(NoTrans, 7) AS KODE',
                'from' => 'transaksibarang',
                'where' => [['LEFT(NoTrans, 10) =' => $prefix2]],
                'limit' => 1,
                'order_by' => 'NoTrans DESC',
                'prefix' => $prefix2
            ]);
            $insertbrg['TanggalTransaksi'] = $insertdata['SPKTanggal'];
            $insertbrg['UserName']         = $this->session->userdata('UserName');
            $insertbrg['Deskripsi']        = $this->input->post('Deskripsi');
            $insertbrg['JenisTransaksi']   = 'PRODUKSI';
            $insertbrg['NoRefTrSistem']    = $insertdata['IDTransJual'];
            $insertbrg['GudangAsal']       = $insertdata['KodeGudang'];
            $insertbrg['GudangTujuan']     = $insertdata['KodeGudang'];
            $insertbrg['IsHapus']          = 0;
            $inserttrbarang = $this->crud->insert($insertbrg, 'transaksibarang');

            $id = $insertdata['IDTransJual'];
            $isEdit = false;
        } else {
            $updatedata['NoRefTrManual']    = $this->input->post('NoRefTrManual');
            $updatedata['Deskripsi']        = $this->input->post('Deskripsi');
            $updatedata['GudangAsal']       = $this->input->post('Gudang');
            $updatedata['GudangTujuan']     = $this->input->post('Gudang');
            $trbarang = $this->crud->get_count(
                [
                    'select' => '*',
                    'from' => 'transaksibarang',
                    'where' => [['NoRefTrSistem' => $this->input->post('NoRefTrSistem')]],
                ]
            );
            if ($trbarang > 0) {
                $updatetrbarang = $this->crud->update($updatedata, ['NoRefTrSistem' => $this->input->post('NoRefTrSistem')], 'transaksibarang');
            }
            $id = $this->input->post('NoRefTrSistem');
            $isEdit = true;
        }

        $aksi = 'tambah';
        if ($id) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Surat Perintah Kerja (SPK)',
                'Description' => $ket . ' data surat perintah kerja ' . $id
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ("Berhasil menambah Data"),
                'id' => $id,
                'action' => $aksi
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
        $kode  = $this->input->get('IDTransJual');
        $kode2  = $this->input->get('NoTrans');

        $countbarang = $this->crud->get_count([
            'select' => 'NoTrans',
            'from' => 'itemtransaksibarang',
            'where' => [['NoTrans' => $kode2]],
        ]);

        $countaktivitas = $this->crud->get_count([
            'select' => 'NoTrans',
            'from' => 'aktivitasproduksi',
            'where' => [['NoTrans' => $kode2]],
        ]);

        // jika ada item barang dan aktivitas produksi maka dihapus
        if ($countbarang > 0) {
            $itembarang = $this->crud->update(['IsHapus' => 1], ['NoTrans' => $kode2], 'itemtransaksibarang');
        }
        if ($countaktivitas > 0) {
            $aktivitasprod = $this->crud->delete(['NoTrans' => $kode2], 'aktivitasproduksi');
        }
        $deletetrbarang = $this->crud->delete(['NoTrans' => $kode2], 'transaksibarang');
        $res = $this->crud->delete(['IDTransJual' => $kode], 'transpenjualan');
        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Surat Perintah Kerja (SPK)',
                'Description' => 'hapus data surat perintah kerja ' . $kode
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

    public function tambah()
    {
        checkAccess($this->session->userdata('fiturview')[28]);
        $noreftrsistem        = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA TRANSAKSI BARANG
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'spk';
            $data['title'] = 'Detail Surat Perintah Kerja (SPK)';
            $data['view'] = 'transaksi/v_spk_tambah';
            $data['scripts'] = 'transaksi/s_spk_tambah';

            $dtinduk = [
                'select' => 'j.IDTransJual, j.TglSlipOrder, j.EstimasiSelesai, j.SPKTanggal, j.NoSlipOrder, j.SPKNomor, j.NoRef_Manual, j.KodePerson, p.NamaPersonCP, b.NoRefTrSistem, b.Deskripsi, b.GudangAsal, ga.NamaGudang as NamaGudangAsal, b.GudangTujuan, gt.NamaGudang as NamaGudangTujuan, j.KodeGudang, gu.NamaGudang as Gudang, j.SPKDibuatOleh, j.SPKDisetujuiOleh, j.SPKDisetujuiTgl, j.SPKDiketahuiOleh, j.SPKDiketahuiTgl, j.StatusProses, j.StatusProduksi, b.NoTrans',
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
                        'table' => ' mstgudang gu',
                        'on' => "gu.KodeGudang = j.KodeGudang",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['j.IDTransJual' => $noreftrsistem]],
                'group_by' => 'b.NoRefTrSistem',
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['itemProd'] = $this->crud->get_count([
                'select' => '*',
                'from' => 'itemtransaksibarang',
                'where' => [[
                    'NoTrans' => $data['dtinduk']['NoTrans'],
                    'IsBarangJadi' => 1,
                    'IsHapus' => 0
                ]]
            ]);
            $data['itemBahan'] = $this->crud->get_count([
                'select' => '*',
                'from' => 'itemtransaksibarang',
                'where' => [[
                    'NoTrans' => $data['dtinduk']['NoTrans'],
                    'IsBahanBaku' => 1,
                    'IsHapus' => 0
                ]]
            ]);
            $data['NoRefTrSistem'] = $noreftrsistem;

            $data['barangjadi'] = $this->crud->get_rows([
                'select' => '*',
                'from' => 'mstbarang b',
                'join' => [[
                    'table' => 'mstjenisbarang j',
                    'on' => "b.KodeJenis = j.KodeJenis",
                    'param' => 'INNER'
                ]],
                'where' => ["j.NamaJenisBarang LIKE 'BARANG JADI' OR j.NamaJenisBarang LIKE '%CETAKAN%'"],
                'order_by' => 'b.KodeBarang ASC'
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

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $noreftrsistem   = $this->input->get('noreftrsistem');
            $configData['table'] = 'transaksibarang b';
            $configData['where'] = [
                [
                    'b.NoRefTrSistem' => $noreftrsistem,
                    'b.IsHapus' => 0,
                ]
            ];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (b.NoTrans LIKE '%$cari%')";
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
                    'table' => ' itempenjualan ij',
                    'on' => ' ij.KodeBarang = b.KodeBarang AND ij.IDTransJual = b.NoRefTrSistem',
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Username', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrSistem', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.GudangAsal', 'ga.NamaGudang as NamaGudangAsal', 'b.GudangTujuan', 'gt.NamaGudang as NamaGudangTujuan', 'b.KodeBarang', 'br.NamaBarang', 'j.TglSlipOrder', 'j.SPKNomor', 'ij.NoUrut', 'ij.Qty', 'b.JmlProduksi'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Username', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrSistem', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.GudangAsal', 'ga.NamaGudangAsal', 'b.GudangTujuan', 'gt.NamaGudangTujuan', 'b.KodeBarang', 'br.NamaBarang', 'j.TglSlipOrder', 'j.SPKNomor', 'ij.NoUrut', 'ij.Qty', 'b.JmlProduksi',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 28; //FiturID di tabel serverfitur
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
                $temp['ProdTglSelesai'] = isset($temp['ProdTglSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($temp['ProdTglSelesai']))) . ' ' . date('H:i', strtotime($temp['ProdTglSelesai'])) : '-';
                if ($temp['TglSlipOrder']) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } else {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function get_item_produksi()
    {
        ## AMBIL DATA BARANG PRODUKSI
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'spk';
            $data['title'] = 'Detail Surat Perintah Kerja (SPK)';
            $data['view'] = 'transaksi/v_spk_tambah';
            $data['scripts'] = 'transaksi/s_spk_tambah';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $notrans = $this->input->get('notrans');
            $barangjadi = $this->input->get('barangjadi');
            $bahanbaku = $this->input->get('bahanbaku');
            $configData['table'] = 'itemtransaksibarang i';
            $configData['where'] = [
                [
                    'i.NoTrans' => $notrans,
                    'i.IsBarangJadi' => $barangjadi,
                    'i.IsBahanBaku' => $bahanbaku,
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
                    'on' => "b.NoTrans = i.NoTrans",
                    'param' => 'INNER',
                ],
                [
                    'table' => ' transpenjualan j',
                    'on' => "j.IDTransJual = b.NoRefTrSistem",
                    'param' => 'INNER'
                ]
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.NoUrut', 'i.NoTrans', 'i.KodeBarang', 'i.Qty', 'i.HargaSatuan', 'i.Total', 'i.Deskripsi', 'i.JenisStok', 'i.GudangAsal', 'i.GudangTujuan', 'i.SatuanBarang', 'i.JenisBarang', 'i.Kategory', 'i.IsBarangJadi', 'i.ProdUkuran', 'i.ProdJmlDaun', 'i.IsHapus', 'br.NamaBarang', 'b.TanggalTransaksi', 'b.NoRefTrSistem', 'j.StatusProses', 'j.StatusProduksi', 'jb.NamaJenisBarang'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.NoUrut', 'i.NoTrans', 'i.KodeBarang', 'i.Qty', 'i.HargaSatuan', 'i.Total', 'i.Deskripsi', 'i.JenisStok', 'i.GudangAsal', 'i.GudangTujuan', 'i.SatuanBarang', 'i.JenisBarang', 'i.Kategory', 'i.IsBarangJadi', 'i.ProdUkuran', 'i.ProdJmlDaun', 'i.IsHapus', 'br.NamaBarang', 'b.TanggalTransaksi', 'b.NoRefTrSistem', 'j.StatusProses', 'j.StatusProduksi', 'jb.NamaJenisBarang',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 28; //FiturID di tabel serverfitur
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
                $temp['PemakaianBahanMasak'] = ($temp['IsBarangJadi'] == 1) ? $this->pemakaian_bahan_masak($temp['NoTrans'], $temp['Qty']) : 0;
                if ($temp['StatusProses'] != 'DONE') {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($temp) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
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

    public function simpantambah()
    {
        $insertdata = $this->input->post();
        unset($insertdata['Gudang']);
        $isEdit = true;
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
            $insertdata['HargaSatuan'] = ($this->input->post('HargaSatuan') != null) ? str_replace(['.', ','], ['', '.'], $this->input->post('HargaSatuan')) : 0;
            $insertdata['Qty'] = ($this->input->post('Qty') != null) ? str_replace(['.', ','], ['', '.'], $this->input->post('Qty')) : 0;
            $insertdata['BeratKotor'] = 0;
            $insertdata['IsHapus'] = 0;
            $isEdit = false;
        } else {
            $insertdata['HargaSatuan'] = ($this->input->post('HargaSatuan') != null) ? str_replace(['.', ','], ['', '.'], $this->input->post('HargaSatuan')) : 0;
            $insertdata['Qty'] = ($this->input->post('Qty') != null) ? str_replace(['.', ','], ['', '.'], $this->input->post('Qty')) : 0;
            $isEdit = true;
        }
        $insertdata['Total'] = $insertdata['HargaSatuan'] * $insertdata['Qty'];

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

    public function hapusitemtambah()
    {
        $kode = $this->input->get('NoTrans');
        $kode2 = $this->input->get('NoUrut');

        $countaktivitas = $this->crud->get_count([
            'select' => 'NoTrans',
            'from' => 'aktivitasproduksi',
            'where' => [[
                'NoTrans' => $kode,
                'NoUrut' => $kode2
            ]],
        ]);

        // hapus data di aktivitasproduksi
        if ($countaktivitas > 0) {
            $aktivitasprod = $this->crud->delete(['NoTrans' => $kode, 'NoUrut' => $kode2], 'aktivitasproduksi');
        }

        $res = $this->crud->delete(['NoTrans' => $kode, 'NoUrut' => $kode2], 'itemtransaksibarang');

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

    public function selesaispk()
    {
        $notrans = $this->input->get('NoTrans');

        $gettrbarang = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transaksibarang',
            'where' => [['NoTrans' => $notrans]]
        ]);

        $getProduksi = $this->crud->get_rows([
            'select' => '*',
            'from' => 'itemtransaksibarang',
            'where' => [[
                'NoTrans' => $notrans,
                'IsBarangJadi' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $countitemprod = count($getProduksi);

        if ($countitemprod < 1) {
            echo json_encode([
                'status' => false,
                'msg'  => "Item produksi belum diinputkan!"
            ]);
        } else {
            $res = $this->crud->update(['StatusProses' => 'DONE'], ['IDTransJual' => $gettrbarang['NoRefTrSistem']], 'transpenjualan');
    
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

    }

    public function hapustambah()
    {
        $kode = $this->input->get('NoRefTrSistem');
        $data = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'transpenjualan',
                'where' => [['IDTransJual' => $kode]],
            ]
        );
        if (!($data['TglSlipOrder'])) {
            $counttrbarang = $this->crud->get_count(
                [
                    'select' => '*',
                    'from' => 'transaksibarang',
                    'where' => [['NoRefTrSistem' => $kode]],
                ]
            );
            if ($counttrbarang > 0) {
                $deletetrbarang = $this->crud->delete(['NoRefTrSistem' => $kode], 'transaksibarang');
            }
            $res = $this->crud->delete(['IDTransJual' => $kode], 'transpenjualan');
        } else {
            $res = $this->crud->update(['TanggalTransaksi' => null, 'ProdUkuran' => null, 'ProdJmlDaun' => null, 'NoRefTrManual' => null, 'Deskripsi' => null, 'GudangAsal' => null, 'GudangTujuan' => null], ['NoRefTrSistem' => $kode], 'transaksibarang');
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Surat Perintah Kerja (SPK)',
                'Description' => 'hapus data surat perintah kerja ' . $kode
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

    public function verifikasitambah()
    {
        $kode = $this->input->get('NoRefTrSistem');
        $trbarang = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'transaksibarang',
                'where' => [['NoRefTrSistem' => $kode]],
            ]
        );
        $data = [];
        $i = 0;
        foreach ($trbarang as $key) {
            $data[$i]['NoTrans'] = $key['NoTrans'];
            $data[$i]['TanggalTransaksi'] = $key['TanggalTransaksi'];

            // hapus data trbarang jika tanggal transaksi null
            if (!($key['TanggalTransaksi'])) {
                $this->crud->delete(['NoTrans' => $key['NoTrans']], 'transaksibarang');
            }

            $i++;
        }

        // update status produksi pada tr penjualan
        $res = $this->crud->update(['StatusProduksi' => 'WIP'], ['IDTransJual' => $kode], 'transpenjualan');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'edit',
                'JenisTransaksi' => 'Surat Perintah Kerja (SPK)',
                'Description' => 'update data surat perintah kerja ' . $kode
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Menyimpan Data",
                'id' => $kode
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Menyimpan Data"
            ]);
        }
    }

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[28]);
        $noreftrsistem        = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM SPK DETAIL
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'spk';
            $data['title'] = 'Detail Surat Perintah Kerja (SPK)';
            $data['view'] = 'transaksi/v_spk_detail';
            $data['scripts'] = 'transaksi/s_spk_detail';

            $dtinduk = [
                'select' => 'j.IDTransJual, j.TglSlipOrder, j.EstimasiSelesai, j.SPKTanggal, j.NoSlipOrder, j.SPKNomor, j.KodePerson, p.NamaPersonCP, b.NoRefTrSistem, b.Deskripsi, b.GudangAsal, ga.NamaGudang as NamaGudangAsal, b.GudangTujuan, gt.NamaGudang as NamaGudangTujuan, j.SPKDibuatOleh, j.SPKDisetujuiOleh, j.SPKDisetujuiTgl, j.SPKDiketahuiOleh, j.SPKDiketahuiTgl, j.StatusProses, j.StatusProduksi, j.KodeGudang, b.NoRefTrManual',
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
                ],
                'where' => [['j.IDTransJual' => $noreftrsistem]],
                'group_by' => 'b.NoRefTrSistem',
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['NoRefTrSistem'] = $noreftrsistem;

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
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.UserName', 'u.ActualName', 'b.GudangAsal', 'ga.NamaGudang as NamaGudangAsal', 'gt.NamaGudang as NamaGudangTujuan', 'b.KodeBarang', 'br.NamaBarang', 'b.NoRefTrSistem', 'j.IDTransJual', 'j.SPKNomor', 'j.SPKTanggal', 'ij.NoUrut', 'ij.Qty', 'b.KodeProduksi', 'b.JmlProduksi'
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
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.UserName', 'u.ActualName', 'b.GudangAsal', 'ga.NamaGudang as NamaGudangAsal', 'gt.NamaGudang as NamaGudangTujuan', 'b.KodeBarang', 'br.NamaBarang', 'b.NoRefTrSistem', 'j.IDTransJual', 'j.SPKNomor', 'j.SPKTanggal', 'ij.NoUrut', 'ij.Qty', 'b.KodeProduksi', 'b.JmlProduksi',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 28; //FiturID di tabel serverfitur
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
                if ($temp['NoRefTrManual'] != null || $temp['ProdTglSelesai'] != null) {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/spk/subdetail/' . base64_encode($temp['NoTrans'])) . '" type="button" title="Sub Detail SPK"><span class="fa fa-list" aria-hidden="true"></span></a>';
                } else {
                    if ($canEdit == 1 && $canDelete == 1) {
                        $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;&nbsp;<a class="btnfitur" href="' . base_url('transaksi/spk/subdetail/' . base64_encode($temp['NoTrans'])) . '" type="button" title="Sub Detail SPK"><span class="fa fa-list" aria-hidden="true"></span></a>';
                    } elseif ($canEdit == 1 && $canDelete == 0) {
                        $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;&nbsp;<a class="btnfitur" href="' . base_url('transaksi/spk/subdetail/' . base64_encode($temp['NoTrans'])) . '" type="button" title="Sub Detail SPK"><span class="fa fa-list" aria-hidden="true"></span></a>';
                    } elseif ($canEdit == 0 && $canDelete == 1) {
                        $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;&nbsp;<a class="btnfitur" href="' . base_url('transaksi/spk/subdetail/' . base64_encode($temp['NoTrans'])) . '" type="button" title="Sub Detail SPK"><span class="fa fa-list" aria-hidden="true"></span></a>';
                    } else {
                        $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/spk/subdetail/' . base64_encode($temp['NoTrans'])) . '" type="button" title="Sub Detail SPK"><span class="fa fa-list" aria-hidden="true"></span></a>';
                    }
                }
                $temp['SPKTanggal'] = shortdate_indo(date('Y-m-d', strtotime($temp['SPKTanggal'])));
                $temp['TanggalTransaksi'] = shortdate_indo(date('Y-m-d', strtotime($temp['TanggalTransaksi'])));
                $temp['ProdTglSelesai'] = isset($temp['ProdTglSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($temp['ProdTglSelesai']))) : '-';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetakdetail()
    {
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('transaksi/spk/detail/') . $this->uri->segment(4); 

        $dtinduk = [
            'select' => 't.IDTransJual, t.TglSlipOrder, t.EstimasiSelesai, t.SODibuatOleh, t.SPKDisetujuiOleh, t.SPKDisetujuiTgl, t.SPKDiketahuiOleh, t.SPKDiketahuiTgl, t.SPKDibuatOleh, t.SPKTanggal, t.NoSlipOrder, t.SPKNomor, t.StatusProses, p.KodePerson, p.NamaPersonCP, p.NamaUsaha, b.NoTrans, b.NoRefTrManual',
            'from' => 'transpenjualan t',
            'join' => [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = t.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksibarang b',
                    'on' => "t.IDTransJual = b.NoRefTrSistem",
                    'param' => 'LEFT'
                ],
            ],
            'where' => [['t.IDTransJual' => $idtransjual]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
        $data['EstimasiSelesai'] = isset($data['dtinduk']['EstimasiSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['EstimasiSelesai']))) : ''; //. ' ' . date('H:i', strtotime($data['dtinduk']['EstimasiSelesai']))
        $data['SPKTanggal'] = isset($data['dtinduk']['SPKTanggal']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['SPKTanggal']))) : ''; //. ' ' . date('H:i', strtotime($data['dtinduk']['SPKTanggal']))
        $data['SPKDisetujuiTgl'] = isset($data['dtinduk']['SPKDisetujuiTgl']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['SPKDisetujuiTgl']))) : ''; // . ' ' . date('H:i', strtotime($data['dtinduk']['SPKDisetujuiTgl']))
        $data['SPKDiketahuiTgl'] = isset($data['dtinduk']['SPKDiketahuiTgl']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['SPKDiketahuiTgl']))) : ''; // . ' ' . date('H:i', strtotime($data['dtinduk']['SPKDiketahuiTgl']))
        $data['NoSO'] = isset($data['dtinduk']['TglSlipOrder']) ? $data['dtinduk']['IDTransJual'] : '';
        $data['TglSlipOrder'] = isset($data['dtinduk']['TglSlipOrder']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['TglSlipOrder']))) : '-'; //. ' ' . date('H:i', strtotime($data['dtinduk']['TglSlipOrder']))
        $data['KodePerson'] = isset($data['dtinduk']['KodePerson']) ? $data['dtinduk']['KodePerson'] : '-';
        $data['NamaPersonCP'] = isset($data['dtinduk']['KodePerson']) ? $data['dtinduk']['NamaPersonCP'] : '';

        $data['model'] = $this->crud->get_rows([
            'select' => 'i.*, br.NamaBarang',
            'from' => 'itemtransaksibarang i',
            'join' => [
                [
                    'table' => 'mstbarang br',
                    'on' => 'i.KodeBarang = br.KodeBarang',
                    'param' => 'INNER',
                ],
            ],
            'where' => [[
                'NoTrans' => $data['dtinduk']['NoTrans'],
                'IsBarangJadi' => 1,
                'IsHapus' => 0
            ]]
        ]);

        $this->load->library('Pdf');
        $this->load->view('transaksi/cetak_spkspk', $data);
    }

    public function subdetail()
    {
        checkAccess($this->session->userdata('fiturview')[28]);
        $notrans        = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM SPK
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'spk';
            $data['title'] = 'Sub Detail Surat Perintah Kerja (SPK)';
            $data['view'] = 'transaksi/v_spk_subdetail';
            $data['scripts'] = 'transaksi/s_spk_subdetail';

            $dtinduk = [
                'select' => 'b.NoTrans, b.NoRefTrManual, b.TanggalTransaksi, b.Deskripsi, b.ProdTglSelesai, b.ProdUkuran, b.ProdJmlDaun, b.GudangAsal, ga.NamaGudang as NamaGudangAsal, b.GudangTujuan, gt.NamaGudang as NamaGudangTujuan, b.NoRefTrSistem, j.IDTransJual, j.SPKNomor, j.SPKTanggal, b.KodeBarang, br.NamaBarang, ij.NoUrut, ij.Qty, b.JmlProduksi',
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
                ],
                'where' => [['b.NoTrans' => $notrans]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['ProdTglSelesai'] = isset($data['dtinduk']['ProdTglSelesai']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['ProdTglSelesai']))) . ' ' . date('H:i', strtotime($data['dtinduk']['ProdTglSelesai'])) : '-';
            $data['NoTrans'] = $notrans;
            $data['NoRefTrSistem'] = $data['dtinduk']['NoRefTrSistem'];

            $dtbarang = [
                'select' => 'itb.GudangTujuan, itb.KodeBarang, br.NamaBarang',
                'from' => 'itemtransaksibarang itb',
                'join' => [
                    [
                        'table' => ' mstbarang br',
                        'on' => "br.KodeBarang = itb.KodeBarang",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' mstjenisbarang j',
                        'on' => "j.KodeJenis = br.KodeJenis",
                        'param' => 'LEFT'
                    ]
                ],
                'where' => [
                    [
                        // 'itb.GudangTujuan' => $data['dtinduk']['GudangAsal'],
                        'itb.IsHapus' => 0,
                        'br.IsAktif' => 1,
                    ],
                    "j.NamaJenisBarang NOT LIKE '%BARANG JADI%'"
                ],
                'group_by' => 'itb.KodeBarang',
            ];
            $data['dtbarang'] = $this->crud->get_rows($dtbarang);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $notrans   = $this->input->get('notrans');
            $configData['table'] = 'itemtransaksibarang i';
            $configData['where'] = [
                [
                    'i.NoTrans' => $notrans,
                    'i.JenisBarang !=' => 'Barang Jadi',
                    'i.IsHapus' => 0,
                ]
            ];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.NoUrut LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksibarang b',
                    'on' => "b.NoTrans = i.NoTrans",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstjenisbarang j',
                    'on' => "j.KodeJenis = br.KodeJenis",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.NoUrut', 'i.NoTrans', 'i.Qty', 'i.HargaSatuan', 'i.Total', 'i.Deskripsi', 'i.JenisStok', 'i.GudangAsal', 'i.GudangTujuan', 'i.SatuanBarang', 'i.IsHapus', 'i.KodeBarang', 'br.NamaBarang', 'br.KodeJenis', 'j.NamaJenisBarang', 'b.ProdTglSelesai'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.NoUrut', 'i.NoTrans', 'i.Qty', 'i.HargaSatuan', 'i.Total', 'i.Deskripsi', 'i.JenisStok', 'i.GudangAsal', 'i.GudangTujuan', 'i.SatuanBarang', 'i.IsHapus', 'i.KodeBarang', 'br.NamaBarang', 'br.KodeJenis', 'j.NamaJenisBarang', 'b.ProdTglSelesai',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 28; //FiturID di tabel serverfitur
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
                $stok = $this->lokasi->get_stok_per_gudang($temp['GudangAsal'], $temp['KodeBarang']);
                $stokgudang = isset($stok['stok']) ? $stok['stok'] : 0;
                $temp['StokGudangAsal'] = $stokgudang;
                if ($canEdit == 1 && $canDelete == 1 && $temp['ProdTglSelesai'] == null) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($temp) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1 && $temp['ProdTglSelesai'] == null) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($temp) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1 && $temp['ProdTglSelesai'] == null) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function checkStock()
    {
        $StokAsal = $this->input->get('StokAsal');
        $StokTujuan = $this->input->get('StokTujuan');
        $QtyLama = $this->input->get('QtyLama');
        if ($StokTujuan > ($StokAsal + $QtyLama)) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Jumlah kebutuhan tidak boleh melebihi jumlah stok']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Jumlah stok cocok']);
        }
    }

    public function simpansubdetail()
    {
        $insertdata = $this->input->post();
        $isEdit = true;
        unset($insertdata['StokTujuan']);

        // Mengambil data barang dari tabel master barang
        $dtbarang = $this->crud->get_one_row(
            [
                'select'=> '*',
                'from'  => 'mstbarang br',
                'where' => [['br.KodeBarang' => $this->input->post('KodeBarang')]],
                'join' => [
                    [
                        'table' => ' mstjenisbarang j',
                        'on' => "j.KodeJenis = br.KodeJenis",
                        'param' => 'LEFT',
                    ],
                ],
            ]
        );

        // Mengambil data transaksi barang
        $induk = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'transaksibarang',
                'where' => [['NoTrans' => $this->input->post('NoTrans')]],
            ]
        );

        ## POST DATA
        if (!($this->input->post('NoUrut') != null && $this->input->post('NoUrut') != '')) {
            $isEdit = false;
            $getNoUrut = $this->db->from('itemtransaksibarang')
            ->where('NoTrans', $this->input->post('NoTrans'))
            ->select('NoUrut')
            ->order_by('NoUrut', 'desc')
            ->get()->row();
            if ($getNoUrut) {
                $NoUrut = (int)$getNoUrut->NoUrut;
            } else {
                $NoUrut = 0;
            }
            $insertdata['NoUrut']       = $NoUrut + 1;
            $insertdata['Qty']          = $this->input->post('StokTujuan');
            $insertdata['HargaSatuan']  = $dtbarang['HargaBeliTerakhir'];
            $insertdata['Total']        = $insertdata['HargaSatuan'] * $insertdata['Qty'];
            $insertdata['JenisStok']    = 'KELUAR';
            $insertdata['GudangAsal']   = $induk['GudangAsal'];
            $insertdata['SatuanBarang'] = $dtbarang['SatuanBarang'];
            $insertdata['JenisBarang']  = $dtbarang['NamaJenisBarang'];
            $insertdata['IsHapus']      = 0;

            $res = $this->crud->insert($insertdata, 'itemtransaksibarang');
        } else {
            $isEdit = true;
            $updatedata['HargaSatuan']  = $dtbarang['HargaBeliTerakhir'];
            $updatedata['Qty']          = $this->input->post('StokTujuan');
            $updatedata['Total']        = $updatedata['HargaSatuan'] * $updatedata['Qty'];

            $res = $this->crud->update($updatedata, ['NoTrans' => $this->input->post('NoTrans'), 'NoUrut' => $this->input->post('NoUrut')], 'itemtransaksibarang');
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

    public function hapussubdetail()
    {
        $kode  = $this->input->get('NoTrans');
        $kode2 = $this->input->get('NoUrut');

        $res = $this->crud->update(['IsHapus' => 1], ['NoTrans' => $kode, 'NoUrut' => $kode2], 'itemtransaksibarang');
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

