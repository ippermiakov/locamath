<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function safe($array, $key, $default = NULL){
    return (isset($array[$key]) ? $array[$key] : $default);
}

if(!function_exists('base_url')){
    function base_url($uri = ''){
        $CI =& get_instance();
        return $CI->config->base_url($uri);
    }
}

function getTable($table = ''){
    $ci =& get_instance();
    return $ci->db->dbprefix.$table;
}
