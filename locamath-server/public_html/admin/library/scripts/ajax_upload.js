function attachFileUploader(options) {

  var selectorId     = options.selectorId;
  var containerId    = options.containerId;
  var extensions     = options.extensions;
  var sessionId      = options.sessionId?options.sessionId:'fum_session';
  var thumbWidth     = options.thumbWidth?options.thumbWidth:200;
  var thumbHeight    = options.thumbHeight?options.thumbHeight:80;
  var imageCheck     = options.imageCheck?1:0;
  var renderUploaded = options.renderUploaded==undefined?1:(options.renderUploaded?1:0);
  var imagesLimit    = options.imagesLimit?options.imagesLimit:0;
  var hideLegend     = options.hideLegend?1:0;

  $(document).ready(function() {

    if (renderUploaded) {
      $.get('?__fumMethod=render&sessionId=' + sessionId +
                               '&hideLegend=' + hideLegend, function(response) {
        if (response && /^</.test(response)) {  
          if (imagesLimit > 0) {
            $('#' + containerId).html(response);
          } else {
            $('#' + containerId).append(response);
          }
        }
      });
    }

    new Ajax_upload('#' + selectorId, {
        action: '?__fumMethod=upload&selectorId='  + selectorId + 
                                   '&sessionId='   + sessionId + 
                                   '&width='       + thumbWidth + 
                                   '&height='      + thumbHeight + 
                                   '&imagesLimit=' + imagesLimit + 
                                   '&hideLegend='  + hideLegend +
                                   '&imageCheck='  + imageCheck
      , name: selectorId
      , onSubmit: function(file, ext) {
          if (imageCheck && !(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
            alert('Please select image file');
            return false;
          }
          if (imagesLimit > 0) {
            $('#' + containerId).html('<div id="fum_upload_indicator" class="fum_upload_indicator">Uploading ' + file + '...</div>');
          } else {
            $('#' + containerId).append('<div id="fum_upload_indicator" class="fum_upload_indicator">Uploading ' + file + '...</div>');
          } 
        }
      , onComplete: function(file, response) {
          $('#fum_upload_indicator').remove();
          if (response && /^</.test(response)) {  
            if (imagesLimit > 0) {
              $('#' + containerId).html(response);
            } else {
              $('#' + containerId).append(response);
            } 
	  } else {
            alert(response);
          }
        }
    });
  });

}
