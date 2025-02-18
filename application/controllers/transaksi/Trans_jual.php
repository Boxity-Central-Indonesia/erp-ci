<?php
defined('BASEPATH') or exit('No direct script access allowed');

class trans_jual extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'transpenjualan j';
        $this->load->model('M_Lokasi', 'lokasi');
    }

    public function index()
    {
        $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
        $data['menu'] = 'trans_jual';
        $data['title'] = 'Transaksi Penjualan';
        $data['view'] = 'transaksi/v_trans_jual';
        $data['scripts'] = 'transaksi/s_trans_jual';
        $data['customer'] = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'mstperson',
                'where' => [
                    [
                        'IsAktif' => 1,
                        'JenisPerson' => 'CUSTOMER'
                    ]
                ],
                'order_by' => 'KodePerson'
            ]
        );

        $customer_piutang = [
            'select' => '*',
            'from' => 'mstperson p',
            'join' => [
                [
                    'table' => ' transpenjualan j',
                    'on' => "j.KodePerson = p.KodePerson",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [
                [
                    // 'p.IsAktif' => 1,
                    'p.JenisPerson' => 'CUSTOMER',
                    'j.StatusBayar !=' => 'LUNAS',
                    'j.StatusProses' => 'DONE',
                ]
            ],
            'group_by' => 'p.KodePerson',
            'order_by' => 'p.KodePerson'
        ];
        $data['customer_piutang'] = $this->crud->get_rows($customer_piutang);

        $idtrans_inretur = $this->crud->get_rows([
            'select' => 'IDTrans',
            'from' => 'transaksiretur',
            'where' => [[
                'JenisRetur'         => 'RETUR_JUAL',
                'LEFT(IDTrans, 3) =' => 'TJL',
                'IsVoid'             => 0,
            ]]
        ]);

        $idtrans_selected = ['0'];
        foreach ($idtrans_inretur as $key) {
            $idtrans_selected[] = $key['IDTrans'];
        }

        $data['idtrans'] = $this->db->select('IDTransJual')
            ->from('transpenjualan')
            ->where([
                'StatusProses' => 'DONE',
                'StatusKirim' => 'TERKIRIM',
            ])
            ->where_not_in('IDTransJual', $idtrans_selected)
            ->get()
            ->result_array();

        $data['gudang'] = $this->crud->get_rows(
            [
                'select' => '*',
                'from' => 'mstgudang',
            ]
        );

        $data['sliporderview'] = 0;
        $data['sliporderadd'] = 0;
        $data['quotationview'] = 0;
        $data['quotationadd'] = 0;
        $data['transjualview'] = 0;
        $data['transjualadd'] = 0;
        $data['returview']     = 0;
        $data['returadd'] = 0;
        $data['piutangview']   = 0;
        $data['piutangadd'] = 0;

        $view = [];
        foreach ($this->session->userdata('fiturview') as $key => $value) {
            $view[$key] = $value;
            if ($key == 23 && $value == 1) {
                $data['sliporderview'] = 1;
            }
            if ($key == 24 && $value == 1) {
                $data['quotationview'] = 1;
            }
            if ($key == 25 && $value == 1) {
                $data['transjualview'] = 1;
            }
            if ($key == 26 && $value == 1) {
                $data['returview'] = 1;
            }
            if ($key == 27 && $value == 1) {
                $data['piutangview'] = 1;
            }
        }

        $add = [];
        foreach ($this->session->userdata('fituradd') as $key => $value) {
            $view[$key] = $value;
            if ($key == 23 && $value == 1) {
                $data['sliporderadd'] = 1;
            }
            if ($key == 24 && $value == 1) {
                $data['quotationadd'] = 1;
            }
            if ($key == 25 && $value == 1) {
                $data['transjualadd'] = 1;
            }
            if ($key == 26 && $value == 1) {
                $data['returadd'] = 1;
            }
            if ($key == 27 && $value == 1) {
                $data['piutangadd'] = 1;
            }
        }

        loadview($data);
    }
}
