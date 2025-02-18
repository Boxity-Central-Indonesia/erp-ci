<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class import_absensi extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'absensipegawai';
        checkAccess($this->session->userdata('fiturview')[49]);
    }

    function _remap($method, $params = array())
    {
        $method_exists = method_exists($this, $method);
        $methodToCall = $method_exists ? $method : 'index';
        $this->$methodToCall($method_exists ? $params : $method);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[49]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'importabs';
            $data['title'] = 'Import Absensi Per Bulan';
            $data['view'] = 'payroll/v_import_absensi';
            $data['scripts'] = 'payroll/s_import_absensi';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'absensipegawai a';

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPegawai LIKE '%$cari%')";
            }

            $bulan     = $this->input->get('bulan');
            if ($bulan) {
                $configData['where'] = [['DATE_FORMAT(a.Tanggal,  "%Y-%m") =' => $bulan]];
            }

            $configData['join'] = [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = a.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = p.KodeJabatan",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'a.Tanggal', 'a.IDFinger', 'a.JamKerjaMasuk', 'a.JamKerjaPulang', 'a.JamMasuk', 'a.JamPulang', 'a.Telat', 'a.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'a.Tanggal, a.KodePegawai';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'a.Tanggal', 'a.IDFinger', 'a.JamKerjaMasuk', 'a.JamKerjaPulang', 'a.JamMasuk', 'a.JamPulang', 'a.Telat', 'a.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 49; //FiturID di tabel serverfitur
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
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodePegawai'] . ' data-kode2=' . $temp['Tanggal'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodePegawai'] . ' data-kode2=' . $temp['Tanggal'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $temp['Tanggal'] = isset($temp['Tanggal']) ? shortdate_indo(date('Y-m-d', strtotime($temp['Tanggal']))) : '';
                $temp['JamKerjaMasuk'] = isset($temp['JamKerjaMasuk']) ? date('H:i', strtotime($temp['JamKerjaMasuk'])) : '';
                $temp['JamKerjaPulang'] = isset($temp['JamKerjaPulang']) ? date('H:i', strtotime($temp['JamKerjaPulang'])) : '';
                $temp['JamMasuk'] = isset($temp['JamMasuk']) ? date('H:i', strtotime($temp['JamMasuk'])) : '';
                $temp['JamPulang'] = isset($temp['JamPulang']) ? date('H:i', strtotime($temp['JamPulang'])) : '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpan()
    {
        if (!empty($_FILES['file']['name'])) {
            $path = 'assets/upload_temp/';
            $this->upload_config($path);
            if (!$this->upload->do_upload('file')) {
                setresponse(200, ['status' => false, 'msg' => $this->upload->display_errors()]);
            } else {
                $file_data = $this->upload->data();
                $file_name = $path . $file_data['file_name'];
                $arr_file = explode('.', $file_name);
                // $extension = end($arr_file);
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $spreadsheet = $reader->load($file_name);
                $sheet_data = $spreadsheet->getActiveSheet()->toArray();
                $list = [];
                $list_gagal = [];
                $cekDataType = $spreadsheet->getActiveSheet()
                ->getCell('F2')
                ->getDataType();

                foreach ($sheet_data as $key => $val) {
                    if ($key != 0 && $val[0] != null && $val[0] != '') {
                        $datapeg = $this->crud->get_one_row([
                            'select' => 'p.KodePegawai, p.IDFinger, p.KodeJabatan, j.NamaJabatan',
                            'from' => 'mstpegawai p',
                            'join' => [
                                ['table' => 'mstjabatan j', 'on' => 'j.KodeJabatan = p.KodeJabatan', 'param' => 'left']
                            ],
                            'where' => [
                                ['p.NIP' => $val[0], 'p.IsAktif' => 1]
                            ]
                        ]);
                        if (!$datapeg) {
                            $list_gagal[] = ['Data ke ' . ($key + 1) . ' dengan NIP ' . $val[0] . ' tidak ditemukan.'];
                            continue;
                        }

                        $sub = '00.00';
                        $replacetgl = str_replace('/', '-', $val[5]);
                        $tgl = explode('-', $replacetgl);
                        $jkm = isset($val[7]) ? explode('.', $val[7]) : explode('.', $sub);
                        $jkp = isset($val[8]) ? explode('.', $val[8]) : explode('.', $sub);
                        $jm = isset($val[9]) ? explode('.', $val[9]) : explode('.', $sub);
                        $jp = isset($val[10]) ? explode('.', $val[10]) : explode('.', $sub);
                        $tanggal = ($cekDataType == 'n') ? $tgl[2].'-'.$tgl[0].'-'.$tgl[1] : $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
                        $jamkerjamasuk = $tanggal.' '.$jkm[0].':'.$jkm[1];
                        $jamkerjapulang = $tanggal.' '.$jkp[0].':'.$jkp[1];
                        $jammasuk = $tanggal.' '.$jm[0].':'.$jm[1];
                        $jampulang = $tanggal.' '.$jp[0].':'.$jp[1];
                        $selisihmasuk = ((strtotime($jammasuk) - strtotime($jamkerjamasuk))/60 > 0) ? (strtotime($jammasuk) - strtotime($jamkerjamasuk))/60 : 0;
                        $selisihpulang = ((strtotime($jamkerjapulang) - strtotime($jampulang))/60 > 0) ? (strtotime($jamkerjapulang) - strtotime($jampulang))/60 : 0;
                        $telat = isset($val[9]) ? $selisihmasuk + $selisihpulang : 0;
                        $ket = isset($val[9]) ? 'Hadir' : 'Alpha';

                        $list = [
                            'Tanggal' => $tanggal,
                            'KodePegawai' => $datapeg['KodePegawai'],
                            'IDFinger' => $datapeg['IDFinger'],
                            'JamKerjaMasuk' => $jamkerjamasuk,
                            'JamKerjaPulang' => $jamkerjapulang,
                            'JamMasuk' => $jammasuk,
                            'JamPulang' => $jampulang,
                            'Telat' => $telat,
                            'Keterangan' => $ket
                        ];

                        $result[] = $this->crud->insert_or_update($list, 'absensipegawai');
                    }
                }
                if (file_exists($file_name))
                    unlink($file_name);
                if (count($result) > 0) {
                    if ($result) {
                        ## INSERT TO SERVER LOG
                        $this->logsrv->insert_log([
                            'Action' => 'tambah',
                            'JenisTransaksi' => 'Import Absensi Per Bulan',
                            'Description' => 'import data absensi pegawai ' . $list['KodePegawai'] . ' ' . date('M-Y', strtotime($tanggal))
                        ]);
                        setresponse(200, ['status' => true, 'msg' => 'Berhasil import data.', 'gagal' => $list_gagal]);
                    } else {
                        setresponse(200, ['status' => false, 'msg' => 'Something went wrong. Please try again.', 'gagal' => $list_gagal]);
                    }
                } else {
                    setresponse(200, ['status' => false, 'msg' => 'No new record is found.', 'gagal' => $list_gagal]);
                }
            }
        } else {
            setresponse(200, ['status' => false, 'msg' => 'Pilih file terlebih dahulu!']);
        }
    }

    public function upload_config($path)
    {
        // if (!is_dir($path)) mkdir($path, 0777, TRUE);
        $config['upload_path'] = './' . $path;
        $config['allowed_types'] = 'xlsx|XLSX';
        $config['max_filename'] = '255';
        $config['encrypt_name'] = TRUE;
        $config['max_size'] = 4096;
        $this->load->library('upload', $config);
    }

    public function hapus()
    {
        $Tanggal = $this->input->get('Tanggal');
        $KodePegawai = $this->input->get('KodePegawai');
        if ($this->crud->delete(['Tanggal' => $Tanggal, 'KodePegawai' => $KodePegawai], 'absensipegawai')) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Import Absensi Per Bulan',
                'Description' => 'hapus import data absensi pegawai ' . $KodePegawai . ' ' . $Tanggal
            ]);
            setresponse(200, ['status' => true, 'msg' => 'Berhasil menghapus absensi pegawai.']);
        } else {
            setresponse(200, ['status' => false, 'msg' => 'Gagal menghapus absensi pegawai.']);
        }
    }
}
