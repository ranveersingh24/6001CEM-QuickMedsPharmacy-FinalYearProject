<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use View, Auth, DB;
use App\Admin;
use App\Cart;
use App\Product;
use App\ProductImage;
use App\Category;
use App\WebsiteSetting;
use App\Permission;
use App\Favourite;
use App\SettingBanner;
use App\AdminSetting;
use App\Brand;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        View::composer('*', function($view)
        {

           if(Auth::guard('merchant')->check()){
             $userGuardRole = "merchant";
           }elseif(Auth::guard('web')->check()){
             $userGuardRole = "web";
           }elseif(Auth::guard('admin')->check()){
             $userGuardRole = "admin";
           }else{
             $userGuardRole = "";
           }

           $new_guest = "";
           if(empty($_COOKIE['new_guest']) && empty($userGuardRole)){
              setcookie('new_guest', strtotime(date('Y-m-d H:i:s')).rand(100000, 999999), time() + (86400 * 30), "/");
            
           }

           if(!empty($_COOKIE['new_guest'])){
              $new_guest = $_COOKIE['new_guest'];
           }else{
              $new_guest = setcookie('new_guest', strtotime(date('Y-m-d H:i:s')).rand(100000, 999999), time() + (86400 * 30), "/");
	         }

           

           

           if(!empty(Auth::guard('admin')->user()->code)){
              $HeaderBuyerCode = Auth::guard('admin')->user()->code;
           }elseif(!empty(Auth::guard('web')->user()->code)){
              $HeaderBuyerCode = Auth::guard('web')->user()->code;
           }else{
              $HeaderBuyerCode = $new_guest;
           }

           $totalCart = 0;
           $totalWish = 0;
           if (!empty($userGuardRole) || !empty($new_guest)){
                $cart = Cart::select(DB::raw('SUM(qty) AS totalCart'))
                            ->where('user_id', $HeaderBuyerCode)
                            ->first();

                $cartP = Cart::select(DB::raw("IF(special_price != '0', special_price, price) AS Price"),
                                      DB::raw("IF(agent_special_price != '0', agent_special_price, agent_price) AS AgentPrice"),
                                      DB::raw("IF(variation_agent_special_price != '0', variation_agent_special_price, variation_agent_price) AS VAgentPrice"),
                                      DB::raw("IF(variation_special_price != '0', variation_special_price, variation_price) AS VPrice"),
                                      'qty', 'p.variation_enable')
                            ->join('products AS p', 'p.id', 'carts.product_id')
                            ->leftJoin('product_variations AS v', 'v.id', 'carts.sub_category_id')
                            ->where('user_id', $HeaderBuyerCode)
                            ->get();
                $totalPrice = 0;

                foreach($cartP as $cartP_item){
                  if($userGuardRole == 'admin' || $userGuardRole == 'merchant'){
                      if($cartP_item->variation_enable == 1){
                        $totalPrice += $cartP_item->VAgentPrice * $cartP_item->qty;
                      }else{
                        $totalPrice += $cartP_item->AgentPrice * $cartP_item->qty;
                      }
                  }elseif($userGuardRole == 'web' || !empty($new_guest)){
                      if($cartP_item->variation_enable == 1){
                        $totalPrice += $cartP_item->VPrice * $cartP_item->qty;
                      }else{
                        $totalPrice += $cartP_item->Price * $cartP_item->qty;
                      }
                  }else{
                      $totalPrice = 0;
                  }                  
                }

                // $totalPrice = $cartP->totalPrice;
                // $totalPrice = 123;

                $totalCart = !empty($cart->totalCart) ? $cart->totalCart : 0;

                $wish = Favourite::select(DB::raw('COUNT(id) AS totalWish'))
                                 ->where('user_id', $HeaderBuyerCode)
                                 ->first();

                $totalWish = $wish->totalWish;

              $top_carts = Cart::select('i.image', 'p.*', 'v.*', 'carts.qty', 'carts.sub_category_id AS c_sc_id', 'p.id AS pid', 'carts.id AS cid')
                           ->join('products AS p', 'p.id', 'carts.product_id')
                           ->leftJoin('product_variations AS v', 'v.id', 'carts.sub_category_id')
                           ->leftJoin('product_images AS i', 'i.product_id', 'p.id')
                           ->where('user_id', $HeaderBuyerCode)
                           ->whereNull('carts.mall')
                           ->groupBy('carts.id')
                           ->get();

              $top_mall_carts = Cart::select('i.image', 'p.*', 'v.*', 'carts.qty', 'carts.sub_category_id AS c_sc_id', 'p.id AS pid', 'carts.id AS cid')
                           ->join('products AS p', 'p.id', 'carts.product_id')
                           ->leftJoin('product_variations AS v', 'v.id', 'carts.sub_category_id')
                           ->leftJoin('product_images AS i', 'i.product_id', 'p.id')
                           ->where('user_id', $HeaderBuyerCode)
                           ->where('carts.mall', '1')
                           ->groupBy('carts.id')
                           ->get();
           }

           if($totalCart == 0){
                $totalCart = "";
                $totalPrice = "";
           }
           $leftJoin = DB::raw("(SELECT * FROM product_images ORDER BY created_at ASC) AS i");
           $products = Product::select('products.*', 'i.image')
                               ->join('product_images as i', 'products.id', 'i.product_id')
                               ->where('products.status', '1')
                               ->groupBy('products.id')
                               ->take(5)
                               ->get();
            
            $website_logo = Admin::where('id', '1')->first();
            $top_categories = Category::where('menu_bar', '1')->where('status', '1')->take(8)->get();
            $web_setting = WebsiteSetting::find(1);

            $admin = Admin::where('id', '1')->first();

            $pls = Permission::get();

            if(!$pls->isEmpty()){
              foreach($pls as $pl){
                $permission[$pl->permission_lvl][$pl->page] = $pl->status;
              }              
            }


          

            
            $banners = SettingBanner::get();

             $brands = Brand::where('status', '1')->get();

            $data = array(
                'totalCart' => $totalCart,
                'totalPrice' => $totalPrice,
                'totalWish' => $totalWish,
                'products' => $products,
                'userGuardRole' =>$userGuardRole,
                'website_logo' => !empty($website_logo) && $website_logo->logo_hidden == '1' ? $website_logo->website_logo : '',
                'website_name' => !empty($website_logo) && $website_logo->name_hidden == '1' ? $website_logo->website_name : '',
                'top_categories' => $top_categories,
                'web_setting' => $web_setting,
                'admin' => $admin,
                'permission' => compact('permission'),
                'banners'=> $banners,
                'new_guest'=>$new_guest,
                'top_carts'=>$top_carts,
                'top_mall_carts'=>$top_mall_carts,
                'brands'=>$brands
            );
            view()->share('data', $data);
        });
    }
}
