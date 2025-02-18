<script>
	$(document).ready(function() {
		users();

		function users() {
			$.ajax({
				type: "GET",
				url: "<?= base_url() ?>user/chats/getusers",
				data: {
					pengirim: "<?= $this->session->userdata('UserName') ?>"
				},
				dataType: "json",
				success: function(r) {
					var html = "";
					var d = r.data;
					var status = "";
					var icon = "";
					pengirim = "<?= $this->session->userdata('UserName') ?>";
					d.forEach(d => {
						if (d.IsOnline == 1) {
							status = "online";
							icon = "online_icon";
						} else {
							status = "offline";
							icon = "offline";
						}
						var hdn = (d.JumlahPesan > 0) ? '' : 'hidden';
						var url_image = (d.Photo == null) ? "<?= base_url('assets/img/avatar.svg.png') ?>" : "<?= base_url('assets/img/users/') ?>" + d.Photo;

						html += `
						<li class="active coba" onclick="getpenerima('${d.UserName}')" style="cursor: pointer;">
							<div class="d-flex bd-highlight ">
								<div class="img_cont ">
									<span class="msg-count badge-circle badge-success badge-sm jml_pesan" ${hdn}>${d.JumlahPesan}</span>
									<img src="${url_image}" alt="Boxity assets svg/png" class="rounded-circle user_img">
									<span class="${icon} " ></span>
								</div>
								<div class="user_info ">	
									<span class="">${d.ActualName}</span>
									<p class="smalling">${d.ActualName} is ${status} </p>
								</div>
							</div>
						</li>`;
					});
					$('#list_user').html(html);
				}
			});
		}
		// setInterval(() => {
		// 	users()
		// }, 7000);

		var timesRun = 0;
		var interval = setInterval(function() {
			timesRun += 1;
			users();
			if (timesRun === 300) {
				clearInterval(interval);
			}
			//do whatever here..
		}, 6000);
	});

	function getpenerima(d) {
		var penerima = d;
		window.location.replace("<?= base_url() ?>user/chats?rcv=" + btoa(d));
	}

	var receiver = "<?= $penerima ?>";
	if (receiver != '') {
		scrollToBottom();
		pesan();

		function pesan() {
			var pengirim = "<?= $this->session->userdata('UserName') ?>";
			var penerima = receiver;
			$.ajax({
				type: "GET",
				url: "<?= base_url() ?>user/chats/getpesan",
				data: {
					pengirim: pengirim,
					penerima: penerima
				},
				dataType: "json",
				success: function(p) {
					var html = "";
					var rcv = p.rcv;
					var d = p.data;
					if (rcv.IsOnline == 1) {
						$('#status').html("active now");
						$('#icon').removeClass("dot-danger");
						$('#icon').addClass("dot-success");
					} else {
						$('#status').html("inactive");
						$('#icon').removeClass("dot-success");
						$('#icon').addClass("dot-danger");
					}
					d.forEach(d => {
						var today = new Date();
						var dd = String(today.getDate()).padStart(2, '0');
						var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
						var yyyy = today.getFullYear();

						today = dd + '-' + mm + '-' + yyyy;

						var times = new Date(d.TglChat)
						var time = times.toLocaleTimeString()
						var tanggal = String(times.getDate()).padStart(2, '0');
						var bulan = String(times.getMonth() + 1).padStart(2, '0');
						var tahun = times.getFullYear()
						var lengkapDB = tanggal + '-' + bulan + '-' + tahun
						var kapan = "Today"
						var tanggal_bulan = tanggal + "-" + bulan
						if (lengkapDB != today) {
							kapan = tanggal_bulan;
						}

						var tampilpesan;
						if (d.IsiPesan) {
							tampilpesanpengirim = d.IsiPesan;
							tampilpesanpenerima = d.IsiPesan;
						} else {
							tampilpesanpengirim = '<a class="color-white" href="<?= base_url('assets/chats/') ?>' + d.File + '" target="_blank" download="' + d.FileName + '">' + d.FileName + '</a>';
							tampilpesanpenerima = '<a class="color-gray" href="<?= base_url('assets/chats/') ?>' + d.File + '" target="_blank" download="' + d.FileName + '">' + d.FileName + '</a>';
						}

						var url_image = (rcv.Photo == null) ? "<?= base_url('assets/img/avatar.svg.png') ?>" : "<?= base_url('assets/img/users/') ?>" + rcv.Photo;

						if ((d.Pengirim) != pengirim) {
							html += `<div class="flex-1 incoming-chat mt-30">
                                        <div class="chat-text-box">
                                            <div class="media ">
                                                <div class="chat-text-box__photo ">
                                                    <img src="${url_image}" class="align-self-start mr-15 wh-46" alt="Boxity assets svg/png">
                                                </div>
                                                <div class="media-body">
                                                    <div class="chat-text-box__content">
                                                        <div class="chat-text-box__title d-flex align-items-center">
                                                            <h6 class="fs-14">${rcv.ActualName}</h6>
                                                            <span class="chat-text-box__time fs-12 color-light fw-400 ml-15">${kapan}, ${time}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-20 mt-10">
                                                            <div class="chat-text-box__subtitle p-20 bg-primary">
                                                                <p class="color-white">${tampilpesanpengirim}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
						} else {
							html += `<div class="flex-1 justify-content-end d-flex outgoing-chat">
		                                <div class="chat-text-box">
		                                    <div class="media ">
		                                        <div class="media-body">
		                                            <div class="chat-text-box__content">
		                                                <div class="chat-text-box__title d-flex align-items-center justify-content-end mb-2">
		                                                    <span class="chat-text-box__time fs-12 color-light fw-400">${kapan}, ${time}</span>
		                                                </div>
		                                                <div class="d-flex align-items-center justify-content-end">
		                                                	<div class="chat-text-box__other d-flex">
                                                                <div class="px-15">
                                                                    <a href="javascript:void(0);" class="btn-link border-0 bg-transparent p-0 btnhapus" onclick="hapuspesan('${d.KodeChat}')">
                                                                        <span class="fa fa-trash" aria-hidden="true"></span>
                                                                    </a>
                                                                </div>
                                                            </div>
		                                                    <div class="chat-text-box__subtitle p-20 bg-deep">
		                                                        <p class="color-gray">${tampilpesanpenerima}</p>
		                                                    </div>
		                                                </div>
		                                            </div>
		                                        </div>
		                                    </div>
		                                </div>
		                            </div>`;
						}

					});
					$('#isian').html(html);
				}
			});
		}
		// setInterval(() => {
		// 	pesan()
		// }, 2000);

		var timesRun = 0;
		var interval = setInterval(function() {
			timesRun += 1;
			pesan();
			if (timesRun === 900) {
				clearInterval(interval);
			}
			//do whatever here..
		}, 2000);

		function hapuspesan(id) {
			Swal.fire({
				title: 'Apa anda yakin?',
				// text: "data terhapus tidak dapat di kembalikan",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#FA7C41',
				cancelButtonColor: '#FA7C41',
				confirmButtonText: 'Ya, Hapus pesan!'
			}).then((result) => {
				if (result.isConfirmed) {
					hapuschat(id)
				}
			})
		}

		function hapuschat(id) {
			let data = {
				KodeChat: id
			}

			get_response("<?= base_url('user/chats/hapus') ?>", data, function(response) {
				if (response.status) {
					pesan();
				} else {
					return false;
				}
			})

		}

		function openAttachment() {
			$('#attachment').click();
		}

		function fileSelected(input) {
			$('#isipesan').val('');
			$('#isipesan').attr('hidden', true);
			$('#inputanfile').attr('hidden', false);
			$('#btn-file').value = "File: " + input.files[0].name
		}

		function resetMyform() {
			$('#isipesan').val('');
			$('#attachment').val(undefined);
			$('#isipesan').attr('hidden', false);
			$('#inputanfile').attr('hidden', true);
		}

		$('#kirim-pesan').submit(function(e) {
			e.preventDefault();
			var isipesan = $('#isipesan').val();
			var attachment = $('#attachment').val();
			var self = $(this);
			let data_post = new FormData(self[0]);
			// console.log(isipesan, attachment);
			if (isipesan != "" || isipesan != undefined || attachment != undefined) {
				simpanchat(self, data_post);
			}
			return false;
		});

		function simpanchat(self, data_post) {
			post_response("<?= base_url('user/chats/kirimpesan') ?>", data_post, function(response) {
				if (response.status) {
					self[0].reset();
					$('#isipesan').attr('hidden', false);
					$('#inputanfile').attr('hidden', true);
					scrollToBottom();
				} else {
					self[0].reset();
				}
			});
		}
		scrollToBottom();

		function scrollToBottom() {
			$("#isian").animate({
				scrollTop: 200000000000000000000000000000000
			}, "slow");
		}

		pesan();
	}
</script>