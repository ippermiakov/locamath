var appl_selection = true;
function __jsoxLookupComboCallBack(response, character, id) {

  function getAbsolutePosition(o) {

    var x=0, y=0;
    do {
      x += o.offsetLeft;
      y += o.offsetTop;
    } while ((o = o.offsetParent));

    canvas = document.getElementsByTagName((document.compatMode && document.compatMode == "CSS1Compat") ? "HTML" : "BODY")[0];

    return { x: x, y: y };

  }
    
  d = document;

  text_id  = id + '[text]';
  value_id = id + '[value]';
  list_id  = id + '[list]';

  ctrlText = d.getElementById(text_id);
  ctrlText.ctrlCombo.options.length = 0;
  options = response.split(";");
  for (i = 0; i < options.length; i++) {
    values = options[i].split(",");
    if (values[0]) {
      var option = d.createElement("option");
      option.value = values[0];
      if (window.navigator.userAgent.indexOf("MSIE") != -1)
        option.innerText = values[1];
      else
        option.text      = values[1];
      ctrlText.ctrlCombo.appendChild(option);
    }
  }

  var sz = Math.min(ctrlText.ctrlCombo.options.length, 10);
  if (sz < 2)
    sz = 2;
  ctrlText.ctrlCombo.size = sz;
  
  if (ctrlText.ctrlCombo.options.length == 0) 
    ctrlText.ctrlCombo.style.display  = 'none';
  else {
    o = getAbsolutePosition(ctrlText);
    ctrlText.ctrlCombo.style.display  = 'inline';
	
	if (window.jQuery) {
      c = $(ctrlText.ctrlCombo).children().mousemove(
		  function(){
			 //alert($(this).val());
			 $(this).attr('selected','selected');
		}
		);	  
    }


    ctrlText.ctrlCombo.style.left     = o.x;
    ctrlText.ctrlCombo.style.top      = o.y + ctrlText.offsetHeight;
  }

  if ((ctrlText.ctrlCombo.options.length == 1)&&(appl_selection)) {
    ctrlText.ctrlCombo.selectedIndex = 0;
    ctrlText.applySelection();
  }

}

function makeLookupCombo(id, ajax_method, show_if_selected, min_length, text_allowed, on_select, on_enter_pressed, ajax_param, apply_selection) {

  if(typeof(apply_selection) == 'undefined'){
    apply_selection = true;
  }
  appl_selection = apply_selection;
  d = document;               

  text_id  = id + '[text]';
  value_id = id + '[value]';
  list_id  = id + '[list]';
  
  var ctrlText  = d.getElementById(text_id);
  var ctrlValue = d.getElementById(value_id);
  if(d.getElementById(list_id) == null){    
    var ctrlCombo = d.createElement('select');
  } else {    
    var ctrlCombo = d.getElementById(list_id);
  }
  
  ctrlText.setAttribute('autocomplete', 'off');
  
//  ctrlCombo.className      = 'lookup_values_combo';
  ctrlCombo.style.position = 'absolute';
  ctrlCombo.style.display  = 'none';
  ctrlCombo.style.height   = '100px';
  ctrlCombo.id             = list_id;
  ctrlCombo.name           = list_id;

  ctrlText.ctrlCombo       = ctrlCombo;
  ctrlText.ctrlValue       = ctrlValue;
  if (text_allowed)
    //ctrlText.className     = 'lookup_complete';
    ctrlText.style.color             = '#000000';
  else  
  if (ctrlValue.value)
    //ctrlText.className     = 'lookup_complete';
    ctrlText.style.color             = '#000000';
  else
    //ctrlText.className     = 'lookup_incomplete';
    ctrlText.style.color = '#888888';
  ctrlText.style.width      = '220px';
  ctrlText.show_if_selected = show_if_selected;
  ctrlText.last_value       = ctrlText.value;
  ctrlText.on_enter_pressed = on_enter_pressed;
  ctrlText.on_select        = on_select;
  ctrlText.min_length       = min_length;
  ctrlText.text_allowed     = text_allowed;

  ctrlCombo.ctrlText = ctrlText;

  ctrlText.applySelection = function() {
    if (window.navigator.userAgent.indexOf("MSIE") != -1)
      this.value                 = this.ctrlCombo.options[this.ctrlCombo.selectedIndex].innerText;
    else
      this.value                 = this.ctrlCombo.options[this.ctrlCombo.selectedIndex].text;
	
	
    this.last_value              = this.value;
    //this.className               = 'lookup_complete';
    this.style.color             = '#000000';
    this.ctrlValue.value         = this.ctrlCombo.value;
    this.ctrlCombo.style.display = 'none';

    if (this.show_if_selected) {
      var control = document.getElementById(this.show_if_selected);
      if (control) {
        control.style.display = 'inline';
		
        var inputs = control.getElementsByTagName('INPUT');
        for(var i = 0; i < inputs.length; i++) {
          if (inputs[i].type != 'hidden') {
            inputs[i].focus();
            break;
          }
        }
      }
    }
    
    if (this.on_select)
      eval(this.on_select);
  }

  ctrlText.clearSelection = function() {
    this.value = '';
    this.ctrlValue.value = '';
    if(!ctrlComboFocus){
      this.ctrlCombo.style.display = 'none';
    }
    if (!this.text_allowed)
      //this.className = 'lookup_incomlete';
      this.color = '#888888';
  }

  ctrlText.onfocus = function() {
    this.select();
  }
  var ctrlComboFocus = false;
  ctrlCombo.onfocus = function() {      
    ctrlComboFocus = true;
    this.select();
  }

  ctrlText.onkeyup = function(e) {

    if (this.last_value != this.value) {
      this.ctrlValue.value = '';
      if (!this.text_allowed)
        this.color = '#888888';
//        this.className = 'lookup_incomlete';
      this.last_value = this.value;
      if (this.value) {
        if (this.value.length >= this.min_length) {
          jsoxCall(ajax_method, '__jsoxLookupComboCallBack', this.value, id, ajax_param);
        } 
      } else
        this.clearSelection();  
    } else {
      e = e || window.event;
      if (e.keyCode == 13)
        if (this.on_enter_pressed)
          eval(this.on_enter_pressed);
    }    
  }

  ctrlText.onkeydown = function(e) {   
    e = e || window.event;
//  alert(e);    
    if (this.ctrlCombo.style.display == 'inline') {
      if (e.keyCode == 40)
        this.ctrlCombo.focus();
    }
  }

  //ctrlCombo.ondblclick = function(e) {
  ctrlCombo.onclick = function(e) {
    this.ctrlText.applySelection();
  }

  ctrlCombo.onkeyup = function(e) {
    e = e || window.event;
    if (e.keyCode == 13) 
      this.ctrlText.applySelection();
  }

  ctrlText.onblur = function() {
    if (this.ctrlCombo.style.display == 'inline')
      if (document.activeElement && (document.activeElement.id != this.ctrlCombo.id)){
	      	setTimeout('functionCtrlTextBlur("'+this.id+'")', 1);
		}
        //this.clearSelection();
  }
  
  functionCtrlTextBlur = function(mid){
    if(!ctrlComboFocus && (document.activeElement.id != (mid.substr(0, mid.length-6)+'[list]'))){	          
	  document.getElementById(mid.substr(0, mid.length-6)+'[list]').style.display = 'none';	  	  
	} //else alert(6);
  }

  ctrlText.ondeactivate = function(e) {
    //if (this.ctrlCombo.style.display == 'inline')
    //  if (document.activeElement && (document.activeElement.id != this.ctrlCombo.id))
    //    this.clearSelection();
  }

  ctrlCombo.ondeactivate = function(e) {
    if (this.style.display == 'inline')
      if (document.activeElement && (document.activeElement.id != this.ctrlText.id))
        this.ctrlText.clearSelection();
  }

  ctrlCombo.onblur = function(e) {
    ctrlComboFocus = false;  
    if (this.style.display == 'inline')
      if (document.activeElement && (document.activeElement.id != this.ctrlText.id)){        
	     this.style.display = 'none';
      }
        //this.ctrlText.clearSelection();
  }

  ctrlText.parentNode.insertBefore(ctrlCombo, ctrlText);

}
