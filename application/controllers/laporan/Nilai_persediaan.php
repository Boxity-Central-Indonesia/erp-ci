<?php
defined('BASEPATH') or exit('No direct script access allowed');

class nilai_persediaan extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'itemtransaksibarang i';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[38]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[38]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'lapnilaibrg';
            $data['title'] = 'Laporan Persediaan Barang';
            $data['view'] = 'laporan/v_nilai_persediaan';
            $data['scripts'] = 'laporan/s_nilai_persediaan';

            $data['dtgudang'] = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstgudang',
                    'where' => [['KodeGudang !=' => null]],
                ]
            );

            $dtjenis = [
                'select' => '*',
                'from' => 'mstjenisbarang',
                'where' => [['IsAktif !=' => null]],
                'order_by' => 'KodeJenis'
            ];
            $data['dtjenis'] = $this->crud->get_rows($dtjenis);

            $data['gudang'] = base64_decode($this->input->get('gudang'));

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstbarang br';

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (br.KodeManual LIKE '%$cari%' OR br.NamaBarang LIKE '%$cari%')";
            }

            $jenis   = $this->input->get('jenis');
            if ($jenis != '') {
                $configData['where'] = [['br.KodeJenis' => $jenis]];
            }

            $configData['join'] = [
                [
                    'table' => ' itemtransaksibarang i',
                    'on' => "i.KodeBarang = br.KodeBarang",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang ga',
                    'on' => "ga.KodeGudang = i.GudangAsal",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstgudang gt',
                    'on' => "gt.KodeGudang = i.GudangTujuan",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstjenisbarang j',
                    'on' => "j.KodeJenis = br.KodeJenis",
                    'param' => 'LEFT',
                ],
            ];

            $stok    = $this->input->get('stock');
            ## select -> fill with all column you need
            $gudang = $this->input->get('gudang');
            if ($gudang != '') {
                $configData['selected_column'] = [
                    'br.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang', 'br.NilaiHPP', 'br.KodeJenis', 'br.KodeManual', 'j.NamaJenisBarang', 'i.GudangAsal', 'i.GudangTujuan', 'SUM(IF(i.GudangTujuan = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.GudangAsal = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok'
                ];
                $configData['display_column'] = [
                    false,
                    'br.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang', 'br.NilaiHPP', 'br.KodeJenis', 'br.KodeManual', 'j.NamaJenisBarang', 'i.GudangAsal', 'i.GudangTujuan', 'SUM(IF(i.GudangTujuan = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.GudangAsal = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok',
                    false
                ];

                $configData['selectColumnCount'] = 'count(*) as allcount, SUM(IF(i.GudangTujuan = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.GudangAsal = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok';

                if ($stok == '') {
                    $configData['having'][] = "( Stok > 0)";
                }
            } else {
                $configData['selected_column'] = [
                    'br.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang', 'br.NilaiHPP', 'br.KodeJenis', 'br.KodeManual', 'j.NamaJenisBarang', 'i.GudangAsal', 'i.GudangTujuan', 'SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok'
                ];
                $configData['display_column'] = [
                    false,
                    'br.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang', 'br.NilaiHPP', 'br.KodeJenis', 'br.KodeManual', 'j.NamaJenisBarang', 'i.GudangAsal', 'i.GudangTujuan', 'SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok',
                    false
                ];

                $configData['selectColumnCount'] = 'count(*) as allcount, SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok';

                if ($stok == '') {
                    $configData['having'][] = "( Stok > 0)";
                }
            }
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['group_by'] = 'br.KodeBarang';
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'br.KodeBarang';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];

            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 38; //FiturID di tabel serverfitur
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
                $gudang = $this->crud->get_rows([
                    'select' => '*',
                    'from' => 'mstgudang',
                    'where' => [['KodeGudang !=' => null]],
                ]);
                $listgudang = [];
                foreach ($gudang as $gdg) {
                    $stok = $this->lokasi->get_stok_per_gudang($gdg['KodeGudang'], $temp['KodeBarang']);
                    $listgudang[] = [
                        'NamaGudang' => $gdg['NamaGudang'],
                        'StokGudang' => isset($stok['stok']) ? $stok['stok'] : 0,
                        'SatuanBrg'  => isset($stok['SatuanBarang']) ? $stok['SatuanBarang'] : ''
                    ];

                }
                $temp['listgudang'] = $listgudang;

                $temp['no'] = ++$num_start_row;
                $temp['TotalHPP'] = $temp['NilaiHPP'] * $temp['Stok'];
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function get_total_hpp()
    {
        $cari = $this->input->get('cari');
        $gudang = $this->input->get('gudang');
        $jenis = $this->input->get('jenis');
        $stock = $this->input->get('stock');

        $where1 = "WHERE br.IsAktif = 1";
        $where2 = ($jenis != '' && $jenis != null) ? " AND br.KodeJenis = '$jenis'" : " ";
        $where3 = ($cari != '' && $cari != null) ? " AND br.KodeManual LIKE '%$cari%' OR br.NamaBarang LIKE '%$cari%'" : " ";
        $where = $where1 . $where2 . $where3;

        $sql = "SELECT br.KodeBarang, br.NamaBarang, br.SatuanBarang, br.NilaiHPP, br.KodeJenis, br.KodeManual, j.NamaJenisBarang
            FROM mstbarang br
            LEFT JOIN itemtransaksibarang i ON br.KodeBarang = br.KodeBarang
            LEFT JOIN mstgudang ga ON i.GudangAsal = ga.KodeGudang
            LEFT JOIN mstgudang gt ON i.GudangTujuan = gt.KodeGudang
            LEFT JOIN mstjenisbarang j ON br.KodeJenis = j.KodeJenis
            $where
            GROUP BY br.KodeBarang";
        $datas = $this->db->query($sql)->result_array();
        $model = [];
        $totalhpp = 0;
        foreach ($datas as $key => $value) {
            $model[$key] = $value;
            $model[$key]['Stok'] = ($gudang != '' && $gudang != null) ? $this->lokasi->get_stok_per_gudang($gudang, $value['KodeBarang'])['stok'] : $this->lokasi->get_stok_asli($value['KodeBarang'])['stok'];
            if ($stock == null || $stock == '') {
                if ($model[$key]['Stok'] >= 0) {
                    $totalhpp += $model[$key]['NilaiHPP'] * $model[$key]['Stok'];
                }
            } else {
                $totalhpp += $model[$key]['NilaiHPP'] * $model[$key]['Stok'];
            }
        }

        echo json_encode([
            'status' => true,
            'total' => $totalhpp
        ]);
    }

    public function cetak()
    {
        $kodegudang   = base64_decode($this->input->get('gudang'));
        $kodejenis   = base64_decode($this->input->get('jenis'));
        $cari   = $this->input->get('cari');
        $jenisstok   = base64_decode($this->input->get('stock'));
        $data['src_url'] = base_url('laporan/nilai_persediaan?gudang='.$this->input->get('gudang').'&jenis='.$this->input->get('jenis').'&cari='.$this->input->get('cari').'&stock='.$this->input->get('stock'));

        $data['gudang'] = $this->crud->get_one_row(
            [
                'select' => 'g.KodeGudang, g.NamaGudang',
                'from' => 'mstgudang g',
                'where' => [['g.KodeGudang' => $kodegudang]],
            ]
        );

        $where1 = "WHERE br.IsAktif = 1";
        $where2 = ($kodejenis != '' && $kodejenis != null) ? " AND br.KodeJenis = '$kodejenis'" : " ";
        $where3 = ($cari != '' && $cari != null) ? " AND br.KodeManual LIKE '%$cari%' OR br.NamaBarang LIKE '%$cari%'" : " ";
        $where = $where1 . $where2 . $where3;

        $sql = "SELECT br.KodeBarang, br.NamaBarang, br.SatuanBarang, br.NilaiHPP, br.KodeJenis, br.KodeManual, j.NamaJenisBarang
            FROM mstbarang br
            LEFT JOIN itemtransaksibarang i ON br.KodeBarang = br.KodeBarang
            LEFT JOIN mstgudang ga ON i.GudangAsal = ga.KodeGudang
            LEFT JOIN mstgudang gt ON i.GudangTujuan = gt.KodeGudang
            LEFT JOIN mstjenisbarang j ON br.KodeJenis = j.KodeJenis
            $where
            GROUP BY br.KodeBarang";
        $datas = $this->db->query($sql)->result_array();
        $model = [];
        $stocking = 0;
        foreach ($datas as $key => $value) {
            $stocking = ($kodegudang != null) ? $this->lokasi->get_stok_per_gudang($kodegudang, $value['KodeBarang'])['stok'] : $this->lokasi->get_stok_asli($value['KodeBarang'])['stok'];
            if ($jenisstok == null || $jenisstok == '') {
                if ($stocking > 0) {
                    $model[$key] = $value;
                    $model[$key]['Stok'] = $stocking;
                }
            } else {
                $model[$key] = $value;
                $model[$key]['Stok'] = $stocking;
            }
        }
        $data['model'] = $model;
        $data['kodegudang'] = $kodegudang;
        $data['jenisstok'] = $jenisstok;

        $this->load->library('Pdf');
        $this->load->view('laporan/cetak_nilai_persediaan', $data);
    }

    public function simpan_nilai_persediaan()
    {
        $bulan = $this->input->get('bulan');
        $m = date('m', strtotime($bulan));
        $y = date('Y', strtotime($bulan));
        $d = cal_days_in_month(CAL_GREGORIAN,$m,$y);
        $startdate = date('Y-m-d', strtotime($y . '-' . $m . '-' . '01'));
        $enddate   = date('Y-m-d', strtotime($y . '-' . $m . '-' . $d));
        $date = new DateTime($startdate);
        $enddate_lastmonth = $date->modify("last day of previous month")->format("Y-m-d");

        $sql = "SELECT br.KodeBarang, br.NamaBarang, br.SatuanBarang, br.NilaiHPP, br.KodeJenis, br.KodeManual, j.NamaJenisBarang
            FROM mstbarang br
            LEFT JOIN itemtransaksibarang i ON br.KodeBarang = br.KodeBarang
            LEFT JOIN mstgudang ga ON i.GudangAsal = ga.KodeGudang
            LEFT JOIN mstgudang gt ON i.GudangTujuan = gt.KodeGudang
            LEFT JOIN mstjenisbarang j ON br.KodeJenis = j.KodeJenis
            WHERE br.IsAktif = 1
            GROUP BY br.KodeBarang";
        $datas = $this->db->query($sql)->result_array();
        $model = [];
        $totalhpp = 0;
        foreach ($datas as $key => $value) {
            $model[$key] = $value;
            $model[$key]['Stok'] = $this->lokasi->get_stok_asli($value['KodeBarang'])['stok'];
            $totalhpp += $model[$key]['NilaiHPP'] * $model[$key]['Stok'];
        }

        $this->db->trans_begin();
        // simpan penjurnalan & item jurnal
        $savejurnal = $this->simpan_jurnal($startdate, $enddate, $enddate_lastmonth);

        $data = [
            'Tanggal' => $enddate,
            'Nominal' => $totalhpp
        ];
        $result = $this->crud->insert_or_update($data, 'nilaipersediaanbarang');
        if ($savejurnal && $result) {
            $this->db->trans_commit();
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil menyimpan data."
            ]);
        } else {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menyimpan data."
            ]);
        }
    }

    public function simpan_persediaan_cron()
    {
        $bulan = date('Y-m');
        $m = date('m', strtotime($bulan));
        $y = date('Y', strtotime($bulan));
        $d = cal_days_in_month(CAL_GREGORIAN,$m,$y);
        $startdate = date('Y-m-d', strtotime($y . '-' . $m . '-' . '01'));
        $enddate   = date('Y-m-d', strtotime($y . '-' . $m . '-' . $d));
        $date = new DateTime($startdate);
        $enddate_lastmonth = $date->modify("last day of previous month")->format("Y-m-d");

        $sql = "SELECT br.KodeBarang, br.NamaBarang, br.SatuanBarang, br.NilaiHPP, br.KodeJenis, br.KodeManual, j.NamaJenisBarang
            FROM mstbarang br
            LEFT JOIN itemtransaksibarang i ON br.KodeBarang = br.KodeBarang
            LEFT JOIN mstgudang ga ON i.GudangAsal = ga.KodeGudang
            LEFT JOIN mstgudang gt ON i.GudangTujuan = gt.KodeGudang
            LEFT JOIN mstjenisbarang j ON br.KodeJenis = j.KodeJenis
            WHERE br.IsAktif = 1
            GROUP BY br.KodeBarang";
        $datas = $this->db->query($sql)->result_array();
        $model = [];
        $totalhpp = 0;
        foreach ($datas as $key => $value) {
            $model[$key] = $value;
            $model[$key]['Stok'] = $this->lokasi->get_stok_asli($value['KodeBarang'])['stok'];
            $totalhpp += $model[$key]['NilaiHPP'] * $model[$key]['Stok'];
        }

        $this->db->trans_begin();
        // simpan penjurnalan & item jurnal
        $savejurnal = $this->simpan_jurnal($startdate, $enddate, $enddate_lastmonth);

        $data = [
            'Tanggal' => $enddate,
            'Nominal' => $totalhpp
        ];
        $result = $this->crud->insert_or_update($data, 'nilaipersediaanbarang');
        if ($savejurnal && $result) {
            $this->db->trans_commit();
            $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(['status' => true]));
        } else {
            $this->db->trans_rollback();
            $this->output->set_status_header(500)->set_content_type('application/json')->set_output(json_encode(['status' => false]));
        }
    }

    public function simpan_jurnal($startdate, $enddate, $enddate_lastmonth)
    {
        $startmonth = date("Ym", strtotime($enddate_lastmonth));
        $endmonth = date("Ym", strtotime($enddate));
        $noreftrans = "PSY-" . $startmonth . "-" . $endmonth;

        $cekjurnal = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transjurnal',
            'where' => [['NoRefTrans' => $noreftrans]],
        ]);

        $insertjurnalitem = [];
        if ($cekjurnal == null) {
            $prefix = "JRN-" . date("Ym");
            $list_jurnal['IDTransJurnal'] = $this->crud->get_kode([
                'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                'from' => 'transjurnal',
                'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'IDTransJurnal DESC',
                'prefix' => $prefix
            ]);
            $list_jurnal['KodeTahun'] = $this->akses->get_tahun_aktif();
            $list_jurnal['TglTransJurnal'] = date("Y-m-d H:i");
            $list_jurnal['TipeJurnal'] = "PENYESUAIAN";
            $list_jurnal['NoRefTrans'] = $noreftrans;
            $list_jurnal['UserName'] = $this->session->userdata('UserName');
            $blnawal = strtoupper(substr(date_indo($enddate_lastmonth), 3));
            $blnakhir = strtoupper(substr(date_indo($enddate), 3));
            $list_jurnal['NarasiJurnal'] = "PENYESUAIN SALDO AKHIR " . $blnawal . " AWAL " . $blnakhir;
            $list_jurnal['NominalTransaksi'] = $this->hitung_pembelian($startdate, $enddate);
            $insertjurnal = $this->crud->insert($list_jurnal, 'transjurnal');
    
            $persediaanawal = $this->hitung_persediaan($enddate_lastmonth);
            $persediaanakhir = $this->hitung_persediaan($enddate);
            $dtakun = [
                [
                    "KodeAkun" => "1.02",
                    "NamaAkun" => $this->lokasi->getnamaakun("1.02"),
                    "NoUrut"   => 1
                ],
                [
                    "KodeAkun" => "7.01",
                    "NamaAkun" => $this->lokasi->getnamaakun("7.01"),
                    "NoUrut"   => 2
                ]
            ];
            if ($persediaanakhir >= $persediaanawal) {
                // jika pakhir > pawal maka persediaan barang debet & pembelian kredit
                foreach ($dtakun as $value) {
                    $list_item = [
                        'NoUrut' => $value['NoUrut'],
                        'IDTransJurnal' => $list_jurnal['IDTransJurnal'],
                        'KodeTahun' => $list_jurnal['KodeTahun'],
                        'KodeAkun' => $value['KodeAkun'],
                        'NamaAkun' => $value['NamaAkun'],
                        'Debet' => ($value['NoUrut'] == 1) ? $list_jurnal['NominalTransaksi'] : 0,
                        'Kredit' => ($value['NoUrut'] == 2) ? $list_jurnal['NominalTransaksi'] : 0,
                        'Uraian' => "PENYESUAIN SALDO AKHIR " . $blnawal . " AWAL " . $blnakhir
                    ];
                    $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                }
            } else {
                //  jika pakhir < pawal maka persediaan barang kredit & pembelian debet 
                foreach ($dtakun as $value) {
                    $list_item = [
                        'NoUrut' => $value['NoUrut'],
                        'IDTransJurnal' => $list_jurnal['IDTransJurnal'],
                        'KodeTahun' => $list_jurnal['KodeTahun'],
                        'KodeAkun' => $value['KodeAkun'],
                        'NamaAkun' => $value['NamaAkun'],
                        'Debet' => ($value['NoUrut'] == 2) ? $list_jurnal['NominalTransaksi'] : 0,
                        'Kredit' => ($value['NoUrut'] == 1) ? $list_jurnal['NominalTransaksi'] : 0,
                        'Uraian' => "PENYESUAIN SALDO AKHIR " . $blnawal . " AWAL " . $blnakhir
                    ];
                    $insertjurnalitem[] = $this->crud->insert($list_item, 'transjurnalitem');
                }
            }
        } else {
            $insertjurnalitem = true;
        }

        return $insertjurnalitem;
    }

    public function hitung_persediaan($tgl)
    {
        $sql1 = "SELECT COALESCE(SUM(Nominal), 0) AS Nominal
            FROM nilaipersediaanbarang
            WHERE DATE(Tanggal) = '$tgl'";
        $result = (double)$this->db->query($sql1)->row_array()['Nominal'];

        return $result;
    }

    public function hitung_pembelian($tglawal, $tglakhir)
    {
        $sql2 = "SELECT COALESCE(SUM(i.Debet), 0) AS Total -- - COALESCE(SUM(i.Kredit), 0)
            FROM transjurnalitem i
            JOIN transjurnal t ON i.IDTransJurnal = t.IDTransJurnal
            WHERE LEFT(i.KodeAkun, 1) = 7
            AND DATE(t.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir'";
        $result = $this->db->query($sql2)->row_array()['Total'];

        return $result;
    }
}

