<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?= @$title . ' - ' ?>Boxity ERP</title>

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
  <!-- datatables -->
  <link href="<?= base_url() ?>assets/plugins/datatables/css/dataTables.bootstrap4.min.css" rel="stylesheet">
  <!-- endinject -->

  <link rel="icon" type="image/png" sizes="16x16" href="https://res.cloudinary.com/boxity-id/image/upload/v1678791753/asset_boxity/logo/icon-web_qusdsv.png">
  <style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
      cursor: default;
      color: #666 !important;
      border: 1px solid transparent;
      background: transparent;
      box-shadow: none;
    }

    .dataTables_wrapper .dataTables_info {
      clear: both;
      float: left;
      padding-top: 0.755em;
    }

    .dataTables_wrapper .dataTables_paginate {
      float: right;
      text-align: right;
      padding-top: 0.25em;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
      border-radius: 50%;
      color: #F95B12 !important;
      background: rgba(114, 124, 245, 0.1);
      border: 0 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
      border-radius: 50%;
      color: #F95B12 !important;
      background: rgba(114, 124, 245, 0.1);
      border: 0;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
      border: 0;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.previous:hover {
      background: transparent;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.next:hover {
      background: transparent;
    }

    .dataTables_wrapper input[type="search"],
    .dataTables_wrapper input[type="text"],
    .dataTables_wrapper select {
      border: 1px solid #adb5bd;
      padding: .3rem 1rem;
      color: #715d5d;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
      box-sizing: border-box;
      display: inline-block;
      min-width: 1.5em;
      padding: 0.5em 1em;
      margin-left: 2px;
      text-align: center;
      text-decoration: none !important;
      cursor: pointer;
      *cursor: hand;
      color: #333 !important;
      border: 1px solid transparent;
      border-radius: 2px;
    }

    .label-dot {
        width: 8px;
        height: 8px;
        display: inline-block;
        border-radius: 50%;
        margin-right: 3px;
    }

    .row .stock {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: 0px;
    }

    .legend-static li {
        font-size: 11px !important;
    }

    .inEx-wrap .inEx-chart .legend-static {
        margin-top: 0px !important;
    }

    .inEx-wrap .inEx-chart .legend-static li:not(:last-child) {
        margin-right: 12px;
    }

    .cashflow-display__amount {
        margin-top: 0px;
        margin-bottom: 6px;
    }

    #balance:hover {
        color: black;
    }
  </style>
</head>