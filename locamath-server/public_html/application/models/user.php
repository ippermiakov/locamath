<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_User extends API_Model {

    public function getUsers($conditions = array(),$oneRow = false){
        $this->db->from("users")
            ->select('
                users.*,
                location_cities.name AS cityName,
                location_countries.name AS countryName
            ')
            ->join('location_cities','location_cities.id = users.city_id','left')
            ->join('location_countries','location_countries.id = location_cities.country_id','left');
        if(count($conditions)){
            foreach($conditions as $key => $item){
                if(is_numeric($key)){
                    $this->db->where("users.".$item);
                }else{
                    $this->db->where("users.".$key,$item);
                }
            }
        }
        if($oneRow){
            $this->db->limit(1);
            return $this->db->get()->row_array();
        }else{
            return $this->db->get()->result_array();
        }
    }
    
    public function getUser($conditions = array()){
        return $this->getUsers($conditions,true);
    }
    
    public function getUserByEmail($email = ''){
        return $this->getUser(array('email' => $email));
    }
    
    public function getUserByFbId($id = ''){
        return $this->getUser(array('fb_id' => $id));
    }

    public function getUserById($id = ''){
        return $this->getUser(array('id' => $id));
    }
    
    public function getNewAccessToken(){
        $this->load->library('encrypt');
        return base64_encode($this->encrypt->encode(time()));
    }
    
    public function getUserByAuthId($authid = ''){
        if($authid){
            $user = $this->db->select('u.*,s.id as session_id,s.child_id as active_child_id,s.authid,s.modified as loggedin')->from('sessions AS s')
                ->join('users as u','u.id = s.user_id','left')
                ->where('s.authid',$authid)->get()->row_array();
            return (isset($user['id']) && $user['id'] ? $user : array());
        }
        return array();
    }
    
    public function createSession($userId = 0){
        if(!$userId) return null;
        $this->db->where("DATE_SUB('".date('Y-m-d H:i:s')."', INTERVAL 2 YEAR) > `modified`")->delete('sessions');
        $at = $this->getNewAccessToken();
        $this->Resource->setTable('sessions')->save(array(
            'user_id' => $userId,
            'authid' => $at,
        ));
        return $at;
    }
    
}   
