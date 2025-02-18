<script>
    const $table = $('#table-podetail').DataTable({
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
            "url": "<?= base_url('transaksi/transaksi_po/detail'); ?>",
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
            simpan(self, data_post);
            return false;
        });

        $("#table-podetail").on("click", ".btnhapus", function() {
            const kode = $(this).data('kode');
            const kode2 = $(this).data('kode2');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "data terhapus tidak dapat di kembalikan",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Hapus data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    hapus(kode, kode2)
                }
            })
        });
    });

    function hapus(kode, kode2) {
        let data = {
            IDTransBeli: kode,
            NoUrut: kode2
        }

        get_response("<?= base_url('transaksi/transaksi_po/hapusdetail') ?>", data, function(response) {
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
        post_response("<?= base_url('transaksi/transaksi_po/simpandetail') ?>", data_post, function(response) {
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

    $("#table-podetail").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoUrut').val(model.NoUrut);
        $('#TotalLama').val(model.Total);
        $('#KodeBarang').val(model.KodeBarang).change();
        $('#KodeBarang').attr('disabled', true);
        $('#HargaSatuan').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.HargaSatuan).replace("Rp", "").trim());
        $('#Qty').val(model.Qty);
        $('#SatuanBarang').val(model.SatuanBarang);
        document.getElementById("SatuanEdit1").innerHTML = model.SatuanBarang;
        $('#Spesifikasi').val(model.Spesifikasi);
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
                        document.getElementById("SatuanEdit1").innerHTML = data.SatuanBarang;
                    } else {
                        $('#SatuanBarang').val('');
                        $('#HargaSatuan').val('');
                        $('#Spesifikasi').val('');
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