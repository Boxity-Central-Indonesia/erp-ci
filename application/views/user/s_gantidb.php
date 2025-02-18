<script>
	$(document).ready(function() {
        $(".btn-hapus").on("click", function() {
            const kode = $(this).data('kode');
            Swal.fire({
                title: 'Apa anda yakin?',
                text: "database terhapus tidak dapat di kembalikan",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#FA7C41',
                confirmButtonText: 'Ya, Hapus data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    hapus(kode)
                }
            })
        });
    });

    function hapus(kode) {
        let data = {
            db: kode
        }

        get_response("<?= base_url('user/ganti_db/hapus') ?>", data, function(response) {
            if (response.status === false) {
                showSwal('error', 'Peringatan', response.msg);
                return false;
            } else {
                showSwal('success', 'Informasi', 'Database berhasil dihapus.').then( function() {
                	window.location.reload();
                });

            }
        })

    }

	$("#btntambah").on("click", function() {
        $('#form-simpan')[0].reset();
        $('#ModalTambah').modal('show');
        $('#defaultModalLabel').html('Tambah Data');
        $('#hostname').val('');
        $('#username').val('');
        $('#password').val('');
        $('#database').val('');
        document.querySelector("input#database").addEventListener("input", function(){
		  	const allowedCharacters="0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBNz_"; // You can add any other character in the same way
		  	this.value = this.value.split('').filter(char => allowedCharacters.includes(char)).join('');
		});
    });

    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
    });

    $(".btn-edit").on("click", function(e) {
        e.preventDefault();
        $('#form-update')[0].reset();
        $('#ModalEdit').modal('show');
        $('#defaultModalLabel2').html('Edit Data');
        const model = JSON.parse($(this).attr('data-obj'));
        $('#hostname2').val(model.hostname);
        $('#username2').val(model.username);
        $('#password2').val(model.psw);
        $('#database2').val(model.db);
        $('#db_alias2').val(model.db_alias);
        document.querySelector("input#database2").addEventListener("input", function(){
            const allowedCharacters="0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBNz_"; // You can add any other character in the same way
            this.value = this.value.split('').filter(char => allowedCharacters.includes(char)).join('');
        });
    });

    const togglePassword2 = document.querySelector('#togglePassword2');
    const password2 = document.querySelector('#password2');

    togglePassword2.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = password2.getAttribute('type') === 'password' ? 'text' : 'password';
        password2.setAttribute('type', type);
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
    });

</script>