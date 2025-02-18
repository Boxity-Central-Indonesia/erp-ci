<?php
if (!function_exists('currency')) { // ganti ke mata uang
	function currency($value)
	{
		$currency = number_format($value, 0, ".", ".");
		return $currency;
	}
}
if (!function_exists('convert_percent')) { // ganti ke persen
	function convert_percent($value)
	{
		$number = $value / 100;
		return $number;
	}
}
if (!function_exists('protect')) { // protect input html
	function protect($str)
	{
		$str = htmlentities($str, ENT_QUOTES, 'UTF-8');
		return $str;
	}
}
if (!function_exists('escape')) { // escape string input injection
	function escape($str)
	{
		$str = get_instance()->db->escape_str($str);
		return $str;
	}
}
if (!function_exists('protect_input_xss')) { // protect input xss
	function protect_input_xss($str)
	{
		$str = htmlentities(strip_tags(htmlspecialchars($str)), ENT_QUOTES, 'UTF-8');
		return $str;
	}
}
if (!function_exists('setresponse')) {
	function setresponse($code, $data = array())
	{
		header('content-type:application/json');
		http_response_code($code);
		echo json_encode($data);
		exit;
	}
}
if (!function_exists('redirect_back')) {
	function redirect_back()
	{
		redirect($_SERVER['HTTP_REFERER']);
	}
}

if (!function_exists('swal')) {
	/**
	 * @param string $message
	 * @param string $title optional
	 * @param string $type success, info, warning, error
	 * @return void
	 */
	function swal($message, $title = '', $type = 'success')
	{
		$CI = &get_instance();
		$CI->session->set_flashdata("swal", [
			'title' => $title,
			'msg' => $message,
			'icon' => $type
		]);
	}
}

if (!function_exists('str_limit')) {
	function str_limit($desc, $length = 20)
	{
		if (strlen($desc) > $length) {
			$limited = substr($desc, 0, $length);
			return '<span title="' . $desc . '">' . $limited . '...</span>';
		} else {
			return $desc;
		}
	}
}

if (!function_exists('getBalance')) {
	function getBalance()
	{
		$CI = &get_instance();
	    return $CI->akses->getbalance();
	}
}

if (!function_exists('checkAccess')) {
	function checkAccess($value)
	{
		if(!isset($value) || $value == 0){  
            redirect(base_url('errors/er403'));
        }
	}
}

if (!function_exists('dataPerusahaan')) {
	function dataPerusahaan($val)
	{
		$CI = &get_instance();
		return $CI->akses->dt_perusahaan($val);
	}
}
