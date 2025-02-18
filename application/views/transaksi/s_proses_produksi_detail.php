<script>
    const $table = $('#table-prosesproddetail').DataTable({
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
        columns: [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": '',
                "render": function () {
                    return '<i class="fa fa-caret-right" aria-hidden="true"></i>';
                }
            },
            {
                data: 'no',
                "orderable": false,
                "searchable": false,
                className: 'text-center'
            },
            {
                data: 'NoTrans',
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
                className: 'text-center',
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
                data: 'TanggalTransaksi',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'ProdTglSelesai',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Status',
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
        "ajax": {
            "url": "<?= base_url('transaksi/proses_produksi/detail_old'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.tgl = $("#tgl-transaksi").val();
                d.noreftrsistem = '<?= $NoRefTrSistem ?>';
            }
        }
    });

    // Add event listener for opening and closing details
    $('#table-prosesproddetail tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        console.log("test")
        var tdi = tr.find("i.fa");
        var row = $table.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
            tdi.first().removeClass('fa-caret-down');
            tdi.first().addClass('fa-caret-right');
        } else {
            // Open this row
            row.child(format(row.data())).show();
            tr.addClass('shown');
            tdi.first().removeClass('fa-caret-right');
            tdi.first().addClass('fa-caret-down');
        }
    });

    $table.on("user-select", function (e, dt, type, cell, originalEvent) {
        if ($(cell.node()).hasClass("details-control")) {
            e.preventDefault();
        }
    });

    function format(d){        
         // `d` is the original data object for the row
        return '<table cellpadding="5" cellspacing="0" border="0" style="background-color:#e3e4e6;">' +
            '<tr>' +
                '<th style="text-align:center; width:10%;">Pc/Np</th>' +
                '<th style="text-align:center; width:10%;">T. Cetak</th>' +
                '<th style="text-align:center; width:10%;">Potong</th>' +
                '<th style="text-align:center; width:10%;">Kasar</th>' +
                '<th style="text-align:center; width:10%;">Bubut CR</th>' +
                '<th style="text-align:center; width:10%;">Bubut T</th>' +
                '<th style="text-align:center; width:10%;">Bubut R</th>' +
                '<th style="text-align:center; width:10%;">Halus</th>' +
                '<th style="text-align:center; width:10%;">Berat Kotor(kg)</th>' +
                '<th style="text-align:center; width:10%;">Berat Bersih(kg)</th>' +
            '</tr>' +
            '<tr>' +
                '<td style="text-align:center; width:10%;">' + d.KodeProduksi + '</td>' +
                '<td style="text-align:center; width:10%;">' + d.Cetak + '</td>' +
                '<td style="text-align:center; width:10%;">' + d.Potong + '</td>' +
                '<td style="text-align:center; width:10%;">' + d.Kasar + '</td>' +
                '<td style="text-align:center; width:10%;">' + d.CR + '</td>' +
                '<td style="text-align:center; width:10%;">' + d.T + '</td>' +
                '<td style="text-align:center; width:10%;">' + d.R + '</td>' +
                '<td style="text-align:center; width:10%;">' + d.Halus + '</td>' +
                '<td style="text-align:center; width:10%;">' + d.BeratKotor + '</td>' +
                '<td style="text-align:center; width:10%;">' + d.BeratBersih + '</td>' +
            '</tr>' +
        '</table>';  
    }

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
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            locale: {
                format: "DD-MM-YYYY"
            }
        },
        function(start, end) {
            $('#tgl-transaksi').val(start.format("DD-MM-YYYY") + ' - ' + end.format("DD-MM-YYYY"));
            $table.ajax.reload();
        }
    );

    function changeIcon(anchor) {
        var icon = anchor.querySelector("i");
        icon.classList.toggle('fa-caret-right');
        icon.classList.toggle('fa-caret-down');
    }

    $(document).ready(function() {
        $("#btn-selesai").on("click", function () {
            const kode = $(this).data('kode');
            const kode2 = $(this).data('kode2');
            Swal.fire({
                title: 'Apa anda yakin?',
                text: "Proses produksi dinyatakan selesai!",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Simpan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    selesaiproduksi(kode, kode2)
                }
            })
        });
    });

    function selesaiproduksi(kode, kode2) {
        let data = {
            IDTransJual: kode,
            NoTrans: kode2
        }

        get_response("<?= base_url('transaksi/proses_produksi/checkQty') ?>", data, function(response) {
            if (response.status === false) {
                showSwal1('warning', 'Peringatan', response.msg);
                return false;
            } else {
                get_response("<?= base_url('transaksi/proses_produksi/selesai') ?>", data, function(response) {
                    if (response.status === false) {
                        showSwal('error', 'Peringatan', response.msg);
                        return false;
                    } else {
                        showSwal('success', 'Informasi', 'Proses produksi berhasil disimpan.').then(function() {
                            window.location.reload();
                        });
        
                    }
                })
            }
        });

    }
</script>