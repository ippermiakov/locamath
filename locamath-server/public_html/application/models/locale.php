<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_Locale extends API_Model {
    
    protected $_locales = null;
    
    public function getLocales(){
        if($this->_locales === null){
            $locales = $this->db->from("locales")->order_by('is_system','desc')->get()->result_array();
            $this->_locales = array();
            foreach($locales as $locale){
                $this->_locales[$locale['id']] = $locale['locale'];
            }
        }
        return $this->_locales;
    }
    
    public function getLocaleByStr($locale = 'en'){
        $locales = $this->getLocales();
        $locales = array_flip($locales);
        if(isset($locales[$locale])){
            return $locales[$locale];
        }
        return 1;
    }  

    public function getLocaleById($locale_id = 1){
        $locales = $this->getLocales();
        if(isset($locales[$locale_id])){
            return $locales[$locale_id];
        }
        return 'en';
    }
}   
