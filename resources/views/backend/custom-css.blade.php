@extends('layouts.backend')

@section('title', __('Custom CSS'))

@section('content')
<!-- main Section -->
<div class="main-body">
	<div class="container-fluid">
		
		<div class="row mt-25">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">{{ __('Theme Options') }}</div>
					<div class="card-body tabs-area p-0">
						@include('backend.partials.theme_options_tabs_nav')
						<div class="tabs-body">
							<!--Custom CSS-->
							<form novalidate="" data-validate="parsley" id="DataEntry_formId">
								<div class="row">
									<div class="col-lg-12">
										<div class="form-group">
											<label for="custom_css">{{ __('Custom CSS') }}</label>
											<textarea name="custom_css" id="custom_css" class="form-control" rows="13">{{ $datalist['custom_css'] }}</textarea>
											<small class="form-text text-muted">Paste your custom CSS code here</small>
										</div>
									</div>
								</div>
								<div class="row tabs-footer mt-15">
									<div class="col-lg-12">
										<a id="submit-form" href="javascript:void(0);" class="btn blue-btn">{{ __('Save') }}</a>
									</div>
								</div>
							</form>
							<!--/Custom CSS-->
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
<script src="{{asset('backend/pages/custom-css.js')}}"></script>
@endpush