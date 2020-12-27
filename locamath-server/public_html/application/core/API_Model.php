<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class API_Model extends CI_Model {
    
    protected $_CI = null;
    protected $_messages = array();
    
    protected static $_resources = array();
    
    function __construct(){
        $this->_construct();
    }

    protected function _construct(){}

    public function getCI($key = null){
        if($key){
            return parent::__get($key);
        }
        if($this->_CI === null){
            $this->_CI =& get_instance();
        }
        return $this->_CI;
    }

    /* Messages */
    public function addMessage($message){
        if(is_array($message)){
            foreach($message as $m){
                $this->addMessage($m);
            }
        }else{
            $this->_messages[] = __($message);
        }
        return $this;
    }
    
    public function isHaveMessages(){
        return (count($this->_messages) ? TRUE: FALSE);
    }
    
    public function getMessages(){
        return $this->_messages;
    }
    
    public function __toString(){
        return $this->toString();
    }
    
    public function toString(){
        return '';
    }
    
}
