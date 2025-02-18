<?php
defined('BASEPATH') or exit('No direct script access allowed');

class jurnal_penyesuaian extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transjurnal j';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[59]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[59]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'jrnpenyesuaian';
            $data['title'] = 'Jurnal Penyesuaian';
            $data['view'] = 'akuntansi/v_jurnal_penyesuaian';
            $data['scripts'] = 'akuntansi/s_jurnal_penyesuaian';

            $data['tahunaktif'] = $this->akses->get_tahun_aktif();
            $data['tahunanggaran'] = $this->crud->get_rows([
                'select' => '*',
                'from' => 'msttahunanggaran',
                'where' => [['IsAktif !=' => null]],
            ]);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'transjurnal j';

            $kodetahun = $this->input->get('kodetahun');
            if ($kodetahun != '') {
                $configData['where'] = [[
                    'j.KodeTahun' => $kodetahun,
                    'j.TipeJurnal' => 'PENYESUAIAN',
                ]];
            } else {
                $configData['where'] = [['j.TipeJurnal' => 'PENYESUAIAN']];
            }

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (j.IDTransJurnal LIKE '%$cari%' OR j.NarasiJurnal LIKE '%$cari%')";
            }

            $tgl = escape($this->input->get('tgl'));
            if ($tgl != '') {
                $tgl = explode(" - ", $tgl);
                $tglawal = date('Y-m-d', strtotime($tgl[0]));
                $tglakhir = date('Y-m-d', strtotime($tgl[1]));
                $configData['filters'][] = " (DATE(j.TglTransJurnal) BETWEEN '$tglawal' AND '$tglakhir')";
            }

            $configData['join'] = [
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = j.UserName",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' msttahunanggaran ta',
                    'on' => "ta.KodeTahun = j.KodeTahun",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'j.IDTransJurnal', 'j.KodeTahun', 'j.TglTransJurnal', 'j.TipeJurnal', 'j.NarasiJurnal', 'j.NominalTransaksi', 'j.NoRefTrans', 'j.UserName', 'u.ActualName'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'j.TglTransJurnal';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'j.IDTransJurnal', 'j.KodeTahun', 'j.TglTransJurnal', 'j.TipeJurnal', 'j.NarasiJurnal', 'j.NominalTransaksi', 'j.NoRefTrans', 'j.UserName', 'u.ActualName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 59; //FiturID di tabel serverfitur
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
                $temp['TglTransJurnal'] = isset($temp['TglTransJurnal']) ? shortdate_indo(date('Y-m-d', strtotime($temp['TglTransJurnal']))) : '';
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btn-edit" href="' . base_url('akuntansi/jurnal_penyesuaian/edit/' . base64_encode($temp['IDTransJurnal'])) . '" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransJurnal'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete == 0) {
                    $temp['btn_aksi'] = '<a class="btn-edit" href="' . base_url('akuntansi/jurnal_penyesuaian/edit/' . base64_encode($temp['IDTransJurnal'])) . '" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit == 0) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['IDTransJurnal'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '-';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function checkManualCode()
    {
        $NoRefTrans = $this->input->get('NoRefTrans');
        $Manual_Lama = $this->input->get('Manual_Lama');
        if ($Manual_Lama != null && $Manual_Lama != '') {
            $count =  $this->crud->get_count([
                'select' => '*',
                'from' => 'transjurnal',
                'where' => [
                    [
                        'NoRefTrans' => $NoRefTrans,
                        'NoRefTrans !=' => $Manual_Lama,
                    ]
                ]
            ]);
        } else {
            $count =  $this->crud->get_count([
                'select' => '*',
                'from' => 'transjurnal',
                'where' => [['NoRefTrans' => $NoRefTrans]]
            ]);
        }
        if ($count > 0) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'No Referensi telah digunakan']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'No Referensi tersedia']);
        }
    }

    public function simpan()
    {
        $insertdata = $this->input->post();
        $isEdit = true;

        $nominaltransaksi = str_replace(['.', ','], ['', '.'], $this->input->post('NominalTransaksi'));
        unset($insertdata['NominalTransaksi']);
        $insertdata['NominalTransaksi'] = $nominaltransaksi;

        $tahun = $this->crud->get_one_row(
            [
                'select' => '*',
                'from' => 'msttahunanggaran',
                'where' => [['IsAktif' => 1]],
            ]
        );

        ## POST DATA
        if (!($this->input->post('IDTransJurnal') != null && $this->input->post('IDTransJurnal') != '')) {
            $prefix = "JRN-" . date("Ym");
            $insertdata['IDTransJurnal'] = $this->crud->get_kode([
                'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                'from' => 'transjurnal',
                'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'IDTransJurnal DESC',
                'prefix' => $prefix
            ]);
            $insertdata['KodeTahun'] = $tahun['KodeTahun'];
            $insertdata['TipeJurnal'] = "PENYESUAIAN";
            $insertdata['UserName'] = $this->session->userdata('UserName');

            $isEdit = false;
            $res = $this->crud->insert($insertdata, 'transjurnal');
        } else {
            $isEdit = true;
            $res = $this->crud->update($insertdata, ['IDTransJurnal' => $this->input->post('IDTransJurnal')], 'transjurnal');
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $aksi   = $isEdit ? "edit" : "tambah";
            $ket    = $isEdit ? "update" : "tambah";
            $id     = $isEdit ? $this->input->post('IDTransJurnal') : $insertdata['IDTransJurnal'];
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Jurnal Penyesuaian',
                'Description' => $ket . ' data jurnal penyesuaian ' . $id
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => ($isEdit ? "Berhasil menambah Data" : "Berhasil data Data"),
                'action' => $aksi,
                'id' => $id
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => ($isEdit ? "Gagal Edit Data" : "Gagal Menambah Data")
            ]);
        }
    }

    public function tambah()
    {
        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu'] = 'jrnpenyesuaian';
        $data['title'] = 'Tambah Jurnal Penyesuaian';
        $data['view'] = 'akuntansi/v_jurnal_penyesuaian_tambah';
        $data['scripts'] = 'akuntansi/s_jurnal_penyesuaian_tambah';

        $data['dtakun'] = $this->crud->get_rows([
            'select' => '*',
            'from' => 'mstakun',
            'where' => [[
                'IsParent' => 0,
                'IsAktif' => 1,
            ]]
        ]);
        $data['tahunaktif'] = $this->akses->get_tahun_aktif();

        loadview($data);
    }

    public function edit()
    {
        $idtransjurnal   = escape(base64_decode($this->uri->segment(4)));

        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu'] = 'jrnpenyesuaian';
        $data['title'] = 'Edit Jurnal Penyesuaian';
        $data['view'] = 'akuntansi/v_jurnal_penyesuaian_tambah';
        $data['scripts'] = 'akuntansi/s_jurnal_penyesuaian_tambah';

        $data['dtakun'] = $this->crud->get_rows([
            'select' => '*',
            'from' => 'mstakun',
            'where' => [[
                'IsParent' => 0,
                'IsAktif' => 1,
            ]]
        ]);
        $data['tahunaktif'] = $this->akses->get_tahun_aktif();

        $data['data'] = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transjurnal',
            'where' => [['IDTransJurnal' => $idtransjurnal]]
        ]);

        $data['item'] = $this->crud->get_rows([
            'select' => '*',
            'from' => 'transjurnalitem',
            'where' => [['IDTransJurnal' => $idtransjurnal]]
        ]);

        loadview($data);
    }

    public function check_total()
    {
        $nominal = $this->input->get('nominaltransaksi');
        $debet = $this->input->get('total_debet');
        $kredit = $this->input->get('total_kredit');
        if ($nominal == $debet && $nominal == $kredit) {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Total OKE']);
        } else {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Total Debet dan Total Kredit harus sama dengan Nominal Transaksi!']);
        }
    }

    public function simpanlangsung()
    {
        $this->db->trans_begin();
        $insertdata = $this->input->post();
        $insertdata['NominalTransaksi'] = str_replace('.', '', $this->input->post('NominalTransaksi'));
        unset($insertdata['KodeAkun']);
        unset($insertdata['Debet']);
        unset($insertdata['Kredit']);

        $IDTransJurnal = $this->input->post('IDTransJurnal');
        if (!($IDTransJurnal != null && $IDTransJurnal != '')) {
            $prefix = "JRN-" . date("Ym");
            $insertdata['IDTransJurnal'] = $this->crud->get_kode([
                'select' => 'RIGHT(IDTransJurnal, 7) AS KODE',
                'from' => 'transjurnal',
                'where' => [['LEFT(IDTransJurnal, 10) =' => $prefix]],
                'limit' => 1,
                'order_by' => 'IDTransJurnal DESC',
                'prefix' => $prefix
            ]);
            $insertdata['TipeJurnal'] = "PENYESUAIAN";
            $insertdata['UserName'] = $this->session->userdata('UserName');
            $aksi = "tambah";
            $ket = "tambah";
            $id = $insertdata['IDTransJurnal'];
            $msgtrue = "Berhasil Menambah Data";
            $msgfalse = "Gagal Menambah Data";
        } else {
            $aksi = "edit";
            $ket = "update";
            $id = $this->input->post('IDTransJurnal');
            $msgtrue = "Berhasil Mengedit Data";
            $msgfalse = "Gagal Mengedit Data";
        }

        $res = $this->crud->insert_or_update($insertdata, 'transjurnal');

        $kodeakun = $this->input->post('KodeAkun');
        $debet = $this->input->post('Debet');
        $kredit = $this->input->post('Kredit');
        if ($kodeakun) {
            $no = 1;
            foreach ($kodeakun as $key => $value) {
                if ($value != null && $value != '') {
                    $itemjurnal = [
                        'NoUrut' => $no,
                        'IDTransJurnal' => $insertdata['IDTransJurnal'],
                        'KodeTahun' => $insertdata['KodeTahun'],
                        'KodeAkun' => $value,
                        'NamaAkun' => $this->get_nama_akun($value),
                        'Debet' => str_replace('.', '', $debet[$key]),
                        'Kredit' => str_replace('.', '', $kredit[$key])
                    ];
                    $insertitemjurnal[] = $this->crud->insert_or_update($itemjurnal, 'transjurnalitem');
                }
                $no++;
            }
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Jurnal Penyesuaian',
                'Description' => $ket . ' data jurnal penyesuaian ' . $id
            ]);
            $this->db->trans_commit();
            echo json_encode([
                'status' => true,
                'msg'  => $msgtrue,
                'action' => $aksi,
                'id' => $id
            ]);
        } else {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => false,
                'msg'  => $msgfalse
            ]);
        }
    }

    public function get_nama_akun($kodeakun)
    {
        $data = $this->crud->get_one_row([
            'select' => 'NamaAkun',
            'from' => 'mstakun',
            'where' => [['KodeAkun' => $kodeakun]]
        ]);

        return $data['NamaAkun'];
    }

    public function hapus()
    {
        $kode  = $this->input->get('IDTransJurnal');
        $dtitemjurnal = $this->crud->get_rows([
            'select' => '*',
            'from' => 'transjurnalitem i',
            'join' => [[
                'table' => ' transjurnal t',
                'on' => "t.IDTransJurnal = i.IDTransJurnal",
                'param' => 'LEFT',
            ]],
            'where' => [['t.IDTransJurnal' => $kode]],
        ]);

        if (count($dtitemjurnal) > 0) {
            $deleteitemjurnal = $this->crud->delete(['IDTransJurnal' => $kode], 'transjurnalitem');
        }

        $res = $this->crud->delete(['IDTransJurnal' => $kode], 'transjurnal');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => 'hapus',
                'JenisTransaksi' => 'Jurnal Penyesuaian',
                'Description' => 'hapus data jurnal penyesuaian ' . $kode
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
            $where = [[
                'k.JenisTransaksiKas' => $jenis,
                'k.IDRekap' => null,
                'k.KodePegawai' => null,
            ]];
        } else {
            $where = [
                [
                    'k.IDRekap' => null,
                    'k.KodePegawai' => null,
                ],
                " (k.JenisTransaksiKas = 'KAS MASUK' OR k.JenisTransaksiKas = 'KAS KELUAR')"
            ];
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
        $this->load->view('transaksi/v_transaksi_kas_cetak', $data);
    }

    public function jurnal()
    {
        checkAccess($this->session->userdata('fiturview')[59]);
        $idtransjurnal   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM JURNAL
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'jrnpenyesuaian';
            $data['title'] = 'Jurnal Penyesuaian Detail';
            $data['view'] = 'akuntansi/v_jurnal_penyesuaian_detail';
            $data['scripts'] = 'akuntansi/s_jurnal_penyesuaian_detail';

            $dtjurnal = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'transjurnal t',
                'where' => [['t.IDTransJurnal' => $idtransjurnal]],
            ]);
            $data['dtjurnal'] = $dtjurnal;
            $data['IDTransJurnal'] = $idtransjurnal;

            $dtakun = [
                'select' => '*',
                'from' => 'mstakun',
                'where' => [[
                    'IsParent' => 0,
                    'IsAktif' => 1,
                ]]
            ];
            $data['dtakun'] = $this->crud->get_rows($dtakun);

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $idtransjurnal   = $this->input->get('idtransjurnal');
            $configData['table'] = 'transjurnalitem i';
            $configData['where'] = [['i.IDTransJurnal'  => $idtransjurnal]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.KodeAkun LIKE '%$cari%' OR i.NamaAkun LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' transjurnal t',
                    'on' => "t.IDTransJurnal = i.IDTransJurnal",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstakun a',
                    'on' => "a.KodeAkun = i.KodeAkun",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.NoUrut', 'i.IDTransJurnal', 'i.KodeTahun', 'i.KodeAkun', 'i.NamaAkun', 'i.Debet', 'i.Kredit', 'i.Uraian', 'i.Keterangan2'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.NoUrut', 'i.IDTransJurnal', 'i.KodeTahun', 'i.KodeAkun', 'i.NamaAkun', 'i.Debet', 'i.Kredit', 'i.Uraian', 'i.Keterangan2',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 59; //FiturID di tabel serverfitur
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
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDTransJurnal'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['IDTransJurnal'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function simpanjurnal()
    {
        $insertdata = $this->input->post();
        unset($insertdata['JenisJurnal']);
        unset($insertdata['Nominal']);
        $idtransjurnal = $this->input->post('IDTransJurnal');
        $nominal = str_replace(['.', ','], ['', '.'], $this->input->post('Nominal'));
        $insertdata['Debet'] = ($this->input->post('JenisJurnal') == "Debet") ? $nominal : 0;
        $insertdata['Kredit'] = ($this->input->post('JenisJurnal') == "Kredit") ? $nominal : 0;

        $dtjurnal = $this->crud->get_one_row([
            'select' => 't.IDTransJurnal, t.KodeTahun, SUM(i.Debet) AS Debet, SUM(i.Kredit) AS Kredit, t.NominalTransaksi',
            'from' => 'transjurnal t',
            'join' => [[
                'table' => ' transjurnalitem i',
                'on' => "i.IDTransJurnal = t.IDTransJurnal",
                'param' => 'LEFT',
            ]],
            'where' => [['t.IDTransJurnal' => $idtransjurnal]],
            'group_by' => 't.IDTransJurnal',
        ]);

        $isEdit = true;

        ## POST DATA
        if (!($this->input->post('NoUrut') != null && $this->input->post('NoUrut') != '')) {
            $isEdit = false;
            $getNoUrut = $this->db->from('transjurnalitem')
            ->where('IDTransJurnal', $this->input->post('IDTransJurnal'))
            ->select('NoUrut')
            ->order_by('NoUrut', 'desc')
            ->get()->row();
            if ($getNoUrut) {
                $NoUrut = (int)$getNoUrut->NoUrut;
            } else {
                $NoUrut = 0;
            }
            $insertdata['NoUrut'] = $NoUrut + 1;
            $nominaledit = 0;
            $isEdit = false;
        } else {
            $dtedit = $this->crud->get_one_row([
                'select' => '*',
                'from' => 'transjurnalitem',
                'where' => [[
                    'NoUrut' => $this->input->post('NoUrut'),
                    'IDTransJurnal' => $this->input->post('IDTransJurnal'),
                ]],
            ]);
            $nominaledit = ($dtedit['Debet'] > 0) ? $dtedit['Debet'] : $dtedit['Kredit'];
            $isEdit = true;
        }

        $debet = ($this->input->post('JenisJurnal') == "Debet") ? ($dtjurnal['Debet'] + $nominal) : $dtjurnal['Debet'];
        $jmldebet = $isEdit ? ($debet - $nominaledit) : $debet;

        $kredit = ($this->input->post('JenisJurnal') == "Kredit") ? ($dtjurnal['Kredit'] + $nominal) : $dtjurnal['Kredit'];
        $jmlkredit = $isEdit ? ($kredit - $nominaledit) : $kredit;

        if ($jmldebet > $dtjurnal['NominalTransaksi']) {
            echo json_encode([
                'status' => false,
                'msg'  => ($isEdit ? "Gagal mengedit data, jumlah debet tidak boleh melebihi nominal transaksi." : "Gagal menambah data, jumlah debet tidak boleh melebihi nominal transaksi.")
            ]);
        } elseif ($jmlkredit > $dtjurnal['NominalTransaksi']) {
            echo json_encode([
                'status' => false,
                'msg'  => ($isEdit ? "Gagal mengedit data, jumlah kredit tidak boleh melebihi nominal transaksi." : "Gagal menambah data, jumlah kredit tidak boleh melebihi nominal transaksi.")
            ]);
        } else {
            $res = $this->crud->insert_or_update($insertdata, 'transjurnalitem');

            if ($res) {
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
    }

    public function hapusjurnal()
    {
        $kode = $this->input->get('IDTransJurnal');
        $kode2 = $this->input->get('NoUrut');

        $res = $this->crud->delete(['IDTransJurnal' => $kode, 'NoUrut' => $kode2], 'transjurnalitem');

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

    public function cetakjurnal()
    {
        $idtransjurnal   = escape(base64_decode($this->uri->segment(4)));

        $data['dtinduk'] = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'transjurnal t',
            'join' => [[
                'table' => ' transaksikas k',
                'on' => "k.NoTransKas = t.NoRefTrans",
                'param' => 'LEFT',
            ]],
            'where' => [['t.IDTransJurnal' => $idtransjurnal]],
        ]);

        $sql = $this->crud->get_rows([
            'select' => '*',
            'from' => 'transjurnalitem',
            'where' => [['IDTransJurnal' => $idtransjurnal]],
        ]);
        $data['model'] = $sql;
        $data['IDTransJurnal'] = $idtransjurnal;
        
        $this->load->library('Pdf');
        $this->load->view('transaksi/v_transaksi_kas_jurnal_cetak', $data);
    }
}
