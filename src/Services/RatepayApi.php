<?php

namespace Stegback\Ratepay\Services;
use Stegback\Ratepay\Services\BuildXmlService;
use Stegback\Ratepay\Services\CurlService;
use Illuminate\Http\Request;

class RatepayApi
{
    protected $profileID;
    protected $securityCode;

    public function __construct()
    {
        $this->profileID = env('PROFILE_ID');
        $this->securityCode = env('SECURITY_CODE');
    }


    public function submitRequest($xmlRequest){
        $response = (new CurlService)->sendRequest($xmlRequest);
        return $this->xmlResponseToArray($response);
    }

    public function request($data)
    {
        $method = $data['method'];
        $device_token = $data['device_token'];

        $transactionId = $this->paymentInit();

        if (!$transactionId) {
            return response()->json(['error' => 'Failed to initialize payment.'], 500);
        }

        // Build and send payment request XML
        if ($method == 'INSTALLMENT') {
            $paymentResponse = $this->EmiPaymentRequest($transactionId, $method, $device_token, $data);
        } else {
            $paymentResponse = $this->paymentReq($transactionId, $method, $device_token, $data);
        }

        return response()->json($paymentResponse);
    }

    public function paymentInit()
    {
       $xmlRequest = (new BuildXmlService)->build([
            'operation' => 'PAYMENT_INIT',
            'profileID' => $this->profileID,
            'securityCode' => $this->securityCode,
            'method' => '',
        ]);
        $response = (new CurlService)->sendRequest($xmlRequest);
        $responseArray = $this->xmlResponseToArray($response);
        
        // dd($responseArray);
        return $responseArray['head']['transaction-id']['value'] ?? null;
       
    }

    protected function paymentReq($transactionId, $method, $device_token, $data)
    {
        $xmlRequest = (new BuildXmlService)->build([
            'operation' => 'PAYMENT_REQUEST',
            'method' => $method,
            'profileID' => $this->profileID,
            'securityCode' => $this->securityCode,
            'transactionId' => $transactionId,
            'orderData' => $data['order_data'],
            'device_token' => $device_token,
        ]);

        return $xmlRequest
        // $response = (new CurlService)->sendRequest($xmlRequest);
        // return $this->xmlResponseToArray($response);
    }

    public function paymentCapture($data)
    {
        $transactionId = $data['transactionId'];

        if (!$transactionId) {
            return response()->json(['error' => 'Transaction id is missing.'], 500);
        }
        $xmlRequest = (new BuildXmlService)->build([
            'operation' => 'PAYMENT_CONFIRM',
            'profileID' => $this->profileID,
            'securityCode' => $this->securityCode,
            'transactionId' => $transactionId,
            'method' => '',
            'order_id' => $data['order_id'],
        ]);

        return $xmlRequest;
        // $response = (new CurlService)->sendRequest($xmlRequest);
        // return $this->xmlResponseToArray($response);
    }

    public function EmiPaymentRequest($transactionId, $method, $device_token, $data)
    {
        $xmlRequest = (new BuildXmlService)->emiXmlBuild([
            'operation' => 'PAYMENT_REQUEST',
            'method' => $method,
            'profileID' => $this->profileID,
            'securityCode' => $this->securityCode,
            'transactionId' => $transactionId,
            'orderData' => $data['order_data'],
            'device_token' => $device_token, // Device token should be unique for each device.
        ]);
        return $xmlRequest;
        // $response = (new CurlService)->sendRequest($xmlRequest);
        // return $this->xmlResponseToArray($response);
    }

    

    public function xmlResponseToArray($xmlResponse)
    {
        $xmlObject = mb_convert_encoding($xmlResponse, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');

        $xmlObject = simplexml_load_string($xmlObject, "SimpleXMLElement", LIBXML_NOCDATA);
        return $this->xmlObjectToArray($xmlObject);
    }

    public function xmlObjectToArray($xmlObject)
    {
        $array = [];
        foreach ($xmlObject->attributes() as $attrName => $attrValue) {
            $array['attributes'][$attrName] = (string) $attrValue;
        }
        foreach ($xmlObject as $childName => $child) {
            $childArray = $this->xmlObjectToArray($child);
            if (isset($array[$childName])) {
                if (!is_array($array[$childName])) {
                    $array[$childName] = [$array[$childName]];
                }
                $array[$childName][] = $childArray;
            } else {
                $array[$childName] = $childArray;
            }
        }
        $text = trim((string) $xmlObject);
        if (!empty($text)) {
            $array['value'] = $text;
        }

        return $array;
    }
}