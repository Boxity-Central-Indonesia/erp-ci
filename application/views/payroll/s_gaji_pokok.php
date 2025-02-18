<script>
    const $table = $('#table-gajipokok').DataTable({
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
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JabatanAtasan',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JenisPegawai',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'GajiPokok',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'IsGajiHarian',
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
            if (!(data.JabatanAtasan)) {
                $('td:eq(4)', row).html( '-' );
            }
            if (!(data.JenisPegawai)) {
                $('td:eq(5)', row).html( '-' );
            }
        },
        "ajax": {
            "url": "<?= base_url('payroll/gaji_pokok'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.jabatan = $("#combo-jab").val();
            }
        }
    });

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

        $("#table-gajipokok").on("click", ".btnhapus", function() {
            const kode = $(this).data('kode');

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
                    hapus(kode)
                }
            })
        });
    });

    function hapus(kode) {
        let data = {
            KodeKompGaji: kode
        }

        get_response("<?= base_url('payroll/gaji_pokok/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('payroll/gaji_pokok/simpan') ?>", data_post, function(response) {
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

    $("#table-gajipokok").on("click", '.btnaktif', function() {
        const value = $(this).data('value');
        const kode = $(this).data('kode');

        showconfirm("Apakah anda yakin " + (value > 0 ? 'mengaktifkan' : 'menonaktifkan') + " data?", '').then((result) => {
            if (result.isConfirmed) {
                aktif(value, kode);
            }
        })

        function aktif(value, kode) {
            let data = {
                IsAktif: value,
                KodeKompGaji: kode
            }

            get_response("<?= base_url('payroll/gaji_pokok/aktif') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', 'Data gagal diubah.');
                    return false;
                } else {
                    $table.ajax.reload();
                    showSwal('success', 'Berhasil', 'Data berhasil diubah.');

                }
            })
        }

    })

    $("#table-gajipokok").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#KodePegawai').val(model.KodePegawai);
        $('#NamaPegawai').val(model.NamaPegawai);
        $('#NIP').val(model.NIP);
        $('#KodeJabatan').val(model.KodeJabatan).change();
        $('#KodeJabAtasanLangsung').val(model.KodeJabAtasanLangsung).change();
        $('#JenisPegawai').val(model.JenisPegawai);
        $('#GajiPokok').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.GajiPokok).replace("Rp", "").trim());
        $("#IsGajiHarian").prop("checked", model.IsGajiHarian == 1);
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodeJabatan').val('').change();
        $('#KodeJabAtasanLangsung').val('').change();
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('GajiPokok');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>