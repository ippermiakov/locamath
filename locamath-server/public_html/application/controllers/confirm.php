<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Confirm extends CI_Controller {
    
    public function _remap($method,$arg){
        $this->_index();
    }
    
    protected function _index(){
	if(!$this->input->get('link') && !$this->input->get('link_child_delete')){
            $this->load->view('confirm_error');
        }else{
    	    $link = $this->input->get('link');
    	    if($link){
        	$user = $this->db->from('users')->where('confirmation_code',base64_decode($link))->where('is_confirm',0)->get()->row_array();
        	if(count($user)){
            	    $this->db->where('id',$user['id'])->update('users',array('is_confirm'=>1));
            	    $data = array();
            	    $data['message'] = "Account has been confirmed.";
            	    $this->load->view('confirm_success', $data);
        	}else{
            	    $this->load->view('confirm_error');
        	}
    	    }
    	    
    	    $link_child_delete = $this->input->get('link_child_delete');
    	    if($link_child_delete){
        	$mChild = new Model_Child;
    	        $result = $mChild->deleteChildByConfirmationCode(base64_decode($link_child_delete));
        	if($result){
            	    $data = array();
            	    $data['message'] = "Child has been deleted.";
            	    $this->load->view('confirm_success', $data);
        	}else{
            	    $this->load->view('confirm_error');
        	}
    	    }
    	}
    }
}
