<?php
defined('BASEPATH') or exit('No direct script access allowed');

class laba_rugi extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[32]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[32]);
        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu'] = 'labarugi';
        $data['title'] = 'Laporan Laba Rugi';
        $data['view'] = 'laporan/v_laba_rugi';
        $data['scripts'] = 'laporan/s_laba_rugi';

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
        $firstdate  = date('Y-m-d', strtotime($y . '-' . $m . '-' . '01'));
        $lastdate   = date('Y-m-d', strtotime($y . '-' . $m . '-' . $d));
        $date = new DateTime($firstdate);
        $lastdate_lastmonth = $date->modify("last day of previous month")->format("Y-m-d");
        $explode = explode("-", $lastdate_lastmonth);
        $firstdate_lastmonth = $explode[0] . '-' . $explode[1] . '-01';

        $data['TotalHPP'] = $this->lokasi->hitung_hpp_total($firstdate, $lastdate, $lastdate_lastmonth);

        $akun = $this->get_akun();
        $data_isi = [];
        foreach ($akun as $key => $value) {
            if ($value['KodeAkun'] > 3) {
                $data_isi[$key] = $value;
                $data_isi[$key]['SaldoAnak'] = $this->getsaldokelompokrange($value['KodeAkun'], $firstdate, $lastdate, $data['kodetahun']);
                $data_isi[$key]['SaldoAnakPenyesuaian'] = $this->getsaldokelompokrangepenyesuaian($value['KodeAkun'], $firstdate, $lastdate, $data['kodetahun']);
                if ($level != 'semua' && (int)$level === 0) continue;
                $parent = $this->akun->get_parent($value['NamaAkun']);
                foreach ($parent as $i => $val) {
                    $parent[$i]['SaldoAnak'] = $this->getsaldoindukrange($val['KodeAkun'], $firstdate, $lastdate, $data['kodetahun']);
                    $parent[$i]['SaldoAnakPenyesuaian'] = $this->getsaldoindukrangepenyesuaian($val['KodeAkun'], $firstdate, $lastdate, $data['kodetahun']);

                    if ($level != 'semua' && (int)$level === 1) continue;
                    $anak = $this->akun->get_induk($value['NamaAkun'], $val['KodeAkun']);

                    $parent[$i]['anak'] = $anak;
                    foreach ($parent[$i]['anak'] as $num => $sal) {
                        $saldo['SaldoAnak'] = $this->gettotalrange($sal['KodeAkun'], $firstdate, $lastdate, $data['kodetahun']);
                        $saldo['SaldoAnakPenyesuaian'] = $this->gettotalrangepenyesuaian($sal['KodeAkun'], $firstdate, $lastdate, $data['kodetahun']);
                        $parent[$i]['anak'][$num] = array_merge($parent[$i]['anak'][$num], $saldo);
                        if ($level != 'semua' && (int)$level === 2) continue;
                    }
                }

                $data_isi[$key]['anak'] = $parent;
            }
        }
        $data['data'] = $data_isi;

        if ($this->input->get('print') == 'cetak') {
            $data['src_url'] = base_url('laporan/laba_rugi?lvl=') . $level . '&bulan=' . $bulan;
            $data['nama_level'] = ($level == 'semua') ? 'Semua_Level' : 'Level_' . $level;
            $this->load->library('Pdf');
            $this->load->view('laporan/cetak_laporan_laba_rugi', $data);
        } else {
            loadview($data);
        }
    }

    public function hitung_hpp_total($tglawal, $tglakhir, $tglawallalu, $tglakhirlalu)
    {

        $sql1 = "SELECT COALESCE(SUM(i.Debet), 0) - COALESCE(SUM(i.Kredit), 0) AS Total
            FROM transjurnalitem i
            JOIN transjurnal t ON i.IDTransJurnal = t.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE a.IsPersediaan = 1
            AND DATE(t.TglTransJurnal) BETWEEN '$tglawallalu' AND '$tglakhirlalu'";
        $persediaanawal = $this->db->query($sql1)->row_array()['Total'];

        $sql2 = "SELECT COALESCE(SUM(i.Debet), 0) - COALESCE(SUM(i.Kredit), 0) AS Total
            FROM transjurnalitem i
            JOIN transjurnal t ON i.IDTransJurnal = t.IDTransJurnal
            WHERE LEFT(i.KodeAkun, 1) = 7
            AND DATE(t.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir'";
        $pembelianbersih = $this->db->query($sql2)->row_array()['Total'];

        $sql3 = "SELECT COALESCE(SUM(i.Debet), 0) - COALESCE(SUM(i.Kredit), 0) AS Total
            FROM transjurnalitem i
            JOIN transjurnal t ON i.IDTransJurnal = t.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE a.IsPersediaan = 1
            AND DATE(t.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir'";
        $persediaanakhir = $this->db->query($sql3)->row_array()['Total'];

        $totalhpp = $persediaanawal + $pembelianbersih - $persediaanakhir;
        return $totalhpp;
    }

    public function hitung_persediaan($tgl, $sign, $kodetahun)
    {
        $tahunlalu = (int)$kodetahun - 1;
        $sql = "SELECT COALESCE(SUM(i.Debet), 0) - COALESCE(SUM(i.Kredit), 0) AS Total
            FROM transjurnalitem i
            JOIN transjurnal t ON i.IDTransJurnal = t.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE a.IsPersediaan = 1
            AND DATE(t.TglTransJurnal) $sign '$tgl'
            AND t.KodeTahun = '$kodetahun'";
        $persediaan_tahun_ini = $this->db->query($sql)->row_array()['Total'];

        $sqllalu = "SELECT COALESCE(SUM(n.SaldoAkhir), 0) AS Total
            FROM neracasaldo n
            JOIN mstakun a ON n.KodeAkun = a.KodeAkun
            WHERE n.Kodetahun = '$tahunlalu'
            AND a.IsPersediaan = 1";
        $persediaan_tahun_lalu = $this->db->query($sqllalu)->row_array()['Total'];

        $result = $persediaan_tahun_ini + $persediaan_tahun_lalu;
        return $result;
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

    public function getsaldokelompok($kelompokakun, $tgl_akhir, $kodetahun) // menghitung total saldo grandparents
    {
        $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE LEFT(a.AkunInduk, 1) = '$kelompokakun'
            AND j.TipeJurnal = 'UMUM'
            AND DATE(j.TglTransJurnal) <= '$tgl_akhir'
            AND j.KodeTahun = '$kodetahun'";
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalNominal'] : 0;

        return $resultsql;
    }

    public function getsaldokelompokpenyesuaian($kelompokakun, $tgl_akhir, $kodetahun) // menghitung total saldo grandparents
    {
        $sql = "SELECT SUM(COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0)) AS TotalPenyesuaian
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE LEFT(a.AkunInduk, 1) = '$kelompokakun'
            AND j.TipeJurnal = 'PENYESUAIAN'
            AND DATE(j.TglTransJurnal) <= '$tgl_akhir'
            AND j.KodeTahun = '$kodetahun'";
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalPenyesuaian'] : 0;

        return $resultsql;
    }

    public function getsaldokelompokrange($kelompokakun, $tgl_awal, $tgl_akhir, $kodetahun) // menghitung total saldo grandparents
    {
        $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE LEFT(a.AkunInduk, 1) = '$kelompokakun'
            AND j.TipeJurnal = 'UMUM'
            AND DATE(j.TglTransJurnal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
            AND j.KodeTahun = '$kodetahun'";
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalNominal'] : 0;

        return $resultsql;
    }

    public function getsaldokelompokrangepenyesuaian($kelompokakun, $tgl_awal, $tgl_akhir, $kodetahun) // menghitung total saldo grandparents
    {
        $sql = "SELECT SUM(COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0)) AS TotalPenyesuaian
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE LEFT(a.AkunInduk, 1) = '$kelompokakun'
            AND j.TipeJurnal = 'PENYESUAIAN'
            AND DATE(j.TglTransJurnal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
            AND j.KodeTahun = '$kodetahun'";
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalPenyesuaian'] : 0;

        return $resultsql;
    }

    public function getsaldoinduk($kodeakun, $tgl_akhir, $kodetahun) // menghitung total saldo parents
    {
        $check_children = $this->check_have_child($kodeakun);
        if (isset($check_children) && $check_children > 0) {
            $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
                FROM transjurnalitem i
                JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                JOIN mstakun a ON i.KodeAkun = a.KodeAkun
                WHERE a.AkunInduk = '$kodeakun'
                AND j.TipeJurnal = 'UMUM'
                AND DATE(j.TglTransJurnal) <= '$tgl_akhir'
                AND j.KodeTahun = '$kodetahun'";
        } else {
            $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
                FROM transjurnalitem i
                JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                JOIN mstakun a ON i.KodeAkun = a.KodeAkun
                WHERE a.KodeAkun = '$kodeakun'
                AND j.TipeJurnal = 'UMUM'
                AND DATE(j.TglTransJurnal) <= '$tgl_akhir'
                AND j.KodeTahun = '$kodetahun'";
        }
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalNominal'] : 0;

        return $resultsql;
    }

    public function getsaldoindukpenyesuaian($kodeakun, $tgl_akhir, $kodetahun) // menghitung total saldo parents
    {
        $check_children = $this->check_have_child($kodeakun);
        if (isset($check_children) && $check_children > 0) {
            $sql = "SELECT SUM(COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0)) AS TotalPenyesuaian
                FROM transjurnalitem i
                JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                JOIN mstakun a ON i.KodeAkun = a.KodeAkun
                WHERE a.AkunInduk = '$kodeakun'
                AND j.TipeJurnal = 'PENYESUAIAN'
                AND DATE(j.TglTransJurnal) <= '$tgl_akhir'
                AND j.KodeTahun = '$kodetahun'";
        } else {
            $sql = "SELECT SUM(COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0)) AS TotalPenyesuaian
                FROM transjurnalitem i
                JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                JOIN mstakun a ON i.KodeAkun = a.KodeAkun
                WHERE a.KodeAkun = '$kodeakun'
                AND j.TipeJurnal = 'PENYESUAIAN'
                AND DATE(j.TglTransJurnal) <= '$tgl_akhir'
                AND j.KodeTahun = '$kodetahun'";
        }
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalPenyesuaian'] : 0;

        return $resultsql;
    }

    public function getsaldoindukrange($kodeakun, $tgl_awal, $tgl_akhir, $kodetahun) // menghitung total saldo parents
    {
        $check_children = $this->check_have_child($kodeakun);
        if (isset($check_children) && $check_children > 0) {
            $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
                FROM transjurnalitem i
                JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                JOIN mstakun a ON i.KodeAkun = a.KodeAkun
                WHERE a.AkunInduk = '$kodeakun'
                AND j.TipeJurnal = 'UMUM'
                AND DATE(j.TglTransJurnal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                AND j.KodeTahun = '$kodetahun'";
        } else {
            $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
                FROM transjurnalitem i
                JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                JOIN mstakun a ON i.KodeAkun = a.KodeAkun
                WHERE a.KodeAkun = '$kodeakun'
                AND j.TipeJurnal = 'UMUM'
                AND DATE(j.TglTransJurnal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                AND j.KodeTahun = '$kodetahun'";
        }
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalNominal'] : 0;

        return $resultsql;
    }

    public function getsaldoindukrangepenyesuaian($kodeakun, $tgl_awal, $tgl_akhir, $kodetahun) // menghitung total saldo parents
    {
        $check_children = $this->check_have_child($kodeakun);
        if (isset($check_children) && $check_children > 0) {
            $sql = "SELECT SUM(COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0)) AS TotalPenyesuaian
                FROM transjurnalitem i
                JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                JOIN mstakun a ON i.KodeAkun = a.KodeAkun
                WHERE a.AkunInduk = '$kodeakun'
                AND j.TipeJurnal = 'PENYESUAIAN'
                AND DATE(j.TglTransJurnal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                AND j.KodeTahun = '$kodetahun'";
        } else {
            $sql = "SELECT SUM(COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0)) AS TotalPenyesuaian
                FROM transjurnalitem i
                JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                JOIN mstakun a ON i.KodeAkun = a.KodeAkun
                WHERE a.KodeAkun = '$kodeakun'
                AND j.TipeJurnal = 'PENYESUAIAN'
                AND DATE(j.TglTransJurnal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                AND j.KodeTahun = '$kodetahun'";
        }
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalPenyesuaian'] : 0;

        return $resultsql;
    }

    public function gettotal($kodeakun, $tgl_akhir, $kodetahun) // menghitung total saldo children
    {
        $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE a.KodeAkun = '$kodeakun'
            AND j.TipeJurnal = 'UMUM'
            AND DATE(j.TglTransJurnal) <= '$tgl_akhir'
            AND j.KodeTahun = '$kodetahun'";
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalNominal'] : 0;

        return $resultsql;
    }

    public function gettotalpenyesuaian($kodeakun, $tgl_akhir, $kodetahun) // menghitung total saldo children
    {
        $sql = "SELECT SUM(COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0)) AS TotalNominal
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE a.KodeAkun = '$kodeakun'
            AND j.TipeJurnal = 'PENYESUAIAN'
            AND DATE(j.TglTransJurnal) <= '$tgl_akhir'
            AND j.KodeTahun = '$kodetahun'";
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalNominal'] : 0;

        return $resultsql;
    }

    public function gettotalrange($kodeakun, $tgl_awal, $tgl_akhir, $kodetahun) // menghitung total saldo children
    {
        $sql = "SELECT SUM(IF(a.JenisAkun = 'Debit', COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0), COALESCE(i.Kredit, 0) - COALESCE(i.Debet, 0))) AS TotalNominal
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE a.KodeAkun = '$kodeakun'
            AND j.TipeJurnal = 'UMUM'
            AND DATE(j.TglTransJurnal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
            AND j.KodeTahun = '$kodetahun'";
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalNominal'] : 0;

        return $resultsql;
    }

    public function gettotalrangepenyesuaian($kodeakun, $tgl_awal, $tgl_akhir, $kodetahun) // menghitung total saldo children
    {
        $sql = "SELECT SUM(COALESCE(i.Debet, 0) - COALESCE(i.Kredit, 0)) AS TotalNominal
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE a.KodeAkun = '$kodeakun'
            AND j.TipeJurnal = 'PENYESUAIAN'
            AND DATE(j.TglTransJurnal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
            AND j.KodeTahun = '$kodetahun'";
        $hasilsql = $this->db->query($sql)->row_array();
        $resultsql = isset($hasilsql) ? $hasilsql['TotalNominal'] : 0;

        return $resultsql;
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
