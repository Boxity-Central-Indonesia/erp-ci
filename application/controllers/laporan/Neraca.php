<?php
defined('BASEPATH') or exit('No direct script access allowed');

class neraca extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transjurnalitem i';
        $this->load->model('M_Lokasi', 'lokasi');
        $this->load->model('M_Akun', 'akun');
        checkAccess($this->session->userdata('fiturview')[33]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[33]);
        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu'] = 'neraca';
        $data['title'] = 'Laporan Neraca';
        $data['view'] = 'laporan/v_neraca';
        $data['scripts'] = 'laporan/s_neraca';

        $dtakun = [
            'select' => '*',
            'from' => 'mstakun',
            'where' => [[
                'IsParent' => 0,
                'IsAktif' => 1,
            ]]
        ];
        $data['dtakun'] = $this->crud->get_rows($dtakun);

        $data['kodetahun'] = $this->akses->get_tahun_aktif();

        $level = $this->input->get('lvl') != '' ? $this->input->get('lvl') : 'semua';
        $data['level'] = $level;

        $data['bulan'] = $bulan = escape($this->input->get('bulan')) <> '' ? escape($this->input->get('bulan')) : Date("Y-m");
        $m = date('m', strtotime($bulan));
        $y = date('Y', strtotime($bulan));
        $d = cal_days_in_month(CAL_GREGORIAN,$m,$y);
        $firstdate = date('Y-m-d', strtotime($y . '-' . $m . '-' . '01'));
        $lastdate = date('Y-m-d', strtotime($y . '-' . $m . '-' . $d));

        $aktiva = $this->getsaldokelompok(1, $firstdate, $lastdate, $data['kodetahun']);
        $aktiva_lalu = $this->getsaldolalukelompok(1, ($data['kodetahun'])); // - 1
        $data['aktiva'] = $aktiva;
        $data['aktivalalu'] = $aktiva_lalu;
        $data['totalaktiva'] = $aktiva_lalu - $aktiva;
        $akun = $this->get_akun();
        $data_isi = [];
        foreach ($akun as $key => $value) {
            if ($value['KodeAkun'] < 4) {
                $data_isi[$key] = $value;
                $data_isi[$key]['SaldoAnak'] = $this->getsaldokelompok($value['KodeAkun'], $firstdate, $lastdate, $data['kodetahun']);
                $data_isi[$key]['SaldoAnakLalu'] = $this->getsaldolalukelompok($value['KodeAkun'], ($data['kodetahun'])); // - 1
                if ($level != 'semua' && (int)$level === 0) continue;
                $parent = $this->akun->get_parent($value['NamaAkun']);
                foreach ($parent as $i => $val) {
                    $parent[$i]['SaldoAnak'] = $this->getsaldoinduk($val['KodeAkun'], $firstdate, $lastdate, $data['kodetahun']);
                    $parent[$i]['SaldoAnakLalu'] = $this->getsaldolaluinduk($val['KodeAkun'], ($data['kodetahun'])); // - 1

                    if ($level != 'semua' && (int)$level === 1) continue;
                    $anak = $this->akun->get_induk($value['NamaAkun'], $val['KodeAkun']);

                    $parent[$i]['anak'] = $anak;
                    foreach ($parent[$i]['anak'] as $num => $sal) {
                        $saldo['SaldoAnak'] = $this->gettotal($sal['KodeAkun'], $firstdate, $lastdate, $data['kodetahun']);
                        $saldo['SaldoAnakLalu'] = $this->getsaldolalu($sal['KodeAkun'], ($data['kodetahun'])); // - 1
                        $parent[$i]['anak'][$num] = array_merge($parent[$i]['anak'][$num], $saldo);
                        if ($level != 'semua' && (int)$level === 2) continue;
                    }
                }

                $data_isi[$key]['anak'] = $parent;
            }
        }
        $data['data'] = $data_isi;

        if ($this->input->get('print') == 'cetak') {
            $data['src_url'] = base_url('laporan/neraca?lvl=') . $level . '&bulan=' . $bulan;
            $data['nama_level'] = ($level == 'semua') ? 'Semua_Level' : 'Level_' . $level;
            $this->load->library('Pdf');
            $this->load->view('laporan/cetak_laporan_neraca', $data);
        } else {
            loadview($data);
        }

    }

    public function get_akun()
    {
        $data = [
            [
                'KodeAkun' => 1,
                'NamaAkun' => 'AKTIVA'
            ],
            [
                'KodeAkun' => 2,
                'NamaAkun' => 'KEWAJIBAN'
            ],
            [
                'KodeAkun' => 3,
                'NamaAkun' => 'EKUITAS'
            ],
            [
                'KodeAkun' => 4,
                'NamaAkun' => 'PENDAPATAN'
            ],
            [
                'KodeAkun' => 5,
                'NamaAkun' => 'BEBAN PRODUKSI'
            ],
            [
                'KodeAkun' => 6,
                'NamaAkun' => 'BEBAN DILUAR PRODUKSI'
            ]
        ];

        return $data;
    }

    public function getsaldokelompok($kelompokakun, $tglawal, $tglakhir, $kodetahun) // menghitung total saldo grandparents
    {
        $neraca_akun = $this->get_setting_kode_akun('Neraca', 'Laba Rugi', 'Kredit');
        $akun_neraca = isset($neraca_akun) ? $neraca_akun['KodeAkun'] : '0.00';
        $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE LEFT(a.AkunInduk, 1) = '$kelompokakun'
            AND DATE(j.TglTransJurnal) <= '$tglakhir'
            AND j.KodeTahun = '$kodetahun'";
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalNominal'] : 0;
        if (substr($akun_neraca, 0, 1) != $kelompokakun) {
            $total = $resultsql;
        } else {
            // $labarugi       = $this->lokasi->get_laba_rugi_monthly($kodetahun, $tglawal, $tglakhir);
            // $hasil_labarugi = isset($labarugi) ? $labarugi : 0;
            $hasil_labarugi = $this->lokasi->get_laba_rugi($kodetahun, $tglakhir)['labarugi'];
            $total = $resultsql + $hasil_labarugi;
        }

        return $total;
    }

    public function getsaldolalukelompok($kelompokakun, $kodetahun) // menghitung total saldo grandparents tahun lalu
    {
        $tahunlalu = (int)$kodetahun - 1;
        $sql = "SELECT SUM(n.SaldoAkhir) AS SaldoAkhir
            FROM neracasaldo n
            JOIN mstakun a ON n.KodeAkun = a.KodeAkun
            WHERE LEFT(a.AkunInduk, 1) = '$kelompokakun'
            AND n.KodeTahun = '$tahunlalu'";
        $res = $this->db->query($sql)->row_array()['SaldoAkhir'];
        $result = isset($res) ? $res : 0;

        return $result;
    }

    public function getsaldoinduk($kodeakun, $tglawal, $tglakhir, $kodetahun) // menghitung total saldo parents
    {
        $neraca_akun = $this->get_setting_kode_akun('Neraca', 'Laba Rugi', 'Kredit');
        $akun_neraca = isset($neraca_akun) ? $neraca_akun['KodeAkun'] : '0.00';
        $check_children = $this->check_have_child($kodeakun);
        if (isset($check_children) && $check_children > 0) {
            $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
                FROM transjurnalitem i
                JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                JOIN mstakun a ON i.KodeAkun = a.KodeAkun
                WHERE a.AkunInduk = '$kodeakun'
                AND DATE(j.TglTransJurnal) <= '$tglakhir'
                AND j.KodeTahun = '$kodetahun'";
        } else {
            $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
                FROM transjurnalitem i
                JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                JOIN mstakun a ON i.KodeAkun = a.KodeAkun
                WHERE a.KodeAkun = '$kodeakun'
                AND DATE(j.TglTransJurnal) <= '$tglakhir'
                AND j.KodeTahun = '$kodetahun'";
        }
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalNominal'] : 0;
        if (substr($akun_neraca, 0, 4) != substr($kodeakun, 0, 4)) {
            $total = $resultsql;
        } else {
            // $labarugi       = $this->lokasi->get_laba_rugi_monthly($kodetahun, $tglawal, $tglakhir);
            // $hasil_labarugi = isset($labarugi) ? $labarugi : 0;
            $hasil_labarugi = $this->lokasi->get_laba_rugi($kodetahun, $tglakhir)['labarugi'];
            $total = $resultsql + $hasil_labarugi;
        }

        return $total;
    }

    public function getsaldolaluinduk($kodeakun, $kodetahun) // menghitung total saldo parents tahun lalu
    {
        $tahunlalu = (int)$kodetahun - 1;
        $check_children = $this->check_have_child($kodeakun);
        if (isset($check_children) && $check_children > 0) {
            $sql = "SELECT SUM(n.SaldoAkhir) AS SaldoAkhir
                FROM neracasaldo n
                JOIN mstakun a ON n.KodeAkun = a.KodeAkun
                WHERE LEFT(a.AkunInduk, 4) = LEFT('$kodeakun', 4)
                AND n.KodeTahun = '$tahunlalu'";
        } else {
            $sql = "SELECT SUM(n.SaldoAkhir) AS SaldoAkhir
                FROM neracasaldo n
                JOIN mstakun a ON n.KodeAkun = a.KodeAkun
                WHERE a.KodeAkun = '$kodeakun'
                AND n.KodeTahun = '$tahunlalu'";
        }
        $res = $this->db->query($sql)->row_array()['SaldoAkhir'];
        $result = isset($res) ? $res : 0;

        return $result;
    }

    public function gettotal($kodeakun, $tglawal, $tglakhir, $kodetahun) // menghitung total saldo children
    {
        $neraca_akun = $this->get_setting_kode_akun('Neraca', 'Laba Rugi', 'Kredit');
        $akun_neraca = isset($neraca_akun) ? $neraca_akun['KodeAkun'] : '0.00';
        $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE a.KodeAkun = '$kodeakun'
            AND DATE(j.TglTransJurnal) <= '$tglakhir'
            AND j.KodeTahun = '$kodetahun'";
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalNominal'] : 0;
        if ($akun_neraca != $kodeakun) {
            $total = $resultsql;
        } else {
            // $labarugi       = $this->lokasi->get_laba_rugi_monthly($kodetahun, $tglawal, $tglakhir);
            // $hasil_labarugi = isset($labarugi) ? $labarugi : 0;
            $hasil_labarugi = $this->lokasi->get_laba_rugi($kodetahun, $tglakhir)['labarugi'];
            $total = $resultsql + $hasil_labarugi;
        }

        return $total;
    }

    public function getsaldolalu($kodeakun, $kodetahun) // menghitung total saldo children tahun lalu
    {
        $tahunlalu = (int)$kodetahun - 1;
        $sql = "SELECT SUM(n.SaldoAkhir) AS SaldoAkhir
            FROM neracasaldo n
            JOIN mstakun a ON n.KodeAkun = a.KodeAkun
            WHERE a.KodeAkun = '$kodeakun'
            AND n.KodeTahun = '$tahunlalu'";
        $res = $this->db->query($sql)->row_array()['SaldoAkhir'];
        $result = isset($res) ? $res : 0;

        return $result;
    }

    public function check_have_child($kodeakun)
    {
        $data = $this->crud->get_count([
            'select' => 'KodeAkun',
            'from' => 'mstakun',
            'where' => [['AkunInduk' => $kodeakun]],
        ]);

        return $data;
    }

    public function get_setting_kode_akun($namatransaksi, $jenistransaksi, $jenisjurnal)
    {
        $data = $this->crud->get_one_row([
            'select' => 'd.KodeAkun, a.NamaAkun',
            'from' => 'detailsetakun d',
            'join' => [
                [
                    'table' => ' setakunjurnal s',
                    'on' => "s.KodeSetAkun = d.KodeSetAkun",
                    'param' => 'INNER',
                ],
                [
                    'table' => ' mstakun a',
                    'on' => "a.KodeAkun = d.KodeAkun",
                    'param' => 'INNER',
                ],
            ],
            'where' => [
                [
                    's.NamaTransaksi' => $namatransaksi,
                    's.JenisTransaksi' => $jenistransaksi,
                    'd.JenisJurnal' => $jenisjurnal,
                ]
            ],
        ]);

        return $data;
    }

    public function cetak()
    {
        $kodeakun = escape($this->uri->segment(4));
        $tglawal = escape($this->uri->segment(5));
        $tglakhir = escape($this->uri->segment(6));

        if ($kodeakun != null && $tglawal != null && $tglakhir != null) {
            $sql = [
                'select' => 'j.TglTransJurnal, j.IDTransJurnal, j.NoRefTrans, j.NarasiJurnal, i.Debet, i.Kredit',
                'from' => 'transjurnalitem i',
                'where' => [
                    [
                        'i.KodeAkun' => $kodeakun,
                    ],
                    "(DATE(j.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir')",
                ],
                'join' => [
                    [
                        'table' => ' transjurnal j',
                        'on' => "j.IDTransJurnal = i.IDTransJurnal",
                        'param' => 'INNER',
                    ],
                ],
            ];
            $data['model'] = $this->crud->get_rows($sql);

            $dataakun = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'mstakun',
                'where' => [['KodeAkun' => $kodeakun]],
            ]);
            $data['dataakun'] = $dataakun;

            $data['tglawal'] = date('d-m-Y', strtotime($tglawal));
            $data['tglakhir'] = date('d-m-Y', strtotime($tglakhir));

            $slda = $this->getAwalsaldo($kodeakun, $tglawal);
            $saldoawal = isset($slda) ? $slda : 0;
            $sldb = $this->getAkhirsaldo($kodeakun, $tglakhir);
            $saldoakhir = isset($sldb) ? $sldb : 0;
            $data['saldoawal'] = $saldoawal;
            $data['saldoakhir'] = $saldoakhir;

            $this->load->library('Pdf');
            $this->load->view('laporan/cetak_laporan_kas_besar', $data);
        } else {
            echo "Pilih tanggal transaksi & kode akun terlebih dahulu!";
        }
    }
}
