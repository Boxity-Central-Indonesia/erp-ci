<!doctype html>
<html lang="en" dir="ltr">
<?php $this->load->view('layout/header') ?>

<body class="layout-light side-menu overlayScroll">
    <div class="mobile-search">
        <form class="search-form">
            <span data-feather="search"></span>
            <input class="form-control mr-sm-2 box-shadow-none" type="text" placeholder="Search...">
        </form>
    </div>

    <div class="mobile-author-actions"></div>
    <header class="header-top">
        <div id="fortoast"></div>
        <?php $this->load->view('layout/toolbar') ?>

    </header>
    <main class="main-content">

        <script>
            var flip = '';
        </script>

        <?php $this->load->view('layout/menu') ?>

        <div class="contents">
            <div class="container-fluid">
                <?php $this->load->view(isset($view) ? $view : 'errors/html/error_404'); ?>
            </div>
        </div>
        <?php $this->load->view('layout/footer') ?>
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
    <div class="overlay-dark-sidebar"></div>

    <!-- <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDduF2tLXicDEPDMAtC6-NLOekX0A5vlnY"></script> -->
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
    <script src="<?= base_url('assets/') ?>vendor_assets/js/cleave.min.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/drag-drop.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/footable.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/full-calendar.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/googlemap-init.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/icon-loader.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/jvectormap-init.js"></script>
    <script src="<?= base_url('assets/') ?>theme_assets/js/leaflet-init.js"></script>


    <!-- datatables -->
    <script src="<?= base_url() ?>assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url('assets/plugins/datatables/js/dataTables.buttons.min.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/datatables/js/buttons.bootstrap4.min.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/datatables/js/jszip.min.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/datatables/js/pdfmake.min.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/datatables/js/vfs_fonts.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/datatables/js/buttons.html5.min.js') ?>"></script>

    <script src="<?= base_url('assets/') ?>theme_assets/js/main.js"></script>
    <!-- endinject-->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function startTime() {
            const today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            dd = checkDate(dd);
            var mm = today.toLocaleString('default', { month: 'long' });
            var yyyy = today.getFullYear();
            let h = today.getHours();
            let m = today.getMinutes();
            let s = today.getSeconds();
            m = checkTime(m);
            s = checkTime(s);

            var dow = [
                "Minggu",
                "Senin",
                "Selasa",
                "Rabu",
                "Kamis",
                "Jumat",
                "Sabtu"
            ],
            months = [
                "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Augustus",
                "September",
                "Oktober",
                "November",
                "Desember"
            ];

            var hr;
            var ampm = (today.getHours() >= 12) ? "PM" : "AM";
            if (today.getHours() == 0) {
                hr = 12;
            } else if (today.getHours() > 12) {
                hr = today.getHours() - 12;
            } else {
                hr = today.getHours();
            }

            var sapaan;
            if (today.getHours() >= 5 && today.getHours() < 11) {
                sapaan = "pagi";
            } else if (today.getHours() >= 11 && today.getHours() < 15) {
                sapaan = "siang";
            } else if (today.getHours() >= 15 && today.getHours() < 19) {
                sapaan = "sore";
            } else {
                sapaan = "malam";
            }

            document.getElementById('digitalClock').innerHTML = dow[today.getDay()] + ", " + dd + " " + months[today.getMonth()] + " " + yyyy + " " + hr + ":" + m + " " + ampm;
            document.getElementById('sapaan').innerHTML = sapaan;
            setTimeout(startTime, 1000);
        }
        function checkDate(i) {
            if (i < 10) {i = i.replace(0, '')};  // remove zero in front of numbers < 10
            return i;
        }
        function checkTime(i) {
            if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
            return i;
        }

        var statusbalance = "<?= $this->akses->getStatusBalance(); ?>";
        var showBalance = document.getElementById('showBalance');
        if (statusbalance == 'on' && flip == 1) {
            showBalance.style.display = 'block';
            var balance = <?= getBalance() ?>;
            document.getElementById('balance').innerHTML = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 0}).format(balance).trim();
        } else {
            showBalance.style.display = 'none';
            var balance = 0;
            document.getElementById('balance').innerHTML = Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 0}).format(balance).trim();
        }

        function openFlip() {
            window.location.href = "<?= base_url('user/flip') ?>";
        }
    </script>

    <script>
        console.clear();
        $(document).ready(function() {
            unread();

            function unread() {
                $.ajax({
                    type: "GET",
                    url: "<?= base_url() ?>user/chats/getunread",
                    data: {
                        pengirim: "<?= $this->session->userdata('UserName') ?>"
                    },
                    dataType: "json",
                    success: function(r) {
                        var html = "";
                        var d = r.data;
                        // console.log(d.length);
                        pengirim = "<?= $this->session->userdata('UserName') ?>";
                        d.forEach(d => {
                            var status      = (d.IsOnline == 1) ? "author-online" : "author-offline";
                            var last_chat   = (d.LastUnreadChat.IsiPesan != null) ? d.LastUnreadChat.IsiPesan : d.LastUnreadChat.FileName;
                            var waktu       = new Date(d.LastUnreadChat.TglChat);
                            var time_since  = timeSince(waktu);
                            var url_image   = (d.Photo == null) ? "<?= base_url('assets/img/avatar.svg.png') ?>" : "<?= base_url('assets/img/users/') ?>" + d.Photo;

                            html += `
                            <li class="${status}" onclick="getpenerimatoolbar('${d.UserName}')" style="cursor: pointer;">
                                <div class="user-avater">
                                    <img src="${url_image}" alt="no image">
                                </div>
                                <div class="user-message">
                                    <p>
                                        <span class="subject stretched-link text-truncate" style="max-width: 180px;">${d.ActualName}</span>
                                        <span class="time-posted">${time_since}</span>
                                    </p>
                                    <p>
                                        <span class="desc text-truncate" style="max-width: 215px;">${last_chat}</span>
                                        <span class="msg-count badge-circle badge-success badge-sm">${d.JumlahPesan}</span>
                                    </p>
                                </div>
                            </li>`;
                        });
                        $('#total_unread').html(d.length);
                        $('#list_unread').html(html);

                        if (d.length > 0) {
                            $('#badge-pesan').removeClass("nav-settings");
                            $('#badge-pesan').addClass("nav-message");
                        } else {
                            $('#badge-pesan').removeClass("nav-message");
                            $('#badge-pesan').addClass("nav-settings");
                        }
                    }
                });
            }
            var timesRun = 0;
            var interval = setInterval(function(){
                timesRun += 1;
                unread();
                console.clear();
                if(timesRun === 300){
                    clearInterval(interval);
                    window.location.reload();
                }
                //do whatever here..
            }, 6000);
        });

        const intervals = [
            { label: 'year', seconds: 31536000 },
            { label: 'month', seconds: 2592000 },
            { label: 'day', seconds: 86400 },
            { label: 'hour', seconds: 3600 },
            { label: 'minute', seconds: 60 },
            { label: 'second', seconds: 1 }
        ];

        function timeSince(date) {
            const seconds = Math.floor((Date.now() - date.getTime()) / 1000);
            const interval = intervals.find(i => i.seconds < seconds);
            const count = Math.floor(seconds / interval.seconds);
            return `${count} ${interval.label}${count !== 1 ? 's' : ''} ago`;
        }

        function getpenerimatoolbar(d) {
            var penerima = d;
            window.location.replace("<?= base_url() ?>user/chats?rcv=" + btoa(d));
        }

        $('#btn-logout').click(function(e) {
            e.preventDefault();

            const href = $(this).attr('href')

            Swal.fire({
                title: 'Peringatan',
                text: 'Anda yakin akan keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA7C41',
                cancelButtonColor: '#1a0d06',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = href
                }
            })

        });
    </script>

    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $(function() {
        //Initialize Select2 Elements
        $('.select2').select2()
        $('.select-cabang').select2()
      })

      // const ax = axios.create({
      //   baseURL: "<?= base_url() ?>"
      // });

      const log = (data) => {
        console.log(data)
      }

      // function axGet(url, params = {}) {
      //   return ax.get(url, params)
      //     .then(res => res.data)
      //     .catch(err => err);
      // }

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

      function showSwal1(icon, title, text) {
        return Swal.fire({
          icon: icon,
          title: title,
          text: text
        })
      }

      function showSwal(icon, title, text) {
        var id = document.getElementById('fortoast');
        const Toast = Swal.mixin({
          toast: true,
          position: 'top',
          showConfirmButton: false,
          timer: 1300,
          timerProgressBar: false,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        })

        return Toast.fire({
          icon: icon,
          title: text,
          target: id
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

      function get_response(url, data, callback) {
        $.ajax({
          type: "GET",
          url: url,
          data: data,
          dataType: "json",
          success: function(response) {
            callback(response)
          },
          error: function(xhr, status, error) {
            console.log(xhr, status, error)
          }
        });
      }

      function formatRupiah(angka, prefix) {
        var number_string = String(angka).replace(/[^,\d]/g, '').toString(),
          split = number_string.split(','),
          sisa = split[0].length % 3,
          rupiah = split[0].substr(0, sisa),
          ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
          separator = sisa ? '.' : '';
          rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
      }

      function initSelect2() {
        const select2 = $('.select2').select2({
          theme: 'bootstrap4'
        })
        return select2
      }
    </script>

    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>

    <?php if (isset($scripts)) {
        if (is_array($scripts)) {
            foreach ($scripts as $script) {
                $this->load->view($script);
            }
        } else {
            $this->load->view($scripts);
        }
    } ?>
</body>

</html>