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

        $response = (new CurlService)->sendRequest($xmlRequest);
        $responseArray = (new RatepayApi)->xmlResponseToArray($response);
        return response()->json($responseArray);
    }


    public function EmiCalculationRequest(Request $request)
    {
       
        $amount = $request->input('amount');
        $type = $request->input('type');
        $value = $request->input('value');

        $xmlRequest = (new BuildXmlService)->calculation([
            'operation' => 'CALCULATION_REQUEST',
            'profileID' => $this->profileID,
            'securityCode' => $this->securityCode,
            'amount' => $amount,
            'value' => $value,
            'type' => $type,
        ]);
        // dd($xmlRequest);
        $response = (new CurlService)->sendRequest($xmlRequest);
        $responseArray = (new RatepayApi)->xmlResponseToArray($response);
        return response()->json($responseArray);
    }
}