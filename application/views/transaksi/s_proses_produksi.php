<script>
    const $table = $('#table-prosesprod').DataTable({
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
                data: 'EstimasiSelesai',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NamaUsaha',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
                visible: false
            },
            {
                data: 'StatusProduksi',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'SPKDibuatOleh',
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
        //     if (!(data.KodePerson)) {
        //         $('td:eq(5)', row).html( 'Produksi Langsung' );
        //     }
        //     if (!(data.StatusProduksi)) {
        //         $('td:eq(6)', row).html( '-' );
        //     }
        // },
        "ajax": {
            "url": "<?= base_url('transaksi/proses_produksi'); ?>",
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

        $("#table-prosesprod").on("click", ".btnhapus", function() {
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
            NoTrans: kode
        }

        get_response("<?= base_url('transaksi/proses_produksi/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('transaksi/proses_produksi/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    if (response.action === 'tambah') {
                        window.location.href = "<?= base_url('transaksi/proses_produksi/tambah/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                    }
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#table-prosesprod").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoTrans').val(model.NoTrans);
        $('#NoRefTrManual').val(model.NoRefTrManual);
        $('#TanggalTransaksi').val(model.TanggalTransaksi);
        $('#Deskripsi').val(model.Deskripsi);
        // $('#SPKNomor').attr('disabled', true);
        // $('#SPKNomor').val(model.SPKNomor).change();
        $('#ProdUkuran').val(model.ProdUkuran);
        $('#ProdJmlDaun').val(model.ProdJmlDaun);
        $('#GudangAsal').select2({
            placeholder: model.NamaGudangAsal,
            id: model.GudangAsal,
            theme: 'bootstrap4',
        });
        $('#GudangAsal').attr('disabled', true);
        $('#GudangTujuan').select2({
            placeholder: model.NamaGudangTujuan,
            id: model.GudangTujuan,
            theme: 'bootstrap4',
        });
        $('#GudangTujuan').attr('disabled', true);
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        // var now = new Date();
        // now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        // document.getElementById('TanggalTransaksi').value = now.toISOString().slice(0,16);
        $('#view_file').hide();
        $('#SPKNomor').attr('disabled', false);
        $('#SPKNomor').val('').change();
        $('#SPKNomor').change(function() {
            var spknomor = $(this).find('option:selected').attr('value');
            console.log(spknomor);
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataSPK'); ?>",
                method: "GET",
                data: {
                    SPKNomor: spknomor
                },
                dataType: 'json',
                success: function(data) {
                    if (spknomor) {
                        $('#NoRefTrSistem').val(data.IDTransJual);
                        $('#SPKTanggal').val(data.SPKTanggal);
                        $('#Customer').val(data.KodePerson + ' | ' + data.NamaUsaha);
                    } else {
                        $('#NoRefTrSistem').val('');
                        $('#SPKTanggal').val('');
                        $('#Customer').val('');
                    }
                }
            });
        });
        $('#GudangAsal').attr('disabled', false);
        $('#GudangAsal').val('').change();
        $('#GudangAsal').select2({
            placeholder: "Pilih Gudang Asal",
            theme: 'bootstrap4',
            allowClear: true,
            ajax: {
                dataType: 'json',
                delay: 250,
                url: '<?php echo base_url('user/Lokasi/DataGudangAsal'); ?>',
                data: function(params) {
                    return {
                        searchTerm: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(obj) {
                            return {
                                id: obj.KodeGudang,
                                text: obj.NamaGudang
                            };
                        })
                    };
                }
            }
        });
        $('#GudangTujuan').attr('disabled', false);
        $('#GudangTujuan').val('').change();
        $('#GudangTujuan').select2({
            placeholder: "Pilih Gudang Tujuan",
            theme: 'bootstrap4',
            allowClear: true,
            ajax: {
                dataType: 'json',
                delay: 250,
                url: '<?php echo base_url('user/Lokasi/DataGudangAsal'); ?>',
                data: function(params) {
                    return {
                        searchTerm: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(obj) {
                            return {
                                id: obj.KodeGudang,
                                text: obj.NamaGudang
                            };
                        })
                    };
                }
            }
        });
    });
</script>