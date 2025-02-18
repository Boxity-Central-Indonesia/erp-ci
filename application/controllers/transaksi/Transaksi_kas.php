<?php
defined('BASEPATH') or exit('No direct script access allowed');

class transaksi_kas extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transaksikas k';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[47]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[47]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'transkas';
            $data['title'] = 'Transaksi Biaya';
            $data['view'] = 'transaksi/v_transaksi_kas';
            $data['scripts'] = 'transaksi/s_transaksi_kas';

            $sql1 = "SELECT SUM(if(k.JenisTransaksiKas = 'KAS MASUK', k.TotalTransaksi, 0)) - SUM(if(k.JenisTransaksiKas = 'KAS KELUAR', k.TotalTransaksi, 0)) AS Total
                FROM transaksikas k
                WHERE k.IDRekap IS NULL
                AND k.KodePegawai IS NULL
                AND k.Status = 'PAID'
                AND MONTH(k.TanggalTransaksi) = MONTH(CURRENT_DATE())
                AND YEAR(k.TanggalTransaksi) = YEAR(CURRENT_DATE())";
            $thismonth = $this->db->query($sql1)->row_array()['Total'];
            $data['thismonth'] = isset($thismonth) ? $thismonth : 0;

            $sql2 = "SELECT SUM(if(k.JenisTransaksiKas = 'KAS MASUK', k.TotalTransaksi, 0)) - SUM(if(k.JenisTransaksiKas = 'KAS KELUAR', k.TotalTransaksi, 0)) AS Total30
                FROM transaksikas k
                WHERE k.IDRekap IS NULL
                AND k.KodePegawai IS NULL
                AND k.Status = 'PAID'
                AND DATE(k.TanggalTransaksi) BETWEEN CURRENT_DATE() - INTERVAL 30 DAY AND CURDATE()";
            $last30 = $this->db->query($sql2)->row_array()['Total30'];
            $data['last30'] = isset($last30) ? $last30 : 0;

            $sql3 = "SELECT SUM(if(k.JenisTransaksiKas = 'KAS MASUK', k.TotalTransaksi, 0)) - SUM(if(k.JenisTransaksiKas = 'KAS KELUAR', k.TotalTransaksi, 0)) AS BelumBayar
                FROM transaksikas k
                WHERE k.IDRekap IS NULL
                AND k.KodePegawai IS NULL
                AND k.Status != 'PAID'";
            $belumbayar = $this->db->query($sql3)->row_array()['BelumBayar'];
            $data['belumbayar'] = isset($belumbayar) ? $belumbayar : 0;

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transaksikas k';

            $configData['where'] = [
                [
                    // 'k.NoRef_Sistem' => null,
                    'k.KodePerson' => null,
                    'k.KodePegawai' => null,
                    'k.IDRekap' => null,
                ],
                " (k.JenisTransaksiKas = 'KAS MASUK' OR k.JenisTransaksiKas = 'KAS KELUAR')",
                " (LEFT(k.NoRef_Sistem, 3) = 'PJM' OR k.NoRef_Sistem IS NULL)"
            ];

            $jenis   = $this->input->get('jenis');
            if ($jenis != '') {
                $configData['filters'][] = " (k.JenisTransaksiKas = '$jenis')";
            }

            $status   = $this->input->get('status');
            if ($status != '') {
                $configData['filters'][] = " (k.Status = '$status')";
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (k.NoTransKas LIKE '%$cari%' OR k.NoRef_Manual LIKE '%$cari%' OR k.Uraian LIKE '%$cari%' OR k.KodeTahun LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(k.TanggalTransaksi) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = k.UserName",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' msttahunanggaran ta',
                    'on' => "ta.KodeTahun = k.KodeTahun",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'k.NoTransKas', 'k.TanggalTransaksi', 'k.NoRef_Sistem', 'k.NoRef_Manual', 'k.TotalTransaksi', 'k.JenisTransaksiKas', 'k.Uraian', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.KodeTahun', 'k.TanggalJatuhTempo', 'k.Status', 'k.UserName', 'u.ActualName'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'k.TanggalTransaksi';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'k.NoTransKas', 'k.TanggalTransaksi', 'k.NoRef_Sistem', 'k.NoRef_Manual', 'k.TotalTransaksi', 'k.JenisTransaksiKas', 'k.Uraian', 'k.IsDijurnalkan', 'k.NoTransJurnal', 'k.TipeJurnal', 'k.NarasiJurnal', 'k.KodeTahun', 'k.TanggalJatuhTempo', 'k.Status', 'k.UserName', 'u.ActualName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 47; //FiturID di tabel serverfitur
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
                $temp['TanggalTransaksi'] = isset($temp['TanggalTransaksi']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalTransaksi']))) . ' ' . date('H:i', strtotime($temp['TanggalTransaksi'])) : '';
                $temp['TanggalJatuhTempo'] = isset($temp['TanggalJatuhTempo']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalJatuhTempo']))) : '-';
                if ($temp['NoRef_Sistem'] == null) {
                    if ($canEdit == 1 && $canDelete == 1) {
                        if ($temp['Status'] == 'PAID') {
                            $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_kas/jurnal/' . base64_encode($temp['NoTransKas'])) . '" type="button" title="Jurnalkan Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTransKas'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                        } else {
                            $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTransKas'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                        }
                    } elseif ($canEdit == 1 && $canDelete == 0) {
                        if ($temp['Status'] == 'PAID') {
                            $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_kas/jurnal/' . base64_encode($temp['NoTransKas'])) . '" type="button" title="Jurnalkan Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                        } else {
                            $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                        }
                    } elseif ($canDelete == 1 && $canEdit == 0) {
                        if ($temp['Status'] == 'PAID') {
                            $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_kas/jurnal/' . base64_encode($temp['NoTransKas'])) . '" type="button" title="Jurnalkan Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTransKas'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                        } else {
                            $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['NoTransKas'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                        }
                    } else {
                        if ($temp['Status'] == 'PAID') {
                            $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_kas/jurnal/' . base64_encode($temp['NoTransKas'])) . '" type="button" title="Jurnalkan Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
                        } else {
                            $temp['btn_aksi'] = '';
                        }
                    }
                } else {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('transaksi/transaksi_kas/jurnal/' . base64_encode($temp['NoTransKas'])) . '" type="button" title="Jurnalkan Transaksi"><span class="fa fa-list" aria-hidden="true"></span></a>';
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
        $Manual_Lama = $this->input->get('Manual_Lama');
        if ($Manual_Lama != null && $Manual_Lama != '') {
            $count =  $this->crud->get_count([
                'select' => '*',
                'from' => 'transaksikas',
                'where' => [
                    [
                        'NoRef_Manual' => $NoRef_Manual,
                        'NoRef_Manual !=' => $Manual_Lama,
                    ]
                ]
            ]);
        } else {
            $count =  $this->crud->get_count([
                'select' => '*',
                'from' => 'transaksikas',
                'where' => [['NoRef_Manual' => $NoRef_Manual]]
            ]);
        }
        if ($count > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'No Referensi telah digunakan']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'No Referensi tersedia']);
        }
    }

    public function simpan()
    {
        $totaltransaksi = str_replace(['.', ','], ['', '.'], $this->input->post('TotalTransaksi'));
        $isEdit = true;

        $tahun = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'msttahunanggaran',
                'where' => [['IsAktif' => 1]],
            ]
        );

        ## POST DATA
        if (!($this->input->post('NoTransKas') != null && $this->input->post('NoTransKas') != '')) {
            $insertdata = $this->input->post();
            unset($insertdata['TotalTransaksi']);
            unset($insertdata['StatusEdit']);
            $prefix = "TRK-" . date("Ym");
            $insertdata['NoTransKas'] = $this->crud->get_kode([
                'select' => 'RIGHT(NoTransKas, 7) AS KODE',
                'where' => [['LEFT(NoTransKas, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'NoTransKas DESC',
                'prefix' => $prefix
            ]);
            $insertdata['KodeTahun'] = $tahun['KodeTahun'];
            $insertdata['TotalTransaksi'] = $totaltransaksi;
            $insertdata['UserName']  = $this->session->userdata('UserName');
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
            $insertjurnal['KodeTahun'] = $insertdata['KodeTahun'];
            $insertjurnal['TglTransJurnal'] = $insertdata['TanggalTransaksi'];
            $insertjurnal['TipeJurnal'] = "UMUM";
            $insertjurnal['NarasiJurnal'] = "Transaksi Biaya";
            $insertjurnal['NominalTransaksi'] = $totaltransaksi;
            $insertjurnal['NoRefTrans'] = $insertdata['NoTransKas'];
            $insertjurnal['UserName'] = $this->session->userdata['UserName'];

            $isEdit = false;
            $res = $this->crud->insert($insertdata, 'transaksikas');
            $res2 = $this->crud->insert($insertjurnal, 'transjurnal');
        } else {
            $updatedata = $this->input->post();
            unset($updatedata['TotalTransaksi']);
            unset($updatedata['NoTransKas']);
            unset($updatedata['StatusEdit']);
            $status = $this->input->post('StatusEdit');
            if ($status != 'PAID') {
                $updatedata['Status'] = 'PAID';
            }
            $updatedata['TotalTransaksi']       = $totaltransaksi;

            $updatejurnal['TglTransJurnal']     = $updatedata['TanggalTransaksi'];
            $updatejurnal['NominalTransaksi']   = $totaltransaksi;

            $isEdit = true;
            $res = $this->crud->update($updatedata, ['NoTransKas' => $this->input->post('NoTransKas')], 'transaksikas');
            $res2 = $this->crud->update($updatejurnal, ['NoRefTrans' => $this->input->post('NoTransKas')], 'transjurnal');
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('NoTransKas') : $insertdata['NoTransKas'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Transaksi Biaya',
                'Description' => $ket . ' data transaksi biaya ' . $id
            ]);
            echo json_encode([
                'status'    => true,
                'msg'       => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data"),
                'action'    => $isEdit ? "edit" : "tambah",
                'id'        => $isEdit ? $this->input->post('NoTransKas') : $insertdata['NoTransKas'],
                'bayar'     => $isEdit ? $this->input->post('StatusEdit') : $this->input->post('Status'),
            ]);
        } else {
            echo json_encode([
                'status'    => false,
                'msg'       => ($isEdit ? "Gagal Edit Data" : "Gagal Menambah Data")
            ]);
        }
    }

    public function hapus()
    {
        $kode  = $this->input->get('NoTransKas');
        $dtitemjurnal = $this->crud->get_rows([
            'select' => '*',
            'from' => 'transjurnalitem i',
            'join' => [[
                'table' => ' transjurnal t',
                'on' => "t.IDTransJurnal = i.IDTransJurnal",
                'param' => 'LEFT',
            ]],
            'where' => [['t.NoRefTrans' => $kode]],
        ]);

        if (count($dtitemjurnal) > 0) {
            $deleteitemjurnal = $this->crud->delete(['IDTransJurnal' => $dtitemjurnal[0]['IDTransJurnal']], 'transjurnalitem');
        }

        $deletejurnal = $this->crud->delete(['NoRefTrans' => $kode], 'transjurnal');
        $res = $this->crud->delete(['NoTransKas' => $kode], 'transaksikas');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Transaksi Biaya',
                'Description' => 'hapus data transaksi biaya ' . $kode
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

    public function cetak()
    {
        $jenis   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('transaksi/transaksi_kas'); // . $this->uri->segment(4);
        if ($jenis) {
            $where = [[
                'k.JenisTransaksiKas' => $jenis,
                'k.IDRekap' => null,
                'k.KodePegawai' => null,
            ]];
        } else {
            $where = [
                [
                    'k.IDRekap' => null,
                    'k.KodePegawai' => null,
                ],
                " (k.JenisTransaksiKas = 'KAS MASUK' OR k.JenisTransaksiKas = 'KAS KELUAR')"
            ];
        }
        $sql = [
            'select' => '*',
            'from' => 'transaksikas k',
            'where' => $where,
            'order_by' => 'k.TanggalTransaksi',
        ];
        $data['model'] = $this->crud->get_rows($sql);
        $data['jenis'] = $jenis;

        $this->load->library('Pdf');
        $this->load->view('transaksi/v_transaksi_kas_cetak', $data);
    }

    public function jurnal()
    {
        checkAccess($this->session->userdata('fiturview')[47]);
        $notranskas   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM JURNAL
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'transkas';
            $data['title'] = 'Jurnal Transaksi Biaya';
            $data['view'] = 'transaksi/v_transaksi_kas_jurnal';
            $data['scripts'] = 'transaksi/s_transaksi_kas_jurnal';

            $dtjurnal = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'transjurnal t',
                'join' => [
                    [
                        'table' => ' transaksikas k',
                        'on' => "k.NoTransKas = t.NoRefTrans",
                        'param' => 'LEFT'
                    ],
                    [
                        'table' => ' userlogin u',
                        'on' => "u.UserName = k.UserName",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['t.NoRefTrans' => $notranskas]],
            ]);
            $data['dtjurnal'] = $dtjurnal;
            $data['IDTransJurnal'] = $dtjurnal['IDTransJurnal'];

            $nominaljurnal = $this->crud->get_one_row([
                'select' => 'SUM(i.Debet) AS Debet, SUM(i.Kredit) AS Kredit',
                'from' => 'transjurnalitem i',
                'join' => [[
                    'table' => ' transjurnal j',
                    'on' => "j.IDTransJurnal = i.IDTransJurnal",
                    'param' => 'INNER'
                ]],
                'where' => [['j.NoRefTrans' => $notranskas]],
            ]);
            $data['saldoDebet'] = ($nominaljurnal['Debet'] != null) ? (int)$nominaljurnal['Debet'] : 0;
            $data['saldoKredit'] = ($nominaljurnal['Kredit'] != null) ? (int)$nominaljurnal['Kredit'] : 0;

            $dtakun = [
                'select' => '*',
                'from' => 'mstakun',
                'where' => [[
                    'IsParent' => 0,
                    'IsAktif' => 1,
                ]]
            ];
            $data['dtakun'] = $this->crud->get_rows($dtakun);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $idtransjurnal   = $this->input->get('idtransjurnal');
            $configData['table'] = 'transjurnalitem i';
            $configData['where'] = [['i.IDTransJurnal'  => $idtransjurnal]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.KodeAkun LIKE '%$cari%' OR i.NamaAkun LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' transjurnal t',
                    'on' => "t.IDTransJurnal = i.IDTransJurnal",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.NoTransKas = t.NoRefTrans",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstakun a',
                    'on' => "a.KodeAkun = i.KodeAkun",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.NoUrut', 'i.IDTransJurnal', 'i.KodeTahun', 'i.KodeAkun', 'i.NamaAkun', 'i.Debet', 'i.Kredit', 'i.Uraian', 'i.Keterangan2'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.NoUrut', 'i.IDTransJurnal', 'i.KodeTahun', 'i.KodeAkun', 'i.NamaAkun', 'i.Debet', 'i.Kredit', 'i.Uraian', 'i.Keterangan2',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 47; //FiturID di tabel serverfitur
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
                $temp['Diskon'] = isset($temp['Diskon']) ? $temp['Diskon'] : 0;
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransJurnal'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['IDTransJurnal'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpanjurnal()
    {
        $insertdata = $this->input->post();
        unset($insertdata['JenisJurnal']);
        unset($insertdata['Nominal']);
        $idtransjurnal = $this->input->post('IDTransJurnal');
        $nominal = str_replace(['.', ','], ['', '.'], $this->input->post('Nominal'));
        $insertdata['Debet'] = ($this->input->post('JenisJurnal') == "Debet") ? $nominal : 0;
        $insertdata['Kredit'] = ($this->input->post('JenisJurnal') == "Kredit") ? $nominal : 0;

        $dtjurnal = $this->crud->get_one_row([
            'select' => 't.IDTransJurnal, t.KodeTahun, SUM(i.Debet) AS Debet, SUM(i.Kredit) AS Kredit, t.NominalTransaksi',
            'from' => 'transjurnal t',
            'join' => [[
                'table' => ' transjurnalitem i',
                'on' => "i.IDTransJurnal = t.IDTransJurnal",
                'param' => 'LEFT',
            ]],
            'where' => [['t.IDTransJurnal' => $idtransjurnal]],
            'group_by' => 't.IDTransJurnal',
        ]);

        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('NoUrut') != null && $this->input->post('NoUrut') != '')) {
            $isEdit = false;
            $getNoUrut = $this->db->from('transjurnalitem')
            ->where('IDTransJurnal', $this->input->post('IDTransJurnal'))
            ->select('NoUrut')
            ->order_by('NoUrut', 'desc')
            ->get()->row();
            if ($getNoUrut) {
                $NoUrut = (int)$getNoUrut->NoUrut;
            } else {
                $NoUrut = 0;
            }
            $insertdata['NoUrut'] = $NoUrut + 1;
            $nominaledit = 0;
            $isEdit = false;
        } else {
            $dtedit = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'transjurnalitem',
                'where' => [[
                    'NoUrut' => $this->input->post('NoUrut'),
                    'IDTransJurnal' => $this->input->post('IDTransJurnal'),
                ]],
            ]);
            $nominaledit = ($dtedit['Debet'] > 0) ? $dtedit['Debet'] : $dtedit['Kredit'];
            $isEdit = true;
        }

        $debet = ($this->input->post('JenisJurnal') == "Debet") ? ($dtjurnal['Debet'] + $nominal) : $dtjurnal['Debet'];
        $jmldebet = $isEdit ? ($debet - $nominaledit) : $debet;

        $kredit = ($this->input->post('JenisJurnal') == "Kredit") ? ($dtjurnal['Kredit'] + $nominal) : $dtjurnal['Kredit'];
        $jmlkredit = $isEdit ? ($kredit - $nominaledit) : $kredit;

        if ($jmldebet > $dtjurnal['NominalTransaksi']) {
            echo json_encode([
                'status' => false,
                'msg'  => ($isEdit ? "Gagal mengedit data, jumlah debet tidak boleh melebihi nominal transaksi." : "Gagal menambah data, jumlah debet tidak boleh melebihi nominal transaksi.")
            ]);
        } elseif ($jmlkredit > $dtjurnal['NominalTransaksi']) {
            echo json_encode([
                'status' => false,
                'msg'  => ($isEdit ? "Gagal mengedit data, jumlah kredit tidak boleh melebihi nominal transaksi." : "Gagal menambah data, jumlah kredit tidak boleh melebihi nominal transaksi.")
            ]);
        } else {
            $res = $this->crud->insert_or_update($insertdata, 'transjurnalitem');

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

    public function hapusjurnal()
    {
        $kode = $this->input->get('IDTransJurnal');
        $kode2 = $this->input->get('NoUrut');

        $res = $this->crud->delete(['IDTransJurnal' => $kode, 'NoUrut' => $kode2], 'transjurnalitem');

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

    public function cetakjurnal()
    {
        $idtransjurnal   = escape(base64_decode($this->uri->segment(4)));

        $data['dtinduk'] = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transjurnal t',
            'join' => [[
                'table' => ' transaksikas k',
                'on' => "k.NoTransKas = t.NoRefTrans",
                'param' => 'LEFT',
            ]],
            'where' => [['t.IDTransJurnal' => $idtransjurnal]],
        ]);

        $data['src_url'] = base_url('transaksi/transaksi_kas/jurnal/') . base64_encode($data['dtinduk']['NoTransKas']);

        $sql = $this->crud->get_rows([
            'select' => '*',
            'from' => 'transjurnalitem',
            'where' => [['IDTransJurnal' => $idtransjurnal]],
        ]);
        $data['model'] = $sql;
        $data['IDTransJurnal'] = $idtransjurnal;
        
        $this->load->library('Pdf');
        $this->load->view('transaksi/v_transaksi_kas_jurnal_cetak', $data);
    }

    public function jurnalmanual()
    {
        $menu = escape(base64_decode($this->uri->segment(4)));
        $idtransjurnal  = escape(base64_decode($this->uri->segment(5)));
        $notrans = ($this->uri->segment(6) != null) ? escape(base64_decode($this->uri->segment(6))) : '';
        $url = ($this->uri->segment(7) != null) ? escape(base64_decode($this->uri->segment(7))) : '';

        ## DATA ITEM JURNAL
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = $menu;
            $data['title'] = 'Penjurnalan Manual Transaksi';
            $data['view'] = 'transaksi/v_jurnal_manual';
            $data['scripts'] = 'transaksi/s_jurnal_manual';

            $dtjurnal = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'transjurnal t',
                'join' => [[
                    'table' => ' userlogin u',
                    'on' => "u.UserName = t.UserName",
                    'param' => 'LEFT',
                ]],
                'where' => [['t.IDTransJurnal' => $idtransjurnal]],
            ]);
            $data['dtjurnal'] = $dtjurnal;
            $data['IDTransJurnal'] = $idtransjurnal;
            $data['NoTrans'] = $notrans;
            $data['url'] = $url;

            $dtakun = [
                'select' => '*',
                'from' => 'mstakun',
                'where' => [[
                    'IsParent' => 0,
                    'IsAktif' => 1,
                ]]
            ];
            $data['dtakun'] = $this->crud->get_rows($dtakun);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $idtransjurnal   = $this->input->get('idtransjurnal');
            $configData['table'] = 'transjurnalitem i';
            $configData['where'] = [['i.IDTransJurnal'  => $idtransjurnal]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.KodeAkun LIKE '%$cari%' OR i.NamaAkun LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' transjurnal t',
                    'on' => "t.IDTransJurnal = i.IDTransJurnal",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.NoTransKas = t.NoRefTrans",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstakun a',
                    'on' => "a.KodeAkun = i.KodeAkun",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.NoUrut', 'i.IDTransJurnal', 'i.KodeTahun', 'i.KodeAkun', 'i.NamaAkun', 'i.Debet', 'i.Kredit', 'i.Uraian', 'i.Keterangan2'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.NoUrut', 'i.IDTransJurnal', 'i.KodeTahun', 'i.KodeAkun', 'i.NamaAkun', 'i.Debet', 'i.Kredit', 'i.Uraian', 'i.Keterangan2',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 47; //FiturID di tabel serverfitur
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
                $temp['Diskon'] = isset($temp['Diskon']) ? $temp['Diskon'] : 0;
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransJurnal'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['IDTransJurnal'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function checkNominal()
    {
        $idtransjurnal = $this->input->get('IDTransJurnal');
        $totaltransaksi = (int)$this->input->get('totaltransaksi');
        $totaldebet = (int)str_replace(['.', ','], ['', '.'], $this->input->get('totaldebet'));
        $totalkredit = (int)str_replace(['.', ','], ['', '.'], $this->input->get('totalkredit'));
        $notrans = $this->input->get('notrans');
        $urls = $this->input->get('url');

        $dtjurnal = $this->crud->get_one_row([
            'select' => 'j.*, k.NoRef_Sistem, k.KodePerson',
            'from' => 'transjurnal j',
            'join' => [[
                'table' => ' transaksikas k',
                'on' => "k.NoTransKas = j.NoRefTrans",
                'param' => 'LEFT',
            ]],
            'where' => [['IDTransJurnal' => $idtransjurnal]],
        ]);

        if ($dtjurnal['NarasiJurnal'] == 'Transaksi Pembelian Kredit' || $dtjurnal['NarasiJurnal'] == 'Transaksi Pembelian Tunai' || $dtjurnal['NarasiJurnal'] == 'Transaksi Penjualan Kredit' || $dtjurnal['NarasiJurnal'] == 'Transaksi Penjualan Tunai' || $dtjurnal['NarasiJurnal'] == 'Retur Penjualan' || $dtjurnal['NarasiJurnal'] == 'Proses Produksi') {
            $url = 'transaksi/' . $urls . '/' . base64_encode($notrans);
        } elseif ($dtjurnal['NarasiJurnal'] == 'Transaksi Hutang') {
            $url = 'transaksi/transaksi_pembelian/kirimpembayaran/' . base64_encode($notrans) . '/' . base64_encode($dtjurnal['KodePerson']);
        } elseif ($dtjurnal['NarasiJurnal'] == 'Transaksi Terima Piutang') {
            $url = 'transaksi/transaksi_penjualan/terimapembayaran/' . base64_encode($notrans) . '/' . base64_encode($dtjurnal['KodePerson']);
        } elseif ($dtjurnal['NarasiJurnal'] == 'Penggajian') {
            $url = 'payroll/' . $urls . '/' . base64_encode($notrans);
        } elseif ($dtjurnal['NarasiJurnal'] == 'Transaksi Penerimaan Barang') {
            $dtpembelian = $this->crud->get_one_row([
                'select' => 'NoTrans, NoRefTrSistem',
                'from' => 'transaksibarang',
                'where' => [['NoTrans' => $notrans]]
            ]);
            $url = 'transaksi/' . $urls . '/' . base64_encode($notrans) . '/' . base64_encode($dtpembelian['NoRefTrSistem']);
        } elseif ($dtjurnal['NarasiJurnal'] == 'Transaksi Biaya') {
            $url = 'transaksi/transaksi_kas';
        } elseif ($dtjurnal['NarasiJurnal'] == 'Transaksi Pinjaman Karyawan') {
            $url = 'transaksi/transaksi_pinjaman';
        }

        if ($totaltransaksi == $totaldebet && $totaltransaksi == $totalkredit) {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Total sesuai', 'url' => $url]);
        } else {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Total Transaksi, Total Debet dan Total Kredit tidak sama!']);
        }
    }
}
