<script>
    const $table = $('#table-so').DataTable({
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
                data: 'IDTransJual',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NoSlipOrder',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TglSlipOrder',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            // {
            //     data: 'EstimasiSelesai',
            //     className: 'text-left',
            //     "orderable": false,
            //     "searchable": false,
            // },
            {
                data: 'NamaUsaha',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Total',
                render: $.fn.dataTable.render.number('.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'StatusProses',
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
        "rowCallback": function(row, data) {
            if (!(data.NoSlipOrder)) {
                $('td:eq(2)', row).html('-');
            }
        },
        "ajax": {
            "url": "<?= base_url('transaksi/slip_order'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.tgl = $("#tgl-transaksi").val();
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#combo-status").on('change', function() {
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
        }
    );

    $(document).ready(function() {
        $("#form-simpan-so").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpan(self, data_post);
            return false;
        });

        $("#table-so").on("click", ".btnhapusso", function() {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "jika anda menghapus data transaksi penjualan, maka detail item transaksi juga akan terhapus!",
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

        $("#table-so").on("click", ".btnspk", function() {
            const kode = $(this).data('kode');
            cetak(kode);
        });
    });

    function cetak(kode) {
        let data = {
            IDTransJual: kode
        }

        get_response("<?= base_url('transaksi/slip_order/jmlproduksi') ?>", data, function(response) {
            if (response.status === false) {
                // showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                window.location.href = "<?= base_url('transaksi/slip_order/detailspk/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                // $table.ajax.reload();
                // showSwal('success', 'Informasi', 'Data berhasil dihapus.');
            }
        })
    }

    function hapus(kode) {
        let data = {
            IDTransJual: kode
        }

        get_response("<?= base_url('transaksi/slip_order/hapus') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Data berhasil dihapus.');

            }
        })

    }

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/slip_order/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambahSO').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    if (response.action === 'tambah') {
                        window.location.href = "<?= base_url('transaksi/slip_order/detail/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                    }
                });
            } else {
                $('#ModalTambahSO').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#table-so").on("click", ".btneditso", function() {
        const model = $(this).data('model');
        $('#form-simpan-so')[0].reset();
        $('#IDTransJual').val(model.IDTransJual);
        $('#NoSlipOrder').val(model.NoSlipOrder);
        $('#TglSlipOrder').val(model.TglSlipOrder);
        // $('#EstimasiSelesai').val(model.EstimasiSelesai);
        $('#KodePerson').val(model.KodePerson).change();
        $('#ModalTambahSO').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambahso").on("click", function() {
        $('#form-simpan-so')[0].reset();
        $('#ModalTambahSO').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodePerson').val('').change();
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('TglSlipOrder').value = now.toISOString().slice(0, 16);
        // document.getElementById('EstimasiSelesai').value = now.toISOString().slice(0,16);
        $('#view_file').hide();
    });
</script>