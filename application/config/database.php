<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$db['db2'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'u481419426_user_erp_setti',
	'password' => 'j@cAU9n@U9Wk7mS',
	'database' => 'u481419426_erp_setting_db',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$con = mysqli_connect($db['db2']['hostname'], $db['db2']['username'], $db['db2']['password'], $db['db2']['database']);
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
  	exit();
}
$data = mysqli_query($con, "SELECT * FROM u481419426_erp_setting_db.list_db WHERE isaktif = 1");
$dflt = mysqli_fetch_assoc($data);

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => $dflt['hostname'],
	'username' => $dflt['username'],
	'password' => $dflt['psw'],
	'database' => $dflt['db'],
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
