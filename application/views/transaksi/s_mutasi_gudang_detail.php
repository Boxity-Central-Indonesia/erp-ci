<script>
    const $table = $('#table-mutasigdgdetail').DataTable({
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
                data: 'KodeBarang',
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
                data: 'StokGudangAsal',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Qty',
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
            if (data.StokGudangAsal) {
                $('td:eq(3)', row).html('<span style="padding-left:30%;">' + data.StokGudangAsal+' '+data.SatuanBarang + '</span>');
            }
            if (data.Qty) {
                $('td:eq(4)', row).html('<span style="padding-left:30%;">' + data.Qty+' '+data.SatuanBarang + '</span>');
            }
        },
        "ajax": {
            "url": "<?= base_url('transaksi/mutasi_gudang/detail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.notrans = '<?= $NoTrans ?>';
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
                StokAsal: $('#StokAsal').val(),
                StokTujuan: $('#StokTujuan').val(),
                QtyLama: $('#QtyLama').val()
            }
            get_response("<?= base_url('transaksi/mutasi_gudang/checkStock') ?>", data, function(response) {
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

        $("#table-mutasigdgdetail").on("click", ".btnhapus", function() {
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
                    hapusterima(kode, kode2)
                }
            })
        });
    });

    function hapusterima(kode, kode2) {
        let data = {
            NoTrans: kode,
            NoUrut: kode2
        }

        get_response("<?= base_url('transaksi/mutasi_gudang/hapusdetail') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Transaksi berhasil dibatalkan.');

            }
        })
    }

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/mutasi_gudang/simpandetail') ?>", data_post, function(response) {
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

    $("#table-mutasigdgdetail").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#QtyLama').val(model.Qty);
        $('#NoUrut').val(model.NoUrut);
        $('#KodeBarang').val(model.KodeBarang);
        $('#kodebrg').val(model.KodeBarang).change();
        $('#kodebrg').attr('disabled', true);
        $('#StokAsal').val(model.StokGudangAsal);
        $('#StokTujuan').val(model.Qty);
        document.getElementById("SatuanEdit1").innerHTML = model.SatuanBarang;
        document.getElementById("SatuanEdit2").innerHTML = model.SatuanBarang;
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#QtyLama').val('0');
        $('#kodebrg').attr('disabled', false);
        $('#kodebrg').val('').change();
        $('#kodebrg').change(function () {
            var KodeBarang = $(this).find('option:selected').attr('value');
            var gudangasal = '<?= $dtinduk['GudangAsal'] ?>';
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/jumlahStokAsal'); ?>",
                method: "GET",
                data: {
                    GudangAsal: gudangasal,
                    KodeBarang: KodeBarang
                },
                dataType: 'json',
                success: function (data) {
                    if (KodeBarang) {
                        $('#StokAsal').val(data.stok);
                    } else {
                        $('#StokAsal').val('');
                    }
                }
            });
            $.ajax({
                url: '<?php echo site_url('user/Lokasi/DataBarang'); ?>',
                method: "GET",
                data: { KodeBarang: KodeBarang },
                dataType: 'json',
                success: function (data) {
                    if (KodeBarang) {
                        $('#KodeBarang').val(data.KodeBarang);
                        document.getElementById("SatuanEdit1").innerHTML = data.SatuanBarang;
                        document.getElementById("SatuanEdit2").innerHTML = data.SatuanBarang;
                    } else {
                        $('#KodeBarang').val('');
                        document.getElementById("SatuanEdit1").innerHTML = "";
                        document.getElementById("SatuanEdit2").innerHTML = "";
                    }
                }
            });
        });
    });
</script>