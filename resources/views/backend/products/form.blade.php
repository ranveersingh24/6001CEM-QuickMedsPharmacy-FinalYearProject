@php
if(isset($product)){
	$action_url = route('product.products.update', $product->id);
}else{
	$action_url = route('product.products.store');
}

@endphp
<div class="col-md-12">
<div class="alert alert-info alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h5><i class="icon fas fa-info"></i> Notice!</h5>
        <li>
            Fill in <b>Quantity</b> for the stock.
        </li>
                </div>

	<div class="col-lg-12">
		<form method="POST" action="{{ $action_url }}" id="product-form">
			@csrf
			@if(isset($product))
			@method('PUT')
			@endif
			<div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">New Medicine Details</h3>

            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
				<div class="form-group">
							<div class="form-group">
				<div class="row">
					<div class="col-sm-2">

					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="row">
					<div class="col-sm-2">
						Name: <span class="important-text">*</span>
					</div>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="product_name" value="{{ isset($product) ? $product->product_name : old('product_name') }}" placeholder="Name *">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-2">
						Medicine: <span class="important-text">*</span>
					</div>
					<div class="col-sm-6">
						<select class="form-control category_id" name="category_id">
							<option value="">Select Medicine</option>
							@foreach($categories as $category)
							<option {{ (isset($product) && $product->category_id == $category->id) ? 'selected': '' }} value="{{ $category->id }}">
								{{ $category->category_name }}
							</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-4 item_code">
						@if(isset($product) && !empty($product->category_id) && empty($product->sub_category_id))
						{{ isset($product) ? "Item code: ".$product->item_code : '' }}
						@endif
					</div>
				</div>
			</div>

			
			<input type="hidden" name="item_code" class="hidden_item_code" value="{{ isset($product) ? $product->item_code: '' }}">

			
			
			<div class="form-group">
				<div class="row">
					<div class="col-sm-2">
					 Varieties: 
					</div>
					<div class="col-sm-10">
						<select class="form-control" name="brand_id">
							<option value>Select Varieties</option>
							@foreach($brands as $brand)
							<option {{ (isset($product) && $product->brand_id == $brand->id) ? 'selected': '' }} value="{{ $brand->id }}">
								{{ $brand->brand_name }}
							</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>

<input type="hidden" name="variation_enable" class="variation_enable" value="{{ isset($product) ? $product->variation_enable : '0' }}">
			<div class="non-variation-tab"
				 style="{{ (!isset($product) || (isset($product) && $product->variation_enable == 0)) ? 'display: block;' : 'display: none;'  }}">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-6">
							<div class="row">
								<div class="col-sm-4">
									Customer Price (RM): <span class="important-text">*</span>
								</div>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="price" value="{{ isset($product) ? $product->price : old('price') }}" placeholder="Price *" onkeypress="return isNumberKey(event)">
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="row">
								<div class="col-sm-4">
									Customer Special Price (RM):
								</div>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="special_price" value="{{ isset($product) ? $product->special_price : old('special_price') }}" placeholder="Special Price" onkeypress="return isNumberKey(event)">
								</div>
							</div>
						</div>
					</div>
				</div>

			

				<div class="form-group">
					<div class="row">
						<div class="col-sm-2">
							Quantity: <span class="important-text">*</span>
						</div>
						<div class="col-sm-10">
							@if(isset($product))
								{{ $stockBalance }} 
							@else
								<input type="text" class="form-control" name="quantity" value="{{ isset($product) ? $product->quantity : old('quantity') }}" placeholder="Quantity *" onkeypress="return isNumberKey(event)">
							@endif
						</div>
					</div>
				</div>



				<div class="form-group">
					<div class="row">
						<div class="col-sm-2">
							Weight (KG): 
						</div>
						<div class="col-sm-10">
							<input type="text" name="weight" class="form-control" value="{{ isset($product) ? $product->weight : old('weight') }}" placeholder="Weight (KG)" onkeypress="return isNumberKey(event)">
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="form-group">
					<a href="#" class="btn btn-block btn-primary add-variation">
						Add Variation
					</a>
				</div>
				</div>
			</div>
				</div>
            </div>		
         </div>
      </div>	
</div>
		<div class="col-lg-12">
			<div class="variation-tab" 
				 style="{{ (isset($product) && $product->variation_enable == 1) ? 'display: block;' : 'display: none;'  }}">
				<div class="row">
					<div class="col-xs-11">
						<h4>Medicine Variation</h4>
					</div>
					<div class="col-lg-10" align="right">
						<a href="#" class="delete-variation">
							<i class="fa fa-times"></i>
						</a>
					</div>
				</div>
				<hr>
				<div class="form-group">
					<div class="row" style="">
						<div class="col-md-3">
							<b>Medicine Name</b>
						</div>
						<div class="col-md-1">
							<b>Price</b>
						</div>
						<div class="col-md-1">
							<b>Special Price</b>
						</div>
						<div class="col-md-1">
							<b>Weight</b>
						</div>
						<div class="col-md-1">
							<b>Stock</b>
						</div>
					</div>
				</div>
				<div class="form-group variation_box">
					<div class="parent_variation">
						@if(!empty($variations))
						@foreach($variations as $variation)
							<input type="hidden" name="vid[]" value="{{ $variation->id }}">
							<div class="form-group child-row">
								<div class="row">
									<div class="col-md-3">
										<input type="text" name="variation_name[]" class="form-control" placeholder="Variation Name" value="{{ $variation->variation_name }}">
									</div>
									<div class="col-md-1">
										<input type="text" name="variation_price[]" class="form-control" placeholder="Price" value="{{ $variation->variation_price }}">
									</div>
									<div class="col-md-1">
										<input type="text" name="variation_special_price[]" class="form-control" placeholder="Special Price" value="{{ $variation->variation_special_price }}">
									</div>
									<div class="col-md-1">
										<input type="text" name="variation_weight[]" class="form-control" placeholder="Weight"  value="{{ $variation->variation_weight }}">
									</div>
									<div class="col-md-1">
										<input type="hidden" name="variation_stock[]" class="form-control" placeholder="Stock" value="{{ $vStock[$variation->id] }}">
										{{ $vStock[$variation->id] }}
										&nbsp;&nbsp; 
										<a href="{{ route('variation_stock', [$variation->id]) }}" class="green">
											<i class="ace-icon fa fa-upload bigger-130"></i>
										</a>
									</div>
								</div>
							</div>
						@endforeach
						@endif
						<div class="form-group child-row">
							<div class="row">
								<div class="col-md-3">
									<input type="text" name="variation_name[]" class="form-control" placeholder="Variation Name">
								</div>
								<div class="col-md-1">
									<input type="text" name="variation_price[]" class="form-control" placeholder="Price">
								</div>
								<div class="col-md-1">
									<input type="text" name="variation_special_price[]" class="form-control" placeholder="Special Price">
								</div>
								<div class="col-md-1">
									<input type="text" name="variation_weight[]" class="form-control" placeholder="Weight">
								</div>
								<div class="col-md-1">
									<input type="text" name="variation_stock[]" class="form-control" placeholder="Stock">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group" align="center">
						<button class="add-row-btn" id="add-row-btn">
							<i class="fa fa-plus"></i>
						</button>
					</div>
				</div>
			</div>
			</div>
			
		<div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Description</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-12">
						<textarea class="form-control" name="description" id="description">{!! isset($product) ? $product->description : old('description') !!}</textarea>
					</div>
					</div>
				</div>
            </div>		
         </div>
      </div>	
</div>	
	

			
<div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Short Description</h3>

            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-10">
						<input type="text" class="form-control" name="short_description" value="{{ isset($product) ? $product->short_description : old('short_description') }}" placeholder="Short Description">
					</div>
					</div>
				</div>
            </div>		
         </div>
      </div>
      </div>
     
 </form>			
		

		<div class="form-group">
			<div class="row">
				<div class="col-sm-2">
					Image: 
				</div>
				<div class="col-sm-12">
					<div class="form-group product-image-list">
						<div class="row">
							
						</div>
						<div class="clear-both"></div>
					</div>
					<!-- <div class="form-group">
						<form method="POST" action="" class="asdasd" id="upload_image_form" enctype="multipart/form-data">
							<input type="file" name="upload_image" id="upload_image" class="form-control" />
							<br />
				  			<div id="uploaded_image"></div>
						</form>
					</div> -->
					<div>
						<form method="POST" action="{{ route('uploadImage', isset($product->id) ? $product->id : 0) }}" class="dropzone well" id="dropzone" enctype="multipart/form-data">
							@csrf
							<div class="fallback">
								<input name="file" type="file" multiple="" />
							</div>
						</form>
					</div>

					<div id="preview-template" class="hide">
						<div class="dz-preview dz-file-preview">
							<div class="dz-image">
								<img data-dz-thumbnail="" />
							</div>

							<div class="dz-details">
								<div class="dz-size">
									<span data-dz-size=""></span>
								</div>

								<div class="dz-filename">
									<span data-dz-name=""></span>
								</div>
							</div>

							<div class="dz-progress">
								<span class="dz-upload" data-dz-uploadprogress=""></span>
							</div>

							<div class="dz-error-message">
								<span data-dz-errormessage=""></span>
							</div>

							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="uploadimageModal" class="modal bs-example-modal-lg" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal">&times;</button>
        		<h4 class="modal-title">Upload & Crop Image</h4>
      		</div>
      		<div class="modal-body">
        		<div class="row">
  					<div class="col-md-12 text-center">
  						<center>
							<div id="image_demo" style="width: 100%; margin-top:30px"></div>  							
  						</center>
  					</div>
  					<div class="form-group" align="center">
						<button class="btn btn-success crop_image">Crop & Upload Image</button>
					</div>
				</div>
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      		</div>
    	</div>
    </div>
</div>