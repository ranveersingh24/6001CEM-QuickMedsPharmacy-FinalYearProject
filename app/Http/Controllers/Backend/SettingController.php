<?php

namespace App\Http\Controllers\Backend;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SettingMerchantBonus;
use App\SettingMerchantRebate;
use App\SettingMerchantCommission;
use App\SettingPerformanceDividend;
use App\SettingPerformanceMain;
use App\SettingTeamDividend;
use App\SettingTeamMain;
use App\SettingRefferalReward;
use App\AgentLevel;
use App\Product;
use App\Transaction;
use App\Merchant;
use App\SettingShippingFee;
use App\SettingUom;
use App\SettingBanner;
use App\SettingSpecificDate;
use App\UserContact;

use Validator, Redirect, Toastr, DB, File;

class SettingController extends Controller
{   
    public function setting_merchant_bonus()
    {
        $selects = SettingMerchantBonus::get();
        $levels = AgentLevel::where('status', '1')->get();

        // $selectDetails = [];

        // foreach($selects as $select){
        //     $selectDetails[$select->agent_lvl] = array($select->id, $select->type, $select->amount);
        // }

        return view('backend.settings.setting_merchant_bonus', ['selects'=>$selects, 'levels'=>$levels]);
    }

    public function save_setting_merchant_bonus(Request $request)
    {
        $insert = [];
        $caseString = $caseString1 = $caseString2 = 'case id';
        $ids = '';

        for($a=0; $a<count($request->amount); $a++){

            if(!empty($request->sid[$a])){
                
                $sid = $request->sid[$a];
                $qty = $request->qty[$a];
                $type = $request->type[$a];
                $amount = $request->amount[$a];

                $caseString .= " when $sid then '$qty'";
                $caseString1 .= " when $sid then '$type'";
                $caseString2 .= " when $sid then '$amount'";

                $ids .= "$sid,";
            }else{
                
                if(!empty($request->amount[$a])){
                    
                    $insert[] = [
                                    "agent_lvl"=>$request->lvl[$a],
                                    "qty"=>$request->qty[$a],
                                    "type"=>$request->type[$a],
                                    "amount"=>$request->amount[$a],
                                ];
                }
            }
            
        }
        
        $ids = trim($ids, ',');

        $create = SettingMerchantBonus::insert($insert);
        if($ids != ''){
            DB::update("update setting_merchant_bonuses set qty = $caseString end,
                                                            type = $caseString1 end,
                                                            amount = $caseString2 end
                                                            where id in ($ids)");
        }

        Toastr::success("Setting Agent Rebate Successful");
        return redirect()->route('setting_merchant_bonus');
    } 

    public function setting_agent_rebate()
    {
    	$selects = SettingMerchantRebate::get();
        $levels = AgentLevel::where('status', '1')->get();
        $selectDetails = [];

        foreach($selects as $select){
            $selectDetails[$select->agent_lvl] = array($select->id, $select->type, $select->amount);
        }

        return view('backend.settings.setting_agent_rebate', ['selects'=>$selects, 'levels'=>$levels], compact('selectDetails'));
    }

    public function save_setting_agent_rebate(Request $request)
    {
        $caseString1 = $caseString2 = 'case id';
        $ids = '';
        $insert = [];
        for($a=0; $a<count($request->agent_lvl); $a++){
            if(!empty($request->sid[$a])){
                //Update
                $id = $request->sid[$a];
                $type = $request->type[$a];
                $amount = $request->amount[$a];

                $caseString1 .= " when $id then '$type'";
                $caseString2 .= " when $id then '$amount'";

                $ids .= "$id,";

            }else{
                //Create
                if(!empty($request->amount[$a])){
                    $insert[] = [
                                    'agent_lvl'=>$request->agent_lvl[$a],
                                    'type'=>$request->type[$a],
                                    'amount'=>$request->amount[$a]
                                ];
                }
            }
        }

        $ids = trim($ids, ',');

        if($ids != ''){
            DB::update("update setting_merchant_rebates set type = $caseString1 end,
                                                amount = $caseString2 end
                                                where id in ($ids)");
        }


        $create = SettingMerchantRebate::insert($insert);

        Toastr::success("Merchant Bonus Updated!");
        return redirect()->route('setting_agent_rebate');
    }

    public function setting_merchant_commission()
    {
        $setting_merchant_commissions = SettingMerchantCommission::get();
        $levels = AgentLevel::get();
        $value = [];
        foreach($setting_merchant_commissions as $smc){
            $value[$smc->level][$smc->agent_lvl] = array($smc->comm_type, $smc->comm_amount, $smc->id);
        }

        return view('backend.settings.setting_merchant_commission', ['levels'=>$levels], compact('value'));
    }

    public function save_setting_merchant_commission(Request $request)
    {
        $caseString = $caseString1 = "case id";
        $ids = '';

        $insert = [];
        for($a=0; $a<count($request->comm_amount); $a++){
            if(!empty($request->ids[$a])){
                //Update
                $id = $request->ids[$a];
                $comm_type = $request->comm_type[$a];
                $comm_amount = $request->comm_amount[$a];

                $caseString .= " when $id then '$comm_type'";
                $caseString1 .= " when $id then '$comm_amount'";

                $ids .= "$id,";
            }else{
                //Create

                if(!empty($request->comm_amount[$a])){
                    $insert[] = [
                                    'agent_lvl'=>$request->agent_lvl[$a],
                                    'level'=>$request->level[$a],
                                    'comm_type'=>$request->comm_type[$a],
                                    'comm_amount'=>$request->comm_amount[$a]
                                ];
                }
            }
        }

        $ids = trim($ids, ',');

        if($ids != ''){
            DB::update("update setting_merchant_commissions set comm_type = $caseString end,
                                                                comm_amount = $caseString1 end
                                                                where id in ($ids)");
        }


        $create = SettingMerchantCommission::insert($insert);

        Toastr::success("Setting Affiliate Bonus Successful");
        return redirect()->route('setting_merchant_commission');
    }

    public function setting_performance_dividend()
    {
        $selects = SettingPerformanceDividend::get();
        $levels = AgentLevel::get();
        $setting = SettingPerformanceMain::first();

        $selectDetails = [];

        foreach($selects as $select){
            $selectDetails[$select->lvl] = array($select->id, $select->type, $select->amount, $select->target);
        }

        return view('backend.settings.setting_performance_dividend', ['levels'=>$levels, 'setting'=>$setting], compact('selectDetails'));
    }

    public function save_setting_performance_dividend(Request $request)
    {
           
        $input = $request->all();
        
        $insert = [];
        $caseString1 = $caseString2 = $caseString3 = 'case id';

        $ids = "";
        for($a=0; $a<count($request->amount); $a++){

            if(!empty($request->sid[$a])){
                $sid = $request->sid[$a];

                $amount = $request->amount[$a];
                $lvl = $request->lvl[$a];
                $target = $request->target[$a];

                $caseString1 .= " when $sid then '$target'";
                $caseString2 .= " when $sid then '$amount'";
                $caseString3 .= " when $sid then '$lvl'";

                $ids .= "$sid,";
            }else{
                if(!empty($request->amount[$a])){

                    $insert[] = [
                                    "target"=>$request->target[$a],
                                    "amount"=>$request->amount[$a],
                                    "lvl"=>$request->lvl[$a],
                                ];
                }
            }
            
        }
        $ids = trim($ids, ',');

        $create = SettingPerformanceDividend::insert($insert);
        if($ids != ''){
            DB::update("update setting_performance_dividends set amount = $caseString2 end,
                                                                    lvl = $caseString3 end,
                                                                 target = $caseString1 end
                                                               where id in ($ids)");
        }

        $checkSetting = SettingPerformanceMain::first();
        if(!empty($checkSetting->id)){
            $setting = SettingPerformanceMain::find(1);
            $setting = $setting->update($input);
        }else{
            $setting = SettingPerformanceMain::create($input);            
        }
        

        Toastr::success("Setting Performance Dividend Successful");
        return redirect()->route('setting_performance_dividend');
    }

     public function setting_team_dividend()
    {
        $selects = SettingTeamDividend::get();
        $levels = AgentLevel::get();
        $setting = SettingTeamMain::first();

        $selectDetails = [];

        foreach($selects as $select){
            $selectDetails[$select->lvl] = array($select->id, $select->target_box, $select->amount, $select->target);
        }

        return view('backend.settings.setting_team_dividend', ['levels'=>$levels, 'setting'=>$setting], compact('selectDetails'));
    }

    public function save_setting_team_dividend(Request $request)
    {
        $input = $request->all();
        
        $insert = [];
        $caseString2 = $caseString3 = $caseString4 = 'case id';

        $ids = "";
        for($a=0; $a<count($request->amount); $a++){

            if(!empty($request->sid[$a])){
                $sid = $request->sid[$a];

                $target_box = $request->target_box[$a];
                $amount = $request->amount[$a];
                $lvl = $request->lvl[$a];

                $caseString2 .= " when $sid then '$amount'";
                $caseString3 .= " when $sid then '$lvl'";
                $caseString4 .= " when $sid then '$target_box'";

                $ids .= "$sid,";
            }else{
                if(!empty($request->amount[$a])){

                    $insert[] = [
                                    "target_box"=>$request->target_box[$a],
                                    "amount"=>$request->amount[$a],
                                    "lvl"=>$request->lvl[$a],
                                ];
                }
            }
            
        }
        $ids = trim($ids, ',');

        $create = SettingTeamDividend::insert($insert);
        if($ids != ''){
            DB::update("update setting_team_dividends set amount = $caseString2 end,
                                                             lvl = $caseString3 end,
                                                      target_box = $caseString4 end
                                                        where id in ($ids)");
        }
        
        $checkSetting = SettingTeamMain::first();
        if(!empty($checkSetting->id)){
            $setting = SettingTeamMain::find(1);
            $setting = $setting->update($input);
        }else{
            $setting = SettingTeamMain::create($input);            
        }

        Toastr::success("Setting Performance Dividend Successful");
        return redirect()->route('setting_team_dividend');
    }


    public function setting_recommend_bonus()
    {
        $selects = SettingRefferalReward::get();
        $levels = AgentLevel::get();

        $selectDetails = [];

        foreach($selects as $select){
            $selectDetails[$select->agent_lvl] = array($select->id, $select->amount);
        }

        return view('backend.settings.setting_recommend_bonus', ['levels'=>$levels], compact('selectDetails'));
    }

    public function save_setting_recommend_bonus(Request $request)
    {
        $insert = [];
        $caseString = 'case id';

        $ids = "";

        for($a=0; $a<count($request->amount); $a++){
            if(!empty($request->ids[$a])){
                //Update
                $id = $request->ids[$a];

                $amount = $request->amount[$a];

                $caseString .= " when $id then '$amount'";

                $ids .= "$id,";
            }else{
                //Create

                if(!empty($request->amount[$a])){

                    $insert[] = [
                                    "agent_lvl"=>$request->agent_lvl[$a],
                                    "amount"=>$request->amount[$a],
                                ];
                }
            }
        }

        $ids = trim($ids, ',');

        $create = SettingRefferalReward::insert($insert);
        if($ids != ''){
            DB::update("update setting_refferal_rewards set amount = $caseString end
                                                        where id in ($ids)");
        }

        Toastr::success("Setting Recommended Reward Successful");
        return redirect()->route('setting_recommend_bonus');
    }

    public function setting_agent_level()
    {
        $products = Product::where('status', '1')->get();
        $levels = AgentLevel::where('status', '1')->get();
        
        return view('backend.settings.setting_agent_lvl', ['products'=>$products, 'levels'=>$levels]);
    }

    public function setting_agent_level_save(Request $request)
    {
        $caseString = $caseString1 = $caseString2 = $caseString3 = 'case id';
        $ids = '';

        $insert = [];
        for($a=0; $a<count($request->agent_lvl); $a++){
            if(!empty($request->lvl_id[$a])){
                //Update

                $id = $request->lvl_id[$a];
                $agent_lvl = $request->agent_lvl[$a];
                $product_id = $request->product_id[$a];
                $buy_quantity = $request->buy_quantity[$a];
                $affiliate_quantity = $request->affiliate_quantity[$a];

                $caseString .= " when $id then '$agent_lvl'";
                $caseString1 .= " when $id then '$product_id'";
                $caseString2 .= " when $id then '$buy_quantity'";
                $caseString3 .= " when $id then '$affiliate_quantity'";

                $ids .= "$id,";
            }else{
                //Create
                if(!empty($request->agent_lvl[$a])){
                    $insert[] = [
                                    'agent_lvl'=>$request->agent_lvl[$a],
                                    'product_id'=>$request->product_id[$a],
                                    'buy_quantity'=>$request->buy_quantity[$a],
                                    'affiliate_quantity'=>$request->affiliate_quantity[$a],
                                ];
                }                
            }
        }
        //Update
        $ids = trim($ids, ',');

        if($ids != ''){
            DB::update("update agent_levels set agent_lvl = $caseString end,
                                                            product_id = $caseString1 end,
                                                            buy_quantity = $caseString2 end,
                                                            affiliate_quantity = $caseString3 end
                                                            where id in ($ids)");
        }

        //Insert
        $create = AgentLevel::insert($insert);


        Toastr::success("Agent Level Setting Updated!");
        return redirect()->route('setting_agent_level');
    }

     public function setting_customer_feedback()
    {
       
        return view('backend.settings.setting_customer_feedback');
    }
  
     public function index()
    {
       
        $student =DB::select('select * from user_contacts');
        return view('backend.settings.setting_customer_feedback',['student'=>$student]);
    }
  

    public function setting_shipping_fee()
    {
        $settingShippingFees = SettingShippingFee::get();
        return view('backend.settings.setting_shipping_fee', ['settingShippingFees'=>$settingShippingFees]);
    }

    public function save_setting_shipping_fee(Request $request)
    {
        $b = [];
        $caseString = $caseString1 = $caseString2 = 'case id';
        $ids = '';
        for($a=0; $a<count($request->type); $a++){
            if(empty($request->sid[$a])){
                if(!empty($request->weight[$a]) && !empty($request->shipping_fee[$a])){
                    $b[] = [
                              "area"=>$request->type[$a],
                              "weight"=>$request->weight[$a],
                              "shipping_fee"=>$request->shipping_fee[$a],
                           ];
                }
            }else{
                $sid = $request->sid[$a];
                $area = $request->type[$a];
                $weight = $request->weight[$a];
                $shipping_fee = $request->shipping_fee[$a];

                $caseString .= " when $sid then '$area'";
                $caseString1 .= " when $sid then '$weight'";
                $caseString2 .= " when $sid then '$shipping_fee'";

                $ids .= "$sid,";
            }
        }

        $insert = SettingShippingFee::insert($b);
        

        
        $ids = trim($ids, ',');
        if($ids != ''){
            DB::update("update setting_shipping_fees set area = $caseString end,
                                                                weight = $caseString1 end,
                                                                shipping_fee = $caseString2 end
                                                            where id in ($ids)");
        }

        Toastr::success("Shipping Fee Setting Successful");
        return redirect()->route('setting_shipping_fee');
    }

    public function setting_uom()
    {
        $select = SettingUom::get();

        return view('backend.settings.setting_uom', ['setting_uoms'=>$select]);
    }


    public function setting_uom_save(Request $request)
    {
        $caseString = 'case id';
        $ids = '';

        $insert = [];
        for($a=0; $a<count($request->uom_name); $a++){
            if(!empty($request->uid[$a])){
                //Update

                $id = $request->uid[$a];
                $uom_name = $request->uom_name[$a];

                $caseString .= " when $id then '$uom_name'";

                $ids .= "$id,"; 
            }else{
                //Create
                if(!empty($request->uom_name[$a])){
                    $insert[] = [
                                    'uom_name'=>$request->uom_name[$a],
                                    'status'=>'1',
                                    'created_at'=>date('Y-m-d H:i:s'),
                                    'updated_at'=>date('Y-m-d H:i:s'),
                                ];
                }                
            }
        }
        //Update
        $ids = trim($ids, ',');

        if($ids != ''){
            DB::update("update setting_uoms set uom_name = $caseString end
                                                            where id in ($ids)");
        }

        //Insert
        $create = SettingUom::insert($insert);

        Toastr::success("Updated UOM setting Successful");
        return redirect()->route('setting_uom');
    }

    public function setting_banner()
    {
        // $select = SettingUom::get();
        return view('backend.settings.setting_banner');
    }

    public function setting_specific_date()
    {
        $products = Product::where('status', '1')->get();
        $selects = SettingSpecificDate::get();

        return view('backend.settings.setting_specific_date', ['selects'=>$selects, 'products'=>$products]);
    }

    public function save_setting_specific_date(Request $request)
    {
        $caseString = $caseString1 = $caseString2 = $caseString3 = $caseString4 = $caseString5 = 'case id';
        $ids = '';

        $insert = [];
        for($a=0; $a<count($request->specific_date); $a++){
            if(!empty($request->bid[$a])){
                //Update

                $id = $request->bid[$a];
                $products = implode(',', $request['product'.$a]);
                $specific_date = date('Y-m-d', strtotime($request->specific_date[$a]));
                $type = $request->type[$a];
                $amount = $request->amount[$a];

                $caseString .= " when $id then '$products'";
                $caseString1 .= " when $id then '$specific_date'";
                $caseString2 .= " when $id then '$type'";
                $caseString3 .= " when $id then '$amount'";

                $ids .= "$id,"; 
            }else{
                //Create
                if(!empty($request['product'.$a])){
                    foreach($request['product'.$a] as $value){
                        $check = SettingSpecificDate::where('specific_date', date('Y-m-d', strtotime($request->specific_date[$a])))
                                                    ->whereRaw('find_in_set('.$value.', products)')
                                                    ->first();

                        if(!empty($check->id)){
                            $product = Product::find($value);
                            return Redirect::back()->withInput(Input::all())->withErrors($product->product_name.' has duplicated on '.date('Y-m-d', strtotime($request->specific_date[$a])));
                        }
                    }
                    $insert[] = [
                                    'products'=>implode(',', $request['product'.$a]),
                                    'specific_date'=>date('Y-m-d', strtotime($request->specific_date[$a])),
                                    'type'=>$request->type[$a],
                                    'amount'=>$request->amount[$a],
                                    'status'=>'1',
                                    'created_at'=>date('Y-m-d H:i:s'),
                                    'updated_at'=>date('Y-m-d H:i:s'),
                                ];
                }
            }
        }
        //Update
        $ids = trim($ids, ',');

        if($ids != ''){
            DB::update("update setting_specific_dates set products = $caseString end,
                                                          specific_date = $caseString1 end,
                                                          type = $caseString2 end,
                                                          amount = $caseString3 end
                                                   where id in ($ids)");
        }

        //Insert
        $create = SettingSpecificDate::insert($insert);

        Toastr::success("Updated Specific Date setting Successful");
        return redirect()->route('setting_specific_date');
    }

}
