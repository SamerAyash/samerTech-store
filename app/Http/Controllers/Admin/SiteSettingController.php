<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Stevebauman\Purify\Facades\Purify;

class SiteSettingController extends Controller
{
    /**
     * Display the site settings form.
     */
    public function index()
    {
        $shipping_information_en = SiteSetting::firstOrCreate(
            ['key' => 'shipping_information_en'],
            ['value' => null, 'is_active' => true]
        );
        
        $shipping_information_ar = SiteSetting::firstOrCreate(
            ['key' => 'shipping_information_ar'],
            ['value' => null, 'is_active' => true]
        );
        
        $returns_exchanges_en = SiteSetting::firstOrCreate(
            ['key' => 'returns_exchanges_en'],
            ['value' => null, 'is_active' => true]
        );
        
        $returns_exchanges_ar = SiteSetting::firstOrCreate(
            ['key' => 'returns_exchanges_ar'],
            ['value' => null, 'is_active' => true]
        );
        
        $payment_options_security_en = SiteSetting::firstOrCreate(
            ['key' => 'payment_options_security_en'],
            ['value' => null, 'is_active' => true]
        );
        
        $payment_options_security_ar = SiteSetting::firstOrCreate(
            ['key' => 'payment_options_security_ar'],
            ['value' => null, 'is_active' => true]
        );

        return view('admin.content-settings', compact(
            'shipping_information_en',
            'shipping_information_ar',
            'returns_exchanges_en',
            'returns_exchanges_ar',
            'payment_options_security_en',
            'payment_options_security_ar'
        ));
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'shipping_information_en' => 'nullable|string',
            'shipping_information_ar' => 'nullable|string',
            'returns_exchanges_en' => 'nullable|string',
            'returns_exchanges_ar' => 'nullable|string',
            'payment_options_security_en' => 'nullable|string',
            'payment_options_security_ar' => 'nullable|string',
        ]);

        // Update Shipping Information
        $shippingInfoEn = SiteSetting::firstOrCreate(
            ['key' => 'shipping_information_en'],
            ['value' => null]
        );
        $shippingInfoEn->value = Purify::clean($request->input('shipping_information_en'));
        $shippingInfoEn->save();

        $shippingInfoAr = SiteSetting::firstOrCreate(
            ['key' => 'shipping_information_ar'],
            ['value' => null]
        );
        $shippingInfoAr->value = Purify::clean($request->input('shipping_information_ar'));
        $shippingInfoAr->save();

        // Update Returns & Exchanges
        $returnsExchangesEn = SiteSetting::firstOrCreate(
            ['key' => 'returns_exchanges_en'],
            ['value' => null]
        );
        $returnsExchangesEn->value = Purify::clean($request->input('returns_exchanges_en'));
        $returnsExchangesEn->save();

        $returnsExchangesAr = SiteSetting::firstOrCreate(
            ['key' => 'returns_exchanges_ar'],
            ['value' => null]
        );
        $returnsExchangesAr->value = Purify::clean($request->input('returns_exchanges_ar'));
        $returnsExchangesAr->save();

        // Update Payment Options & Security
        $paymentOptionsEn = SiteSetting::firstOrCreate(
            ['key' => 'payment_options_security_en'],
            ['value' => null]
        );
        $paymentOptionsEn->value = Purify::clean($request->input('payment_options_security_en'));
        $paymentOptionsEn->save();

        $paymentOptionsAr = SiteSetting::firstOrCreate(
            ['key' => 'payment_options_security_ar'],
            ['value' => null]
        );
        $paymentOptionsAr->value = Purify::clean($request->input('payment_options_security_ar'));
        $paymentOptionsAr->save();
        foreach (['ar', 'en'] as $key) {
            Cache::forget('content_site_' . $key);
        }
        return back()->with('success', 'Site settings updated successfully.');
    }
}

