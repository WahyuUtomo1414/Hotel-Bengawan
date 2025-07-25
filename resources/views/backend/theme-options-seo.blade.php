@extends('layouts.backend')

@section('title', __('SEO'))

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
								{{ __('SEO') }}
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
											<label for="og_title">{{ __('SEO Title') }}</label>
											<input value="{{ $datalist['og_title'] }}" type="text" name="og_title" id="og_title" class="form-control">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<div class="form-group">
											<label for="og_keywords">{{ __('SEO Keywords') }}</label>
											<input value="{{ $datalist['og_keywords'] }}" type="text" name="og_keywords" id="og_keywords" class="form-control">
										</div>
									</div>
								</div>
								<div class="row">	
									<div class="col-md-12">
										<div class="form-group">
											<label for="og_description">{{ __('SEO Description') }}</label>
											<textarea name="og_description" id="og_description" class="form-control" rows="2">{{ $datalist['og_description'] }}</textarea>
										</div>
									</div>
								</div>
								
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="og_image">{{ __('Open Graph Image') }}</label>
											<div class="tp-upload-field">
												<input value="{{ $datalist['og_image'] }}" type="text" name="og_image" id="og_image" class="form-control" readonly>
												<a onClick="onGlobalMediaModalView()" href="javascript:void(0);" class="tp-upload-btn"><i class="fa fa-window-restore"></i>{{ __('Browse') }}</a>
											</div>
											<em>e.g. Facebook share image. Recommended image size width: 600px and height: 315px.</em>
											<div id="remove_og_image" class="select-image dnone">
												<div class="inner-image" id="view_og_image">
												</div>
												<a onClick="onMediaImageRemove('og_image')" class="media-image-remove" href="javascript:void(0);"><i class="fa fa-remove"></i></a>
											</div>
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

<!--Global Media-->
@include('backend.partials.global_media')
<!--/Global Media/-->

@endsection

@push('scripts')
<!-- css/js -->
<script type="text/javascript">
var media_type = 'SEO_Image';

var og_image = "{{ $datalist['og_image'] }}";
if(og_image == ''){
	$("#remove_og_image").hide();
	$("#og_image").html('');
}
if(og_image != ''){
	$("#remove_og_image").show();
	$("#view_og_image").html('<img src="'+public_path+'/media/'+og_image+'">');
}
</script>
<script src="{{asset('backend/pages/theme_option_seo.js')}}"></script>
<script src="{{asset('backend/pages/global-media.js')}}"></script>
@endpush