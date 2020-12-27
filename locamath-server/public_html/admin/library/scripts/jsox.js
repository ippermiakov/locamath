/**
 * Project:     Generic: the PHP framework
 * File:        jsox.js
 *
 * @version 1.1.0.0
 * @package Generic
 */

/**
 * JSOX - JavaScript Objects eXchange java scrip library
 * @package Generic
 */
var __jsox_dynamic_param = new Array();

if (!document.__jsox_script_linked) {

__jsoxDebug = false;
__jsoxBaseUrl = document.location.href;
__jsoxTemp = __jsoxBaseUrl.lastIndexOf("?");
__jsoxTemp = __jsoxBaseUrl.lastIndexOf("/") + 1;
__jsoxBaseUrl = __jsoxBaseUrl.substring(0, __jsoxTemp);


/**
 * Internal Function
 */

function __jsoxCall(methodName, callbackFunction) { 

  // create jsox server
  jsoxServer = null;
  var callbackStr = null;

  if (window.XMLHttpRequest) {
    // Opera, Mozilla
    jsoxServer = new XMLHttpRequest();
    if (jsoxServer.overrideMimeType) {
      jsoxServer.overrideMimeType("text/xml");
    }
  } else 
  if (window.ActiveXObject) {
    // Internet Explorer 
    try {
      jsoxServer = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        jsoxServer = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (e) {

      }
    }
  }

  if (jsoxServer == null) 
    alert("jsox Error: Can not create JSOX transport provider");

  if (callbackFunction)
    callbackStr = callbackFunction + "(jsoxServer.responseText";
  requestStr = __jsoxBaseUrl + "?__jsoxMethod=" + methodName;
  k = 0;
  for (var i = 2; i < arguments.length; i++) {
    if (callbackFunction)
      callbackStr = callbackStr + ", '" + arguments[i] + "'";
    //requestStr = requestStr + "&__jsoxParam[" + k + "]=" + escape(arguments[i]);
    requestStr = requestStr + "&__jsoxParam[" + k + "]=" + encodeURI(arguments[i]);
    k++;
  }
  
  if(__jsox_dynamic_param.length > 0){
	for(var i = 0;i < __jsox_dynamic_param.length;i++){		
    //requestStr = requestStr + "&__jsoxParam[" + k + "]=" + escape(__jsox_dynamic_param[i]);
		requestStr = requestStr + "&__jsoxParam[" + k + "]=" + encodeURI(__jsox_dynamic_param[i]);
	}
  }

  if (callbackFunction)
    callbackStr = callbackStr + ")";
  if (__jsoxDebug)
    alert("Request: " + requestStr + "\n" + "Callback: " +callbackStr);
  jsoxServer.onreadystatechange = function() {
    if (jsoxServer.readyState == 4) {
      if (jsoxServer.status == 200) {
        if (callbackFunction)
          eval(callbackStr); 
      } else {
        alert("jsox Error: " + jsoxServer.status);
      }
      //window.clearTimeout('__jsoxHideProgress();');
      //__jsoxHideProgress();
    }
  }

  __jsoxShowProgress();

  jsoxServer.open("GET", requestStr, true); 
  jsoxServer.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  jsoxServer.setRequestHeader("Content-length", 0);//arguments.length+1);
  jsoxServer.send(null); 

} 

/**
 * Internal Function
 */
function __jsoxCallback_FillCombo(responseText, comboName) {

  var combo = document.getElementById(comboName);
  options = responseText.split(";");
  for (i = 0; i < options.length; i++) {
    option = document.createElement("OPTION");
    combo.options.add(option);
    values = options[i].split(",");
    option.value = values[0];
    option.innerText = values[1];
  }
  combo.setAttribute("__jsoxFilled", "1");

}

/**
 * Internal Function
 */
var __jsoxProgressCounter = 0;

function __jsoxShowProgress() {

  var div = document.getElementById("__jsoxProgressIndicator");

  if (div == null) {

    canvas = document.getElementsByTagName((document.compatMode && document.compatMode == "CSS1Compat") ? "HTML" : "BODY")[0];

    var div        = document.createElement("div");
    div.id         = "__jsoxProgressIndicator";
    div.className  = "jsoxProgressIndicator";
    div.style.top  = canvas.scrollTop  + 1;
    div.style.left = canvas.scrollLeft + 1;
    document.body.appendChild(div);
    div.style.display = 'inline';
    
/*  var img        = document.createElement("div");
    img.id         = "__jsoxProgress";
    img.className  = "jsoxProgress";
    img.innerText  = "Loading...";
    img.style.top  = canvas.scrollTop  + 1;
    img.style.left = canvas.scrollLeft + 1 + 16;
    document.body.appendChild(img);
    img.style.display = 'inline';*/
    
  }

  __jsoxProgressCounter++;

  window.setTimeout('__jsoxHideProgress();', 1000);

}

/**
 * Internal Function
 */
function __jsoxHideProgress() {

  __jsoxProgressCounter--;

  if (__jsoxProgressCounter == 0) {
/*    var div = document.getElementById("__jsoxProgress");
    if (div != null)
      document.body.removeChild(div);*/
    var img = document.getElementById("__jsoxProgressIndicator");
    if (img != null)
      document.body.removeChild(img);
  }

}

/**
 * JavaScript Call function
 * @param $methodName Method to call on server side
 * @param $callbackFunction JavaScript callback function, will be called after server response
 */

var __jsoxQueue = new Array();

function jsoxQueuedCall(identifier, methodName, callbackFunction) {

  callStr = "__jsoxCall('" + methodName + "', '" + callbackFunction + "'";
 
  for (var i = 3; i < arguments.length; i++) {
    callStr = callStr + ", '" + arguments[i] + "'";
  }

  callStr = callStr + ");";

  if (__jsoxDebug)
    alert("Queued Call: " + callStr);

  if (__jsoxQueue[identifier] != null)
    window.clearTimeout(__jsoxQueue[identifier]);
  __jsoxQueue[identifier] = window.setTimeout(callStr, 1000);

}

function jsoxCall(methodName, callbackFunction) {

  callStr = "__jsoxCall('" + methodName + "', '" + callbackFunction + "'";
 
  for (var i = 2; i < arguments.length; i++) {
    callStr = callStr + ", '" + arguments[i] + "'";
  }

  callStr = callStr + ");";

  if (__jsoxDebug)
    alert("Call: " + callStr);

  eval(callStr);

}


function jsoxCancelCall(identifier) {

  if (__jsoxQueue[identifier] != null) {
    window.clearTimeout(__jsoxQueue[identifier]);
    __jsoxQueue[identifier] = null;
  }

}

/**
 * Remote Combo filling method
 * @param $methodName Method to call on server side which will return combo values in form  id,value;id,value...
 * @param $comboName Combobox name
 */
function jsoxFillCombo(methodName, comboName) {

  callStr = "__jsoxCall('" + methodName + "', '__jsoxCallback_FillCombo'";
  for (var i = 1; i < arguments.length; i++) {
    callStr = callStr + ", '" + arguments[i] + "'";
  }
  callStr = callStr + ")";

  if (__jsoxDebug)
    alert("Call: " + callStr);

  eval(callStr);

}

/**
 * Remote Combo filling method, same as before but fill combo only if it's empty
 * @param $methodName Method to call on server side which will return combo values in form  id,value;id,value...
 * @param $comboName Combobox name
 */
function jsoxFillEmptyCombo(methodName, comboName) {

  var combo = document.getElementById(comboName);
  if (combo.attributes.getNamedItem("__jsoxFilled") == null) {
    jsoxFillCombo(methodName, comboName);
  }

}

document.__jsox_script_linked = true;

}