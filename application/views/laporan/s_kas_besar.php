<script>
    const $table = $('#table-kasbesar').DataTable({
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
                data: 'TglTransJurnal',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'IDTransJurnal',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NoRefTrans',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NarasiJurnal',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Debet',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Kredit',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Saldo',
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
                visible: false,

            },
        ],
        "ajax": {
            "url": "<?= base_url('laporan/kas_besar'); ?>",
            "type": "GET",
            "data": function(d) {
                d.tgl = $("#tgl-transaksi").val();
                d.kodeakun = $("#combo-akun").val();
                getAwal();
                getAkhir();
                getUrlCetak();
            }
        }
    });

    function getAwal() {
        var kodetahun = "<?= $kodetahun ?>";
        var tglawal = $("#tgl_awal").val() == '' ? "<?= $t_awal ?>" : $("#tgl_awal").val();
        $.get('<?= base_url('laporan/kas_besar/getSaldoAwal') ?>?kodeakun='+$("#combo-akun").val()+'&tglawal='+tglawal+'&kodetahun='+kodetahun, function(data) {
            if (data != null) {
                document.getElementById("saldo_awal").innerHTML = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data).replace("Rp", "").trim();
            } else {
                document.getElementById("saldo_awal").innerHTML = 0;
            }
        });
    }

    function getAkhir() {
        var kodetahun = "<?= $kodetahun ?>";
        var tglakhir = $("#tgl_akhir").val() == '' ? "<?= $t_akhir ?>" : $("#tgl_akhir").val();
        $.get('<?= base_url('laporan/kas_besar/getSaldoAkhir') ?>?kodeakun='+$("#combo-akun").val()+'&tglakhir='+tglakhir+'&kodetahun='+kodetahun, function(data) {
            if (data != null) {
                document.getElementById("saldo_akhir").innerHTML = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data).replace("Rp", "").trim();
            } else {
                document.getElementById("saldo_akhir").innerHTML = 0;
            }
        });
    }

    $('#combo-akun').on('change', function(e) {
        $table.ajax.reload();
    });

    function getUrlCetak() {
        var t_awal = $("#tgl_awal").val();
        var url = "";
        if (t_awal == '') {
            url = '<?= base_url('laporan/kas_besar/cetak/') ?>' + $("#combo-akun").val() + '/' + "<?= $t_awal ?>" + '/' + "<?= $t_akhir ?>";
        } else {
            url = '<?= base_url('laporan/kas_besar/cetak/') ?>' + $("#combo-akun").val() + '/' + $("#tgl_awal").val() + '/' + $("#tgl_akhir").val();
        }
        $('#btn-cetak').attr('href', url);
    }

    $('#tgl-transaksi').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            // autoUpdateInput: false,
            startDate: "<?= $tglawal ?>",
            endDate: "<?= $tglakhir ?>",
            locale: {
                format: "DD-MM-YYYY"
            }
        },
        function(start, end) {
            $('#tgl-transaksi').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
            var dateStart = start.format("DD-MM-YYYY");
            var dateEnd = end.format("DD-MM-YYYY");
            $('#tglawal').text(dateStart);
            $('#tglakhir').text(dateEnd);
            $('#tgl_awal').val(start.format("YYYY-MM-DD"));
            $('#tgl_akhir').val(end.format("YYYY-MM-DD"));
            $table.ajax.reload();
        }
    );

    $(document).ready(function() {
    });
</script>