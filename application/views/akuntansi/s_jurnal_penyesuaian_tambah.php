<script>
    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            let data = {
                nominaltransaksi: parseInt($('#NominalTransaksi').val().replace(/\./g,'')),
                total_debet: parseInt($('#TotalDebet').val().replace(/\./g,'')),
                total_kredit: parseInt($('#TotalKredit').val().replace(/\./g,''))
            }
            get_response("<?= base_url('akuntansi/jurnal_penyesuaian/check_total') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal1('error', 'Gagal', response?.msg);
                    return false;
                } else {
                    simpan(self, data_post);
                }
            });
            return false;
        });
    });

    function simpan(self, data_post) {
        post_response("<?= base_url('akuntansi/jurnal_penyesuaian/simpanlangsung') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    window.location.href = "<?= base_url('akuntansi/jurnal_penyesuaian') ?>";
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    var now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    var tgl = "<?= @$data['TglTransJurnal'] ?>";
    if (tgl == '') {
        document.getElementById('TglTransJurnal').value = now.toISOString().slice(0,16);
    }

    function template(no) {
        return '<tr class="tambahan">' +
            '<td>' + no + '</td>' +
            '<td>' +
            '<select class="form-control form-select select2" name="KodeAkun[]" id="KodeAkun' + no + '">' +
                '<option value="">- Pilih Akun -</option>' +
                <?php if($dtakun){
                    foreach ($dtakun as $akun) {
                        echo "'" . '<option value="'.$akun['KodeAkun'].'">'.$akun['KodeAkun'].' - '.$akun['NamaAkun'].'</option>' . "' +";
                    }
                } ?>
            '</select>' +
            '</td>' +
            '<td>' +
                '<input type="text" class="form-control text-right input-debet" onkeyup="rpdb(' + no + ')" name="Debet[]" id="Debet' + no + '" value="0">' +
            '</td>' +
            '<td>' +
                '<input type="text" class="form-control text-right input-kredit" onkeyup="rpkr(' + no + ')" name="Kredit[]" id="Kredit' + no + '" value="0">' +
            '</td>' +
            '<td>' +
                '<button type="button" title="Hapus data" class="btn btn-sm btn-danger hapusakun" id="btn-' + no + '">x</button>' +
            '</td>' +
        '</tr>';
    }

    function rpdb(no) {
        var rups = document.getElementById("Debet" + no);
        rups.addEventListener('keyup', function(e)
        {
            rups.value = formatRupiah(this.value);
        });
    }

    function rpkr(no) {
        var rups2 = document.getElementById("Kredit" + no);
        rups2.addEventListener('keyup', function(e)
        {
            rups2.value = formatRupiah(this.value);
        });
    }

    function inputdebet() {
        let total = 0;
        $('.input-debet').each(function(i, k) {
            const n = ($(this).val() != '') ? $(this).val().replace(/\./g,'') : 0
            total += parseInt(n)
        })
        $('#TotalDebet').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 0}).format(total).replace("Rp", "").trim());
    }

    function inputkredit() {
        let total = 0;
        $('.input-kredit').each(function(i, k) {
            const n = ($(this).val() != '') ? $(this).val().replace(/\./g,'') : 0
            total += parseInt(n)
        })
        $('#TotalKredit').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 0}).format(total).replace("Rp", "").trim());
    }

    $('.input-debet').on('input', inputdebet)
    $('.input-kredit').on('input', inputkredit)

    function tambahAkun() {
        var rowCount = $("#tbl-container > tbody > tr").length;
        var no = rowCount + 1;
        $('#tbl-container').children('tbody').append(template(no));
        $('.select2').select2();
        $('.input-debet').off('input').on('input', inputdebet);
        $('.input-kredit').off('input').on('input', inputkredit)
    }

    $('body').on('click', '.hapusakun', function(e) {
        var btn = $(this).closest('tr > td > button');
        var id = btn[0].id;
        var no = id.substr(4);
        var nilaidebet = parseInt($('#Debet' + no).val().replace(/\./g,''));
        var totaldebet = parseInt($('#TotalDebet').val().replace(/\./g,''));
        var hasild = totaldebet - nilaidebet;
        var nilaikredit = parseInt($('#Kredit' + no).val().replace(/\./g,''));
        var totalkredit = parseInt($('#TotalKredit').val().replace(/\./g,''));
        var hasilk = totalkredit - nilaikredit;
        $('#Debet' + no).val(0);
        $('#Kredit' + no).val(0);
        $('#TotalDebet').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 0}).format(hasild).replace("Rp", "").trim());
        $('#TotalKredit').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 0}).format(hasilk).replace("Rp", "").trim());
        console.log($(this).closest('tr').remove())
    });
    $(function() {
        inputkredit()
        inputdebet()
    });

    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('NominalTransaksi');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>