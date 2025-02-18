<?php
defined('BASEPATH') or exit('No direct script access allowed');

class flip extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $Akses = $this->akses->CekWaktu();
        if (!$Akses) {
            redirect(base_url());
        }
        $this->crud->table = 'flip';
        $this->load->model('M_Lokasi', 'lokasi');
        checkAccess($this->session->userdata('fiturview')[66]);
    }

    public function index()
    {
        checkAccess($this->session->userdata('fiturview')[66]);
        if (!$this->input->is_ajax_request()) {
            $data['breadcrumb'][] = array('Name' => '', 'Url' => '#', 'IsAktif' => 0);
            $data['menu'] = 'flip';
            $data['title'] = 'Riwayat Top Up Flip';
            $data['view'] = 'user/v_flip';
            $data['scripts'] = 'user/s_flip';

            // get data bank from API
            $data['dtbank'] = $this->bank_code_payment();

            $companyName = $this->lokasi->get_sistem_setting((1));
            $rmvComp = str_replace(['PT. ', 'CV. '], ['', ''], $companyName);
            $spltComp = explode(" ", $rmvComp);
            $s1 = isset($spltComp[0]) ? substr($spltComp[0], 0, 1) : '';
            $s2 = isset($spltComp[1]) ? substr($spltComp[1], 0, 1) : '';
            $s3 = isset($spltComp[2]) ? substr($spltComp[2], 0, 1) : '';
            $s4 = isset($spltComp[3]) ? substr($spltComp[3], 0, 1) : '';
            $s5 = isset($spltComp[4]) ? substr($spltComp[4], 0, 1) : '';
            $data['companyCode'] = $s1 . $s2 . $s3 . $s4 . $s5;
            $data['curYear'] = date('Y');

            loadview($data);
        } else {
            $this->load->model('M_Datatables');
            $configData = $this->input->get();
            ## table
            $configData['table'] = 'flip f';
            // $configData['where'] = [
            //     ['u.IsAktif !=' => null]
            // ];
            $cari     = $this->input->get('cari');
            $status   = $this->input->get('isaktif');
            if ($cari != '') {
                $configData['filters'][] = " (f.Title LIKE '%$cari%' OR f.SenderName LIKE '%$cari%' OR f.BankName LIKE '%$cari%' OR f.Amount LIKE '%$cari%' OR f.Status LIKE '%$cari%')";
            }

            if ($status != '') {
                $configData['filters'][] = " f.Status = $status ";
            }

            $configData['join'] = [
                [
                    'table' => ' userlogin u',
                    'on' => "u.UserName = f.UserName",
                    'param' => 'LEFT',
                ],
            ];

            ## select -> fill with all column you need
            $configData['selected_column'] = [
                'f.FlipID', 'f.LinkID', 'f.LinkUrl', 'f.Title', 'f.Amount', 'f.ExpDate', 'f.SenderName', 'f.SenderEmail', 'f.SenderPhoneNumber', 'f.SenderAddress', 'f.SenderBank', 'f.BankName', 'f.SenderBankType', 'f.NoRekening', 'f.Status', 'f.PaymentUrl', 'f.UserName', 'u.ActualName'
            ];
            ## display column -> Represent column in view
            // index must same with table column
            // set false if column not in db table (column number, action, etc)
            $configData['use_custom_order'] = true;
            $configData['custom_column_name_order'] = 'FlipID';
            $configData['custom_column_sort_order'] = 'DESC';
            $num_start_row = $configData['start'];
            $configData['display_column'] = [
                false,
                'f.FlipID', 'f.LinkID', 'f.LinkUrl', 'f.Title', 'f.Amount', 'f.ExpDate', 'f.SenderName', 'f.SenderEmail', 'f.SenderPhoneNumber', 'f.SenderAddress', 'f.SenderBank', 'f.BankName', 'f.SenderBankType', 'f.NoRekening', 'f.Status', 'f.PaymentUrl', 'f.UserName', 'u.ActualName',
                false
            ];
            ## get data
            $data = $this->M_Datatables->get_data_assoc($configData);
            $records = $data['records'];
            $data['data'] = [];

            foreach ($records as $record) {
                $temp = [];
                $temp = (array)$record;
                $temp['no'] = ++$num_start_row;
                $temp['SenderBankType'] = ($temp['SenderBankType'] == 'virtual_account') ? 'Virtual Account' : 'E-Wallet';

                if (strtolower($temp['Status']) == 'not_confirmed') {
                    $temp['PaymentStatus'] = 'Belum Terkonfirmasi';
                } elseif (strtolower($temp['Status']) == 'pending') {
                    $rn = date('Y-m-d H:i:'.'00');
                    $temp['PaymentStatus'] = ($temp['ExpDate'] > $rn) ? 'Pending' : 'Kadaluarsa';
                } elseif (strtolower($temp['Status']) == 'processed') {
                    $temp['PaymentStatus'] = 'Proses';
                } elseif (strtolower($temp['Status']) == 'cancelled' || strtolower($temp['Status']) == 'failed') {
                    $temp['PaymentStatus'] = 'Gagal';
                } elseif (strtolower($temp['Status']) == 'done' || strtolower($temp['Status']) == 'successful') {
                    $temp['PaymentStatus'] = 'Berhasil';
                } else {
                    $temp['PaymentStatus'] = '-';
                }

                $temp['ExpDate'] = isset($temp['ExpDate']) ? shortdate_indo(date('Y-m-d', strtotime($temp['ExpDate']))) . ' ' . date('H:i', strtotime($temp['ExpDate'])) : '';
                $temp['btn_aksi'] = '<a class="btndetail" href="' . base_url('user/flip/detail_pembayaran/' . base64_encode($temp['FlipID'])) . '" type="button" data-model=\'' . json_encode($record) . '\' title="Detail Pembayaran"><i class="fa fa-search-plus"></i></a>';
                $data['data'][] = $temp;
            }
            unset($data['records']);
            echo json_encode($data);
        }
    }

    public function bank_code_payment()
    {
        $data = [
            [
                'bank_code' => 'bni',
                'name' => 'BNI',
                'logo' => 'assets/logo_bank/bni.png',
                'transfer_step' => [
                    'internet_banking' => [
                        '1' => "Login ke internet banking Bank BNI Anda",
                        '2' => "Pilih menu <b>Transaksi</b> lalu klik menu <b>Virtual Account Billing</b>",
                        '3' => "Masukkan Nomor VA <b>#va</b> lalu pilih <b>Rekening Debit</b>",
                        '4' => "Detail transaksi akan ditampilkan, pastikan data sudah sesuai",
                        '5' => "Masukkan respon key BNI appli 2",
                        '6' => "Transaksi sukses, simpan bukti transaksi Anda"
                    ],
                    'atm' => [
                        '1' => "Masukkan kartu Anda",
                        '2' => "Pilih Bahasa",
                        '3' => "Masukkan PIN ATM Anda",
                        '4' => "Kemudian, pilih <b>Menu Lainnya</b>",
                        '5' => "Pilih <b>Transfer</b> dan pilih jenis rekening yang akan digunakan (Contoh: Dari rekening Tabungan)",
                        '6' => "Pilih <b>Virtual Account Billing</b>. Masukkan Nomor VA <b>#va</b>",
                        '7' => "Tagihan yang harus dibayarkan akan muncul pada layar konfirmasi",
                        '8' => "Konfirmasi, apabila telah selesai, lanjutkan transaksi",
                        '9' => "Transaksi Anda telah selesai"
                    ]
                ]
            ],
            [
                'bank_code' => 'bri',
                'name' => 'BRI',
                'logo' => 'assets/logo_bank/bri.png',
                'transfer_step' => [
                    'internet_banking' => [
                        '1' => "Login ke internet banking Bank BRI Anda",
                        '2' => "Pilih menu <b>Pembayaran</b> lalu klik menu <b>BRIVA</b>",
                        '3' => "Pilih rekening sumber dan masukkan Kode Bayar <b>#va</b> lalu klik <b>Kirim</b>",
                        '4' => "Detail transaksi akan ditampilkan, pastikan data sudah sesuai",
                        '5' => "Klik <b>Lanjutkan</b>",
                        '6' => "Masukkan kata sandi ibanking lalu klik <b>Request</b> untuk mengirim m-PIN ke nomor HP Anda",
                        '7' => "Periksa HP Anda dan masukkan m-PIN yang diterima lalu klik <b>Kirim</b>",
                        '8' => "Transaksi sukses, simpan bukti transaksi Anda"
                    ],
                    'mobile_banking' => [
                        '1' => "Login ke aplikasi BRImo Anda",
                        '2' => "Pilih menu <b>BRIVA</b>",
                        '3' => "Pilih sumber dana dan masukkan Nomor Pembayaran <b>#va</b> lalu klik <b>Lanjut</b>",
                        '4' => "Klik <b>Lanjut</b>",
                        '5' => "Detail transaksi akan ditampilkan, pastikan data sudah sesuai",
                        '6' => "Klik <b>Lanjutkan</b>",
                        '7' => "Klik <b>Konfirmasi</b>",
                        '8' => "Klik <b>Lanjut</b>",
                        '9' => "Masukkan kata sandi ibanking Anda",
                        '10' => "Klik <b>Lanjut</b>",
                        '11' => "Transaksi sukses, simpan bukti transaksi Anda"
                    ],
                    'atm' => [
                        '1' => "Lakukan pembayaran melalui ATM Bank BRI",
                        '2' => "Pilih menu <b>Transaksi Lain > Pembayaran > Lainnya > Pilih BRIVA</b>",
                        '3' => "Masukkan Nomor VA <b>#va</b>",
                        '4' => "Pilih <b>Ya</b> untuk memproses pembayaran"
                    ]
                ]
            ],
            [
                'bank_code' => 'bca',
                'name' => 'BCA',
                'logo' => 'assets/logo_bank/bca.png',
                'transfer_step' => [
                    'internet_banking' => [
                        '1' => "Login ke internet banking klikbca Anda",
                        '2' => "Pilih menu <b>Transfer Dana</b> lalu pilih <b>Transfer ke BCA Virtual Account</b>",
                        '3' => "Pilih <b>Dari Rekening</b>",
                        '4' => "Masukkan Nomor VA <b>#va</b> lalu klik <b>Lanjutkan</b>",
                        '5' => "Detail transaksi akan ditampilkan, pastikan data sudah sesuai lalu masukkan respon <b>Key BCA Appli 1</b>",
                        '6' => "Klik <b>Kirim</b>",
                        '7' => "Transaksi sukses, simpan bukti transaksi Anda"
                    ],
                    'mobile_banking' => [
                        '1' => "Login pada aplikasi BCA mobile",
                        '2' => "Pilih <b>m-BCA</b> masukkan kode akses m-BCA",
                        '3' => "Pilih <b>m-Transfer</b>",
                        '4' => "Pilih <b>BCA Virtual Account</b>",
                        '5' => "Masukkan Nomor VA <b>#va</b> lalu klik <b>OK</b>",
                        '6' => "Konfirmasi no virtual account dan rekening pendebetan",
                        '7' => "Periksa kembalian rincian pembayaran anda, lalu klik <b>Ya</b>",
                        '8' => "Masukkan pin m-BCA",
                        '9' => "Pembayaran Selesai"
                    ],
                    'atm' => [
                        '1' => "Masukkan kartu ATM BCA & PIN",
                        '2' => "Pilih menu <b>Transaksi Lainnya > Transfer > Ke Rekening BCA Virtual Account</b>",
                        '3' => "Masukkan Nomor VA <b>#va</b>",
                        '4' => "Di halaman konfirmasi, pastikan detail pembayaran sudah sesuai seperti Nomor VA, Nama, Produk dan Total Tagihan",
                        '5' => "Jika sudah benar, klik <b>Ya</b>",
                        '6' => "Simpan struk transaksi sebagai bukti pembayaran"
                    ]
                ]
            ],
            [
                'bank_code' => 'mandiri',
                'name' => 'Mandiri',
                'logo' => 'assets/logo_bank/mandiri.png',
                'transfer_step' => [
                    'internet_banking' => [
                        '1' => "Login ke internet banking Anda",
                        '2' => "Pilih menu <b>Bayar</b> lalu klik menu <b>E-Commerce</b>",
                        '3' => "Masukkan Kode <b>I-Pay (70017)</b>",
                        '4' => "Masukkan Nomor VA <b>#va</b>",
                        '5' => "Masukkan Nominal (<b>4004250</b>)",
                        '6' => "Detail transaksi akan ditampilkan, pastikan data sudah sesuai",
                        '7' => "Klik tombol <b>Konfirmasi</b>",
                        '8' => "Periksa aplikasi Mandiri Online di ponsel Anda untuk menyelesaikan persetujuan transaksi",
                        '9' => "Transaksi sukses, simpan bukti transaksi Anda"
                    ],
                    'atm' => [
                        '1' => "Masukkan kartu ATM & isi PIN ATM Anda",
                        '2' => "Pilih menu <b>Bayar/Beli</b> lalu pilih menu <b>Lainnya</b>",
                        '3' => "Pilih lagi menu <b>Lainnya</b>",
                        '4' => "Pilih menu <b>Multi Payment</b>",
                        '5' => "Masukkan kode <b>I-Pay (70017)</b> lalu tekan <b>Benar</b>",
                        '6' => "Masukkan Nomor VA <b>#va</b>",
                        '7' => "Detail transaksi akan ditampilkan, pastikan data sudah sesuai",
                        '8' => "Tekan <b>1</b> lalu tekan <b>YA</b>",
                        '9' => "Transaksi sukses, simpan bukti transaksi Anda"
                    ],
                ]
            ]
        ];

        return $data;
    }

    public function simpan()
    {
        ## POST DATA
        $insertdata = $this->input->post();
        $prefix = "FLP-" . date("Ym");
        $insertdata['FlipID'] = $this->crud->get_kode([
            'select' => 'RIGHT(FlipID, 7) AS KODE',
            'where' => [['LEFT(FlipID, 10) =' => $prefix]],
            'limit' => 1,
            'order_by' => 'FlipID DESC',
            'prefix' => $prefix
        ]);

        unset($insertdata['Amount']);
        $amount = str_replace(['.', ','], ['', '.'], $this->input->post('Amount'));
        $insertdata['Amount'] = $amount;

        $today = date('Y-m-d H:i');
        $insertdata['ExpDate'] = date('Y-m-d H:i', strtotime('+1 day', strtotime($today)));

        $bank = $insertdata['SenderBank'];
        $bank_name = $this->lokasi->get_one_bank($bank)['name'];
        $code_bank = ($bank == 'shopeepay') ? 'shopeepay_app' : $bank;
        $sender_type = ($bank == 'gopay' || $bank == 'ovo' || $bank == 'dana' || $bank == 'shopeepay' || $bank == 'linkaja') ? 'wallet_account' : 'virtual_account';
        $insertdata['BankName'] = $bank_name;
        $insertdata['SenderBankType'] = $sender_type;
        $insertdata['UserName'] = $this->session->userdata('UserName');

        // creating bill from API
        $createbill = $this->lokasi->createBill($insertdata['Title'], $amount, $insertdata['ExpDate'], $code_bank, $sender_type);
        $cek_error = isset($createbill['code']) ? $createbill['code'] : null;

        if ($cek_error == null) {
            $insertdata['LinkID']               = $createbill['link_id'];
            $insertdata['LinkUrl']              = 'https://' . $createbill['link_url'];
            $insertdata['SenderName']           = $createbill['customer']['name'];
            $insertdata['SenderEmail']          = $createbill['customer']['email'];
            $insertdata['SenderPhoneNumber']    = $createbill['customer']['phone'];
            $insertdata['SenderAddress']        = $createbill['customer']['address'];
            $insertdata['NoRekening']           = $createbill['bill_payment']['receiver_bank_account']['account_number'];
            $insertdata['Status']               = $createbill['bill_payment']['status'];
            $insertdata['PaymentUrl']           = $createbill['payment_url'];

            $res = $this->crud->insert($insertdata, 'flip');

            if ($res) {
                ## INSERT TO SERVER LOG
                $this->logsrv->insert_log([
                    'Action' => 'tambah',
                    'JenisTransaksi' => 'Flip',
                    'Description' => 'tambah data top up flip ' . $insertdata['FlipID'],
                ]);
                echo json_encode([
                    'status' => true,
                    'msg'  => "Berhasil menambah Data",
                    'link' => base_url('user/flip/detail_pembayaran/' . base64_encode($insertdata['FlipID']))
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'msg'  => "Gagal Menambah Data"
                ]);
            }
        } else {
            echo json_encode([
                'status' => false,
                'msg'  => "Gagal, data yang anda masukkan salah"
            ]);
        }
    }

    public function detail_pembayaran()
    {
        setlocale(LC_ALL, 'IND');
        checkAccess($this->session->userdata('fiturview')[66]);
        $flip_id   = escape(base64_decode($this->uri->segment(4)));

        $data['menu'] = 'flip';
        $data['title'] = 'Detail Pembayaran Top Up Flip';
        $data['view'] = 'user/v_flip_detail_pembayaran';
        $data['scripts'] = 'user/s_flip_detail_pembayaran';

        $data['data'] = $this->crud->get_one_row([
            'select' => '*',
            'from' => 'flip',
            'where' => [['FlipID' => $flip_id]],
        ]);
        $data['FlipID'] = $flip_id;

        $expdate = $data['data']['ExpDate'];
        $data['tanggal_kadaluarsa'] = (int)date('d', strtotime($expdate)) . ' ' . strftime('%B %Y', strtotime($expdate)) . ' pukul ' . date('H:i', strtotime($expdate)) . ' WIB.';

        $getbank = $this->bank_code_payment();
        $dtbank = [];
        foreach ($getbank as $key) {
            if ($key['bank_code'] == $data['data']['SenderBank']) {
                $dtbank = $key;
            }
        }
        $data['dtbank'] = $dtbank;
        $data['namaperusahaan'] = $this->lokasi->get_sistem_setting(1);

        loadview($data);
    }
}
