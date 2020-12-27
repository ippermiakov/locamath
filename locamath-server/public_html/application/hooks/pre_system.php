<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function load_pre_system(){
    spl_autoload_register('_autoload');
}

function _autoload($class){
    if(class_exists($class,false) || interface_exists($class,false)){
        return;
    }
    /* Autoload Model */
    $subFolder = null;

    if(strpos($class,'Model_') !== false){
        $subFolder = "models";
        $pos = 6;
    }
    if($subFolder !== null){
        $path = strtolower(str_replace('_','/',substr($class,$pos)));
        if(file_exists(APPPATH.$subFolder."/".$path.EXT)){
            include_once APPPATH.$subFolder."/".$path.EXT;
        }elseif(file_exists(APPPATH."third_party/".$subFolder."/".$path.EXT)){
            include_once APPPATH."third_party/".$subFolder."/".$path.EXT;
        }
    }
}
