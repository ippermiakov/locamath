<?php	

class Model_Object extends API_Model implements ArrayAccess
{   
    protected $_data = array();
    protected $_origData;
    protected static $_underscoreCache = array();
    
    public function __construct($data = array()){
        $this->_data = $data;
        $this->_origData = $data;
        $this->_construct();
    }
    
    protected function _construct(){}
    
    public function addData(array $arr){
        foreach($arr as $index=>$value) {
            if(is_array($value)){
                $this->_data[$index] = array_merge((array)$this->_data[$index],$value);
            }else{
                $this->setData($index, $value);
            }
        }
        return $this;
    }

    public function setData($key, $value=null){
        if(is_array($key)) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    public function unsetData($key=null){
        if (is_null($key)) {
            $this->_data = array();
        } else {
            unset($this->_data[$key]);
        }
        return $this;
    }

    protected function _getData($key='', $default=null, $arrData = null, $checkExists = false){
        if(!$arrData){
            $arrData = $this->_data;
        }
        if(''===$key) {
            return ($checkExists ? !empty($arrData) : $arrData);
        }
        if(strpos($key,'/')) {
            $keyArr = explode('/', $key);
            $data = $arrData;
            foreach($keyArr as $k) {
                if($k==='') {
                    return $default;
                }
                if(is_array($data)) {
                    if (!isset($data[$k])) {
                        return $default;
                    }
                    $data = $data[$k];
                } elseif ($data instanceof obj) {
                    $data = $data->getData($k);
                } else {
                    return $default;
                }
            }
            return ($checkExists ? true : $data);
        }elseif(array_key_exists($key, $arrData)){
            return ($checkExists ? true : $arrData[$key]);
        }
        return $default;
    }
    
    public function getData($key='', $default=null){
        return $this->_getData($key, $default);
    }

    public function hasData($key=''){
        return $this->_getData($key, false,null,true);
    }

    public function toArray(){
        return $this->_data;
    }

    public function toXml($rootName = 'item', $addOpenTag=true, $addCdata=true){
        $xml = '';
        if($addOpenTag) {
            $xml.= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        }
        if(!empty($rootName)) {
            $xml.= '<'.$rootName.'>'."\n";
        }
        foreach ($this->_data as $fieldName => $fieldValue) {
            if ($addCdata === true) {
                $fieldValue = "<![CDATA[$fieldValue]]>";
            }
            $xml.= "<$fieldName>$fieldValue</$fieldName>"."\n";
        }
        if (!empty($rootName)) {
            $xml.= '</'.$rootName.'>'."\n";
        }
        return $xml;
    }

    public function toJson(){
        return json_encode($this->_data);
    }

    public function __toString(){
        return $this->toString();
    }

    public function toString($buildQuery = false){
        if($buildQuery){
            return http_build_query($this->getData());
        }else{
            $data = $this->getData();
            $html = '';
            if($data instanceof self){
                $html .= $data->getData();
            }
            elseif(is_array($data)){
                foreach($data as $item){
                    $html .= $item;
                }
            }else{
                $html .= $data;
            }    
            return $html;
        }
    }

    public function __call($method, $args){
        switch (substr($method, 0, 3)) {
            case 'get' :
                $key = $this->_underscore(substr($method,3));
                $data = $this->getData($key, isset($args[0]) ? $args[0] : null);
                return $data;
            case 'set' :
                $key = $this->_underscore(substr($method,3));
                $result = $this->setData($key, isset($args[0]) ? $args[0] : null);
                return $result;
            case 'uns' :
                $key = $this->_underscore(substr($method,3));
                $result = $this->unsetData($key);
                return $result;
            case 'has' :
                $key = $this->_underscore(substr($method,3));
                return isset($this->_data[$key]);
        }        
    }

    public function __get($var){
        $var = $this->_underscore($var);
        return $this->getData($var);
    }

    public function __set($var, $value){
        $var = $this->_underscore($var);
        $this->setData($var, $value);
    }

    public function isEmpty(){
        if (empty($this->_data)) {
            return true;
        }
        return false;
    }

    protected function _underscore($name){
        if (!isset(self::$_underscoreCache[$name])) {
            $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
            self::$_underscoreCache[$name] = $result;
        }
        return self::$_underscoreCache[$name];
    }

    public function serialize(){
        return serialize($this->_data);
    }
    
    public function resetData(){
        $this->_data = $this->_origData;
    }

    public function getOrigData($key='', $default=null){
        return $this->_getData($key, $default, $this->_origData);
    }
    
    public function setOrigData($key, $value=null){
        if(is_array($key)) {
            $this->_origData = $key;
        } else {
            $this->_origData[$key] = $value;
        }
        return $this;
    }

    public function offsetSet($offset, $value){
        $this->_data[$offset] = $value;
    }

    public function offsetExists($offset){
        return isset($this->_data[$offset]);
    }
    
    public function offsetUnset($offset){
        unset($this->_data[$offset]);
    }

    public function offsetGet($offset){
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }
}
