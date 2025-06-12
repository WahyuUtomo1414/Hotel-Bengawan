@extends('layouts.backend')

@section('title', __('Payment Methods'))

@section('content')
<!-- main Section -->
<div class="main-body">
	<div class="container-fluid">
		<div class="row mt-25">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-lg-6">
								<span>{{ __('Payment Methods') }}</span>
							</div>
							<div class="col-lg-6"></div>
						</div>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-12">
								<div class="float-right">
									<a onClick="onListPanel()" href="javascript:void(0);" class="btn warning-btn btn-list float-right dnone"><i class="fa fa-reply"></i> {{ __('Back to List') }}</a>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">

								<!--/Data grid-->
								<div id="list-panel">
									<div class="table-responsive">
										<table class="table table-borderless table-theme" style="width:100%;">
											<tbody>
												<tr>
													<td class="text-left" width="70%">{{ __('Midtrans') }}</td>
													<td class="text-left" width="25%">
														@if($midtransList['isenable'] == 1)
														<span class="enable_btn">{{ __('Active') }}</span>
														@else
														<span class="disable_btn">{{ __('Inactive') }}</span>	
														@endif
													</td>
													<td class="text-center" width="5%">
														<div class="btn-group action-group">
															<a class="action-btn" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<a onclick="onEdit(3)" class="dropdown-item" href="javascript:void(0);">{{ __('Edit') }}</a>
															</div>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<!--/Data grid-->						
								<!--/Bank Transfer Form-->
								<div id="form-panel-3" class="dnone">
									<form novalidate="" data-validate="parsley" id="midtrans_formId">
										<div class="row mb-10">
											<div class="col-lg-8">
												<h5>{{ __('Bank Transfer') }}</h5>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12">
												<div class="tw_checkbox checkbox_group">
													<input id="isenable_midtrans" name="isenable_midtrans" type="checkbox" {{ $midtransList['isenable'] == 1 ? 'checked' : '' }}>
													<label for="isenable_midtrans">{{ __('Active/Inactive') }}</label>
													<span></span>
												</div>
												<div class="form-group">
													<label for="server_key">{{ __('Server Key') }}</label>
													<input name="server_key" class="form-control" type="text" value="{{ $midtransList['server_key'] }}"/>
												</div>
												<div class="form-group">
													<label for="client_key">{{ __('Client Key') }}</label>
													<input name="client_key" class="form-control" type="text" value="{{ $midtransList['client_key'] }}"/>
												</div>
												<div class="form-group">
													<label for="merchant_id">{{ __('Merchant ID') }}</label>
													<input name="merchant_id" class="form-control" type="text" value="{{ $midtransList['merchant_id'] }}"/>
												</div>
											</div>
											<div class="col-lg-4"></div>
										</div>
										<div class="row tabs-footer mt-15">
											<div class="col-lg-12">
											<a id="submit-form-midtrans" href="javascript:void(0);" class="btn blue-btn mr-10">{{ __('Save') }}</a>
											</div>
										</div>
									</form>
								</div>
								<!--/Bank Transfer Form-->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /main Section -->
@endsection

@push('scripts')
<script type="text/javascript">
var TEXT = [];
	TEXT['Do you really want to edit this record'] = "{{ __('Do you really want to edit this record') }}";
</script>
<script src="{{asset('backend/pages/payment-gateway.js')}}"></script>
@endpush