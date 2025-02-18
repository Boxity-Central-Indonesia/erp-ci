<script>
    // Master Barang
    // $("#custom-tabs-barang-tab").on("click", function() {
        const $table = $('#table-barang').DataTable({
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
            "bDestroy": true,
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
                    data: 'KodeBarang',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'KodeManual',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'NamaBarang',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'NamaJenisBarang',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'NamaKategori',
                    className: 'text-center',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'hargabeli',
                    render: $.fn.dataTable.render.number( '.', ',', 2, ),
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'hargajual',
                    render: $.fn.dataTable.render.number( '.', ',', 2, ),
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'hpp',
                    render: $.fn.dataTable.render.number( '.', ',', 2, ),
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'stokasli',
                    render: $.fn.dataTable.render.number( '.', ',', 2, ),
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'hppbalance',
                    render: $.fn.dataTable.render.number( '.', ',', 2, ),
                    className: 'text-right',
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
                if (!(data.KodeManual)) {
                    $('td:eq(2)', row).html( '-' );
                }
                if (!(data.KodeKategori)) {
                    $('td:eq(5)', row).html( '-' );
                }
            },
            "ajax": {
                "url": "<?= base_url('master/barang'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cari = $("#inp-search-brg").val();
                    d.isaktif = $("#combo-status-brg").val();
                    d.jenis = $("#combo-jenis-brg").val();
                    d.kategori = $("#combo-kategori-brg").val();
                }
            }
        });

        $('#inp-search-brg').on('input', function(e) {
            $table.ajax.reload();
        });

        $("#combo-status-brg").on('change', function() {
            $table.ajax.reload();
        });

        $("#combo-jenis-brg").on('change', function() {
            $table.ajax.reload();
        });

        $("#combo-kategori-brg").on('change', function() {
            $table.ajax.reload();
        });

        $(document).ready(function() {
            $("#table-barang").on("click", ".btnhapus", function() {
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

            $("#table-barang").on("click", ".btnedit", function() {
                const kode = $(this).data('kode');
                qr_bar_code(kode);
            });
        });

        function qr_bar_code(kode) {
            let data = {
                KodeBarang: kode
            }

            get_response("<?= base_url('master/barang/create_qr_bar') ?>", data, function(response) {
                if (response.status === false) {
                    // showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                    return false;
                } else {
                    $table.ajax.reload();
                    window.location.href = "<?= base_url('master/barang/edit/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                    // showSwal('success', 'Informasi', 'Data berhasil dihapus.');

                }
            })
        }

        function hapus(kode) {
            let data = {
                KodeBarang: kode
            }

            get_response("<?= base_url('master/barang/hapus') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                    return false;
                } else {
                    $table.ajax.reload();
                    showSwal('success', 'Informasi', 'Data berhasil dihapus.');

                }
            })

        }

        $("#table-barang").on("click", '.btnaktif', function() {
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
                    KodeBarang: kode
                }

                get_response("<?= base_url('master/barang/aktif') ?>", data, function(response) {
                    if (response.status === false) {
                        showSwal('error', 'Peringatan', 'Data Barang gagal diubah.');
                        return false;
                    } else {
                        $table.ajax.reload();
                        showSwal('success', 'Berhasil', 'Data Barang berhasil diubah.');

                    }
                })
            }

        })
    // });

    // Master Jenis Barang
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
            "sDom": 'flrtip',
            "bDestroy": true,
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
                    data: 'NamaJenisBarang',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'Deskripsi',
                    className: 'text-left',
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
                if (!(data.Deskripsi)) {
                    $('td:eq(2)', row).html( '-' );
                }
            },
            "ajax": {
                "url": "<?= base_url('master/jenisbarang'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cari = $("#inp-search-jenis").val();
                    d.isaktif = $("#combo-status-jenis").val();
                }
            }
        });

        $('#inp-search-jenis').on('input', function(e) {
            $table.ajax.reload();
        });

        $("#combo-status-jenis").on('change', function() {
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
                KodeJenis: kode
            }

            get_response("<?= base_url('master/jenisbarang/hapus') ?>", data, function(response) {
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
            post_response("<?= base_url('master/jenisbarang/simpan') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalTambahJns').modal('hide');
                    showSwal("success", "Informasi", "Berhasil input data").then(function() {
                        $table.ajax.reload(null, false);
                        self[0].reset();
                    });
                } else {
                    $('#ModalTambahJns').modal('hide');
                    showSwal("error", "Gagal", response.msg);
                }
            });
        }

        $("#table-jenis").on("click", '.btnaktif', function() {
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
                    KodeJenis: kode
                }

                get_response("<?= base_url('master/jenisbarang/aktif') ?>", data, function(response) {
                    if (response.status === false) {
                        showSwal('error', 'Peringatan', 'Data Jenis Barang gagal diubah.');
                        return false;
                    } else {
                        $table.ajax.reload();
                        showSwal('success', 'Berhasil', 'Data Jenis Barang berhasil diubah.');

                    }
                })
            }

        })

        $("#table-jenis").on("click", ".btnedit", function() {
            const model = $(this).data('model');
            $('#form-jenis')[0].reset();
            $('#KodeJenis').val(model.KodeJenis);
            $('#NamaJenisBarang').val(model.NamaJenisBarang);
            $('#Deskripsi').val(model.Deskripsi);
            $('#ModalTambahJns').modal('show');
            $('#defaultModalLabel').html('Edit Data');
            $("#Isedit").val("edit");
        });

        $("#btntambahjenis").on("click", function() {
            $('#form-jenis')[0].reset();
            $('#ModalTambahJns').modal('show');
            $('#defaultModalLabel').html('Tambah Data');
            $('#view_file').hide();
        });
    });

    // Master Kategori Barang
    $("#custom-tabs-kategori-tab").on("click", function() {
        const $table = $('#table-kategori').DataTable({
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
            "bDestroy": true,
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
                    data: 'NamaKategori',
                    className: 'text-left',
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
            "ajax": {
                "url": "<?= base_url('master/kategori'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cari = $("#inp-search-ktg").val();
                    d.isaktif = $("#combo-status-ktg").val();
                }
            }
        });

        $('#inp-search-ktg').on('input', function(e) {
            $table.ajax.reload();
        });

        $("#combo-status-ktg").on('change', function() {
            $table.ajax.reload();
        });

        $(document).ready(function() {
            $("#form-kategori").submit(function(e) {
                e.preventDefault();
                var self = $(this)
                let data_post = new FormData(self[0]);
                simpan(self, data_post);
                return false;
            });

            $("#table-kategori").on("click", ".btnhapus", function() {
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
                KodeKategori: kode
            }

            get_response("<?= base_url('master/kategori/hapus') ?>", data, function(response) {
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
            post_response("<?= base_url('master/kategori/simpan') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalTambahKtg').modal('hide');
                    showSwal("success", "Informasi", "Berhasil input data").then(function() {
                        $table.ajax.reload(null, false);
                        self[0].reset();
                    });
                } else {
                    $('#ModalTambahKtg').modal('hide');
                    showSwal("error", "Gagal", response.msg);
                }
            });
        }

        $("#table-kategori").on("click", '.btnaktif', function() {
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
                    KodeKategori: kode
                }

                get_response("<?= base_url('master/kategori/aktif') ?>", data, function(response) {
                    if (response.status === false) {
                        showSwal('error', 'Peringatan', 'Data Kategori Barang gagal diubah.');
                        return false;
                    } else {
                        $table.ajax.reload();
                        showSwal('success', 'Berhasil', 'Data Kategori Barang berhasil diubah.');

                    }
                })
            }

        })

        $("#table-kategori").on("click", ".btnedit", function() {
            const model = $(this).data('model');
            $('#form-kategori')[0].reset();
            $('#KodeKategori').val(model.KodeKategori);
            $('#NamaKategori').val(model.NamaKategori);
            $('#ModalTambahKtg').modal('show');
            $('#defaultModalLabel2').html('Edit Data');
            $("#Isedit").val("edit");
        });

        $("#btntambahktg").on("click", function() {
            $('#form-kategori')[0].reset();
            $('#ModalTambahKtg').modal('show');
            $('#defaultModalLabel2').html('Tambah Data');
            $('#view_file').hide();
        });
    });
</script>