<?php	

class Model_Request extends Model_Object{
    
    public function __construct(){
        $get = (array)$this->getCI()->input->get();
        $post = (array)$this->getCI()->input->post();
        parent::__construct(array_merge($get,$post));
    }
}
