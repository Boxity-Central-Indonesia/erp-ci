<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class errors extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
    }

	// public function index()
	// {
	// 	$this->load->view('errors/404');
    // }
    
    public function er403(Type $var = null)
    {
        $this->load->view('errors/403');
    }
}
