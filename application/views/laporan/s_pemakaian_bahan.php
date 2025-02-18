<script>
    const $table = $('#table-pemakaianbahan').DataTable({
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
                data: 'NamaJenisBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NamaKategori',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
                visible: false,
            },
            {
                data: 'hargabeli',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'hargajual',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'hpp',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'IsAktif',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
                visible: false,
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center'

            },
        ],
        // "rowCallback": function( row, data ) {
        //     if (!(data.KodeKategori)) {
        //         $('td:eq(4)', row).html( '-' );
        //     }
        // },
        "ajax": {
            "url": "<?= base_url('laporan/pemakaian_bahan'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.jenis = $("#combo-jenis").val();
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#combo-status").on('change', function() {
        $table.ajax.reload();
    });

    $("#combo-jenis").on('change', function() {
        $table.ajax.reload();
    });

    $(document).ready(function() {
    });
</script>