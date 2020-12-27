<?php

DEFINE('USPS_IMAGE_TYPE_NONE', 0);
DEFINE('USPS_IMAGE_TYPE_PDF',  1);
DEFINE('USPS_IMAGE_TYPE_GIF',  2);
DEFINE('USPS_IMAGE_TYPE_TIF',  3);

DEFINE('USPS_API_MODE_TEST', 0);
DEFINE('USPS_API_MODE_LIVE', 1);

DEFINE('USPS_API_PROTOCOL_SSL', 0);
DEFINE('USPS_API_PROTOCOL_NONSSL', 1);

class usps {
  
  var $urls = array()
    , $errors = array()
    , $response
    , $USERID
    , $mode = USPS_API_MODE_TEST
    , $protocols = array()
    , $ApiNames = array()
    , $RequestNames = array()
    ;
  
  function usps($USERID, $mode = USPS_API_MODE_TEST) {

    $this->USERID = $USERID;
    $this->mode = $mode;
    
    $this->urls[USPS_API_PROTOCOL_SSL][USPS_API_MODE_TEST] = 'https://secure.shippingapis.com/ShippingAPITest.dll';
    $this->urls[USPS_API_PROTOCOL_SSL][USPS_API_MODE_LIVE] = 'https://secure.shippingapis.com/ShippingAPI.dll';
    $this->urls[USPS_API_PROTOCOL_NONSSL][USPS_API_MODE_TEST] = 'http://testing.shippingapis.com/ShippingAPITest.dll';
    $this->urls[USPS_API_PROTOCOL_NONSSL][USPS_API_MODE_LIVE] = 'http://production.shippingapis.com/ShippingAPI.dll';

    $this->init();
    
  }
  
  function init() {
    
  }
  
  function pack_header() {
    
    return '<'.$this->RequestNames[$this->mode].' USERID="'.$this->USERID.'">';
    
  }
  
  function pack() {
    
  }
  
  function unpack($response) {
    
    if (preg_match('/<Error>(.+)<\/Error>/mis', $response, $args))
      if (preg_match('/<Description>([^>]+)<\/Description>/mis', $args[1], $args2))
        $this->errors[] = $args2[1];
      else 
        $this->errors[] = 'Uknown error';
    return !count($this->errors);
    
  }
  
  function pack_footer() {

      return '</'.$this->RequestNames[$this->mode].'>';

  }
  
  function process() {

    $this->errors = array();

    $uri = $this->urls[$this->protocols[$this->mode]][$this->mode].'?API='.$this->ApiNames[$this->mode].'&XML='.urlencode($this->pack());
    
    $ch = curl_init($uri);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $this->response = curl_exec($ch); 

    if (!$this->response) {
      $this->errors[] = curl_error($ch).' ('.curl_errno($ch).')';
      return false;
    } else {
      return $this->unpack($this->response);
    }

  }
  
}

class usps_custom_shipping_label extends usps {
  
  var $Option
    , $ImageParameters
    , $FromFirm
    , $FromFirstName
    , $FromLastName
    , $FromAddress1
    , $FromAddress2
    , $FromCity
    , $FromState
    , $FromZip5
    , $FromZip4
    , $FromPhone
    , $ToFirm
    , $ToFirstName
    , $ToLastName
    , $ToAddress1
    , $ToAddress2
    , $ToCity
    , $ToState
    , $ToZip5
    , $ToZip4
    , $ToPhone
    , $SeparateReceiptPage = false
    , $POZipCode
    , $ImageType = USPS_IMAGE_TYPE_GIF
    , $LabelDate
    , $CustomerRefNo
    , $SenderName
    , $SenderEMail
    , $RecipientName
    , $RecipientEMail
    , $LabelNumber
    , $LabelBody;
  
  function SaveLabelTo($file_name = null, $add_extension = true) {
    
    if ($this->LabelBody) {
      if (!$file_name)
        $file_name = $this->LabelNumber;
      if ($add_extension)
        switch ($this->ImageType) {
          case USPS_IMAGE_TYPE_GIF:
            $file_name .= '.gif';
            break;
          case USPS_IMAGE_TYPE_TIF:
            $file_name .= '.tif';
            break;
          case USPS_IMAGE_TYPE_PDF:
            $file_name .= '.pdf';
            break;
        }
      file_put_contents($file_name, $this->LabelBody);
      return $file_name;
    } else
      return false;
    
  }
  
}

class usps_express_mail_label extends usps_custom_shipping_label {
  
  var $EMCAAccount
    , $EMCAPassword
    , $FromPhone
    , $ToPhone
    , $WeightInOunces
    , $FlatRate = true
    , $StandardizeAddress = true
    , $WaiverOfSignature = false
    , $NoHoliday = false
    , $NoWeekend = false;
  
  function init() {
    
    $this->ApiNames[USPS_API_MODE_TEST] = 'ExpressMailLabelCertify';
    $this->ApiNames[USPS_API_MODE_LIVE] = 'ExpressMailLabel';
    
    $this->RequestNames[USPS_API_MODE_TEST] = 'ExpressMailLabelCertifyRequest';
    $this->RequestNames[USPS_API_MODE_LIVE] = 'ExpressMailLabelRequest';
    
    $this->protocols[USPS_API_MODE_TEST] = USPS_API_PROTOCOL_SSL;
    $this->protocols[USPS_API_MODE_LIVE] = USPS_API_PROTOCOL_SSL;
    
  }
  
  function pack() {

    return $this->pack_header().'<Option /><EMCAAccount /><EMCAPassword /><ImageParameters /><FromFirstName>'.$this->FromFirstName.'</FromFirstName><FromLastName>'.$this->FromLastName.'</FromLastName><FromFirm>'.$this->FromFirm.'</FromFirm><FromAddress1>'.$this->FromAddress1.'</FromAddress1><FromAddress2>'.$this->FromAddress2.'</FromAddress2><FromCity>'.$this->FromCity.'</FromCity><FromState>'.$this->FromState.'</FromState><FromZip5>'.$this->FromZip5.'</FromZip5><FromZip4>'.$this->FromZip4.'</FromZip4><FromPhone>'.$this->FromPhone.'</FromPhone><ToFirstName>'.$this->ToFirstName.'</ToFirstName><ToLastName>'.$this->ToLastName.'</ToLastName><ToFirm>'.$this->ToFirm.'</ToFirm><ToAddress1>'.$this->ToAddress1.'</ToAddress1><ToAddress2>'.$this->ToAddress2.'</ToAddress2><ToCity>'.$this->ToCity.'</ToCity><ToState>'.$this->ToState.'</ToState><ToZip5>'.$this->ToZip5.'</ToZip5><ToZip4>'.$this->ToZip4.'</ToZip4><ToPhone>'.$this->ToPhone.'</ToPhone><WeightInOunces>'.$this->WeightInOunces.'</WeightInOunces><FlatRate>'.($this->FlatRate?'TRUE':'FALSE').'</FlatRate><StandardizeAddress>'.($this->StandardizeAddress?'TRUE':'FALSE').'</StandardizeAddress><WaiverOfSignature>'.($this->WaiverOfSignature?'TRUE':'FALSE').'</WaiverOfSignature><NoHoliday>'.($this->NoHoliday?'TRUE':'FALSE').'</NoHoliday><NoWeekend>'.($this->NoWeekend?'TRUE':'FALSE').'</NoWeekend><SeparateReceiptPage>'.($this->SeparateReceiptPage?'TRUE':'FALSE').'</SeparateReceiptPage><POZipCode>'.$this->POZipCode.'</POZipCode><ImageType>'.($this->ImageType==USPS_IMAGE_TYPE_GIF?'GIF':($this->ImageType==USPS_IMAGE_TYPE_PDF?'PDF':'NONE')).'</ImageType><LabelDate>'.($this->LabelDate?date('d-M-Y', $this->LabelDate):'').'</LabelDate><CustomerRefNo>'.$this->CustomerRefNo.'</CustomerRefNo><SenderName>'.$this->SenderName.'</SenderName><SenderEMail>'.$this->SenderEMail.'</SenderEMail><RecipientName>'.$this->RecipientName.'</RecipientName><RecipientEMail>'.$this->RecipientEMail.'</RecipientEMail>'.$this->pack_footer();
    
  }
  
  function unpack($response) {
    
    $result = parent::unpack($response);
    
    if ($result) {
      if (preg_match('/<EMConfirmationNumber>([^>]+)<\/EMConfirmationNumber>/mis', $response, $args)) {
        $this->EMConfirmationNumber = $args[1];
        $this->LabelNumber = $args[1];
      } else
        $this->errors[] = 'EMConfirmationNumber not returned';
      if (($this->ImageType == USPS_IMAGE_TYPE_GIF) or ($this->ImageType == USPS_IMAGE_TYPE_PDF)) {
        if (preg_match('/<EMLabel>([^>]+)<\/EMLabel>/mis', $response, $args)) {
          $this->EMLabel = base64_decode($args[1]);
          $this->LabelBody = $args[1];
        } else
          $this->errors[] = 'EMLabel not returned';
      }
    }
    
    return !count($this->errors);
        
  }
  
}

class usps_delivery_confirmation_label extends usps_custom_shipping_label {
  
  var $AddressServiceRequested = false
    , $FromName
    , $ToName
    , $ServiceType = 1
    , $DeliveryConfirmationNumber
    ;
  
  function init() {
  
    $this->ApiNames[USPS_API_MODE_TEST] = 'DelivConfirmCertifyV3';
    $this->ApiNames[USPS_API_MODE_LIVE] = 'DeliveryConfirmationV3';
    
    $this->RequestNames[USPS_API_MODE_TEST] = 'DelivConfirmCertifyV3.0Request';
    $this->RequestNames[USPS_API_MODE_LIVE] = 'DeliveryConfirmationV3.0Request';

    $this->protocols[USPS_API_MODE_TEST] = USPS_API_PROTOCOL_SSL;
    $this->protocols[USPS_API_MODE_LIVE] = USPS_API_PROTOCOL_SSL;
    
    $this->Option = 1;

  }
  
  function pack() {

    return $this->pack_header().'<Option>'.$this->Option.'</Option><ImageParameters /><FromName>'.($this->FromName?$this->FromName:$this->FromFirstName.' '.$this->FromLastName).'</FromName><FromFirm>'.$this->FromFirm.'</FromFirm><FromAddress1>'.$this->FromAddress1.'</FromAddress1><FromAddress2>'.$this->FromAddress2.'</FromAddress2><FromCity>'.$this->FromCity.'</FromCity><FromState>'.$this->FromState.'</FromState><FromZip5>'.$this->FromZip5.'</FromZip5><FromZip4>'.$this->FromZip4.'</FromZip4><ToName>'.($this->ToName?$this->ToName:$this->ToFirstName.' '.$this->ToLastName).'</ToName><ToFirm>'.$this->ToFirm.'</ToFirm><ToAddress1>'.$this->ToAddress1.'</ToAddress1><ToAddress2>'.$this->ToAddress2.'</ToAddress2><ToCity>'.$this->ToCity.'</ToCity><ToState>'.$this->ToState.'</ToState><ToZip5>'.$this->ToZip5.'</ToZip5><ToZip4>'.$this->ToZip4.'</ToZip4><WeightInOunces>'.$this->WeightInOunces.'</WeightInOunces><ServiceType>'.$this->ServiceType.'</ServiceType><SeparateReceiptPage>'.($this->SeparateReceiptPage?'TRUE':'FALSE').'</SeparateReceiptPage><POZipCode>'.$this->POZipCode.'</POZipCode><ImageType>'.($this->ImageType==USPS_IMAGE_TYPE_GIF?'GIF':($this->ImageType==USPS_IMAGE_TYPE_PDF?'PDF':($this->ImageType==USPS_IMAGE_TYPE_TIF?'TIF':'NONE'))).'</ImageType><LabelDate>'.($this->LabelDate?date('d-M-Y', $this->LabelDate):'').'</LabelDate><CustomerRefNo>'.$this->CustomerRefNo.'</CustomerRefNo><AddressServiceRequested>'.($this->AddressServiceRequested?'TRUE':'FALSE').'</AddressServiceRequested><SenderName>'.$this->SenderName.'</SenderName><SenderEMail>'.$this->SenderEMail.'</SenderEMail><RecipientName>'.$this->RecipientName.'</RecipientName><RecipientEMail>'.$this->RecipientEMail.'</RecipientEMail>'.$this->pack_footer();
    
  }
  
  function unpack($response) {
    
    $result = parent::unpack($response);
    
    if ($result) {
      if (preg_match('/<DeliveryConfirmationNumber>([^>]+)<\/DeliveryConfirmationNumber>/mis', $response, $args)) {
        $this->DeliveryConfirmationNumber = $args[1];
        $this->LabelNumber = $args[1];
      } else
        $this->errors[] = 'DeliveryConfirmationNumber not returned';
      if (preg_match('/<Postnet>([^>]+)<\/Postnet>/mis', $response, $args))
        $this->Postnet = $args[1];
      if (($this->ImageType == USPS_IMAGE_TYPE_PDF) or ($this->ImageType == USPS_IMAGE_TYPE_TIF)) {
        if (preg_match('/<DeliveryConfirmationLabel>([^>]+)<\/DeliveryConfirmationLabel>/mis', $response, $args))
          $this->LabelBody = base64_decode($args[1]);
        else
          $this->errors[] = 'DeliveryConfirmationLabel not returned';
      }
    }
    
    return !count($this->errors);
        
  }
  
}

class usps_signature_confirmation_label extends usps_custom_shipping_label {
  
  var $AddressServiceRequested = false
    , $FromName
    , $ToName
    , $ServiceType = 1
    , $SignatureConfirmationNumber
    , $WaiverOfSignature = false
    ;
  
  function init() {
  
    $this->ApiNames[USPS_API_MODE_TEST] = 'SignatureConfirmationCertifyV3';
    $this->ApiNames[USPS_API_MODE_LIVE] = 'SignatureConfirmationV3';
    
    $this->RequestNames[USPS_API_MODE_TEST] = 'SigConfirmCertifyV3.0Request';
    $this->RequestNames[USPS_API_MODE_LIVE] = 'SignatureConfirmationV3.0Request';

    $this->protocols[USPS_API_MODE_TEST] = USPS_API_PROTOCOL_SSL;
    $this->protocols[USPS_API_MODE_LIVE] = USPS_API_PROTOCOL_SSL;
    
    $this->Option = 1;

  }
  
  function pack() {

    return $this->pack_header().'<Option>'.$this->Option.'</Option><ImageParameters /><FromName>'.($this->FromName?$this->FromName:$this->FromFirstName.' '.$this->FromLastName).'</FromName><FromFirm>'.$this->FromFirm.'</FromFirm><FromAddress1>'.$this->FromAddress1.'</FromAddress1><FromAddress2>'.$this->FromAddress2.'</FromAddress2><FromCity>'.$this->FromCity.'</FromCity><FromState>'.$this->FromState.'</FromState><FromZip5>'.$this->FromZip5.'</FromZip5><FromZip4>'.$this->FromZip4.'</FromZip4><ToName>'.($this->ToName?$this->ToName:$this->ToFirstName.' '.$this->ToLastName).'</ToName><ToFirm>'.$this->ToFirm.'</ToFirm><ToAddress1>'.$this->ToAddress1.'</ToAddress1><ToAddress2>'.$this->ToAddress2.'</ToAddress2><ToCity>'.$this->ToCity.'</ToCity><ToState>'.$this->ToState.'</ToState><ToZip5>'.$this->ToZip5.'</ToZip5><ToZip4>'.$this->ToZip4.'</ToZip4><WeightInOunces>'.$this->WeightInOunces.'</WeightInOunces><ServiceType>'.$this->ServiceType.'</ServiceType><WaiverOfSignature>'.($this->WaiverOfSignature?'TRUE':'FALSE').'</WaiverOfSignature><SeparateReceiptPage>'.($this->SeparateReceiptPage?'TRUE':'FALSE').'</SeparateReceiptPage><POZipCode>'.$this->POZipCode.'</POZipCode><ImageType>'.($this->ImageType==USPS_IMAGE_TYPE_GIF?'GIF':($this->ImageType==USPS_IMAGE_TYPE_PDF?'PDF':($this->ImageType==USPS_IMAGE_TYPE_TIF?'TIF':'NONE'))).'</ImageType><LabelDate>'.($this->LabelDate?date('d-M-Y', $this->LabelDate):'').'</LabelDate><CustomerRefNo>'.$this->CustomerRefNo.'</CustomerRefNo><AddressServiceRequested>'.($this->AddressServiceRequested?'TRUE':'FALSE').'</AddressServiceRequested><SenderName>'.$this->SenderName.'</SenderName><SenderEMail>'.$this->SenderEMail.'</SenderEMail><RecipientName>'.$this->RecipientName.'</RecipientName><RecipientEMail>'.$this->RecipientEMail.'</RecipientEMail>'.$this->pack_footer();
    
  }
  
  function unpack($response) {
    
    $result = parent::unpack($response);
    
    if ($result) {
      if (preg_match('/<SignatureConfirmationNumber>([^>]+)<\/SignatureConfirmationNumber>/mis', $response, $args)) {
        $this->SignatureConfirmationNumber = $args[1];
        $this->LabelNumber = $args[1];
      } else
        $this->errors[] = 'SignatureConfirmationNumber not returned';
      if (preg_match('/<Postnet>([^>]+)<\/Postnet>/mis', $response, $args))
        $this->Postnet = $args[1];
      if (($this->ImageType == USPS_IMAGE_TYPE_PDF) or ($this->ImageType == USPS_IMAGE_TYPE_TIF)) {
        if (preg_match('/<SignatureConfirmationLabel>([^>]+)<\/SignatureConfirmationLabel>/mis', $response, $args))
          $this->LabelBody = base64_decode($args[1]);
        else
          $this->errors[] = 'SignatureConfirmationLabel not returned';
      }
    }
    
    return !count($this->errors);
        
  }
  
}

class usps_address_validate extends usps {
  
  var $FirmName
    , $Address1
    , $Address2
    , $City
    , $State
    , $Zip5
    , $Zip4;
  
  function init() {
    
    $this->ApiNames[USPS_API_MODE_TEST] = 'Verify';
    $this->ApiNames[USPS_API_MODE_LIVE] = 'Verify';
    
    $this->RequestNames[USPS_API_MODE_TEST] = 'AddressValidateRequest';
    $this->RequestNames[USPS_API_MODE_LIVE] = 'AddressValidateRequest';

    $this->protocols[USPS_API_MODE_TEST] = USPS_API_PROTOCOL_NONSSL;
    $this->protocols[USPS_API_MODE_LIVE] = USPS_API_PROTOCOL_NONSSL;
    
  }
  
  function pack() {

    return $this->pack_header().'<Address ID="0"><Address1>'.$this->Address1.'</Address1><Address2>'.$this->Address2.'</Address2><City>'.$this->City.'</City><State>'.$this->State.'</State><Zip5>'.$this->Zip5.'</Zip5><Zip4>'.$this->Zip4.'</Zip4></Address>'.$this->pack_footer();
    
  }
  
  function unpack($response) {
    
    $result = parent::unpack($response);
    
    if ($result) {
      if (preg_match('/<Address1>([^>]+)<\/Address1>/mis', $response, $args))
        $this->Address1 = $args[1];
      if (preg_match('/<Address2>([^>]+)<\/Address2>/mis', $response, $args))
        $this->Address2 = $args[1];
      if (preg_match('/<City>([^>]+)<\/City>/mis', $response, $args))
        $this->City = $args[1];
      if (preg_match('/<State>([^>]+)<\/State>/mis', $response, $args))
        $this->State = $args[1];
      if (preg_match('/<Zip4>([^>]+)<\/Zip4>/mis', $response, $args))
        $this->Zip4 = $args[1];
      if (preg_match('/<Zip5>([^>]+)<\/Zip5>/mis', $response, $args))
        $this->Zip5 = $args[1];
    }
    
    return !count($this->errors);

        
  }
 
}

?>