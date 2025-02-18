<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ampas_dapur extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'aktivitasproduksi a';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[48]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[48]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'ampas';
            $data['title'] = 'Insentif Ampas Dapur';
            $data['view'] = 'transaksi/v_ampas_dapur';
            $data['scripts'] = 'transaksi/s_ampas_dapur';

            $dtpegawai = $this->crud->get_rows(
                [
                    'select' => '*',
                    'from' => 'mstpegawai',
                    // 'where' => [['KodeJabatan' => 'JBT-0000003']],
                ]
            );
            $data['dtpegawai'] = $dtpegawai;

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'aktivitasproduksi a';

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPegawai LIKE '%$cari%' OR a.JmlAmpasDapur LIKE '%$cari%' OR a.GoniAmpasDapur LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(a.TglAktivitas) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['where'] = [
                [
                    'a.KodeAktivitas' => null,
                    'a.JenisAktivitas' => 'Ampas Dapur',
                ]
            ];

            $configData['join'] = [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = a.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = a.UserName",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'a.NoTrAktivitas', 'a.Biaya', 'a.JenisAktivitas', 'a.TglAktivitas', 'a.Keterangan', 'a.JmlAmpasDapur', 'a.GoniAmpasDapur', 'a.Satuan', 'a.KodePegawai', 'p.NamaPegawai', 'a.UserName', 'u.ActualName'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'a.NoTrAktivitas';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'a.NoTrAktivitas', 'a.Biaya', 'a.JenisAktivitas', 'a.TglAktivitas', 'a.Keterangan', 'a.JmlAmpasDapur', 'a.GoniAmpasDapur', 'a.Satuan', 'a.KodePegawai', 'p.NamaPegawai', 'a.UserName', 'u.ActualName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 48; //FiturID di tabel serverfitur
            $canEdit = 0;
            $edit = [];
            foreach ($this->session->userdata('fituredit') as $key => $value) {
                $edit[$key] = $value;
                if ($key == $FiturID && $value == 1) {
                    $canEdit = 1;
                }
            }
            $canDelete = 0;
            $delete = [];
            foreach ($this->session->userdata('fiturdelete') as $key => $value) {
                $delete[$key] = $value;
                if ($key == $FiturID && $value == 1) {
                    $canDelete = 1;
                }
            }

            foreach ($records as $record) {
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['TanggalAktivitas'] = isset($temp['TglAktivitas']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TglAktivitas']))) : '';
                $temp['Total'] = $temp['JmlAmpasDapur'] * $temp['Biaya'];
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['NoTrAktivitas'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete == 0) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit == 0) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['NoTrAktivitas'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpan()
    {
        $insertdata = $this->input->post();
        unset($insertdata['Biaya']);
        unset($insertdata['JmlAmpasDapur']);
        $biaya = str_replace(['.', ','], ['', '.'], $this->input->post('Biaya'));
        $jmlampas = str_replace(['.', ','], ['', '.'], $this->input->post('JmlAmpasDapur'));
        $insertdata['Biaya'] = $biaya;
        $insertdata['JmlAmpasDapur'] = $jmlampas;

        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('NoTrAktivitas') != null && $this->input->post('NoTrAktivitas') != '')) {
            $prefix = "AKP-" . date("Ym");
            $insertdata['NoTrAktivitas'] = $this->crud->get_kode([
                'select' => 'RIGHT(NoTrAktivitas, 7) AS KODE',
                'where' => [['LEFT(NoTrAktivitas, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'NoTrAktivitas DESC',
                'prefix' => $prefix
            ]);
            $insertdata['JenisAktivitas'] = 'Ampas Dapur';
            $insertdata['UserName'] = $this->session->userdata('UserName');
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'aktivitasproduksi');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('NoTrAktivitas') : $insertdata['NoTrAktivitas'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Insentif Ampas Dapur',
                'Description' => $ket . ' data insentif ampas dapur ' . $id
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data")
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ($isEdit ? "Gagal Edit Data" : "Gagal Menambah Data")
            ]);
        }
    }

    public function hapus()
    {
        $kode  = $this->input->get('NoTrAktivitas');

        $res = $this->crud->delete(['NoTrAktivitas' => $kode], 'aktivitasproduksi');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Insentif Ampas Dapur',
                'Description' => 'hapus data insentif ampas dapur ' . $kode
            ]);
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

    public function cetak()
    {
        $jenis   = escape(base64_decode($this->uri->segment(4)));
        if ($jenis) {
            $where = [['k.JenisTransaksiKas' => $jenis]];
        } else {
            $where = [" (k.JenisTransaksiKas = 'KAS MASUK' OR k.JenisTransaksiKas = 'KAS KELUAR')"];
        }
        $sql = [
            'select' => '*',
            'from' => 'transaksikas k',
            'where' => $where,
            'order_by' => 'k.TanggalTransaksi',
        ];
        $data['model'] = $this->crud->get_rows($sql);
        $data['jenis'] = $jenis;

        $this->load->library('Pdf');
        $this->load->view('transaksi/v_ampas_dapur_cetak', $data);
    }
}
