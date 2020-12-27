<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Request extends CI_Controller {

    protected $versionMajor = 1;
    protected $versionMinor = 0;
    protected $versionBuild = 60;
    
    public function _remap($method,$arg){
        @date_default_timezone_set('Europe/Moscow');
        $this->load->model('Request');
        $this->load->model('Response');
        $this->load->model('Resource');
        $this->output->enable_profiler(TRUE);
        $action = strtolower($this->Request->getData('ac')).'Action';
        
        $log = new Model_Log;
        $log->run();

	// check if the version of requesting client is appropriate
	$this->load->library('user_agent');
	preg_match('/[Mathematic|SchemesCreator]\w*\/((\d+)\.(\d+)\.(\d+))/', $this->agent->agent_string(), $version);
	if($version[2] < $this->versionMajor || $version[3] < $this->versionMinor || $version[4] < $this->versionBuild){
	    $this->Response->addError(1111);
	}
	else{
    	    if(method_exists($this,$action)){
        	$this->$action();
    	    }else{
        	$this->Response->addError(1000);
    	    }
    	}
        $this->Response->send();
    }
    
    protected function _authorize(){
        $this->load->model('User');
        $user = $this->User->getUserByAuthId($this->Request->getData('authid'));
        if(count($user)){
            $this->Auth = new Model_Object($user);
            return true;
        }
        $this->Response->addError(1021);
        $this->Response->send();
        return false;
    }
    
    public function deleteaccountAction(){
        $email = $this->Request->getData('email');
        if($email){
            $mUser = new Model_User;
            if(count($mUser->getUserByEmail($this->Request->getData('email')))){
                $this->db->where('email',$email)->delete('users');
            }else{
                $this->Response->addError(1000);
            }
        }
    }
    
    public function deletesessionAction(){
        $this->_authorize();
        $this->db->where('id',$this->Auth->getData('id'))->delete('users');
    }

    public function resetpasswordAction(){
        if(!$this->Request->getData('email')){
            $this->Response->addError(1024);
            $this->Response->send();
        }
        $this->load->helper('email');
        if(!valid_email($this->Request->getData('email'))){
            $this->Response->addError(1002);
            $this->Response->send();
        }
        $mUser = new Model_User;
        $user = $mUser->getUserByEmail($this->Request->getData('email'));
        if(count($user)){
            	    $newPassword = $this->_randomPassword(8);
            	    $saveItem = array();
            	    $saveItem['password'] = md5($newPassword);
		    $this->db->where('id',$user['id'])->update('users',$saveItem);
        	    $mMail = new Model_Mail;
        	    $mMail->sendPasswordChange($user['id'], $newPassword, $user['locale_id']);
        }else{
    	    $this->Response->addError(1005);
	}
    }

    public function changepasswordAction(){
        if(!$this->Request->getData('email') || !$this->Request->getData('oldpassword') || !$this->Request->getData('newpassword')){
            $this->Response->addError(1025);
            $this->Response->send();
        }
        $this->load->helper('email');
        if(!valid_email($this->Request->getData('email'))){
            $this->Response->addError(1002);
            $this->Response->send();
        }
        $mUser = new Model_User;
        $user = $mUser->getUserByEmail($this->Request->getData('email'));
        if(count($user)){
                    if(md5($this->Request->getData('oldpassword')) == $user['password'] || !$user['password']){
            		$saveItem = array();
            		$saveItem['password'] = md5($this->Request->getData('newpassword'));
			$this->db->where('id',$user['id'])->update('users',$saveItem);
        		$mMail = new Model_Mail;
        		$mMail->sendPasswordChange($user['id'], $this->Request->getData('newpassword'), $user['locale_id']);
                    }else{
                        $this->Response->addError(1004);
                    }
        }else{
    	    $this->Response->addError(1005);
	}
    }

    public function registerAction(){
        if(!$this->Request->getData('email') || !$this->Request->getData('password')){
            $this->Response->addError(1001);
            $this->Response->send();
        }
        $this->load->helper('email');
        if(!valid_email($this->Request->getData('email'))){
            $this->Response->addError(1002);
            $this->Response->send();
        }
        $mUser = new Model_User;
        if(count($mUser->getUserByEmail($this->Request->getData('email')))){
            $this->Response->addError(1003);
        }else{
            $confirmation_code = $mUser->getNewAccessToken();
            $mLocale = new Model_Locale;
            $actionLocale = $mLocale->getLocaleByStr($this->Request->getData('locale'));
            $data = array(
                'fb_id' => null, 
                'email' => $this->Request->getData('email'), 
                'password' => md5($this->Request->getData('password')),
                'is_confirm' => 0, 
                'confirmation_code'  => $confirmation_code,
                'locale_id' => $actionLocale,
            );
            $userId = $this->Resource->setTable('users')->save($data);
            $mMail = new Model_Mail;
            $mMail->sendConfirmation($userId, $this->Request->getData('password'), $actionLocale);
        }
    }
    

    public function updatelocationAction(){
        $this->_authorize();

        if(!$this->Request->getData('latitude') && !$this->Request->getData('longitude')){
            $this->Response->addError(1031);
            $this->Response->send();
        } 
        $mLocation = new Model_Location;
        $loc = $mLocation->getLocation($this->Request->getData('latitude'), $this->Request->getData('longitude'));
        if($loc->getCityId()){ 
	  $this->db->where('id',$this->Auth->getData('id'))->update('users',array('city_id' => $loc->getCityId()));
        }
    }

    private function _randomPassword($length){
	$alphabet = "abcdefghijklmnpqrstuwxyz123456789";
	$pass = ''; 
	$alphaLength = strlen($alphabet) - 1;
	for ($i = 0; $i < $length; $i++){
	    $n = mt_rand(0, $alphaLength);
	    $pass = $pass.$alphabet[$n];
	}
	
	return $pass;
    }
    
    public function loginAction(){
        if(!$this->Request->getData('email') || !$this->Request->getData('password')){
            $this->Response->addError(1001);
            $this->Response->send();
        }
        $this->load->helper('email');
        if(!valid_email($this->Request->getData('email'))){
            $this->Response->addError(1002);
            $this->Response->send();
        }
        $mUser = new Model_User;
        $user = $mUser->getUserByEmail($this->Request->getData('email'));
        if(count($user)){
        /* As requested allow to login without email confirmation */
                    if(md5($this->Request->getData('password')) == $user['password'] && $user['password']){
                        $this->Response->setData('authid',$mUser->createSession($user['id']));
                        $this->Response->setData('city',$user['cityName']);
                        $this->Response->setData('country',$user['countryName']);
                    }else{
                        $this->Response->addError(1004);
                    }
        }else{
            $this->Response->addError(1005);
        }
    }

    public function logindeviceAction() {
        if(!$this->Request->getData('social_id')){
            $this->Response->addError(1006);
            $this->Response->send();
        }

        $mUser = new Model_User;
        $user = $mUser->getUserByFbId($this->Request->getData('social_id'));
        if(count($user)){
    	      $responseData = array('authid' => $mUser->createSession($user['id']));
    	      $responseData['city'] = $user['cityName'];
              $responseData['country'] = $user['countryName'];
              if($user['password']) {
        	$responseData['passwordHash'] = $user['password'];
	      }
              $this->Response->setData($responseData);
        }else{
              $this->Response->addError(1005);
              $this->Response->send();
        }
    } 
      
    public function testerAction(){
    	$this->load->helper('locamath');
    	$modFile = 0;
//	dir_walk("updateFileModificationTime", "resources/", array('txt'), true, "resources/", &$modFile);
//	$this->Response->setData(array('test' => 'good', 'notest' => 'nogood', 'file' => $modFile, 'fileDate' => date ("F d Y H:i:s.", $modFile)));
    }

    public function registerdeviceAction(){
        if(!$this->Request->getData('social_id')){
            $this->Response->addError(1006);
            $this->Response->send();
        }
        if(!$this->Request->getData('email')){
            $this->Response->addError(1007);
            $this->Response->send();
        }

        if($this->Request->getData('email')){
            $this->load->helper('email');
            if(!valid_email($this->Request->getData('email'))){
                $this->Response->addError(1002);
                $this->Response->send();
            }
        }
        $mUser = new Model_User;
        $user = $mUser->getUserByFbId($this->Request->getData('social_id'));
//        $isRegisteredViaMail = 0;
        if(count($user)){
/*    	    if($this->Request->getData('autologinifexists') == 1){
    	      $responseData = array('authid' => $mUser->createSession($user['id']));
    	      $responseData['city'] = $user['cityName'];
              $responseData['country'] = $user['countryName'];

              if($user['password']) {
                $isRegisteredViaMail = 1;
        	$responseData['passwordHash'] = $user['password'];
    	      }
    	      $responseData['isRegisteredViaMail'] = $isRegisteredViaMail;

              $this->Response->setData($responseData);
            }
            else{*/
              $this->Response->addError(1003);
              $this->Response->send();
/*            }*/
        }else{
            $mLocale = new Model_Locale;
            $user = $mUser->getUserByEmail($this->Request->getData('email'));
            $responseData = array();
            if(count($user)){
            	$saveItem = array();
            	$saveItem['fb_id'] = $this->Request->getData('social_id');
            	$saveItem['is_confirm'] = 1;
            	$saveItem['confirmation_code'] = null;
            	$saveItem['locale_id'] = $mLocale->getLocaleByStr($this->Request->getData('locale'));
            	$this->db->where('id',$user['id'])->update('users',$saveItem);
/*        	$userId = $user['id'];
        	$isRegisteredViaMail = 1;
        	$responseData['passwordHash'] = $user['password'];*/
                $this->Response->addError(1003);
                $this->Response->send();
            }
            else {
        	$data = array(
            	    'fb_id' => $this->Request->getData('social_id'), 
            	    'email' => $this->Request->getData('email'), 
            	    'password' => null,
            	    'is_confirm' => 1, 
            	    'confirmation_code'  => null,
            	    'locale_id' => $mLocale->getLocaleByStr($this->Request->getData('locale')), 
        	);
        	$userId = $this->Resource->setTable('users')->save($data);
        	$user = $mUser->getUserById($userId);
        	$responseData['authid'] = $mUser->createSession($userId);
    		$responseData['city'] = $user['cityName'];
        	$responseData['country'] = $user['countryName'];
//        	$responseData['isRegisteredViaMail'] = $isRegisteredViaMail;
        	$this->Response->setData($responseData);
            }
        }
    }
    
    public function getchildsAction(){
        $this->_authorize();
        $mChild = new Model_Child;
        $childs = $mChild->getChilds();
        $this->Response->setData('childs',$mChild->prepareData($childs));
    }
    
    public function addchildAction(){
        $this->_authorize();
        if(!$this->Request->getData('name')){
            $this->Response->addError(1008);
            $this->Response->send();
        }
        $mChild = new Model_Child;
        if(count($mChild->getChildByName($this->Request->getData('name')))){
            $this->Response->addError(1009);
            $this->Response->send();
        }
        $childId = $mChild->updateChild(null,$this->Request->getData());
        $this->Response->setData('childid',$childId);
    }
    
/*    public function deletechildAction(){
        $this->_authorize();
        if(!$this->Request->getData('name')){
            $this->Response->addError(1008);
            $this->Response->send();
        }
        $mChild = new Model_Child;
        if(!$mChild->deleteChild($this->Request->getData('name'))){
            $this->Response->addError(1010);
        }
    }*/

    public function deletechildAction(){
        $this->_authorize();
        if(!$this->Request->getData('name')){
            $this->Response->addError(1008);
            $this->Response->send();
        }
        $mChild = new Model_Child;
        $confirmation_code = $mChild->confirmDeleteChild($this->Request->getData('name'));
        
        $mMail = new Model_Mail;
        $mMail->sendChildDeleteConfirmation($this->Auth->getData('id'), $this->Request->getData('name'), $confirmation_code, $this->Auth->getData('locale_id'));
    }
    
    public function setactivechildAction(){
        $this->_authorize();
        if(!$this->Request->getData('name') && !$this->Request->getData('id')){
            $this->Response->addError(1008);
            $this->Response->send();
        }
        $mChild = new Model_Child;
        if(!$mChild->setActiveChild(!$this->Request->getData('id') ? 0 : $this->Request->getData('id'), $this->Request->getData('name'))){
            $this->Response->addError(1010);
        }else {
    	    $this->load->helper('locamath');
    	    $modFile = 0;
	    dir_walk("updateFileModificationTime", "resources/", array('txt'), true, "resources/", &$modFile);
	    $this->Response->setData('lastjsonmodtime', $modFile);
	    $this->Response->setData('childs', $mChild->prepareData(array(!$this->Request->getData('id') ? $mChild->getChildByName($this->Request->getData('name')) : $mChild->getChildById($this->Request->getData('id')))));
        }
    }
    
    public function getactivechildAction(){
        $this->_authorize();
        $mChild = new Model_Child;
        $this->Response->setData('child',$mChild->getActiveChild());
    }
    
    public function setchilddetailsAction(){
        $this->_authorize();
        $mChild = new Model_Child;
        if(!$this->Auth->getData('active_child_id')){
            $this->Response->addError(1011);
            $this->Response->send();
        }
        // if child with active_child_id exists
        if(count($mChild->getChildById($this->Auth->getData('active_child_id')))){
    	    $mChild->updateChild($this->Auth->getData('active_child_id'),$this->Request->getData());
    	}
    	else{
            $this->Response->addError(1010);
            $this->Response->send();
    	}
    }
    
    public function getlevelsAction(){
        $this->_authorize();
        if(!$this->Auth->getData('active_child_id')){
            $this->Response->addError(1011);
            $this->Response->send();
        }
        $mChild = new Model_Level;
        $data = $mChild->getChildLevels($this->Auth->getData('active_child_id'));
        $this->Response->setData('levels',$data);
    }
    
    public function updatelevelsAction(){
        $this->_authorize();
        if(!$this->Auth->getData('active_child_id')){
            $this->Response->addError(1011);
            $this->Response->send();
        }
        if(!$this->Request->getData('data')){
            $this->Response->addError(1012);
            $this->Response->send();
        }
        $mChild = new Model_Level;
        $mChild->updateChildLevels($this->Auth->getData('active_child_id'),$this->Request->getData('data'));
    }

    public function updatelevelsdebugAction(){
        if(!$this->Request->getData('data')){
            $this->Response->addError(1012);
            $this->Response->send();
        }
        $mChild = new Model_Level;
        $mChild->updateChildLevels(270,$this->Request->getData('data'));
    }
    
    public function getolymplevelsAction(){
        $this->_authorize();
        if(!$this->Auth->getData('active_child_id')){
            $this->Response->addError(1011);
            $this->Response->send();
        }
        $mChild = new Model_Level;
        $data = $mChild->getChildOlympLevels($this->Auth->getData('active_child_id'));
        $this->Response->setData('olymplevels',$data);
    }
    
    public function updateolymplevelsAction(){
        $this->_authorize();
        if(!$this->Auth->getData('active_child_id')){
            $this->Response->addError(1011);
            $this->Response->send();
        }
        if(!$this->Request->getData('data')){
            $this->Response->addError(1012);
            $this->Response->send();
        }
        $mChild = new Model_Level;
        $mChild->updateChildOlympLevels($this->Auth->getData('active_child_id'),$this->Request->getData('data'));
    }
    
    public function getrateAction(){
        $this->_authorize();
        $mRate = new Model_Rate;
        $this->Response->setData('rate',$mRate->getRateByAllChilds());
    }

    public function recordtimeinappAction(){
        $this->_authorize();
        if(!$this->Request->getData('data')){
            $this->Response->addError(1020);
            $this->Response->send();
        }

        $mChild = new Model_Child;
        $mChild->recordTimeInApp($this->Request->getData('data'));
    }

    public function retrievejsonAction(){
	// object: level | task | scheme | help
        if(!$this->Request->getData('object') || !$this->Request->getData('level')){
            $this->Response->addError(1026);
            $this->Response->send();
        }

        $actionLocale = $this->Request->getData('locale') ? $this->Request->getData('locale') : "en";
        
        $olympiad = $this->Request->getData('olympiad') ? TRUE : FALSE;

    	$jsonData = $this->_getLevelJSON($this->Request->getData('object'), $this->Request->getData('level'), $olympiad, $actionLocale);
    	if($jsonData){
    	    $this->Response->setData('json', json_decode($jsonData));
    	}else{
    	    if(!$this->Response->isHaveError()) {
    		$this->Response->addError(1027);
    	    }
    	}
    }
    
    public function _getLevelJSON($object, $level, $olympiad, $locale){
	$jsonFileData = FALSE;
	$dataFileName = FALSE;

	switch($object) {
	    case 'level':
		$dataFileName = $olympiad ? "resources/Level_".$level."/Levels/".$locale.".lproj/OlympiadLevels.txt":
					    "resources/Level_".$level."/Levels/".$locale.".lproj/Level_".$level.".txt";
		break;
	    
	    case 'task':
		switch($level) {
		    case 1:
			$dataFileName = $olympiad ? "resources/Level_".$level."/Tasks/".$locale.".lproj/olympiad.txt":
						    "resources/Level_".$level."/Tasks/".$locale.".lproj/".$level."st240.txt";
			break;
		    case 2:
			$dataFileName = $olympiad ? "resources/Level_".$level."/Tasks/".$locale.".lproj/olympiad.txt":
						    "resources/Level_".$level."/Tasks/".$locale.".lproj/".$level."nd275rus.txt";
			break;
		    case 3:
			$dataFileName = $olympiad ? "resources/Level_".$level."/Tasks/".$locale.".lproj/olympiad.txt":
						    "resources/Level_".$level."/Tasks/".$locale.".lproj/".$level.".txt";
			break;
		    case 4:
			$dataFileName = $olympiad ? "resources/Level_".$level."/Tasks/".$locale.".lproj/olympiad.txt":
						    "resources/Level_".$level."/Tasks/".$locale.".lproj/".$level.".txt";
			break;
		    default:
			$this->Response->addError(1029);
		}
		break;

	    case 'scheme':
		$dataFileName = "resources/Level_".$level."/Tasks/training_schemes.txt";
		break;
	
	    case 'help':
		$dataFileName = array();
		$dataFileName[] = "resources/Level_".$level."/Help/".$locale.".lproj/Help_1.txt";
		$dataFileName[] = "resources/Level_".$level."/Help/".$locale.".lproj/Help_2.txt";
		$dataFileName[] = "resources/Level_".$level."/Help/".$locale.".lproj/Help_3.txt";
		$dataFileName[] = "resources/Level_".$level."/Help/".$locale.".lproj/Help_4.txt";
		break;
	    
	    default:
		$this->Response->addError(1028);
	}

	if($dataFileName !== FALSE) {
	    if(!is_array($dataFileName)) $dataFileName = array($dataFileName);

	    foreach($dataFileName as $item){
		if($jsonFileData) $jsonFileData .= ",";
		$jsonFileData .= rtrim(trim(trim(trim(file_get_contents($item))), "{"), "}");
	    }
	    
	    if($jsonFileData) $jsonFileData = "{".$jsonFileData."}";
	}
//	$this->Response->setData('flatdata', $jsonFileData);
	return $jsonFileData;
    }

    public function updatetrainingschemeAction() {
        if(!$this->Request->getData('level')){
            $this->Response->addError(1030);
            $this->Response->send();
        }
        if(!$this->Request->getData('data')){
            $this->Response->addError(1012);
            $this->Response->send();
        }

        $data = json_decode($this->Request->getData('data'),true);

	// retrieve current json object for training scheme
        $jsonData = $this->_getLevelJSON("scheme", $this->Request->getData('level'), FALSE, "en");
        $schemeData = json_decode($jsonData);

        if($data && is_array($data)){
    	    // copy only necessary elements - if passed smth else it's just ignored
    	    $dataArray = array();
	    $dataArray['task_id'] = $data['task_id'];
	    $dataArray['elements'] = $data['elements'];

	    $processed = false;
    	    foreach($schemeData->training_schemes as $key => $taskScheme){
//    		echo $taskScheme->task_id  . "  " . $dataArray['task_id'] . "\n";
    		if($taskScheme->task_id == $dataArray['task_id']){
    		    // replace with the new data
		    $schemeData->training_schemes[$key] = json_decode(json_encode($dataArray), FALSE);
    		    $processed = true;
    		    break;
    		}
	    }
	    if(!$processed){
		$schemeData->training_schemes[] = json_decode(json_encode($dataArray), FALSE);
    	    }
    	}
//   		    unset($schemeData->training_schemes[$key]);  // for delete command

	// dump into file
	$fp = fopen("resources/Level_".$this->Request->getData('level')."/Tasks/training_schemes.txt", 'w');
	fwrite($fp, json_encode($schemeData));
	fclose($fp);
    }

    function __destruct() {
	if(!empty($this->db->queries) && count($this->db->queries) > 0) {
	    $times = $this->db->query_times;
	    foreach ($this->db->queries as $key => $query) {
		log_message('debug', "Query Time: " . $times[$key] . " | Query: " . $query);
	    }
	}
    }
}
