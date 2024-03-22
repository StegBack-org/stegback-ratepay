<?php

namespace Stegback\Ratepay\Services;
use Stegback\Ratepay\Services\BuildXmlService;
use Stegback\Ratepay\Services\CurlService;
use Illuminate\Http\Request;
use Stegback\Ratepay\Services\RatepayApi;

class RatepayInstallmentApi
{
    protected $profileID;
    protected $securityCode;

    public function __construct()
    {
        $this->profileID = env('PROFILE_ID');
        $this->securityCode = env('SECURITY_CODE');
    }

    public function submitRequest($xmlRequest,$mode){
        $response = (new CurlService)->sendRequest($xmlRequest,$mode);
        return (new RatepayApi)->xmlResponseToArray($response);
    }

    public function configurationRequest($data)
    {
        $type = $data['type'];
        $xmlRequest = (new BuildXmlService)->calculation([
            'operation' => 'CONFIGURATION_REQUEST',
            'profileID' =>  $this->profileID,
            'securityCode' => $this->securityCode,
            'type' => $type ?? 'rate',
            'amount' => 500,
        ]);
        return $xmlRequest;
        // $response = (new CurlService)->sendRequest($xmlRequest);
        // $responseArray = (new RatepayApi)->xmlResponseToArray($response);
        // return response()->json($responseArray);
    }


    /**
     * @param amount
     * @param type time | rate
     * @param integer value number of month or emi
     */
    public function EMICalculationRequest($request)
    {
        $amount = $request['amount'];
        $type = $request['type'];
        $value = $request['value'];

        $xmlRequest = (new BuildXmlService)->calculation([
            'operation' => 'CALCULATION_REQUEST',
            'profileID' => $this->profileID,
            'securityCode' => $this->securityCode,
            'amount' => $amount,
            'value' => $value,
            'type' => $type,
        ]);
        // dd($xmlRequest);
        return $xmlRequest
        // $response = (new CurlService)->sendRequest($xmlRequest);
        // $responseArray = (new RatepayApi)->xmlResponseToArray($response);
        // return response()->json($responseArray);
    }
}