<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class API_Loader extends CI_Loader {
    
    public function model($model, $name = '', $db_conn = FALSE)
    {
        if (is_array($model))
        {
            foreach ($model as $babe)
            {
                $this->model($babe);
            }
            return;
        }

        if ($model == '')
        {
            return;
        }

        if ($name == '')
        {
            $name = $model;
        }

        if (in_array($name, $this->_ci_models, TRUE))
        {
            return;
        }

        $CI =& get_instance();
        if (isset($CI->$name))
        {
            show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
        }

        if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
        {
            if ($db_conn === TRUE)
            {
                $db_conn = '';
            }

            $CI->load->database($db_conn, FALSE, TRUE);
        }

        $model = 'Model_'.$model;
        $CI->$name = new $model();

        $this->_ci_models[] = $name;
    }

}
