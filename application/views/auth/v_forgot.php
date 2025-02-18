<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Boxity ERP - Forgot Password</title>

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

<style type="text/css">
    .alert.alert-dismissible .close {
        padding: 20px 20px;
        padding-top: 20px;
        padding-right: 20px;
        padding-bottom: 20px;
        padding-left: 20px;
    }
</style>

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
                            <h3 style="font-weight: bold;">Forgot Password?</h3>
                            <p>Please fill in your emails that registered with our system. We will send the confirmation page through your email.</p>
                            <div class="row">
                                <div class="col-xl-10 col-lg-8 col-md-12">
                                    <div class="edit-profile mt-md-25 mt-0">
                                        <div class="card border-0">
                                            <!-- <div class="card-header border-0 pt-0 pb-0">
                                                <div class="signUp-header-top mt-md-0 mt-30">
                                                </div>
                                            </div> -->
                                            <div class="card-body pt-20 pb-0">
                                                <br>
                                                <?php if ($this->session->flashdata('success')) { ?>
                                                    <div class=" alert alert-success alert-dismissible fade show " role="alert">
                                                        <div class="alert-icon">
                                                            <i style="color:#95370B;" class="fas fa-check-circle"></i>
                                                        </div>
                                                        <div class="alert-content">
                                                            <p><?= $this->session->flashdata('success') ?></p>
                                                        </div>
                                                        <div class="alert-icon">
                                                            <button type="button" class="close text-capitalize" data-dismiss="alert" aria-label="Close">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x" aria-hidden="true">
                                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($this->session->flashdata('error')) { ?>
                                                    <div class="alert-icon-area alert alert-danger alert-dismissible fade show " role="alert">
                                                        <div class="alert-icon">
                                                            <i style="color:#95370B;" class="fas fa-exclamation-triangle"></i>
                                                        </div>
                                                        <div class="alert-content">
                                                            <p><?= $this->session->flashdata('error') ?></p>
                                                        </div>
                                                        <div class="alert-icon">
                                                            <button type="button" class="close text-capitalize" data-dismiss="alert" aria-label="Close">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x" aria-hidden="true">
                                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <form action="<?= base_url('login/sendforgot') ?>" method="post" id="myForm">
                                                    <div class="edit-profile__body">
                                                        <div class="form-group mb-20">
                                                            <label for="username">Email</label>
                                                            <div class="position-relative input-group">
                                                                <input type="email" style="color:#501F08; background:#FEF0EA;" class="form-control" id="Email" name="Email" onclick="insert()" required>
                                                                <div class="input-group-append" id="dangericon" hidden>
                                                                    <div class="input-group-text" style="background: #FEF0EA; border: none !important;">
                                                                        <i style="color:#95370B;" class="fas fa-exclamation-triangle"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex mb-sm-35 mb-20">
                                                            <button class="btn btn-primary btn-default btn-squared text-capitalize lh-normal px-md-50 py-15 signIn-createBtn">
                                                                Request Reset Password
                                                            </button>
                                                        </div>
                                                        <p class="mb-0 fs-14 fw-500 text-gray text-capitalize">
                                                            return to
                                                            <a href="<?= base_url('login') ?>" class="color-primary">
                                                                Sign in
                                                            </a>
                                                        </p>
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
        var dangericon = document.getElementById('dangericon');
        dangericon.hidden = true;

        var fd = "<?= $this->session->flashdata('error') ?>";
        if (fd) {
            dangericon.hidden = false;
        } else {
            dangericon.hidden = true;
        }

        function insert() {
            dangericon.hidden = true;
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