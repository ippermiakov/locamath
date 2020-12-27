<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|   http://codeigniter.com/user_guide/general/hooks.html
|
*/


// This is required to load the Exceptions library early enough
$hook['pre_system'] = array(
    0 => array(         
        'function' => 'load_pre_system',
        'filename' => 'pre_system.php',
        'filepath' => 'hooks'
    ),
);

$hook['pre_controller'] = array(
    0 => array(         
        'function' => 'load_pre_controller',
        'filename' => 'pre_controller.php',
        'filepath' => 'hooks'
    ),
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */
