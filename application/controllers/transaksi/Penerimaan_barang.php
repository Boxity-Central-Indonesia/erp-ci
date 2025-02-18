<?php
defined('BASEPATH') or exit('No direct script access allowed');

class penerimaan_barang extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[15]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[15]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'penerimaanbrg';
            $data['title'] = 'Transaksi Penerimaan Barang';
            $data['view'] = 'transaksi/v_penerimaan_barang';
            $data['scripts'] = 'transaksi/s_penerimaan_barang';

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
                    // 'b.NoRefTrManual !=' => null,
                    'b.IsHapus' => 0,
                    'pb.StatusKirim !=' => 'BELUM',
                ]
            ];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (b.NoTrans LIKE '%$cari%' OR b.NoRefTrManual LIKE '%$cari%')";
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
                    'table' => ' transpembelian pb',
                    'on' => "pb.IDTransBeli = b.NoRefTrSistem",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = b.UserName",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = b.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' itemtransaksibarang ib',
                    'on' => "ib.NoTrans = b.NoTrans",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = ib.GudangTujuan",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.UserName', 'u.ActualName', 'b.KodePerson', 'p.NamaPersonCP', 'b.NoRefTrSistem', 'pb.IDTransBeli', 'pb.NoPO', 'pb.StatusKirim', 'pb.NoRef_Manual', 'pb.TanggalPembelian', 'ib.GudangTujuan', 'g.NamaGudang'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['group_by'] = 'ib.NoTrans';
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'b.NoTrans';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.UserName', 'u.ActualName', 'b.KodePerson', 'p.NamaPersonCP', 'b.NoRefTrSistem', 'pb.IDTransBeli', 'pb.NoPO', 'pb.StatusKirim', 'pb.NoRef_Manual', 'pb.TanggalPembelian', 'ib.GudangTujuan', 'g.NamaGudang',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 15; //FiturID di tabel serverfitur
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
                $temp['TanggalPembelian'] = shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPembelian']))) . ' ' . date('H:i', strtotime($temp['TanggalPembelian']));
                $temp['TanggalTransaksi'] = shortdate_indo(date('Y-m-d', strtotime($temp['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($temp['TanggalTransaksi']));
                if ($canEdit == 1 && $canDelete == 1 && $temp['StatusKirim'] == 'BELUM') {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/penerimaan_barang/detail/' . base64_encode($temp['NoTrans']) . '/' . base64_encode($temp['NoRefTrSistem'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a class="btnedit" type="button" href="#" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' data-kode2=' . $temp['NoRefTrSistem'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1 && $temp['StatusKirim'] == 'BELUM') {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/penerimaan_barang/detail/' . base64_encode($temp['NoTrans']) . '/' . base64_encode($temp['NoRefTrSistem'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a class="btnedit" type="button" href="#" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1 && $temp['StatusKirim'] == 'BELUM') {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/penerimaan_barang/detail/' . base64_encode($temp['NoTrans']) . '/' . base64_encode($temp['NoRefTrSistem'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' data-kode2=' . $temp['NoRefTrSistem'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/penerimaan_barang/detail/' . base64_encode($temp['NoTrans']) . '/' . base64_encode($temp['NoRefTrSistem'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
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
        unset($insertdata['KodeGudang']);
        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('NoTrans') != null && $this->input->post('NoTrans') != '')) {
            $prefix = "TBR-" . date("Ym");
            $insertdata['NoTrans'] = $this->crud->get_kode([
                'select' => 'RIGHT(NoTrans, 7) AS KODE',
                'where' => [['LEFT(NoTrans, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'NoTrans DESC',
                'prefix' => $prefix
            ]);
            $insertdata['UserName']           = $this->session->userdata('UserName');
            $insertdata['JenisTransaksi'] = 'BARANG DATANG';
            $insertdata['GudangTujuan'] = $this->input->post('KodeGudang');
            $insertdata['IsHapus'] = 0;
            $isEdit = false;
            $aksi = 'tambah';
        } else {
            unset($insertdata['NoRefTrSistem']);
            $updategudang = $this->crud->update(['GudangTujuan' => $this->input->post('KodeGudang')], ['NoTrans' => $this->input->post('NoTrans')], 'itemtransaksibarang');
            $isEdit = true;
            $aksi = 'edit';
        }
        $res = $this->crud->insert_or_update($insertdata, 'transaksibarang');
        if ($isEdit == false) {
            // $updatestatuskirim = $this->crud->update(['StatusKirim' => 'SEBAGIAN'], ['IDTransBeli' => $this->input->post('NoRefTrSistem')], 'transpembelian');
            $item = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'itempembelian',
                    'where' => [['IDTransBeli' => $this->input->post('NoRefTrSistem')]],
                ]
            );

            $insertitem = [];
            $i = 0;
            foreach ($item as $items) {
                $insertitems = $this->db->insert('itemtransaksibarang', array(
                    'NoUrut'        => $items['NoUrut'],
                    'NoTrans'       => $insertdata['NoTrans'],
                    'KodeBarang'    => $items['KodeBarang'],
                    'Qty'           => 0,
                    'HargaSatuan'   => $items['Total'] / $items['Qty'],
                    'Deskripsi'     => $items['Deskripsi'],
                    'SatuanBarang'  => $items['SatuanBarang'],
                    'JenisStok'     => 'MASUK',
                    'GudangTujuan'  => $this->input->post('KodeGudang'),
                    'IsHapus'       => 0
                ));

                $i++;
            }
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('NoTrans') : $insertdata['NoTrans'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Transaksi Penerimaan Barang',
                'Description' => $ket . ' data transaksi penerimaan barang ' . $id
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data"),
                'id' => $insertdata['NoTrans'],
                'id2' => $this->input->post('NoRefTrSistem'),
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
        $kode  = $this->input->get('NoTrans');
        $kode2 = $this->input->get('NoRefTrSistem');

        ## Mengubah status kirim
        $updatestatuskirim = $this->crud->update(['StatusKirim' => 'SEBAGIAN'], ['IDTransBeli' => $kode2], 'transpembelian');

        ## Menghapus data di tabel item transaksi barang
        $countitem = $this->crud->get_count(
            [
                'select' => 'NoUrut',
                'from' => 'itemtransaksibarang',
                'where' => [['NoTrans' => $kode]],
            ]
        );
        if ($countitem > 0) {
            $item = $this->crud->update(['IsHapus' => 1], ['NoTrans' => $kode], 'itemtransaksibarang');
        }

        $res = $this->crud->update(['IsHapus' => 1], ['NoTrans' => $kode], 'transaksibarang');
        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Transaksi Penerimaan Barang',
                'Description' => 'hapus data transaksi penerimaan barang ' . $kode
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
        checkAccess($this->session->userdata('fiturview')[15]);
        $notrans        = escape(base64_decode($this->uri->segment(4)));
        $idtransbeli    = escape(base64_decode($this->uri->segment(5)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'penerimaanbrg';
            $data['title'] = 'Detail Transaksi Penerimaan Barang';
            $data['view'] = 'transaksi/v_penerimaan_barang_detail';
            $data['scripts'] = 'transaksi/s_penerimaan_barang_detail';

            $dtbarang = [
                'select' => '*',
                'from' => 'mstbarang',
                'where' => [['IsAktif' => 1]]
            ];
            $data['dtbarang'] = $this->crud->get_rows($dtbarang);

            $dtinduk = [
                'select' => 'b.NoTrans, b.NoRefTrManual, b.TanggalTransaksi, b.Deskripsi, b.NoRefTrSistem, pb.IDTransBeli, pb.NoPO, pb.StatusProses, pb.StatusKirim, pb.TanggalPembelian, pb.NoRef_Manual, b.KodePerson, p.NamaPersonCP, p.NamaUsaha, itb.GudangTujuan, g.NamaGudang, pb.TotalTagihan, pb.DiskonBawah, pb.PPN, u.ActualName',
                'from' => 'transaksibarang b',
                'join' => [
                    [
                        'table' => ' transpembelian pb',
                        'on' => "pb.IDTransBeli = b.NoRefTrSistem",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = b.KodePerson",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' itemtransaksibarang itb',
                        'on' => "itb.NoTrans = b.NoTrans",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' mstgudang g',
                        'on' => "g.KodeGudang = itb.GudangTujuan",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' userlogin u',
                        'on' => "u.UserName = b.UserName",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['b.NoTrans' => $notrans]],
                'groupby' => 'b.NoTrans',
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['NoTrans'] = $notrans;
            $data['IDTransBeli'] = $idtransbeli;

            $items = $this->crud->get_rows(
                [
                    'select' => 'Qty',
                    'from' => 'itempembelian',
                    'where' => [['IDTransBeli' => $idtransbeli]]
                ]
            );
            $totalPembelianBarang = 0;
            foreach ($items as $item) {
                $totalPembelianBarang += $item['Qty'];
            }
            $data['totalbarang'] = $totalPembelianBarang;

            $dtjurnal = $this->crud->get_one_row([
                'select' => 'IDTransJurnal, NominalTransaksi, NoRefTrans',
                'from' => 'transjurnal j',
                'where' => [['j.NoRefTrans' => $notrans]],
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
            $notrans   = $this->input->get('notrans');
            $idtransbeli   = $this->input->get('idtransbeli');
            $configData['table'] = 'itempembelian i';
            $configData['where'] = [
                [
                    'i.IDTransBeli' => $idtransbeli,
                    'itb.NoTrans' => $notrans,
                    'itb.IsHapus' => 0,
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
                    'on' => "b.NoRefTrSistem = i.IDTransBeli",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transpembelian pb',
                    'on' => "pb.IDTransBeli = i.IDTransBeli",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' itemtransaksibarang itb',
                    'on' => "itb.NoUrut = i.NoUrut AND itb.NoTrans = b.NoTrans",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.IDTransBeli', 'i.NoUrut', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Diskon', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'itb.Qty as JumlahDiterima', 'itb.HargaSatuan as SatuanResult'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.IDTransBeli', 'i.NoUrut', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Diskon', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir', 'itb.Qty as JumlahDiterima', 'itb.HargaSatuan as SatuanResult',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 15; //FiturID di tabel serverfitur
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

            $dtjurnal = $this->crud->get_one_row([
                'select' => 'IDTransJurnal, NominalTransaksi, NoRefTrans',
                'from' => 'transjurnal j',
                'where' => [['j.NoRefTrans' => $notrans]],
            ]);
            $idjurnal = isset($dtjurnal['IDTransJurnal']) ? $dtjurnal['IDTransJurnal'] : '';
            $itemjurnal = $this->lokasi->get_total_item_jurnal($idjurnal);
            $nominaltransaksi   = isset($dtjurnal['NominalTransaksi']) ? (int)$dtjurnal['NominalTransaksi'] : 0;
            $totaljurnaldebet   = (int)$itemjurnal['Debet'];
            $totaljurnalkredit  = (int)$itemjurnal['Kredit'];

            foreach ($records as $record) {
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['JumlahDiterima'] = isset($temp['JumlahDiterima']) ? $temp['JumlahDiterima'] . ' ' . $temp['SatuanBarang'] : 0 . ' ' . $temp['SatuanBarang'];
                $NoRefTrSistem = $temp['IDTransBeli'];
                $KodeBarang = $temp['KodeBarang'];
                $barangdatang = $this->lokasi->get_barang_datang($NoRefTrSistem, $KodeBarang);
                $temp['Stok'] = $barangdatang['jml_datang'] . ' ' . $temp['SatuanBarang'];
                $temp['Qty'] = $temp['Qty'] . ' ' . $temp['SatuanBarang'];
                if ($canEdit == 1) {
                    if ($temp['Stok'] == $temp['Qty']) {
                        $temp['btn_aksi'] = '<a class="btndone" type="button" title="Edit"><i class="fa fa-edit"></i></a>';
                    } elseif ($nominaltransaksi > 0 && $nominaltransaksi == $totaljurnaldebet && $nominaltransaksi == $totaljurnalkredit) {
                        $temp['btn_aksi'] = '<a class="btnsudahjurnal" type="button" title="Edit"><i class="fa fa-edit"></i></a>';
                    } else {
                        if ($temp['JumlahDiterima'] > 0) {
                            $temp['btn_aksi'] = '<a class="btnisi" type="button" title="Edit"><i class="fa fa-edit"></i></a>';
                        } else {
                            $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                        }
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
        $notrans        = escape(base64_decode($this->uri->segment(4)));
        $idtransbeli    = escape(base64_decode($this->uri->segment(5)));
        $data['src_url'] = base_url('transaksi/penerimaan_barang/detail/') . $this->uri->segment(4) . '/' . $this->uri->segment(5);

        $dtinduk = [
            'select' => 'b.NoTrans, b.NoRefTrManual, b.TanggalTransaksi, b.Deskripsi, b.NoRefTrSistem, pb.IDTransBeli, pb.NoPO, pb.StatusProses, pb.StatusKirim, pb.TanggalPembelian, pb.NoRef_Manual, b.KodePerson, p.NamaPersonCP, p.NamaUsaha, itb.GudangTujuan, g.NamaGudang',
            'from' => 'transaksibarang b',
            'join' => [
                [
                    'table' => ' transpembelian pb',
                    'on' => "pb.IDTransBeli = b.NoRefTrSistem",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = b.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' itemtransaksibarang itb',
                    'on' => "itb.NoTrans = b.NoTrans",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = itb.GudangTujuan",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['b.NoTrans' => $notrans]],
            'groupby' => 'b.NoTrans',
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
        $data['kodePO'] = isset($data['dtinduk']['NoPO']) ? $data['dtinduk']['IDTransBeli'] : '-';
        $data['NoTrans'] = $notrans;
        $data['IDTransBeli'] = $idtransbeli;

        $sql = [
            'select' => 'i.IDTransBeli, i.NoUrut, i.Spesifikasi, i.HargaSatuan, i.Qty, i.Diskon, i.Total, i.SatuanBarang, i.Deskripsi, i.KodeBarang, br.NamaBarang, br.SatuanBarang as satuanAsal, br.Spesifikasi as spesifikasiAsal, br.HargaBeliTerakhir, itb.Qty as JumlahDiterima, itb.HargaSatuan as SatuanResult',
            'from' => 'itempembelian i',
            'join' => [
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksibarang b',
                    'on' => "b.NoRefTrSistem = i.IDTransBeli",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transpembelian pb',
                    'on' => "pb.IDTransBeli = i.IDTransBeli",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' itemtransaksibarang itb',
                    'on' => "itb.NoUrut = i.NoUrut AND itb.NoTrans = b.NoTrans",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [
                [
                    'i.IDTransBeli' => $idtransbeli,
                    'itb.NoTrans' => $notrans,
                ]
            ],
        ];
        $data['model'] = $this->crud->get_rows($sql);
        $this->load->library('Pdf');
        $this->load->view('transaksi/v_penerimaan_barang_detail_cetak', $data);
    }

    public function checkJmlPenerimaan()
    {
        $IDTransBeli = $this->input->get('IDTransBeli');
        $KodeBarang = $this->input->get('KodeBarang');
        $JumlahDiterima = (int)$this->input->get('JumlahDiterima');
        $JumlahLama = (int)$this->input->get('JumlahLama');
        $jml_pesan = (int)$this->input->get('Qty');
        $barangdatang = $this->lokasi->get_barang_datang($IDTransBeli, $KodeBarang);
        $jml_datang = isset($barangdatang['jml_datang']) ? (int)$barangdatang['jml_datang'] : 0;

        if (($jml_datang + $JumlahDiterima - $JumlahLama) > $jml_pesan) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Jumlah penerimaan tidak boleh melebihi jumlah pesanan']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Jumlah penerimaan cocok']);
        }
    }

    public function simpandetail()
    {
        ## POST DATA
        $NoRefTrSistem          = $this->input->post('IDTransBeli');
        $updatedata['Qty']      = $this->input->post('JumlahDiterima');
        $updatedata['Total']    = $this->input->post('HargaSatuan') * $updatedata['Qty'];

        ## Perhitungan HPP
        $kodebarang = $this->input->post('KodeBarang');
        $getdtbarang = $this->crud->get_one_row([
            'select' => 'NilaiHPP',
            'from' => 'mstbarang',
            'where' => [['KodeBarang' => $kodebarang]],
        ]);

        $stok       = $this->lokasi->get_stok_asli($kodebarang);
        $stoksistem = isset($stok['stok']) ? (int)$stok['stok'] : 0;
        $hpp        = $this->lokasi->get_hpp_sistem($kodebarang);
        $hppsistem  = isset($hpp) ? $hpp : 0;
        $stokdatang = (int)$this->input->post('JumlahDiterima'); // pengganti jumlah beli ygy
        $totalbarang = (int)$this->input->post('TotalBarang');
        $diskonbawah = (int)$this->input->post('DiskonBawah');
        $ppn = (int)$this->input->post('PPN');
        $hargasatuan  = (int)$this->input->post('HargaSatuan');

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

        $updatedatabarang = $this->crud->update($dtbrg, ['KodeBarang' => $kodebarang], 'mstbarang');

        $res = $this->crud->update($updatedata, ['NoTrans' => $this->input->post('NoTrans'), 'NoUrut' => $this->input->post('NoUrut')], 'itemtransaksibarang');

        $barangpesan = $this->db->from('itempembelian')
        ->where('IDTransBeli', $NoRefTrSistem)
        ->select_sum('Qty')
        ->get()
        ->row();
        $jml_pesan = $barangpesan->Qty;
        $barangdatang = $this->lokasi->get_tr_barang_datang($NoRefTrSistem);
        $jml_datang = $barangdatang['jml_datang'];

        if ($updatedata['Qty'] > 0) {
            if ($jml_datang < $jml_pesan) {
                $updatestatuskirim = $this->crud->update(['StatusKirim' => 'SEBAGIAN'], ['IDTransBeli' => $NoRefTrSistem], 'transpembelian');
            } else {
                $updatestatuskirim = $this->crud->update(['StatusKirim' => 'TERKIRIM'], ['IDTransBeli' => $NoRefTrSistem], 'transpembelian');
            }
        }

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

    public function penjurnalan()
    {
        $notrans = $this->input->get('NoTrans');
        $dtitembarang = $this->crud->get_rows([
            'select' => '*',
            'from' => 'itemtransaksibarang',
            'where' => [['NoTrans' => $notrans]]
        ]);

        $nominaltr = 0;
        foreach ($dtitembarang as $key) {
            if ($key['Qty'] > 0) {
                $nominaltr += $key['Total'];
            }
        }

        $cekjurnal = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transjurnal',
            'where' => [['NoRefTrans' => $notrans]]
        ]);

        $status_jurnal = $this->lokasi->setting_jurnal_status();

        $getakun = $this->crud->get_rows([
            'select' => 's.KodeSetAkun, d.NoUrut, d.JenisJurnal, d.KodeAkun, a.NamaAkun, s.NamaTransaksi, s.JenisTransaksi',
            'from' => 'setakunjurnal s',
            'join' => [
                [
                    'table' => 'detailsetakun d',
                    'on' => "d.KodeSetAkun = s.KodeSetAkun",
                    'param' => 'LEFT',
                ],
                [
                    'table' => 'mstakun a',
                    'on' => "a.KodeAkun = d.KodeAkun",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [[
                's.NamaTransaksi' => 'Penerimaan Barang',
                's.JenisTransaksi' => 'Tunai',
            ]],
        ]);

        if (!$cekjurnal) {
            $prefix2 = "JRN-" . date("Ym");
            $insertjurnal['IDTransJurnal']      = $this->crud->get_kode([
                'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                'from' => 'transjurnal',
                'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                'limit' => 1,
                'order_by' => 'IDTransJurnal DESC',
                'prefix' => $prefix2
            ]);
            $insertjurnal['KodeTahun']          = $this->akses->get_tahun_aktif();
            $insertjurnal['TglTransJurnal']     = date('Y-m-d H:i');
            $insertjurnal['TipeJurnal']         = "UMUM";
            $insertjurnal['NarasiJurnal']       = "Transaksi Penerimaan Barang";
            $insertjurnal['NominalTransaksi']   = $nominaltr;
            $insertjurnal['NoRefTrans']         = $notrans;
            $insertjurnal['UserName']           = $this->session->userdata('UserName');
            $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');

            if ($status_jurnal == 'on') {
                if ($getakun) {
                    foreach ($getakun as $key) {
                        $list_item = [
                            'NoUrut' => $key['NoUrut'],
                            'IDTransJurnal' => $insertjurnal['IDTransJurnal'],
                            'KodeTahun' => $insertjurnal['KodeTahun'],
                            'KodeAkun' => $key['KodeAkun'],
                            'NamaAkun' => $key['NamaAkun'],
                            'Debet' => ($key['JenisJurnal'] == 'Debet') ? $insertjurnal['NominalTransaksi'] : 0,
                            'Kredit' => ($key['JenisJurnal'] == 'Kredit') ? $insertjurnal['NominalTransaksi'] : 0,
                            'Uraian' => "Penjurnalan otomatis untuk Transaksi Penerimaan Barang"
                        ];

                        $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                    }
                }
            }
        }

        echo json_encode([
            'status' => true,
            'msg'  => ("Berhasil Menjurnalkan Transaksi Penerimaan Barang"),
            'idjurnal' => ($cekjurnal != null) ? $cekjurnal['IDTransJurnal'] : $insertjurnal['IDTransJurnal'],
            'stj' => $status_jurnal
        ]);
    }
}
