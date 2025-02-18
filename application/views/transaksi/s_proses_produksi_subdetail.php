<script>
    const $table = $('#table-prosesprodsubdetail').DataTable({
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
                data: 'TglAktivitas',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JenisAktivitas',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'BatasBawah',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'BatasAtas',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JmlDaun',
                className: 'text-center',
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
                data: 'Biaya',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'Keterangan',
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
        "rowCallback": function( row, data ) {
            if (!(data.BatasBawah)) {
                $('td:eq(3)', row).html( '-' );
            }
            if (!(data.BatasAtas)) {
                $('td:eq(4)', row).html( '-' );
            }
            if (!(data.JmlDaun)) {
                $('td:eq(5)', row).html( '-' );
            }
        },
        "ajax": {
            "url": "<?= base_url('transaksi/proses_produksi/subdetail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.notrans = '<?= $NoTrans ?>';
                d.nourut = '<?= $NoUrut ?>';
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

        $("#table-prosesprodsubdetail").on("click", ".btnhapus", function() {
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
                    hapusterima(kode, kode2)
                }
            })
        });

        $("#form-selesai").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            let data = {
                NoTrans: $('#NoTrans').val(),
                NoUrut: $('#NoUrut').val()
            }
            get_response("<?= base_url('transaksi/proses_produksi/checkJmlAktivitas') ?>", data, function(response) {
                if (response.status === false) {
                    $('#ModalSelesai').modal('hide');
                    showSwal1('warning', 'Peringatan', response.msg);
                    return false;
                } else {
                    simpanselesai(self, data_post);
                }
            });
            return false;
        });
    });

    function hapusterima(kode, kode2) {
        let data = {
            NoTrAktivitas: kode
        }

        get_response("<?= base_url('transaksi/proses_produksi/hapussubdetail') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', response.msg);

            }
        })
    }

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/proses_produksi/simpansubdetail') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    // window.location.reload();
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#table-prosesprodsubdetail").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#Biaya').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Biaya).replace("Rp", "").trim());
        $('#NoTrAktivitas').val(model.NoTrAktivitas);
        $('#JenisAktivitas').val(model.JenisAktivitas);
        $('#TglAktivitas').val(model.TglAktivitas);
        $('#Keterangan').val(model.Keterangan);
        $('#KodeAktivitas').attr('disabled', true);
        $('#KodeAktivitas').val(model.KodeAktivitas).change();
        $('#KodePegawai').attr('disabled', true);
        $('#KodePegawai').val(model.KodePegawai).change();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodeAktivitas').attr('disabled', false);
        $('#KodeAktivitas').val('').change();
        $('#KodeAktivitas').change(function () {
            var kodeaktivitas = $(this).find('option:selected').attr('value');
            console.log(kodeaktivitas);
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataAktivitas'); ?>",
                method: "GET",
                data: {KodeAktivitas: kodeaktivitas},
                dataType: 'json',
                success: function (data) {
                    if (kodeaktivitas) {
                        $('#JenisAktivitas').val(data.JenisAktivitas);
                        $('#Biaya').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.Biaya).replace("Rp", "").trim());
                    } else {
                        $('#JenisAktivitas').val('');
                        $('#Biaya').val('');
                    }
                }
            });
        });
        $('#KodePegawai').attr('disabled', false);
        $('#KodePegawai').val('').change();
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('TglAktivitas').value = now.toISOString().substring(0, 10);
    });

    $("#selesai").on("click", function() {
        $('#form-selesai')[0].reset();
        $('#ModalSelesai').modal('show');
        $('#defaultModalLabel').html('Selesai Proses Produksi');
    });

    function simpanselesai(self, data_post) {
        post_response("<?= base_url('transaksi/proses_produksi/selesai_per_item') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalSelesai').modal('hide');
                showSwal("success", "Informasi", response.msg).then(function() {
                    self[0].reset();
                    window.location.reload();
                });
            } else {
                $('#ModalSelesai').modal('hide');
                showSwal("error", "Peringatan", response.msg);
            }
        });
    }

</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('Qty');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>