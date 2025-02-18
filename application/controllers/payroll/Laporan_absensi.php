<?php
defined('BASEPATH') or exit('No direct script access allowed');

class laporan_absensi extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'mstkomponengaji';
        checkAccess($this->session->userdata('fiturview')[54]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[54]);
        $data['bulan']       = $bulan   = escape($this->input->get('bulan')) <> '' ? escape($this->input->get('bulan')) : Date("Y-m");
        $data['menu'] = 'lapabsensi';
        $data['title'] = 'Laporan Absensi Semua Pegawai';
        $data['view'] = 'payroll/v_laporan_absensi';
        $data['scripts'] = 'payroll/s_laporan_absensi';
        $sql = "SELECT p.NIP, p.NamaPegawai,
            MAX(CASE DAY(a.Tanggal) WHEN 1 THEN a.Keterangan ELSE '-' END) d1,
            MAX(CASE DAY(a.Tanggal) WHEN 2 THEN a.Keterangan ELSE '' END) d2,
            MAX(CASE DAY(a.Tanggal) WHEN 3 THEN a.Keterangan ELSE '' END) d3,
            MAX(CASE DAY(a.Tanggal) WHEN 4 THEN a.Keterangan ELSE '' END) d4,
            MAX(CASE DAY(a.Tanggal) WHEN 5 THEN a.Keterangan ELSE '' END) d5,
            MAX(CASE DAY(a.Tanggal) WHEN 6 THEN a.Keterangan ELSE '' END) d6,
            MAX(CASE DAY(a.Tanggal) WHEN 7 THEN a.Keterangan ELSE '' END) d7,
            MAX(CASE DAY(a.Tanggal) WHEN 8 THEN a.Keterangan ELSE '' END) d8,
            MAX(CASE DAY(a.Tanggal) WHEN 9 THEN a.Keterangan ELSE '' END) d9,
            MAX(CASE DAY(a.Tanggal) WHEN 10 THEN a.Keterangan ELSE '' END) d10,
            MAX(CASE DAY(a.Tanggal) WHEN 11 THEN a.Keterangan ELSE '' END) d11,
            MAX(CASE DAY(a.Tanggal) WHEN 12 THEN a.Keterangan ELSE '' END) d12,
            MAX(CASE DAY(a.Tanggal) WHEN 13 THEN a.Keterangan ELSE '' END) d13,
            MAX(CASE DAY(a.Tanggal) WHEN 14 THEN a.Keterangan ELSE '' END) d14,
            MAX(CASE DAY(a.Tanggal) WHEN 15 THEN a.Keterangan ELSE '' END) d15,
            MAX(CASE DAY(a.Tanggal) WHEN 16 THEN a.Keterangan ELSE '' END) d16,
            MAX(CASE DAY(a.Tanggal) WHEN 17 THEN a.Keterangan ELSE '' END) d17,
            MAX(CASE DAY(a.Tanggal) WHEN 18 THEN a.Keterangan ELSE '' END) d18,
            MAX(CASE DAY(a.Tanggal) WHEN 19 THEN a.Keterangan ELSE '' END) d19,
            MAX(CASE DAY(a.Tanggal) WHEN 20 THEN a.Keterangan ELSE '' END) d20,
            MAX(CASE DAY(a.Tanggal) WHEN 21 THEN a.Keterangan ELSE '' END) d21,
            MAX(CASE DAY(a.Tanggal) WHEN 22 THEN a.Keterangan ELSE '' END) d22,
            MAX(CASE DAY(a.Tanggal) WHEN 23 THEN a.Keterangan ELSE '' END) d23,
            MAX(CASE DAY(a.Tanggal) WHEN 24 THEN a.Keterangan ELSE '' END) d24,
            MAX(CASE DAY(a.Tanggal) WHEN 25 THEN a.Keterangan ELSE '' END) d25,
            MAX(CASE DAY(a.Tanggal) WHEN 26 THEN a.Keterangan ELSE '' END) d26,
            MAX(CASE DAY(a.Tanggal) WHEN 27 THEN a.Keterangan ELSE '' END) d27,
            MAX(CASE DAY(a.Tanggal) WHEN 28 THEN a.Keterangan ELSE '' END) d28,
            MAX(CASE DAY(a.Tanggal) WHEN 29 THEN a.Keterangan ELSE '' END) d29,
            MAX(CASE DAY(a.Tanggal) WHEN 30 THEN a.Keterangan ELSE '' END) d30,
            MAX(CASE DAY(a.Tanggal) WHEN 31 THEN a.Keterangan ELSE '' END) d31
            FROM absensipegawai a
            LEFT JOIN mstpegawai p ON p.KodePegawai = a.KodePegawai
            WHERE DATE_FORMAT(a.Tanggal,  '%Y-%m') = '$bulan'
            GROUP BY a.KodePegawai
            ORDER BY a.KodePegawai";
        $data['model'] = $this->db->query($sql)->result_array();
        loadview($data);
    }

    public function cetak()
    {
        setlocale(LC_ALL, 'IND');
        $bulan = $this->uri->segment(4);
        $data['bln'] = $bulan;
        $data['bulan'] = strftime('%B %Y', strtotime($bulan));
        $data['src_url'] = base_url('payroll/laporan_absensi?bulan=') . $this->uri->segment(4);

        $sql = "SELECT p.NIP, p.NamaPegawai,
            MAX(CASE DAY(a.Tanggal) WHEN 1 THEN a.Keterangan ELSE '-' END) d1,
            MAX(CASE DAY(a.Tanggal) WHEN 2 THEN a.Keterangan ELSE '' END) d2,
            MAX(CASE DAY(a.Tanggal) WHEN 3 THEN a.Keterangan ELSE '' END) d3,
            MAX(CASE DAY(a.Tanggal) WHEN 4 THEN a.Keterangan ELSE '' END) d4,
            MAX(CASE DAY(a.Tanggal) WHEN 5 THEN a.Keterangan ELSE '' END) d5,
            MAX(CASE DAY(a.Tanggal) WHEN 6 THEN a.Keterangan ELSE '' END) d6,
            MAX(CASE DAY(a.Tanggal) WHEN 7 THEN a.Keterangan ELSE '' END) d7,
            MAX(CASE DAY(a.Tanggal) WHEN 8 THEN a.Keterangan ELSE '' END) d8,
            MAX(CASE DAY(a.Tanggal) WHEN 9 THEN a.Keterangan ELSE '' END) d9,
            MAX(CASE DAY(a.Tanggal) WHEN 10 THEN a.Keterangan ELSE '' END) d10,
            MAX(CASE DAY(a.Tanggal) WHEN 11 THEN a.Keterangan ELSE '' END) d11,
            MAX(CASE DAY(a.Tanggal) WHEN 12 THEN a.Keterangan ELSE '' END) d12,
            MAX(CASE DAY(a.Tanggal) WHEN 13 THEN a.Keterangan ELSE '' END) d13,
            MAX(CASE DAY(a.Tanggal) WHEN 14 THEN a.Keterangan ELSE '' END) d14,
            MAX(CASE DAY(a.Tanggal) WHEN 15 THEN a.Keterangan ELSE '' END) d15,
            MAX(CASE DAY(a.Tanggal) WHEN 16 THEN a.Keterangan ELSE '' END) d16,
            MAX(CASE DAY(a.Tanggal) WHEN 17 THEN a.Keterangan ELSE '' END) d17,
            MAX(CASE DAY(a.Tanggal) WHEN 18 THEN a.Keterangan ELSE '' END) d18,
            MAX(CASE DAY(a.Tanggal) WHEN 19 THEN a.Keterangan ELSE '' END) d19,
            MAX(CASE DAY(a.Tanggal) WHEN 20 THEN a.Keterangan ELSE '' END) d20,
            MAX(CASE DAY(a.Tanggal) WHEN 21 THEN a.Keterangan ELSE '' END) d21,
            MAX(CASE DAY(a.Tanggal) WHEN 22 THEN a.Keterangan ELSE '' END) d22,
            MAX(CASE DAY(a.Tanggal) WHEN 23 THEN a.Keterangan ELSE '' END) d23,
            MAX(CASE DAY(a.Tanggal) WHEN 24 THEN a.Keterangan ELSE '' END) d24,
            MAX(CASE DAY(a.Tanggal) WHEN 25 THEN a.Keterangan ELSE '' END) d25,
            MAX(CASE DAY(a.Tanggal) WHEN 26 THEN a.Keterangan ELSE '' END) d26,
            MAX(CASE DAY(a.Tanggal) WHEN 27 THEN a.Keterangan ELSE '' END) d27,
            MAX(CASE DAY(a.Tanggal) WHEN 28 THEN a.Keterangan ELSE '' END) d28,
            MAX(CASE DAY(a.Tanggal) WHEN 29 THEN a.Keterangan ELSE '' END) d29,
            MAX(CASE DAY(a.Tanggal) WHEN 30 THEN a.Keterangan ELSE '' END) d30,
            MAX(CASE DAY(a.Tanggal) WHEN 31 THEN a.Keterangan ELSE '' END) d31
            FROM absensipegawai a
            LEFT JOIN mstpegawai p ON p.KodePegawai = a.KodePegawai
            WHERE DATE_FORMAT(a.Tanggal,  '%Y-%m') = '$bulan'
            GROUP BY a.KodePegawai
            ORDER BY a.KodePegawai";
        $data['model'] = $this->db->query($sql)->result_array();

        $this->load->library('Pdf');
        $this->load->view('payroll/cetak_laporan_absensi_pegawai', $data);
    }
}
