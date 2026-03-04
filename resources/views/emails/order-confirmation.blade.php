<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="x-apple-disable-message-reformatting" />
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no" />
    <title>Order Confirmation - {{ $order->order_number }}</title>
    <style type="text/css">
        /* Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; height: 100% !important; }

        /* Responsive */
        @media only screen and (max-width: 620px) {
            .email-container { width: 100% !important; max-width: 100% !important; }
            .responsive-table { width: 100% !important; }
            .mobile-padding { padding-left: 16px !important; padding-right: 16px !important; }
            .mobile-stack { display: block !important; width: 100% !important; }
            .product-image { width: 80px !important; height: 80px !important; }
            .hide-mobile { display: none !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    <!-- Preview Text -->
    <div style="display: none; max-height: 0; overflow: hidden; mso-hide: all;">
        Your order {{ $order->order_number }} has been confirmed! Thank you for shopping with Romano.
        &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>

    <!-- Email Wrapper -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f4f7;">
        <tr>
            <td align="center" style="padding: 24px 16px;">

                <!-- Email Container -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" class="email-container" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

                    <!-- ============================================ -->
                    <!-- HEADER -->
                    <!-- ============================================ -->
                    <tr>
                        <td align="center" style="background-color: #C4B4A0; padding: 32px 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <img src="{{ asset('cp_assets/logos/Romano-TopHeader-Logo.png') }}" alt="Romano" height="100" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- ============================================ -->
                    <!-- GREETING -->
                    <!-- ============================================ -->
                    <tr>
                        <td style="padding: 32px 40px 16px 40px;" class="mobile-padding">
                            <p style="margin: 0 0 8px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 600; color: #1a1a2e;">
                                Hello {{ $order->user ? $order->user->name : ($order->guest_name ?? 'Valued Customer') }},
                            </p>
                            <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 22px; color: #64748b;">
                                Thank you for your order! We've received your order and it is now being processed. Below are the details of your purchase.
                            </p>
                        </td>
                    </tr>

                    <!-- ============================================ -->
                    <!-- ORDER INFO CARD -->
                    <!-- ============================================ -->
                    <tr>
                        <td style="padding: 8px 40px 24px 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                            <!-- Order Number -->
                                            <tr>
                                                <td style="padding-bottom: 12px;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                                        <tr>
                                                            <td width="50%" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                                                                Order Number
                                                            </td>
                                                            <td width="50%" align="right" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                                                                Order Date
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="50%" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 700; color: #1a1a2e; padding-top: 4px;">
                                                                {{ $order->order_number }}
                                                            </td>
                                                            <td width="50%" align="right" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #475569; padding-top: 4px;">
                                                                {{ $order->created_at->format('M d, Y - h:i A') }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <!-- Divider -->
                                            <tr>
                                                <td style="padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">&nbsp;</td>
                                            </tr>
                                            <!-- Status Badges -->
                                            <tr>
                                                <td style="padding-top: 12px;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                                        <tr>
                                                            <td width="50%" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                                                                Order Status
                                                            </td>
                                                            <td width="50%" align="right" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                                                                Payment Status
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="50%" style="padding-top: 8px;">
                                                                @php
                                                                    $statusColors = [
                                                                        'pending'    => ['bg' => '#fef3c7', 'text' => '#92400e', 'border' => '#fcd34d'],
                                                                        'processing' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'border' => '#93c5fd'],
                                                                        'shipped'    => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'border' => '#a5b4fc'],
                                                                        'delivered'  => ['bg' => '#dcfce7', 'text' => '#166534', 'border' => '#86efac'],
                                                                        'cancelled'  => ['bg' => '#fee2e2', 'text' => '#991b1b', 'border' => '#fca5a5'],
                                                                    ];
                                                                    $statusColor = $statusColors[$order->status] ?? $statusColors['pending'];
                                                                @endphp
                                                                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                                    <tr>
                                                                        <td style="background-color: {{ $statusColor['bg'] }}; border: 1px solid {{ $statusColor['border'] }}; border-radius: 20px; padding: 4px 14px;">
                                                                            <span style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; color: {{ $statusColor['text'] }}; text-transform: capitalize;">
                                                                                {{ $order->status }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td width="50%" align="right" style="padding-top: 8px;">
                                                                @php
                                                                    $paymentColors = [
                                                                        'pending'  => ['bg' => '#fef3c7', 'text' => '#92400e', 'border' => '#fcd34d'],
                                                                        'paid'     => ['bg' => '#dcfce7', 'text' => '#166534', 'border' => '#86efac'],
                                                                        'failed'   => ['bg' => '#fee2e2', 'text' => '#991b1b', 'border' => '#fca5a5'],
                                                                        'refunded' => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'border' => '#a5b4fc'],
                                                                    ];
                                                                    $paymentColor = $paymentColors[$order->payment_status] ?? $paymentColors['pending'];
                                                                @endphp
                                                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-left: auto;">
                                                                    <tr>
                                                                        <td style="background-color: {{ $paymentColor['bg'] }}; border: 1px solid {{ $paymentColor['border'] }}; border-radius: 20px; padding: 4px 14px;">
                                                                            <span style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; color: {{ $paymentColor['text'] }}; text-transform: capitalize;">
                                                                                {{ $order->payment_status }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Order Link in frontend website -->
                    <tr>
                        <td style="padding: 8px 40px 24px 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                    @php
                                        $paymentRoutes = [
                                            'pending'  => 'pending',
                                            'paid'     => 'success',
                                            'failed'   => 'failure',
                                            'refunded' => 'failure',
                                        ];
                                        $frontendBase = config('app.frontend_url', env('FRONTEND_URL', ''));
                                        if ($order->user) {
                                            $order_url = $frontendBase . '/profile/orders';
                                        } else {
                                            $statusPath = $paymentRoutes[$order->payment_status] ?? 'pending';
                                            $order_url = $frontendBase . '/checkout/' . $statusPath
                                                . '?order=' . $order->order_number
                                                . '&token=' . $order->access_token;
                                        }
                                    @endphp

                                        <a href="{{ $order_url }}"
                                        target="_blank"
                                        style="text-align: center;color: #000;background-color: #C4B4A0;
                                        padding: 8px 16px;border-radius: 4px;
                                        text-decoration: none;
                                        font-size: 14px;
                                        font-weight: 600;
                                        ">Order Link</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ============================================ -->
                    <!-- ORDER ITEMS SECTION -->
                    <!-- ============================================ -->
                    <tr>
                        <td style="padding: 0 40px 8px 40px;" class="mobile-padding">
                            <h2 style="margin: 0 0 16px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 700; color: #1a1a2e; border-bottom: 2px solid #1a1a2e; padding-bottom: 8px;">
                                Order Items
                            </h2>
                        </td>
                    </tr>

                    <!-- Items Table Header -->
                    <tr>
                        <td style="padding: 0 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-bottom: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 8px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;" width="55%">
                                        Product
                                    </td>
                                    <td align="center" style="padding: 8px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;" width="15%">
                                        Qty
                                    </td>
                                    <td align="center" style="padding: 8px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;" width="15%">
                                        Price
                                    </td>
                                    <td align="right" style="padding: 8px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;" width="15%">
                                        Subtotal
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Items Rows -->
                    @foreach($order->orderItems as $item)
                    <tr>
                        <td style="padding: 0 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-bottom: 1px solid #f1f5f9;">
                                <tr>
                                    <!-- Product Details -->
                                    <td width="55%" style="padding: 16px 8px 16px 0; vertical-align: middle;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                            <tr>
                                                @if($item->image)
                                                <td width="64" valign="top" style="padding-right: 12px;">
                                                    <img src="{{  $item->image }}" alt="{{ $item->product_name }}" width="60" height="60" class="product-image" style="display: block; border-radius: 6px; object-fit: cover; border: 1px solid #e2e8f0;" />
                                                </td>
                                                @endif
                                                <td valign="middle">
                                                    <p style="margin: 0 0 4px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 600; color: #1e293b; line-height: 18px;">
                                                        {{ $item->product_name }}
                                                    </p>
                                                    <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; color: #94a3b8;">
                                                        SKU: {{ $item->product_sku }}
                                                    </p>
                                                    @if($item->color || $item->size)
                                                    <p style="margin: 4px 0 0 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; color: #64748b;">
                                                        @if($item->color)
                                                            <span style="display: inline-block; background-color: #f1f5f9; border-radius: 4px; padding: 1px 8px; margin-right: 4px;">{{ $item->color }}</span>
                                                        @endif
                                                        @if($item->size)
                                                            <span style="display: inline-block; background-color: #f1f5f9; border-radius: 4px; padding: 1px 8px;">{{ $item->size }}</span>
                                                        @endif
                                                    </p>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <!-- Quantity -->
                                    <td width="15%" align="center" valign="middle" style="padding: 16px 4px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #475569;">
                                        {{ $item->quantity }}
                                    </td>
                                    <!-- Unit Price -->
                                    <td width="15%" align="center" valign="middle" style="padding: 16px 4px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #475569;">
                                        {{ number_format($item->price, 2) }}
                                    </td>
                                    <!-- Subtotal -->
                                    <td width="15%" align="right" valign="middle" style="padding: 16px 0 16px 4px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 600; color: #1e293b;">
                                        {{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endforeach

                    <!-- ============================================ -->
                    <!-- ORDER SUMMARY -->
                    <!-- ============================================ -->
                    <tr>
                        <td style="padding: 24px 40px 0 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td width="50%">&nbsp;</td>
                                    <td width="50%">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                            <!-- Subtotal -->
                                            <tr>
                                                <td style="padding: 6px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #64748b;">
                                                    Subtotal
                                                </td>
                                                <td align="right" style="padding: 6px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #1e293b;">
                                                    {{ number_format($order->subtotal, 2) }} {{ $order->currency }}
                                                </td>
                                            </tr>
                                            <!-- Shipping -->
                                            <tr>
                                                <td style="padding: 6px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #64748b;">
                                                    Shipping
                                                </td>
                                                <td align="right" style="padding: 6px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #1e293b;">
                                                    @if((float) $order->shipping_cost > 0)
                                                        {{ number_format($order->shipping_cost, 2) }} {{ $order->currency }}
                                                    @else
                                                        <span style="color: #16a34a; font-weight: 600;">Free</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <!-- Discount (if applicable) -->
                                            @if((float) $order->discount_amount > 0)
                                            <tr>
                                                <td style="padding: 6px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #16a34a;">
                                                    Discount
                                                    @if($order->discountCode)
                                                        <span style="font-size: 12px; background-color: #dcfce7; border-radius: 4px; padding: 1px 6px; color: #166534;">({{ $order->discountCode->code }})</span>
                                                    @endif
                                                </td>
                                                <td align="right" style="padding: 6px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #16a34a; font-weight: 600;">
                                                    -{{ number_format($order->discount_amount, 2) }} {{ $order->currency }}
                                                </td>
                                            </tr>
                                            @endif
                                            <!-- Total Divider -->
                                            <tr>
                                                <td colspan="2" style="padding: 8px 0 0 0; border-bottom: 2px solid #1a1a2e;">&nbsp;</td>
                                            </tr>
                                            <!-- Total -->
                                            <tr>
                                                <td style="padding: 12px 0 4px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 700; color: #1a1a2e;">
                                                    Total
                                                </td>
                                                <td align="right" style="padding: 12px 0 4px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 700; color: #1a1a2e;">
                                                    {{ number_format($order->total, 2) }} {{ $order->currency }}
                                                </td>
                                            </tr>
                                            <!-- Total in base currency if different -->
                                            @if($order->currency !== 'QAR' && $order->total_in_base_currency)
                                            <tr>
                                                <td colspan="2" align="right" style="padding: 2px 0 0 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; color: #94a3b8;">
                                                    &#8776; {{ number_format($order->total_in_base_currency, 2) }} QAR
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ============================================ -->
                    <!-- PAYMENT & SHIPPING METHOD -->
                    <!-- ============================================ -->
                    <tr>
                        <td style="padding: 32px 40px 0 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <!-- Payment Method -->
                                    <td width="48%" valign="top" class="mobile-stack" style="padding-right: 2%;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <tr>
                                                <td style="padding: 16px;">
                                                    <p style="margin: 0 0 8px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Payment Method
                                                    </p>
                                                    <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 600; color: #1e293b; text-transform: capitalize;">
                                                        {{ str_replace('_', ' ', $order->payment_method ?? 'N/A') }}
                                                    </p>
                                                    @if($order->payment_transaction_id)
                                                    <p style="margin: 4px 0 0 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; color: #94a3b8;">
                                                        Txn: {{ $order->payment_transaction_id }}
                                                    </p>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <!-- Shipping Method -->
                                    <td width="48%" valign="top" class="mobile-stack" style="padding-left: 2%;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <tr>
                                                <td style="padding: 16px;">
                                                    <p style="margin: 0 0 8px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Shipping Method
                                                    </p>
                                                    <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 600; color: #1e293b; text-transform: capitalize;">
                                                        {{ str_replace(['_', '-'], ' ', $order->shipping_method ?? 'N/A') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ============================================ -->
                    <!-- ADDRESSES -->
                    <!-- ============================================ -->
                    <tr>
                        <td style="padding: 24px 40px 0 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <!-- Shipping Address -->
                                    @if($order->shippingAddress)
                                    <td width="48%" valign="top" class="mobile-stack" style="padding-right: 2%;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <tr>
                                                <td style="padding: 16px;">
                                                    <p style="margin: 0 0 12px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Shipping Address
                                                    </p>
                                                    <p style="margin: 0 0 2px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 600; color: #1e293b;">
                                                        {{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}
                                                    </p>
                                                    @if($order->shippingAddress->company)
                                                    <p style="margin: 0 0 2px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px; color: #64748b;">
                                                        {{ $order->shippingAddress->company }}
                                                    </p>
                                                    @endif
                                                    <p style="margin: 0 0 2px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px; color: #64748b; line-height: 20px;">
                                                        {{ $order->shippingAddress->address }}
                                                        @if($order->shippingAddress->apartment)
                                                            <br>{{ $order->shippingAddress->apartment }}
                                                        @endif
                                                        <br>{{ $order->shippingAddress->city }}@if($order->shippingAddress->postal_code), {{ $order->shippingAddress->postal_code }}@endif
                                                        <br>{{ $order->shippingAddress->country }}
                                                    </p>
                                                    @if($order->shippingAddress->phone)
                                                    <p style="margin: 6px 0 0 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px; color: #64748b;">
                                                        &#9742; {{ $order->shippingAddress->phone }}
                                                    </p>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    @endif
                                    <!-- Billing Address -->
                                    @if($order->billingAddress)
                                    <td width="48%" valign="top" class="mobile-stack" style="padding-left: 2%;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            <tr>
                                                <td style="padding: 16px;">
                                                    <p style="margin: 0 0 12px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Billing Address
                                                    </p>
                                                    <p style="margin: 0 0 2px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 600; color: #1e293b;">
                                                        {{ $order->billingAddress->first_name }} {{ $order->billingAddress->last_name }}
                                                    </p>
                                                    @if($order->billingAddress->company)
                                                    <p style="margin: 0 0 2px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px; color: #64748b;">
                                                        {{ $order->billingAddress->company }}
                                                    </p>
                                                    @endif
                                                    <p style="margin: 0 0 2px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px; color: #64748b; line-height: 20px;">
                                                        {{ $order->billingAddress->address }}
                                                        @if($order->billingAddress->apartment)
                                                            <br>{{ $order->billingAddress->apartment }}
                                                        @endif
                                                        <br>{{ $order->billingAddress->city }}@if($order->billingAddress->postal_code), {{ $order->billingAddress->postal_code }}@endif
                                                        <br>{{ $order->billingAddress->country }}
                                                    </p>
                                                    @if($order->billingAddress->phone)
                                                    <p style="margin: 6px 0 0 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px; color: #64748b;">
                                                        &#9742; {{ $order->billingAddress->phone }}
                                                    </p>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ============================================ -->
                    <!-- ORDER NOTES (if any) -->
                    <!-- ============================================ -->
                    @if($order->notes)
                    <tr>
                        <td style="padding: 24px 40px 0 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #fffbeb; border-radius: 8px; border: 1px solid #fde68a;">
                                <tr>
                                    <td style="padding: 16px;">
                                        <p style="margin: 0 0 6px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; font-weight: 600; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Order Notes
                                        </p>
                                        <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #78350f; line-height: 22px;">
                                            {{ $order->notes }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- ============================================ -->
                    <!-- PAYMENT URL BUTTON (if pending) -->
                    <!-- ============================================ -->
                    @if($order->payment_status === 'pending' && $order->payment_url)
                    <tr>
                        <td style="padding: 32px 40px 0 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #fff7ed; border-radius: 8px; border: 1px solid #fed7aa;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="margin: 0 0 16px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; color: #9a3412; font-weight: 600;">
                                            Your payment is pending. Please complete your payment to process your order.
                                        </p>
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center">
                                            <tr>
                                                <td align="center" style="background-color: #1a1a2e; border-radius: 6px;">
                                                    <a href="{{ $order->payment_url }}" target="_blank" style="display: inline-block; padding: 14px 32px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 6px;">
                                                        Complete Payment
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- ============================================ -->
                    <!-- HELP SECTION -->
                    <!-- ============================================ -->
                    <tr>
                        <td style="padding: 32px 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-top: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding-top: 24px; text-align: center;">
                                        <p style="margin: 0 0 8px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 600; color: #1e293b;">
                                            Need Help?
                                        </p>
                                        <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px; color: #64748b; line-height: 22px;">
                                            If you have any questions about your order, feel free to reply to this email or contact our support team.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
                <!-- End Email Container -->

                <!-- ============================================ -->
                <!-- FOOTER -->
                <!-- ============================================ -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" class="email-container" style="max-width: 600px; width: 100%;">
                    <tr>
                        <td style="padding: 24px 40px;" class="mobile-padding">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 8px 0; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 18px; font-weight: 700; color: #C4B4A0; letter-spacing: 4px; text-transform: uppercase;">
                                            ROMANO
                                        </p>
                                        <p style="margin: 0 0 16px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; color: #94a3b8; line-height: 18px;">
                                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                                        </p>
                                        <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; color: #cbd5e1; line-height: 18px;">
                                            This email was sent to you because you placed an order on our website.<br>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
    <!-- End Email Wrapper -->

</body>
</html>
