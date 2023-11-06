<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductImage;
use App\Cart;
use App\UserShippingAddress;
use App\Favourite;
use App\Product;
use App\Transaction;
use App\TransactionDetail;
use App\Merchant;
use App\Admin;
use App\User;
use App\VerifyCode;
use App\ProductVariation;


use Validator, Redirect, Toastr, DB, File, Auth, Session, DateTime;

class AjaxController extends Controller
{
    public function AddToCart(Request $request)
    {

    	if($request->quantity <= 0){
    		return "quantity error";
    	}

    	$product = Product::find($request->product_id);
      if($product->variation_enable == '1'){
          $BalanceQty = $this->VariationBalanceQuantity($request->sub_category_id);
      }else{
        if($product->packages == 1){
          $BalanceQty = "1000000000";
        }else{
          $BalanceQty = HomeController::BalanceQuantity($request->product_id);
        }        
      }

      if(!empty(Auth::guard('admin')->check())){
          $BuyerCode = Auth::guard('admin')->user()->code;
      }elseif(!empty(Auth::guard('merchant')->check())){
          $BuyerCode = Auth::guard('merchant')->user()->code;
      }elseif(!empty(Auth::guard('web')->check())){
          $BuyerCode = Auth::guard('web')->user()->code;
      }else{
        if(empty($_COOKIE['new_guest'])){
          $BuyerCode = setcookie('new_guest', strtotime(date('Y-m-d H:i:s')).rand(100000, 999999), time() + (86400 * 30), "/");
        }else{
          $BuyerCode = $_COOKIE['new_guest'];
        }
      }

      $product = Product::find($request->product_id);

      // if($product->mall == 1){
      //     if(Auth::guard('admin')->check() || Auth::guard('merchant')->check()){
      //       $actual_price = (!empty($product->agent_special_price) && $product->agent_special_price != 0) ? $product->agent_special_price : $product->agent_price;
      //     }else{
      //       $actual_price = (!empty($product->special_price) && $product->special_price != 0) ? $product->special_price : $product->price;
      //     }
      //     $totalBalance = $this->GetWalletBalance();

      //     if($totalBalance < $actual_price){
      //         return "wallet not enough balance";
      //     }
      // }


    	if($BalanceQty < $request->quantity){
    		return "quantity exceed error";
    	}

    	$check = Cart::where('user_id', $BuyerCode)
    			         ->where('product_id', $request->product_id)
    			         ->where('status', '1');

      if(isset($request->sub_category_id) && !empty($request->sub_category_id) && isset($request->second_sub_category_id) && !empty($request->second_sub_category_id)){
          $check = $check->where('sub_category_id', $request->sub_category_id)
                         ->where('second_sub_category_id', $request->second_sub_category_id);
      }

    	$check = $check->first();

    	if(isset($check) && !empty($check->id)){

    		$update = Cart::find($check->id);
    		$totalQty = $update->qty + $request->quantity;
    		if($request->quantity <= $BalanceQty){
    			$update = $update->update(['qty'=>$totalQty]);
    		}else{
    			return "quantity personal exceed";
    		}

    		return "ok";
    	}

    	$input = $request->all();
      $input['mall'] = $request->mall;
      $input['product_id'] = $request->product_id;
      $input['sub_category_id'] = $request->sub_category_id;
    	$input['second_sub_category_id'] = $request->second_sub_category_id;
      $input['user_id'] = $BuyerCode;
    	$input['qty'] = $request->quantity;

    	$cart = Cart::create($input);

    	return "ok";
    }

    public function SelectCart(Request $request)
    {
        $amount = 0;
        $count = 0;
        $totalWeight = 0;
        if(!empty($request->cart_id)){

            $explode = explode(",", $request->cart_id);
            foreach(array_unique($explode) as $key => $value){
                $count++;
                $carts = Cart::select('carts.qty', 'p.weight', 
                                      DB::raw('COALESCE(special_price, price) AS actual_price'),
                                      DB::raw('COALESCE(agent_special_price, agent_price) AS agent_actual_price'), 'p.*')
                             ->join('products AS p', 'p.id', 'carts.product_id')
                             ->where(DB::raw('md5(carts.id)'), $value)
                             ->first();
                // if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
                //     $amount += $carts->agent_actual_price * $carts->qty;                    
                // }else{
                //     $amount += $carts->actual_price * $carts->qty;
                // }
                if(!empty($carts->special_price)){
                  $amount += $carts->special_price * $carts->qty;
                }else{
                  $amount += $carts->price * $carts->qty;
                }
                $totalWeight += $carts->weight * $carts->qty;
            }
            
        }


        return array(number_format($amount, 2), $count, $totalWeight);
    }

    public function updateQuantity(Request $request)
    {

        $update = Cart::where(DB::raw('md5(id)'), $request->cart_id);
        $update = $update->update(['qty'=>$request->quantity]);

        $carts = Cart::select('carts.qty', DB::raw('COALESCE(special_price, price) AS actual_price'),
                                      DB::raw('COALESCE(agent_special_price, agent_price) AS agent_actual_price'), 'p.*')
                     ->join('products AS p', 'p.id', 'carts.product_id')
                     ->where(DB::raw('md5(carts.id)'), $request->cart_id)
                     ->first();

        // if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
        //     return number_format($carts->agent_actual_price * $request->quantity, 2);
        // }else{
        //     return number_format($carts->actual_price * $request->quantity, 2);
        // }

        if(!empty($carts->special_price)){
          return number_format($carts->special_price * $request->quantity, 2);
        }else{
          return number_format($carts->price * $request->quantity, 2);
        }
    }

    public function deleteCart(Request $request)
    {
        
        $delete = Cart::where(DB::raw('md5(id)'), $request->cart_id);
        $delete = $delete->delete();

        
    }

    public function CountCart(Request $request)
    {
        if(!empty(Auth::guard('admin')->check())){
            $BuyerCode = Auth::guard('admin')->user()->code;
        }elseif(!empty(Auth::guard('merchant')->check())){
            $BuyerCode = Auth::guard('merchant')->user()->code;
        }elseif(!empty(Auth::guard('web')->check())){
            $BuyerCode = Auth::guard('web')->user()->code;
        }else{
          if(empty($_COOKIE['new_guest'])){
            $BuyerCode = setcookie('new_guest', strtotime(date('Y-m-d H:i:s')).rand(100000, 999999), time() + (86400 * 30), "/");
          }else{
            $BuyerCode = $_COOKIE['new_guest'];
          }
        }

        $cart = Cart::select(DB::raw('SUM(qty) AS totalCart'))
                    ->where('user_id', $BuyerCode)->first();

        $cartP = Cart::select(DB::raw("IF(special_price != '0', special_price, price) AS Price"),
                                      DB::raw("IF(agent_special_price != '0', agent_special_price, agent_price) AS AgentPrice"),
                                      DB::raw("IF(variation_agent_special_price != '0', variation_agent_special_price, variation_agent_price) AS VAgentPrice"),
                                      DB::raw("IF(variation_special_price != '0', variation_special_price, variation_price) AS VPrice"),
                                      'qty', 'variation_enable')
                            ->join('products AS p', 'p.id', 'carts.product_id')
                            ->leftJoin('product_variations AS v', 'v.id', 'carts.sub_category_id')
                            ->where('user_id', $BuyerCode)
                            ->get();
        $totalPrice = 0;
        
        foreach($cartP as $cartP_item){
          if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
            if($cartP_item->variation_enable == 1){
              $totalPrice += $cartP_item->VAgentPrice * $cartP_item->qty;
            }else{
              $totalPrice += $cartP_item->AgentPrice * $cartP_item->qty;
            }
          }else{
            if($cartP_item->variation_enable == 1){
              $totalPrice += $cartP_item->VPrice * $cartP_item->qty;
            }else{
              $totalPrice += $cartP_item->Price * $cartP_item->qty;
            }
          }
        }
                            
        

        return array(!empty($cart->totalCart) ? $cart->totalCart : 0, $totalPrice);
    }

    public function SelectHeaderCart()
    {
        if(!empty(Auth::guard('admin')->check())){
            $BuyerCode = Auth::guard('admin')->user()->code;
        }elseif(!empty(Auth::guard('merchant')->check())){
            $BuyerCode = Auth::guard('merchant')->user()->code;
        }elseif(!empty(Auth::guard('web')->check())){
            $BuyerCode = Auth::guard('web')->user()->code;
        }else{
          if(empty($_COOKIE['new_guest'])){
            $BuyerCode = setcookie('new_guest', strtotime(date('Y-m-d H:i:s')).rand(100000, 999999), time() + (86400 * 30), "/");
          }else{
            $BuyerCode = $_COOKIE['new_guest'];
          }
        }

        $top_carts = Cart::select('i.image', 'p.*', 'v.*', 'carts.qty', 'carts.sub_category_id AS c_sc_id', 'p.id AS pid', 'carts.id AS cid')
                           ->join('products AS p', 'p.id', 'carts.product_id')
                           ->leftJoin('product_variations AS v', 'v.id', 'carts.sub_category_id')
                           ->leftJoin('product_images AS i', 'i.product_id', 'p.id')
                           ->where('user_id', $BuyerCode)
                           ->groupBy('carts.id')
                           ->get();
        $headerTotalCart = 0;
        $headerTotalQty = 0;
        $top_cart_list = '<div class="ps-cart__content">';
        foreach($top_carts as $top_cart){
            if($top_cart->variation_enable == '1'){
                if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
                  $price = !empty($top_cart->variation_agent_special_price) ? $top_cart->variation_agent_special_price : $top_cart->variation_agent_price;
                }else{
                  $price = !empty($top_cart->variation_special_price) ? $top_cart->variation_special_price : $top_cart->variation_agent_price;
                }
            }else{
                if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
                  $price = !empty($top_cart->agent_special_price) ? $top_cart->agent_special_price : $top_cart->agent_price;
                }else{
                  $price = !empty($top_cart->special_price) ? $top_cart->special_price : $top_cart->price;
                }
            }
            $variation = "";
            if($top_cart->variation_enable == '1'){
                $variation = "<br> Variation:".$top_cart->variation_name;
            }
            

            $headerTotalCart += $price * $top_cart->qty;
            $headerTotalQty += $top_cart->qty;

            $top_cart_list .= '<div class="ps-cart-item"><a class="ps-cart-item__close delete-cart" data-id="'.md5($top_cart->cid).'" href="#"></a>
                                <div class="ps-cart-item__thumbnail">
                                  <a href=" '.route('details', [str_replace('/', '-', $top_cart->product_name), md5($top_cart->pid)]).'"></a>
                                    <img src="'.url($top_cart->image).'" alt="">
                                </div>
                                <div class="ps-cart-item__content">
                                  <a class="ps-cart-item__title" href="'.route('details', [str_replace('/', '-', $top_cart->product_name), md5($top_cart->pid)]).'">
                                      '.$top_cart->product_name.'
                                      '.$variation.'
                                  </a>
                                  <p>
                                  <span style="margin-right: 0;">Quantity:<i>'.$top_cart->qty.'</i></span>
                                  <br>
                                  <span style="margin-right: 0;">Total:<i>RM '.$price.'
                                </div>
                              </div>';
        }
        $top_cart_list .= '</div>';
        $top_cart_list .= '<div class="ps-cart__total">
                            <p>Number of items:<span>'.$headerTotalQty.'</span></p>
                            <p>Item Total:<span>RM '.number_format($headerTotalCart, 2).'</span></p>
                          </div>
                          <div class="ps-cart__footer"><a class="ps-btn" href="cart.html">Check out<i class="ps-icon-arrow-left"></i></a></div>';


        return $top_cart_list;
    }

    public function changeDefaultAddress(Request $request)
    {
        $clearDefault = UserShippingAddress::where('user_id', Auth::user()->code)->update(['default' => NULL]);

        $setDefault = UserShippingAddress::where(DB::raw('md5(id)'), $request->address_id);
        $setDefault = $setDefault->update(['default' => '1']);

    }

    public function deleteAddress(Request $request)
    {
        $delete = UserShippingAddress::where(DB::raw('md5(id)'), $request->address_id);
        $delete = $delete->delete();
    }

    public function add_wish(Request $request)
    {
        $favourite = Favourite::where('user_id', Auth::user()->code)
                              ->where('product_id', $request->product_id)
                              ->first();

        if(!empty($favourite->id)){
          $delete = Favourite::find($favourite->id);
          $delete = $delete->delete();

          // return 0;
          $return_value = "2";
        }else{
          $create = Favourite::create(['user_id'=>Auth::user()->code,
                                       'product_id'=>$request->product_id]);
          // return 1;
          $return_value = "1";
        }

        $wish = Favourite::select(DB::raw('COUNT(id) AS totalWish'))
                                 ->where('user_id', Auth::user()->code)
                                 ->first();
                                 
        return array($return_value, $wish->totalWish);
    }


    public function add_to_wish(Request $request)
    {
        $favourite = Favourite::where('user_id', Auth::user()->code)
                              ->where('product_id', $request->product_id)
                              ->first();

        if(!empty($favourite->id)){
          return 0;
        }else{
          $create = Favourite::create(['user_id'=>Auth::user()->code,
                                       'product_id'=>$request->product_id]);
          return 1;
        }
    }


    public function remove_wish(Request $request)
    {
        
        $favourite = Favourite::where('user_id', Auth::user()->code)
                              ->where('product_id', $request->product_id)
                              ->first();

        $delete = Favourite::where('id', $favourite->id)->delete();
    }

    

    public function Repayment(Request $request)
    {
        $transaction = Transaction::where(DB::raw('md5(id)'), $request->transaction_id)
                                  ->first();

        $transactionDs =  TransactionDetail::where('transaction_id', $transaction->id)->get();

        $transaction_no = $transaction->transaction_no;
        $explodeTransaction = explode('-', $transaction_no);

        if(!empty($explodeTransaction[1])){
            ++$transaction_no;
        }else{
            $transaction_no = $transaction_no."-A";
        }


        // Set Old Transaction Status to Failed
        $updateOldTstatus = Transaction::where(DB::raw('md5(id)'), $request->transaction_id)
                                       ->update(['status'=>'95']);

        $createNewTransaction = Transaction::create(['transaction_no'=>$transaction_no,
                                                     'user_id'=>$transaction->user_id,
                                                     'weight'=>$transaction->weight,
                                                     'discount_code'=>$transaction->discount_code,
                                                     'sub_total'=>$transaction->sub_total,
                                                     'discount'=>$transaction->discount,
                                                     'tax'=>$transaction->tax,
                                                     'processing_fee'=>$transaction->processing_fee,
                                                     'shipping_fee'=>$transaction->shipping_fee,
                                                     'grand_total'=>$transaction->grand_total,
                                                     'address_name'=>$transaction->address_name,
                                                     'address'=>$transaction->address,
                                                     'postcode'=>$transaction->postcode,
                                                     'city'=>$transaction->city,
                                                     'state'=>$transaction->state,
                                                     'country'=>$transaction->country,
                                                     'phone'=>$transaction->phone,
                                                     'email'=>$transaction->email,
                                                     'status'=>$transaction->status]);
        $tdetails = [];
        foreach($transactionDs as $transactionD){
            $tdetails[] = [
                            'transaction_id'=>$createNewTransaction->id,
                            'product_image'=>$transactionD->product_image,
                            'product_id'=>$transactionD->product_id,
                            'item_code'=>$transactionD->item_code,
                            'product_code'=>$transactionD->product_code,
                            'unit_weight'=>$transactionD->weight,
                            'sub_category'=>$transactionD->sub_category,
                            'product_name'=>$transactionD->product_name,
                            'unit_price'=>$transactionD->unit_price,
                            'quantity'=>$transactionD->quantity,
                            'total_amount'=>$transactionD->total_amount,
                            'status'=>$transactionD->status,
                            'created_at'=>date('Y-m-d H:i:s'),
                            'updated_at'=>date('Y-m-d H:i:s')
                          ];
        }
        
        $createNewTransactionD = TransactionDetail::insert($tdetails);
        

        return md5($createNewTransaction->id);
    }
    
    public function setNewGuest()
    {
        Session::put('continue_guest', '1');
    }

    

   

    public function CheckLogin(Request $request)
    {

        $phone = preg_replace("/^\+?{$request->country_code}/", '', $request->phone);
         
        if(isset($request->code) && !empty($request->code)){
            $code = VerifyCode::where('code', $request->code)
                              ->where('phone', $phone)
                              ->where('status', '1')
                              ->first();

            if(empty($code->id)){
                return 1;
            }

            if(date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($code->created_at." +10 minutes")))
            {
                return 1; 
            }
        }

        
        if(!empty($request->refferal_code)){
            $merchant = Merchant::where('phone', $request->refferal_code)->first();
            $admin = Admin::where('phone', $request->refferal_code)->first();

            if(empty($merchant->id) && empty($admin->id)){
                return 4;
            }
        }



        $user = User::where('phone', $phone)
                    ->where('country_code', $request->country_code)
                    ->where('status', '1')
                    ->exists();
        $merchant = Merchant::where('phone', $phone)
                            ->where('country_code', $request->country_code)
                            ->where('status', '1')
                            ->exists();

        $admin = Admin::where('phone', $phone)
                            ->where('country_code', $request->country_code)
                            ->where('status', '1')
                            ->exists();

        if($user == 0 && $merchant == 0 && $admin == 0){
            return 2;
        }else{
            return 3;
        }
    }

    public function getVariation(Request $request)
    {
        $variation = ProductVariation::find($request->vid);
        $price = 0;
        $special_price = 0;

        if(Auth::guard('admin')->check() || Auth::guard('merchant')->check()){
          
          $price = $variation->variation_agent_price;
          $special_price = $variation->variation_agent_special_price;

        }else{

          $price = $variation->variation_price;
          $special_price = $variation->variation_special_price;

        }

        $balance = $this->VariationBalanceQuantity($request->vid);

        return array(number_format($special_price, 2), number_format($price, 2), $balance);
    }

    
    public function VariationBalanceQuantity($id)
    {
        $quantityAmount = ProductVariation::find($id);

        $cart = Cart::select(DB::raw('SUM(qty) AS InCart'))
                    ->where('status', '1')
                    ->where('sub_category_id', $id)
                    ->first();

        $transaction = TransactionDetail::select(DB::raw('SUM(quantity) AS TransCart'))
                                        ->join('transactions AS t', 't.id', 'transaction_details.transaction_id')
                                        ->whereIn('t.status', ['1', '97', '98', '99'])
                                        ->where('variation_id', $id)
                                        ->first();

        return $quantityAmount->variation_stock - $cart->InCart - $transaction->TransCart;
    }

    public function guestAgent(Request $request)
    {
        Session::put('guest_agent', $request->agent);
    }

    public function update_complete(Request $request)
    {
        $transaction = Transaction::find($request->tid);
        if(!empty($transaction->id)){
          $transaction = $transaction->update(['completed'=>'1']);
        }
    }

    
}
