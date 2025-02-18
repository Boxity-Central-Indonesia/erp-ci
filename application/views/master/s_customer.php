<script>
    const $table = $('#table-customer').DataTable({
        "paging": true,
        "lengthChange": true,
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
        "sDom": 'flrtip',
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
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
                data: 'NamaPersonCP',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'status',
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
        "ajax": {
            "url": "<?= base_url('master/person/customer'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
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

        $("#table-customer").on("click", ".btnhapus", function() {
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
            KodePerson: kode
        }

        get_response("<?= base_url('master/person/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('master/person/simpancustomer') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                $table.ajax.reload(null, false);
                self[0].reset();
                showSwal("success", "Informasi", "Berhasil input data");
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#table-customer").on("click", '.btnaktif', function() {
        const value = $(this).data('value');
        const kode = $(this).data('kode');

        showconfirm("Apakah anda yakin " + (value > 0 ? 'mengaktifkan' : 'menonaktifkan') + " data?", '').then((result) => {
            if (result.isConfirmed) {
                aktif(value, kode);
            }
        })

        function aktif(value, kode) {
            let data = {
                IsAktif: value,
                KodePerson: kode
            }

            get_response("<?= base_url('master/person/aktif') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', 'Data customer gagal diubah.');
                    return false;
                } else {
                    $table.ajax.reload();
                    showSwal('success', 'Berhasil', 'Data customer berhasil diubah.');

                }
            })
        }

    })

    $("#table-customer").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#KodePerson').val(model.KodePerson);
        $('#NamaPersonCP').val(model.NamaPersonCP);
        $('#NoHP').val(model.NoHP);
        $('#AlamatPerson').val(model.AlamatPerson);
        $('#NamaUsaha').val(model.NamaUsaha);
        $('#Keterangan').val(model.Keterangan);
        $('#KodeManual').val(model.KodeManual);
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#view_file').hide();
    });
</script>