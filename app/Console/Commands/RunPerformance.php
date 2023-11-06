<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Cart;
use App\Transaction;
use App\WithdrawalTransaction;

use App\SettingPerformanceMain;
use App\SettingPerformanceDividend;
use App\Merchant;
use App\AffiliateCommission;

use App\SettingTeamMain;
use App\SettingTeamDividend;
use DB;

date_default_timezone_set("Asia/Kuala_Lumpur");
class RunPerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:cronjob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Cron Job';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
        //Clear Cart
        $cart = Cart::where(DB::raw('DATE_ADD(created_at, INTERVAL 7 DAY)'), '<=', date('Y-m-d H:i:s'))->delete();

        $transaction = Transaction::where('status', '99')->where(DB::raw('DATE_ADD(created_at, INTERVAL 7 DAY)'), '<=', date('Y-m-d H:i:s'))->update(['status'=>'55']);

        
        $settingMain = SettingPerformanceMain::first();
        
        if(!empty($settingMain->id)){

            if($settingMain->date_update == date('d') || ($settingMain->date_update != date('d') && date('t') == date('d'))){
                $comm = [];
                

                $merchants = Merchant::where('status', '1')->get();

                foreach($merchants as $merchant){

                    $transaction = Transaction::select(DB::raw('SUM(grand_total) AS TotalSales'))
                                              ->leftJoin('users AS u', 'u.code', 'transactions.user_id')
                                              ->leftJoin('merchants AS m', 'm.code', 'transactions.user_id')
                                              ->where(function ($query) {
                                                    $query->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), date('Y-m'))
                                                          ->where('transactions.status', '1');
                                              })
                                              ->where(function ($query) use ($merchant) {
                                                    $query->where('u.master_id', '=', $merchant->code)
                                                          ->orWhere('transactions.user_id', '=', $merchant->code)
                                                          ->orWhere('m.master_id', '=', $merchant->code);
                                              })
                                              ->first();
                    
                    
                    if(!empty($transaction->TotalSales)){
                        $mtSales = $transaction->TotalSales;
                    }else{
                        $mtSales = 0;
                    }

                    
                    $dividend = SettingPerformanceDividend::select('setting_performance_dividends.*', 'p.agent_lvl AS agentLvl')
                                                          ->join('agent_levels AS p', 'p.id', 'setting_performance_dividends.lvl')
                                                          ->where('p.id', $merchant->lvl)
                                                          ->where('target', '<=', $mtSales)
                                                          ->first();
                    
                    if(!empty($dividend->id)){

                    // echo 'Code:'.$merchant->code.' - lvl:'.$merchant->lvl.' - TotalSales:'.$mtSales.' - Commssion:'.$dividend->amount.' - TotalCommission:'.$mtSales * $dividend->amount / 100;
                    // echo ' - ';
                        $comm[] = [
                                    "type"=>'4',
                                    "user_id"=>$merchant->code,
                                    "comm_pa_type"=>'Percentage',
                                    "comm_pa"=>$dividend->amount,
                                    "comm_amount"=>$mtSales * $dividend->amount / 100,
                                    "comm_desc"=>"Performance Reward For ".date('Y-m')
                                  ];
                    }

                }

                $create = AffiliateCommission::insert($comm);   
            }
        }

        $settingTeamMain = SettingTeamMain::first();

        if($settingTeamMain->date_update == date('d') || ($settingTeamMain->date_update != date('d') && date('d') == date('t'))){
            $commTeam = [];
            $TeamTransaction = Transaction::select(DB::raw('SUM(grand_total) AS TotalSales'))
                                          ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), date('Y-m'))
                                          ->where('status', '1')
                                          ->first();
            

            if(!empty($TeamTransaction->TotalSales) && $TeamTransaction->TotalSales >= $settingTeamMain->target){
                // echo $TeamTransaction->TotalSales;
                $tSales = $TeamTransaction->TotalSales;

                $merchants = Merchant::where('status', '1')->get();
                foreach($merchants as $merchant){
                    $getBox = Transaction::select(DB::raw('SUM(d.quantity) AS totalQuantity'))
                                         ->join('transaction_details AS d', 'd.transaction_id', 'transactions.id')
                                         ->where('transactions.user_id', $merchant->code)
                                         ->where('transactions.status', 1)
                                         ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), date('Y-m'))
                                         ->first();

                    if(!empty($getBox->totalQuantity)){
                      $totalBox = $getBox->totalQuantity;
                    }else{
                      $totalBox = 0;
                    }

                    $dividend = SettingTeamDividend::select('setting_team_dividends.*', 'p.agent_lvl AS agentLvl')
                                                  ->join('agent_levels AS p', 'p.id', 'setting_team_dividends.lvl')
                                                  ->where('p.id', $merchant->lvl)
                                                  ->where('setting_team_dividends.target_box', '<=', $totalBox)
                                                  ->orderBy('id', 'desc')
                                                  ->first();
                    
                    if(!empty($dividend->id)){
                        $commTeam[] = [
                                    "type"=>'5',
                                    "user_id"=>$merchant->code,
                                    "comm_pa_type"=>'Percentage',
                                    "comm_pa"=>$dividend->amount,
                                    "comm_amount"=>$tSales * $dividend->amount / 100,
                                    "comm_desc"=>"Team Reward For ".date('Y-m')
                                  ];                        
                    }

                }
                $create = AffiliateCommission::insert($commTeam);  
            }
        }
    }
}
