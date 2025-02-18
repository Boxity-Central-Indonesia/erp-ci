<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CI_Email {
	public $useragent = 'CodeIgniter';
	public $mailpath = '/usr/sbin/sendmail';
	public $protocol = 'smtp';
	public $smtp_host = 'boxity.id';
	public $smtp_user = 'info@boxity.id';
	public $smtp_pass = '5YD}~e&Q2Leq';
	public $smtp_port = 465;
	public $smtp_timeout = 5;
	public $smtp_keepalive = FALSE;
	public $smtp_crypto = 'ssl';
	public $wordwrap = TRUE;
	public $wrapchars = 76;
	public $mailtype = 'html';
	public $charset = 'UTF-8';
	public $alt_message = '';
	public $validate = TRUE;
	public $priority = 3;
	public $newline = "\r\n";
	public $crlf = "\r\n";
	public $dsn = TRUE;
	public $send_multipart = TRUE;
	public $bcc_batch_mode = FALSE;
	public $bcc_batch_size = 200;

	protected $_safe_mode = FALSE;
	protected $_subject = '';
	protected $_body = '';
	protected $_finalbody = '';
	protected $_header_str = '';
	protected $_smtp_connect = '';
	protected $_encoding = '8bit';
	protected $_smtp_auth = TRUE;
	protected $_replyto_flag = FALSE;
	protected $_debug_msg = array();
	protected $_recipients = array();
	protected $_cc_array = array();
	protected $_bcc_array = array();
	protected $_headers = array();
	protected $_attachments = array();
	protected $_protocols = array('mail', 'sendmail', 'smtp');
	protected $_base_charsets = array('us-ascii', 'iso-2022-');
	protected $_bit_depths = array('7bit', '8bit');
	protected $_priorities = array(
		1 => '1 (Highest)',
		2 => '2 (High)',
		3 => '3 (Normal)',
		4 => '4 (Low)',
		5 => '5 (Lowest)'
	);
	protected static $func_overload;

	public function __construct(array $config = array()) {
		$this->charset = config_item('charset');
		$this->initialize($config);
		$this->_safe_mode = (!is_php('5.4') && ini_get('safe_mode'));
		isset(self::$func_overload) OR self::$func_overload = (!is_php('8.0') && extension_loaded('mbstring') && @ini_get('mbstring.func_overload'));
		log_message('info', 'Email Class Initialized');
	}

	public function initialize(array $config = array()) {
		$this->clear();
		foreach ($config as $key => $val) {
			if (isset($this->$key)) {
				$method = 'set_'.$key;
				if (method_exists($this, $method)) {
					$this->$method($val);
				} else {
					$this->$key = $val;
				}
			}
		}
		$this->charset = strtoupper($this->charset);
		$this->_smtp_auth = isset($this->smtp_user[0], $this->smtp_pass[0]);
		return $this;
	}

	public function clear($clear_attachments = FALSE) {
		$this->_subject = '';
		$this->_body = '';
		$this->_finalbody = '';
		$this->_header_str = '';
		$this->_replyto_flag = FALSE;
		$this->_recipients = array();
		$this->_cc_array = array();
		$this->_bcc_array = array();
		$this->_headers = array();
		$this->_debug_msg = array();
		$this->set_header('Date', $this->_set_date());
		if ($clear_attachments !== FALSE) {
			$this->_attachments = array();
		}
		return $this;
	}

	public function from($from, $name = '', $return_path = NULL) {
		if (preg_match('/\<(.*)\>/', $from, $match)) {
			$from = $match[1];
		}
		$this->set_header('From', $name.' <'.$from.'>');
		isset($return_path) OR $return_path = $from;
		$this->set_header('Return-Path', '<'.$return_path.'>');
		return $this;
	}
}