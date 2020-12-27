<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class API_Controller extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->_construct();
    }
    
    protected function _construct(){}

    public function _remap($method,$args){
        $method = strtolower($method).'Action';
        if(method_exists($this,$method)){
            call_user_func_array(array($this,$method),$args);
        }else{
            show_404();
        }
    } 

}
