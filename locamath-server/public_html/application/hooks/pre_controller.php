<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function load_pre_controller(){
    if(!class_exists('CI_Model',false)){
        load_class('Model', 'core');
    }
}
