<script>
    const $table = $('#table-penjualan').DataTable({
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
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center'

            },
        ],
        "ajax": {
            "url": "<?= base_url('transaksi/transaksi_penjualan'); ?>",
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
                    showSwal('error', 'Peringatan', response.msg).then(function() {
                        $('#ModalTambahTJL').modal('show');
                        $('#defaultModalLabel').html('Tambah Data');
                    });
                    return false;
                } else {
                    simpan(self, data_post);
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
                    hapus(kode)
                }
            })
        });
    });

    function hapus(kode) {
        let data = {
            IDTransJual: kode
        }

        get_response("<?= base_url('transaksi/transaksi_penjualan/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('transaksi/transaksi_penjualan/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambahTJL').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
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
        $('#KodePerson').attr('disabled', false);
        $('#KodePerson').val('').change();
        $('#IDTransJual').attr('disabled', false);
        $('#IDTransJual').val('').change();
        $('#IDTransJual').select2({
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
        $('#IDTransJual').change(function() {
            var idtransjual = $(this).find('option:selected').attr('value');
            console.log(idtransjual);
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataCustomer'); ?>",
                method: "GET",
                data: {
                    IDTransJual: idtransjual
                },
                dataType: 'json',
                success: function(data) {
                    if (idtransjual) {
                        $('#KodePerson').val(data.KodePerson).change();
                        $('#KodePerson').attr('disabled', true);
                    } else {
                        $('#KodePerson').val('').change();
                        $('#KodePerson').attr('disabled', false);
                    }
                }
            });
        });
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('TanggalPenjualan').value = now.toISOString().slice(0, 16);
        $('#view_file').hide();
    });
</script>