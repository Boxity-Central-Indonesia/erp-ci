<script>
    $('#tgl-transaksi').datepicker({
        // format: 'yyyy-mm-dd',
        dateFormat: "dd-mm-yy",
        altFormat: "yy-mm-dd",
        altField: "#altField",
        autoclose: true
        // defaultDate: new Date()
    });

    $(document).ready(function() {
        $("#btn-tutupbuku").on("click", function() {
            const kode = $(this).data('kode');
            const kode2 = $(this).data('kode2');

            Swal.fire({
                title: 'Peringatan!',
                text: "Apa anda yakin ingin Menutup Buku Tahun Anggaran " + <?= $tahunaktif ?> + "?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya!'
            }).then((result) => {
                if (result.isConfirmed) {
                    tutupbuku(kode, kode2)
                }
            })
        });
    });

    function tutupbuku(kode, kode2) {
        let data = {
            KodeTahun: kode,
            TglTransJurnal: kode2
        }

        get_response("<?= base_url('akuntansi/tutup_buku/simpan') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                showSwal('success', 'Informasi', response.msg).then(function() {
                    window.location.reload();
                });

            }
        })

    }
</script>