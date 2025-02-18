<script>
    const $table = $('#table-pinjaman').DataTable({
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
                data: 'NamaPegawai',
                className: 'text-left',
                "orderable": true,
                "searchable": false,
            },
            {
                data: 'TanggalPinjam',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'MingguKe',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NominalPinjam',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NominalDibayar',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Keterangan',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'ActualName',
                className: 'text-left',
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
        // "rowCallback": function( row, data ) {
        //     if (!(data.NoRef_Manual)) {
        //         $('td:eq(2)', row).html( '-' );
        //     }
        // },
        "ajax": {
            "url": "<?= base_url('transaksi/transaksi_pinjaman'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.tgl = $("#tgl-transaksi").val();
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $('#tgl-transaksi').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            locale: {
                format: "DD-MM-YYYY"
            }
        },
        function(start, end) {
            $('#tgl-transaksi').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
            $table.ajax.reload();

            // var url = '<?= base_url('transaksi/ampas_dapur/cetak/') ?>' + btoa($("#combo-jenis").val());
            // $('#btn-cetak').attr('href', url);
        }
    );

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            let data = {
                KodeTrPinjam: $('#KodeTrPinjam').val(),
                KodePegawai: $('#KodePegawai').val(),
                TanggalPinjam: $('#TanggalPinjam').val(),
                NominalPinjam: $('#NominalPinjam').val()
            }
            var isedit = $('#isedit').val();

            get_response("<?= base_url('transaksi/transaksi_pinjaman/checkPinjaman') ?>", data, function(response) {
                if (response.status === false) {
                    $('#ModalTambah').modal('hide');
                    showSwal1('warning', 'Peringatan', response.msg).then(function() {
                        $('#ModalTambah').modal('show');
                        $('#defaultModalLabel').html('Tambah Data');
                    });
                    return false;
                } else {
                    simpan(self, data_post);
                }
            });
            return false;
        });

        $("#table-pinjaman").on("click", ".btnhapus", function() {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "data terhapus tidak dapat di kembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Hapus data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    hapus(kode)
                }
            })
        });

        $("#table-pinjaman").on("click", ".btnjurnalsudah", function() {
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

    function hapus(kode) {
        let data = {
            KodeTrPinjam: kode
        }

        get_response("<?= base_url('transaksi/transaksi_pinjaman/hapus') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Data berhasil dihapus.');

            }
        })

    }

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/transaksi_pinjaman/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    if (response.stj == 'off') {
                        window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnalmanual/') ?>" + btoa("pinjaman") + "/" + btoa(unescape(encodeURIComponent(response.idjurnal))) + "/" + btoa("transaksi") + "/" + btoa("transaksi_pinjaman");
                    }
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#table-pinjaman").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#KodeTrPinjam').val(model.KodeTrPinjam);
        $('#KodePegawai').val(model.KodePegawai).change();
        $('#TanggalPinjam').val(model.TanggalPinjam);
        $('#NominalPinjam').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.NominalPinjam).replace("Rp", "").trim());
        $('#Keterangan').val(model.Keterangan);
        $('#ModalTambah').modal('show');
        $("#isedit").val("edit");
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodeTrPinjam').val('');
        $('#KodePegawai').val('').change();
        $("#isedit").val("tambah");
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('TanggalPinjam').value = now.toISOString().slice(0,16);
        $('#view_file').hide();
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('NominalPinjam');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>