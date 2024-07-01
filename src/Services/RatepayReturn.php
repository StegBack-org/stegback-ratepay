<?php

namespace Stegback\Ratepay\Services;
use Stegback\Ratepay\Services\BuildXmlService;
use Stegback\Ratepay\Services\CurlService;
use Illuminate\Http\Request;

class RatepayReturn
{
    protected $profileID;
    protected $securityCode;

    public function __construct()
    {
        $this->profileID = config('ratepay.profile_id');
        $this->securityCode = config('ratepay.SECURITY_CODE');
    }

    public function submitRequest($xmlRequest,$mode){
        $response = (new CurlService)->sendRequest($xmlRequest,$mode);
        return (new RatepayApi)->xmlResponseToArray($response);
    }

    /**
     * 
     */
    public function return(string $type, array $product, string $transaction_id)
    {
        if(!$transaction_id || $transaction_id == '')
        {
            $response = '<content><error code="400">transaction_id is required.</error></content>';
        }else{
               $xmlRequest = (new BuildXmlService)->returnOrCancel([
                'operation' => 'PAYMENT_CHANGE',
                'subtype' => 'return',
                'type' => $type,
                'profileID' => $this->profileID,
                'securityCode' => $this->securityCode,
                'transaction_id' => $transaction_id,
                'product' => $product,
            ]);
            // $response = (new CurlService)->sendRequest($xmlRequest);
            return $xmlRequest;
        }

        $responseArray = (new RatepayApi)->xmlResponseToArray($response);
        return $responseArray;
    }

    /**
     * @param transaction-id should be the order genrated transaction-idd
     */
    public function cancellation(string $type, array $product, string $transaction_id)
    {
        if(!$transaction_id || $transaction_id == '')
        {
            $response = '<content><error code="400">transaction_id is required.</error></content>';
        }else{
               $xmlRequest = (new BuildXmlService)->returnOrCancel([
                'operation' => 'PAYMENT_CHANGE',
                'subtype' => 'cancellation',
                'type' => $type,
                'profileID' => $this->profileID,
                'securityCode' => $this->securityCode,
                'transaction_id' => $transaction_id,
                'product' => $product,
            ]);
            // $response = (new CurlService)->sendRequest($xmlRequest);

            return $xmlRequest;
        }
        $responseArray = (new RatepayApi)->xmlResponseToArray($response);
        return $responseArray;
    }

    public function credit(array $data, string $transaction_id)
    {
        if(!$transaction_id || $transaction_id == '')
        {
            $response = '<content><error code="400">transaction_id is required.</error></content>';
        }else{
            $xmlRequest = (new BuildXmlService)->creditXml([
                'operation' => 'PAYMENT_CHANGE',
                'subtype' => 'credit',
                'profileID' => $this->profileID,
                'securityCode' => $this->securityCode,
                'transaction_id' => $transaction_id,
                'data' => $data,
            ]);
            return $xmlRequest;
            // $response = (new CurlService)->sendRequest($xmlRequest);

            // $responseArray = (new RatepayApi)->xmlResponseToArray($response);
            // return $responseArray;

        }
    }

    public function debit(array $data, string $transaction_id)
    {
        if (!$transaction_id || $transaction_id == '') {
            $response = '<content><error code="400">transaction_id is required.</error></content>';
        } else {
            $xmlRequest = (new BuildXmlService)->debitXml([
                'operation' => 'PAYMENT_CHANGE',
                'subtype' => 'credit',
                'profileID' => $this->profileID,
                'securityCode' => $this->securityCode,
                'transaction_id' => $transaction_id,
                'data' => $data,
            ]);

            return $xmlRequest;

            // $response = (new CurlService)->sendRequest($xmlRequest);
        }
        $responseArray = (new RatepayApi)->xmlResponseToArray($response);
        return $responseArray;
    }


    public function changeOrder($transaction_id, array $NewProduct)
    {
        if(!$transaction_id || $transaction_id == '')
        {
            $response = '<content><error code="400">transaction_id is required.</error></content>';
        }else{
               $xmlRequest = (new BuildXmlService)->returnOrCancel([
                'operation' => 'PAYMENT_CHANGE',
                'subtype' => 'change-order',
                'type' => 'partial',
                'profileID' => $this->profileID,
                'securityCode' => $this->securityCode,
                'transaction_id' => $transaction_id,
                'product' => $NewProduct,
            ]);

            return $xmlRequest;
            // $response = (new CurlService)->sendRequest($xmlRequest);
        }
        $responseArray = (new RatepayApi)->xmlResponseToArray($response);
        return $responseArray;
    }
}