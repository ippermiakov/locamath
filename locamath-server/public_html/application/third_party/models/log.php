<?php	

class Model_Log {
        
    public function run($path = null){
        if(!$path){
            $path = 'requests.log';
        }
        $path = FCPATH.$path;
        $ci =& get_instance();
        $ci->load->helper('file');
        $data = "[".date('Y-m-d H:i:s')."]\r\n";
        $data .= "\t[GET]: \r\n";
        if(isset($_GET) && count($_GET)){
            foreach($_GET as $k => $v){
                $data .= "\t\t[".$k."] => ".$v."\r\n";
            }
        }else{
            $data .= "\t\t --EMPTY-- \r\n";
        }
        $data .= "\t[POST]: \r\n";
        if(isset($_POST) && count($_POST)){
            foreach($_POST as $k => $v){
                $data .= "\t\t[".$k."] => ".$v."\r\n";
            }
        }else{
            $data .= "\t\t --EMPTY-- \r\n";
        }
        $data .= "\t[SERVER]: \r\n";
        if(isset($_SERVER) && count($_SERVER)){
            foreach($_SERVER as $k => $v){
                $data .= "\t\t[".$k."] => ".$v."\r\n";
            }
        }else{
            $data .= "\t\t --EMPTY-- \r\n";
        }
        $data .= "\r\n";
        write_file($path,$data,'a');
    }
}
