<script>
    const $table = $('#table-hppenjualan').DataTable({
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
                data: 'NoRef_Manual',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TanggalPenjualan',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NamaUsaha',
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
                data: 'Qty',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'HPPSaatJual',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number('.', ',', 2, ),
            },
            {
                data: 'HPPTotal',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number('.', ',', 2, ),
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center',
                visible: false,

            },
        ],
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();

            // Remove the formatting to get integer data for summation
            var intVal = function(i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            // Total over all pages
            total = api
                .column(8)
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var amount = Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2
            }).format(total).replace("Rp", "").trim();

            // Update Total
            // $('#TotalNilaiHPP').val(amount);
        },
        "ajax": {
            "url": "<?= base_url('laporan/hp_penjualan'); ?>",
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

            var url = '<?= base_url('laporan/hp_penjualan/cetak/') ?>' + btoa($("#tgl-transaksi").val());
            $('#btn-cetak').attr('href', url);
        }
    );

    $(document).ready(function() {});
</script>