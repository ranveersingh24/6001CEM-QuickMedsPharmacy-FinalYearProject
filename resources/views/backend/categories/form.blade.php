<div class="col-md-12">
<div class="alert alert-info alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h5><i class="icon fas fa-info"></i> Notice!</h5>
                  <li>
            Note: If "<b>Show on menu bar</b>" is checked, the Conditions will appear at the <b>Frontend Top Menu Bar</b> .
        </li>
                </div>

<div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">New Condition</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
         <div class="col-md-4">

		<div class="form-group">
			<div class="row">
				<div class="col-sm-4">
					Code: <span class="important-text">*</span>
				</div>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="code" value="{{ isset($category) ? $category->code : old('code') }}" placeholder="Code *">
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-4">
					Name: <span class="important-text">*</span>
				</div>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="category_name" value="{{ isset($category) ? $category->category_name : old('category_name') }}" placeholder="Name *">
				</div>
			</div>
		</div>
	</div>		
         </div>
      </div>	
      </div>   


</div>
  
    