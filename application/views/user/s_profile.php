<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#UserPsw');

    togglePassword.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
    });

    $(document).ready(function() {
        $("#form-simpan").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            let data = {
                emailLama: $('#emailLama').val(),
                Email: $('#Email').val()
            }
            get_response("<?= base_url('user/profile/checkEmail') ?>", data, function(response) {
                if (response.status === false) {
                    showSwal('error', 'Peringatan', response.msg);
                    return false;
                } else {
                    simpan(self, data_post);
                }
            });
            return false;
        });
    });

    function simpan(self, data_post) {
        post_response("<?= base_url('user/profile/simpan') ?>", data_post, function(response) {
            if (response.status) {
                showSwal("success", "Informasi", response.msg).then(function() {
                    self[0].reset();
                    window.location.reload();
                });
            } else {
                showSwal("error", "Gagal", response.msg);
            }
        });
    }
</script>