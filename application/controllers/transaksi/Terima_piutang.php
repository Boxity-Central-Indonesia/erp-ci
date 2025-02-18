<?php
defined('BASEPATH') or exit('No direct script access allowed');

class terima_piutang extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[27]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[27]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'piutang';
            $data['title'] = 'Transaksi Terima Piutang';
            $data['view'] = 'transaksi/v_terima_piutang';
            $data['scripts'] = 'transaksi/s_terima_piutang';

            $customer = [
                'select' => '*',
                'from' => 'mstperson p',
                'where' => [
                    [
                        // 'p.IsAktif' => 1,
                        'p.JenisPerson' => 'CUSTOMER',
                        'j.StatusBayar !=' => 'LUNAS',
                        'j.StatusProses' => 'DONE',
                    ]
                ],
                'join' => [
                    [
                        'table' => ' transpenjualan j',
                        'on' => "j.KodePerson = p.KodePerson",
                        'param' => 'LEFT',
                    ],
                ],
                'group_by' => 'p.KodePerson',
                'order_by' => 'p.KodePerson'
            ];
            $data['customer'] = $this->crud->get_rows($customer);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transpenjualan j';

            $configData['where'] = [['j.StatusBayar !=' => 'BELUM']];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (j.IDTransJual LIKE '%$cari%' OR j.NoRef_Manual LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%')";
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
            ];

            $configData['group_by'] = 'j.IDTransJual';

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.IDTransJual', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.NoRef_Manual', 'j.TanggalPenjualan', 'j.KodeGudang', 'g.NamaGudang', 'j.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'SUM(k.TotalTransaksi) as TotalBayar', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName'
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
                'j.IDTransJual', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.NoRef_Manual', 'j.TanggalPenjualan', 'j.KodeGudang', 'g.NamaGudang', 'j.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'SUM(k.TotalTransaksi) as TotalBayar', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 27; //FiturID di tabel serverfitur
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
                $TotalBayar = $record->TotalBayar > 0 ? $record->TotalBayar : 0;
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['TotalTagihan'] = $TotalTagihan;
                $temp['TotalBayar'] = $TotalBayar;
                $temp['SisaTagihan'] = $TotalTagihan - $TotalBayar;
                $temp['TanggalPenjualan'] = isset($temp['TanggalPenjualan']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPenjualan']))) . ' ' . date('H:i', strtotime($temp['TanggalPenjualan'])) : '';
                $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/terima_piutang/detail/' . base64_encode($temp['IDTransJual'])) . '" type="button" title="Detail Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
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
            'from' => 'transaksikas',
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
        $tahun = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'msttahunanggaran',
                'where' => [['IsAktif' => 1]],
            ]
        );
        $dtpenjualan = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'transpenjualan j',
                'where' => [
                    [
                        'j.KodePerson' => $this->input->post('KodePerson'),
                        'j.StatusProses' => 'DONE',
                        'j.StatusBayar !=' => 'LUNAS',
                    ]
                ],
                'order_by' => 'j.IDTransJual',
            ]
        );

        $prefix = "TRK-" . date("Ym");
        $i = 0;
        $res = null;
        foreach ($dtpenjualan as $key) {
            $notranskas = $this->crud->get_kode([
                'select' => 'RIGHT(NoTransKas, 7) AS KODE',
                'from' => 'transaksikas',
                'where' => [['LEFT(NoTransKas, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'NoTransKas DESC',
                'prefix' => $prefix
            ]);
            // Insert transaksi kas
            $res = $this->db->insert('transaksikas', array(
                'NoTransKas'        => $notranskas,
                'KodeTahun'         => $tahun['KodeTahun'],
                'TanggalTransaksi'  => $this->input->post('TanggalTransaksi'),
                'NoRef_Sistem'      => $key['IDTransJual'],
                'NoRef_Manual'      => $this->input->post('NoRef_Manual'),
                'Uraian'            => $this->input->post('Uraian'),
                'UserName'          => $this->session->userdata('UserName'),
                'KodePerson'        => $this->input->post('KodePerson'),
                'NominalBelumPajak' => $key['NominalBelumPajak'],
                'PPN'               => $key['PPN'],
                'PPh'               => $key['PPh'],
                'TotalTransaksi'    => 0,
                'IsDijurnalkan'     => 0,
                'JenisTransaksiKas' => 'TERIMA PIUTANG',
                'Diskon'            => 0,
            ));

            $i++;
        }

        if ($res) {
            echo json_encode([
                'status' => true,
                'msg'  => ("Berhasil Menambah Data"),
                'id' => $this->input->post('KodePerson'),
                'id2' => $this->input->post('NoRef_Manual')
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ("Gagal Menambah Data")
            ]);
        }
    }

    public function tambah()
    {
        checkAccess($this->session->userdata('fiturview')[27]);
        $kodeperson   = escape(base64_decode($this->uri->segment(4)));
        $kodemanual   = escape(base64_decode($this->uri->segment(5)));

        ## AMBIL DATA PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_jual';
            $data['title'] = 'Detail Transaksi Terima Piutang';
            $data['view'] = 'transaksi/v_terima_piutang_tambah';
            $data['scripts'] = 'transaksi/s_terima_piutang_tambah';

            $dtinduk = [
                'select' => 'k.NoTransKas, k.NoRef_Manual, k.TanggalTransaksi, k.Uraian, k.KodePerson, p.NamaPersonCP',
                'from' => 'transaksikas k',
                'join' => [
                    [
                        'table' => ' mstperson p',
                        'on' => "p.KodePerson = k.KodePerson",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [
                    [
                        'k.KodePerson' => $kodeperson,
                        'k.NoRef_Manual' => $kodemanual,
                        'k.JenisTransaksiKas' => 'TERIMA PIUTANG',
                    ]
                ],
                'group_by' => 'k.NoRef_Manual',
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['KodePerson'] = $kodeperson;
            $data['NoRef_Manual'] = $kodemanual;

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $kodeperson   = $this->input->get('kodeperson');
            $noref_manual   = $this->input->get('noref_manual');
            $configData['table'] = 'transpenjualan j';
            $configData['where'] = [
                [
                    'j.KodePerson'  => $kodeperson,
                    'k.NoRef_Manual' => $noref_manual,
                ]
            ];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (j.IDTransJual LIKE '%$cari%' OR j.KodePerson LIKE '%$cari%')";
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
            ];

            $configData['group_by'] = 'j.IDTransJual';

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.IDTransJual', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.NoRef_Manual', 'j.TanggalPenjualan', 'j.KodeGudang', 'g.NamaGudang', 'j.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName', 'k.TotalTransaksi as DibayarSekarang'
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
                'j.IDTransJual', 'j.TotalTagihan', 'j.StatusProses', 'j.StatusKirim', 'j.StatusBayar', 'j.NoRef_Manual', 'j.TanggalPenjualan', 'j.KodeGudang', 'g.NamaGudang', 'j.KodePerson', 'p.NamaPersonCP', 'k.NoRef_Sistem', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NominalBelumPajak', 'k.PPN', 'k.PPh', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.Diskon', 'k.KodeTahun', 'k.UserName', 'u.ActualName', 'k.TotalTransaksi as DibayarSekarang',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 27; //FiturID di tabel serverfitur
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
                $DibayarSekarang = $record->DibayarSekarang > 0 ? $record->DibayarSekarang : 0;
                $KodePerson = $record->KodePerson;
                $NoRef_Sistem = $record->IDTransJual;
                $jumlah_bayar = $this->lokasi->get_total_dibayar($KodePerson, $NoRef_Sistem);
                if ($jumlah_bayar) {
                    $jml_bayar = $jumlah_bayar['total_bayar'];
                } else {
                    $jml_bayar = 0;
                }
                $TotalBayar = $jml_bayar;
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['TotalTagihan'] = $TotalTagihan;
                $temp['TotalBayar'] = $TotalBayar;
                $temp['SisaTagihan'] = $TotalTagihan - $TotalBayar;
                $temp['DibayarSekarang'] = $DibayarSekarang;
                $temp['TanggalPenjualan'] = isset($temp['TanggalPenjualan']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPenjualan']))) . ' ' . date('H:i', strtotime($temp['TanggalPenjualan'])) : '';
                if ($canEdit == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($temp) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpantambah()
    {
        $TotalTagihan = str_replace(['.', ','], ['', '.'], $this->input->post('TotalTagihan'));
        $DibayarSekarang = str_replace(['.', ','], ['', '.'], $this->input->post('DibayarSekarang'));
        $KodePerson = $this->input->post('KodePerson');
        $NoRef_Sistem = $this->input->post('NoRef_Sistem');

        $updatedata['TotalTransaksi'] = $DibayarSekarang;
        $updatedata['IsDijurnalkan'] = 0;

        $tahun = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'msttahunanggaran',
                'where' => [['IsAktif' => 1]],
            ]
        );

        $getakun = $this->crud->get_rows([
            'select' => 's.KodeSetAkun, d.NoUrut, d.JenisJurnal, d.KodeAkun, a.NamaAkun, s.NamaTransaksi, s.JenisTransaksi',
            'from' => 'setakunjurnal s',
            'join' => [
                [
                    'table' => 'detailsetakun d',
                    'on' => "d.KodeSetAkun = s.KodeSetAkun",
                    'param' => 'INNER',
                ],
                [
                    'table' => 'mstakun a',
                    'on' => "a.KodeAkun = d.KodeAkun",
                    'param' => 'INNER',
                ],
            ],
            'where' => [[
                's.NamaTransaksi' => 'Piutang',
                's.JenisTransaksi' => 'Tunai',
            ]],
        ]);
        if ($getakun) {
            $updatedata['IsDijurnalkan'] = 1;

            $check_existance = $this->crud->get_one_row([
                'select' => 'IDTransJurnal, NoRefTrans',
                'from' => 'transjurnal',
                'where' => [['NoRefTrans' => $this->input->post('NoTransKas')]],
            ]);

            $insertjurnal['KodeTahun'] = $tahun['KodeTahun'];
            $insertjurnal['TglTransJurnal'] = date("Y-m-d H:i");
            $insertjurnal['TipeJurnal'] = "UMUM";
            $insertjurnal['NarasiJurnal'] = "Transaksi Terima Piutang";
            $insertjurnal['NominalTransaksi'] = $DibayarSekarang;
            $insertjurnal['NoRefTrans'] = $this->input->post('NoTransKas');
            $insertjurnal['UserName'] = $this->session->userdata['UserName'];

            if ($check_existance) {
                $updatejurnalinduk = $this->crud->update($insertjurnal, ['NoRefTrans' => $this->input->post('NoTransKas')], 'transjurnal');
            } else {
                $prefix2 = "JRN-" . date("Ym");
                $insertjurnal['IDTransJurnal'] = $this->crud->get_kode([
                    'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                    'from' => 'transjurnal',
                    'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                    'limit' => 1,
                    'order_by' => 'IDTransJurnal DESC',
                    'prefix' => $prefix2
                ]);

                $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');
            }

            foreach ($getakun as $key) {
                $countakun = $this->crud->get_count([
                    'select' => 'd.NoUrut, d.KodeAkun',
                    'from' => 'detailsetakun d',
                    'join' => [[
                        'table' => ' setakunjurnal s',
                        'on' => "s.KodeSetAkun = d.KodeSetAkun",
                        'param' => 'LEFT',
                    ]],
                    'where' => [[
                        's.NamaTransaksi' => $key['NamaTransaksi'],
                        's.JenisTransaksi' => $key['JenisTransaksi'],
                        'd.JenisJurnal' => $key['JenisJurnal'],
                    ]],
                ]);

                $nilai = $DibayarSekarang / $countakun;

                $list_item = [
                    'NoUrut' => $key['NoUrut'],
                    'IDTransJurnal' => ($check_existance ? $check_existance['IDTransJurnal'] : $insertjurnal['IDTransJurnal']),
                    'KodeTahun' => $insertjurnal['KodeTahun'],
                    'KodeAkun' => $key['KodeAkun'],
                    'NamaAkun' => $key['NamaAkun'],
                    'Debet' => ($key['JenisJurnal'] == 'Debet') ? $nilai : 0,
                    'Kredit' => ($key['JenisJurnal'] == 'Kredit') ? $nilai : 0,
                    'Uraian' => "Penjurnalan otomatis untuk Transaksi Terima Piutang"
                ];

                $insertjurnalitem[] = $this->crud->insert_or_update($list_item, 'transjurnalitem');
            }

            if ($updatedata['TotalTransaksi'] == 0) {
                $updatedata['IsDijurnalkan'] = 0;

                if ($check_existance) {
                    $deleteitemjurnal = $this->crud->delete(['IDTransJurnal' => $check_existance['IDTransJurnal']], 'transjurnalitem');
                    $deletejurnalinduk = $this->crud->delete(['IDTransJurnal' => $check_existance['IDTransJurnal']], 'transjurnal');
                }
            }
        }

        $res = $this->crud->update($updatedata, ['NoTransKas' => $this->input->post('NoTransKas')], 'transaksikas');

        $jumlah_bayar = $this->lokasi->get_total_dibayar($KodePerson, $NoRef_Sistem);
        if ($jumlah_bayar['total_bayar'] < $TotalTagihan) {
            $statusbayar = $this->crud->update(['StatusBayar' => 'SEBAGIAN'], ['IDTransJual' => $NoRef_Sistem], 'transpenjualan');
        } else {
            $statusbayar = $this->crud->update(['StatusBayar' => 'LUNAS'], ['IDTransJual' => $NoRef_Sistem], 'transpenjualan');
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

    public function hapustambah()
    {
        $kode = $this->input->get('NoRef_Manual');

        $res = $this->crud->delete(['NoRef_Manual' => $kode], 'transaksikas');

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

    public function verifikasitambah()
    {
        $kode = $this->input->get('NoRef_Manual');
        $trkas = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'transaksikas',
                'where' => [['NoRef_Manual' => $kode]],
            ]
        );
        $data = [];
        $i = 0;
        foreach ($trkas as $key) {
            $data[$i]['NoTransKas'] = $key['NoTransKas'];
            $data[$i]['TotalTransaksi'] = $key['TotalTransaksi'];

            // hapus data trkas jika total dibayar sekarang 0
            if ($key['TotalTransaksi'] == 0) {
                $this->crud->delete(['NoTransKas' => $key['NoTransKas']], 'transaksikas');
            }

            $i++;
        }

        if ($data) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Menyimpan Data"
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
        checkAccess($this->session->userdata('fiturview')[27]);
        $idtransjual   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'trans_jual';
            $data['title'] = 'Detail Transaksi Terima Piutang';
            $data['view'] = 'transaksi/v_terima_piutang_detail';
            $data['scripts'] = 'transaksi/s_terima_piutang_detail';

            $dtinduk = [
                'select' => 't.IDTransJual, t.PPN, t.NoSlipOrder, t.NominalBelumPajak, t.TotalTagihan, t.StatusProses, t.StatusBayar, t.NoRef_Manual, t.TanggalPenjualan, t.KodeGudang, g.NamaGudang, p.KodePerson, p.NamaPersonCP, p.NamaUsaha',
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
                ],
                'where' => [['t.IDTransJual' => $idtransjual]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['IDTransJual'] = $idtransjual;

            $KodePerson = $data['dtinduk']['KodePerson'];
            $NoRef_Sistem = $idtransjual;
            $jumlah_bayar = $this->lokasi->get_total_dibayar($KodePerson, $NoRef_Sistem);
            if ($jumlah_bayar) {
                $jml_bayar = $jumlah_bayar['total_bayar'];
            } else {
                $jml_bayar = 0;
            }
            $data['TotalBayar'] = $jml_bayar;
            $data['SisaTagihan'] = $data['dtinduk']['TotalTagihan'] - $data['TotalBayar'];

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $idtransjual   = $this->input->get('idtransjual');
            $configData['table'] = 'transaksikas k';
            $configData['where'] = [['k.NoRef_Sistem'  => $idtransjual]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (k.NoTransKas LIKE '%$cari%' OR k.NoRef_Manual LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' transpenjualan t',
                    'on' => "t.IDTransJual = k.NoRef_Sistem",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = k.UserName",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = k.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = t.KodeGudang",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'k.KodeTahun', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NoRef_Manual', 'k.Uraian', 'k.TotalTransaksi', 'k.JenisTransaksiKas', 'k.NoRef_Sistem', 't.IDTransJual', 't.NominalBelumPajak', 't.TotalTagihan', 't.PPN', 'k.UserName', 'u.ActualName', 'k.KodePerson', 'p.NamaPersonCP', 't.KodeGudang', 'g.NamaGudang'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'k.KodeTahun', 'k.NoTransKas', 'k.TanggalTransaksi', 'k.NoRef_Manual', 'k.Uraian', 'k.TotalTransaksi', 'k.JenisTransaksiKas', 'k.NoRef_Sistem', 't.IDTransJual', 't.NominalBelumPajak', 't.TotalTagihan', 't.PPN', 'k.UserName', 'u.ActualName', 'k.KodePerson', 'p.NamaPersonCP', 't.KodeGudang', 'g.NamaGudang',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 27; //FiturID di tabel serverfitur
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
                $temp['TotalTransaksi'] = isset($temp['TotalTransaksi']) ? $temp['TotalTransaksi'] : 0;
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTransKas'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['NoTransKas'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
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

        $dtinduk = [
            'select' => 't.IDTransJual, t.PPN, t.NoSlipOrder, t.NominalBelumPajak, t.TotalTagihan, t.StatusProses, t.StatusBayar, t.NoRef_Manual, t.TanggalPenjualan, t.KodeGudang, g.NamaGudang, p.KodePerson, p.NamaPersonCP, p.NamaUsaha',
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
            ],
            'where' => [['t.IDTransJual' => $idtransjual]],
        ];
        $data['dtinduk'] = $this->crud->get_one_row($dtinduk);

        $sql = [
            'select' => 'k.KodeTahun, k.NoTransKas, k.TanggalTransaksi, k.NoRef_Manual, k.Uraian, k.TotalTransaksi, k.JenisTransaksiKas, k.NoRef_Sistem, t.IDTransJual, t.NominalBelumPajak, t.TotalTagihan, t.PPN, k.UserName, u.ActualName, k.KodePerson, p.NamaPersonCP, t.KodeGudang, g.NamaGudang',
            'from' => 'transaksikas k',
            'join' => [
                [
                    'table' => ' transpenjualan t',
                    'on' => "t.IDTransJual = k.NoRef_Sistem",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = k.UserName",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = k.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang g',
                    'on' => "g.KodeGudang = t.KodeGudang",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['k.NoRef_Sistem' => $idtransjual]],
        ];
        $data['model'] = $this->crud->get_rows($sql);

        $this->load->library('Pdf');
        $this->load->view('transaksi/v_terima_piutang_detail_cetak', $data);
    }

    public function simpandetail()
    {
        $TotalTransaksi = str_replace(['.', ','], ['', '.'], $this->input->post('TotalTransaksi'));
        $notranskas = $this->input->post('NoTransKas');

        $updatedata['TotalTransaksi'] = $TotalTransaksi;
        $updatedata['IsDijurnalkan'] = 0;

        $tahun = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'msttahunanggaran',
                'where' => [['IsAktif' => 1]],
            ]
        );

        $getakun = $this->crud->get_rows([
            'select' => 's.KodeSetAkun, d.NoUrut, d.JenisJurnal, d.KodeAkun, a.NamaAkun, s.NamaTransaksi, s.JenisTransaksi',
            'from' => 'setakunjurnal s',
            'join' => [
                [
                    'table' => 'detailsetakun d',
                    'on' => "d.KodeSetAkun = s.KodeSetAkun",
                    'param' => 'INNER',
                ],
                [
                    'table' => 'mstakun a',
                    'on' => "a.KodeAkun = d.KodeAkun",
                    'param' => 'INNER',
                ],
            ],
            'where' => [[
                's.NamaTransaksi' => 'Piutang',
                's.JenisTransaksi' => 'Tunai',
            ]],
        ]);
        if ($getakun) {
            $updatedata['IsDijurnalkan'] = 1;

            $check_existance = $this->crud->get_one_row([
                'select' => 'IDTransJurnal, NoRefTrans',
                'from' => 'transjurnal',
                'where' => [['NoRefTrans' => $notranskas]],
            ]);

            $insertjurnal['KodeTahun'] = $tahun['KodeTahun'];
            $insertjurnal['TglTransJurnal'] = $this->input->post('TanggalTransaksi');
            $insertjurnal['TipeJurnal'] = "UMUM";
            $insertjurnal['NarasiJurnal'] = "Transaksi Terima Piutang";
            $insertjurnal['NominalTransaksi'] = $TotalTransaksi;
            $insertjurnal['NoRefTrans'] = $notranskas;
            $insertjurnal['UserName'] = $this->session->userdata['UserName'];

            if ($check_existance) {
                $updatejurnalinduk = $this->crud->update($insertjurnal, ['NoRefTrans' => $notranskas], 'transjurnal');
            } else {
                $prefix2 = "JRN-" . date("Ym");
                $insertjurnal['IDTransJurnal'] = $this->crud->get_kode([
                    'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                    'from' => 'transjurnal',
                    'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                    'limit' => 1,
                    'order_by' => 'IDTransJurnal DESC',
                    'prefix' => $prefix2
                ]);

                $insertjurnalinduk = $this->crud->insert($insertjurnal, 'transjurnal');
            }

            foreach ($getakun as $key) {
                $countakun = $this->crud->get_count([
                    'select' => 'd.NoUrut, d.KodeAkun',
                    'from' => 'detailsetakun d',
                    'join' => [[
                        'table' => ' setakunjurnal s',
                        'on' => "s.KodeSetAkun = d.KodeSetAkun",
                        'param' => 'LEFT',
                    ]],
                    'where' => [[
                        's.NamaTransaksi' => $key['NamaTransaksi'],
                        's.JenisTransaksi' => $key['JenisTransaksi'],
                        'd.JenisJurnal' => $key['JenisJurnal'],
                    ]],
                ]);

                $nilai = $TotalTransaksi / $countakun;

                $list_item = [
                    'NoUrut' => $key['NoUrut'],
                    'IDTransJurnal' => ($check_existance ? $check_existance['IDTransJurnal'] : $insertjurnal['IDTransJurnal']),
                    'KodeTahun' => $insertjurnal['KodeTahun'],
                    'KodeAkun' => $key['KodeAkun'],
                    'NamaAkun' => $key['NamaAkun'],
                    'Debet' => ($key['JenisJurnal'] == 'Debet') ? $nilai : 0,
                    'Kredit' => ($key['JenisJurnal'] == 'Kredit') ? $nilai : 0,
                    'Uraian' => "Penjurnalan otomatis untuk Transaksi Terima Piutang"
                ];

                $insertjurnalitem[] = $this->crud->insert_or_update($list_item, 'transjurnalitem');
            }
        }

        $getinduk = $this->crud->get_one_row(
            [
                'select' => 'k.NoTransKas, k.KodePerson, k.NoRef_Sistem, j.IDTransJual, j.TotalTagihan',
                'from' => 'transaksikas k',
                'join' => [[
                    'table' => ' transpenjualan j',
                    'on' => 'j.IDTransJual = k.NoRef_Sistem',
                    'param' => 'LEFT',
                ]],
                'where' => [['k.NoTransKas' => $notranskas]],
            ]
        );

        $res = $this->crud->update($updatedata, ['NoTransKas' => $notranskas], 'transaksikas');

        $jumlah_bayar = $this->lokasi->get_total_dibayar($getinduk['KodePerson'], $getinduk['NoRef_Sistem']);

        // update status bayar di tabel transaksi pembelian
        if (!$jumlah_bayar) {
            $update = $this->crud->update(['StatusBayar' => 'BELUM'], ['IDTransJual' => $getinduk['NoRef_Sistem']], 'transpenjualan');
        } else {
            if ($jumlah_bayar['total_bayar'] < $getinduk['TotalTagihan']) {
                $update = $this->crud->update(['StatusBayar' => 'SEBAGIAN'], ['IDTransJual' => $getinduk['NoRef_Sistem']], 'transpenjualan');
            } else {
                $update = $this->crud->update(['StatusBayar' => 'LUNAS'], ['IDTransJual' => $getinduk['NoRef_Sistem']], 'transpenjualan');
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

    public function hapusdetail()
    {
        $kode  = $this->input->get('NoTransKas');

        $getjurnal = $this->crud->get_one_row([
            'select' => 'IDTransJurnal, NoRefTrans',
            'from' => 'transjurnal',
            'where' => [['NoRefTrans' => $kode]],
        ]);

        if ($getjurnal) {
            $deleteitemjurnal = $this->crud->delete(['IDTransJurnal' => $getjurnal['IDTransJurnal']], 'transjurnalitem');
            $deletejurnalinduk = $this->crud->delete(['IDTransJurnal' => $getjurnal['IDTransJurnal']], 'transjurnal');
        }

        $getinduk = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'transaksikas',
                'where' => [['NoTransKas' => $kode]],
            ]
        );

        $res = $this->crud->delete(['NoTransKas' => $kode], 'transaksikas');

        $jumlah_bayar = $this->lokasi->get_total_dibayar($getinduk['KodePerson'], $getinduk['NoRef_Sistem']);

        // update status bayar di tabel transaksi pembelian
        if (!$jumlah_bayar) {
            $update = $this->crud->update(['StatusBayar' => 'BELUM'], ['IDTransJual' => $getinduk['NoRef_Sistem']], 'transpenjualan');
        } else {
            $update = $this->crud->update(['StatusBayar' => 'SEBAGIAN'], ['IDTransJual' => $getinduk['NoRef_Sistem']], 'transpenjualan');
        }

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
