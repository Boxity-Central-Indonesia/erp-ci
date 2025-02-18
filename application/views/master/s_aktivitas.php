<script>
    // Daftar Aktivitas
    // $("#custom-tabs-aktivitas-tab").on("click", function() {
        const $table = $('#table-aktivitas').DataTable({
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
                    data: 'KodeAktivitas',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'JenisAktivitas',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'BatasBawah',
                    className: 'text-center',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'BatasAtas',
                    className: 'text-center',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'JmlDaun',
                    className: 'text-center',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'Biaya',
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                    render: $.fn.dataTable.render.number( '.', ',', 2, ),
                },
                {
                    data: 'Satuan',
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
                if (!(data.BatasBawah)) {
                    $('td:eq(3)', row).html( '-' );
                }
                if (!(data.BatasAtas)) {
                    $('td:eq(4)', row).html( '-' );
                }
                if (!(data.JmlDaun)) {
                    $('td:eq(5)', row).html( '-' );
                }
                if (!(data.Satuan)) {
                    $('td:eq(7)', row).html( '-' );
                }
            },
            "ajax": {
                "url": "<?= base_url('master/aktivitas'); ?>",
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

            $("#table-aktivitas").on("click", ".btnhapus", function() {
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
                KodeAktivitas: kode
            }

            get_response("<?= base_url('master/aktivitas/hapus') ?>", data, function(response) {
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
            post_response("<?= base_url('master/aktivitas/simpan') ?>", data_post, function(response) {
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

        $("#table-aktivitas").on("click", ".btnedit", function() {
            const model = $(this).data('model');
            $('#form-simpan')[0].reset();
            $('#KodeAktivitas').val(model.KodeAktivitas);
            $('#KodeJenisAktivitas').attr('disabled', false);
            $('#KodeJenisAktivitas').val(model.KodeJenisAktivitas).change();
            $('#KodeJenisAktivitas').change(function () {
                var kodeJns = $(this).find('option:selected').attr('value');
                console.log(kodeJns);
                $.ajax({
                    url: "<?php echo site_url('master/aktivitas/datajenis'); ?>",
                    method: "GET",
                    data: {KodeJenisAktivitas: kodeJns},
                    dataType: 'json',
                    success: function (data) {
                        if (kodeJns) {
                            $('#JenisAktivitas').val(data.JenisAktivitas);
                        } else {
                            $('#JenisAktivitas').val('');
                        }
                    }
                });
            });
            $('#JenisAktivitas').val(model.JenisAktivitas);
            $('#BatasBawah').val(model.BatasBawah);
            $('#BatasAtas').val(model.BatasAtas);
            $('#JmlDaun').val(model.JmlDaun);
            $('#Biaya').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Biaya).replace("Rp", "").trim());
            $('#Satuan').val(model.Satuan);
            $('#ModalTambah').modal('show');
            $('#defaultModalLabel').html('Edit Data');
            $("#Isedit").val("edit");
        });

        $("#btntambah").on("click", function() {
            $('#form-simpan')[0].reset();
            $('#ModalTambah').modal('show');
            $('#defaultModalLabel').html('Tambah Data');
            $('#view_file').hide();
            $('#KodeJenisAktivitas').attr('disabled', false);
            $('#KodeJenisAktivitas').val('').change();
            $('#KodeJenisAktivitas').change(function () {
                var kodeJns = $(this).find('option:selected').attr('value');
                console.log(kodeJns);
                $.ajax({
                    url: "<?php echo site_url('master/aktivitas/datajenis'); ?>",
                    method: "GET",
                    data: {KodeJenisAktivitas: kodeJns},
                    dataType: 'json',
                    success: function (data) {
                        if (kodeJns) {
                            $('#JenisAktivitas').val(data.JenisAktivitas);
                        } else {
                            $('#JenisAktivitas').val('');
                        }
                    }
                });
            });
        });
    // });

    // Daftar Jenis Aktivtas
    $("#custom-tabs-jenis-tab").on("click", function() {
        const $table = $('#table-jenis').DataTable({
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
            "bDestroy": true,
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
                    data: 'KodeJenisAktivitas',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'NoUrut',
                    className: 'text-center',
                    "orderable": false,
                    "searchable": false,
                    visible: false
                },
                {
                    data: 'JenisAktivitas',
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
            //     if (!(data.BatasBawah)) {
            //         $('td:eq(3)', row).html( '-' );
            //     }
            // },
            "ajax": {
                "url": "<?= base_url('master/aktivitas/jenis'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cari = $("#inp-search-jenis").val();
                }
            }
        });

        $('#inp-search-jenis').on('input', function(e) {
            $table.ajax.reload();
        });

        $(document).ready(function() {
            $("#form-jenis").submit(function(e) {
                e.preventDefault();
                var self = $(this)
                let data_post = new FormData(self[0]);
                simpanjenis(self, data_post);
                return false;
            });

            $("#table-jenis").on("click", ".btnhapus", function() {
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
                        hapusjenis(kode)
                    }
                })
            });
        });

        function hapusjenis(kode) {
            let data = {
                KodeJenisAktivitas: kode
            }

            get_response("<?= base_url('master/aktivitas/hapusjenis') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', response.msg);
                    return false;
                } else {
                    $table.ajax.reload();
                    showSwal('success', 'Informasi', 'Data berhasil dihapus.');

                }
            })

        }

        function simpanjenis(self, data_post) {
            post_response("<?= base_url('master/aktivitas/simpanjenis') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalJenis').modal('hide');
                    showSwal("success", "Informasi", "Berhasil input data").then(function() {
                        $table.ajax.reload(null, false);
                        self[0].reset();
                    });
                } else {
                    $('#ModalJenis').modal('hide');
                    showSwal("error", "Gagal", response.msg);
                }
            });
        }

        $("#table-jenis").on("click", ".btnedit", function() {
            const model = $(this).data('model');
            $('#form-jenis')[0].reset();
            $('#KodeJenisAktivitas2').val(model.KodeJenisAktivitas);
            $('#NoUrut').val(model.NoUrut);
            $('#JenisAktivitas2').val(model.JenisAktivitas);
            $('#ModalJenis').modal('show');
            $('#defaultModalLabel2').html('Edit Data');
            $("#Isedit").val("edit");
        });

        $("#btnjenis").on("click", function() {
            $('#form-jenis')[0].reset();
            $('#ModalJenis').modal('show');
            $('#defaultModalLabel2').html('Tambah Data');
            $('#view_file').hide();
        });
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('Biaya');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>