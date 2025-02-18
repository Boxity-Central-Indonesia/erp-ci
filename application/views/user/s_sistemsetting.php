<script>
    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpan(self, data_post);
            return false;
        });

        $("#form-simpan-reset").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            let data = {
                psw: $('#password').val()
            }
            get_response("<?= base_url('user/sistemsetting/checkPsw') ?>", data, function(response) {
                if (response.status === false) {
                    $('#ModalReset').modal('hide');
                    showSwal('error', 'Peringatan', response.msg).then(function() {
                        $('#ModalReset').modal('show');
                        $('#defaultModalLabel').html('Form Force Reset Data');
                    });
                    return false;
                } else {
                    forcerst(self, data_post);
                }
            });
            return false;
        });

        // $("#btn-forcereset").on("click", function() {
        //     Swal.fire({
        //         title: 'Apa anda yakin?',
        //         text: "Jika melakukan Force Reset Data maka semua data akan terhapus kecuali Data Master.",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#FA7C41',
        //         cancelButtonColor: '#FA7C41',
        //         confirmButtonText: 'Ya!'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             forcereset()
        //         }
        //     })
        // });
    });

    function simpan(self, data_post) {
        post_response("<?= base_url('user/sistemsetting/simpan') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalTambah').modal('hide');
                showSwal("success", "Informasi", response.msg).then(function() {
                    self[0].reset();
                    window.location.reload();
                });
            } else {
                $('#ModalTambah').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    function forcerst(self, data_post) {
        post_response("<?= base_url('user/sistemsetting/forcereset') ?>", data_post, function(response) {
            if (response.status) {
                $('#ModalReset').modal('hide');
                showSwal("success", "Informasi", response.msg).then(function() {
                    self[0].reset();
                    window.location.reload();
                });
            } else {
                $('#ModalReset').modal('hide');
                showSwal("error", "Gagal", response.msg);
            }
        });
    }

    // function forcereset() {
    //     let data = {
    //         confirm: '1'
    //     }
    //     get_response("<?= base_url('user/sistemsetting/forcereset') ?>", data, function(response) {
    //         if (response.status === false) {
    //             showSwal('error', 'Peringatan', response.msg);
    //             return false;
    //         } else {
    //             showSwal('success', 'Informasi', response.msg).then(function() {
    //                 window.location.reload();
    //             });

    //         }
    //     })
    // }

    function matching() {
        var newpass = $('#password').val();
        var conf = $('#confirmation').val();

        if (newpass != '' && conf != '') {
            if (newpass == conf) {
                $('#btnsavereset').attr('disabled', false);
            } else {
                $('#btnsavereset').attr('disabled', true);
            }
        }

        console.log(newpass, conf);
    }

    $("#btn-forcereset").on("click", function() {
        $('#form-simpan-reset')[0].reset();
        $('#ModalReset').modal('show');
        $('#defaultModalLabel').html('Form Force Reset Data');
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });

        const togglePassword2 = document.querySelector('#togglePassword2');
        const confirmation = document.querySelector('#confirmation');

        togglePassword2.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = confirmation.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmation.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });
    });
</script>

<script type="text/javascript">
    /* Tanpa Rupiah */
    var tanpa_rupiah = document.getElementById('LimitPinjamanKaryawan');
    tanpa_rupiah.addEventListener('keyup', function(e)
    {
        tanpa_rupiah.value = formatRupiah(this.value);
    });
</script>