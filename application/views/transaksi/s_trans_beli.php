<script>
    var poview = "<?= $poview ?>";
    var approvalview = "<?= $approvalview ?>";
    var transbeliview = "<?= $transbeliview ?>";
    var returview = "<?= $returview ?>";

    // Pre Order
    if (poview == 1) {
        // $("#custom-tabs-po-tab").on("click", function() {
        const $tablepo = $('#table-po').DataTable({
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
            "bDestroy": true,
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
                    data: 'NamaPersonCP',
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
                    data: 'UserPO',
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
                "url": "<?= base_url('transaksi/transaksi_po'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.caripo = $("#inp-search-po").val();
                    d.tglpo = $("#tgl-transaksi-po").val();
                }
            }
        });

        $('#inp-search-po').on('input', function(e) {
            $tablepo.ajax.reload();
        });

        $('#tgl-transaksi-po').daterangepicker({
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
                $('#tgl-transaksi-po').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
                $tablepo.ajax.reload();
            }
        );

        $(document).ready(function() {
            $("#form-simpan-po").submit(function(e) {
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
                    $tablepo.ajax.reload();
                    showSwal('success', 'Informasi', 'Data berhasil dihapus.');

                }
            })

        }

        function simpan(self, data_post) {
            post_response("<?= base_url('transaksi/transaksi_po/simpan') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalTambahPO').modal('hide');
                    showSwal("success", "Informasi", "Berhasil input data").then(function() {
                        $tablepo.ajax.reload(null, false);
                        self[0].reset();
                        if (response.action === 'tambah') {
                            window.location.href = "<?= base_url('transaksi/transaksi_po/detail/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                        }
                    });
                } else {
                    $('#ModalTambahPO').modal('hide');
                    showSwal("error", "Gagal", response.msg);
                }
            });
        }

        $("#table-po").on("click", ".btneditpo", function() {
            const model = $(this).data('model');
            $('#form-simpan-po')[0].reset();
            $('#IDTransBeli').val(model.IDTransBeli);
            $('#NoPO').val(model.NoPO);
            $('#TglPO').val(model.TglPO);
            $('#KodePerson').val(model.KodePerson).change();
            $('#ModalTambahPO').modal('show');
            $('#defaultModalLabel').html('Edit Data');
        });

        $("#btntambahpo").on("click", function() {
            $('#form-simpan-po')[0].reset();
            $('#ModalTambahPO').modal('show');
            $('#defaultModalLabel').html('Tambah Data');
            $('#KodePerson').val('').change();
            var now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('TglPO').value = now.toISOString().slice(0, 16);
            $('#view_file').hide();
        });
        // });
    }


    // Approval PO
    $("#custom-tabs-approval-tab").on("click", function() {
        const $tableapproval = $('#table-approval').DataTable({
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
            "bDestroy": true,
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
                    data: 'NamaPersonCP',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'TotalTagihan',
                    render: $.fn.dataTable.render.number('.', ',', 2, ),
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'ApprovedNo',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'ApprovedBy',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'ApprovedDate',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'ApprovedDesc',
                    className: 'text-left',
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
                    data: 'UserPO',
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
                "url": "<?= base_url('transaksi/approval_pembelian'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cariapv = $("#inp-search-approval").val();
                    d.statusapv = $("#combo-status-approval").val();
                    d.tglapv = $("#tgl-transaksi-approval").val();
                }
            }
        });

        $("#combo-status-approval").on('change', function() {
            $tableapproval.ajax.reload();
        });

        $('#inp-search-approval').on('input', function(e) {
            $tableapproval.ajax.reload();
        });

        $('#tgl-transaksi-approval').daterangepicker({
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
                $('#tgl-transaksi-approval').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
                $tableapproval.ajax.reload();
            }
        );
    });


    // Transaksi Pembelian
    $("#custom-tabs-tbl-tab").on("click", function() {
        const $tabletbl = $('#table-pembelian').DataTable({
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
            "bDestroy": true,
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
                    data: 'NoRef_Manual',
                    className: 'text-center',
                    "orderable": false,
                    "searchable": false,
                    visible: false,
                },
                {
                    data: 'TanggalPembelian',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'NamaPersonCP',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'TotalTagihan',
                    render: $.fn.dataTable.render.number('.', ',', 2, ),
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'TotalBayar',
                    render: $.fn.dataTable.render.number('.', ',', 2, ),
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'StatusBayar',
                    className: 'text-center',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'UserPO',
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
            "rowCallback": function(row, data) {
                if (!(data.NoRef_Manual)) {
                    // $('td:eq(2)', row).html( '-' );
                }
                if (!(data.UserPO)) {
                    $('td:eq(7)', row).html('-');
                }
            },
            "ajax": {
                "url": "<?= base_url('transaksi/transaksi_pembelian'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.caribeli = $("#inp-search-beli").val();
                    d.tglbeli = $("#tgl-transaksi-beli").val();
                }
            }
        });

        $('#inp-search-beli').on('input', function(e) {
            $tabletbl.ajax.reload();
        });

        $('#tgl-transaksi-beli').daterangepicker({
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
                $('#tgl-transaksi-beli').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
                $tabletbl.ajax.reload();
            }
        );

        $(document).ready(function() {
            $("#form-simpan-beli").submit(function(e) {
                e.preventDefault();
                var self = $(this)
                let data_post = new FormData(self[0]);
                let data = {
                    NoRef_Manual: $('#NoRef_Manual').val()
                }
                get_response("<?= base_url('transaksi/transaksi_pembelian/checkManualCode') ?>", data, function(response) {
                    if (response.status === false) {
                        $('#ModalTambahTBL').modal('hide');
                        showSwal1('warning', 'Peringatan', response.msg).then(function() {
                            $('#ModalTambahTBL').modal('show');
                            $('#defaultModalLabel').html('Tambah Data');
                        });
                        return false;
                    } else {
                        simpantbl(self, data_post);
                    }
                });
                return false;
            });

            $("#table-pembelian").on("click", ".btnhapus", function() {
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
                        hapustbl(kode)
                    }
                })
            });
        });

        function hapustbl(kode) {
            let data = {
                IDTransBeli: kode
            }

            get_response("<?= base_url('transaksi/transaksi_pembelian/hapus') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                    return false;
                } else {
                    $tabletbl.ajax.reload();
                    showSwal('success', 'Informasi', 'Data berhasil dihapus.');

                }
            })

        }

        function simpantbl(self, data_post) {
            post_response("<?= base_url('transaksi/transaksi_pembelian/simpan') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalTambahTBL').modal('hide');
                    showSwal("success", "Informasi", "Berhasil input data").then(function() {
                        $tabletbl.ajax.reload(null, false);
                        self[0].reset();
                        window.location.href = "<?= base_url('transaksi/transaksi_pembelian/detail/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                    });
                } else {
                    $('#ModalTambahTBL').modal('hide');
                    showSwal("error", "Gagal", response.msg);
                }
            });
        }

        $("#btntambahbeli").on("click", function() {
            $('#form-simpan-beli')[0].reset();
            $('#ModalTambahTBL').modal('show');
            $('#defaultModalLabel').html('Tambah Data');
            $('#KodePerson2').attr('disabled', false);
            $('#KodePerson2').val('').change();
            $('#IDTransBeli2').attr('disabled', false);
            $('#IDTransBeli2').val('').change();
            $('#IDTransBeli2').select2({
                placeholder: "Pilih Kode PO",
                theme: 'bootstrap4',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    delay: 250,
                    url: '<?php echo base_url('user/Lokasi/DataPO'); ?>',
                    data: function(params) {
                        return {
                            searchTerm: params.term
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(obj) {
                                return {
                                    id: obj.IDTransBeli,
                                    text: obj.IDTransBeli + ' | ' + obj.NoPO
                                };
                            })
                        };
                    }
                }
            });
            $('#IDTransBeli2').change(function() {
                var idtransbeli = $(this).find('option:selected').attr('value');
                console.log(idtransbeli);
                $.ajax({
                    url: "<?php echo site_url('user/Lokasi/DataSupplier'); ?>",
                    method: "GET",
                    data: {
                        IDTransBeli: idtransbeli
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (idtransbeli) {
                            $('#KodePerson2').val(data.KodePerson).change();
                            $('#KodePerson2').attr('disabled', true);
                        } else {
                            $('#KodePerson2').val('').change();
                            $('#KodePerson2').attr('disabled', false);
                        }
                    }
                });
            });
            var now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('TanggalPembelian').value = now.toISOString().slice(0, 16);
            $('#view_file').hide();
        });
    });


    // Retur Pembelian
    $("#custom-tabs-rtr-tab").on("click", function() {
        const $tableretur = $('#table-retur').DataTable({
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
            "bDestroy": true,
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
                    data: 'IDTransRetur',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'IDTrans',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'TanggalTransaksi',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'NamaPersonCP',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'TotalRetur',
                    render: $.fn.dataTable.render.number('.', ',', 2, ),
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'NamaGudang',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'ActualName',
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
            "rowCallback": function(row, data) {
                if (!(data.ActualName)) {
                    $('td:eq(7)', row).html('-');
                }
            },
            "ajax": {
                "url": "<?= base_url('transaksi/retur_pembelian'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cari = $("#inp-search-retur").val();
                    d.tgl = $("#tgl-transaksi-retur").val();
                }
            }
        });

        $('#inp-search-retur').on('input', function(e) {
            $tableretur.ajax.reload();
        });

        $('#tgl-transaksi-retur').daterangepicker({
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
                $('#tgl-transaksi-retur').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
                $tableretur.ajax.reload();
            }
        );

        $(document).ready(function() {
            $("#form-simpan-retur").submit(function(e) {
                e.preventDefault();
                var self = $(this)
                let data_post = new FormData(self[0]);
                simpanretur(self, data_post);
                return false;
            });

            $("#table-retur").on("click", ".btnhapusretur", function() {
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
                        hapusretur(kode)
                    }
                })
            });
        });

        function hapusretur(kode) {
            let data = {
                IDTransRetur: kode
            }

            get_response("<?= base_url('transaksi/retur_pembelian/hapus') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                    return false;
                } else {
                    $tableretur.ajax.reload();
                    showSwal('success', 'Informasi', 'Data berhasil dihapus.');

                }
            })

        }

        function simpanretur(self, data_post) {
            post_response("<?= base_url('transaksi/retur_pembelian/simpan') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalTambahRet').modal('hide');
                    showSwal("success", "Informasi", "Berhasil input data").then(function() {
                        $tableretur.ajax.reload(null, false);
                        self[0].reset();
                        window.location.href = "<?= base_url('transaksi/retur_pembelian/detail/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                    });
                } else {
                    $('#ModalTambahRet').modal('hide');
                    showSwal("error", "Gagal", response.msg);
                }
            });
        }

        $("#btntambahretur").on("click", function() {
            $('#form-simpan-retur')[0].reset();
            $('#ModalTambahRet').modal('show');
            $('#defaultModalLabel').html('Tambah Data');
            $('#KodeGudangRet').attr('disabled', false);
            $('#KodeGudangRet').val('').change();
            $('#IDTrans').attr('disabled', false);
            $('#IDTrans').val('').change();
            $('#IDTrans').change(function() {
                var idtransbeli = $(this).find('option:selected').attr('value');
                // console.log(idtransbeli);
                $.ajax({
                    url: "<?php echo site_url('user/Lokasi/DataSupplier'); ?>",
                    method: "GET",
                    data: {
                        IDTransBeli: idtransbeli
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (idtransbeli) {
                            $('#KodePerson4').val(data.KodePerson);
                            $('#KodePersonView').val(data.KodePerson + ' | ' + data.NamaPersonCP);
                            $('#TglPembelian').val(data.TanggalPembelian);
                        } else {
                            $('#KodePerson4').val('');
                            $('#KodePersonView').val('');
                            $('#TglPembelian').val('');
                        }
                    }
                });
            });
            var now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('TanggalTransaksiRet').value = now.toISOString().slice(0, 16);
            $('#view_file').hide();
        });
    });


    // Transaksi Hutang
    $("#custom-tabs-hutang-tab").on("click", function() {
        const $table = $('#table-bayarhutang').DataTable({
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
            "bDestroy": true,
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
                    data: 'NoRef_Manual',
                    className: 'text-center',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'TanggalPembelian',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'NamaPersonCP',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'TotalTagihan',
                    render: $.fn.dataTable.render.number('.', ',', 2, ),
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'TotalBayar',
                    render: $.fn.dataTable.render.number('.', ',', 2, ),
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'SisaTagihan',
                    render: $.fn.dataTable.render.number('.', ',', 2, ),
                    className: 'text-right',
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
                if (!(data.NoRef_Manual)) {
                    $('td:eq(2)', row).html('-');
                }
            },
            "ajax": {
                "url": "<?= base_url('transaksi/bayar_hutang'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.carihutang = $("#inp-search-hutang").val();
                    d.tglhutang = $("#tgl-transaksi-hutang").val();
                }
            }
        });

        $('#inp-search-hutang').on('input', function(e) {
            $table.ajax.reload();
        });

        $('#tgl-transaksi-hutang').daterangepicker({
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
                $('#tgl-transaksi-hutang').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
                $table.ajax.reload();
            }
        );

        $(document).ready(function() {
            $("#form-simpan-hutang").submit(function(e) {
                e.preventDefault();
                var self = $(this)
                let data_post = new FormData(self[0]);
                let data = {
                    NoRef_Manual: $('#NoRef_Manual2').val()
                }
                get_response("<?= base_url('transaksi/bayar_hutang/checkManualCode') ?>", data, function(response) {
                    if (response.status === false) {
                        $('#ModalTambahHT').modal('hide');
                        showSwal1('warning', 'Peringatan', response.msg).then(function() {
                            $('#ModalTambahHT').modal('show');
                            $('#defaultModalLabel').html('Tambah Data');
                        });
                        return false;
                    } else {
                        simpanhutang(self, data_post);
                    }
                });
                return false;
            });

            $("#table-bayarhutang").on("click", ".btnhapus", function() {
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
                        hapushutang(kode)
                    }
                })
            });
        });

        function hapushutang(kode) {
            let data = {
                IDTransBeli: kode
            }

            get_response("<?= base_url('transaksi/bayar_hutang/hapus') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                    return false;
                } else {
                    $table.ajax.reload();
                    showSwal('success', 'Informasi', 'Data berhasil dihapus.');

                }
            })

        }

        function simpanhutang(self, data_post) {
            post_response("<?= base_url('transaksi/bayar_hutang/simpan') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalTambahHT').modal('hide');
                    showSwal("success", "Informasi", "Berhasil input data").then(function() {
                        $table.ajax.reload(null, false);
                        self[0].reset();
                        window.location.href = "<?= base_url('transaksi/bayar_hutang/tambah/') ?>" + btoa(unescape(encodeURIComponent(response.id))) + '/' + btoa(unescape(encodeURIComponent(response.id2)));
                    });
                } else {
                    $('#ModalTambahHT').modal('hide');
                    showSwal("error", "Gagal", response.msg);
                }
            });
        }

        $("#btntambahhutang").on("click", function() {
            $('#form-simpan-hutang')[0].reset();
            $('#ModalTambahHT').modal('show');
            $('#defaultModalLabel').html('Tambah Data');
            $('#KodePerson3').attr('disabled', false);
            $('#KodePerson3').val('').change();
            var now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('TanggalTransaksi').value = now.toISOString().slice(0, 16);
            $('#view_file').hide();
        });
    });

    $(document).ready(function() {
        if (poview == 0 && approvalview == 1) {
            $("#custom-tabs-approval-tab").trigger("click");
        }
        if (poview == 0 && approvalview == 0 && transbeliview == 1) {
            $("#custom-tabs-tbl-tab").trigger("click");
        }
        if (poview == 0 && approvalview == 0 && transbeliview == 0 && returview == 1) {
            $("#custom-tabs-rtr-tab").trigger("click");
        }
    });
</script>