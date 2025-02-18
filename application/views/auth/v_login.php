<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Boxity ERP - Login</title>

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
    <!-- recaptcha -->
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body style="background: #fff !important;">
    <main class="main-content">

        <div class="col-lg-12" style="background-color: white;">
            <center style="padding-top: 5px; padding-bottom: none !important;">
                <img width="auto" height="65px" src="https://res.cloudinary.com/boxity-id/image/upload/v1678791965/asset_boxity/logo/logo_primary_um5cgb.png" style="
    margin: 1rem 0;
">
                <!-- <span style="font-size:24px; font-weight:bold; color:black !important;">BOXITY</span> -->
            </center>
        </div>

        <div class="signUP-admin">
            <div class="container">
                <div class="row justify-content-center" style="margin-top: 2rem !important;">
                    <div class="col-xl-4 col-lg-4 col-md-4 p-0">
                        <div class="signIn-admin-left position-relative">
                            <div class="signUP-overlay">
                                <!-- <img class="svg signupTop" src="<?= base_url('assets/') ?>img/svg/signupTop.svg" alt="Boxity Assets" />
                                <img class="svg signupBottom" src="<?= base_url('assets/') ?>img/svg/signupBottom.svg" alt="Boxity Assets" /> -->
                                <img class="img-fluid svg" src="https://res.cloudinary.com/boxity-id/image/upload/v1703865013/logins_hkc8rp.svg" alt="Boxity Assets" />
                            </div><!-- End: .signUP-overlay  -->
                        </div><!-- End: .signUP-admin-left  -->
                    </div><!-- End: .col-xl-4  -->
                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8">
                        <div class="signIn-admin-right  p-md-40 p-10">
                            <h2 style="font-weight: bold; margin-bottom: 1rem;">Sign in</h2>
                            <span class="text-muted mt-3" style="font-size: 1rem !important;">This page is protected by reCAPTCHA and subject to the Google Privacy Policy and Terms of service.</span>
                            <div class="row">
                                <div class="col-xl-10 col-lg-8 col-md-12">
                                    <div class="edit-profile mt-md-25 mt-0">
                                        <div class="card border-0">
                                            <!-- <div class="card-header border-0  pb-md-15 pb-10 pt-md-20 pt-10 ">
                                                <div class="edit-profile__title">
                                                </div>
                                            </div> -->
                                            <div class="card-body" style="padding:0 !important;">
                                                <form action="<?= base_url('login/loginuser') ?>" method="post" id="myForm">
                                                    <div class="edit-profile__body">
                                                        <div class="form-group mb-20">
                                                            <label for="username">Username</label>
                                                            <input type="hidden" style="color:#501F08; background:#FEF0EA;" name="tokens" class="form-control" id="tokens" placeholder="Username" readonly>
                                                            <input type="text" style="color:#501F08; background:#FEF0EA;" name="username" class="form-control" id="username" placeholder="Username" required>
                                                        </div>
                                                        <div class="form-group mb-15">
                                                            <label for="password-field">password</label>
                                                            <div class="position-relative input-group">
                                                                <input style="color:#501F08; background:#FEF0EA;" name="password" id="password" type="password" class="form-control" placeholder="Password" required>
                                                                <div class="input-group-append" style="cursor: pointer">
                                                                    <div class="input-group-text" style="background: #FEF0EA; border: none !important;">
                                                                        <i style="color:#95370B;" class="fa fa-eye" id="togglePassword"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="g-recaptcha" data-sitekey="<?php echo $this->config->item('google_key') ?>"></div>
                                                        <div class="signUp-condition signIn-condition">
                                                            <div class="checkbox-theme-default custom-checkbox " hidden>
                                                                <input class="checkbox" type="checkbox" id="check-1">
                                                                <label for="check-1">
                                                                    <span class="checkbox-text">Keep me logged in</span>
                                                                </label>
                                                            </div>
                                                            <a href="<?= base_url('login/forgot') ?>">forgot / reset password?</a>
                                                        </div>
                                                        <div class="button-group d-flex pt-1 justify-content-md-start justify-content-center">
                                                            <button class="btn btn-primary btn-block btn-squared mr-15 text-capitalize lh-normal px-50 py-15 signIn-createBtn " style="background: #F95B11 !important;">
                                                                sign in
                                                            </button>
                                                        </div>
                                                        <!-- <p class="social-connector text-center mb-sm-25 mb-15  mt-sm-30 mt-20"><span>Or</span></p>
                                                        <div class="button-group d-flex align-items-center justify-content-md-start justify-content-center">
                                                            <ul class="signUp-socialBtn">
                                                                <li>
                                                                    <button class="btn text-dark px-30"><img class="svg" src="<?= base_url('assets/') ?>img/svg/google.svg" alt="Boxity Assets" /> Sign up with
                                                                        Google</button>
                                                                </li>
                                                                <li>
                                                                    <button class=" radius-md wh-48 content-center"><img class="svg" src="<?= base_url('assets/') ?>img/svg/facebook.svg" alt="Boxity Assets" /></button>
                                                                </li>
                                                                <li>
                                                                    <button class="radius-md wh-48 content-center"><img class="svg" src="<?= base_url('assets/') ?>img/svg/twitter.svg" alt="Boxity Assets" /></button>
                                                                </li>
                                                            </ul>
                                                        </div> -->
                                                    </div>
                                                </form>
                                            </div><!-- End: .card-body -->
                                        </div><!-- End: .card -->
                                    </div><!-- End: .edit-profile -->
                                </div><!-- End: .col-xl-5 -->
                            </div>
                            <p style="margin-top:1rem !important;">
                                ©2022 - Boxity Central Indonesia.<br>
                                All Rights Reserved. Web App Version 0.0.1
                            </p>
                        </div><!-- End: .signUp-admin-right  -->
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

    <!-- firebase -->
    <script src="https://www.gstatic.com/firebasejs/7.16.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.16.1/firebase-messaging.js"></script>
    <script>
        // TODO: Replace with your project's customized code snippet
        var config = {
            'messagingSenderId': '991735093530',
            'apiKey': 'AIzaSyBz-3zig-1E43VRMlg9v9kI7bORiRp0p6w',
            'projectId': 'boxity-app',
            'appId': '1:991735093530:web:62e909ac5ac1150b5774a3',
        };
        firebase.initializeApp(config);

        const messaging = firebase.messaging();
        messaging
            .requestPermission()
            .then(function() {
                console.log("Notification permission granted.");

                // get the token in the form of promise
                return messaging.getToken()
            })
            .then(function(token) {
                var tkn = token;
                // console.log(tkn);
                $('#tokens').val(token);
            })
            .catch(function(err) {
                console.log("Unable to get permission to notify.", err);
            });
    </script>

    <script>
        function checkRecaptcha() {
            res = $('#g-recaptcha-response').val();

            if (res == "" || res == undefined || res.length == 0)
                return false;
            else
                return true;
        }

        $('#myForm').submit(function(e) {
            e.preventDefault();
            if (!checkRecaptcha()) {
                // $( "#frm-result" ).text("Please validate your reCAPTCHA.");
                Swal.fire({
                    title: 'Peringatan',
                    text: "Please validate your reCAPTCHA.",
                    icon: 'info',
                    showCancelButton: false,
                    confirmButtonColor: '#FA7C41',
                    confirmButtonText: 'Ok'
                })
                return false;
            } else {
                var self = $(this)
                let data_post = new FormData(self[0]);
                login_yok(self, data_post);
                return false;
            }
            //...
        });

        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });

        function login_yok(self, data_post) {
            post_response("<?= base_url('login/loginuser') ?>", data_post, function(response) {
                if (response.status) {
                    Swal.fire({
                        title: 'Informasi',
                        text: "Login Berhasil",
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#FA7C41',
                        confirmButtonText: 'Ok'
                    }).then(function() {
                        self[0].reset();
                        window.location.href = "<?= base_url() ?>" + response.url;
                    });
                } else {
                    Swal.fire({
                        title: 'Peringatan',
                        text: response.msg,
                        icon: 'error',
                        showCancelButton: false,
                        confirmButtonColor: '#FA7C41',
                        confirmButtonText: 'Ok'
                    });
                    self[0].reset();
                }
            });
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