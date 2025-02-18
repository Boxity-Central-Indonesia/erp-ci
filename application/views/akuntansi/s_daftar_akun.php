<script>
    function clps(d) {
        var btn = $('#no'+d);
        var tr = $('.myCollapse'+d);
        if (btn.hasClass('fa-caret-right') && !(tr.hasClass('show'))) {
            btn.removeClass('fa-caret-right').addClass('fa-caret-down');
        }else{
            btn.removeClass('fa-caret-down').addClass('fa-caret-right');
        }
        console.log(tr);
    }

    $('.btn-edit').click(function(e) {
        e.preventDefault();
        $('#modaltitle2').html("Edit Data")
        $('#modal-default2').modal('show');
        const model = JSON.parse($(this).attr('data-obj'));
        document.getElementById("AkunInduk2").disabled = true;
        document.getElementById("KelompokAkun2").disabled = true;
        $('#KelompokAkun2').val(model.KelompokAkun);
        $('#JenisAkun2').val(model.JenisAkun);
        $('#cbparent2').prop('checked', model.IsParent == 1)
        $('#KatArusKas').val(model.KategoriArusKas).change()
        $('#Keterangan2').val(model.Keterangan)
        $('#NamaAkun2').val(model.NamaAkun);
        $('#KodeAkun2').val(model.KodeAkun);
        $('#AkunInduk2').val(model.AkunInduk);
        if (model.jumlah_anak > 0 || model.saldo != null) {
            document.getElementById("cbparent2").disabled = true;
        } else {
            document.getElementById("cbparent2").disabled = false;
        }

        $("#cbaktif2").prop("checked", model.IsAktif == 1);
        $("#persediaan2").prop("checked", model.IsPersediaan == 1);
    })

    $('#btntambah').click(function(e) {
        e.preventDefault()
        $('#modaltitle').html("Tambah Data")
        $('#myform')[0].reset()
        $('#modal-default').modal('show');
    })

    $('#cbkelompok').change(function(event) {
        var kelompok = $(this).find('option:selected').attr('value');
        let kode = $(this).find('option:selected').attr('data-kode')

        var draw = '<option value="" selected>Pilih Akun Induk</option>';
        draw += '<option value="' + kode + '">' + kode + ' - ' + kelompok + '</option>';
        $.ajax({
            type: "GET",
            url: "<?= base_url('akuntansi/daftar_akun/getIndukByKelompok2') ?>",
            data: {
                kelompok: kelompok
            },
            dataType: "JSON",
            success: function(response) {

                if (response.status) {
                    const res = response.data;

                    for (i = 0; i < res.length; i++) {
                        if (res[i]['Saldo'] != null || res[i]['IsLabaRugi'] == 'Ya') {
                            draw += '<option value="' + res[i]['KodeAkun'] + '" disabled>' + res[i]['KodeAkun'] + ' - ' + res[i]['NamaAkun'] + '</option>';
                        } else {
                            draw += '<option value="' + res[i]['KodeAkun'] + '">' + res[i]['KodeAkun'] + ' - ' + res[i]['NamaAkun'] + '</option>';
                        }
                    }
                }
                $('#indukakun').html(draw);

            },
            error: function(xhr, status, error) {
                // $('#indukakun').html(draw);
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
            }
        });
        $('#txt-kode-akun-add').html('-');
    })

    $('#indukakun').change(function(event) {
        var kodeinduknya = $(this).find('option:selected').attr('value');
        $.ajax({
            type: "GET",
            url: "<?= base_url('akuntansi/daftar_akun/generatecode') ?>",
            data: {
                kodeakun: kodeinduknya
            },
            dataType: "JSON",
            success: function(response) {
                const data = response.data;
                $('#txt-kode-akun-add').html(data);
            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
            }
        });
    })

    $('.btn-hapus').click(function(e) {
        e.preventDefault();
        const kode = $(this).data('kode');
        Swal.fire({
            title: 'Apa anda yakin?',
            text: "data terhapus tidak dapat di kembalikan",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus data!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = '<?= base_url('akuntansi/daftar_akun/hapus/') ?>' + kode
            }
        })
    })
</script>
