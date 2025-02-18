<?php
defined('BASEPATH') or exit('No direct script access allowed');

class daftar_akun extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('M_Akun', 'akun');
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        checkAccess($this->session->userdata('fiturview')[57]);
    }

    function _remap($method, $params = array())
    {
        $method_exists = method_exists($this, $method);
        $methodToCall = $method_exists ? $method : 'index';
        $this->$methodToCall($method_exists ? $params : $method);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[57]);
        $akun = $this->generateKelompokakun();
        foreach ($akun as $key => $value) {
            $parent = $this->akun->get_parent($value['NamaAkun']);
            foreach ($parent as $i => $val) {
                $anak = $this->akun->get_induk($val['KelompokAkun'], $val['KodeAkun']);
                foreach ($anak as $j => $val2) {
                    $jumlah_anak = $this->jumlah_anak($val2['KodeAkun']);
                    $saldo = $this->checkSaldo($val2['KodeAkun']);
                    $anak[$j]['jumlah_anak'] = $jumlah_anak;
                    $anak[$j]['saldo'] = $saldo;
                }
                $parent[$i]['anak'] = $anak;
                $jumlah_anak = $this->jumlah_anak($val['KodeAkun']);
                $saldo = $this->checkSaldo($val['KodeAkun']);
                $parent[$i]['jumlah_anak'] = $jumlah_anak;
                $parent[$i]['saldo'] = $saldo;
            }
            $akun[$key]['anak'] = $parent;
        }

        $data['kat'] = $this->generatekategori();
        $data['kelompok'] = $akun;

        $data['data'] = $akun;
        // die(json_encode($data['data']));

        $data['breadcrumb'][] = array('Name' => 'Akun', 'Url' => '#', 'IsAktif' => 0);
        $data['menu'] = 'daftarakun';
        $data['title'] = 'Daftar Akun Akuntansi';
        $data['view'] = 'akuntansi/v_daftar_akun';
        $data['scripts'] = 'akuntansi/s_daftar_akun';
        loadview($data);
    }

    public function checkSaldo($KodeAkun)
    {
        $sql = "SELECT SUM(i.Debet) - SUM(i.Kredit) as Saldo
            FROM transjurnalitem i
            WHERE i.KodeAkun = '$KodeAkun'";
        $result = $this->db->query($sql)->row_array()['Saldo'];

        return $result;
    }

    public function jumlah_anak($kodeakun)
    {
        $data = $this->crud->get_count([
            'select' => 'KodeAkun',
            'from' => 'mstakun',
            'where' => [['AkunInduk' => $kodeakun]]
        ]);

        return $data;
    }

    public function getIndukByKelompok()
    {
        $kel = $this->input->get('kelompok');
        $data = $this->akun->get_all_parent([
            'KelompokAkun' => $kel
        ]);

        if ($data) {
            echo json_encode([
                'status' => true,
                'msg'  => 'sukses',
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => 'error mendapat data'
            ]);
        }
    }

    public function getIndukByKelompok2()
    {
        $kel = $this->input->get('kelompok');
        $dtakun = $this->crud->get_rows([
            'select' => '*',
            'from' => 'mstakun a',
            'where' => [[
                'a.IsAktif' => 1,
                'a.KelompokAkun' => $kel
            ]],
        ]);

        $akun_laba_rugi = $this->crud->get_one_row([
            'select' => 'd.KodeAkun',
            'from' => 'detailsetakun d',
            'join' => [[
                'table' => ' setakunjurnal s',
                'on' => "s.KodeSetAkun = d.KodeSetAkun",
                'param' => 'INNER'
            ]],
            'where' => [[
                's.NamaTransaksi' => 'Neraca',
                's.JenisTransaksi' => 'Laba Rugi',
                'd.JenisJurnal' => 'Kredit',
            ]],
        ]);
        $kode_akun_labarugi = isset($akun_laba_rugi['KodeAkun']) ? $akun_laba_rugi['KodeAkun'] : 0;

        $listakun = [];
        foreach ($dtakun as $key) {
            $cek_saldo = $this->checkSaldo($key['KodeAkun']);

            $listakun[] = [
                'KodeAkun' => $key['KodeAkun'],
                'NamaAkun' => $key['NamaAkun'],
                'IsParent' => $key['IsParent'],
                'Saldo' => $cek_saldo,
                'IsLabaRugi' => ($key['KodeAkun'] == $kode_akun_labarugi) ? 'Ya' : 'Tidak'
            ];
        }
        $data = $listakun;

        if ($data) {
            echo json_encode([
                'status' => true,
                'msg'  => 'sukses',
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => 'error mendapat data'
            ]);
        }
    }

    public function simpan()
    {
        $kodeakun = $this->input->post('KodeAkun');
        $isEdit = $kodeakun != '';
        $data = $this->input->post();
        $parent = $this->input->post('IsParent');
        $isaktif = $this->input->post('IsAktif');
        $persediaan = $this->input->post('IsPersediaan');

        $data['IsParent']       = $parent != 'on' ? (int) 0 : (int) 1;
        $data['IsAktif']        = $isaktif != 'on' ? (int) 0 : (int) 1;
        $data['IsPersediaan']   = $persediaan != 'on' ? (int) 0 : (int) 1;

        $akuninduk = $this->input->post('AkunInduk');
        if (substr($akuninduk, 0, 1) == 1) {
            $kelompokakun = 'AKTIVA';
        } elseif (substr($akuninduk, 0, 1) == 2) {
            $kelompokakun = 'KEWAJIBAN';
        } elseif (substr($akuninduk, 0, 1) == 3) {
            $kelompokakun = 'EKUITAS';
        } elseif (substr($akuninduk, 0, 1) == 4) {
            $kelompokakun = 'PENDAPATAN';
        } elseif (substr($akuninduk, 0, 1) == 5) {
            $kelompokakun = 'BEBAN PRODUKSI';
        } elseif (substr($akuninduk, 0, 1) == 6) {
            $kelompokakun = 'BEBAN DILUAR PRODUKSI';
        } elseif (substr($akuninduk, 0, 1) == 7) {
            $kelompokakun = 'PEMBELIAN';
        }

        $cek_induk = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'mstakun',
            'where' => [['KodeAkun' => $akuninduk]],
        ]);
        if (isset($cek_induk['IsParent']) && $cek_induk['IsParent'] == 0) {
            $updateinduk = $this->crud->update(['IsParent' => 1], ['KodeAkun' => $akuninduk], 'mstakun');
        }

        if ($data['IsPersediaan'] == 1) {
            $updatepersediaan = $this->crud->update(['IsPersediaan' => (int)0], ['KodeAkun !=' => null], 'mstakun');
        }

        if ($isEdit) {
            // unset($data['IsParent']);
            $result = $this->crud->update($data, ['KodeAkun' => $kodeakun], 'mstakun');
        } else {
            $data['KodeAkun']       = $this->akun->get_kode($data['AkunInduk']);
            $data['KelompokAkun']   = $kelompokakun;
            $result = $this->crud->insert($data, 'mstakun');
        }
        if ($result) {
            ## TAMBAH LOG
            $keterangan = ($isEdit ? 'update data daftar akun : ' . $kodeakun : 'tambah data daftar akun : ' . $data['KodeAkun']);
            $aksi = ($isEdit ? 'edit' : 'tambah');
            $this->logsrv->insert_log([
                'Action' => $aksi,
                'JenisTransaksi' => 'Daftar Akun Akuntansi',
                'Description' => $keterangan,
            ]);
            $this->session->set_flashdata('berhasil', 'Berhasil ' . ($isEdit ? 'mengubah ' : 'menambah ') . 'data!');
        } else {
            $this->session->set_flashdata('gagal', 'Gagal ' . ($isEdit ? 'mengubah ' : 'menambah ') . 'data!');
        }

        redirect(base_url('akuntansi/daftar_akun'));
    }

    public function generatecode()
    {
        $kodeakun = $this->input->get('kodeakun');
        $data = $this->akun->get_kode($kodeakun);

        if ($data) {
            echo json_encode([
                'status' => true,
                'msg'  => 'sukses',
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => 'error mendapat data'
            ]);
        }
    }

    public function generateKelompokakun()
    {
        $data = [
            [
                'KodeAkun' => 1,
                'NamaAkun' => 'AKTIVA'
            ],
            [
                'KodeAkun' => 2,
                'NamaAkun' => 'KEWAJIBAN'
            ],
            [
                'KodeAkun' => 3,
                'NamaAkun' => 'EKUITAS'
            ],
            [
                'KodeAkun' => 4,
                'NamaAkun' => 'PENDAPATAN'
            ],
            [
                'KodeAkun' => 5,
                'NamaAkun' => 'BEBAN PRODUKSI'
            ],
            [
                'KodeAkun' => 6,
                'NamaAkun' => 'BEBAN DILUAR PRODUKSI'
            ],
            [
                'KodeAkun' => 7,
                'NamaAkun' => 'PEMBELIAN'
            ]
        ];

        return $data;
    }

    public function generatekategori()
    {
        $data = ["Arus Kas Operational", "Arus Kas Investasi", "Arus Kas Pembiayaan"];
        return $data;
    }

    public function hapus()
    {
        $kodeakun = $this->uri->segment(4);
        $cek = $this->crud->get_count(
            [
                'select' => 'KodeAkun',
                'from' => 'mstakun',
                'where' => [
                    ['AkunInduk' => $kodeakun]
                ]
            ]
        );
        if ($cek < 1) {
            $res = $this->crud->delete(['KodeAkun' => $kodeakun], 'mstakun');
            if ($res) {
                $keterangan = 'hapus data daftar akun ' . $kodeakun;
                $aksi = 'hapus';
                $this->logsrv->insert_log([
                    'Action' => $aksi,
                    'JenisTransaksi' => 'Daftar Akun Akuntansi',
                    'Description' => $keterangan,
                ]);
                $this->session->set_flashdata('berhasil', 'Berhasil menghapus data.');
            }
        } else {
            $this->session->set_flashdata('gagal', 'Gagal menghapus data, terdapat data lain yang terkait data tersebut.');
        }

        redirect(base_url('akuntansi/daftar_akun'));
    }
}
