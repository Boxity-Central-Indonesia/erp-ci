<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Database extends CI_Model
{
		private $db2 = '';
    public function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('db2', TRUE);
    }

	public function getDB($db_name)
	{
		$nonaktivasi = $this->db2->update('list_db', array('isaktif' => 0), []);
		$aktivasi = $this->db2->update('list_db', array('isaktif' => 1), array('db' => $db_name));

		$this->db2->select('*')
			->from('list_db')
			->where('isaktif', 1);
		return $this->db2->get()->row_array();
	}

	public function checkDBExistance($db_name)
	{
		$sql = "SELECT COUNT(SCHEMA_NAME) AS COUNT
			FROM information_schema.SCHEMATA
 			WHERE SCHEMA_NAME = '$db_name'";
 		$checkDB = $this->db2->query($sql)->row_array()['COUNT'];

 		return $checkDB;
	}
}
