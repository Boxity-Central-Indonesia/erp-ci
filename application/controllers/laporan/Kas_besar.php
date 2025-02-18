<?php
defined('BASEPATH') or exit('No direct script access allowed');

class kas_besar extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transjurnalitem i';
        checkAccess($this->session->userdata('fiturview')[39]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[39]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'kasbesar';
            $data['title'] = 'Laporan Kas Besar';
            $data['view'] = 'laporan/v_kas_besar';
            $data['scripts'] = 'laporan/s_kas_besar';

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

            $data['kodeakun'] = $this->input->get('kodeakun');
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

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transjurnalitem i';

            $kodeakun   = $this->input->get('kodeakun');
            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                // $configData['filters'][] = " (DATE(j.TanggalPenjualan) BETWEEN '$tglawal' AND '$tglakhir')";
            } else {
                $tglawal = '0000-00-00';
                $tglakhir = '0000-00-00';
            }
            if ($tgl != '' && $kodeakun != '') {
                $configData['where'] = [
                    [
                        'i.KodeAkun' => $kodeakun,
                    ],
                    "(DATE(j.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir')",
                ];
            } else {
                $configData['where'] = [['i.IDTransJurnal' => null]];
            }

            $configData['join'] = [
                [
                    'table' => ' transjurnal j',
                    'on' => "j.IDTransJurnal = i.IDTransJurnal",
                    'param' => 'INNER',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.TglTransJurnal', 'j.IDTransJurnal', 'j.NoRefTrans', 'j.NarasiJurnal', 'i.Debet', 'i.Kredit'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'j.IDTransJurnal';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'j.TglTransJurnal', 'j.IDTransJurnal', 'j.NoRefTrans', 'j.NarasiJurnal', 'i.Debet', 'i.Kredit',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 39; //FiturID di tabel serverfitur
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

            setlocale(LC_ALL, 'IND');

            $kodetahun = $this->akses->get_tahun_aktif();
            $sld = $this->getAwalAkhirsaldo($kodeakun, $tglawal, '<', $kodetahun);
            $saldo = isset($sld) ? $sld : 0;
            foreach ($records as $record) {
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['TglTransJurnal'] = isset($temp['TglTransJurnal']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TglTransJurnal']))) : '';
                $temp['btn_aksi'] = '';

                $saldo += $temp['Debet'] - $temp['Kredit'];
                $temp['Saldo'] = $saldo;

                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    function getAwalsaldo($kodeakun, $tglawal)
    {
        $sql = "SELECT SUM(i.Debet - i.Kredit) AS Saldo
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            WHERE i.KodeAkun = '$kodeakun'
            AND DATE(j.TglTransJurnal) < '$tglawal'
            AND j.TipeJurnal = 'UMUM'";
        return $this->db->query($sql)->row_array()['Saldo'];
    }

    function getAkhirsaldo($kodeakun, $tglakhir)
    {
        $sql  = "SELECT SUM(i.Debet - i.Kredit) AS Saldo
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            WHERE i.KodeAkun = '$kodeakun'
            AND DATE(j.TglTransJurnal) <= '$tglakhir'
            AND j.TipeJurnal = 'UMUM'";
        return $this->db->query($sql)->row_array()['Saldo'];
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

    function getSaldoAwal()
    {
        $kodeakun = $this->input->get('kodeakun');
        $tglawal = $this->input->get('tglawal');
        $kodetahun = $this->input->get('kodetahun');
        $sql = "SELECT SUM(i.Debet - i.Kredit) AS Saldo
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            WHERE i.KodeAkun = '$kodeakun'
            AND i.KodeTahun = '$kodetahun'
            AND DATE(j.TglTransJurnal) < '$tglawal'
            AND j.TipeJurnal != 'PENUTUP'";
        $res = $this->db->query($sql)->row_array();
        $totaljurnal = $res ? $res['Saldo'] : 0;

        $tahunlalu = (int)$kodetahun - 1;
        $nrc = $this->get_saldo_neraca($kodeakun, $tahunlalu);
        $saldoneraca = $nrc ? $nrc : 0;

        $result = $totaljurnal + $saldoneraca;

        echo $result;
    }

    function getSaldoAkhir()
    {
        $kodeakun = $this->input->get('kodeakun');
        $tglakhir = $this->input->get('tglakhir');
        $kodetahun = $this->input->get('kodetahun');
        $sql  = "SELECT SUM(i.Debet - i.Kredit) AS Saldo
            FROM transjurnalitem i
            JOIN transjurnal j ON i.IDTransJurnal = j.IDTransJurnal
            WHERE i.KodeAkun = '$kodeakun'
            AND i.KodeTahun = '$kodetahun'
            AND DATE(j.TglTransJurnal) <= '$tglakhir'
            AND j.TipeJurnal != 'PENUTUP'";
        $res = $this->db->query($sql)->row_array();
        $totaljurnal = $res ? $res['Saldo'] : 0;

        $tahunlalu = (int)$kodetahun - 1;
        $nrc = $this->get_saldo_neraca($kodeakun, $tahunlalu);
        $saldoneraca = $nrc ? $nrc : 0;

        $result = $totaljurnal + $saldoneraca;

        echo $result;
    }

    public function cetak()
    {
        $kodeakun = escape($this->uri->segment(4));
        $tglawal = escape($this->uri->segment(5));
        $tglakhir = escape($this->uri->segment(6));
        $data['src_url'] = base_url('laporan/kas_besar?kodeakun=') . $kodeakun . '&tgl=' . $tglawal . '+-+' . $tglakhir;

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

            $kodetahun = $this->akses->get_tahun_aktif();
            $slda = $this->getAwalAkhirsaldo($kodeakun, $tglawal, '<', $kodetahun);
            $saldoawal = isset($slda) ? $slda : 0;
            $sldb = $this->getAwalAkhirsaldo($kodeakun, $tglakhir, '<=', $kodetahun);
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
