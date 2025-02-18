<?php
defined('BASEPATH') or exit('No direct script access allowed');

class beranda extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		 $Akses = $this->akses->CekWaktu();
		if (!$Akses) {
			redirect(base_url('login'), ['error' => 'Anda tidak memiliki akses ke aplikasi ini.']);
		} 
	}

	public function index()
	{
		$data['menu'] = "beranda";
		$data['title'] = "Beranda";
		$data['view'] = 'beranda/v_beranda';
        $data['scripts'] = 'beranda/script';

        $data['production'] = $this->crud->get_rows([
            'select' => 'i.NoTrans, i.NoUrut, br.KodeManual, br.NamaBarang, i.BeratKotor, i.Qty, b.TanggalTransaksi, b.ProdTglSelesai, SUM(if(pc.KodePegawai IS NOT NULL, 1, 0)) AS PCetak, SUM(if(pp.KodePegawai IS NOT NULL, 1, 0)) AS PPotong, SUM(if(pk.KodePegawai IS NOT NULL, 1, 0)) AS PKasar, SUM(if(pcr.KodePegawai IS NOT NULL, 1, 0)) AS PCR, SUM(if(pt.KodePegawai IS NOT NULL, 1, 0)) AS PBT, SUM(if(pr.KodePegawai IS NOT NULL, 1, 0)) AS PR, SUM(if(ph.KodePegawai IS NOT NULL, 1, 0)) AS PHalus',
            'from' => 'itemtransaksibarang i',
            'join' => [
                [
                    'table' => 'mstbarang br',
                    'on' => 'i.KodeBarang = br.KodeBarang',
                    'param' => 'INNER',
                ],
                [
                    'table' => 'transaksibarang b',
                    'on' => 'i.NoTrans = b.NoTrans',
                    'param' => 'INNER',
                ],
                [
                    'table' => ' aktivitasproduksi ac',
                    'on' => "ac.NoTrans = i.NoTrans AND ac.NoUrut = i.NoUrut AND ac.JenisAktivitas = 'T. Cetak'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pc',
                    'on' => "pc.KodePegawai = ac.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ap',
                    'on' => "ap.NoTrans = i.NoTrans AND ap.NoUrut = i.NoUrut AND ap.JenisAktivitas = 'Potong'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pp',
                    'on' => "pp.KodePegawai = ap.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ak',
                    'on' => "ak.NoTrans = i.NoTrans AND ak.NoUrut = i.NoUrut AND ak.JenisAktivitas = 'Kasar'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pk',
                    'on' => "pk.KodePegawai = ak.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi cr',
                    'on' => "cr.NoTrans = i.NoTrans AND cr.NoUrut = i.NoUrut AND cr.JenisAktivitas = 'Bubut CR'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pcr',
                    'on' => "pcr.KodePegawai = cr.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi bt',
                    'on' => "bt.NoTrans = i.NoTrans AND bt.NoUrut = i.NoUrut AND bt.JenisAktivitas = 'Bubut T'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pt',
                    'on' => "pt.KodePegawai = bt.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ar',
                    'on' => "ar.NoTrans = i.NoTrans AND ar.NoUrut = i.NoUrut AND ar.JenisAktivitas = 'Bubut R'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai pr',
                    'on' => "pr.KodePegawai = ar.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' aktivitasproduksi ah',
                    'on' => "ah.NoTrans = i.NoTrans AND ah.NoUrut = i.NoUrut AND ah.JenisAktivitas = 'Halus'",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai ph',
                    'on' => "ph.KodePegawai = ah.KodePegawai",
                    'param' => 'LEFT',
                ]
            ],
            'where' => [[
                'i.IsBarangJadi' => 1,
                'i.IsHapus' => 0,
                'i.NoRefProduksi !=' => null
            ]],
            'group_by' => 'i.NoTrans, i.NoUrut',
        	'order_by' => 'i.NoTrans DESC',
        	'limit' => 6,
        ]);

        $sql = "SELECT SUM(if(i.JenisStok = 'MASUK', i.Qty, 0)) - SUM(if(i.JenisStok = 'KELUAR', i.Qty, 0)) AS TotalStok
			FROM itemtransaksibarang AS i
			LEFT JOIN transaksibarang AS t ON t.NoTrans = i.NoTrans
			LEFT JOIN mstbarang AS br ON br.KodeBarang = i.KodeBarang
			WHERE i.IsHapus = 0";
        $data['totalweight'] = $this->db->query($sql)->row_array()['TotalStok'];

        $kategoriBrg = $this->crud->get_rows([
            'select' => '*',
            'from' => 'mstkategori',
            'where' => [['IsAktif' => 1]]
        ]);
        $dtkategori = [];
        foreach ($kategoriBrg as $key) {
            $kodektg = $key['KodeKategori'];
            $sql = "SELECT SUM(if(i.JenisStok = 'MASUK', i.Qty, 0)) - SUM(if(i.JenisStok = 'KELUAR', i.Qty, 0)) AS TotalStok
                FROM itemtransaksibarang AS i
                LEFT JOIN transaksibarang AS t ON t.NoTrans = i.NoTrans
                LEFT JOIN mstbarang AS br ON br.KodeBarang = i.KodeBarang
                WHERE i.IsHapus = 0
                AND br.KodeKategori = '$kodektg'";
            $beratTotal = $this->db->query($sql)->row_array()['TotalStok'];

            $dtkategori[] = [
                'NamaKategori'  => $key['NamaKategori'],
                'BeratTotal'    => ($beratTotal != null) ? $beratTotal : 0,
                'WarnaKategori' => '#' . $this->random_color()
            ];
        }
        $data['dataktg'] = json_encode($dtkategori);

        $data['totalitems'] = $this->crud->get_count([
        	'select' => 'KodeBarang',
        	'from' => 'mstbarang',
        	'where' => [['IsAktif !=' => null]],
        ]);

        $tgl = $this->input->get('tgl');
        $tglfilter = date('Y-m-d', strtotime($tgl));
        $today = isset($tgl) ? $tglfilter : date("Y-m-d"); // yg dirubah untuk filternya
        $y = date('Y', strtotime($today));
        $m = date('m', strtotime($today));

        $bulan = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        $thnhutanglunas = [];
        $totalthnhutanglunas = 0;
        foreach ($bulan as $keys) {
            $thnhutanglunas[] = ($this->hutangLunasTahun($keys, $y) != null) ? round((int)$this->hutangLunasTahun($keys, $y)/1000000, 2) : 0;
            $totalthnhutanglunas += $this->hutangLunasTahun($keys, $y);
        }
        $data['thnhutanglunas'] = json_encode($thnhutanglunas);
        $data['totalthnhutanglunas'] = $totalthnhutanglunas;

        $thnhutang = [];
        $totalthnhutang = 0;
        foreach ($bulan as $keys) {
            $thnhutang[] = ($this->hutangTahun($keys, $y) != null) ? round((int)$this->hutangTahun($keys, $y)/1000000, 2) : 0;
            $totalthnhutang += $this->hutangTahun($keys, $y);
        }
        $data['thnhutang'] = json_encode($thnhutang);
        $data['totalthnhutang'] = $totalthnhutang;

        $thnpiutanglunas = [];
        $totalthnpiutanglunas = 0;
        foreach ($bulan as $keys) {
            $thnpiutanglunas[] = ($this->piutangLunasTahun($keys, $y) != null) ? round((int)$this->piutangLunasTahun($keys, $y)/1000000, 2) : 0;
            $totalthnpiutanglunas += $this->piutangLunasTahun($keys, $y);
        }
        $data['thnpiutanglunas'] = json_encode($thnpiutanglunas);
        $data['totalthnpiutanglunas'] = $totalthnpiutanglunas;

        $thnpiutang = [];
        $totalthnpiutang = 0;
        foreach ($bulan as $keys) {
            $thnpiutang[] = ($this->piutangTahun($keys, $y) != null) ? round((int)$this->piutangTahun($keys, $y)/1000000, 2) : 0;
            $totalthnpiutang += $this->piutangTahun($keys, $y);
        }
        $data['thnpiutang'] = json_encode($thnpiutang);
        $data['totalthnpiutang'] = $totalthnpiutang;

        $totalhari = cal_days_in_month(CAL_GREGORIAN,$m,$y);
        $range = ['01-05', '06-10', '11-15', '16-20', '21-25', '26-'.$totalhari];
        $data['totalhari'] = $totalhari;

        $blnhutanglunas = [];
        $totalblnhutanglunas = 0;
        foreach ($range as $keys) {
            $blnhutanglunas[] = ($this->hutangLunasBulan($keys, $y, $m) != null) ? round((int)$this->hutangLunasBulan($keys, $y, $m)/1000000, 2) : 0;
            $totalblnhutanglunas += $this->hutangLunasBulan($keys, $y, $m);
        }
        $data['blnhutanglunas'] = json_encode($blnhutanglunas);
        $data['totalblnhutanglunas'] = $totalblnhutanglunas;

        $blnhutang = [];
        $totalblnhutang = 0;
        foreach ($range as $keys) {
            $blnhutang[] = ($this->hutangBulan($keys, $y, $m) != null) ? round((int)$this->hutangBulan($keys, $y, $m)/1000000, 2) : 0;
            $totalblnhutang += $this->hutangBulan($keys, $y, $m);
        }
        $data['blnhutang'] = json_encode($blnhutang);
        $data['totalblnhutang'] = $totalblnhutang;

        $blnpiutanglunas = [];
        $totalblnpiutanglunas = 0;
        foreach ($range as $keys) {
            $blnpiutanglunas[] = ($this->piutangLunasBulan($keys, $y, $m) != null) ? round((int)$this->piutangLunasBulan($keys, $y, $m)/1000000, 2) : 0;
            $totalblnpiutanglunas += $this->piutangLunasBulan($keys, $y, $m);
        }
        $data['blnpiutanglunas'] = json_encode($blnpiutanglunas);
        $data['totalblnpiutanglunas'] = $totalblnpiutanglunas;

        $blnpiutang = [];
        $totalblnpiutang = 0;
        foreach ($range as $keys) {
            $blnpiutang[] = ($this->piutangBulan($keys, $y, $m) != null) ? round((int)$this->piutangBulan($keys, $y, $m)/1000000, 2) : 0;
            $totalblnpiutang += $this->piutangBulan($keys, $y, $m);
        }
        $data['blnpiutang'] = json_encode($blnpiutang);
        $data['totalblnpiutang'] = $totalblnpiutang;

        $week = $this->rangeWeek($today);
        $data['today'] = $today;
        $data['first_week'] = date('d-m-Y', strtotime($week[1]));
        $data['end_week'] = date('d-m-Y', strtotime($week[7]));

        $mghutanglunas = [];
        $totalmghutanglunas = 0;
        foreach ($week as $keys) {
            $mghutanglunas[] = ($this->hutangLunasMinggu($keys) != null) ? round((int)$this->hutangLunasMinggu($keys)/1000000, 2) : 0;
            $totalmghutanglunas += $this->hutangLunasMinggu($keys);
        }
        $data['mghutanglunas'] = json_encode($mghutanglunas);
        $data['totalmghutanglunas'] = $totalmghutanglunas;

        $mghutang = [];
        $totalmghutang = 0;
        foreach ($week as $keys) {
            $mghutang[] = ($this->hutangMinggu($keys) != null) ? round((int)$this->hutangMinggu($keys)/1000000, 2) : 0;
            $totalmghutang += $this->hutangMinggu($keys);
        }
        $data['mghutang'] = json_encode($mghutang);
        $data['totalmghutang'] = $totalmghutang;

        $mgpiutanglunas = [];
        $totalmgpiutanglunas = 0;
        foreach ($week as $keys) {
            $mgpiutanglunas[] = ($this->piutangLunasMinggu($keys) != null) ? round((int)$this->piutangLunasMinggu($keys)/1000000, 2) : 0;
            $totalmgpiutanglunas += $this->piutangLunasMinggu($keys);
        }
        $data['mgpiutanglunas'] = json_encode($mgpiutanglunas);
        $data['totalmgpiutanglunas'] = $totalmgpiutanglunas;

        $mgpiutang = [];
        $totalmgpiutang = 0;
        foreach ($week as $keys) {
            $mgpiutang[] = ($this->piutangMinggu($keys) != null) ? round((int)$this->piutangMinggu($keys)/1000000, 2) : 0;
            $totalmgpiutang += $this->piutangMinggu($keys);
        }
        $data['mgpiutang'] = json_encode($mgpiutang);
        $data['totalmgpiutang'] = $totalmgpiutang;

		loadview($data);
	}

    function rangeWeek ($datestr) {
        date_default_timezone_set (date_default_timezone_get());
        $dt = strtotime ($datestr);
        return array (
            "1" => date('N', $dt) == 1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt)),
            "2" => date('N', $dt) == 2 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday +1 day', $dt)),
            "3" => date('N', $dt) == 3 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday +2 days', $dt)),
            "4" => date('N', $dt) == 4 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday +3 days', $dt)),
            "5" => date('N', $dt) == 5 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday +4 days', $dt)),
            "6" => date('N', $dt) == 6 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday +5 days', $dt)),
            "7" => date('N', $dt) == 7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt))
        );
    }

    function hutangLunasTahun($m, $y)
    {
        $sql = "SELECT SUM(k.TotalTransaksi) AS Total
            FROM transpembelian b
            LEFT JOIN transaksikas k ON b.IDTransBeli = k.NoRef_Sistem
            WHERE b.StatusBayar != 'BELUM'
            AND b.StatusProses = 'DONE'
            AND YEAR(b.TanggalPembelian) = '$y'
            AND MONTH(b.TanggalPembelian) = '$m'
            AND b.IsVoid = 0";
        return $this->db->query($sql)->row_array()['Total'];
    }

    function hutangTahun($m, $y)
    {
        $sql = "SELECT b.TotalTagihan - SUM(if(b.StatusBayar = 'SEBAGIAN', k.TotalTransaksi, 0)) AS Total
            FROM transpembelian b
            LEFT JOIN transaksikas k ON b.IDTransBeli = k.NoRef_Sistem
            WHERE b.StatusBayar != 'LUNAS'
            AND b.StatusProses = 'DONE'
            AND YEAR(b.TanggalPembelian) = '$y'
            AND MONTH(b.TanggalPembelian) = '$m'
            AND b.IsVoid = 0
            GROUP BY b.IDTransBeli";
        $sqlr = $this->db->query($sql)->result_array();
        $total = 0;
        if ($sqlr) {
            foreach ($sqlr as $key) {
                $total += $key['Total'];
            }
        }
        return $total;
    }

    function piutangLunasTahun($m, $y)
    {
        $sql = "SELECT SUM(k.TotalTransaksi) AS Total
            FROM transpenjualan j
            LEFT JOIN transaksikas k ON j.IDTransJual = k.NoRef_Sistem
            WHERE j.StatusBayar != 'BELUM'
            AND j.StatusProses = 'DONE'
            AND LEFT(j.IDTransJual, 3) = 'TJL'
            AND YEAR(j.TanggalPenjualan) = '$y'
            AND MONTH(j.TanggalPenjualan) = '$m'";
        return $this->db->query($sql)->row_array()['Total'];
    }

    function piutangTahun($m, $y)
    {
        $sql = "SELECT j.TotalTagihan - SUM(if(j.StatusBayar = 'SEBAGIAN', k.TotalTransaksi, 0)) AS Total
            FROM transpenjualan j
            LEFT JOIN transaksikas k ON j.IDTransJual = k.NoRef_Sistem
            WHERE j.StatusBayar != 'LUNAS'
            AND j.StatusProses = 'DONE'
            AND LEFT(j.IDTransJual, 3) = 'TJL'
            AND YEAR(j.TanggalPenjualan) = '$y'
            AND MONTH(j.TanggalPenjualan) = '$m'
            GROUP BY j.IDTransJual";
        $sqlr = $this->db->query($sql)->result_array();
        $total = 0;
        if ($sqlr) {
            foreach ($sqlr as $key) {
                $total += $key['Total'];
            }
        }
        return $total;
    }

    function hutangLunasBulan($r, $y, $m)
    {
        $ym = $y.'-'.$m;
        $tglawal = $ym . '-' . substr($r, 0, 2);
        $tglakhir = $ym . '-' . substr($r, 3, 2);
        $sql = "SELECT SUM(k.TotalTransaksi) AS Total
            FROM transpembelian b
            LEFT JOIN transaksikas k ON b.IDTransBeli = k.NoRef_Sistem
            WHERE b.StatusBayar != 'BELUM'
            AND b.StatusProses = 'DONE'
            AND DATE(b.TanggalPembelian) BETWEEN '$tglawal' AND '$tglakhir'
            AND b.IsVoid = 0";
        return $this->db->query($sql)->row_array()['Total'];
    }

    function hutangBulan($r, $y, $m)
    {
        $ym = $y.'-'.$m;
        $tglawal = $ym . '-' . substr($r, 0, 2);
        $tglakhir = $ym . '-' . substr($r, 3, 2);
        $sql = "SELECT b.TotalTagihan - SUM(if(b.StatusBayar = 'SEBAGIAN', k.TotalTransaksi, 0)) AS Total
            FROM transpembelian b
            LEFT JOIN transaksikas k ON b.IDTransBeli = k.NoRef_Sistem
            WHERE b.StatusBayar != 'LUNAS'
            AND b.StatusProses = 'DONE'
            AND DATE(b.TanggalPembelian) BETWEEN '$tglawal' AND '$tglakhir'
            AND b.IsVoid = 0
            GROUP BY b.IDTransBeli";
        $sqlr = $this->db->query($sql)->result_array();
        $total = 0;
        if ($sqlr) {
            foreach ($sqlr as $key) {
                $total += $key['Total'];
            }
        }
        return $total;
    }

    function piutangLunasBulan($r, $y, $m)
    {
        $ym = $y.'-'.$m;
        $tglawal = $ym . '-' . substr($r, 0, 2);
        $tglakhir = $ym . '-' . substr($r, 3, 2);
        $sql = "SELECT SUM(k.TotalTransaksi) AS Total
            FROM transpenjualan j
            LEFT JOIN transaksikas k ON j.IDTransJual = k.NoRef_Sistem
            WHERE j.StatusBayar != 'BELUM'
            AND j.StatusProses = 'DONE'
            AND LEFT(j.IDTransJual, 3) = 'TJL'
            AND DATE(j.TanggalPenjualan) BETWEEN '$tglawal' AND '$tglakhir'";
        return $this->db->query($sql)->row_array()['Total'];
    }

    function piutangBulan($r, $y, $m)
    {
        $ym = $y.'-'.$m;
        $tglawal = $ym . '-' . substr($r, 0, 2);
        $tglakhir = $ym . '-' . substr($r, 3, 2);
        $sql = "SELECT j.TotalTagihan - SUM(if(j.StatusBayar = 'SEBAGIAN', k.TotalTransaksi, 0)) AS Total
            FROM transpenjualan j
            LEFT JOIN transaksikas k ON j.IDTransJual = k.NoRef_Sistem
            WHERE j.StatusBayar != 'LUNAS'
            AND j.StatusProses = 'DONE'
            AND LEFT(j.IDTransJual, 3) = 'TJL'
            AND DATE(j.TanggalPenjualan) BETWEEN '$tglawal' AND '$tglakhir'
            GROUP BY j.IDTransJual";
        $sqlr = $this->db->query($sql)->result_array();
        $total = 0;
        if ($sqlr) {
            foreach ($sqlr as $key) {
                $total += $key['Total'];
            }
        }
        return $total;
    }

    function hutangLunasMinggu($d)
    {
        $sql = "SELECT SUM(k.TotalTransaksi) AS Total
            FROM transpembelian b
            LEFT JOIN transaksikas k ON b.IDTransBeli = k.NoRef_Sistem
            WHERE b.StatusBayar != 'BELUM'
            AND b.StatusProses = 'DONE'
            AND DATE(b.TanggalPembelian) = '$d'
            AND b.IsVoid = 0";
        return $this->db->query($sql)->row_array()['Total'];
    }

    function hutangMinggu($d)
    {
        $sql = "SELECT b.TotalTagihan - SUM(if(b.StatusBayar = 'SEBAGIAN', k.TotalTransaksi, 0)) AS Total
            FROM transpembelian b
            LEFT JOIN transaksikas k ON b.IDTransBeli = k.NoRef_Sistem
            WHERE b.StatusBayar != 'LUNAS'
            AND b.StatusProses = 'DONE'
            AND DATE(b.TanggalPembelian) = '$d'
            AND b.IsVoid = 0
            GROUP BY b.IDTransBeli";
        $sqlr = $this->db->query($sql)->result_array();
        $total = 0;
        if ($sqlr) {
            foreach ($sqlr as $key) {
                $total += $key['Total'];
            }
        }
        return $total;
    }

    function piutangLunasMinggu($d)
    {
        $sql = "SELECT SUM(k.TotalTransaksi) AS Total
            FROM transpenjualan j
            LEFT JOIN transaksikas k ON j.IDTransJual = k.NoRef_Sistem
            WHERE j.StatusBayar != 'BELUM'
            AND j.StatusProses = 'DONE'
            AND LEFT(j.IDTransJual, 3) = 'TJL'
            AND DATE(j.TanggalPenjualan) = '$d'";
        return $this->db->query($sql)->row_array()['Total'];
    }

    function piutangMinggu($d)
    {
        $sql = "SELECT j.TotalTagihan - SUM(if(j.StatusBayar = 'SEBAGIAN', k.TotalTransaksi, 0)) AS Total
            FROM transpenjualan j
            LEFT JOIN transaksikas k ON j.IDTransJual = k.NoRef_Sistem
            WHERE j.StatusBayar != 'LUNAS'
            AND j.StatusProses = 'DONE'
            AND LEFT(j.IDTransJual, 3) = 'TJL'
            AND DATE(j.TanggalPenjualan) = '$d'
            GROUP BY j.IDTransJual";
        $sqlr = $this->db->query($sql)->result_array();
        $total = 0;
        if ($sqlr) {
            foreach ($sqlr as $key) {
                $total += $key['Total'];
            }
        }
        return $total;
    }

    function random_color_part() {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }

    function random_color() {
        return $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
    }
}
