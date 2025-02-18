<?php
defined('BASEPATH') or exit('No direct script access allowed');

class trans_beli extends CI_Controller
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
        $data['menu'] = 'trans_beli';
        $data['title'] = 'Transaksi Pembelian';
        $data['view'] = 'transaksi/v_trans_beli';
        $data['scripts'] = 'transaksi/s_trans_beli';
        
        $supplier = [
            'select' => '*',
            'from' => 'mstperson',
            'where' => [
                [
                    'IsAktif' => 1,
                    'JenisPerson' => 'SUPPLIER'
                ]
            ],
            'order_by' => 'KodePerson'
        ];
        $data['supplier'] = $this->crud->get_rows($supplier);

        $po = [
            'select' => 'b.IDTransBeli, b.NoPO, b.KodePerson, p.NamaPersonCP',
            'from' => 'transpembelian b',
            'join' => [
                [
                    'table' => ' mstperson p',
                    'on' => "p.KodePerson = b.KodePerson",
                    'param' => 'LEFT',
                ],
            ],
            'where' => [['StatusProses' => 'APPROVED']],
        ];
        $data['po'] = $this->crud->get_rows($po);

        $supplier_hutang = [
            'select' => '*',
            'from' => 'mstperson p',
            'where' => [
                [
                    // 'p.IsAktif' => 1,
                    'p.JenisPerson' => 'SUPPLIER',
                    'b.StatusBayar !=' => 'LUNAS',
                    'b.StatusProses' => 'DONE',
                ]
            ],
            'join' => [
                [
                    'table' => ' transpembelian b',
                    'on' => "b.KodePerson = p.KodePerson",
                    'param' => 'LEFT',
                ],
            ],
            'group_by' => 'p.KodePerson',
            'order_by' => 'p.KodePerson'
        ];
        $data['supplier_hutang'] = $this->crud->get_rows($supplier_hutang);

        $idtrans_inretur = $this->crud->get_rows([
            'select' => 'IDTrans',
            'from' => 'transaksiretur',
            'where' => [[
                'JenisRetur'         => 'RETUR_BELI',
                'LEFT(IDTrans, 3) =' => 'TBL',
                'IsVoid'             => 0,
            ]]
        ]);

        $idtrans_selected = ['0'];
        foreach ($idtrans_inretur as $key) {
            $idtrans_selected[] = $key['IDTrans'];
        }

        $data['idtrans'] = $this->db->select('IDTransBeli')
            ->from('transpembelian')
            ->where([
                'StatusProses' => 'DONE',
                'StatusKirim !=' => 'BELUM',
            ])
            ->where_not_in('IDTransBeli', $idtrans_selected)
            ->get()
            ->result_array();

        $data['poview'] = 0;
        $data['poadd'] = 0;
        $data['approvalview'] = 0;
        $data['approvaladd'] = 0;
        $data['transbeliview'] = 0;
        $data['transbeliadd'] = 0;
        $data['hutangview'] = 0;
        $data['hutangadd'] = 0;
        $data['returview'] = 0;
        $data['returadd'] = 0;

        $view = [];
        foreach ($this->session->userdata('fiturview') as $key => $value) {
            $view[$key] = $value;
            if ($key == 11 && $value == 1) {
                $data['poview'] = 1;
            }
            if ($key == 12 && $value == 1) {
                $data['approvalview'] = 1;
            }
            if ($key == 13 && $value == 1) {
                $data['transbeliview'] = 1;
            }
            if ($key == 14 && $value == 1) {
                $data['hutangview'] = 1;
            }
            if ($key == 68 && $value == 1) {
                $data['returview'] = 1;
            }
        }

        $add = [];
        foreach ($this->session->userdata('fituradd') as $key => $value) {
            $add[$key] = $value;
            if ($key == 11 && $value == 1) {
                $data['poadd'] = 1;
            }
            if ($key == 12 && $value == 1) {
                $data['approvaladd'] = 1;
            }
            if ($key == 13 && $value == 1) {
                $data['transbeliadd'] = 1;
            }
            if ($key == 14 && $value == 1) {
                $data['hutangadd'] = 1;
            }
            if ($key == 68 && $value == 1) {
                $data['returadd'] = 1;
            }
        }

        loadview($data);
    }
}
