<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\MidtransHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Room;
use App\Models\Room_manage;
use App\Models\Booking_manage;
use App\Models\Country;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

use Razorpay\Api\Api;

use Mollie\Laravel\Facades\Mollie;

class CheckoutFrontController extends Controller
{
	protected $PayPalClient;

	public function LoadCheckout($id, $title)
	{
		$country_list = Country::where('is_publish', '=', 1)->orderBy('country_name', 'ASC')->get();
		$rtdata = Room::where('id', $id)->where('is_publish', '=', 1)->first();
		$total_room = Room_manage::where('roomtype_id', '=', $id)->where('book_status', '=', 2)->where('is_publish', '=', 1)->count();

		return view('frontend.checkout', compact('country_list', 'rtdata', 'total_room'));
	}

	public function LoadThank()
	{
		return view('frontend.thank');
	}

	public function SendBookingRequest(Request $request)
	{
		$res = array();
		$gtext = gtext();
		$gtax = getTax();

		Session::forget('pt_payment_error');

		$roomtype_id = $request->input('roomtype_id');
		$total_room = $request->input('room');

		if ($total_room == 0) {
			$res['msgType'] = 'error';
			$res['msg'] = array('oneError' => array(__('Oops! Your booking request is failed. Please enter room number.')));
			return response()->json($res);
		}

		$customer_id = '';

		$newaccount = $request->input('new_account');

		if ($newaccount == 'true' || $newaccount == 'on') {
			$new_account = 1;
		} else {
			$new_account = 0;
		}

		$payment_method_id = $request->input('payment_method');

		if ($new_account == 1) {

			$validator = Validator::make($request->all(), [
				'name' => 'required',
				'phone' => 'required',
				'country' => 'required',
				'state' => 'required',
				'zip_code' => 'required',
				'city' => 'required',
				'address' => 'required',
				'payment_method' => 'required',
				'checkin_date' => 'required',
				'checkout_date' => 'required',
				'room' => 'required',
				'email' => 'required|email|unique:users',
				'password' => 'required|confirmed',
				'person' => 'required',
				'extra_bed' => 'required',
			]);

			if (!$validator->passes()) {
				$res['msgType'] = 'error';
				$res['msg'] = $validator->errors()->toArray();
				return response()->json($res);
			}

			$userData = array(
				'name' => $request->input('name'),
				'email' => $request->input('email'),
				'phone' => $request->input('phone'),
				'address' => $request->input('address'),
				'state' => $request->input('state'),
				'zip_code' => $request->input('zip_code'),
				'city' => $request->input('city'),
				'password' => Hash::make($request->input('password')),
				'bactive' => base64_encode($request->input('password')),
				'status_id' => 1,
				'role_id' => 2
			);

			$customer_id = User::create($userData)->id;
		} else {

			$validator = Validator::make($request->all(), [
				'name' => 'required',
				'email' => 'required',
				'phone' => 'required',
				'country' => 'required',
				'state' => 'required',
				'zip_code' => 'required',
				'city' => 'required',
				'address' => 'required',
				'payment_method' => 'required',
				'checkin_date' => 'required',
				'checkout_date' => 'required',
				'room' => 'required',
				'person' => 'required',
				'extra_bed' => 'required',
			]);

			if (!$validator->passes()) {
				$res['msgType'] = 'error';
				$res['msg'] = $validator->errors()->toArray();
				return response()->json($res);
			}

			$customer_id = $request->input('customer_id');
		}

		$rtdata = Room::where('id', $roomtype_id)->where('is_publish', '=', 1)->first();

		$start_random = RandomString(3);
		$end_random = RandomString(3);

		$booking_no = $start_random . date("his") . $end_random;

		$in_date = $request->input('checkin_date');
		$out_date = $request->input('checkout_date');
		$person = $request->input('person');
		$room_price = comma_remove($person > 1 ? $rtdata->price2 : $rtdata->price);
		$extra_bed = $request->input('extra_bed');
		$extra_person = $person > 2 ? $person - 2 : 0;


		$is_discount = $rtdata->is_discount;

		$total_days = DateDiffInDays($in_date, $out_date);


		$extra_person_price = ($rtdata->extra_person * $extra_person) * $total_days;

		$extra_bed_price = ($rtdata->extra_bed * $extra_bed) * $total_days;


		$subtotal = $room_price * $total_room * $total_days;

		$total_discount = 0;
		if ($is_discount == 1) {
			if ($rtdata->old_price != '') {
				$old_price = $rtdata->old_price;
				$discount = $old_price * $total_room * $total_days;
				$total_discount = $discount - $subtotal;
			}
		}

		$tax_rate = $gtax['percentage'];

		$total_tax = ((($subtotal + $extra_person_price + $extra_bed_price) * $tax_rate) / 100);

		$total_amount = $subtotal + $extra_person_price + $extra_bed_price + $total_tax;

		$paid_amount = 0;
		$due_amount = $total_amount;

		$data = array(
			'booking_no' => $booking_no,
			'roomtype_id' => $roomtype_id,
			'customer_id' => $customer_id,
			'payment_method_id' => $payment_method_id,
			'payment_status_id' => 2,
			'booking_status_id' => 1,
			'total_room' => $total_room,
			'total_price' => $room_price,
			'discount' => $total_discount,
			'tax' => $total_tax,
			'subtotal' => $subtotal,
			'total_amount' => $total_amount,
			'paid_amount' => $paid_amount,
			'due_amount' => $due_amount,
			'in_date' => $in_date,
			'out_date' => $out_date,
			'name' => $request->input('name'),
			'email' => $request->input('email'),
			'phone' => $request->input('phone'),
			'country' => $request->input('country'),
			'state' => $request->input('state'),
			'zip_code' => $request->input('zip_code'),
			'city' => $request->input('city'),
			'address' => $request->input('address'),
			'comments' => $request->input('comments'),
			'extra_person' => $extra_person,
			'extra_bed' => $extra_bed,
			'person' => $person,
			'extra_person_price' => $extra_person_price,
			'extra_bed_price' => $extra_bed_price
		);


		$order_master_id = Booking_manage::create($data)->id;

		//set order master ids into session
		Session::put('order_master_ids', $order_master_id);

		if ($order_master_id > 0) {
			$intent = '';

			$description = 'Total Room: ' . $total_room . ', Booking No: ' . $booking_no;

			$totalAmount = $total_amount;

			//Stripe
			if ($payment_method_id == 2) {

				$payment_id = Str::uuid();

				$payment_url = MidtransHelper::createPaymentUrl([
					'transaction_details' => [
						'order_id' => $payment_id,
						'gross_amount' => $totalAmount
					],
					'customer_details' => [
						'first_name' => $request->input('name'),
						'email' => $request->input('email'),
					]
				]);

				Booking_manage::where('id', $order_master_id)->update([
					'payment_id' => $payment_id,
					'payment_url' => $payment_url
				]);
			} else {
				$intent = '';
			}

			if ($payment_method_id != 4) {

				if ($gtext['ismail'] == 1) {
					BookingNotify($order_master_id, 'booking_request');
				}
			}

			$res['msgType'] = 'success';
			$res['msg'] = __('Your booking request is successfully.');
			$res['intent'] = $intent;
			return response()->json($res);
		} else {
			$res['msgType'] = 'error';
			$res['msg'] = __('Oops! Your booking request is failed. Please try again.');
			return response()->json($res);
		}
	}


	public function getCheckOutTotalPrice(Request $request)
	{
		$res = array();
		$gtext = gtext();
		$gtax = getTax();

		$roomtype_id = $request->input('roomtype_id');
		$in_date = $request->input('checkin_date');
		$out_date = $request->input('checkout_date');
		$total_room = $request->input('total_room');
		$person = $request->input('person');
		$extra_bed = $request->input('extra_bed');
		$extra_person = $person > 2 ? $person - 2 : 0;


		$rtdata = Room::where('id', $roomtype_id)->where('is_publish', '=', 1)->first();

		$room_price = comma_remove($person > 1 ? $rtdata->price2 : $rtdata->price);



		$is_discount = $rtdata->is_discount;

		$total_days = DateDiffInDays($in_date, $out_date);

		$extra_person_price = ($rtdata->extra_person * $extra_person) * $total_days;

		$extra_bed_price = ($rtdata->extra_bed * $extra_bed) * $total_days;



		$subtotal = ($room_price * $total_room * $total_days);



		$total_discount = 0;
		if ($is_discount == 1) {
			if ($rtdata->old_price != '') {
				$old_price = $rtdata->old_price;
				$discount = $old_price * $total_room * $total_days;
				$total_discount = $discount - $subtotal;
			}
		}


		$tax_rate = $gtax['percentage'];

		$total_tax = ((($subtotal + $extra_person_price + $extra_bed_price) * $tax_rate) / 100);

		$total_amount = $subtotal + $total_tax + $extra_person_price + $extra_bed_price;

		$res['subtotal'] = $subtotal;
		$res['total_tax'] = $total_tax;
		$res['total_amount'] = $total_amount;

		$additional_rows = '';

		if ($gtext['currency_position'] == 'left') {



			if ($extra_bed_price > 0) {
				$additional_rows .= '<tr><td><span class="title">' . __('Extra Bed') .' ('.$extra_bed. 'x)</span><span class="price">' . $gtext['currency_icon'] . NumberFormat($extra_bed_price) . '</span></td></tr>';
			}

			if ($extra_person_price > 0) {
				$additional_rows .= '<tr><td><span class="title">' . __('Extra Person').' ('.$extra_person. 'x)</span><span class="price">' . $gtext['currency_icon'] . NumberFormat($extra_person_price) . '</span></td></tr>';
			}

			// Now, construct the final table string using heredoc or concatenation
			// Heredoc is generally cleaner for multi-line HTML strings
			$res['total_table'] = '<table class="table total-price-card">
				<tbody>
					'.$additional_rows.'
					<tr><td><span class="title">' . __('Subtotal') . '</span><span class="price">' . $gtext['currency_icon'] . NumberFormat($subtotal) . '</span></td></tr>
					<tr><td><span class="title">' . __('Tax') . '</span><span class="price">' . $gtext['currency_icon'] . NumberFormat($total_tax) . '</span></td></tr>
					<tr><td><span class="title">' . __('Discount') . '</span><span class="price">' . $gtext['currency_icon'] . NumberFormat($total_discount) . '</span></td></tr>
					<tr><td><span class="title">' . __('Total') . '</span><span class="price">' . $gtext['currency_icon'] . NumberFormat($total_amount) . '</span></td></tr>
				</tbody>	
			</table>';
		} else {
			if ($extra_bed_price > 0) {
				$additional_rows .= '<tr><td><span class="title">' . __('Extra Bed') . '</span><span class="price">' . NumberFormat($extra_bed_price) . $gtext['currency_icon'] . '</span></td></tr>';
			}

			if ($extra_person_price > 0) {
				$additional_rows .= '<tr><td><span class="title">' . __('Extra Person') . ' ('.$extra_person . 'x) </span><span class="price">' . NumberFormat($extra_person_price) . $gtext['currency_icon'] . '</span></td></tr>';
			}

			$res['total_table'] = '<table class="table total-price-card">
					<tbody>
					' . $additional_rows . '
						<tr><td><span class="title">' . __('Subtotal') . '</span><span class="price">' . NumberFormat($subtotal) . $gtext['currency_icon'] . '</span></td></tr>
						<tr><td><span class="title">' . __('Tax') . '</span><span class="price">' . NumberFormat($total_tax) . $gtext['currency_icon'] . '</span></td></tr>
						<tr><td><span class="title">' . __('Discount') . '</span><span class="price">' . NumberFormat($total_discount) . $gtext['currency_icon'] . '</span></td></tr>
						<tr><td><span class="title">' . __('Total') . '</span><span class="price">' . NumberFormat($total_amount) . $gtext['currency_icon'] . '</span></td></tr>
						
					</tbody>
				</table>';
		}

		return response()->json($res);
	}
}
