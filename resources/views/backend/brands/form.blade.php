 <div class="col-lg-12">
<div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">New Varieties</h3>

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
							Name: <span class="important-text">*</span>
						</div>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="brand_name" value="{{ isset($brand) ? $brand->brand_name : old('brand_name') }}" placeholder="Name *">
						</div>
					</div>
				</div>
            </div>		
         </div>
      </div>	
</div>
</div>