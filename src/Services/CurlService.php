<?php

namespace Stegback\Ratepay\Services;

class CurlService
{
    public $apiUrl;
    public function __construct()
    {
        $this->apiUrl = 'https://gateway-int.ratepay.com/api/xml/1_0';
       
    }


    public function sendRequest($reqXML)
    {
        try {
            $connection = curl_init();
            curl_setopt($connection, CURLOPT_URL, $this->apiUrl);
            curl_setopt($connection, CURLOPT_HTTPHEADER, array('Content-Type: application/xml;charset=UTF-8'));
            curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($connection, CURLOPT_POST, true);
            curl_setopt($connection, CURLOPT_POSTFIELDS, $reqXML);
            curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($connection);
            curl_close($connection);
            return $response;
        } catch (\Exception $e) {

            return null;
        }
    }
}