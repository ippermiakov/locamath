/*
   DynAPI Distribution
   Key Event Extensions by Henrik V�glin (hvaglin@yahoo.com)

   The DynAPI Distribution is distributed under the terms of the GNU LGPL license.

   Requirements:
	dynapi.api [dynlayer, dyndocument, browser]
	dynapi.event [listeners]
*/
DynKeyEvent=function() {
	this.DynEvent=DynEvent;
	this.DynEvent();
};
DynKeyEvent.prototype=new DynEvent();
DynKeyEvent.prototype.getKey=function() {
	return this.charKey;
};
DynKeyEvent.prototype.bubbleEvent=function() {
	if (!this.bubble||this.src.isDynDocument||this.src.parent==null) return;
	this.src=this.src.parent;
	this.src.invokeEvent(this.type,this);
	this.bubbleEvent();
	return;
};
DynKeyEvent.EventMethod = function(e) {
	var dynobject=this.lyrobj;
	if(is.def) {
		if (is.ie) var e=dynobject.frame.event;
		else if (e.eventPhase!=3) return false;
	        e.cancelBubble=true;
	}
	if(is.def) var realsrc = Methods.getContainerLayerOf(is.ie?e.srcElement:e.target)||dynobject;
	else if(is.ns4) var realsrc=e.target.lyrobj||dynobject;

	if (!realsrc) { alert('Error in MouseEvents'); return true; }

	var evt=DynKeyEvent._e;
	evt.type=e.type;
	evt.src=realsrc;
	evt.browserReturn=true;
	evt.bubble=true;
	evt.which=(is.ns4)?e.which:e.keyCode;
	var curKey = String.fromCharCode(evt.which).toLowerCase();

	if (((curKey>='a')&&(curKey<='z'))||((curKey>='0')&&(curKey<='9'))) evt.charKey=curKey;
	else evt.charKey=null;

	evt.ctrlKey=(is.ns4)?(e.modifiers & Event.CONTROL_MASK):(e.ctrlKey||e.ctrlLeft||e.keyCode==17);
	evt.shiftKey=(is.ns4)?(e.modifiers & Event.SHIFT_MASK):(e.shiftKey||e.shiftLeft||e.keyCode==16);
	evt.orig=e;

	realsrc.invokeEvent(evt.type,evt);
	evt.bubbleEvent();
	return evt.browserReturn;
};
DynKeyEvent._e=new DynKeyEvent();
DynDocument.prototype.captureKeyEvents=function() {
	if(is.def&&!is.ie) {
		this.doc.addEventListener("keydown",DynKeyEvent.EventMethod,false)
		this.doc.addEventListener("keyup",DynKeyEvent.EventMethod,false)
		this.doc.addEventListener("keypress",DynKeyEvent.EventMethod,false)
	}
	else {
		if (is.ns4) this.doc.captureEvents(Event.KEYPRESS | Event.KEYDOWN | Event.KEYUP);
		this.doc.onkeypress=this.doc.onkeydown=this.doc.onkeyup=DynKeyEvent.EventMethod;
	};
};
DynDocument.prototype.releaseKeyEvents=function() {
	if(is.def&&!is.ie) {
		this.doc.removeEventListener("keydown",DynKeyEvent.EventMethod,false)
		this.doc.removeEventListener("keyup",DynKeyEvent.EventMethod,false)
		this.doc.removeEventListener("keypress",DynKeyEvent.EventMethod,false)
	}
	else {
		if (is.ns4) this.doc.releaseEvents(Event.KEYPRESS | Event.KEYDOWN | Event.KEYUP);
		this.doc.onkeypress=this.doc.onkeydown=this.doc.onkeyup=null;
	};
};
// DynDocument specific
DynDocument.prototype._OldK_specificCreate = DynDocument.prototype.specificCreate;
DynDocument.prototype.specificCreate = function() {
	this._OldK_specificCreate();
	this.captureKeyEvents();
};
DynDocument.prototype._OldK_specificRemove = DynDocument.prototype.specificRemove;
DynDocument.prototype.specificRemove = function() {
	this.releaseKeyEvents();
	this._OldK_specificCreate();
};
