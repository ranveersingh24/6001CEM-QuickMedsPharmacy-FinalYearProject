<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator, Redirect, Toastr, DB, File, Auth, DateTime;
use App\Transaction;
use App\WithdrawalTransaction;
use App\TransactionDetail;
use App\User;
use App\Merchant;
use App\Admin;
use App\AffiliateCommission;
use App\Bank;
use App\ProductQrcode;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if(!empty(request('dates'))){

            $new_dates = explode('-', request('dates'));
            $start = date('Y-m-d', strtotime($new_dates[0]));
            $end = date('Y-m-d', strtotime($new_dates[1]));

            $startDate = $new_dates[0];
            $endDate = $new_dates[1];

        }else{

            $ds = new DateTime("first day of this month");
            $de = new DateTime("last day of this month");

            $start = $ds->format('Y-m-d');
            $end = $de->format('Y-m-d');

            $startDate = $ds->format('m/d/Y');
            $endDate = $de->format('m/d/Y');
        }

        if(Auth::guard('admin')->check()){
            $transactions = Transaction::select(DB::raw('COALESCE(COALESCE(CONCAT(m.f_name, " ", m.l_name), CONCAT(s.f_name, " ", s.l_name)), CONCAT(a.f_name, " ", a.l_name)) AS customer_name'),
                                                'transactions.transaction_no', 'product_name', 'unit_price', 'quantity', 'total_amount', 'transactions.status', 'transactions.created_at', 'd.sub_category',
                                                'transactions.id AS Tid', 'transactions.grand_total', 'transactions.shipping_fee', 'transactions.processing_fee','transactions.additional_shipping_fee', 'transactions.completed', 'transactions.address_name',
                                                'm.code as Acode', 's.code as Ccode', 'a.code as ADcode', 'm.agent_type', 'awb_no', 
                                                'mall', 'online_payment_method')
                                       ->leftJoin('merchants AS m', 'm.code', 'transactions.user_id')
                                       ->leftJoin('users AS s', 's.code', 'transactions.user_id')
                                       ->leftJoin('admins AS a', 'a.code', 'transactions.user_id')
                                       ->join('transaction_details AS d', 'd.transaction_id', 'transactions.id')
                                       ->whereBetween(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m-%d")'), array($start, $end))
                                       ->groupBy('transactions.id')
                                       ->orderBy('transactions.created_at', 'desc');
        }else{
            $transactions = Transaction::select(DB::raw('COALESCE(CONCAT(m.f_name, " ", m.l_name), CONCAT(s.f_name, " ", s.l_name)) AS customer_name'),
                                                'transactions.transaction_no', 'product_name', 'unit_price', 'quantity', 'total_amount', 'transactions.status', 'transactions.created_at', 'd.sub_category')
                                       ->leftJoin('merchants AS m', 'm.code', 'transactions.user_id')
                                       ->leftJoin('users AS s', 's.code', 'transactions.user_id')
                                       ->join('transaction_details AS d', 'd.transaction_id', 'transactions.id')
                                       ->leftJoin('affiliates AS a', 'a.affiliate_id', 's.id')
                                       ->where('a.user_id', Auth::user()->code)
                                       ->orderBy('transactions.created_at', 'desc');            
        }
        $per_page = 10;
        if(!empty(request('per_page'))){
            $per_page = request('per_page');
        }

        $queries = [];
        $columns = [
            'transaction_no', 'status', 'dates', 'per_page'
        ];

        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
                if($column == 'status'){
                    $transactions = $transactions->where("transactions.status", 'like', "%".request($column)."%");
                }elseif($column == 'dates'){
                  $transactions = $transactions->whereBetween(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m-%d")'), array($start, $end));
                }elseif($column == 'per_page'){
                  $transactions = $transactions->paginate($per_page);
                }else{
                    $transactions = $transactions->where($column, 'like', "%".request($column)."%");
                }

                $queries[$column] = request($column);

            }
        }

        if(!empty(request('per_page'))){
            $transactions = $transactions->appends($queries);        
        }else{
            $transactions = $transactions->paginate($per_page)->appends($queries);
        }
        // $transactions = $transactions->paginate($per_page)->appends($queries);


        $netTransaction = Transaction::select(DB::raw('SUM(transactions.grand_total) AS netTotal'))
                                     ->where('status', '1')
                                     ->whereBetween(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m-%d")'), array($start, $end))
                                     ->first();

        return view('backend.transactions.index', ['transactions'=>$transactions, 'netTransaction'=>$netTransaction,
                                                    'startDate'=>$startDate, 'endDate'=>$endDate]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $transaction = Transaction::select('transactions.*', 'p.amount_type', 'p.amount AS discount_amount', 'p.discount_code')
                                  ->leftJoin('promotions AS p', 'p.id', 'transactions.discount_code')
                                  ->where('transactions.id', $id)
                                  ->first();
        if(empty($transaction->id)){
            abort(404);
        }

        $bank_online = Bank::find($transaction->bank_id);
        $bank_cdm = Bank::where('bank_code', $transaction->cdm_bank_id)->first();

        $details = TransactionDetail::where('transaction_id', $transaction->id)->get();

        $qrList = [];
        foreach($details as $detail){
            $qrList[$detail->id] = ProductQrcode::where('status', '1');

            if(!empty($detail->variation_id)){
                $qrList[$detail->id] = $qrList[$detail->id]->where('variation_id', $detail->variation_id);
            }else{
                $qrList[$detail->id] = $qrList[$detail->id]->where('product_id', $detail->product_id);
            }
            $qrList[$detail->id] = $qrList[$detail->id]->get();
        }
        
        return view('backend.transactions.view', ['transaction'=>$transaction, 'details'=>$details, 'bank_online'=>$bank_online, 
                                                  'bank_cdm'=>$bank_cdm], compact('qrList'));
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
        //
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

    public function withdrawal_list()
    {
        $transactions = WithdrawalTransaction::select(DB::raw('coalesce(CONCAT(m.f_name, " ", m.l_name), CONCAT(a.f_name, " ", a.l_name)) AS agent_name'), 'withdrawal_transactions.*')
                                             ->leftJoin('merchants AS m', 'm.code', 'withdrawal_transactions.user_id')
                                             ->leftJoin('admins AS a', 'a.code', 'withdrawal_transactions.user_id')
                                             ->orderBy('withdrawal_transactions.id', 'desc');
        $queries = [];
        $columns = [
            'withdrawal_no', 'agent_name', 'status'
        ];

        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
                if($column == 'agent_name'){
                    $transactions = $transactions->where(DB::raw('CONCAT(m.f_name, " ", m.l_name)'), 'like', "%".request($column)."%");
                }elseif($column == 'status'){
                    $transactions = $transactions->where('withdrawal_transactions.status', 'like', "%".request($column)."%");
                }else{
                    $transactions = $transactions->where($column, 'like', "%".request($column)."%");                    
                }

                $queries[$column] = request($column);

            }
        }

        $per_page = 10;
        if(!empty(request('per_page'))){
            $per_page = request('per_page');
        }
        $transactions = $transactions->paginate($per_page)->appends($queries);

        return view('backend.transactions.withdrawal_list', ['transactions'=>$transactions]);
    }

    public function uploadBankSlip(Request $request)
    {

        $files = $request->file('uploadSlip'); 
        $name = $files->getClientOriginalName();
        $exp = explode(".", $name);
        $file_ext = end($exp);
        $name = md5($name.date('Y-m-d H:i:s')).'.'.$file_ext;
        $files->move("uploads/withdrawal_bank_slip/", $name);

        $input = $request->all();
        $input['withdrawal_slip'] = "uploads/withdrawal_bank_slip/".$name;

        $withdrawal = WithdrawalTransaction::find($request->wid);

        $merchants = Merchant::where('code', $withdrawal->user_id)->first();
        $admins = Admin::where('code', $withdrawal->user_id)->first();

        if(!empty($merchants->id)){
            $phone = $merchants->phone;
        }else{
            $phone = $admins->phone;
        }

        if($request->withAction == 1){
            // $destination = urlencode($phone);
            // $message = "因诗美: 您的提款号: ".$withdrawal->withdrawal_no." 已批准";
            // $message = html_entity_decode($message, ENT_QUOTES, 'utf-8'); 
            // $message = urlencode($message);
              
            // $username = urlencode("yinshimei");
            // $password = urlencode("yinshimei1234");
            // $sender_id = urlencode("66300");
            // $type = "2";

            // $fp = "https://www.isms.com.my/isms_send_all.php";
            // $fp .= "?un=$username&pwd=$password&dstno=$destination&msg=$message&type=$type&sendid=$sender_id&agreedterm=YES";
            // //echo $fp;
              
            // $http = curl_init($fp);

            // curl_setopt($http, CURLOPT_RETURNTRANSFER, TRUE);
            // $http_result = curl_exec($http);
            // $http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);
            // curl_close($http);

            
            $totalBalance = $this->GetWalletBalance($withdrawal->user_id);
            if($totalBalance < $withdrawal->amount){
                Toastr::error("This user has insufficient balance!");
            }else{
                $input['status'] = 1;
            }
        }

        $withdrawal = $withdrawal->update($input);

        Toastr::success("Upload Successful!");
        return redirect()->route('withdrawal_list');
    }

    public function GetWalletBalance($user)
    {
        $balance = AffiliateCommission::select(DB::raw('SUM(comm_amount) as totalBalance'))
                                      ->where('user_id', $user)
                                      ->where('status', '1')
                                      ->first();

        $withdrawal = WithdrawalTransaction::select(DB::raw('SUM(amount) as totalWithdrawal'))
                                             ->where('user_id', $user)
                                             ->where('status', '1')
                                             ->first();
        $totalBalance = 0;
        
        $totalBalance = $balance->totalBalance - $withdrawal->totalWithdrawal;
        

        return $totalBalance;
    }

    public function transaction_invoice($transaction_no)
    {
        $transaction = Transaction::select('transactions.*', 'p.amount_type', 'p.amount AS discount_amount', 'p.discount_code')
                                  ->leftJoin('promotions AS p', 'p.id', 'transactions.discount_code')
                                  ->where('transactions.transaction_no', $transaction_no)
                                  ->first();

        if(empty($transaction->id)){
            abort(404);
        }

        $bank_online = Bank::find($transaction->bank_id);
        $bank_cdm = Bank::where('bank_code', $transaction->cdm_bank_id)->first();

        $details = TransactionDetail::select('transaction_details.*', 'transaction_details.quantity as t_qty', 'u.uom_name', 'p.packages')
                                    ->join('products AS p', 'p.id', 'transaction_details.product_id')
                                    ->leftJoin('setting_uoms AS u', 'u.id', 'p.product_type')
                                    ->where('transaction_id', $transaction->id)
                                    ->get();

        return view('backend.transactions.invoice', ['transaction'=>$transaction, 'details'=>$details]);
    }

    public function add_awb_no(Request $request)
    {
        $transaction = Transaction::find($request->transaction_id);
        if(!empty($transaction->id)){
            $transaction = $transaction->update(['awb_no'=>$request->awb_no]);
        }

        Toastr::success("Successful!");
        return redirect()->back();
    }
}
