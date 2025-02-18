<script>
    const $table = $('#table-tahun').DataTable({
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
                data: 'KodeTahun',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Keterangan',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'IsAktif',
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
            "url": "<?= base_url('master/tahunanggaran'); ?>",
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

            // TODO
            if ($('#isedit').val() != "false") {

                simpan(self, data_post);
            } else {
                let data = {
                    kodeLama: $('#kodaLama').val(),
                    KodeTahun: $('#KodeTahun').val()
                }

                get_response("<?= base_url('master/tahunanggaran/cekkode') ?>", data, function(response) {
                    if (response.status === false) {
                        $('#ModalTambah').modal('hide');
                        showSwal1('warning', 'Peringatan', 'Kode Tahun sudah digunakan.').then(function() {
                            $('#ModalTambah').modal('show');
                            $('#defaultModalLabel').html('Tambah Data');
                            $("#KodeTahun").prop('readonly', false);
                            $('#isedit').val(false);
                        });
                        return false;
                    } else {
                        simpan(self, data_post);
                    }
                })
            }
            return false;
        });

        $("#table-tahun").on("click", ".btnhapus", function() {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "data terhapus tidak dapat di kembalikan",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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
            KodeTahun: kode
        }

        get_response("<?= base_url('master/tahunanggaran/hapus') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Kode Tahun berhasil dihapus.');

            }
        })

    }

    function simpan(self, data_post) {
        post_response("<?= base_url('master/tahunanggaran/simpan') ?>", data_post, function(response) {
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

    $("#table-tahun").on("click", '.btnaktif', function() {
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
                KodeTahun: kode
            }

            get_response("<?= base_url('master/tahunanggaran/aktif') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', 'Kode Tahun gagal diubah.');
                    return false;
                } else {
                    $table.ajax.reload();
                    showSwal('success', 'Berhasil', 'Kode Tahun berhasil diubah.');

                }
            })
        }

    })

    $("#table-tahun").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#kodeLama').val(model.KodeTahun);
        $('#KodeTahun').val(model.KodeTahun);
        // $("#KodeTahun").datepicker("remove");
        $("#KodeTahun").prop('readonly', true)
        $('#Keterangan').html(model.Keterangan);
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
        $('#isedit').val(true)
        // $("#TanggalAkhir").datepicker("remove");
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        // $("#KodeTahun").datepicker({
        //     format: "yyyy",
        //     viewMode: "years",
        //     minViewMode: "years",
        //     autoclose: true
        // });
        $("#KodeTahun").prop('readonly', false)
        $('#Keterangan').val('')
        $('#isedit').val(false)
    });

    // $('#KodeTahun').datepicker({
    //     format: "yyyy",
    //     viewMode: "years",
    //     minViewMode: "years",
    //     autoclose: true
    // });

    // function toMMDDYYYY(date) {
    //     var datePart = date.split("-");
    //     var MMDDYYYY = [datePart[1], datePart[0], datePart[2]].join('-');
    //     return MMDDYYYY
    // }

</script>