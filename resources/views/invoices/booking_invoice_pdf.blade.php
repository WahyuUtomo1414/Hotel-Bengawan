<!DOCTYPE html>
<html>
<head>
    <title>{{ $gtext['site_name'] }} - Invoice {{ $booking->booking_no }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* Your existing CSS styles */
        body { font-family: 'dejavusans', sans-serif; font-size: 9px; color: #686868; } /* Ensure font is available or use a web-safe font */
        .w-100 {width: 100%;}
        .w-75 {width: 75%;}
        .w-60 {width: 60%;}
        .w-50 {width: 50%;}
        .w-40 {width: 40%;}
        .w-35 {width: 35%;}
        .w-30 {width: 30%;}
        .w-25 {width: 25%;}
        .w-20 {width: 20%;}
        .w-15 {width: 15%;}
        .w-10 {width: 10%;}
        table td, table th {
            color: #686868;
            text-decoration: none;
        }
        a {
            color: #686868;
            text-decoration: none;
        }
        table.border td, table.border th {
            border: 1px solid #f0f0f0;
        }
        table.border-tb td, table.border-tb th {
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
        }
        table.border-header td {
            border-bottom: 1px solid #f0f0f0;
        }
        table.border-t td, table.border-t th {
            border-top: 1px solid #f0f0f0;
        }
        table.border-none td, table.border-none th {
            border: none;
        }
        .company-logo img{
            width: 100px;
            height: auto;
        }
        td.invoice-name {
            font-size: 25px;
            font-weight: 600;
            text-align: right;
        }
        p.com-address {
            line-height: 5px;
        }
        h3, h4 {
            line-height: 10px;
        }
        p {
            line-height: 5px;
        }
        h3 {
            font-size: 16px;
        }
        h4 {
            font-size: 12px;
            margin-bottom: 0px;
            font-weight: 400;
        }
        p.color_size {
            font-size: 8px;
        }
        .pstatus_1 {
            font-weight: bold;
            color: #26c56d; /* Example: Paid */
        }
        .pstatus_2 {
            font-weight: bold;
            color: #fe9e42; /* Example: Pending */
        }
        .pstatus_3 {
            font-weight: bold;
            color: #f25961; /* Example: Failed */
        }
        .pstatus_4 {
            font-weight: bold;
            color: #f25961; /* Example: Refunded */
        }
        .ostatus_1{
            font-weight: bold;
            color: #fe9e42; /* Example: Pending */
        }
        .ostatus_2 {
            font-weight: bold;
            color: #26c56d; /* Example: Confirmed */
        }
        .ostatus_3 {
            font-weight: bold;
            color: #919395; /* Example: Cancelled */
        }
        .ostatus_4 {
            font-weight: bold;
            color: #f25961; /* Example: Completed (or similar final state) */
        }
        .old-price {
            text-decoration: line-through;
            color: #ee0101;
        }
    </style>
</head>
<body>

    <table class="border-header" width="100%" cellpadding="10" cellspacing="0">
        <tr>
            <td class="w-40"><span class="company-logo"><img src="{{ $logoPath }}"/></span></td>
            <td class="w-60 invoice-name">{{ __('Invoice') }}</td>
        </tr>
    </table>
    <table class="border-none" width="100%" cellpadding="1" cellspacing="0">
        <tr><td class="w-100" align="center"></td></tr>
    </table>
    <table class="border-none" width="100%" cellpadding="2" cellspacing="0">
        <tr>
            <td class="w-50" align="left">
                <h3>{{ __('BILL TO') }}:</h3>
                <p><strong>{{ $booking->name }}</strong></p>
                <p>{{ $booking->address }}</p>
                <p>{{ $booking->city }}, {{ $booking->state }}, {{ $booking->zip_code }}, {{ $booking->country }}</p>
                <p>{{ $booking->email }}</p>
                <p>{{ $booking->phone }}</p>
            </td>
            <td class="w-50" align="right">
                <p><strong>{{ __('Booking No') }}</strong>: {{ $booking->booking_no }}</p>
                <p><strong>{{ __('Booking Date') }}</strong>: {{ Carbon\Carbon::parse($booking->created_at)->format('d-m-Y') }}</p>
                <p><strong>{{ __('Payment Method') }}</strong>: {{ $booking->method_name }}</p>
                <p><strong>{{ __('Payment Status') }}</strong>: <span class="pstatus_{{ $booking->payment_status_id }}">{{ $booking->pstatus_name }}</span></p>
                <p><strong>{{ __('Order Status') }}</strong>: <span class="ostatus_{{ $booking->booking_status_id }}">{{ $booking->bstatus_name }}</span></p>
            </td>
        </tr>
    </table>
    <table class="border-none" width="100%" cellpadding="5" cellspacing="0">
        <tr><td class="w-100" align="center"></td></tr>
    </table>
    <table class="border-none" width="100%" cellpadding="6" cellspacing="0">
        <tr>
            <td class="w-100" align="left">
                <h3>{{ __('BILL FROM') }}:</h3>
                <p><strong>{{ $gtext['company'] }}</strong></p>
                <p>{{ $gtext['invoice_address'] }}</p>
                <p>{{ $gtext['invoice_email'] }}</p>
                <p>{{ $gtext['invoice_phone'] }}</p>
            </td>
        </tr>
    </table>
    <table class="border-none" width="100%" cellpadding="10" cellspacing="0">
        <tr><td class="w-100" align="center"></td></tr>
    </table>

    <table class="border-none" width="100%" cellpadding="6" cellspacing="0">
        <tr>
            <td class="w-30" align="left">
                <strong>{{ __('Room Type') }}</strong>
            </td>
            <td class="w-10" align="center">
                <strong>{{ __('Total Room') }}</strong>
            </td>
            <td class="w-15" align="center">
                <strong>{{ __('Price') }}</strong>
            </td>
            <td class="w-20" align="center">
                <strong>{{ __('In / Out Date') }}</strong>
            </td>
            <td class="w-10" align="center">
                <strong>{{ __('Total Days') }}</strong>
            </td>
            <td class="w-15" align="right">
                <strong>{{ __('Total') }}</strong>
            </td>
        </tr>
    </table>

    <table class="border-tb" width="100%" cellpadding="6" cellspacing="0">
        {!! $item_list_html !!} {{-- Render the pre-generated item list HTML --}}
    </table>
    <table class="border-none" width="100%" cellpadding="2" cellspacing="0">
        <tr><td class="w-100" align="center"></td></tr>
    </table>
    <table class="border-none" width="100%" cellpadding="6" cellspacing="0">
        <tr>
            <td class="w-50" align="left"><strong>{{ $assignRoomsText }}</strong></td>
            <td class="w-30" align="right"><strong>{{ __('Subtotal') }}</strong>: </td>
            <td class="w-20" align="right"><strong>{{ $subtotalFormatted }}</strong></td>
        </tr>
        <tr>
            <td class="w-50" align="left"></td>
            <td class="w-30" align="right"><strong>{{ __('Tax') }}</strong>: </td>
            <td class="w-20" align="right"><strong>{{ $taxFormatted }}</strong></td>
        </tr>
        <tr>
            <td class="w-50" align="left"></td>
            <td class="w-30" align="right"><strong>{{ __('Discount') }}</strong>: </td>
            <td class="w-20" align="right"><strong>{{ $discountFormatted }}</strong></td>
        </tr>
        <tr>
            <td class="w-50" align="left"></td>
            <td class="w-30" align="right"><strong>{{ __('Grand Total') }}</strong>: </td>
            <td class="w-20" align="right"><strong>{{ $totalAmountFormatted }}</strong></td>
        </tr>
    </table>
    <table class="border-none" width="100%" cellpadding="70" cellspacing="0">
        <tr><td class="w-100" align="center"></td></tr>
    </table>
    <table class="border-t" width="100%" cellpadding="10" cellspacing="0">
        <tr>
            <td class="w-100" align="center">
                <p>{{ __('Thank you for booking our rooms.') }}</p>
                <p>{{ __('If you have any questions about this invoice, please contact us') }}</p>
                <p><a href="{{ $base_url }}">{{ $base_url }}</a></p>
            </td>
        </tr>
    </table>
</body>
</html>