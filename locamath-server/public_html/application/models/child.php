<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_Child extends API_Model {
    
    public function getChilds(){
        return $this->db->select('a.*')->from('accounts as a')->where('a.user_id',$this->Auth->getData('id'))->get()->result_array();
    }

    public function getAllChilds(){
        return $this->db->select('a.*')->from('accounts as a')->get()->result_array();
    }

    public function getChildById($id){
        return $this->db->from('accounts')->where('id', $id)->get()->row_array();
    }
    
    public function getChildByName($name = ''){
        $name = strtolower($name);
        return $this->db->from('accounts')->where('user_id',$this->Auth->getData('id'))->where("LOWER(`name`) = '".mysql_real_escape_string($name)."'")->get()->row_array();
    }
    
    public function deleteChild($name = ''){
        $name = strtolower($name);
        $this->db->where('user_id',$this->Auth->getData('id'))->where("LOWER(`name`) = '".mysql_real_escape_string($name)."'")->delete('accounts');
        return $this->db->affected_rows();
    }

    public function confirmDeleteChild($name) {
        $name = strtolower($name);
        $confirmation_code = $this->getNewAccessToken();
        $this->db->where('user_id',$this->Auth->getData('id'))->where("LOWER(`name`) = '".mysql_real_escape_string($name)."'")->update('accounts', array('confirmation_code' => $confirmation_code));
        return $confirmation_code;
    }

    public function deleteChildByConfirmationCode($confirmation_code) {
	$result = 0;
        $child = $this->db->from('accounts')->where('confirmation_code',$confirmation_code)->get()->row_array();
        if(count($child)){
            $this->db->where('id',$child['id'])->delete('accounts');
            $result = $this->db->affected_rows();
        }
        return $result;
    }
    
    public function getGender($gender = 0){
        $gender = (int)$gender;
        return in_array($gender,array(1,2)) ? $gender : 1; 
    }
    
    public function setActiveChild($id = 0, $name = 0){
        $child = $id ? $this->getChildById($id) : $this->getChildByName($name);
        if(count($child)){
            $this->db->where('child_id',$child['id'])->update('sessions',array(
                'child_id' => 0,
            ));
            $this->db->where('id',$this->Auth->getData('session_id'))->update('sessions',array(
                'child_id' => $child['id'],
                'modified' => date('Y-m-d H:i:s'),
            ));
            return true;
        }
        return false;
    }
    
    public function getActiveChild(){
        $childs = $this->db->select('a.*')->from('accounts as a')->where('a.id',(int)$this->Auth->getData('active_child_id'))->get()->result_array();
        if(count($childs)){
            return array_shift($this->prepareData($childs));
        }
        return array();
    }
    
    public function updateChild($childId = null,$child = array()){
        if($childId !== null){
            $data['id'] = (int)$childId;
        }
        $data['user_id'] = $this->Auth->getData('id');
        if(isset($child['name'])){ 
            $data['name'] = $child['name'];
        }
        if(isset($child['gender'])){ 
            $data['sex'] = $this->getGender($child['gender']);
        }
/*        if(isset($child['latitude']) && $child['latitude'] && isset($child['longitude']) && $child['longitude']){ 
            $mLocation = new Model_Location;
            $loc = $mLocation->getLocation($child['latitude'], $child['longitude']);
            if($loc->getCountryId()){
                $data['country_id'] = $loc->getCountryId();
            }
            if($loc->getCityId()){ 
                $data['city_id'] = $loc->getCityId();
            }
        }
        if(isset($child['city'])){  
             $data['city'] = $child['city'];
        }
        if(isset($child['country'])){  
             $data['country'] = $child['country'];
        }*/
        if(isset($child['avatar'])){  
             $data['avatar'] = $child['avatar'];
        }
        if(isset($child['avatar64'])){  
             $data['avatar'] = $this->uploadBase64Image($child['avatar64']);
        }
        if(isset($child['isTrainingComplete'])){ 
            $data['is_training_complete'] = (int)(bool)$child['isTrainingComplete']; 
        }
        if(isset($child['isSoundsOn'])){ 
            $data['is_sounds_on'] = (int)(bool)$child['isSoundsOn'];
        }
        if(isset($child['isMusicOn'])){ 
            $data['is_music_on'] = (int)(bool)$child['isMusicOn'];
        }
        if(isset($child['PostStatisticsType'])){ 
            $data['post_statistics_type'] = (int)$child['PostStatisticsType'];
        }
        if(isset($child['SendStatisticsType'])){ 
            $data['send_statistics_type'] = (int)$child['SendStatisticsType'];
        }
        if(isset($child['postStatisticsAccounts'])){ 
            $data['post_statistics_accounts'] = (string)$child['postStatisticsAccounts'];
        }
        if(isset($child['sendStatisticsAccounts'])){ 
            $data['send_statistics_accounts'] = (string)$child['sendStatisticsAccounts'];
        }
        if(isset($child['points'])){ 
            $data['points'] = (float)$child['points'];
        }
        if($this->Response->isHaveErrors()){
            $this->Response->send();
        }
        $this->db->db_debug = false;
        return $this->Resource->setTable('accounts')->save($data);
    }
    
    public function uploadBase64Image($data = ''){
        $file = 'public/media/'.md5(time());
        $target = FCPATH.$file;
        $whandle = @fopen($target,'w');
        if($whandle){
            @stream_filter_append($whandle, 'convert.base64-decode',STREAM_FILTER_WRITE);
            @fwrite($whandle,$data);
            @fclose($whandle);
            return $file;
        }else{
            $this->Response->addError(1013);
        }
        return '';
    }
    
    public function prepareData($childs = array()){
        $result = array();
        foreach($childs as $item){
            $result[] = array(
                'id' => (int)$item['id'],
                'name' => (string)$item['name'],
                'gender' => (int)$item['sex'],
                'country' => (string)$item['country'],
                'city' => (string)$item['city'],
                'avatar' => (string)$item['avatar'],
                'isTrainingComplete' => (bool)$item['is_training_complete'],
                'isSoundsOn' => (bool)$item['is_sounds_on'],
                'isMusicOn' => (bool)$item['is_music_on'],
                'PostStatisticsType' => (int)$item['post_statistics_type'],
                'SendStatisticsType' => (int)$item['send_statistics_type'],
                'postStatisticsAccounts' => (array)json_decode($item['post_statistics_accounts'],true),
                'sendStatisticsAccounts' => (array)json_decode($item['send_statistics_accounts'],true),
                'points' => (float)$item['points'],
                'created' => (string)$item['created'],
            );
        }
        return $result;
    }

    public function getNewAccessToken(){
        $this->load->library('encrypt');
        return base64_encode($this->encrypt->encode(time()));
    }

    public function recordTimeInApp($data){
	if($data){
	    $data = (array)json_decode($data);
	    for($i = 0; $i < count($data); $i++){
		$item = $data[$i];
		$dateArray = explode("-", $item->date);
    		$dateData = $this->db->from('account_time')->where(array('child_id' => $item->user_id, 'day' => $dateArray[2], 'month' => $dateArray[1], 'year' => $dateArray[0]))->get()->row_array();
    		if(count($dateData)){
    		    $this->db->where(array('child_id' => $item->user_id, 'day' => $dateArray[2], 'month' => $dateArray[1], 'year' => $dateArray[0]))->update('account_time', array('time' => ($dateData['time']+$item->time)));
    		}else{
    		    $this->db->insert('account_time', array('child_id' => $item->user_id, 'day' => $dateArray[2], 'month' => $dateArray[1], 'year' => $dateArray[0], 'time' => $item->time));
    		}
	    }
        }else{
             $this->Response->addError(1020);
             $this->Response->send();
        }
    }
}   
