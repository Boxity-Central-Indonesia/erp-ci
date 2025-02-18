<?php
defined('BASEPATH') or exit('No direct script access allowed');

class logserver extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'serverlog';
        checkAccess($this->session->userdata('fiturview')[64]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[64]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'logserv';
            $data['title'] = 'Server Log';
            $data['view'] = 'user/v_logserver';
            $data['scripts'] = 'user/s_logserver';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'serverlog s';
            $configData['where'] = [
                ['s.JenisTransaksi !=' => null]
            ];
            $cari     = $this->input->get('cari');
            $status   = $this->input->get('isaktif');
            if ($cari != '') {
                $configData['filters'][] = " (s.UserName LIKE '%$cari%' OR s.JenisTransaksi LIKE '%$cari%' OR s.Description LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(s.DateTimeLog) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = s.UserName",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                's.LogID', 's.DateTimeLog', 's.NoTransaksi', 's.JenisTransaksi', 's.Action', 's.Description', 's.IPUser', 's.UserName', 'u.ActualName'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'LogID';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                's.LogID', 's.DateTimeLog', 's.NoTransaksi', 's.JenisTransaksi', 's.Action', 's.Description', 's.IPUser', 's.UserName', 'u.ActualName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            foreach ($records as $record) {
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['DateTimeLog'] = isset($temp['DateTimeLog']) ? shortdate_indo(date('Y-m-d', strtotime($temp['DateTimeLog']))) . ' ' . date('H:i', strtotime($temp['DateTimeLog'])) : '';
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }
}
