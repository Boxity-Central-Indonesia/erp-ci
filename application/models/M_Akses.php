<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Akses extends CI_Model
{

	public function CekWaktu()
	{

		if ($this->session->userdata('username') != '') {
			$waktu    = time() + 25200;
			$expired  = 30000;
			$timeout = $this->session->userdata('timeout');
			if ($waktu < $timeout) {
				$this->session->unset_userdata('timeout');
				$this->session->set_userdata('timeout', ($waktu + $expired));
				return true;
			} else {
				$updateul = $this->crud->update(
					[
						'IsOnline' 			=> 0,
						'TglTerakhirLogin' 	=> date('Y-m-d H:i:s'),
						'Token'				=> null
					],
					['UserName' => $this->session->userdata('username')],
					'userlogin'
				);
				$this->session->sess_destroy();
				return false;
			}
		} else {
			return false;
		}
	}

	public function get_one_user($wheredata)
	{
		$this->db->select('*')
			->from('userlogin')
			->where($wheredata);
		return $this->db->get()->row_array();
	}

	public function get_tahun_aktif()
	{
		$this->db->select('KodeTahun')
			->from('msttahunanggaran')
			->where(['IsAktif' => 1]);
		return $this->db->get()->row()->KodeTahun;
	}

	public function insertbatch_menu($data)
	{
		$this->db->insert_batch('userfitur', $data);
		return $this->db->affected_rows();
	}

	public function getStatusBalance()
	{
		$this->db->select('*')
			->from('sistemsetting')
			->where('KodeSetting', 12);
		return $this->db->get()->row()->ValueSetting;
	}

	public function flip_api_status()
    {
        $data = $this->crud->get_one_row([
            'select' => 'ValueSetting',
            'from' => 'sistemsetting',
            'where' => [['KodeSetting' => 13]],
        ]);
        $url = ($data['ValueSetting'] == 'on') ? "https://bigflip.id/api" : "https://bigflip.id/big_sandbox_api";

        return $url;
    }

	public function getbalance()
	{
		$ch = curl_init();
        $secret_key = getenv('SECRET_KEY');
        $encoded_auth = base64_encode($secret_key.":");

        curl_setopt($ch, CURLOPT_URL, $this->flip_api_status() . "/v2/general/balance");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/x-www-form-urlencoded"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key.":");

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        $result = isset($response['status']) ? 0 : $response['balance'];

        return $result;
	}

	public function dt_perusahaan($val)
	{
		$data = $this->crud->get_one_row([
			'select' => '*',
			'from' => 'sistemsetting',
			'where' => [['NamaSetting' => $val]]
		]);

		return $data;
	}
}
