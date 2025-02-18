<script>
    const $table = $('#table-srvlg').DataTable({
        "paging": true,
        "lengthChange": true,
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
        "sDom": 'flrtip',
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
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
                data: 'DateTimeLog',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JenisTransaksi',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Action',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Description',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'UserName',
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
        "ajax": {
            "url": "<?= base_url('user/logserver'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.tgl = $("#tgl-transaksi").val();
            }
        }
    });

    $('#inp-search').on('input', function(e) {
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

            var url = '<?= base_url('laporan/penjualan/cetak/') ?>' + btoa($("#tgl-transaksi").val());
            $('#btn-cetak').attr('href', url);
        }
    );

    $(document).ready(function() {});
</script>