<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_Location extends API_Model {
    
    public function getLocation($country = null,$city = null,$locale_id = 1){
        $data = new Model_Object(array(
                'country' => '',
                'city' => '',
                'country_id' => 0,
                'city_id' => 0,
        ));
        if($country || $city){
            $loc = array($country,$city);
            $this->load->library('curl');
            $this->curl->setUrl('http://maps.googleapis.com/maps/api/geocode/json?address='.implode(',',$loc).'&sensor=false');
            $this->curl->send();
            $body = json_decode($this->curl->getResponse()->body,true);
            
            $results = isset($body['results']) && is_array($body['results']) ? $body['results'] : array();
            $country = null;
            $city = null;
            foreach($results as $item1){
                if($country && $city){
                    break;
                }
                if(isset($item1['address_components']) && is_array($item1['address_components'])){
                    foreach($item1['address_components'] as $item2){
                        if(isset($item2['types']) && is_array($item2['types'])){
                            if(in_array('locality',$item2['types'])){
                                $city = $item2['long_name'];
                            }
                            if(in_array('country',$item2['types'])){
                                $country = $item2['long_name'];
                            }
                        }
                    }
                }
            }
            $country_id = 0;
            $city_id = 0;
            //$this->db->db_debug = false;
            if($country){
                $row1 = $this->db->from('location_countries')->where('name',$country)->get()->row_array();
                if(count($row1)){
                    $country_id = $row1['id'];
                    if($city){
                        $row2 = $this->db->from('location_cities')->where('country_id',$country_id)->where('name',$city)->get()->row_array();
                        if(count($row2)){
                            $city_id = $row2['id'];
                        }else{
                            $this->db->insert('location_cities',array(
                                'country_id' => $country_id,
                                'name' => $city,
                            ));
                            $city_id = $this->db->insert_id();
                        }
                    }
                }else{
                    $this->db->insert('location_countries',array(
                        'name' => $country
                    ));
                    $country_id = $this->db->insert_id();
                    if($city){
                        $this->db->insert('location_cities',array(
                            'country_id' => $country_id,
                            'name' => $city,
                        ));
                        $city_id = $this->db->insert_id();
                    }
                }
            }
            $data->setData(array(
                'country' => $country,
                'city' => $city,
                'country_id' => (int)$country_id,
                'city_id' => (int)$city_id,
            ));
        }
        return $data;
    }
    
}   
