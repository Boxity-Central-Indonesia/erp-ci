<script>
    countdownTimeStart();
    function countdownTimeStart(){
        var expdate = "<?= $data['ExpDate'] ?>";
        var countDownDate = new Date(expdate).getTime();

        // Update the count down every 1 second
        var x = setInterval(function() {

            // Get todays date and time
            var now = new Date().getTime();
            
            // Find the distance between now an the count down date
            var distance = countDownDate - now;
            
            // Time calculations for days, hours, minutes and seconds
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Output the result in an element with id="bataswaktu"
            document.getElementById("bataswaktu").innerHTML = hours + "h "
            + minutes + "m " + seconds + "s ";
            
            // If the count down is over, write some text 
            if (distance < 0) {
                clearInterval(x);
                document.getElementById("bataswaktu").innerHTML = "KADALUARSA";
            }
        }, 1000);
    }

    function copyFunction() {
        // Get the text field
        var copyText = $('#norek').val();

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText);
        
        // Alert the copied text
        showSwal1('success', 'Informasi', 'Berhasil menyalin Nomor VA: ' + copyText);
    }
</script>