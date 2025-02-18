<script>
    const $table = $('#table-pergerakanstokdetail').DataTable({
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
                data: 'Transaksi',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TanggalTransaksi',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
                visible: false,
            },
            {
                data: 'Masuk',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Keluar',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Saldo',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center',
                visible: false,

            },
        ],
        "rowCallback": function( row, data ) {
            if (data.Masuk) {
                $('td:eq(2)', row).html('<span style="padding-left:30%;">' + data.Masuk+' '+data.SatuanBarang + '</span>');
            }
            if (data.Keluar) {
                $('td:eq(3)', row).html('<span style="padding-left:30%;">' + data.Keluar+' '+data.SatuanBarang + '</span>');
            }
            if (data.Saldo) {
                $('td:eq(4)', row).html('<span style="padding-left:30%;">' + data.Saldo+' '+data.SatuanBarang + '</span>');
            }
        },
        "ajax": {
            "url": "<?= base_url('transaksi/pergerakan_stok/detail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.gudang = $("#combo-gudang").val();
                d.kodebarang = '<?= $KodeBarang ?>';
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#combo-gudang").on('change', function() {
        $table.ajax.reload();
        var url = '<?= base_url('transaksi/pergerakan_stok/cetak/') ?>' + btoa("<?= $KodeBarang ?>") + '/' + btoa($("#combo-gudang").val());
        $('#btn-cetak').attr('href', url);
    });

    $("#combo-status").on('change', function() {
        $table.ajax.reload();
    });

    $(document).ready(function() {
    });
</script>