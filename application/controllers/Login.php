<?php
defined('BASEPATH') or exit('No direct script access allowed');

class login extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
	}

	function _remap($method, $params = array())
	{
		$method_exists = method_exists($this, $method);
		$methodToCall = $method_exists ? $method : 'index';
		$this->$methodToCall($method_exists ? $params : $method);
	}

	public function index()
	{
		$this->load->view('auth/v_login');
	}

	public function loginuser()
	{
		$recaptchaResponse = trim($this->input->post('g-recaptcha-response'));
 
        $userIp=$this->input->ip_address();
     
        $secret = $this->config->item('google_secret');
   
        $url="https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$recaptchaResponse."&remoteip=".$userIp;
 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch);      
         
        $status= json_decode($output, true);
 
        if ($status['success']) {
            $username = $this->input->post('username');
			$pass = $this->input->post('password');

			$res = $this->akses->get_one_user(
				[
					'UserName' => $username,
					'UserPsw'  => base64_encode($pass),
				]
			);
			// echo $this->db->last_query(); die;
			if ($res) {
				$token = $this->input->post('tokens');
				$cek_token = $this->crud->get_count([
					'select' => '*',
					'from' => 'userlogin',
					'where' => [['Token' => $token]]
				]);
				if ($cek_token > 0) {
					$delete_token = $this->crud->update(['Token' => null], ['Token' => $token], 'userlogin');
				}

				// update userlogin
				$updateul = $this->crud->update(
					[
						'IsOnline' 			=> 1,
						'TglTerakhirLogin' 	=> date('Y-m-d H:i:s'),
						'Token'				=> $token
					],
					[
						'UserName' 	=> $username,
						'UserPsw' 	=> base64_encode($pass),
					],
					'userlogin'
				);

				$row = $res;
				$waktu = time() + 25200;
				$expired = 30000;
				$row['username']	= $row['UserName'];
				$row['photo']		= $row['Photo'];
				$row['timeout']     = ($waktu + $expired);

				/**
				 * fitur get access
				 * ditutup karena perubahan struktur tabel user login
				 * dipakai lagi ya guys
				 */
				$sql = [
					'select' => '*',
					'from' => 'fiturlevel',
					'where' => [[
						'LevelID' => $row['LevelID']
					]]
				];
				$fitur = $this->crud->get_rows($sql);;
				$mapfitur = [];
				$view = [];
				$add = [];
				$edit = [];
				$delete = [];
				$print = [];
				// set fitur[]
				foreach ($fitur as $dt) {
					$mapfitur[$dt['FiturID']] = $dt['FiturID'];
					$view[$dt['FiturID']] = $dt['ViewData'];
					$add[$dt['FiturID']] = $dt['AddData'];
					$edit[$dt['FiturID']] = $dt['EditData'];
					$delete[$dt['FiturID']] = $dt['DeleteData'];
					$print[$dt['FiturID']] = $dt['PrintData'];
				}
				$row['fitur']		= $mapfitur; 
				$row['fiturview']	= $view;
				$row['fituradd']	= $add;
				$row['fituredit']	= $edit;
				$row['fiturdelete']	= $delete;
				$row['fiturprint']	= $print;
				$this->session->set_userdata($row);

				echo json_encode([
	                'status' => true,
	                'msg'  => ("Login Berhasil"),
	                'url' => "beranda"
	            ]);
			} else {
				echo json_encode([
	                'status' => false,
	                'msg'  => ("Username atau Password Salah")
	            ]);
			}
        }else{
        	echo json_encode([
                'status' => false,
                'msg'  => ("Sorry Google Recaptcha Unsuccessful!!")
            ]);
        }
	}

	public function forgot()
	{
		$this->load->view('auth/v_forgot');
	}

	public function sendforgot()
	{
		$Email = $this->input->post('Email');
		$data = $this->crud->get_one_row(
			[
				'select' => '*',
				'from' => 'userlogin',
				'where' => [['Email' => $Email]],
			]
		);

		if ($data) {
			$link = base_url('login/reset/'.base64_encode($data['Email']).'/'.base64_encode($data['UserPsw']));
			$mailContent = "<p>Untuk mereset password anda silahkan klik link berikut: ".$link."</p>";
			$tujuan = $data['Email'];
			$res = $this->SendEmailReset($mailContent, $tujuan);

			if($res){
				$this->session->set_flashdata('success', 'We have sent a confirmation page to your email. Please check your email to open the confirmation page.');
            }else{
            	$this->session->set_flashdata('error', 'There was an error when we sent the confirmation page to your email.');
            }
		} else {
			$this->session->set_flashdata("error", "We can't find your registered email in our system. Please re-check the email that you've input.");
		}

		redirect(base_url('login/forgot'));
	}

	function SendEmailReset($mailContent, $tujuan){
        // $config = Array(
        //     'protocol' => 'smtp',
        //     'smtp_host' => 'ssl://smtp.googlemail.com',
        //     'smtp_port' => 465,
        //     'smtp_user' => 'develop.afindo@gmail.com', // change it to yours
        //     'smtp_pass' => 'xzeiqgqkwnnppgur', // change it to yours
        //     'mailtype' => 'html',
        //     'charset' => 'iso-8859-1',
        //     'wordwrap' => TRUE
        // );

//         $config = Array(
// 		 	'protocol' => 'smtp',
// 		 	'smtp_host' => 'smtp.mailtrap.io',
// 		 	'smtp_port' => 2525,
// 		 	'smtp_user' => 'de6b1c11b67b43',
// 		 	'smtp_pass' => '7badd9a3876f36',
// 		 	'crlf' => "\r\n",
// 		 	'newline' => "\r\n",
// 		 	'mailtype' => 'html',
//             'charset' => 'iso-8859-1'
// 		);

		$config = Array(
		 	'protocol' => 'smtp',
		 	'smtp_host' => 'mail.boxity.id',
		 	'smtp_port' => 587,
		 	'smtp_user' => 'system@boxity.id',
		 	'smtp_pass' => 'LibrA21101998',
		 	'crlf' => "\r\n",
		 	'newline' => "\r\n",
		 	'mailtype' => 'html',
            'charset' => 'iso-8859-1'
		);
            
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from('system@boxity.id'); // change it to yours
        $this->email->to($tujuan);// change it to yours
        $this->email->subject('RESET PASSWORD BOXITY');
        $this->email->message($mailContent);
        if($this->email->send()){
            return true;
        } else  {
            return false;
            // echo($this->email->print_debugger());
        }
    }

    public function reset()
    {
    	$email  = escape(base64_decode($this->uri->segment(3)));
    	$pw 	= escape(base64_decode($this->uri->segment(4)));

    	$dtuser = $this->crud->get_one_row([
    		'select' => '*',
    		'from' => 'userlogin',
    		'where' => [['Email' => $email]],
    	]);
    	$this->data['Email'] = $email;

    	if ($pw == $dtuser['UserPsw']) {
    		$this->load->view('auth/v_reset', $this->data);
    	} else {
    		echo "Page Expired! Password anda sudah berhasil dirubah.";
    	}
    }

    public function sendreset()
    {
    	$password = $this->input->post('password');
        $confirmation = $this->input->post('confirmation');

        $this->form_validation->set_rules(
            'password',
            'confirmation',
            'required|matches[confirmation]',
            ['matches' => 'password dont match']
        );
        $this->form_validation->set_rules('confirmation', 'Ulangi Password', 'required');
        if ($this->form_validation->run() != FALSE) {
            $data = array('UserPsw' => base64_encode($password));
            $id = array('Email' => $this->input->post('Email'));
            $res = $this->crud->update($data, $id, 'userlogin');

            if ($res) {
	            echo json_encode([
	                'status' => true,
	                'msg'  => ("Your account has been successfully restored, please login using the new password.")
	            ]);
	        } else {
	            echo json_encode([
	                'status' => false,
	                'msg'  => ("Failed to reset password.")
	            ]);
	        }
        } else {
        	echo json_encode([
                'status' => false,
                'msg'  => ("Password doesn't match.")
            ]);
        }
    }

	function logout()
	{
		// update userlogin
		$updateul = $this->crud->update(
			[
				'IsOnline' 			=> 0,
				'TglTerakhirLogin' 	=> date('Y-m-d H:i:s'),
				'Token'				=> null
			],
			['UserName' => $this->session->userdata('UserName')],
			'userlogin'
		);
		$this->session->sess_destroy();
		redirect('login');
	}
}
