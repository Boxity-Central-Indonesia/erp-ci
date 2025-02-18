<?php
defined('BASEPATH') or exit('No direct script access allowed');

class pegawai extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'mstpegawai';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[10]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[10]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'pegawai';
            $data['title'] = 'Master Pegawai';
            $data['view'] = 'master/v_pegawai';
            $data['scripts'] = 'master/s_pegawai';

            $dtjab = [
                'select' => 'KodeJabatan, NamaJabatan',
                'from' => 'mstjabatan',
                'where' => [['IsAktif !=' => null]],
                'order_by' => 'KodeJabatan'
            ];
            $data['dtjab'] = $this->crud->get_rows($dtjab);

            $atasan = [
                'select' => 'pgat.KodePegawai, pgat.NamaPegawai, jbat.NamaJabatan',
                'from' => 'mstpegawai pgat',
                'where' => [
                    [
                        'pgat.IsAktif !=' => null,
                        // 'KodeJabAtasanLangsung' => null
                    ]
                ],
                'join' => [
                    [
                        'table' => ' mstjabatan jbat',
                        'on' => "jbat.KodeJabatan = pgat.KodeJabatan",
                        'param' => 'LEFT'
                    ]
                ],
                'order_by' => 'pgat.KodePegawai'
            ];
            $data['atasan'] = $this->crud->get_rows($atasan);

            // get data bank from API
            $data['dtbank'] = $this->lokasi->get_all_bank();

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstpegawai p';

            $status   = $this->input->get('isaktif');
            $cari     = $this->input->get('cari');
            $jabatan  = $this->input->get('jabatan');

            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPegawai LIKE '%$cari%')";
            }
            if ($status != '') {
                $configData['filters'][] = " p.IsAktif = $status ";
            }
            if ($jabatan != '') {
                $configData['where'] = [['p.KodeJabatan' => $jabatan]];
            }

            $configData['join'] = [
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = p.KodeJabatan",
                    'param' => 'LEFT'

                ],
                [
                    'table' => ' mstpegawai pa',
                    'on' => "pa.KodePegawai = p.KodeJabAtasanLangsung",
                    'param' => 'LEFT'
                ],
                [
                    'table' => ' mstjabatan ja',
                    'on' => "ja.KodeJabatan = pa.KodeJabatan",
                    'param' => 'LEFT'
                ]
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'p.KodePegawai', 'p.NIP', 'p.NamaPegawai', 'p.TTL', 'p.Alamat', 'p.TelpHP', 'p.Email', 'p.TglMulaiKerja', 'p.TglResign', 'p.IsAktif', 'p.IDFinger', 'p.KodeJabatan', 'j.NamaJabatan', 'p.KodeJabAtasanLangsung', 'pa.NamaPegawai as NamaAtasan', 'ja.NamaJabatan as JabatanAtasan', 'p.KodeBank', 'p.NoRek', 'p.GajiPokok', 'p.IsGajiHarian', 'p.JenisPegawai'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'p.KodePegawai';
            $configData['custom_column_sort_order'] = 'ASC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'p.KodePegawai', 'p.NIP', 'p.NamaPegawai', 'p.TTL', 'p.Alamat', 'p.TelpHP', 'p.Email', 'p.TglMulaiKerja', 'p.TglResign', 'p.IsAktif', 'p.IDFinger', 'p.KodeJabatan', 'j.NamaJabatan', 'p.KodeJabAtasanLangsung', 'pa.NamaPegawai as NamaAtasan', 'ja.NamaJabatan as JabatanAtasan', 'p.KodeBank', 'p.NoRek', 'p.GajiPokok', 'p.IsGajiHarian', 'p.JenisPegawai',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 10; //FiturID di tabel serverfitur
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
                $temp['IsAktif'] = $temp['IsAktif'] == "1" ? 'Aktif' : 'NonAktif';
                $temp['JabatanAtasan'] = $temp['JabatanAtasan'] != null ? $temp['JabatanAtasan'] : '-';
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodePegawai'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodePegawai'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodePegawai'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>') . '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['KodePegawai'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . ((int)$record->IsAktif == 1 ? '&nbsp;&nbsp;<a data-kode=' . $temp['KodePegawai'] . ' class="btnaktif" type="button" data-value="0" data-kode=' . $temp['KodePegawai'] . '><i class="fa fa-ban"></i></a>' : '&nbsp;&nbsp;<a class="btnaktif" type="button" data-kode=' . $temp['KodePegawai'] . ' title="Aktifkan" data-value="1"><i class="fa fa-check"></i></a>');
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['KodePegawai'] . ' class="btnhapus" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
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
        $isEdit = true;
        unset($insertdata['Isedit']);
        unset($insertdata['GajiPokok']);
        unset($insertdata['IsGajiHarian']);
        $gapok = str_replace(['.', ','], ['', '.'], $this->input->post('GajiPokok'));
        $gh = ($this->input->post('IsGajiHarian') == 'on') ? 1 : 0;
        $insertdata['GajiPokok'] = $gapok;
        $insertdata['IsGajiHarian'] = $gh;

        ## POST DATA
        if (!($this->input->post('KodePegawai') != null && $this->input->post('KodePegawai') != '')) {
            $insertdata['KodePegawai'] = $this->crud->get_kode([
                'select' => 'RIGHT(KodePegawai, 7) AS KODE',
                'limit' => 1,
                'order_by' => 'KodePegawai DESC',
                'prefix' => 'PEG'
            ]);
            $insertdata['IsAktif'] = 1;
            $isEdit = false;
        } else {
            $isEdit = true;
        }
        $res = $this->crud->insert_or_update($insertdata, 'mstpegawai');

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('KodePegawai') : $insertdata['KodePegawai'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Master Pegawai',
                'Description' => $ket . ' data master pegawai ' . $id
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

    public function checkDB()
    {
        $nipLama = $this->input->get('nipLama');
        $emailLama = $this->input->get('emailLama');
        $fingerLama = $this->input->get('fingerLama');
        $NIP = $this->input->get('NIP');
        $Email = $this->input->get('Email');
        $IDFinger = $this->input->get('IDFinger');

        $countNIP =  $this->crud->get_count([
            'select' => 'NIP',
            'from' => 'mstpegawai',
            'where' => [
                [
                    'NIP' => $NIP,
                    'NIP !=' => $nipLama
                ]
            ]
        ]);
        $countEmail =  $this->crud->get_count([
            'select' => 'Email',
            'from' => 'mstpegawai',
            'where' => [
                [
                    'Email' => $Email,
                    'Email !=' => $emailLama
                ]
            ]
        ]);
        $countFinger = $this->crud->get_count([
            'select' => 'IDFinger',
            'from' => 'mstpegawai',
            'where' => [
                [
                    'IDFinger' => $IDFinger,
                    'IDFinger !=' => $fingerLama
                ]
            ]
        ]);
        if ($countNIP > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'NIP telah terdaftar']);
        } elseif ($countEmail > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Email telah terdaftar']);
        } elseif ($countFinger > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'ID Finger telah terdaftar']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'NIP dan Email tersedia']);
        }
    }

    public function import()
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
                        $ceknip = $this->crud->get_one_row([
                            'select' => 'p.KodePegawai',
                            'from' => 'mstpegawai p',
                            'where' => [['p.NIP' => $val[0]]]
                        ]);
                        $cekfinger = $this->crud->get_one_row([
                            'select' => 'p.KodePegawai',
                            'from' => 'mstpegawai p',
                            'where' => [['p.IDFinger' => $val[6]]]
                        ]);
                        if ($ceknip != null && $cekfinger != null) {
                            $list_gagal[] = ['Data ke ' . ($key + 1) . ' NIP atau ID Finger sudah digunakan.'];
                            continue;
                        }

                        $gapok   = str_replace(',', '', $val[7]);
                        $replacetgl = str_replace('/', '-', $val[5]);
                        $tgl     = explode('-', $replacetgl);
                        $m       = ($cekDataType == 'n') ? $tgl[0] : $tgl[1];
                        $d       = ($cekDataType == 'n') ? $tgl[1] : $tgl[0];
                        $year    = $tgl[2];
                        $month   = (strlen($m) > 1) ? $m : '0'.$m;
                        $day     = (strlen($d) > 1) ? $d : '0'.$d;
                        $tanggal = $year.'-'.$month.'-'.$day;
                        $nohp    = (substr($val[3], 0, 1) != 0) ? '0'.$val[3] : $val[3];

                        $kodepeg = $this->crud->get_kode([
                            'select' => 'RIGHT(KodePegawai, 7) AS KODE',
                            'limit' => 1,
                            'order_by' => 'KodePegawai DESC',
                            'prefix' => 'PEG'
                        ]);

                        if ($ceknip == null && $cekfinger == null) {
                            $list = [
                                'KodePegawai' => $kodepeg,
                                'NIP' => $val[0],
                                'NamaPegawai' => $val[1],
                                'Alamat' => $val[2],
                                'TelpHP' => $nohp,
                                'Email' => $val[4],
                                'TglMulaiKerja' => $tanggal,
                                'IDFinger' => $val[6],
                                'GajiPokok' => $gapok,
                                'JenisPegawai' => $val[8],
                                'IsAktif' => 1
                            ];

                            $result[] = $this->crud->insert($list, 'mstpegawai');
                        }
                    }
                }
                if (file_exists($file_name)) {
                    unlink($file_name);
                }
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
        $kode = $this->input->get('KodePegawai');
        $res = $this->crud->delete(['KodePegawai' => $kode], 'mstpegawai');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Master Pegawai',
                'Description' => 'hapus data master pegawai ' . $kode
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

    public function aktif()
    {
        $kode = $this->input->get('KodePegawai');
        $value = (int) $this->input->get('IsAktif');

        $data = ['IsAktif' => $value];
        $result = $this->crud->update($data, ['KodePegawai' => $kode], "mstpegawai");

        if ($result) {
            $keterangan = 'update data tahun ' . $kode;
            $aksi = 'edit';
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'edit',
                'JenisTransaksi' => 'Master Pegawai',
                'Description' => 'update data master pegawai ' . $kode
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Mengubah Data"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Mengubah Data"
            ]);
        }
    }
}
