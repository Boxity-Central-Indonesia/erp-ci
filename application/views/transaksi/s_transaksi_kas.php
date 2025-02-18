<script>
    const $table = $('#table-transkas').DataTable({
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
                data: 'NoTransKas',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NoRef_Manual',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
                visible: false,
            },
            {
                data: 'TanggalTransaksi',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TanggalJatuhTempo',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JenisTransaksiKas',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TotalTransaksi',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
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
                data: 'ActualName',
                className: 'text-left',
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
        // "rowCallback": function( row, data ) {
        //     if (!(data.NoRef_Manual)) {
        //         $('td:eq(2)', row).html( '-' );
        //     }
        // },
        "ajax": {
            "url": "<?= base_url('transaksi/transaksi_kas'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.jenis = $("#combo-jenis").val();
                d.status = $("#combo-status").val();
                d.tgl = $("#tgl-transaksi").val();
            }
        }
    });

    // Add event listener for opening and closing details
    $('#table-transkas tbody').on('click', 'td.details-control', function () {
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
         // `d` is the original data object for the row style="background-color:#e3e4e6;"
        return '<table cellpadding="5" cellspacing="0" border="0"' +
            '<tr>' +
                '<th style="text-align:left; width:15%;">Tahun Anggaran</th>' +
                '<td style="text-align:left; width:85%;">:&nbsp;' + d.KodeTahun + '</td>' +
            '</tr>' +
            '<tr>' +
                '<th style="text-align:left; width:15%;">Uraian</th>' +
                '<td style="text-align:left; width:85%;">:&nbsp;' + d.Uraian + '</td>' +
            '</tr>' +
        '</table>';  
    }

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#combo-jenis").on('change', function() {
        $table.ajax.reload();
        var url = '<?= base_url('transaksi/transaksi_kas/cetak/') ?>' + btoa($("#combo-jenis").val());
        $('#btn-cetak').attr('href', url);
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

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            let data = {
                NoRef_Manual: $('#NoRef_Manual').val(),
                Manual_Lama: $('#Manual_Lama').val()
            }
            get_response("<?= base_url('transaksi/transaksi_kas/checkManualCode') ?>", data, function(response) {
                if (response.status === false) {
                    $('#ModalTambah').modal('hide');
                    showSwal1('warning', 'Peringatan', response.msg).then(function() {
                        $('#ModalTambah').modal('show');
                        $('#defaultModalLabel').html('Tambah Data');
                    });
                    return false;
                } else {
                    simpan(self, data_post);
                }
            });
            return false;
        });

        $("#table-transkas").on("click", ".btnhapus", function() {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "data terhapus tidak dapat di kembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Hapus data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    hapus(kode)
                }
            })
        });
    });

    function hapus(kode) {
        let data = {
            NoTransKas: kode
        }

        get_response("<?= base_url('transaksi/transaksi_kas/hapus') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Data berhasil dihapus.');

            }
        })

    }

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/transaksi_kas/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    if (response.action == 'tambah' && response.bayar == 'PAID') {
                        window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnal/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                    } else if (response.action == 'edit' && response.bayar != 'PAID') {
                        window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnal/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
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

    $("#table-transkas").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoTransKas').val(model.NoTransKas);
        $('#Manual_Lama').val(model.NoRef_Manual);
        $('#NoRef_Manual').val(model.NoRef_Manual);
        $('#TanggalTransaksi').val(model.TanggalTransaksi);
        $('#JenisTransaksiKas').val(model.JenisTransaksiKas);
        $('#Uraian').val(model.Uraian);
        $('#TanggalJatuhTempo').val(model.TanggalJatuhTempo);
        $('#Status').attr('disabled', true);
        $('#Status').val(model.Status);
        $('#StatusEdit').val(model.Status);
        $('#TotalTransaksi').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.TotalTransaksi).replace("Rp", "").trim());
        if (model.Status == 'PAID') {
            $('#TotalTransaksi').attr('readonly', true);
            $('#btnsave').html('Simpan');
            $('#btnsave').addClass('btn btn-primary waves-effect');
        } else {
            $('#TotalTransaksi').attr('readonly', false);
            $('#btnsave').html('Bayar');
            $('#btnsave').addClass('btn btn-success waves-effect');
        }
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#btnsave').html('Simpan');
        $('#btnsave').addClass('btn btn-primary waves-effect');
        $('#Status').attr('disabled', false);
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('TanggalTransaksi').value = now.toISOString().slice(0,16);
        $('#view_file').hide();
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('TotalTransaksi');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>