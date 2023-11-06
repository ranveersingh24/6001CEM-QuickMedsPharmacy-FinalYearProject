<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductImage;
use App\SettingRefferalReward;
use App\Cart;
use App\Merchant;
use App\Admin;
use App\Affiliate;
use App\AgentLevel;
use App\SettingMerchantBonus;
use App\AgentRebateHistory;
use App\AffiliateCommission;
use App\Permission;
use App\Transaction;
use App\WithdrawalTransaction;
use App\Category;
use App\Brand;
use App\Product;
use App\Promotion;
use App\SettingMerchantCommission;
use App\SettingMerchantRebate;
use App\CategoryImage;
use App\TransactionDetail;
use App\User;
use App\SettingBanner;
use App\ProductQrcode;
use App\ProductVariation;
use App\SubCategory;
use App\SettingSpecificDate;

use Validator, Redirect, Toastr, DB, File, Auth, Mail;

class AjaxController extends Controller
{
    public function uploadImage(Request $request, $id)
    {	

        $files = $request->file('file'); 
        $name = $files->getClientOriginalName();
        $exp = explode(".", $name);
        $file_ext = end($exp);
        $name = md5($name.date('Y-m-d H:i:s')).'.'.$file_ext;
        
        $input = $request->all();
        if($id == 0){
          $input['product_id'] = $id;
          $input['status'] = '99';
          $input['image'] = "uploads/".$name;

          $files->move("uploads/", $name);
        }else{
          $input['product_id'] = $id; 
          $input['status'] = '1';
          $input['image'] = "uploads/".$id."/".$name;

          $files->move("uploads/".$id."/", $name);
        }
        $product_image = ProductImage::create($input);
      
        
    if($id == 0){
      $select = ProductImage::where('status', '99')->get();
    }else{
      $select = ProductImage::where('status', '1')
                   ->where('product_id', $id)
                   ->get();
    }
        // return 123;

		$image_list = "";
		if (!$select->isEmpty()) { 
			foreach($select as $key => $value){

        $exp = explode(".", $value->image);
        $file_ext = end($exp);

				$image_list .= '<div class="product-image-thumbnail">
        									<div class="form-group">
        										<div class="delete-image-box">
        											<a href="#" class="delete-image" data-id="'.$value->id.'">
        												<i class="fa fa-trash"></i>
        											</a>
        										</div>';
        if($file_ext == 'mp4'){
          $image_list .= '<video id="myVideo" style="width: 100%;" >
                            <source src="'.url($value->image).'" type="video/mp4">
                          </video>';
        }else{
          $image_list .= '<div class="product-image-thumbnail-img" style="background-image: url('.url($value->image).')"></div>';
        }
        $image_list .= '</div>
        								</div>';
			}
		}

		return json_encode($image_list);
    }

    public function LoadImage($id)
    {
    	if($id == 0){
			$select = ProductImage::where('status', '99')->get();
		}else{
			$select = ProductImage::where('status', '1')
								   ->where('product_id', $id)
								   ->get();
		}

		$image_list = "";
		if (!$select->isEmpty()) {
			foreach($select as $key => $value){
        $exp = explode(".", $value->image);
        $file_ext = end($exp);

				$image_list .= '<div class="product-image-thumbnail">
                          <div class="form-group">
                            <div class="delete-image-box">
                              <a href="#" class="delete-image" data-id="'.$value->id.'">
                                <i class="fa fa-trash"></i>
                              </a>
                            </div>';
        if($file_ext == 'mp4'){
          $image_list .= '<video id="myVideo" style="width: 100%;" autoplay="autoplay" loop="1">
                            <source src="'.url($value->image).'" type="video/mp4">
                          </video>';
        }else{
          $image_list .= '<div class="product-image-thumbnail-img" style="background-image: url('.url($value->image).')"></div>';          
        }
        $image_list .= '</div>
                        </div>';
			}
		}

		return $image_list;
    }

    public function DeleteImage($id)
    {
    	$delete = ProductImage::find($id);
    	File::delete($delete->image);
    	$delete = $delete->delete();
    }

    public function uploadBannerImage(Request $request)
    {
        $files = $request->file('file'); 
        $name = $files->getClientOriginalName();
        $exp = explode(".", $name);
        $file_ext = end($exp);
        $name = md5($name.date('Y-m-d H:i:s')).'.'.$file_ext;
          
        $input = $request->all();
          
        $input['status'] = '1';
        $input['image'] = "uploads/banner/".$name;

        $files->move("uploads/banner/", $name);
          
        $product_image = SettingBanner::create($input);
        
        
        $select = SettingBanner::where('status', '1')->get();
        

      $image_list = "";
      if (!$select->isEmpty()) { 
        foreach($select as $key => $value){
          $image_list .= '<div class="product-image-thumbnail">
                            <div class="form-group">
                              <div class="delete-image-box">
                                <a href="#" class="delete-image" data-id="'.$value->id.'">
                                  <i class="fa fa-trash"></i>
                                </a>
                              </div>
                              <div class="product-image-thumbnail-img" style="background-image: url('.url($value->image).')"></div>
                            </div>
                          </div>';
        }
      }

      return json_encode($image_list);
    }

    public function LoadBannerImage()
    {
      $select = SettingBanner::where('status', '1')->get();

    $image_list = "";
    if (!$select->isEmpty()) {
      foreach($select as $key => $value){
        $image_list .= '<div class="product-image-thumbnail">
                  <div class="form-group">
                    <div class="delete-image-box">
                      <a href="#" class="delete-image" data-id="'.$value->id.'">
                        <i class="fa fa-trash"></i>
                      </a>
                    </div>
                    <div class="product-image-thumbnail-img" style="background-image: url('.url($value->image).')"></div>
                  </div>
                </div>';
      }
    }

    return $image_list;
    }

    public function DeleteBannerImage($id)
    {
      $delete = SettingBanner::find($id);
      File::delete($delete->image);
      $delete = $delete->delete();
    }

    public function uploadCategoryImage(Request $request, $id)
    { 
        $image = $request->image;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = str_random(10).'.'.'png';
        
        $input = $request->all();
        if($id == 0){
          
          $input['status'] = '99';
          $input['image'] = "uploads/".$imageName;

          \File::put('uploads/'. '/' . $imageName, base64_decode($image));

          $cat_image = CategoryImage::create($input);
        }else{

          $input['status'] = '1';
          $input['image'] = "uploads/".$imageName;

          \File::put('uploads/' . $imageName, base64_decode($image));

          $category = CategoryImage::where('category_id', $id)->first();
          if(!empty($category)){
            $category = CategoryImage::where('category_id', $id)->update($input);
          }else{
            $input['category_id'] = $id;
            $cat_image = CategoryImage::create($input);
          }
        }

        // 
      
        
        if($id == 0){
          $select = CategoryImage::where('status', '99')->get();
        }else{
          $select = CategoryImage::where('status', '1')
                       ->where('category_id', $id)
                       ->get();
        }

        $image_list = "";
        if (!$select->isEmpty()) { 
          foreach($select as $key => $value){
            $image_list .= '<div class="product-image-thumbnail">
                              <div class="form-group">
                                <div class="delete-image-box">
                                  <a href="#" class="delete-image" data-id="'.$value->id.'">
                                    <i class="fa fa-trash"></i>
                                  </a>
                                </div>
                                <div class="product-image-thumbnail-img" style="background-image: url('.url($value->image).')"></div>
                              </div>
                            </div>';
          }
      }
      return $image_list;
    }


    public function LoadCategoryImage($id)
    {
      if($id == 0){
        $select = CategoryImage::where('status', '99')->get();
      }else{
        $select = CategoryImage::where('status', '1')
                     ->where('category_id', $id)
                     ->get();
      }


      $image_list = "";
      if (!$select->isEmpty()) {
        foreach($select as $key => $value){
          $image_list .= '<div class="product-image-thumbnail">
                    <div class="form-group">
                      
                      <div class="product-image-thumbnail-img" style="background-image: url('.url($value->image).')"></div>
                    </div>
                  </div>';
        }
      }
      return $image_list;
    }
    public function DeleteCategoryImage($id)
    {
      $delete = CategoryImage::find($id);
      File::delete($delete->image);
      $delete = $delete->delete();
    }

    public function change_withdrawal_transaction_action(Request $request)
    {
        $input = $request->all();
        $input['status'] = $request->action_id;

        $withdrawal = WithdrawalTransaction::find($request->tid);
        $withdrawal = $withdrawal->update($input);
    }


    
    public function SetPermission(Request $request)
    {
        $select = Permission::where('permission_lvl', $request->permission_lvl)
                            ->where('page', $request->page)
                            ->first();

        if(!empty($select->id)){
            $update = Permission::find($select->id);
            if($update->status == 1){

                // $update = $update->update(['status' => '0']);
            }else{
                $update = $update->update(['status' => '1']);
            }
        }else{
            $input = $request->all();
            $create = Permission::create($input);            
        }
        
    }

    public function UnsetPermission(Request $request)
    {
        $select = Permission::where('permission_lvl', $request->permission_lvl)
                            ->where('page', $request->page)
                            ->first();

        if(!empty($select->id)){
            $update = Permission::find($select->id);
            if($update->status == 1){
                $update = $update->update(['status' => '0']);
            }else{
                // $update = $update->update(['status' => '1']);
            }
        }else{
            $input = $request->all();
            $create = Permission::create($input);            
        }
        
    }

    public function GetPermission()
    {
        $selects = Permission::get();
        return $selects;
    }

    public function getItemCode(Request $request)
    {

        $category = Category::find($request->cid);
        if(empty($category->id)){
            return "null";
        }
        $pCount = Product::select(DB::raw('COUNT(id) AS TotalCount'))
                          ->where('category_id', $request->cid)
                          ->first();

        $product = Product::where('id', $request->pid)
                          ->where('category_id', $request->cid)
                          ->first();

        if(!empty($product->code)){
            return $category->code.$product->code;
        }else{
            $totalCount = $pCount->TotalCount+1;
        }

        if(strlen($totalCount) == 1){
            $code = $category->code."00".$totalCount;
        }elseif(strlen($totalCount) == 2){
            $code = $category->code."0".$totalCount;
        }else{
            $code = $category->code.$totalCount;
        }

        return $code;
    }

    

    public function ProductStatus(Request $request)
    {
        $input = $request->all();
        $input['status'] = $request->status;
        $table = Product::find($request->row_id);
        $table = $table->update($input);
    }

    public function CategoryStatus(Request $request)
    {
        $input = $request->all();
        $input['status'] = $request->status;
        $table = Category::find($request->row_id);
        $table = $table->update($input);
    }

    public function BrandStatus(Request $request)
    {
        $input = $request->all();
        $input['status'] = $request->status;
        $table = Brand::find($request->row_id);
        $table = $table->update($input);
    }

    public function PromotionStatus(Request $request)
    {
        $input = $request->all();
        $input['status'] = $request->status;
        $table = Promotion::find($request->row_id);
        $table = $table->update($input);
    }


    public function change_transaction_action(Request $request)
    {

        $select = Transaction::find($request->tid);
        $input = $request->all();
        $input['status'] = $request->action_id;
        $transaction = Transaction::find($request->tid);
        $details = TransactionDetail::where('transaction_id', $select->id)->get();
        
        $amount = $select->grand_total - $select->shipping_fee - $select->processing_fee;
        if($request->action_id == '11'){
          $transaction = $transaction->update(['completed'=>'1']);
        }
        
        if($request->action_id == '1'){
            $transaction = $transaction->update(['status'=>'1']);
            
            $merchant = Merchant::where('code', $select->user_id)->first();
            if(!empty($merchant->id)){
              $SettingMerchantRebate = SettingMerchantRebate::where('agent_lvl', $merchant->lvl)->first();
              if(!empty($SettingMerchantRebate->id)){
                  foreach($details as $detail){
                    $SettingSpecificDate = SettingSpecificDate::where('specific_date', date('Y-m-d'))
                                                              ->whereRaw('find_in_set('.$detail->product_id.', products)')
                                                              ->first();
                    if(!empty($SettingSpecificDate->id)){
                      if($SettingSpecificDate->type == '1'){
                          $extra_type = "1";
                          $extra_amount = "2";
                      }else{
                          $extra_type = "2";
                          $extra_amount = $SettingSpecificDate->amount;
                      }
                    }else{
                        $extra_type = "";
                        $extra_amount = 0;
                    }
                    // echo $extra_amount;
                    if($SettingMerchantRebate->type == 'Amount'){
                        if($extra_amount > 0){
                          if($extra_type == 1){
                              $totalPoint = ($SettingMerchantRebate->amount * $detail->quantity) * $extra_amount;  
                          }else{
                              $totalPoint = ($SettingMerchantRebate->amount * $detail->quantity) + $extra_amount;  
                          }
                        }else{
                          $totalPoint = $SettingMerchantRebate->amount * $detail->quantity;
                        }
                    }else{
                        if($extra_amount > 0){
                          if($extra_type == 1){
                              $totalPoint = (($detail->unit_price * $detail->quantity) * $SettingMerchantRebate->amount / 100) * $extra_amount;  
                          }else{
                              $totalPoint = (($detail->unit_price * $detail->quantity) * $SettingMerchantRebate->amount / 100) + $extra_amount;  
                          }
                        }else{
                          $totalPoint = ($detail->unit_price * $detail->quantity) * $SettingMerchantRebate->amount / 100;
                        }
                    }
                    // echo $totalPoint;
                    $agentBonus = AffiliateCommission::create(['user_id'=>$select->user_id,
                                                               'type'=>'2',
                                                               'transaction_no'=>$select->transaction_no,
                                                               'comm_pa_type'=>$SettingMerchantRebate->type,
                                                               'comm_pa'=>$SettingMerchantRebate->amount,
                                                               'ex_type'=>$extra_type,
                                                               'ex_amount'=>$extra_amount,
                                                               'comm_amount'=>$totalPoint,
                                                               'comm_desc'=>'Rebate on order: From Transaction #'.$select->transaction_no]);
                  }

              }
            }
            // exit();

            $affiliates = Affiliate::where('affiliate_id', $select->user_id)
                                   ->orderBy('id', 'desc')
                                   ->take(3)
                                   ->get();
            $insertAFComm = [];
            foreach($affiliates as $key => $affiliate){
                $SettingMerchantCommission = SettingMerchantCommission::where('level', $key+1)->where('agent_lvl', $merchant->lvl)->first();
                if($SettingMerchantCommission->comm_type == 'Amount'){
                    $totalCommission = $SettingMerchantCommission->comm_amount;
                }else{
                    $totalCommission = $amount * $SettingMerchantCommission->comm_amount / 100;
                }

                $insertAFComm[] = ['user_id'=>$affiliate->user_id,
                                   'type'=>'3',
                                   'transaction_no'=>$select->transaction_no,
                                   'comm_pa_type'=>$SettingMerchantCommission->comm_type,
                                   'comm_pa'=>$SettingMerchantCommission->comm_amount,
                                   'comm_amount'=>$totalCommission,
                                   'comm_desc'=>'Affiliate Reward: From Transaction #'.$select->transaction_no];

            }

            AffiliateCommission::insert($insertAFComm);
        }
        if($request->action_id == '95'){
          $transaction = $transaction->update($input);
        }

        if($request->action_id == '96'){
          $transaction = $transaction->update($input);
        }
    }

    

    public function setFeatured(Request $request)
    {
        $product = Product::find($request->id);
        
        if($product->featured == '1'){
          $product = $product->update(['featured'=>'0']);
        }else{
          $product = $product->update(['featured'=>'1']);
        }

    }

    public function CKEditorUploadImage(Request $request)
    {
        if($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName.'_'.time().'.'.$extension;
            $path = "";
            if($request->type == 1){
                $path = "Description";
            }elseif($request->type == 2){
                $path = "Hiring";
            }elseif($request->type == 3){
                $path = "Mission";
            }
            
            $request->file('upload')->move('uploads/Product_description/', $fileName);
   
            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('uploads/Product_description/'.$fileName); 
            $msg = 'Image uploaded successfully'; 
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
               
            @header('Content-type: text/html; charset=utf-8'); 
            echo $response;
        }
    }

    public function getProducts(Request $request)
    {
        $product = Product::find($request->product_id);

        if(!empty($product->id)){
            return $product->price;
        }
    }

    public function InsertQrList(Request $request)
    {
        $td = TransactionDetail::find($request->row_id);
        $input = [];
        $input['qr_list'] = $request->title;
        $ex = explode(', ', $request->title);

        
        if(!empty($td->qr_list)){
          //update
          $ex2 = explode(', ', $td->qr_list);
          $qr2 = ProductQrcode::whereIn('running_code', $ex2)->update(['status'=>'1']);
          
          $td = $td->update($input);
        }
        
        $td = $td->update($input);
        $qr = ProductQrcode::whereIn('running_code', $ex)->update(['status'=>'2']);
    }

    public function variation_list(Request $request)
    {
        $variations = ProductVariation::where('product_id', $request->id)->where('status', '1')->get();
        $listing = '<table class="table">
                    <tr>
                      <td>Variation Name</td>
                      <td></td>
                    </tr>';
        foreach($variations as $variation)
        $listing .= '<tr>
                        <td>'.$variation->variation_name.'</td>
                        <td align="center">
                            <a href="'.route("print_security_code", [$request->id, $variation->id]).'" target="_blank">
                                <i class="ace-icon fa fa-print bigger-130"></i> Print Product Security Code
                            </a>
                        </td>
                     </tr>';
        $listing .= '</table>';

        return $listing;
    }

    public function setPointRedemption(Request $request)
    {
        $product = Product::find($request->id);
        
        if($product->mall == '1'){
          $product = $product->update(['mall'=>'0']);
        }else{
          $product = $product->update(['mall'=>'1']);
        }
    }

    public function SubCategoryStatus(Request $request)
    {
        $input = $request->all();
        $input['status'] = $request->status;
        $table = SubCategory::find($request->row_id);
        $table = $table->update($input);
    }

    public function GetSubCategory(Request $request)
    {
        $subs = SubCategory::where('category_id', $request->cid)->get();

        $select = '<select class="form-control sub_category_id" name="sub_category_id">';
        $select .= "<option>Select Subcategory</option>";
        foreach($subs as $sub){
        $select .= "<option value='".$sub->id."'>".$sub->sub_category_name."</option>";
        }
        $select .="</select>";

        return $select;
    }

    public function getSubItemCode(Request $request)
    {

        $category = Category::find($request->cid);
        $sub_category = SubCategory::find($request->scid);
        if(empty($category->id)){
            return "null";
        }
        $pCount = Product::select(DB::raw('COUNT(id) AS TotalCount'))
                          ->where('category_id', $request->cid)
                          ->first();

        $product = Product::where('id', $request->pid)
                          ->where('category_id', $request->cid)
                          ->first();

        if(!empty($product->code)){
            return $category->code.$product->code;
        }else{
            $totalCount = $pCount->TotalCount+1;
        }

        if(strlen($totalCount) == 1){
            $code = $category->code.'-'.$sub_category->sub_category_code."00".$totalCount;
        }elseif(strlen($totalCount) == 2){
            $code = $category->code.'-'.$sub_category->sub_category_code."0".$totalCount;
        }else{
            $code = $category->code.'-'.$sub_category->sub_category_code.$totalCount;
        }

        return $code;
    }

    public function UserStatus(Request $request)
    {
        $input = $request->all();
        $input['status'] = $request->status;
        $table = User::find($request->row_id);
        $table = $table->update($input);
    }

    public function delete_specific_date(Request $request)
    {
        $delete = SettingSpecificDate::find($request->bid)->delete();
    }
}
