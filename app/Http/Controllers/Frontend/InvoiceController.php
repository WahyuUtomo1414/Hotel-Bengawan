<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Use the correct facade for Laravel DomPDF
use Carbon\Carbon; // Use Carbon for date handling

class InvoiceController extends Controller
{
    /**
     * Generate and download an invoice PDF for a given booking.
     *
     * @param int $booking_id The ID of the booking.
     * @param string $booking_no The booking number.
     * @return \Illuminate\Http\Response
     */
    public function getInvoice(int $booking_id, string $booking_no)
    {
        // Retrieve global settings (assuming gtext() is a helper function)
        $gtext = gtext();

        // 1. Fetch Booking Data
        $booking = DB::table('booking_manages')
            ->join('rooms', 'booking_manages.roomtype_id', '=', 'rooms.id')
            ->join('payment_method', 'booking_manages.payment_method_id', '=', 'payment_method.id')
            ->join('payment_status', 'booking_manages.payment_status_id', '=', 'payment_status.id')
            ->join('booking_status', 'booking_manages.booking_status_id', '=', 'booking_status.id')
            ->select(
                'booking_manages.*',
                'rooms.title',
                'rooms.old_price',
                'rooms.is_discount',
                'payment_method.method_name',
                'payment_status.pstatus_name',
                'booking_status.bstatus_name'
            )
            ->where('booking_manages.id', $booking_id)
            ->first(); // Use first() since you expect a single record

        // Handle case where booking is not found
        if (!$booking) {
            abort(404, 'Booking not found.');
        }

        // 2. Prepare Booking Data for View
        // No need for $mdata array and foreach loop if using first() and directly accessing properties
        $inDate = Carbon::parse($booking->in_date);
        $outDate = Carbon::parse($booking->out_date);
        $totalDays = $inDate->diffInDays($outDate);

        // Format prices based on currency position
        $formatPrice = function ($amount) use ($gtext) {
            return $gtext['currency_position'] == 'left'
                ? $gtext['currency_icon'] . NumberFormat($amount)
                : NumberFormat($amount) . $gtext['currency_icon'];
        };

        $oPriceFormatted = $formatPrice($booking->old_price ?? 0);
        $caloPriceFormatted = $formatPrice(($booking->old_price ?? 0) * $booking->total_room * $totalDays);
        $totalPriceFormatted = $formatPrice($booking->total_price ?? 0);
        $subtotalFormatted = $formatPrice($booking->subtotal ?? 0);
        $taxFormatted = $formatPrice($booking->tax ?? 0);
        $discountFormatted = $formatPrice($booking->discount ?? 0);
        $totalAmountFormatted = $formatPrice($booking->total_amount ?? 0);
           $extraBedPriceFormatted = $formatPrice($booking->extra_bed_price ?? 0);
        $extraPersonPriceFormatted = $formatPrice($booking->extra_person_price ?? 0);

        $oldPriceHtml = '';
        $calOldPriceHtml = '';
        if ($booking->is_discount == 1) {
            $oldPriceHtml = '<p class="old-price">' . $oPriceFormatted . '</p>';
            $calOldPriceHtml = '<p class="old-price">' . $caloPriceFormatted . '</p>';
        }

        // 3. Prepare Room Item List HTML
        $item_list = '<tr>
            <td class="w-30" align="left">' . e($booking->title) . '</td>
            <td class="w-10" align="center">' . e($booking->total_room) . '</td>
            <td class="w-15" align="center"><p>' . $totalPriceFormatted . '</p>' . $oldPriceHtml . '</td>
            <td class="w-20" align="center"><p>' . $inDate->format('d-m-Y') . '</p><p>to</p><p>' . $outDate->format('d-m-Y') . '</p></td>
            <td class="w-10" align="center">' . $totalDays . '</td>
            <td class="w-15" align="right"><p>' . $subtotalFormatted . '</p>' . $calOldPriceHtml . '</td>
        </tr>';

        // 4. Fetch Assigned Room Numbers
        $roomNumbers = DB::table('room_manages')
            ->join('room_assigns', 'room_manages.id', '=', 'room_assigns.room_id')
            ->select('room_manages.room_no')
            ->where('room_assigns.booking_id', $booking_id)
            ->orderBy('room_manages.room_no', 'asc')
            ->pluck('room_no')
            ->implode(', '); // Use pluck and implode for cleaner collection handling

        $assignRoomsText = '';
        if (!empty($roomNumbers)) {
            $assignRoomsText = __('Your assign room no') . ': ' . $roomNumbers;
        }

        $base_url = url('/');
        $logoPath = public_path('media/' . $gtext['front_logo']);

        // 5. PDF Generation
        // Using `loadView` is generally preferred for cleaner HTML generation
        // You would create a Blade view for the PDF content, e.g., resources/views/invoices/booking_invoice_pdf.blade.php
        $data = [
            'gtext'                 => $gtext,
            'booking'               => $booking, // Pass the whole booking object
            'totalDays'             => $totalDays,
            'extraBedPriceFormatted'=> $extraBedPriceFormatted,
            'extraPersonPriceFormatted'=> $extraPersonPriceFormatted,
            'oPriceFormatted'       => $oPriceFormatted,
            'caloPriceFormatted'    => $caloPriceFormatted,
            'totalPriceFormatted'   => $totalPriceFormatted,
            'subtotalFormatted'     => $subtotalFormatted,
            'taxFormatted'          => $taxFormatted,
            'discountFormatted'     => $discountFormatted,
            'totalAmountFormatted'  => $totalAmountFormatted,
            'oldPriceHtml'          => $oldPriceHtml,
            'calOldPriceHtml'       => $calOldPriceHtml,
            'item_list_html'        => $item_list, // Still passing this pre-generated HTML for simplicity with existing styles
            'assignRoomsText'       => $assignRoomsText,
            'base_url'              => $base_url,
            'logoPath'              => $logoPath,
        ];

        // Load the view and pass data
        $pdf = PDF::loadView('invoices.booking_invoice_pdf', $data);

        // Set PDF options
        // This is where you might set paper size, orientation if not done in CSS
        // PDF::setPaper('A4', 'portrait');

        // Set font (if needed, otherwise DomPDF will use defaults or embedded fonts from CSS)
        // Ensure 'dejavusans' font is configured in config/dompdf.php if custom font is required
        // $pdf->getDomPDF()->getCanvas()->get_font_metrics()->get_font_family_resolver()->add_font('dejavusans', 'normal', 'path/to/dejavusans.ttf');
        // $pdf->getDomPDF()->getCanvas()->get_font_metrics()->get_font_family_resolver()->add_font('dejavusans', 'bold', 'path/to/dejavusans-bold.ttf');
        // $pdf->getDomPDF()->set_option('defaultFont', 'dejavusans');


        // Download the PDF
        return $pdf->stream('invoice-' . $booking->booking_no . '.pdf');
    }

    /**
     * Helper function to format numbers (assuming this exists globally or in a helper file).
     *
     * @param float $number
     * @return string
     */
    protected function NumberFormat(float $number): string
    {
        return number_format($number, 2, '.', ','); // Example implementation
    }
}