  <!-- Navbar -->
  <nav class="navbar navbar-light">
      <div class="navbar-left">
          <a href="" class="sidebar-toggle" style="
    width: 40px;
    height: 40px;
    padding: 10px;
">
              <img class="svg" src="https://res.cloudinary.com/boxity-id/image/upload/v1703865011/bars_nsb93e.svg" alt="Boxity assets svg/png"></a>
          <a class="navbar-brand" href="/"><img class="dark" src="https://res.cloudinary.com/boxity-id/image/upload/v1678791965/asset_boxity/logo/logo_primary_um5cgb.png" alt="svg"><img class="light" src="https://res.cloudinary.com/boxity-id/image/upload/v1678792040/asset_boxity/logo/logo_with_text_white_-_primary_nnszu2.png" alt="Boxity assets svg/png"></a>
          <!-- <form action="/" class="search-form">
              <span data-feather="search"></span>
              <input class="form-control mr-sm-2 box-shadow-none" type="text" placeholder="Search...">
          </form> -->

          <body>
              <span class="las la-wallet" style="
    margin-right: 10px;
    font-size: 1.5rem;
"></span>
              <ul id="showBalance" style="display:none;">
                  <li>
                      <div style="font-size: 12px; font-family: sans-serif; color: black;">Saldo</div>
                  </li>
                  <li>
                      <div><span style="font-size:14px; font-family:sans-serif; font-weight: bold; cursor:pointer;" onclick="openFlip()" id="balance"></span></div>
                  </li>
              </ul>
          </body>
      </div>
      <!-- ends: navbar-left -->
      <div class="navbar-right">
          <ul class="navbar-right__menu">
              <li class="nav-search d-none">
                  <a href="#" class="search-toggle">
                      <i class="la la-search"></i>
                      <i class="la la-times"></i>
                  </a>
                  <form action="/" class="search-form-topMenu">
                      <span class="search-icon" data-feather="search"></span>
                      <input class="form-control mr-sm-2 box-shadow-none" type="text" placeholder="Search...">
                  </form>
              </li>
              <li class="nav-message" id="badge-pesan">
                  <div class="dropdown-custom">
                      <a href="javascript:;" class="nav-item-toggle">
                          <span data-feather="mail"></span></a>
                      <div class="dropdown-wrapper">
                          <h2 class="dropdown-wrapper__title">Messages <span class="badge-circle badge-success ml-1" id="total_unread"></span></h2>
                          <ul id="list_unread"></ul>
                          <a href="<?= base_url('user/chats') ?>" class="dropdown-wrapper__more">See All Message</a>
                      </div>
                  </div>
              </li>
              <!-- ends: nav-message -->
              <li class="nav-search">
                  <ul>
                      <li>
                          <div style="font-size:16px; font-family:sans-serif; color:black; font-weight: bold; text-align: right;"><?= $this->session->userdata('ActualName'); ?></div>
                      </li>
                      <li>
                          <div style="font-size: 14px; font-family: sans-serif; color: black;"><span class="label-dot dot-success"></span>Online - <span data-feather="database" class="nav-item-toggle" style="width:16px; height:16px;"></span> <?= $this->db->database ?></div>
                      </li>
                  </ul>
              </li>
              <!-- ends: .nav-flag-select -->
              <li class="nav-author">
                  <?php
                    $url_foto = ($this->session->userdata('photo') != null) ? base_url('assets/img/users/' . $this->session->userdata('photo')) : "https://res.cloudinary.com/boxity-id/image/upload/v1703865472/male_avatar_uhy4qg.svg";
                    ?>
                  <div class="dropdown-custom">
                      <a href="javascript:;" class="nav-item-toggle"><img src="<?= $url_foto ?>" alt="" class="rounded-circle"></a>
                      <div class="dropdown-wrapper">
                          <div class="nav-author__info">
                              <div class="author-img">
                                  <img src="<?= $url_foto ?>" style="max-width:42px;" alt="" class="rounded-circle">
                              </div>
                              <div>
                                  <h6 style="font-size: 16px !important;"><?= $this->session->userdata('ActualName'); ?></h6>
                                  <div style="font-size: 14px; font-family: sans-serif; color: black;"><span class="label-dot dot-success"></span>Online<br><span data-feather="database" class="nav-item-toggle" style="width:16px; height:16px;"></span> <?= $this->db->database ?></div>
                                  <!-- <span><?= $this->session->userdata('LevelID'); ?></span> -->
                              </div>
                          </div>
                          <?php
                            $profile = 0;
                            $gantipass = 0;
                            $gantidb = 0;

                            $view = [];
                            foreach ($this->session->userdata('fiturview') as $key => $value) {
                                $view[$key] = $value;
                                if ($key == 61 && $value == 1) {
                                    $profile = 1;
                                }
                                if ($key == 62 && $value == 1) {
                                    $gantipass = 1;
                                }
                                if ($key == 63 && $value == 1) {
                                    $gantidb = 1;
                                }
                            }
                            ?>
                          <div class="nav-author__options">
                              <?php if ($profile == 1 || $gantipass == 1 || $gantidb == 1) { ?>
                                  <ul>
                                      <?php if ($profile == 1) { ?>
                                          <li>
                                              <a href="<?= base_url('user/profile') ?>">
                                                  <span data-feather="user"></span> Profile</a>
                                          </li>
                                      <?php } ?>
                                      <?php if ($gantipass == 1) { ?>
                                          <li>
                                              <a href="<?= base_url('user/ganti_password') ?>">
                                                  <span data-feather="key"></span> Ganti Password</a>
                                          </li>
                                      <?php } ?>
                                      <?php if ($gantidb) { ?>
                                          <li>
                                              <a href="<?= base_url('user/ganti_db') ?>">
                                                  <span data-feather="server"></span> Switch Database</a>
                                          </li>
                                      <?php } ?>
                                  </ul>
                              <?php } ?>
                              <a href="<?= base_url('login/logout') ?>" id="btn-logout" class="nav-author__signout">
                                  <span data-feather="log-out"></span> Sign Out</a>
                          </div>
                      </div>
                      <!-- ends: .dropdown-wrapper -->
                  </div>
              </li>
              <!-- ends: .nav-author -->
          </ul>
          <!-- ends: .navbar-right__menu -->
          <div class="navbar-right__mobileAction d-md-none">
              <a href="#" class="btn-search">
                  <span data-feather="search"></span>
                  <span data-feather="x"></span></a>
              <a href="#" class="btn-author-action">
                  <span data-feather="more-vertical"></span></a>
          </div>
      </div>
      <!-- ends: .navbar-right -->
  </nav>