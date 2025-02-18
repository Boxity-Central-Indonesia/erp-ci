<?php
defined('BASEPATH') or exit('No direct script access allowed');

class hp_penjualan extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[41]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[41]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'laphpj';
            $data['title'] = 'Laporan Harga Pokok Penjualan';
            $data['view'] = 'laporan/v_hppenjualan';
            $data['scripts'] = 'laporan/s_hppenjualan';

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'itempenjualan i';

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (j.IDTransJual LIKE '%$cari%' OR j.NoRef_Manual LIKE '%$cari%' OR p.NamaPersonCP LIKE '%$cari%' OR br.NamaBarang LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(j.TanggalPenjualan) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['where'] = [
                [
                    'j.StatusProses' => 'DONE',
                    'LEFT(j.IDTransJual, 3) =' => 'TJL',
                ]
            ];

            $configData['join'] = [
                [
                    'table' => ' transpenjualan j',
                    'on' => "j.IDTransJual = i.IDTransJual",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = j.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.IDTransJual', 'i.NoUrut', 'j.NoRef_Manual', 'j.TanggalPenjualan', 'j.KodePerson', 'p.NamaPersonCP', 'i.KodeBarang', 'br.NamaBarang', 'i.Qty', 'i.HPPSaatJual', 'i.HargaSatuan'
            ];

            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'j.IDTransJual, i.NoUrut';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'j.IDTransJual', 'i.NoUrut', 'j.NoRef_Manual', 'j.TanggalPenjualan', 'j.KodePerson', 'p.NamaPersonCP', 'i.KodeBarang', 'br.NamaBarang', 'i.Qty', 'i.HPPSaatJual', 'i.HargaSatuan',
                false
            ];

            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 41; //FiturID di tabel serverfitur
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
                $temp['TanggalPenjualan'] = shortdate_indo(date('Y-m-d', strtotime($temp['TanggalPenjualan']))) . ' ' . date('H:i', strtotime($temp['TanggalPenjualan']));
                $temp['HPPTotal'] = $temp['HPPSaatJual'] * $temp['Qty'];
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetak()
    {
        $tgltransaksi = escape(base64_decode($this->uri->segment(4)));
        $tgl = explode(" - ", $tgltransaksi);
        $d1 = date('Y-m-d', strtotime('-29 days'));
        $d2 = date('Y-m-d');
        $tglawal = $tgl[0] != '' ? date('Y-m-d', strtotime($tgl[0])) : $d1;
        $tglakhir = isset($tgl[1]) ? date('Y-m-d', strtotime($tgl[1])) : $d2;

        $sql = [
            'select' => 'j.IDTransJual, i.NoUrut, j.NoRef_Manual, j.TanggalPenjualan, j.KodePerson, p.NamaPersonCP, i.KodeBarang, br.NamaBarang, i.Qty, i.HPPSaatJual, i.HargaSatuan',
            'from' => 'itempenjualan i',
            'join' => [
                [
                    'table' => ' transpenjualan j',
                    'on' => "j.IDTransJual = i.IDTransJual",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = j.KodePerson",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstbarang br',
                    'on' => "br.KodeBarang = i.KodeBarang",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [
                [
                    'j.StatusProses' => 'DONE',
                    'LEFT(j.IDTransJual, 3) =' => 'TJL',
                ],
                " (DATE(j.TanggalPenjualan) BETWEEN '$tglawal' AND '$tglakhir')",
            ],
            'order_by' => 'j.IDTransJual, i.NoUrut',
        ];
        $data['model'] = $this->crud->get_rows($sql);
        $data['tglawal'] = $tglawal;
        $data['tglakhir'] = $tglakhir;

        $this->load->library('Pdf');
        $this->load->view('laporan/cetak_laporan_hppenjualan', $data);
    }
}
