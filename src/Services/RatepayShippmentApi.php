<?php

namespace Stegback\Ratepay\Services;
use Stegback\Ratepay\Services\BuildXmlService;
use Stegback\Ratepay\Services\CurlService;
use Illuminate\Http\Request;
use Stegback\Ratepay\Services\RatepayApi;

class RatepayShippmentApi
{
    protected $profileID;
    protected $securityCode;

    public function __construct()
    {
        $this->profileID = env('PROFILE_ID');
        $this->securityCode = env('SECURITY_CODE');
    }

    public function deliveryConfirm($data)
    {
        $transactionId = $data['transactionId'];
        $order_id = $data['order_id'];
        $paymentResponse = $this->addShippingBuildXml($transactionId, $order_id, $data);
        return response()->json($paymentResponse);
    }


    protected function addShippingBuildXml($transactionId, $order_id, $data)
    {
        $xmlRequest = (new BuildXmlService)->shippingXml([
            'operation' => 'CONFIRMATION_DELIVER',
            'profileID' => $this->profileID,
            'securityCode' => $this->securityCode,
            'transactionId' => $transactionId,
            'order_id' => $order_id,
            'tracking_data' => $data,
        ]);

        return $xmlRequest;
        // $response = (new CurlService)->sendRequest($xmlRequest);
        // return (new RatepayApi)->xmlResponseToArray($response);
    }

    public function submitRequest($xmlRequest){
        $response = (new CurlService)->sendRequest($xmlRequest);
        return (new RatepayApi)->xmlResponseToArray($response);
    }

}