<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class API_Router extends CI_Router {

    function _set_routing(){
        parent::_set_routing();
        $this->setCronController();
    }
    
    function _set_overrides($routing){
        parent::_set_overrides($routing);
        $this->setCronController();
    }
    
    function setCronController(){
        if(php_sapi_name() === 'cli' OR defined('STDIN')){
            $this->set_class('cron');
            $this->set_method('index');
        }
    }

}
