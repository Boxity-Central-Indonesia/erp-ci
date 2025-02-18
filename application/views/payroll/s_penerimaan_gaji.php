<script>
    const $table = $('#table-penerimaangaji').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
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
                data: 'NIP',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NamaPegawai',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NamaJabatan',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Bulan',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'InsentifPegawai',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'Status',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'ActualName',
                className: 'text-center',
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
        "rowCallback": function( row, data ) {
            if (!(data.KodeJabatan)) {
                $('td:eq(5)', row).html( '-' );
            }
        },
        "ajax": {
            "url": "<?= base_url('payroll/penerimaan_gaji'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.bulan = $("#bulan").val();
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $('#bulan').on('input', function(e) {
        $table.ajax.reload();
        var url = '<?= base_url('payroll/penerimaan_gaji/cetak/') ?>' + btoa($("#bulan").val());
        $('#btn-cetak').attr('href', url);
    });

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpan(self, data_post);
            return false;
        });

        $("#form-cair").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            cair(self, data_post);
            return false;
        });

        $("#table-penerimaangaji").on("click", ".btnbayar", function() {
            const kode = $(this).data('kode');
            const kode2 = $(this).data('kode2');

            Swal.fire({
                title: 'Pembayaran Gaji',
                text: "Bayarkan gaji kepada karyawan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Bayarkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    bayarkan(kode, kode2);
                    $("body").prepend('<div id="overlayer2"><span class="loader-overlay"><div class="atbd-spin-dots spin-lg"><span class="spin-dot badge-dot dot-primary"></span><span class="spin-dot badge-dot dot-primary"></span><span class="spin-dot badge-dot dot-primary"></span><span class="spin-dot badge-dot dot-primary"></span></div></span></div>');
                    $('#overlayer2').show();
                }
            })
        });

        $("#table-penerimaangaji").on("click", ".btnjurnalsudah", function() {
            Swal.fire({
                title: 'Informasi',
                text: "transaksi sudah dijurnalkan.",
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#FA7C41',
                confirmButtonText: 'Ok'
            })
        });
    });

    function bayarkan(kode, kode2) {
        let data = {
            IDRekap: kode,
            Bulan: kode2
        }

        get_response("<?= base_url('payroll/penerimaan_gaji/bayarperpegawai') ?>", data, function(response) {
            $('#overlayer2').hide();
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                showSwal('success', 'Informasi', 'Data transaksi penerimaan gaji telah berhasil dibayarkan.').then(function() {
                    // if (response.stj == 'on') {
                        $table.ajax.reload();
                    // } else {
                    //     window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnalmanual/') ?>" + btoa("penerimaangaji") + "/" + btoa(unescape(encodeURIComponent(response.idjurnal)));
                    // }
                });
            }
        })

    }

    function simpan(self, data_post) {
        post_response("<?= base_url('payroll/penerimaan_gaji/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", response.msg).then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    function cair(self, data_post) {
        $("body").prepend('<div id="overlayer1"><span class="loader-overlay"><div class="atbd-spin-dots spin-lg"><span class="spin-dot badge-dot dot-primary"></span><span class="spin-dot badge-dot dot-primary"></span><span class="spin-dot badge-dot dot-primary"></span><span class="spin-dot badge-dot dot-primary"></span></div></span></div>');
        $('#overlayer1').show();
        $('#ModalCair').modal('hide');

        post_response("<?= base_url('payroll/penerimaan_gaji/cair') ?>", data_post, function(response) {
            $('#overlayer1').hide();
            if (response.status) {
                showSwal("success", "Informasi", response.msg).then(function() {
                    // if (response.stj == 'on') {
                        $table.ajax.reload(null, false);
                        self[0].reset();
                    // } else {
                    //     window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnalmanual/') ?>" + btoa("penerimaangaji") + "/" + btoa(unescape(encodeURIComponent(response.idjurnal)));
                    // }
                });
            } else {
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Rekap Data');
    });

    $("#btncair").on("click", function() {
        $('#form-cair')[0].reset();
        $('#ModalCair').modal('show');
    });
</script>