<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Merchant;
use App\Affiliate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use DB, Auth, Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'country_code' => ['required'],
            'phone' => ['required', 'unique:users', 'unique:merchants'],
            'f_name' => ['required', 'string', 'max:255'],
            'f_name' => ['required', 'string', 'max:255'],
            'l_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'unique:merchants'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function MemberCode()
    {
        $user = User::select(DB::raw("COUNT(id) AS totalUser"))->first();
        $totalCount = $user->totalUser + 1;

        if(strlen($totalCount) == '1'){
            $member_id = "Mb00000".$totalCount;
        }elseif(strlen($totalCount) == '2'){
            $member_id = "Mb0000".$totalCount;
        }elseif(strlen($totalCount) == '3'){
            $member_id = "Mb000".$totalCount;
        }elseif(strlen($totalCount) == '4'){
            $member_id = "Mb00".$totalCount;
        }elseif(strlen($totalCount) == '5'){
            $member_id = "Mb0".$totalCount;
        }else{
            $member_id = "Mb".$totalCount;
        }

        return $member_id;
    }

    protected function MerchantCode()
    {
        $user = Merchant::select(DB::raw("COUNT(id) AS totalUser"))->first();
        $totalCount = $user->totalUser + 1;

        if(strlen($totalCount) == '1'){
            $member_id = "M00000".$totalCount;
        }elseif(strlen($totalCount) == '2'){
            $member_id = "M0000".$totalCount;
        }elseif(strlen($totalCount) == '3'){
            $member_id = "M000".$totalCount;
        }elseif(strlen($totalCount) == '4'){
            $member_id = "M00".$totalCount;
        }elseif(strlen($totalCount) == '5'){
            $member_id = "M0".$totalCount;
        }else{
            $member_id = "M".$totalCount;
        }

        return $member_id;
    }

    protected function create(array $data)
    {
        if(!empty(Session::get('guest_agent'))){
          $master_id = Session::get('guest_agent');
        }else{
          $master_id = (!empty($data['master_id'])) ? $data['master_id'] : 'AD000001';
        }

        if($data['role'] == '1'){
            return User::create([
                'master_id' => $master_id,
                'code' => $this->MemberCode(),
                'country_code' => $data['country_code'],
                'phone' => preg_replace("/^\+?{$data['country_code']}/", '',$data['phone']),
                'f_name' => $data['f_name'],
                'l_name' => $data['l_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => '1',
            ]);
        }else{
            return Merchant::create([
                'master_id' => $master_id,
                'code' => $this->MerchantCode(),
                'country_code' => $data['country_code'],
                'phone' => preg_replace("/^\+?{$data['country_code']}/", '',$data['phone']),
                'f_name' => $data['f_name'],
                'l_name' => $data['l_name'],
                'email' => $data['email'],
                'lvl'=> '1',
                'agent_type'=> '2',
                'password' => Hash::make($data['password']),
                'status' => '99',
            ]);
        }
    }


    protected function sendEmailNotification($to, $from, $subject, $user_id)
    {
     

      
    }
}
