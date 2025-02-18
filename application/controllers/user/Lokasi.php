<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lokasi extends CI_Controller {

	function __construct() {
        parent::__construct();
        $this->load->model('M_Lokasi', 'lokasi');
	}

	function _remap($method, $params=array()){
        $method_exists = method_exists($this, $method);
        $methodToCall = $method_exists ? $method : 'index';
        $this->$methodToCall($method_exists ? $params : $method);
    }

    public function DataComboKab(){
        $KodeProv = $this->input->get('KodeProv');
        echo json_encode($this->lokasi->get_data_kab_list($KodeProv));
    }

    public function DataComboKec(){
        $KodeKab = $this->input->get('KodeKab');
        echo json_encode($this->lokasi->get_data_kec_list($KodeKab));
    }

    public function DataComboDesa(){
        $KodeKec = $this->input->get('KodeKec');
        echo json_encode($this->lokasi->get_data_desa_list($KodeKec));
    }

    public function DataBarang(){
        $KodeBarang = $this->input->get('KodeBarang');
        echo json_encode($this->lokasi->get_data_barang($KodeBarang));
    }

    public function DataTransaksi(){
        $NoTransKas = $this->input->get('NoTransKas');
        echo json_encode($this->lokasi->get_data_transaksi($NoTransKas));
    }

    public function DataSupplier(){
        $IDTransBeli = $this->input->get('IDTransBeli');
        echo json_encode($this->lokasi->get_data_supplier($IDTransBeli));
    }

    public function DataPO(){
        if ($this->input->get('searchTerm', TRUE)) {
            $data = $this->lokasi->get_data_po_like($this->input->get('searchTerm', TRUE));
        } else {
            $data = $this->lokasi->get_data_po();
        }

        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function DataPembelian(){
        if ($this->input->get('searchTerm', TRUE)) {
            $data = $this->lokasi->get_data_pembelian_like($this->input->get('searchTerm', TRUE));
        } else {
            $data = $this->lokasi->get_data_pembelian();
        }

        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function DataSO(){
        if ($this->input->get('searchTerm', TRUE)) {
            $data = $this->lokasi->get_data_so_like($this->input->get('searchTerm', TRUE));
        } else {
            $data = $this->lokasi->get_data_so();
        }

        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function DataTJL(){
        if ($this->input->get('searchTerm', TRUE)) {
            $data = $this->lokasi->get_data_tjl_like($this->input->get('searchTerm', TRUE));
        } else {
            $data = $this->lokasi->get_data_tjl();
        }

        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function DataCustomer(){
        $IDTransJual = $this->input->get('IDTransJual');
        echo json_encode($this->lokasi->get_data_customer($IDTransJual));
    }

    public function DataGudangAsal(){
        if ($this->input->get('searchTerm', TRUE)) {
            $data = $this->lokasi->get_gudang_asal_like($this->input->get('searchTerm', TRUE));
        } else {
            $data = $this->lokasi->get_gudang_asal();
        }

        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function DataGudangTujuan(){
        $GudangAsal = $this->input->get('GudangAsal');
        if ($this->input->get('searchTerm', TRUE)) {
            $data = $this->lokasi->get_gudang_tujuan_like($GudangAsal, $this->input->get('searchTerm', TRUE));
        } else {
            $data = $this->lokasi->get_gudang_tujuan($GudangAsal);
        }

        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function jumlahStokAsal(){
        $KodeGudang = $this->input->get('GudangAsal');
        $KodeBarang = $this->input->get('KodeBarang');
        echo json_encode($this->lokasi->get_stok_per_gudang($KodeGudang, $KodeBarang));
    }

    public function DataSPK(){
        $SPKNomor = $this->input->get('SPKNomor');
        echo json_encode($this->lokasi->get_data_spk($SPKNomor));
    }

    public function DataAktivitas(){
        $KodeAktivitas = $this->input->get('KodeAktivitas');
        echo json_encode($this->lokasi->get_aktivitas($KodeAktivitas));
    }

    public function DataBarangJadi(){
        if ($this->input->get('searchTerm', TRUE)) {
            $data = $this->lokasi->get_barang_jadi_like($this->input->get('searchTerm', TRUE));
        } else {
            $data = $this->lokasi->get_barang_jadi();
        }

        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function DataPegawai(){
        $KodePegawai = $this->input->get('KodePegawai');
        echo json_encode($this->lokasi->get_data_pegawai($KodePegawai));
    }

    public function DataKodeAkun(){
        $KodeAkun = $this->input->get('KodeAkun');
        echo json_encode($this->lokasi->get_data_akun($KodeAkun));
    }

    public function Saldo(){
        $bulan = $this->input->get('bulan');
        echo ($this->lokasi->get_saldo_bulan_lalu($bulan));
    }

    public function SaldoSekarang(){
        $bulan = $this->input->get('bulan');
        echo ($this->lokasi->get_saldo_bulan_ini($bulan));
    }
}