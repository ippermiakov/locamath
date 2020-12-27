<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_Mail extends API_Model {
    const STAT_DAILY   = "daily";
    const STAT_WEEKLY  = "weekly";
    const STAT_MONTHLY = "monthly";
    const STAT_OLYMP   = "olympic";

    protected $_templates = null;
    protected $_frequencies = null;
    
    public function getTemplateByCode($code,$locale_id = 1){
        $templates = $this->getTemplates();
        if(isset($templates[$code])){
            $template = $templates[$code];

            return isset($template[$locale_id]) ? $template[$locale_id] : $template[1];
        }
        return null;
    }
    
    public function getTemplates(){
        if(!$this->_templates){
            $this->_templates = array();
            $sql = "SELECT 
                        `l`.`id`,
                        `et`.`template`,
                        `et`.`from_name`,
                        `et`.`from`,
                        `etl`.`subject`,
                        `etl`.`body`
                      FROM `".getTable('email_templates')."` AS `et`
                 LEFT JOIN `".getTable('email_templates_locale')."` AS `etl`
                        ON `etl`.`email_template_id` = `et`.`id`
                 LEFT JOIN `".getTable('locales')."` AS `l`
                        ON `l`.`id` = `etl`.`locale_id`";
            $result = $this->db->query($sql)->result_array();
            foreach($result as $item){
                $this->_templates[$item['template']][$item['id']] = array(
                    'from_name' => $item['from_name'],
                    'from' => $item['from'],
                    'subject' => $item['subject'],
                    'body' => $item['body'],
                );
            }
        }
        return $this->_templates;
    }
    
    public function sendConfirmation($userId = 0, $userPassword = '', $locale_id = 1){
	// if password if passed - confirmation during regisrtaion, otherwise it's reminder notificaiton
        $tpl = ($userPassword) ? $this->getTemplateByCode('confirm', $locale_id) : $this->getTemplateByCode('confirm_reminder', $locale_id);
        $mUser = new Model_User;
        $user = $mUser->getUser(array('id'=>$userId));
        if(count($user) && count($tpl)){
    	    if($userPassword) $user['password'] = $userPassword;
            $tpl['body'] = $this->usePlaceholders($tpl['body'],$user);
            $this->load->library('email');
            $this->email->clear(TRUE);
            $this->email->from($tpl['from'],$tpl['from_name']);
            $this->email->to($user['email']);
            $this->email->subject($tpl['subject']);
            $this->email->message($tpl['body']);
            $this->email->set_mailtype('html');
            $this->email->set_newline("\r\n");
            $this->email->set_crlf("\r\n");
            return $this->email->send();
        }
    }

    public function sendPasswordChange($userId = 0, $userPassword, $locale_id = 1){
        $tpl = $this->getTemplateByCode('changepassword', $locale_id);
        $mUser = new Model_User;
        $user = $mUser->getUser(array('id'=>$userId));
        if(count($user) && count($tpl)){
    	    if($userPassword) $user['password'] = $userPassword;
            $tpl['body'] = $this->usePlaceholders($tpl['body'],$user);
            $this->load->library('email');
            $this->email->clear(TRUE);
            $this->email->from($tpl['from'],$tpl['from_name']);
            $this->email->to($user['email']);
            $this->email->subject($tpl['subject']);
            $this->email->message($tpl['body']);
            $this->email->set_mailtype('html');
            $this->email->set_newline("\r\n");
            $this->email->set_crlf("\r\n");
            return $this->email->send();
        }
    }

    public function sendChildDeleteConfirmation($userId = 0, $child_name, $confirmation_child_delete_code, $locale_id = 1){
        $tpl = $this->getTemplateByCode('confirm_child_delete', $locale_id);
        $mUser = new Model_User;
        $user = $mUser->getUser(array('id'=>$userId));
        $user['child_name'] = $child_name;
        $user['confirmation_child_delete_code'] = $confirmation_child_delete_code;
        if(count($user) && count($tpl)){
            $tpl['body'] = $this->usePlaceholders($tpl['body'],$user);
            $this->load->library('email');
            $this->email->clear(TRUE);
            $this->email->from($tpl['from'],$tpl['from_name']);
            $this->email->to($user['email']);
            $this->email->subject($tpl['subject']);
            $this->email->message($tpl['body']);
            $this->email->set_mailtype('html');
            $this->email->set_newline("\r\n");
            $this->email->set_crlf("\r\n");
            return $this->email->send();
        }
    }

    public function sendStatistics($userId = 0, $childAddresses, $stats, $locale_id = 1) {
        $tpl = $this->getTemplateByCode('statistics_html', $locale_id);
        $mUser = new Model_User;
        $user = $mUser->getUser(array('id'=>$userId));
        $user['child_name'] = $child_name;

        if(count($user) && count($tpl)){
    	    $tplType = $this->getTemplateByCode('statistics_type_'.$stats['reportType'], $locale_id);
    	    $stats['report_type'] = $tplType['body'];
            $tpl['body'] = $this->usePlaceholders($tpl['body'],$stats);
            echo "<pre>".$tpl['body']."</pre>";
            $this->load->library('email');
            foreach($childAddresses as $addr) {
        	if($addr->name) {
        	    $this->email->clear(TRUE);
        	    $this->email->from($tpl['from'],$tpl['from_name']);
        	    $this->email->subject($tpl['subject']);
        	    $this->email->message($tpl['body']);
        	    $this->email->set_mailtype('html');
        	    $this->email->set_newline("\r\n");
        	    $this->email->set_crlf("\r\n");
        	    $this->email->to($addr->name);
//        	    if($user['id'] == 157) $this->email->send();
//        	    $this->email->send();
		}
	    }
	}
    }

    public function usePlaceholders($data = '',$userData = array(),$userId = 0){
        if(!count($userData)){
            if(!$userId){
                $userId = $this->getUser('id');
            }
            $userData = $this->db->from('users')->where('id',$userId)->get()->row_array();
        }
        $pSearch = array(
            '{user_email}',
            '{user_password}',
            '{confirmation_link}',
            '{confirmation_child_delete_link}',
            '{child_name}',
            '{avatar}',
            '{report_type}',
            '{task_speed}',
            '{task_difficult_id}',
            '{task_difficult_path}',
            '{task_difficult_path_name}',
            '{task_difficult_number}',
            '{task_difficult_name}',
            '{task_easy_id}',
            '{task_easy_path}',
            '{task_easy_path_name}',
            '{task_easy_number}',
            '{task_easy_name}',
            '{task_solved}',
            '{task_test_solved}',
            '{task_olympic_solved}',
            '{solution_action}',
            '{error_action}',
            '{solution_expression}',
            '{error_expression}',
            '{score}',
            '{total_time}'
        );
        $pReplace = array(
            $userData['email'],
            $userData['password'],
            $this->config->item('base_url').'confirm/?link='.base64_encode($userData['confirmation_code']),
            $this->config->item('base_url').'confirm/?link_child_delete='.base64_encode($userData['confirmation_child_delete_code']),
            $userData['child_name'],
            $userData['avatar'],
            $userData['report_type'],
            $userData['taskSpeed'],
            $userData['taskDifficultId'],
            $userData['taskDifficultPath'],
            $userData['taskDifficultPathName'],
            $userData['taskDifficultNumber'],
            $userData['taskDifficultName'],
            $userData['taskEasyId'],
            $userData['taskEasyPath'],
            $userData['taskEasyPathName'],
            $userData['taskEasyNumber'],
            $userData['taskEasyName'],
            $userData['taskSolved'],
            $userData['taskTestSolved'],
            $userData['taskOlympicSolved'],
            $userData['solutionAction'],
            $userData['errorAction'],
            $userData['solutionExpression'],
            $userData['errorExpression'],
            $userData['score'],
            $userData['totalTime']
        );
        return str_replace($pSearch,$pReplace,$data);
    }
    
}   
