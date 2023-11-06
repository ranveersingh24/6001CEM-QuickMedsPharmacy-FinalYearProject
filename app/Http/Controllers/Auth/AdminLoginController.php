<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin;
use App\Merchant;
use App\User;
use Auth, Toastr;

class AdminLoginController extends Controller
{
	public function __construct()
    {
        // $this->middleware('guest:admin');
    }

    public function ShowAdminLogin()
    {
        if(Auth::guard('admin')->check() || Auth::guard('merchant')->check()){
            if(Auth::guard('merchant')->check() && empty(Auth::guard('merchant')->user()->permission_lvl)){
                return redirect()->route('admin_login');
            }
            return redirect()->route('admin.admins.index');
        }
    	return view('auth.admin_login');
    }

    public function login(Request $request)
    {
    	$this->validate($request, [
    		'email' => 'required|email',
    		'password' => 'required'
    	]);

        $admin = Admin::where('email', $request->email)
                      ->where('status', '1')
                      ->exists();

        $merchant = Merchant::where('email', $request->email)
                      ->where('status', '1')
                      ->exists();

        $user = User::where('email', $request->email)
                      ->where('status', '1')
                      ->exists();

        $merchantD = Merchant::where('email', $request->email)
                             ->where('status', '1')
                             ->first();

        $userD = User::where('email', $request->email)
                             ->where('status', '1')
                             ->first();
        
        if($admin == 1 || $merchant == 1 || $user == 1){
            if((!empty($merchantD->id) && empty($merchantD->permission_lvl))){
                return redirect()->back()->withErrors("You do not have permission to log in");
            }
            if((!empty($userD->id) && empty($userD->permission_lvl))){
                return redirect()->back()->withErrors("You do not have permission to log in");
            }
            // echo $merchant->permission_lvl.' - 123';
            // exit();

        	if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)){
                Toastr::success("Login Successfully!");
        		return redirect()->route('dashboard.dashboards.index');
        	}elseif(Auth::guard('merchant')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)){

                Toastr::success("Login Successfully!");
                return redirect()->route('dashboard.dashboards.index');
            }elseif(Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)){

                Toastr::success("Login Successfully!");
                return redirect()->route('dashboard.dashboards.index');
            }
            
        }

    	return redirect()->back()->withErrors("Username or Password Incorrect");
    }

    public function admin_logout()
    {
        if(Auth::guard('admin')->check()){
            Auth::guard('admin')->logout();
        }else{
            Auth::guard('merchant')->logout();
        }

        return redirect()->route('admin_login');
    }
}
