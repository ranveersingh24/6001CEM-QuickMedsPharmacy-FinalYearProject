@extends('layouts.admin_app')
@section('css')
<style type="text/css">
	span.input-icon{
		display: block !important;
	}
</style>
@endsection
@section('content')
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Profile
            	<small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            @if(Auth::check())
            	@if(!empty(Auth::user()->f_name) && !empty(Auth::user()->l_name))
            		{{ Auth::user()->f_name }} {{ Auth::user()->l_name }} 
            	@else
            		{{ Auth::user()->email }}
            	@endif
            @endif
        </small>
            </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Home</a></li>
              <li class="breadcrumb-item active">Profile</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

@if($errors->any())
                      <div class="alert alert-danger">{!! implode('<br/>', $errors->all(':message')) !!}</div>
                    @endif
					<div class="space"></div>

					<form class="form-horizontal" method="POST" action="{{ route('admin.admins.update', Auth::user()->id) }}" enctype="multipart/form-data">
						@csrf
						@method('PUT')
          <div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab"><i class="green ace-icon fa fa-pencil-square-o bigger-125"></i>
										@if(Auth::guard('admin')->check())
											Company Info
										@else
											Personal Info
										@endif</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab"><i class="blue ace-icon fa fa-key bigger-125"></i>
										Password</a></li>
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab"><i class="blue ace-icon fa fa-cogs bigger-125"></i>
										Website Setting Logo</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                    					<div class="tab-content profile-edit-tab-content">
								<div id="edit-basic" class="tab-pane in active">
									<h4 class="header blue bolder smaller">General</h4>
									<div class="row">
										<div class="col-sm-6">
											<input type="text" class="form-control" placeholder="First Name" name="f_name" value="{{ Auth::user()->f_name }}">
										</div>
										<div class="col-sm-6">
											<input type="text" class="form-control" placeholder="Last Name" name="l_name" value="{{ Auth::user()->l_name }}">
										</div>
									</div>
									 <div class="space-12"></div>
									<div class="row">
										<div class="col-sm-6">
											<i class="fa fa-calendar" aria-hidden="true"></i>
											<div class="input-group">
												<input class="form-control date-picker" id="form-field-date" type="text" data-date-format="dd-mm-yyyy" placeholder="dd-mm-yyyy" name="dob" value="{{ Auth::user()->dob }}" />
												
											</div>
										</div>
										<div class="col-sm-6">
											<label class="inline">
												<input name="gender" type="radio" value="Male" class="ace" 
												 {{ Auth::user()->gender == 'Male' ? 'checked' : '' }} />
												<span class="lbl middle"> Male</span>
											</label>

											&nbsp; &nbsp; &nbsp;
											<label class="inline">
												<input name="gender" type="radio" value="Female" class="ace" 
												 {{ Auth::user()->gender == 'Female' ? 'checked' : '' }} />
												<span class="lbl middle"> Female</span>
											</label>
										</div>
									</div>
<br>
									<h4 class="header blue bolder smaller">Contact</h4>
									<p class="important-text">* Email & Phone for  display on frontend</p>
									<div class="row">
										<div class="col-sm-6">
											<span class="input-icon input-icon-right">
												<i class="fa fa-envelope" aria-hidden="true"></i>
												<input type="email" id="form-field-email" class="form-control" name="contact_email" value="{{ Auth::user()->contact_email }}" placeholder="Email address" />
												
											</span>
										</div>
										<div class="col-sm-6">
											<span class="input-icon input-icon-right">
												<i class="fa fa-phone" aria-hidden="true"></i>
												<input class="form-control input-mask-phone" type="text" name="phone" id="form-field-phone" value="{{ Auth::user()->phone }}" placeholder="Phone" onkeypress="return isNumberKey(event)" />
												
											</span>		
										</div>
									</div>
								
									<div class="space-12"></div>

									<div class="row">
										<div class="col-sm-6">
											<i style='font-size:20px' class='fas'>&#xf2bb;</i>
											<textarea class="form-control" name="address" placeholder="Address">{{ (!empty($setting)) ? $setting->address : '' }}</textarea>
										</div>
									</div>
								</div>	
</div>
                    

                    
          
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                    <!-- The timeline -->
                	<div id="edit-password" class="tab-pane">
									<h4 class="header blue bolder smaller">
										Change New Password
									</h4>

									
									<input type="password" name="password" class="form-control" id="form-field-pass1" placeholder="New Password" />
									

									<div class="space-12"></div>
									<br>

									<input type="password" name="password_confirmation" class="form-control" id="form-field-pass2" placeholder="Confirm New Password" />

								</div>
                  </div>
                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="settings">
         			<div id="website-setting" class="tab-pane">
									
									@if(Auth::guard('admin')->check())
									<h4 class="header blue bolder smaller">
										Website Logo
										<label>
				                            <input name="logo_hidden" type="checkbox" class="ace" value="1" {{ Auth::user()->logo_hidden == '1' ? 'checked' : '' }} />
				                            <span class="lbl"> </span>
				                        </label>
										<p class="important-text" style="font-size: 12px;">Click the checkbox if you would like to display it in your website</p>
									</h4>

									<div class="row">
										<div class="col-sm-12">
											<input type="file" name="website_logo" class="form-control">
											@if(!empty(Auth::user()->website_logo))
												<img src="{{ url(Auth::user()->website_logo) }}" style="width: 100px;">
											@endif
										</div>
									</div>
<br>
									<h4 class="header blue bolder smaller">
										Website Name
										<label>
				                            <input name="name_hidden" type="checkbox" class="ace" value="1" {{ Auth::user()->name_hidden == '1' ? 'checked' : '' }} />
				                            <span class="lbl"> </span>
				                        </label>
									<p class="important-text" style="font-size: 12px;">Click the checkbox if you would like to display it in your website</p>
									</h4>
									<div class="row">
										<div class="col-sm-12">
											<input type="text" class="form-control" name="website_name" value="{{ Auth::user()->website_name }}">
										</div>
									</div>
									@endif

									<br>
									<h4 class="header blue bolder smaller">
										Profile Logo
									</h4>
									<div class="row">
										<div class="col-sm-12">
											<input type="file" name="profile_logo" class="form-control">
											@if(!empty(Auth::user()->profile_logo))
											<img src="{{ url(Auth::user()->profile_logo) }}" style="width: 70px;">
											@endif
										</div>
									</div>
								</div>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>

						<div class="submit-form-btn">
	                 <div class="clearfix form-actions" align="right">
			             <button class="btn btn-primary">
			             <i class="fa fa-check"> Save Changes</i>
		               </button>
	                  </div>
                    </div>

					</form>

		
@endsection

@section('js')
<script type="text/javascript">
	$('.date-picker').datepicker().next().on(ace.click_event, function(){
		$(this).prev().focus();
	})

	var descriptionUrl = '{{ route("CKEditorUploadImage", ["_token" => csrf_token(), "p_id"=> ":p_id", "type" => "1" ]) }}';

	var about_us = CKEDITOR.instances["about_us"];
  		descriptionUrl = descriptionUrl.replace(':p_id', '1');

	if(!about_us){
	    CKEDITOR.replace( 'about_us',{
	        filebrowserUploadUrl: descriptionUrl,
	        filebrowserUploadMethod: 'form'
	    });
	}

	var setting_faqs_description = CKEDITOR.instances["setting_faqs_description"];
  		descriptionUrl = descriptionUrl.replace(':p_id', '1');

	if(!setting_faqs_description){
	    CKEDITOR.replace( 'setting_faqs_description',{
	        filebrowserUploadUrl: descriptionUrl,
	        filebrowserUploadMethod: 'form'
	    });
	}

	var privacy_policy_description = CKEDITOR.instances["privacy_policy_description"];
  		descriptionUrl = descriptionUrl.replace(':p_id', '1');

	if(!privacy_policy_description){
	    CKEDITOR.replace( 'privacy_policy_description',{
	        filebrowserUploadUrl: descriptionUrl,
	        filebrowserUploadMethod: 'form'
	    });
	}

	var return_policy_description = CKEDITOR.instances["return_policy_description"];
  		descriptionUrl = descriptionUrl.replace(':p_id', '1');

	if(!return_policy_description){
	    CKEDITOR.replace( 'return_policy_description',{
	        filebrowserUploadUrl: descriptionUrl,
	        filebrowserUploadMethod: 'form'
	    });
	}

	var shipping_policy_description = CKEDITOR.instances["shipping_policy_description"];
  		descriptionUrl = descriptionUrl.replace(':p_id', '1');

	if(!shipping_policy_description){
	    CKEDITOR.replace( 'shipping_policy_description',{
	        filebrowserUploadUrl: descriptionUrl,
	        filebrowserUploadMethod: 'form'
	    });
	}






</script>
@endsection