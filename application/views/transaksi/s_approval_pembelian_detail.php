<script>
    const $table = $('#table-approvaldetail').DataTable({
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
                data: 'NamaBarang',
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
                data: 'HargaSatuan',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'Qty',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Total',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center'

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
                .column(5)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var amount = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total).replace("Rp", "").trim();
 
            // Update footer
            $(api.column(5).footer()).html(amount);
        },
        "rowCallback": function( row, data ) {
            if (data.Qty) {
                $('td:eq(3)', row).html('<span style="padding-left:30%;">' + data.Qty+' '+data.SatuanBarang + '</span>');
            }
        },
        "ajax": {
            "url": "<?= base_url('transaksi/approval_pembelian/detail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.idtransbeli = '<?= $IDTransBeli ?>';
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#combo-status").on('change', function() {
        $table.ajax.reload();
    });

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            let data = {
                NoRef_Manual: $('#NoRef_Manual').val()
            }
            get_response("<?= base_url('transaksi/transaksi_pembelian/checkManualCode') ?>", data, function(response) {
                if (response.status === false) {
                    $('#ModalTambah').modal('hide');
                    showSwal1('warning', 'Peringatan', response.msg).then(function() {
                        $('#ModalTambah').modal('show');
                        $('#defaultModalLabel').html('Approve Transaksi Pembelian');
                    });
                    return false;
                } else {
                    simpan(self, data_post);
                }
            });
            return false;
        });
    });

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/approval_pembelian/approve') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                    showSwal("success", "Informasi", response.msg).then(function() {
                        $table.ajax.reload(null, false);
                        self[0].reset();
                        if (response.stj == 'on') {
                            window.location.reload();
                        } else {
                            window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnalmanual/') ?>" + btoa("trans_beli") + "/" + btoa(unescape(encodeURIComponent(response.idjurnal))) + "/" + btoa("<?= $IDTransBeli ?>") + "/" + btoa("approval_pembelian/detail");
                        }
                    });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#btn-approve").on("click", function() {
        $('#form-simpan')[0].reset();
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('ApprovedDate').value = now.toISOString().slice(0,16);
        document.getElementById('TanggalPembelian').value = now.toISOString().slice(0,16);
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Approve Transaksi Pembelian');
    });
</script>