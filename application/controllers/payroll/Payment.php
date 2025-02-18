<?php
defined('BASEPATH') or exit('No direct script access allowed');

class payment extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        // $Akses = $this->akses->CekWaktu();
        // if (!$Akses) {
        //     redirect(base_url());
        // }
        $this->crud->table = 'mstpegawai';
        $this->load->model('M_Lokasi', 'lokasi');
        $this->load->model('M_Database', 'db2');
    }

    function _remap($method, $params=array()){
        $method_exists = method_exists($this, $method);
        $methodToCall = $method_exists ? $method : 'index';
        $this->$methodToCall($method_exists ? $params : $method);
    }

    public function getBank(){
        $data = $this->lokasi->get_all_bank();
        echo json_encode($data);
    }

    public function getBalances()
    {
        // $data = $this->lokasi->get_balance();
        $data = getBalance();
        echo json_encode($data);
    }

    public function getMaintenance()
    {
        $data = $this->lokasi->get_maintenance();
        echo json_encode($data['maintenance']);
    }

    public function getDisbursement()
    {
        $id = 68652;

        $data = $this->lokasi->getDisbursementById($id);
        echo json_encode($data);
    }

    public function getFlipStatus()
    {
        $data = $this->lokasi->flip_api_status();
        echo json_encode($data);
    }

    public function getSettingJurnal()
    {
        $data = $this->lokasi->setting_jurnal_status();
        echo json_encode($data);
    }

    public function createbill()
    {
        $today = date('Y-m-d H:i');
        $date = date('Y-m-d H:i', strtotime('+1 day', strtotime($today)));

        // $data = $this->lokasi->createBill('Nyoba step 2 pake bank', 25000, $date, 'bni', 'virtual_account');
        // echo json_encode($data);
    }

    public function getbill()
    {
        $data = $this->lokasi->getBill(20951);
        echo json_encode($data);
    }
}
