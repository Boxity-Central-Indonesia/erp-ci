<script>
    const $table = $('#table-ampas').DataTable({
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
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TanggalAktivitas',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'GoniAmpasDapur',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JmlAmpasDapur',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Satuan',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Biaya',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Total',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
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
            "url": "<?= base_url('transaksi/ampas_dapur'); ?>",
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
            simpan(self, data_post);
            return false;
        });

        $("#table-ampas").on("click", ".btnhapus", function() {
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
    });

    function hapus(kode) {
        let data = {
            NoTrAktivitas: kode
        }

        get_response("<?= base_url('transaksi/ampas_dapur/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('transaksi/ampas_dapur/simpan') ?>", data_post, function(response) {
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

    $("#table-ampas").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoTrAktivitas').val(model.NoTrAktivitas);
        $('#KodePegawai').val(model.KodePegawai).change();
        $('#TglAktivitas').val(model.TglAktivitas);
        $('#GoniAmpasDapur').val(model.GoniAmpasDapur);
        $('#JmlAmpasDapur').val(model.JmlAmpasDapur);
        $('#Satuan').val(model.Satuan);
        $('#Keterangan').val(model.Keterangan);
        $('#Biaya').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Biaya).replace("Rp", "").trim());
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#Satuan').val('kilogram');
        $('#KodePegawai').val('').change();
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('TglAktivitas').value = now.toISOString().substring(0, 10);
        $('#view_file').hide();
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('Biaya');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });

    var tanpa_rupiah2 = document.getElementById('JmlAmpasDapur');
    tanpa_rupiah2.addEventListener('keyup', function(e)
    {
        tanpa_rupiah2.value = formatRupiah(this.value);
    });
</script>