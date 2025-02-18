<?php
defined('BASEPATH') or exit('No direct script access allowed');

class laporan_slipgaji extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'rekapinsentifbulanan';
        checkAccess($this->session->userdata('fiturview')[55]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[55]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'lapslipgaji';
            $data['title'] = 'Laporan Gaji';
            $data['view'] = 'payroll/v_laporan_slipgaji';
            $data['scripts'] = 'payroll/s_laporan_slipgaji';

            $month = $this->input->get('bulan');
            $data['month'] = ($month != null) ? date('Y-m', strtotime($month)) : date('Y-m');
            $month_ins = $this->input->get('bulan-ins');
            $data['month_ins'] = ($month_ins != null) ? date('Y-m', strtotime($month_ins)) : date('Y-m');
            $month_pjm = $this->input->get('bulan-pjm');
            $data['month_pjm'] = ($month_pjm != null) ? date('Y-m', strtotime($month_pjm)) : date('Y-m');

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'rekapinsentifbulanan r';

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPegawai LIKE '%$cari%' OR j.NamaJabatan LIKE '%$cari%' OR r.TotalInsentif LIKE '%$cari%')";
            }

            $bulan     = $this->input->get('bulan');
            if ($bulan) {
                $configData['where'] = [['r.Bulan' => $bulan]];
            }

            $configData['join'] = [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = p.KodeJabatan",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.KodePegawai = r.KodePegawai AND k.IDRekap = r.IDRekap",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' iteminsentifbulanan i',
                    'on' => "i.IDRekap = r.IDRekap AND i.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ]
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'r.IDRekap', 'r.KodeTahun', 'r.Bulan', 'r.TglRekap', 'r.TotalInsentif', 'r.Keterangan', 'r.IsTelahDibayarkan', 'r.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan', 'r.NoTransBayar', 'SUM(if(i.CaraHitung = "Tambah", i.JmlPerolehan, 0)) - SUM(if(i.CaraHitung = "Kurang", i.JmlPerolehan, 0)) AS InsentifPegawai'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'r.IDRekap, r.KodePegawai';
            $configData['custom_column_sort_order'] = 'ASC';
            $configData['group_by'] = 'r.IDRekap';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'r.IDRekap', 'r.KodeTahun', 'r.Bulan', 'r.TglRekap', 'r.TotalInsentif', 'r.Keterangan', 'r.IsTelahDibayarkan', 'r.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan', 'r.NoTransBayar', 'SUM(if(i.CaraHitung = "Tambah", i.JmlPerolehan, 0)) - SUM(if(i.CaraHitung = "Kurang", i.JmlPerolehan, 0)) AS InsentifPegawai',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 55; //FiturID di tabel serverfitur
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
            $canPrint = 0;
            $print = [];
            foreach ($this->session->userdata('fiturprint') as $key => $value) {
                $print[$key] = $value;
                if ($key == $FiturID && $value == 1) {
                    $canPrint = 1;
                }
            }

            foreach ($records as $record) {
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                setlocale(LC_ALL, 'IND');
                $temp['Bulan'] = isset($temp['Bulan']) ? strftime('%B %Y', strtotime($temp['Bulan'])) : '';
                $temp['IsTelahDibayarkan'] = $temp['IsTelahDibayarkan'] == 0 ? 'Belum' : 'Sudah';
                $temp['InsentifPegawai'] = isset($temp['InsentifPegawai']) ? $temp['InsentifPegawai'] : 0;
                if ($canPrint == 1 && $temp['IsTelahDibayarkan'] == 'Sudah') {
                    $temp['btn_aksi'] = '<a target="_blank" class="btnprint" href="' . base_url('payroll/laporan_slipgaji/cetak/' . base64_encode($temp['IDRekap'])) . '" type="button" title="Cetak Slip Gaji"><span class="fa fa-print" aria-hidden="true"></span></a>';
                } elseif ($canPrint == 1 && $temp['IsTelahDibayarkan'] == 'Belum') {
                    $temp['btn_aksi'] = '<a target="_blank" class="btnbelum" type="button" title="Cetak Slip Gaji"><span class="fa fa-print" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetaklist_slip()
    {
        setlocale(LC_ALL, 'IND');
        $bulan = $this->uri->segment(4);

        $models = $this->crud->get_rows([
            'select' => 'r.IDRekap, r.KodeTahun, r.Bulan, r.TglRekap, r.TotalInsentif, r.Keterangan, r.IsTelahDibayarkan, r.KodePegawai, p.NamaPegawai, p.NIP, p.KodeJabatan, j.NamaJabatan, r.NoTransBayar, SUM(if(i.CaraHitung = "Tambah", i.JmlPerolehan, 0)) - SUM(if(i.CaraHitung = "Kurang", i.JmlPerolehan, 0)) AS InsentifPegawai',
            'from' => 'rekapinsentifbulanan r',
            'join' => [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = p.KodeJabatan",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.KodePegawai = r.KodePegawai AND k.IDRekap = r.IDRekap",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' iteminsentifbulanan i',
                    'on' => "i.IDRekap = r.IDRekap AND i.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ]
            ],
            'where' => [['r.Bulan' => $bulan]],
            'group_by' => 'r.IDRekap, r.KodePegawai'
        ]);

        $data['bulan'] = isset($bulan) ? strftime('%B %Y', strtotime($bulan)) : '';
        $data['model'] = $models;
        $data['total'] = count($models);
        $data['src_url'] = base_url('payroll/laporan_slipgaji?bulan=') . $bulan;

        if ($bulan != null) {
            $this->load->library('Pdf');
            $this->load->view('payroll/cetak_list_slip', $data);
        } else {
            echo 'Silahkan pilih periode/bulan terlebih dahulu!';
        }
    }

    public function cetak()
    {
        setlocale(LC_ALL, 'IND');
        $idrekap   = escape(base64_decode($this->uri->segment(4)));
        $data['src_url'] = base_url('payroll/laporan_slipgaji');
        $data['dtinduk'] = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'rekapinsentifbulanan r',
            'join' => [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = p.KodeJabatan",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['IDRekap' => $idrekap]],
        ]);
        $data['bulan'] = strftime('%B %Y', strtotime($data['dtinduk']['Bulan']));

        $sql = [
            'select' => '*',
            'from' => 'iteminsentifbulanan i',
            'where' => [['i.IDRekap' => $idrekap]],
        ];
        $data['model'] = $this->crud->get_rows($sql);
        $data['IDRekap'] = $idrekap;

        $this->load->library('Pdf');
        $this->load->view('payroll/cetak_slip_gaji', $data);
    }

    public function detail()
    {
        checkAccess($this->session->userdata('fiturview')[55]);
        $idrekap   = escape(base64_decode($this->uri->segment(4)));

        ## AMBIL DATA ITEM PEMBELIAN
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'penerimaangaji';
            $data['title'] = 'Detail Transaksi Penerimaan Gaji';
            $data['view'] = 'payroll/v_penerimaan_gaji_detail';
            $data['scripts'] = 'payroll/s_penerimaan_gaji_detail';

            $dtinduk = [
                'select' => 'r.IDRekap, r.KodePegawai, r.Bulan, p.NIP, p.NamaPegawai, p.KodeJabatan, j.NamaJabatan, SUM(if(i.CaraHitung = "Tambah", i.JmlPerolehan, 0)) - SUM(if(i.CaraHitung = "Kurang", i.JmlPerolehan, 0)) AS InsentifPegawai',
                'from' => 'rekapinsentifbulanan r',
                'join' => [
                    [
                        'table' => ' mstpegawai p',
                        'on' => "p.KodePegawai = r.KodePegawai",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' mstjabatan j',
                        'on' => "j.KodeJabatan = p.KodeJabatan",
                        'param' => 'LEFT',
                    ],
                    [
                        'table' => ' iteminsentifbulanan i',
                        'on' => "i.IDRekap = r.IDRekap AND i.KodePegawai = r.KodePegawai",
                        'param' => 'LEFT',
                    ],
                ],
                'where' => [['r.IDRekap' => $idrekap]],
            ];
            $data['dtinduk'] = $this->crud->get_one_row($dtinduk);
            $data['IDRekap'] = $idrekap;

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $idrekap   = $this->input->get('idrekap');
            $configData['table'] = 'iteminsentifbulanan i';
            $configData['where'] = [['i.IDRekap'  => $idrekap]];

            $cari     = $this->input->get('cari');
            if ($cari != '') {
                $configData['filters'][] = " (i.BagianPekerjaan LIKE '%$cari%' OR i.NamaPekerjaan LIKE '%$cari%')";
            }

            $configData['join'] = [
                [
                    'table' => ' rekapinsentifbulanan r',
                    'on' => "r.IDRekap = i.IDRekap",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = i.KodePegawai",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'i.NoUrut', 'i.IDRekap', 'i.KodePegawai', 'i.BagianPekerjaan', 'i.NamaPekerjaan', 'i.SatuanPekerjaan', 'i.RateSatuan', 'i.JmlPerolehan', 'i.NominalInsentif', 'i.CaraHitung', 'i.Keterangan', 'r.KodeTahun', 'r.Bulan', 'r.TglRekap', 'p.NIP', 'p.NamaPegawai'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'i.NoUrut', 'i.IDRekap', 'i.KodePegawai', 'i.BagianPekerjaan', 'i.NamaPekerjaan', 'i.SatuanPekerjaan', 'i.RateSatuan', 'i.JmlPerolehan', 'i.NominalInsentif', 'i.CaraHitung', 'i.Keterangan', 'r.KodeTahun', 'r.Bulan', 'r.TglRekap', 'p.NIP', 'p.NamaPegawai',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 55; //FiturID di tabel serverfitur
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
                $temp['JmlPerolehan'] = ($temp['CaraHitung'] == 'Tambah') ? $temp['JmlPerolehan'] : '('.str_replace(',', '.', number_format($temp['JmlPerolehan'])).')';
                if ($canEdit == 1 && $canDelete == 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>' . '&nbsp;&nbsp;<a href="javascript:void(0);" data-kode=' . $temp['IDRekap'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } elseif ($canEdit == 1 && $canDelete != 1) {
                    $temp['btn_aksi'] = '<a class="btnedit" href="#" type="button" data-model=\'' . json_encode($record) . '\' title="Edit"><i class="fa fa-edit"></i></a>';
                } elseif ($canDelete == 1 && $canEdit != 1) {
                    $temp['btn_aksi'] = '<a href="javascript:void(0);" data-kode=' . $temp['IDRekap'] . ' data-kode2=' . $temp['NoUrut'] . ' class="btnhapus" type="button" title="Hapus"><span class="fa fa-trash" aria-hidden="true"></span></a>';
                } else {
                    $temp['btn_aksi'] = '';
                }
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function insentif()
    {
        checkAccess($this->session->userdata('fiturview')[55]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'lapslipgaji';
            $data['title'] = 'Laporan Gaji';
            $data['view'] = 'payroll/v_laporan_slipgaji';
            $data['scripts'] = 'payroll/s_laporan_slipgaji';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'mstpegawai p';

            $cari     = $this->input->get('cari_ins');
            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPegawai LIKE '%$cari%' OR j.NamaJabatan LIKE '%$cari%')";
            }

            $bulan     = $this->input->get('bulan_ins');

            $configData['where'] = [['p.IsAktif !=' => null]];

            $configData['join'] = [
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = p.KodeJabatan",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'p.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan'
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
                'p.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 55; //FiturID di tabel serverfitur
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
            $canPrint = 0;
            $print = [];
            foreach ($this->session->userdata('fiturprint') as $key => $value) {
                $print[$key] = $value;
                if ($key == $FiturID && $value == 1) {
                    $canPrint = 1;
                }
            }

            foreach ($records as $record) {
                setlocale(LC_ALL, 'IND');
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['Bulan'] = ($bulan != '') ? strftime('%B %Y', strtotime($bulan)) : '';
                $temp['InsentifPegawai'] = $this->getTotalInsentif($temp['KodePegawai'], $bulan);
                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetaklist_insentif()
    {
        setlocale(LC_ALL, 'IND');
        $bulan = $this->uri->segment(4);

        $dtpegawai = $this->crud->get_rows([
            'select' => 'p.KodePegawai, p.NamaPegawai, p.NIP, p.KodeJabatan, j.NamaJabatan',
            'from' => 'mstpegawai p',
            'join' => [[
                'table' => ' mstjabatan j',
                'on' => "j.KodeJabatan = p.KodeJabatan",
                'param' => 'LEFT',
            ]],
            'where' => [['p.IsAktif !=' => null]]
        ]);

        $data['model'] = [];
        foreach ($dtpegawai as $key) {
            $data['model'][] = [
                'KodePegawai'       => $key['KodePegawai'],
                'NamaPegawai'       => $key['NamaPegawai'],
                'NIP'               => $key['NIP'],
                'KodeJabatan'       => $key['KodeJabatan'],
                'NamaJabatan'       => $key['NamaJabatan'],
                'InsentifPegawai'   => $this->getTotalInsentif($key['KodePegawai'], $bulan)
            ];
        }

        $data['total'] = sizeof($dtpegawai);
        $data['bulan'] = isset($bulan) ? strftime('%B %Y', strtotime($bulan)) : '';
        $data['src_url'] = base_url('payroll/laporan_slipgaji?bulan-ins=') . $bulan;

        if ($bulan != null) {
            $this->load->library('Pdf');
            $this->load->view('payroll/cetak_list_insentif', $data);
        } else {
            echo 'Silahkan pilih periode/bulan terlebih dahulu!';
        }
    }

    public function getTotalInsentif($kodepegawai, $bulan)
    {
        $bln = date('Y-m', strtotime($bulan));
        $data = $this->crud->get_one_row([
            'select' => 'SUM(i.JmlPerolehan) AS InsentifPegawai',
            'from' => 'rekapinsentifbulanan r',
            'join' => [
                [
                    'table' => ' iteminsentifbulanan i',
                    'on' => "i.IDRekap = r.IDRekap AND i.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ]
            ],
            'where' => [
                [
                    'r.KodePegawai' => $kodepegawai,
                    'r.Bulan' => $bln,
                    'i.BagianPekerjaan' => 'Aktivitas',
                    'i.CaraHitung' => 'Tambah'
                ]
            ]
        ]);

        $total = isset($data['InsentifPegawai']) ? (int)$data['InsentifPegawai'] : 0;

        return $total;
    }

    public function pinjaman()
    {
        checkAccess($this->session->userdata('fiturview')[55]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'lapslipgaji';
            $data['title'] = 'Laporan Gaji';
            $data['view'] = 'payroll/v_laporan_slipgaji';
            $data['scripts'] = 'payroll/s_laporan_slipgaji';
            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'rekapinsentifbulanan r';

            $cari     = $this->input->get('cari_pjm');
            if ($cari != '') {
                $configData['filters'][] = " (p.NamaPegawai LIKE '%$cari%' OR j.NamaJabatan LIKE '%$cari%' OR r.TotalInsentif LIKE '%$cari%')";
            }

            $bulan     = $this->input->get('bulan_pjm');
            if ($bulan) {
                $configData['where'] = [
                    [
                        'r.Bulan' => $bulan,
                        'i.BagianPekerjaan' => 'Komponen Gaji',
                        'i.NamaPekerjaan' => 'Potongan Pinjaman',
                        'i.CaraHitung' => 'Kurang'
                    ]
                ];
            } else {
                $configData['where'] = [
                    [
                        'i.BagianPekerjaan' => 'Komponen Gaji',
                        'i.NamaPekerjaan' => 'Potongan Pinjaman',
                        'i.CaraHitung' => 'Kurang'
                    ]
                ];
            }

            $configData['join'] = [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = p.KodeJabatan",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.KodePegawai = r.KodePegawai AND k.IDRekap = r.IDRekap",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' iteminsentifbulanan i',
                    'on' => "i.IDRekap = r.IDRekap AND i.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ]
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'r.IDRekap', 'r.KodeTahun', 'r.Bulan', 'r.TglRekap', 'r.TotalInsentif', 'r.Keterangan', 'r.IsTelahDibayarkan', 'r.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan', 'r.NoTransBayar', 'SUM(i.JmlPerolehan) AS InsentifPegawai'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'r.IDRekap, r.KodePegawai';
            $configData['custom_column_sort_order'] = 'ASC';
            $configData['group_by'] = 'r.IDRekap';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'r.IDRekap', 'r.KodeTahun', 'r.Bulan', 'r.TglRekap', 'r.TotalInsentif', 'r.Keterangan', 'r.IsTelahDibayarkan', 'r.KodePegawai', 'p.NamaPegawai', 'p.NIP', 'p.KodeJabatan', 'j.NamaJabatan', 'r.NoTransBayar', 'SUM(i.JmlPerolehan) AS InsentifPegawai',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            ## Set fitur level untuk edit dan hapus
            $FiturID = 55; //FiturID di tabel serverfitur
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
            $canPrint = 0;
            $print = [];
            foreach ($this->session->userdata('fiturprint') as $key => $value) {
                $print[$key] = $value;
                if ($key == $FiturID && $value == 1) {
                    $canPrint = 1;
                }
            }

            $bln = '';
            foreach ($records as $record) {
                setlocale(LC_ALL, 'IND');
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $bln = date('Y-m', strtotime($temp['Bulan']));
                $temp['Bulan'] = isset($temp['Bulan']) ? strftime('%B %Y', strtotime($temp['Bulan'])) : '';
                $temp['IsTelahDibayarkan'] = $temp['IsTelahDibayarkan'] == 0 ? 'Belum' : 'Sudah';
                $temp['InsentifPegawai'] = isset($temp['InsentifPegawai']) ? $temp['InsentifPegawai'] : 0;

                $temp['TotalDibayar'] = $this->getTotalBayar($temp['KodePegawai'], $bln);
                $temp['SisaBayar'] = $temp['InsentifPegawai'] - $temp['TotalDibayar'];

                $temp['btn_aksi'] = '';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function cetaklist_pinjaman()
    {
        setlocale(LC_ALL, 'IND');
        $bulan = $this->uri->segment(4);

        $dtrekap = $this->crud->get_rows([
            'select' => 'r.IDRekap, r.KodeTahun, r.Bulan, r.TglRekap, r.TotalInsentif, r.Keterangan, r.IsTelahDibayarkan, r.KodePegawai, p.NamaPegawai, p.NIP, p.KodeJabatan, j.NamaJabatan, r.NoTransBayar, SUM(i.JmlPerolehan) AS InsentifPegawai',
            'from' => 'rekapinsentifbulanan r',
            'join' => [
                [
                    'table' => ' mstpegawai p',
                    'on' => "p.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' mstjabatan j',
                    'on' => "j.KodeJabatan = p.KodeJabatan",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' transaksikas k',
                    'on' => "k.KodePegawai = r.KodePegawai AND k.IDRekap = r.IDRekap",
                    'param' => 'LEFT',
                ],
                [
                    'table' => ' iteminsentifbulanan i',
                    'on' => "i.IDRekap = r.IDRekap AND i.KodePegawai = r.KodePegawai",
                    'param' => 'LEFT',
                ]
            ],
            'where' => [
                [
                    'r.Bulan' => $bulan,
                    'i.BagianPekerjaan' => 'Komponen Gaji',
                    'i.NamaPekerjaan' => 'Potongan Pinjaman',
                    'i.CaraHitung' => 'Kurang'
                ]
            ],
            'group_by' => 'r.IDRekap, r.KodePegawai'
        ]);

        $data['model'] = [];
        $bln = date('Y-m', strtotime($bulan));
        $totalbayar = 0;
        $sisabayar = 0;
        foreach ($dtrekap as $key) {
            $totalbayar = $this->getTotalBayar($key['KodePegawai'], $bln);
            $sisabayar = $key['InsentifPegawai'] - $totalbayar;
            $data['model'][] = [
                'KodePegawai'   => $key['KodePegawai'],
                'NIP'           => $key['NIP'],
                'NamaPegawai'   => $key['NamaPegawai'],
                'NamaJabatan'   => $key['NamaJabatan'],
                'NominalPinjam' => $key['InsentifPegawai'],
                'TotalDibayar'  => $totalbayar,
                'SisaBayar'     => $sisabayar
            ];
        }

        $data['bulan'] = isset($bulan) ? strftime('%B %Y', strtotime($bulan)) : '';
        $data['total'] = sizeof($dtrekap);
        $data['src_url'] = base_url('payroll/laporan_slipgaji?bulan-pjm=') . $bulan;

        if ($bulan != null) {
            $this->load->library('Pdf');
            $this->load->view('payroll/cetak_list_pinjaman', $data);
        } else {
            echo 'Silahkan pilih periode/bulan terlebih dahulu!';
        }
    }

    public function getTotalBayar($kodepegawai, $bulan)
    {
        $data = $this->crud->get_one_row([
            'select' => 'SUM(NominalPinjam) as TotalPinjam, SUM(NominalDibayar) as TotalDibayar',
            'from' => 'trpinjamankaryawan',
            'where' => [
                [
                    'KodePegawai' => $kodepegawai,
                    'DATE_FORMAT(TanggalPinjam, "%Y-%m") =' => $bulan
                ]
            ],
            'group_by' => 'KodePegawai'
        ]);

        $total = isset($data['TotalDibayar']) ? (int)$data['TotalDibayar'] : 0;

        return $total;
    }
}
