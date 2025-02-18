<script>
    var tagawal = 0;
    const $table = $('#table-belidetail').DataTable({
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
                data: 'SatuanBarang',
                className: 'text-left',
                "orderable": false,
                "searchable": false,
                visible: false,
            },
            {
                data: 'HargaSatuan',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'Qty',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, ),
            },
            {
                data: 'Diskon',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, )
            },
            {
                data: 'Total',
                className: 'text-right',
                "orderable": false,
                "searchable": false,
                render: $.fn.dataTable.render.number( '.', ',', 2, )
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
                tagawal = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(total).replace("Rp", "").trim();
 
            // Update footer
            $(api.column(6).footer()).html(amount);
        },
        "rowCallback": function( row, data ) {
            if (data.Qty) {
                $('td:eq(3)', row).html('<span style="padding-left:30%;">' + Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.Qty).replace("Rp", "").trim()+' '+data.SatuanBarang + '</span>');
            }
        },
        "ajax": {
            "url": "<?= base_url('transaksi/transaksi_pembelian/detail'); ?>",
            "type": "GET",
            "data": function(d) {
                d.cari = $("#inp-search").val();
                d.isaktif = $("#combo-status").val();
                d.idtransbeli = "<?= $IDTransBeli ?>";
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

            get_response("<?= base_url('transaksi/transaksi_pembelian/check_retur') ?>", data, function(response) {
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

        $("#table-belidetail").on("click", ".btnhapus", function() {
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

        $("#form-simpan-pembelian").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            let data = {
                IDTransRetur: "<?= $dtinduk['IDTransRetur'] ?>"
            }

            get_response("<?= base_url('transaksi/transaksi_pembelian/check_retur') ?>", data, function(response) {
                if (response.status === false) {
                    $('#ModalTambah').modal('hide');
                    showSwal1('error', 'Peringatan', 'Gagal menyimpan data. Silahkan hapus transaksi retur terlebih dahulu.');
                    return false;
                } else {
                    simpanpembelian(self, data_post);
                }
            })
            return false;
        });

        $(".batalbeli").on("click", function() {
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
                    hapusbeli(kode)
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
            IDTransBeli: kode,
            JenisRealisasi: kode2
        }

        get_response("<?= base_url('transaksi/transaksi_pembelian/check_barang_datang') ?>", data, function(response) {
            if (response.status === false) {
                showSwal1('error', 'Peringatan', response.msg);
                return false;
            } else {
                get_response("<?= base_url('transaksi/transaksi_pembelian/retur') ?>", data, function(response) {
                    if (response.status === false) {
                        // showSwal('error', 'Peringatan', response.msg);
                        return false;
                    } else {
                        window.location.href = "<?= base_url('transaksi/retur_pembelian/detail/') ?>" + btoa(unescape(encodeURIComponent(response.id)));
                        // showSwal('success', 'Informasi', 'Data berhasil dihapus.');
                    }
                })
            }
        })
    }

    function hapus(kode, kode2) {
        let data = {
            IDTransBeli: kode,
            NoUrut: kode2
        }
        let dtretur = {
            IDTransRetur: "<?= $dtinduk['IDTransRetur'] ?>"
        }

        get_response("<?= base_url('transaksi/transaksi_pembelian/check_retur') ?>", dtretur, function(response) {
            if (response.status === false) {
                showSwal1('error', 'Peringatan', 'Gagal menghapus data. Silahkan hapus transaksi retur terlebih dahulu.');
                return false;
            } else {
                get_response("<?= base_url('transaksi/transaksi_pembelian/hapusdetail') ?>", data, function(response) {
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
        post_response("<?= base_url('transaksi/transaksi_pembelian/simpandetail') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data.").then(function() {
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

    function simpanpembelian(self, data_post) {
        post_response("<?= base_url('transaksi/transaksi_pembelian/simpanpembelian') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalPembelian').modal('hide');
                showSwal("success", "Informasi", "Berhasil input data").then(function() {
                    self[0].reset();
                    if (response.stj == 'on') {
                        window.location.reload();
                    } else {
                        window.location.href = "<?= base_url('transaksi/transaksi_kas/jurnalmanual/') ?>" + btoa("trans_beli") + "/" + btoa(unescape(encodeURIComponent(response.idjurnal))) + "/" + btoa("<?= $IDTransBeli ?>") + "/" + btoa("transaksi_pembelian/detail");
                    }
                });
            } else {
                $('#ModalPembelian').modal('hide');
                showSwal("error", "Gagal", response.msg).then(function() {
                    $('#ModalPembelian').modal('show');
                });
            }
        });
    }

    function hapusbeli(kode) {
        let data = {
            IDTransBeli: kode
        }
        let dtretur = {
            IDTransRetur: "<?= $dtinduk['IDTransRetur'] ?>"
        }

        get_response("<?= base_url('transaksi/transaksi_pembelian/check_retur') ?>", dtretur, function(response) {
            if (response.status === false) {
                showSwal1('error', 'Peringatan', 'Gagal menghapus data. Silahkan hapus transaksi retur terlebih dahulu.');
                return false;
            } else {
                get_response("<?= base_url('transaksi/transaksi_pembelian/hapus') ?>", data, function(response) {
                    if (response.status === false) {
                        showSwal('error', 'Peringatan', response.msg);
                        return false;
                    } else {
                        $table.ajax.reload();
                        showSwal('success', 'Informasi', 'Transaksi berhasil dibatalkan.').then(function() {
                            window.location.href = "<?= base_url('transaksi/trans_beli') ?>";
                        });
        
                    }
                })
            }
        })

    }

    $("#table-belidetail").on("click", ".btnedit", function() {
        const model = $(this).data('model');
        $('#form-simpan')[0].reset();
        $('#NoUrut').val(model.NoUrut);
        $('#TotalLama').val(model.Total);
        $('#KodeBarang').val(model.KodeBarang).change();
        $('#KodeBarang').attr('disabled', true);
        $('#HargaSatuan').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.HargaSatuan).replace("Rp", "").trim());
        $('#Qty').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Qty).replace("Rp", "").trim());
        $("input[name=JenisDiskon][value=Nominal]").prop('checked', true);
        $('#Diskon').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(model.Diskon).replace("Rp", "").trim());
        $('#SatuanBarang').val(model.SatuanBarang);
        $('#Spesifikasi').val(model.Spesifikasi);
        if (model.TglPO) {
            $('#Qty').attr('readonly', true);
        } else {
            $('#Qty').attr('readonly', false)
        }
        document.getElementById("SatuanEdit1").innerHTML = model.SatuanBarang;
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Edit Data');
    });

    $("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#KodeBarang').attr('disabled', false);
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
                        $('#HargaSatuan').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(data.HargaBeliTerakhir).replace("Rp", "").trim());
                        $('#Spesifikasi').val(data.Spesifikasi);
                        document.getElementById("SatuanEdit1").innerHTML = data.SatuanBarang;
                    } else {
                        $('#SatuanBarang').val('');
                        $('#HargaSatuan').val('');
                        $('#Spesifikasi').val('');
                        document.getElementById("SatuanEdit1").innerHTML = "";
                    }
                }
            });
        });
        $('#view_file').hide();
    });

    $("#btnmemo").on("click", function() {
        $('#ModalMemo').modal('show');
    });

    $(".simpanbeli").on("click", function() {
        $('#form-simpan-pembelian')[0].reset();
        $('#ModalPembelian').modal('show');
        $('#defaultModalLabel').html('Transaksi');
        $('#NoRef_Manual').val("<?= $dtinduk['NoRef_Manual']; ?>");
        $('#TanggalPembelian').val("<?= $dtinduk['TanggalPembelian']; ?>");
        $("input[name=JenisDiskonBawah][value=Nominal]").prop('checked', true);
        $('#DiskonBawah').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format("<?= $dtinduk['DiskonBawah'] ?>").replace("Rp", "").trim());
        var nilaippn = "<?= $dtinduk['PPN'] ?>";
        $('#NilaiPPN').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(nilaippn).replace("Rp", "").trim());
        if (parseInt(nilaippn) > 0) {
            $('#radioPrimary8').prop('checked', true);
        } else {
            $('#radioPrimary9').prop('checked', true);
        }
        $('#TotalTransaksi').val(Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format("<?= $dtinduk['TotalTransaksi'] ?>").replace("Rp", "").trim());
        $('#TotalTagihan').val(tagawal);
        var akhir = document.getElementById('t-akhir');
        var dp = document.getElementById('dp');
        var akunkas = document.getElementById('akunkas');
        var gudang = document.getElementById('gudang');
        akhir.style.display = 'none';
        dp.style.display = 'none';
        akunkas.style.display = 'none';
        gudang.style.display = 'none';
        $('#btnsimpan').attr('hidden', true);
        $('#hitung').attr('hidden', false);
        $('#btnreset').attr('hidden', true);
    });

    function getHitung() {
        var JenisDiskonBawah = $('input[name="JenisDiskonBawah"]:checked').val();
        var JenisPPN = $('input[name="JenisPPN"]:checked').val();
        var DiskonBawah = $('#DiskonBawah').val().split('.').join('').split(',').join('.');
        var tagihanawal = Number("<?= $tagihanawal ?>").toFixed(2);
        var tanpadiskon = Number("<?= $tanpadiskon ?>").toFixed(2);
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
            var gudang = document.getElementById('gudang');
            akhir.style.display = 'block';
            dp.style.display = 'block';
            akunkas.style.display = 'block';
            gudang.style.display = 'block';
            $('#btnsimpan').attr('hidden', false);
            $('#hitung').attr('hidden', true);
            $('#btnreset').attr('hidden', false);
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
        $('#DiskonBawah').val('');
        $('#NilaiDiskon').val('');
        $('#NilaiPPN').val('');
        $('#NominalBelumPajak').val('');
        $('#TagihanAkhir').val('');
        $('#TotalTransaksi').val('');

        var akhir = document.getElementById('t-akhir');
        var dp = document.getElementById('dp');
        var akunkas = document.getElementById('akunkas');
        var gudang = document.getElementById('gudang');
        akhir.style.display = 'none';
        dp.style.display = 'none';
        akunkas.style.display = 'none';
        gudang.style.display = 'none';
        $('#btnsimpan').attr('hidden', true);
        $('#hitung').attr('hidden', false);
        $('#btnreset').attr('hidden', true);
    }
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('HargaSatuan');
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