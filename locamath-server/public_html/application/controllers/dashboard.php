<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends API_Controller {

    public function indexAction()
    {
//	$this->output->enable_profiler(TRUE);

//        $mChild = new Model_Level;
//        $data = $mChild->getChildLevels(208);

        $this->load->view('dashboard');
    }
}
