<script>
    const $table = $('#table-bahanpenolong').DataTable({
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
                data: 'SatuanBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
                visible: false,
            },
            {
                data: 'Stok',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            // {
            //     data: 'btn_aksi',
            //     "orderable": false,
            //     "searchable": false,
            //     className: 'text-center'

            // },
        ],
        "rowCallback": function( row, data ) {
            if (data.Stok) {
                $('td:eq(3)', row).html(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.Stok).replace("Rp", "").trim()+' '+data.SatuanBarang);
            }
        },
        "ajax": {
            "url": "<?= base_url('transaksi/bahan_penolong'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.gudang = $("#combo-gudang").val();
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#combo-gudang").on('change', function() {
        $table.ajax.reload();
        var url = '<?= base_url('transaksi/bahan_penolong/cetak/') ?>' + btoa($("#combo-gudang").val());
        $('#btn-cetak').attr('href', url);
    });

    $(document).ready(function() {
    });
</script>