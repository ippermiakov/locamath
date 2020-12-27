<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Curl {
    
    protected $url = null; 
    protected $options = array();
    protected $error = '';
    protected $errorNumber = 0;
    protected $header = '';
    protected $body = '';
    protected $cookie = '';
    protected $statusOk = 0;
    protected $verbose = 0;
    
    /* set Url */
    public function setUrl($url = null){
        $this->url = $url;
        return $this;
    }
    /* set request options */
    public function setOptions($options = null){
        foreach($options as $keyOption => $valueOption){
            $this->options[$keyOption] = $valueOption;
        }
        return $this;
    }
    /* get response */ 
    public function getResponse(){
        return (object)array(
             'header' => $this->header
            ,'body' => $this->body
            ,'cookie' => $this->cookie
            ,'error' => $this->error
            ,'errorNumber' => $this->errorNumber
            ,'statusok' => $this->statusOk
        );
    }

    /* send request */
    public function send(){
        $this->error = '';
        $this->errorNumber = 0;
        $h = curl_init();  
        curl_setopt_array($h,array(
             CURLOPT_URL => $this->url
            ,CURLOPT_HEADER => 1
            ,CURLOPT_FOLLOWLOCATION => 1
            ,CURLOPT_RETURNTRANSFER => 1
            ,CURLOPT_FAILONERROR => 0
            /*,CURLOPT_HTTPHEADER => array(
                 'Connection: keep-alive'
                ,'Content-type: application/x-www-form-urlencoded'
            )*/
            //,CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)"
            ,CURLOPT_VERBOSE => $this->verbose
        ));  
        if(count($this->options)){
            curl_setopt_array($h,$this->options);
        }
        $result = curl_exec($h);
        $this->header = substr($result,0,curl_getinfo($h,CURLINFO_HEADER_SIZE));
        if($this->header){
            preg_match("/Set-Cookie: (.+)/i",$this->header,$match);
            if(isset($match[1]) && $match[1]){
                $this->cookie = trim($match[1]);
            }
            if(preg_match("/HTTP\/1\.1 200 OK/i",$this->header,$match)){
                $this->statusOk = 1;
            }
        }
        $this->body = substr($result,curl_getinfo($h,CURLINFO_HEADER_SIZE));
        $this->errorNumber =curl_errno($h);
        if($this->errorNumber){
            $this->error = curl_error($h); 
        } 
        curl_close($h);
        return (!(bool)$this->error); 
    }
    
}
