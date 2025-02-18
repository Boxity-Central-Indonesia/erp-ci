<script>
    const $table = $('#table-adjustment').DataTable({
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
                data: 'Tgl',
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
                data: 'Keterangan',
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
            "url": "<?= base_url('payroll/adjustment_absensi'); ?>",
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
            if ($('#Isedit').val() === 'edit') {
                simpan(self, data_post);
            } else {
                let data = {
                    Tanggal: $('#Tanggal').val(),
                    KodePegawai: $('#KodePegawai').val(),
                }
                get_response("<?= base_url('payroll/adjustment_absensi/checkDB') ?>", data, function(response) {
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
            }
            return false;
        });

        $("#table-adjustment").on("click", ".btnhapus", function() {
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
            KodePegawai: kode,
            Tanggal: kode2
        }

        get_response("<?= base_url('payroll/adjustment_absensi/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('payroll/adjustment_absensi/simpan') ?>", data_post, function(response) {
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

    $("#table-adjustment").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#Isedit').val('edit');
        $('#kodepegLama').val(model.KodePegawai);
        $('#KodePegawai').attr('disabled', true);
        $('#KodePegawai').val(model.KodePegawai).change();
        $('#IDFinger').val(model.IDFinger);
        $('#NamaJabatan').val(model.NamaJabatan);
        $('#Tanggal').attr('readonly', true);
        $('#Tanggal').val(model.Tanggal);
        $('#JamKerjaMasuk').val(model.JamKerjaMasuk);
        $('#JamKerjaPulang').val(model.JamKerjaPulang);
        $('#JamMasuk').val(model.JamMasuk);
        $('#JamPulang').val(model.JamPulang);
        $('#Keterangan').val(model.Keterangan);
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#Isedit').val('tambah');
        $('#Tanggal').attr('readonly', false);
        $('#KodePegawai').attr('disabled', false);
        $('#KodePegawai').val('').change();
        $('#KodePegawai').change(function () {
            var kodepeg = $(this).find('option:selected').attr('value');
            console.log(kodepeg);
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataPegawai'); ?>",
                method: "GET",
                data: {KodePegawai: kodepeg},
                dataType: 'json',
                success: function (data) {
                    if (kodepeg) {
                        $('#NamaJabatan').val(data.NamaJabatan);
                        $('#IDFinger').val(data.IDFinger);
                    } else {
                        $('#NamaJabatan').val('');
                        $('#IDFinger').val('');
                    }
                }
            });
        });
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('Tanggal').value = now.toISOString().substring(0, 10);
    });
</script>