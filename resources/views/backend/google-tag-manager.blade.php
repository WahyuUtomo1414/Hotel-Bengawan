@extends('layouts.backend')

@section('title', __('Google Tag Manager'))

@section('content')
<!-- main Section -->
<div class="main-body">
	<div class="container-fluid">

		<div class="row mt-25">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-lg-12">
								{{ __('Google Tag Manager') }}
							</div>
						</div>
					</div>
					<div class="card-body tabs-area p-0">
						@include('backend.partials.theme_options_tabs_nav')
						<div class="tabs-body">
							<!--Data Entry Form-->
							<form novalidate="" data-validate="parsley" id="DataEntry_formId">
								<div class="row">
									<div class="col-lg-12">
										<div class="form-group">
											<label for="google_tag_manager_id">Google Tag Manager ID</label>
											<input value="{{ $datalist['google_tag_manager_id'] }}" type="text" name="google_tag_manager_id" id="google_tag_manager_id" class="form-control" placeholder="GTM-PVPT123">
											<small class="form-text text-muted">e.g. <strong>Google Tag Manager ID: GTM-PVPT123</strong></small>
										</div>
									</div>
								</div>
								<div class="row">	
									<div class="col-md-4">
										<div class="form-group">
											<label for="is_publish">{{ __('Status') }}</label>
											<select name="is_publish" id="is_publish" class="chosen-select form-control">
											@foreach($statuslist as $row)
												<option {{ $row->id == $datalist['is_publish'] ? "selected=selected" : '' }} value="{{ $row->id }}">
													{{ $row->status }}
												</option>
											@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-8"></div>
								</div>
								<div class="row tabs-footer mt-15">
									<div class="col-lg-12">
										<a id="submit-form" href="javascript:void(0);" class="btn blue-btn">{{ __('Save') }}</a>
									</div>
								</div>
							</form>
							<!--/Data Entry Form/-->
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
<!-- css/js -->
<script src="{{asset('backend/pages/google_tag_manager.js')}}"></script>
@endpush