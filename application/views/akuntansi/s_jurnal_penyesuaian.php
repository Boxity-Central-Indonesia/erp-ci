<script>
    const $table = $('#table-jrnpenyesuaian').DataTable({
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
                data: 'IDTransJurnal',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NoRefTrans',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'KodeTahun',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'TglTransJurnal',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NarasiJurnal',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NominalTransaksi',
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                className: 'text-right',
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
        //     if (!(data.NoRefTrans)) {
        //         $('td:eq(2)', row).html( '-' );
        //     }
        // },
        "ajax": {
            "url": "<?= base_url('akuntansi/jurnal_penyesuaian'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.kodetahun = $("#kode-tahun").val();
                d.tgl = $("#tgl-transaksi").val();
            }
        }
    });

    $('#inp-search').on('input', function(e) {
        $table.ajax.reload();
    });

    $("#kode-tahun").on('change', function() {
        $table.ajax.reload();
        // var url = '<?= base_url('akuntansi/jurnal_penyesuaian/cetak/') ?>' + btoa($("#kode-tahun").val());
        // $('#btn-cetak').attr('href', url);
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
                NoRefTrans: $('#NoRefTrans').val(),
                Manual_Lama: $('#Manual_Lama').val()
            }
            get_response("<?= base_url('akuntansi/jurnal_penyesuaian/checkManualCode') ?>", data, function(response) {
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

        $("#table-jrnpenyesuaian").on("click", ".btnhapus", function() {
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
            IDTransJurnal: kode
        }

        get_response("<?= base_url('akuntansi/jurnal_penyesuaian/hapus') ?>", data, function(response) {
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
        post_response("<?= base_url('akuntansi/jurnal_penyesuaian/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    if (response.action === 'tambah') {
                        window.location.href = "<?= base_url('akuntansi/jurnal_penyesuaian/jurnal/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                    }
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#table-jrnpenyesuaian").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#IDTransJurnal').val(model.IDTransJurnal);
        $('#Manual_Lama').val(model.NoRefTrans);
        $('#NoRefTrans').val(model.NoRefTrans);
        $('#TglTransJurnal').val(model.TglTransJurnal);
        $('#NarasiJurnal').val(model.NarasiJurnal);
        $('#NominalTransaksi').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.NominalTransaksi).replace("Rp", "").trim());
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('TglTransJurnal').value = now.toISOString().slice(0,16);
        $('#view_file').hide();
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('NominalTransaksi');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>