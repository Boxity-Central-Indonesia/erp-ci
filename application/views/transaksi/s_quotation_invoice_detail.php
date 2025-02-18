<script>
    const $table = $('#table-qidetail').DataTable({
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
                data: 'Barang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JenisBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Kategory',
                className: 'text-center',
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
                className: 'text-center',
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
        "rowCallback": function( row, data ) {
            if (data.Qty) {
                $('td:eq(5)', row).html('<span style="padding-left:30%;">' + data.Qty+' '+data.SatuanBarang + '</span>');
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
                .column(6)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var amount = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total).replace("Rp", "").trim();
 
            // Update footer
            $(api.column(6).footer()).html(amount);
        },
        "ajax": {
            "url": "<?= base_url('transaksi/quotation_invoice/detail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.idtransjual = '<?= $IDTransJual ?>';
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
            simpan(self, data_post);
            return false;
        });
    });

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/quotation_invoice/simpandetail') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#table-qidetail").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoUrut').val(model.NoUrut);
        $('#TotalLama').val(model.Total);
        $('#KodeBarang').val(model.KodeBarang).change();
        $('#KodeBarang').attr('disabled', true);
        $('#HargaSatuan').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.HargaSatuan).replace("Rp", "").trim());
        $('#JenisBarang').val(model.JenisBarang);
        $('#Kategory').val(model.Kategory);
        $('#SatuanBarang').val(model.SatuanBarang);
        $('#Spesifikasi').val(model.Spesifikasi);
        $('#Qty').val(model.Qty);
        $('#AdditionalName').val(model.AdditionalName);
        document.getElementById("SatuanEdit1").innerHTML = model.SatuanBarang;
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodeBarang').attr('disabled', false);
        $('#KodeBarang').val('').change();
        $('#KodeBarang').change(function () {
            var kodeBrg = $(this).find('option:selected').attr('value');
            console.log(kodeBrg);
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataBarang'); ?>",
                method: "GET",
                data: {KodeBarang: kodeBrg},
                dataType: 'json',
                success: function (data) {
                    if (kodeBrg) {
                        $('#SatuanBarang').val(data.SatuanBarang);
                        $('#HargaSatuan').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.HargaBeliTerakhir).replace("Rp", "").trim());
                        $('#Spesifikasi').val(data.Spesifikasi);
                        $('#JenisBarang').val(data.NamaJenisBarang);
                        $('#Kategory').val(data.KodeJenis);
                        document.getElementById("SatuanEdit1").innerHTML = data.SatuanBarang;
                    } else {
                        $('#SatuanBarang').val('');
                        $('#HargaSatuan').val('');
                        $('#Spesifikasi').val('');
                        $('#JenisBarang').val('');
                        $('#Kategory').val('');
                        document.getElementById("SatuanEdit1").innerHTML = "";
                    }
                }
            });
        });
        $('#view_file').hide();
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('HargaSatuan');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>