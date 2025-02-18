<?php
defined('BASEPATH') or exit('No direct script access allowed');

class sistemsetting extends CI_Controller
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
        $this->crud->table = 'sistemsetting';
        checkAccess($this->session->userdata('fiturview')[31]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[31]);
        $data['menu'] = 'setting';
        $data['title'] = 'Sistem Setting';
        $data['view'] = 'user/v_sistemsetting';
        $data['scripts'] = 'user/s_sistemsetting';

        $comp = $this->crud->get_rows([
            'select' => '*',
            'from' => 'sistemsetting',
        ]);
        $models = [];
        foreach ($comp as $key) {
            if ($key['KodeSetting'] == 1) {
                $models['NamaPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 2) {
                $models['EmailPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 3) {
                $models['NoTelpPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 4) {
                $models['AlamatPerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 5) {
                $models['WebsitePerusahaan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 6) {
                $models['NamaPimpinan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 7) {
                $models['NamaBank'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 8) {
                $models['CabangBank'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 9) {
                $models['NoAkunBank'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 10) {
                $models['AtasNamaBank'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 11) {
                $models['Pesan'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 12) {
                $models['Balance'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 13) {
                $models['FlipApi'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 14) {
                $models['SettingJurnal'] = $key['ValueSetting'];
            }
            if ($key['KodeSetting'] == 15) {
                $models['LimitPinjamanKaryawan'] = $key['ValueSetting'];
            }
        }

        $data['model'] = $models;
        loadview($data);
    }

    public function simpan()
    {
        $res = $this->crud->update(['ValueSetting' => $this->input->post('NamaPerusahaan')], ['KodeSetting' => 1], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('EmailPerusahaan')], ['KodeSetting' => 2], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('NoTelpPerusahaan')], ['KodeSetting' => 3], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('AlamatPerusahaan')], ['KodeSetting' => 4], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('WebsitePerusahaan')], ['KodeSetting' => 5], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('NamaPimpinan')], ['KodeSetting' => 6], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('NamaBank')], ['KodeSetting' => 7], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('CabangBank')], ['KodeSetting' => 8], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('NoAkunBank')], ['KodeSetting' => 9], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('AtasNamaBank')], ['KodeSetting' => 10], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('Pesan')], ['KodeSetting' => 11], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('Balance')], ['KodeSetting' => 12], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('FlipApi')], ['KodeSetting' => 13], 'sistemsetting');
        $res = $this->crud->update(['ValueSetting' => $this->input->post('SettingJurnal')], ['KodeSetting' => 14], 'sistemsetting');

        $limitPinjaman = str_replace(['.', ','], ['', '.'], $this->input->post('LimitPinjamanKaryawan'));
        $res = $this->crud->update(['ValueSetting' => $limitPinjaman], ['KodeSetting' => 15], 'sistemsetting');

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => "edit",
                'JenisTransaksi' => "Sistem Setting",
                'Description' => "update data sistem setting"
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

    public function checkPsw()
    {
        $psw = $this->input->get('psw');
        $main_psw = base64_decode($this->session->userdata('UserPsw'));

        if ($psw != $main_psw) {
            setresponse(HTTP_OK, ['status' => false, 'msg' => 'Password anda salah!']);
        } else {
            setresponse(HTTP_ACCEPTED, ['status' => true, 'msg' => 'Password ok']);
        }
    }

    public function forcereset()
    {
        $confirm = $this->input->post('confirm');
        $res = null;
        if ($confirm == 1) {
            $stringoftables='absensipegawai, aktivitasproduksi, chat, draftbahanproduksi, iteminsentifbulanan, itemretur, itempembelian, itempenjualan, itemtransaksibarang, neracalabarugi, neracasaldo, serverlog, transaksibarang, transjurnalitem, transjurnal, transaksikas, rekapinsentifbulanan, transaksiretur, transpembelian, transpenjualan, mstgudang, mstperson, mstbarang, mstkategori, mstjenisbarang, msttahunanggaran, trpinjamankaryawan, flip';
            $res = $this->emptytablesbycomma($stringoftables);

            // update di tabel master barang
            // $updatetbl_barang = $this->crud->update(
            //     [
            //         'HargaBeliTerakhir' => 0,
            //         'NilaiHPP'          => 0
            //     ],
            //     [],
            //     'mstbarang'
            // );
        }

        if ($res) {
            ## INSERT TO SERVER LOG
            $this->logsrv->insert_log([
                'Action' => "hapus",
                'JenisTransaksi' => "Force Reset Data",
                'Description' => "Clear All Data"
            ]);
            echo json_encode([
                'status' => true,
                'msg'  => "Berhasil Melakukan Force Reset Data"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal Mengubah Force Reset Data"
            ]);
        }
    }

    function emptytablesbycomma($stringoftables) {
        $result = [];
        $array_tablenames = explode(", ", $stringoftables);
        if (!empty($array_tablenames)) {
            foreach ($array_tablenames as $tablename) {
                $fields = $this->db->list_fields($tablename);
                $result[] = $this->crud->delete([$fields[0].'!=' => null], $tablename);
            }
        }
        return $result;
    }
}
