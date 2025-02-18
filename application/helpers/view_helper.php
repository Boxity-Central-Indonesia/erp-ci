<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('loadview')) {
	function loadview($data)
	{
		$ci = &get_instance();
		$ci->load->view('layout/content', $data);
	}
}

if (!function_exists('flashdata')) {
	function flashdata($url, $msg, $status = true)
	{
		$ci = &get_instance();
		$ci->session->set_flashdata('msg', '<div class="alert alert-' . ($status ? 'success' : 'danger') . '" id="draw" role="alert">
                <button type="button" class="close" data-dismiss="alert"  id="closeButton"><span aria-hidden="true">&times;</span><span class="sr-only"> Close </span></button><strong>'.$msg.'</strong></div>');
		redirect($url);
	}
}
