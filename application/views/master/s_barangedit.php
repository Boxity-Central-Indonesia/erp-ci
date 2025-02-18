<script>
    $('#KodeGudang').change(function () {
        var kodegudang = $(this).find('option:selected').attr('value');
        var kodebarang = "<?= @$model['KodeBarang'] ?>";
        console.log(kodegudang, kodebarang);
        $.ajax({
            url: "<?php echo site_url('master/Barang/get_ob'); ?>",
            method: "GET",
            data: {KodeGudang: kodegudang, KodeBarang: kodebarang},
            dataType: 'json',
            success: function (data) {
                if (kodegudang) {
                    if (data) {
                        $('#StokOpeningBalance').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.Qty).replace("Rp", "").trim());
                        $('#HPPOpeningBalance').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.HargaSatuan).replace("Rp", "").trim());
                    } else {
                        $('#StokOpeningBalance').val('0');
                        $('#HPPOpeningBalance').val('0');
                    }
                } else {
                    $('#StokOpeningBalance').val('0');
                    $('#HPPOpeningBalance').val('0');
                }
            }
        });
    });

    /* Tanpa Rupiah */
    var tanpa_rupiah2 = document.getElementById('HargaBeliTerakhir');
    tanpa_rupiah2.addEventListener('keyup', function(e)
    {
        tanpa_rupiah2.value = formatRupiah(this.value);
    });

    var tanpa_rupiah3 = document.getElementById('HargaJual');
    tanpa_rupiah3.addEventListener('keyup', function(e)
    {
        tanpa_rupiah3.value = formatRupiah(this.value);
    });

    var tanpa_rupiah4 = document.getElementById('HPPOpeningBalance');
    tanpa_rupiah4.addEventListener('keyup', function(e)
    {
        tanpa_rupiah4.value = formatRupiah(this.value);
    });

    var tanpa_rupiah5 = document.getElementById('StokOpeningBalance');
    tanpa_rupiah5.addEventListener('keyup', function(e)
    {
        tanpa_rupiah5.value = formatRupiah(this.value);
    });

    var tanpa_rupiah6 = document.getElementById('BeratBarang');
    tanpa_rupiah6.addEventListener('keyup', function(e)
    {
        tanpa_rupiah6.value = formatRupiah(this.value);
    });
</script>