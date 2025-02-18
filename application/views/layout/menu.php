<aside class="sidebar">
  <div class="sidebar__menu-group">
    <ul class="sidebar_nav">
      <li class="menu-title">
        <span>Main menu</span>
      </li>
      <li class="<?= $menu == 'beranda' ? 'active' : '' ?>">
        <a href="<?= base_url('beranda') ?>" class="nav-link <?= $menu == 'beranda' ? 'active' : '' ?>">
          <span data-feather="home" class="nav-icon"></span>
          <span class="menu-text">Dashboard</span>
        </a>
      </li>
      <?php

      $akseslevel = 0;
      $userlogin = 0;
      $gudang = 0;
      $customer = 0;
      $supplier = 0;
      $tahunanggaran = 0;
      $jenisbarang = 0;
      $barang = 0;
      $jabatan = 0;
      $pegawai = 0;
      $transpo = 0;
      $approvebeli = 0;
      $transbeli = 0;
      $rtrbeli = 0;
      $transhutang = 0;
      $penerimaanbrg = 0;
      $mutasigdg = 0;
      $baku = 0;
      $penolong = 0;
      $pembantu = 0;
      $jadi = 0;
      $penyesuaianstok = 0;
      $pergerakanstok = 0;
      $sliporder = 0;
      $quotation = 0;
      $transjual = 0;
      $retur = 0;
      $piutang = 0;
      $spk = 0;
      $prosesprod = 0;
      $penyesuaianprod = 0;
      $setting = 0;
      $labarugi = 0;
      $neraca = 0;
      $aruskas = 0;
      $lappenjualan = 0;
      $lappembelian = 0;
      $persediaanbrg = 0;
      $lapnilaibrg = 0;
      $kasbesar = 0;
      $laphpp = 0;
      $laphpj = 0;
      $lappemakaian = 0;
      $laphutang = 0;
      $lappiutang = 0;
      $aktivitas = 0;
      $kategori = 0;
      $transkas = 0;
      $ampas = 0;
      $importabs = 0;
      $adjustmentabs = 0;
      $komponengaji = 0;
      $gajipokok = 0;
      $penerimaangaji = 0;
      $lapabsensi = 0;
      $lapslipgaji = 0;
      $lapaktivitas = 0;
      $daftarakun = 0;
      $settingakun = 0;
      $jrnpenyesuaian = 0;
      $tutupbuku = 0;
      $logserv = 0;
      $pinjaman = 0;
      $flip = 0;
      $bukubesar = 0;

      $view = [];
      foreach ($this->session->userdata('fiturview') as $key => $value) {
        $view[$key] = $value;
        if ($key == 1 && $value == 1) {
          $akseslevel = 1;
        }
        if ($key == 2 && $value == 1) {
          $userlogin = 1;
        }
        if ($key == 3 && $value == 1) {
          $gudang = 1;
        }
        if ($key == 4 && $value == 1) {
          $customer = 1;
        }
        if ($key == 5 && $value == 1) {
          $supplier = 1;
        }
        if ($key == 6 && $value == 1) {
          $tahunanggaran = 1;
        }
        if ($key == 7 && $value == 1) {
          $jenisbarang = 1;
        }
        if ($key == 8 && $value == 1) {
          $barang = 1;
        }
        if ($key == 9 && $value == 1) {
          $jabatan = 1;
        }
        if ($key == 10 && $value == 1) {
          $pegawai = 1;
        }
        if ($key == 11 && $value == 1) {
          $transpo = 1;
        }
        if ($key == 12 && $value == 1) {
          $approvebeli = 1;
        }
        if ($key == 13 && $value == 1) {
          $transbeli = 1;
        }
        if ($key == 68 && $value == 1) {
          $rtrbeli = 1;
        }
        if ($key == 14 && $value == 1) {
          $transhutang = 1;
        }
        if ($key == 15 && $value == 1) {
          $penerimaanbrg = 1;
        }
        if ($key == 16 && $value == 1) {
          $mutasigdg = 1;
        }
        if ($key == 17 && $value == 1) {
          $baku = 1;
        }
        if ($key == 18 && $value == 1) {
          $penolong = 1;
        }
        if ($key == 19 && $value == 1) {
          $pembantu = 1;
        }
        if ($key == 20 && $value == 1) {
          $jadi = 1;
        }
        if ($key == 21 && $value == 1) {
          $penyesuaianstok = 1;
        }
        if ($key == 22 && $value == 1) {
          $pergerakanstok = 1;
        }
        if ($key == 23 && $value == 1) {
          $sliporder = 1;
        }
        if ($key == 24 && $value == 1) {
          $quotation = 1;
        }
        if ($key == 25 && $value == 1) {
          $transjual = 1;
        }
        if ($key == 26 && $value == 1) {
          $retur = 1;
        }
        if ($key == 27 && $value == 1) {
          $piutang = 1;
        }
        if ($key == 28 && $value == 1) {
          $spk = 1;
        }
        if ($key == 29 && $value == 1) {
          $prosesprod = 1;
        }
        if ($key == 30 && $value == 1) {
          $penyesuaianprod = 1;
        }
        if ($key == 31 && $value == 1) {
          $setting = 1;
        }
        if ($key == 32 && $value == 1) {
          $labarugi = 1;
        }
        if ($key == 33 && $value == 1) {
          $neraca = 1;
        }
        if ($key == 34 && $value == 1) {
          $aruskas = 1;
        }
        if ($key == 35 && $value == 1) {
          $lappenjualan = 1;
        }
        if ($key == 36 && $value == 1) {
          $lappembelian = 1;
        }
        if ($key == 37 && $value == 1) {
          $persediaanbrg = 1;
        }
        if ($key == 38 && $value == 1) {
          $lapnilaibrg = 1;
        }
        if ($key == 39 && $value == 1) {
          $kasbesar = 1;
        }
        if ($key == 40 && $value == 1) {
          $laphpp = 1;
        }
        if ($key == 41 && $value == 1) {
          $laphpj = 1;
        }
        if ($key == 42 && $value == 1) {
          $lappemakaian = 1;
        }
        if ($key == 43 && $value == 1) {
          $laphutang = 1;
        }
        if ($key == 44 && $value == 1) {
          $lappiutang = 1;
        }
        if ($key == 45 && $value == 1) {
          $aktivitas = 1;
        }
        if ($key == 46 && $value == 1) {
          $kategori = 1;
        }
        if ($key == 47 && $value == 1) {
          $transkas = 1;
        }
        if ($key == 48 && $value == 1) {
          $ampas = 1;
        }
        if ($key == 49 && $value == 1) {
          $importabs = 1;
        }
        if ($key == 50 && $value == 1) {
          $adjustmentabs = 1;
        }
        if ($key == 51 && $value == 1) {
          $komponengaji = 1;
        }
        if ($key == 52 && $value == 1) {
          $gajipokok = 1;
        }
        if ($key == 53 && $value == 1) {
          $penerimaangaji = 1;
        }
        if ($key == 54 && $value == 1) {
          $lapabsensi = 1;
        }
        if ($key == 55 && $value == 1) {
          $lapslipgaji = 1;
        }
        if ($key == 56 && $value == 1) {
          $lapaktivitas = 1;
        }
        if ($key == 57 && $value == 1) {
          $daftarakun = 1;
        }
        if ($key == 58 && $value == 1) {
          $settingakun = 1;
        }
        if ($key == 59 && $value == 1) {
          $jrnpenyesuaian = 1;
        }
        if ($key == 60 && $value == 1) {
          $tutupbuku = 1;
        }
        if ($key == 64 && $value == 1) {
          $logserv = 1;
        }
        if ($key == 65 && $value == 1) {
          $pinjaman = 1;
        }
        if ($key == 66 && $value == 1) {
          $flip = 1;
        }
        if ($key == 67 && $value == 1) {
          $bukubesar = 1;
        }
      }
      ?>
      <script>
        flip = "<?= $flip ?>";
      </script>
      <?php if ($akseslevel == 1 || $userlogin == 1 || $gudang == 1 || $customer == 1 || $supplier == 1 || $tahunanggaran == 1 || $jenisbarang == 1 || $barang == 1 || $jabatan == 1 || $pegawai == 1 || $setting == 1 || $aktivitas == 1 || $kategori == 1) { ?>
        <li class="menu-title m-top-30">
          <span>Aplikasi</span>
        </li>
      <?php } ?>
      <?php if ($akseslevel == 1 || $userlogin == 1 || $setting) { ?>
        <li class="has-child <?= $menu == 'akseslevel' || $menu == 'userlogin' || $menu == 'setting' ? 'open' : '' ?>">
          <a href="#" class="<?= $menu == 'akseslevel' || $menu == 'userlogin' || $menu == 'setting' ? 'active' : '' ?>">
            <span data-feather="folder" class="nav-icon"></span>
            <span class="menu-text">Administrator</span>
            <span class="toggle-icon"></span>
          </a>
          <ul>
            <?php if ($akseslevel == 1) { ?>
              <li>
                <a href="<?= base_url('user/akseslevel') ?>" class="nav-link <?= $menu == 'akseslevel' ? 'active' : '' ?>">Akses Level</a>
              </li>
            <?php }  ?>
            <?php if ($userlogin == 1) { ?>
              <li>
                <a href="<?= base_url('user/userlogin') ?>" class="nav-link <?= $menu == 'userlogin' ? 'active' : '' ?>">User Login</a>
              </li>
            <?php } ?>
            <?php if ($setting == 1) { ?>
              <li>
                <a href="<?= base_url('user/sistemsetting') ?>" class="nav-link <?= $menu == 'setting' ? 'active' : '' ?>">Sistem Setting</a>
              </li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <?php if ($gudang == 1 || $customer == 1 || $supplier == 1 || $tahunanggaran == 1 || $jenisbarang == 1 || $barang == 1 || $jabatan == 1 || $pegawai == 1 || $aktivitas == 1 || $kategori == 1) { ?>
        <li class="has-child <?= $menu == 'gudang' || $menu == 'customer' || $menu == 'supplier' || $menu == 'tahunanggaran' || $menu == 'jenisbarang' || $menu == 'barang' || $menu == 'jabatan' || $menu == 'pegawai' || $menu == 'aktivitas' || $menu == 'kategori' ? 'open' : '' ?>">
          <a href="#" class="<?= $menu == 'gudang' || $menu == 'customer' || $menu == 'supplier' || $menu == 'tahunanggaran' || $menu == 'jenisbarang' || $menu == 'barang' || $menu == 'jabatan' || $menu == 'pegawai' || $menu == 'aktivitas' || $menu == 'kategori' ? 'active' : '' ?>">
            <span data-feather="cpu" class="nav-icon"></span>
            <span class="menu-text">Data Master</span>
            <span class="toggle-icon"></span>
          </a>
          <ul>
            <?php if ($gudang == 1) { ?>
              <li>
                <a href="<?= base_url('master/gudang') ?>" class="nav-link <?= $menu == 'gudang' ? 'active' : '' ?>">Master Gudang</a>
              </li>
            <?php } ?>
            <?php if ($customer == 1) { ?>
              <li>
                <a href="<?= base_url('master/person/customer') ?>" class="nav-link <?= $menu == 'customer' ? 'active' : '' ?>">Master Customer</a>
              </li>
            <?php } ?>
            <?php if ($supplier == 1) { ?>
              <li>
                <a href="<?= base_url('master/person/supplier') ?>" class="nav-link <?= $menu == 'supplier' ? 'active' : '' ?>">Master Supplier</a>
              </li>
            <?php } ?>
            <?php if ($tahunanggaran == 1) { ?>
              <li>
                <a href="<?= base_url('master/tahunanggaran') ?>" class="nav-link <?= $menu == 'tahunanggaran' ? 'active' : '' ?>">Master Tahun Anggaran</a>
              </li>
            <?php } ?>
            <?php if ($jenisbarang == 1) { ?>
              <li hidden>
                <a href="<?= base_url('master/jenisbarang') ?>" class="nav-link <?= $menu == 'jenisbarang' ? 'active' : '' ?>">Master Jenis Barang</a>
              </li>
            <?php } ?>
            <?php if ($kategori == 1) { ?>
              <li hidden>
                <a href="<?= base_url('master/kategori') ?>" class="nav-link <?= $menu == 'kategori' ? 'active' : '' ?>">Master Kategori Barang</a>
              </li>
            <?php } ?>
            <?php if ($barang == 1) { //|| $jenisbarang == 1 || $kategori == 1 ?>
              <li>
                <a href="<?= base_url('master/barang') ?>" class="nav-link <?= $menu == 'barang' ? 'active' : '' ?>">Master Barang</a>
              </li>
            <?php } ?>
            <?php if ($jabatan == 1) { ?>
              <li>
                <a href="<?= base_url('master/jabatan') ?>" class="nav-link <?= $menu == 'jabatan' ? 'active' : '' ?>">Master Jabatan</a>
              </li>
            <?php } ?>
            <?php if ($pegawai == 1) { ?>
              <li>
                <a href="<?= base_url('master/pegawai') ?>" class="nav-link <?= $menu == 'pegawai' ? 'active' : '' ?>">Master Pegawai</a>
              </li>
            <?php } ?>
            <?php if ($aktivitas == 1) { ?>
              <li>
                <a href="<?= base_url('master/aktivitas') ?>" class="nav-link <?= $menu == 'aktivitas' ? 'active' : '' ?>">Master Aktivitas</a>
              </li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <?php if ($transpo == 1 || $approvebeli == 1 || $transbeli == 1 || $rtrbeli == 1 || $transhutang == 1 || $penerimaanbrg == 1 || $mutasigdg == 1 || $baku == 1 || $penolong == 1 || $pembantu == 1 || $jadi == 1 || $penyesuaianstok == 1 || $pergerakanstok == 1 || $sliporder == 1 || $quotation == 1 || $transjual == 1 || $retur == 1 || $piutang == 1 || $spk == 1 || $prosesprod == 1 || $penyesuaianprod == 1 || $transkas == 1) { ?>
        <li class="menu-title m-top-30">
          <span>Transaksi</span>
        </li>
      <?php } ?>
      <?php if ($transpo == 1 || $approvebeli == 1 || $transbeli == 1 || $rtrbeli == 1) { //|| $transhutang == 1 ?>
        <li class="<?= $menu == 'trans_beli' ? 'active' : '' ?>">
          <a href="<?= base_url('transaksi/trans_beli') ?>" class="nav-link <?= $menu == 'trans_beli' ? 'active' : '' ?>">
            <span data-feather="shopping-cart" class="nav-icon"></span>
            <span class="menu-text">Transaksi Pembelian</span>
          </a>
        </li>
        <li hidden class="has-child <?= $menu == 'transpo' || $menu == 'approvebeli' || $menu == 'transbeli' || $menu == 'transhutang' ? 'open' : '' ?>">
          <a href="#" class="<?= $menu == 'transpo' || $menu == 'approvebeli' || $menu == 'transbeli' || $menu == 'transhutang' ? 'active' : '' ?>">
            <span data-feather="shopping-cart" class="nav-icon"></span>
            <span class="menu-text">Transaksi Pembelian</span>
            <span class="toggle-icon"></span>
          </a>
          <ul>
            <?php if ($transpo == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/transaksi_po') ?>" class="nav-link <?= $menu == 'transpo' ? 'active' : '' ?>">Cetak Pembelian (PO)</a>
              </li>
            <?php } ?>
            <?php if ($approvebeli == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/approval_pembelian') ?>" class="nav-link <?= $menu == 'approvebeli' ? 'active' : '' ?>">Approval Transaksi Pembelian (PO)</a>
              </li>
            <?php } ?>
            <?php if ($transbeli == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/transaksi_pembelian') ?>" class="nav-link <?= $menu == 'transbeli' ? 'active' : '' ?>">Transaksi Pembelian</a>
              </li>
            <?php } ?>
            <?php if ($transhutang == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/bayar_hutang') ?>" class="nav-link <?= $menu == 'transhutang' ? 'active' : '' ?>">Transaksi Hutang</a>
              </li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <?php if ($sliporder == 1 || $quotation == 1 || $transjual == 1 || $retur == 1) { //|| $piutang == 1 ?>
        <li class="<?= $menu == 'trans_jual' ? 'active' : '' ?>">
          <a href="<?= base_url('transaksi/trans_jual') ?>" class="nav-link <?= $menu == 'trans_jual' ? 'active' : '' ?>">
            <span data-feather="book" class="nav-icon"></span>
            <span class="menu-text">Transaksi Penjualan</span>
          </a>
        </li>
        <li hidden> class="has-child <?= $menu == 'sliporder' || $menu == 'quotation' || $menu == 'transjual' || $menu == 'retur' || $menu == 'piutang' ? 'open' : '' ?>">
          <a href="#" class="<?= $menu == 'sliporder' || $menu == 'quotation' || $menu == 'transjual' || $menu == 'retur' || $menu == 'piutang' ? 'active' : '' ?>">
            <span data-feather="dollar-sign" class="nav-icon"></span>
            <span class="menu-text">Transaksi Penjualan</span>
            <span class="toggle-icon"></span>
          </a>
          <ul>
            <?php if ($sliporder == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/slip_order') ?>" class="nav-link <?= $menu == 'sliporder' ? 'active' : '' ?>">Transaksi Slip Order</a>
              </li>
            <?php } ?>
            <?php if ($quotation == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/quotation_invoice') ?>" class="nav-link <?= $menu == 'quotation' ? 'active' : '' ?>">Quotation, Purchase Invoice, dan Invoice</a>
              </li>
            <?php } ?>
            <?php if ($transjual == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/transaksi_penjualan') ?>" class="nav-link <?= $menu == 'transjual' ? 'active' : '' ?>">Transaksi Penjualan</a>
              </li>
            <?php } ?>
            <?php if ($retur == 1) { ?>
              <li>
                <a href="#" class="nav-link <?= $menu == 'retur' ? 'active' : '' ?>">Retur</a>
              </li>
            <?php } ?>
            <?php if ($piutang == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/terima_piutang') ?>" class="nav-link <?= $menu == 'piutang' ? 'active' : '' ?>">Transaksi Terima Piutang</a>
              </li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <?php if ($transkas == 1) { ?>
        <li class="<?= $menu == 'transkas' ? 'active' : '' ?>">
          <a href="<?= base_url('transaksi/transaksi_kas') ?>" class="nav-link <?= $menu == 'transkas' ? 'active' : '' ?>">
            <span data-feather="dollar-sign" class="nav-icon"></span>
            <span class="menu-text">Transaksi Biaya</span>
          </a>
        </li>
      <?php } ?>
      <?php if ($spk == 1 || $prosesprod == 1 || $penyesuaianprod == 1) { ?>
        <li class="has-child <?= $menu == 'spk' || $menu == 'prosesprod' || $menu == 'listproduksi' || $menu == 'penyesuaianprod' ? 'open' : '' ?>">
          <a href="#" class="<?= $menu == 'spk' || $menu == 'prosesprod' || $menu == 'listproduksi' || $menu == 'penyesuaianprod' ? 'active' : '' ?>">
            <span data-feather="grid" class="nav-icon"></span>
            <span class="menu-text">Manufaktur</span>
            <span class="toggle-icon"></span>
          </a>
          <ul>
            <?php if ($spk == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/spk') ?>" class="nav-link <?= $menu == 'spk' ? 'active' : '' ?>">Surat Perintah Kerja (SPK)</a>
              </li>
            <?php } ?>
            <?php if ($penyesuaianprod == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/penyesuaian_produksi') ?>" class="nav-link <?= $menu == 'penyesuaianprod' ? 'active' : '' ?>">Penyesuaian Produksi</a>
              </li>
            <?php } ?>
            <?php if ($prosesprod == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/proses_produksi') ?>" class="nav-link <?= $menu == 'prosesprod' ? 'active' : '' ?>">Proses Produksi</a>
              </li>
              <li>
                <a href="<?= base_url('transaksi/proses_produksi/list_produksi') ?>" class="nav-link <?= $menu == 'listproduksi' ? 'active' : '' ?>">List Produksi</a>
              </li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <?php if ($penerimaanbrg == 1 || $mutasigdg == 1 || $baku == 1 || $penolong == 1 || $pembantu == 1 || $jadi == 1 || $penyesuaianstok == 1 || $pergerakanstok == 1) { ?>
        <li class="has-child <?= $menu == 'penerimaanbrg' || $menu == 'mutasigdg' || $menu == 'baku' || $menu == 'penolong' || $menu == 'pembantu' || $menu == 'jadi' || $menu == 'penyesuaianstok' || $menu == 'pergerakanstok' ? 'open' : '' ?>">
          <a href="#" class="<?= $menu == 'penerimaanbrg' || $menu == 'mutasigdg' || $menu == 'baku' || $menu == 'penolong' || $menu == 'pembantu' || $menu == 'jadi' || $menu == 'penyesuaianstok' || $menu == 'pergerakanstok' ? 'active' : '' ?>">
            <span data-feather="layers" class="nav-icon"></span>
            <span class="menu-text">Warehouse</span>
            <span class="toggle-icon"></span>
          </a>
          <ul>
            <?php if ($penerimaanbrg == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/penerimaan_barang') ?>" class="nav-link <?= $menu == 'penerimaanbrg' ? 'active' : '' ?>">Transaksi Penerimaan Barang</a>
              </li>
            <?php } ?>
            <?php if ($mutasigdg == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/mutasi_gudang') ?>" class="nav-link <?= $menu == 'mutasigdg' ? 'active' : '' ?>">Transaksi Mutasi Gudang</a>
              </li>
            <?php } ?>
            <?php if ($baku == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/bahan_baku') ?>" class="nav-link <?= $menu == 'baku' ? 'active' : '' ?>">List Bahan Baku</a>
              </li>
            <?php } ?>
            <?php if ($penolong == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/bahan_penolong') ?>" class="nav-link <?= $menu == 'penolong' ? 'active' : '' ?>">List Bahan Penolong</a>
              </li>
            <?php } ?>
            <?php if ($pembantu == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/barang_pembantu') ?>" class="nav-link <?= $menu == 'pembantu' ? 'active' : '' ?>">List Barang Pembantu</a>
              </li>
            <?php } ?>
            <?php if ($jadi == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/barang_jadi') ?>" class="nav-link <?= $menu == 'jadi' ? 'active' : '' ?>">List Barang Jadi</a>
              </li>
            <?php } ?>
            <?php if ($penyesuaianstok == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/penyesuaian_stok') ?>" class="nav-link <?= $menu == 'penyesuaianstok' ? 'active' : '' ?>">Penyesuaian Stok</a>
              </li>
            <?php } ?>
            <?php if ($pergerakanstok == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/pergerakan_stok') ?>" class="nav-link <?= $menu == 'pergerakanstok' ? 'active' : '' ?>">Pergerakan Stok</a>
              </li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <?php if ($daftarakun == 1 || $settingakun == 1 || $jrnpenyesuaian == 1 || $tutupbuku == 1) { ?>
        <li class="menu-title m-top-30">
          <span>Akuntansi</span>
        </li>
      <?php } ?>
      <?php if ($daftarakun == 1 || $settingakun == 1 || $jrnpenyesuaian == 1 || $tutupbuku == 1) { ?>
        <li class="has-child <?= $menu == 'daftarakun' || $menu == 'settingakun' || $menu == 'jrnpenyesuaian' || $menu == 'tutupbuku' ? 'open' : '' ?>">
          <a href="#" class="<?= $menu == 'daftarakun' || $menu == 'settingakun' || $menu == 'jrnpenyesuaian' || $menu == 'tutupbuku' ? 'active' : '' ?>">
            <span data-feather="book-open" class="nav-icon"></span>
            <span class="menu-text">Akuntansi</span>
            <span class="toggle-icon"></span>
          </a>
          <ul>
            <?php if ($daftarakun == 1) { ?>
              <li>
                <a href="<?= base_url('akuntansi/daftar_akun') ?>" class="nav-link <?= $menu == 'daftarakun' ? 'active' : '' ?>">Daftar Akun Akuntansi</a>
              </li>
            <?php }  ?>
            <?php if ($settingakun == 1) { ?>
              <li>
                <a href="<?= base_url('akuntansi/setting_akun') ?>" class="nav-link <?= $menu == 'settingakun' ? 'active' : '' ?>">Setting Akun Penjurnalan</a>
              </li>
            <?php } ?>
            <?php if ($jrnpenyesuaian == 1) { ?>
              <li>
                <a href="<?= base_url('akuntansi/jurnal_penyesuaian') ?>" class="nav-link <?= $menu == 'jrnpenyesuaian' ? 'active' : '' ?>">Jurnal Penyesuaian</a>
              </li>
            <?php } ?>
            <?php if ($tutupbuku == 1) { ?>
              <li>
                <a href="<?= base_url('akuntansi/tutup_buku') ?>" class="nav-link <?= $menu == 'tutupbuku' ? 'active' : '' ?>">Tutup Buku Akhir Tahun</a>
              </li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <?php if ($labarugi == 1 || $neraca == 1 || $aruskas == 1 || $lappenjualan == 1 || $lappembelian == 1 || $persediaanbrg == 1 || $lapnilaibrg == 1 || $kasbesar == 1 || $laphpp == 1 || $laphpj == 1 || $lappemakaian == 1 || $laphutang == 1 || $lappiutang == 1 || $bukubesar == 1) { ?>
        <li class="menu-title m-top-30">
          <span>Laporan</span>
        </li>
      <?php } ?>
      <?php if ($persediaanbrg == 1 || $lapnilaibrg == 1 || $lappemakaian == 1) { ?>
        <li class="has-child <?= $menu == 'persediaanbrg' || $menu == 'lapnilaibrg' || $menu == 'lappemakaian' ? 'open' : 'closed' ?>">
          <a href="#" class="<?= $menu == 'persediaanbrg' || $menu == 'lapnilaibrg' || $menu == 'lappemakaian' ? 'active' : '' ?>">
            <span data-feather="file-text" class="nav-icon"></span>
            <span class="menu-text">Laporan Stok</span>
            <span class="toggle-icon"></span>
          </a>
          <ul>
            <?php if ($persediaanbrg == 1) { ?>
              <li hidden>
                <a href="<?= base_url('laporan/persediaan_barang') ?>" class="nav-link <?= $menu == 'persediaanbrg' ? 'active' : '' ?>">Persediaan Barang</a>
              </li>
            <?php } ?>
            <?php if ($lapnilaibrg == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/nilai_persediaan') ?>" class="nav-link <?= $menu == 'lapnilaibrg' ? 'active' : '' ?>">Persediaan Barang</a>
              </li>
            <?php } ?>
            <?php if ($lappemakaian == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/pemakaian_bahan') ?>" class="nav-link <?= $menu == 'lappemakaian' ? 'active' : '' ?>">Laporan Pemakaian Bahan Baku, Penolong & Pembantu</a>
              </li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <?php if ($labarugi == 1 || $neraca == 1 || $aruskas == 1 || $lappenjualan == 1 || $lappembelian == 1 || $kasbesar == 1 || $laphpp == 1 || $laphpj == 1 || $laphutang == 1 || $lappiutang == 1 || $bukubesar == 1) { ?>
        <li class="has-child <?= $menu == 'labarugi' || $menu == 'neraca' || $menu == 'aruskas' || $menu == 'lappenjualan' || $menu == 'lappembelian' || $menu == 'kasbesar' || $menu == 'laphpp' || $menu == 'laphpj' || $menu == 'laphutang' || $menu == 'lappiutang' || $menu == 'bukubesar' ? 'open' : 'closed' ?>">
          <a href="#" class="<?= $menu == 'labarugi' || $menu == 'neraca' || $menu == 'aruskas' || $menu == 'lappenjualan' || $menu == 'lappembelian' || $menu == 'kasbesar' || $menu == 'laphpp' || $menu == 'laphpj' || $menu == 'laphutang' || $menu == 'lappiutang' || $menu == 'bukubesar' ? 'active' : '' ?>">
            <span data-feather="file-text" class="nav-icon"></span>
            <span class="menu-text">Laporan Keuangan</span>
            <span class="toggle-icon"></span>
          </a>
          <ul>
            <?php if ($neraca == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/neraca') ?>" class="nav-link <?= $menu == 'neraca' ? 'active' : '' ?>">Neraca</a>
              </li>
            <?php } ?>
            <?php if ($labarugi == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/laba_rugi') ?>" class="nav-link <?= $menu == 'labarugi' ? 'active' : '' ?>">Laba Rugi</a>
              </li>
            <?php }  ?>
            <?php if ($aruskas == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/arus_kas') ?>" class="nav-link <?= $menu == 'aruskas' ? 'active' : '' ?>">Arus Kas</a>
              </li>
            <?php } ?>
            <?php if ($lappenjualan == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/penjualan') ?>" class="nav-link <?= $menu == 'lappenjualan' ? 'active' : '' ?>">Penjualan</a>
              </li>
            <?php } ?>
            <?php if ($lappembelian == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/pembelian') ?>" class="nav-link <?= $menu == 'lappembelian' ? 'active' : '' ?>">Pembelian</a>
              </li>
            <?php } ?>
            <?php if ($kasbesar == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/kas_besar') ?>" class="nav-link <?= $menu == 'kasbesar' ? 'active' : '' ?>">Kas Besar</a>
              </li>
            <?php } ?>
            <?php if ($bukubesar == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/buku_besar') ?>" class="nav-link <?= $menu == 'bukubesar' ? 'active' : '' ?>">Buku Besar</a>
              </li>
            <?php } ?>
            <?php if ($laphpp == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/hp_produksi') ?>" class="nav-link <?= $menu == 'laphpp' ? 'active' : '' ?>">Laporan HPP (Harga Pokok Produksi)</a>
              </li>
            <?php } ?>
            <?php if ($laphpj == 1) { ?>
              <li hidden>
                <a href="<?= base_url('laporan/hp_penjualan') ?>" class="nav-link <?= $menu == 'laphpj' ? 'active' : '' ?>">Laporan Harga Pokok Penjualan</a>
              </li>
            <?php } ?>
            <?php if ($laphutang == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/hutang') ?>" class="nav-link <?= $menu == 'laphutang' ? 'active' : '' ?>">Laporan Hutang</a>
              </li>
            <?php } ?>
            <?php if ($lappiutang == 1) { ?>
              <li>
                <a href="<?= base_url('laporan/piutang') ?>" class="nav-link <?= $menu == 'lappiutang' ? 'active' : '' ?>">Laporan Piutang</a>
              </li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <?php if ($importabs == 1 || $adjustmentabs == 1 || $komponengaji == 1 || $gajipokok == 1 || $penerimaangaji == 1 || $lapabsensi == 1 || $lapslipgaji == 1 || $lapaktivitas == 1 || $ampas == 1 || $pinjaman == 1) { ?>
        <li class="menu-title m-top-30">
          <span>Payroll</span>
        </li>
      <?php } ?>
      <?php if ($importabs == 1 || $adjustmentabs == 1 || $komponengaji == 1 || $gajipokok == 1 || $penerimaangaji == 1 || $lapabsensi == 1 || $lapslipgaji == 1 || $lapaktivitas == 1 || $ampas == 1 || $pinjaman == 1) { ?>
        <li class="has-child <?= $menu == 'importabs' || $menu == 'adjustmentabs' || $menu == 'komponengaji' || $menu == 'gajipokok' || $menu == 'penerimaangaji' || $menu == 'lapabsensi' || $menu == 'lapslipgaji' || $menu == 'lapaktivitas' || $menu == 'ampas' || $menu == 'pinjaman' ? 'open' : '' ?>">
          <a href="#" class="<?= $menu == 'importabs' || $menu == 'adjustmentabs' || $menu == 'komponengaji' || $menu == 'gajipokok' || $menu == 'penerimaangaji' || $menu == 'lapabsensi' || $menu == 'lapslipgaji' || $menu == 'lapaktivitas' || $menu == 'ampas' || $menu == 'pinjaman' ? 'active' : '' ?>">
            <span data-feather="server" class="nav-icon"></span>
            <span class="menu-text">Payroll</span>
            <span class="toggle-icon"></span>
          </a>
          <ul>
            <?php if ($ampas == 1) { ?>
              <li class="has-child <?= $menu == 'ampas' ? 'open' : '' ?>">
                <a href="#" class="nav-link <?= $menu == 'ampas' ? 'active' : '' ?>">
                  <!-- <span data-feather="columns" class="nav-icon"></span> -->
                  <span class="menu-text">Insentif</span>
                  <span class="toggle-icon"></span>
                </a>
                <ul>
                  <?php if ($ampas == 1) { ?>
                    <li>
                      <a href="<?= base_url('transaksi/ampas_dapur') ?>" class="nav-link <?= $menu == 'ampas' ? 'active' : '' ?>">Ampas Dapur</a>
                    </li>
                  <?php } ?>
                </ul>
              </li>
            <?php } ?>
            <?php if ($importabs == 1) { ?>
              <li>
                <a href="<?= base_url('payroll/import_absensi') ?>" class="nav-link <?= $menu == 'importabs' ? 'active' : '' ?>">Import Absensi Per Bulan</a>
              </li>
            <?php }  ?>
            <?php if ($adjustmentabs == 1) { ?>
              <li>
                <a href="<?= base_url('payroll/adjustment_absensi') ?>" class="nav-link <?= $menu == 'adjustmentabs' ? 'active' : '' ?>">Adjustment Absensi</a>
              </li>
            <?php } ?>
            <?php if ($komponengaji == 1) { ?>
              <li>
                <a href="<?= base_url('payroll/komponen_gaji') ?>" class="nav-link <?= $menu == 'komponengaji' ? 'active' : '' ?>">Setting Komponen Gaji</a>
              </li>
            <?php } ?>
            <?php if ($gajipokok == 1) { ?>
              <li>
                <a href="<?= base_url('payroll/gaji_pokok') ?>" class="nav-link <?= $menu == 'gajipokok' ? 'active' : '' ?>">Setting Gaji Pokok</a>
              </li>
            <?php } ?>
            <?php if ($penerimaangaji == 1) { ?>
              <li>
                <a href="<?= base_url('payroll/penerimaan_gaji') ?>" class="nav-link <?= $menu == 'penerimaangaji' ? 'active' : '' ?>">Transaksi Penerimaan Gaji</a>
              </li>
            <?php } ?>
            <?php if ($pinjaman == 1) { ?>
              <li>
                <a href="<?= base_url('transaksi/transaksi_pinjaman') ?>" class="nav-link <?= $menu == 'pinjaman' ? 'active' : '' ?>">Transaksi Pinjaman Karyawan</a>
              </li>
            <?php } ?>
            <?php if ($lapabsensi == 1) { ?>
              <li>
                <a href="<?= base_url('payroll/laporan_absensi') ?>" class="nav-link <?= $menu == 'lapabsensi' ? 'active' : '' ?>">Laporan Absensi Semua Pegawai</a>
              </li>
            <?php } ?>
            <?php if ($lapslipgaji == 1) { ?>
              <li>
                <a href="<?= base_url('payroll/laporan_slipgaji') ?>" class="nav-link <?= $menu == 'lapslipgaji' ? 'active' : '' ?>">Laporan Gaji</a>
              </li>
            <?php } ?>
            <?php if ($lapaktivitas == 1) { ?>
              <li>
                <a href="<?= base_url('payroll/laporan_aktivitas') ?>" class="nav-link <?= $menu == 'lapaktivitas' ? 'active' : '' ?>">Laporan Aktivitas Pegawai</a>
              </li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <?php if ($flip == 1 ) { ?>
        <li class="menu-title m-top-30" hidden>
          <span>Flip</span>
        </li>
      <?php } ?>
      <?php if ($flip == 1) { ?>
        <li class="<?= $menu == 'flip' ? 'active' : '' ?>" hidden>
          <a href="<?= base_url('user/flip') ?>" class="nav-link <?= $menu == 'flip' ? 'active' : '' ?>">
            <span data-feather="zap" class="nav-icon"></span>
            <span class="menu-text">Flip</span>
          </a>
        </li>
      <?php } ?>
      <?php if ($logserv == 1 ) { ?>
        <li class="menu-title m-top-30">
          <span>Server Log</span>
        </li>
      <?php } ?>
      <?php if ($logserv == 1) { ?>
        <li class="<?= $menu == 'logserv' ? 'active' : '' ?>">
          <a href="<?= base_url('user/logserver') ?>" class="nav-link <?= $menu == 'logserv' ? 'active' : '' ?>">
            <span data-feather="repeat" class="nav-icon"></span>
            <span class="menu-text">Server Log</span>
          </a>
        </li>
      <?php } ?>
    </ul>
  </div>
</aside>