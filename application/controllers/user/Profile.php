<?php
defined('BASEPATH') or exit('No direct script access allowed');

class profile extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        checkAccess($this->session->userdata('fiturview')[61]);
        $this->crud->table = 'userlogin';
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[61]);
        $data['menu'] = 'profile';
        $data['title'] = 'Edit Profile';
        $data['view'] = 'user/v_profile';
        $data['scripts'] = 'user/s_profile';

        $data['model'] = $this->crud->get_one_row([
            'select' => 'ActualName, Address, Phone, Email, Photo, UserName, FROM_BASE64(UserPsw) as UserPsw',
            'from' => 'userlogin',
            'where' => [['UserName' => $this->session->userdata('UserName')]],
        ]);

        loadview($data);
    }

    public function checkEmail()
    {
        $emailLama =$this->input->get('emailLama');
        $Email = $this->input->get('Email');

        if ($emailLama != null && $Email != null) {
            $countEmail =  $this->crud->get_count([
                'select' => 'Email',
                'from' => 'userlogin',
                'where' => [[
                    'Email' => $Email,
                    'Email !=' => $emailLama
                ]]
            ]);
        } else {
            $countEmail =  $this->crud->get_count([
                'select' => 'Email',
                'from' => 'userlogin',
                'where' => [['Email' => $Email]]
            ]);
        }

        if ($countEmail > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Email telah terdaftar']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Email tersedia']);
        }
    }

    public function simpan()
    {
        $this->load->library('upload');
        $updatedata = $this->input->post();
        $username   = $this->session->userdata('UserName');
        $old_photo      = $this->session->userdata('photo');

        ## UPDATE FOTO USER
        if(!empty($_FILES['Photo']['name'])){
            $Photo = $this->uploadFoto('Photo');
            $updatedata['Photo'] = $Photo;
            if ($Photo) {
                if ($old_photo) {
                    $path = realpath(APPPATH . '../assets/img/users/'.$old_photo);
                    if (@file_exists($path)) 
                    {
                        @unlink($path);
                    }
                }
                $this->session->set_userdata('photo', $Photo);
            }
        } else {
            unset($updatedata['Photo']);
        }

        $res = $this->crud->update($updatedata, ['UserName' => $username], 'userlogin');

        if ($res) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Mengubah Data"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Mengubah Data"
            ]);
        }
    }

    private function uploadFoto($param, $targetdir = "assets/img/users") {
        $this->load->library('upload');
        $config['upload_path'] = $targetdir; //path folder
        $config['allowed_types'] = 'png|jpeg|jpg'; //type yang dapat diakses bisa anda sesuaikan
        $config['encrypt_name'] = TRUE; //Enkripsi nama yang terupload
        $this->upload->initialize($config);

        $gambar1 = "";
        if ($this->upload->do_upload($param)) {
            $gbr = $this->upload->data();
            if ($gbr) {
                $gambar1 = $gbr['file_name'];
                return $gambar1;
            } else {
                $this->upload->display_errors();
            }
        } else {
            $this->upload->display_errors();
        }
    }
}
