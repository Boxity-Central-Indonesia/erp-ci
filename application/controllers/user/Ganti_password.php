<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ganti_password extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = "userlogin";
        $this->load->library('form_validation');
        checkAccess($this->session->userdata('fiturview')[62]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[62]);
        $data['menu'] = 'Ganti_Password';
        $data['title'] = 'Ganti Password';
        $data['view'] = 'user/v_gantipassword';
        $data['scripts'] = 'user/s_gantipassword';
        loadview($data);
    }

    public function _rules()
    {
        $this->form_validation->set_rules('UserName', 'Username', 'required');
        $this->form_validation->set_rules('Password', 'password', 'required');
    }

    public function ganti_password_aksi()
    {
        $pass_baru = $this->input->post('pass_baru');
        $ulang_pass = $this->input->post('ulang_pass');

        $this->form_validation->set_rules(
            'pass_baru',
            'ulang_pass',
            'required|matches[ulang_pass]',
            ['matches' => 'password dont match']
        );
        $this->form_validation->set_rules('ulang_pass', 'Ulangi Password', 'required');
        if ($this->form_validation->run() != FALSE) {
            $data = array('UserPsw' => base64_encode($pass_baru));
            $id = array('UserName' => $this->session->userdata('UserName'));
            $this->crud->update($data, $id, 'userlogin');
            $this->session->set_flashdata('message', ' <div class="alert alert-success" role="alert">
            Selamat Password berhasil di Ubah', '</div>');
            redirect('user/ganti_password');
        } else {

            $this->session->set_flashdata('message', ' <div class="alert alert-danger" role="alert">
          Password Salah!', '</div>');
            redirect('user/ganti_password');
        }
    }
}
