<script>
    const $table = $('#table-absensipegawai').DataTable({
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
                data: 'Tanggal',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JamKerjaMasuk',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JamKerjaPulang',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JamMasuk',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JamPulang',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Telat',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center',
                visible: false,

            },
        ],
        "rowCallback": function( row, data ) {
            if (data.Telat < 1) {
                $('td:eq(9)', row).html( '-' );
            }
        },
        "ajax": {
            "url": "<?= base_url('payroll/import_absensi'); ?>",
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
    });

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpan(self, data_post);
            return false;
        });

        $("#table-absensipegawai").on("click", ".btnhapus", function() {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "data terhapus tidak dapat di kembalikan",
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
            KodePegawai: kode
        }

        get_response("<?= base_url('payroll/import_absensi/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('payroll/import_absensi/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg).then(function() {
                    $('#ModalTambah').modal('show');
                });
            }
        });
    }

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Import Data Absensi Pegawai');
        $("#Isedit").val("tambah");
    });
</script>