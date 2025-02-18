<style type="text/css">
    #ModalTambah {
        overflow-y: scroll !important;
    }

    .user_img {
        width: 35px;
        height: 40px;
        border: 1.5px solid #f5f6fa;
    }

    .img_cont {
        position: relative;
        width: 35px;
        height: 40px;
    }

    .smalling {
        font-size: 12px;
    }

    .online_icon {
        position: absolute;
        height: 10px;
        width: 10px;
        background-color: #4cd137;
        border-radius: 50%;
        bottom: 0.2em;
        right: 0.4em;
        border: 1.5px solid white;
    }

    .offline {
        position: absolute;
        height: 10px;
        width: 10px;
        background-color: #c23616 !important;
        border-radius: 50%;
        bottom: 0.2em;
        right: 0.4em;
        border: 1.5px solid white;
    }

    .jml_pesan {
        position: absolute;
        height: 15px;
        width: 15px;
        border-radius: 50%;
        top: 0.2em;
        left: 0.4em;
        border: 1.5px solid white;
    }
</style>

<div class="social-dash-wrap">
    <div class="row">
        <div class="col-lg-12">

            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title"><?= @$title ?></h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                </div>
            </div>

        </div>
        <div class="col-lg-12 mb-30">
            <!-- <div class="card-header color-dark fw-500">
                Pesan
            </div> -->
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-9 mb-20">
                        <div class="card card-default card-md bg-white ">
                            <div class="chat">
                                <div class="chat-body bg-white radius-xl">
                                    <div class="chat-header bg-white">
                                        <div class="media chat-name align-items-center">
                                            <div class="media-body align-self-center ">
                                                <h5 class=" mb-0 fw-500 mb-2"><?= ($penerima != '') ? $dtpenerima['ActualName'] : 'User' ?></h5>
                                                <div class="d-flex align-items-center">
                                                    <span id="icon" class="badge-dot mr-1"></span>
                                                    <small id="status" class="d-flex color-light fs-12 text-capitalize"></small>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($penerima != '') { ?>
                                        <div class="chat-box p-xl-30 pl-lg-20 pr-lg-0" id="isian">
                                            <!-- Start: Incomming -->
                                            <div class="flex-1 incoming-chat mt-30" hidden>
                                                <div class="chat-text-box">
                                                    <div class="media ">
                                                        <div class="chat-text-box__photo ">
                                                            <img src="#" class="align-self-start mr-15 wh-46" alt="Boxity assets svg/png">
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="chat-text-box__content">
                                                                <div class="chat-text-box__title d-flex align-items-center">
                                                                    <h6 class="fs-14">Domnic Harys</h6>
                                                                    <span class="chat-text-box__time fs-12 color-light fw-400 ml-15">8:30
                                                                        PM</span>
                                                                </div>
                                                                <div class="d-flex align-items-center mb-20 mt-10">
                                                                    <div class="chat-text-box__subtitle p-20 bg-primary">
                                                                        <p class="color-white">Jam nonumy eirmod tempor invidunt ut
                                                                            labore
                                                                            et dolore magna.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End: Incomming -->
                                            <!-- Start: Outgoing -->
                                            <div class="flex-1 justify-content-end d-flex outgoing-chat" hidden>
                                                <div class="chat-text-box">
                                                    <div class="media ">
                                                        <div class="media-body">
                                                            <div class="chat-text-box__content">
                                                                <div class="chat-text-box__title d-flex align-items-center justify-content-end mb-2">
                                                                    <span class="chat-text-box__time fs-12 color-light fw-400">8:30
                                                                        PM</span>
                                                                </div>
                                                                <div class="d-flex align-items-center justify-content-end">
                                                                    <div class="chat-text-box__subtitle p-20 bg-deep">
                                                                        <p class="color-gray">Jam nonumy eirmod tempor invidunt ut
                                                                            labore et
                                                                            dolore magna.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End: Outgoing  -->

                                        </div>
                                        <div class="chat-footer px-xl-30 px-lg-20 pb-lg-30 pt-1" id="tombol">
                                            <div class="chat-type-text">
                                                <div class="pt-0 outline-0 pb-0 pr-0 pl-0 rounded-0 position-relative d-flex align-items-center" tabindex="-1">
                                                    <form action="#" method="post" id="kirim-pesan" style="width: 100%;" enctype="multipart/form-data">
                                                        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
                                                            <div class=" flex-1 d-flex align-items-center chat-type-text__write ml-0">
                                                                <input type="hidden" class="form-control" id="pengirim" name="pengirim" value="<?= $this->session->userdata('UserName') ?>">
                                                                <input type="hidden" class="form-control" id="penerima" name="penerima" value="<?= @$penerima ?>">
                                                                <input class="form-control border-0 bg-transparent box-shadow-none" id="isipesan" name="isipesan" placeholder="Type your message...">
                                                                <div class="input-group" hidden id="inputanfile">
                                                                    <input type="file" class="form-control border-0 bg-transparent box-shadow-none file" id="attachment" name="attachment" onchange="fileSelected(this)">
                                                                    <div class="input-icon">
                                                                        <button type="button" class="close text-capitalize" aria-label="Close" id="btnreset" onclick="resetMyform()">
                                                                            <span data-feather="x" aria-hidden="true"></span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="chat-type-text__btn">
                                                                <button type="button" id="btn-file" class="border-0 btn-deep color-light wh-50 p-10 rounded-circle file" onclick="openAttachment()" value="File">
                                                                    <span data-feather="paperclip"></span></button>
                                                                <button type="submit" id="btn-kirim" class="border-0 btn-primary wh-50 p-10 rounded-circle">
                                                                    <span data-feather="send"></span></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="chat-box p-xl-30 pl-lg-20 pr-lg-0" id="beginning">
                                            Select user to start a chat!
                                        </div>
                                    <?php } ?>
                                </div>
                            </div><!-- ends: .chat -->
                        </div>
                    </div>
                    <div class="col-lg-3 mb-20">
                        <div class="card card-default card-md bg-white ">
                            <div class="card-header uang">
                                <h5>Users</h5>
                            </div>
                            <div class="card-body contacts_body">
                                <ul id="list_user"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>