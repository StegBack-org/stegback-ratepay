<?php

namespace Stegback\Ratepay\Services;

class BuildXmlService
{
    public function build($data)
    {
        $operation = $data['operation'];
        $method = $data['method'];
        $profileID = htmlspecialchars($data['profileID']);
        $securityCode = htmlspecialchars($data['securityCode']);
        $transactionId = $data['transactionId'] ?? '';

        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlString .= '<request version="1.0" xmlns="urn://www.ratepay.com/payment/1_0">';

        $xmlString .= '<head>
        <system-id>'.env('SHOP_NAME').'</system-id>
        <operation>' . $operation . '</operation>
        <credential>
            <profile-id>' . $profileID . '</profile-id>
            <securitycode>' . $securityCode . '</securitycode>
        </credential>';

        if (!empty($transactionId)) {
            $xmlString .= '<transaction-id>' . htmlspecialchars($transactionId) . '</transaction-id>';
        }

        switch ($operation) {
            case 'PAYMENT_INIT':

                $xmlString .= '</head>';
                break;
            case 'PAYMENT_REQUEST':

                $xmlString .= '
                                <customer-device>
                                    <device-token>' . $data['device_token'] . '</device-token>
                                </customer-device>
                            </head>';
                $xmlString .=
                    '<content>
                    <customer>
                        <first-name>' . $data['orderData']['firstname'] . '</first-name>
                        <last-name>' . $data['orderData']['lastname'] . '</last-name>
                        <gender>' . $data['orderData']['gender'] . '</gender>
                        <date-of-birth>' . $data['orderData']['dob'] . '</date-of-birth>
                        <ip-address>' . $data['orderData']['ip_address'] . '</ip-address>
                        <contacts>
                            <email>' . $data['orderData']['email'] . '</email>
                            <phone>
                                <direct-dial>' . $data['orderData']['phone'] . '</direct-dial>
                            </phone>
                        </contacts>
                        <addresses>
                            <address type="BILLING">
                                <street><![CDATA[' . $data['orderData']['address'] . ']]></street>
                                <street-number><![CDATA[' . $data['orderData']['street'] . ']]></street-number>
                                <zip-code>' . $data['orderData']['zip'] . '</zip-code>
                                <city><![CDATA[' . $data['orderData']['city'] . ']]></city>
                                <country-code>' . $data['orderData']['country'] . '</country-code>
                            </address>
                            <address type="DELIVERY">
                                <street><![CDATA[' . $data['orderData']['shipaddress'] . ']]></street>
                                <street-number><![CDATA[' . $data['orderData']['shipstreet'] . ']]></street-number>
                                <zip-code>' . $data['orderData']['shipzip'] . '</zip-code>
                                <city><![CDATA[' . $data['orderData']['shipcity'] . ']]></city>
                                <country-code>' . $data['orderData']['country'] . '</country-code>
                            </address>
                        </addresses>
                        <nationality>' . $data['orderData']['country_code'] . '</nationality>
                    <customer-allow-credit-inquiry>yes</customer-allow-credit-inquiry>';

                switch ($method) {
                    case 'INVOICE':
                        break;
                    case 'ELV':
                        $xmlString .=
                            '<bank-account>
                                <owner>' . $data['orderData']['owner'] . '</owner>
                                <iban>' . $data['orderData']['iban'] . '</iban>

                            </bank-account>
                            ';
                        break;
                }

                $xmlString .= '</customer>';

                $xmlString .=
                    '<shopping-basket amount="' . $data['orderData']['totalAmount'] . '" currency="EUR">
                    <items>
                    ';

                foreach ($data['orderData']['product'] as $product) {
                    $xmlString .=
                        '<item article-number="' . $product['id'] . '" quantity="' . $product['quantity'] . '" tax-rate="19" unit-price-gross="' . $product['price'] . '">' . $product['name'] . '</item>';
                }

                $xmlString .= '
                    </items>
                        <discount unit-price-gross="-' . $data['orderData']['discountAmount'] . '" tax-rate="19">Rabatt</discount>
                        <shipping unit-price-gross="' . $data['orderData']['shippingAmount'] . '" tax-rate="19">Versandkosten</shipping>
                    </shopping-basket>
                    <payment currency="EUR" method="' . $method . '">
                        <amount>' . $data['orderData']['totalAmount'] . '</amount>
                    </payment>
                    </content>';
                break;

            case 'PAYMENT_CONFIRM':
                $xmlString .=
                    '<external>
                        <order-id>' . $data['order_id'] . '</order-id>
                    </external>';

                $xmlString .= '</head>';

                break;
        }
        $xmlString .= '</request>';
        return $xmlString;
    }

    public function calculation($data)
    {
        $operation = $data['operation'];
        $profileID = htmlspecialchars($data['profileID']);
        $securityCode = htmlspecialchars($data['securityCode']);

        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlString .= '<request version="1.0" xmlns="urn://www.ratepay.com/payment/1_0">';

        $xmlString .= '<head>
        <system-id>'.env('SHOP_NAME').'</system-id>
        <operation subtype="calculation-by-' . $data['type'] . '">' . $operation . '</operation>
        <credential>
            <profile-id>' . $profileID . '</profile-id>
            <securitycode>' . $securityCode . '</securitycode>
        </credential>';

        switch ($operation) {
            case 'CONFIGURATION_REQUEST':
                $xmlString .= '</head>';
                break;

            case 'CALCULATION_REQUEST':
                $xmlString .= '</head>';
                $xmlString .=
                    '<content>
                    <installment-calculation>
                        <amount>' . $data['amount'] . '</amount>';

                switch ($data['type']) {
                    case 'time':
                        $xmlString .= '<calculation-time>
                            <month>' . $data['value'] . '</month>
                        </calculation-time>';
                        break;
                    case 'rate':
                        $xmlString .= '<calculation-rate>
                            <rate>' . $data['value'] . '</rate>
                        </calculation-rate>';
                        break;
                }

                $xmlString .= '</installment-calculation>
                    </content>';
                break;
        }
        $xmlString .= '</request>';
        return $xmlString;
    }

    public function emiXmlBuild($data)
    {
        $operation = $data['operation'];
        $profileID = htmlspecialchars($data['profileID']);
        $securityCode = htmlspecialchars($data['securityCode']);
        $transactionId = $data['transactionId'] ?? '';

        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlString .= '<request version="1.0" xmlns="urn://www.ratepay.com/payment/1_0">';
        $xmlString .= '<head>
                        <system-id>'.env('SHOP_NAME').'</system-id>
                        <operation>' . $operation . '</operation>
                        <credential>
                            <profile-id>' . $profileID . '</profile-id>
                            <securitycode>' . $securityCode . '</securitycode>
                        </credential>';

        if (!empty($transactionId)) {
            $xmlString .= '<transaction-id>' . htmlspecialchars($transactionId) . '</transaction-id>';
        }

        $xmlString .= '<external>
                            <merchant-consumer-id>123</merchant-consumer-id>
                        </external>
                        <customer-device>
                            <device-token>' . $data['device_token'] . '</device-token>
                        </customer-device>
                        </head>';
        $xmlString .= '<content>
                        <customer>
                            <first-name>' . $data['orderData']['firstname'] . '</first-name>
                            <last-name>' . $data['orderData']['lastname'] . '</last-name>
                            <gender>' . $data['orderData']['gender'] . '</gender>
                            <date-of-birth>' . $data['orderData']['dob'] . '</date-of-birth>
                            <ip-address>' . $data['orderData']['ip_address'] . '</ip-address>
                            <contacts>
                                <email>' . $data['orderData']['email'] . '</email>
                                <phone>
                                    <direct-dial>' . $data['orderData']['phone'] . '</direct-dial>
                                </phone>
                            </contacts>
                            <addresses>
                                <address type="BILLING">
                                    <street><![CDATA[' . $data['orderData']['address'] . ']]></street>
                                    <street-number><![CDATA[' . $data['orderData']['street'] . ']]></street-number>
                                    <zip-code>' . $data['orderData']['zip'] . '</zip-code>
                                    <city><![CDATA[' . $data['orderData']['city'] . ']]></city>
                                    <country-code>' . $data['orderData']['country'] . '</country-code>
                                </address>
                                <address type="DELIVERY">
                                    <street><![CDATA[' . $data['orderData']['shipaddress'] . ']]></street>
                                    <street-number><![CDATA[' . $data['orderData']['shipstreet'] . ']]></street-number>
                                    <zip-code>' . $data['orderData']['shipzip'] . '</zip-code>
                                    <city><![CDATA[' . $data['orderData']['shipcity'] . ']]></city>
                                    <country-code>' . $data['orderData']['country'] . '</country-code>
                                </address>
                            </addresses>
                            <nationality>' . $data['orderData']['country_code'] . '</nationality>
                        <customer-allow-credit-inquiry>yes</customer-allow-credit-inquiry>';

        $xmlString .= '<bank-account>
                                <owner>' . $data['orderData']['owner'] . '</owner>
                                <iban>' . $data['orderData']['iban'] . '</iban>

                            </bank-account>
                        </customer>';

        $xmlString .=
            '<shopping-basket amount="' . $data['orderData']['shipping_basket'] . '" currency="EUR">
                        <items>';

        foreach ($data['orderData']['product'] as $product) {
            $xmlString .=
                '<item article-number="' . $product['id'] . '" quantity="' . $product['quantity'] . '" tax-rate="19" unit-price-gross="' . $product['price'] . '">' . $product['name'] . '</item>';
        }

        $xmlString .= '
                        </items>
                        <discount unit-price-gross="-' . $data['orderData']['discountAmount'] . '" tax-rate="19">Rabatt</discount>
                        <shipping unit-price-gross="' . $data['orderData']['shippingAmount'] . '" tax-rate="19">Versandkosten</shipping>
                        </shopping-basket>';

        $xmlString .= '<payment currency="EUR" method="' . $data['method'] . '">
                                <amount>' . $data['orderData']['amount'] . '</amount>
                                <installment-details>
                                    <installment-number>' . $data['orderData']['installment_number'] . '</installment-number>
                                    <installment-amount>' . $data['orderData']['installment_amount'] . '</installment-amount>
                                    <last-installment-amount>' . $data['orderData']['last_installment_amount'] . '</last-installment-amount>
                                    <interest-rate>' . $data['orderData']['interest_rate'] . '</interest-rate>
                                    <payment-firstday>' . $data['orderData']['payment_firstday'] . '</payment-firstday>
                                </installment-details>
                                <debit-pay-type>DIRECT-DEBIT</debit-pay-type>
                        </payment>
                        </content>';
        $xmlString .= '</request>';
        return $xmlString;
    }

    public function shippingXml($data)
    {
        $operation = $data['operation'];
        $profileID = htmlspecialchars($data['profileID']);
        $securityCode = htmlspecialchars($data['securityCode']);
        $transactionId = $data['transactionId'] ?? '';
        $order_id = $data['order_id'] ?? '';
        $transactionId = $data['transactionId'] ?? '';

        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlString .= '<request version="1.0" xmlns="urn://www.ratepay.com/payment/1_0">';

        $xmlString .=
            '<head>
                <system-id>'.env('SHOP_NAME').'</system-id>
                <transaction-id>' . $transactionId . '</transaction-id>
                <operation>' . $operation . '</operation>
                <credential>
                    <profile-id>' . $profileID . '</profile-id>
                    <securitycode>' . $securityCode . '</securitycode>
                </credential>
                <external>
                    <order-id>' . $order_id . '</order-id>
                    <tracking>
                        <id provider="' . $data['tracking_data']['shipping_provider'] . '">' . $data['tracking_data']['tracking_number'] . '</id>
                    </tracking>
                </external>
                <meta>
                    <systems>
                        <api-version>1.8</api-version>
                    </systems>
                </meta>
            </head>';

        $xmlString .=
            '<content>
                <invoicing>
                    <invoice-id>' . $data['tracking_data']['invoice_id'] . '</invoice-id>
                </invoicing>
                <shopping-basket amount="' . $data['tracking_data']['totalAmount'] . '" currency="EUR">
                    <items>';

        foreach ($data['tracking_data']['products'] as $product) {
            $xmlString .= '<item article-number="' . $product['id'] . '" quantity="' . $product['quantity'] . '" tax-rate="19" unit-price-gross="' . $product['cost_per_item'] . '">' . $product['product_name'] . '</item>';
        }

        $xmlString .=
            '</items>
                    <discount unit-price-gross="-' . $data['tracking_data']['discountAmount'] . '" tax-rate="19">Rabatt</discount>
                    <shipping unit-price-gross="' . $data['tracking_data']['shippingAmount'] . '" tax-rate="19">Versandkosten</shipping>
                </shopping-basket>
            </content>';

        $xmlString .= '</request>';
        return $xmlString;
    }


    public function returnOrCancel(array $data)
    {
        $transactionId = $data['transaction_id'];
        
        $profileID = htmlspecialchars($data['profileID']);
        $securityCode = htmlspecialchars($data['securityCode']);

        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlString .= '<request version="1.0" xmlns="urn://www.ratepay.com/payment/1_0">';

        $xmlString .= '<head><system-id>myshop</system-id>';
        $xmlString .= '<transaction-id>'.$data['transaction_id'].'</transaction-id>';
        switch ($data['subtype']) {
            case 'cancellation':
                $xmlString .= '<operation subtype="cancellation">' . $data['operation'] . '</operation>';
                break;
            case 'return':
                $xmlString .= '<operation subtype="return">' . $data['operation'] . '</operation>';
            }
        $xmlString .= '<credential>
                            <profile-id>' . $profileID . '</profile-id>
                            <securitycode>' . $securityCode . '</securitycode>
                        </credential>';

        $xmlString .= '</head>';

        $xmlString .= '<content>';

        $xmlString .= '<shopping-basket amount="'.$data['product']['total_amount'].'" currency="EUR">';

        switch ($data['type']) {
            case 'partial':
                $xmlString .= '<items>';

                foreach($data['product']['item'] as $item){
                    $xmlString .= '<item article-number="'.$item['product_id'].'" quantity="'.$item['product_quantity'].'" tax-rate="19" unit-price-gross="'.$item['product_price'].'">'.$item['product_name'].'</item>';
                }

                 $xmlString .= '</items>
                                <discount unit-price-gross="-'.$data['product']['discount'].'" tax-rate="19">Rabatt</discount>';
                break;
            case 'full':
                $xmlString .= '<items/>';
            }
            
        $xmlString .= '</shopping-basket></content></request>';
        return $xmlString;
    }
}
