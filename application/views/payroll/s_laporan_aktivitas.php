<script>
    const $table = $('#table-lapaktivitas').DataTable({
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
                data: 'NoTrAktivitas',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NamaPegawai',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TglAktivitas',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JenisAktivitas',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Nominal',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center',
                visible: false,
            },
        ],
        // "rowCallback": function( row, data ) {
        //     if (!(data.KodeJabatan)) {
        //         $('td:eq(5)', row).html( '-' );
        //     }
        // },
        "ajax": {
            "url": "<?= base_url('payroll/laporan_aktivitas'); ?>",
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
            startDate: "<?= $tglawal ?>",
            endDate: "<?= $tglakhir ?>",
            locale: {
                format: "DD-MM-YYYY"
            }
        },
        function(start, end) {
            $('#tgl-transaksi').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
            $table.ajax.reload();

            var url = '<?= base_url('payroll/laporan_aktivitas/cetak/') ?>' + btoa($("#tgl-transaksi").val());
            $('#btn-cetak').attr('href', url);
        }
    );

    $(document).ready(function() {
    });
</script>