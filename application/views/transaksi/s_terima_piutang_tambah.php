<script>
    const $table = $('#table-terimapiutangtambah').DataTable({
        "paging": false,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "bAutoWidth": false,
        "pageLength": 10,
        "searching": false,
        "sDom": 'frtip',
        "language": {
            "url": "<?= base_url() ?>assets/dist/js/Indonesian.js"
        },
        "order": [
            [1, 'desc']
        ],
        columns: [{
                data: 'no',
                "orderable": false,
                "searchable": false,
                className: 'text-center'
            },
            {
                data: 'IDTransJual',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NoRef_Manual',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TanggalPenjualan',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TotalTagihan',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'TotalBayar',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'SisaTagihan',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'DibayarSekarang',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center'

            },
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
 
            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
 
            // Total over all pages
            total = api
                .column(6)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var amount = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total).replace("Rp", "").trim();
 
            // Update footer
            $(api.column(6).footer()).html(amount);
        },
        "ajax": {
            "url": "<?= base_url('transaksi/terima_piutang/tambah'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.kodeperson = '<?= $KodePerson ?>';
                d.noref_manual = '<?= $NoRef_Manual ?>';
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#combo-status").on('change', function() {
        $table.ajax.reload();
    });

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpan(self, data_post);
            return false;
        });

        $("#batalbayar").on("click", function() {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "jika membatalkan transaksi, semua data transaksi akan terhapus",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Batalkan transaksi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    hapusbayar(kode)
                }
            })
        });

        $("#simpan-bayar").on("click", function () {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "jika menyimpan transaksi, semua data akan tersimpan dan data dengan pembayaran 0 akan terhapus",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Simpan transaksi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    simpanbayar(kode)
                }
            })
        });
    });

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/terima_piutang/simpantambah') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    function hapusbayar(kode) {
        let data = {
            NoRef_Manual: kode
        }

        get_response("<?= base_url('transaksi/terima_piutang/hapustambah') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Transaksi berhasil dibatalkan.').then(function() {
                    window.location.href = "<?= base_url('transaksi/trans_jual') ?>";
                });

            }
        })
    }

    function simpanbayar(kode) {
        let data = {
            NoRef_Manual: kode
        }

        get_response("<?= base_url('transaksi/terima_piutang/verifikasitambah') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Transaksi berhasil disimpan.').then(function() {
                    window.location.href = "<?= base_url('transaksi/trans_jual') ?>";
                });

            }
        })
    }

    $("#table-terimapiutangtambah").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoTransKas').val(model.NoTransKas);
        $('#NoRef_Sistem').val(model.IDTransJual);
        $('#NoRef_Manual').val(model.NoRef_Manual);
        $('#TanggalPenjualan').val(model.TanggalPenjualan);
        $('#TotalTagihan').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.TotalTagihan).replace("Rp", "").trim());
        $('#TotalBayar').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.TotalBayar).replace("Rp", "").trim());
        $('#SisaTagihan').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.SisaTagihan).replace("Rp", "").trim());
        $('#DibayarSekarang').val(model.DibayarSekarang);
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('DibayarSekarang');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>