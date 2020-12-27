<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function index() {
      echo 'index';
    }
    
    public function _remap($method,$arg){
        if(!$this->input->is_cli_request()) show_404();
        if(defined('CRON_SCHEDULE')){
            $this->_run();
        }
        exit;
    }
    
    protected function _run(){
        if(defined('CRON_TEST')){
            $this->load->helper('file');
            write_file(FCPATH.'cron.txt','Executed at '.date('Y-m-d H:i:s')); 
            exit;
        }
    }
}
