<?php

class M_Log extends CI_Model
{
	public function jml_data_log($wheredata)
	{
		$jml = $this->db->select('COUNT(l.LogID) AS JUMLAH')
			->from('serverlog l')
			->where($wheredata)
			->get();
		if ($jml->num_rows() > 0) {
			$jumlah = $jml->row();
			return $jumlah->JUMLAH;
		}
		return 0;
	}

	public function get_data_log($wheredata, $limit = 0, $offset = 0)
	{
		$this->db->select('l.LogID,l.DateTimeLog,l.Action,l.Description,l.username,p.nama')
			->from('serverlog l')
			->join('tb_pegawai p', 'p.username = l.UserName')
			->where($wheredata)
			->order_by('LogID', 'asc');
		if ($offset > 0) {
			$this->db->limit($offset, $limit);
		}
		return $this->db->get()->result();
	}

	public function get_one_data_log($wheredata)
	{
		return $this->db->select('LogID,DateTimeLog,Action,Description,UserName')
			->from('serverlog')
			->where($wheredata)
			->get()
			->row();
	}

	public function get_kode_log()
	{
		$Tahun = date('Y');
		$res = $this->db->select('RIGHT(LogID,7) AS KODE')
			->from('serverlog')
			->like('LogID', $Tahun)
			->order_by('LogID', 'desc')
			->limit(1)
			->get();
		if ($res) {
			$num = $res->num_rows();
			if ($num != 0) {
				$data = $res->row();
				$kode = $data->KODE + 1;
			} else {
				$kode = 1;
			}
		} else {
			$kode = 1;
		}
		$bikin_kode = str_pad($kode, 7, "0", STR_PAD_LEFT);
		$kode_jadi = "LOG-" . $Tahun . "-" . $bikin_kode;
		return $kode_jadi;
	}

	public function insert_log($data)
	{
		date_default_timezone_set("Asia/Bangkok");
		$UserName = $this->session->UserName;
		$data['LogID'] = $this->logsrv->get_kode_log();
		$data['DateTimeLog'] = date("Y-m-d H:i:s");
		$data['username'] = $UserName;
		$this->db->insert('serverlog', $data);
		return $this->db->affected_rows();
	}
}
