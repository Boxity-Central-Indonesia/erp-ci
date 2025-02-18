<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Lokasi extends CI_Model
{

    public function get_data_provinsi_list()
    {
        return $this->db->select('KodeProv, NamaProv')
            ->from('mstprov')
            ->order_by("NamaProv", "asc")
            ->get()
            ->result();
    }

    public function get_data_kab_list($KodeProv)
    {
        return $this->db->select('NamaKab, KodeKab, KodeProv')
            ->from('mstkab')
            ->where(array('KodeProv' => $KodeProv))
            ->order_by("NamaKab", "asc")
            ->get()
            ->result();
    }

    public function get_data_kec_list($KodeKab)
    {
        return $this->db->select('NamaKec, KodeKec, KodeKab, KodeProv')
            ->from('mstkec')
            ->where(array('KodeKab' => $KodeKab))
            ->order_by("NamaKec", "asc")
            ->get()
            ->result();
    }

    public function get_data_desa_list($KodeKec)
    {
        return $this->db->select('KodeDesa,NamaDesa, KodeKec, KodeKab, KodeProv')
            ->from('mstdesa')
            ->where(array('KodeKec' => $KodeKec))
            ->order_by("NamaDesa", "asc")
            ->get()
            ->result();
    }

    public function get_data_barang($KodeBarang)
    {
        return $this->db->select('b.KodeBarang, b.SatuanBarang, b.HargaBeliTerakhir, b.HargaJual, b.NilaiHPP, b.Spesifikasi, b.KodeJenis, j.NamaJenisBarang, b.KodeKategori, k.NamaKategori')
            ->from('mstbarang as b')
            ->join('mstjenisbarang as j', 'j.KodeJenis = b.KodeJenis', 'left')
            ->join('mstkategori as k', 'k.KodeKategori = b.KodeKategori', 'left')
            ->where('KodeBarang', $KodeBarang)
            ->get()
            ->row();
    }

    public function get_data_transaksi($NoTransKas)
    {
        return $this->db->select('*')
            ->from('transaksikas k')
            ->where('NoTransKas', $NoTransKas)
            ->get()
            ->row();
    }

    public function get_data_supplier($IDTransBeli)
    {
        return $this->db->select('*')
            ->from('transpembelian as b')
            ->join('mstperson as p', 'p.KodePerson = b.KodePerson', 'left')
            ->where('b.IDTransBeli', $IDTransBeli)
            ->get()
            ->row();
    }

    public function get_data_customer($IDTransJual)
    {
        return $this->db->select('*')
            ->from('transpenjualan as j')
            ->join('mstperson as p', 'p.KodePerson = j.KodePerson', 'left')
            ->where('j.IDTransJual', $IDTransJual)
            ->get()
            ->row();
    }

    public function get_data_po()
    {
        return $this->db->select('*')
            ->from('transpembelian')
            ->where([
                'StatusProses' => 'APPROVED',
                'IsVoid' => 0
            ])
            ->get()
            ->result();
    }

    public function get_data_po_like($searchTerm)
    {
        return $this->db->select('*')
            ->from('transpembelian')
            ->where([
                'StatusProses' => 'APPROVED',
                'IsVoid' => 0
            ])
            ->like('IDTransBeli', $searchTerm)
            ->or_like('NoPO', $searchTerm)
            ->get()
            ->result();
    }

    public function get_data_pembelian()
    {
        return $this->db->select('*')
            ->from('transpembelian')
            ->where(
                [
                    'StatusProses' => 'DONE',
                    'StatusKirim !=' => 'TERKIRIM',
                    'IsVoid' => 0,
                ]
            )
            ->get()
            ->result();
    }

    public function get_data_pembelian_like($searchTerm)
    {
        return $this->db->select('*')
            ->from('transpembelian')
            ->where(
                [
                    'StatusProses' => 'DONE',
                    'StatusKirim !=' => 'TERKIRIM',
                    'IsVoid' => 0,
                ]
            )
            ->like('IDTransBeli', $searchTerm)
            ->or_like('NoPO', $searchTerm)
            ->get()
            ->result();
    }

    public function get_data_so()
    {
        return $this->db->select('*')
            ->from('transpenjualan')
            ->where('StatusProses', 'SPK')
            // ->like('IDTransJual', 'TJL')
            ->like('IDTransJual', 'SPO')
            ->get()
            ->result();
    }

    public function get_data_so_like($searchTerm)
    {
        return $this->db->select('*')
            ->from('transpenjualan')
            ->where('StatusProses', 'SPK')
            // ->like('IDTransJual', 'TJL')
            ->like('IDTransJual', 'SPO')
            ->like('IDTransJual', $searchTerm)
            ->or_like('NoSlipOrder', $searchTerm)
            ->get()
            ->result();
    }

    public function get_data_tjl()
    {
        return $this->db->select('*')
            ->from('transpenjualan')
            ->where([
                'StatusProses' => 'DONE',
                'StatusKirim' => 'TERKIRIM'
            ])
            ->get()
            ->result();
    }

    public function get_data_tjl_like($searchTerm)
    {
        return $this->db->select('*')
            ->from('transpenjualan')
            ->where([
                'StatusProses' => 'DONE',
                'StatusKirim' => 'TERKIRIM'
            ])
            ->like('IDTransJual', $searchTerm)
            ->get()
            ->result();
    }

    public function get_barang_datang($NoRefTrSistem, $KodeBarang) // hitung barang datang per item
    {
        $sql = "SELECT SUM(itb.Qty) AS jml_datang
            FROM itempembelian AS i
            LEFT JOIN transpembelian AS pb ON pb.IDTransBeli = i.IDTransBeli
            LEFT JOIN transaksibarang AS tb ON tb.NoRefTrSistem = pb.IDTransBeli
            LEFT JOIN itemtransaksibarang AS itb ON itb.NoTrans = tb.NoTrans AND itb.NoUrut = i.NoUrut
            LEFT JOIN mstbarang AS br ON br.KodeBarang = itb.KodeBarang
            WHERE tb.NoRefTrSistem = '$NoRefTrSistem'
            AND itb.KodeBarang = '$KodeBarang'
            AND itb.IsHapus = 0";
        return $this->db->query($sql)->row_array();
    }

    public function get_tr_barang_datang($NoRefTrSistem) // hitung barang datang per transaksi
    {
        $sql = "SELECT SUM(itb.Qty) AS jml_datang
            FROM itempembelian AS i
            LEFT JOIN transpembelian AS pb ON pb.IDTransBeli = i.IDTransBeli
            LEFT JOIN transaksibarang AS tb ON tb.NoRefTrSistem = pb.IDTransBeli
            LEFT JOIN itemtransaksibarang AS itb ON itb.NoTrans = tb.NoTrans AND itb.NoUrut = i.NoUrut
            LEFT JOIN mstbarang AS br ON br.KodeBarang = itb.KodeBarang
            WHERE tb.NoRefTrSistem = '$NoRefTrSistem'
            AND tb.IsHapus = 0";
        return $this->db->query($sql)->row_array();
    }

    public function get_total_dibayar($KodePerson, $NoRef_Sistem)
    {
        $sql = "SELECT SUM(k.TotalTransaksi) AS total_bayar
            FROM transaksikas AS k
            WHERE k.KodePerson = '$KodePerson'
            AND k.NoRef_Sistem = '$NoRef_Sistem'
            GROUP BY k.NoRef_Sistem";
        return $this->db->query($sql)->row_array();
    }

    public function get_stok_asli($KodeBarang)
    {
        $sql = "SELECT SUM(if(i.JenisStok = 'MASUK', i.Qty, 0)) - SUM(if(i.JenisStok = 'KELUAR', i.Qty, 0)) AS stok, br.SatuanBarang
            FROM itemtransaksibarang AS i
            LEFT JOIN transaksibarang AS t ON t.NoTrans = i.NoTrans
            LEFT JOIN mstbarang AS br ON br.KodeBarang = i.KodeBarang
            WHERE i.KodeBarang = '$KodeBarang'
            AND i.IsHapus = 0";
        return $this->db->query($sql)->row_array();
    }

    public function get_hpp_sistem($KodeBarang)
    {
        return (int)$this->db->select('NilaiHPP')
            ->from('mstbarang')
            ->where('KodeBarang', $KodeBarang)
            ->get()
            ->row_array()['NilaiHPP'];
    }

    public function get_stok_per_gudang($KodeGudang, $KodeBarang)
    {
        $sql = "SELECT SUM(IF(i.GudangTujuan = '$KodeGudang' AND (i.JenisStok = 'MASUK' OR i.JenisStok = 'MUTASI'), i.Qty, 0)) - SUM(IF(i.GudangAsal = '$KodeGudang' AND (i.JenisStok = 'KELUAR' OR i.JenisStok = 'MUTASI'), i.Qty, 0)) AS stok, br.SatuanBarang
            FROM itemtransaksibarang AS i
            JOIN mstbarang br ON i.KodeBarang = br.KodeBarang
            WHERE i.KodeBarang = '$KodeBarang'
            AND i.IsHapus = 0";
        return $this->db->query($sql)->row_array();
    }

    public function get_gudang_asal()
    {
        return $this->db->select('*')
            ->from('mstgudang')
            ->get()
            ->result();
    }

    public function get_gudang_asal_like($searchTerm)
    {
        $this->db->select('*')
            ->from('mstgudang')
            ->like('NamaGudang', $searchTerm)
            ->get()
            ->result();
    }

    public function get_gudang_tujuan($GudangAsal)
    {
        return $this->db->select('*')
            ->from('mstgudang')
            ->where_not_in('KodeGudang', $GudangAsal)
            ->get()
            ->result();
    }

    public function get_gudang_tujuan_like($GudangAsal, $searchTerm)
    {
        return $this->db->select('*')
            ->from('mstgudang')
            ->where_not_in('KodeGudang', $GudangAsal)
            ->like('NamaGudang', $searchTerm)
            ->get()
            ->result();
    }

    public function get_data_spk($SPKNomor)
    {
        return $this->db->select('*')
            ->from('transpenjualan as j')
            ->join('mstperson as p', 'p.KodePerson = j.KodePerson', 'left')
            ->where('SPKNomor', $SPKNomor)
            ->get()
            ->row();
    }

    public function get_aktivitas($KodeAktivitas)
    {
        return $this->db->select('*')
            ->from('mstaktivitas')
            ->where('KodeAktivitas', $KodeAktivitas)
            ->get()
            ->row();
    }

    public function get_barang_jadi()
    {
        return $this->db->select('*')
            ->from('mstbarang as b')
            ->join('mstjenisbarang as j', 'b.KodeJenis = j.KodeJenis', 'left')
            ->like('j.NamaJenisBarang', 'BARANG JADI')
            ->get()
            ->result();
    }

    public function get_barang_jadi_like($searchTerm)
    {
        return $this->db->select('*')
            ->from('mstbarang as b')
            ->join('mstjenisbarang as j', 'b.KodeJenis = j.KodeJenis', 'left')
            ->like('j.NamaJenisBarang', 'BARANG JADI')
            ->like('b.KodeBarang', $searchTerm)
            ->or_like('b.NamaBarang', $searchTerm)
            ->get()
            ->result();
    }

    public function get_item_produksi($kodegudang)
    {
        $sql = "SELECT i.*, br.NamaBarang, br.KodeManual, j.SPKNomor
            FROM itemtransaksibarang i
            INNER JOIN mstbarang br ON i.KodeBarang = br.KodeBarang
            INNER JOIN transaksibarang b ON i.NoTrans = b.NoTrans
            INNER JOIN transpenjualan j ON b.NoRefTrSistem = j.IDTransJual
            WHERE i.NoRefProduksi IS NULL
            AND i.GudangTujuan = '$kodegudang'
            AND i.IsBarangJadi = 1
            AND i.IsBahanBaku = 0
            AND i.IsHapus = 0
            AND j.StatusProses = 'DONE'
            GROUP BY i.KodeBarang";
        return $this->db->query($sql)->result_array();
    }

    public function get_item_produksi_like($searchTerm, $kodegudang)
    {
        $sql = "SELECT i.*, br.NamaBarang, br.KodeManual, j.SPKNomor
            FROM itemtransaksibarang i
            INNER JOIN mstbarang br ON i.KodeBarang = br.KodeBarang
            INNER JOIN transaksibarang b ON i.NoTrans = b.NoTrans
            INNER JOIN transpenjualan j ON b.NoRefTrSistem = j.IDTransJual
            WHERE i.NoRefProduksi IS NULL
            AND i.GudangTujuan = '$kodegudang'
            AND i.IsBarangJadi = 1
            AND i.IsBahanBaku = 0
            AND i.IsHapus = 0
            AND j.StatusProses = 'DONE'
            AND (br.NamaBarang LIKE '%$searchTerm%' OR br.KodeManual LIKE '%$searchTerm%')
            GROUP BY i.KodeBarang";
        return $this->db->query($sql)->result_array();
    }

    public function get_biaya_bahan_prod($NoTrans)
    {
        $sql = "SELECT SUM(if(i.JenisStok = 'KELUAR' AND i.IsHapus = 0, i.Total, 0)) AS BiayaBahanProd
            FROM transaksibarang AS t
            LEFT JOIN itemtransaksibarang AS i ON t.NoTrans = i.NoTrans
            WHERE t.NoTrans = '$NoTrans'";
        return $this->db->query($sql)->row_array();
    }

    public function get_biaya_aktivitas($NoTrans)
    {
        $sql = "SELECT SUM(a.Biaya) AS BiayaAktivitas
            FROM transaksibarang AS t
            LEFT JOIN aktivitasproduksi AS a ON t.NoTrans = a.NoTrans
            WHERE t.NoTrans = '$NoTrans'";
        return $this->db->query($sql)->row_array();
    }

    public function get_data_pegawai($KodePegawai)
    {
        return $this->db->select('*')
            ->from('mstpegawai as p')
            ->join('mstjabatan as j', 'p.KodeJabatan = j.KodeJabatan', 'left')
            ->where('p.KodePegawai', $KodePegawai)
            ->get()
            ->row();
    }

    public function get_data_akun($KodeAkun)
    {
        return $this->db->select('*')
            ->from('mstakun')
            ->where('KodeAkun', $KodeAkun)
            ->get()
            ->row();
    }

    public function get_saldo_bulan_lalu($bulan)
    {
        $sql = "SELECT SUM(if(k.JenisTransaksiKas = 'DP PENJUALAN' OR k.JenisTransaksiKas = 'TERIMA PIUTANG' OR k.JenisTransaksiKas = 'KAS MASUK', k.TotalTransaksi, 0)) - SUM(if(k.JenisTransaksiKas = 'DP PEMBELIAN' OR k.JenisTransaksiKas = 'BAYAR HUTANG' OR k.JenisTransaksiKas = 'KAS KELUAR' OR k.JenisTransaksiKas = 'BIAYA PRODUKSI', k.TotalTransaksi, 0)) AS Saldo
            FROM transaksikas AS k
            WHERE SUBSTRING(k.TanggalTransaksi, 1, 7) < '$bulan'";
        return $this->db->query($sql)->row_array()['Saldo'];
    }

    public function get_saldo_bulan_ini($bulan)
    {
        $sql = "SELECT SUM(if(k.JenisTransaksiKas = 'DP PENJUALAN' OR k.JenisTransaksiKas = 'TERIMA PIUTANG' OR k.JenisTransaksiKas = 'KAS MASUK', k.TotalTransaksi, 0)) - SUM(if(k.JenisTransaksiKas = 'DP PEMBELIAN' OR k.JenisTransaksiKas = 'BAYAR HUTANG' OR k.JenisTransaksiKas = 'KAS KELUAR' OR k.JenisTransaksiKas = 'BIAYA PRODUKSI', k.TotalTransaksi, 0)) AS Saldo
            FROM transaksikas AS k
            WHERE SUBSTRING(k.TanggalTransaksi, 1, 7) <= '$bulan'";
        return $this->db->query($sql)->row_array()['Saldo'];
    }

    public function get_history_bayar($id)
    {
        $sql = "SELECT k.NoTransKas, k.TanggalTransaksi, k.TotalTransaksi, u.ActualName, k.JenisTransaksiKas, k.KodeTahun
            FROM transaksikas k
            LEFT JOIN userlogin u ON k.UserName = u.UserName
            WHERE k.NoRef_Sistem = '$id'";
        return $this->db->query($sql)->result_array();
    }

    public function get_tutup_buku($kodetahun, $tgl)
    {
        $tahunlalu = (int)$kodetahun - 1;
        $sql = "SELECT T1.KodeAkun, T1.NamaAkun, T1.JenisAkun, T1.AkunInduk, if(T2.Debet > 0, T2.Debet, 0) AS DebetUmum, if(T2.Kredit > 0, T2.Kredit, 0) AS KreditUmum, SUM(if(T1.JenisAkun = 'Debit', COALESCE(T2.Debet, 0) - COALESCE(T2.Kredit, 0), COALESCE(T2.Kredit, 0) - COALESCE(T2.Debet, 0))) AS NominalUmum, if(T3.DebetP > 0, T3.DebetP, 0) AS DebetPenyesuaian, if(T3.KreditP > 0, T3.KreditP, 0) AS KreditPenyesuaian, SUM(if(T1.JenisAkun = 'Debit', COALESCE(T3.DebetP, 0) - COALESCE(T3.KreditP, 0), COALESCE(T3.KreditP, 0) - COALESCE(T3.DebetP, 0))) AS NominalPenyesuaian, COALESCE(T4.SaldoAkhir, 0) AS SaldoNeraca
            FROM (
                SELECT a.KodeAkun, a.NamaAkun, a.IsParent, a.JenisAkun, a.AkunInduk
                FROM mstakun a
                WHERE a.IsParent = 0
            ) AS T1
            LEFT JOIN (
                SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS Debet, SUM(i.Kredit) AS Kredit, j.TipeJurnal
                FROM transjurnalitem i
                LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                WHERE i.KodeTahun = '$kodetahun'
                AND DATE(j.TglTransJurnal) <= '$tgl'
                AND j.TipeJurnal = 'UMUM'
                GROUP BY i.KodeAkun
            ) AS T2 ON T1.KodeAkun = T2.KodeAkun
            LEFT JOIN (
                SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS DebetP, SUM(i.Kredit) AS KreditP, j.TipeJurnal
                FROM transjurnalitem i
                LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                WHERE i.KodeTahun = '$kodetahun'
                AND DATE(j.TglTransJurnal) <= '$tgl'
                AND j.TipeJurnal = 'PENYESUAIAN'
                GROUP BY i.KodeAkun
            ) AS T3 ON T1.KodeAkun = T3.KodeAkun
            LEFT JOIN (
                SELECT n.KodeAkun, n.KodeTahun, n.SaldoAkhir
                FROM neracasaldo n
                WHERE n.Kodetahun = '$tahunlalu'
            ) AS T4 ON T1.KodeAkun = T4.KodeAkun
            GROUP BY T1.KodeAkun
            ORDER BY T1.KodeAkun";
        return $this->db->query($sql)->result_array();
    }

    public function get_akun_neraca($kodetahun, $tgl)
    {
        $sql = "SELECT T1.KodeAkun, T1.NamaAkun, T1.JenisAkun, if(T2.Debet > 0, T2.Debet, 0) AS DebetUmum, if(T2.Kredit > 0, T2.Kredit, 0) AS KreditUmum, SUM(if(T1.JenisAkun = 'Debit', COALESCE(T2.Debet, 0) - COALESCE(T2.Kredit, 0), COALESCE(T2.Kredit, 0) - COALESCE(T2.Debet, 0))) AS NominalUmum, if(T3.DebetP > 0, T3.DebetP, 0) AS DebetPenyesuaian, if(T3.KreditP > 0, T3.KreditP, 0) AS KreditPenyesuaian, SUM(if(T1.JenisAkun = 'Debit', COALESCE(T3.DebetP, 0) - COALESCE(T3.KreditP, 0), COALESCE(T3.KreditP, 0) - COALESCE(T3.DebetP, 0))) AS NominalPenyesuaian, COALESCE(T4.SaldoAkhir, 0) AS SaldoNeraca
            FROM (
                SELECT a.KodeAkun, a.NamaAkun, a.IsParent, a.JenisAkun
                FROM mstakun a
                WHERE a.IsParent = 0
                AND (LEFT(a.KodeAkun, 1) < 4 OR LEFT(a.KodeAkun, 1) = 7)
            ) AS T1
            LEFT JOIN (
                SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS Debet, SUM(i.Kredit) AS Kredit, j.TipeJurnal
                FROM transjurnalitem i
                LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                WHERE i.KodeTahun = '$kodetahun'
                AND DATE(j.TglTransJurnal) <= '$tgl'
                AND j.TipeJurnal = 'UMUM'
                GROUP BY i.KodeAkun
            ) AS T2 ON T1.KodeAkun = T2.KodeAkun
            LEFT JOIN (
                SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS DebetP, SUM(i.Kredit) AS KreditP, j.TipeJurnal
                FROM transjurnalitem i
                LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                WHERE i.KodeTahun = '$kodetahun'
                AND DATE(j.TglTransJurnal) <= '$tgl'
                AND j.TipeJurnal = 'PENYESUAIAN'
                GROUP BY i.KodeAkun
            ) AS T3 ON T1.KodeAkun = T3.KodeAkun
            LEFT JOIN (
                SELECT n.KodeAkun, n.KodeTahun, n.SaldoAkhir
                FROM neracasaldo n
                WHERE n.Kodetahun = '$kodetahun'
            ) AS T4 ON T1.KodeAkun = T4.KodeAkun
            GROUP BY T1.KodeAkun
            ORDER BY T1.KodeAkun";
        $result = $this->db->query($sql)->result_array();

        $data = [];
        foreach ($result as $key) {
            $data[] = [
                'KodeAkun'           => $key['KodeAkun'],
                'NamaAkun'           => $key['NamaAkun'],
                'NominalUmum'        => $key['NominalUmum'],
                'NominalPenyesuaian' => $key['NominalPenyesuaian'],
                'SaldoNeraca'        => $key['SaldoNeraca'],
                'Nominal'            => $key['NominalUmum'] + $key['NominalPenyesuaian'] + $key['SaldoNeraca']
            ];
        }

        return $data;
    }

    public function hitung_hpp_total($tglawal, $tglakhir, $tglakhirlalu)
    {
        $sql1 = "SELECT COALESCE(SUM(Nominal), 0) AS Nominal
            FROM nilaipersediaanbarang
            WHERE DATE(Tanggal) = '$tglakhirlalu'";
        $persediaanawal = $this->db->query($sql1)->row_array()['Nominal'];

        // $sql2_old = "SELECT COALESCE(SUM(i.Debet), 0) AS Total -- - COALESCE(SUM(i.Kredit), 0)
        //     FROM transjurnalitem i
        //     JOIN transjurnal t ON i.IDTransJurnal = t.IDTransJurnal
        //     WHERE LEFT(i.KodeAkun, 1) = 7
        //     AND DATE(t.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir'";
        $sql2 = "SELECT COALESCE(sum(b.TotalTagihan), 0) as Total
            FROM transpembelian b
            WHERE b.StatusProses = 'DONE'
            AND b.IsVoid = 0 AND
            DATE(b.TanggalPembelian) BETWEEN '$tglawal' AND '$tglakhir'";
        $pembelianbersih = $this->db->query($sql2)->row_array()['Total'];

        $sql3 = "SELECT COALESCE(SUM(Nominal), 0) AS Nominal
            FROM nilaipersediaanbarang
            WHERE DATE(Tanggal) = '$tglakhir'";
        $persediaanakhir = $this->db->query($sql3)->row_array()['Nominal'];

        $totalhpp = $persediaanawal + $pembelianbersih - $persediaanakhir;
        return $totalhpp;
    }

    public function get_laba_rugi($kodetahun, $tgl)
    {
        $tahunlalu = (int)$kodetahun - 1;
        $sql = "SELECT T1.KodeAkun, T1.NamaAkun, T1.JenisAkun, SUM(IF(T1.JenisAkun = 'Debit', COALESCE(T2.Debet, 0) - COALESCE(T2.Kredit, 0), COALESCE(T2.Kredit, 0) - COALESCE(T2.Debet, 0))) AS NominalUmum, SUM(if(T1.JenisAkun = 'Debit', COALESCE(T3.DebetP, 0) - COALESCE(T3.KreditP, 0), COALESCE(T3.KreditP, 0) - COALESCE(T3.DebetP, 0))) AS NominalPenyesuaian
            FROM (
                SELECT a.KodeAkun, a.NamaAkun, a.IsParent, a.JenisAkun
                FROM mstakun a
                WHERE a.IsParent = 0
                AND LEFT(a.KodeAkun, 1) > 3
            ) AS T1
            LEFT JOIN (
                SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS Debet, SUM(i.Kredit) AS Kredit, j.TipeJurnal
                FROM transjurnalitem i
                LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                WHERE i.KodeTahun = '$kodetahun' AND DATE(j.TglTransJurnal) <= '$tgl' AND j.TipeJurnal = 'UMUM'
                GROUP BY i.KodeAkun
            ) AS T2 ON T1.KodeAkun = T2.KodeAkun
            LEFT JOIN (
                SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS DebetP, SUM(i.Kredit) AS KreditP, j.TipeJurnal
                FROM transjurnalitem i
                LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                WHERE i.KodeTahun = '$kodetahun' AND DATE(j.TglTransJurnal) <= '$tgl' AND j.TipeJurnal = 'PENYESUAIAN'
                GROUP BY i.KodeAkun
            ) AS T3 ON T1.KodeAkun = T3.KodeAkun
            GROUP BY T1.KodeAkun
            ORDER BY T1.KodeAkun";
        $result = $this->db->query($sql)->result_array();

        $n4 = 0;
        $n5 = 0;
        $n6 = 0;
        // $n7 = 0;
        foreach ($result as $key) {
            if (substr($key['KodeAkun'], 0, 1) == 4) {
                $n4 += $key['NominalUmum'] + $key['NominalPenyesuaian'];
            }
            if (substr($key['KodeAkun'], 0, 1) == 5) {
                $n5 += $key['NominalUmum'] + $key['NominalPenyesuaian'];
            }
            if (substr($key['KodeAkun'], 0, 1) == 6) {
                $n6 += $key['NominalUmum'] + $key['NominalPenyesuaian'];
            }
            // if (substr($key['KodeAkun'], 0, 1) == 7) {
            //     $n7 += $key['NominalUmum'] + $key['NominalPenyesuaian'];
            // }
        }
        $totaldebet = $n5 + $n6; // + $n7;
        $totalkredit = $n4;

        $sqlawal = "SELECT COALESCE(SUM(n.SaldoAkhir), 0) AS Total
            FROM neracasaldo n
            JOIN mstakun a ON n.KodeAkun = a.KodeAkun
            WHERE n.Kodetahun = '$tahunlalu'
            AND a.IsPersediaan = 1";
        $persediaanawal = $this->db->query($sqlawal)->row_array()['Total'];
        $persediaanakhir = $this->hitung_persediaan($tgl, '<=', $kodetahun);
        $sqlpembelian = "SELECT COALESCE(SUM(i.Debet), 0) - COALESCE(SUM(i.Kredit), 0) AS Total
            FROM transjurnalitem i
            JOIN transjurnal t ON i.IDTransJurnal = t.IDTransJurnal
            WHERE LEFT(i.KodeAkun, 1) = 7
            AND DATE(t.TglTransJurnal) <= '$tgl'
            AND t.KodeTahun = '$kodetahun'";
        $pembelianbersih = $this->db->query($sqlpembelian)->row_array()['Total'];
        $result['totalhpp'] = $persediaanawal + $pembelianbersih - $persediaanakhir;

        $result['labarugi'] = $totalkredit - $totaldebet; // - $result['totalhpp'];
        return $result;
    }

    public function get_laba_rugi_monthly($kodetahun, $tglawal, $tglakhir)
    {
        $sql = "SELECT T1.KodeAkun, T1.NamaAkun, T1.JenisAkun, SUM(IF(T1.JenisAkun = 'Debit', COALESCE(T2.Debet, 0) - COALESCE(T2.Kredit, 0), COALESCE(T2.Kredit, 0) - COALESCE(T2.Debet, 0))) AS NominalUmum, SUM(if(T1.JenisAkun = 'Debit', COALESCE(T3.DebetP, 0) - COALESCE(T3.KreditP, 0), COALESCE(T3.KreditP, 0) - COALESCE(T3.DebetP, 0))) AS NominalPenyesuaian
            FROM (
                SELECT a.KodeAkun, a.NamaAkun, a.IsParent, a.JenisAkun
                FROM mstakun a
                WHERE a.IsParent = 0
                AND LEFT(a.KodeAkun, 1) > 3
            ) AS T1
            LEFT JOIN (
                SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS Debet, SUM(i.Kredit) AS Kredit, j.TipeJurnal
                FROM transjurnalitem i
                LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                WHERE i.KodeTahun = '$kodetahun' AND DATE(j.TglTransJurnal) <= '$tglakhir' AND j.TipeJurnal = 'UMUM'
                GROUP BY i.KodeAkun
            ) AS T2 ON T1.KodeAkun = T2.KodeAkun
            LEFT JOIN (
                SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS DebetP, SUM(i.Kredit) AS KreditP, j.TipeJurnal
                FROM transjurnalitem i
                LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
                WHERE i.KodeTahun = '$kodetahun' AND DATE(j.TglTransJurnal) <= '$tglakhir' AND j.TipeJurnal = 'PENYESUAIAN'
                GROUP BY i.KodeAkun
            ) AS T3 ON T1.KodeAkun = T3.KodeAkun
            GROUP BY T1.KodeAkun
            ORDER BY T1.KodeAkun";
        $result = $this->db->query($sql)->result_array();

        $n4 = 0;
        $n5 = 0;
        $n6 = 0;
        foreach ($result as $key) {
            if (substr($key['KodeAkun'], 0, 1) == 4) {
                $n4 += $key['NominalUmum'] + $key['NominalPenyesuaian'];
            }
            if (substr($key['KodeAkun'], 0, 1) == 5) {
                $n5 += $key['NominalUmum'] + $key['NominalPenyesuaian'];
            }
            if (substr($key['KodeAkun'], 0, 1) == 6) {
                $n6 += $key['NominalUmum'] + $key['NominalPenyesuaian'];
            }
        }
        $totaldebet = $n5 + $n6;
        $totalkredit = $n4;

        $persediaanawal = $this->hitung_persediaan($tglawal, '<', $kodetahun);
        $persediaanakhir = $this->hitung_persediaan($tglakhir, '<=', $kodetahun);

        $sql2 = "SELECT COALESCE(SUM(i.Debet), 0) - COALESCE(SUM(i.Kredit), 0) AS Total
            FROM transjurnalitem i
            JOIN transjurnal t ON i.IDTransJurnal = t.IDTransJurnal
            WHERE LEFT(i.KodeAkun, 1) = 7
            AND DATE(t.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir'
            AND t.KodeTahun = '$kodetahun'";
        $pembelianbersih = $this->db->query($sql2)->row_array()['Total'];
        $totalhpp = $persediaanawal + $pembelianbersih - $persediaanakhir;

        $labarugi = $totalkredit - $totaldebet - $totalhpp;
        return $labarugi;
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

    public function hitung_persediaan_range($tglawal, $tglakhir, $kodetahun)
    {
        $sql = "SELECT COALESCE(SUM(i.Debet), 0) - COALESCE(SUM(i.Kredit), 0) AS Total
            FROM transjurnalitem i
            JOIN transjurnal t ON i.IDTransJurnal = t.IDTransJurnal
            JOIN mstakun a ON i.KodeAkun = a.KodeAkun
            WHERE a.IsPersediaan = 1
            AND DATE(t.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir'
            AND t.KodeTahun = '$kodetahun'";
        $result = $this->db->query($sql)->row_array()['Total'];
        return $result;
    }

    public function get_laba_rugi_range($kodetahun, $tglawal, $tglakhir)
    {
        $sql = "SELECT T1.KodeAkun, T1.NamaAkun, T1.JenisAkun, SUM(if(T1.JenisAkun = 'Debit', COALESCE(T2.Debet, 0) - COALESCE(T2.Kredit, 0), COALESCE(T2.Kredit, 0) - COALESCE(T2.Debet, 0))) AS NominalUmum, SUM(if(T1.JenisAkun = 'Debit', COALESCE(T3.DebetP, 0) - COALESCE(T3.KreditP, 0), COALESCE(T3.KreditP, 0) - COALESCE(T3.DebetP, 0))) AS NominalPenyesuaian
            FROM (
            SELECT a.KodeAkun, a.NamaAkun, a.IsParent, a.JenisAkun
            FROM mstakun a
            WHERE a.IsParent = 0
            AND LEFT(a.KodeAkun, 1) > 3
            ) AS T1
            LEFT JOIN (
            SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS Debet, SUM(i.Kredit) AS Kredit, j.TipeJurnal
            FROM transjurnalitem i
            LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            WHERE i.KodeTahun = '$kodetahun' AND DATE(j.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir' AND j.TipeJurnal = 'UMUM'
            GROUP BY i.KodeAkun
            ) AS T2 ON T1.KodeAkun = T2.KodeAkun
            LEFT JOIN (
            SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS DebetP, SUM(i.Kredit) AS KreditP, j.TipeJurnal
            FROM transjurnalitem i
            LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            WHERE i.KodeTahun = '$kodetahun' AND DATE(j.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir' AND j.TipeJurnal = 'PENYESUAIAN'
            GROUP BY i.KodeAkun
            ) AS T3 ON T1.KodeAkun = T3.KodeAkun
            GROUP BY T1.KodeAkun
            ORDER BY T1.KodeAkun";
        $result = $this->db->query($sql)->result_array();

        $n4 = 0;
        $n5 = 0;
        $n6 = 0;
        foreach ($result as $key) {
            if (substr($key['KodeAkun'], 0, 1) == 4) {
                $n4 += $key['NominalUmum'] + $key['NominalPenyesuaian'];
            }
            if (substr($key['KodeAkun'], 0, 1) == 5) {
                $n5 += $key['NominalUmum'] + $key['NominalPenyesuaian'];
            }
            if (substr($key['KodeAkun'], 0, 1) == 6) {
                $n6 += $key['NominalUmum'] + $key['NominalPenyesuaian'];
            }
        }
        $totaldebet = $n5 + $n6;
        $totalkredit = $n4;
        $labarugi = $totalkredit - $totaldebet;

        return $labarugi;
    }

    public function get_akun_labarugi($kodetahun, $tgl)
    {
        $sql = "SELECT T1.KodeAkun, T1.NamaAkun, T1.JenisAkun, SUM(if(T1.JenisAkun = 'Debit', COALESCE(T2.Debet, 0) - COALESCE(T2.Kredit, 0), COALESCE(T2.Kredit) - COALESCE(T2.Debet))) AS NominalUmum, SUM(if(T1.JenisAkun = 'Debit', COALESCE(T3.DebetP, 0) - COALESCE(T3.KreditP, 0), COALESCE(T3.KreditP, 0) - COALESCE(T3.DebetP, 0))) AS NominalPenyesuaian
            FROM (
            SELECT a.KodeAkun, a.NamaAkun, a.IsParent, a.JenisAkun
            FROM mstakun a
            WHERE a.IsParent = 0
            AND (LEFT(a.KodeAkun, 1) BETWEEN 4 AND 6)
            ) AS T1
            LEFT JOIN (
            SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS Debet, SUM(i.Kredit) AS Kredit, j.TipeJurnal
            FROM transjurnalitem i
            LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            WHERE i.KodeTahun = '$kodetahun' AND DATE(j.TglTransJurnal) <= '$tgl' AND j.TipeJurnal = 'UMUM'
            GROUP BY i.KodeAkun
            ) AS T2 ON T1.KodeAkun = T2.KodeAkun
            LEFT JOIN (
            SELECT i.KodeAkun, i.NamaAkun, i.IDTransJurnal, i.KodeTahun, SUM(i.Debet) AS DebetP, SUM(i.Kredit) AS KreditP, j.TipeJurnal
            FROM transjurnalitem i
            LEFT JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            WHERE i.KodeTahun = '$kodetahun' AND DATE(j.TglTransJurnal) <= '$tgl' AND j.TipeJurnal = 'PENYESUAIAN'
            GROUP BY i.KodeAkun
            ) AS T3 ON T1.KodeAkun = T3.KodeAkun
            GROUP BY T1.KodeAkun
            ORDER BY T1.KodeAkun";
        $result = $this->db->query($sql)->result_array();

        $data = [];
        foreach ($result as $key) {
            $data[] = [
                'KodeAkun'              => $key['KodeAkun'],
                'NamaAkun'              => $key['NamaAkun'],
                'NominalUmum'           => $key['NominalUmum'],
                'NominalPenyesuaian'    => $key['NominalPenyesuaian'],
                'Nominal'               => $key['NominalUmum'] + $key['NominalPenyesuaian']
            ];
        }

        return $data;
    }

    public function setting_jurnal_status()
    {
        $data = $this->crud->get_one_row([
            'select' => 'ValueSetting',
            'from' => 'sistemsetting',
            'where' => [['KodeSetting' => 14]],
        ]);

        return $data['ValueSetting'];
    }

    public function get_limit_pinjaman()
    {
        $data = $this->crud->get_one_row([
            'select' => 'ValueSetting',
            'from' => 'sistemsetting',
            'where' => [['KodeSetting' => 15]],
        ]);

        return $data['ValueSetting'];
    }

    public function get_total_item_jurnal($IDTransJurnal)
    {
        $data = $this->crud->get_one_row([
            'select' => 'SUM(Debet) as Debet, SUM(Kredit) as Kredit',
            'from' => 'transjurnalitem',
            'where' => [['IDTransJurnal' => $IDTransJurnal]],
        ]);

        return $data;
    }

    public function get_akun_penjurnalan($namatr, $jenistr)
    {
        $data = $this->crud->get_rows([
            'select' => 's.KodeSetAkun, d.NoUrut, d.JenisJurnal, d.KodeAkun, d.StatusAkun, d.IsBank, a.NamaAkun, s.NamaTransaksi, s.JenisTransaksi',
            'from' => 'detailsetakun d',
            'join' => [
                [
                    'table' => 'setakunjurnal s',
                    'on' => 'd.KodeSetAkun = s.KodeSetAkun',
                    'param' => 'INNER'
                ],
                [
                    'table' => 'mstakun a',
                    'on' => 'd.KodeAkun = a.KodeAkun',
                    'param' => 'INNER'
                ],
            ],
            'where' => [[
                's.NamaTransaksi' => $namatr,
                's.JenisTransaksi' => $jenistr
            ]]
        ]);

        return $data;
    }

    public function getnamaakun($kodeakun)
    {
        $data = $this->crud->get_one_row([
            'select' => 'NamaAkun',
            'from' => 'mstakun',
            'where' => [['KodeAkun' => $kodeakun]]
        ]);

        return $data['NamaAkun'];
    }

    public function count_total_bayar($id)
    {
        $sql = "SELECT COALESCE(SUM(TotalTransaksi), 0) AS Total
            FROM transaksikas
            WHERE NoRef_Sistem = '$id'";
        $res = $this->db->query($sql)->row_array()['Total'];

        return $res;
    }

    public function get_pesan($id1, $id2)
    {
        $sql = "SELECT *
            FROM chat
            WHERE (Pengirim = '$id1' AND Penerima = '$id2' AND IsHapus = 0) OR (Pengirim = '$id2' AND Penerima = '$id1' AND IsHapus = 0)";

        $r = $this->db->query($sql);

        return $r->result();
    }

    public function get_jml_pesan($penerima, $pengirim)
    {
        $data = $this->crud->get_count([
            'select' => 'KodeChat',
            'from' => 'chat',
            'where' => [[
                'Pengirim' => $pengirim,
                'Penerima' => $penerima,
                'IsHapus'  => 0,
                'IsRead'   => 0
            ]],
        ]);

        return $data;
    }

    public function get_last_unread($penerima, $pengirim)
    {
        $data = $this->crud->get_one_row([
            'select' => 'KodeChat, TglChat, IsiPesan, FileName',
            'from' => 'chat',
            'where' => [[
                'Pengirim' => $pengirim,
                'Penerima' => $penerima,
                'IsHapus'  => 0,
                'IsRead'   => 0
            ]],
            'order_by' => 'KodeChat DESC'
        ]);

        return $data;
    }

    public function flip_api_status()
    {
        $data = $this->crud->get_one_row([
            'select' => 'ValueSetting',
            'from' => 'sistemsetting',
            'where' => [['KodeSetting' => 13]],
        ]);
        $url = ($data['ValueSetting'] == 'on') ? "https://bigflip.id/api" : "https://bigflip.id/big_sandbox_api";

        return $url;
    }

    public function get_all_bank()
    {
        $ch = curl_init();
        $secret_key = getenv('SECRET_KEY');
        $encoded_auth = base64_encode($secret_key.":");

        curl_setopt($ch, CURLOPT_URL, $this->flip_api_status() . "/v2/general/banks");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/x-www-form-urlencoded"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key.":");

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;
    }

    public function get_one_bank($KodeBank)
    {
        $ch = curl_init();
        $secret_key = getenv('SECRET_KEY');
        $encoded_auth = base64_encode($secret_key.":");

        curl_setopt($ch, CURLOPT_URL, $this->flip_api_status() . "/v2/general/banks?code=" . $KodeBank);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/x-www-form-urlencoded"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key.":");

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response[0];
    }

    public function get_balance()
    {
        $ch = curl_init();
        $secret_key = getenv('SECRET_KEY');
        $encoded_auth = base64_encode($secret_key.":");

        curl_setopt($ch, CURLOPT_URL, $this->flip_api_status() . "/v2/general/balance");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/x-www-form-urlencoded"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key.":");

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;
    }

    public function get_maintenance()
    {
        $ch = curl_init();
        $secret_key = getenv('SECRET_KEY');
        $encoded_auth = base64_encode($secret_key.":");

        curl_setopt($ch, CURLOPT_URL, $this->flip_api_status() . "/v2/general/maintenance");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/x-www-form-urlencoded"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key.":");

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;
    }

    public function transfer($KodeBank, $NoRek, $Amount)
    {
        $ch = curl_init();
        $secret_key = getenv('SECRET_KEY');
        $encoded_auth = base64_encode($secret_key.":");

        curl_setopt($ch, CURLOPT_URL, $this->flip_api_status() . "/v3/disbursement");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        $payloads = [
            "account_number" => $NoRek,
            "bank_code" => $KodeBank,
            "amount" => $Amount,
            "remark" => "some remark",
            "recipient_city" => "391",
            "beneficiary_email" => "test@mail.com,user@mail.com"
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payloads));

        $idemkey = substr(uniqid(), 0, 8);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/x-www-form-urlencoded",
          "idempotency-key: " . $idemkey
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key.":");

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;
    }

    public function getDisbursementById($id)
    {
        $ch = curl_init();
        $secret_key = getenv('SECRET_KEY');

        curl_setopt($ch, CURLOPT_URL, $this->flip_api_status() . "/v3/get-disbursement?id=" . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/x-www-form-urlencoded"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key.":");

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response['status'];
    }

    public function get_sistem_setting($kode)
    {
        $data = $this->crud->get_one_row([
            'select' => 'ValueSetting',
            'from' => 'sistemsetting',
            'where' => [['KodeSetting' => $kode]],
        ]);

        return $data['ValueSetting'];
    }

    public function createBill($title, $amount, $date, $bank, $sender_type)
    {
        $ch = curl_init();
        $secret_key = getenv('SECRET_KEY');

        $notelp = ($this->lokasi->get_sistem_setting(3) != null) ? $this->lokasi->get_sistem_setting(3) : "021 2902 1873";
        $notelpp = preg_replace('/^0?/', '+62', $notelp);
        $notelppp = str_replace(' ', '', $notelpp);

        curl_setopt($ch, CURLOPT_URL, $this->flip_api_status() . "/v2/pwf/bill");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        $payloads = [
            "title" => $title,
            "amount" => $amount,
            "type" => "SINGLE",
            "expired_date" => $date,
            // "redirect_url" => "https://someurl.com",
            "is_address_required" => 1,
            "is_phone_number_required" => 1,
            "step" => 3,
            "sender_name" => ($this->get_sistem_setting(1) != null) ? $this->get_sistem_setting(1) : "Boxity Central Indonesia",
            "sender_email" => ($this->get_sistem_setting(2) != null) ? $this->get_sistem_setting(2) : "corp.sec@bci.com",
            "sender_phone_number" => $notelppp,
            "sender_address" => ($this->get_sistem_setting(4) != null) ? $this->get_sistem_setting(4) : "Grand Slipi Tower, Jl. Jend Jl. Jelambar Barat No.22-24, Jelambar Baru, Kota Jakarta Barat, DKI Jakarta",
            "sender_bank" => $bank,
            "sender_bank_type" => $sender_type
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payloads));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/x-www-form-urlencoded"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key.":");

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;
    }

    public function getBill($id)
    {
        $ch = curl_init();
        $secret_key = getenv('SECRET_KEY');

        curl_setopt($ch, CURLOPT_URL, $this->flip_api_status() . "/v2/pwf/" . $id . "/bill");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/x-www-form-urlencoded"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key.":");

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;
    }

    public function sendMsg($sender, $receiver, $body)
    {
        define( 'API_ACCESS_KEY', 'AAAA5ugEmRo:APA91bGpWtCUH29wYrpF_WqZzM4YvtalN1k3kf1d9PBLfQfI1SdHDC8dPDP5INDKlVhVq9-XccfbyXN7_2K5kUchEoeC_ayhLVGYbLjblWOX8K40ASha5eNbzUz1eNZGyIdmnyN_2IJM' );

        $rcv = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'userlogin',
            'where' => [['UserName' => $receiver]]
        ]);

        $data = array(
            "to" => $rcv['Token'],
            "notification" => array(
                "title" => $this->session->userdata('ActualName'),
                "body" => $body,
                "icon" => $url_foto = ($this->session->userdata('photo') != null) ? base_url('assets/img/users/'.$this->session->userdata('photo')) : base_url('assets/img/avatar.svg.png'),
                "click_action" => base_url('user/chats?rcv=' . base64_encode($sender))
            )
        );
        $data_string = json_encode($data);

        // echo "The Json Data : ".$data_string;

        $headers = array
        (
             'Authorization: key=' . API_ACCESS_KEY, 
             'Content-Type: application/json'
        );                                                                                 
                                                                                                                             
        $ch = curl_init();  

        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );                                                                  
        curl_setopt( $ch,CURLOPT_POST, true );  
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, $data_string);                                                                  
                                                                                                                             
        $result = curl_exec($ch);

        curl_close ($ch);

        $result = json_decode($result, true);
        return $result;

        // echo "<p>&nbsp;</p>";
        // echo "The Result : ".$result;
    }
}
