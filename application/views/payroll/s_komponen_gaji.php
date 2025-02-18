<script>
    const $table = $('#table-kompgaji').DataTable({
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
        columns: [{
                data: 'no',
                "orderable": false,
                "searchable": false,
                className: 'text-center'
            },
            {
                data: 'NamaKomponenGaji',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JenisKomp',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'CaraHitung',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Kriteria',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NamaJabatan',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NominalRp',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'IsAktif',
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
            if (!(data.KodeJabatan)) {
                $('td:eq(5)', row).html( '-' );
            }
        },
        "ajax": {
            "url": "<?= base_url('payroll/komponen_gaji'); ?>",
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

        $("#table-kompgaji").on("click", ".btnhapus", function() {
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

        get_response("<?= base_url('payroll/komponen_gaji/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('payroll/komponen_gaji/simpan') ?>", data_post, function(response) {
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

    $("#table-kompgaji").on("click", '.btnaktif', function() {
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

            get_response("<?= base_url('payroll/komponen_gaji/aktif') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', 'Data Komponen Gaji gagal diubah.');
                    return false;
                } else {
                    $table.ajax.reload();
                    showSwal('success', 'Berhasil', 'Data Komponen Gaji berhasil diubah.');

                }
            })
        }

    })

    $("#table-kompgaji").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#KodeKompGaji').val(model.KodeKompGaji);
        $('#NamaKomponenGaji').val(model.NamaKomponenGaji);
        $('#JenisKomponen').val(model.JenisKomponen).change();
        $('#NominalRp').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.NominalRp).replace("Rp", "").trim());
        $('#Deskripsi').val(model.Deskripsi);
        $('#CaraHitung').val(model.CaraHitung);
        $('#Kriteria').val(model.Kriteria);
        $('#KodeJabatan').val(model.KodeJabatan).change();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodeJabatan').val('').change();
        $('#JenisKomponen').val('').change();
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('NominalRp');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>