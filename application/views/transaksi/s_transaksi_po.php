<script>
    const $table = $('#table-po').DataTable({
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
                data: 'IDTransBeli',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NoPO',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TglPO',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NamaUsaha',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TotalNilaiBarang',
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
        "ajax": {
            "url": "<?= base_url('transaksi/transaksi_po'); ?>",
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
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpan(self, data_post);
            return false;
        });

        $("#table-po").on("click", ".btnhapus", function() {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "jika anda menghapus data transaksi pembelian, maka detail item transaksi juga akan terhapus!",
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
            IDTransBeli: kode
        }

        get_response("<?= base_url('transaksi/transaksi_po/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('transaksi/transaksi_po/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    if (response.action === 'tambah') {
                        window.location.href = "<?= base_url('transaksi/transaksi_po/detail/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                    }
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#table-po").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#IDTransBeli').val(model.IDTransBeli);
        $('#NoPO').val(model.NoPO);
        $('#TglPO').val(model.TglPO);
        $('#KodePerson').val(model.KodePerson).change();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodePerson').val('').change();
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('TglPO').value = now.toISOString().slice(0, 16);
        $('#view_file').hide();
    });
</script>