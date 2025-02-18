<script>
    const $table = $('#table-piutang').DataTable({
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
                className: 'text-center',
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
                data: 'NamaPersonCP',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TotalTagihan',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TotalBayar',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'SisaTagihan',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center',

            },
        ],
        "rowCallback": function( row, data ) {
            if (!(data.NoRef_Manual)) {
                $('td:eq(2)', row).html( '-' );
            }
        },
        "ajax": {
            "url": "<?= base_url('laporan/piutang'); ?>",
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
            filtrasi();
            $table.ajax.reload();

            var url = '<?= base_url('laporan/piutang/cetak/') ?>' + btoa($("#tgl-transaksi").val());
            $('#btn-cetak').attr('href', url);
        }
    );

    filtrasi();
    function filtrasi() {
        var cari = $('#inp-search').val();
        var tgl = $('#tgl-transaksi').val();
        let data = {
            cari: cari,
            tgl: tgl
        }

        get_response("<?= base_url('laporan/piutang/get_total') ?>", data, function(res) {
            $('#total-sisa').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(res.totalsisa).replace("Rp", "").trim());
        });
    }

    $(document).ready(function() {
    });
</script>