<script>
    // Slip Gaji
    // $("#custom-tabs-po-tab").on("click", function() {
        const $table = $('#table-lapslipgaji').DataTable({
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
            "bDestroy": true,
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
                    data: 'NIP',
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
                    data: 'NamaJabatan',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'Bulan',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'InsentifPegawai',
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                    render: $.fn.dataTable.render.number( '.', ',', 2, ),
                },
                {
                    data: 'IsTelahDibayarkan',
                    className: 'text-center',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'btn_aksi',
                    "orderable": false,
                    "searchable": false,
                    className: 'text-center'

                },
            ],
            "rowCallback": function( row, data ) {
                if (!(data.KodeJabatan)) {
                    $('td:eq(5)', row).html( '-' );
                }
            },
            "ajax": {
                "url": "<?= base_url('payroll/laporan_slipgaji'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cari = $("#inp-search").val();
                    d.bulan = $("#bulan").val();
                }
            }
        });

        $('#inp-search').on('input', function(e) {
            $table.ajax.reload();
        });

        $('#bulan').on('input', function(e) {
            $table.ajax.reload();
            var url = '<?= base_url('payroll/laporan_slipgaji/cetaklist_slip/') ?>' + $("#bulan").val();
            $('#cetak-slip').attr('href', url);
        });

        $(document).ready(function() {
            $("#table-lapslipgaji").on("click", ".btnbelum", function() {
                Swal.fire({
                    title: 'Informasi',
                    text: "Insentif belum dibayarkan kepada pegawai.",
                    icon: 'info',
                    showCancelButton: false,
                    confirmButtonColor: '#FA7C41',
                    confirmButtonText: 'Ok'
                })
            });
        });
    // });

    // Insentiif Pegawai
    $("#custom-tabs-insentif-tab").on("click", function() {
        const $tableinsentif = $('#table-insentif').DataTable({
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
            "bDestroy": true,
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
                    data: 'NIP',
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
                    data: 'NamaJabatan',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'Bulan',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'InsentifPegawai',
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
            "ajax": {
                "url": "<?= base_url('payroll/laporan_slipgaji/insentif'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cari_ins = $("#inp-search-ins").val();
                    d.bulan_ins = $("#bulan-ins").val();
                }
            }
        });

        $('#inp-search-ins').on('input', function(e) {
            $tableinsentif.ajax.reload();
        });

        $('#bulan-ins').on('input', function(e) {
            $tableinsentif.ajax.reload();
            var url = '<?= base_url('payroll/laporan_slipgaji/cetaklist_insentif/') ?>' + $("#bulan-ins").val();
            $('#cetak-insentif').attr('href', url);
        });
    });

    // Pinjaman Pegawai
    $("#custom-tabs-pinjaman-tab").on("click", function() {
        const $tablepinjaman = $('#table-pinjaman').DataTable({
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
            "bDestroy": true,
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
                    data: 'NIP',
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
                    data: 'NamaJabatan',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'Bulan',
                    className: 'text-left',
                    "orderable": false,
                    "searchable": false,
                },
                {
                    data: 'InsentifPegawai',
                    className: 'text-right',
                    "orderable": false,
                    "searchable": false,
                    render: $.fn.dataTable.render.number( '.', ',', 2, ),
                },
                {
                    data: 'SisaBayar',
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
            "ajax": {
                "url": "<?= base_url('payroll/laporan_slipgaji/pinjaman'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.cari_pjm = $("#inp-search-pjm").val();
                    d.bulan_pjm = $("#bulan-pjm").val();
                }
            }
        });

        $('#inp-search-pjm').on('input', function(e) {
            $tablepinjaman.ajax.reload();
        });

        $('#bulan-pjm').on('input', function(e) {
            $tablepinjaman.ajax.reload();
            var url = '<?= base_url('payroll/laporan_slipgaji/cetaklist_pinjaman/') ?>' + $("#bulan-pjm").val();
            $('#cetak-pinjaman').attr('href', url);
        });
    });
</script>