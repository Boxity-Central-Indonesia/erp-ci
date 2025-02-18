<?php
defined('BASEPATH') or exit('No direct script access allowed');

class transaksi_pinjaman extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'trpinjamankaryawan tp';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[65]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[65]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'pinjaman';
            $data['title'] = 'Transaksi Pinjaman Karyawan';
            $data['view'] = 'transaksi/v_transaksi_pinjaman';
            $data['scripts'] = 'transaksi/s_transaksi_pinjaman';

            $dtpegawai = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstpegawai',
                    // 'where' => [['KodeJabatan' => 'JBT-0000003']],
                ]
            );
            $data['dtpegawai'] = $dtpegawai;

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'trpinjamankaryawan tp';

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPegawai LIKE '%$cari%' OR tp.MingguKe LIKE '%$cari%' OR tp.Keterangan LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(tp.TanggalPinjam) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['where'] = [
                [
                    'tp.IsHapus' => 0,
                    // 'a.JenisAktivitas' => 'Ampas Dapur',
                ]
            ];

            $configData['join'] = [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = tp.KodePegawai",
                    'param' => 'INNER',
                ],
                [
                    'table' => 'userlogin u',
                    'on' => "u.UserName = tp.UserName",
                    'param' => 'LEFT',
                ],
                [
                    'table' => 'transaksikas k',
                    'on' => "k.NoRef_Sistem = tp.KodeTrPinjam",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'tp.KodeTrPinjam', 'tp.TanggalPinjam', 'tp.NominalPinjam', 'tp.MingguKe', 'tp.IsDibayar', 'tp.NominalDibayar', 'tp.Keterangan', 'tp.IsHapus', 'tp.KodePegawai', 'p.NamaPegawai', 'tp.UserName', 'u.ActualName', 'k.NoTransKas'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'tp.TanggalPinjam, tp.KodeTrPinjam';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'tp.KodeTrPinjam', 'tp.TanggalPinjam', 'tp.NominalPinjam', 'tp.MingguKe', 'tp.IsDibayar', 'tp.NominalDibayar', 'tp.Keterangan', 'tp.IsHapus', 'tp.KodePegawai', 'p.NamaPegawai', 'tp.UserName', 'u.ActualName', 'k.NoTransKas',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 65; //FiturID di tabel serverfitur
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
                $temp['TanggalPinjam'] = isset($temp['TanggalPinjam']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPinjam'])))  . ' ' . date('H:i', strtotime($temp['TanggalPinjam'])) : '';

                $dtjurnal = $this->crud->get_one_row([
                    'select' => 'IDTransJurnal, NominalTransaksi, NoRefTrans',
                    'from' => 'transjurnal',
                    'where' => [['NoRefTrans' => $temp['NoTransKas']]],
                ]);
                $temp['IDTransJurnal'] = isset($dtjurnal['IDTransJurnal']) ? $dtjurnal['IDTransJurnal'] : '';
                $temp['NominalTransaksi'] = isset($dtjurnal['NominalTransaksi']) ? $dtjurnal['NominalTransaksi'] : 0;
                $itemjurnal = $this->lokasi->get_total_item_jurnal($temp['IDTransJurnal']);
                $temp['TotalDebet'] = (int)$itemjurnal['Debet'];
                $temp['TotalKredit'] = (int)$itemjurnal['Kredit'];

                if ($temp['NominalTransaksi'] > 0 && ($temp['NominalTransaksi'] > $temp['TotalDebet'] || $temp['NominalTransaksi'] > $temp['TotalKredit'])) {
                    if ($canEdit == 1 && $canDelete == 1) {
                        $temp['btn_aksi'] = '<a class="btnjurnal" style="color:#2c99ff;" href="' . base_url('transaksi/transaksi_kas/jurnalmanual/' . base64_encode('pinjaman') . '/' . base64_encode($temp['IDTransJurnal']) . '/' . base64_encode('transaksi') . '/' . base64_encode('transaksi_pinjaman')) . '" type="button" title="Jurnalkan"><span class="fas fa-journal-whills" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodeTrPinjam'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                    } elseif ($canEdit == 1 && $canDelete == 0) {
                        $temp['btn_aksi'] = '<a class="btnjurnal" style="color:#2c99ff;" href="' . base_url('transaksi/transaksi_kas/jurnalmanual/' . base64_encode('pinjaman') . '/' . base64_encode($temp['IDTransJurnal']) . '/' . base64_encode('transaksi') . '/' . base64_encode('transaksi_pinjaman')) . '" type="button" title="Jurnalkan"><span class="fas fa-journal-whills" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                    } elseif ($canDelete == 1 && $canEdit == 0) {
                        $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodeTrPinjam'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                    } else {
                        $temp['btn_aksi'] = '';
                    }
                } else {
                    $temp['btn_aksi'] = '<a class="btnjurnalsudah" type="button" title="Jurnalkan"><span class="fas fa-journal-whills" aria-hidden="true"></span></a>';
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
        unset($insertdata['NominalPinjam']);
        $nominalpinjam = str_replace(['.', ','], ['', '.'], $this->input->post('NominalPinjam'));
        $insertdata['NominalPinjam'] = $nominalpinjam;

        $date = date('Y-m-d', strtotime($this->input->post('TanggalPinjam')));
        $week = $this->weekOfMonth($date);
        $insertdata['MingguKe'] = ($week > 4) ? 4 : $week;

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
                's.NamaTransaksi' => 'Transaksi Pinjaman Karyawan',
                's.JenisTransaksi' => 'Tunai',
            ]],
        ]);

        $status_jurnal = $this->lokasi->setting_jurnal_status();

        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('KodeTrPinjam') != null && $this->input->post('KodeTrPinjam') != '')) {
            $prefix = "PJM-" . date("Y");
            $insertdata['KodeTrPinjam'] = $this->crud->get_kode([
                'select' => 'RIGHT(KodeTrPinjam, 7) AS KODE',
                'where' => [['LEFT(KodeTrPinjam, 8) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'KodeTrPinjam DESC',
                'prefix' => $prefix
            ]);
            $insertdata['IsDibayar']        = 0;
            $insertdata['NominalDibayar']   = 0;
            $insertdata['IsHapus']          = 0;
            $insertdata['UserName']         = $this->session->userdata('UserName');

            $prefix2 = "TRK-" . date("Ym");
            $kas = [
                'NoTransKas' => $this->crud->get_kode([
                    'select' => 'RIGHT(NoTransKas, 7) AS KODE',
                    'from' => 'transaksikas',
                    'where' => [['LEFT(NoTransKas, 10) =' => $prefix2]],
                    'limit' => 1,
                    'order_by' => 'NoTransKas DESC',
                    'prefix' => $prefix2
                ]),
                'KodeTahun'         => $this->akses->get_tahun_aktif(),
                'TanggalTransaksi'  => $insertdata['TanggalPinjam'],
                'NoRef_Sistem'      => $insertdata['KodeTrPinjam'],
                'Uraian'            => $insertdata['KodePegawai'] . '#' . $insertdata['Keterangan'],
                'UserName'          => $this->session->userdata('UserName'),
                'TotalTransaksi'    => $insertdata['NominalPinjam'],
                'IsDijurnalkan'     => 1,
                'JenisTransaksiKas' => 'KAS KELUAR',
                'Status'            => 'PAID'
            ];
            $insertkas = $this->crud->insert($kas, 'transaksikas');

            $prefix3 = "JRN-" . date("Ym");
            $jurnal = [
                'IDTransJurnal' => $this->crud->get_kode([
                    'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                    'from' => 'transjurnal',
                    'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix3]],
                    'limit' => 1,
                    'order_by' => 'IDTransJurnal DESC',
                    'prefix' => $prefix3
                ]),
                'KodeTahun' => $kas['KodeTahun'],
                'TglTransJurnal' => $kas['TanggalTransaksi'],
                'TipeJurnal' => "UMUM",
                'NarasiJurnal' => "Transaksi Pinjaman Karyawan",
                'NominalTransaksi' => $kas['TotalTransaksi'],
                'NoRefTrans' => $kas['NoTransKas'],
                'UserName' => $this->session->userdata('UserName')
            ];
            $insertjurnalinduk = $this->crud->insert($jurnal, 'transjurnal');

            if ($status_jurnal == 'on') {
                if ($getakun) {
                    foreach ($getakun as $keys) {
                        $itemjurnal = [
                            'NoUrut' => $keys['NoUrut'],
                            'IDTransJurnal' => $jurnal['IDTransJurnal'],
                            'KodeTahun' => $jurnal['KodeTahun'],
                            'KodeAkun' => $keys['KodeAkun'],
                            'NamaAkun' => $keys['NamaAkun'],
                            'Debet' => ($keys['JenisJurnal'] == 'Debet') ? $jurnal['NominalTransaksi'] : 0,
                            'Kredit' => ($keys['JenisJurnal'] == 'Kredit') ? $jurnal['NominalTransaksi'] : 0,
                            'Uraian' => "Penjurnalan otomatis untuk Transaksi Pinjaman Karyawan"
                        ];

                        $insertjurnalitem[] = $this->crud->insert_or_update($itemjurnal, 'transjurnalitem');
                    }
                }
            }

            $isEdit = false;
        } else {
            $getKas = $this->crud->get_one_row([
                'select' => 'k.NoTransKas, k.NoRef_Sistem, j.IDTransJurnal, k.KodeTahun',
                'from' => 'transaksikas k',
                'join' => [
                    [
                        'table' => 'transjurnal j',
                        'on' => "j.NoRefTrans = k.NoTransKas",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['k.NoRef_Sistem' => $this->input->post('KodeTrPinjam')]]
            ]);
            if ($getKas) {
                $deleteitemjurnal = $this->crud->delete(['IDTransJurnal' => $getKas['IDTransJurnal']], 'transjurnalitem');
                $kas = [
                    'TanggalTransaksi'  => $insertdata['TanggalPinjam'],
                    'TotalTransaksi'    => $insertdata['NominalPinjam'],
                    'Uraian'            => $insertdata['KodePegawai'] . '#' . $insertdata['Keterangan'],
                ];
                $updatekas = $this->crud->update($kas, ['NoTransKas' => $getKas['NoTransKas']], 'transaksikas');

                $jurnal = [
                    'TglTransJurnal' => $kas['TanggalTransaksi'],
                    'NominalTransaksi' => $kas['TotalTransaksi'],
                ];
                $updatejurnalinduk = $this->crud->update($jurnal, ['NoRefTrans' => $getKas['NoTransKas']], 'transjurnal');

                if ($status_jurnal == 'on') {
                    if ($getakun) {
                        foreach ($getakun as $keys) {
                            $itemjurnal = [
                                'NoUrut' => $keys['NoUrut'],
                                'IDTransJurnal' => $getKas['IDTransJurnal'],
                                'KodeTahun' => $getKas['KodeTahun'],
                                'KodeAkun' => $keys['KodeAkun'],
                                'NamaAkun' => $keys['NamaAkun'],
                                'Debet' => ($keys['JenisJurnal'] == 'Debet') ? $jurnal['NominalTransaksi'] : 0,
                                'Kredit' => ($keys['JenisJurnal'] == 'Kredit') ? $jurnal['NominalTransaksi'] : 0,
                                'Uraian' => "Penjurnalan otomatis untuk Transaksi Pinjaman Karyawan"
                            ];

                            $insertjurnalitem[] = $this->crud->insert_or_update($itemjurnal, 'transjurnalitem');
                        }
                    }
                }
            }

            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'trpinjamankaryawan');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('KodeTrPinjam') : $insertdata['KodeTrPinjam'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Transaksi Pinjaman Karyawan',
                'Description' => $ket . ' data transaksi pinjaman karyawan ' . $id
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data"),
                'idjurnal' => $isEdit ? $getKas['IDTransJurnal'] : $jurnal['IDTransJurnal'],
                'stj' => $status_jurnal
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ($isEdit ? "Gagal Edit Data" : "Gagal Menambah Data")
            ]);
        }
    }

    public function weekOfMonth($date)
    {
        $week = ceil(date('j', strtotime($date)) / 7);
        return $week;
    }

    public function checkPinjaman()
    {
        $kodetr = $this->input->get('KodeTrPinjam');
        $kodepegawai = $this->input->get('KodePegawai');
        $nominalpinjam = (int)str_replace(['.', ','], ['', '.'], $this->input->get('NominalPinjam'));
        $tgl_pinjam = date('Y-m-d', strtotime($this->input->get('TanggalPinjam')));
        $bln_pinjam = date('Y-m', strtotime($tgl_pinjam));
        $week = $this->weekOfMonth($tgl_pinjam);
        $minggu_ke = ($week > 4) ? 4 : $week;
        $m = date('m', strtotime($tgl_pinjam));
        $y = date('Y', strtotime($tgl_pinjam));
        $totalhari = cal_days_in_month(CAL_GREGORIAN,$m,$y);

        // $getlimit = $this->lokasi->get_limit_pinjaman();
        // $limit = ($getlimit != null) ? $getlimit : 0;

        $dtpegawai = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'mstpegawai',
            'where' => [['KodePegawai' => $kodepegawai]]
        ]);

        $limit = ($dtpegawai['IsGajiHarian'] == 0) ? round($dtpegawai['GajiPokok']) : round($dtpegawai['GajiPokok'] * $totalhari);

        $cekminggu = $this->mingguPinjam($kodepegawai, $bln_pinjam, $minggu_ke, $kodetr);
        $cekbulan = $this->bulanPinjam($kodepegawai, $bln_pinjam, $nominalpinjam, $kodetr, $limit);

        if ($cekminggu > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Pegawai tidak boleh melakukan pinjaman lebih dari satu kali dalam minggu dan bulan yang sama.']);
        } elseif ($cekbulan == 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Total pinjaman tidak boleh melebihi limit dalam satu bulan. Limit pinjaman karyawan sebesar Rp. ' . str_replace(',', '.', number_format($limit))]);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Pinjaman tersedia']);
        }
    }

    public function mingguPinjam($kodepegawai, $bulan, $minggu, $kodetr)
    {
        $dtpinjam = $this->crud->get_count([
            'select' => 'KodeTrPinjam',
            'from' => 'trpinjamankaryawan',
            'where' => [
                [
                    'KodeTrPinjam !=' => $kodetr,
                    'KodePegawai' => $kodepegawai,
                    'MingguKe' => $minggu,
                    'DATE_FORMAT(TanggalPinjam, "%Y-%m") =' => $bulan
                ]
            ],
        ]);

        return $dtpinjam;
    }

    public function bulanPinjam($kodepegawai, $bulan, $nominal, $kodetr, $limit)
    {
        $dtpinjam = $this->crud->get_one_row([
            'select' => 'KodeTrPinjam, KodePegawai, TanggalPinjam, SUM(NominalPinjam) as TotalPinjam',
            'from' => 'trpinjamankaryawan',
            'where' => [
                [
                    'KodePegawai' => $kodepegawai,
                    'DATE_FORMAT(TanggalPinjam, "%Y-%m") =' => $bulan
                ],
            ],
            'group_by' => 'KodePegawai'
        ]);

        $gettr = $this->crud->get_one_row([
            'select' => 'KodeTrPinjam, NominalPinjam',
            'from' => 'trpinjamankaryawan',
            'where' => [['KodeTrPinjam' => $kodetr]]
        ]);
        $nominaledit = isset($gettr['NominalPinjam']) ? (int)$gettr['NominalPinjam'] : 0;
        $total = isset($dtpinjam['TotalPinjam']) ? (int)$dtpinjam['TotalPinjam'] : 0;
        $perhitungan = ($gettr != null) ? ($total + $nominal - $nominaledit) : ($total + $nominal);
        $avalable = ($perhitungan > $limit) ? 0 : 1;

        return $avalable;
    }

    public function hapus()
    {
        $kode  = $this->input->get('KodeTrPinjam');

        $cekKasJurnal = $this->crud->get_one_row([
            'select' => 'k.NoTransKas, k.NoRef_Sistem, j.IDTransJurnal, k.KodeTahun',
            'from' => 'transaksikas k',
            'join' => [
                [
                    'table' => 'transjurnal j',
                    'on' => "j.NoRefTrans = k.NoTransKas",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['k.NoRef_Sistem' => $kode]]
        ]);
        if ($cekKasJurnal) {
            $deleteitemjurnal = $this->crud->delete(['IDTransJurnal' => $cekKasJurnal['IDTransJurnal']], 'transjurnalitem');
            $deletejurnalinduk = $this->crud->delete(['IDTransJurnal' => $cekKasJurnal['IDTransJurnal']], 'transjurnal');
            $deletetranskas = $this->crud->delete(['NoRef_Sistem' => $kode], 'transaksikas');
        }

        $res = $this->crud->update(['IsHapus' => 1], ['KodeTrPinjam' => $kode], 'trpinjamankaryawan');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Transaksi Pinjaman Karyawan',
                'Description' => 'hapus data transaksi pinjaman karyawan ' . $kode
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
        if ($jenis) {
            $where = [['k.JenisTransaksiKas' => $jenis]];
        } else {
            $where = [" (k.JenisTransaksiKas = 'KAS MASUK' OR k.JenisTransaksiKas = 'KAS KELUAR')"];
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
        $this->load->view('transaksi/v_ampas_dapur_cetak', $data);
    }
}
