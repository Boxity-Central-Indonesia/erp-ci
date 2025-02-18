<?php
defined('BASEPATH') or exit('No direct script access allowed');

class buku_besar extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transjurnalitem i';
        checkAccess($this->session->userdata('fiturview')[67]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[67]);
        $data['title'] = 'Laporan Buku Besar';
        $data['menu'] = 'bukubesar';

        $data['kodetahun'] = $kodetahun = $this->akses->get_tahun_aktif();
        $tanggalan = $this->input->get('tgl');
        $tgl = explode(" - ", $tanggalan);
        $d1 = date('Y-m-d', strtotime('-29 days'));
        $d2 = date('Y-m-d');
        $tglawal = $tgl[0] != '' ? date('Y-m-d', strtotime($tgl[0])) : $d1;
        $tglakhir = isset($tgl[1]) ? date('Y-m-d', strtotime($tgl[1])) : $d2;
        $data['tglawal'] = date('d-m-Y', strtotime($tglawal));
        $data['tglakhir'] = date('d-m-Y', strtotime($tglakhir));
        $data['t_awal'] = $tglawal;
        $data['t_akhir'] = $tglakhir;

        $dtakun = $this->crud->get_rows([
            'select' => 'a.KodeAkun, a.NamaAkun, i.KodeTahun',
            'from' => 'mstakun a',
            'join' => [[
                'table' => 'transjurnalitem i',
                'on' => 'a.KodeAkun = i.KodeAkun',
                'param' => 'INNER'
            ]],
            'where' => [[
                'a.IsParent' => 0,
                'a.IsAktif' => 1,
            ]],
            'group_by' => 'a.KodeAkun'
        ]);

        $model = [];
        foreach ($dtakun as $key => $value) {
            $model[$key]                = $value;
            $model[$key]['SaldoAwal']   = $this->getAwalAkhirsaldo($value['KodeAkun'], $tglawal, '<', $kodetahun);
            $model[$key]['SaldoAkhir']  = $this->getAwalAkhirsaldo($value['KodeAkun'], $tglakhir, '<=', $kodetahun);
            $model[$key]['Item']        = $this->get_item_jurnal($value['KodeAkun'], $tglawal, $tglakhir, $kodetahun);
        }
        // die(json_encode($model));
        $data['data'] = $model;

        $jenis = $this->input->get('jenis');
        if ($jenis == 'cetak') {
            $tglurl = str_replace(' ', '+', $tanggalan);
            $data['src_url'] = base_url('laporan/buku_besar?tgl=') . $tglurl;

            $this->load->library('Pdf');
            $this->load->view('laporan/cetak_laporan_buku_besar', $data);
        } else {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['view'] = 'laporan/v_buku_besar';
            $data['scripts'] = 'laporan/s_buku_besar';
    
            loadview($data);
        }
    }

    public function get_item_jurnal($kodeakun, $tglawal, $tglakhir, $kodetahun)
    {
        $item = $this->crud->get_rows([
            'select' => 'i.NoUrut, i.IDTransJurnal, j.TglTransJurnal, j.NarasiJurnal, j.NoRefTrans, i.KodeAkun, i.Debet, i.Kredit',
            'from' => 'transjurnalitem i',
            'join' => [[
                'table' => 'transjurnal j',
                'on' => 'i.IDTransJurnal = j.IDTransJurnal',
                'param' => 'INNER'
            ]],
            'where' => [
                [
                    'i.KodeAkun' => $kodeakun,
                    'j.KodeTahun' => $kodetahun
                ], " (DATE(j.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir')"
            ]
        ]);

        $data = [];
        $saldoawal = $this->getAwalAkhirsaldo($kodeakun, $tglawal, '<', $kodetahun);
        $saldo = $saldoawal ? $saldoawal : 0;
        foreach ($item as $key => $value) {
            $data[$key] = $value;
            $saldo += $value['Debet'] - $value['Kredit'];
            $data[$key]['Saldo'] = $saldo;
        }

        return $data;
    }

    public function getAwalAkhirsaldo($kodeakun, $tgl, $sign, $kodetahun)
    {
        $sql = "SELECT SUM(i.Debet - i.Kredit) AS Saldo
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            WHERE i.KodeAkun = '$kodeakun'
            AND i.KodeTahun = '$kodetahun'
            AND DATE(j.TglTransJurnal) $sign '$tgl'
            AND j.TipeJurnal != 'PENUTUP'";
        $res = $this->db->query($sql)->row_array();
        $saldojurnal = $res ? $res['Saldo'] : 0;

        $tahunlalu = (int)$kodetahun - 1;
        $saldoneraca = $this->get_saldo_neraca($kodeakun, $tahunlalu);

        $nilaisaldo = $saldojurnal + $saldoneraca;
        return $nilaisaldo;
    }

    public function get_saldo_neraca($kodeakun, $kodetahun)
    {
        $sql = "SELECT SUM(COALESCE(SaldoDebet, 0) - COALESCE(SaldoKredit, 0)) AS Saldo
            FROM neracasaldo
            WHERE KodeAkun = '$kodeakun'
            AND KodeTahun = '$kodetahun'";
        $res = $this->db->query($sql)->row_array();
        $result = $res ? $res['Saldo'] : 0;

        return $result;
    }
}
