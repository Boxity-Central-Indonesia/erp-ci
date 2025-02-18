<script>
    const $table = $('#table-sodetail').DataTable({
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
                data: 'Barang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'JenisBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Kategory',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Qty',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'ProdUkuran',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'ProdJmlDaun',
                className: 'text-center',
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
        "ajax": {
            "url": "<?= base_url('transaksi/slip_order/detail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.idtransjual = '<?= $IDTransJual ?>';
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

        $("#table-sodetail").on("click", ".btnhapus", function() {
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

        $("#form-simpan-penjualan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpanpenjualan(self, data_post);
            return false;
        });

        $(".bataljual").on("click", function() {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "jika membatalkan transaksi, semua data transaksi akan terhapus",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Batalkan transaksi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    hapusjual(kode)
                }
            })
        });

        $("#btnspk").on("click", function() {
            const kode = $(this).data('kode');
            cetakSPK(kode);
        });
    });

    function cetakSPK(kode) {
        let data = {
            IDTransJual: kode
        }

        get_response("<?= base_url('transaksi/slip_order/jmlproduksi') ?>", data, function(response) {
            if (response.status === false) {
                // showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                window.location.href = "<?= base_url('transaksi/slip_order/detailspk/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                // showSwal('success', 'Informasi', 'Data berhasil dihapus.');
            }
        })
    }

    function hapus(kode, kode2) {
        let data = {
            IDTransJual: kode,
            NoUrut: kode2
        }

        get_response("<?= base_url('transaksi/slip_order/hapusdetail') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Data berhasil dihapus.').then(function() {
                    window.location.reload();
                });

            }
        })

    }

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/slip_order/simpandetail') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    window.location.reload();
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    function simpanpenjualan(self, data_post) {
        post_response("<?= base_url('transaksi/slip_order/simpanpenjualan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalPenjualan').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    self[0].reset();
                    window.location.reload();
                });
            } else {
                $('#ModalPenjualan').modal('hide');
                showSwal("error", "Gagal", response.msg).then(function() {
                    $('#ModalPenjualan').modal('show');
                });
            }
        });
    }

    function hapusjual(kode) {
        let data = {
            IDTransJual: kode
        }

        get_response("<?= base_url('transaksi/slip_order/hapus') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'Transaksi berhasil dibatalkan.').then(function() {
                    window.location.href = "<?= base_url('transaksi/trans_jual') ?>";
                });

            }
        })

    }


    $("#table-sodetail").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoUrut').val(model.NoUrut);
        $('#KodeBarang').val(model.KodeBarang).change();
        $('#KodeBarang').attr('disabled', true);
        $('#Qty').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Qty).replace("Rp", "").trim());
        $('#JenisBarang').val(model.JenisBarang);
        $('#Kategory').val(model.Kategory);
        $('#SatuanBarang').val(model.SatuanBarang);
        $('#Spesifikasi').val(model.Spesifikasi);
        $('#ProdUkuran').val(model.ProdUkuran);
        $('#ProdJmlDaun').val(model.ProdJmlDaun);
        $('#AdditionalName').val(model.AdditionalName);
        $('#Deskripsi').val(model.Deskripsi);
        $('#GS').val(model.NamaGudang + " | " + model.Stok + " (" + model.SatuanBarang + ")");
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');

        $('#first_row').hide();
        const datadraft = model.dtdraft;
        datadraft.forEach(function(key) {
            $('#tbl-container').children('tbody').append('<tr class="editan">' +
                '<td>' +
                    '<input type="text" class="form-control" name="" id="NamaBahan' + key.DraftID + '" value="' + key.NamaBarang + '" readonly>' +
                    '<input type="hidden" class="form-control" name="KodeBahan[]" id="KodeBahan' + key.DraftID + '" value="' + key.KodeBarang + '" readonly>' +
                '</td>' +
                '<td>' +
                    '<input type="number" ' + (key.StatusProses != 'SO' ? 'readonly' : '') + ' class="form-control" name="QtyBahan[]" id="QtyBahan' + key.DraftID + '" value="' + key.Qty + '">' +
                '</td>' +
                '<td>' +
                    '<input type="text" class="form-control" name="SatuanBahan[]" id="SatuanBahan' + key.DraftID + '" value="' + key.SatuanBarang + '" readonly>' +
                '</td>' +
                '<td>' +
                    '<button type="button" ' + (key.StatusProses != 'SO' ? 'hidden' : '') + ' title="Hapus data" class="btn btn-sm btn-danger hapusbahan">x</button>' +
                '</td>' +
            '</tr>');
            console.log(key);
        });
    });

    $("#ModalTambah").on('hide.bs.modal', function(){
        $('.tambahan').remove();
        $('.editan').remove();
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodeBarang').attr('disabled', false);
        $('#KodeBarang').val('').change();
        $('#KodeBarang').change(function () {
            var kodeBrg = $(this).find('option:selected').attr('value');
            var kodeGdg = "<?= $dtinduk['KodeGudang'] ?>";
            console.log(kodeBrg);
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataBarang'); ?>",
                method: "GET",
                data: {KodeBarang: kodeBrg},
                dataType: 'json',
                success: function (data) {
                    if (kodeBrg) {
                        $('#SatuanBarang').val(data.SatuanBarang);
                        $('#Spesifikasi').val(data.Spesifikasi);
                        $('#JenisBarang').val(data.NamaJenisBarang);
                        if (data.NamaKategori) {
                            $('#Kategory').val(data.NamaKategori);
                        } else {
                            $('#Kategory').val('-');
                        }
                    } else {
                        $('#SatuanBarang').val('');
                        $('#Spesifikasi').val('');
                        $('#JenisBarang').val('');
                        $('#Kategory').val('');
                    }
                }
            });
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/jumlahStokAsal') ?>",
                method: "GET",
                data: {GudangAsal: kodeGdg, KodeBarang: kodeBrg},
                dataType: 'json',
                success: function (data) {
                    if (kodeBrg) {
                        if (data.stok) {
                            $('#GS').val("<?= $dtinduk['NamaGudang'] ?>" + " | " + data.stok + " (" + data.SatuanBarang + ")");
                        } else {
                            $('#GS').val("<?= $dtinduk['NamaGudang'] ?>" + " | 0 (" + data.SatuanBarang + ")");
                        }
                    } else {
                        $('#GS').val('');
                    }
                }
            });
        });
        $('#view_file').hide();

        $('#first_row').show();
        $('#KodeBahan1').val('').change();
        $('#KodeBahan1').change(function () {
            var kodeBrg = $(this).find('option:selected').attr('value');
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataBarang'); ?>",
                method: "GET",
                data: {KodeBarang: kodeBrg},
                dataType: 'json',
                success: function (data) {
                    if (kodeBrg) {
                        $('#SatuanBahan1').val(data.SatuanBarang);
                    } else {
                        $('#SatuanBahan1').val('');
                    }
                }
            });
        });
    });

    function template(no) {
        return '<tr class="tambahan">' +
            '<td>' +
            '<select class="form-control form-select select2" onchange="satuan('+no+')" name="KodeBahan[]" id="KodeBahan' + no + '">' +
                '<option value="">Pilih Barang</option>' +
                <?php if($dtbahan){
                    foreach ($dtbahan as $key) {
                        echo "'" . '<option value="'.$key['KodeBarang'].'">'.$key['NamaBarang'].'</option>' . "' +";
                    }
                } ?>
            '</select>' +
            '</td>' +
            '<td>' +
                '<input type="number" class="form-control" name="QtyBahan[]" id="QtyBahan' + no + '">' +
            '</td>' +
            '<td>' +
                '<input type="text" class="form-control" name="SatuanBahan[]" id="SatuanBahan' + no + '" readonly>' +
            '</td>' +
            '<td>' +
                '<button type="button" title="Hapus data" class="btn btn-sm btn-danger hapusbahan">x</button>' +
            '</td>' +
        '</tr>';
    }

    function satuan(no) {
        var kodeBrg = document.getElementById("KodeBahan" + no).value;
        $.ajax({
            url: "<?php echo site_url('user/Lokasi/DataBarang'); ?>",
            method: "GET",
            data: {KodeBarang: kodeBrg},
            dataType: 'json',
            success: function (data) {
                if (kodeBrg) {
                    $('#SatuanBahan' + no).val(data.SatuanBarang);
                    $('#no' + no).val(no);
                } else {
                    $('#SatuanBahan' + no).val('');
                    $('#no' + no).val('');
                }
            }
        });
    }

    function tambahBahan() {
        var rowCount = $("#tbl-container > tbody > tr").length;
        var no = rowCount + 1;
        $('#tbl-container').children('tbody').append(template(no));
        $('.select2').select2();
    }

    $('body').on('click', '.hapusbahan', function(e) {
        console.log($(this).closest('tr').remove())
    });

    $(".simpanjual").on("click", function() {
        $('#form-simpan-penjualan')[0].reset();
        $('#ModalPenjualan').modal('show');
        $('#defaultModalLabel').html('Transaksi');
        $('#TglSlipOrder').val('<?= $dtinduk['TglSlipOrder']; ?>');
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('Qty');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });

    var tanpa_rupiah3 = document.getElementById('DiskonBawah');
    tanpa_rupiah3.addEventListener('keyup', function(e)
    {
        tanpa_rupiah3.value = formatRupiah(this.value);
    });

    var tanpa_rupiah4 = document.getElementById('TotalTransaksi');
    tanpa_rupiah4.addEventListener('keyup', function(e)
    {
        tanpa_rupiah4.value = formatRupiah(this.value);
    });
</script>