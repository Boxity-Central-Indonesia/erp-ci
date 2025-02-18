<script>
    const $table = $('#table-hpproduksi').DataTable({
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
                data: 'NoRefTrSistem',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'SPKNomor',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'SPKTanggal',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'KodeProduksi',
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
                data: 'JmlProduksi',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'HPPProduksi',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'HPPTotal',
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
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
 
            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
 
            // Total over all pages
            total = api
                .column(8)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var amount = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total).replace("Rp", "").trim();

            // Update Total
            // $('#TotalNilaiHPP').val(amount);
        },
        "ajax": {
            "url": "<?= base_url('laporan/hp_produksi'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.tgl = $("#tgl-transaksi").val();
            },
            "dataSrc": function (json){
                var beratkotor  = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 0}).format(json.beratkotor).replace("Rp", "").trim(),
                    biayakotor  = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(json.biayakotor).trim(),
                    beratbersih = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 0}).format(json.beratbersih).replace("Rp", "").trim(),
                    biayabersih = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(json.biayabersih).trim();

                document.getElementById("beratbahanbaku").innerHTML = beratkotor+' kg';
                document.getElementById("biayabahanbaku").innerHTML = '('+biayakotor+')';
                document.getElementById("beratkotorptod").innerHTML = beratkotor+' kg';
                document.getElementById("biayakotorprod").innerHTML = '('+biayakotor+')';
                document.getElementById("beratbersihprod").innerHTML = beratbersih+' kg';
                document.getElementById("biayabersihprod").innerHTML = '('+biayabersih+')';
                console.log(json);
                return json.data;
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
            startDate: "<?= $tglawal ?>",
            endDate: "<?= $tglakhir ?>",
            locale: {
                format: "DD-MM-YYYY"
            }
        },
        function(start, end) {
            $('#tgl-transaksi').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
            $table.ajax.reload();

            var url = '<?= base_url('laporan/hp_produksi/cetak/') ?>' + btoa($("#tgl-transaksi").val());
            $('#btn-cetak').attr('href', url);
        }
    );

    $(document).ready(function() {
    });
</script>