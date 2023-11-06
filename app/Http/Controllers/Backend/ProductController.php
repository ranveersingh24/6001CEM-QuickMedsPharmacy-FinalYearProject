<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Product;
use App\ProductImage;
use App\Stock;
use App\Category;
use App\SubCategory;
use App\Brand;
use App\Cart;
use App\TransactionDetail;
use App\SettingUom;
use App\ProductVariation;
use App\ProductVariationStock;
use App\VariationStock;
use App\ProductQrcode;
use App\Transaction;

use Validator, Redirect, Toastr, DB, File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        $product = Product::where('status', '!=', '3')
                          ->whereNull('packages')
                          ->orderBy('created_at', 'desc');

        $queries = [];
        $columns = [
            'product_name', 'status'
        ];
        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
                
                $product = $product->where($column, 'like', "%".request($column)."%");

                $queries[$column] = request($column);

            }
        }
        $per_page = 10;
        if(!empty(request('per_page'))){
            $per_page = request('per_page');
        }
        $product = $product->paginate($per_page)->appends($queries);
        $quantity = [];
        foreach($product as $value){
            $quantity[$value->id] = $this->BalanceQuantity($value->id);
        }

        return view('backend.products.index', ['products' => $product], compact('quantity'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('status', '1')->get();
        $brands = Brand::where('status', '1')->get();
        $UOMs = SettingUom::get();

        return view('backend.products.create', ['categories'=>$categories, 'brands'=>$brands, 'UOMs'=>$UOMs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
        ]);

        if($request->variation_enable != '1'){
            $validator = Validator::make($request->all(), [
                'price' => 'required',
                'quantity' => 'required',
            ]);

            if($request->price <= 0){
                return Redirect::back()->withInput(Input::all())->withErrors('Price must > 0');
            }
        }

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }

        

        $c_detail = Category::find($request->category_id);
        $product = Product::select(DB::raw('COUNT(id) AS TotalCount'))
                          ->where('category_id', $request->category_id)
                          ->first();

        $totalCount = $product->TotalCount+1;

        if(strlen($totalCount) == 1){
            $code = "00".$totalCount;
        }elseif(strlen($totalCount) == 2){
            $code = "0".$totalCount;
        }else{
            $code = $totalCount;
        }

        $input = $request->all();
        
        $input['product_code'] = trim($request->product_code);
        $input['product_name'] = trim($request->product_name);
        $input['category_id'] = trim($request->category_id);
        $input['brand_id'] = trim($request->brand_id);
        $input['description'] = trim($request->description);
        $input['price'] = preg_replace("/[^0-9\.]/", '', $input['price']);
        $input['special_price'] = preg_replace("/[^0-9\.]/", '', $input['special_price']);
        $input['weight'] = preg_replace("/[^0-9\.]/", '', $input['weight']);
        $input['featured'] = isset($request->featured) ? '1' : '0';
        $input['mall'] = isset($request->mall) ? '1' : '0';
        
        $create = Product::create($input);

        //Move File
        $move = ProductImage::where('status', '99')->get();

        foreach($move as $key => $value){
            $files = $value->image;
            $explode = explode('/', $files);

            if (!file_exists('uploads/'.$create->id)) {
                File::makeDirectory('uploads/'.$create->id, $mode = 0777, true, true);
            }
            
            rename($value->image, 'uploads/'.$create->id.'/'.end($explode));
            $updateI = ProductImage::find($value->id);
            $updateI = $updateI->update(['image'=>'uploads/'.$create->id.'/'.end($explode),
                                         'product_id' => $create->id, 
                                         'status'=> '1']);
        }


        $createQ = Stock::create([
                                    'type'=>'Increase',
                                    'quantity'=>$request->quantity,
                                    'product_id' => $create->id,
                                    'remark' => 'Open Stock'
                                 ]);
        $running_code ="";

        for($a=1; $a<=$request->quantity; $a++){
            $running_code = $create->id.round(microtime(true) * 1000);
            $insert = ProductQrcode::insert(['product_id'=>$create->id,
                                             'running_code'=>$running_code,
                                             'status'=>'1']);
        }

        if($create->variation_enable == '1'){
            
            $insert = [];
            $caseString = $caseString1 = $caseString2 = $caseString3 = 'case id';
            $ids = '';

            for($a=0; $a<count($request->variation_name); $a++){
                if(!empty($request->vid[$a])){
                    
                    $vid = $request->vid[$a];
                    $variation_name = $request->variation_name[$a];
                    $variation_price = $request->variation_price[$a];
                    $variation_special_price = $request->variation_special_price[$a];


                    $caseString .= " when $vid then '$variation_name'";
                    $caseString1 .= " when $vid then '$variation_price'";
                    $caseString2 .= " when $vid then '$variation_special_price'";


                    $ids .= "$vid,";
                }else{
                    
                    if(!empty($request->variation_name[$a])){
                        
                        $insert[] = [
                                        "product_id"=>$create->id,
                                        "variation_name"=>$request->variation_name[$a],
                                        "variation_price"=>preg_replace("/[^0-9\.]/", '', $request->variation_price[$a]),
                                        "variation_special_price"=>preg_replace("/[^0-9\.]/", '', $request->variation_special_price[$a]),
                                        "variation_weight"=>preg_replace("/[^0-9\.]/", '', $request->variation_weight[$a]),
                                        "variation_stock"=>preg_replace("/[^0-9\.]/", '', $request->variation_stock[$a])
                                    ];
                    }
                }
            }
            $ids = trim($ids, ',');

            $create_variation = ProductVariation::insert($insert);

            $insertS = [];
            $getVars = ProductVariation::where('product_id', $create->id)->get();
            foreach($getVars as $getVar){
                $insertS[] = [
                                "variation_id"=>$getVar->id,
                                "type" => "Increase",
                                "quantity" => $getVar->variation_stock,
                                'remark' => 'Open Stock',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ];            
            }
            $createQ = VariationStock::insert($insertS);

            $getVS = VariationStock::get();

            foreach($getVS as $value){
                $running_code ="";
                for($a=1; $a<=$value->quantity; $a++){
                    $running_code = $create->id.$value->id.round(microtime(true) * 1000);
                    $insert = ProductQrcode::insert(['product_id'=>$create->id,
                                                     'variation_id'=>$value->id,
                                                     'running_code'=>$running_code,
                                                     'status'=>'1']);
                }
            }
            
            if($ids != ''){
                DB::update("update product_variations set variation_name = $caseString end,
                                                          variation_price = $caseString1 end,
                                                          variation_special_price = $caseString2 end
                                                          where id in ($ids)");
            }
        }

        Toastr::success("Product $create->product_name Create Successfully!");
        return redirect()->route('product.products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = Category::where('status', '1')->get();
        
        $product = Product::find($id);
        $code = Category::find($product->category_id);
        if(!isset($product) && empty($product)){
            abort(404);
        }

        $sub_categories = SubCategory::where('status', '1')->where('category_id', $product->category_id)->get();

        $brands = Brand::where('status', '1')->get();
        $stockBalance = $this->BalanceQuantity($id);
        $UOMs = SettingUom::get();

        $variations = ProductVariation::where('product_id', $id)->get();

        $vStock = [];
        foreach($variations as $variation){
            $vStock[$variation->id] = $this->VariationBalanceQuantity($variation->id);
        }
        
         $productTotalSoldCount=[];
         $soldCount = $this->getProductSold($product->id);
         $productTotalSoldCount[$product->id] = $product->sold_count + $soldCount;
         
          $soldCount = [];
         $soldCount[$product->id] = $this->getProductSold($product->id);


        return view('backend.products.edit', ['product'=>$product, 'categories'=>$categories, 'brands'=>$brands, 
                                              'sub_categories'=>$sub_categories, 'code'=>$code,
                                              'stockBalance'=>$stockBalance, 'UOMs'=>$UOMs, 'variations'=>$variations],
                                              compact('vStock','soldCount','productTotalSoldCount'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            
            'category_id' => 'required',
            
        ]);

        if($request->variation_enable != '1'){
            $validator = Validator::make($request->all(), [
                'price' => 'required',
            ]);

            if($request->price <= 0){
                return Redirect::back()->withInput(Input::all())->withErrors('Price must > 0');
            }
        }

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }

        


        $c_detail = Category::find($request->category_id);
        $product = Product::select(DB::raw('COUNT(id) AS TotalCount'))
                          ->where('category_id', $request->category_id)
                          ->first();

        $totalCount = $product->TotalCount+1;

        if(strlen($totalCount) == 1){
            $code = "00".$totalCount;
        }elseif(strlen($totalCount) == 2){
            $code = "0".$totalCount;
        }else{
            $code = $totalCount;
        }

        $input = $request->all();

        $input['product_name'] = str_replace('/','',$request->product_name);
        $input['category_id'] = htmlspecialchars(trim($request->category_id));
        $input['brand_id'] = htmlspecialchars(trim($request->brand_id));
        $input['description'] = htmlspecialchars(trim($request->description));
        $input['price'] = preg_replace("/[^0-9\.]/", '', $input['price']);
        $input['special_price'] = preg_replace("/[^0-9\.]/", '', $input['special_price']);
        $input['weight'] = preg_replace("/[^0-9\.]/", '', $input['weight']);

        $input['featured'] = isset($request->featured) ? '1' : '0';
        $input['mall'] = isset($request->mall) ? '1' : '0';
        

        $update = Product::find($id);
        $product_name = $update->product_name;
        
        $update = $update->update($input);


        //Product Variation
        $insert = [];
        $caseString = $caseString1 = $caseString2 = $caseString3 = $caseString4 = 'case id';
        $ids = '';

        for($a=0; $a<count($request->variation_name); $a++){
            if(!empty($request->vid[$a])){
                
                $pv = ProductVariation::find($request->vid[$a]);

                $vStock = $this->VariationBalanceQuantity($request->vid[$a]);
                $balance = $request->variation_stock[$a] - $vStock;
                $stockQty = $pv->variation_stock + $balance;

                $vid = $request->vid[$a];
                $variation_name = $request->variation_name[$a];
                $variation_price = preg_replace("/[^0-9\.]/", '', $request->variation_price[$a]);
                $variation_special_price = preg_replace("/[^0-9\.]/", '', $request->variation_special_price[$a]);
                $variation_weight = preg_replace("/[^0-9\.]/", '', $request->variation_weight[$a]);


                $caseString .= " when $vid then '$variation_name'";
                $caseString1 .= " when $vid then '$variation_price'";
                $caseString2 .= " when $vid then '$variation_special_price'";
                $caseString3 .= " when $vid then '$stockQty'";
                $caseString4 .= " when $vid then '$variation_weight'";

                $ids .= "$vid,";
            }else{
                
                if(!empty($request->variation_name[$a])){
                    
                    $insert[] = [
                                    "product_id"=>$id,
                                    "variation_name"=>$request->variation_name[$a],
                                    "variation_price"=>preg_replace("/[^0-9\.]/", '', $request->variation_price[$a]),
                                    "variation_special_price"=>preg_replace("/[^0-9\.]/", '', $request->variation_special_price[$a]),
                                    "variation_weight"=>preg_replace("/[^0-9\.]/", '', $request->variation_weight[$a]),
                                    "variation_stock"=>preg_replace("/[^0-9\.]/", '', $request->variation_stock[$a])
                                ];
                }
            }
        }
        $ids = trim($ids, ',');

        $create = ProductVariation::insert($insert);
            
        $insertS = [];
        $getVars = ProductVariation::where('product_id', $id)->get();
        foreach($getVars as $getVar){
            $check = VariationStock::where('variation_id', $getVar->id)->first();
            if(empty($check->id)){
                
                $insertS[] = [
                                "variation_id"=>$getVar->id,
                                "type" => "Increase",
                                "quantity" => $getVar->variation_stock,
                                'remark' => 'Open Stock',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ];                
            }
            
        }
        $createQ = VariationStock::insert($insertS);

        if($ids != ''){
            DB::update("update product_variations set variation_name = $caseString end,
                                                      variation_price = $caseString1 end,
                                                      variation_special_price = $caseString2 end,
                                                      variation_weight = $caseString3 end,
                                                      variation_stock = $caseString4 end
                                                      where id in ($ids)");
        }


        Toastr::success("Product $product_name Update Successfully!");
        return redirect()->route('product.products.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function stock($id)
    {
        $product = Product::find($id);
        if(!isset($product) && empty($product)){
            abort(404);
        }

        $stocks = Stock::where('product_id', $id)
                         ->where('status', '1')
                         ->orderBy('created_at', 'desc');

        $itemPerPage = 10;
        if(request()->has('per_page') && !empty(request('per_page'))){
            $itemPerPage = request('per_page');
        }
        $queries = [];
        $columns = [
            'type', 'status'
        ];

        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
                
                if($column == 'status'){
                    $stocks = $stocks->where($column, ''.request($column).'');
                }else{
                    $stocks = $stocks->where($column, 'like', "%".request($column)."%");
                }

                $queries[$column] = request($column);

            }
        }

        $stockBalance = $this->BalanceQuantity($id);

        $stocks = $stocks->paginate($itemPerPage)->appends($queries);

        return view('backend.products.stock', ['product'=>$product, 'stocks'=>$stocks, 'stockBalance'=>$stockBalance]);
    }

    public function variation_stock($id)
    {
        $product = ProductVariation::find($id);

        if(!isset($product) && empty($product)){
            abort(404);
        }

        $stocks = VariationStock::where('variation_id', $id)
                                ->where('status', '1')
                                ->orderBy('created_at', 'desc');

        $itemPerPage = 10;
        if(request()->has('per_page') && !empty(request('per_page'))){
            $itemPerPage = request('per_page');
        }
        $queries = [];
        $columns = [
            'type', 'status'
        ];

        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
                
                if($column == 'status'){
                    $stocks = $stocks->where($column, ''.request($column).'');
                }else{
                    $stocks = $stocks->where($column, 'like', "%".request($column)."%");
                }

                $queries[$column] = request($column);

            }
        }

        $stockBalance = $this->VariationBalanceQuantity($id);

        $stocks = $stocks->paginate($itemPerPage)->appends($queries);

        return view('backend.products.variation_stock', ['product'=>$product, 'stocks'=>$stocks, 'stockBalance'=>$stockBalance]);
    }

    public function Submitstock(Request $request, $id)
    {
        $stockBalance = $this->BalanceQuantity($id);

        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }


        if($stockBalance < $request->quantity && $request->type == 'Decrease'){
            return Redirect::back()->withInput(Input::all())->withErrors('Quantity Exceed! Quantity More Than Balance Quantity.');
        }

        if($request->type == 'Increase'){
            $running_code ="";
            for($a=1; $a<=$request->quantity; $a++){
                $running_code = $id.round(microtime(true) * 1000);
                $insert = ProductQrcode::insert(['product_id'=>$id,
                                                 'running_code'=>$running_code,
                                                 'status'=>'1']);
            }
        }

        if($request->type == 'Decrease'){
            $running_code = $id.round(microtime(true) * 1000);
            $selectQr = ProductQrcode::where('status', '1')
                                     ->where('product_id', $id)
                                     ->take($request->quantity)
                                     ->orderBy('created_at', 'desc')
                                     ->delete();
        }

        $input = $request->all();
        $input['product_id'] = $id;
        $product = Product::find($id);
        $create = Stock::create($input);

        Toastr::success("$request->type Create Successfully!");
        return redirect()->route('stock', $id);
    }

    public function SubmitVariationStock(Request $request, $id)
    {
        $stockBalance = $this->VariationBalanceQuantity($id);

        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }


        if($stockBalance < $request->quantity && $request->type == 'Decrease'){
            return Redirect::back()->withInput(Input::all())->withErrors('Quantity Exceed! Quantity More Than Balance Quantity.');
        }

        $variationD = ProductVariation::find($id);

        if($request->type == 'Increase'){
            $running_code ="";
            for($a=1; $a<=$request->quantity; $a++){
                $running_code = $variationD->product_id.$id.round(microtime(true) * 1000);
                $insert = ProductQrcode::insert(['product_id'=>$variationD->product_id,
                                                 'variation_id'=>$id,
                                                 'running_code'=>$running_code,
                                                 'status'=>'1']);
            }
        }

        if($request->type == 'Decrease'){
            $running_code = $variationD->product_id.$id.round(microtime(true) * 1000);
            $selectQr = ProductQrcode::where('status', '1')
                                     ->where('variation_id', $id)
                                     ->take($request->quantity)
                                     ->orderBy('created_at', 'desc')
                                     ->delete();
        }
        
        $input = $request->all();
        $input['variation_id'] = $id;
        $product = ProductVariation::find($id);
        $create = VariationStock::create($input);

        Toastr::success("$request->type Create Successfully!");
        return redirect()->route('variation_stock', $id);
    }

    public function BalanceQuantity($id)
    {
        $stockBalance = Stock::select(DB::raw('SUM(IF(type = "Increase", quantity, NULL)) AS totalStockIn'),
                                      DB::raw('SUM(IF(type = "Decrease", quantity, NULL)) AS totalStockOut'))
                                ->where('product_id', $id)
                                ->first();

        $cart = Cart::select(DB::raw('SUM(qty) AS InCart'))
                    ->where('status', '1')
                    ->where('product_id', $id)
                    ->first();

        $transaction = TransactionDetail::select(DB::raw('SUM(quantity) AS TransCart'))
                                        ->join('transactions AS t', 't.id', 'transaction_details.transaction_id')
                                        ->whereIn('t.status', ['1', '97', '98', '99'])
                                        ->where('product_id', $id)
                                        ->whereNull('variation_id')
                                        ->first();

        return $stockBalance->totalStockIn - $stockBalance->totalStockOut - $cart->InCart - $transaction->TransCart;
    }



    
    public function VariationBalanceQuantity($id)
    {
        $stockBalance = VariationStock::select(DB::raw('SUM(IF(type = "Increase", quantity, NULL)) AS totalStockIn'),
                                      DB::raw('SUM(IF(type = "Decrease", quantity, NULL)) AS totalStockOut'))
                                ->where('variation_id', $id)
                                ->first();

        $cart = Cart::select(DB::raw('SUM(qty) AS InCart'))
                    ->where('status', '1')
                    ->where('sub_category_id', $id)
                    ->first();

        $transaction = TransactionDetail::select(DB::raw('SUM(quantity) AS TransCart'))
                                        ->join('transactions AS t', 't.id', 'transaction_details.transaction_id')
                                        ->whereIn('t.status', ['1', '97', '98', '99'])
                                        ->where('variation_id', $id)
                                        ->first();

        return $stockBalance->totalStockIn - $stockBalance->totalStockOut - $cart->InCart - $transaction->TransCart;
    }

    
}
