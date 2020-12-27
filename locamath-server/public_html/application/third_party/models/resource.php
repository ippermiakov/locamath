<?php	

class Model_Resource extends Model_Object{
    
    protected $table = null;
    
    protected static $_fields = array();
    
    public function __construct($table = null){
        if($table){
            $this->table = $table;
        }
    } 
    
    public function setTable($table){
        $this->table = $table;
        return $this;
    }
    
    public function getFields($table){
        if(!isset(self::$_fields[$table])){
            self::$_fields[$table] = array_flip($this->getCI()->db->list_fields($table));
        }
        return self::$_fields[$table];
    }
    
    public function save($data = null){
        $table = $this->table;
        if($data === null){
            $data = $this->getData();
        }
        $fields = $this->getFields($table);
        $data = array_intersect_key($data,$fields);
        if(isset($data['id']) && $data['id']){
            if(isset($fields['modified'])){
                $data['modified'] = date('Y-m-d H:i:s');
            }
            $this->getCI()->db->where('id',$data['id'])->update($table,$data);
            return $data['id'];
        }else{
            if(isset($fields['created'])){
                $data['created'] = date('Y-m-d H:i:s');
            }
            if(isset($fields['modified'])){
                $data['modified'] = date('Y-m-d H:i:s');
            }
            $this->getCI()->db->insert($table,$data);
            return $this->getCI()->db->insert_id();
        }
    }
    
}
