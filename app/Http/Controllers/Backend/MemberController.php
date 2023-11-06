<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Admin;
use App\Merchant;
use App\User;
use App\Affiliate;
use App\VerifyCode;
use App\State;
use App\SettingRefferalReward;
use App\AgentLevel;
use App\SettingMerchantBonus;
use App\AffiliateCommission;
use App\AgentRebateHistory;
use App\Transaction;
use App\MemberWallet;
use Validator, Redirect, Toastr, DB, File, Auth, Hash;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::select('users.*')
                             ->whereNotIn('users.status', ['99', '3'])
                             ->orderBy('users.created_at', 'desc');

        $per_page = 10;
        if(!empty(request('per_page'))){
            $per_page = request('per_page');
        }

        $queries = [];
        $columns = [
            'code', 'member_name', 'status'
        ];
        foreach($columns as $column){
            if(request()->has($column) && !empty(request($column))){
                if($column == 'member_name'){
                    $users = $users->where(DB::raw('CONCAT(f_name, " ", l_name)'), 'like', "%".request($column)."%");
                }elseif($column == 'status'){
                    $users = $users->where('users.status', 'like', "%".request($column)."%");
                }elseif($column == 'per_page'){
                  $users = $users->paginate($per_page);
                }else{
                    $users = $users->where($column, 'like', "%".request($column)."%");
                }

                $queries[$column] = request($column);

            }
        }
        
        $users = $users->paginate($per_page)->appends($queries);
        

        return view('backend.members.index', ['users'=>$users]);
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
        $user = User::select('users.*', 'm.code AS master_code')
                            ->leftJoin('users AS m', 'm.code', 'users.master_id')
                            ->where('users.id', $id)
                            ->first();

        return view('backend.members.edit', ['user'=>$user]);
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
            'f_name' => 'required',
            'l_name' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validator);
        }
        
        $input = $request->all();
        $input['f_name'] = htmlspecialchars(trim($request->f_name));
        $input['l_name'] = htmlspecialchars(trim($request->l_name));

        $user = User::find($id);
        $user_name = $user->f_name;
        $user = $user->update($input);

        Toastr::success("$user_name Updated!");
        return redirect()->route('member.members.edit', $id);
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

   
}
