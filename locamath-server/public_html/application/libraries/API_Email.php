<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class API_Email extends CI_Email{
     
    protected $q_installTables = FALSE;      // check installed tables 
    protected $q_tables = array(             // Queue Tables 
        'update' => 'math_email_queue_update',
        'list' => 'math_email_queue_list',
    );
    protected $q_quantity = 20;              // Quantity emails for time 
    protected $q_tryToSend = 1;              // Try to send
    
    public function setFrequency($iF){
        if(!is_string($iF)){
            $iF = strtotime($iF);
        }
        $iF = (int)$iF;
        $ci =& get_instance();
        $ci->db->update($this->q_tables['update'],array('frequency' => $iF));
    }
    
    public function addToQueue($options = array()){
        $queue = array(
            'from_name' => (isset($options['from_name']) ? $options['from_name'] : ''),
            'from' => (isset($options['from']) ? $options['from'] : ''),
            'to' => (isset($options['to']) ? $options['to'] : ''),
            'subject' => (isset($options['subject']) ? $options['subject'] : ''),
            'body' => (isset($options['body']) ? $options['body'] : ''),
            'created' => date('Y-m-d H:i:s'),
        );
        $ci =& get_instance();
        $ci->db->insert($this->q_tables['list'],$queue);
        return true;
    }
    
    public function runJobQueue(){
        $this->_q_installTables();
        $ci =& get_instance();
        /* Check Available */
        $time = time();
        $sql = "UPDATE `".$this->q_tables['update']."` 
                   SET `last_update` = '".$time."' 
                 WHERE (".$time." > (`frequency` + `last_update`))";
        $ci->db->query($sql);
        if(!$ci->db->affected_rows()){
            return false;
        }
        /* Email Job */
        $sql = "SELECT *
                  FROM `".$this->q_tables['list']."`
                 WHERE `senttime` IS NULL 
                   AND `created` < '".date('Y-m-d H:i:s')."' 
                   AND `try_send` < ".$this->q_tryToSend."
              ORDER BY `created` ASC
                 LIMIT 0,".$this->q_quantity;
        $emails = $ci->db->query($sql)->result_array();
        if(count($emails)){
            $results = array(
                'success' => array(),
                'error' => array(),
            );
            foreach($emails as $email){
                if($this->_q_sendEmail($email)){
                    $resultType = 'success';
                }else{
                    $resultType = 'error';
                }
                $results[$resultType][] = $email['id'];
            }
            $this->clear(TRUE);
            if(count($results['success'])){
                $sql = "UPDATE `".$this->q_tables['list']."` 
                           SET `senttime` = '".date('Y-m-d H:i:s')."',
                               `try_send` = `try_send` + 1
                         WHERE `id` IN(".implode(",",$results['success']).")";
                $ci->db->query($sql);
            }
            if(count($results['error'])){
                $sql = "UPDATE `".$this->q_tables['list']."` 
                           SET `try_send` = `try_send` + 1
                         WHERE `id` IN(".implode(",",$results['error']).")";
                $ci->db->query($sql);
            }
        }
    }
    
    protected function _q_sendEmail($email = array()){
        $this->clear(TRUE);
        $this->from($email['from'],$email['from_name']);
        $this->to($email['to']);
        $this->subject($email['subject']);
        $this->message($email['body']);
        $this->set_mailtype('html');
        $this->set_newline("\r\n");
        $this->set_crlf("\r\n");
        return $this->send();
    }
    
    protected function _q_installTables(){
        if(!$this->q_installTables) return false;
        $sqls = array(
        "CREATE TABLE IF NOT EXISTS `".$this->q_tables['update']."` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `frequency` varchar(50) NOT NULL,
          `last_update` varchar(50) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `idx_id` (`id`),
          KEY `idx_frequency` (`frequency`),
          KEY `idx_update` (`last_update`)
        )",
        "INSERT IGNORE INTO `".$this->q_tables['update']."`(`id`,`frequency`,`last_update`) VALUES (1,'60','0')",
        "CREATE TABLE IF NOT EXISTS `".$this->q_tables['list']."` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `from_name` varchar(255) DEFAULT NULL,
          `from` varchar(255) NOT NULL,
          `to` varchar(255) NOT NULL,
          `subject` varchar(255) NOT NULL,
          `body` text NOT NULL,
          `senttime` datetime DEFAULT NULL,
          `try_send` int(11) NOT NULL DEFAULT '0',
          `created` datetime NOT NULL,
          PRIMARY KEY (`id`),
          KEY `idx_id` (`id`),
          KEY `idx_senttime` (`senttime`),
          KEY `idx_try_send` (`try_send`),
          KEY `idx_created` (`created`),
          KEY `idx_sent_try_date` (`senttime`,`try_send`,`created`)
        )",
        );
        $ci =& get_instance();
        foreach($sqls as $sql){
            $ci->db->query($sql);
        }
        return true;
    }

}
