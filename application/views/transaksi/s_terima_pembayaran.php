<script>
    var statusjrn = '<?= $status_jurnal ?>';
    var isVisibleColumns = statusjrn == 'off' ? true : false;
    const $table = $('#table-terimabayar').DataTable({
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
                data: 'IDTransJual',
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
                data: 'TotalTagihan',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'TotalBayar',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'SisaTagihan',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'DibayarSekarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center',
                visible: isVisibleColumns,

            },
        ],
        "ajax": {
            "url": "<?= base_url('transaksi/transaksi_penjualan/terimapembayaran'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.kodeperson = '<?= $KodePerson ?>';
                d.status_jurnal = '<?= $status_jurnal ?>';
            }
        }
    });

    // Add event listener for opening and closing details
    $('#table-terimabayar tbody').on('click', 'td.details-control i.fa', function () {
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
                    '<th style="text-align:center; width:10%;">Kode Transkasi</th>' +
                    '<th style="text-align:center; width:10%;">Tgl Terima Piutang</th>' +
                    '<th style="text-align:center; width:10%;">Jenis Bayar</th>' +
                    '<th style="text-align:center; width:10%;">Nominal</th>' +
                    '<th style="text-align:center; width:10%;">Diterima Oleh</th>' +
                    '<th style="text-align:center; width:5%;">#</th>' +
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
        const dthistory = d.dthistory;
        dthistory.forEach(function(key) {
            var tgl = key.TanggalTransaksi.split(' ');
            var tgl2 = tgl[0].split('-');
            var finaltgl = tgl2[2]+'/'+tgl2[1]+'/'+tgl2[0];
            var tahunaktif = "<?= $tahunaktif ?>";
            var hdn = (key.JenisTransaksiKas == 'DP PENJUALAN' || key.KodeTahun != tahunaktif) ? 'hidden' : '';
            $('#isian'+n).append('<tr>' +
                    '<td style="text-align:center; width:10%;">' + key.NoTransKas + '</td>' +
                    '<td style="text-align:center; width:10%;">' + finaltgl + '</td>' +
                    '<td style="text-align:center; width:10%;">' + key.JenisTransaksiKas + '</td>' +
                    '<td style="width:10%;" class="text-right">' + Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(key.TotalTransaksi).replace("Rp", "").trim() + '</td>' +
                    '<td style="text-align:center; width:10%;">' + key.ActualName + '</td>' +
                    '<td style="text-align:center; width:10%;">' + '<a class="btnedit" href="javascript:(0)" type="button" onclick="editbayar(' + "'" + key.NoTransKas + "'" + ')" title="Edit" ' + hdn + '><i class="fa fa-edit"></i></a>' + '</td>' +
                '</tr>');
        });
    }

    function editbayar(id) {
        $('#NoTransKas').val(id);
        $.ajax({
            url: "<?php echo site_url('user/Lokasi/DataTransaksi'); ?>",
            method: "GET",
            data: {NoTransKas: id},
            dataType: 'json',
            success: function (data) {
                var tgl = data.TanggalTransaksi.split(' ');
                var tgl2 = tgl[0].split('-');
                var finaltgl = tgl2[2]+'/'+tgl2[1]+'/'+tgl2[0];
                $('#Tgl').val(finaltgl);
                $('#JenisTransaksiKas').val(data.JenisTransaksiKas);
                $('#TotalEdit').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.TotalTransaksi).replace("Rp", "").trim());
                $("#KodeTahun").val(data.KodeTahun);
                $("#TanggalTransaksi").val(data.TanggalTransaksi);
                // console.log(data);
            }
        });
        $('#ModalEdit').modal('show');
        $('#defaultModalLabel').html('Edit Bayar Hutang');
    }

    $("#KodeAkun").change(function() {
        var kodeakun = $(this).find('option:selected').attr('value');
        if (kodeakun != '') {
            $("#KodeAkunInp").val(kodeakun);
        } else {
            $("#KodeAkunInp").val('');
        }
    });

    $(document).ready(function() {
        $("#form-terimabayar").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpanbayar(self, data_post);
            return false;
        });

        $("#table-terimabayar").on("click", ".simpanperrow", function() {
            const kode = $(this).data('kode');
            const kode2 = $(this).data('kode2');
            kirimpertrans(kode, kode2)
        });

        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            update(self, data_post);
            return false;
        });
    });

    function kirimpertrans(kode, kode2) {
        let data = {
            IDTransJual: kode,
            no: kode2,
            nilaibayar: $('#Bayar'+kode2).val()
        }

        get_response("<?= base_url('transaksi/transaksi_penjualan/kirimpertransaksi') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                showSwal('success', 'Informasi', response.msg).then(function() {
                    window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnalmanual/') ?>" + btoa("trans_jual") + "/" + btoa(unescape(encodeURIComponent(response.idjurnal))) + "/" + btoa(unescape(encodeURIComponent('<?= $IDTransJual ?>')));
                    self[0].reset();
                    $table.ajax.reload();
                });

            }
        })
    }

    function simpanbayar(self, data_post) {
        post_response("<?= base_url('transaksi/transaksi_penjualan/terimapembayaranproses') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", response.msg).then(function() {
                    self[0].reset();
                    if (response.stj == 'off' && response.cn > 0) {
                        window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnalmanual/') ?>" + btoa("trans_jual") + "/" + btoa(unescape(encodeURIComponent(response.idjurnal))) + "/" + btoa(unescape(encodeURIComponent('<?= $IDTransJual ?>')));
                    } else {
                        window.location.reload();
                    }
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    function update(self, data_post) {
        post_response("<?= base_url('transaksi/transaksi_penjualan/editpertransaksi') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalEdit').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data.").then(function() {
                    self[0].reset();
                    window.location.reload();
                });
            } else {
                $('#ModalEdit').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }
</script>