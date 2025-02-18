<script>
    const $table = $('#table-spkdetail').DataTable({
        "paging": false,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": false,
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
                data: 'SPKNomor',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'SPKTanggal',
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
                data: 'JmlProduksi',
                className: 'text-center',
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
                data: 'KodeProduksi',
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
            "url": "<?= base_url('transaksi/spk/detail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.noreftrsistem = '<?= $NoRefTrSistem ?>';
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

        $("#table-spkdetail").on("click", ".btnhapus", function() {
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

        $("#form-simpan-spk").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            if ('<?= $dtinduk['StatusProses'] ?>' === 'SO') {
                spkverify(self, data_post);
            } else {
                let data = {
                    SPKNomor: $('#SPKNomor').val(),
                    SPKLama: $('#SPKLama').val()
                }
                get_response("<?= base_url('transaksi/slip_order/checkNoSPK') ?>", data, function(response) {
                    if (response.status === false) {
                        $('#ModalSPK').modal('hide');
                        showSwal1('warning', 'Peringatan', response.msg).then(function() {
                            $('#ModalSPK').modal('show');
                            $('#defaultModalLabel').html('Simpan SPK');
                            $('#view_file').hide();
                        });
                        return false;
                    } else {
                        spkverify(self, data_post);
                    }
                });
            }
            return false;
        });
    });

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/spk/simpantambah') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    // window.location.reload();
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    function hapus(kode) {
        let data = {
            NoTrans: kode
        }

        get_response("<?= base_url('transaksi/spk/hapusitemtambah') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Data berhasil dihapus.');

            }
        })

    }

    function spkverify(self, data_post) {
        post_response("<?= base_url('transaksi/slip_order/simpanspk') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalSPK').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    window.location.reload();
                });
            } else {
                $('#ModalSPK').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#verifikasispk").on("click", function() {
        $('#form-simpan-spk')[0].reset();
        $('#ModalSPK').modal('show');
        $('#SPKNomor').val("<?= $dtinduk['SPKNomor'] ?>");
        $('#SPKLama').val("<?= $dtinduk['SPKNomor'] ?>");
        $('#EstimasiSelesai').val("<?= $dtinduk['EstimasiSelesai'] ?>");
        $('#SPKDisetujuiOleh').val("<?= $dtinduk['SPKDisetujuiOleh'] ?>");
        $('#SPKDisetujuiTgl').val("<?= $dtinduk['SPKDisetujuiTgl'] ?>");
        $('#SPKDiketahuiOleh').val("<?= $dtinduk['SPKDiketahuiOleh'] ?>");
        $('#SPKDiketahuiTgl').val("<?= $dtinduk['SPKDiketahuiTgl'] ?>");
        $('#SPKTanggal').val("<?= $dtinduk['SPKTanggal'] ?>");
        $('#defaultModalLabel').html('Verifikasi SPK');
        $('#view_file').hide();
    });

    $("#table-spkdetail").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoTrans').val(model.NoTrans);
        $('#KodeBarang').attr('disabled', true);
        $('#KodeBarang').attr('required', false);
        $('#KodeBarang').select2({
            placeholder: model.NamaBarang,
            id: model.KodeBarang,
            theme: 'bootstrap4',
        });
        $('#TanggalTransaksi').val(model.TanggalTransaksi);
        $('#ProdUkuran').val(model.ProdUkuran);
        $('#ProdJmlDaun').val(model.ProdJmlDaun);
        if ('<?= $dtinduk['TglSlipOrder'] ?>') {
            $('#Qty').attr('readonly', true);
            $('#Qty').attr('required', false);
        } else {
            $('#Qty').attr('readonly', false);
            $('#Qty').attr('required', true);
        }
        $('#Qty').val(model.JmlProduksi);
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#Qty').attr('readonly', false);
        $('#Qty').attr('required', true);
        $('#KodeBarang').attr('disabled', false);
        $('#KodeBarang').attr('required', true);
        $('#KodeBarang').val('').change();
        $('#KodeBarang').select2({
            placeholder: "Pilih Barang Produksi",
            theme: 'bootstrap4',
            allowClear: true,
            ajax: {
                dataType: 'json',
                delay: 250,
                url: '<?php echo base_url('user/Lokasi/DataBarangJadi'); ?>',
                data: function(params) {
                    return {
                        searchTerm: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(obj) {
                            return {
                                id: obj.KodeBarang,
                                text: obj.NamaBarang
                            };
                        })
                    };
                }
            }
        });
    });
</script>