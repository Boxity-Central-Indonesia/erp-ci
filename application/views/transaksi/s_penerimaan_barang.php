<script>
    const $table = $('#table-penerimaan').DataTable({
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
                data: 'NoTrans',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NoRefTrManual',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
                visible: false,
            },
            {
                data: 'TanggalTransaksi',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
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
                data: 'NamaUsaha',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'StatusKirim',
                className: 'text-center',
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
            if (!(data.NoRef_Manual)) {
                $('td:eq(5)', row).html('-');
            }
        },
        "ajax": {
            "url": "<?= base_url('transaksi/penerimaan_barang'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.tgl = $("#tgl-transaksi").val();
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#combo-status").on('change', function() {
        $table.ajax.reload();
    });

    $('#tgl-transaksi').daterangepicker({
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
            $('#tgl-transaksi').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
            $table.ajax.reload();
        }
    );

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpan(self, data_post);
            return false;
        });

        $("#table-penerimaan").on("click", ".btnhapus", function() {
            const kode = $(this).data('kode');
            const kode2 = $(this).data('kode2');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "jika anda menghapus data transaksi penerimaan barang, maka detail item transaksi juga akan terhapus!",
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
            NoTrans: kode,
            NoRefTrSistem: kode2
        }

        get_response("<?= base_url('transaksi/penerimaan_barang/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('transaksi/penerimaan_barang/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    if (response.action === 'tambah') {
                        window.location.href = "<?= base_url('transaksi/penerimaan_barang/detail/') ?>" + btoa(unescape(encodeURIComponent(response.id))) + '/' + btoa(unescape(encodeURIComponent(response.id2)));
                    }
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#table-penerimaan").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoTrans').val(model.NoTrans);
        $('#NoRefTrManual').val(model.NoRefTrManual);
        $('#TanggalTransaksi').val(model.TanggalTransaksi);
        $('#Deskripsi').val(model.Deskripsi);
        $('#KodeGudang').val(model.GudangTujuan).change();
        $('#NoRefTrSistem').select2({
            placeholder: model.NoRefTrSistem,
            id: model.NoRefTrSistem,
            theme: 'bootstrap4',
        });
        $('#NoRefTrSistem').attr('disabled', true);
        if (model.NoPO) {
            $('#KodePO').val(model.IDTransBeli);
        } else {
            $('#KodePO').val('-');
        }
        $('#NoRef_Manual').val(model.NoRef_Manual);
        $('#TanggalPembelian').val(model.TanggalPembelian);
        $('#Supplier').val(model.KodePerson + ' | ' + model.NamaUsaha);
        $('#KodePerson').val(model.KodePerson);
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('TanggalTransaksi').value = now.toISOString().slice(0, 16);
        $('#view_file').hide();
        $('#KodeGudang').val('').change();
        $('#NoRefTrSistem').attr('disabled', false);
        $('#NoRefTrSistem').val('').change();
        $('#NoRefTrSistem').select2({
            placeholder: "Pilih No Transaksi Pembelian",
            theme: 'bootstrap4',
            allowClear: true,
            ajax: {
                dataType: 'json',
                delay: 250,
                url: '<?php echo base_url('user/Lokasi/DataPembelian'); ?>',
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
                                text: obj.IDTransBeli
                            };
                        })
                    };
                }
            }
        });
        $('#NoRefTrSistem').change(function() {
            var idtransbeli = $(this).find('option:selected').attr('value');
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataSupplier'); ?>",
                method: "GET",
                data: {
                    IDTransBeli: idtransbeli
                },
                dataType: 'json',
                success: function(data) {
                    if (idtransbeli) {
                        if (data.NoPO) {
                            $('#KodePO').val(data.IDTransBeli);
                        } else {
                            $('#KodePO').val('-');
                        }
                        if (data.NoRef_Manual) {
                            $('#NoRef_Manual').val(data.NoRef_Manual);
                        } else {
                            $('#NoRef_Manual').val('-');
                        }
                        $('#TanggalPembelian').val(data.TanggalPembelian);
                        $('#Supplier').val(data.KodePerson + ' | ' + data.NamaUsaha);
                        $('#KodePerson').val(data.KodePerson);
                    } else {
                        $('#KodePO').val('');
                        $('#NoRef_Manual').val('');
                        $('#TanggalPembelian').val('');
                        $('#Supplier').val('');
                        $('#KodePerson').val('');
                    }
                }
            });
        });
    });
</script>