<?php
defined('BASEPATH') or exit('No direct script access allowed');

class approval_pembelian extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[12]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[12]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'approvebeli';
            $data['title'] = 'Approval Transaksi Pembelian (PO)';
            $data['view'] = 'transaksi/v_approval_pembelian';
            $data['scripts'] = 'transaksi/s_approval_pembelian';

            $supplier = [
                'select' => '*',
                'from' => 'mstperson',
                'where' => [
                    [
                        'IsAktif' => 1,
                        'JenisPerson' => 'SUPPLIER'
                    ]
                ],
                'order_by' => 'NamaPersonCP'
            ];
            $data['supplier'] = $this->crud->get_rows($supplier);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transpembelian b';

            $status   = $this->input->get('statusapv');
            if ($status != '') {
                $configData['where'] = [
                    [
                        'b.StatusProses' => $status,
                        'b.NoPO !=' => null,
                        'b.IsVoid' => 0,
                    ]
                ];
            } else {
                $configData['where'] = [
                    [
                        'b.StatusProses !=' => null,
                        'b.NoPO !=' => null,
                        'b.IsVoid' => 0,
                    ]
                ];
            }

            $cari     = $this->input->get('cariapv');
            if ($cari != '') {
                $configData['filters'][] = " (b.IDTransBeli LIKE '%$cari%' OR b.NoPO LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%' OR b.StatusProses LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tglapv'));
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
                'b.IDTransBeli', 'b.NoPO', 'b.TglPO', 'b.UserPO', 'b.ApprovedNo', 'b.ApprovedDate', 'b.ApprovedBy', 'b.ApprovedDesc', 'b.TotalNilaiBarang', 'b.PPN', 'PPh', 'b.TotalTagihan', 'b.StatusProses', 'b.StatusKirim', 'b.StatusBayar', 'b.KodePerson', 'p.NamaPersonCP'
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
                'b.IDTransBeli', 'b.NoPO', 'b.TglPO', 'b.UserPO', 'b.ApprovedNo', 'b.ApprovedDate', 'b.ApprovedBy', 'b.ApprovedDesc', 'b.TotalNilaiBarang', 'b.PPN', 'PPh', 'b.TotalTagihan', 'b.StatusProses', 'b.StatusKirim', 'b.StatusBayar', 'b.KodePerson', 'p.NamaPersonCP',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            foreach ($records as $record) {
                $TotalTagihan = $record->TotalTagihan > 0 ? $record->TotalTagihan : 0;
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['TotalTagihan'] = $TotalTagihan;
                $temp['TglPO'] = shortdate_indo(date('Y-m-d', strtotime($temp['TglPO']))) . ' ' . date('H:i', strtotime($temp['TglPO']));
                $temp['ApprovedDate'] = isset($temp['ApprovedDate']) ? shortdate_indo(date('Y-m-d', strtotime($temp['ApprovedDate']))) . ' ' . date('H:i', strtotime($temp['ApprovedDate'])) : '';
                $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/approval_pembelian/detail/' . base64_encode($temp['IDTransBeli'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[12]);
        $idtransbeli   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_beli';
            $data['title'] = 'Detail Approval Transaksi Pembelian (PO)';
            $data['view'] = 'transaksi/v_approval_pembelian_detail';
            $data['scripts'] = 'transaksi/s_approval_pembelian_detail';

            $dtbarang = [
                'select' => '*',
                'from' => 'mstbarang',
                'where' => [['IsAktif' => 1]]
            ];
            $data['dtbarang'] = $this->crud->get_rows($dtbarang);

            $dtinduk = [
                'select' => 't.IDTransBeli, t.UserPO, t.TglPO, t.ApprovedNo, t.ApprovedDate, t.ApprovedBy, t.ApprovedDesc, t.StatusProses, t.TotalTagihan, p.KodePerson, p.NamaPersonCP, p.NamaUsaha, jr.IDTransJurnal, jr.NominalTransaksi',
                'from' => 'transpembelian t',
                'join' => [
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = t.KodePerson",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' transjurnal jr',
                        'on' => "jr.NoRefTrans = t.IDTransBeli",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['t.IDTransBeli' => $idtransbeli]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['IDTransBeli'] = $idtransbeli;

            $data['idtransjurnal'] = isset($data['dtinduk']['IDTransJurnal']) ? $data['dtinduk']['IDTransJurnal'] : '';
            $itemjurnal = $this->lokasi->get_total_item_jurnal($data['idtransjurnal']);
            $data['nominaltransaksi']   = isset($data['dtinduk']['NominalTransaksi']) ? (int)$data['dtinduk']['NominalTransaksi'] : 0;
            $data['totaljurnaldebet']   = (int)$itemjurnal['Debet'];
            $data['totaljurnalkredit']  = (int)$itemjurnal['Kredit'];

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
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.IDTransBeli', 'i.NoUrut', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.IDTransBeli', 'i.NoUrut', 'i.Spesifikasi', 'i.HargaSatuan', 'i.Qty', 'i.Total', 'i.SatuanBarang', 'i.Deskripsi', 'i.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang as satuanAsal', 'br.Spesifikasi as spesifikasiAsal', 'br.HargaBeliTerakhir',
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
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetakdetail()
    {
        $idtransbeli   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('transaksi/approval_pembelian/detail/') . $this->uri->segment(4);
        $data['title'] = 'Detail Approval Transaksi Pembelian (PO)';

        $dtinduk = [
            'select' => 't.IDTransBeli, t.TglPO, t.ApprovedNo, t.ApprovedDate, t.ApprovedBy, t.ApprovedDesc, p.KodePerson, p.NamaPersonCP, p.NamaUsaha',
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
        $data['ApprovedDate'] = isset($data['dtinduk']['ApprovedDate']) ? shortdate_indo(date('Y-m-d', strtotime($data['dtinduk']['ApprovedDate']))) . ' ' . date('H:i', strtotime($data['dtinduk']['ApprovedDate'])) : '';

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
        // $this->load->view('transaksi/v_approval_pembelian_detail_cetak', $data);
    }

    public function approve()
    {
        $insertdata = $this->input->post();
        unset($insertdata['IDTransBeli']);
        unset($insertdata['TotalTagihan']);

        $tahun = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'msttahunanggaran',
                'where' => [['IsAktif' => 1]],
            ]
        );

        ## POST DATA
        $prefix = "APR-" . date("Ym");
        $insertdata['ApprovedNo'] = $this->crud->get_kode([
            'select' => 'RIGHT(ApprovedNo, 7) AS KODE',
            'where' => [['LEFT(ApprovedNo, 10) =' => $prefix]],
            'limit' => 1,
            'order_by' => 'ApprovedNo DESC',
            'prefix' => $prefix
        ]);
        $insertdata['ApprovedBy']   = $this->session->userdata('ActualName');
        if ($this->input->post('StatusProses') == 'APPROVED') {
            unset($insertdata['StatusProses']);
            $insertdata['StatusProses']         = 'DONE'; // ketika di approve langsung masuk ke transaksi pembelian
            $insertdata['StatusKirim']          = 'BELUM';
            $insertdata['StatusBayar']          = 'BELUM';
            $insertdata['IsDijurnalkan']        = 0;
            $insertdata['PPN']                  = 0;
            $insertdata['DiskonBawah']          = 0;
            $insertdata['NominalBelumPajak']    = 0;

            ## Update HPP saat beli
            $items = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'itempembelian',
                    'where' => [['IDTransBeli' => $this->input->post('IDTransBeli')]]
                ]
            );
            foreach ($items as $item) {
                $barang = $this->crud->get_one_row([
                    'select' => '*',
                    'from' => 'mstbarang',
                    'where' => [['KodeBarang' => $item['KodeBarang']]],
                ]);

                $hppbeli = $barang['NilaiHPP'];
                $updatehppbeli[] = $this->crud->update(
                    [
                        'HPPSaatBeli' => $hppbeli
                    ],
                    [
                        'IDTransBeli' => $this->input->post('IDTransBeli'),
                        'NoUrut'      => $item['NoUrut'],
                    ],
                    'itempembelian'
                );
            }

            ## Penjurnalan
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
                    // 's.NamaTransaksi' => 'PO',
                    // 's.JenisTransaksi' => 'Tunai',
                    's.NamaTransaksi' => 'Pembelian',
                    's.JenisTransaksi' => 'Kredit',
                ]],
            ]);
            $insertdata['IsDijurnalkan'] = 1;

            $prefix2 = "JRN-" . date("Ym");
            $insertjurnal['IDTransJurnal'] = $this->crud->get_kode([
                'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                'from' => 'transjurnal',
                'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                'limit' => 1,
                'order_by' => 'IDTransJurnal DESC',
                'prefix' => $prefix2
            ]);
            $insertjurnal['KodeTahun'] = $tahun['KodeTahun'];
            $insertjurnal['TglTransJurnal'] = $insertdata['ApprovedDate'];
            $insertjurnal['TipeJurnal'] = "UMUM";
            $insertjurnal['NarasiJurnal'] = "Transaksi Pembelian Kredit"; // "Cetak Pembelian (PO)";
            $insertjurnal['NominalTransaksi'] = $this->input->post('TotalTagihan');
            $insertjurnal['NoRefTrans'] = $this->input->post('IDTransBeli');
            $insertjurnal['UserName'] = $this->session->userdata['UserName'];
            
            $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');

            if ($status_jurnal == 'on') {
                // jika status jurnal otomatis
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
                            'Uraian' => "Penjurnalan otomatis untuk Transaksi Pembelian Kredit" // "Penjurnalan otomatis untuk Cetak Pembelian (PO)"
                        ];

                        $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                    }
                }
            } else {
                // jika status jurnal manual
            }
        }

        $res = $this->crud->update($insertdata, ['IDTransBeli' => $this->input->post('IDTransBeli')], 'transpembelian');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'tambah',
                'JenisTransaksi' => 'Approval Transaksi Pembelian (PO)',
                'Description' => 'approval transaksi pembelian (po) ' . $this->input->post('IDTransBeli')
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ("Berhasil Mengubah Data"),
                'idjurnal' => $insertjurnal['IDTransJurnal'],
                'stj' => $status_jurnal
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ("Gagal Mengubah Data")
            ]);
        }
    }

}
