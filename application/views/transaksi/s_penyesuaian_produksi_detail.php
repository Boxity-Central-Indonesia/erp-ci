<script>
    var beratprod = 0;
    var biayaprod = 0;
    var beratkotor = 0;
    var pemakaianbahan = 0;
    var tglselesai = "<?= $dtinduk['ProdTglSelesai'] ?>";
    var isVisibleColumns = (tglselesai == '') ? false : true;
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
                data: 'BeratKotor',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'Qty',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                visible: false //isVisibleColumns
            },
            {
                data: 'PemakaianBahanMasak',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
                // visible: isVisibleColumns
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
            total4 = api
                .column(7)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            var amount = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total).replace("Rp", "").trim();
            var amount2 = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total2).replace("Rp", "").trim();
            var amount3 = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total3).replace("Rp", "").trim();
            var amount4 = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total4).replace("Rp", "").trim();
 
            // Update footer
            beratkotor = amount;
            pemakaianbahan = amount2;
            $(api.column(4).footer()).html(amount);
            $(api.column(5).footer()).html(amount2);
            $(api.column(6).footer()).html(amount3);
            $(api.column(7).footer()).html(amount4);
        },
        "ajax": {
            "url": "<?= base_url('transaksi/penyesuaian_produksi/get_all'); ?>",
            "type": "GET",
            "data": function(d) {
                d.notrans = '<?= $dtinduk['NoTrans'] ?>';
                d.isbarangjadi = 1;
                d.isbahanbaku = 0;
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
            "url": "<?= base_url('transaksi/penyesuaian_produksi/get_all'); ?>",
            "type": "GET",
            "data": function(d) {
                d.notrans = '<?= $dtinduk['NoTrans'] ?>';
                d.isbarangjadi = 0;
                d.isbahanbaku = 1;
            }
        }
    });

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpanbrgjadi(self, data_post);
            return false;
        });

        $("#form-simpan2").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpanbahan(self, data_post);
            return false;
        });

        $("#table-spktambah").on("click", ".btnhapus", function() {
            const kode = $(this).data('kode');
            const kode2 = $(this).data('kode2');
            const kode3 = $(this).data('kode3');
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
                    hapus(kode, kode2, kode3)
                }
            })
        });

        $("#table-bahanbaku").on("click", ".btnhapus", function() {
            const kode = $(this).data('kode');
            const kode2 = $(this).data('kode2');
            const kode3 = $(this).data('kode3');
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
                    hapus(kode, kode2, kode3)
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
                    simpanselesai(kode)
                }
            })
        });
    });

    function simpanbrgjadi(self, data_post) {
        post_response("<?= base_url('transaksi/penyesuaian_produksi/simpanbarangjadi') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    // window.location.reload();
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    function simpanbahan(self, data_post) {
        post_response("<?= base_url('transaksi/penyesuaian_produksi/simpanbahan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalBahan').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    $table2.ajax.reload(null, false);
                    $table.ajax.reload(null, false);
                    self[0].reset();
                    // window.location.reload();
                });
            } else {
                $('#ModalBahan').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    function hapus(kode, kode2, kode3) {
        let data = {
            NoTrans: kode,
            NoUrut: kode2,
            NoRefProduksi: kode3
        }

        get_response("<?= base_url('transaksi/penyesuaian_produksi/hapusdetail') ?>", data, function(response) {
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

    function simpanselesai(kode) {
        let data = {
            NoTrans: kode
        }

        get_response("<?= base_url('transaksi/penyesuaian_produksi/checkItemProd') ?>", data, function(response) {
            if (response.status === false) {
                showSwal1('warning', 'Peringatan', response.msg);
                return false;
            } else {
                get_response("<?= base_url('transaksi/penyesuaian_produksi/selesai_produksi') ?>", data, function(response) {
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
        })
    }

    $("#table-spktambah").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
        $('#NoTrans').val(model.NoTrans);
        $('#NoUrut').val(model.NoUrut);
        $('#KodeBarang').attr('disabled', true);
        $('#KodeBarang').attr('required', false);
        $('#KodeBarang').val(model.KodeBarang);
        $('#KodeBarang').attr('hidden', true);
        $('#NamaBarang').attr('hidden', false);
        $('#NamaBarang').val(model.NamaBarang);
        $('#ProdUkuran').val(model.ProdUkuran);
        $('#ProdJmlDaun').val(model.ProdJmlDaun);
        $('#Qty').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.BeratKotor).replace("Rp", "").trim());
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
        $('#IsBahanBaku2').prop('checked', model.IsBahanBaku == 1);
        $('#ModalBahan').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#NoUrut').val('');
        $('#NamaBarang').attr('hidden', true);
        $('#KodeBarang').attr('disabled', false);
        $('#KodeBarang').attr('required', true);
        $('#KodeBarang').val('').change();
        $('#KodeBarang').select2({
            placeholder: "Pilih Barang Produksi",
                theme: 'bootstrap4',
                allowClear: true,
                ajax: {
                    dataType: 'json',
                    delay: 250,
                    url: '<?php echo base_url('transaksi/penyesuaian_produksi/list_barang_jadi'); ?>',
                    data: function(params) {
                        return {
                            searchTerm: params.term,
                            kodegudang: "<?= $dtinduk['GudangAsal'] ?>"
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(obj) {
                                return {
                                    id: obj.KodeBarang,
                                    text: obj.KodeManual + ' - ' + obj.NamaBarang
                                };
                            })
                        };
                    }
                }
        });
        $('#KodeBarang').change(function () {
            var kodeBrg = $(this).find('option:selected').attr('value');
            $.ajax({
                url: "<?php echo site_url('transaksi/penyesuaian_produksi/get_one_jadi'); ?>",
                method: "GET",
                data: {KodeBarang: kodeBrg},
                dataType: 'json',
                success: function (data) {
                    if (kodeBrg) {
                        $('#NoTrans').val(data.NoTrans);
                        $('#NoUrut').val(data.NoUrut);
                        $('#ProdUkuran').val(data.ProdUkuran);
                        $('#ProdJmlDaun').val(data.ProdJmlDaun);
                    } else {
                        $('#NoTrans').val('');
                        $('#NoUrut').val('');
                        $('#ProdUkuran').val('');
                        $('#ProdJmlDaun').val('');
                    }
                }
            });
        });
    });

    $("#btntambah2").on("click", function() {
        $('#form-simpan2')[0].reset();
        $('#ModalBahan').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#NoUrut2').val('');
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