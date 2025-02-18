<?php
defined('BASEPATH') or exit('No direct script access allowed');

class penyesuaian_stok extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[21]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[21]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'penyesuaianstok';
            $data['title'] = 'Penyesuaian Stok';
            $data['view'] = 'transaksi/v_penyesuaian_stok';
            $data['scripts'] = 'transaksi/s_penyesuaian_stok';

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
                    'b.JenisTransaksi' => 'PENYESUAIAN STOK',
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
                    'table' => ' userlogin u',
                    'on' => "u.UserName = b.UserName",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' itemtransaksibarang ib',
                    'on' => "ib.NoTrans = b.NoTrans",
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
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.UserName', 'u.ActualName', 'b.GudangAsal', 'ga.NamaGudang as NamaGudangAsal', 'gt.NamaGudang as NamaGudangTujuan'
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
                'b.NoTrans', 'b.TanggalTransaksi', 'b.Deskripsi', 'b.JenisTransaksi', 'b.NoRefTrManual', 'b.ProdTglSelesai', 'b.ProdUkuran', 'b.ProdJmlDaun', 'b.UserName', 'u.ActualName', 'b.GudangAsal', 'ga.NamaGudang as NamaGudangAsal', 'gt.NamaGudang as NamaGudangTujuan',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 21; //FiturID di tabel serverfitur
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
                $temp['TanggalTransaksi'] = shortdate_indo(date('Y-m-d', strtotime($temp['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($temp['TanggalTransaksi']));
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/penyesuaian_stok/detail/' . base64_encode($temp['NoTrans'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a class="btnedit" type="button" href="#" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/penyesuaian_stok/detail/' . base64_encode($temp['NoTrans'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a class="btnedit" type="button" href="#" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/penyesuaian_stok/detail/' . base64_encode($temp['NoTrans'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' .
                    '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/penyesuaian_stok/detail/' . base64_encode($temp['NoTrans'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
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
        unset($insertdata['Gudang']);
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
            $insertdata['UserName']         = $this->session->userdata('UserName');
            $insertdata['JenisTransaksi']   = 'PENYESUAIAN STOK';
            $insertdata['GudangAsal']       = $this->input->post('Gudang');
            $insertdata['GudangTujuan']     = $this->input->post('Gudang');
            $insertdata['IsHapus']          = 0;
            $isEdit = false;
            $aksi = 'tambah';
        } else {
            $isEdit = true;
            $aksi = 'edit';
        }
        $res = $this->crud->insert_or_update($insertdata, 'transaksibarang');

        if ($res) {
            ## INSERT TO SERVER LOG
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('NoTrans') : $insertdata['NoTrans'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Penyesuaian Stok',
                'Description' => $ket . ' data penyesuaian sotk ' . $id
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data"),
                'id' => $insertdata['NoTrans'],
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

        ## Menghapus data di tabel item transaksi barang
        $countitem = $this->crud->get_count(
            [
                'select' => 'NoUrut',
                'from' => 'itemtransaksibarang',
                'where' => [
                    [
                        'NoTrans' => $kode,
                        'IsHapus' => 0,
                    ]
                ],
            ]
        );
        if ($countitem > 0) {
            // $item = $this->crud->update(['IsHapus' => 1], ['NoTrans' => $kode], 'itemtransaksibarang');
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menghapus data transaksi, silahkan hapus item detail terlebih dahulu"
            ]);
        } else {
            $res = $this->crud->update(['IsHapus' => 1], ['NoTrans' => $kode], 'transaksibarang');
            if ($res) {
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => 'hapus',
                    'JenisTransaksi' => 'Penyesuaian Stok',
                    'Description' => 'hapus data penyesuaian stok ' . $kode
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
        checkAccess($this->session->userdata('fiturview')[21]);
        $notrans        = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'penyesuaianstok';
            $data['title'] = 'Detail Penyesuaian Stok';
            $data['view'] = 'transaksi/v_penyesuaian_stok_detail';
            $data['scripts'] = 'transaksi/s_penyesuaian_stok_detail';

            $dtinduk = [
                'select' => 'b.NoTrans, b.NoRefTrManual, b.TanggalTransaksi, b.Deskripsi, b.GudangAsal, ga.NamaGudang as NamaGudangAsal, b.GudangTujuan, gt.NamaGudang as NamaGudangTujuan, u.ActualName',
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
                        'table' => ' userlogin u',
                        'on' => "u.UserName = b.UserName",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['b.NoTrans' => $notrans]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['NoTrans'] = $notrans;

            $dtbarang = [
                'select' => '*',
                'from' => 'mstbarang br',
                'where' => [['br.IsAktif' => 1]],
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
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.NoUrut', 'i.NoTrans', 'i.Qty', 'i.HargaSatuan', 'i.Total', 'i.Deskripsi', 'i.JenisStok', 'i.GudangAsal', 'i.GudangTujuan', 'i.SatuanBarang', 'i.IsHapus', 'i.KodeBarang', 'br.NamaBarang', 'br.KodeManual', 'i.StokSistemPenyesuaian', 'i.StokFisikPenyesuaian'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.NoUrut', 'i.NoTrans', 'i.Qty', 'i.HargaSatuan', 'i.Total', 'i.Deskripsi', 'i.JenisStok', 'i.GudangAsal', 'i.GudangTujuan', 'i.SatuanBarang', 'i.IsHapus', 'i.KodeBarang', 'br.NamaBarang', 'i.StokSistemPenyesuaian', 'i.StokFisikPenyesuaian',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 21; //FiturID di tabel serverfitur
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
                if ($canDelete == 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['NoTrans'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;&nbsp;<a class="btnedit" hidden href="#" type="button" data-model=\'' . json_encode($temp) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                }  else {
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
        $data['src_url'] = base_url('transaksi/penyesuaian_stok/detail/') . $this->uri->segment(4);

        $dtinduk = [
            'select' => 'b.NoTrans, b.NoRefTrManual, b.TanggalTransaksi, b.Deskripsi, b.GudangAsal, ga.NamaGudang as NamaGudangAsal, b.GudangTujuan, gt.NamaGudang as NamaGudangTujuan',
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
            ],
            'where' => [['b.NoTrans' => $notrans]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
        $data['NoTrans'] = $notrans;

        $sql = [
            'select' => 'i.NoUrut, i.NoTrans, i.Qty, i.HargaSatuan, i.Total, i.Deskripsi, i.JenisStok, i.GudangAsal, i.GudangTujuan, i.SatuanBarang, i.IsHapus, i.KodeBarang, br.NamaBarang, i.StokSistemPenyesuaian, i.StokFisikPenyesuaian',
            'from' => 'itemtransaksibarang i',
            'join' => [
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
            ],
            'where' => [['i.NoTrans' => $notrans]],
        ];
        $data['model'] = $this->crud->get_rows($sql);

        $this->load->library('Pdf');
        $this->load->view('transaksi/v_penyesuaian_stok_detail_cetak', $data);
    }

    public function checkStock()
    {
        $StokAsal = $this->input->get('StokAsal');
        $StokFisik = $this->input->get('StokFisik');
        if ($StokFisik == $StokAsal) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Jumlah stok fisik tidak boleh sama dengan jumlah stok sistem']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Jumlah stok cocok']);
        }
    }

    public function simpandetail()
    {
        $insertdata = $this->input->post();
        $StokSistem = $this->input->post('StokSistemPenyesuaian');
        $StokFisik  = $this->input->post('StokFisikPenyesuaian');
        $isEdit = true;

        // Mengambil data barang dari tabel master barang
        $dtbarang = $this->crud->get_one_row(
            [
                'select'=> '*',
                'from'  => 'mstbarang b',
                'join' => [[
                    'table' => 'mstjenisbarang j',
                    'on' => "b.KodeJenis = j.KodeJenis",
                    'param' => 'LEFT'
                ]],
                'where' => [['KodeBarang' => $this->input->post('KodeBarang')]],
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
            if ($StokSistem > $StokFisik) {
                $insertdata['Qty']          = $StokSistem - $StokFisik;
                $insertdata['JenisStok']    = 'KELUAR';
                $insertdata['GudangAsal']   = $induk['GudangAsal'];
            } else {
                $insertdata['Qty']          = $StokFisik - $StokSistem;
                $insertdata['JenisStok']    = 'MASUK';
                $insertdata['GudangTujuan'] = $induk['GudangTujuan'];
            }
            $insertdata['HargaSatuan']  = $dtbarang['NamaJenisBarang'] == 'BARANG JADI' ? $dtbarang['HargaJual'] : $dtbarang['HargaBeliTerakhir'];
            $insertdata['Total']        = $insertdata['HargaSatuan'] * $insertdata['Qty'];
            $insertdata['SatuanBarang'] = $dtbarang['SatuanBarang'];
            $insertdata['IsHapus']      = 0;

            $res = $this->crud->insert($insertdata, 'itemtransaksibarang');
        } else {
            $isEdit = true;

            // $res = $this->crud->update($updatedata, ['NoTrans' => $this->input->post('NoTrans'), 'NoUrut' => $this->input->post('NoUrut')], 'itemtransaksibarang');
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

