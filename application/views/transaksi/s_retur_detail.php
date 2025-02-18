<script>
    var totalreturs = 0;
    const $table = $('#table-returdetail').DataTable({
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
                data: 'NamaBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JmlJual',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JmlRetur',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'HargaJual',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'TotalRetur',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'AlasanRetur',
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
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
 
            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
 
            // Total over all pages
            total = api
                .column(5)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var amount = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total).replace("Rp", "").trim();
            // Update footer
            $(api.column(5).footer()).html(amount);
            totalreturs = amount;
        },
        "ajax": {
            "url": "<?= base_url('transaksi/retur/detail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.idretur = '<?= $IDTransRetur ?>';
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            let data = {
                JmlJual: $('#JmlJual').val(),
                JmlRetur: $('#JmlRetur').val()
            }
            get_response("<?= base_url('transaksi/retur/checkitemjual') ?>", data, function(response) {
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
            return false;
        });

        $("#table-returdetail").on("click", ".btnhapus", function() {
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

        $("#form-simpan-retur").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpanreturs(self, data_post);
            return false;
        });

        $(".batalretur").on("click", function() {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "jika membatalkan transaksi, semua data item transaksi akan terhapus",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Batalkan transaksi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    batalretur(kode)
                }
            })
        });
    });

    function hapus(kode, kode2) {
        let data = {
            IDTransRetur: kode,
            NoUrut: kode2
        }

        get_response("<?= base_url('transaksi/retur/hapusdetail') ?>", data, function(response) {
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
        post_response("<?= base_url('transaksi/retur/simpandetail') ?>", data_post, function(response) {
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

    function simpanreturs(self, data_post) {
        post_response("<?= base_url('transaksi/retur/simpanretur') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalRetur').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    self[0].reset();
                    if (response.jenisrealisasi == 'KEMBALI UANG' && response.stj == 'off') {
                        window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnalmanual/') ?>" + btoa("trans_jual") + "/" + btoa(unescape(encodeURIComponent(response.idjurnal))) + "/" + btoa("<?= $IDTransRetur ?>") + "/" + btoa("retur/detail");
                    } else {
                        window.location.reload();
                    }
                });
            } else {
                $('#ModalRetur').modal('hide');
                showSwal("error", "Gagal", response.msg).then(function() {
                    $('#ModalRetur').modal('show');
                });
            }
        });
    }

    function batalretur(kode) {
        let data = {
            IDTransRetur: kode
        }

        get_response("<?= base_url('transaksi/retur/batal') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Transaksi berhasil dibatalkan.').then(function() {
                    window.location.href = "<?= base_url('transaksi/trans_jual') ?>";
                });

            }
        })

    }

    $("#table-returdetail").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoUrut').val(model.NoUrut);
        $('#JmlRetur').val(model.JmlRetur);
        $('#JmlJual').val(model.JmlJual);
        $('#SatuanBarang').val(model.SatuanBarang);
        $('#KodeBarang').val(model.KodeBarang).change();
        $('#KodeBarang').attr('disabled', true);
        $('#HargaJual').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.HargaJual).replace("Rp", "").trim());
        $('#HargaJualReal').val(model.HargaJual);
        $('#AdditionalName').val(model.AdditionalName);
        $('#AlasanRetur').val(model.AlasanRetur);
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodeBarang').attr('disabled', false);
        $('#KodeBarang').val('').change();
        $('#KodeBarang').change(function () {
            var kodeBrg = $(this).find('option:selected').attr('value');
            var idtransjual = "<?= $dtinduk['IDTransJual'] ?>";
            // console.log(kodeBrg, idtransjual);
            $.ajax({
                url: "<?php echo site_url('transaksi/retur/getitemjual'); ?>",
                method: "GET",
                data: {KodeBarang: kodeBrg, IDTransJual: idtransjual},
                dataType: 'json',
                success: function (data) {
                    if (kodeBrg) {
                        $('#SatuanBarang').val(data.SatuanBarang);
                        $('#AdditionalName').val(data.AdditionalName);
                        $('#HargaJual').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.HargaSatuan - data.Diskon).replace("Rp", "").trim());
                        $('#HargaJualReal').val(data.HargaSatuan - data.Diskon);
                        $('#JmlJual').val(data.Qty);
                    } else {
                        $('#SatuanBarang').val('');
                        $('#AdditionalName').val('');
                        $('#HargaJual').val('');
                        $('#HargaJualReal').val('');
                        $('#JmlJual').val('');
                    }
                }
            });
        });
        $('#view_file').hide();
    });

    $("#btnmemo").on("click", function() {
        $('#ModalMemo').modal('show');
    });


    $(".btnsimpanrt").on("click", function() {
        $('#form-simpan-retur')[0].reset();
        $('#ModalRetur').modal('show');
        $('#defaultModalLabel').html('Simpan Transaksi Retur');
        $('#JenisRealisasi').val("<?= $dtinduk['JenisRealisasi'] ?>");
        $('#TotalReturs').val(totalreturs);
    });
</script>