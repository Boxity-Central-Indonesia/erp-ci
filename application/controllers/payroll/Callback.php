<?php
defined('BASEPATH') or exit('No direct script access allowed');

class callback extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->crud->table = 'mstpegawai';
        $this->load->model('M_Lokasi', 'lokasi');
        $this->load->model('M_Database', 'db2');
    }

    function _remap($method, $params=array()){
        $method_exists = method_exists($this, $method);
        $methodToCall = $method_exists ? $method : 'index';
        $this->$methodToCall($method_exists ? $params : $method);
    }

    public function disbursement()
    {
        $data = isset($_POST['data']) ? $_POST['data'] : null;
        $token = isset($_POST['token']) ? $_POST['token'] : null;

        if($token === getenv('VALIDATION_TOKEN')){
            $obj = json_decode($data);

            //example of what will be printed are listed below
            $status = isset($obj->status) ? $obj->status : null;
            $id = isset($obj->id) ? $obj->id : null;

            if ($status != null) {
                $file = './assets/logs/disbursement_' . $id . '.txt';
                $saved = file_put_contents ($file, json_encode($obj), FILE_APPEND);
                if (strtolower($status) == 'done') {
                    $res = $this->crud->update(['IsTelahDibayarkan' => 1], ['Keterangan' => $id], 'rekapinsentifbulanan');
                    return setresponse(200, $obj);
                } elseif (strtolower($status) == 'cancelled') {
                    $res = $this->crud->update(['IsTelahDibayarkan' => 2], ['Keterangan' => $id], 'rekapinsentifbulanan');
                    return setresponse(200, $obj);
                } elseif (strtolower($status) == 'pending') {
                    $res = $this->crud->update(['IsTelahDibayarkan' => 3], ['Keterangan' => $id], 'rekapinsentifbulanan');
                    return setresponse(200, $obj);
                }
            } else {
                return setresponse(304, ['status' => false]);
            }
        } else {
            return setresponse(304, ['status' => false]);
        }
    }

    public function acceptpayment()
    {
        $data = isset($_POST['data']) ? $_POST['data'] : null;
        $token = isset($_POST['token']) ? $_POST['token'] : null;

        if($token === getenv('VALIDATION_TOKEN')){
            $obj = json_decode($data);

            //example of what will be printed are listed below
            $status = isset($obj->status) ? $obj->status : null;
            $id = isset($obj->bill_link_id) ? $obj->bill_link_id : null;
            $bank_name = isset($obj->sender_bank) ? $this->lokasi->get_one_bank($obj->sender_bank)['name'] : null;

            if ($status != null) {
                $file = './assets/logs/accept_payment_' . $id . '.txt';
                $saved = file_put_contents ($file, json_encode($obj), FILE_APPEND);

                $updatedata = [
                    'BankName' => $bank_name,
                    'Status' => $status
                ];
                $res = $this->crud->update($updatedata, ['LinkID' => $id], 'flip');

                return setresponse(200, $obj);
            } else {
                return setresponse(304, ['status' => false]);
            }
        } else {
            return setresponse(304, ['status' => false]);
        }
    }
}
