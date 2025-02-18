<?php
defined('BASEPATH') or exit('No direct script access allowed');

class arus_kas extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[34]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[34]);
        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu'] = 'aruskas';
        $data['title'] = 'Laporan Arus Kas';
        $data['view'] = 'laporan/v_arus_kas';
        $data['scripts'] = 'laporan/s_arus_kas';

        $tgl_filter = escape($this->input->get('tgl')) != '' ? escape($this->input->get('tgl')) : date("01-m-Y").' - '.date("d-m-Y");
        $tgl        = explode(" - ", $tgl_filter);
        $tglawal    = date('Y-m-d', strtotime($tgl[0]));
        $tglakhir   = date('Y-m-d', strtotime($tgl[1]));

        $data['tglawal'] = date("d-m-Y", strtotime($tglawal));
        $data['tglakhir'] = date("d-m-Y", strtotime($tglakhir));

        $kategori_op = 'Arus Kas Operational';
        $item_masuk_op = $this->getArusKasMasuk($kategori_op, $tglawal, $tglakhir);
        $masuk_op = [];
        foreach ($item_masuk_op as $key) {
            if ($key['Nominal'] > 0) {
                $masuk_op[] = $key;
            }
        }
        $item_keluar_op = $this->getArusKasKeluar($kategori_op, $tglawal, $tglakhir);
        $keluar_op = [];
        foreach ($item_keluar_op as $key) {
            if ($key['Nominal'] > 0) {
                $keluar_op[] = $key;
            }
        }
        $data['masuk_op'] = $masuk_op;
        $data['keluar_op'] = $keluar_op;

        $kategori_inv = 'Arus Kas Investasi';
        $item_masuk_inv = $this->getArusKasMasuk($kategori_inv, $tglawal, $tglakhir);
        $masuk_inv = [];
        foreach ($item_masuk_inv as $key) {
            if ($key['Nominal'] > 0) {
                $masuk_inv[] = $key;
            }
        }
        $item_keluar_inv = $this->getArusKasKeluar($kategori_inv, $tglawal, $tglakhir);
        $keluar_inv = [];
        foreach ($item_keluar_inv as $key) {
            if ($key['Nominal'] > 0) {
                $keluar_inv[] = $key;
            }
        }
        $data['masuk_inv'] = $masuk_inv;
        $data['keluar_inv'] = $keluar_inv;

        $kategori_bi = 'Arus Kas Pembiayaan';
        $item_masuk_bi = $this->getArusKasMasuk($kategori_bi, $tglawal, $tglakhir);
        $masuk_bi = [];
        foreach ($item_masuk_bi as $key) {
            if ($key['Nominal'] > 0) {
                $masuk_bi[] = $key;
            }
        }
        $item_keluar_bi = $this->getArusKasKeluar($kategori_bi, $tglawal, $tglakhir);
        $keluar_bi = [];
        foreach ($item_keluar_bi as $key) {
            if ($key['Nominal'] > 0) {
                $keluar_bi[] = $key;
            }
        }
        $data['masuk_bi'] = $masuk_bi;
        $data['keluar_bi'] = $keluar_bi;

        $masuk = $this->getSaldoMasuk($tglawal);
        $saldoMasuk = isset($masuk) ? $masuk : 0;
        $keluar = $this->getSaldoKeluar($tglawal);
        $saldoKeluar = isset($keluar) ? $keluar : 0;
        $saldoawal = $saldoMasuk - $saldoKeluar;
        $data['saldoawal'] = $saldoawal;

        loadview($data);
    }

    function getArusKasMasuk($kategori, $tglawal, $tglakhir)
    {
        $sql = "SELECT i.NoUrut, j.IDTransJurnal, j.NoRefTrans, i.KodeAkun, i.NamaAkun,
            if(k.NoTransKas IS NOT NULL, SUM(if(k.JenisTransaksiKas = 'DP PENJUALAN' OR k.JenisTransaksiKas = 'TERIMA PIUTANG' OR k.JenisTransaksiKas = 'KAS MASUK', i.Kredit, 0)), SUM(if(LEFT(j.NoRefTrans, 3) = 'TJL', i.Kredit, 0))) Nominal
            FROM transjurnal j
            JOIN transjurnalitem i ON j.IDTransJurnal = i.IDTransJurnal AND i.Kredit > 0
            LEFT JOIN transaksikas k ON j.NoRefTrans = k.NoTransKas
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE a.KategoriArusKas = '$kategori'
            AND DATE(j.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir'
            AND j.TipeJurnal = 'UMUM'
            GROUP BY j.IDTransJurnal, i.NoUrut
            ORDER BY j.IDTransJurnal, i.NoUrut";
        return $this->db->query($sql)->result_array();
    }

    function getArusKasKeluar($kategori, $tglawal, $tglakhir)
    {
        $sql = "SELECT i.NoUrut, j.IDTransJurnal, j.NoRefTrans, i.KodeAkun, i.NamaAkun,
            if(k.NoTransKas IS NOT NULL, SUM(if(k.JenisTransaksiKas = 'DP PEMBELIAN' OR k.JenisTransaksiKas = 'BAYAR HUTANG' OR k.JenisTransaksiKas = 'KAS KELUAR' OR k.JenisTransaksiKas = 'BIAYA PRODUKSI', i.Debet, 0)), SUM(if(LEFT(j.NoRefTrans, 3) = 'TBL', i.Debet, 0))) AS Nominal
            FROM transjurnal j
            JOIN transjurnalitem i ON j.IDTransJurnal = i.IDTransJurnal AND i.Debet > 0
            LEFT JOIN transaksikas k ON j.NoRefTrans = k.NoTransKas
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE a.KategoriArusKas = '$kategori'
            AND DATE(j.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir'
            AND j.TipeJurnal = 'UMUM'
            GROUP BY j.IDTransJurnal, i.NoUrut
            ORDER BY j.IDTransJurnal, i.NoUrut";
        return $this->db->query($sql)->result_array();
    }

    function getSaldoMasuk($tglawal)
    {
        $sql = "SELECT if(k.NoTransKas IS NOT NULL, SUM(if(k.JenisTransaksiKas = 'DP PENJUALAN' OR k.JenisTransaksiKas = 'TERIMA PIUTANG' OR k.JenisTransaksiKas = 'KAS MASUK', im.Kredit, 0)), 0) + if(k.NoTransKas IS NULL, 0, SUM(if(LEFT(j.NoRefTrans, 3) = 'TJL', im.Kredit, 0))) AS Saldo
            FROM transjurnal j
            JOIN transjurnalitem im ON j.IDTransJurnal = im.IDTransJurnal AND im.Kredit > 0
            LEFT JOIN transaksikas k ON j.NoRefTrans = k.NoTransKas
            WHERE DATE(j.TglTransJurnal) < '$tglawal'
            AND j.TipeJurnal = 'UMUM'";
        return $this->db->query($sql)->row_array()['Saldo'];
    }

    function getSaldoKeluar($tglawal)
    {
        $sql = "SELECT if(k.NoTransKas IS NOT NULL, SUM(if(k.JenisTransaksiKas = 'DP PEMBELIAN' OR k.JenisTransaksiKas = 'BAYAR HUTANG' OR k.JenisTransaksiKas = 'KAS KELUAR' OR k.JenisTransaksiKas = 'BIAYA PRODUKSI', ik.Debet, 0)), 0) + if(k.NoTransKas IS NULL, 0, SUM(if(LEFT(j.NoRefTrans, 3) = 'TBL', ik.Debet, 0))) AS Saldo
            FROM transjurnal j
            JOIN transjurnalitem ik ON j.IDTransJurnal = ik.IDTransJurnal AND ik.Debet > 0
            LEFT JOIN transaksikas k ON j.NoRefTrans = k.NoTransKas
            WHERE DATE(j.TglTransJurnal) < '$tglawal'
            AND j.TipeJurnal = 'UMUM'";
        return $this->db->query($sql)->row_array()['Saldo'];
    }

    public function cetak()
    {
        $tgl_awal = escape($this->uri->segment(4));
        $tgl_akhir = escape($this->uri->segment(5));
        $tglawal    = date('Y-m-d', strtotime($tgl_awal));
        $tglakhir   = date('Y-m-d', strtotime($tgl_akhir));

        $data['tglawal'] = $tgl_awal;
        $data['tglakhir'] = $tgl_akhir;

        $data['src_url'] = base_url('laporan/arus_kas?tgl=') . $this->uri->segment(4) . '+-+' . $this->uri->segment(5);

        $kategori_op = 'Arus Kas Operational';
        $item_masuk_op = $this->getArusKasMasuk($kategori_op, $tglawal, $tglakhir);
        $masuk_op = [];
        foreach ($item_masuk_op as $key) {
            if ($key['Nominal'] > 0) {
                $masuk_op[] = $key;
            }
        }
        $item_keluar_op = $this->getArusKasKeluar($kategori_op, $tglawal, $tglakhir);
        $keluar_op = [];
        foreach ($item_keluar_op as $key) {
            if ($key['Nominal'] > 0) {
                $keluar_op[] = $key;
            }
        }
        $data['masuk_op'] = $masuk_op;
        $data['keluar_op'] = $keluar_op;

        $kategori_inv = 'Arus Kas Investasi';
        $item_masuk_inv = $this->getArusKasMasuk($kategori_inv, $tglawal, $tglakhir);
        $masuk_inv = [];
        foreach ($item_masuk_inv as $key) {
            if ($key['Nominal'] > 0) {
                $masuk_inv[] = $key;
            }
        }
        $item_keluar_inv = $this->getArusKasKeluar($kategori_inv, $tglawal, $tglakhir);
        $keluar_inv = [];
        foreach ($item_keluar_inv as $key) {
            if ($key['Nominal'] > 0) {
                $keluar_inv[] = $key;
            }
        }
        $data['masuk_inv'] = $masuk_inv;
        $data['keluar_inv'] = $keluar_inv;

        $kategori_bi = 'Arus Kas Pembiayaan';
        $item_masuk_bi = $this->getArusKasMasuk($kategori_bi, $tglawal, $tglakhir);
        $masuk_bi = [];
        foreach ($item_masuk_bi as $key) {
            if ($key['Nominal'] > 0) {
                $masuk_bi[] = $key;
            }
        }
        $item_keluar_bi = $this->getArusKasKeluar($kategori_bi, $tglawal, $tglakhir);
        $keluar_bi = [];
        foreach ($item_keluar_bi as $key) {
            if ($key['Nominal'] > 0) {
                $keluar_bi[] = $key;
            }
        }
        $data['masuk_bi'] = $masuk_bi;
        $data['keluar_bi'] = $keluar_bi;

        $masuk = $this->getSaldoMasuk($tglawal);
        $saldoMasuk = isset($masuk) ? $masuk : 0;
        $keluar = $this->getSaldoKeluar($tglawal);
        $saldoKeluar = isset($keluar) ? $keluar : 0;
        $saldoawal = $saldoMasuk - $saldoKeluar;
        $data['saldoawal'] = $saldoawal;

        $this->load->library('Pdf');
        $this->load->view('laporan/cetak_laporan_arus_kas', $data);
    }
}
