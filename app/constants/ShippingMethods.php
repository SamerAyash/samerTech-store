<?php

namespace App\Constants;

use App\Services\CurrencyService;
use Illuminate\Http\Request;

class ShippingMethods
{
    private static $shipping_methods = [
        [
            'id'=> 'aramex',
            'name'=> 'Aramex',
            'cost'=> 118, //this cost by QAT currency
            'notes'=> [
                'en'=> 'Customs clearance fees are not included and must be borne by the customer. Additional shipping charges will apply for shipments over 2 kg, and our team will contact you if applicable.',
                'ar'=> 'رسوم التخليص الجمركي غير مشمولة ويجب على العميل تحملها. سيتم تطبيق رسوم شحن إضافية للشحنات التي تزيد عن 2 كجم، وسيتصل فريقنا بك إذا كان ذلك ينطبق.'
            ]
        ],
        [
            'id'=> 'dhl',
            'name'=> 'DHL',
            'cost'=> 120, //this cost by QAT currency
            'notes'=> [
                'en'=> 'Customs clearance fees are not included and must be borne by the customer. Additional shipping charges will apply for shipments over 2 kg, and our team will contact you if applicable.',
                'ar'=> 'رسوم التخليص الجمركي غير مشمولة ويجب على العميل تحملها. سيتم تطبيق رسوم شحن إضافية للشحنات التي تزيد عن 2 كجم، وسيتصل فريقنا بك إذا كان ذلك ينطبق.'
            ]
        ]
    ];
    
    public static function getShippingMethods($currency = 'QAR', $locale = 'en', ?Request $request = null)
    {
        $currency = $currency ?? 'QAR';
        $shipping_methods = self::$shipping_methods;
        $currencyService = app(CurrencyService::class);
        
        foreach ($shipping_methods as &$shipping_method) {
            $shipping_method['cost'] = $currencyService->convertFromQAR($shipping_method['cost'], $currency, $request);
            $shipping_method['note'] = $shipping_method['notes'][$locale];
            unset($shipping_method['notes']);
        }
        
        return $shipping_methods;
    }

    public static function getShippingCost($shipping_method , $currency = 'QAR'){
        $currency = $currency ?? 'QAR';
        $shipping_methods = self::$shipping_methods;
        $currencyService = app(CurrencyService::class);
        $ids = array_column($shipping_methods, 'id');
        $index = array_search($shipping_method, $ids);
        $cost = 0;
        if ($index !== false) {
            $cost = $shipping_methods[$index]['cost'];
        }
        $cost = $currencyService->convertFromQAR($cost, $currency);
        return round($cost, 2);
    }
}
