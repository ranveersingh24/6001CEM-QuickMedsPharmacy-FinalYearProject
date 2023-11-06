<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Bank;
use App\User;
use App\Merchant;
use App\Product;
use App\ProductImage;
use App\State;
use App\Stock;
use App\Cart;
use App\Favourite;
use App\Transaction;
use App\TransactionDetail;
use App\UserShippingAddress;
use App\Category;
use App\Brand;
use App\SubCategory;
use App\SettingShippingFee;
use App\AgentLevel;
use App\WithdrawalTransaction;
use App\Admin;
use App\PackageItem;
use App\TblCountry;
use App\ProductVariation;
use App\SettingBanner;
use App\ProductQrcode;
use App\UserContact;

use Twilio\Rest\Client;

use DB, Auth, Validator, Redirect, Toastr, Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */



    public function save(Request $req)
    {
      $user= new UserContact;
      $user->user_name = $req->user_name;
      $user->user_mail = $req->user_mail;
      $user->user_feedback = $req->user_feedback;
      echo $user->save();
      Toastr::success("Respond Sent Successfully");
      return view('frontend.contact');
    }
    public function about()
    {
        return view('frontend.about');
    }

    public function faqs()
    {
        return view('frontend.faqs');
    }

    public function contact()
    {
        return view('frontend.contact');
    }

   

    public function index()
    {
        $leftJoin = DB::raw("(SELECT * FROM product_images WHERE image NOT LIKE '%.mp4%' ORDER BY created_at ASC) AS i");
        $leftJoin2 = DB::raw("(SELECT * FROM category_images ORDER BY created_at ASC) AS i");

        $products_top = Product::select('products.*', 'i.image')
                           ->leftJoin($leftJoin, function($join) {
                                $join->on('products.id', '=', 'i.product_id');
                           })
                           ->where('products.status', '1')
                           ->where('products.category_id', '1')
                           ->groupBy('products.id')
                           ->get();


        $products_featured = Product::select('products.*', 'i.image', 'c.category_name',
                                             DB::raw('(CASE WHEN agent_special_price != 0  THEN agent_special_price ELSE agent_price END) AS agent_actual_price'),
                                             DB::raw('(CASE WHEN special_price != 0  THEN special_price ELSE price END) AS retail_price'))
                                   ->leftJoin($leftJoin, function($join) {
                                        $join->on('products.id', '=', 'i.product_id');
                                   })
                                   ->join('categories as c', 'c.id', 'products.category_id')
                                   ->where('products.status', '1')
                                   ->where('products.featured', '1')
                                   
                                   ->groupBy('products.id')
                                   ->orderBy('products.created_at', 'desc')
                                   ->take(12)
                                   ->get();

        $products_latest = Product::select('products.*', 'i.image', 'c.category_name',
                                             DB::raw('(CASE WHEN agent_special_price != 0  THEN agent_special_price ELSE agent_price END) AS agent_actual_price'),
                                             DB::raw('(CASE WHEN special_price != 0  THEN special_price ELSE price END) AS retail_price'))
                                   ->leftJoin($leftJoin, function($join) {
                                        $join->on('products.id', '=', 'i.product_id');
                                   })
                                   ->join('categories as c', 'c.id', 'products.category_id')
                                   ->where('products.status', '1')
                                   
                                   ->groupBy('products.id')
                                   ->orderBy('products.created_at', 'desc')
                                   ->take(12)
                                   ->get();

        $favourite = [];
        $priceV = [];
      
        $productImages = [];
        $featureTotalSoldCount=[];
        foreach ($products_featured as $key => $value) {
            $favourite[$value->id] = Favourite::where('product_id', $value->id)->exists();

            $variations = ProductVariation::select(DB::raw("max(IF(variation_special_price != '0', variation_special_price, variation_price)) AS maxVPrice"),
                                                   DB::raw("max(IF(variation_agent_special_price != '0', variation_agent_special_price, variation_agent_price)) AS maxVAPrice"),
                                                   DB::raw("min(IF(variation_special_price != '0', variation_special_price, variation_price)) AS minVPrice"),
                                                   DB::raw("min(IF(variation_agent_special_price != '0', variation_agent_special_price, variation_agent_price)) AS minVAPrice"))
                                          ->where('product_id', $value->id)
                                          ->where('variation_name', '!=', '')
                                          ->first();
            $priceV[$value->id] = [$variations->maxVPrice, $variations->minVPrice, $variations->maxVAPrice, $variations->minVAPrice];

            $productImages[$value->id] = ProductImage::where('product_id', $value->id)->get();

            $soldCount = $this->getProductSold($value->id);
            $featureTotalSoldCount[$value->id] = $value->sold_count + $soldCount;
        }

        $priceVF = [];
        $productImages_lastest = [];
        $latestTotalSoldCount=[];
        
        foreach ($products_latest as $key1 => $value1) {
            $favourite[$value1->id] = Favourite::where('product_id', $value1->id)->exists();

            $variations1 = ProductVariation::select(DB::raw("max(IF(variation_special_price != '0', variation_special_price, variation_price)) AS maxVPrice"),
                                                   DB::raw("max(IF(variation_agent_special_price != '0', variation_agent_special_price, variation_agent_price)) AS maxVAPrice"),
                                                   DB::raw("min(IF(variation_special_price != '0', variation_special_price, variation_price)) AS minVPrice"),
                                                   DB::raw("min(IF(variation_agent_special_price != '0', variation_agent_special_price, variation_agent_price)) AS minVAPrice"))
                                          ->where('product_id', $value1->id)
                                          ->first();
            $priceVF[$value1->id] = [$variations1->maxVPrice, $variations1->minVPrice, $variations1->maxVAPrice, $variations1->minVAPrice];

            $productImages_lastest[$value1->id] = ProductImage::where('product_id', $value1->id)->get();
            $soldCount = $this->getProductSold($value1->id);
            $latestTotalSoldCount[$value1->id] = $value1->sold_count + $soldCount;
        }

        $featured_categories = Category::select('categories.*')
                              ->join('products as p', 'p.category_id', 'categories.id')
                              ->where('categories.status', '1')
                              ->where('p.featured', '1')
                              ->where('p.status', '1')
                              ->groupBy('categories.id')
                              ->orderBy('categories.created_at', 'desc')
                              ->get();

        $new_categories = Category::select('categories.*')
                              ->join('products as p', 'p.category_id', 'categories.id')
                              ->where('categories.status', '1')
                              ->groupBy('categories.id')
                              ->orderBy('categories.created_at', 'desc')
                              ->get();


        $categories = Category::select('categories.*', 'i.image')
                              ->join('category_images as i', 'categories.id', 'i.category_id')
                             ->where('categories.status', '1')
                             ->groupBy('categories.id')
                             ->get();


        // $this->sendMessage('User registration successful!!', '+60174194868');
        // $leftJoin = DB::raw("(SELECT * FROM product_images ORDER BY created_at ASC) AS i");
        $thisweek = Transaction::select('i.image', 'p.*', DB::raw('SUM(d.quantity) AS totalBuy'))
                               ->join('transaction_details AS d', 'd.transaction_id', 'transactions.id')
                               ->join('products AS p', 'p.id', 'd.product_id')
                               ->leftJoin($leftJoin, function($join) {
                                  $join->on('p.id', '=', 'i.product_id');
                               })
                               ->where('transactions.status', '1')
                               ->groupBy('p.id')
                               ->orderBy('transactions.created_at', 'desc')
                               ->take(3)
                               ->get();
        $BalanceQuantity = [];
        foreach($thisweek as $value){
            $BalanceQuantity[$value->id] = $this->BalanceQuantity($value->id);
        }

        $banners = SettingBanner::get();

        // $this->sendEmailNotification("sonezack5577@gmail.com", "noreply@threestar.my", "Zack", "Account Verify", "Mb000001");

        return view('frontend.home', ['products_top'=>$products_top, 'products_featured'=>$products_featured, 'featured_categories'=>$featured_categories,
                                      'thisweek'=>$thisweek, 'categories'=>$categories, 'products_latest'=>$products_latest, 'new_categories'=>$new_categories, 'banners'=>$banners],
                                     compact('favourite', 'BalanceQuantity', 'priceV', 'productImages', 'priceVF', 'productImages_lastest',
                                             'featureTotalSoldCount','latestTotalSoldCount'));
    }

    public function getProductSold($product_id)
    {
        $transaction = Transaction::select(DB::raw('SUM(d.quantity) AS totalQty'))
                                  ->join('transaction_details as d', 'd.transaction_id', 'transactions.id')
                                  ->where('transactions.status', '1')
                                  ->where('d.product_id', $product_id)
                                  ->first();
        

        return $transaction->totalQty;
    }

    private function sendMessage($message, $recipients)
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, 
                ['from' => $twilio_number, 'body' => $message] );
    }

    public function profile()
    {

      $lvl = "";
      if(!empty($agentLVL->id)){
        $lvl = $agentLVL->agent_lvl;
      }

      $countPending = $this->countPending();
      $countToShip = $this->countToShip();
      $countToReceive = $this->countToReceive();
      $countCompleted = $this->countCompleted();
      $countCancelled = $this->countCancelled();

      return view('frontend.profile', ['lvl'=>$lvl, 
                                       'countPending'=>$countPending,
                                       'countToShip'=>$countToShip,
                                       'countToReceive'=>$countToReceive,
                                       'countCompleted'=>$countCompleted,
                                       'countCancelled'=>$countCancelled]);
    }

    public function updateProfile(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'f_name' => 'required',
          'l_name' => 'required',
      ]);

      if ($validator->fails()) {
          return Redirect::back()->withInput(Input::all())->withErrors($validator);
      }

      $input = $request->all();
      if(Auth::guard('admin')->check()){
        $user = Admin::where('code', Auth::user()->code)->first();
      }elseif(Auth::guard('merchant')->check()){
        $user = Merchant::where('code', Auth::user()->code)->first();
      }else{
        $user = User::where('code', Auth::user()->code)->first();
      }

      $input = Input::except('email');
      if(!empty($request->file('profile_logo'))){
          $files = $request->file('profile_logo'); 
          $name = $files->getClientOriginalName();
          $exp = explode(".", $name);
          $file_ext = end($exp);
          $name = md5($name.date('Y-m-d H:i:s')).'.'.$file_ext;

          $files->move("uploads/profile_logo/", $name);
          $input['profile_logo'] = "uploads/profile_logo/".$name;
          
      }
      $user = $user->update($input);

      Toastr::success("Profile Updated!");
      return redirect()->route('home');
    }



    public function pending_order()
    {
      $transactions = Transaction::where('user_id', Auth::user()->code)->where('status', '99')->orderBy('created_at', 'desc')->get();

      $lvl = "";
      if(!empty($agentLVL->id)){
        $lvl = $agentLVL->agent_lvl;
      }

      $details = [];
      foreach($transactions as $transaction){
         $details[$transaction->id] = TransactionDetail::where('transaction_id', $transaction->id)->get();
      }

      $countPending = $this->countPending();
      $countToShip = $this->countToShip();
      $countToReceive = $this->countToReceive();
      $countCompleted = $this->countCompleted();
      $countCancelled = $this->countCancelled();

      return view('frontend.pending_order', ['transactions'=>$transactions, 'lvl'=>$lvl,
                                             'countPending'=>$countPending,
                                             'countToShip'=>$countToShip,
                                             'countToReceive'=>$countToReceive,
                                             'countCompleted'=>$countCompleted,
                                             'countCancelled'=>$countCancelled], compact('details'));
    }

    public function pending_shipping()
    {
      $transactions = Transaction::where('user_id', Auth::user()->code)
                                 ->where('status', '98')
                                 ->orderBy('created_at', 'desc')
                                 ->get();

      $lvl = "";
      if(!empty($agentLVL->id)){
        $lvl = $agentLVL->agent_lvl;
      }

      $details = [];
      foreach($transactions as $transaction){
         $details[$transaction->id] = TransactionDetail::where('transaction_id', $transaction->id)->get();
      }

      $countPending = $this->countPending();
      $countToShip = $this->countToShip();
      $countToReceive = $this->countToReceive();
      $countCompleted = $this->countCompleted();
      $countCancelled = $this->countCancelled();



      return view('frontend.pending_shipping', ['transactions'=>$transactions, 'lvl'=>$lvl,
                                                 'countPending'=>$countPending,
                                                 'countToShip'=>$countToShip,
                                                 'countToReceive'=>$countToReceive,
                                                 'countCompleted'=>$countCompleted,
                                                 'countCancelled'=>$countCancelled], compact('details'));
    }

    public function pending_receive()
    {
      $transactions = Transaction::where('user_id', Auth::user()->code)->where('status', '1')->whereNull('completed')->orderBy('created_at', 'desc')->get();

      $lvl = "";

      if(!empty($agentLVL->id)){
        $lvl = $agentLVL->agent_lvl;
      }

      $details = [];
      foreach($transactions as $transaction){
         $details[$transaction->id] = TransactionDetail::where('transaction_id', $transaction->id)->get();
      }


      $countPending = $this->countPending();
      $countToShip = $this->countToShip();
      $countToReceive = $this->countToReceive();
      $countCompleted = $this->countCompleted();
      $countCancelled = $this->countCancelled();

      return view('frontend.pending_receive', ['transactions'=>$transactions, 'lvl'=>$lvl,
                                               'countPending'=>$countPending,
                                               'countToShip'=>$countToShip,
                                               'countToReceive'=>$countToReceive,
                                               'countCompleted'=>$countCompleted,
                                               'countCancelled'=>$countCancelled], compact('details'));
    }

    public function completed_order()
    {
      $transactions = Transaction::where('user_id', Auth::user()->code)->where('status', '1')->where('completed', '1')->orderBy('created_at', 'desc')->get();

      $lvl = "";

      if(!empty($agentLVL->id)){
        $lvl = $agentLVL->agent_lvl;
      }

      $details = [];
      foreach($transactions as $transaction){
         $details[$transaction->id] = TransactionDetail::where('transaction_id', $transaction->id)->get();
      }

      $countPending = $this->countPending();
      $countToShip = $this->countToShip();
      $countToReceive = $this->countToReceive();
      $countCompleted = $this->countCompleted();
      $countCancelled = $this->countCancelled();

      return view('frontend.completed_order', ['transactions'=>$transactions, 'lvl'=>$lvl,
                                               'countPending'=>$countPending,
                                               'countToShip'=>$countToShip,
                                               'countToReceive'=>$countToReceive,
                                               'countCompleted'=>$countCompleted,
                                               'countCancelled'=>$countCancelled], compact('details')); 
    }


    public function cancelled_order()
    {
      $transactions = Transaction::where('user_id', Auth::user()->code)->where('status', '95')->orderBy('created_at', 'desc')->get();

      $lvl = "";

      if(!empty($agentLVL->id)){
        $lvl = $agentLVL->agent_lvl;
      }

      $details = [];
      foreach($transactions as $transaction){
         $details[$transaction->id] = TransactionDetail::where('transaction_id', $transaction->id)->get();
      }


      $countPending = $this->countPending();
      $countToShip = $this->countToShip();
      $countToReceive = $this->countToReceive();
      $countCompleted = $this->countCompleted();
      $countCancelled = $this->countCancelled();

      return view('frontend.cancelled_order', ['transactions'=>$transactions, 'lvl'=>$lvl,
                                               'countPending'=>$countPending,
                                               'countToShip'=>$countToShip,
                                               'countToReceive'=>$countToReceive,
                                               'countCompleted'=>$countCompleted,
                                               'countCancelled'=>$countCancelled], compact('details')); 
    }

    public function my_setting()
    {
        $lvl = "";
        if(!empty($agentLVL->id)){
          $lvl = $agentLVL->agent_lvl;
        }

        return view('frontend.my_settings', ['lvl'=>$lvl]);
    }
  
    public function order_list()
    {
        $lvl = "";
        $agentLVL = AgentLevel::find(Auth::user()->lvl);
        if(!empty($agentLVL->id)){
          $lvl = $agentLVL->agent_lvl;
        }

        $transactions = Transaction::where('user_id', Auth::user()->code)->orderBy('created_at', 'desc')->get();
        $details = [];
        foreach($transactions as $transaction){
           $details[$transaction->id] = TransactionDetail::where('transaction_id', $transaction->id)->get();
        }

        return view('frontend.order', ['transactions'=>$transactions, 'lvl'=>$lvl], compact('details'));
    }

    public function order_detail($no)
    {

        $lvl = "";
       
        if(!empty($agentLVL->id)){
          $lvl = $agentLVL->agent_lvl;
        }
        
        $transaction = Transaction::select('transactions.*', 'p.amount_type', 'p.amount AS discount_amount')
                                  ->leftJoin('promotions AS p', 'p.id', 'transactions.discount_code')
                                  ->where('transaction_no', $no)
                                  ->first();
        if(empty($transaction->id)){
          abort(404);
        }

        $details = TransactionDetail::where('transaction_id', $transaction->id)->get();

        return view('frontend.order_detail', ['transaction'=>$transaction, 'details'=>$details, 'lvl'=>$lvl]);
    }

    public function wish_list()
    {
        $leftJoin = DB::raw("(SELECT * FROM product_images ORDER BY created_at ASC) AS i");
  
        $favourites = Favourite::select('p.*', 'i.image', DB::raw('COALESCE(special_price, price) AS actual_price'))
                               ->join('products AS p', 'p.id', 'favourites.product_id')
                               ->leftJoin($leftJoin, function($join) {
                                  $join->on('p.id', '=', 'i.product_id');
                               })
                               ->where('user_id', Auth::user()->code)
                               ->groupBy('p.id')
                               ->get();
        $stockBalance = [];
        foreach($favourites as $favourite){
            $stockBalance[$favourite->id] = $this->BalanceQuantity($favourite->id);
        }

        $lvl = "";
        if(!empty($agentLVL->id)){
          $lvl = $agentLVL->agent_lvl;
        }

        return view('frontend.wish_list', ['favourites'=>$favourites, 'lvl'=>$lvl], compact('stockBalance'));
    }

    public function changePassword()
    {
        return view('frontend.change_password');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }

        
        if (!Hash::check($request->old_password, Auth::user()->password)) {
            return Redirect::back()->withInput(Input::all())->withErrors(['Current Password Not Match']);
        }

        if($request->new_password != $request->password_confirmation){
            return Redirect::back()->withInput(Input::all())->withErrors(['The new password confirmation does not match.']);
        }
        
        if(Auth::guard('admin')->check()){
          $update = Admin::where('code', Auth::user()->code)->first();
        }else{
          $update = User::where('code', Auth::user()->code)->first();
        }
        $update = $update->update(['password'=>Hash::make($request->new_password)]);

        Toastr::success("Password Changed Successfully!");
        return redirect()->route('changePassword');
    }

    public function listing()
    {

        if(Auth::guard('web')->check()){
          $user = Auth::guard('web')->check();
          $userCode = Auth::guard('web')->user()->code;
        }elseif (Auth::guard('admin')->check()) {
          $user = Auth::guard('admin')->check();
          $userCode = Auth::guard('admin')->user()->code;
        }else{
          $user = "";
          $userCode = "";
        }

        $leftJoin = DB::raw("(SELECT * FROM product_images ORDER BY created_at ASC) AS i");
        $products = Product::select('products.*', 'i.image', 'c.category_name', 'b.brand_name',
                                    DB::raw('(CASE WHEN agent_special_price != 0  THEN agent_special_price ELSE agent_price END) AS agent_actual_price'),
                                    DB::raw('(CASE WHEN special_price != 0  THEN special_price ELSE price END) AS retail_price'))
                           ->leftJoin('categories AS c', 'c.id', 'products.category_id')
                           ->leftJoin('sub_categories AS sc', 'sc.id', 'products.sub_category_id')
                           ->leftJoin('brands AS b', 'b.id', 'products.brand_id')
                           ->leftJoin($leftJoin, function($join) {
                              $join->on('products.id', '=', 'i.product_id');
                           })
                           ->where('products.status', '1')
                           ->groupBy('products.id')
                           ->orderBy('created_at', 'desc');
        $queries = [];
        $columns = [
           'result', 'brand', 'category', 'from', 'to', 'sub_category'
        ];
        // return htmlspecialchars(request('category'));
        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
                
                if($column == 'category'){
                  $products = $products->where('c.category_name', request($column));
                }elseif($column == 'sub_category'){
                  $products = $products->where('sc.sub_category_name', request($column));
                }elseif($column == 'brand'){
                  $products = $products->where('b.brand_name', request($column));
                }elseif($column == 'from' || $column == 'to'){
                  $from = preg_replace("/[^0-9\.]/", '', request('from'));
                  $to = preg_replace("/[^0-9\.]/", '', request('to'));
                  if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
                      if(!empty(request('from')) && empty(request('to'))){

                          $products = $products->where(DB::raw('COALESCE(agent_special_price, agent_price)'), '>=', $from);
                      }elseif(empty(request('from')) && !empty(request('to'))){
                          $products = $products->where(DB::raw('COALESCE(agent_special_price, agent_price)'), '<=', $to);
                      }else{
                          $products = $products->where(DB::raw('COALESCE(agent_special_price, agent_price)'), '>=', $from)
                                               ->where(DB::raw('COALESCE(agent_special_price, agent_price)'), '<=', $to);
                      }
                  }else{
                      if(!empty(request('from')) && empty(request('to'))){
                          $products = $products->where(DB::raw('COALESCE(special_price, price)'), '>=', $from);
                      }elseif(empty(request('from')) && !empty(request('to'))){
                          $products = $products->where(DB::raw('COALESCE(special_price, price)'), '<=', $to);
                      }else{
                          $products = $products->where(DB::raw('COALESCE(special_price, price)'), '>=', $from)
                                               ->where(DB::raw('COALESCE(special_price, price)'), '<=', $to);
                      }                    
                  }

                }else{
                  // $products = $products->WhereRaw("MATCH(products.product_name) AGAINST('".request($column)."*' IN BOOLEAN MODE)")
                  //                      ->orWhereRaw("MATCH(b.brand_name) AGAINST('".request($column)."*' IN BOOLEAN MODE)")
                  //                      ->orWhereRaw("MATCH(c.category_name) AGAINST('".request($column)."*' IN BOOLEAN MODE)");
                  $products = $products->where('products.product_name', 'like', '%'.request($column).'%');
                }
                
                $queries[$column] = request($column);

            }
        }
        // echo $products = $products->toSql();

        // exit();

        $p = $products->get();
        $count_p = count($p);
        $products = $products->paginate(24)->appends($queries);
        $priceV = [];
       
        $productImages = [];
        $favourite = [];
        $totalSoldCount=[];
        foreach($products as $product){
            $variations = ProductVariation::select(DB::raw("max(IF(variation_special_price != '0', variation_special_price, variation_price)) AS maxVPrice"),
                                                   DB::raw("max(IF(variation_agent_special_price != '0', variation_agent_special_price, variation_agent_price)) AS maxVAPrice"),
                                                   DB::raw("min(IF(variation_special_price != '0', variation_special_price, variation_price)) AS minVPrice"),
                                                   DB::raw("min(IF(variation_agent_special_price != '0', variation_agent_special_price, variation_agent_price)) AS minVAPrice"))
                                          ->where('product_id', $product->id)
                                          ->where('variation_name', '!=', '')
                                          ->first();
            $priceV[$product->id] = [$variations->maxVPrice, $variations->minVPrice, $variations->maxVAPrice, $variations->minVAPrice];

            $productImages[$product->id] = ProductImage::where('product_id', $product->id)->get();

            if(!empty($userCode)){
              $favourite[$product->id] = Favourite::where('product_id', $product->id)->where('user_id', $userCode)->exists();
            }

            $soldCount = $this->getProductSold($product->id);
            $totalSoldCount[$product->id] = $product->sold_count + $soldCount;
        }

        $categories = Category::where('status', '1')->get();
        $brands = Brand::where('status', '1')->get();

        $top_subCategory = [];
        foreach($categories as $t_category){
            $top_subCategory[$t_category->id] = SubCategory::where('category_id', $t_category->id)->where('status', '1')->get();
        }

        $sp_products = Product::select('products.*', 'i.image',
                                       DB::raw('(CASE WHEN agent_special_price != 0  THEN agent_special_price ELSE agent_price END) AS agent_actual_price'),
                                       DB::raw('(CASE WHEN special_price != 0  THEN special_price ELSE price END) AS retail_price'))
                              ->leftJoin($leftJoin, function($join) {
                                  $join->on('products.id', '=', 'i.product_id');
                              })
                              ->where(function($query){
                                  $query->where('agent_special_price', '!=', '0')
                                        ->orWhere('special_price', '!=', '0');
                              })
                              ->where('products.status', '1')
                              ->get();

        $MaxMinPrice = Product::select(DB::raw('max(COALESCE(special_price, price)) AS max_price'),
                                       DB::raw('min(COALESCE(special_price, price)) AS min_price'))
                              ->where('status', '1')
                              ->first();

            
  

        


     

        return view('frontend.listing', ['products'=>$products, 'categories'=>$categories, 'brands'=>$brands, 'count_p'=>$count_p, 'sp_products'=>$sp_products, 'MaxMinPrice'=>$MaxMinPrice], 
                                        compact('favourite', 'priceV', 'productImages', 'top_subCategory','totalSoldCount'));
    }

    

  
    public function details($name, $id)
    {
        if(Auth::guard('web')->check()){
          $user = Auth::guard('web')->check();
          $userCode = Auth::guard('web')->user()->code;
        }elseif (Auth::guard('admin')->check()) {
          $user = Auth::guard('admin')->check();
          $userCode = Auth::guard('admin')->user()->code;
        }else{
          $user = "";
          $userCode = "";
        }

  
        $product = Product::select('products.*', 'u.uom_name', 'b.brand_name')
                          ->leftJoin('setting_uoms AS u', 'u.id', 'products.product_type')
                          ->leftJoin('brands AS b', 'b.id', 'products.brand_id')
                          ->where(DB::raw('md5(products.id)'), $id)
                          ->first();
        if(empty($product)){
            return redirect()->route('home');
        }

        if($product->packages == 1 || $product->variation_enable == 1){
          $stockBalance = 1000000000;
        }else{
          $stockBalance = $this->BalanceQuantity($product->id);          
        }
        $images = ProductImage::where('product_id', $product->id)
                              ->where('status', '1')
                              ->orderBy('created_at', 'asc')
                              ->get();

        $favourite = [];
        if($user){
          $favourite = Favourite::where('user_id', $userCode)
                                ->where('product_id', $product->id)
                                ->first();
        }

        $leftJoin = DB::raw("(SELECT * FROM product_images ORDER BY created_at ASC) AS i");
        $products = Product::select('products.*', 'i.image')
                           ->leftJoin($leftJoin, function($join) {
                                $join->on('products.id', '=', 'i.product_id');
                           })
                           ->where(DB::raw('md5(products.id)'), '<>', $id)
                           ->where('products.status', '1')
                           ->groupBy('products.id')
                           ->take(8)
                           ->get();
        $sub_category_id = [];
        if(!empty($product->sub_category_id)){
           $sub_category_id = SubCategory::whereIn('id', explode(",", $product->sub_category_id))->get();
        }

        $Pimage = ProductImage::where(DB::raw('md5(product_id)'), $id)->orderBy('product_id', 'asc')->first();

        $Ppackages = PackageItem::select('p.product_name', 'package_items.*', 'i.image')
                                ->join('products AS p', 'p.id', 'package_items.products')
                                ->leftJoin($leftJoin, function($join) {
                                    $join->on('p.id', '=', 'i.product_id');
                                })
                                ->where(DB::raw('md5(package_items.product_id)'), $id)
                                ->groupBy('package_items.products')
                                ->get();

        $variations = ProductVariation::where(DB::raw('md5(product_id)'), $id)->get();

 

        $vStock = [];
        foreach($variations as $variation){
            $vStock[$variation->id] = $this->VariationBalanceQuantity($variation->id);
        }

        return view('frontend.details', ['product'=>$product, 'stockBalance'=>$stockBalance, 'images'=>$images, 'favourite'=>$favourite, 
                                         'products'=>$products, 'Pimage'=>$Pimage, 'Ppackages'=>$Ppackages,
                                         'sub_category_id'=>$sub_category_id, 'variations'=>$variations], compact('vStock'));
    }


   

    public function cart()
    {
        $leftJoin = DB::raw("(SELECT * FROM product_images ORDER BY created_at ASC) AS i");

        $carts = Cart::select('carts.id AS cid', 'p.*', 'i.image', 'carts.qty', 'p.weight', 
                              DB::raw('COALESCE(special_price, price) AS actual_price'), 
                              DB::raw('COALESCE(agent_special_price, agent_price) AS agent_actual_price'), 
                              'scl.sub_category_name AS l_sub_name', 'scr.sub_category_name AS r_sub_name')
                     ->join('products AS p', 'p.id', 'carts.product_id')
                     ->leftJoin('sub_categories AS scl', 'scl.id', 'carts.sub_category_id')
                     ->leftJoin('sub_categories AS scr', 'scr.id', 'carts.second_sub_category_id')
                     ->leftJoin($leftJoin, function($join) {
                        $join->on('p.id', '=', 'i.product_id');
                     })
                     ->where('carts.status', '1')
                     ->where('carts.user_id', Auth::user()->code)
                     ->groupBy('carts.id');
        if(!empty(request('m')) && request('m') == '1'){
          $carts = $carts->where('p.mall', '1');
        }else{
          $carts = $carts->whereNULL('p.mall');
        }

        $carts = $carts->get();
        $leftJoin = DB::raw("(SELECT * FROM product_images ORDER BY created_at ASC) AS i");
        $products = Product::select('products.*', 'i.image')
                           ->leftJoin($leftJoin, function($join) {
                                $join->on('products.id', '=', 'i.product_id');
                           })
                           ->where('products.status', '1')
                           ->groupBy('products.id')
                           ->take(16)
                           ->get();

        if(!$carts->isEmpty()){
            foreach($carts as $key => $cart){
                $stockBalance[$cart->cid] = $this->BalanceQuantity($cart->id);
            }

            return view('frontend.cart', ['carts'=>$carts, 'products'=>$products], compact('stockBalance'));
        }else{
            $stockBalance = 0;
            return view('frontend.cart', ['carts'=>$carts, 'products'=>$products], compact('stockBalance'));
        }
    }

    public function checkout()
    {
        if(!empty(Auth::guard('admin')->check())){
            $buyerCode = Auth::guard('admin')->user()->code;
        }elseif(!empty(Auth::guard('web')->check())){
            $buyerCode = Auth::guard('web')->user()->code;
        }else{
          if(empty($_COOKIE['new_guest'])){
            $buyerCode = setcookie('new_guest', strtotime(date('Y-m-d H:i:s')).rand(100000, 999999), time() + (86400 * 30), "/");
          }else{
            $buyerCode = $_COOKIE['new_guest'];
          }
        }

        $leftJoin = DB::raw("(SELECT * FROM product_images ORDER BY created_at ASC) AS i");
        $carts = Cart::select('carts.id AS cid', 'p.*', 'i.image', 'carts.qty', DB::raw('COALESCE(special_price, price) AS actual_price'),
                              DB::raw('COALESCE(agent_special_price, agent_price) AS agent_actual_price'),
                              'scl.variation_name', 'scl.variation_price', 'scl.variation_special_price', 'scl.variation_agent_price', 'scl.variation_agent_special_price', 
                              'p.weight', 'p.special_price', 'p.price', 'p.agent_special_price', 'p.agent_price', 'variation_enable',
                              'point_price', 'point_agent_price', 'variation_point_price', 'variation_point_agent_price', 'variation_weight')
                     ->join('products AS p', 'p.id', 'carts.product_id')
                     ->leftJoin('product_variations AS scl', 'scl.id', 'carts.sub_category_id')
                     ->leftJoin($leftJoin, function($join) {
                        $join->on('p.id', '=', 'i.product_id');
                     })
                     ->where('carts.status', '1')
                     ->where('carts.user_id', $buyerCode)
                     ->groupBy('carts.id');
        if(!empty(request('m'))){
        $carts = $carts->where('carts.mall', '1');
        }else{
        $carts = $carts->whereNull('carts.mall');
        }
        $carts = $carts->get();
    

        if($carts->isEmpty()){
          Toastr::info("Cart is empty");
          return redirect()->route('home');
        }

        $shipping_address = UserShippingAddress::where('user_id', $buyerCode)
                                               ->where('default', '1')
                                               ->first();

        $weight = 0;
        foreach($carts as $cart){
            if($cart->variation_enable == '1'){
              if(!empty($cart->variation_weight)){
                  $weight += $cart->variation_weight * $cart->qty;
              }
            }else{
              $weight += $cart->weight * $cart->qty;
            }
        }

        $totalshipping_fees = 0;
        
        if(!empty($shipping_address)){
            if($shipping_address->state > 16){
              $shipping_fees = SettingShippingFee::where('weight', '<=', ceil($weight))
                                                 ->where('area', 'sg')
                                                 ->orderBy('weight', 'desc')
                                                 ->first();
              if(!empty($shipping_fees->id)){
                $totalshipping_fees = $shipping_fees->shipping_fee;                
              }
            }elseif($shipping_address->state != '11' && $shipping_address->state != '12' && $shipping_address->state != '15'){
              $shipping_fees = SettingShippingFee::where('weight', '<=', ceil($weight))
                                                 ->where('area', 'west')
                                                 ->orderBy('weight', 'desc')
                                                 ->first();
              if(!empty($shipping_fees->id)){
                $totalshipping_fees = $shipping_fees->shipping_fee;                
              }

            }else{
              $shipping_fees = SettingShippingFee::where('weight', '<=', ceil($weight))
                                                 ->where('area', 'east')
                                                 ->orderBy('weight', 'desc')
                                                 ->first();
              if(!empty($shipping_fees->id)){
                $totalshipping_fees = $shipping_fees->shipping_fee;                
              }
            }
        }

        // if(!empty(request('m'))){
        //     $totalshipping_fees =  $shipping_fees->shipping_fee;
        // }


        

        

        $banks = Bank::orderBy('id', 'asc')->get();

        

        return view('frontend.checkout', ['carts'=>$carts,  'shipping_address'=>$shipping_address, 'banks'=>$banks,
                                          'totalshipping_fees'=>$totalshipping_fees]);
    }

    public function postCheckout(Request $request)
    {
    }


    public function placeOrder(Request $request){
        if(!empty(Auth::guard('admin')->check())){
            $buyerCode = Auth::guard('admin')->user()->code;
        }elseif(!empty(Auth::guard('web')->check())){
            $buyerCode = Auth::guard('web')->user()->code;
        }else{
          if(empty($_COOKIE['new_guest'])){
            $buyerCode = setcookie('new_guest', strtotime(date('Y-m-d H:i:s')).rand(100000, 999999), time() + (86400 * 30), "/");
          }else{
            $buyerCode = $_COOKIE['new_guest'];
          }
        }
        if(empty($request->billing_details_im)){
            $validator = Validator::make($request->all(), [
                'f_name' => 'required',
                'l_name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'city' => 'required',
                'postcode' => 'required',
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withInput(Input::all())->withErrors($validator);
            }

            $input = $request->all();
            $input['user_id'] = $buyerCode;
            $input['default'] = '1';
            $input['state'] = $request->state;
            $create_shipping_address = UserShippingAddress::create($input);
        }

        $shipping_address = UserShippingAddress::where('user_id', $buyerCode)
                                               ->where('default', '1')
                                               ->first();

        $selected_cart = [];
        foreach($request->selected_cart as $key => $value){
            $selected_cart[] = [$value];
        }

        $leftJoin = DB::raw("(SELECT * FROM product_images ORDER BY created_at ASC) AS i");
        $carts = Cart::select('carts.*', 'p.product_name', 'agent_price', 'agent_special_price', 'price', 'special_price', 'weight',
                              DB::raw('SUM(IF(special_price != "0", special_price * qty, price * qty)) AS totalSum'), 
                              DB::raw('SUM(IF(agent_special_price != "0", agent_price * qty, price * qty)) AS totalAgentSum'), 
                              'i.image', 'p.item_code', 'p.product_code', 
                              'scl.variation_name', 'scl.variation_price', 'scl.variation_special_price', 'scl.variation_agent_price', 'scl.variation_agent_special_price', 
                              'p.product_comm_type', 'p.product_comm_amount', 'own_product_comm_type', 'own_product_comm_amount', 
                              'in_product_comm_type', 'in_product_comm_amount', 'p.variation_enable', 'scl.id as vid',
                              'variation_get_point_price', 'variation_get_point_agent_price',
                              'get_point_price', 'get_point_agent_price',
                              'point_price', 'point_agent_price', 'variation_point_price', 'variation_point_agent_price')
                     ->join('products AS p', 'p.id', 'carts.product_id')
                     ->leftJoin('product_variations AS scl', 'scl.id', 'carts.sub_category_id')
                     ->leftJoin($leftJoin, function($join) {
                        $join->on('p.id', '=', 'i.product_id');
                     })
                     ->where('carts.status', '1')
                     ->where('carts.user_id', $buyerCode)
                     ->whereIn(DB::raw("md5(carts.id)"), $selected_cart)
                     ->groupBy('carts.id')
                     ->get();
        $totalAmount = 0;
        $totalWeight = 0;
        $totalPFee = 0;
        foreach($carts as $cart){
          if(!empty($request->mall) && $request->mall == 1){
            if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
              if($cart->variation_enable == '1'){
                  $totalAmount += $cart->variation_point_agent_price * $cart->qty;
              }else{
                  $totalAmount += $cart->point_agent_price * $cart->qty;
              }
            }else{
              if($cart->variation_enable == '1'){
                  $totalAmount += $cart->variation_point_price * $cart->qty;
              }else{
                  $totalAmount += $cart->point_price * $cart->qty;
              }
            }
          }else{
            if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
              if($cart->variation_enable == '1'){
                if(!empty($cart->variation_agent_special_price)){
                  $totalAmount += $cart->variation_agent_special_price * $cart->qty;
                }else{
                  $totalAmount += $cart->variation_agent_price * $cart->qty;             
                }
              }else{
                if(!empty($cart->agent_special_price)){
                  $totalAmount += $cart->agent_special_price * $cart->qty;
                }else{
                  $totalAmount += $cart->agent_price * $cart->qty;             
                }              
              }
            }else{
              if($cart->variation_enable == '1'){
                if(!empty($cart->variation_special_price)){
                  $totalAmount += $cart->variation_special_price * $cart->qty;
                }else{
                  $totalAmount += $cart->variation_price * $cart->qty;             
                }
              }else{
                if(!empty($cart->special_price)){
                  $totalAmount += $cart->special_price * $cart->qty;
                }else{
                  $totalAmount += $cart->price * $cart->qty;             
                }
              }
            }            
          }
          // $totalAmount += $cart->totalSum;
          $totalWeight += $cart->weight * $cart->qty;
        }
        // echo $totalAmount;
        // exit();
        if($carts->isEmpty()){
          Toastr::info("Cart is empty, please re-order/re-payment.");
          return redirect()->route('home');
        }
        if($request->mall == 1){
          $totalAmount = $totalAmount;
        }else{
          $totalAmount = $totalAmount + $request->hidden_shipping_amount;
        }

        if(!empty($request->hidden_discount) && $request->hidden_discount != '0'){
          $totalAmount = $totalAmount - $request->hidden_discount;
          $updateAppliedDiscount = AppliedPromotion::where('status', '1')->where('promotion_id', $request->discount_code)->where('user_id', $buyerCode)->update(['status'=>'2']);
          $input['discount'] = $request->discount;
          $input['discount_code'] = $request->discount_code;
        }

        if(!empty($request->hidden_rebate) && $request->hidden_rebate != '0'){
          $totalAmount = $totalAmount - $request->hidden_rebate;
        }

        if(empty($request->mall) && $request->mall != '1' && $request->cdm != '1'){
          // $totalPFee = $totalAmount * 1.6 / 100;
          // $totalAmount = $totalAmount + ($totalAmount * 1.6 / 100);
        }

        if($totalAmount <= 0){
            if($request->cdm == 1){
                $totalAmount = $request->hidden_shipping_amount;
            }else{
                $totalAmount = $request->hidden_shipping_amount + ($request->hidden_shipping_amount * 1.6 /100);
            }
        }

        $input['weight'] = $totalWeight;
        $input['transaction_no'] = $this->GenerateTransactionNo();
        
        $input['sub_total'] = $request->sub_total;
        if($request->mall == 1){
          $input['shipping_fee'] = 0;
          $input['additional_shipping_fee'] = $request->hidden_shipping_amount;
        }else{
          $input['shipping_fee'] = $request->hidden_shipping_amount;
          $input['additional_shipping_fee'] = 0;
        }

        $input['rebate_amount'] = $request->hidden_rebate;
        $input['grand_total'] = number_format($totalAmount, 2, '.', '');
        $input['user_id'] = $buyerCode;
        $input['address_name'] = $shipping_address->f_name.' '.$shipping_address->l_name;
        $input['address'] = $shipping_address->address;
        $input['postcode'] = $shipping_address->postcode;
        $input['city'] = $shipping_address->city;
        $input['state'] = $shipping_address->state;
        $input['phone'] = $shipping_address->phone;
        $input['email'] = $shipping_address->email;
        
        
        

        // if(empty($request->mall) && $request->mall != '1'){
        if($request->cdm == 1){
            $files = $request->file('bank_slip'); 
            $name = $files->getClientOriginalName();
            $exp = explode(".", $name);
            $file_ext = end($exp);
            $name = md5($name.date('Y-m-d H:i:s')).'.'.$file_ext;
            $files->move("uploads/bank_slip/".$buyerCode."/", $name);

            $input['cdm_bank_id'] = $request->cdm_bank_id;
            $input['bank_slip'] = "uploads/bank_slip/".$buyerCode."/".$name;
            $input['status'] = '98';
        }else{
            $input['processing_fee'] = 0;
            $input['bank_id'] = $request->bank_id;
            $input['status'] = '99';
        }
        if(!empty($request->mall) && $request->mall == '1'){
          $input['mall'] = '1';
        }
        // }else{
        //   $input['status'] = '1';
        // }

        $transaction = Transaction::create($input);
        $items = [];
        $own_product_comm_type = "";
        $own_product_comm_amount = 0;
        $actual_point = 0;
        foreach($carts as $cart){

            if(empty($request->mall) && $request->mall != '1'){
              if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
                if($cart->variation_enable == '1'){
                  $actual_price = (!empty($cart->variation_agent_special_price) && $cart->variation_agent_special_price != 0) ? $cart->variation_agent_special_price : $cart->variation_agent_price;
                  $actual_point = $cart->variation_get_point_agent_price;
                }else{
                  $actual_price = (!empty($cart->agent_special_price) && $cart->agent_special_price != 0) ? $cart->agent_special_price : $cart->agent_price;
                  $actual_point = $cart->get_point_agent_price;
                }
              }else{
                if($cart->variation_enable == '1'){
                  $actual_price = (!empty($cart->variation_special_price) && $cart->variation_special_price != 0) ? $cart->variation_special_price : $cart->variation_price;
                  $actual_point = $cart->variation_get_point_price;
                }else{
                  $actual_price = (!empty($cart->special_price) && $cart->special_price != 0) ? $cart->special_price : $cart->price;
                  $actual_point = $cart->get_point_price;
                }
              }
            }else{
              
              if(Auth::guard('merchant')->check() || Auth::guard('admin')->check()){
                if($cart->variation_enable == '1'){
                  $actual_price = $cart->variation_point_agent_price;
                  $actual_point = "0";
                }else{
                  $actual_price = $cart->point_agent_price;
                  $actual_point = "0";
                }
              }else{
                if($cart->variation_enable == '1'){
                  $actual_price = $cart->variation_point_price;
                  $actual_point = "0";
                }else{
                  $actual_price = $cart->point_price;
                  $actual_point = "0";
                }
              }
            }

            $items[] = ['transaction_id'=>$transaction->id,
                        'product_id'=>$cart->product_id,
                        'item_code'=>$cart->item_code,
                        'product_code'=>$cart->product_code,
                        'unit_weight'=>$cart->weight,
                        'product_image'=>$cart->image,
                        'product_name'=>$cart->product_name,
                        'unit_price'=>$actual_price,
                        'quantity'=>$cart->qty,
                        'sub_category'=>$cart->variation_name,
                        'variation_id'=>$cart->vid,
                        'second_sub_category'=>$cart->r_sub_name,
                        'total_amount'=>$cart->totalSum,
                        'get_point'=>$actual_point,
                        'status'=>'1',
                        'created_at'=>date('Y-m-d H:i:s'),
                        'updated_at'=>date('Y-m-d H:i:s')];
        }

        $t_detail = TransactionDetail::insert($items);

        $bank = Bank::find($request->bank_id);

        $delete_cart = Cart::whereIn(DB::raw("md5(carts.id)"), $selected_cart)->delete();
        
        // Toastr::success("Order Successfully!");
        // return redirect()->route('order_list');
        if(!empty($request->cdm) && $request->cdm == '1'){
          // $this->sendEmailNotification("3starfamilymart@gmail.com", "noreply@threestar.my", "Zack", "New Order", $transaction->transaction_no);
          // $this->sendEmailNotification("sonezack5577@gmail.com", "noreply@threestar.my", "Zack", "New Order", $transaction->transaction_no);
          if(Auth::guard('web')->check() || Auth::guard('admin')->check() || Auth::guard('merchant')->check()){
            Toastr::success('Order Successfully');
            return \Redirect::route('pending_shipping');
          }else{
            
            $this->guestPlacedOrderMessage($shipping_address->phone, $transaction->transaction_no, $transaction->grand_total);
            setcookie('new_guest', strtotime(date('Y-m-d H:i:s')).rand(100000, 999999), time() + (86400 * 30), "/");
            Toastr::success('Your order has been placed successfully');
            return \Redirect::route('home');
          }
        }else{
          // $this->guestPlacedOrderMessage($shipping_address->phone, $transaction->transaction_no, $transaction->grand_total);
          // return \Redirect::route('PaymentProcess', array('transactions'=>md5($transaction->id), 'bank_code'=>$bank->bank_code));
          return \Redirect::route('PaymentProcess', array('transaction'=>md5($transaction->id)));
        }


    }

    public function guestPlacedOrderMessage($phone, $transaction_no, $grand_total)
    {
        $destination = urlencode($phone);
        $message = "Hwajing: Thanks for purchasing on our website. \nYour order has been placed. \nOrder No: #".$transaction_no."\nRM ".$grand_total;
        $message = html_entity_decode($message, ENT_QUOTES, 'utf-8'); 
        $message = urlencode($message);
          
        $username = urlencode("hwajing2020");
        $password = urlencode("hwajing20201234");
        $sender_id = urlencode("66300");
        $type = "1";

        $fp = "https://www.isms.com.my/isms_send_all.php";
        $fp .= "?un=$username&pwd=$password&dstno=$destination&msg=$message&type=$type&sendid=$sender_id&agreedterm=YES";
        //echo $fp;
          
        $http = curl_init($fp);

        curl_setopt($http, CURLOPT_RETURNTRANSFER, TRUE);
        $http_result = curl_exec($http);
        $http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);
        curl_close($http);
    }

    public function PaymentProcess($transaction)
    {
        $transactions = Transaction::where(DB::raw('md5(id)'), $transaction)->first();

        return view('frontend.payment_processing', ['transactions'=>$transactions]);
    }

    public function Payment_Error()
    {
        return view('frontend.payment_error');
    }

    public function payment_successfully(Request $request)
    {

        if($request->TxnStatus == '0'){
            $select = Transaction::where('transaction_no', $request->PaymentID)->first();
            $amount = $select->grand_total;
            // $this->sendEmailNotification("3starfamilymart@gmail.com", "noreply@threestar.my", "Zack", "New Order", $transaction->transaction_no);
            // $this->sendEmailNotification("sonezack5577@gmail.com", "noreply@threestar.my", "Zack", "New Order", $select->transaction_no);
            $details = TransactionDetail::where('transaction_id', $select->id)->get();

            $update = Transaction::where('transaction_no', $request->PaymentID);
            $update = $update->update(['status'=>'1',
                                       'online_payment_method'=>$request->PymtMethod,
                                       'bank_name'=>$request->IssuingBank,
                                       'card_holder_name'=>$request->CardHolder,
                                       'card_mask'=>$request->CardNoMask,
                                       'card_exp'=>$request->CardExp,
                                       'card_type'=>$request->CardType]);
         

          return redirect()->route('pending_receive');

        }else{
            return redirect()->route('pending_order');
        }
    }



    public function mall()
    {
        $leftJoin = DB::raw("(SELECT * FROM product_images ORDER BY created_at ASC) AS i");
        $products = Product::select('products.*', 'i.image', 'c.category_name', 'b.brand_name')
                                                   ->join('categories AS c', 'c.id', 'products.category_id')
                                                   ->leftJoin('brands AS b', 'b.id', 'products.brand_id')
                                                   ->leftJoin($leftJoin, function($join) {
                                                      $join->on('products.id', '=', 'i.product_id');
                                                   })
                                                   ->where('mall', '1')
                                                   ->groupBy('products.id')
                                                   ->orderBy('created_at', 'desc');
        $queries = [];
        $columns = [
           'result', 'brand', 'category', 'from', 'to'
        ];

        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
                
                if($column == 'category'){
                  $products = $products->where('c.category_name', request($column));
                }elseif($column == 'brand'){
                  $products = $products->where('b.brand_name', request($column));
                }elseif($column == 'from' || $column == 'to'){
                  if(Auth::guard('merchant')->check()){
                      if(!empty(request('from')) && empty(request('to'))){
                          $products = $products->where(DB::raw('COALESCE(agent_special_price, agent_price)'), '>=', request('from'));
                      }elseif(empty(request('from')) && !empty(request('to'))){
                          $products = $products->where(DB::raw('COALESCE(agent_special_price, agent_price)'), '<=', request('to'));
                      }else{
                          $products = $products->where(DB::raw('COALESCE(agent_special_price, agent_price)'), '>=', request('from'))
                                               ->where(DB::raw('COALESCE(agent_special_price, agent_price)'), '<=', request('to'));
                      }
                  }else{
                      if(!empty(request('from')) && empty(request('to'))){
                          $products = $products->where(DB::raw('COALESCE(special_price, price)'), '>=', (int)request('from'));
                      }elseif(empty(request('from')) && !empty(request('to'))){
                          $products = $products->where(DB::raw('COALESCE(special_price, price)'), '<=', (int)request('to'));
                      }else{
                          $products = $products->where(DB::raw('COALESCE(special_price, price)'), '>=', (int)request('from'))
                                               ->where(DB::raw('COALESCE(special_price, price)'), '<=', (int)request('to'));
                      }                    
                  }

                }else{
                  // $products = $products->WhereRaw("MATCH(products.product_name) AGAINST('".request($column)."*' IN BOOLEAN MODE)")
                  //                      ->orWhereRaw("MATCH(b.brand_name) AGAINST('".request($column)."*' IN BOOLEAN MODE)")
                  //                      ->orWhereRaw("MATCH(c.category_name) AGAINST('".request($column)."*' IN BOOLEAN MODE)");
                  $products = $products->where('products.product_name', 'like', '%'.request($column).'%');
                }
                
                $queries[$column] = request($column);

            }
        }

        


        $products = $products->paginate(10)->appends($queries);
        

        // $categories = [];
        // $brands = [];
        // foreach($products as $product){
        //   $categories[] = $product->category_name;
        //   $brands[] = $product->brand_name;
        // }

        $categories = Category::where('status', '1')->get();
        $brands = Brand::where('status', '1')->get();

        return view('frontend.mall', ['products'=>$products, 'categories'=>$categories, 'brands'=>$brands]);
    }

    

    public static function BalanceQuantity($id)
    {
        $stockBalance = Stock::select(DB::raw('SUM(IF(type = "Increase", quantity, NULL)) AS totalStockIn'),
                                      DB::raw('SUM(IF(type = "Decrease", quantity, NULL)) AS totalStockOut'))
                                ->where('product_id', $id)
                                ->first();

        $cart = Cart::select(DB::raw('SUM(qty) AS InCart'))
                    ->where('status', '1')
                    ->where('product_id', $id);
                    

        if(Auth::check()){
          $cart = $cart->where('user_id', '<>', Auth::user()->code);
        }

        $cart = $cart->first();

        $transaction = TransactionDetail::select(DB::raw('SUM(quantity) AS TransCart'))
                                        ->join('transactions AS t', 't.id', 'transaction_details.transaction_id')
                                        ->whereIn('t.status', ['1', '98', '99', '97'])
                                        ->where('product_id', $id)
                                        ->first();


        return $stockBalance->totalStockIn - $stockBalance->totalStockOut - $cart->InCart - $transaction->TransCart;
    }

    public static function GenerateTransactionNo()
    {
      $transaction = Transaction::select(DB::raw('COUNT(id) AS TotalTransaction'))
                                ->first();
      $TotalTransaction = $transaction->TotalTransaction + 1;
      if(strlen($TotalTransaction) == 1){
          $tNo = strtotime(date('Y-m-d H:i:s'))."0000".$TotalTransaction;
      }elseif(strlen($TotalTransaction) == 2){
          $tNo = strtotime(date('Y-m-d H:i:s'))."000".$TotalTransaction;
      }elseif(strlen($TotalTransaction) == 3){
          $tNo = strtotime(date('Y-m-d H:i:s'))."00".$TotalTransaction;
      }elseif(strlen($TotalTransaction) == 4){
          $tNo = strtotime(date('Y-m-d H:i:s'))."0".$TotalTransaction;
      }else{
          $tNo = strtotime(date('Y-m-d H:i:s')).$TotalTransaction;
      }
      return $tNo;
    }

    public static function getState($id)
    {
        $state = State::find($id);
        return $state->name;
    }

    public static function GenerateWithdrawalTransactionNo()
    {
      $transaction = WithdrawalTransaction::select(DB::raw('COUNT(id) AS TotalTransaction'))
                                          ->first();
      $TotalTransaction = $transaction->TotalTransaction + 1;

      if(strlen($TotalTransaction) == 1){
          $wtNo = 'W'.strtotime(date('Y-m-d H:i:s'))."0000".$TotalTransaction;
      }elseif(strlen($TotalTransaction) == 2){
          $wtNo = 'W'.strtotime(date('Y-m-d H:i:s'))."000".$TotalTransaction;
      }elseif(strlen($TotalTransaction) == 3){
          $wtNo = 'W'.strtotime(date('Y-m-d H:i:s'))."00".$TotalTransaction;
      }elseif(strlen($TotalTransaction) == 4){
          $wtNo = 'W'.strtotime(date('Y-m-d H:i:s'))."0".$TotalTransaction;
      }else{
          $wtNo = 'W'.strtotime(date('Y-m-d H:i:s')).$TotalTransaction;
      }
      return $wtNo;
    }

    public function countPending()
    {
      $transactions = Transaction::where('status', '99')->where('user_id', Auth::user()->code)->get();

      return count($transactions);
    }


    public function countToShip()
    {
      $transactions = Transaction::where('status', '98')->where('user_id', Auth::user()->code)->get();

      return count($transactions);
    }

    public function countToReceive()
    {
      $transactions = Transaction::where('status', '1')->whereNull('completed')->where('user_id', Auth::user()->code)->get();

      return count($transactions);
    }

    public function countCompleted()
    {
      $transactions = Transaction::where('status', '1')->where('completed', '1')->where('user_id', Auth::user()->code)->get();

      return count($transactions);
    }

    public function countCancelled()
    {
      $transactions = Transaction::where('status', '95')->where('user_id', Auth::user()->code)->get();

      return count($transactions);
    }

   

    public function VerifyAccount($user_id)
    {
        $update = User::where(DB::raw('md5(code)'), $user_id)->first();
        // return $user_id;

        $update = $update->update(['status' => '1']);

        return redirect()->route('verify_success');
    }


    public function verify_success()
    {
      return view('frontend.verify_success');   
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

    public function product_anti_counterfeiting()
    {
        $result = "";
        if(!empty(request('code'))){
          $leftJoin = DB::raw("(SELECT * FROM product_images WHERE image NOT LIKE '%.mp4%' ORDER BY created_at ASC) AS i");

          $result = ProductQrcode::select('v.variation_name', 'p.product_name', 'i.image')
                                 ->join('products AS p', 'p.id', 'product_qrcodes.product_id')
                                 ->leftJoin('product_variations AS v', 'v.id', 'product_qrcodes.variation_id')
                                 ->leftJoin($leftJoin, function($join) {
                                    $join->on('p.id', '=', 'i.product_id');
                                 })
                                 ->where('running_code', request('code'))
                                 ->first();
        }

        return view('frontend.product_anti_counterfeiting', ['result'=>$result]);
    }

    public function add_new_address(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'postcode' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }
        
        $input = $request->all();
        $input['default'] = 1;
        $input['user_id'] = Auth::user()->code;

        $create = UserShippingAddress::create($input);

        return redirect()->back();
    }
}
