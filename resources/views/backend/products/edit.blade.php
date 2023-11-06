@extends('layouts.admin_app')

@section('content')
<div class="page-header">
    <h1>
        Medicine Details
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            {{ $product->product_name }}
        </small>
    </h1>
</div>
@if($errors->any())
  <div class="alert alert-danger">{!! implode('<br/>', $errors->all(':message')) !!}</div>
@endif
@include('backend.products.form')

<div class="submit-form-btn">
  <div class="form-group wizard-actions" align="right">
    <a href="{{ route('product.products.index') }}" class="btn btn-default">
      <i class="fa fa-ban"> 
        Cancel
      </i>
    </a>

    <button class="btn btn-primary">
      <i class="fa fa-check"> 
        Save Changes
      </i>
    </button>

  </div>
</div>

@endsection

@section('js')

<script type="text/javascript">
	// CKEDITOR.replace( 'description',{
	//                     filebrowserUploadUrl: descriptionUrl,
	//                     filebrowserUploadMethod: 'form'
	//                 });
  var descriptionUrl = '{{ route("CKEditorUploadImage", ["_token" => csrf_token(), "p_id"=> ":p_id", "type" => "1" ]) }}';

	var description = CKEDITOR.instances["description"];
  descriptionUrl = descriptionUrl.replace(':p_id', '{{ $product->id }}');

  if(!description){
      CKEDITOR.replace( 'description',{
          filebrowserUploadUrl: descriptionUrl,
          filebrowserUploadMethod: 'form'
      });
  }

    var url = '{{ route("LoadImage", ":id") }}';
    url = url.replace(':id', '{{ $product->id }}');
    
	$.ajax({
        url: url,
        type: 'get',
        success: function(response){
            $('.product-image-list .row').html(response);
            
        },
    });

</script>

<script type="text/javascript">
    jQuery(function($){
            
        try {
            
            var myDropzone = Dropzone.options.dropzone =
            {

                maxFilesize: 500000,
                renameFile: function(file) {
                    var dt = new Date();
                    var time = dt.getTime();
                   return time+file.name;
                },
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.mp4",
                timeout: 5000,
                dictRemoveFile: 'Remove',
                maxFiles: 100,
                dataType:'json',
                dictDefaultMessage :
                '<span class="bigger-150 bolder"><i class="ace-icon fa fa-caret-right red"></i> Drop files</span> to upload \
                <span class="smaller-80 grey">(or click)</span> <br /> \
                <i class="upload-icon ace-icon fa fa-cloud-upload blue fa-3x"></i>',
                
                thumbnail: function(file, dataUrl) {
                  if (file.previewElement) {
                    $(file.previewElement).removeClass("dz-file-preview");
                    var images = $(file.previewElement).find("[data-dz-thumbnail]").each(function() {
                        var thumbnailElement = this;
                        thumbnailElement.alt = file.name;
                        thumbnailElement.src = dataUrl;
                    });
                    setTimeout(function() { $(file.previewElement).addClass("dz-image-preview"); }, 1);

                  }
                },
                success: function(file, response) 
                {
                    $('.product-image-list .row').html(response);
                },
            };
        
          //simulating upload progress
          var minSteps = 6,
              maxSteps = 60,
              timeBetweenSteps = 100,
              bytesPerStep = 100000;
        
          
        
           
           //remove dropzone instance when leaving this page in ajax mode
           $(document).one('ajaxloadstart.page', function(e) {
                try 
                {
                    myDropzone.destroy();
                } catch(e) {}
           });
        
        } catch(e) {
          alert('Dropzone.js does not support older browsers!');
        }
        
    });

    $('.submit-form-btn .btn-primary').click( function(e){
    	e.preventDefault();

      var v_enable =  $('.variation_enable').val();
      if(v_enable == 1){
        var checkRow;
        var checkProductsQty;
        var productQtyVal;
        var checkProducts;

        $(".child-row input[name='variation_name[]']").each(function( index ) {
          var productVal = $(this).closest('.child-row').find('input[name="variation_price[]"]');
          var productQtyVal = $(this).closest('.child-row').find('input[name="variation_stock[]"]');
         

          

          if($(this).val()){
            
            checkRow = 1;
            if(parseFloat(productVal.val()) && parseFloat(productVal.val()) != 0 && !parseFloat(productQtyVal.val())){


              checkProductsQty = 1;
              productQtyVal.addClass('input-required-field');

            }else if(!parseFloat(productVal.val()) && parseFloat(productQtyVal.val()) && parseFloat(productQtyVal.val()) != 0){

              productVal.addClass('input-required-field');
              checkProducts = 1;

            }else if(!parseFloat(productAgentVal.val()) && parseFloat(productQtyVal.val()) && parseFloat(productQtyVal.val()) != 0){

              productAgentVal.addClass('input-required-field');
              checkProducts = 1;

            }else if(!parseFloat(productVal.val()) && !parseFloat(productQtyVal.val())){

              productVal.addClass('input-required-field');
              productQtyVal.addClass('input-required-field');
              checkProducts = 1;
              checkProductsQty = 1;

            }
          }else{
            if(parseFloat(productVal.val()) || parseFloat(productQtyVal.val())){
              $(this).addClass('input-required-field');
            }
          }
      });
        
        if(checkRow != 1){
          alert('Please add at least one variation');
          return false;
        }

        if(checkProductsQty == 1){
          alert("Please add variation's minimum purchase quantity (At least 1)");
          return false;
        }

        if(checkProducts == 1){
          alert("Please insert variation price");
          return false;
        }        
      }
    	
      // var mall = $('.mall').prop("checked");

      // if(mall == true){
      //     var point_price = $('.point_price');
      //     var point_agent_price = $('.point_agent_price');

      //     if(!point_price.val() || !point_agent_price.val()){
      //         alert('Please fill in customer point & agent point');
      //         return false;
      //     }
      // }

    	$('#product-form').submit();
    });

    $('.product-image-list').on('click', '.product-image-thumbnail .delete-image', function(e){
        e.preventDefault();
        var delete_btn = $(this);
        if(confirm('Delete This Image?') == true){
            var url = '{{ route("DeleteImage", ":id") }}';
            url = url.replace(':id', $(this).data('id'));
            $.ajax({
                url: url,
                type: 'get',
                success: function(response){
                    delete_btn.closest('.product-image-thumbnail').hide();
                },
            });
        }else{
            return false;
        }
    });

    $('.category_id').change(function(){
        $('.loading-gif').show();
        var ele = $(this);

        var fd = new FormData();
        fd.append('cid', ele.val());
        fd.append('pid', '');
        fd.append('_token', '{{ csrf_token() }}');

        $.ajax({
           url: '{{ route("getItemCode") }}',
           type: 'post',
           data: fd,
           contentType: false,
           processData: false,
           success: function(response){
                $('.loading-gif').hide();
                if(response != 'null'){
                    $('.hidden_item_code').val(response);
                    $('.item_code').html('Item Code: '+response);
                }else{
                    $('.item_code').html(' ');
                }
           },
        });

        $.ajax({
             url: '{{ route("GetSubCategory") }}',
             type: 'post',
             data: fd,
             contentType: false,
             processData: false,
             success: function(response){
                  $('.loading-gif').hide();
                  $('.sub_category').html(response);

                  $('.sub_category_id').change( function(e){

                      var fd = new FormData();
                          fd.append('cid', $('.category_id').val());
                          fd.append('scid', $(this).val());

                      $.ajax({
                         url: '{{ route("getSubItemCode") }}',
                         type: 'post',
                         data: fd,
                         contentType: false,
                         processData: false,
                         success: function(response){
                              $('.loading-gif').hide();
                              $('.item_code').html(' ');
                              if(response != 'null'){
                                  $('.hidden_item_code').val(response);
                                  $('.sub_item_code').html('Item Code: '+response);
                              }else{
                                  $('.sub_item_code').html(' ');
                              }
                         },
                      });
                  });
             },
          });
    });
</script>

<script>  
$(document).ready(function(){

    $image_crop = $('#image_demo').croppie({
    enableExif: true,
    viewport: {
      width: 800,
      height: 800,
      type:'square' //circle
    },
    boundary:{
      width: 800,
      height: 800
    }
  });

  $('#upload_image').on('change', function(){
    var reader = new FileReader();
    reader.onload = function (event) {
      $image_crop.croppie('bind', {
        url: event.target.result
      }).then(function(){
        console.log('jQuery bind complete');
      });
    }
    reader.readAsDataURL(this.files[0]);
    $('#uploadimageModal').modal('show');
  });

  $('.crop_image').click(function(event){
    $('.loading-gif').show();
    $image_crop.croppie('result', {
      type: 'canvas',
      size: 'viewport'
    }).then(function(response){
      $.ajax({
        url:"{{ route('uploadImage', isset($product->id) ? $product->id : 0) }}",
        type: "POST",
        data:{"image": response},
        success:function(data)
        {
          $('.loading-gif').hide();
          // alert(data);
          $('#uploadimageModal').modal('hide');
          $('.product-image-list .row').html(data);
        }
      });
    })
  });

});  

$('.add-variation').click( function(e){
    e.preventDefault();
    $('.variation-tab').show();
    $('.non-variation-tab').hide();

    $('.variation_enable').val(1);
});

$('.delete-variation').click( function(e){
    e.preventDefault();

    $('.variation-tab').hide();
    $('.non-variation-tab').show();
    $('.variation_enable').val(0);
});


$('#add-row-btn').click( function(e){
    e.preventDefault();

    $(this).closest('.variation_box').find('.parent_variation').append('<div class="form-group child-row">\
                                                                          <div class="row">\
                                                                            <div class="col-md-3">\
                                                                              <input type="text" name="variation_name[]" class="form-control" placeholder="Variation Name">\
                                                                            </div>\
                                                                            <div class="col-md-1">\
                                                                              <input type="text" name="variation_price[]" class="form-control" placeholder="Price">\
                                                                            </div>\
                                                                            <div class="col-md-1">\
                                                                              <input type="text" name="variation_special_price[]" class="form-control" placeholder="Special Price">\
                                                                            </div>\
                                                                            <div class="col-md-1">\
                                                                              <input type="text" name="variation_weight[]" class="form-control" placeholder="Weight">\
                                                                            </div>\
                                                                            <div class="col-md-1">\
                                                                              <input type="text" name="variation_stock[]" class="form-control" placeholder="Stock">\
                                                                            </div>\
                                                                          </div>');
});
</script>

@endsection