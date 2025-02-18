<script>
    const $table = $('#table-sodetailspk').DataTable({
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
                data: 'KodeProduksi',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Barang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JenisBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Kategory',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'SatuanBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Qty',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
                visible: false,
            },
            {
                data: 'JmlProduksi',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
                // visible: false,
            },
            // {
            //     data: 'Total',
            //     className: 'text-right',
            //     "orderable": false,
            //     "searchable": false,
            //     render: $.fn.dataTable.render.number( '.', ',', 2, ),
            // },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center'

            },
        ],
        "ajax": {
            "url": "<?= base_url('transaksi/slip_order/detailspk'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.idtransjual = '<?= $IDTransJual ?>';
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
            if ('<?= $dtinduk['StatusProses'] ?>' === 'SO') {
                simpan(self, data_post);
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
                        simpan(self, data_post);
                    }
                });
            }
            return false;
        });
    });

    function simpan(self, data_post) {
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

    $("#simpanspk").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalSPK').modal('show');
        $('#SPKNomor').val('<?= $dtinduk['SPKNomor'] ?>');
        $('#SPKLama').val('<?= $dtinduk['SPKNomor'] ?>');
        $('#EstimasiSelesai').val('<?= $dtinduk['EstimasiSelesai'] ?>');
        $('#SPKDisetujuiOleh').val("<?= $dtinduk['SPKDisetujuiOleh'] ?>");
        $('#SPKDisetujuiTgl').val('<?= $dtinduk['SPKDisetujuiTgl'] ?>');
        $('#SPKDiketahuiOleh').val("<?= $dtinduk['SPKDiketahuiOleh'] ?>");
        $('#SPKDiketahuiTgl').val('<?= $dtinduk['SPKDiketahuiTgl'] ?>');
        $('#Gudang').val('<?= $dtinduk['KodeGudang'] ?>');
        $('#defaultModalLabel').html('Simpan SPK');
        if ('<?= $dtinduk['SPKTanggal'] ?>') {
            $('#SPKTanggal').val('<?= $dtinduk['SPKTanggal'] ?>');
        } else {
            var now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('SPKTanggal').value = now.toISOString().slice(0,16);
        }
        $('#view_file').hide();
    });
</script>