<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ganti_db extends CI_Controller
{
    private $db2 = '';
    function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('db2', TRUE);

        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }

        $this->load->model('M_Database', 'database2');
        checkAccess($this->session->userdata('fiturview')[63]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[63]);
        $data['listdb'] = $this->db2->select('*')
            ->from('list_db')
            ->where('ishapus !=', 1)
            ->order_by('db_alias')
            ->get()->result_array();
        $data['menu'] = 'Ganti_DB';
        $data['title'] = 'Swicth Database Connection';
        $data['view'] = 'user/v_gantidb';
        $data['scripts'] = 'user/s_gantidb';
        loadview($data);
    }

    public function ganti_db_aksi()
    {
        $updateul_old = $this->crud->update(
            [
                'IsOnline'          => 0,
                'TglTerakhirLogin'  => date('Y-m-d H:i:s'),
                'Token'             => null
            ],
            ['UserName' => $this->session->userdata('UserName')],
            'userlogin'
        );

        $data = $this->input->post('database');
        $res = $this->database2->getDB($data);

        $getadmin = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'userlogin',
            'where' => [['UserName' => 'admin']],
        ]);
        if ($getadmin) {
            // update userlogin
            $updateul = $this->crud->update(
                [
                    'IsOnline'          => 1,
                    'TglTerakhirLogin'  => date('Y-m-d H:i:s'),
                ],
                ['UserName'  => $getadmin['UserName']],
                'userlogin'
            );

            $row = $getadmin;
            $waktu = time() + 25200;
            $expired = 30000;
            $row['username']    = $row['UserName'];
            $row['photo']       = $row['Photo'];
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
            $row['fitur']       = $mapfitur; 
            $row['fiturview']   = $view;
            $row['fituradd']    = $add;
            $row['fituredit']   = $edit;
            $row['fiturdelete'] = $delete;
            $row['fiturprint']  = $print;
            $this->session->set_userdata($row);
        }

        if ($res) {
            $this->session->set_flashdata('berhasil', 'Berhasil mengubah koneksi database!');
        } else {
            $this->session->set_flashdata('gagal', 'Gagal mengubah koneksi, database tidak ditemukan!');
        }

        redirect(base_url('user/ganti_db'));
    }

    public function simpan()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // select main db
        $main = $this->db2->select('*')
            ->from('list_db')
            ->where('ismain', 1)
            ->get()
            ->row_array();
        $servername = $main['hostname'];
        $username = $main['username'];
        $password = $main['psw'];
        $db = mysqli_connect($servername, $username, $password, $main['db']);

        $countDB = $this->db2->select('*')
            ->from('list_db')
            ->where('db', $this->input->post('database'))
            ->get()->result_array();
        if (count($countDB) > 0) {
            $this->session->set_flashdata('gagal', 'Nama database sudah digunakan!');
        } else {
            $data = [
                'hostname' => $this->input->post('hostname'),
                'username' => $this->input->post('username'),
                'psw' => $this->input->post('password'),
                'db' => $this->input->post('database'),
                'db_alias' => $this->input->post('database'),
                'isaktif' => 0,
                'ishapus' => 0,
                'ismain' => 0
            ];

            // $checkDB = $this->database2->checkDBExistance($data['db']);
            if (mysqli_connect_errno()){
                die("Koneksi database gagal : " . mysqli_connect_error());
            }
            $tables     = '*';
            $newserver  = $data['hostname'];
            $newuser    = $data['username'];
            $newpass    = $data['psw'];
            $newdb      = $data['db'];
            $result     = $this->backupDatabaseTables($db, $tables, $newdb); //disini berarti saya akan membackup DB "codingan"

            if ($result != null && $result != 'null') {
                // Create connection
                $conn = mysqli_connect($newserver, $newuser, $newpass);
                // Check connection
                if (!$conn) {
                  // die("Connection failed: " . mysqli_connect_error());
                    $this->session->set_flashdata('gagal', 'Username / Password / Nama database anda salah!');
                } else {
                    // Create database
                    $sql = "USE $newdb";
                    if (mysqli_query($conn, $sql)) {
                        // echo "Database created successfully";
                        $this->newDBs = mysqli_connect($newserver, $newuser, $newpass, $newdb);
                        if (mysqli_connect_errno()){
                            die("Koneksi database gagal : " . mysqli_connect_error());
                        }

                        // Set line to collect lines that wrap
                        $templine = '';

                        // Read in entire file
                        $lines = file($result);

                        // Loop through each line
                        foreach ($lines as $line)
                        {
                            // Skip it if it's a comment
                            if (substr($line, 0, 2) == '--' || $line == '')
                            continue;

                            // Add this line to the current templine we are creating
                            $templine .= $line;

                            // If it has a semicolon at the end, it's the end of the query so can process this templine
                            if (substr(trim($line), -1, 1) == ';') {
                                // Perform the query
                                $this->newDBs->query($templine);

                                // Reset temp variable to empty
                                $templine = '';
                            }
                        }
                    }

                    $res = $this->db2->insert('list_db', $data);
                    if ($res) {
                        $this->session->set_flashdata('berhasil', 'Berhasil menambahkan database baru!');
                    } else {
                        $this->session->set_flashdata('gagal', 'Gagal menambahkan database baru');
                    }
                }

                // Delete file .sql
                if (@file_exists($result)) 
                {
                    @unlink($result);
                }
            }
        }

        redirect(base_url('user/ganti_db'));
    }

    public function update()
    {
        $countDB = $this->db2->select('*')
            ->from('list_db')
            ->where('db', $this->input->post('database'))
            ->where('db !=', $this->input->post('db_alias'))
            ->get()->result_array();

        if (count($countDB) > 0) {
            $this->session->set_flashdata('gagal', 'Nama database sudah digunakan!');
        } else {
            $data = [
                'hostname' => $this->input->post('hostname'),
                'username' => $this->input->post('username'),
                'psw' => $this->input->post('password'),
                'db' => $this->input->post('database'),
                'db_alias' => $this->input->post('database'),
                'isaktif' => 0,
                'ishapus' => 0
            ];

            $res = $this->db2->update('list_db', $data, array('db_alias' => $this->input->post('db_alias')));
            if ($res) {
                $this->session->set_flashdata('berhasil', 'Berhasil mengubah database!');
            } else {
                $this->session->set_flashdata('gagal', 'Gagal mengubah database!');
            }
        }

        redirect(base_url('user/ganti_db'));
    }

    public function simpan_old()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        //Menghasilkan backup DB
        $dflt = $this->db;
        $servername = $dflt->hostname;
        $username = $dflt->username;
        $password = $dflt->password;

        $db = mysqli_connect($servername, $username, $password, $dflt->database);
        if (mysqli_connect_errno()){
            die("Koneksi database gagal : " . mysqli_connect_error());
        }
        $tables = '*';
        $newdb = "erp_boxity_".date('dmyHis');
        $result = $this->backupDatabaseTables($db, $tables, $newdb); //disini berarti saya akan membackup DB "codingan"

        if ($result != null && $result != 'null') {
            // Create connection
            $conn = mysqli_connect($servername, $username, $password);
            // Check connection
            if (!$conn) {
              die("Connection failed: " . mysqli_connect_error());
            }

            // Create database
            $sql = "CREATE DATABASE $newdb";
            if (mysqli_query($conn, $sql)) {
                // echo "Database created successfully";
                $this->newDBs = mysqli_connect($servername, $username, $password, $newdb);
                if (mysqli_connect_errno()){
                    die("Koneksi database gagal : " . mysqli_connect_error());
                }

                // Set line to collect lines that wrap
                $templine = '';

                // Read in entire file
                $lines = file($result);

                // Loop through each line
                foreach ($lines as $line)
                {
                    // Skip it if it's a comment
                    if (substr($line, 0, 2) == '--' || $line == '')
                    continue;

                    // Add this line to the current templine we are creating
                    $templine .= $line;

                    // If it has a semicolon at the end, it's the end of the query so can process this templine
                    if (substr(trim($line), -1, 1) == ';') {
                        // Perform the query
                        $this->newDBs->query($templine);

                        // Reset temp variable to empty
                        $templine = '';
                    }
                }


                $getdb = $this->db2->select('*')
                    ->from('list_db')
                    ->where('database', 'erp_boxity_v1')
                    ->get()->row_array();

                $data = [
                    'hostname' => $getdb['hostname'],
                    'username' => $getdb['username'],
                    'password' => $getdb['password'],
                    'database' => $newdb,
                    'db_alias' => $this->input->post('database'),
                    'isaktif' => 0,
                    'ishapus' => 0
                ];

                $res = $this->db2->insert('list_db', $data);
                if ($res) {
                    $this->session->set_flashdata('berhasil', 'Berhasil menambahkan database baru!');
                } else {
                    $this->session->set_flashdata('gagal', 'Gagal menambahkan database baru!');
                }
            } else {
                // echo "Error creating database: " . mysqli_error($conn);
                $this->session->set_flashdata('gagal', 'Gagal menambahkan database baru!');
            }
        } else {
            $this->session->set_flashdata('gagal', 'Gagal menambahkan database baru!');
        }

        if (@file_exists($result)) 
        {
            @unlink($result);
        }

        redirect(base_url('user/ganti_db'));
    }

    public function backupDatabaseTables($db, $tables = '*', $newdb)
    {
        //Mendapatkan semua Table
        if($tables == '*'){
            $tables = array();
            $result = $db->query("SHOW TABLES");
            while($row = $result->fetch_row()){
                $tables[] = $row[0];
            }
        }else{
            $tables = is_array($tables)?$tables:explode(',',$tables);
        }
        $return = "";

        // membuat database
        // $return .= "CREATE DATABASE IF NOT EXISTS ".$newdb.";\n\n";

        // disable foreign chekcs
        $return .= "SET FOREIGN_KEY_CHECKS = 0;";

        set_time_limit(500);

        //Loop melalui Table
        foreach($tables as $table){
            $result = $db->query("SELECT * FROM $table");
            $numColumns = $result->field_count;

            //$return .= "DROP TABLE $table;";

            $result2 = $db->query("SHOW CREATE TABLE $table");
            $row2 = $result2->fetch_row();

            $return .= "\n\n".$row2[1].";\n\n";

            // || $table == 'userlogin' || $table == 'mstjenisaktivitas' || $table == 'mstaktivitas' || $table == 'mstjenisbarang' || $table == 'mstkategori' || $table == 'mstbarang' || $table == 'mstgudang' || $table == 'mstjabatan' || $table == 'mstpegawai' || $table == 'mstperson' || $table == 'msttahunanggaran'

            if ($table == 'accesslevel' || $table == 'mstakun' || $table == 'mstkomponengaji' || $table == 'sistemsetting' || $table == 'serverfitur' || $table == 'fiturlevel' || $table == 'setakunjurnal' || $table == 'detailsetakun') {
                for($i = 0; $i < $numColumns; $i++){
                    while($row = $result->fetch_row()){
                        $return .= "INSERT INTO $table VALUES(";
                        for($j=0; $j < $numColumns; $j++){
                            $row[$j] = addslashes($row[$j]);
                            //$row[$j] = preg_replace("/\n","\\n",$row[$j]);
                            if (isset($row[$j])) { $return .= '"'.$row[$j].'"' ; } else { $return .= '""'; }
                            if ($j < ($numColumns-1)) { $return.= ','; }
                        }
                        $return .= ");\n";
                    }
                }
            }
            $return .= "\n";
        }

        $return .= "INSERT INTO userlogin (UserName, UserPsw, ActualName, Address, Phone, Email, Photo, LevelID, IsAktif, IsOnline, TglTerakhirLogin, Token) VALUES('admin', 'YWRtaW4=', 'Super Admin', NULL, NULL, NULL, NULL, 1, b'1', 0, NULL, NULL);";

        $return .= "\n\n\n";

        // enable foreign chekcs
        $return .= "SET FOREIGN_KEY_CHECKS = 1;";

        $dir = "assets/db";

        if( is_dir($dir) === false )
        {
            mkdir($dir);
        }

        //Use this setting will help 
        $path = 'assets/db\\';

        //simpan file
        $namaFile= $newdb.'.sql';
        $handle = fopen($path.$namaFile,'w+');
        fwrite($handle,$return);
        fclose($handle);

        if (!file_exists($path.$namaFile)) { // file does not exist
            die('null');
        } else {
            return $path.$namaFile;
        }
    }

    public function hapus()
    {
        $kode = $this->input->get('db');

        $res = $this->db2->update('list_db', array('ishapus' => 1), array('db' => $kode));
        // $res = $this->db2->delete('list_db', array('db' => $kode));

        if ($res) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil menghapus database!"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal menghapus database!"
            ]);
        }
    }
}
