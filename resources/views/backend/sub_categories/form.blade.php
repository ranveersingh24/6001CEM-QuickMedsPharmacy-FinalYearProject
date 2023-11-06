<div class="col-lg-12">
<div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">New Sub Category</h3>

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
						<div class="col-sm-2">
					        Category: <span class="important-text">*</span>
				        </div>
						<div class="col-sm-10">
							@php
						$checkedBox = isset($sub_category) ? $sub_category->category_id : old('category_id');
					@endphp
					<select class="form-control" name="category_id">
						<option value="">Select Category</option>
						@foreach($categories as $category)
						<option {{ ($checkedBox == $category->id) ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->category_name }}</option>
						@endforeach
					</select>
						</div>
					</div>
				</div>
            </div>		
         </div>

         <div class="form-group">
			<div class="row">
				<div class="col-sm-1">
					Code: <span class="important-text">*</span>
				</div>
				<div class="col-sm-5">
					<input type="text" class="form-control" name="sub_category_code" value="{{ isset($sub_category) ? $sub_category->sub_category_code : old('sub_category_code') }}" placeholder="Code *">
				</div>
			</div>
		</div>

			<div class="form-group">
			<div class="row">
				<div class="col-sm-1">
					Name: <span class="important-text">*</span>
				</div>
				<div class="col-sm-5">
					<input type="text" class="form-control" name="sub_category_name" value="{{ isset($sub_category) ? $sub_category->sub_category_name : old('sub_category_name') }}" placeholder="Name *">
				</div>
			</div>
		</div>
	
      </div>	
</div>

	</div>
</div>