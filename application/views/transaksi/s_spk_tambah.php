<script>
    var beratprod = 0;
    var biayaprod = 0;
    var beratkotor = 0;
    var pemakaianbahan = 0;
    var statusproduksi = "<?= $dtinduk['StatusProduksi'] ?>";
    var isVisibleColumns = (statusproduksi == 'SELESAI') ? true : false;
    const $table = $('#table-spktambah').DataTable({
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
                data: 'NamaBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'ProdUkuran',
                className: 'text-center',
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
                data: 'Qty',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                visible: isVisibleColumns
            },
            {
                data: 'PemakaianBahanMasak',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                visible: isVisibleColumns
            },
            {
                data: 'Total',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                visible: isVisibleColumns
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center'

            },
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
 
            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
 
            // Total over all pages
            total = api
                .column(4)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            total2 = api
                .column(5)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            total3 = api
                .column(6)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            var amount = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total).replace("Rp", "").trim();
            var amount2 = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total2).replace("Rp", "").trim();
            var amount3 = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total3).replace("Rp", "").trim();
 
            // Update footer
            beratkotor = amount;
            pemakaianbahan = amount2;
            $(api.column(4).footer()).html(amount);
            $(api.column(5).footer()).html(amount2);
            $(api.column(6).footer()).html(amount3);
        },
        "ajax": {
            "url": "<?= base_url('transaksi/spk/get_item_produksi'); ?>",
            "type": "GET",
            "data": function(d) {
                d.notrans = '<?= $dtinduk['NoTrans'] ?>';
                d.barangjadi = 1;
                d.bahanbaku = 0;
            }
        }
    });

    const $table2 = $('#table-bahanbaku').DataTable({
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
                data: 'NamaBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'NamaJenisBarang',
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
                data: 'HargaSatuan',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'Total',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'btn_aksi',
                "orderable": false,
                "searchable": false,
                className: 'text-center'

            },
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
 
            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
 
            // Total over all pages
            total = api
                .column(3)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            total2 = api
                .column(5)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            var amount = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total).replace("Rp", "").trim();
            var amount2 = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total2).replace("Rp", "").trim();
 
            // Update footer
            beratprod = amount;
            biayaprod = amount2;
            $(api.column(3).footer()).html(amount);
            $(api.column(5).footer()).html(amount2);
        },
        "ajax": {
            "url": "<?= base_url('transaksi/spk/get_item_produksi'); ?>",
            "type": "GET",
            "data": function(d) {
                d.notrans = '<?= $dtinduk['NoTrans'] ?>';
                d.barangjadi = 0;
                d.bahanbaku = 1;
            }
        }
    });

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpan(self, data_post);
            return false;
        });

        $("#form-simpan2").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpan(self, data_post);
            return false;
        });

        $("#table-spktambah").on("click", ".btnhapus", function() {
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

        $("#table-bahanbaku").on("click", ".btnhapus", function() {
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

        $("#btn-batal").on("click", function() {
            const kode = $(this).data('kode');

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "jika membatalkan SPK, semua data detail SPK akan terhapus",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Batalkan SPK!'
            }).then((result) => {
                if (result.isConfirmed) {
                    hapusSPK(kode)
                }
            })
        });

        $("#btn-verifikasi").on("click", function () {
            const kode = $(this).data('kode');
            Swal.fire({
                title: 'Apa anda yakin?',
                text: "Proses input bahan dan item produksi dinyatakan selesai!",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Simpan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    simpanspk(kode)
                }
            })
        });

        $("#form-simpan-spk").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            if ('<?= $dtinduk['StatusProses'] ?>' === 'SO') {
                spkverify(self, data_post);
            } else {
                let data = {
                    SPKNomor: $('#SPKNomor').val(),
                    SPKLama: $('#SPKLama').val()
                }
                get_response("<?= base_url('transaksi/slip_order/checkNoSPK') ?>", data, function(response) {
                    if (response.status === false) {
                        $('#ModalSPK').modal('hide');
                        showSwal1('warning', 'Peringatan', response.msg).then(function() {
                            $('#ModalSPK').modal('show');
                            $('#defaultModalLabel').html('Simpan SPK');
                            $('#view_file').hide();
                        });
                        return false;
                    } else {
                        spkverify(self, data_post);
                    }
                });
            }
            return false;
        });
    });

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/spk/simpantambah') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                $('#ModalBahan').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    $table2.ajax.reload(null, false);
                    self[0].reset();
                    // window.location.reload();
                });
            } else {
                $('#ModalTambah').modal('hide');
                $('#ModalBahan').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    function hapus(kode, kode2) {
        let data = {
            NoTrans: kode,
            NoUrut: kode2
        }

        get_response("<?= base_url('transaksi/spk/hapusitemtambah') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                $table.ajax.reload();
                $table2.ajax.reload();
                showSwal('success', 'Informasi', 'Data berhasil dihapus.').then(function() {
                    $table.ajax.reload(null, false);
                    $table2.ajax.reload(null, false);
                    // window.location.reload();
                });

            }
        })
    }

    function hapusSPK(kode) {
        let data = {
            NoRefTrSistem: kode
        }

        get_response("<?= base_url('transaksi/spk/hapustambah') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', 'Data gagal dihapus.');
                return false;
            } else {
                $table.ajax.reload();
                showSwal('success', 'Informasi', 'SPK berhasil dibatalkan.').then(function() {
                    window.location.href = "<?= base_url('transaksi/spk') ?>";
                });

            }
        })
    }

    function simpanspk(kode) {
        let data = {
            NoTrans: kode
        }

        get_response("<?= base_url('transaksi/spk/selesaispk') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                showSwal('success', 'Informasi', 'SPK berhasil disimpan.').then(function() {
                    window.location.reload();
                });

            }
        })
    }

    function spkverify(self, data_post) {
        post_response("<?= base_url('transaksi/slip_order/simpanspk') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalSPK').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    window.location.reload();
                });
            } else {
                $('#ModalSPK').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    $("#table-spktambah").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoTrans').val(model.NoTrans);
        $('#NoUrut').val(model.NoUrut);
        $('#KodeBarang').attr('disabled', true);
        $('#KodeBarang').attr('required', false);
        $('#KodeBarang').val(model.KodeBarang).change();
        $('#ProdUkuran').val(model.ProdUkuran);
        $('#ProdJmlDaun').val(model.ProdJmlDaun);
        $('#Qty').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Qty).replace("Rp", "").trim());
        $('#SatuanBarang').val(model.SatuanBarang);
        $('#JenisBarang').val(model.JenisBarang);
        $('#Kategory').val(model.Kategory);
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#table-bahanbaku").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan2')[0].reset();
        $('#NoTrans2').val(model.NoTrans);
        $('#NoUrut2').val(model.NoUrut);
        $('#KodeBarang2').attr('disabled', true);
        $('#KodeBarang2').attr('required', false);
        $('#KodeBarang2').val(model.KodeBarang).change();
        $('#HargaSatuan').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.HargaSatuan).replace("Rp", "").trim());
        $('#Qty2').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Qty).replace("Rp", "").trim());
        $('#Stok').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Stok).replace("Rp", "").trim());
        $('#SatuanBarang2').val(model.SatuanBarang);
        $('#JenisBarang2').val(model.JenisBarang);
        $('#Kategory2').val(model.Kategory);
        $('#ModalBahan').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#Qty').val('0,00');
        $('#KodeBarang').attr('disabled', false);
        $('#KodeBarang').attr('required', true);
        $('#KodeBarang').val('').change();
        $('#KodeBarang').change(function () {
            var kodeBrg = $(this).find('option:selected').attr('value');
            console.log(kodeBrg);
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataBarang'); ?>",
                method: "GET",
                data: {KodeBarang: kodeBrg},
                dataType: 'json',
                success: function (data) {
                    if (kodeBrg) {
                        $('#SatuanBarang').val(data.SatuanBarang);
                        $('#JenisBarang').val(data.NamaJenisBarang);
                        if (data.NamaKategori) {
                            $('#Kategory').val(data.NamaKategori);
                        } else {
                            $('#Kategory').val('-');
                        }
                    } else {
                        $('#SatuanBarang').val('');
                        $('#JenisBarang').val('');
                        $('#Kategory').val('');
                    }
                }
            });
        });
    });

    $("#btntambah2").on("click", function() {
        $('#form-simpan2')[0].reset();
        $('#ModalBahan').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodeBarang2').attr('disabled', false);
        $('#KodeBarang2').attr('required', true);
        $('#KodeBarang2').val('').change();
        $('#KodeBarang2').change(function () {
            var kodeBrg = $(this).find('option:selected').attr('value');
            var kodeGdg = "<?= $dtinduk['GudangAsal'] ?>";
            $.ajax({
                url: "<?php echo site_url('user/Lokasi/DataBarang'); ?>",
                method: "GET",
                data: {KodeBarang: kodeBrg},
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (kodeBrg) {
                        $('#SatuanBarang2').val(data.SatuanBarang);
                        $('#JenisBarang2').val(data.NamaJenisBarang);
                        $('#HargaSatuan').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.NilaiHPP).replace("Rp", "").trim());
                        if (data.NamaKategori) {
                            $('#Kategory2').val(data.NamaKategori);
                        } else {
                            $('#Kategory2').val('-');
                        }
                    } else {
                        $('#SatuanBarang2').val('');
                        $('#JenisBarang2').val('');
                        $('#Kategory2').val('');
                        $('#HargaSatuan').val('0,00');
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
                            $('#Stok').val(data.stok);
                        } else {
                            $('#Stok').val('0,00');
                        }
                    } else {
                        $('#Stok').val('0,00');
                    }
                }
            });
        });
    });

    $("#verifikasispk").on("click", function() {
        $('#form-simpan-spk')[0].reset();
        $('#ModalSPK').modal('show');
        $('#SPKNomor').val("<?= $dtinduk['SPKNomor'] ?>");
        $('#SPKLama').val("<?= $dtinduk['SPKNomor'] ?>");
        $('#EstimasiSelesai').val("<?= $dtinduk['EstimasiSelesai'] ?>");
        $('#SPKDisetujuiOleh').val("<?= $dtinduk['SPKDisetujuiOleh'] ?>");
        $('#SPKDisetujuiTgl').val("<?= $dtinduk['SPKDisetujuiTgl'] ?>");
        $('#SPKDiketahuiOleh').val("<?= $dtinduk['SPKDiketahuiOleh'] ?>");
        $('#SPKDiketahuiTgl').val("<?= $dtinduk['SPKDiketahuiTgl'] ?>");
        $('#SPKTanggal').val("<?= $dtinduk['SPKTanggal'] ?>");
        $('#defaultModalLabel').html('Verifikasi SPK');
        $('#view_file').hide();
    });

    var tanpa_rupiah = document.getElementById('Qty');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });

    var tanpa_rupiah2 = document.getElementById('Qty2');
    tanpa_rupiah2.addEventListener('keyup', function(e)
    {
        tanpa_rupiah2.value = formatRupiah(this.value);
    });
</script>