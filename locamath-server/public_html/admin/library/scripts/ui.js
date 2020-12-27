if (!document.__ui_script_linked) {

var isIE = (/msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent));
var isOpera = /opera/i.test(navigator.userAgent);
var isIE7 = (document.all && !window.opera && window.XMLHttpRequest) ? true : false;

__codeMirrors = new Array();

// disable context menu, ie only :-(
__ContextMenuDisabled = false;

function __ValidateContextMenu(e) {

  if (__ContextMenuDisabled) {
    e = e || window.event || {};
    event.cancelBubble = true;
    event.returnValue = false;
    return false;
  }
  
} 

if (typeof document.attachEvent != 'undefined') {  
  document.attachEvent('oncontextmenu', __ValidateContextMenu);
} else {
  document.addEventListener('contextmenu', __ValidateContextMenu, false);
}


function MenuController() {

  this.Controls   = new Array();
  this.Menus      = new Array();
  this.PopUpMenus = new Array();

}

MenuController.prototype.AddPopUpMenu = function(controlId, menuId) {

  this.Controls[this.Controls.length]     = controlId;
  this.PopUpMenus[this.PopUpMenus.length] = menuId;
  
  this.AddMenu(menuId);

}

MenuController.prototype.AddMenu = function(menuId) {

  this.Menus[this.Menus.length] = menuId;
  if ($.browser.msie) {
    $('#' + menuId + ' li').bind('mouseover', function(e) {
      this.className+=" over";
      if (isIE && !isIE7) {
        var uls = this.getElementsByTagName("UL");
        var pos,selpos,selects;
        for (var n=0; n<uls.length; n++) {
          pos = __GetAbsolutePositionAndSizes(uls[0]);
          selects = document.getElementsByTagName("select");
          for (var k=0; k<selects.length; k++) {
            selpos = __GetAbsolutePositionAndSizes(selects[k]);
            if (!(selpos.x>pos.x+pos.w || pos.x>selpos.x+selpos.w || selpos.y>pos.y+pos.h || pos.y>selpos.y+selpos.h))
              selects[k].style.visibility = 'hidden';
          }
        }
      }
    });
    $('#' + menuId + ' li').bind('mouseout', function(e) {
      e = e || window.event || {};
      var target = e.toElement || e.relatedTarget;
      if (!__IsChildOf(target, this)) {
        if (isIE && !isIE7) {
          var uls = this.getElementsByTagName("UL");
          var pos,selpos,selects;
          for (var n=0; n<uls.length; n++) {
            pos = __GetAbsolutePositionAndSizes(uls[0]);
            selects = document.getElementsByTagName("select");
            for (var k=0; k<selects.length; k++) {
              selpos = __GetAbsolutePositionAndSizes(selects[k]);
              if (!(selpos.x>pos.x+pos.w || pos.x>selpos.x+selpos.w || selpos.y>pos.y+pos.h || pos.y>selpos.y+selpos.h))
                selects[k].style.visibility = '';
            }
          }
        }
        this.className = this.className.replace > (" over", "");
      }
    });
  }
          
}

MenuController.prototype.FindControl = function(el) {

  temp = el;
  while ((temp != null) && (temp.tagName != "BODY")) {
    for (i in this.Controls) {
      if (temp.id == this.Controls[i]) {
        el = temp;
        return {control: el, menu: this.PopUpMenus[i]};
      }
    }
    temp = temp.parentElement;
  }
  return null;

}

MenuController.prototype.ShowMenu = function(menu) {

  if (typeof menu == 'string')
    menu = document.getElementById(menu);
  if (menu) {
    __ContextMenuDisabled = true;

    menu.style.left    = document.mouseX;
    menu.style.top     = document.mouseY;
    menu.style.display = "inline";

    menu.onmouseout = function(e) {
      e = e || window.event || {};
      var target = e.toElement || e.relatedTarget;
      if (!__IsChildOf(target, this)) {
        this.style.display = 'none';
        __ContextMenuDisabled = false;
      } 
    }
  }
  
}

MenuController.prototype.MouseDown = function(e) {

  if (isIE && (e.button == 2)) {
    var ctrl, menu;
    ctrl = this.FindControl(e.target?e.target:e.srcElement);
    if (ctrl) {
      menu = document.getElementById(ctrl.menu);
      this.ShowMenu(menu);
    }
  }
  
}

menuController = new MenuController();

MenuController_MouseDown = function (e) { menuController.MouseDown(e); }

if (typeof document.attachEvent != 'undefined') { 
  document.attachEvent('onmousedown', MenuController_MouseDown);
} else {
  if (window.navigator.userAgent.indexOf("MSIE") != -1) 
  document.addEventListener('mousedown', MenuController_MouseDown, false);
}


function __DoPostBackSelection(context_name, event_name, event_value, confirmation, confirmation_as_value, please_select_text) {

  count = Browser_CalcSelection(context_name);

  if (count == 0) {
    if (!please_select_text) {
      please_select_text = 'Please select at least one record';
    }
    alert(please_select_text)
  } else {
    __DoPostBack(context_name, event_name, event_value, confirmation, confirmation_as_value);
  }
  
}

function __DoPostBack(context_name, event_name, event_value, confirmation, confirmation_as_value) {

  if (confirmation) {
    doit = (confirm(confirmation));
  } else
    doit = true;
    
  if (doit || confirmation_as_value) {
    var ctrl;
    
    if ((event_name != null)  && (event_name != "")) {
      $('#' + context_name + "_event_name").val(event_name);
    }
    if ((event_value != null) && (event_value != "")) {
      $('#' + context_name + "_event_value").val(event_value);
    }
    if (confirmation_as_value) {
      $('#' + context_name + "_confirm_value").val(doit?1:0);
    }

    if (window.nicEditors && window.nicEditors.editors) {
      for (i = 0; i < window.nicEditors.editors.length; i++) {
        if (window.nicEditors.editors[i].nicInstances) {
          for (j = 0; j < window.nicEditors.editors[i].nicInstances.length; j++) {
            window.nicEditors.editors[i].nicInstances[j].saveContent();
          }
        }
      }
    }

    for (i = 0; i < __codeMirrors.length; i++) {
      __codeMirrors[i].options.container.value = __codeMirrors[i].getCode();
    }

    window.onbeforeunload = null;
    $('#' + $('#v9E01DDADv').val()).submit();

  }

}

function __DoPostBackWithReason(context_name, event_name, event_value, reason_query) {

  var ctrl;
  ctrl = document.getElementById(context_name + "_reason_value");
  if (new_reason = prompt(reason_query, ctrl.value)) {
    ctrl.value = new_reason;
    if ((event_name != null)  && (event_name != "")) {
      ctrl = document.getElementById(context_name + "_event_name");
      ctrl.value = event_name;
    }
    if ((event_value != null) && (event_value != "")) {
      ctrl = document.getElementById(context_name + "_event_value");
      ctrl.value = event_value;
    }
    var formId = document.getElementById("v9E01DDADv");
    if (formId)
      form = document.getElementById(formId.value);
    else 
      form = document.forms[0];
      
    window.onbeforeunload = null;
    form.submit();
  }

}

function __OnSubmit() {

  window.onbeforeunload = null;
  return false;

};

function __NotImplemented() {

  alert('This function is not implemented yet');

};

function Browser_CalcSelection(context_name) {

  result = 0;
  
  var elements = document.forms[0].elements;

  for(var i = 0; i < elements.length; i++) {
    if ((elements[i].id != null) && (elements[i].id.indexOf(context_name + '_rec') == 0))
      if (elements[i].checked)
        result++;
  }

  return result;

}

function Browser_DoSelectAll(checkbox, context_name) {

  var elements = document.forms[0].elements;

  for(var i = 0; i < elements.length; i++) {
    if ((elements[i].id != null) && (elements[i].id.indexOf(context_name + '_rec') == 0))
      elements[i].checked = (checkbox.checked == 1);
  }

};

function Browser_DoDeleteSelected(context_name) {

  count = Browser_CalcSelection(context_name);

  if (count == 0)
    alert('Please select at least one record')
  else
    __DoPostBack(context_name, 'brw_delete_selected', null, 'Are you sure you want to delete ' + count + ' selected record(s)?');

};

function __Browser_RowOver(ctrl, cursor) {

  if (cursor)
    ctrl.style.cursor = 'pointer';
  ctrl.oldClassName = ctrl.className;
  if (ctrl.className)
    ctrl.className    = ctrl.className + '_selected';
  else
    ctrl.className    = 'selected';

}

function __Browser_RowOut(ctrl) {

  ctrl.style.cursor = 'default';    
  ctrl.className    = ctrl.oldClassName; 

}

function __Browser_RowClick(ctrl) {


}

function __Popup(url, w, h) {

  if (w == null) { 
    if (screen.width)
      if (screen.width >= 1280)
        w = 1000;
      else
      if (screen.width >= 1024)
        w = 800;
      else
        w = 600;
  }
  if (h == null) { 
    if (screen.height)
      if (screen.height >= 900)
        h = 700;
      else
      if (screen.height >= 800)
        h = 600;
      else
        h = 500;
  }
  var left = (screen.width) ? (screen.width-w)/2 : 0;
  var settings = 'height='+h+',width='+w+',top=20,left='+left+',menubar=1,scrollbars=1,resizable=1,toolbar=1'
  var win = window.open(url, '_blank', settings)
  if (win) {
    win.focus();
  }

}

function __PopupEditor(url, invokerControl) {

  document.popupInvokerControl  = document.getElementById(invokerControl);
  document.popupInvokedReadOnly = false;
  __Popup(url);

}

function __PopupSelector(url, invokerControl) {

  document.popupInvokerControl  = document.getElementById(invokerControl);
  document.popupInvokedReadOnly = true;
  __Popup(url);

}

function __PopupEx(url, top, left, width, height, menubar) {

  if (top==null) t=50;
  if (left==null) l=50;
  if (width==null) w=600;
  if (height==null) h=400;
  if (menubar==null) mb=0;
  var w = window.open(url, '_blank', 'menubar='+menubar+',resizable=0,scrollbars=0,top='+top+',left='+left+',width='+width+',height='+height, true);
  w.focus();

}

function __Open(url, params) {
  
  if (params == null)
    params = 'menubar=1,resizable=1,scrollbars=1';
  var w = window.open(url, '_blank', params, true);
  w.focus();

}

function __OpenUrlFrom(href, control_name, params) {
   
  if ((control_name != null) && (control_name != "")) {
    ctrl = document.getElementById(control_name);
    if (ctrl != null) {
      url = ctrl.value;
      if (url != "")
        __Open(url, params);
      else
        alert("Url is empty");  
    }
  }

}

function __SetUrlFrom(to, from) {
   
  from.href = 'javascript:;';
   
  if (to) {
    var toControl = document.getElementById(to);
    if (toControl) {
      var url = toControl.value;
      if (url)
        from.href = url;
      else
        alert("Url is empty");  
    }
  }

}

function __ClosePopup(refresh, key) {

  if (self.opener && !self.opener.closed && self.opener.document && self.opener.document.forms) {
    var od = self.opener.document;
    if (od.popupInvokerControl && key) {
      if (od.popupInvokedReadOnly) { 
        od.popupInvokerControl.value = key;
      } else
      if (od.popupInvokerControl.tagName == 'SELECT') {
        var option = od.createElement("option");
        option.value = key;
        if (window.navigator.userAgent.indexOf("MSIE") != -1)
          option.innerText  = '';
        else  
          option.text  = '';
        od.popupInvokerControl.appendChild(option);
        od.popupInvokerControl.value = key;
      } else 
        od.popupInvokerControl.value = key;
    }
    if (refresh) {
      var formId = od.getElementById("v9E01DDADv");
      var form;
      if (formId)
        form = od.getElementById(formId.value);
      if (form) {
        window.onbeforeunload = null;
        form.submit();
      } else {
        od.location = od.location;  
      }
    }
    self.opener.focus(); 
  }

  if (arguments[2])
    if (typeof self.opener.window[arguments[2]] == 'function')
      self.opener.window[arguments[2]](key);
  
  self.close();

}

function __EnableRadioContainer(radiobox) {

  var row = radiobox.parentNode.parentNode.parentNode;
  var inputs = row.getElementsByTagName('INPUT');
  for (var i = 0; i < inputs.length; i++) {
    if (inputs[i].type != 'radio')
      inputs[i].disabled = true;
  }
  var inputs = row.getElementsByTagName('SELECT');
  for (var i = 0; i < inputs.length; i++) {
    inputs[i].disabled = true;
  }
  var inputs = row.getElementsByTagName('TEXTAREA');
  for (var i = 0; i < inputs.length; i++) {
    inputs[i].disabled = true;
  }

  var row = radiobox.parentNode.parentNode;
  var inputs = row.getElementsByTagName('INPUT');
  for (var i = 0; i < inputs.length; i++) {
    if (inputs[i].type != 'radio')
      inputs[i].disabled = false;
  }
  var inputs = row.getElementsByTagName('SELECT');
  for (var i = 0; i < inputs.length; i++) {
    inputs[i].disabled = false;
  }
  var inputs = row.getElementsByTagName('TEXTAREA');
  for (var i = 0; i < inputs.length; i++) {
    inputs[i].disabled = false;
  }
  
}

function __SyncDependentControls(checkbox, controls, enable) {
                       
  var list = controls.split(";");
  var ctrl;
  for (var i = 0; i < list.length; i++) {
    ctrl = document.getElementById(list[i]);
    if (ctrl) {
      if (enable) {
        ctrl.disabled = checkbox.checked;
      } else {
        ctrl.disabled = !checkbox.checked;
      }
    }
  }
  
}

function __SyncDependentControlsVisible(control, controls, current) {

  if (control.currentvalue == undefined)
    control.currentvalue = current;
  var list = controls.split(";");
  var ctrl, ctrlName, otherControls;
  for (var i = 0; i < list.length; i++) {
    ctrlName = list[i] + '[' + control.currentvalue + ']';
    ctrl = document.getElementById(ctrlName);
    if (ctrl)
      ctrl.style.display = 'none';
      
    ctrlName = list[i] + '[' + control.value + ']';
    ctrl = document.getElementById(ctrlName);
    if (ctrl)
      ctrl.style.display = 'inline';
      
    control.currentvalue = control.value;  
  }
  
}

function __GetAbsolutePosition(o) {

  var x = 0, y = 0;
  do {
    x += o.offsetLeft;
    y += o.offsetTop;
  } while ((o = o.offsetParent));

  return { x: x, y: y };

}

function __GetAbsolutePositionAndSizes(o) {

  var x = 0, y = 0, w = o.offsetWidth, h = o.offsetHeight;
  do {
    x += o.offsetLeft;
    y += o.offsetTop;
  } while ((o = o.offsetParent));

  return { x: x, y: y, w: w, h: h };

}

var highslide_caller;

function save_highslide_caller(ctrl) { 

  highslide_caller = ctrl;
  
}

var highslide_divs = 0;

function show_highslide_hint(text, caller) {

  highslide_divs++;
    
  var div = document.createElement("div");
  div.innerHTML = "<div class=\"highslide-html-content\" id=\"highslide-html-" + highslide_divs + "\"><div class=\"highslide-header\"><ul><li class=\"highslide-move\"><a href=\"#\" onclick=\"return false\">Move</a></li><li class=\"highslide-close\"><a href=\"#\" onclick=\"return hs.close(this)\">Close</a><li></ul></div><div class=\"highslide-body\" id=\"highslide-html-body-" + highslide_divs + "\"></div><div class=\"highslide-footer\"><div><span class=\"highslide-resize\" title=\"Resize\"><span></span></span></div></div></div>";
  document.body.appendChild(div);
    
  div = document.getElementById("highslide-html-body-" + highslide_divs);
  div.innerHTML = text;
    
  hs.htmlExpand(caller, { contentId: "highslide-html-" + highslide_divs } );
  
}

function show_record_hint(response, record_id, centered) {
   
  show_highslide_hint(response, highslide_caller);
  
}

function hide_record_hint(identifier, deferred) {

  jsoxCancelCall(identifier);

}

function show_record_details(response, className, keyValue, controlName) {

  var control = document.getElementById(controlName);
  if (control) {
    control.innerHTML = response;
    if (response)
      control.style.display = '';
    else  
      control.style.display = 'none';
  }

}

function addEnterHandler(id, on_enter_pressed) {

  d = document;               

  var ctrl  = d.getElementById(id);
  
  ctrl.on_enter_pressed = on_enter_pressed;

  ctrl.onfocus = function() {
    this.select();
  }

  ctrl.onkeyup = function(e) {     
    e = e || window.event;
    if (e.keyCode == 13)
      if (this.on_enter_pressed)
        eval(this.on_enter_pressed);
  }

}

function __Editor_ShowIfSelected(combo, ctrl_id) {
  
  var ctrl  = document.getElementById(ctrl_id);
  if (ctrl) {
    if (combo.value)
      ctrl.style.display = 'inline';
    else  
      ctrl.style.display = 'none';
  }
  
}

function __Editor_ShowIfValueExists(ctrlid1, ctrlid2) {
  
  var ctrl1  = document.getElementById(ctrlid1);
  var ctrl2  = document.getElementById(ctrlid2);
  if (ctrl1 && ctrl2) {
    if (ctrl2.value)
      ctrl1.style.display = 'inline';
    else  
      ctrl1.style.display = 'none';
  }
  
}

function __Editor_ClearValue(ctrlid) {
                            
  var ctrl = document.getElementById(ctrlid);
  if (ctrl) {
    if (ctrl.value)
      ctrl.value = '';
    else
    if (ctrl.innerText)
      ctrl.innerText = '';
  }
  
}

function __EncryptNum(numb) {

  //alert(num);
  var num = parseInt(numb); 
  var rand1 = 100+Math.floor(Math.random()*899);
  var rand2 = 100+Math.floor(Math.random()*899);
  //alert(rand1);
  //alert(rand2);
  //$rand1 = rand(100, 999);
  //$rand2 = rand(100, 999);
  var key1 = (num + rand1) * rand2;
  var key2 = (num + rand2) * rand1;
  //alert(key1);
  //alert(key2);
  //$key1 = ($num + $rand1) * $rand2;
  //$key2 = ($num + $rand2) * $rand1;
  var result = rand1 + '' + rand2 + '' + key1 + '' + key2;
  //alert(result);
  //$result = $rand1.$rand2.$key1.$key2;
  var rand1_len = String.fromCharCode('A'.charCodeAt(0) + (rand1 + '').length);
  var rand2_len = String.fromCharCode('D'.charCodeAt(0) + (rand2 + '').length);
  var key1_len  = String.fromCharCode('G'.charCodeAt(0) + (key1 + '').length);
  //alert(rand1_len);
  //alert(rand2_len);
  //alert(key1_len);
  //$rand1_len = chr(ord('A') + strlen($rand1));
  //$rand2_len = chr(ord('D') + strlen($rand2));
  //$key1_len  = chr(ord('G') + strlen($key1));
  var rand1_pos = Math.floor(Math.random()*Math.floor(result.length/3));
  var result1 = result.substr(0, rand1_pos) + rand1_len + result.substr(rand1_pos);
  //$rand1_pos = rand(0, floor(strlen($result)/3));
  //$result1 = substr_replace($result, $rand1_len, $rand1_pos, 0);
  var rand2_pos = rand1_pos + 1 + Math.floor(Math.random()*(Math.floor(2*result.length/3) -  rand1_pos - 1));
  var result2 = result1.substr(0, rand2_pos) + rand2_len + result1.substr(rand2_pos);
  //$rand2_pos = rand($rand1_pos + 1, floor(2*strlen($result1)/3));
  //$result2 = substr_replace($result1, $rand2_len, $rand2_pos, 0);
  var key1_pos = rand2_pos + 1 + Math.floor(Math.random()*(result2.length - 1 -  rand2_pos - 1));
  var result3 = result2.substr(0, key1_pos) + key1_len + result2.substr(key1_pos);
  //$key1_pos  = rand($rand2_pos + 1, strlen($result2)-1);
  //$result3 = substr_replace($result2, $key1_len, $key1_pos, 0);
  
  return result3;
  
} 

function __PopupEditorOfKey(url, invokerControl) {

  document.popupInvokerControl  = document.getElementById(invokerControl);
  document.popupInvokedReadOnly = false;
  
  __Popup(url.replace('__key_enc__', __EncryptNum(document.popupInvokerControl.value)));

}

function __TrackMouse(e) {

  var posx = 0;
  var posy = 0;

  e = e || window.event || {};

  if (e.pageX || e.pageY) {
    posx = e.pageX;
    posy = e.pageY;
  } else {
    if (e.clientX || e.clientY) {
      posx = e.clientX;
      if (document.body)
        posx = posx + document.body.scrollLeft;
      if (document.documentElement)
        posx = posx + document.documentElement.scrollLeft;
      posy = e.clientY;
      if (document.body)
        posy = posy + document.body.scrollTop;
      if (document.documentElement)
        posy = posy + document.documentElement.scrollTop;
    }
  }

  document.mouseX = posx;
  document.mouseY = posy

}

if (typeof document.attachEvent != 'undefined') { 
  document.attachEvent('onmousemove', __TrackMouse);
} else {
  document.addEventListener('mousemove', __TrackMouse, false);
}

function __IsChildOf(child, parent) {

  if (child && child.parentNode) 
    while (child = child.parentNode)
      if (child == parent) {
        return true;
      }
  return false;
  
}
  
// interactive grid
function InteractiveGrids() {

  this.Grids = Array();
  this.SelectionClass = 'selected';
  this.SelectionClassSuffix = '_selected';
  
}

InteractiveGrids.prototype.ProcessRowSelection = function(rowName) {

  var control = document.getElementById(rowName);
  if (control) {
    var attr = control.attributes['onselectrow'];
    if (attr) {
      var script = attr.value;
      if (script)
        eval(script);
    }
  }
  
}

InteractiveGrids.prototype.KeyDown = function(e) {

  e = e || window.event || {};
  var el = e.target?e.target:e.srcElement;
  if ((el.tagName != 'DIV') &&
      (el.tagName != 'SELECT') &&
      (el.tagName != 'INPUT') && 
      (el.tagName != 'TEXTAREA')) {
    var prefix;
    k = this.CurrentGrid;
    if (k != null) {
      prefix = this.Grids[k].prefix;
      if (e.keyCode == 38) {
        // up
        this.Grids[k].current = null;
        var trs = document.getElementsByTagName('tr');
        var prior;
        for(var i = 0; i < trs.length; i++) {
          if ((trs[i].id != null) && (trs[i].id.indexOf(prefix) == 0)) {
            if ((trs[i].className == this.SelectionClass) || (trs[i].className == trs[i].ClassNameBeforeSelection + this.SelectionClassSuffix)) {
              if (prior) {
                prior.ClassNameBeforeSelection = prior.className;
                if (prior.className)
                  prior.className = prior.className + this.SelectionClassSuffix;
                else
                  prior.className = this.SelectionClass;
                this.Grids[k].current = prior.id;
                this.ProcessRowSelection(this.Grids[k].current);
                trs[i].className = trs[i].ClassNameBeforeSelection;

                var pos = __GetAbsolutePosition(prior);
                var y = pos.y;
                var height = document.body.scrollTop;
                if (y > height) {
                  e.returnValue = false;
                  e.cancelBubble = true;
                  if (e.stopPropagation) 
                    e.stopPropagation();
                }  
              }
            } 
            prior = trs[i];
          }
        }
      } else 
      if (e.keyCode == 40) {
        // down     
        this.Grids[k].current = null;
        var trs = document.getElementsByTagName('tr');
        var prior;
        for(var i = 0; i < trs.length; i++) {
          if ((trs[i].id != null) && (trs[i].id.indexOf(prefix) == 0)) {
            if ((trs[i].className == this.SelectionClass) || (trs[i].className == trs[i].ClassNameBeforeSelection + this.SelectionClassSuffix)) {
              prior = trs[i];
            } else {
              if (prior) {
                if ((prior.className == this.SelectionClass) || (prior.className == prior.ClassNameBeforeSelection + this.SelectionClassSuffix)) {
                  prior.className = prior.ClassNameBeforeSelection;
                }
                trs[i].ClassNameBeforeSelection = trs[i].className;
                if (trs[i].className)
                  trs[i].className = trs[i].className + this.SelectionClassSuffix;
                else
                  trs[i].className = this.SelectionClass;
                this.Grids[k].current = trs[i].id;
                this.ProcessRowSelection(this.Grids[k].current);
                prior = null;
                
                var pos = __GetAbsolutePosition(trs[i]);
                var y = pos.y + trs[i].offsetHeight;
                if (isIE || isOpera)
                  var height = document.body.offsetHeight + document.body.scrollTop;
                else  
                  var height = window.innerHeight + document.body.scrollTop;
                if (y < height) {
                  e.returnValue = false;
                  e.cancelBubble = true;
                  if (e.stopPropagation)
                    e.stopPropagation();
                }  
              } else {
                if ((trs[i].className == this.SelectionClass) || (trs[i].className == trs[i].ClassNameBeforeSelection + this.SelectionClassSuffix)) {
                  trs[i].className = trs[i].ClassNameBeforeSelection;
                }
              }
            }
          }
        }
      } else
      if (e.keyCode == 13) {
        if (this.Grids[k].current) {
          var control = document.getElementById(this.Grids[k].current);
          if (control) {
            var attr = control.attributes['onenterpress'];
            if (attr) {
              var script = attr.value;
              if (script) {
                eval(script);
              }  
            }    
          }
        }
      }
    }
  }
  
}

InteractiveGrids.prototype.MouseClick = function(e) {
   
  e = e || window.event || {};
  var el = e.target?e.target:e.srcElement;
  var prt = el.parentNode;
  
  if ((el.tagName != 'TD') && (el.tagName == 'SPAN') && (prt) && (prt.tagName == 'TD')) {
    el = prt;
  }
  
  if (el.tagName == 'TD') {
    var row = el.parentNode;
    var gid = row.attributes['gid']
    var grh = row.attributes['grh']
    var table = row.parentNode;
    table = table.parentNode;
    var gcl = table.attributes['gcl']
    var ghp = table.attributes['ghp']
    if (grh && gid && gcl) {
      showRecordHint(el, gcl.value, gid.value, ghp.value); 
    }
    if (row.className != this.SelectionClass) {
      var prefix;
      for (k in this.Grids) {
        prefix = this.Grids[k].prefix;
        if ((row.id != null) && (row.id.indexOf(prefix) == 0)) {
          var trs = document.getElementsByTagName('TR');
          for (var i = 0; i < trs.length; i++) {
            if ((trs[i].id != null) && (trs[i].id.indexOf(prefix) == 0))
              if ((trs[i].className == this.SelectionClass) || (trs[i].className == trs[i].ClassNameBeforeSelection + this.SelectionClassSuffix))
                trs[i].className = trs[i].ClassNameBeforeSelection;
          }
          row.ClassNameBeforeSelection = row.className;
          if (row.className)
            row.className = row.className + this.SelectionClassSuffix;
          else
            row.className = this.SelectionClass;
          if (el.onclick)
            row.style.cursor = 'pointer';
          this.Grids[k].current = row.id;
          this.CurrentGrid = k;
        }
      }
    }
  }
  
}

InteractiveGrids.prototype.MouseOver = function(e) {
   
  e = e || window.event || {};
  var el = e.target?e.target:e.srcElement;

  if (el) {
    if (el.tagName == 'TD') {
      var row = el.parentNode;
      var grh = row.attributes['grh']
      var prefix;
      for (k in this.Grids) {
        prefix = this.Grids[k].prefix;
        if ((row.id != null) && (row.id.indexOf(prefix) == 0)) {
          if (el.onclick || row.onclick || grh)
            row.style.cursor = 'pointer';
        }
      }
    }
  }
  
}

InteractiveGrids.prototype.AddRowPrefix = function(prefix) {

  var l = this.Grids.length;
  this.Grids[l] = {prefix: prefix, current: null};
  this.CurrentGrid = l;
  
  var trs = document.getElementsByTagName('tr');
  var first;
  for(i = 0; i < trs.length; i++) {
    if ((trs[i].id != null) && (trs[i].id.indexOf(prefix) == 0)) {
      if (trs[i].className == this.SelectionClass) {
        trs[i].ClassNameBeforeSelection = '';
        this.Grids[l].current = trs[i].id;
        if (first)
          first = null;
      } else 
        if (!first && !this.Grids[l].current)
          first = trs[i];    
    }
  }
  if (first) {
    first.ClassNameBeforeSelection = first.className;
    if (first.className)
      first.className = first.className + this.SelectionClassSuffix;
    else
      first.className = this.SelectionClass;
    this.Grids[l].current = first.id;
  }

}

function InteractiveGrids_KeyDown(e) { interactiveGrids.KeyDown(e); }
function InteractiveGrids_MouseClick(e) { interactiveGrids.MouseClick(e); }
function InteractiveGrids_MouseOver(e) { interactiveGrids.MouseOver(e); }

interactiveGrids = new InteractiveGrids();

if (document.attachEvent) {
  if (window.navigator.userAgent.indexOf("MSIE") != -1) 
  // IE
    document.attachEvent('onkeydown', InteractiveGrids_KeyDown);
  else  
  // Opera
    document.attachEvent('onkeypress', InteractiveGrids_KeyDown);
  document.attachEvent('onmousedown', InteractiveGrids_MouseClick);
  document.attachEvent('onmouseover', InteractiveGrids_MouseOver);
} else {                     
  document.addEventListener('keydown', InteractiveGrids_KeyDown, false); 
  document.addEventListener('mousedown', InteractiveGrids_MouseClick, false);
  document.addEventListener('mouseover', InteractiveGrids_MouseOver, false);
}

function ResizePopUpToContent() {

  var pos = 0;
  var inputs = document.getElementsByTagName('INPUT');
  var new_pos;
  for (var i = 0; i < inputs.length; i++) {
    new_pos = __GetAbsolutePosition(inputs[i]);
    if (new_pos.y > pos)
      pos = new_pos.y;
  }

  if (pos > 0) {
    if (pos < screen.height/4)
      pos = screen.height/4;
    else  
    if (isOpera) {
      if (pos > (screen.height - 3*110))
        pos = screen.height - 3*110;
    } else {
      if (pos > (screen.height - 2*110))
        pos = screen.height - 2*110;
    }
    
    if (isIE)
      var height = document.body.offsetHeight;
    else
      var height = window.innerHeight;  

    try {   
      window.resizeBy(0, -(height-pos) + 50); 
    } catch (e) {
    }
    
  }
  
}

function switch_theme_callback(responseText) {

  var theme = responseText.split(";");
  var links = document.getElementsByTagName('LINK');
  for (var i = 0; i < links.length; i++) 
    links[i].href = links[i].href.replace(theme[1], theme[3]);
  var a = document.getElementById('theme_switcher_' + theme[0]);
  a.className = '';
  var a = document.getElementById('theme_switcher_' + theme[2]);
  a.className = 'switcher_selected_mode';
  
}

function ajax_callback_switch_theme(responseText, container) {

  var theme = responseText.split(";");
  var links = document.getElementsByTagName('LINK');
  for (var i = 0; i < links.length; i++) 
    links[i].href = links[i].href.replace(theme[1], theme[3]);
  var a = document.getElementById('theme_switcher_' + theme[0]);
  $('#' + container).html(theme[2]);
  
}

function switch_language_callback(responseText) {

  document.location = document.location;
  
}

function ajax_callback_switch_language(responseText) {

  window.location.reload();
  
}

function filter_group_visibility_switcher(control, tag) {

  var panel;
  for (var j=1; j < control.parentNode.parentNode.childNodes.length; j++) {
    panel = control.parentNode.parentNode.childNodes[j];
    if (panel.style.display != 'none')
      panel.style.display = 'none';
    else
      panel.style.display = 'inline';
    jsoxCall('generic_call_save_browser_filter_group_visibility', '', tag, panel.style.display);
  }

}

function show_column_menu(control, ajaxMenuMethod, ajaxSelectMethod, container, recordId, callbackMethod) {

  $('#column_menu').remove();
  var list=$('<ul id="column_menu"><div class="ajax_call">Updating...</div></ul>').appendTo("body");
  list.css('position', 'absolute'); 
  list.css('border', '1px dotted blue');
  list.css('padding', '5px 5px'); 
  list.css('background-color', '#FFFFFF');
  list.css('text-align', 'left');
  list.css('left', document.mouseX); 
  list.css('top',  document.mouseY);
  
  var ajaxBaseUrl = document.location.href;
  var ajaxTemp = ajaxBaseUrl.lastIndexOf("/") + 1;
  ajaxBaseUrl = ajaxBaseUrl.substring(0, ajaxTemp);
  requestStr = ajaxBaseUrl + "?__ajaxMethod=" + ajaxMenuMethod;
  $.get( requestStr
       , { recordId: recordId
         }
       , function(data, textStatus) {
           list.html(data);
           $('#column_menu li').hover( 
               function() { $(this).css('background-color', 'blue');
                            $(this).css('color', 'white');
                            $(this).css('cursor', 'pointer'); 
                          } 
             , function() { $(this).css('background-color', ''); 
                            $(this).css('color', ''); 
                            $(this).css('cursor', ''); 
                          } 
           );
           $('#column_menu a').click(
             function(){
               var ajaxBaseUrl = document.location.href;
               var ajaxTemp = ajaxBaseUrl.lastIndexOf("/") + 1;
               ajaxBaseUrl = ajaxBaseUrl.substring(0, ajaxTemp);
               requestStr = ajaxBaseUrl + "?__ajaxMethod=" + ajaxSelectMethod;

               if (container)
                 $('#' + container).html('<div class="ajax_call">Updating...</div>');

               $.get( requestStr
                    , { recordId: recordId 
                      , valueId: $(this).attr('name') 
                      }
                    , function (data, textStatus) {
                        if (callbackMethod)
                          eval(callbackMethod + '(data, container);');
                        else
                        if (container) 
                          $('#' + container).html(data);
                      }
                    );
               list.remove();
             }
           )
         }
      );
      
  list.hover(
      function() { }
    , function() { 
      $(this).remove(); 
    }
  );

}

function _scm(control, ajaxMenuMethod, ajaxSelectMethod, container, recordId, callbackMethod) {
  show_column_menu(control, ajaxMenuMethod, ajaxSelectMethod, container, recordId, callbackMethod);
}

function ajax_load_combo(combo, ajax_method, ajax_params, force) {

  if (!$(combo).attr('__requested') || force) {

    $(combo).attr('disabled', 'disabled');
    $(combo).attr('__requested', '1');

    var ajaxBaseUrl = document.location.href;
    var ajaxTemp = ajaxBaseUrl.lastIndexOf("/") + 1;
    ajaxBaseUrl = ajaxBaseUrl.substring(0, ajaxTemp);
    requestStr = ajaxBaseUrl + "?__ajaxMethod=" + ajax_method + ajax_params;

    $.get( requestStr
         , { 
           }
         , function(data, textStatus) {
             var control = $(combo);
             var options = control.children().clone();
             var selected = control.val();
             control.html(data);
             for(i=options.length-1;i>=0;i--) {
               if (!(options[i].value > 0))
                 control.prepend(options[i]);
             }
             control.val(selected);
             control.attr('disabled', '');
             // to force redraw in IE and Opera
             control.css('display', 'inline');
             //combo.dropdown();
           }
        );
  }

}

function addEvent(obj, type, listener) {
        if(obj.addEventListener) {
                obj.addEventListener(type, listener, false);
        }
        else if(obj.attachEvent) {
                obj.attachEvent('on' + type, function() {listener.apply(obj);});
        }
}

function codeMirror_find(id) {

  for (i = 0; i < __codeMirrors.length; i++) {
    if (__codeMirrors[i].options.container.id == id) {
      return __codeMirrors[i];
    }
  }

}

var commentEditor = null;
var htmlNicEditor = null;

function ShowEntityCommentEditor(entityId) {

  if (!commentEditor) {
    commentEditor = new nicEditor({fullPanel : true, 
                                   iconsPath: nicEditorIcons, 
                                   maxHeight: 120});
  }
  
  var editorInstance = commentEditor.instanceById(entityId + '_ec_editor');
  
  if (!editorInstance) {
    commentEditor.panelInstance(entityId + '_ec_editor', {hasPanel : true});
    $('#' + entityId + '_ec_edit').css('display', 'none');
    $('#' + entityId + '_ec_save').fadeIn();
    $('#' + entityId + '_ec_cancel').fadeIn();
  } 
  
}  

function OnOffEntityCommentWindow(entityId, entityName) {

  if ($('#' + entityId + '_ec_window').css('display') == 'none') {
    $('#' + entityId + '_ec_window').fadeIn();
    $.post( '?__ajaxMethod=generic_SaveEntityCommentState'
          , { name: entityName, is_visible: 1 }
          );
  } else {  
    $('#' + entityId + '_ec_window').fadeOut();
    $.post( '?__ajaxMethod=generic_SaveEntityCommentState'
          , { name: entityName, is_visible: 0 }
          );
  }   
  
}

function LoadEntityComment(entityId, entityName, isInternal) {

  $(document).ready(function() {
    $.getJSON( '?__ajaxMethod=generic_LoadEntityComment'
             , { name: entityName } 
             , function(data) {
                 $('#' + entityId + '_ec_editor').html(data.body);
                 if (!isInternal) {
                   $('#' + entityId + '_ec_switcher').fadeOut( 'normal'
                                                    , function() { 
                                                        if ($('#' + entityId + '_ec_editor').html().length > 0) {
                                                          $('#' + entityId + '_ec_switcher').attr('src', $('#' + entityId + '_ec_switcher').attr('src').replace('img_ajax_call', 'img_comment_exists')); 
                                                        } else {
                                                          $('#' + entityId + '_ec_switcher').attr('src', $('#' + entityId + '_ec_switcher').attr('src').replace('img_ajax_call', 'img_comment')); 
                                                        }
                                                        $('#' + entityId + '_ec_switcher').fadeIn(); 
                                                        if (data.is_visible == 1) {
                                                          $('#' + entityId + '_ec_window').fadeIn();
                                                        }
                                                    });
                 } else {
                   $('#' + entityId + '_ec_edit').fadeIn();
                 }
               }
             );
  });
  
}

function SaveEntityComment(entityId, entityName) {

  $('#' + entityId + '_ec_save').fadeOut();
  $('#' + entityId + '_ec_cancel').fadeOut();
  
  var body = commentEditor.instanceById(entityId + '_ec_editor').getContent();
  
  $.post( '?__ajaxMethod=generic_SaveEntityComment'
        , { name: entityName, body: body }
        , function(data, textStatus) {  
            commentEditor.removeInstance(entityId + '_ec_editor');
            //commentEditor = null;
            $('#' + entityId + '_ec_edit').fadeIn();
          } 
        );

}

function CancelEntityComment(entityId, entityName) {

  $('#' + entityId + '_ec_save').fadeOut();
  $('#' + entityId + '_ec_cancel').fadeOut();
  
  commentEditor.removeInstance(entityId + '_ec_editor'); 
  //commentEditor = null;
  
  LoadEntityComment(entityId, entityName, true);

}

function showRecordHint(control, className, recordId, procPrefix) {

  save_highslide_caller(control);
  jsoxCall(procPrefix + 'get_record_hint', 'show_record_hint', className, recordId);

}

$(document).ready(function() {

  $('.brw_order_asc,.brw_order_desc,.brw_order_clear').each(function(i) { 
    $(this).addClass('clickable');
    $(this).attr('title', $(this).attr('alt'));
  });
  
  $('.brw_order_asc').bind('click', function(e) {
    var className = $(this).closest('div').attr('id');
    __DoPostBack(className, 'brw_order_asc', $(this).attr('rel'), '', '');
  });

  $('.brw_order_desc').bind('click', function(e) {
    var className = $(this).closest('div').attr('id');
    __DoPostBack(className, 'brw_order_desc', $(this).attr('rel'), '', '');
  });

  $('.brw_order_clear').bind('click', function(e) {
    var className = $(this).closest('div').attr('id');
    __DoPostBack(className, 'brw_order_clear', $(this).attr('rel'), '', '');
  });

});


document.__ui_script_linked = true;

}