<div class="form-group address-book-list">
	<input type="hidden" name="address_imp" value="{{ isset($address) ? md5($address->id) : '' }}">
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label>First Name</label>
				<input type="text" class="form-control required-feild" placeholder="First Name" name="f_name" value="{{ isset($address) ? $address->f_name : old('f_name') }}">
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Last Name</label>
				<input type="text" class="form-control required-feild" placeholder="Last Name" name="l_name" value="{{ isset($address) ? $address->l_name : old('l_name') }}">
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Phone</label>
				<input type="text" class="form-control required-feild" placeholder="Phone" name="phone" value="{{ isset($address) ? $address->phone : old('phone') }}" onkeypress="return isNumberKey(event)">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label>Address</label>
				<textarea class="form-control required-feild" placeholder="Address" name="address">{{ isset($address) ? $address->address : old('address') }}</textarea>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Postcode</label>
				<input type="text" class="form-control required-feild" placeholder="Postcode" name="postcode" value="{{ isset($address) ? $address->postcode : old('postcode') }}">
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Email</label>
				<input type="text" class="form-control required-feild" placeholder="Email" name="email" value="{{ isset($address) ? $address->email : old('email') }}">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label>City</label>
				<input type="text" class="form-control required-feild" placeholder="City" name="city" value="{{ isset($address) ? $address->city : old('city') }}">
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>State</label>
				<select class="form-control" name="state">
					<option>Select State</option>
					@foreach($states as $state)
					<option {{ (isset($address) && $address->state == $state->id) ? 'selected' : '' }} value="{{ $state->id }}">{{ $state->name }}</option>
					@endforeach
				</select>
			</div>
		</div>
	</div>
	<div class="form-group">
		<b id="error-message" class="important-text"></b>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="form-group">
				<button class="btn btn-primary submit-address btn-sm">
					<i class="fa fa-check"></i> &nbsp;&nbsp; Save changes
				</button>
			</div>
		</div>
	</div>
</div>