<?php
defined('BASEPATH') or exit('No direct script access allowed');

class tutup_buku extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[60]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[60]);
        $data['tahunaktif'] = $this->akses->get_tahun_aktif();
        $data['tahunanggaran'] = $this->crud->get_rows([
            'select' => '*',
            'from' => 'msttahunanggaran',
            'where' => [['IsAktif !=' => null]],
        ]);

        $kodetahun = ($this->input->get('kodetahun') != null) ? $this->input->get('kodetahun') : $this->akses->get_tahun_aktif();
        $data['kodetahun'] = $kodetahun;

        $tgl = ($this->input->get('tgl') != null) ? $this->input->get('tgl') : date('Y-m-d');
        $tgl_filter = date('Y-m-d', strtotime($tgl));
        $explode = explode("-", $tgl_filter);
        $start_date = $explode[0] . "-" . $explode[1] . "-01";
        $data['tgl'] = $tgl;

        $result = $this->lokasi->get_tutup_buku($kodetahun, $tgl_filter);

        $labarugi = $this->lokasi->get_laba_rugi($kodetahun, $tgl_filter)['labarugi'];
        $data['totalhpp'] = $this->lokasi->get_laba_rugi($kodetahun, $tgl_filter)['totalhpp'];

        $getakunneraca = $this->get_setting_kode_akun('Neraca', 'Laba Rugi', 'Kredit');
        $kodeakunneraca = isset($getakunneraca) ? $getakunneraca['KodeAkun'] : 0;

        $model = [];
        foreach ($result as $key) {
            $jenisakuninduk = $this->get_induk($key['AkunInduk']);
            $jenisakun = $key['JenisAkun'];
            $nominalumum = ($jenisakuninduk == $jenisakun) ? $key['NominalUmum'] : $key['NominalUmum'] * -1;
            $model[] = [
                'KodeAkun'              => $key['KodeAkun'],
                'NamaAkun'              => $key['NamaAkun'],
                'JenisAkunInduk'        => $jenisakuninduk,
                'JenisAkun'             => $jenisakun,
                'NominalUmum'           => ($key['KodeAkun'] == $kodeakunneraca) ? $labarugi : $nominalumum + $key['SaldoNeraca'],
                'NominalPenyesuaian'    => $key['NominalPenyesuaian']
            ];
        }
        $data['model'] = $model;
        // die(json_encode($model));

        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['title'] = 'Tutup Buku Tahun ' . $kodetahun;
        $data['menu'] = 'tutupbuku';
        $data['view'] = 'akuntansi/v_tutup_buku';
        $data['scripts'] = 'akuntansi/s_tutup_buku';
        loadview($data);
    }

    public function get_induk($akuninduk)
    {
        $induk = substr($akuninduk, 0, 1);
        if ($induk == 2 || $induk == 3 || $induk == 4) {
            $res = 'Kredit';
        } else {
            $res = 'Debit';
        }
        return $res;
    }

    public function simpan()
    {
        $this->db->trans_begin();
        $is_pendapatan_sukses = false;
        $is_pembiayaan_sukses = false;

        $kodetahun      = $this->input->get('KodeTahun');
        $tgltransjurnal = $this->input->get('TglTransJurnal');
        $explode = explode("-", $tgltransjurnal);
        $start_date = $explode[0] . "-" . $explode[1] . "-01";

        $kode_akun_pendapatan = $this->get_setting_kode_akun('Penutup', 'Pendapatan', 'Kredit'); // kode akun lawan pendapatan
        $kode_akun_pembiayaan = $this->get_setting_kode_akun('Penutup', 'Pembiayaan', 'Debet'); // kode akun lawan pembiayaan
        $kode_akun_laba       = $this->get_setting_kode_akun_labarugi('Penutup', 'Laba'); // kode akun untuk laba
        $kode_akun_rugi       = $this->get_setting_kode_akun_labarugi('Penutup', 'Rugi'); // kode akun untuk rugi
        $hasillabarugi        = $this->lokasi->get_laba_rugi($kodetahun, $tgltransjurnal)['labarugi']; // perhitungan laba rugi
        $dataneraca           = $this->lokasi->get_akun_neraca($kodetahun, $tgltransjurnal); // jurnal neraca dengan kode akun 1, 2, 3, 7
        $datajurnal_labarugi  = $this->lokasi->get_akun_labarugi($kodetahun, $tgltransjurnal); // jurnal dengan kode akun 4, 5, 6

        ## Jurnal Penutup Pendapatan ##
        $prefix1 = "JRN-" . date("Ym");
        $jurnalpendapatan['IDTransJurnal'] = $this->crud->get_kode([
            'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
            'from' => 'transjurnal',
            'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix1]],
            'limit' => 1,
            'order_by' => 'IDTransJurnal DESC',
            'prefix' => $prefix1
        ]);
        $jurnalpendapatan['KodeTahun'] = $kodetahun;
        $jurnalpendapatan['TglTransJurnal'] = $tgltransjurnal;
        $jurnalpendapatan['TipeJurnal'] = "PENUTUP";
        $jurnalpendapatan['NarasiJurnal'] = "Jurnal Penutup Pendapatan Tahun " . $kodetahun;
        $jurnalpendapatan['NominalTransaksi'] = 0;
        $jurnalpendapatan['UserName'] = $this->session->userdata('UserName');
        $penutup_pendapatan = $this->crud->insert($jurnalpendapatan, 'transjurnal');

        // item jurnal penutup pendapatan
        $nourutpendapatan = 1;
        $total_pendapatan = 0;
        foreach ($datajurnal_labarugi as $key) {
            if (substr($key['KodeAkun'], 0, 1) == 4 && $key['Nominal'] != 0) {
                $itempendapatan = [
                    'NoUrut'        => $nourutpendapatan,
                    'IDTransJurnal' => $jurnalpendapatan['IDTransJurnal'],
                    'KodeTahun'     => $jurnalpendapatan['KodeTahun'],
                    'KodeAkun'      => $key['KodeAkun'],
                    'NamaAkun'      => $key['NamaAkun'],
                    'Debet'         => $key['Nominal'],
                    'Kredit'        => 0,
                    'Uraian'        => "Jurnal Tutup Buku Tahun " . $jurnalpendapatan['KodeTahun']
                ];
                $simpanitempendapatan[] = $this->crud->insert($itempendapatan, 'transjurnalitem');
                $total_pendapatan += $key['Nominal'];

                $nourutpendapatan++;
            }
        }

        // lawan jurnal penutup pendapatan
        if ($nourutpendapatan > 0) {
            $lawanpendapatan = [
                'NoUrut'        => $nourutpendapatan,
                'IDTransJurnal' => $jurnalpendapatan['IDTransJurnal'],
                'KodeTahun'     => $jurnalpendapatan['KodeTahun'],
                'KodeAkun'      => $kode_akun_pendapatan['KodeAkun'],
                'NamaAkun'      => $kode_akun_pendapatan['NamaAkun'],
                'Debet'         => 0,
                'Kredit'        => $total_pendapatan,
                'Uraian'        => "Jurnal Tutup Buku Tahun " . $jurnalpendapatan['KodeTahun']
            ];
            $simpanlawanpendapatan = $this->crud->insert($lawanpendapatan, 'transjurnalitem');
            $is_pendapatan_sukses = true;
        }

        ## Jurnal Penutup Pembiayaan ##
        $prefix2 = "JRN-" . date("Ym");
        $jurnalpembiayaan['IDTransJurnal'] = $this->crud->get_kode([
            'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
            'from' => 'transjurnal',
            'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix2]],
            'limit' => 1,
            'order_by' => 'IDTransJurnal DESC',
            'prefix' => $prefix2
        ]);
        $jurnalpembiayaan['KodeTahun'] = $kodetahun;
        $jurnalpembiayaan['TglTransJurnal'] = $tgltransjurnal;
        $jurnalpembiayaan['TipeJurnal'] = "PENUTUP";
        $jurnalpembiayaan['NarasiJurnal'] = "Jurnal Penutup Pembiayaan Tahun " . $kodetahun;
        $jurnalpembiayaan['NominalTransaksi'] = 0;
        $jurnalpembiayaan['UserName'] = $this->session->userdata('UserName');
        $penutup_pembiayaan = $this->crud->insert($jurnalpembiayaan, 'transjurnal');

        // item jurnal penutup pembiayaan
        $nourutpembiayaan = 1;
        $total_pembiayaan = 0;
        foreach ($datajurnal_labarugi as $key) {
            if ($key['Nominal'] != 0) {
                if (substr($key['KodeAkun'], 0, 1) == 5 || substr($key['KodeAkun'], 0, 1) == 6) {
                    $itempembiayaan = [
                        'NoUrut'        => $nourutpembiayaan,
                        'IDTransJurnal' => $jurnalpembiayaan['IDTransJurnal'],
                        'KodeTahun'     => $jurnalpembiayaan['KodeTahun'],
                        'KodeAkun'      => $key['KodeAkun'],
                        'NamaAkun'      => $key['NamaAkun'],
                        'Debet'         => 0,
                        'Kredit'        => $key['Nominal'],
                        'Uraian'        => "Jurnal Tutup Buku Tahun " . $jurnalpembiayaan['KodeTahun']
                    ];
                    $simpanitempembiayaan[] = $this->crud->insert($itempembiayaan, 'transjurnalitem');
                    $total_pembiayaan += $key['Nominal'];

                    $nourutpembiayaan++;
                }
            }
        }

        // lawan jurnal penutup pembiayaan
        if ($nourutpembiayaan > 0) {
            $lawanpembiayaan = [
                'NoUrut'        => $nourutpembiayaan,
                'IDTransJurnal' => $jurnalpembiayaan['IDTransJurnal'],
                'KodeTahun'     => $jurnalpembiayaan['KodeTahun'],
                'KodeAkun'      => $kode_akun_pembiayaan['KodeAkun'],
                'NamaAkun'      => $kode_akun_pembiayaan['NamaAkun'],
                'Debet'         => $total_pembiayaan,
                'Kredit'        => 0,
                'Uraian'        => "Jurnal Tutup Buku Tahun " . $jurnalpembiayaan['KodeTahun']
            ];
            $simpanlawanpembiayaan = $this->crud->insert($lawanpembiayaan, 'transjurnalitem');
            $is_pembiayaan_sukses = true;
        }

        ## Jurnal Penutup Ikhtisar Laba Rugi ##
        $prefix3 = "JRN-" . date("Ym");
        $jurnal_labarugi['IDTransJurnal'] = $this->crud->get_kode([
            'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
            'from' => 'transjurnal',
            'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix3]],
            'limit' => 1,
            'order_by' => 'IDTransJurnal DESC',
            'prefix' => $prefix3
        ]);
        $jurnal_labarugi['KodeTahun'] = $kodetahun;
        $jurnal_labarugi['TglTransJurnal'] = $tgltransjurnal;
        $jurnal_labarugi['TipeJurnal'] = "PENUTUP";
        $jurnal_labarugi['NarasiJurnal'] = "Jurnal Penutup Ikhtisar Laba Rugi Tahun " . $kodetahun;
        $jurnal_labarugi['NominalTransaksi'] = 0;
        $jurnal_labarugi['UserName'] = $this->session->userdata('UserName');
        $penutup_labarugi = $this->crud->insert($jurnal_labarugi, 'transjurnal');

        // item jurnal penutup labarugi
        $total_labarugi = $total_pendapatan - $total_pembiayaan;
        $kode_akun_labarugi = [];
        if ($total_labarugi >= 0) {
            $kode_akun_labarugi = $kode_akun_laba;
            $nominal_labarugi = $total_labarugi;
            $kodenrc = $this->get_setting_kode_akun('Penutup', 'Laba', 'Kredit');
            $kodeneraca = $kodenrc ? $kodenrc['KodeAkun'] : 0;
        } else {
            $kode_akun_labarugi = $kode_akun_rugi;
            $nominal_labarugi = $total_labarugi * -1;
            $kodenrc = $this->get_setting_kode_akun('Penutup', 'Rugi', 'Debet');
            $kodeneraca = $kodenrc ? $kodenrc['KodeAkun'] : 0;
        }
        $nourut_labarugi = 1;
        $item_labarugi = [];
        foreach ($kode_akun_labarugi as $key) {
            $item_labarugi = [
                'NoUrut'        => $nourut_labarugi,
                'IDTransJurnal' => $jurnal_labarugi['IDTransJurnal'],
                'KodeTahun'     => $jurnal_labarugi['KodeTahun'],
                'KodeAkun'      => $key['KodeAkun'],
                'NamaAkun'      => $key['NamaAkun'],
                'Debet'         => ($key['JenisJurnal'] == 'Debet') ? $nominal_labarugi : 0,
                'Kredit'        => ($key['JenisJurnal'] == 'Kredit') ? $nominal_labarugi : 0,
                'Uraian'        => "Jurnal Tutup Buku Tahun " . $jurnal_labarugi['KodeTahun']
            ];
            $simpanitem_labarugi[] = $this->crud->insert($item_labarugi, 'transjurnalitem');

            $nourut_labarugi++;
        }

        ## Menyimpan Data di Neraca Saldo ##
        $ym = date("Ym");
        if ($dataneraca) {
            $nourut_neraca = 1;
            $neracasaldo = [];
            foreach ($dataneraca as $key) {
                $neracasaldo['BulanTahun']    = $ym;
                $neracasaldo['KodeTahun']     = $kodetahun;
                $neracasaldo['NoUrut']        = $nourut_neraca;
                $neracasaldo['KodeAkun']      = $key['KodeAkun'];
                $neracasaldo['NamaAkun']      = $key['NamaAkun'];
                $neracasaldo['SaldoDebet']    = ($key['KodeAkun'] != $kodeneraca) ? $key['Nominal'] : $hasillabarugi;
                $neracasaldo['SaldoKredit']   = 0;
                $neracasaldo['SaldoAkhir']    = $neracasaldo['SaldoDebet'] - $neracasaldo['SaldoKredit'];
                $neracasaldo['Keterangan']    = 'Tutup Buku Tahun ' . $kodetahun;
                $simpan_neracasaldo[] = $this->crud->insert($neracasaldo, 'neracasaldo');

                $nourut_neraca++;
            }
        }

        ## Menyimpan Data di Neraca Labarugi ##
        if ($datajurnal_labarugi) {
            $nourutt = 1;
            $data_labarugi = [];
            foreach ($datajurnal_labarugi as $key) {
                $data_labarugi['BulanTahun']    = $ym;
                $data_labarugi['KodeTahun']     = $kodetahun;
                $data_labarugi['NoUrut']        = $nourutt;
                $data_labarugi['KodeAkun']      = $key['KodeAkun'];
                $data_labarugi['NamaAkun']      = $key['NamaAkun'];
                $data_labarugi['Debet']         = substr($key['KodeAkun'], 0, 1) == 5 || substr($key['KodeAkun'], 0, 1) == 6 ? $key['Nominal'] : 0;
                $data_labarugi['Kredit']        = substr($key['KodeAkun'], 0, 1) == 4 ? $key['Nominal'] : 0;
                $data_labarugi['Saldo']         = substr($key['KodeAkun'], 0, 1) == 4 ? $data_labarugi['Kredit'] - $data_labarugi['Debet'] : $data_labarugi['Debet'] - $data_labarugi['Kredit'];
                $data_labarugi['Keterangan']    = 'Tutup Buku Tahun ' . $kodetahun;
                $simpan_labarugi[] = $this->crud->insert($data_labarugi, 'neracalabarugi');

                $nourutt++;
            }
        }

        ## Membuat dan Mengaktifkan Tahun Anggaran Baru ##
        $ta_baru = $kodetahun + 1;
        $datatahun = $this->crud->get_one_row([
            'select' => 'KodeTahun',
            'from' => 'msttahunanggaran',
            'where' => [['KodeTahun' => $ta_baru]],
        ]);
        if (!($datatahun)) {
            $dt_ta_baru = [
                'KodeTahun'     => $ta_baru,
                'Keterangan'    => '-',
                'IsAktif'       => 1
            ];
            $updatethnaktif = $this->crud->update(['IsAktif' => 0], [], 'msttahunanggaran');
            $insert_ta_baru = $this->crud->insert($dt_ta_baru, 'msttahunanggaran');
        } else {
            $updatethnaktif = $this->crud->update(['IsAktif' => 0], [], 'msttahunanggaran');
            $update_ta_baru = $this->crud->update(['IsAktif' => 1], ['KodeTahun' => $ta_baru], 'msttahunanggaran');
        }

        if ($is_pendapatan_sukses && $is_pembiayaan_sukses) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => "tambah",
                'JenisTransaksi' => "Tutup Buku Akhir Tahun",
                'Description' => "Tutup Buku Tahun " . $kodetahun
            ]);
            $this->db->trans_commit();
            echo json_encode([
                'status' => true,
                'msg'  => ("Berhasil Menyimpan Data Tutup Buku Tahun " . $kodetahun)
            ]);
        } else {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => false,
                'msg'  => ("Gagal Menyimpan Data Tutup Buku Tahun " . $kodetahun)
            ]);
        }
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

    public function get_setting_kode_akun_labarugi($namatransaksi, $jenistransaksi)
    {
        $data = $this->crud->get_rows([
            'select' => 'd.JenisJurnal, d.KodeAkun, a.NamaAkun',
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
                ]
            ],
        ]);

        return $data;
    }
}
