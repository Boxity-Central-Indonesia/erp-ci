<?php
defined('BASEPATH') or exit('No direct script access allowed');

class bahan_penolong extends CI_Controller
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
        checkAccess($this->session->userdata('fiturview')[18]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[18]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'penolong';
            $data['title'] = 'List Bahan Penolong';
            $data['view'] = 'transaksi/v_bahan_penolong';
            $data['scripts'] = 'transaksi/s_bahan_penolong';

            $data['dtgudang'] = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstgudang',
                    'where' => [['KodeGudang !=' => null]],
                ]
            );

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstbarang br';

            $configData['where'] = ["j.NamaJenisBarang LIKE '%BAHAN PENOLONG%'"];

            $status   = $this->input->get('isaktif');
            if ($status != '') {
                $configData['filters'][] = " IsAktif = $status ";
            }

            $cari = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.KodeBarang LIKE '%$cari%' OR br.NamaBarang LIKE '%$cari%')";
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
                    'on' => "br.KodeJenis = j.KodeJenis",
                    'param' => 'LEFT'
                ]
            ];

            ## select -> fill with all column you need
            $gudang = $this->input->get('gudang');
            if ($gudang != '') {
                $configData['selected_column'] = [
                    'br.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang', 'i.GudangAsal', 'i.GudangTujuan', 'SUM(IF(i.GudangTujuan = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.GudangAsal = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok'
                ];
                $configData['display_column'] = [
                    false,
                    'br.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang', 'i.GudangAsal', 'i.GudangTujuan', 'SUM(IF(i.GudangTujuan = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.GudangAsal = "'.$gudang.'" AND i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok',
                    false
                ];
            } else {
                $configData['selected_column'] = [
                    'br.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang', 'i.GudangAsal', 'i.GudangTujuan', 'SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok'
                ];

                $configData['display_column'] = [
                    false,
                    'br.KodeBarang', 'br.NamaBarang', 'br.SatuanBarang', 'i.GudangAsal', 'i.GudangTujuan', 'SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "MASUK" OR i.JenisStok = "MUTASI"), i.Qty, 0)) - SUM(IF(i.IsHapus = 0 AND (i.JenisStok = "KELUAR" OR i.JenisStok = "MUTASI"), i.Qty, 0)) as Stok',
                    false
                ];
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
            $FiturID = 18; //FiturID di tabel serverfitur
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
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }
}

