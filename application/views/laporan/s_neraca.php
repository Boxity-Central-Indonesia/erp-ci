<script>
    // $('#tgl-transaksi').datepicker({
    //     // format: 'yyyy-mm-dd',
    //     dateFormat: "dd-mm-yy",
    //     altFormat: "yy-mm-dd",
    //     altField: "#altField",
    //     autoclose: true
    //     // defaultDate: new Date()
    // });

    $('#bln').on("input", function(e) {
        $("#myForm").submit();
	});
</script>