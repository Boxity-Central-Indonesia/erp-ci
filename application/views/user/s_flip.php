<script>
    const $table = $('#table-flip').DataTable({
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
                data: 'Title',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'SenderBankType',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'BankName',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NoRekening',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Amount',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 0, ),
            },
            {
                data: 'ExpDate',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'PaymentStatus',
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
            if (!(data.NoRekening)) {
                $('td:eq(4)', row).html( '-' );
            }
        },
        "ajax": {
            "url": "<?= base_url('user/flip'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
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
        post_response("<?= base_url('user/flip/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    window.open(response.link, "_blank");
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    var kodePerusahaan  = "<?= $companyCode ?>",
        thnSekarang     = "<?= $curYear ?>";
    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#SenderBank').val('').change();
        var randomNum       = Math.floor(1000 + Math.random() * 9000);
        $('#Title').val(kodePerusahaan + randomNum + thnSekarang);
        $('#defaultModalLabel').html('Tambah Data');
        $('#view_file').hide();
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('Amount');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>