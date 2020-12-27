<?php	

class Model_Response extends Model_Object{
    
    protected $_errorList = array(
        1000 => 'Unable to perform action',
        1001 => 'Email and Password fields are required',
        1002 => 'Incorrect email format',
        1003 => 'User with same email already exists',
        1004 => 'Incorrect password',
        1005 => 'User not found',
        1006 => 'Social Id is required',
        1007 => 'User not found, you need to specify email to register',
        1008 => 'Name field is required',
        1009 => 'Child with this name already exists',
        1010 => 'Child not found',
        1011 => 'You need to set active child',
        1012 => 'You need to set JSON data',
        1013 => 'Permissions Denied',
        1014 => 'Missed level identifier value',
        1015 => 'Missed task identifier value',
        1016 => 'Missed action identifier value',
        1017 => 'Missed subAction identifier value',
        1018 => 'Missed hint identifier value',
        1019 => 'Incorrect JSON format',
        1020 => 'Data is empty',
        1021 => 'Authorization Error',
        1022 => 'Missed level path identifier value',
        1023 => 'You need to confirm your account',
        1024 => 'Email is required',
        1025 => 'Email, Old password and New password are required',
        1026 => 'Object type and Level are required',
        1027 => 'Unable to fetch json data file',
        1028 => 'There is no such object',
        1029 => 'There is no such level',
        1030 => 'Level is required',
        1031 => 'Longitude and latitude are required',
        1111 => 'You should update application to make it work properly!'
    );
    
    protected $_errors = array();
    
    public function clearErrors(){
        $this->_errors = array();
        return $this;
    }
    
    public function addError($error){
        $this->_errors[(int)$error] = isset($this->_errorList[$error]) ? $this->_errorList[$error] : '';
        return $this;
    }
    
    public function isHaveErrors(){
        return count($this->_errors) ? true : false;
    }
    
    public function getErrors(){
        return $this->_errors;
    }
    
    public function send(){
//	echo "here";
//	$this->load->library('user_agent');
	
        if($this->isHaveErrors()){
            $this->setData('status','fail');
            $this->setData('errors',$this->_errors);
        }else{
            $this->setData('status','ok');
        }
        header('Content-Type: application/json');
        echo json_encode($this->getData());
        exit;
    }
    
}
