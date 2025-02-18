<script>
    $(document).ready(function() {
        $("#myform").submit(function(e) {
            e.preventDefault();
            var self = $(this)
            let data_post = new FormData(self[0]);
            simpan(self, data_post);
            return false;
        });
    });
    var masukan = document.getElementById('pass_baru'),
        pas1 = document.getElementById('pas1'),
        label = document.getElementById('shw');


    pas1.onclick = function() {

        if (pas1.checked) {

            masukan.setAttribute('type', 'text');
            label.textContent = 'Hide Passowrd';
        } else {

            masukan.setAttribute('type', 'password');
            label.textContent = 'Show Passowrd';
        }

    }

    var masukanulang = document.getElementById('ulang_pass'),
        ulang = document.getElementById('ulang'),
        label2 = document.getElementById('hide');


    ulang.onclick = function() {

        if (ulang.checked) {

            masukanulang.setAttribute('type', 'text');
            label2.textContent = 'Hide Passowrd';
        } else {

            masukanulang.setAttribute('type', 'password');
            label2.textContent = 'Show Passowrd';
        }

    }
</script>