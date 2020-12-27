<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_Rate extends API_Model {

    public function getRateByAllChilds(){
        $rows = $this->db->from('accounts AS a')
            ->select('
                a.id,
                a.name,
                a.points,
                lcn.name AS country,
                lc.name AS city
            ')
            ->join('users AS u','u.id = a.user_id','left')
            ->join('location_cities AS lc','lc.id = u.city_id','left')
            ->join('location_countries AS lcn','lcn.id = lc.country_id','left')
            ->order_by('points DESC')
            ->get()->result_array();
        $result = array();
        foreach($rows as $row){
            $result[] = array(
                'id' => (int)$row['id'],
                'name' => (string)$row['name'],
                'city' => (string)$row['city'],
                'country' => (string)$row['country'],
                'points' => (float)$row['points'],
            );
        }
        return $result;
    }
   
}  
