<script>
    const $table = $('#table-penerimaandetail').DataTable({
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
                data: 'SatuanBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
                visible: false,
            },
            {
                data: 'Qty',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Stok',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JumlahDiterima',
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
            "url": "<?= base_url('transaksi/penerimaan_barang/detail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.notrans = '<?= $NoTrans ?>';
                d.idtransbeli = '<?= $IDTransBeli ?>';
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
            let data = {
                IDTransBeli: $('#IDTransBeli').val(),
                KodeBarang: $('#KodeBarang').val(),
                Qty: $('#Qty').val(),
                JumlahLama: $('#JumlahLama').val(),
                JumlahDiterima: $('#JumlahDiterima').val()
            }
            get_response("<?= base_url('transaksi/penerimaan_barang/checkJmlPenerimaan') ?>", data, function(response) {
                if (response.status === false) {
                    $('#ModalTambah').modal('hide');
                    showSwal1('warning', 'Peringatan', response.msg).then(function() {
                        $('#ModalTambah').modal('show');
                        $('#defaultModalLabel').html('Edit Data');
                    });
                    return false;
                } else {
                    simpan(self, data_post);
                }
            });
            return false;
        });

        $("#batalterima").on("click", function() {
            const kode = $(this).data('kode');
            const kode2 = $(this).data('kode2');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "jika membatalkan transaksi, semua data transaksi akan terhapus",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Batalkan transaksi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    hapusterima(kode, kode2)
                }
            })
        });

        $("#table-penerimaandetail").on("click", ".btndone", function() {
            Swal.fire({
                title: 'Tidak dapat mengedit',
                text: "item barang sudah terkirim seluruhnya.",
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#FA7C41',
                confirmButtonText: 'Ok'
            })
        });

        $("#table-penerimaandetail").on("click", ".btnisi", function() {
            Swal.fire({
                title: 'Tidak dapat mengedit',
                text: "item barang sudah terinput.",
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#FA7C41',
                confirmButtonText: 'Ok'
            })
        });

        $("#table-penerimaandetail").on("click", ".btnsudahjurnal", function() {
            Swal.fire({
                title: 'Tidak dapat mengedit',
                text: "transaksi penerimaan barang sudah terjurnal.",
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#FA7C41',
                confirmButtonText: 'Ok'
            })
        });

        $("#btn-jurnal").on("click", function() {
            const kode = $(this).data('kode');

            jurnalkan(kode);
        });
    });

    function hapusterima(kode, kode2) {
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
                showSwal('success', 'Informasi', 'Transaksi berhasil dibatalkan.').then(function() {
                    window.location.href = "<?= base_url('transaksi/penerimaan_barang') ?>";
                });

            }
        })
    }

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/penerimaan_barang/simpandetail') ?>", data_post, function(response) {
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

    function jurnalkan(kode) {
        let data = {
            NoTrans: kode
        }

        get_response("<?= base_url('transaksi/penerimaan_barang/penjurnalan') ?>", data, function(response) {
            if (response.status) {
                showSwal1('success', 'Informasi', response.msg).then(function() {
                    if (response.stj == 'on') {
                        window.location.reload();
                    } else {
                        window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnalmanual/') ?>" + btoa("penerimaanbrg") + "/" + btoa(response.idjurnal) + "/" + btoa("<?= $NoTrans ?>") + "/" + btoa("penerimaan_barang/detail");
                    }
                });
            }
        })
    }

    $("#table-penerimaandetail").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoUrut').val(model.NoUrut);
        $('#KodeBarang').val(model.KodeBarang);
        $('#kodebrg').val(model.KodeBarang).change();
        $('#kodebrg').attr('disabled', true);
        $('#SatuanBarang').val(model.SatuanBarang);
        $('#Qty').val(model.Qty);
        $('#HargaSatuan').val(model.SatuanResult);
        $('#JumlahDiterima').val(model.JumlahDiterima);
        $('#JumlahLama').val(model.JumlahDiterima);
        document.getElementById("SatuanEdit1").innerHTML = model.SatuanBarang;
        document.getElementById("SatuanEdit2").innerHTML = model.SatuanBarang;
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });
</script>