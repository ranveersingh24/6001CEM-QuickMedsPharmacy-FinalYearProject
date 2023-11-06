<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\AffiliateCommission;
use App\Merchant;
use App\Product;
use App\User;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
    	$totalSales = Transaction::select(DB::raw('SUM(grand_total) AS totalSales'))
    							 ->where('status', '1')
                                 ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), date('Y-m'))
    							 ->first();

        $totalMembers = User::select(DB::raw('COUNT(id) AS totalMembers'))
                               ->where('status', '1')
                               ->first();

   		$totalProduct = Product::select(DB::raw('COUNT(id) AS totalProduct'))
    						  ->where('status', '1')
    						  ->first();

    	$transactions = Transaction::whereNotIn('status', ['1', '99', '95', '96'])
    							   ->get();

    	

        $list=array();
        $month = date('m');
        $year = date('Y');
        $dailySales = 0;

        for($d=1; $d<=31; $d++)
        {

            $time=mktime(12, 0, 0, $month, $d, $year);          
            if (date('m', $time)==$month){
                // $list[]=date('Y-m-d', $time);

                $dTransaction = Transaction::select(DB::raw('SUM(grand_total) AS dailySales'), DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") AS today'))
                                   ->where('status', '1')
                                   ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), date('Y-m-d', $time))
                                   ->first();
                if(!empty($dTransaction->dailySales)){
                    $dailySales = $dTransaction->dailySales;
                }else{
                    $dailySales = 0;
                }

                $list[] = $dailySales;
            }
        }

        $list2 = array();
        for ($m=1; $m<=12; $m++) {
            $month = date('Y-m', mktime(0,0,0,$m, 1, date('Y')));    
            $mTransaction = Transaction::select(DB::raw('SUM(grand_total) AS monthlySales'))
                                       ->where('status', '1')
                                       ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $month)
                                       ->first();
            if(!empty($mTransaction->monthlySales)){
                $monthlySales = $mTransaction->monthlySales;
            }else{
                $monthlySales = 0;
            }
            $list2[] = $monthlySales;

        }


        $totalCustomer = User::select(DB::raw('COUNT(id) AS totalCustomer'))
                        ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), date('Y-m-d'))
                        ->first();

       
        

    	return view('backend.dashboard.index', ['totalSales'=>$totalSales,   'totalMembers'=>$totalMembers ,'totalProduct'=>$totalProduct,
    											'transactions'=>$transactions,  'list'=>$list, 'list2'=>$list2, 'totalCustomer'=>$totalCustomer]);
    }

    
}
