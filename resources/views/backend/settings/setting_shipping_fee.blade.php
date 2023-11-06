@extends('layouts.admin_app')
@section('content')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          	           <h1 class="m-0">Settings
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
 Setting Shipping Fee
        </small>
           </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.dashboards.index') }}">Home</a></li>
              <li class="breadcrumb-item active">     Setting Shipping Fee </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

<form method="POST" action="{{ route('save_setting_shipping_fee') }}" id="setting-merchant-form">
@csrf
<h3 class="important-text">
	
</h3>
 <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header border-1">
                <div class="d-flex justify-content-between">
                  <h2 class="card-title"><b>West Malaysia (shipping)</b></h2>
                      <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
                </div>
              </div>
              <div class="card-body">
                <div class="form-group">
			<div class="row">
				<div align="center"class="col-md-5">
					Weight (kg)
				</div>

				<div align="center"class="col-md-5">
					Shipping fee (MYR)
				</div>
			</div>
		</div>
		<div class="west row-parent" >
			@if(!$settingShippingFees->isEmpty())
			@foreach($settingShippingFees as $settingShippingFee)
				@if($settingShippingFee->area == 'west')

					<div align="center"class="form-group">
						<input type="hidden" name="sid[]" value="{{ $settingShippingFee->id }}">
						<input type="hidden" name="type[]" value="west">
						<div class="row">
							<div class="col-md-5">
								<input type="text" name="weight[]" class="form-control" placeholder='Weight (kg)' value="{{ $settingShippingFee->weight }}">
							</div>
							<div class="col-md-5">
								<input type="text" name="shipping_fee[]" class="form-control" placeholder="Shipping Fee" value="{{ $settingShippingFee->shipping_fee }}">
							</div>
							<div class="col-xs-2" align="center">
								<a href="#"  class="important-text del">
									<i class="fa fa-trash fa-2x"></i>
								</a>
							</div>
						</div>
					</div>
				@endif
			@endforeach
			@endif
			<div class="form-group" >
				<input type="hidden" name="sid[]" value="">
				<input type="hidden" name="type[]" value="west">
				<div class="row">
					<div class="col-md-5">
						<input type="text" name="weight[]" class="form-control" placeholder='Weight (kg)'>
					</div>
					<div class="col-md-5">
						<input type="text" name="shipping_fee[]" class="form-control" placeholder="Shipping Fee">
					</div>
					<div class="col-xs-3" align="center">
						<a href="#"  class="important-text del">
							<i class="fa fa-trash fa-2x"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="form-group">
			<div class="row">
				<div class="col-md-10" align="center">
					<a href="#" class="add-shipping-btn" id="add-west">
						<i class="fa fa-plus"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
              </div>
            </div>
            <!-- /.card -->
	<div class="col-lg-6">
            <div class="card">
              <div class="card-header border-1">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title"><b>East Malaysia (shipping)</b></h3>
                      <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
                </div>
              </div>
              <div class="card-body">
                     <div class="form-group">
			<div class="row">
				<div align="center"class="col-md-5">
					Weight (kg)
				</div>

				<div align="center"class="col-md-5">
					Shipping fee (MYR)
				</div>
			</div>
		</div>
		<div class="east row-parent">
			@if(!$settingShippingFees->isEmpty())
			@foreach($settingShippingFees as $settingShippingFee)
				@if($settingShippingFee->area == 'east')
					<div class="form-group">
						<input type="hidden" name="sid[]" value="{{ $settingShippingFee->id }}">
						<input type="hidden" name="type[]" value="east">
						<div class="row">
							<div class="col-md-5">
								<input type="text" name="weight[]" class="form-control" placeholder='Weight (kg)' value="{{ $settingShippingFee->weight }}">
							</div>
							<div class="col-md-5">
								<input type="text" name="shipping_fee[]" class="form-control" placeholder="Shipping fee" value="{{ $settingShippingFee->shipping_fee }}">
							</div>
							<div class="col-xs-2" align="center">
								<a href="#" class="important-text del">
									<i class="fa fa-trash fa-2x"></i>
								</a>
							</div>
						</div>
					</div>
				@endif
			@endforeach
			@endif
			<div class="form-group">
				<div class="row">
					<input type="hidden" name="sid[]" value="">
					<input type="hidden" name="type[]" value="east">
					<div class="col-md-5">
						<input type="text" name="weight[]" class="form-control" placeholder='Weight (kg)'>
					</div>
					<div class="col-md-5">
						<input type="text" name="shipping_fee[]" class="form-control" placeholder="Shipping fee">
					</div>
					<div class="col-xs-2" align="center">
						<a href="#" class="important-text del">
							<i class="fa fa-trash fa-2x"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="form-group">
			<div class="row">
				<div class="col-md-10" align="center">
					<a href="#" class="add-shipping-btn" id="add-east">
						<i class="fa fa-plus"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
              </div>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col-md-6 -->
</div>
</div>

		

</form>

<div class="submit-form-btn">
	<div class="form-group wizard-actions" align="right">
		<button class="btn btn-primary">
			<i class="fa fa-check"> Save Changes</i>
		</button>

	</div>
</div>
@endsection
@section('js')
<script type="text/javascript">
	$('.submit-form-btn .btn-primary').click( function(e){
    	e.preventDefault();
    	$('.loading-gif').show();
    	$('#setting-merchant-form').submit();
    });
	var west_item = '<div class="form-group">\
						<input type="hidden" name="sid[]" value="">\
						<input type="hidden" name="type[]" value="west">\
						<div class="row">\
							<div class="col-xs-5">\
								<input type="text" name="weight[]" class="form-control" placeholder="Weight (kg)">\
							</div>\
							<div class="col-xs-5">\
								<input type="text" name="shipping_fee[]" class="form-control" placeholder="Shipping fee (MYR)">\
							</div>\
							<div class="col-xs-2" align="center">\
								<a href="#"  class="important-text del">\
									<i class="fa fa-trash fa-2x"></i>\
								</a>\
							</div>\
						</div>\
					</div>';
    $('#add-west').click(function (e){
    	e.preventDefault();
    	$('.west').append(west_item);
    });

    var east_item = '<div class="form-group">\
    					<input type="hidden" name="sid[]" value="">\
    					<input type="hidden" name="type[]" value="east">\
						<div class="row">\
							<div class="col-xs-5">\
								<input type="text" name="weight[]" class="form-control" placeholder="Weight (kg)">\
							</div>\
							<div class="col-xs-5">\
								<input type="text" name="shipping_fee[]" class="form-control" placeholder="Shipping fee (MYR)">\
							</div>\
							<div class="col-xs-2" align="center">\
								<a href="#" class="important-text del">\
									<i class="fa fa-trash fa-2x"></i>\
								</a>\
							</div>\
						</div>\
					</div>';
    $('#add-east').click(function (e){
    	e.preventDefault();
    	$('.east').append(east_item);
    });


    $('.row-parent').on('click', '.del', function (e){
    	e.preventDefault();
    	$(this).closest('.row-parent .form-group').remove();
    });
</script>