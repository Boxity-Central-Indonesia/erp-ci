<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Boxity ERP - Reset Password</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- inject:css-->

    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/daterangepicker.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/fontawesome.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/footable.standalone.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/fullcalendar@5.2.0.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/jquery-jvectormap-2.0.5.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/leaflet.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/magnific-popup.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/MarkerCluster.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/MarkerCluster.Default.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/select2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/slick.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/star-rating-svg.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/trumbowyg.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>vendor_assets/css/wickedpicker.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/') ?>style.css">
    <!-- endinject -->
    <link rel="icon" type="image/png" sizes="16x16" href="https://res.cloudinary.com/boxity-id/image/upload/v1678791753/asset_boxity/logo/icon-web_qusdsv.png">
</head>

<body>
    <main class="main-content">

        <div class="col-lg-12" style="background-color: white;">
            <center style="padding-top: 5px; padding-bottom: none !important;">
                <img width="40px" height="40px" src="https://res.cloudinary.com/boxity-id/image/upload/v1678791753/asset_boxity/logo/icon-web_qusdsv.png">
                <span style="font-size:24px; font-weight:bold; color:black !important;">BOXITY</span>
            </center>
        </div>

        <div class="signUP-admin">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-6 col-md-6 p-0">
                        <div class="signIn-admin-left position-relative">
                            <div class="signUP-overlay">
                                <!-- <img class="svg signupTop" src="<?= base_url('assets/') ?>img/svg/signupTop.svg" alt="Boxity Assets" />
                                <img class="svg signupBottom" src="<?= base_url('assets/') ?>img/svg/signupBottom.svg" alt="Boxity Assets" /> -->
                                <img class="img-fluid svg" src="https://res.cloudinary.com/boxity-id/image/upload/v1703865013/logins_hkc8rp.svg" alt="Boxity Assets" />
                            </div><!-- End: .signUP-overlay  -->
                        </div><!-- End: .signUP-admin-left  -->
                    </div><!-- End: .col-xl-4  -->
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-8">
                        <div class="signIn-admin-right  p-md-40 p-10">
                            <h3 style="font-weight: bold;">Reset Password</h3>
                            <div class="row">
                                <div class="col-xl-10 col-lg-12 col-md-12">
                                    <div class="edit-profile mt-md-25 mt-0">
                                        <div class="card border-0">
                                            <!-- <div class="card-header border-0 pt-0 pb-0">
                                                <div class="signUp-header-top mt-md-0 mt-30">
                                                </div>
                                            </div> -->
                                            <div class="card-body pt-20 pb-0">
                                                <form action="<?= base_url('login/sendreset') ?>" method="post" id="myForm">
                                                    <div class="edit-profile__body">
                                                        <div class="form-group mb-20">
                                                            <label for="password-field">new password</label>
                                                            <input type="hidden" class="form-control" name="Email" id="Email" value="<?= $Email ?>">
                                                            <div class="position-relative input-group">
                                                                <input style="color:#501F08; background:#FEF0EA;" name="password" id="password" type="password" class="form-control" placeholder="New Password" onkeyup="matching()" required>
                                                                <div class="input-group-append" style="cursor: pointer">
                                                                    <div class="input-group-text" style="background: #FEF0EA; border: none !important;">
                                                                        <i style="color:#95370B;" class="fa fa-eye" id="togglePassword"></i>
                                                                    </div>
                                                                </div>
                                                                &nbsp;&nbsp;
                                                                <span id="matches" style="color:green; display:none; padding-top:10px; font-weight:bold;"><i class="fas fa-check-circle"></i>&nbsp;Match!</span>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-20">
                                                            <label for="password-field">confirm password</label>
                                                            <div class="position-relative input-group">
                                                                <input style="color:#501F08; background:#FEF0EA;" name="confirmation" id="confirmation" type="password" class="form-control" placeholder="Confirm Password" onkeyup="matching()" required>
                                                                <div class="input-group-append" style="cursor: pointer">
                                                                    <div class="input-group-text" style="background: #FEF0EA; border: none !important;">
                                                                        <i style="color:#95370B;" class="fa fa-eye" id="togglePassword2"></i>
                                                                    </div>
                                                                </div>
                                                                &nbsp;&nbsp;
                                                                <span id="matches2" style="color:green; display:none; padding-top:10px; font-weight:bold;"><i class="fas fa-check-circle"></i>&nbsp;Match!</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex mb-sm-35 mb-20">
                                                            <button class="btn btn-primary btn-default btn-squared text-capitalize lh-normal px-md-50 py-15 signIn-createBtn" id="btnreset" disabled>
                                                                Reset Password
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div><!-- End: .card -->
                                    </div><!-- End: .edit-profile -->
                                </div><!-- End: .col-xl-5 -->
                            </div>
                            <br><br><br>
                            <p>
                                ©2022 - Boxity Central Indonesia.<br>
                                All Rights Reserved. Web App Version 0.0.1
                            </p>
                        </div> <!-- End: .signUp-admin-right  -->
                    </div><!-- End: .col-xl-8  -->
                </div>
            </div>
        </div><!-- End: .signUP-admin  -->

    </main>
    <div id="overlayer">
        <span class="loader-overlay">
            <div class="atbd-spin-dots spin-lg">
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
            </div>
        </span>
    </div>

    <!-- inject:js-->
    <script src="<?= base_url('assets/') ?>vendor_assets/js/jquery/jquery-3.5.1.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/jquery/jquery-ui.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/bootstrap/popper.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/bootstrap/bootstrap.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/moment/moment.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/accordion.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/autoComplete.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/Chart.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/charts.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/daterangepicker.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/drawer.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/dynamicBadge.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/dynamicCheckbox.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/feather.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/footable.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/fullcalendar@5.2.0.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/google-chart.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/jquery-jvectormap-2.0.5.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/jquery-jvectormap-world-mill-en.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/jquery.countdown.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/jquery.filterizr.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/jquery.magnific-popup.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/jquery.mCustomScrollbar.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/jquery.peity.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/jquery.star-rating-svg.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/leaflet.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/leaflet.markercluster.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/loader.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/message.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/moment.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/muuri.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/notification.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/popover.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/select2.full.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/slick.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/trumbowyg.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/trumbowyg.upload64.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor_assets/js/wickedpicker.min.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/drag-drop.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/footable.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/full-calendar.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/googlemap-init.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/icon-loader.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/jvectormap-init.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/leaflet-init.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/main.js"></script>

    <!-- endinject-->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function showconfirm(title, text) {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                allowOutsideClick: false,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            })
        }

        function showSwal(icon, title, text) {
            return Swal.fire({
                icon: icon,
                title: title,
                text: text
            })
        }

        function post_response(url, data, callback) {
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    callback(response)
                },
                error: function(xhr, status, error) {
                    console.log(xhr, status, error)
                }
            });
        }

        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });

        const togglePassword2 = document.querySelector('#togglePassword2');
        const confirmation = document.querySelector('#confirmation');

        togglePassword2.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = confirmation.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmation.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });

        $(document).ready(function() {
            $("#myForm").submit(function(e) {
                e.preventDefault();
                var self = $(this)
                let data_post = new FormData(self[0]);
                simpan(self, data_post);
                return false;
            });
        });

        function simpan(self, data_post) {
            post_response("<?= base_url('login/sendreset') ?>", data_post, function(response) {
                if (response.status) {
                    $('#ModalTambah').modal('hide');
                    showSwal("success", "Informasi", response.msg).then(function() {
                        // self[0].reset();
                        window.location.href = "<?= base_url('login') ?>";
                    });
                } else {
                    $('#ModalTambah').modal('hide');
                    showSwal("error", "Gagal", response.msg);
                }
            });
        }

        var matches = document.getElementById('matches');
        var matches2 = document.getElementById('matches2');
        matches.style.display = 'none';
        matches2.style.display = 'none';
        $('#btnreset').attr('disabled', true);

        function matching() {
            var newpass = $('#password').val();
            var conf = $('#confirmation').val();

            if (newpass != '' && conf != '') {
                if (newpass == conf) {
                    matches.style.display = 'block';
                    matches2.style.display = 'block';
                    $('#btnreset').attr('disabled', false);
                } else {
                    matches.style.display = 'none';
                    matches2.style.display = 'none';
                    $('#btnreset').attr('disabled', true);
                }
            }

            console.log(newpass, conf);
        }
    </script>

    <?php if ($msg = $this->session->flashdata('swal')) : ?>
        <script>
            Swal.fire({
                title: "<?= @$msg['title'] ?>",
                html: "<?= @$msg['msg'] ?>",
                icon: "<?= @$msg['icon'] ?>"
            })
        </script>
    <?php endif ?>
</body>

</html>