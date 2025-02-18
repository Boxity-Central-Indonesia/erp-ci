<script>
    const $table = $('#table-jualdetail').DataTable({
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
                data: 'JenisBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Kategory',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
            },
            {
                data: 'Quantity',
                className: 'text-center',
                "orderable": false,
                "searchable": false,
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
                .column(6)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var amount = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total).replace("Rp", "").trim();
 
            // Update footer
            $(api.column(6).footer()).html(amount);
        },
        // "rowCallback": function( row, data ) {
        //     if ( data.Sisa < 0 ) {
        //         $('td:eq(3)', row).html( '<b style="color:red;">'+data.StokGudang+'</b>' );
        //     }
        //     if (data.StatusProses == 'DONE') {
        //         $('td:eq(3)', row).html( '<span style="color:red;" hidden>'+data.StokGudang+'</span>' );
        //     }
        // },
        "ajax": {
            "url": "<?= base_url('transaksi/transaksi_penjualan/detail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.idtransjual = "<?= $IDTransJual ?>";
                d.exptahun = "<?= $exptahun ?>";
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
            let data = {
                IDTransRetur: "<?= $dtinduk['IDTransRetur'] ?>"
            }

            get_response("<?= base_url('transaksi/transaksi_penjualan/check_retur') ?>", data, function(response) {
                if (response.status === false) {
                    $('#ModalTambah').modal('hide');
                    showSwal1('error', 'Peringatan', 'Gagal menyimpan data. Silahkan hapus transaksi retur terlebih dahulu.');
                    return false;
                } else {
                    simpan(self, data_post);
                }
            })
            return false;
        });

        $("#table-jualdetail").on("click", ".btnhapus", function() {
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
            let data = {
                IDTransRetur: "<?= $dtinduk['IDTransRetur'] ?>"
            }

            get_response("<?= base_url('transaksi/transaksi_penjualan/check_retur') ?>", data, function(response) {
                if (response.status === false) {
                    $('#ModalPenjualan').modal('hide');
                    showSwal1('error', 'Peringatan', 'Gagal menyimpan data. Silahkan hapus transaksi retur terlebih dahulu.');
                    return false;
                } else {
                    simpanpenjualan(self, data_post);
                }
            })
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

        $(".btn-retur").on("click", function() {
            const kode = $(this).data('kode');
            const kode2 = $(this).data('kode2');
            retur(kode, kode2);
        });
    });

    function retur(kode, kode2) {
        let data = {
            IDTransJual: kode,
            JenisRealisasi: kode2
        }

        get_response("<?= base_url('transaksi/transaksi_penjualan/retur') ?>", data, function(response) {
            if (response.status === false) {
                // showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                window.location.href = "<?= base_url('transaksi/retur/detail/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                // showSwal('success', 'Informasi', 'Data berhasil dihapus.');
            }
        })
    }

    function hapus(kode, kode2) {
        let data = {
            IDTransJual: kode,
            NoUrut: kode2
        }
        let dtretur = {
            IDTransRetur: "<?= $dtinduk['IDTransRetur'] ?>"
        }

        get_response("<?= base_url('transaksi/transaksi_penjualan/check_retur') ?>", dtretur, function(response) {
            if (response.status === false) {
                showSwal1('error', 'Peringatan', 'Gagal menghapus data. Silahkan hapus transaksi retur terlebih dahulu.');
                return false;
            } else {
                get_response("<?= base_url('transaksi/transaksi_penjualan/hapusdetail') ?>", data, function(response) {
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
        })


    }

    function simpan(self, data_post) {
        post_response("<?= base_url('transaksi/transaksi_penjualan/simpandetail') ?>", data_post, function(response) {
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
        post_response("<?= base_url('transaksi/transaksi_penjualan/simpanpenjualan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalPenjualan').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    self[0].reset();
                    if (response.stj == 'on' && response.idjurnal != null) {
                        window.location.reload();
                    } else {
                        window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnalmanual/') ?>" + btoa("trans_jual") + "/" + btoa(unescape(encodeURIComponent(response.idjurnal))) + "/" + btoa("<?= $IDTransJual ?>") + "/" + btoa("transaksi_penjualan/detail");
                    }
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
        let dtretur = {
            IDTransRetur: "<?= $dtinduk['IDTransRetur'] ?>"
        }

        get_response("<?= base_url('transaksi/transaksi_penjualan/check_retur') ?>", dtretur, function(response) {
            if (response.status === false) {
                showSwal1('error', 'Peringatan', 'Gagal menghapus data. Silahkan hapus transaksi retur terlebih dahulu.');
                return false;
            } else {
                get_response("<?= base_url('transaksi/transaksi_penjualan/hapus') ?>", data, function(response) {
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
        })


    }

    $("#table-jualdetail").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoUrut').val(model.NoUrut);
        $('#KodeBarang').val(model.KodeBarang).change();
        $('#KodeBarang').attr('disabled', true);
        $('#JenisBarang').val(model.JenisBarang);
        $('#Kategory').val(model.Kategory);
        $('#radioPrimary1').attr('checked', true);
        $('#Diskon').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Diskon).replace("Rp", "").trim());
        $('#Deskripsi').val(model.Deskripsi);
        $('#Qty').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Qty).replace("Rp", "").trim());
        $('#SatuanBarang').val(model.SatuanBarang);
        $('#Spesifikasi').val(model.Spesifikasi);
        $('#GS').val(model.NamaGudang + " | " + Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Sisa).replace("Rp", "").trim() + " (" + model.SatuanBarang + ")");
        $('#SatuanPenjualan').val(model.SatuanPenjualan);
        $('#JmlStok').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Qty).replace("Rp", "").trim());
        if (model.SatuanPenjualan == 'pcs') {
            $('#JmlStok').attr('readonly', true);
            $('#JmlStok').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(1).replace("Rp", "").trim());
        } else {
            $('#JmlStok').attr('readonly', false);
            $('#JmlStok').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Qty).replace("Rp", "").trim());
        }
        if (model.SatuanPenjualan == 'ecer') {
            $('#Total').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.HargaSatuan).replace("Rp", "").trim());
        } else {
            $('#Total').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Total).replace("Rp", "").trim());
        }
        $('#SatuanPenjualan').change(function () {
            var satuan = $(this).find('option:selected').attr('value');
            if (satuan == 'pcs') {
                $('#JmlStok').attr('readonly', true);
                $('#JmlStok').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(1).replace("Rp", "").trim());
            } else {
                $('#JmlStok').attr('readonly', false);
                $('#JmlStok').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Qty).replace("Rp", "").trim());
            }
        })
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodeBarang').attr('disabled', false);
        $('#SatuanPenjualan').attr('disabled', true);
        $('#SatuanPenjualan').attr('required', false);
        $('#KodeBarang').val('').change();
        var jmlStok = 0;
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
                        $('#Total').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.HargaJual).replace("Rp", "").trim());
                        $('#Spesifikasi').val(data.Spesifikasi);
                        $('#JenisBarang').val(data.NamaJenisBarang);
                        if (data.NamaKategori) {
                            $('#Kategory').val(data.NamaKategori);
                        } else {
                            $('#Kategory').val('-');
                        }
                    } else {
                        $('#SatuanBarang').val('');
                        $('#Total').val('');
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
                            $('#GS').val("<?= $dtinduk['NamaGudang'] ?>" + " | " + Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.stok).replace("Rp", "").trim() + " (" + data.SatuanBarang + ")");
                            $('#Qty').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.stok).replace("Rp", "").trim());
                        } else {
                            $('#GS').val("<?= $dtinduk['NamaGudang'] ?>" + " | 0 (" + data.SatuanBarang + ")");
                            $('#Qty').val(0);
                        }
                        if (parseInt(data.stok) > 0) {
                            $('#simpan-detail').attr('hidden', false);
                            $('#SatuanPenjualan').attr('disabled', false);
                            $('#SatuanPenjualan').attr('required', true);
                            jmlStok = data.stok;
                        } else {
                            $('#simpan-detail').attr('hidden', true);
                            $('#SatuanPenjualan').attr('disabled', true);
                            $('#SatuanPenjualan').attr('required', false);
                            $('#SatuanPenjualan').val('');
                            $('#JmlStok').val(0);
                        }
                    } else {
                        $('#GS').val('');
                        $('#Qty').val(0);
                        $('#simpan-detail').attr('hidden', true);
                        $('#SatuanPenjualan').attr('disabled', true);
                        $('#SatuanPenjualan').attr('required', false);
                        $('#SatuanPenjualan').val('');
                        $('#JmlStok').val(0);
                    }
                }
            });
        });
        $('#SatuanPenjualan').change(function () {
            var satuan = $(this).find('option:selected').attr('value');
            if (satuan != '') {
                if (satuan == 'pcs') {
                    $('#JmlStok').attr('readonly', true);
                    $('#JmlStok').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(1).replace("Rp", "").trim());
                } else {
                    $('#JmlStok').attr('readonly', false);
                    $('#JmlStok').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(jmlStok).replace("Rp", "").trim());
                }
            } else {
                $('#JmlStok').attr('readonly', true);
                $('#JmlStok').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(0).replace("Rp", "").trim());
            }
        })
        $('#view_file').hide();
    });

    $("#btnmemo").on("click", function() {
        $('#ModalMemo').modal('show');
    });


    $(".simpanjual").on("click", function() {
        $('#form-simpan-penjualan')[0].reset();
        $('#ModalPenjualan').modal('show');
        $('#defaultModalLabel').html('Transaksi');
        $('#NoRef_Manual').val('<?= $dtinduk['NoRef_Manual']; ?>');
        $('#TglSlipOrder').val('<?= $dtinduk['TglSlipOrder']; ?>');
        $('#TanggalPenjualan').val('<?= $dtinduk['TanggalPenjualan']; ?>');
        $('#TanggalJatuhTempo').val('<?= $dtinduk['TanggalJatuhTempo']; ?>');
        $('#DiskonBawah').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format('<?= $dtinduk['DiskonBawah']; ?>').replace("Rp", "").trim());
        $('#NilaiPPN').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format('<?= $dtinduk['PPN']; ?>').replace("Rp", "").trim());
        $('#TotalTransaksi').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format('<?= $dtinduk['TotalTransaksi']; ?>').replace("Rp", "").trim());
        $('#TotalTransaksi').attr('readonly', false);
        var diskonbawahradio = document.getElementById("diskonbawahradio");
        var ppnradio = document.getElementById("ppnradio");
        var akhir = document.getElementById('t-akhir');
        var dp = document.getElementById('dp');
        if ('<?= $dtinduk['TglSlipOrder'] ?>') {
            $('#TagihanAkhir').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format('<?= $dtinduk['TotalTagihan']; ?>').replace("Rp", "").trim());
            diskonbawahradio.style.display = 'none';
            ppnradio.style.display = 'none';
            $('#DiskonBawah').attr('readonly', true);
            $('#NilaiPPN').attr('hidden', false);
            $('#radioPrimary4').attr('required', false);
            $('#radioPrimary5').attr('required', false);
            $('#radioPrimary6').attr('required', false);
            // $('#radioPrimary7').attr('required', false);
            $('#radioPrimary8').attr('required', false);
            $('#radioPrimary9').attr('required', false);
            akhir.style.display = 'block';
            dp.style.display = 'block';
            $('#btnsimpan').attr('hidden', false);
            $('#hitung').attr('hidden', true);
            $('#btnreset').attr('hidden', true);
        } else {
            diskonbawahradio.style.display = 'block';
            ppnradio.style.display = 'block';
            $("input[name=JenisDiskonBawah][value=Nominal]").prop('checked', true);
            $('#DiskonBawah').attr('readonly', false);
            $('#NilaiPPN').attr('hidden', true);
            akhir.style.display = 'none';
            dp.style.display = 'none';
            $('#btnsimpan').attr('hidden', true);
            $('#hitung').attr('hidden', false);
            $('#btnreset').attr('hidden', true);
        }
        var akunkas = document.getElementById('akunkas');
        akunkas.style.display = 'none';
    });

    function getHitung() {
        var JenisDiskonBawah = $('input[name="JenisDiskonBawah"]:checked').val();
        var JenisPPN = $('input[name="JenisPPN"]:checked').val();
        var DiskonBawah = $('#DiskonBawah').val().split('.').join('').split(',').join('.');
        var tagihanawal = Number('<?= $tagihanawal ?>').toFixed(2);
        var tanpadiskon = Number('<?= $tanpadiskon ?>').toFixed(2);
        var nilaidiskon = 0;
        var nilaippn = 0;

        if (DiskonBawah != undefined && DiskonBawah > 0) {
            if (JenisDiskonBawah != undefined && JenisDiskonBawah != 'None') {
                if (JenisDiskonBawah == 'Nominal') {
                    nilaidiskon = DiskonBawah;
                } else {
                    nilaidiskon = Number(DiskonBawah / 100 * tagihanawal).toFixed(2);
                }
            } else {
                nilaidiskon = 0;
            }
        } else {
            nilaidiskon = 0;
        }

        if (JenisPPN != undefined && JenisPPN != 'None') {
            if (JenisPPN == 'Sebelum') {
                nilaippn = Number(11 / 100 * tanpadiskon).toFixed(2);
            } else {
                nilaippn = Number(11 / 100 * (tagihanawal - nilaidiskon)).toFixed(2);
            }
        } else {
            nilaippn = 0;
        }

        var nominalbelumpajak = Number(tagihanawal - nilaidiskon).toFixed(2);
        var tagakhir = parseFloat(nominalbelumpajak) + parseFloat(nilaippn);
        var tagihanakhir = Number(tagakhir).toFixed(2);

        // console.log("awal",tagihanawal, "diskon",nilaidiskon, "ppn",nilaippn, "belumpajak",nominalbelumpajak, "akhir",tagihanakhir);

        if (JenisDiskonBawah != undefined && JenisPPN != undefined) {
            $('#NilaiDiskon').val(nilaidiskon);
            $('#NilaiPPN').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(nilaippn).replace("Rp", "").trim());
            $('#NominalBelumPajak').val(nominalbelumpajak);
            $('#TagihanAkhir').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(tagihanakhir).replace("Rp", "").trim());

            var akhir = document.getElementById('t-akhir');
            var dp = document.getElementById('dp');
            var akunkas = document.getElementById('akunkas');
            akhir.style.display = 'block';
            dp.style.display = 'block';
            akunkas.style.display = 'block';
            $('#btnsimpan').attr('hidden', false);
            $('#hitung').attr('hidden', true);
            $('#btnreset').attr('hidden', false);
            $('#NilaiPPN').attr('hidden', false);
            $('#KodeAkun').val('').change();
        } else {
            alert('Silahkan pilih Diskon Bawah dan Jenis PPN terlebih dahulu.');
            return false;
        }
    }

    function getReset() {
        var JenisDiskonBawah = $('input[name="JenisDiskonBawah"]');
        JenisDiskonBawah[0].checked = false;
        JenisDiskonBawah[1].checked = false;
        JenisDiskonBawah[2].checked = false;
        var JenisPPN = $('input[name="JenisPPN"]');
        JenisPPN[0].checked = false;
        JenisPPN[1].checked = false;
        JenisPPN[2].checked = false;
        $('#DiskonBawah').val('');
        $('#NilaiDiskon').val('');
        $('#NilaiPPN').val('');
        $('#NominalBelumPajak').val('');
        $('#TagihanAkhir').val('');
        $('#TotalTransaksi').val('');

        var akhir = document.getElementById('t-akhir');
        var dp = document.getElementById('dp');
        var akunkas = document.getElementById('akunkas');
        akhir.style.display = 'none';
        dp.style.display = 'none';
        akunkas.style.display = 'none';
        $('#btnsimpan').attr('hidden', true);
        $('#hitung').attr('hidden', false);
        $('#btnreset').attr('hidden', true);
        $('#NilaiPPN').attr('hidden', true);
    }
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('Total');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });

    var tanpa_rupiah2 = document.getElementById('Diskon');
    tanpa_rupiah2.addEventListener('keyup', function(e)
    {
        tanpa_rupiah2.value = formatRupiah(this.value);
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

    var tanpa_rupiah5 = document.getElementById('Qty');
    tanpa_rupiah5.addEventListener('keyup', function(e)
    {
        tanpa_rupiah5.value = formatRupiah(this.value);
    });
</script>