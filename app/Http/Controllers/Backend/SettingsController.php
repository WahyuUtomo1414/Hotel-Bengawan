<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Tp_option;
use App\Models\Media_setting;

class SettingsController extends Controller
{

    //General page load
    public function getGeneralPageLoad(){
		
		$timezonelist = DB::table('timezones')->orderBy('timezone_name', 'asc')->get();
		
		$datalist = Tp_option::where('option_name', 'general_settings')->get();
		$id = '';
		$option_value = '';
		foreach ($datalist as $row){
			$id = $row->id;
			$option_value = json_decode($row->option_value);
		}

		$data = array();
		if($id != ''){
			$data['site_name'] = $option_value->site_name;
			$data['site_title'] = $option_value->site_title;
			$data['company'] = $option_value->company;
			$data['email'] = $option_value->email;
			$data['phone'] = $option_value->phone;
			$data['address'] = $option_value->address;
			$data['timezone'] = $option_value->timezone;
		}else{
			$data['site_name'] = '';
			$data['site_title'] = '';
			$data['company'] = '';
			$data['email'] = '';
			$data['phone'] = '';
			$data['address'] = '';
			$data['timezone'] = '';
		}

		$datalist = $data;

        return view('backend.general', compact('timezonelist', 'datalist'));
    }

	//Save data for general Setting
    public function GeneralSettingUpdate(Request $request){
		$res = array();
		
		$id = $request->input('RecordId');
		$company = $request->input('company');
		$email = $request->input('email');
		$phone = $request->input('phone');
		$site_name = $request->input('site_name');
		$site_title = $request->input('site_title');
		$address = $request->input('address');
		$timezone = $request->input('timezone');

		$validator_array = array(
			'company' => $request->input('company'),
			'email' => $request->input('email'),
			'phone' => $request->input('phone'),
			'site_name' => $request->input('site_name'),
			'address' => $request->input('address'),
			'site_title' => $request->input('site_title')
		);

		$validator = Validator::make($validator_array, [
			'company' => 'required',
			'email' => 'required',
			'phone' => 'required',
			'site_name' => 'required',
			'address' => 'required',
			'site_title' => 'required'
		]);

		$errors = $validator->errors();

		if($errors->has('company')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('company');
			return response()->json($res);
		}
		
		if($errors->has('email')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('email');
			return response()->json($res);
		}
		
		if($errors->has('phone')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('phone');
			return response()->json($res);
		}
		
		if($errors->has('site_name')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('site_name');
			return response()->json($res);
		}
		
		if($errors->has('site_title')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('site_title');
			return response()->json($res);
		}
		
		if($errors->has('address')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('address');
			return response()->json($res);
		}
		
		$option_value = array(
			'company' => $company,
			'email' => $email,
			'phone' => $phone,
			'site_name' => $site_name,
			'site_title' => $site_title,
			'address' => $address,
			'timezone' => $timezone
		);

		$data = array(
			'option_name' => 'general_settings',
			'option_value' => json_encode($option_value)
		);
		
		$gData = Tp_option::where('option_name', 'general_settings')->get();
		$id = '';
		foreach ($gData as $row){
			$id = $row['id'];
		}
		
		if($id == ''){
			$response = Tp_option::create($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Saved Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data insert failed');
			}
		}else{
			$response = Tp_option::where('id', $id)->update($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Updated Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data update failed');
			}
		}

		return response()->json($res);
    }
	
    //Google Recaptcha page load
    public function loadGoogleRecaptchaPage(){
		$datalist = Tp_option::where('option_name', 'google_recaptcha')->get();
		$id = '';
		$option_value = '';
		foreach ($datalist as $row){
			$id = $row->id;
			$option_value = json_decode($row->option_value);
		}

		$data = array();
		if($id != ''){
			$data['sitekey'] = $option_value->sitekey;
			$data['secretkey'] = $option_value->secretkey;
			$data['is_recaptcha'] = $option_value->is_recaptcha;
		}else{
			$data['sitekey'] = '';
			$data['secretkey'] = '';
			$data['is_recaptcha'] = '';
		}
		
		$datalist = $data;
		
		return view('backend.google-recaptcha', compact('datalist'));
    }
	
	//Save data for Google Recaptcha
    public function GoogleRecaptchaUpdate(Request $request){
		$res = array();
		
		$sitekey = $request->input('sitekey');
		$secretkey = $request->input('secretkey');
		$g_recaptcha = $request->input('recaptcha');
		
		if ($g_recaptcha == 'true' || $g_recaptcha == 'on') {
			$recaptcha = 1;
		}else {
			$recaptcha = 0;
		}
		
		$validator_array = array(
			'sitekey' => $request->input('sitekey'),
			'secretkey' => $request->input('secretkey')
		);

		$validator = Validator::make($validator_array, [
			'sitekey' => 'required',
			'secretkey' => 'required'
		]);

		$errors = $validator->errors();

		if($errors->has('sitekey')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('sitekey');
			return response()->json($res);
		}
		if($errors->has('secretkey')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('secretkey');
			return response()->json($res);
		}

		$option_value = array(
			'sitekey' => $sitekey,
			'secretkey' => $secretkey,
			'is_recaptcha' => $recaptcha
		);
		
		$data = array(
			'option_name' => 'google_recaptcha',
			'option_value' => json_encode($option_value)
		);

		$gData = Tp_option::where('option_name', 'google_recaptcha')->get();
		$id = '';
		foreach ($gData as $row){
			$id = $row['id'];
		}
		
		if($id == ''){
			$response = Tp_option::create($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Saved Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data insert failed');
			}
		}else{
			$response = Tp_option::where('id', $id)->update($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Updated Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data update failed');
			}
		}

		return response()->json($res);
    }
	
    //Google Map page load
    public function loadGoogleMapPage(){
		$datalist = Tp_option::where('option_name', 'google_map')->get();
		$id = '';
		$option_value = '';
		foreach ($datalist as $row){
			$id = $row->id;
			$option_value = json_decode($row->option_value);
		}

		$data = array();
		if($id != ''){
			$data['googlemap_apikey'] = $option_value->googlemap_apikey;
			$data['is_googlemap'] = $option_value->is_googlemap;
		}else{
			$data['googlemap_apikey'] = '';
			$data['is_googlemap'] = '';
		}
		
		$datalist = $data;
		
		return view('backend.google-map', compact('datalist'));
    }
	
	//Save data for Google Map
    public function GoogleMapUpdate(Request $request){
		$res = array();
		
		$googlemap_apikey = $request->input('googlemap_apikey');
		$isGooglemap = $request->input('is_googlemap');
		
		if ($isGooglemap == 'true' || $isGooglemap == 'on') {
			$is_googlemap = 1;
		}else {
			$is_googlemap = 0;
		}
		
		$validator_array = array(
			'googlemap_apikey' => $request->input('googlemap_apikey')
		);

		$validator = Validator::make($validator_array, [
			'googlemap_apikey' => 'required'
		]);

		$errors = $validator->errors();

		if($errors->has('googlemap_apikey')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('googlemap_apikey');
			return response()->json($res);
		}

		$option_value = array(
			'googlemap_apikey' => $googlemap_apikey,
			'is_googlemap' => $is_googlemap
		);
		
		$data = array(
			'option_name' => 'google_map',
			'option_value' => json_encode($option_value)
		);

		$gData = Tp_option::where('option_name', 'google_map')->get();
		$id = '';
		foreach ($gData as $row){
			$id = $row['id'];
		}
		
		if($id == ''){
			$response = Tp_option::create($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Saved Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data insert failed');
			}
		}else{
			$response = Tp_option::where('id', $id)->update($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Updated Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data update failed');
			}
		}

		return response()->json($res);
    }
	
    //load Theme Register page
    public function loadThemeRegisterPage(){
		
		$results = Tp_option::where('option_name', 'pcode')->get();
		$id = '';
		$option_value = '';
		foreach ($results as $row){
			$id = $row->id;
			$option_value = json_decode($row->option_value);
		}

		$data = array();
		if($id != ''){
			$data['verified'] = $option_value->verified;
		}else{
			$data['verified'] = 0;
		}
		
		$datalist = $data;

        return view('backend.theme-register', compact('datalist'));
    }
	
    //get ajax Pcode Data
	public function getPcodeData(Request $request){

		if($request->ajax()){

			$results = Tp_option::where('option_name', 'pcode')->get();
			$id = '';
			$option_value = '';
			foreach ($results as $row){
				$id = $row->id;
				$option_value = json_decode($row->option_value);
			}

			$data = array();
			if($id != ''){
				$data['verified'] = $option_value->verified;
			}else{
				$data['verified'] = 0;
			}
			
			$datalist = $data;

			return view('backend.partials.purchase_code', compact('datalist'))->render();
		}
	}
	
	//Save data for Purchase Code Setting
    public function CodeVerified(Request $request){
		$res = array();
		
		$pcode = $request->input('pcode');
		
		$validator_array = array(
			'PurchaseCode' => $request->input('pcode')
		);

		$validator = Validator::make($validator_array, [
			'PurchaseCode' => 'required'
		]);

		$errors = $validator->errors();

		if($errors->has('PurchaseCode')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('PurchaseCode');
			return response()->json($res);
		}
		
		$purchase_code = htmlspecialchars($pcode);
		$verifyRes = verifyPurchase($purchase_code);

		if($verifyRes == 1){
			Tp_option::where('option_name', 'vipc')->delete();	
			$op_value = array('bactive' => 0,'resetkey' => 0, 'log_id' => 0);
			$vipc_data = array('option_name' => 'vipc', 'option_value' => json_encode($op_value));
			Tp_option::create($vipc_data);
			
			$res['msgType'] = 'error';
			$res['msg'] = __('Sorry, This is not a valid purchase code.');
			return response()->json($res);
		}
		
		$option_value = array(
			'pcode' => base64_encode($pcode),
			'verified' => 1
		);

		$data = array(
			'option_name' => 'pcode',
			'option_value' => json_encode($option_value)
		);
		
		$res_id = Tp_option::create($data)->id;
		if($res_id !=''){

			Tp_option::where('option_name', 'vipc')->delete();	
			$op_value = array('bactive' => 1,'resetkey' => 5,'log_id' => $verifyRes);
			$vipc_data = array('option_name' => 'vipc', 'option_value' => json_encode($op_value));
			Tp_option::create($vipc_data);
			
			$res['msgType'] = 'success';
			$res['msg'] = __('Theme registered Successfully');
		}else{
			$res['msgType'] = 'error';
			$res['msg'] = __('Data insert failed');
		}

		return response()->json($res);
    }

	//Delete data for Pcode
	public function deletePcode(Request $request){
		
		$res = array();
		
		$datalist = Tp_option::where('option_name', 'vipc')->get();

		$id = '';
		$option_value = '';
		foreach ($datalist as $row){
			$id = $row->id;
			$option_value = json_decode($row->option_value);
		}
		
		$log_id = 0;
		if($id != ''){
			$log_id = $option_value->log_id;
		}else{
			$log_id = 0;
		}
		
		$data_list = Tp_option::where('option_name', 'pcode')->get();

		$pcodeId = '';
		$option_value_obj = '';
		foreach ($data_list as $row){
			$pcodeId = $row->id;
			$option_value_obj = json_decode($row->option_value);
		}
		
		$pcode = 0;
		if($pcodeId != ''){
			$pcode = base64_decode($option_value_obj->pcode);
		}else{
			$pcode = 0;
		}		
		
		$response = Tp_option::where('option_name', 'pcode')->delete();	
		if($response){
			Tp_option::where('option_name', 'vipc')->delete();	
			$op_value = array('bactive' => 0,'resetkey' => 0,'log_id' => 0);
			$vipc_data = array('option_name' => 'vipc', 'option_value' => json_encode($op_value));
			Tp_option::create($vipc_data);
			deleteLog($log_id, $pcode);
			
			$res['msgType'] = 'success';
			$res['msg'] = __('Theme deregister Successfully');
		}else{
			$res['msgType'] = 'error';
			$res['msg'] = __('Data remove failed');
		}

		return response()->json($res);
	}

    //load Payment Methods page
    public function loadPaymentMethodsPage(){
		

		
		
		//Bank Transfer
		$midtrans = Tp_option::where('option_name', 'midtrans')->get();

		
		$bank_id = '';
		foreach ($midtrans as $row){
			$bank_id = $row->id;
		}


		$midtransList = array();
		if($bank_id != ''){
			$btData = json_decode($midtrans);
			$btObj = json_decode($btData[0]->option_value);
			$midtransList['merchant_id'] = $btObj->merchant_id;
			$midtransList['client_key'] = $btObj->client_key;
			$midtransList['server_key'] = $btObj->server_key;
			$midtransList['isenable'] = $btObj->isenable;
		}else{
			$midtransList['merchant_id'] = '';
			$midtransList['client_key'] = '';
			$midtransList['server_key'] = '';
			$midtransList['isenable'] = 0;
		}
		
        return view('backend.payment-methods', compact('midtransList'));
    }
	
	//Save data for Bank Transfer
    public function MidtransSettingsUpdate(Request $request){
		$res = array();
		
		$description = $request->input('server_key');
		$merchant_id = $request->input('merchant_id');
		$client_key = $request->input('client_key');
		$is_enable = $request->input('isenable_midtrans');


		
		if ($is_enable == 'true' || $is_enable == 'on') {
			$isenable = 1;
		}else {
			$isenable = 0;
		}
		
		$option_value = array(
			'merchant_id' => $merchant_id,
			'client_key' => $client_key,
			'server_key' => $description,
			'isenable' => $isenable
		);

		
		$data = array(
			'option_name' => 'midtrans',
			'option_value' => json_encode($option_value)
		);

		$gData = Tp_option::where('option_name', 'midtrans')->get();

		$id = '';
		foreach ($gData as $row){
			$id = $row['id'];
		}
		
		if($id == ''){
			$response = Tp_option::create($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Saved Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data insert failed');
			}
		}else{
			$response = Tp_option::where('id', $id)->update($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Updated Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data update failed');
			}
		}

		return response()->json($res);
    }
	
    //load Media Settings page
    public function loadMediaSettingsPage(){
		
		$datalist = Media_setting::paginate(10);
		
        return view('backend.media-settings', compact('datalist'));
    }
	
	//Get data for Media Settings Pagination
	public function getMediaSettingsTableData(Request $request){

		$search = $request->search;
		
		if($request->ajax()){

			if($search != ''){
				
				$datalist = Media_setting::where(function ($query) use ($search){
					$query->where('media_type', 'like', '%'.$search.'%')
						->orWhere('media_width', 'like', '%'.$search.'%')
						->orWhere('media_height', 'like', '%'.$search.'%')
						->orWhere('media_desc', 'like', '%'.$search.'%');
					})->paginate(10);
			}else{
				$datalist = Media_setting::paginate(10);
			}

			return view('backend.partials.media_settings_table', compact('datalist'))->render();
		}
	}
	
	//Get data for Media Settings by id
    public function getMediaSettingsById(Request $request){

		$id = $request->id;
		
		$data = Media_setting::where('id', $id)->first();

		return response()->json($data);
	}
	
	//Save data for Media Settings
    public function MediaSettingsUpdate(Request $request){
		$res = array();
		
		$id = $request->input('RecordId');
		$media_width = $request->input('media_width');
		$media_height = $request->input('media_height');
		
		$validator_array = array(
			'width' => $request->input('media_width'),
			'height' => $request->input('media_height')
		);

		$validator = Validator::make($validator_array, [
			'width' => 'required',
			'height' => 'required'
		]);

		$errors = $validator->errors();

		if($errors->has('width')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('width');
			return response()->json($res);
		}
		
		if($errors->has('height')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('height');
			return response()->json($res);
		}

		$data = array(
			'media_width' => $media_width,
			'media_height' => $media_height
		);

		$response = Media_setting::where('id', $id)->update($data);
		if($response){
			$res['msgType'] = 'success';
			$res['msg'] = __('Updated Successfully');
		}else{
			$res['msgType'] = 'error';
			$res['msg'] = __('Data update failed');
		}

		return response()->json($res);
    }
	
    //load Mail Settings page
    public function loadMailSettingsPage(){
		
		$datalist = Tp_option::where('option_name', 'mail_settings')->get();
		$id = '';
		$option_value = '';
		foreach ($datalist as $row){
			$id = $row->id;
			$option_value = json_decode($row->option_value);
		}

		$data = array();
		if($id != ''){
			$data['ismail'] = $option_value->ismail;
			$data['from_name'] = $option_value->from_name;
			$data['from_mail'] = $option_value->from_mail;
			$data['to_name'] = $option_value->to_name;
			$data['to_mail'] = $option_value->to_mail;
			$data['mailer'] = $option_value->mailer;
			$data['smtp_host'] = $option_value->smtp_host;
			$data['smtp_port'] = $option_value->smtp_port;
			$data['smtp_security'] = $option_value->smtp_security;
			$data['smtp_username'] = $option_value->smtp_username;
			$data['smtp_password'] = $option_value->smtp_password;
		}else{
			$data['ismail'] = '';
			$data['from_name'] = '';
			$data['from_mail'] = '';
			$data['to_name'] = '';
			$data['to_mail'] = '';
			$data['mailer'] = '';
			$data['smtp_host'] = '';
			$data['smtp_port'] = '';
			$data['smtp_security'] = '';
			$data['smtp_username'] = '';
			$data['smtp_password'] = '';
		}
		
		$datalist = $data;
		
        return view('backend.mail-settings', compact('datalist'));
    }

	//Save data for Mail Settings
    public function saveMailSettings(Request $request){
		$res = array();
		
		$from_name = $request->input('from_name');
		$from_mail = $request->input('from_mail');
		$to_name = $request->input('to_name');
		$to_mail = $request->input('to_mail');
		$mailer = $request->input('mailer');
		
		$smtp_host = $request->input('smtp_host');
		$smtp_port = $request->input('smtp_port');
		$smtp_security = $request->input('smtp_security');
		$smtp_username = $request->input('smtp_username');
		$smtp_password = $request->input('smtp_password');
		
		$is_mail = $request->input('ismail');
		if ($is_mail == 'true' || $is_mail == 'on') {
			$ismail = 1;
		}else {
			$ismail = 0;
		}
		
		//Is SMTP
		if($mailer == 'smtp'){
			$validator_array = array(
				'from_name' => $request->input('from_name'),
				'from_mail' => $request->input('from_mail'),
				'to_name' => $request->input('to_name'),
				'to_mail' => $request->input('to_mail'),
				'mailer' => $request->input('mailer'),
				'smtp_host' => $request->input('smtp_host'),
				'smtp_port' => $request->input('smtp_port'),
				'smtp_security' => $request->input('smtp_security'),
				'smtp_username' => $request->input('smtp_username'),
				'smtp_password' => $request->input('smtp_password')
			);

			$validator = Validator::make($validator_array, [
				'from_name' => 'required',
				'from_mail' => 'required',
				'to_name' => 'required',
				'to_mail' => 'required',
				'mailer' => 'required',
				'smtp_host' => 'required',
				'smtp_port' => 'required',
				'smtp_security' => 'required',
				'smtp_username' => 'required',
				'smtp_password' => 'required'
			]);
		}else{
			$validator_array = array(
				'from_name' => $request->input('from_name'),
				'from_mail' => $request->input('from_mail'),
				'to_name' => $request->input('to_name'),
				'to_mail' => $request->input('to_mail'),
				'mailer' => $request->input('mailer')
			);

			$validator = Validator::make($validator_array, [
				'from_name' => 'required',
				'from_mail' => 'required',
				'to_name' => 'required',
				'to_mail' => 'required',
				'mailer' => 'required'
			]);
		}
		
		$errors = $validator->errors();

		if($errors->has('from_name')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('from_name');
			return response()->json($res);
		}
		if($errors->has('from_mail')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('from_mail');
			return response()->json($res);
		}
		if($errors->has('to_name')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('to_name');
			return response()->json($res);
		}
		if($errors->has('to_mail')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('to_mail');
			return response()->json($res);
		}
		if($errors->has('mailer')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('mailer');
			return response()->json($res);
		}
		
		//IS SMTP
		if($mailer == 'smtp'){
			
			if($errors->has('smtp_host')){
				$res['msgType'] = 'error';
				$res['msg'] = $errors->first('smtp_host');
				return response()->json($res);
			}
			if($errors->has('smtp_port')){
				$res['msgType'] = 'error';
				$res['msg'] = $errors->first('smtp_port');
				return response()->json($res);
			}
			if($errors->has('smtp_security')){
				$res['msgType'] = 'error';
				$res['msg'] = $errors->first('smtp_security');
				return response()->json($res);
			}
			if($errors->has('smtp_username')){
				$res['msgType'] = 'error';
				$res['msg'] = $errors->first('smtp_username');
				return response()->json($res);
			}
			if($errors->has('smtp_password')){
				$res['msgType'] = 'error';
				$res['msg'] = $errors->first('smtp_password');
				return response()->json($res);
			}
		}
		
		$option_value = array(
			'ismail' => $ismail,
			'from_name' => $from_name,
			'from_mail' => $from_mail,
			'to_name' => $to_name,
			'to_mail' => $to_mail,
			'mailer' => $mailer,
			'smtp_host' => $smtp_host,
			'smtp_port' => $smtp_port,
			'smtp_security' => $smtp_security,
			'smtp_username' => $smtp_username,
			'smtp_password' => $smtp_password
		);
		
		$data = array(
			'option_name' => 'mail_settings',
			'option_value' => json_encode($option_value)
		);

		$gData = Tp_option::where('option_name', 'mail_settings')->get();
		$id = '';
		foreach ($gData as $row){
			$id = $row['id'];
		}
		
		if($id == ''){
			$response = Tp_option::create($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Saved Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data insert failed');
			}
		}else{
			$response = Tp_option::where('id', $id)->update($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Updated Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data update failed');
			}
		}

		return response()->json($res);
    }
}
