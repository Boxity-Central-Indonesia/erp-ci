<script>
    var statusakun = "<?= $statusakun ?>";
    var visibility = (statusakun == 'hidden') ? false : true;
    const $table = $('#table-setakundetail').DataTable({
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
                data: 'KodeAkun',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NamaAkun',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JenisJurnal',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'StatusAkun',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
                visible: visibility
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center'

            },
        ],
        "ajax": {
            "url": "<?= base_url('akuntansi/setting_akun/detail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.jabatan = $("#combo-jab").val();
                d.kodesetakun = "<?= $KodeSetAkun ?>";
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#combo-status").on('change', function() {
        $table.ajax.reload();
    });

    $("#combo-jab").on('change', function() {
        $table.ajax.reload();
    });

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            let data = {
                KodeSetAkun: $('#KodeSetAkun').val(),
                JenisJurnal: $('#JenisJurnal').val()
            }
            if ($('#action').val() === 'tambah' && $('#statusppn').val() === 'hidden') {
                get_response("<?= base_url('akuntansi/setting_akun/cekJenisJurnal') ?>", data, function(response) {
                    if (response.status === false) {
                        $('#ModalTambah').modal('hide');
                        showSwal('error', 'Peringatan', response.msg);
                        return false;
                    } else {
                        simpan(self, data_post);
                    }
                });
            } else {
                simpan(self, data_post);
            }
            return false;
        });

        $("#table-setakundetail").on("click", ".btnhapus", function() {
            const kode = $(this).data('kode');
            const kode2 = $(this).data('kode2');

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
                    hapus(kode, kode2)
                }
            })
        });
    });

    function hapus(kode, kode2) {
        let data = {
            KodeSetAkun: kode,
            NoUrut: kode2
        }

        get_response("<?= base_url('akuntansi/setting_akun/hapusdetail') ?>", data, function(response) {
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
        post_response("<?= base_url('akuntansi/setting_akun/simpandetail') ?>", data_post, function(response) {
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

    $("#table-setakundetail").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoUrut').val(model.NoUrut);
        $('#KodeSetAkun').val(model.KodeSetAkun);
        $('#JenisJurnal').attr('disabled', true);
        $('#JenisJurnal').val(model.JenisJurnal);
        $('#KodeAkun').val(model.KodeAkun).change();
        $('#StatusAkun').val(model.StatusAkun).change();
        $('#IsBank').prop('checked', model.IsBank == 1);
        $('#action').val('edit');
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#JenisJurnal').attr('disabled', false);
        $('#KodeAkun').val('').change();
        $('#StatusAkun').val('').change();
        $('#action').val('tambah');
    });
</script>