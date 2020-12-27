function fileQueueError(file, errorCode, message, mess_for_user) {
  try {
    var imageName = "error.gif";
    var errorName = "";
    if (errorCode === SWFUpload.errorCode_QUEUE_LIMIT_EXCEEDED) {
      errorName = "You have attempted to queue too many files.";
    }

    if (errorName !== "") {
      alert(errorName);
      return;
    }

    var progress = new FileProgress(file,  this.customSettings.upload_target);
    progress.setError();
    progress.toggleCancel(false);

    switch (errorCode) {
    case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
      progress.setStatus(mess_for_user ? mess_for_user : "File is too big.");
      this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
      break;
    case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
      progress.setStatus("Cannot upload Zero Byte files.");
      this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
      break;
    case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
      progress.setStatus("Invalid File Type.");
      this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
      break;
    default:
      if (file !== null) {
        progress.setStatus("Unhandled Error");
      }
      this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
      break;
    }

  } catch (ex) {
    this.debug(ex);
  }

}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
  try {
    if (numFilesQueued > 0) {
      this.startUpload();
    }
  } catch (ex) {
    this.debug(ex);
  }
}

function uploadProgress(file, bytesLoaded) {
  try {
    var percent = Math.ceil((bytesLoaded / file.size) * 100);

    var progress = new FileProgress(file,  this.customSettings.upload_target);
    progress.setProgress(percent);
    if (percent === 100) {
      if (this.customSettings.response) {
        progress.setStatus("Creating thumbnail...");
      }
      progress.toggleCancel(false, this);
    } else {
      progress.setStatus("Uploading (" + percent + '%)');
      progress.toggleCancel(true, this);
    }
  } catch (ex) {
    this.debug(ex);
  }
}

function uploadSuccess(file, serverData) {
  try {
    var callBaseUrl = document.location.href;
    var callTemp = callBaseUrl.lastIndexOf("/") + 1;
    callBaseUrl = callBaseUrl.substring(0, callTemp);

    var progress = new FileProgress(file,  this.customSettings.upload_target);
    if (serverData.substring(0, 7) === "FILEID:") {
      if (this.customSettings.response){
        requestStr = callBaseUrl + "?__upmMethod=thumbnail&id=" + serverData.replace(/FILEID:/, '') + '&width=' + this.customSettings.response.imageWidth + '&height=' + this.customSettings.response.imageHeight;
        addImage(
                  requestStr, 
                  this.customSettings.response.imagePlaceholder, 
                  this.customSettings.response.imageWidth,
                  this.customSettings.response.imageHeight
                );
      }

      progress.setStatus("Complete.");

      var input = document.getElementById(this.inputName);
      if (!input) {
        input = document.createElement("input");
        input.type = 'hidden';
        input.name = this.inputName;
        forms = document.getElementsByTagName('form');
        form = forms.item(0);
        form.appendChild(input);
      }
      input.value = serverData.replace(/FILEID:/, '');

      progress.toggleCancel(false);
    } else {
      progress.setStatus("Error: " + serverData);
      progress.setError();
      progress.toggleCancel(false);
      alert(serverData);
    }

  } catch (ex) {
    this.debug(ex);
  }
}

function uploadComplete(file) {
  try {
    /*  I want the next upload to continue automatically so I'll call startUpload here */
    if (this.getStats().files_queued > 0) {
      this.startUpload();
    } else {
      var progress = new FileProgress(file,  this.customSettings.upload_target);
      //progress.setComplete();
      //alert(1);
      progress.setStatus("Image(s) received. <input type='hidden' id='" + this.inputName + "' name='" + this.inputName + "' value='" + serverData + "' />");
      progress.toggleCancel(false);
    }
  } catch (ex) {
    this.debug(ex);
  }
}

function uploadError(file, errorCode, message) {
  var imageName =  "error.gif";
  var progress;
  try {
    switch (errorCode) {
    case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
      try {
        progress = new FileProgress(file,  this.customSettings.upload_target);
        progress.setCancelled();
        progress.setStatus("Cancelled");
        progress.toggleCancel(false);
      }
      catch (ex1) {
        this.debug(ex1);
      }
      break;
    case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
      try {
        progress = new FileProgress(file,  this.customSettings.upload_target);
        progress.setCancelled();
        progress.setStatus("Stopped");
        progress.toggleCancel(true);
      }
      catch (ex2) {
        this.debug(ex2);
      }
    case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
      imageName = "uploadlimit.gif";
      break;
    default:
      alert(message);
      break;
    }

    addImage("images/" + imageName);

  } catch (ex3) {
    this.debug(ex3);
  }

}


function addImage(src, target_container, width, height) {
  var newImg = document.createElement("img");
  newImg.style.width = width + 'px';
  newImg.style.height = height + 'px';

  document.getElementById(target_container).innerHTML = '';
  document.getElementById(target_container).appendChild(newImg);
  if (newImg.filters) {
    try {
      newImg.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 0;
    } catch (e) {
      // If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
      newImg.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + 0 + ')';
    }
  } else {
    newImg.style.opacity = 0;
  }

  newImg.onload = function () {
    fadeIn(newImg, 0);
  };
  newImg.src = src;
}

function fadeIn(element, opacity) {
  var reduceOpacityBy = 5;
  var rate = 30;  // 15 fps


  if (opacity < 100) {
    opacity += reduceOpacityBy;
    if (opacity > 100) {
      opacity = 100;
    }

    if (element.filters) {
      try {
        element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
      } catch (e) {
        // If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
        element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
      }
    } else {
      element.style.opacity = opacity / 100;
    }
  }

  if (opacity < 100) {
    setTimeout(function () {
      fadeIn(element, opacity);
    }, rate);
  }
}



/* ******************************************
 *  FileProgress Object
 *  Control object for displaying file info
 * ****************************************** */

function FileProgress(file, targetID) {

  this.fileProgressID = "divFileProgress_" + targetID;

  this.fileProgressWrapper = document.getElementById(this.fileProgressID);
  if (!this.fileProgressWrapper) {
    this.fileProgressWrapper = document.createElement("div");
    this.fileProgressWrapper.className = "progressWrapper";
    this.fileProgressWrapper.id = this.fileProgressID;

    this.fileProgressElement = document.createElement("div");
    this.fileProgressElement.className = "progressContainer";

    var progressCancel = document.createElement("a");
    progressCancel.className = "progressCancel";
    progressCancel.href = "#";
    progressCancel.style.visibility = "hidden";
    progressCancel.appendChild(document.createTextNode(" "));

    var progressText = document.createElement("div");
    progressText.className = "progressName";
    progressText.appendChild(document.createTextNode(file.name));

    var progressBar = document.createElement("div");
    progressBar.className = "progressBarInProgress";

    var progressStatus = document.createElement("div");
    progressStatus.className = "progressBarStatus";
    progressStatus.innerHTML = "&nbsp;";

    this.fileProgressElement.appendChild(progressCancel);
    this.fileProgressElement.appendChild(progressText);
    this.fileProgressElement.appendChild(progressStatus);
    this.fileProgressElement.appendChild(progressBar);

    this.fileProgressWrapper.appendChild(this.fileProgressElement);

    document.getElementById(targetID).appendChild(this.fileProgressWrapper);
    fadeIn(this.fileProgressWrapper, 0);

  } else {
    this.fileProgressElement = this.fileProgressWrapper.firstChild;
    this.fileProgressElement.childNodes[1].firstChild.nodeValue = file.name;
  }

  this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.setProgress = function (percentage) {
  this.fileProgressElement.className = "progressContainer green";
  this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
  this.fileProgressElement.childNodes[3].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function () {
  this.fileProgressElement.className = "progressContainer blue";
  this.fileProgressElement.childNodes[3].className = "progressBarComplete";
  this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setError = function () {
  this.fileProgressElement.className = "progressContainer red";
  this.fileProgressElement.childNodes[3].className = "progressBarError";
  this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setCancelled = function () {
  this.fileProgressElement.className = "progressContainer";
  this.fileProgressElement.childNodes[3].className = "progressBarError";
  this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setStatus = function (status) {
  this.fileProgressElement.childNodes[2].innerHTML = status;
};

FileProgress.prototype.toggleCancel = function (show, swfuploadInstance) {
  this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
  if (swfuploadInstance) {
    var fileID = this.fileProgressID;
    this.fileProgressElement.childNodes[0].onclick = function () {
      swfuploadInstance.cancelUpload(fileID);
      return false;
    };
  }
};
