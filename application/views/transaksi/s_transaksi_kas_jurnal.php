<script>
    var totaldebet = 0;
    var totalkredit = 0;
    const $table = $('#table-jurnalkas').DataTable({
        "paging": false,
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
                className: 'text-center, vertical-align'
            },
            {
                data: 'KodeAkun',
                className: 'text-left, vertical-align',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NamaAkun',
                className: 'text-left, vertical-align',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Uraian',
                className: 'text-left, vertical-align',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Debet',
                className: 'text-right, vertical-align',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'Kredit',
                className: 'text-right, vertical-align',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center, vertical-align'

            },
        ],
        columnDefs: [
            {
                render: function (data, type, full, meta) {
                    return "<div class='text-wrap width-250'>" + data + "</div>";
                },
                targets: 3
            }
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
 
            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
 
            // Total over all pages
            total = api
                .column(4)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var amount = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total).replace("Rp", "").trim();

            total2 = api
                .column(5)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var amount2 = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total2).replace("Rp", "").trim();
 
            // Update footer
            $(api.column(4).footer()).html(amount);
            $(api.column(5).footer()).html(amount2);
            totaldebet = amount;
            totalkredit = amount2;
        },
        "ajax": {
            "url": "<?= base_url('transaksi/transaksi_kas/jurnal'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.jabatan = $("#combo-jab").val();
                d.idtransjurnal = "<?= $IDTransJurnal ?>";
            }
        }
    });

    function wordWrap(str, maxWidth) {
        var newLineStr = "\n"; done = false; res = '';
        while (str.length > maxWidth) {                 
            found = false;
            // Inserts new line at first whitespace of the line
            for (i = maxWidth - 1; i >= 0; i--) {
                if (testWhite(str.charAt(i))) {
                    res = res + [str.slice(0, i), newLineStr].join('');
                    str = str.slice(i + 1);
                    found = true;
                    break;
                }
            }
            // Inserts new line at maxWidth position, the word is too long to wrap
            if (!found) {
                res += [str.slice(0, maxWidth), newLineStr].join('');
                str = str.slice(maxWidth);
            }

        }

        return res + str;
    }

    function testWhite(x) {
        var white = new RegExp(/^\s$/);
        return white.test(x.charAt(0));
    };

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#combo-status").on('change', function() {
        $table.ajax.reload();
    });

    $("#combo-jab").on('change', function() {
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

        $("#table-jurnalkas").on("click", ".btnhapus", function() {
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

        $("#check_simpanjurnal").on("click", function() {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "jurnal akan tersimpan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya'
            }).then((result) => {
                if (result.isConfirmed) {
                    checknominal(kode)
                }
            })
        });
    });

    function checknominal(kode) {
        let data = {
            IDTransJurnal: kode,
            totaltransaksi: "<?= $dtjurnal['NominalTransaksi'] ?>",
            totaldebet: totaldebet,
            totalkredit: totalkredit,
            notrans: "",
            url: ""
        }

        get_response("<?= base_url('transaksi/transaksi_kas/checkNominal') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Jurnal berhasil tersimpan.').then(function() {
                    window.open( "<?= base_url('laporan/neraca') ?>", "_blank");
                    window.location.href = "<?= base_url() ?>" + response.url;
                });
            }
        })
    }

    function hapus(kode, kode2) {
        let data = {
            IDTransJurnal: kode,
            NoUrut: kode2
        }

        get_response("<?= base_url('transaksi/transaksi_kas/hapusjurnal') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Data berhasil dihapus.');

            }
        })

    }

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/transaksi_kas/simpanjurnal') ?>", data_post, function(response) {
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

    $("#table-jurnalkas").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoUrut').val(model.NoUrut);
        $('#Uraian').val(model.Uraian);
        if (model.Debet > 0) {
            $('#JenisJurnal').val('Debet');
            $('#Nominal').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Debet).replace("Rp", "").trim());
        } else {
            $('#JenisJurnal').val('Kredit');
            $('#Nominal').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Kredit).replace("Rp", "").trim());
        }
        $('#NamaAkun').val(model.NamaAkun);
        $('#KodeAkun').attr('disabled', false);
        $('#KodeAkun').val(model.KodeAkun).change();
        $('#KodeAkun').change(function () {
            var kodeakun = $(this).find('option:selected').attr('value');
            console.log(kodeakun);
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataKodeAkun'); ?>",
                method: "GET",
                data: {KodeAkun: kodeakun},
                dataType: 'json',
                success: function (data) {
                    if (kodeakun) {
                        $('#NamaAkun').val(data.NamaAkun);
                    } else {
                        $('#NamaAkun').val('');
                    }
                }
            });
        });
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#NoUrut').val('');
        $('#KodeAkun').attr('disabled', false);
        $('#KodeAkun').val('').change();
        $('#KodeAkun').change(function () {
            var kodeakun = $(this).find('option:selected').attr('value');
            console.log(kodeakun);
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataKodeAkun'); ?>",
                method: "GET",
                data: {KodeAkun: kodeakun},
                dataType: 'json',
                success: function (data) {
                    if (kodeakun) {
                        $('#NamaAkun').val(data.NamaAkun);
                    } else {
                        $('#NamaAkun').val('');
                    }
                }
            });
        });
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('Nominal');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>