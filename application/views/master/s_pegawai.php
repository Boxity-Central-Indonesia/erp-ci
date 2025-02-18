<script>
    const $table = $('#table-pegawai').DataTable({
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
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JabatanAtasan',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JenisPegawai',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'IsAktif',
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
        "rowCallback": function( row, data ) {
            if (!(data.KodeJabatan)) {
                $('td:eq(3)', row).html( '-' );
            }
            if (!(data.JabatanAtasan)) {
                $('td:eq(4)', row).html( '-' );
            }
            if (!(data.JenisPegawai)) {
                $('td:eq(5)', row).html( '-' );
            }
        },
        "ajax": {
            "url": "<?= base_url('master/pegawai'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.jabatan = $("#combo-jab").val();
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
                nipLama: $('#nipLama').val(),
                emailLama: $('#emailLama').val(),
                fingerLama: $('#fingerLama').val(),
                NIP: $('#NIP').val(),
                Email: $('#Email').val(),
                IDFinger: $('#IDFinger').val(),
            }
            get_response("<?= base_url('master/pegawai/checkDB') ?>", data, function(response) {
                if (response.status === false) {
                    $('#ModalTambah').modal('hide');
                    showSwal1('warning', 'Peringatan', response.msg).then(function() {
                        $('#ModalTambah').modal('show');
                        $('#defaultModalLabel').html('Tambah Data');
                        $("#Isedit").val("tambah");
                    });
                    return false;
                } else {
                    simpan(self, data_post);
                }
            });
            return false;
        });

        $("#table-pegawai").on("click", ".btnhapus", function() {
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

        $("#form-import").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            importdata(self, data_post);
            return false;
        });
    });

    function hapus(kode) {
        let data = {
            KodePegawai: kode
        }

        get_response("<?= base_url('master/pegawai/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('master/pegawai/simpan') ?>", data_post, function(response) {
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

    $("#table-pegawai").on("click", '.btnaktif', function() {
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
                KodePegawai: kode
            }

            get_response("<?= base_url('master/pegawai/aktif') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', 'Data Pegawai gagal diubah.');
                    return false;
                } else {
                    $table.ajax.reload();
                    showSwal('success', 'Berhasil', 'Data Pegawai berhasil diubah.');

                }
            })
        }

    })

    $("#table-pegawai").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#nipLama').val(model.NIP);
        $('#emailLama').val(model.Email);
        $('#fingerLama').val(model.IDFinger);
        $('#KodePegawai').val(model.KodePegawai);
        $('#NamaPegawai').val(model.NamaPegawai);
        $('#NIP').val(model.NIP);
        $('#TTL').val(model.TTL);
        $('#Alamat').val(model.Alamat);
        $('#TelpHP').val(model.TelpHP);
        $('#IDFinger').val(model.IDFinger);
        $('#Email').val(model.Email);
        $('#TglMulaiKerja').val(model.TglMulaiKerja);
        $('#TglResign').val(model.TglResign);
        $('#KodeJabatan').val(model.KodeJabatan).change();
        $('#KodeJabAtasanLangsung').val(model.KodeJabAtasanLangsung).change();
        $('#KodeBank').val(model.KodeBank).change();
        $('#NoRek').val(model.NoRek);
        $('#GajiPokok').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.GajiPokok).replace("Rp", "").trim());
        $('#JenisPegawai').val(model.JenisPegawai);
        $("#JenisPegawai").attr('disabled', model.JenisPegawai == 'Gaji Harian');
        $("#IsGajiHarian").prop("checked", model.IsGajiHarian == 1);
        $("#IsGajiHarian").change(function() {
            var mjenispegawai = (model.JenisPegawai == 'Gaji Harian') ? '' : model.JenisPegawai;
            var setval = this.checked ? "Gaji Harian" : mjenispegawai;
            $("#JenisPegawai").val(setval);
            console.log(setval);
            if (setval == 'Gaji Harian') {
                $("#JenisPegawai").attr('disabled', true);
                $("#JenisPegawai").attr('required', false);
                $("#JenisPegawai2").val('Gaji Harian');
            } else {
                $("#JenisPegawai").attr('disabled', false);
                $("#JenisPegawai").attr('required', true);
                $("#JenisPegawai2").val('');
            }
        });
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
        $("#Isedit").val("edit");
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodeJabatan').val('').change();
        $('#KodeJabAtasanLangsung').val('').change();
        $('#KodeBank').val('').change();
        $("#Isedit").val("tambah");
        $("#IsGajiHarian").change(function() {
            var setval = this.checked ? "Gaji Harian" : "";
            $("#JenisPegawai").val(setval);
            if (setval == 'Gaji Harian') {
                $("#JenisPegawai").attr('disabled', true);
                $("#JenisPegawai").attr('required', false);
                $("#JenisPegawai2").val('Gaji Harian');
            } else {
                $("#JenisPegawai").attr('disabled', false);
                $("#JenisPegawai").attr('required', true);
                $("#JenisPegawai2").val('');
            }
        });
    });

    function importdata(self, data_post) {
        post_response("<?= base_url('master/pegawai/import') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalImport').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                });
            } else {
                $('#ModalImport').modal('hide');
                showSwal("error", "Gagal", response.msg).then(function() {
                    $('#ModalImport').modal('show');
                });
            }
        });
    }

    $("#btnimport").on("click", function() {
        $('#form-import')[0].reset();
        $('#ModalImport').modal('show');
        $('#defaultModalLabel2').html('Import Data Pegawai');
        $("#Isedit").val("tambah");
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('GajiPokok');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>