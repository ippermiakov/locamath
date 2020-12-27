<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class In_app_purchase{

    protected $_isSandbox = false;
    protected $_error = null;

    public function getError(){
        return $this->_error;
    } 

    public function prepareData($receipt){
        if(strpos($receipt,'{') !== false){
            $receipt = base64_encode($receipt);
        }
        return $receipt;
    }

    public function validateReceipt($receipt){

        $request = $this->prepareData($receipt);

        if ($this->_isSandbox) {
            $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
        } else {
            $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
        }

        $postData = json_encode(
            array('receipt-data' => $receipt)
        );
       
        $ch = curl_init($endpoint);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
 
        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $errmsg   = curl_error($ch);
        curl_close($ch);

        if ($errno == 0) {
            $data = json_decode($response);
            if (is_object($data)) {
                if (!isset($data->status) || $data->status != 0){
                    $this->_error = 'Invalid receipt';
                }else{
                    return array(
                        'quantity'       =>  $data->receipt->quantity,
                        'product_id'     =>  $data->receipt->product_id,
                        'transaction_id' =>  $data->receipt->transaction_id,
                        'purchase_date'  =>  $data->receipt->purchase_date,
                        'app_item_id'    =>  $data->receipt->app_item_id,
                        'bid'            =>  $data->receipt->bid,
                        'bvrs'           =>  $data->receipt->bvrs
                    );
                }
            }else{
                $this->_error = 'Invalid response data';
            }
        }else{
            $this->_error = $errmsg;
        }
        return false;
    }
}
