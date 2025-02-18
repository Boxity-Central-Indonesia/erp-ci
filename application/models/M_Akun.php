<?php

use phpDocumentor\Reflection\Types\This;

defined('BASEPATH') or exit('No direct script access allowed');

class M_Akun extends CI_Model
{

    public function get_parent($kelompokAkun)
    {
        $this->db->select('*')
            ->from('mstakun')
            ->where([
                'KelompokAkun' => $kelompokAkun
            ])->where_in('AkunInduk', ["1", "2", "3", "4", "5", "6", "7"]);

        return $this->db->get()->result_array();
    }

    public function get_induk($kelompokAkun, $kodeinduk)
    {
        $data =  $this->db->select('*')
            ->from('mstakun')
            ->where(
                [
                    'KelompokAkun' => $kelompokAkun,
                    'AkunInduk' => $kodeinduk
                ]
            );
        $res = $data->get()->result_array();

        foreach ($res as $key => $value) {

            $res[$key]['anak'] =  $this->get_induk($value['KelompokAkun'], $value['KodeAkun']);
        }
        return $res;
    }

    // public function get_sub_akun($kelompokAkun, $kodeinduk, $kodebumdes)
    // {
    //     $data =  $this->db->select('mstakun.KodeAkun, mstakun.NamaAkun, mstakun.KelompokAkun, mstakun.AkunInduk, mstakun.IsParent, mstakun.IsAktif, saldoawal.Debet, saldoawal.Kredit, (saldoawal.Debet+saldoawal.Kredit) as Nominal')
    //         ->from('mstakun')
    //         ->join('saldoawal', 'mstakun.KodeAkun = saldoawal.KodeAkun', 'left')
    //         ->where([
    //             'KelompokAkun' => $kelompokAkun,
    //             'AkunInduk' => $kodeinduk,
    //             'saldoawal.KodeBumdes' => $kodebumdes
    //         ]);
    //     $res = $data->get()->result_array();
    //     return $res;
    // }

    public function get_sub_akun($kelompokAkun, $kodeinduk, $kodebumdes)
    {
        $sql = "
        SELECT a.KodeAkun, a.NamaAkun, a.KelompokAkun, a.AkunInduk, a.IsParent, a.IsAktif, s.Debet, s.Kredit, s.Nominal
        FROM mstakun AS a
        LEFT OUTER JOIN (
            SELECT KodeAkun, SaldoDebet AS Debet, SaldoKredit AS Kredit, (SaldoDebet+SaldoKredit) as Nominal
            FROM neracasaldo
            WHERE KodeBumdes='$kodebumdes'
        ) s ON s.KodeAkun = a.KodeAkun
        WHERE a.KelompokAkun = '$kelompokAkun' AND a.AkunInduk = '$kodeinduk'";

        $query = $this->db->query($sql);
        if ($query) {
            $num = $query->num_rows();
            if ($num != 0) {
                $res = $query->result_array();
                // $res = $data->get()->result_array();
                return $res;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function get_sub_akun_budget($kelompokAkun, $kodeinduk)
    {
        $data =  $this->db->select('mstakun.*, budgeting.NominalBudget')
            ->from('mstakun')
            ->join('budgeting', 'mstakun.KodeAkun = budgeting.KodeAkun', 'left')
            ->where([
                'KelompokAkun' => $kelompokAkun,
                'AkunInduk' => $kodeinduk,
            ]);
        $res = $data->get()->result_array();
        return $res;
    }

    // public function get_parent_akun($kelompokAkun, $kodetahun, $kodebumdes)
    // {
    //     $this->db->select('a.*')
    //         ->from('mstakun a')
    //         ->join('neracasaldo b', 'a.KodeAkun = b.KodeAkun', 'left')
    //         ->where([
    //             'a.KelompokAkun' => $kelompokAkun,
    //             'b.KodeTahun' => $kodetahun,
    //             'b.KodeBumdes' => $kodebumdes
    //         ])->where_in('AkunInduk', ["1", "2", "3", "4", "5", "6", "7"]);

    //     return $this->db->get()->result_array();
    // }
    public function get_parent_akun($kelompokAkun, $kodetahun, $kodebumdes)
    {
        // $this->db->select('a.*, b.NoUrut')
        //     ->from('mstakun a')
        //     ->join('neracasaldo b', 'a.KodeAkun = b.KodeAkun', 'left')
        //     ->where([
        //         'KelompokAkun' => $kelompokAkun, 'b.KodeTahunAnggaran' => $kodetahunanggaran
        //     ])->where_in('AkunInduk', ["1", "2", "3", "4", "5", "6", "7"])->order_by('a.KodeAkun');
        $this->db->select("a.*, (SELECT b.NoUrut FROM neracasaldo b WHERE b.KodeAkun = a.KodeAkun AND b.KodeTahun = '$kodetahun' AND KodeBumdes = '$kodebumdes') AS NoUrut")
            ->from('mstakun a')
            ->where([
                'KelompokAkun' => $kelompokAkun
            ])->where_in('AkunInduk', ["1", "2", "3", "4", "5", "6", "7"])->order_by('a.KodeAkun');

        $rest  = $this->db->get()->result_array();
        return $rest;
    }

    public function get_parent_akun_budget($kelompokAkun, $kodetahun, $kodebumdes)
    {
        $this->db->select('a.*, b.BulanTahun, b.NominalBudget')
            ->from('mstakun a')
            ->join('budgeting b', 'a.KodeAkun = b.KodeAkun', 'left')
            ->where([
                'KelompokAkun' => $kelompokAkun
            ])->where_in('AkunInduk', ["1", "2", "3", "4", "5", "6", "7"]);

        return $this->db->get()->result_array();
    }

    public function get_induk_saldo($wheredata)
    {
        $data =  $this->db->select('IFNULL(SaldoDebet, 0) as SaldoAkhir')
            ->from('neracasaldo')
            ->where($wheredata);
        return $data->get()->row();
    }

    public function get_induk_budget($wheredata)
    {
        $data =  $this->db->select('IFNULL(NominalBudget, 0) as NominalBudget')
            ->from('budgeting')
            ->where($wheredata);
        return $data->get()->row();
    }

    public function get_total_pendapatan($tahun)
    {
        $data =  $this->db->select('SUM(NominalBudget) as totalpendapatan')
            ->from('mstakun')
            ->join('budgeting', 'mstakun.KodeAkun = budgeting.KodeAkun', 'left')
            ->where([
                'AkunInduk' => 4,
                'BulanTahun' => $tahun
            ]);
        $res = $data->get()->result_array();
        return $res;
    }

    public function get_total_biaya($tahun)
    {
        $data =  $this->db->select('SUM(NominalBudget) as totalbiaya')
            ->from('mstakun')
            ->join('budgeting', 'mstakun.KodeAkun = budgeting.KodeAkun', 'left')
            ->where([
                'AkunInduk' => 5,
                'BulanTahun' => $tahun
            ]);
        $res = $data->get()->result_array();
        return $res;
    }

    public function get_total_aktiva($kodetahun, $kodebumdes)
    {
        $data =  $this->db->select('IFNULL(SUM(SaldoDebet), 0) as totalaktiva')
            ->from('mstakun')
            ->join('neracasaldo', 'mstakun.KodeAkun = neracasaldo.KodeAkun', 'left')
            ->where([
                'KelompokAkun' => 'AKTIVA',
                'neracasaldo.KodeBumdes' => $kodebumdes,
                'neracasaldo.KodeTahun' => $kodetahun
            ]);
        $res = @$data->get()->row()->totalaktiva??0;
        // var_dump($this->db->last_query());
        // die();

        return $res;
    }

    public function get_total_wajib($kodetahun, $kodebumdes)
    {
        $data =  $this->db->select('IFNULL(SUM(SaldoDebet), 0) as totalwajib')
            ->from('mstakun')
            ->join('neracasaldo', 'mstakun.KodeAkun = neracasaldo.KodeAkun', 'left')
            ->where([
                'KelompokAkun' => 'KEWAJIBAN',
                'neracasaldo.KodeBumdes' => $kodebumdes,
                'neracasaldo.KodeTahun' => $kodetahun
            ]);
        $res = @$data->get()->row()->totalwajib??0;
        return $res;
    }

    public function get_total_ekuitas($kodetahun, $kodebumdes)
    {
        $data =  $this->db->select('IFNULL(SUM(SaldoDebet), 0) as totalekuitas')
            ->from('mstakun')
            ->join('neracasaldo', 'mstakun.KodeAkun = neracasaldo.KodeAkun', 'left')
            ->where([
                'KelompokAkun' => 'EKUITAS',
                'neracasaldo.KodeBumdes' => $kodebumdes,
                'neracasaldo.KodeTahun' => $kodetahun
            ]);
        $res = @$data->get()->row()->totalekuitas??0;
        
        return $res;
    }

    public function get_all_parent($where)
    {
        $data =  $this->db->select('*')
            ->from('mstakun')
            ->where([
                'IsParent' => 1
            ])->where($where);
        return $data->get()->result_array();
    }

    public function get_kode($akuninduk)
    {
        $data = $this->db->select('KodeAkun')
            ->from('mstakun')
            ->where([
                'AkunInduk' => $akuninduk
            ])
            ->limit(1)
            ->order_by('KodeAkun desc');
        $data = $this->db->get()->row();
        if ($data) {
            $res = (int)substr($data->KodeAkun, -2) + 1;
            if ($res < 10) {
                return $akuninduk . '.0' . $res;
            }
            return $akuninduk . '.' . $res;
        } else {
            return $akuninduk . '.01';
        }
    }

    public function get_tahun()
    {
        $data =  $this->db->select('KodeTahun')
            ->from('tahunanggaran');
        return $data->get()->result_array();
    }

    public function get_anak()
    {
        $data =  $this->db->select('*')
            ->from('mstakun')
            ->where([
                'IsParent' => 0
            ]);
        return $data->get()->result_array();
    }

    public function get_nominal($kodeakun)
    {
        $data =  $this->db->select('* , (SaldoDebet+SaldoKredit) as Nominal')
            ->from('neracasaldo')
            ->where([
                'KodeAkun' => $kodeakun
            ]);
        return $data->get()->result_array();
    }
    public function unFormat($num)
    {
        $num = str_replace(',', '', $num);
        return $num;
    }
}
