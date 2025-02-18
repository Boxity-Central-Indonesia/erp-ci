<script>
    const $table = $('#table-nilaipersediaan').DataTable({
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
        columns: [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": '',
                "render": function (data) {
                    return '<i data-no="'+data.no+'" class="fa fa-caret-right" aria-hidden="true"></i>';
                }
            },
            {
                data: 'no',
                "orderable": false,
                "searchable": false,
                className: 'text-center'
            },
            {
                data: 'KodeManual',
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
                data: 'SatuanBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
                visible: false,
            },
            {
                data: 'Stok',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'NilaiHPP',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'TotalHPP',
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
        "rowCallback": function( row, data ) {
            if (data.Stok) {
                $('td:eq(5)', row).html('<span style="padding-left:30%;">' + Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.Stok).replace("Rp", "").trim() +' '+data.SatuanBarang + '</span>');
            }
        },
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
            "url": "<?= base_url('laporan/nilai_persediaan'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.gudang = $("#combo-gudang").val();
                d.jenis = $("#combo-jenis").val();
                d.stock = $("#combo-stock").val();
            }
        }
    });

    // Add event listener for opening and closing details
    $('#table-nilaipersediaan tbody').on('click', 'td.details-control i.fa', function () {
        var tr = $(this).closest('tr');
        const nomor = $(this).data('no');
        console.log(nomor);
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
            row.child('<table cellpadding="5" cellspacing="0" border="0" style="background-color:#e3e4e6;">' +
                '<thead><tr>' +
                    '<th style="text-align:center; width:10%;">Gudang</th>' +
                    '<th style="text-align:center; width:10%;">Stok</th>' +
                '</tr></thead><tbody id="isian'+nomor+'"></tbody>' +
            '</table>').show();
            format(row.data(), nomor);
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

    function format(d, n){        
         // `d` is the original data object for the row
        const listgudang = d.listgudang;
        console.log(listgudang);
        listgudang.forEach(function(key) {
            $('#isian'+n).append('<tr>' +
                    '<td style="text-align:center; width:10%;">' + key.NamaGudang + '</td>' +
                    '<td style="text-align:center; width:10%;">' + Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(key.StokGudang).replace("Rp", "").trim() + ' ' + key.SatuanBrg + '</td>' +
                '</tr>');
        });
    }

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#combo-gudang").on('change', function() {
        $table.ajax.reload();
    });

    $('#combo-jenis').on('input', function(e) {
        $table.ajax.reload();
    });

    $('#combo-stock').on('input', function(e) {
        $table.ajax.reload();
    });

    var cari = '';
    var gudang = '';
    var jenis = '';
    var stock = '';
    loadtotalhpp(gudang, jenis, cari, stock);
    function loadtotalhpp(gudang, jenis, cari, stock) {
        let data = {
            gudang: gudang,
            jenis: jenis,
            cari: cari,
            stock: stock
        }

        get_response("<?= base_url('laporan/nilai_persediaan/get_total_hpp') ?>", data, function(response) {
            $('#TotalNilaiHPP').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(response.total).replace("Rp", "").trim());
        });
    }

    function filtrasi() {
        var gudang  = $('#combo-gudang').val();
        var jenis   = $('#combo-jenis').val();
        var cari    = $('#inp-search').val();
        var stock   = $('#combo-stock').val();
        var url = '<?= base_url('laporan/nilai_persediaan/cetak?gudang=') ?>' + btoa(gudang) + '&jenis=' + btoa(jenis) + '&cari=' + cari + '&stock=' + btoa(stock);
        $('#btn-cetak').attr('href', url);
        loadtotalhpp(gudang, jenis, cari, stock);
        console.log(url);
    }

    $(document).ready(function() {});

    function simpanhpp() {
        Swal.fire({
            title: 'Apa anda yakin?',
            text: "menyimpan nilai persediaan barang",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FA7C41',
            cancelButtonColor: '#FA7C41',
            confirmButtonText: 'Ya, Simpan!'
        }).then((result) => {
            if (result.isConfirmed) {
                var bulan = "<?= date('Y-m') ?>";
                simpan(bulan)
            }
        })
    }

    function simpan(bulan) {
        let data = {
            bulan: bulan
        }

        get_response("<?= base_url('laporan/nilai_persediaan/simpan_nilai_persediaan') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                showSwal('success', 'Informasi', response.msg);

            }
        })

    }
</script>