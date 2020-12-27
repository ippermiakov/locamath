<?php
class Model_help extends API_Model{
    
    protected $_title = 'Developer API';
    protected $_htmlData = '';
    protected $_aIsSeted = array();

    public function title($title){
        $this->_title = $title;
    }
    
    public function clear(){
        $this->_aIsSeted = array();
    }
    
    public function section($name){
        $this->_htmlData .= '<h1>'.$name.'</h1>';
    }
    
    public function action($action = ''){
        $this->clear();
        $this->_htmlData .= '<p>'.$action.'</p>';
    }
    
    public function code(){
        $this->_htmlData .= '<code><pre>';
    }
    
    public function __call($label,$args = array()){
        if(!in_array($label,$this->_aIsSeted)){
            $this->_htmlData .= "<b>".ucfirst(preg_replace('#[^A-Za-z0-9]#si',' ',$label)).":</b>\n";
            $this->_aIsSeted[] = $label;
        }
        $name = isset($args[0]) ? $args[0] : '';
        $value = (isset($args[1]) ? ' => '.$args[1] : '');
        $this->_htmlData .= (isset($args[2]) && $args[2] ? "<b>*</b>" : " ").' '.($value ? '[' : '').$name.($value ? ']' : '').$value."\n";
    }
    
    public function _code(){
        $this->_htmlData .= '</pre></code>';
    }
 
    public function toString(){
        $html  = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>'.$this->_title.'</title>';
        $html .= '<style type="text/css">';
        $html .= '::selection{ background-color: #E13300; color: white; }';
        $html .= '::moz-selection{ background-color: #E13300; color: white; }';
        $html .= '::webkit-selection{ background-color: #E13300; color: white; }';
        $html .= 'body {background-color: #fff;margin: 40px;font: 13px/20px normal Helvetica, Arial, sans-serif;color: #4F5155;}';
        $html .= 'h1 {color: #444;background-color: transparent;border-bottom: 1px solid #D0D0D0;font-size: 19px;font-weight: normal;margin: 0 0 14px 0;padding: 14px 15px 10px 15px;}';
        $html .= 'code {font-family: Consolas, Monaco, Courier New, Courier, monospace;font-size: 12px;background-color: #f9f9f9;border: 1px solid #D0D0D0;color: #002166;display: block;margin: 14px 0 14px 0;padding: 12px 10px 12px 10px;}';
        $html .= 'pre{margin: 0;}';
        $html .= '#body{margin: 0 15px 0 15px;}';
        $html .= '#container{margin: 10px;border: 1px solid #D0D0D0;-webkit-box-shadow: 0 0 8px #D0D0D0;}';
        $html .= '</style></head>';
        $html .= '<body><div id="container"><h1>'.$this->_title.'</h1><div id="body">';
        $html .= ($this->_htmlData ? $this->_htmlData : '<code><pre>Not Available</pre></code>');
        $html .= '</div></div></body></html>';
        return $html;
    } 
}
