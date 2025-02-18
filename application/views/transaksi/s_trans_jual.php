<script>
    var sliporderview = "<?= $sliporderview ?>";
    var quotationview = "<?= $quotationview ?>";
    var transjualview = "<?= $transjualview ?>";
    var returview = "<?= $returview ?>";

    // Slip Order
    if (sliporderview == 1) {
        // $("#custom-tabs-so-tab").on("click", function() {
        const $tableso = $('#table-so').DataTable({
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
                    data: 'IDTransJual',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'NoSlipOrder',
                    className: 'text-center',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'TglSlipOrder',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                // {
                //     data: 'EstimasiSelesai',
                //     className: 'text-left',
                //     "orderable": false,
                //     "searchable": false,
                // },
                {
                    data: 'NamaPersonCP',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'Total',
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
                    data: 'SODibuatOleh',
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
            "rowCallback": function(row, data) {
                if (!(data.NoSlipOrder)) {
                    $('td:eq(2)', row).html('-');
                }
            },
            "ajax": {
                "url": "<?= base_url('transaksi/slip_order'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cariso = $("#inp-search-so").val();
                    d.tglso = $("#tgl-transaksi-so").val();
                }
            }
        });

        $('#inp-search-so').on('input', function(e) {
            $tableso.ajax.reload();
        });

        $('#tgl-transaksi-so').daterangepicker({
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
                $('#tgl-transaksi-so').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
                $tableso.ajax.reload();
            }
        );

        $(document).ready(function() {
            $("#form-simpan-so").submit(function(e) {
                e.preventDefault();
                var self = $(this)
                let data_post = new FormData(self[0]);
                simpanso(self, data_post);
                return false;
            });

            $("#table-so").on("click", ".btnhapusso", function() {
                const kode = $(this).data('kode');

                Swal.fire({
                    title: 'Apa anda yakin?',
                    text: "jika anda menghapus data transaksi penjualan, maka detail item transaksi juga akan terhapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FA7C41',
                    cancelButtonColor: '#FA7C41',
                    confirmButtonText: 'Ya, Hapus data!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        hapusso(kode)
                    }
                })
            });

            $("#table-so").on("click", ".btnspk", function() {
                const kode = $(this).data('kode');
                cetak(kode);
            });
        });

        function cetak(kode) {
            let data = {
                IDTransJual: kode
            }

            get_response("<?= base_url('transaksi/slip_order/jmlproduksi') ?>", data, function(response) {
                if (response.status === false) {
                    // showSwal('error', 'Peringatan', response.msg);
                    return false;
                } else {
                    window.location.href = "<?= base_url('transaksi/slip_order/detailspk/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                    // showSwal('success', 'Informasi', 'Data berhasil dihapus.');
                }
            })
        }

        function hapusso(kode) {
            let data = {
                IDTransJual: kode
            }

            get_response("<?= base_url('transaksi/slip_order/hapus') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', response.msg);
                    return false;
                } else {
                    $tableso.ajax.reload();
                    showSwal('success', 'Informasi', 'Data berhasil dihapus.');

                }
            })

        }

        function simpanso(self, data_post) {
            post_response("<?= base_url('transaksi/slip_order/simpan') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalTambahSO').modal('hide');
                    showSwal("success", "Informasi", "Berhasil input data").then(function() {
                        $tableso.ajax.reload(null, false);
                        self[0].reset();
                        if (response.action === 'tambah') {
                            window.location.href = "<?= base_url('transaksi/slip_order/detail/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                        }
                    });
                } else {
                    $('#ModalTambahSO').modal('hide');
                    showSwal("error", "Gagal", response.msg);
                }
            });
        }

        $("#table-so").on("click", ".btneditso", function() {
            const model = $(this).data('model');
            $('#form-simpan-so')[0].reset();
            $('#IDTransJual').val(model.IDTransJual);
            $('#NoSlipOrder').val(model.NoSlipOrder);
            $('#TglSlipOrder').val(model.TglSlipOrder);
            // $('#EstimasiSelesai').val(model.EstimasiSelesai);
            $('#KodePerson').val(model.KodePerson).change();
            $('#KodeGudangSO').val(model.KodeGudang).change();
            $('#ModalTambahSO').modal('show');
            $('#defaultModalLabel').html('Edit Data');
        });

        $("#btntambahso").on("click", function() {
            $('#form-simpan-so')[0].reset();
            $('#ModalTambahSO').modal('show');
            $('#defaultModalLabel').html('Tambah Data');
            $('#KodePerson').val('').change();
            $('#KodeGudangSO').val('').change();
            var now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('TglSlipOrder').value = now.toISOString().slice(0, 16);
            // document.getElementById('EstimasiSelesai').value = now.toISOString().slice(0,16);
            $('#view_file').hide();
        });
        // });
    }


    // Quotation and Invoice
    $("#custom-tabs-quotation-tab").on("click", function() {
        const $tableqi = $('#table-qi').DataTable({
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
                    data: 'IDTransJual',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'NoSlipOrder',
                    className: 'text-center',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'TglSlipOrder',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                // {
                //     data: 'EstimasiSelesai',
                //     className: 'text-left',
                //     "orderable": false,
                //     "searchable": false,
                // },
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
                    data: 'SODibuatOleh',
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
                if (!(data.NoSlipOrder)) {
                    $('td:eq(2)', row).html('-');
                }
            },
            "ajax": {
                "url": "<?= base_url('transaksi/quotation_invoice'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cariqi = $("#inp-search-qi").val();
                    d.tglqi = $("#tgl-transaksi-qi").val();
                }
            }
        });

        $('#inp-search-qi').on('input', function(e) {
            $tableqi.ajax.reload();
        });

        $('#tgl-transaksi-qi').daterangepicker({
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
                $('#tgl-transaksi-qi').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
                $tableqi.ajax.reload();
            }
        );
    });


    // Transaksi Penjualan
    $("#custom-tabs-tjl-tab").on("click", function() {
        const $tabletjl = $('#table-penjualan').DataTable({
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
                    data: 'IDTransJual',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'NoRef_Manual',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                    visible: false,
                },
                {
                    data: 'TanggalPenjualan',
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
                    data: 'SODibuatOleh',
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
                if (!(data.SODibuatOleh)) {
                    $('td:eq(7)', row).html('-');
                }
            },
            "ajax": {
                "url": "<?= base_url('transaksi/transaksi_penjualan'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cari = $("#inp-search-tjl").val();
                    d.tgl = $("#tgl-transaksi-tjl").val();
                }
            }
        });

        $('#inp-search-tjl').on('input', function(e) {
            $tabletjl.ajax.reload();
        });

        $('#tgl-transaksi-tjl').daterangepicker({
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
                $('#tgl-transaksi-tjl').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
                $tabletjl.ajax.reload();
            }
        );

        $(document).ready(function() {
            $("#form-simpan-tjl").submit(function(e) {
                e.preventDefault();
                var self = $(this)
                let data_post = new FormData(self[0]);
                let data = {
                    NoRef_Manual: $('#NoRef_Manual').val()
                }
                get_response("<?= base_url('transaksi/transaksi_penjualan/checkManualCode') ?>", data, function(response) {
                    if (response.status === false) {
                        $('#ModalTambahTJL').modal('hide');
                        showSwal1('warning', 'Peringatan', response.msg).then(function() {
                            $('#ModalTambahTJL').modal('show');
                            $('#defaultModalLabel').html('Tambah Data');
                        });
                        return false;
                    } else {
                        simpantjl(self, data_post);
                    }
                });
                return false;
            });

            $("#table-penjualan").on("click", ".btnhapustjl", function() {
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
                        hapustjl(kode)
                    }
                })
            });
        });

        function hapustjl(kode) {
            let data = {
                IDTransJual: kode
            }

            get_response("<?= base_url('transaksi/transaksi_penjualan/hapus') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                    return false;
                } else {
                    $tabletjl.ajax.reload();
                    showSwal('success', 'Informasi', 'Data berhasil dihapus.');

                }
            })

        }

        function simpantjl(self, data_post) {
            post_response("<?= base_url('transaksi/transaksi_penjualan/simpan') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalTambahTJL').modal('hide');
                    showSwal("success", "Informasi", "Berhasil input data").then(function() {
                        $tabletjl.ajax.reload(null, false);
                        self[0].reset();
                        window.location.href = "<?= base_url('transaksi/transaksi_penjualan/detail/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                    });
                } else {
                    $('#ModalTambahTJL').modal('hide');
                    showSwal("error", "Gagal", response.msg);
                }
            });
        }

        $("#btntambahtjl").on("click", function() {
            $('#form-simpan-tjl')[0].reset();
            $('#ModalTambahTJL').modal('show');
            $('#defaultModalLabel').html('Tambah Data');
            $('#KodeGudang').attr('disabled', false);
            $('#KodeGudang').val('').change();
            $('#KodePerson2').attr('disabled', false);
            $('#KodePerson2').val('').change();
            $('#IDTransJual2').attr('disabled', false);
            $('#IDTransJual2').val('').change();
            $('#IDTransJual2').select2({
                placeholder: "Pilih Kode SO",
                theme: 'bootstrap4',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    delay: 250,
                    url: '<?php echo base_url('user/Lokasi/DataSO'); ?>',
                    data: function(params) {
                        return {
                            searchTerm: params.term
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(obj) {
                                return {
                                    id: obj.IDTransJual,
                                    text: obj.IDTransJual // + ' | ' + obj.NoSlipOrder
                                };
                            })
                        };
                    }
                }
            });
            $('#IDTransJual2').change(function() {
                var idtransjual = $(this).find('option:selected').attr('value');
                // console.log(idtransjual);
                $.ajax({
                    url: "<?php echo site_url('user/Lokasi/DataCustomer'); ?>",
                    method: "GET",
                    data: {
                        IDTransJual: idtransjual
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (idtransjual) {
                            $('#KodePerson2').val(data.KodePerson).change();
                            $('#KodePerson2').attr('disabled', true);
                            $('#KodeGudang').val(data.KodeGudang).change();
                            $('#KodeGudang').attr('disabled', true);
                        } else {
                            $('#KodePerson2').val('').change();
                            $('#KodePerson2').attr('disabled', false);
                            $('#KodeGudang').val('').change();
                            $('#KodeGudang').attr('disabled', false);
                        }
                    }
                });
            });
            var now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('TanggalPenjualan').value = now.toISOString().slice(0, 16);
            $('#view_file').hide();
        });
    });


    // Retur Penjualan
    $("#custom-tabs-retur-tab").on("click", function() {
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
                "url": "<?= base_url('transaksi/retur'); ?>",
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

            get_response("<?= base_url('transaksi/retur/hapus') ?>", data, function(response) {
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
            post_response("<?= base_url('transaksi/retur/simpan') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalTambahRet').modal('hide');
                    showSwal("success", "Informasi", "Berhasil input data").then(function() {
                        $tableretur.ajax.reload(null, false);
                        self[0].reset();
                        window.location.href = "<?= base_url('transaksi/retur/detail/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
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
                var idtransjual = $(this).find('option:selected').attr('value');
                // console.log(idtransjual);
                $.ajax({
                    url: "<?php echo site_url('user/Lokasi/DataCustomer'); ?>",
                    method: "GET",
                    data: {
                        IDTransJual: idtransjual
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (idtransjual) {
                            $('#KodePerson4').val(data.KodePerson);
                            $('#KodePersonView').val(data.KodePerson + ' | ' + data.NamaPersonCP);
                            $('#TglPenjualan').val(data.TanggalPenjualan);
                        } else {
                            $('#KodePerson4').val('');
                            $('#KodePersonView').val('');
                            $('#TglPenjualan').val('');
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


    // Terima Piutang
    $("#custom-tabs-piutang-tab").on("click", function() {
        const $tabletp = $('#table-terimapiutang').DataTable({
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
                    data: 'IDTransJual',
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
                    data: 'TanggalPenjualan',
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
                "url": "<?= base_url('transaksi/terima_piutang'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cari = $("#inp-search-tp").val();
                    d.tgl = $("#tgl-transaksi-tp").val();
                }
            }
        });

        $('#inp-search-tp').on('input', function(e) {
            $tabletp.ajax.reload();
        });

        $('#tgl-transaksi-tp').daterangepicker({
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
                $('#tgl-transaksi-tp').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
                $tabletp.ajax.reload();
            }
        );

        $(document).ready(function() {
            $("#form-simpan-tp").submit(function(e) {
                e.preventDefault();
                var self = $(this)
                let data_post = new FormData(self[0]);
                let data = {
                    NoRef_Manual: $('#NoRef_Manual2').val()
                }
                get_response("<?= base_url('transaksi/terima_piutang/checkManualCode') ?>", data, function(response) {
                    if (response.status === false) {
                        $('#ModalTambahTP').modal('hide');
                        showSwal1('warning', 'Peringatan', response.msg).then(function() {
                            $('#ModalTambahTP').modal('show');
                            $('#defaultModalLabel').html('Tambah Data');
                        });
                        return false;
                    } else {
                        simpan(self, data_post);
                    }
                });
                return false;
            });

            $("#table-terimapiutang").on("click", ".btnhapus", function() {
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
                IDTransJual: kode
            }

            get_response("<?= base_url('transaksi/terima_piutang/hapus') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                    return false;
                } else {
                    $tabletp.ajax.reload();
                    showSwal('success', 'Informasi', 'Data berhasil dihapus.');

                }
            })

        }

        function simpan(self, data_post) {
            post_response("<?= base_url('transaksi/terima_piutang/simpan') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalTambahTP').modal('hide');
                    showSwal("success", "Informasi", "Berhasil input data").then(function() {
                        $tabletp.ajax.reload(null, false);
                        self[0].reset();
                        window.location.href = "<?= base_url('transaksi/terima_piutang/tambah/') ?>" + btoa(unescape(encodeURIComponent(response.id))) + '/' + btoa(unescape(encodeURIComponent(response.id2)));
                    });
                } else {
                    $('#ModalTambahTP').modal('hide');
                    showSwal("error", "Gagal", response.msg);
                }
            });
        }

        $("#btntambahtp").on("click", function() {
            $('#form-simpan-tp')[0].reset();
            $('#ModalTambahTP').modal('show');
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
        if (sliporderview == 0 && quotationview == 1) {
            $("#custom-tabs-quotation-tab").trigger("click");
        }
        if (sliporderview == 0 && quotationview == 0 && transjualview == 1) {
            $("#custom-tabs-tjl-tab").trigger("click");
        }
        if (sliporderview == 0 && quotationview == 0 && transjualview == 0 && returview == 1) {
            $("#custom-tabs-retur-tab").trigger("click");
        }
    });
</script>