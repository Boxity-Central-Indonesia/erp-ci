<?php
defined('BASEPATH') or exit('No direct script access allowed');

class penerimaan_gaji extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'rekapinsentifbulanan';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[53]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[53]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'penerimaangaji';
            $data['title'] = 'Transaksi Penerimaan Gaji';
            $data['view'] = 'payroll/v_penerimaan_gaji';
            $data['scripts'] = 'payroll/s_penerimaan_gaji';
            $data['bln'] = $this->input->get('bulan');
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'rekapinsentifbulanan r';

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPegawai LIKE '%$cari%' OR j.NamaJabatan LIKE '%$cari%' OR r.TotalInsentif LIKE '%$cari%')";
            }

            $bulan     = $this->input->get('bulan');
            if ($bulan) {
                $configData['where'] = [['r.Bulan' => $bulan]];
            }

            $configData['join'] = [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = p.KodeJabatan",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.KodePegawai = r.KodePegawai AND k.IDRekap = r.IDRekap",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' iteminsentifbulanan i',
                    'on' => "i.IDRekap = r.IDRekap AND i.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = r.UserName",
                    'param' => 'LEFT',
                ]
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'r.IDRekap', 'r.KodeTahun', 'r.Bulan', 'r.TglRekap', 'r.TotalInsentif', 'r.Keterangan', 'r.IsTelahDibayarkan', 'r.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan', 'r.NoTransBayar', 'SUM(if(i.CaraHitung = "Tambah", i.JmlPerolehan, 0)) - SUM(if(i.CaraHitung = "Kurang", i.JmlPerolehan, 0)) AS InsentifPegawai', 'k.NoTransKas', 'r.UserName', 'u.ActualName'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'r.IDRekap, r.KodePegawai';
            $configData['custom_column_sort_order'] = 'ASC';
            $configData['group_by'] = 'r.IDRekap';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'r.IDRekap', 'r.KodeTahun', 'r.Bulan', 'r.TglRekap', 'r.TotalInsentif', 'r.Keterangan', 'r.IsTelahDibayarkan', 'r.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan', 'r.NoTransBayar', 'SUM(if(i.CaraHitung = "Tambah", i.JmlPerolehan, 0)) - SUM(if(i.CaraHitung = "Kurang", i.JmlPerolehan, 0)) AS InsentifPegawai', 'k.NoTransKas', 'r.UserName', 'u.ActualName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 53; //FiturID di tabel serverfitur
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
                setlocale(LC_ALL, 'IND');
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

                if ($canEdit == 1 && $temp['IsTelahDibayarkan'] == 0) {
                    $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('payroll/penerimaan_gaji/detail/' . base64_encode($temp['IDRekap'])) . '" type="button" title="Detail Penerimaan Gaji"><span class="fa fa-list" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDRekap'] . ' data-kode2=' . $temp['Bulan'] . ' class="btnbayar" title="Bayarkan"><span class="fa fa-check-circle" aria-hidden="true"></span></a>';
                } else {
                    if ($temp['NominalTransaksi'] > 0 && ($temp['NominalTransaksi'] > $temp['TotalDebet'] || $temp['NominalTransaksi'] > $temp['TotalKredit'])) {
                        $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('payroll/penerimaan_gaji/detail/' . base64_encode($temp['IDRekap'])) . '" type="button" title="Detail Penerimaan Gaji"><span class="fa fa-list" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;&nbsp;<a class="btnjurnal" style="color:#2c99ff;" href="' . base_url('transaksi/transaksi_kas/jurnalmanual/' . base64_encode('penerimaangaji') . '/' . base64_encode($temp['IDTransJurnal']) . '/' . base64_encode($temp['IDRekap']) . '/' . base64_encode('penerimaan_gaji/detail')) . '" type="button" title="Jurnalkan"><span class="fas fa-journal-whills" aria-hidden="true"></span></a>';
                    } else {
                        $temp['btn_aksi'] = '<a class="btnfitur" href="' . base_url('payroll/penerimaan_gaji/detail/' . base64_encode($temp['IDRekap'])) . '" type="button" title="Detail Penerimaan Gaji"><span class="fa fa-list" aria-hidden="true"></span></a>' . '&nbsp;&nbsp;&nbsp;<a class="btnjurnalsudah" type="button" title="Jurnalkan"><span class="fas fa-journal-whills" aria-hidden="true"></span></a>';
                    }
                }
                $temp['Bulan'] = isset($temp['Bulan']) ? strftime('%B %Y', strtotime($temp['Bulan'])) : '';
                if ($temp['IsTelahDibayarkan'] == 1) {
                    $temp['Status'] = 'Sudah';
                } elseif ($temp['IsTelahDibayarkan'] == 2) {
                    $temp['Status'] = 'Gagal';
                } elseif ($temp['IsTelahDibayarkan'] == 3) {
                    $temp['Status'] = 'Pending';
                } else {
                    $temp['Status'] = 'Belum';
                }
                $temp['InsentifPegawai'] = isset($temp['InsentifPegawai']) ? $temp['InsentifPegawai'] : 0;
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetak()
    {
        setlocale(LC_ALL, 'IND');
        $bulan   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('payroll/penerimaan_gaji?bulan=') . $bulan;

        if ($bulan == '' && $bulan == null) {
            echo 'Pilih Periode Terlrbih Dahulu!';
        } else {
            $sql = [
                'select' => 'r.IDRekap, r.KodeTahun, r.Bulan, r.TglRekap, r.TotalInsentif, r.Keterangan, r.IsTelahDibayarkan, r.KodePegawai, p.NamaPegawai, p.NIP, p.KodeJabatan, j.NamaJabatan, r.NoTransBayar, SUM(if(i.CaraHitung = "Tambah", i.JmlPerolehan, 0)) - SUM(if(i.CaraHitung = "Kurang", i.JmlPerolehan, 0)) AS InsentifPegawai',
                'from' => 'rekapinsentifbulanan r',
                'where' => [['r.Bulan' => $bulan]],
                'join' => [
                    [
                        'table' => ' mstpegawai p',
                        'on' => "p.KodePegawai = r.KodePegawai",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' mstjabatan j',
                        'on' => "j.KodeJabatan = p.KodeJabatan",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' transaksikas k',
                        'on' => "k.KodePegawai = r.KodePegawai AND k.IDRekap = r.IDRekap",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' iteminsentifbulanan i',
                        'on' => "i.IDRekap = r.IDRekap AND i.KodePegawai = r.KodePegawai",
                        'param' => 'LEFT',
                    ]
                ],
                'group_by' => 'r.IDRekap',
            ];
            $data['model'] = $this->crud->get_rows($sql);
            $data['bulan'] = strftime('%B %Y', strtotime($bulan));

            if (count($data['model']) > 0) {
                $this->load->library('Pdf');
                $this->load->view('payroll/cetak_penerimaan_gaji', $data);
            } else {
                echo 'Tidak ada data transaksi penerimaan gaji pada periode '.$data['bulan'];
            }
        }
    }

    public function simpan()
    {
        $bulan = $this->input->post('Bulan');

        $tahun = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'msttahunanggaran',
                'where' => [['IsAktif' => 1]],
            ]
        );

        $pegawai = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'mstpegawai',
                'where' => [['IsAktif' => 1]],
            ]
        );

        $countrekap = $this->crud->get_count([
            'select' => 'IDRekap',
            'from' => 'rekapinsentifbulanan',
            'where' => [[
                'Bulan' => $bulan,
                'IsTelahDibayarkan' => 1,
            ]],
        ]);

        if ($countrekap < 1) {
            foreach ($pegawai as $peg) {
                $list = [
                    'IDRekap' => str_replace('-', '', $bulan).'-'.$peg['KodePegawai'],
                    'KodePegawai' => $peg['KodePegawai'],
                    'KodeTahun' => $tahun['KodeTahun'],
                    'Bulan' => $bulan,
                    'TglRekap' => date('Y-m-d H:i'),
                    'TotalInsentif' => 0,
                    'IsTelahDibayarkan' => 0,
                    'NoTransBayar' => null,
                    'UserName' => $this->session->userdata('UserName')
                ];

                $getkas = $this->crud->get_one_row([
                    'select' => 'k.NoTransKas, k.IDRekap, k.KodePegawai, j.NoRefTrans, j.IDTransJurnal',
                    'from' => 'transaksikas k',
                    'join' => [[
                        'table' => ' transjurnal j',
                        'on' => "j.NoRefTrans = k.NoTransKas",
                        'param' => 'INNER',
                    ]],
                    'where' => [[
                        'k.IDRekap' => $list['IDRekap'],
                        'k.KodePegawai' => $list['KodePegawai'],
                    ]],
                ]);

                if ($getkas) {
                    $deleteitemjurnal[] = $this->crud->delete(['IDTransJurnal' => $getkas['IDTransJurnal']], 'transjurnalitem');
                    $deletejurnalinduk[] = $this->crud->delete(['IDTransJurnal' => $getkas['IDTransJurnal']], 'transjurnal');
                    $deletekas[] = $this->crud->delete(['NoTransKas' => $getkas['NoTransKas']], 'transaksikas');
                }

                $deleteitemrekap[] = $this->crud->delete(['IDRekap' => $list['IDRekap'], 'KodePegawai' => $list['KodePegawai']], 'iteminsentifbulanan');
                $insertrekap[] = $this->crud->insert_or_update($list, 'rekapinsentifbulanan');
            }

            $getrekap = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'rekapinsentifbulanan r',
                    'join' => [
                        [
                            'table' => ' mstpegawai p',
                            'on' => "p.KodePegawai = r.KodePegawai",
                            'param' => 'LEFT',
                        ],
                        [
                            'table' => ' mstjabatan j',
                            'on' => "j.KodeJabatan = p.KodeJabatan",
                            'param' => 'LEFT',
                        ],
                    ],
                    'where' => [['r.Bulan' => $bulan]],
                ]
            );

            // insert gaji pokok & thr
            foreach ($getrekap as $rek) {
                $getNoUrut = $this->db->from('iteminsentifbulanan')
                ->where([
                    'IDRekap' => $rek['IDRekap'],
                    'KodePegawai' => $rek['KodePegawai'],
                ])
                ->select('NoUrut')
                ->order_by('NoUrut', 'desc')
                ->get()->row();
                $NoUrut = isset($getNoUrut) ? (int)$getNoUrut->NoUrut : 0;

                $absensi = $this->crud->get_one_row(
                    [
                        'select' => 'a.KodePegawai, p.NamaPegawai, SUM(if(a.Keterangan = "Hadir", 1, 0)) AS Hadir, SUM(if(a.Keterangan = "Dinas Luar", 1, 0)) AS DL, SUM(if(a.Keterangan = "Sakit", 1, 0)) AS Sakit, SUM(if(a.Keterangan = "Izin", 1, 0)) AS Izin, SUM(if(a.Keterangan = "Alpha", 1, 0)) AS Alpha, COUNT(a.Keterangan) AS TotalHari, SUM(a.Telat) AS MenitPelanggaran',
                        'from' => 'absensipegawai a',
                        'join' => [
                            [
                                'table' => ' mstpegawai p',
                                'on' => "p.KodePegawai = a.KodePegawai",
                                'param' => 'LEFT',
                            ],
                        ],
                        'where' => [
                            [
                                'DATE_FORMAT(a.Tanggal, "%Y-%m") =' => $bulan,
                                'a.KodePegawai' => $rek['KodePegawai'],
                            ]
                        ],
                    ]
                );

                $hadir          = ($absensi['Hadir'] != null) ? $absensi['Hadir'] : 0;
                $dl             = ($absensi['DL'] != null) ? $absensi['DL'] : 0;
                $jmlperolehan   = ($rek['IsGajiHarian'] == 1) ? ($hadir + $dl) * $rek['GajiPokok'] : $rek['GajiPokok'];
                $satuanpkj      = ($rek['IsGajiHarian'] == 1) ? 'Harian' : 'Bulanan';

                $list_gapok = [
                    'NoUrut' => $NoUrut + 1,
                    'IDRekap' => $rek['IDRekap'],
                    'KodePegawai' => $rek['KodePegawai'],
                    'BagianPekerjaan' => 'Komponen Gaji',
                    'NamaPekerjaan' => 'Gaji Pokok',
                    'SatuanPekerjaan' => $satuanpkj,
                    'JmlPerolehan' => round($jmlperolehan),
                    'CaraHitung' => 'Tambah'
                ];

                $itemgajipokok[] = $this->crud->insert($list_gapok, 'iteminsentifbulanan');

                $thr = $this->crud->get_one_row(
                    [
                        'select' => '*',
                        'from' => 'mstkomponengaji',
                        'where' => [[
                            'JenisKomponen' => 'THR',
                            'KodeJabatan' => $rek['KodeJabatan'],
                        ]],
                    ]
                );

                if ($this->input->post('IsTHR')) {
                    if ($thr) {
                        $list_thr = [
                            'NoUrut' => isset($list_gapok['NoUrut']) ? $list_gapok['NoUrut'] + 1 : $NoUrut + 1,
                            'IDRekap' => $rek['IDRekap'],
                            'KodePegawai' => $rek['KodePegawai'],
                            'BagianPekerjaan' => 'Komponen Gaji',
                            'NamaPekerjaan' => 'Tunjangan Hari Raya',
                            'SatuanPekerjaan' => $thr['Kriteria'],
                            'JmlPerolehan' => round($thr['NominalRp']),
                            'CaraHitung' => $thr['CaraHitung']
                        ];

                        $itemthr[] = $this->crud->insert($list_thr, 'iteminsentifbulanan');
                    }
                }
            }

            // insert uang makan & dinas luar
            foreach ($getrekap as $rek) {
                $getNoUrut = $this->db->from('iteminsentifbulanan')
                ->where([
                    'IDRekap' => $rek['IDRekap'],
                    'KodePegawai' => $rek['KodePegawai'],
                ])
                ->select('NoUrut')
                ->order_by('NoUrut', 'desc')
                ->get()->row();
                $NoUrut = isset($getNoUrut) ? (int)$getNoUrut->NoUrut : 0;

                $absensi = $this->crud->get_one_row(
                    [
                        'select' => 'a.KodePegawai, p.NamaPegawai, SUM(if(a.Keterangan = "Hadir", 1, 0)) AS Hadir, SUM(if(a.Keterangan = "Dinas Luar", 1, 0)) AS DL, SUM(if(a.Keterangan = "Sakit", 1, 0)) AS Sakit, SUM(if(a.Keterangan = "Izin", 1, 0)) AS Izin, SUM(if(a.Keterangan = "Alpha", 1, 0)) AS Alpha, COUNT(a.Keterangan) AS TotalHari, SUM(a.Telat) AS MenitPelanggaran',
                        'from' => 'absensipegawai a',
                        'join' => [
                            [
                                'table' => ' mstpegawai p',
                                'on' => "p.KodePegawai = a.KodePegawai",
                                'param' => 'LEFT',
                            ],
                        ],
                        'where' => [
                            [
                                'DATE_FORMAT(a.Tanggal, "%Y-%m") =' => $bulan,
                                'a.KodePegawai' => $rek['KodePegawai'],
                            ]
                        ],
                    ]
                );

                $kompgaji = $this->crud->get_one_row(
                    [
                        'select' => 'j.KodeJabatan, j.NamaJabatan, um.Kriteria AS KriteriaUM, dl.Kriteria AS KriteriaDL, SUM(if(um.Kriteria = "Harian", um.NominalRp, 0)) AS UangMakanHarian, SUM(if(um.Kriteria = "Bulanan", um.NominalRp, 0)) AS UangMakanBulanan, SUM(if(dl.Kriteria = "Harian", dl.NominalRp, 0)) AS TunjanganDL',
                        'from' => 'mstjabatan j',
                        'join' => [
                            [
                                'table' => ' mstkomponengaji um',
                                'on' => "um.KodeJabatan = j.KodeJabatan AND um.JenisKomponen = 'UANG MAKAN'",
                                'param' => 'LEFT',
                            ],
                            [
                                'table' => ' mstkomponengaji dl',
                                'on' => "dl.KodeJabatan = j.KodeJabatan AND dl.JenisKomponen = 'TUNJANGAN DINAS LUAR'",
                                'param' => 'LEFT',
                            ],
                        ],
                        'where' => [['j.KodeJabatan' => $rek['KodeJabatan']]],
                    ]
                );

                if ($absensi && $kompgaji) {
                    $list_uangmakan = [
                        'NoUrut' => $NoUrut + 1,
                        'IDRekap' => $rek['IDRekap'],
                        'KodePegawai' => $rek['KodePegawai'],
                        'BagianPekerjaan' => 'Komponen Gaji',
                        'NamaPekerjaan' => 'Uang Makan',
                        'SatuanPekerjaan' => $kompgaji['KriteriaUM'],
                        'JmlPerolehan' => $kompgaji['UangMakanHarian'] > 0 ? round(($absensi['TotalHari'] - $absensi['Alpha']) * $kompgaji['UangMakanHarian']) : round($kompgaji['UangMakanBulanan']),
                        'CaraHitung' => 'Tambah'
                    ];

                    $list_dl = [
                        'NoUrut' => isset($list_uangmakan['NoUrut']) ? $list_uangmakan['NoUrut'] + 1 : $NoUrut + 1,
                        'IDRekap' => $rek['IDRekap'],
                        'KodePegawai' => $rek['KodePegawai'],
                        'BagianPekerjaan' => 'Komponen Gaji',
                        'NamaPekerjaan' => 'Tunjangan Dinas Luar',
                        'SatuanPekerjaan' => $kompgaji['KriteriaDL'],
                        'JmlPerolehan' => round($absensi['DL'] * $kompgaji['TunjanganDL']),
                        'CaraHitung' => 'Tambah'
                    ];

                    $item_uangmakan[] = $this->crud->insert($list_uangmakan, 'iteminsentifbulanan');
                    $item_dl[] = $this->crud->insert($list_dl, 'iteminsentifbulanan');
                }
            }

            // insert lembur & insentif jabatan
            foreach ($getrekap as $rek) {
                // code...
            }

            // insert pot telat & pot alpha
            foreach ($getrekap as $rek) {
                $getNoUrut = $this->db->from('iteminsentifbulanan')
                ->where([
                    'IDRekap' => $rek['IDRekap'],
                    'KodePegawai' => $rek['KodePegawai'],
                ])
                ->select('NoUrut')
                ->order_by('NoUrut', 'desc')
                ->get()->row();
                $NoUrut = isset($getNoUrut) ? (int)$getNoUrut->NoUrut : 0;

                $absensi = $this->crud->get_one_row(
                    [
                        'select' => 'a.KodePegawai, p.NamaPegawai, SUM(if(a.Keterangan = "Hadir", 1, 0)) AS Hadir, SUM(if(a.Keterangan = "Dinas Luar", 1, 0)) AS DL, SUM(if(a.Keterangan = "Sakit", 1, 0)) AS Sakit, SUM(if(a.Keterangan = "Izin", 1, 0)) AS Izin, SUM(if(a.Keterangan = "Alpha", 1, 0)) AS Alpha, COUNT(a.Keterangan) AS TotalHari, SUM(a.Telat) AS MenitPelanggaran',
                        'from' => 'absensipegawai a',
                        'join' => [
                            [
                                'table' => ' mstpegawai p',
                                'on' => "p.KodePegawai = a.KodePegawai",
                                'param' => 'LEFT',
                            ],
                        ],
                        'where' => [
                            [
                                'DATE_FORMAT(a.Tanggal, "%Y-%m") =' => $bulan,
                                'a.KodePegawai' => $rek['KodePegawai'],
                            ]
                        ],
                    ]
                );

                $kompgaji = $this->crud->get_one_row(
                    [
                        'select' => 'j.KodeJabatan, j.NamaJabatan, pt.Kriteria AS KriteriaTelat, pa.Kriteria AS KriteriaAlpha, SUM(if(pt.Kriteria = "Menit", pt.NominalRp, 0)) AS PotTelat, SUM(if(pa.Kriteria = "Harian", pa.NominalRp, 0)) AS PotAlpha',
                        'from' => 'mstjabatan j',
                        'join' => [
                            [
                                'table' => ' mstkomponengaji pt',
                                'on' => "pt.KodeJabatan = j.KodeJabatan AND pt.JenisKomponen = 'POT TELAT'",
                                'param' => 'LEFT',
                            ],
                            [
                                'table' => ' mstkomponengaji pa',
                                'on' => "pa.KodeJabatan = j.KodeJabatan AND pa.JenisKomponen = 'POT ALPHA'",
                                'param' => 'LEFT',
                            ],
                        ],
                        'where' => [['j.KodeJabatan' => $rek['KodeJabatan']]],
                    ]
                );

                if ($absensi && $kompgaji) {
                    $list_telat = [
                        'NoUrut' => $NoUrut + 1,
                        'IDRekap' => $rek['IDRekap'],
                        'KodePegawai' => $rek['KodePegawai'],
                        'BagianPekerjaan' => 'Komponen Gaji',
                        'NamaPekerjaan' => 'Potongan Telat',
                        'SatuanPekerjaan' => $kompgaji['KriteriaTelat'],
                        'JmlPerolehan' => round($absensi['MenitPelanggaran'] * $kompgaji['PotTelat']),
                        'CaraHitung' => 'Kurang'
                    ];

                    $list_alpha = [
                        'NoUrut' => isset($list_telat['NoUrut']) ? $list_telat['NoUrut'] + 1 : $NoUrut + 1,
                        'IDRekap' => $rek['IDRekap'],
                        'KodePegawai' => $rek['KodePegawai'],
                        'BagianPekerjaan' => 'Komponen Gaji',
                        'NamaPekerjaan' => 'Potongan Alpha',
                        'SatuanPekerjaan' => $kompgaji['KriteriaAlpha'],
                        'JmlPerolehan' => round($absensi['Alpha'] * $kompgaji['PotAlpha']),
                        'CaraHitung' => 'Kurang'
                    ];

                    $item_telat[] = $this->crud->insert($list_telat, 'iteminsentifbulanan');
                    $item_alpha[] = $this->crud->insert($list_alpha, 'iteminsentifbulanan');
                }
            }

            $aktivitas = $this->crud->get_rows(
                [
                    'select' => 'KodePegawai, JenisAktivitas, SUM(if(JenisAktivitas != "Ampas Dapur", Biaya, 0)) AS InsentifProduksi, SUM(if(JenisAktivitas = "Ampas Dapur", (Biaya * JmlAmpasDapur), 0)) AS InsentifAmpas, Satuan',
                    'from' => 'aktivitasproduksi',
                    'where' => [['DATE_FORMAT(TglAktivitas, "%Y-%m") =' => $bulan]],
                    'group_by' => 'KodePegawai, JenisAktivitas',
                ]
            );

            // insert aktivitas
            foreach ($aktivitas as $akt) {
                $rek = $this->crud->get_one_row(
                    [
                        'select' => '*',
                        'from' => 'rekapinsentifbulanan r',
                        'join' => [
                            [
                                'table' => ' mstpegawai p',
                                'on' => "p.KodePegawai = r.KodePegawai",
                                'param' => 'LEFT',
                            ],
                            [
                                'table' => ' mstjabatan j',
                                'on' => "j.KodeJabatan = p.KodeJabatan",
                                'param' => 'LEFT',
                            ],
                        ],
                        'where' => [['IDRekap' => str_replace('-', '', $bulan).'-'.$akt['KodePegawai']]],
                    ]
                );
                $getNoUrut = $this->db->from('iteminsentifbulanan')
                ->where([
                    'IDRekap' => $rek['IDRekap'],
                    'KodePegawai' => $rek['KodePegawai'],
                ])
                ->select('NoUrut')
                ->order_by('NoUrut', 'desc')
                ->get()->row();
                $NoUrut = isset($getNoUrut) ? (int)$getNoUrut->NoUrut : 0;

                $list_akt = [
                    'NoUrut' => $NoUrut + 1,
                    'IDRekap' => $rek['IDRekap'],
                    'KodePegawai' => $rek['KodePegawai'],
                    'BagianPekerjaan' => 'Aktivitas',
                    'NamaPekerjaan' => $akt['JenisAktivitas'],
                    'SatuanPekerjaan' => ($akt['JenisAktivitas'] == 'Ampas Dapur') ? 'Kilogram' : 'Produksi',
                    'JmlPerolehan' => ($akt['JenisAktivitas'] == 'Ampas Dapur') ? round($akt['InsentifAmpas']) : round($akt['InsentifProduksi']),
                    'CaraHitung' => 'Tambah'
                ];

                $itemaktivitas[] = $this->crud->insert($list_akt, 'iteminsentifbulanan');
            }

            // insert potongan pinjaman
            foreach ($getrekap as $rek) {
                $dtpinjam = $this->crud->get_one_row([
                    'select' => 'KodePegawai, SUM(NominalPinjam) as TotalPinjam',
                    'from' => 'trpinjamankaryawan',
                    'where' => [[
                        'KodePegawai' => $rek['KodePegawai'],
                        'IsHapus' => 0,
                        'DATE_FORMAT(TanggalPinjam, "%Y-%m") =' => $bulan
                    ]],
                ]);
                $totalpinjam = isset($dtpinjam['TotalPinjam']) ? $dtpinjam['TotalPinjam'] : 0;

                $getNoUrut = $this->db->from('iteminsentifbulanan')
                ->where([
                    'IDRekap' => $rek['IDRekap'],
                    'KodePegawai' => $rek['KodePegawai'],
                ])
                ->select('NoUrut')
                ->order_by('NoUrut', 'desc')
                ->get()->row();
                $NoUrut = isset($getNoUrut) ? (int)$getNoUrut->NoUrut : 0;

                $list_pjm = [
                    'NoUrut' => $NoUrut + 1,
                    'IDRekap' => $rek['IDRekap'],
                    'KodePegawai' => $rek['KodePegawai'],
                    'BagianPekerjaan' => 'Komponen Gaji',
                    'NamaPekerjaan' => 'Potongan Pinjaman',
                    'SatuanPekerjaan' => 'Bulanan',
                    'JmlPerolehan' => round($totalpinjam),
                    'CaraHitung' => 'Kurang'
                ];

                $itempinjaman[] = $this->crud->insert($list_pjm, 'iteminsentifbulanan');
            }

            // update nominaldibayar dan isdibayar
            $datapinjam = $this->crud->get_rows([
                'select' => '*',
                'from' => 'trpinjamankaryawan',
                'where' => [[
                    'IsHapus' => 0,
                    'DATE_FORMAT(TanggalPinjam, "%Y-%m") =' => $bulan
                ]],
            ]);
            if ($datapinjam) {
                foreach ($datapinjam as $dp) {
                    $update = [
                        'IsDibayar' => 1,
                        'NominalDibayar' => $dp['NominalPinjam']
                    ];

                    $updatetrpinjam[] = $this->crud->update($update, ['KodeTrPinjam' => $dp['KodeTrPinjam']], 'trpinjamankaryawan');
                }
            }

            if ($bulan) {
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => 'tambah',
                    'JenisTransaksi' => 'Transaksi Penerimaan Gaji',
                    'Description' => 'tambah data transaksi penerimaan gaji periode ' . strftime('%B %Y', strtotime($bulan))
                ]);
                echo json_encode([
                    'status' => true,
                    'msg'  => ("Berhasil Merekap Data")
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'msg'  => ("Gagal Merekap Data")
                ]);
            }
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ("Gagal merekap data, karena data transaksi penerimaan gaji pada periode " . strftime('%B %Y', strtotime($bulan)) . " sudah dibayarkan.")
            ]);
        }
    }

    public function cair()
    {
        setlocale(LC_ALL, 'IND');
        $bulan = $this->input->post('Bulan');

        $countrekap = $this->crud->get_count([
            'select' => 'IDRekap',
            'from' => 'rekapinsentifbulanan',
            'where' => [['Bulan' => $bulan]],
        ]);

        $rekap = $this->crud->get_rows([
            'select' => 'r.KodePegawai, r.IDRekap, r.KodeTahun, r.Bulan, r.TglRekap, r.NoTransBayar, SUM(if(i.CaraHitung = "Tambah", i.JmlPerolehan, 0)) - SUM(if(i.CaraHitung = "Kurang", i.JmlPerolehan, 0)) AS InsentifPegawai, p.NamaPegawai, p.KodeBank, p.NoRek',
            'from' => 'rekapinsentifbulanan r',
            'join' => [
                [
                    'table' => ' iteminsentifbulanan i',
                    'on' => "i.IDRekap = r.IDRekap AND i.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [[
                'r.Bulan' => $bulan,
                'r.IsTelahDibayarkan' => 0,
            ]],
            'group_by' => 'r.IDRekap',
        ]);

        $insentif = $this->crud->get_one_row([
            'select' => 'SUM(i.JmlPerolehan) AS TotalInsentifBulanan',
            'from' => 'iteminsentifbulanan i',
            'join' => [[
                'table' => ' rekapinsentifbulanan r',
                'on' => "r.IDRekap = i.IDRekap",
                'param' => 'INNER',
            ]],
            'where' => [[
                'r.Bulan' => $bulan,
                'r.IsTelahDibayarkan' => 0,
            ]],
        ]);
        $totalinsentif = $insentif['TotalInsentifBulanan'];

        $totalfee = 0;
        foreach ($rekap as $val) {
            $getbank = $this->lokasi->get_one_bank($val['KodeBank']);
            $totalfee += $getbank['fee'];
        }

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
                's.NamaTransaksi' => 'Penggajian',
                's.JenisTransaksi' => 'Tunai',
            ]],
        ]);

        $status_jurnal = $this->lokasi->setting_jurnal_status();

        $maintenance_status = $this->lokasi->get_maintenance();
        $maintenance = isset($maintenance_status['maintenance']) ? $maintenance_status['maintenance'] : null;

        $amount_balance = $this->lokasi->get_balance();
        $balance = isset($amount_balance['balance']) ? $amount_balance['balance'] : 0;

        if ($countrekap > 0) {
            if ($maintenance == false) { // check maintenance
                if ($balance > ($totalinsentif + $totalfee)) { // check balance
                    $result2 = null;
                    $result3 = null;
                    $tf = [];
                    foreach ($rekap as $key) {
                        $countkas = $this->crud->get_count([
                            'select' => '*',
                            'from' => 'transaksikas',
                            'where' => [[
                                'KodePegawai' => $key['KodePegawai'],
                                'IDRekap' => $key['IDRekap'],
                            ]],
                        ]);

                        $cekKas = $this->crud->get_one_row([
                            'select' => 'NoTransKas, KodePegawai, IDRekap',
                            'from' => 'transaksikas',
                            'where' => [['IDRekap' => $key['IDRekap']]],
                        ]);
                        if ($cekKas) { // delete di tabel transaksi kas jika ada
                            $deletekas[] = $this->crud->delete(['IDRekap' => $key['IDRekap']], 'transaksikas');
                        }

                        $prefix = "TRK-" . date("Ym");
                        $data = [
                            'KodeTahun'         => $key['KodeTahun'],
                            'NoTransKas'        => $this->crud->get_kode([
                                'select'    => 'RIGHT(NoTransKas, 7) AS KODE',
                                'from'      => 'transaksikas',
                                'where'     => [['LEFT(NoTransKas, 10) =' => $prefix]],
                                'limit'     => 1,
                                'order_by'  => 'NoTransKas DESC',
                                'prefix'    => $prefix
                            ]),
                            'TanggalTransaksi'  => date('Y-m-d H:i'),
                            'Uraian'            => 'Pembayaran gaji karyawan periode '.strftime('%B %Y', strtotime($bulan)),
                            'UserName'          => $this->session->userdata('Username'),
                            'TotalTransaksi'    => $key['InsentifPegawai'],
                            'IsDijurnalkan'     => 1,
                            'JenisTransaksiKas' => 'KAS KELUAR',
                            'KodePegawai'       => $key['KodePegawai'],
                            'IDRekap'           => $key['IDRekap']
                        ];
                        $data2 = [
                            'TotalInsentif' => $key['InsentifPegawai'],
                            // 'IsTelahDibayarkan' => 1,
                            'NoTransBayar' => $data['NoTransKas'],
                            'Keterangan' => 0
                        ];
                        $where = [
                            'KodePegawai' => $key['KodePegawai'],
                            'IDRekap' => $key['IDRekap']
                        ];
                        if ((int)$key['InsentifPegawai'] >= 10000) { // check minimum transfer
                            $tf = $this->lokasi->transfer($key['KodeBank'], $key['NoRek'], $key['InsentifPegawai']);
                            if ($tf['status']) { // check transfer status
                                $data2['Keterangan'] = $tf['id']; // mengambil disbursement id dari transfer flip

                                $result[] = $this->crud->insert_or_update($data, 'transaksikas'); // simpan data di tabel transaksi kas
                                $result2[] = $this->crud->update($data2, $where, 'rekapinsentifbulanan'); // update data di tabel rekap insentif bulanan

                                $prefix2 = "JRN-" . date("Ym");
                                $data3 = [
                                    'IDTransJurnal' => $this->crud->get_kode([
                                        'select'    => 'RIGHT(IDTransJurnal, 7) AS KODE',
                                        'from'      => 'transjurnal',
                                        'where'     => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                                        'limit'     => 1,
                                        'order_by'  => 'IDTransJurnal DESC',
                                        'prefix'    => $prefix2
                                    ]),
                                    'KodeTahun' => $key['KodeTahun'],
                                    'TglTransJurnal' => date("Y-m-d H:i"),
                                    'TipeJurnal' => "UMUM",
                                    'NarasiJurnal' => "Penggajian",
                                    'NominalTransaksi' => $data['TotalTransaksi'],
                                    'NoRefTrans' => $data['NoTransKas'],
                                    'UserName' => $this->session->userdata['UserName']
                                ];
                                $result3[] = $this->crud->insert_or_update($data3, 'transjurnal'); // simpan data di tabel transjurnal
                                if ($status_jurnal == 'on') { // jika status jurnal otomatis
                                    if ($getakun) {
                                        foreach ($getakun as $item) {
                                            $countakun = $this->crud->get_count([
                                                'select' => 'd.NoUrut, d.KodeAkun',
                                                'from' => 'detailsetakun d',
                                                'join' => [[
                                                    'table' => ' setakunjurnal s',
                                                    'on' => "s.KodeSetAkun = d.KodeSetAkun",
                                                    'param' => 'LEFT',
                                                ]],
                                                'where' => [[
                                                    's.NamaTransaksi' => $item['NamaTransaksi'],
                                                    's.JenisTransaksi' => $item['JenisTransaksi'],
                                                    'd.JenisJurnal' => $item['JenisJurnal'],
                                                ]],
                                            ]);

                                            $nilai = $data3['NominalTransaksi'] / $countakun;

                                            $data4 = [
                                                'NoUrut' => $item['NoUrut'],
                                                'IDTransJurnal' => $data3['IDTransJurnal'],
                                                'KodeTahun' => $data3['KodeTahun'],
                                                'KodeAkun' => $item['KodeAkun'],
                                                'NamaAkun' => $item['NamaAkun'],
                                                'Debet' => ($item['JenisJurnal'] == 'Debet') ? $nilai : 0,
                                                'Kredit' => ($item['JenisJurnal'] == 'Kredit') ? $nilai : 0,
                                                'Uraian' => "Penjurnalan otomatis untuk Penggajian Karyawan Periode ".strftime('%B %Y', strtotime($bulan))
                                            ];

                                            $result4[] = $this->crud->insert_or_update($data4, 'transjurnalitem'); // simpan data di tabel transjurnalitem
                                        }
                                    }
                                } else { // jika status jurnal manual

                                }
                            } else {
                                $result2 = null;
                                $result4 = null;
                                $data3['IDTransJurnal'] = null;
                            }
                        }
                    }

                    if ($result2) {
                        ## INSERT TO SERVER LOG
                        $this->logsrv->insert_log([
                            'Action' => 'edit',
                            'JenisTransaksi' => 'Transaksi Penerimaan Gaji',
                            'Description' => 'update data transaksi penerimaan gaji semua pegawai periode ' . strftime('%B %Y', strtotime($bulan))
                        ]);
                        echo json_encode([
                            'status' => true,
                            'msg'  => ("Berhasil menyimpan data."),
                            'idjurnal' => $data3['IDTransJurnal'],
                            'stj' => $status_jurnal
                        ]);
                    } else {
                        echo json_encode([
                            'status' => false,
                            'msg'  => ("Gagal menyimpan data, karena data transaksi penerimaan gaji pada periode ".strftime('%B %Y', strtotime($bulan))." sudah dibayarkan.")
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => false,
                        'msg'  => ("Saldo anda tidak mencukupi.")
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => false,
                    'msg'  => ("Sistem sedang maintenance.")
                ]);
            }
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ("Data transaksi penerimaan gaji pada periode ".strftime('%B %Y', strtotime($bulan))." belum direkap.")
            ]);
        }
    }

    public function bayarperpegawai()
    {
        $kode = $this->input->get('IDRekap');
        $kode2 = $this->input->get('Bulan');

        $rekap = $this->crud->get_one_row([
            'select' => 'r.KodePegawai, r.IDRekap, r.KodeTahun, r.Bulan, r.TglRekap, r.NoTransBayar, SUM(if(i.CaraHitung = "Tambah", i.JmlPerolehan, 0)) - SUM(if(i.CaraHitung = "Kurang", i.JmlPerolehan, 0)) AS InsentifPegawai, p.NamaPegawai, p.KodeBank, p.NoRek',
            'from' => 'rekapinsentifbulanan r',
            'join' => [
                [
                    'table' => ' iteminsentifbulanan i',
                    'on' => "i.IDRekap = r.IDRekap AND i.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['r.IDRekap' => $kode]],
        ]);

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
                's.NamaTransaksi' => 'Penggajian',
                's.JenisTransaksi' => 'Tunai',
            ]],
        ]);

        $countkas = $this->crud->get_count([
            'select' => '*',
            'from' => 'transaksikas',
            'where' => [['IDRekap' => $kode]],
        ]);

        $prefix = "TRK-" . date("Ym");
        $data = [
            'KodeTahun'         => $rekap['KodeTahun'],
            'NoTransKas'        => $this->crud->get_kode([
                'select'    => 'RIGHT(NoTransKas, 7) AS KODE',
                'from'      => 'transaksikas',
                'where'     => [['LEFT(NoTransKas, 10) =' => $prefix]],
                'limit'     => 1,
                'order_by'  => 'NoTransKas DESC',
                'prefix'    => $prefix
            ]),
            'TanggalTransaksi'  => date('Y-m-d H:i'),
            'Uraian'            => 'Pembayaran gaji karyawan periode '.strftime('%B %Y', strtotime($kode2)),
            'UserName'          => $this->session->userdata('Username'),
            'TotalTransaksi'    => $rekap['InsentifPegawai'],
            'IsDijurnalkan'     => 1,
            'JenisTransaksiKas' => 'KAS KELUAR',
            'KodePegawai'       => $rekap['KodePegawai'],
            'IDRekap'           => $rekap['IDRekap']
        ];
        $data2 = [
            'TotalInsentif' => $rekap['InsentifPegawai'],
            // 'IsTelahDibayarkan' => 1,
            'NoTransBayar' => $data['NoTransKas']
        ];
        $where = [
            'KodePegawai' => $rekap['KodePegawai'],
            'IDRekap' => $rekap['IDRekap']
        ];

        $status_jurnal = $this->lokasi->setting_jurnal_status();

        $maintenance_status = $this->lokasi->get_maintenance();
        $maintenance = isset($maintenance_status['maintenance']) ? $maintenance_status['maintenance'] : null;

        $amount_balance = $this->lokasi->get_balance();
        $balance = isset($amount_balance['balance']) ? $amount_balance['balance'] : 0;

        $getbank = $this->lokasi->get_one_bank($rekap['KodeBank']);
        $fee = $getbank['fee'];

        if ($maintenance == false) { // check maintenance
            if ($balance > ($rekap['InsentifPegawai'] + $fee)) { // check balance
                if (!($countkas > 0)) {
                    if ((int)$rekap['InsentifPegawai'] >= 10000) { // check minimum transfer
                        $tf = $this->lokasi->transfer($rekap['KodeBank'], $rekap['NoRek'], $rekap['InsentifPegawai']);
                        if ($tf['status']) { // check transfer status
                            $data2['Keterangan'] = $tf['id']; // mengambil disbursement id dari transfer flip

                            $result = $this->crud->insert_or_update($data, 'transaksikas'); // simpan data di tabel transaksi kas
                            $result2 = $this->crud->update($data2, $where, 'rekapinsentifbulanan'); // update data di tabel rekap insentif bulanan

                            $prefix2 = "JRN-" . date("Ym");
                            $data3 = [
                                'IDTransJurnal' => $this->crud->get_kode([
                                    'select'    => 'RIGHT(IDTransJurnal, 7) AS KODE',
                                    'from'      => 'transjurnal',
                                    'where'     => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
                                    'limit'     => 1,
                                    'order_by'  => 'IDTransJurnal DESC',
                                    'prefix'    => $prefix2
                                ]),
                                'KodeTahun' => $rekap['KodeTahun'],
                                'TglTransJurnal' => date("Y-m-d H:i"),
                                'TipeJurnal' => "UMUM",
                                'NarasiJurnal' => "Penggajian",
                                'NominalTransaksi' => $data['TotalTransaksi'],
                                'NoRefTrans' => $data['NoTransKas'],
                                'UserName' => $this->session->userdata['UserName']
                            ];

                            $result3 = $this->crud->insert_or_update($data3, 'transjurnal'); // simpan data di tabel transjurnal
                            if ($status_jurnal == 'on') {
                                if ($getakun) {
                                    foreach ($getakun as $item) {
                                        $countakun = $this->crud->get_count([
                                            'select' => 'd.NoUrut, d.KodeAkun',
                                            'from' => 'detailsetakun d',
                                            'join' => [[
                                                'table' => ' setakunjurnal s',
                                                'on' => "s.KodeSetAkun = d.KodeSetAkun",
                                                'param' => 'LEFT',
                                            ]],
                                            'where' => [[
                                                's.NamaTransaksi' => $item['NamaTransaksi'],
                                                's.JenisTransaksi' => $item['JenisTransaksi'],
                                                'd.JenisJurnal' => $item['JenisJurnal'],
                                            ]],
                                        ]);

                                        $nilai = $data3['NominalTransaksi'] / $countakun;

                                        $data4 = [
                                            'NoUrut' => $item['NoUrut'],
                                            'IDTransJurnal' => $data3['IDTransJurnal'],
                                            'KodeTahun' => $data3['KodeTahun'],
                                            'KodeAkun' => $item['KodeAkun'],
                                            'NamaAkun' => $item['NamaAkun'],
                                            'Debet' => ($item['JenisJurnal'] == 'Debet') ? $nilai : 0,
                                            'Kredit' => ($item['JenisJurnal'] == 'Kredit') ? $nilai : 0,
                                            'Uraian' => "Penjurnalan otomatis untuk Penggajian Karyawan Periode ".strftime('%B %Y', strtotime($kode2))
                                        ];

                                        $result4 = $this->crud->insert_or_update($data4, 'transjurnalitem'); // simpan data di tabel transjurnalitem
                                    }
                                }
                            }

                            ## INSERT TO SERVER LOG
                            $this->logsrv->insert_log([
                                'Action' => 'edit',
                                'JenisTransaksi' => 'Transaksi Penerimaan Gaji',
                                'Description' => 'update data transaksi penerimaan gaji ' . $rekap['KodePegawai'] . ' periode ' . strftime('%B %Y', strtotime($kode2))
                            ]);
                            echo json_encode([
                                'status' => true,
                                'msg'  => "Berhasil menyimpan data.",
                                'idjurnal' => $data3['IDTransJurnal'],
                                'stj' => $status_jurnal
                            ]);
                        } else {
                            echo json_encode([
                                'status' => false,
                                'msg'  => "Gagal menyimpan data."
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'status' => false,
                            'msg'  => "Gagal menyimpan data, minimal transfer Rp10.000"
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => false,
                        'msg'  => "Gagal menyimpan data, transaksi sudah dibayarkan."
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => false,
                    'msg'  => "Saldo anda tidak mencukupi."
                ]);
            }
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Sistem sedang maintenance."
            ]);
        }
    }

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[53]);
        $idrekap   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'penerimaangaji';
            $data['title'] = 'Detail Transaksi Penerimaan Gaji';
            $data['view'] = 'payroll/v_penerimaan_gaji_detail';
            $data['scripts'] = 'payroll/s_penerimaan_gaji_detail';

            $dtinduk = [
                'select' => 'r.IDRekap, r.KodePegawai, r.Bulan, p.NIP, p.NamaPegawai, p.KodeJabatan, j.NamaJabatan, SUM(if(i.CaraHitung = "Tambah", i.JmlPerolehan, 0)) - SUM(if(i.CaraHitung = "Kurang", i.JmlPerolehan, 0)) AS InsentifPegawai, r.UserName, u.ActualName',
                'from' => 'rekapinsentifbulanan r',
                'join' => [
                    [
                        'table' => ' mstpegawai p',
                        'on' => "p.KodePegawai = r.KodePegawai",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' mstjabatan j',
                        'on' => "j.KodeJabatan = p.KodeJabatan",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' iteminsentifbulanan i',
                        'on' => "i.IDRekap = r.IDRekap AND i.KodePegawai = r.KodePegawai",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' userlogin u',
                        'on' => "u.UserName = r.UserName",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['r.IDRekap' => $idrekap]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['IDRekap'] = $idrekap;

            $dtjurnal = $this->crud->get_one_row([
                'select' => 'k.NoTransKas, j.IDTransJurnal, j.NominalTransaksi',
                'from' => 'transaksikas k',
                'join' => [[
                    'table' => ' transjurnal j',
                    'on' => "j.NoRefTrans = k.NoTransKas",
                    'param' => 'LEFT'
                ]],
                'where' => [['k.IDRekap' => $idrekap]],
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
            $idrekap   = $this->input->get('idrekap');
            $configData['table'] = 'iteminsentifbulanan i';
            $configData['where'] = [['i.IDRekap'  => $idrekap]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.BagianPekerjaan LIKE '%$cari%' OR i.NamaPekerjaan LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' rekapinsentifbulanan r',
                    'on' => "r.IDRekap = i.IDRekap",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = i.KodePegawai",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.NoUrut', 'i.IDRekap', 'i.KodePegawai', 'i.BagianPekerjaan', 'i.NamaPekerjaan', 'i.SatuanPekerjaan', 'i.RateSatuan', 'i.JmlPerolehan', 'i.NominalInsentif', 'i.CaraHitung', 'i.Keterangan', 'r.KodeTahun', 'r.Bulan', 'r.TglRekap', 'p.NIP', 'p.NamaPegawai'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.NoUrut', 'i.IDRekap', 'i.KodePegawai', 'i.BagianPekerjaan', 'i.NamaPekerjaan', 'i.SatuanPekerjaan', 'i.RateSatuan', 'i.JmlPerolehan', 'i.NominalInsentif', 'i.CaraHitung', 'i.Keterangan', 'r.KodeTahun', 'r.Bulan', 'r.TglRekap', 'p.NIP', 'p.NamaPegawai',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 53; //FiturID di tabel serverfitur
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
                $temp['JmlPerolehan'] = ($temp['CaraHitung'] == 'Tambah') ? $temp['JmlPerolehan'] : '('.str_replace(',', '.', number_format($temp['JmlPerolehan'])).')';
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDRekap'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['IDRekap'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }
}
