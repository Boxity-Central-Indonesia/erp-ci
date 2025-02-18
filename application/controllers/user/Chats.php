<?php
defined('BASEPATH') or exit('No direct script access allowed');

class chats extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->load->model('M_Lokasi', 'lokasi');
        $this->crud->table = 'userlogin';
    }

    public function index()
    {
        $penerima = escape(base64_decode($this->input->get('rcv'))) ? base64_decode($this->input->get('rcv')) : '';

        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu'] = 'chat';
        $data['title'] = 'Pesan';
        $data['view'] = 'user/v_chat';
        $data['scripts'] = 'user/s_chat';

        $data['penerima'] = $penerima;
        $data['dtpenerima'] = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'userlogin',
            'where' => [['UserName' => $penerima]],
        ]);

        loadview($data);
    }

    public function getusers()
    {
        $pengirim = $this->input->get('pengirim');
        $list_user = $this->crud->get_rows([
            'select' => '*',
            'from' => 'userlogin',
            'where' => [
                [
                    'IsAktif' => 1,
                    'UserName !=' => $pengirim,
                ]
            ],
            'order_by' => 'ActualName',
        ]);

        $data = [];
        foreach ($list_user as $key) {
            $data[] = [
                'UserName'          => $key['UserName'],
                'ActualName'        => $key['ActualName'],
                'Photo'             => $key['Photo'],
                'IsOnline'          => $key['IsOnline'],
                'TglTerakhirLogin'  => $key['TglTerakhirLogin'],
                'JumlahPesan'       => $this->lokasi->get_jml_pesan($pengirim, $key['UserName'])
            ];
        }

        echo json_encode(array(
            'data' => $data
        ));
    }

    public function getunread()
    {
        $pengirim = $this->input->get('pengirim');
        $list_user = $this->crud->get_rows([
            'select' => '*',
            'from' => 'userlogin',
            'where' => [
                [
                    'IsAktif' => 1,
                    'UserName !=' => $pengirim,
                ]
            ],
            'order_by' => 'ActualName',
        ]);

        $data = [];
        foreach ($list_user as $key) {
            $cekunread = $this->lokasi->get_last_unread($pengirim, $key['UserName']);
            if ($cekunread) {
                $data[] = [
                    'UserName'          => $key['UserName'],
                    'ActualName'        => $key['ActualName'],
                    'Photo'             => $key['Photo'],
                    'IsOnline'          => $key['IsOnline'],
                    'TglTerakhirLogin'  => $key['TglTerakhirLogin'],
                    'JumlahPesan'       => $this->lokasi->get_jml_pesan($pengirim, $key['UserName']),
                    'LastUnreadChat'    => $cekunread
                ];
            }
        }

        echo json_encode(array(
            'data' => $data
        ));
    }

    public function get_penerima()
    {
        $penerima = $this->input->get('penerima');
        $data = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'userlogin',
            'where' => [['UserName' => $penerima]],
        ]);

        echo json_encode($data);
    }

    public function getpesan()
    {
        $pengirim = $this->input->get('pengirim');
        $penerima = $this->input->get('penerima');

        $data['data']   = $this->lokasi->get_pesan($pengirim, $penerima);
        $data['rcv']    = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'userlogin',
            'where' => [['UserName' => $penerima]]
        ]);

        $data['update_isread'] = $this->crud->update(
            ['IsRead' => 1],
            [
                'Pengirim' => $penerima,
                'Penerima' => $pengirim,
                'IsHapus'  => 0,
                'IsRead'   => 0
            ],
            'chat'
        );

        echo json_encode($data);
    }

    public function kirimpesan()
    {
        $this->load->library('upload');

        $prefix = date("Ymd");
        $insertdata['KodeChat'] = $this->crud->get_kode([
            'select' => 'RIGHT(KodeChat, 7) AS KODE',
            'from' => 'chat',
            'where' => [['LEFT(KodeChat, 8) =' => $prefix]],
            'limit' => 1,
            'order_by' => 'KodeChat DESC',
            'prefix' => $prefix
        ]);

        $insertdata['TglChat']  = date('Y-m-d H:i:s');
        $insertdata['Pengirim'] = $this->input->post('pengirim');
        $insertdata['Penerima'] = $this->input->post('penerima');
        $insertdata['IsHapus']  = 0;
        $insertdata['IsRead']   = 0;

        if ($this->input->post('isipesan') != null || $this->input->post('isipesan') != '' || !empty($_FILES['attachment']['name'])) {
            if(!empty($_FILES['attachment']['name'])){
                $insertdata['FileName'] = $_FILES['attachment']['name'];
                $attachment = $this->uploaderFile('attachment');
                $insertdata['File'] = $attachment;

                // send push notification using FCM
                $fcm_push = $this->lokasi->sendMsg($insertdata['Pengirim'], $insertdata['Penerima'], $insertdata['FileName']);
            } else {
                $insertdata['IsiPesan'] = $this->input->post('isipesan');

                // send push notification using FCM
                $fcm_push = $this->lokasi->sendMsg($insertdata['Pengirim'], $insertdata['Penerima'], $insertdata['IsiPesan']);
            }
            $res = $this->crud->insert($insertdata, 'chat');
            echo json_encode([
                'status' => true,
                'msg'  => ("Berhasil menambah Data")
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ("Gagal menambah Data")
            ]);
        }
    }

    public function hapus()
    {
        $id = $this->input->get('KodeChat');
        $res = $this->crud->update(['IsHapus' => 1], ['KodeChat' => $id], 'chat');

        if ($res) {
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Menghapus Data"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Menghapus Data"
            ]);
        }
    }

    private function uploaderFile($param, $targetdir = "assets/chats")
    {
        $this->load->library('upload');
        $config['upload_path'] = $targetdir; //path folder
        $config['allowed_types'] = 'png|jpeg|jpg|xls|xlsx|doc|docx|ppt|pptx|cdr|pdf'; //type yang dapat diakses bisa anda sesuaikan
        $config['encrypt_name'] = TRUE; //Enkripsi nama yang terupload
        $this->upload->initialize($config);

        $file1 = "";
        if ($this->upload->do_upload($param)) {
            $files = $this->upload->data();
            if ($files) {
                $file1 = $files['file_name'];
                return $file1;
            } else {
                $this->upload->display_errors();
            }
        } else {
            $this->upload->display_errors();
        }
    }
}
